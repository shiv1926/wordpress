<?php
set_time_limit(0);
include_once("includes/social-auto-post-function.php");
global $wpdb;
$upload_dir = wp_upload_dir();
$basedir=$upload_dir['basedir'];
$feeddir=$basedir."/uploadfeed";
$filename=$_GET['feedname'];

require_once ABSPATH . 'wp-admin/includes/import.php';
$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
require $class_wp_importer;

$row = 1;
if(($handle = fopen($feeddir."/".$filename,"r")) !== FALSE)
{
	$headerlist=fgetcsv($handle);
	$maparray=GetMapping($headerlist);
    while(!feof($handle))
    {
		$data=array();
		$data=fgetcsv($handle);
		//print_r($data);
		//echo "<br>==========<br>";
		$check=mysql_query("select ID from ".$wpdb->prefix."posts where post_title='".$data[$maparray['post_title']]."' and post_type='product' ");
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
			}
		}
    }
    fclose($handle);
}

?>