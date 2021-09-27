<?php
set_time_limit(0);
include_once("includes/social-auto-post-function.php");
global $wpdb;
$action=$_GET['action'];
$filename=$_GET['feedname'];

if($action=='deleteproduct')
{
	$check=mysql_query("select ID from ".$wpdb->prefix."posts where post_type='product' or post_type='post' or post_type='attachment' ");
	if(mysql_num_rows($check)>0)
	{
		while($results=mysql_fetch_array($check))
		{
			$postid=$results['ID'];
			wp_delete_post($postid);
		}
	}
}
elseif($action=='importproduct')
{
	UploadProductFromCsv($filename);
}
else
{

}

if(isset($_POST['upload_feed']))
{
	$feed_name=$_FILES['feed_file']['name'];
	$feed_type=$_FILES['feed_file']['type'];
	$feed_tmp_name=$_FILES['feed_file']['tmp_name'];
	$feed_error=$_FILES['feed_file']['error'];
	$feed_size=$_FILES['feed_file']['size'];

	$extarray=explode(".",$feed_name);
	$ext=end($extarray);

	if($feed_name=='')
	{
		$message='Please upload file.';
		$class='error';
	}
	elseif(strtolower($ext)!='csv')
	{
		$message='Please upload csv file.';
		$class='error';
	}
	else
	{
		$upload_dir = wp_upload_dir();
		$basedir=$upload_dir['basedir'];
		$feeddir=$basedir."/uploadfeed";
		if(!is_dir($feeddir)) {
			mkdir($feeddir);
		}
		$time=time();
		$newfilename=$time.".".$ext;
		$newfileloc=$feeddir.'/'.$newfilename;
		move_uploaded_file($feed_tmp_name,$newfileloc);
		$handle=fopen($newfileloc,"r");
		$headerlist=fgetcsv($handle);
		$error='';
		if(!in_array('product_name',$headerlist)) {
			$error.='Required column product_name does not exists.<br>';
		}

		if(!in_array('description',$headerlist)) {
			$error.='Required column description does not exists.<br>';
		}

		if(!in_array('store_price',$headerlist)) {
			$error.='Required column store_price does not exists.<br>';
		}

		if(!in_array('merchant_image_url',$headerlist)) {
			$error.='Required column merchant_image_url does not exists.<br>';
		}

		if(!in_array('category_name',$headerlist)) {
			$error.='Required column category_name does not exists.<br>';
		}

		if(!in_array('category_id',$headerlist)) {
			$error.='Required column category_id does not exists.<br>';
		}

		if($error=='')
		{
			$productcount = count(file($newfileloc, FILE_SKIP_EMPTY_LINES));
			$in="insert into uploadfeed set feedfilename='".$newfilename."', feeduploadeddate='".date("Y-m-d")."', feedstatus=0, originalfilename='".$feed_name."', totalproductsinfeed='".$productcount."' ";
			mysql_query($in);
			$url = admin_url('admin.php?page=uploadfeed');
			echo '<script>window.location.href="'.$url.'";</script>';
		}
		else
		{
			$message=$error;
		}
	}
}
?>
<h3>Upload Feed</h3>
<?php 
if($message!='') { 
	echo '<div class="update-nag">'.$message.'</div>';
} 
?>
<div class="wrap">
<form method="post" enctype="multipart/form-data">
<table class="widefat">
<tr>
	<td>Upload File</td>
	<td><input type="file" name="feed_file" id="id_feed_file"></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="Upload" name="upload_feed"></td>
</tr>
</table>
<br>
<?php
$select="select uploadfeedid, feedtitle, originalfilename, feedfilename, feeduploadeddate, totalproductsinfeed, feedstatus, uploadedproduct from uploadfeed order by uploadfeedid desc ";
$sqlselect=mysql_query($select);
if(mysql_num_rows($sqlselect)>0)
{
	echo '<table class="widefat">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" class="manage-column column-name">Filename</th>';
	echo '<th scope="col" class="manage-column column-name">Uploaded On</th>';
	echo '<th scope="col" class="manage-column column-name">Import</th>';
	echo '<th scope="col" class="manage-column column-name">Delete</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tfoot>';
	echo '<tr>';
	echo '<th scope="col" class="manage-column column-name">Filename</th>';
	echo '<th scope="col" class="manage-column column-name">Uploaded On</th>';
	echo '<th scope="col" class="manage-column column-name">Import</th>';
	echo '<th scope="col" class="manage-column column-name">Delete</th>';
	echo '</tr>';
	echo '</tfoot>';
	$count = 1;
	$class = '';
	while($sqlselectresult=mysql_fetch_assoc($sqlselect))
	{
		$class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
		echo '<tr '.$class.'>';
		echo '<td>'.$sqlselectresult['originalfilename'].'</td>';
		echo '<td>'.getShowDateFormat($sqlselectresult['feeduploadeddate']).'</td>';
		echo '<td><a href="'.admin_url('admin.php?page=uploadfeed&action=importproduct&feedname='.$sqlselectresult['feedfilename']).'">Run Import</a></td>';
		echo '<td><a href="'.admin_url('admin.php?page=uploadfeed&action=deleteproduct').'">Delete</a></td>';
		echo '</tr>';
		$count++;
	}
	echo '</table>';
}
?>
</form>
</div>