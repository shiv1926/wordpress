<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once( 'facebook.php' );
/**
* WooSocio Base Class
*
* All functionality pertaining to core functionality of the WooSocio plugin.
*
* @package WordPress
* @subpackage WooSocio
* @author qsheeraz
* @since 0.0.1
*
* TABLE OF CONTENTS
*
* public $version
* private $file
*
* private $token
* private $prefix
*
* private $plugin_url
* private $assets_url
* private $plugin_path
*
* public $facebook
* private $fb_user_profile
* private $app_id
* private $secret
*
* - __construct()
* - init()
* - woosocio_meta_box()
* - woosocio_ajax_action()
* - woosocio_admin_init()
* - socialize_post()
* - woosocio_admin_menu()
* - woosocio_admin_styles()
* - FacebookSetting()
* - products_list()
* - check_connection()
* - save_app_info()
* - update_page_info()
*
* - load_localisation()
* - activation()
* - register_plugin_version()
*/

class Woo_Socio {
	public $version;
	private $file;

	private $token;
	private $prefix;

	private $plugin_url;
	private $assets_url;
	private $plugin_path;
	
	public $facebook;
	public $fb_user_profile = array();
	public $fb_user_pages = array();
	
	private $fb_app_id;
	private $fb_secret;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct ( $file ) {
		$this->version = '';
		$this->file = $file;
		$this->prefix = 'woo_socio_';
		$this->fb_app_id = get_option( 'fb_app_id' );
		$this->fb_secret = get_option( 'fb_app_secret' );

		/* Plugin URL/path settings. */
		$this->plugin_url = str_replace( '/classes', '', plugins_url( plugin_basename( dirname( __FILE__ ) ) ) );
		$this->plugin_path = str_replace( 'classes', '', plugin_dir_path( __FILE__ ));
		$this->assets_url = $this->plugin_url . '/assets';
		
		$this->facebook = new Facebook(array('appId'  	  => $this->fb_app_id,
  											 'secret' 	  => $this->fb_secret,
											 'status' 	  => true,
											 'cookie' 	  => true,
											 'xfbml' 	  => true,
											 'fileUpload' => true   ));
		
	} // End __construct()

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init () {
		add_action( 'init', array( $this, 'load_localisation' ) );
		add_action( 'admin_init', array( $this, 'woosocio_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'woosocio_admin_menu' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'woosocio_meta_box' ) );
		add_action( 'save_post', array( $this, 'socialize_post' ));
		add_action( 'wp_ajax_my_action', array( $this, 'woosocio_ajax_action' ));
		add_action( 'wp_ajax_save_app_info', array( $this, 'save_app_info' ));
		add_action( 'wp_ajax_update_page_info', array( $this, 'update_page_info' ));
		add_action( 'woocommerce_single_product_summary', array( $this, 'show_sharing_buttons'), 50, 2  );
		add_filter( 'manage_edit-product_columns', array($this, 'woosocio_columns'), 998);
		add_action( 'manage_product_posts_custom_column', array($this, 'woosocio_custom_product_columns') );
		add_action( 'admin_enqueue_scripts',array($this, 'pp_admin_style') );
	}
	
	function pp_admin_style() 
	{
	    wp_register_style('custom_pp_admin_css',plugins_url('/social-auto-post/css/admin-style.css'));
	    wp_enqueue_style('custom_pp_admin_css');
	}

	function pa($arr){

		echo '<pre>';
		print_r($arr);
		echo '</pre>';
	}

		/**
	 * woosocio_columns function.
	 *
	 * @access public
	 * @return columns
	 */
	function woosocio_columns($columns) {
		if ( isset( $_REQUEST['list'] ) && $_REQUEST['list'] == 'woosocio' ) {
			echo '<style>';
			echo '.actions {display: none;}';
			echo '.search-box {display: none;}';
			echo '.subsubsub {display: none;}';
			echo '</style>';

		    $columns = array();
			$columns["cb"] = "<input type=\"checkbox\" />";
			$columns["woo_name"] = __( 'Name', 'woosocio' );
			$columns["like_btn"] = __('Like/ Share Button?', 'woosocio');
			$columns["fb_post"] = __('Posted to Facebook?', 'woosocio');
			$columns["custom_msg"] = __('Custom Message', 'woosocio');

			return $columns;
		}
		else
			return $columns;
	}
		
	/**
	 * woosocio_custom_product_columns function.
	 *
	 * @access public
	 * @return void
	 */
	function woosocio_custom_product_columns( $column ) {
	global $post, $woocommerce, $the_product;

	if ( empty( $the_product ) || $the_product->id != $post->ID )
		$the_product = get_product( $post );

	switch ($column) {
		case "woo_name" :
			$edit_link = get_edit_post_link( $post->ID );
			$title = _draft_or_post_title();
			$post_type_object = get_post_type_object( $post->post_type );
			$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );

			echo '<strong><a class="row-title" href="'.$edit_link.'">' . $title.'</a>';
		break;
		case "like_btn" :
			$woo_like_fb = metadata_exists('post', $post -> ID, '_woosocio_like_facebook') ? get_post_meta( $post -> ID, '_woosocio_like_facebook', true ) : 'No';
			echo $woo_like_fb == 'checked' ? '<img src="'.$this->assets_url.'/yes.png" alt="Yes" width="25">' : '<img src="'.$this->assets_url.'/no.png" alt="No" width="25">';
		break;
		case "fb_post" :
			$woo_post_fb = metadata_exists('post', $post -> ID, '_woosocio_facebook') ? get_post_meta( $post -> ID, '_woosocio_facebook', true ) : 'No';
			echo $woo_post_fb == 'checked' ? '<img src="'.$this->assets_url.'/yes.png" alt="Yes" width="25">' : '<img src="'.$this->assets_url.'/no.png" alt="No" width="25">';			
		break;
		case "custom_msg" :
			echo get_post_meta( $post -> ID, '_woosocio_msg', true );
		break;
	}
}

	/**
	 * show_sharing_buttons function.
	 *
	 * @access public
	 * @return void
	 */
	public function show_sharing_buttons() {
		$post_id = get_the_ID();
		$socio_link = get_permalink( $post_id );
		$fb_like = metadata_exists('post', $post_id, '_woosocio_like_facebook') ? get_post_meta( $post_id, '_woosocio_like_facebook', true ) : 'checked';
		if ($fb_like) {
			if($this->fb_app_id)
				$fb_appid_option = '&appId='.$this->fb_app_id;
		  ?>
		  <div class="fb-like" data-href="<?php echo $socio_link; ?>" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
		  <div id="fb-root"></div>
		  <script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/all.js#xfbml=1<?php echo $fb_appid_option; ?>";
			fjs.parentNode.insertBefore(js, fjs);
		  }(document, 'script', 'facebook-jssdk'));</script> 
		  <?php
		}
	}

	/**
	 * woosocio_meta_box function.
	 *
	 * @access public
	 * @return void
	 */
	public function woosocio_meta_box() {
		global $post;
		global $post_type;
		$post_id = get_the_ID();
		
		if ( $post_type == 'product' )
		{
			?>

		<div id="woosocio" class="misc-pub-section misc-pub-section-last">
			<?php
			$content = '';

			_e( 'WooSocio:', 'woosocio' );
			//metadata_exists('post', $post_id, '_woosocio_facebook');
			$like_chkbox_val = metadata_exists('post', $post_id, '_woosocio_like_facebook') ? get_post_meta( $post_id, '_woosocio_like_facebook', true ) : 'checked';
			$chkbox_val = metadata_exists('post', $post_id, '_woosocio_facebook') ? get_post_meta( $post_id, '_woosocio_facebook', true ) : 'checked';
			$saved_msg = ( get_post_meta( $post_id, '_woosocio_msg', true ) ? get_post_meta( $post_id, '_woosocio_msg', true ) : $post->title );
			if ( $this->check_connection() ): 
				echo '&nbsp;<img src="'.$this->assets_url.'/connected.gif" alt="Connected "> as: '."<b>".$this->fb_user_profile['name']."</b>";
				//echo "&nbsp;" . __( 'Connected as: '."<b>".$this->fb_user_profile['name']."</b>", 'woosocio' );
			else:
				echo "&nbsp;<b>" . __( 'Not Connected', 'woosocio' )."</b>";
				?>&nbsp;<a href="<?php echo admin_url( 'options-general.php?page=woosocio' ); ?>" target="_blank"><?php _e( 'Connect', 'woosocio' ); ?></a>
			<?php endif; ?>
			<div id="woosocio-form" style="display: none;">
            	<br />
                <input type="checkbox" name="like_facebook" id="like-facebook" <?php echo $like_chkbox_val; ?> />
                <label for="like-facebook"><b><?php _e( 'Show Like/Share buttons?', 'woosocio' ); ?></b></label><br />
                <input type="checkbox" name="chk_facebook" id="chk-facebook" <?php echo $chkbox_val; ?> />
                <label for="chk-facebook"><b><?php _e( 'Post to Facebook?', 'woosocio' ); ?></b></label><br />
				<label for="woosocio-custom-msg"><?php _e( 'Custom Message: (No html tags)', 'woosocio' ); ?></label>
				<textarea name="woosocio_custom_msg" id="woosocio-custom-msg"><?php echo $saved_msg; ?></textarea>
				<a href="#" id="woosocio-form-ok" class="button"><?php _e( 'Save', 'woosocio' ); ?></a>
				<a href="#" id="woosocio-form-hide"><?php _e( 'Cancel', 'woosocio' ); ?></a>
                <input type="hidden" name="postid" id="postid" value="<?php echo get_the_ID()?>" />
			</div>
             &nbsp; <a href="#" id="woosocio-form-edit"><?php _e( 'Edit', 'woosocio' ); ?></a>
		</div> 
        
		<script type="text/javascript">
        jQuery(document).ready(function($){
                $("#woosocio-form").hide();
                
            $("#woosocio-form-edit").click(function(){
				$("#woosocio-form-edit").hide();
                $("#woosocio-form").show(1000);
            });
            
            $("#woosocio-form-hide").click(function(){
                $("#woosocio-form").hide(1000);
				$("#woosocio-form-edit").show();
            });
           
		    $("#woosocio-form-ok").click(function(){
				var custom_msg;
       			custom_msg = $("#woosocio-custom-msg").val();
				var data = {
					action: 'my_action',
					text1: custom_msg,
					postid: $("#postid").val(),
					chk_facebook: $("#chk-facebook").attr("checked"),
					like_facebook: $("#like-facebook").attr("checked")
				};
				$.post(ajaxurl, data, function(response) {
					console.log('Got this from the server: ' + response);
				});
                $("#woosocio-form").hide(1000);
				$("#woosocio-form-edit").show();
            });

        });
        </script>
		<?php 
		}
	}

	/**
	 * woosocio_ajax_action function.
	 *
	 * @access public
	 * @return void
	 */	
	public function woosocio_ajax_action($post) {
		//global $post;
		//$post_id = get_the_ID();
		if(!update_post_meta ($_POST['postid'], '_woosocio_msg', $_POST['text1'])) add_post_meta($_POST['postid'],'_woosocio_msg',$_POST['text1'],true);
		if(!update_post_meta ($_POST['postid'], '_woosocio_facebook',$_POST['chk_facebook'])) add_post_meta($_POST['postid'], '_woosocio_facebook',$_POST['chk_facebook'], true );
		if(!update_post_meta ($_POST['postid'], '_woosocio_like_facebook', $_POST['like_facebook'])) add_post_meta($_POST['postid'], '_woosocio_like_facebook', $_POST['like_facebook'], true );
		//echo $_POST['text1'].$_POST['postid'];
		die(0);
		//die(); // this is required to return a proper result
	}
	
	/**
	 * woosocio_admin_init function.
	 *
	 * @access public
	 * @return void
	 */		
	public function woosocio_admin_init() {
       /* Register stylesheet. */
       wp_register_style( 'woosocioStylesheet', $this->assets_url.'/woosocio.css' );
   }

	/**
	* socialize_post function.
	* @access public
	* @return void
	*/
	public function socialize_post($post_id){
		global $wpdb;
		$post_id = get_the_ID();
		$message = get_the_title($post_id);
		$this->check_connection();
		$fb_post = metadata_exists('post', $post_id, '_woosocio_facebook') ? get_post_meta( $post_id, '_woosocio_facebook', true ) : 'checked';
		$message.= metadata_exists('post', $post_id, '_woosocio_msg') ? " - ".get_post_meta( $post_id, '_woosocio_msg', true ) : '';
		$fb_page_value = get_option( $this->fb_user_profile['id'].'_fb_page_id', $this->fb_user_profile['id'] );
		
		$querystr = "
    		SELECT $wpdb->posts.* 
   			FROM   $wpdb->posts
    		WHERE  $wpdb->posts.ID = $post_id
    		AND    $wpdb->posts.post_status = 'publish' 
    		AND    $wpdb->posts.post_type = 'product'
 			";
 		$socio_post = $wpdb->get_row($querystr, OBJECT);
		
		if($this->check_connection())
		{
			$socio_link = get_permalink( $post_id );
	    	
			try {
				$ret_obj = $this -> facebook -> api('/'.$fb_page_value.'/feed', 'POST', array(  'link' 		=> $socio_link,
                                         														'message'	=> $message)
                                      		   );
				if ($ret_obj) {
					if ( ! update_post_meta ($post_id, '_woosocio_fb_posted', 'checked' ) ) 
						   add_post_meta(    $post_id, '_woosocio_fb_posted', 'checked', true );
				}
				if ( ! update_post_meta ($post_id, '_woosocio_facebook', 'checked' ) ) 
			   		   add_post_meta(    $post_id, '_woosocio_facebook', 'checked', true );
      		} 
			catch(FacebookApiException $e) {
        		$login_url = $this->facebook->getLoginUrl( array('scope' => 'photo_upload')); 
        		echo 'Please <a href="' . $login_url . '">login.</a>';
				console.log($e->getType());
      		}   
		}
	}

	/**
	 * woosocio_admin_menu function.
	 *
	 * @access public
	 * @return void
	 */		
	public function woosocio_admin_menu () {
		add_menu_page('Social Auto Post', 'Social Auto Post', 'administrator', 'social-auto-post',array($this,'FacebookSetting'),1111);
		$page_logins   = add_submenu_page('social-auto-post', 'Facebook Settings', 'Facebook', 'administrator', 'facebook',array($this,'FacebookSetting'));
		//$page_products = add_submenu_page('social-auto-post', 'Gmail Settings', 'Gmail', 'administrator', 'gmail', array($this,'GmailSetting'));
		$page_products = add_submenu_page('social-auto-post', 'Twitter Setting', 'Twitter', 'administrator', 'twitter', array($this,'TwitterSetting'));
		//$page_products = add_submenu_page('social-auto-post', 'Access Token', 'Access Token', 'administrator', 'accesstoken', array($this,'getaccesstoken'));
		
		$page_products = add_submenu_page('social-auto-post', 'Post To Social Media', 'Post To Social Media', 'administrator', 'posttosocialmedia', array($this,'PostToSocialMedia'));
		$page_products = add_submenu_page('social-auto-post', 'Posted On Social Media', 'Posted On Social Media', 'administrator', 'postedonsocialmedia', array($this,'PostedOnSocialMedia'));
		$upload_feed = add_submenu_page('social-auto-post', 'Upload Feed', 'Upload Feed', 'administrator', 'uploadfeed', array($this,'UploadFeed'));
		$run_import = add_submenu_page('social-auto-post', 'Import', 'Import', 'administrator', 'runimport', array($this,'RunImport'));

		add_action( 'admin_print_styles-' . $page_logins, array( $this, 'woosocio_admin_styles' ) );
		add_action( 'admin_print_styles-' . $page_products, array( $this, 'woosocio_admin_styles' ) );
		remove_submenu_page( 'social-auto-post', 'social-auto-post') ;
		remove_submenu_page( 'social-auto-post', 'runimport') ;
	}

	/**
	 * woosocio_admin_styles function.
	 *
	 * @access public
	 * @return void
	 */			
	public function woosocio_admin_styles() {
       /*
        * It will be called only on plugin admin page, enqueue stylesheet here
        */
       wp_enqueue_style( 'woosocioStylesheet' );
   }

	/**
	 * FacebookSetting function.
	 *
	 * @access public
	 * @return void
	 */		
	public function FacebookSetting () {
		$filepath = $this->plugin_path.'woosocio.logins.php';
		if (file_exists($filepath))
			include_once($filepath);
		else
			die('Could not load file '.$filepath);
	}

	public function PostToSocialMedia () {
		$filepath = $this->plugin_path.'post-to-social-media.php';
		if (file_exists($filepath))
			include_once($filepath);
		else
			die('Could not load file '.$filepath);
	}
	
	public function GmailSetting () {
		$filepath = $this->plugin_path.'includes/gmail-setting.php';
		if (file_exists($filepath))
			include_once($filepath);
		else
			die('Could not load file '.$filepath);
	}

	public function TwitterSetting () {
		$filepath = $this->plugin_path.'includes/twitter-setting.php';
		if (file_exists($filepath))
			include_once($filepath);
		else
			die('Could not load file '.$filepath);
	}

	public function PostedOnSocialMedia()
	{
		$filepath = $this->plugin_path.'posted-on-social-media.php';
		if (file_exists($filepath))
			include_once($filepath);
		else
			die('Could not load file '.$filepath);
	}

	public function UploadFeed()
	{
		$filepath = $this->plugin_path.'upload-feed.php';
		if (file_exists($filepath))
			include_once($filepath);
		else
			die('Could not load file '.$filepath);
	}

	public function RunImport()
	{
		$filepath = $this->plugin_path.'run-import.php';
		if (file_exists($filepath))
			include_once($filepath);
		else
			die('Could not load file '.$filepath);
	}

	public function DeleteProduct()
	{
		$filepath = $this->plugin_path.'delete-post.php';
		if (file_exists($filepath))
			include_once($filepath);
		else
			die('Could not load file '.$filepath);
	}
	
	/**
	 * products_list function.
	 *
	 * @access public
	 * @return void
	 */		
	public function products_list () {
		
		?>
		<script type="text/javascript">
			url = '<?php echo add_query_arg( array('post_type' => 'product',
											   	   'list'	   => 'woosocio'), admin_url('edit.php')) ?>';
			window.location.replace(url);											   
		</script>
        <?php
		
    	/*wp_safe_redirect( add_query_arg( array('post_type' => 'product',
											   'list'	   => 'woosocio'), $url) );*/
		//wp_safe_redirect( 'www.yahoo.com' );

	}

	/**
	 * check connection function.
	 *
	 * @access public
	 */
	public function check_connection() {

 		try { 
			$this->fb_user_profile = $this->facebook->api('/me');
			$this->fb_user_pages = $this->facebook->api('/me/accounts');
		 	return $this->fb_user_profile;
		} catch (FacebookApiException $e) {
			return false;
		}
	}


	/**
	 * save facebook app id and secret function.
	 *
	 * @access public
	 */
	public function save_app_info() {
		update_option( 'fb_app_id', $_POST['fb_app_id'] );
		update_option( 'fb_app_secret', $_POST['fb_app_secret'] );
 	}

	/**
	 * update facebook page id function.
	 *
	 * @access public
	 */
	public function update_page_info() {
		$this->check_connection();
		$user_sign = $this->fb_user_profile['id'].'_fb_page_id';
		if(update_option( $user_sign, $_POST['fb_page_id'] ))
			_e( 'Page Info Updated!', 'woosocio');
		else
			_e( 'Unable to update page info! Please try again.', 'woosocio');
		die(0);
		//update_option( 'fb_app_secret', $_POST['fb_app_secret'] );
 	}
	
	/**
	 * load_localisation function.
	 *
	 * @access public
	 * @return void
	 */
	public function load_localisation () {
		$lang_dir = trailingslashit( str_replace( 'classes', 'lang', plugin_basename( dirname(__FILE__) ) ) );
		load_plugin_textdomain( 'woosocio', false, $lang_dir );
	} // End load_localisation()

	/**
	 * register_plugin_version function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'woosocio' . '-version', $this->version );
		}
	} // End register_plugin_version()

	public function PostToFacebook()
	{
		global $wpdb;
		$sql=mysql_query("select group_concat(entityid) as totalids from postedtosocialmedia where postedtofb=1 limit 0,1 ");
		if(mysql_num_rows($sql)>0)
		{
			$sqlresult=mysql_fetch_assoc($sql);
			$totalids=$sqlresult['totalids'];
			if($totalids=='' or is_null($totalids))
			{
				$productsql="select ID from ".$wpdb->prefix."posts where post_status='publish' and (post_type='product' or post_type='post') limit 0,2 ";
			}
			else
			{
				$productsql="select ID from ".$wpdb->prefix."posts where ID not in (".$totalids.") and  post_status='publish' and (post_type='product' or post_type='post') limit 0,2 ";
			}
		}
		else
		{
			$productsql="select ID from ".$wpdb->prefix."posts where  post_status='publish' and (post_type='product' or post_type='post') limit 0,2 ";
		}

		$runproductsql=mysql_query($productsql);
		if(mysql_num_rows($runproductsql))
		{
			while($runproductsqlresult=mysql_fetch_assoc($runproductsql))
			{
				$postid=$runproductsqlresult['ID'];
				$thumbid=get_post_meta($postid,'_thumbnail_id',true);
				if($thumbid!='')
				{
					$thumbnailpic=wp_get_attachment_thumb_url($thumbid);
				}
				else
				{
					$thumbnailpic='http://i.imgur.com/lHkOsiH.png';
				}
				//mail('shiv@indyainfotech.com',$postid." , ".$thumbid." , ".$thumbnailpic,$thumbnailpic);

				$postdetails=get_post($postid);
				$params = array(
					// this is the user long lived access token
					//"access_token" => "CAALD1C3ZBdn8BAGfzylkZB7OZAJvMI4WMWQ1RRVRZCvg4jgTnGgubnxDzw1FzZAlbzZAZAlDsaE9PpEq54AdDCoZCB0ThY2CGj6E23aCWYgpZAnpFmuZAYu67BGKEwp6ZBGqmdzmAVtZB2kTH9EhJZB4AN1buVQfFx7HUbAjBEXagErcCZBTwwF5H26dA8lXItdgObRkT5ZAHF9yjz2FqwSNmxDiu1t",
					// this is the page long lived access token, page on which we want to sent the data
					"access_token" => "CAALD1C3ZBdn8BAEduxXsukx56uVdUZCjCc0hbEKzjfcxAjMzsTyZBXcDAL7C1ZAxzaDsHtJ1xYFbmGliS6MVO6004cSLvNZCKwoA03IB9ZCWHIbLWfavYZBwCDSNuQ6xwpCV0Dgo0EiLnu1HUCx47ZCIXGiz8DvESP3eh0ZAEWQXJMwu6ZBx47fCNAfKoCsXnD2VoZD",
					"message" => $postdetails->post_title,
					"link" => get_permalink($postid),
					"picture" => $thumbnailpic,
					"name" => "More Deal UK",
					"caption" => "moredeal.co.uk",
					"description" => $postdetails->post_content,
				);

				try {
					$ret = $this->facebook->api('/760257880688282/feed', 'POST', $params);
					$check=mysql_query("select * from postedtosocialmedia where entityid='".$postid."' ");
					if(mysql_num_rows($check))
					{
						$in="update postedtosocialmedia set postedtofb='1', postedtofbdate='".date("Y-m-d")."' where entityid='".$postid."'  ";
						mysql_query($in);
					}
					else
					{
						$in="insert into postedtosocialmedia set entityid='".$postid."', postedtofb='1', postedtofbdate='".date("Y-m-d")."'  ";
						mysql_query($in);
					}
					echo "<br>".$postdetails->post_title." posted on facebook successfully";

				} catch(Exception $e) {
					echo $e->getMessage();
				}
			}
		}
	}

	public function PostToTwitter()
	{
		global $wpdb;
		$dir = dirname(plugin_dir_path(__FILE__));

		$file = fopen($dir."/twitter.txt","w");
		fwrite($file,'twitter time is '.time());
		fclose($file);

		require_once($dir.'/library/twitter/codebird.php');
		$ap_consumer_key=get_option('ap_consumer_key');
		$ap_consumer_secret=get_option('ap_consumer_secret');
		$ap_access_token=get_option('ap_access_token');
		$ap_access_token_secret=get_option('ap_access_token_secret');

		\Codebird\Codebird::setConsumerKey($ap_consumer_key,$ap_consumer_secret);
		$cb = \Codebird\Codebird::getInstance();
		$cb->setToken($ap_access_token,$ap_access_token_secret);
		
		$sql=mysql_query("select group_concat(entityid) as totalids from postedtosocialmedia where postedtotwitter=1 limit 0,1 ");
		if(mysql_num_rows($sql)>0)
		{
			$sqlresult=mysql_fetch_assoc($sql);
			$totalids=$sqlresult['totalids'];
			if($totalids=='' or is_null($totalids))
			{
				$productsql="select ID from ".$wpdb->prefix."posts where post_status='publish' and (post_type='product' or post_type='post') limit 0,2 ";
			}
			else
			{
				$productsql="select ID from ".$wpdb->prefix."posts where ID not in (".$totalids.") and  post_status='publish' and (post_type='product' or post_type='post') limit 0,2 ";
			}
		}
		else
		{
			$productsql="select ID from ".$wpdb->prefix."posts where  post_status='publish' and (post_type='product' or post_type='post') limit 0,2 ";
		}

		$runproductsql=mysql_query($productsql);
		if(mysql_num_rows($runproductsql))
		{
			while($runproductsqlresult=mysql_fetch_assoc($runproductsql))
			{
				$postid=$runproductsqlresult['ID'];
				$thumbid=get_post_meta($postid,'_thumbnail_id',true);
				if($thumbid!='')
				{
					$thumbnailpic=wp_get_attachment_thumb_url($thumbid);
				}
				else
				{
					$thumbnailpic='http://i.imgur.com/lHkOsiH.png';
				}

				$postdetails=get_post($postid);
				$params = array(
				  'status' => $postdetails->post_title,
				  'media[]' => $thumbnailpic
				);
				$reply = $cb->statuses_updateWithMedia($params);
				$check=mysql_query("select * from postedtosocialmedia where entityid='".$postid."' ");
				if(mysql_num_rows($check))
				{
					$in="update postedtosocialmedia set postedtotwitter='1', postedtotwitterdate='".date("Y-m-d")."' where entityid='".$postid."'  ";
					mysql_query($in);
				}
				else
				{
					$in="insert into postedtosocialmedia set entityid='".$postid."', postedtotwitter='1', postedtotwitterdate='".date("Y-m-d")."'  ";
					mysql_query($in);
				}
				echo "<br>".$postdetails->post_title." posted on twitter successfully";
			}
		}
	}

	public function fbgetaccesstoken()
	{
		echo $this->facebook->getAccessToken();
	}
} // End Class
?>