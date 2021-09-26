<?php  
/* 
* Plugin Name: Site info
*/

register_activation_hook(__FILE__,'activate_site_info');
register_deactivation_hook(__FILE__,'deactivate_site_info');

function activate_site_info() {
    update_option('pp_style_type','existing');
    update_option('pp_existing_style','default');
}

function EscapeString($string)
{
    return mysql_real_escape_string(trim($string));
}

function deactivate_site_info() {
}

add_action('admin_menu', 'site_info_menu');
function site_info_menu() 
{
    add_options_page('Site Info', 'Site Info', 'administrator', 'site-info', 'siteinfo');
}

function siteinfo()
{
    if(isset($_POST['siteinfosubmit']))
    {
        /*$siteinfo = array();
        $siteinfo['site_info_phone_number'] = $_POST['site_info_phone_number'];
        $siteinfo['site_info_google_plus'] = $_POST['site_info_google_plus'];
        $siteinfo['site_info_twitter'] = $_POST['site_info_twitter'];
        $siteinfo['site_info_facebook'] = $_POST['site_info_facebook'];
        $siteinfo['site_info_linkedin'] = $_POST['site_info_linkedin'];
        $siteinfo['site_info_email'] = $_POST['site_info_email'];
        $siteinfo['site_info_footer_contact_address_contact_page'] = $_POST['site_info_footer_contact_address_contact_page'];
        $siteinfo['site_info_footer_contact_address'] = $_POST['site_info_footer_contact_address'];
        $siteinfojson = json_encode($siteinfo);*/

update_option('site_info_phone_number',$_POST['site_info_phone_number'],'yes');
update_option('site_info_google_plus',$_POST['site_info_google_plus'],'yes');
update_option('site_info_twitter',$_POST['site_info_twitter'],'yes');
update_option('site_info_facebook',$_POST['site_info_facebook'],'yes');
update_option('site_info_linkedin',$_POST['site_info_linkedin'],'yes');
update_option('site_info_email',$_POST['site_info_email'],'yes');
update_option('site_info_footer_contact_address_contact_page',$_POST['site_info_footer_contact_address_contact_page'],'yes');
update_option('site_info_footer_contact_address',$_POST['site_info_footer_contact_address'],'yes');
echo 'Data saved successfully';
    }
    ?>
    <div class="wrap">
        <div class="icon32" id="icon-options-general"><br></div><h2>Site Info</h2>
        <form method="post">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="blogname">Phone number</label></th>
                        <td><input type="text" class="regular-text" value="<?php echo get_option('site_info_phone_number'); ?>" name="site_info_phone_number"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="blogname">Google Plus</label></th>
                        <td><input type="text" class="regular-text" value="<?php echo get_option('site_info_google_plus'); ?>" name="site_info_google_plus"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="blogname">Twitter</label></th>
                        <td><input type="text" class="regular-text" value="<?php echo get_option('site_info_twitter'); ?>" name="site_info_twitter"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="blogname">Facebook</label></th>
                        <td><input type="text" class="regular-text" value="<?php echo get_option('site_info_facebook'); ?>" name="site_info_facebook"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="blogname">linkedin</label></th>
                        <td><input type="text" class="regular-text" value="<?php echo get_option('site_info_linkedin'); ?>" name="site_info_linkedin"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="blogname">Email</label></th>
                        <td><input type="text" class="regular-text" value="<?php echo get_option('site_info_email'); ?>" name="site_info_email"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="blogname">Footer Contact Address</label></th>
                        <td><textarea name="site_info_footer_contact_address"><?php echo get_option('site_info_footer_contact_address'); ?></textarea></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="blogname">Contact Address contact us page</label></th>
                        <td><textarea name="site_info_footer_contact_address_contact_page"><?php echo get_option('site_info_footer_contact_address_contact_page'); ?></textarea></td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="siteinfosubmit"></p></form>
        </div>
    <?php
}
?>