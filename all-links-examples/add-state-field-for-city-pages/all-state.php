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
$posts_array = get_term(989);
$totalpage = 0; $pagecount = 0;
$catename = strtolower($posts_array->name);
$cateid = $posts_array->term_id;
$query = "SELECT ID, post_title FROM wp_posts WHERE post_title LIKE 'carports%' AND post_title LIKE '% ".$catename."' AND post_type = 'page' AND post_parent = '0' AND post_status = 'publish'";
$citypages = $wpdb->get_results($query);
foreach($citypages as $currentpost)
{
	$newname = strtolower(trim($currentpost->post_title));
	$newname = str_replace("carports","",$newname);
	$newname = substr($newname, 0,strrpos($newname,$catename));
	$newname = trim($newname);
	echo $currentpost->ID." , ".$newname."<br>";
	update_post_meta($currentpost->ID,'_city_title','field_5c260beb4f0aa');
	update_post_meta($currentpost->ID,'city_title',$newname);
	update_post_meta($currentpost->ID,'_state_of_city','field_5c24d4905169c');
	update_post_meta($currentpost->ID,'state_of_city',$cateid);
	$totalpage++;
	$pagecount++;
}
echo "<br>".$pagecount." : ".$catename;
echo "<br>==============<br>";

echo $totalpage;
?>
</body>
</html>