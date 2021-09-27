<style>
.order-tbl-wrap table {background-color: #F9F9F9;border:1px solid #DFDFDF; width:98%; margin-bottom:25px;}
.order-tbl-wrap table td {font-size: 12px;  padding: 5px 7px 5px; vertical-align: top; color:#555555; border-bottom:1px solid #DFDFDF;}
.order-tbl-wrap table tr:last-child td { border-bottom:0px;}  
</style>

<?php
include_once("social-auto-post-function.php");
if(isset($_POST['submitbtn']) && $_POST['submitbtn']!='')
{
	update_option('ap_fb_application_id',EscapeString($_POST['ap_fb_application_id']));
	update_option('ap_fb_application_secret',EscapeString($_POST['ap_fb_application_secret']));
	update_option('ap_page_id',EscapeString($_POST['ap_page_id']));
	$message="Setting updated successfully.";
}
?>
<h3>Facebook Settings</h3>
<?php if($message!='') { ?>
<div style="margin:20px 0px 20px 0px; text-align:left; padding-left:20px;" class="update-nag"><strong><?php echo $message; ?></strong></div>
<?php } ?>
<div class="order-tbl-wrap">
<form name="postpaginationsettings" action="" method="post" enctype="multipart/form-data">
	<table class="audio_form">
		<tr valign="top">
			<td scope="row"><strong>Application ID</strong></td>
			<td><input type="text" size="50" name="ap_fb_application_id" value="<?php echo get_option('ap_fb_application_id'); ?>" /></td>
		</tr>
		<tr valign="top">
			<td scope="row"><strong>Application Secret</strong></td>
			<td><input type="text" size="50" name="ap_fb_application_secret" value="<?php echo get_option('ap_fb_application_secret'); ?>" /></td>
		</tr>
		<tr valign="top">
			<td scope="row"><strong>Page ID</strong></td>
			<td><input type="text" size="50" name="ap_fb_page_id" value="<?php echo get_option('ap_fb_page_id'); ?>" /></td>
		</tr>
		<tr valign="top">
			<td>&nbsp;</td>
			<td><input type="submit" name="submitbtn" value="Save Settings" class="button-primary" /></td>
		</tr>
	</table>	
</form>
</div>