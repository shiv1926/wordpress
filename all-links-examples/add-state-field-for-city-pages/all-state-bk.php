<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="">
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body>

<?php
include_once("../../wp-load.php");
$args = array('taxonomy' => 'state', 'orderby'=> 'name', 'show_count' =>0, 'pad_counts' => 0, 'hierarchical' => 0, 'title_li' => 0, 'hide_empty' => 0, 'parent' => 0);
$posts_array = get_categories($args);
foreach ($posts_array as $cat) 
{
	$catename = strtolower($cat->name);
	$cateid = $cat->term_id;
	$query = "SELECT ID, post_title FROM wp_posts WHERE post_title LIKE 'carports%' AND post_title LIKE '% ".$catename."' AND post_type = 'page' AND post_parent = '0' AND post_status = 'publish'";
	$citypages = $wpdb->get_results($query);
	$pagecount = 0;
	foreach($citypages as $currentpost)
	{
		$newname = strtolower(trim($currentpost->post_title));
		$newname = str_replace("carports","",$newname);
		$newname = substr($newname, 0,strrpos($newname,$catename));
		//$newname = str_replace(strtolower($catename),"",$newname);
		//echo preg_replace(‘/(fox(?!.*fox))/’, ‘dog’, $string);
		$newname = trim($newname);
		echo $currentpost->ID." ".$newname." , ";

		// update_post_meta($currentpost->ID,'_city_title','field_5c260beb4f0aa');
		// update_post_meta($currentpost->ID,'city_title',$newname);
		// update_post_meta($currentpost->ID,'_state_of_city','field_5c24d4905169c');
		// update_post_meta($currentpost->ID,'state_of_city',$cateid);
		$totalpage++;
		$pagecount++;
	}
	echo "<br>".$pagecount." : ".$catename;
	echo "<br>==============<br>";
}
echo $totalpage;
?>
</body>
</html>