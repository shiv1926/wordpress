<?php
set_time_limit(0);
require_once ABSPATH . 'wp-admin/includes/import.php';
$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
require_once $class_wp_importer;

function EscapeString($string)
{
	return mysql_real_escape_string(trim($string));
}

function GetProductImage($postid)
{
	$thumbnail=get_post_meta($postid,'_thumbnail_id',true);
	if($thumbnail!='')
	{
		$thumbnailpic=wp_get_attachment_thumb_url($thumbid);
	}
	else
	{
		$thumbnailpic='http://i.imgur.com/lHkOsiH.png';
	}
	return $thumbnailpic;
}

function checkEmail($emailid)
{
	if(!preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$emailid)) {
		return "no";
	} else {
		return "yes";
	}
}

function base36_encode($base10) {
    return base_convert($base10, 10, 36);
}
 
function base36_decode($base36) {
    return base_convert($base36, 36, 10);
}

function getEncryptPassword($password)
{
	return base64_encode($password);
}

function getDecryptPassword($passowrd)
{
	echo base64_decode($passowrd);
}

function currentDate()
{
	return date('Y-m-d');
}

function currentTime()
{
	return date("G:i:s");
}

function currentTimestamp() 
{
	return currentDate() . " " . currentTime();
}

function randomkeys($length=8) 
{
	$pattern = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	for ($i = 0; $i < $length; $i++) 
	{
		$key.= $pattern{rand(0, 62)};
	}
	return $key;
}

function getJavaScripRedirect($url) 
{
	echo "<script>window.location.href='".$url."'</script>";
}

function debugArray($array,$die=0)
{
	print("<pre>");
	print_r($array);
	print("</pre>");
	if($die==1) {
		die();
	}
}

function getShowDateFormat($date)
{
	$exp=explode("-",$date);
	$year=$exp[0];
	$month=$exp[1];
	$day=$exp[2];
	if($year>0 && $month>0)
	{	
		$date=date("d M Y",mktime(0,0,0,$month,$day,$year));
		$return=$date;
	}
	else
	{
		$return='';
	}
	return $return;
}

function getExt($filename)
{
	$end='';
	if($filename!='')
	{
		$exp=explode(".",$filename);
		if(is_array($exp) && !empty($exp))
		{
			$end=end($exp);
		}
	}
	return $end;
}

function removeSpecialChars($string)
{
	$string=preg_replace('/[^\sa-zA-Z0-9\']/','',$string);
	return $string;
}

function GetMapping($headerlist)
{
	$maparray=array();
	$product_name=array_search('product_name',$headerlist);
	$description =array_search('description',$headerlist);
	$store_price=array_search('store_price',$headerlist);
	$merchant_image_url=array_search('merchant_image_url',$headerlist);
	$category_name=array_search('category_name',$headerlist);
	$category_id=array_search('category_id',$headerlist);

	$maparray['post_title']=$product_name;
	$maparray['post_content']=$description;
	$maparray['store_price']=$store_price;
	$maparray['merchant_image_url']=$merchant_image_url;
	$maparray['category_name']=$category_name;
	$maparray['category_id']=$category_id;
	return $maparray;
}

function new_attachment($att_id){
    // the post this was sideloaded into is the attachments parent!
    $p = get_post($att_id);
    update_post_meta($p->post_parent,'_thumbnail_id',$att_id);
}

// add the function above to catch the attachments creation
add_action('add_attachment','new_attachment');

function UploadProductFromCsv($filename,$fileid='')
{
	require_once ABSPATH . 'wp-admin/includes/media.php';
	global $wpdb;
	$upload_dir = wp_upload_dir();
	$basedir=$upload_dir['basedir'];
	$feeddir=$basedir."/uploadfeed";
	$row = 1;
	if(($handle = fopen($feeddir."/".$filename,"r")) !== FALSE)
	{
		$headerlist=fgetcsv($handle);
		$maparray=GetMapping($headerlist);
	    while(!feof($handle))
	    {
			$data=array();
			$data=fgetcsv($handle); $uploadcount=0;
			//print_r($data);
			//echo "<br>==========<br>";
			$check=mysql_query("select ID from ".$wpdb->prefix."posts where post_title='".mysql_real_escape_string($data[$maparray['post_title']])."' and post_type='product' ");
			if(mysql_num_rows($check)==0)
			{
				if($data[$maparray['category_name']]!='')
				{
					$term = term_exists($data[$maparray['category_name']],'product_cat');
					if(!empty($term) && isset($term['term_id']) && $term['term_id']!='')
					{
						$tax=array('product_cat'=>array($term['term_id']));
					}
					else 
					{
						$returnterm=wp_insert_term($data[$maparray['category_name']],'product_cat');
						$tax=array('product_cat'=>array($returnterm['term_id']));
					}
				}

				$post = array();
				$post['post_content']=$data[$maparray['post_content']];
				$post['post_title']=$data[$maparray['post_title']];
				$post['post_status']='publish';
				$post['post_type']='product';
				$post['post_author']='1';
				$post['ping_status']='closed';
				$post['menu_order']=0;
				$post['to_ping']='';
				$post['pinged']='';
				$post['post_password']='';
				$post['post_date']=date("Y-m-d H:i:s");
				$post['post_date_gmt']=date("Y-m-d H:i:s");
				$post['comment_status']='open';
				if($data[$maparray['category_name']]!='')
				{
					$post['tax_input']=$tax;
				}

				$postid=wp_insert_post($post, $wp_error);
				if($postid>0)
				{
					update_post_meta($postid,'_price', $data[$maparray['store_price']]);
					update_post_meta($postid,'_regular_price', $data[$maparray['store_price']]);
					//update_post_meta($postid,'_sku', $data[$maparray['store_price']]);
					update_post_meta($postid,'_visibility','visible');
					update_post_meta($postid,'_backorders','no');
					update_post_meta($postid,'_manage_stock','no');
					update_post_meta($postid,'_featured','no');
					update_post_meta($postid,'_virtual','no');
					update_post_meta($postid,'_downloadable','no');
					update_post_meta($postid,'_stock_status','instock');
					$url = $data[$maparray['merchant_image_url']];
					$image = media_sideload_image($url,$postid);
					$uploadcount++;
				}
			}
	    }
	    fclose($handle);
		echo $uploadcount." product uploaded successfully.";
	}
}
?>