<style>
.order-tbl-wrap table {background-color: #F9F9F9;border:1px solid #DFDFDF; width:98%; margin-bottom:25px;}
.order-tbl-wrap table td {font-size: 12px;  padding: 5px 7px 5px; vertical-align: top; color:#555555; border-bottom:1px solid #DFDFDF;}
.order-tbl-wrap table tr:last-child td { border-bottom:0px;}  
</style>

<?php
if(isset($_POST['submitbtn']) && $_POST['submitbtn']!='')
{
	update_option('pp_next_button_text',$_POST['pp_next_button_text']);
	update_option('pp_previous_button_text',$_POST['pp_previous_button_text']);
	update_option('pp_display_next_previous_button',$_POST['pp_display_next_previous_button']);
	update_option('pp_separator',$_POST['pp_separator']);
	$message="Setting updated successfully.";
}
?>
<h3>Gmail Settings</h3>
<?php if($message!='') { ?>
<div style="margin:20px 0px 20px 0px; text-align:left; padding-left:20px;" class="update-nag"><strong><?php echo $message; ?></strong></div>
<?php } ?>
<div class="order-tbl-wrap">
<form name="postpaginationsettings" action="" method="post" enctype="multipart/form-data">
	<table class="audio_form">
		<tr valign="top">
			<td scope="row"><strong>Next Button Text</strong></td>
			<td><input type="text" size="50" name="pp_next_button_text" value="<?php echo get_option('pp_next_button_text'); ?>" /></td>
		</tr>
		<tr valign="top">
			<td scope="row"><strong>Previous Button Text</strong></td>
			<td><input type="text" size="50" name="pp_previous_button_text" value="<?php echo get_option('pp_previous_button_text'); ?>" /></td>
		</tr>
		<tr valign="top">
			<td scope="row"><strong>Display next and previous button</strong></td>
			<td>
				<input type="radio" name="pp_display_next_previous_button" value="yes" checked="checked">Yes
				<input type="radio" name="pp_display_next_previous_button" value="yes">No
			</td>
		</tr>
		<tr valign="top">
			<td scope="row"><strong>Seprator</strong></td>
			<td><input type="text" size="50" name="pp_separator" value="<?php echo get_option('pp_separator'); ?>" /></td>
		</tr>
		<tr valign="top">
			<td scope="row"><strong>Continue Reading Text</strong></td>
			<td><input type="text" size="50" name="pp_continue_reading" value="<?php echo get_option('pp_continue_reading'); ?>" /></td>
		</tr>
		<tr valign="top">
			<td>&nbsp;</td>
			<td><input type="submit" name="submitbtn" value="Save Settings" class="button-primary" /></td>
		</tr>
	</table>	
</form>
</div>