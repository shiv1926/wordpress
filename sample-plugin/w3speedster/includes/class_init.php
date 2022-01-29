<?php
namespace W3speedster;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class w3speedster{
    var $add_settings;
	var $settings;
	public function __construct(){
        if(!empty($_REQUEST['delete-wnw-cache'])){
            add_action('admin_init',array( $this, 'w3_remove_cache_files_hourly_event_callback') );
            add_action('admin_init',array( $this, 'w3_remove_cache_redirect') );
        }
		
		if(!empty($_POST['w3speedster-use-recommended-settings'])){
			
			$arr = (array)json_decode('{"license_key":"","w3_api_url":"","is_activated":"","optimization_on":"on","cdn":"","exclude_cdn":"","lbc":"on","gzip":"on","remquery":"on","lazy_load":"on","lazy_load_iframe":"on","lazy_load_video":"on","lazy_load_px":"200","webp_jpg":"on","webp_png":"on","webp_quality":"90","img_quality":"90","exclude_lazy_load":"base64\r\nlogo\r\nrev-slidebg\r\nno-lazy\r\nfacebook\r\ngoogletagmanager","exclude_pages_from_optimization":"wp-login.php\r\n\/cart\/\r\n\/checkout\/","cache_path":"","css":"on","load_critical_css":"on","exclude_css":"","force_lazyload_css":"","load_combined_css":"after_page_load","internal_css_delay_load":"10","google_fonts_delay_load":".2","exclude_page_from_load_combined_css":"","custom_css":"","js":"on","exclude_javascript":"","custom_javascript":"","exclude_inner_javascript":"google-analytics\r\nhbspt\r\n\/* <![CDATA[ *\/","force_lazy_load_inner_javascript":"googletagmanager\r\nconnect.facebook.net\r\nstatic.hotjar.com\r\njs.driftt.com","load_combined_js":"on_page_load","internal_js_delay_load":"10","exclude_page_from_load_combined_js":"","custom_js":""}');
			w3_update_option( 'w3_speedup_option', $arr );
		}
		$this->settings = w3_get_option( 'w3_speedup_option', true );
	
		if($this->settings == 1){
			add_action('admin_notices', array($this, 'w3_recommended_settings'));
		}
		
		$this->settings = !empty($this->settings) && is_array($this->settings) ? $this->settings : array();
        $this->add_settings = array();
        $this->add_settings['wp_home_url'] = rtrim(home_url(),'/');
		$this->add_settings['wp_site_url'] = rtrim(site_url(),'/');
		if(strpos($this->add_settings['wp_home_url'],'?') !== false){
			$home_url_arr = explode('?',$this->add_settings['wp_home_url']);
			$this->add_settings['wp_home_url'] = $home_url_arr[0];
		}
		$this->add_settings['secure'] =  (isset($this->add_settings['wp_home_url']) && strpos($this->add_settings['wp_home_url'],'https') !== false) ? 'https://' : 'http://';
        $this->add_settings['home_url'] = !empty($_SERVER['HTTP_HOST']) ? $this->add_settings['secure'].$_SERVER['HTTP_HOST'] : $this->add_settings['wp_home_url'];
        
        $this->add_settings['image_home_url'] = !empty($this->settings['cdn']) ? $this->settings['cdn'] : $this->add_settings['wp_site_url'];
        $this->add_settings['w3_api_url'] = !empty($this->settings['w3_api_url']) ? $this->settings['w3_api_url'] : 'https://cloud.w3speedster.com/optimize/';
		//$sitename = 'home';
        $this->add_settings['wp_content_path'] = WP_CONTENT_DIR;
        $wp_content_arr = explode('/',$this->add_settings['wp_content_path']);
        array_pop($wp_content_arr);
        $this->add_settings['document_root'] = rtrim(implode('/',$wp_content_arr),'/');
		$this->add_settings['wp_document_root'] =  $this->add_settings['document_root'];
        //$this->add_settings['wp_document_root'] = rtrim($this->add_settings['document_root'].'/'.trim(str_replace($this->add_settings['home_url'],'',$this->add_settings['wp_home_url']),'/'),'/');
		$this->add_settings['false_url'] = 0;
		if(!is_dir($this->add_settings['wp_document_root'])){
			$this->add_settings['false_url'] = 1;
			$this->add_settings['wp_document_root'] =  $this->add_settings['document_root'];
		}
		
        $this->add_settings['full_url'] = !empty($_SERVER['HTTP_HOST']) ? $this->add_settings['secure'] . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : $this->add_settings['home_url'].$_SERVER['REQUEST_URI'];
        
		$full_url_array = explode('?',$this->add_settings['full_url']);
        $this->add_settings['full_url_without_param'] = $full_url_array[0];
        $this->add_settings['wp_cache_path'] = (!empty($this->settings['cache_path']) ? $this->settings['cache_path'] : $this->add_settings['wp_content_path'].'/cache');
		$this->add_settings['root_cache_path'] = $this->add_settings['wp_cache_path'].'/w3-cache';
		$this->add_settings['critical_css_path'] = str_replace('/cache','',$this->add_settings['wp_cache_path']).'/critical-css';
        $this->add_settings['cache_path'] = str_replace($this->add_settings['wp_document_root'],'',$this->add_settings['root_cache_path']);
        $this->add_settings['cache_url'] = str_replace($this->add_settings['document_root'],$this->add_settings['wp_site_url'],$this->add_settings['root_cache_path']);
		$this->add_settings['upload_path'] = str_replace($this->add_settings['wp_document_root'],'',$this->add_settings['wp_content_path']);
		$upload_dir = wp_upload_dir();
		$this->add_settings['upload_base_url'] = $upload_dir['baseurl'];
		$this->add_settings['upload_base_dir'] = realpath($upload_dir['basedir']);
		$this->add_settings['theme_base_url'] = function_exists('get_theme_root_uri') ? get_theme_root_uri() : '';
		$this->add_settings['theme_base_dir'] = function_exists('get_theme_root') ? get_theme_root().'/' : '';
		
		$this->add_settings['webp_path'] = $this->add_settings['upload_path'].'/w3-webp';
        $useragent= @$_SERVER['HTTP_USER_AGENT'];
        $this->add_settings['is_mobile'] = function_exists('wp_is_mobile') ? wp_is_mobile() : 0 ;
        $this->add_settings['load_ext_js_before_internal_js'] = !empty($this->settings['load_external_before_internal']) ? explode("\r\n", $this->settings['load_external_before_internal']) : array();
        $this->add_settings['load_js_for_mobile_only'] = !empty($this->settings['load_js_for_mobile_only']) ? $this->settings['load_js_for_mobile_only'] : '';
		$this->add_settings['w3_rand_key'] = w3_get_option('w3_rand_key');
        if(!empty($this->add_settings['is_mobile']) && !empty($this->add_settings['load_js_for_mobile_only'])){
            $this->settings['load_combined_js'] = 'after_page_load';
        }
        if(!empty($this->settings['separate_cache_for_mobile']) && $this->add_settings['is_mobile']){
            $this->add_settings['css_ext'] = 'mob.css';
            $this->add_settings['js_ext'] = 'mob.js';
            $this->add_settings['preload_css'] = !empty($this->settings['preload_css_mobile']) ? explode("\r\n", $this->settings['preload_css_mobile']) : array();
        }else{
            $this->add_settings['css_ext'] = '.css';
            $this->add_settings['js_ext'] = '.js';
            $this->add_settings['preload_css']  = !empty($this->settings['preload_css']) ? explode("\r\n", $this->settings['preload_css']) : array();
        }
		$this->add_settings['preload_css_url'] = array();
		$this->add_settings['headers'] = function_exists('getallheaders') ? getallheaders() : array();
        $this->add_settings['main_css_url'] = array();
        $this->add_settings['lazy_load_js'] = array();
        $this->add_settings['exclude_cdn'] = !empty($this->settings['exclude_cdn']) ? explode(',',str_replace(' ','',$this->settings['exclude_cdn'])) : '';
		$this->add_settings['webp_enable'] = array();
		$this->add_settings['webp_enable_instance'] = array($this->add_settings['upload_path']);
		$this->add_settings['webp_enable_instance_replace'] = array($this->add_settings['webp_path']);
		$this->settings['webp_png'] = isset($this->settings['webp_png']) ? $this->settings['webp_png'] : '';
		$this->settings['webp_jpg'] = !empty($this->settings['webp_jpg']) ? $this->settings['webp_jpg'] : '';
		if(!empty($this->settings['webp_jpg'])){
			$this->add_settings['webp_enable'] = array_merge($this->add_settings['webp_enable'],array('.jpg','.jpeg'));
			$this->add_settings['webp_enable_instance'] = array_merge($this->add_settings['webp_enable_instance'],array('.jpg?','.jpeg?','.jpg ','.jpeg ','.jpg"','.jpeg"',".jpg'",".jpeg'"));
			$this->add_settings['webp_enable_instance_replace'] = array_merge($this->add_settings['webp_enable_instance_replace'],array('.jpgw3.webp?','.jpegw3.webp?','.jpgw3.webp ','.jpegw3.webp ','.jpgw3.webp"','.jpegw3.webp"',".jpgw3.webp'",".jpegw3.webp'"));
		}
		if(!empty($this->settings['webp_png'])){
			$this->add_settings['webp_enable'] = array_merge($this->add_settings['webp_enable'],array('.png'));
			$this->add_settings['webp_enable_instance'] = array_merge($this->add_settings['webp_enable_instance'],array('.png?','.png ','.png"',".png'"));
			$this->add_settings['webp_enable_instance_replace'] = array_merge($this->add_settings['webp_enable_instance_replace'],array('.pngw3.webp?','.pngw3.webp ','.pngw3.webp"',".pngw3.webp'"));
		}
		$this->add_settings['htaccess'] = 0;
		if(is_file($this->add_settings['wp_document_root']."/.htaccess")){
			$htaccess = file_get_contents($this->add_settings['wp_document_root']."/.htaccess");
			if(strpos($htaccess,'W3WEBP') !== false){
				$this->add_settings['htaccess'] = 1;
			}
		}
		$this->add_settings['critical_css'] = '';
		$this->add_settings['starttime'] = $this->microtime_float();
        if(!empty($_REQUEST['optimize_image'])){
            add_action('admin_init',array( $this, 'w3_optimize_image') );
        }
		if(!empty($this->settings['remquery'])){
			add_filter( 'style_loader_src',  array($this,'w3_remove_ver_css_js'), 9999, 2 );
			add_filter( 'script_loader_src', array($this,'w3_remove_ver_css_js'), 9999, 2 );
		}
		if(!empty($this->settings['image_home_url'])){
			$this->settings['image_home_url'] = rtrim($this->settings['image_home_url']);
		}
		if(!empty($this->settings['lazy_load'])){
			add_filter( 'wp_lazy_loading_enabled', '__return_false' );
		}
		$this->add_settings['fonts_api_links'] = array();
		$this->add_settings['fonts_api_links_css2'] = array();
		if(is_admin()){
			if(!is_file($this->add_settings['wp_document_root'].$this->add_settings['webp_path'].'/.htaccess')){
				$this->w3_check_if_folder_exists($this->add_settings['wp_document_root'].$this->add_settings['webp_path']);
				$this->w3_create_file($this->add_settings['wp_document_root'].$this->add_settings['webp_path'].'/.htaccess','Options -Indexes');
			}
			if(!is_file($this->add_settings['root_cache_path'].'/.htaccess')){
				$this->w3_check_if_folder_exists($this->add_settings['root_cache_path']);
				$this->w3_create_file($this->add_settings['root_cache_path'].'/.htaccess','Options -Indexes');
			}
			if(!is_file($this->add_settings['critical_css_path'].'/.htaccess')){
				$this->w3_check_if_folder_exists($this->add_settings['critical_css_path']);
				$this->w3_create_file($this->add_settings['critical_css_path'].'/.htaccess','Options -Indexes');
			}
		}
	}
	
	public function w3_header_check() {
        return is_admin()
			|| $this->isSpecialContentType()
	    	|| $this->isSpecialRoute()
	    	|| $_SERVER['REQUEST_METHOD'] === 'POST'
	    	|| $_SERVER['REQUEST_METHOD'] === 'PUT'
			|| $_SERVER['REQUEST_METHOD'] === 'DELETE';
	}
   private function isSpecialContentType() {
		if($this->w3_endswith($this->add_settings['full_url'],'.xml') || $this->w3_endswith($this->add_settings['full_url'],'.xsl')){
        	return true;
        }
		return false;
    }
    private function isSpecialRoute() {
		$current_url = $this->add_settings['full_url'];
		if( preg_match('/(.*\/wp\/v2\/.*)/', $current_url) ) {
			return true;
		}
		if( preg_match('/(.*wp-login.*)/', $current_url) ) {
			return true;
		}
		if( preg_match('/(.*wp-admin.*)/', $current_url) ) {
			return true;
		}
		return false;
    }
	
	function w3_recommended_settings(){
		echo '<div class="notice notice-info" id="w3speedster-setup-wizard-notice">';
			printf(
				'<p id="w3speedster-heading"><strong>%s</strong></p>',
				__('W3speedster Setup', 'w3speedster')
			);
		echo '<p><form method="post">';
		submit_button(
			__('Use Recommended Settings', 'w3speedster'),
			'primary',
			'w3speedster-use-recommended-settings',
			false,
			array(
				'id'       => 'w3speedster-sw-use-recommended-settings',
				'enabled' => 'enabled', 
			)
		);
		echo '</form></p></div>';
	}
	function w3_remove_ver_css_js( $src, $handle ){
		$src = remove_query_arg( 'ver', $src );
		return $src;
	}
	function w3_debug_time($html,$process){
		if(!empty($_REQUEST['w3_debug'])){
			$starttime = !empty($this->add_settings['starttime']) ? $this->add_settings['starttime'] : $this->microtime_float();
			$endtime = $this->microtime_float();
			return $html.$process.'-'.($endtime - $starttime);
		}
		return $html;
	}
	function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	function w3_str_replace_last( $search , $replace , $str ) {
		if( ( $pos = strrpos( $str , $search ) ) !== false ) {
			$search_length  = strlen( $search );
			$str    = substr_replace( $str , $replace , $pos , $search_length );
		}
		return $str;
	}
	function w3speedster_activate_license_key(){
		echo $this->w3speedster_validate_license_key();
		exit;
	}
	function w3speedster_validate_license_key($key=''){
		$key = !empty($_REQUEST['key']) ? $_REQUEST['key'] : $key;
		if(!empty($key)){
			$options = array(
			'method'      => 'GET',
			'timeout'     => 10,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(				
				'license_id'=>$key,
				'domain'=>base64_encode($this->add_settings['wp_home_url'])
			),
			'cookies'     => array()
			);
			$response = wp_remote_post($this->add_settings['w3_api_url'].'get_license_detail.php',$options);
			if(!is_wp_error( $response ) && !empty($response["body"])){
				$res_arr = json_decode($response["body"]);				
				if($res_arr[0] == 'success'){
					return json_encode(array('success','verified',$res_arr[1]));
				}else{
					return json_encode(array('fail','could not verify-1'.$response["body"]));
				}
			}else{
				return json_encode(array('fail','could not verify-2'));
			}
		}else{
			return json_encode(array('fail','could not verify-3'));
		}
	}
	function w3_parse_url($src){
		$pattern = '/(.*)'.str_replace(array('/','//'),array('\/','/'),str_replace($this->add_settings['secure'],'',rtrim($this->add_settings['wp_site_url'],'/'))).'(.*)/';
		$src = preg_replace($pattern, '$2', $src);
		$src = str_replace('//','/',$src);
		$src_arr = parse_url($src);
		return $src_arr;
	}
	function get_home_path() {
		$home    = set_url_scheme( get_option( 'home' ), 'http' );
		$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
		if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
			$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
			$pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
			$home_path           = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
			$home_path           = trailingslashit( $home_path );
		} else {
			$home_path = ABSPATH;
		}
	 
		return str_replace( '\\', '/', $home_path );
	}
    function w3_is_external($url) {
       $components = parse_url($url);
        return !empty($components['host']) && strcasecmp($components['host'], $_SERVER['HTTP_HOST']);
    }
    function w3_endswith($string, $test) {
        $str_arr = explode('?',$string);
        $string = $str_arr[0];
        $ext = '.'.pathinfo($str_arr[0], PATHINFO_EXTENSION);
        if($ext == $test)
            return true;
        else
            return false;
    }
	function w3_echo($text){
		if(!empty($_REQUEST['w3_preload_css'])){
			echo $text;
		}
	}
	function w3_print_r($text){
		if(!empty($_REQUEST['w3_preload_css'])){
			print_r($text);
		}
	}
	function w3_generate_preload_css(){
		if(empty($this->settings['optimization_on'])){
			return;
		}
		if(!empty($_REQUEST['key']) && $this->settings['license_key'] == $_REQUEST['key']){
			$this->w3_remove_critical_css_cache_files();
			$this->w3_rmdir($this->add_settings['wp_document_root'].$this->add_settings['webp_path']);
		}
		if(!empty($_REQUEST['url'])){
			$key_url = $_REQUEST['url'];
		}
		$preload_css = w3_get_option('w3speedup_preload_css');
		
		if(!empty($preload_css)){
			foreach($preload_css as $key => $url){
				if(!empty($key_url)){
					$key = $key_url; 
					$url = $preload_css[$key];
					$key_url = '';
				}
				$this->w3_echo('rocket'.$key.$url[0].$url[1]);
				if(empty($url[2])){
					unset($preload_css[$key]);
					continue;
				}
				$response = $this->w3_create_preload_css($key, $url[0], $url[2]);
				
				if(!empty($response) && $response === "exists"){
					unset($preload_css[$key]);
					w3_update_option('w3speedup_preload_css_total',(int)w3_get_option('w3speedup_preload_css_total')-1,'no');
					continue;
				}
				if(!empty($response) && $response === "hold"){
					$this->w3_echo('rocket5'.$response);
					break;
				}
				if($response || $preload_css[$key][1] == 1){
					$this->w3_echo('rocket4'.$response);
					unset($preload_css[$key]);
				}else{
					$this->w3_echo('rocket6');
					$preload_css[$key][1] = 1;
				}
				break;
			}
		}
		w3_update_option('w3speedup_preload_css',$preload_css,'no');
	}
	
	function w3_get_html_cache_path($url){
		if(function_exists('is_plugin_active') && is_plugin_active('wp-fastest-cache/wpFastestCache.php')){
			$path = $this->add_settings['wp_content_path'].'/cache/all/'.trim(str_replace($this->add_settings['wp_site_url'],'',$url),'/').'/index.html';
			if(is_file($path)){
				return $path;
			}
			return false;
		}
		return false;
	}
	
	function w3_delete_html_cache_after_preload_css($url){
		if($path = $this->w3_get_html_cache_path($url)){
			@unlink($path);
		}
		
	}
	function w3_css_compress_init( $minify ){
    	$minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );
        $minify = str_replace( array("\r\n", "\r", "\n", "\t",'  ','    ', '    '), ' ', $minify );
    	return $minify;
    }
	function w3_create_preload_css($url,$filename, $css_path){
		if(!empty($_REQUEST['key']) && $this->settings['license_key'] == $_REQUEST['key']){
			$this->w3_remove_critical_css_cache_files();
		}
		$this->w3_echo('rocket2'.$filename.$url);
		$this->w3_echo('rocket3'.$css_path);
		if(is_file($css_path.'/'.$filename)){
			$this->w3_echo('rocket9');
			w3_update_option('w3speedup_preload_css_created',(int)w3_get_option('w3speedup_preload_css_created')+1,'no');
			return 'exists';
		}
		$nonce = wp_create_nonce("purge_critical_css");
		w3_update_option('purge_critical_css',$nonce);
		if($this->add_settings['home_url'] != $this->add_settings['image_home_url']){
			$css_urls = $this->add_settings['home_url'].','.$this->add_settings['image_home_url'];
		}else{
			$css_urls = $this->add_settings['home_url'];
		}
		/*$options = array(
			'method'      => 'GET',
			'timeout'     => 10,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'cookies'     => array()
			);
		if($path = $this->w3_get_html_cache_path($url)){
			$url_html = wp_remote_post($path,$options);
		}else{
			$url_html = wp_remote_post($url,$options);
		}
		if ( !is_wp_error( $url_html ) ) {
			$url_html = $url_html['body'];		
		}else{
			$url_html = '';		
		}*/
		$url_html = '';
		$options = array(
			'method'      => 'POST',
			'timeout'     => 10,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(
				'url' => $url,
				'key' => $this->settings['license_key'],
				'_wpnonce'	=> $nonce,
				'filename' => $filename,
				'css_url' => $css_urls,
				'path' => $css_path,
				'html' => $url_html
			),
			'cookies'     => array()
			);
			$options1 = $options;
			
		$response = wp_remote_post($this->add_settings['w3_api_url'].'/css',$options);
		$options1['body']['html'] = '';
		$this->w3_echo('<pre>'); $this->w3_print_r($options1);
		if( !is_wp_error( $response ) ) {
			$this->w3_echo('rocket3'.$css_path.'/'.$filename);
			$this->w3_echo($response['body']);
			if(!empty($response['body'])){
				$response_arr = (array)json_decode($response['body']);
				if(!empty($response_arr['result']) && $response_arr['result'] == 'success'){
					$this->w3_create_file($css_path.'/'.$filename, $response_arr['w3_css']);
					$preload_css = w3_get_option('w3speedup_preload_css');
					unset($preload_css[$response_arr['url']]);
					w3_update_option('w3speedup_preload_css',$preload_css,'no');
					w3_update_option('w3speedup_preload_css_created',(int)w3_get_option('w3speedup_preload_css_created')+1,'no');
					if(is_file($file = $this->w3_get_full_url_cache_path($url).'/main_css.json')){
						unlink($file);
					}
					$this->w3_delete_html_cache_after_preload_css($url);
					return true;
				}elseif(!empty($response_arr['error'])){
					if($response_arr['error'] == 'process already running'){
						return 'hold';
					}else{
						$this->w3_echo('rocket-error'.$response_arr['error']);
						w3_update_option('w3speedup_critical_css_error',$response_arr['error'],'no');
						return false;
					}
				}
				$this->w3_echo('rocket7');
				return false;
			}else{
				$this->w3_echo('rocket8');
				return false;
			}
		}else{
			return false;
		}
		
	}
	
	function w3_preload_css_path($url=''){
		$url = empty($url) ? $this->add_settings['full_url_without_param'] : $url;
		if(!empty($this->add_settings['preload_css_url'][$url])){
			return $this->add_settings['preload_css_url'][$url];
		}
		if(rtrim($url,'/') == rtrim($this->add_settings['wp_home_url'],'/')){
			
		}else{
			global $wp_post_types;
			if(function_exists("w3_create_separate_critical_css_of_post_type")){
				$separate_post_css = w3_create_separate_critical_css_of_post_type();
			}else{
				$separate_post_css = array('page');
			}
			if(function_exists("w3_create_separate_critical_css_of_category")){
				$separate_cat_css = w3_create_separate_critical_css_of_category();
			}else{
				$separate_cat_css = array('category');
			}
			
			$url_path_arr = explode('/',rtrim($url,'/'));
			$url_path = array_pop($url_path_arr);
			
			if(!is_page() && (is_single() || is_singular())){
				global $post;
				if(!in_array($post->post_type,$separate_post_css)){
					$url = rtrim($this->add_settings['wp_home_url'],'/').'/post/'.$post->post_type;
				}
			}
			if(is_404()){
				$url = rtrim($this->add_settings['wp_home_url'],'/').'/'.'w3404';
			}
			if(is_search() || is_page('search')){
				$url = rtrim($this->add_settings['wp_home_url'],'/').'/'.'w3search';
			}
			if(is_archive() || is_category()){
				$cat_id = get_queried_object();
				if(!empty($cat_id->taxonomy) && !in_array($cat_id->taxonomy,$separate_cat_css)){
					$url = rtrim($this->add_settings['wp_home_url'],'/').'/'.$cat_id->taxonomy;
				}
			}
			if(is_author()){
				$url = rtrim($this->add_settings['wp_home_url'],'/').'/'.'author';
			}
		}
		global $page;
		if($page > 1) {
			
			$url_arr = explode('/page/',$url);
			$url = $url_arr[0];
		}
		$full_url = str_replace($this->add_settings['secure'],'',rtrim($url,'/'));
		$path = urldecode($this->w3_get_critical_cache_path($full_url));
		$this->add_settings['preload_css_url'][$url] = $path;
		return $path;
	}
	function w3_put_preload_css(){
		if ( !isset( $_REQUEST['_wpnonce'] ) || $_REQUEST['_wpnonce'] != w3_get_option('purge_critical_css')) {
			echo 'Request not valid'; exit;
		}
		if(!empty($_REQUEST['url']) && !empty($_REQUEST['filename']) && !empty($_REQUEST['w3_css'])){
			$url = $_REQUEST['url'];
			$preload_css = w3_get_option('w3speedup_preload_css');
			echo $path = !empty($preload_css[$_REQUEST['filename']][2]) ? $preload_css[$_REQUEST['filename']][2] : $_REQUEST['path'];
			$this->w3_create_file($path.'/'.$_REQUEST['filename'], stripslashes($_REQUEST['w3_css']));
			unset($preload_css[$_REQUEST['url']]);
			w3_update_option('w3speedup_preload_css',$preload_css,'no');
			if(is_file($file = $this->w3_get_full_url_cache_path($url).'/main_css.json')){
				unlink($file);
			}
			$this->w3_delete_html_cache_after_preload_css($url);
			echo 'saved';
		}
		echo false;
		exit;
	}
    function w3_create_file($path, $text = '//'){
        $realpath = urldecode($path);
        $file = fopen($realpath,'w');
        fwrite($file,$text);
        fclose($file);
        chmod($realpath, 0644); 
        return true;
    }
	function w3_parse_script($tag,$link){
        $data_exists = strpos($link,'>');
        if(!empty($data_exists)){
            $end_tag_pointer = strpos($link,'</script>',$data_exists);
            $link_arr = substr($link, $data_exists+1, $end_tag_pointer-$data_exists-1);
        }
        return $link_arr;
   }
    function w3_parse_link($tag,$link){
        $xmlDoc = new \DOMDocument();
        if (@$xmlDoc->loadHTML($link) === false){
            return array();
        }
        $tag_html = $xmlDoc->getElementsByTagName($tag);
        $link_arr = array();
        if(!empty($tag_html[0])){
            foreach ($tag_html[0]->attributes as $attr) {
                $link_arr[$attr->nodeName] = $attr->nodeValue;
            }
        }
		if(strpos($link,'><') === false){
			$link_arr['html'] = $this->w3_parse_script($tag, $link);
		}
        return $link_arr;
    }
    function w3_implode_link_array($tag,$array){
        $link = '<'.$tag.' ';
		$html = '';
		if(!empty($array['html'])){
			$html = $array['html'];
			unset($array['html']);
		}
        foreach($array as $key => $arr){
            if($key != 'html'){
				$link .= $key."=\"".$arr."\" ";
			}
        }
        if($tag == 'script'){
            $link .= '>'.$html.'</script>';
        }elseif($tag == 'iframe'){
            $link .= '>'.$html.'</iframe>';
        }else{
            $link .= '/>';
        }
        return $link;
    }
    function w3_insert_content_head_in_json(){
        global $insert_content_head;
		if($this->add_settings['full_url'] == $this->add_settings['full_url_without_param']){
			$file = $this->w3_get_full_url_cache_path().'/content_head.json';
			$this->w3_create_file($file,json_encode($insert_content_head));
		}
    }
    
    function w3_insert_content_head($html, $content, $pos){
        global $insert_content_head;
        $insert_content_head[] = array($content,$pos);
        if($pos == 1){
    
            $html = preg_replace('/<style/',  $content.'<style', $html, 1);
    
        }elseif($pos == 2){
    
            $html = preg_replace('/<link(.*)href="([^"]*)"(.*)>/',$content.'<link$1href="$2"$3>',$html,1);
    
        }elseif($pos == 3){
			$html = preg_replace('/<head([^<]*)>/','<head$1>'.$content,$html,1);
		}elseif($pos == 4){
			$html = preg_replace('/<\/head(\s*)>/',$content.'</head$1>',$html,1);
		}elseif($pos == 5){
			$html = preg_replace($content,'',$html,1);
		}else{
    
            $html = preg_replace('/<script/',  $content.'<script', $html, 1);
    
        }
    
        return $html;
    
    }
    function w3_main_css_url_to_json(){
        global $main_css_url;
        if(empty($main_css_url)){
            $main_css_url = array();
        }
		if($this->add_settings['full_url'] == $this->add_settings['full_url_without_param']){
			$file = $this->w3_get_full_url_cache_path().'/main_css.json';
			$this->w3_create_file($file,json_encode($main_css_url));
		}
    }
    function w3_internal_js_to_json(){
        global $internal_js;
        if(empty($internal_js)){
            $internal_js = array();
        }
		if($this->add_settings['full_url'] == $this->add_settings['full_url_without_param']){
			$file = $this->w3_get_full_url_cache_path().'/main_js.json';
			$this->w3_create_file($file,json_encode($internal_js));
		}
    }
    function w3_str_replace_set($str,$rep){
        global $str_replace_str_array, $str_replace_rep_array;
        $str_replace_str_array[] = $str;
        $str_replace_rep_array[] = $rep;
        //echo '<pre>'; print_r($this->str_replace_str_array);
    }
    
    function w3_str_replace_set_img($str,$rep){
        global $str_replace_str_img, $str_replace_rep_img;
        $str_replace_str_img[] = $str;
        $str_replace_rep_img[] = $rep;
    }
    function w3_str_replace_bulk_img($html){
        global $str_replace_str_img, $str_replace_rep_img;
		if($this->add_settings['full_url'] == $this->add_settings['full_url_without_param']){
			$this->w3_create_file($this->w3_get_full_url_cache_path().'/img.json',json_encode(array($str_replace_str_img,$str_replace_rep_img)));
		}
        $html = str_replace($str_replace_str_img,$str_replace_rep_img,$html);
        return $html;
    }
    function w3_str_replace_set_js($str,$rep){
        global $str_replace_str_js, $str_replace_rep_js;
        $str_replace_str_js[] = $str;
        $str_replace_rep_js[] = $rep;
    }
    function w3_str_replace_bulk_js($html){
        global $str_replace_str_js, $str_replace_rep_js;
		if($this->add_settings['full_url'] == $this->add_settings['full_url_without_param']){
			$this->w3_create_file($this->w3_get_full_url_cache_path().'/js.json',json_encode(array($str_replace_str_js,$str_replace_rep_js)));
		}
        $html = str_replace($str_replace_str_js,$str_replace_rep_js,$html);
        return $html;
    }
    
    function w3_str_replace_bulk_json($html,$str=array(), $rep=array()){
        if(!empty($rep['php'])){
            $rep['php'] = '<style>'.file_get_contents($rep['php']).'</style>';
        }
        $html = str_replace($str,$rep,$html);
        return $html;
    }
    
    function w3_str_replace_set_css($str,$rep,$key=''){
        global $str_replace_str_css, $str_replace_rep_css;
        if($key){
            $str_replace_str_css[$key] = $str;
            $str_replace_rep_css[$key] = $rep;
        }else{
            $str_replace_str_css[] = $str;
            $str_replace_rep_css[] = $rep;
        }
    }
    function w3_str_replace_bulk_css($html){
        global $str_replace_str_css, $str_replace_rep_css;
		if($this->add_settings['full_url'] == $this->add_settings['full_url_without_param']){
			$this->w3_create_file($this->w3_get_full_url_cache_path().'/css.json',json_encode(array($str_replace_str_css,$str_replace_rep_css)));
		}
        if(!empty($str_replace_rep_css['php'])){
            $str_replace_rep_css['php'] = '<style>'.file_get_contents($str_replace_rep_css['php']).'</style>';
        }
        $html = str_replace($str_replace_str_css,$str_replace_rep_css,$html);
        return $html;
    }
    function w3_str_replace_bulk($html){
        global $str_replace_str_array, $str_replace_rep_array;
        $html = str_replace($str_replace_str_array,$str_replace_rep_array,$html);
        return $html;
    }
    
    function w3_get_wp_cache_path($path=''){
        $cache_path = $this->add_settings['wp_cache_path'].(!empty($path) ? '/'.$path : '');
        $this->w3_check_if_folder_exists($cache_path);
        return $cache_path;
    }
	function w3_get_cache_path($path=''){
        $cache_path = $this->add_settings['root_cache_path'].(!empty($path) ? '/'.$path : '');
        $this->w3_check_if_folder_exists($cache_path);
        return $cache_path;
    }
	function w3_get_critical_cache_path($path=''){
        $cache_path = $this->add_settings['critical_css_path'].(!empty($path) ? '/'.$path : '');
        $this->w3_check_if_folder_exists($cache_path);
        return $cache_path;
    }
	
	function w3_get_full_url_cache_path($full_url=''){
		$cache_path = $this->w3_check_full_url_cache_path($full_url);
        $this->w3_check_if_folder_exists($cache_path);
        return $cache_path;
    }
	function w3_check_full_url_cache_path($full_url=''){
		$full_url = !empty($full_url) ? $full_url : $this->add_settings['full_url'];
        $url_array = parse_url($full_url);
		$query = !empty($url_array['query']) ? '/?'.$url_array['query'] : '';
        $full_url_arr = explode('/',trim($url_array['path'],'/').$query);
        $cache_path = $this->w3_get_cache_path('all');
        foreach($full_url_arr as $path){
            $cache_path .= '/'.md5($path);
        }
        if(!empty($this->settings['separate_cache_for_mobile']) && !empty($this->add_settings['is_mobile'])){
			$cache_path .= '/mob';
		}
		return $cache_path;
	}
    function w3_check_if_folder_exists($path){
        $realpath = urldecode($path);
        if(is_dir($realpath)){
			return $path;
		}
		try {
			mkdir($realpath,0755,true); 
		}
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();
		}
        return $path;
    }
    function get_curl_url($url){
        if(!function_exists('curl_init')){
			return file_get_contents($url);
		}
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response; 
    }
    
    function optimize_image($width,$url,$is_webp = false){
		$key = $this->settings['license_key'];
		$key_activated = $this->settings['is_activated'];
		if(empty($key) || empty($key_activated)){
			return "License key not activated.";
		}
        $width = $width < 1920 ? $width : 1920;
        if($is_webp){
			$q = !empty($this->settings['webp_quality']) ? $this->settings['webp_quality'] : '';
			return $this->get_curl_url($this->add_settings['w3_api_url'].'basic.php?key='.$key.'&width='.$width.'&q='.$q.'&url='.urlencode($url).'&webp=1');
		}else{
			$q = !empty($this->settings['img_quality']) ? $this->settings['img_quality'] : '';
			return $this->get_curl_url($this->add_settings['w3_api_url'].'basic.php?key='.$key.'&width='.$width.'&q='.$q.'&url='.urlencode($url));
		}
    }
    function w3_combine_google_fonts($full_css_url){
        if(empty($this->settings['google_fonts'])){		
            return false;
        }
        
		$url_arr = parse_url(str_replace('#038;','&',$full_css_url));
		if(strpos($url_arr['path'],'css2') !== false){
			$query_arr = explode('&',$url_arr['query']);
			if(!empty($query_arr) && count($query_arr) > 0){
				foreach($query_arr as $family){
					if(strpos($family,'family') !== false){
						$this->add_settings['fonts_api_links_css2'][] = $family;
					}
				}
				return true;
			}
			return false;
		
		}elseif(!empty($url_arr['query'])){
			parse_str($url_arr['query'], $get_array);
			if(!empty($get_array['family'])){
				$font_array = explode('|',$get_array['family']);
				foreach($font_array as $font){
					
					if(!empty($font)){
						$font_split = explode(':',$font);
							
						if(empty($font_split[0])){
							continue;
						}
						if(empty($this->add_settings['fonts_api_links'][$font_split[0]]) || !is_array($this->add_settings['fonts_api_links'][$font_split[0]])){
							$this->add_settings['fonts_api_links'][$font_split[0]] = array();
						}
						$this->add_settings['fonts_api_links'][$font_split[0]] = !empty($font_split[1]) ? array_merge($this->add_settings['fonts_api_links'][$font_split[0]],explode(',',$font_split[1])) : $this->add_settings['fonts_api_links'][$font_split[0]];
					}
				}
				return true;
			}
			return false;
		}
		return false;
    }
    function w3_get_tags_data_html($data,$start_tag,$end_tag){
        $data_exists = 0; $i=0;
        $tag_char_len = strlen($start_tag);
        $end_tag_char_len = strlen($end_tag);
        $script_array = array();
        while($data_exists != -1 && $i<500) {
            $data_exists = strpos($data,$start_tag,$data_exists);
            if(!empty($data_exists)){
                $end_tag_pointer = strpos($data,$end_tag,$data_exists);
                $script_array[] = substr($data, $data_exists, $end_tag_pointer-$data_exists+$end_tag_char_len);
                $data_exists = $end_tag_pointer;
            }else{
                $data_exists = -1;
            }
            $i++;
        }
        return $script_array;
    }
	function w3_get_tags_data($data,$start_tag,$end_tag){
        $data_exists = 0; $i=0;
        $tag_char_len = strlen($start_tag);
        $end_tag_char_len = strlen($end_tag);
        $script_array = array();
        while($data_exists != -1 && $i<500) {
            $data_exists = strpos($data,$start_tag,$data_exists);
            if(!empty($data_exists)){
                $end_tag_pointer = strpos($data,$end_tag,$data_exists);
                $script_array[] = substr($data, $data_exists, $end_tag_pointer-$data_exists+$end_tag_char_len);
                $data_exists = $end_tag_pointer;
            }else{
                $data_exists = -1;
            }
            $i++;
        }
        return $script_array;
    }
    private function w3_cache_rmdir($dir) {
		if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object){
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir" && $object != 'critical-css'){
                        $this->w3_cache_rmdir($dir."/".$object);
                    }else{
                      @unlink($dir."/".$object);
                    }
                }
            }
            if(is_array($objects))	reset($objects);
            @unlink($dir);
        }
	}
	function w3_rmfiles($dir) {
        //echo $dir; exit;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object){
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) != "dir"){
                      @unlink($dir."/".$object);  
                    }
                }
            }
            reset($objects);
        }
    }
	private function w3_rmdir($dir) {
        //echo $dir; exit;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object){
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir"){
                        $this->w3_rmdir($dir."/".$object);
                    }else{
                      @unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            @unlink($dir);
        }
    }
    function w3_remove_cache_files_hourly_event_callback() {
		if (function_exists('exec')) {
           exec('rm -r '.$this->w3_get_cache_path(),$output, $retval);
        }
		$this->w3_cache_rmdir($this->w3_get_cache_path());
        $this->w3_create_random_key();
        return $this->w3_cache_size_callback();
    
    }
	function w3_remove_critical_css_cache_files() {
		w3_update_option('critical_css_delete_time',date('d:m:Y::h:i:sa').json_encode($_REQUEST),'no');
        $this->w3_rmdir($this->w3_get_critical_cache_path());
		$this->w3_delete_server_cache();
		w3_update_option('w3speedup_preload_css','','no');
		w3_update_option('w3speedup_preload_css_total',0,'no');
		w3_update_option('w3speedup_preload_css_created',0,'no');
        return true;
    
    }
    function w3_delete_server_cache(){
		$options = array(
			'method'      => 'POST',
			'timeout'     => 10,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(
				'url' => $this->add_settings['wp_home_url'],
				'key' => $this->settings['license_key'],
				'_wpnonce'	=> $nonce
			),
			'cookies'     => array()
			);
			
		$response = wp_remote_post($this->add_settings['w3_api_url'].'/css/delete-css.php',$options);
		if( !is_wp_error( $response ) ) {
			return true;
		}else{
			return false;
		}
	}
    function w3_remove_cache_redirect(){
        header("Location:".add_query_arg(array('delete_wp_speedup_cache'=>1),remove_query_arg('delete-wnw-cache',false)));
        exit;
    }
    function w3_optimize_image(){
        $image_url = $_REQUEST['url'];
        $image_width = !empty($_REQUEST['width']) ? $_REQUEST['width'] : '';
        $url_array = parse_url($image_url);
        $image_size = !empty($image_width) ? array($image_width) : getimagesize($document_root.$url_array['path']);
        $optmize_image = optimize_image($image_size[0],$image_url);
        $optimize_image_size = @imagecreatefromstring($optmize_image);
        if(empty($optimize_image_size)){
            echo 'invalid image'; exit;
        }else{    
            $image_type = array('gif','jpg','png','jpeg');
            $type = explode('.',$image_url);
            $type = array_reverse($type);
            if(in_array($type[0],$image_type)){
                rename($document_root.$url_array['path'],$document_root.$url_array['path'].'org.'.$type[0]);
                file_put_contents($document_root.$url_array['path'],$optmize_image);
                chmod($document_root.$url_array['path'], 0775);
                echo $document_root.$url_array['path'];
            }
        }
        exit;
    }
    function w3_setAllLinks($data,$resources=array()){
        $resource_arr = array();
        $comment_tag = $this->w3_get_tags_data($data,'<!--','-->');
        $new_comment_tag = array();
        foreach($comment_tag as $key => $comment){
        	if(strpos($comment,'</script>') !== false || strpos($comment,'<link') !== false){
            	$new_comment_tag[] = $comment;
            }
        }
		$noscript_tag = $this->w3_get_tags_data($data,'<noscript>','</noscript>');
        $data = str_replace(array_merge($new_comment_tag,$noscript_tag),'',$data);
		$scripts = $this->w3_get_tags_data($data,'<script','</script>');
        $data = str_replace($scripts,'',$data);
        
		$data = str_replace($comment_tag,'',$data);
        if(!empty($this->settings['js']) && in_array('script',$resources)){
            $resource_arr['script'] = $scripts;
        }
        
        if( in_array('img',$resources) ){
            $resource_arr['img'] = $this->w3_get_tags_data($data,'<img','>');
		}
        if(!empty($this->settings['css']) && in_array('link',$resources) ){
            $resource_arr['link'] = $this->w3_get_tags_data(str_replace('<style','<link',$data),'<link','>');
			if(!empty($this->settings['load_style_tag_in_head'])){
				$resource_arr['style']  = $this->w3_get_tags_data($data,'<style','</style>');
			}
		}
        
        if(in_array('iframe',$resources)){
            $resource_arr['iframe'] = $this->w3_get_tags_data($data,'<iframe','</iframe>');
        }else{
			$resource_arr['iframe'] = array();
		}
        if(in_array('video',$resources)){
            $resource_arr['video'] = $this->w3_get_tags_data($data,'<video','</video>');
        }else{
			$resource_arr['video'] = array();
		}
		if(in_array('audio',$resources)){
            $resource_arr['audio'] = $this->w3_get_tags_data($data,'<audio','</audio>');
        }else{
			$resource_arr['audio'] = array();
		}
		if(in_array('url',$resources)){
            $resource_arr['url'] = $this->w3_get_tags_data($data,'url(',')');
        }
        return $resource_arr;
    }
    function w3_get_cache_file_size(){
        $dir = $this->w3_get_cache_path();
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->w3_folderSize($each);
        }
        return ($size / 1024) / 1024;
    }
        
    function w3_foldersize($path) {
        $total_size = 0;
		if(is_dir($path)){
			$files = scandir($path);
			$cleanPath = rtrim($path, '/'). '/';
			foreach($files as $t) {
				if ($t<>"." && $t<>"..") {
					$currentFile = $cleanPath . $t;
					if (is_dir($currentFile)) {
						$size = $this->w3_foldersize($currentFile);
						$total_size += $size;
					}
					else {
						$size = filesize($currentFile);
						$total_size += $size;
					}
				}   
			}
		}
		return $total_size;
    }
    function w3_cache_size_callback() {
        $filesize = $this->w3_get_cache_file_size();
        w3_update_option('w3_speedup_filesize',$filesize,true);
        return $filesize;
    }
    function w3_create_random_key(){
        w3_update_option('w3_rand_key',rand(10,1000),false);
    }
    
    function w3_get_pointer_to_inject_files($html){
        global $appendonstyle;
        if(!empty($appendonstyle)){
            return $appendonstyle;
        }
        $start_body_pointer = strpos($html,'<body');
        $start_body_pointer = $start_body_pointer ? $start_body_pointer : strpos($html,'</head');
        $head_html = substr($html,0,$start_body_pointer);
        $comment_tag = $this->w3_get_tags_data($head_html,'<!--','-->');
        foreach($comment_tag as $comment){
            $head_html = str_replace($comment,'',$head_html);
        }
        
        if(strpos($head_html,'<style') !== false){
            $appendonstyle=1;
        }elseif(strpos($head_html,'<link') !== false){
            $appendonstyle=2;
        }else{
            $appendonstyle=3;
        }
        return $appendonstyle;
    }
    function w3_check_if_page_excluded($exclude_setting){
        
        $e_p_from_optimization = !empty($exclude_setting) ? explode("\r\n",$exclude_setting) : array();
        
        if(!empty($e_p_from_optimization)){
            foreach( $e_p_from_optimization as $e_page ){
				if(empty($e_page)){
					continue;
				}
                if(empty($_REQUEST['testing']) && (is_home() || is_front_page()) && $this->add_settings['wp_home_url'] == $e_page){
                    return true;
                }else if($this->add_settings['wp_home_url'] != $e_page){
                    if(strpos($this->add_settings['full_url'], $e_page)!==false){
                        return true;
                    }
                }
            }			
        }
        return false;
    }
	public function w3_is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->w3_is_plugin_active_for_network( $plugin );
	}
	
	public function w3_is_plugin_active_for_network( $plugin ) {
		if ( !is_multisite() )
			return false;
		$plugins = get_site_option( 'active_sitewide_plugins');
		if ( isset($plugins[$plugin]) )
			return true;
		return false;
	}
	function w3_check_super_cache($path, $htaccess){
		if($this->w3_is_plugin_active('wp-super-cache/wp-cache.php')){
			return array("WP Super Cache needs to be deactive", "error");
		}else{
			@unlink($path."wp-content/wp-cache-config.php");
			$message = "";
			
			if(is_file($path."wp-content/wp-cache-config.php")){
				$message .= "<br>- be sure that you removed /wp-content/wp-cache-config.php";
			}
			if(preg_match("/supercache/", $htaccess)){
				$message .= "<br>- be sure that you removed the rules of super cache from the .htaccess";
			}
			return $message ? array("WP Super Cache cannot remove its own remnants so please follow the steps below".$message, "error") : "";
		}
		return "";
	}
	function w3_preload_resources(){		
		$preload_html = '';
		$preload_resources = !empty($this->settings['preload_resources']) ? explode("\r\n", $this->settings['preload_resources']) : array();
		if(!empty($preload_resources)){
			
			foreach($preload_resources as $link){
				$link_arr = explode('?',$link);
				$extension = explode(".", $link_arr[0]);
				$extension = end($extension);
				if(empty($extension)){
					continue;
				}
				if(in_array($extension, array('jpeg','jpg','png','gif','webp','tiff', 'psd', 'raw', 'bmp', 'heif', 'indd'))){
					$preload_html .= '<link rel="preload" href="'.$link.'" as="image"/>';
				}
				if(in_array(strtolower($extension), array('otf','ttf','woff','woff2','gtf','mmm', 'pea', 'tpf', 'ttc', 'wtf'))){
					$preload_html .= '<link rel="preload" href="'.$link.'" as="font" type="font/'.$extension.'" crossorigin>';
				}
				
				if(in_array($extension, array('mp4','webm'))){
					$preload_html .= '<link rel="preload" href="'.$link.'" as="video" type="video/'.$extension.'">';
				}
				if($extension == 'css'){
					$preload_html .= '<link rel="preload" href="'.$link.'" as="style">';
				}
				if($extension == 'js'){
					$preload_html .= '<link rel="preload" href="'.$link.'" as="script">';
				}				
			}
		}
		return $preload_html;
	}
}