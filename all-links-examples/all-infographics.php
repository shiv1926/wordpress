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
include_once("../wp-load.php");
$args = array(
	'posts_per_page'   => -1,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'infographic',
	'post_status'      => 'publish',
);
$posts_array = get_posts( $args );
foreach ($posts_array as $key => $value) 
{
	echo get_permalink($value->ID);
	echo "<br>";
}
?>
</body>
</html>