<?php
global $woosocio, $is_IE;
if(isset($_GET['action']) && $_GET['action'] === 'logout'){
  $woosocio -> facebook -> destroySession();
}
$fb_user = $woosocio -> facebook -> getUser();
/*echo "<pre>";
print_r($fb_user);
print_r($_SESSION);
echo "</pre>";*/
// Login or logout url will be needed depending on current user state.
if ($fb_user) {
  $next_url = array( 'next' => admin_url().'admin.php?page=facebook&logout=yes&action=logout' );
  $logoutUrl = $woosocio -> facebook -> getLogoutUrl( $next_url );
  $user_profile = $woosocio -> facebook -> api('/me');
  $user_pages = $woosocio -> facebook -> api("/me/accounts");
} else {
  $statusUrl = $woosocio->facebook->getLoginStatusUrl();
  $loginUrl = $woosocio->facebook->getLoginUrl(array('scope' => 'publish_stream, manage_pages, publish_actions'));
}
?>
  <div class="woosocio_wrap">
    <h3>Facebbok Settings</h3>
    <?php 
    if ($is_IE){
      echo "<p style='font-size:18px; color:#F00;'>" . __( 'Important Notice:', 'woosocio') . "</p>";
      echo "<p style='font-size:16px; color:#F00;'>" . 
      __( 'You are using Internet Explorer. This plugin may not work properly with IE. Please use any other browser.', 'woosocio') . "</p>";
      echo "<p style='font-size:16px; color:#F00;'>" . __( 'Recommended: Google Chrome.', 'woosocio') . "</p>";
    }
    ?>
    <div id="woosocio-services-block">
      <div class="woosocio-service-right">
        <?php if($fb_user!==0):?>
          <?php _e( 'Connected as:', 'woosocio') ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <a class="woosocio-profile-link" href="https://www.facebook.com" target="_top"><?php echo $user_profile['name'] ?></a><br>
          <a id="pub-disconnect-button1" class="woosocio-add-connection button" href="<?php echo $logoutUrl; ?>" target="_top"><?php _e('Disconnect', 'woosocio')?></a><br>
        <?php else: ?>
          <!--Not Connected...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
          <a id="facebook" class="woosocio-add-connection button" href="<?php echo esc_url( $loginUrl ); ?>" target="_top"><?php _e('Connect', 'woosocio')?></a>
        <?php endif; ?>

        <?php 
        if (get_option( 'fb_app_id' ) && get_option( 'fb_app_secret' )): 
          echo '<div id="app-info">';
        else:            
          echo '<div id="app-info">';
        endif;
        ?>
        <table class="form-table">
          <tr valign="top">
            <th scope="row"><label><?php _e('Your App ID:', 'woosocio') ?></label></th>
            <td>
              <input type="text" name="app_id" id="fb-app-id" placeholder="<?php _e('App ID', 'woosocio') ?>" value="<?php echo get_option( 'fb_app_id' ); ?>"><br>
              <p style="font-size:10px"><?php _e("Don't have an app? You can get from ", 'woosocio') ?>
                <a href="https://developers.facebook.com/apps" target="_new" style="font-size:10px">developers.facebook.com/apps</a>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><label><?php _e('Your App Secret:', 'woosocio') ?></label></th>
              <td>
                <input type="text" name="app_secret" id="fb-app-secret" placeholder="<?php _e('App Secret', 'woosocio') ?>" value="<?php echo get_option( 'fb_app_secret' ); ?>"><br>
                <p style="font-size:11px"><?php _e('Need more help? ', 'woosocio') ?>
                  <a href="https://developers.facebook.com/docs/opengraph/getting-started/#create-app" target="_new" style="font-size:11px"><?php _e('Click here', 'woosocio') ?></a>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"></th>
                <td>
                  <a id="btn-save" class="button-primary button" href="javascript:"><?php _e('Save', 'woosocio') ?></a>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <?php
      /*
      if($fb_user!==0)
      {
        $user_sign = $user_profile['id'].'_fb_page_id';
        //echo get_option( $user_sign);
        $fb_page_value = get_option( $user_sign, $user_profile['id'] );
        echo "<h4>" . __( 'Post to:', 'woosocio' ) . "</h4>";
        ?>
        <img src="http://graph.facebook.com/<?php echo $user_profile['id'] ?>/picture" alt="No Image">
        <input type="radio" name="pages" value="<?php echo $user_profile['id'] ?>" <?php echo ($fb_page_value == $user_profile['id'])?'checked':''?>><?php _e('Personal Page (Wall)', 'woosocio') ?><br>
        <?php
        $page_names = $user_pages['data'];
        foreach($page_names as $key => $page)
        {
          ?>
          <img src="http://graph.facebook.com/<?php echo $page['id'] ?>/picture" alt="No Image">
          <input type="radio" name="pages" value="<?php echo $page['id'] ?>" <?php echo ($fb_page_value == $page['id']) ? 'checked':''?>><?php echo $page['name'] ?><br>
          <?php
        }
      }
      */
      ?>
</div>
<!-- Right Area Widgets -->  
<div class="woosocio-about-us">
  <div id="fb-root"></div>
  <script>
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>
</div>  


<script type="text/javascript">
  jQuery(document).ready(function($){
    $("#btn-save").click(function(){
      var data = {
        action: 'save_app_info',
        fb_app_id: $("#fb-app-id").val(),
        fb_app_secret: $("#fb-app-secret").val()
      };
      $.post(ajaxurl, data, function(response) {
        console.log('Got this from the server: ' + response);
        location.reload();
      });	
    });
    
    $("input:radio[name=pages]").click(function() {
      var data = {
        action: 'update_page_info',
        fb_page_id: $(this).val()
      };
      $.post(ajaxurl, data, function(response) {
        console.log('Got this from the server: ' + response);
        $("#working-page").hide();
        alert(response);
      });
    });
  });
</script>