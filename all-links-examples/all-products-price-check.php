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
	'post_type'        => 'product',
	'post_status'      => 'publish',
);
$posts_array = get_posts( $args );
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr>';
echo '<td>ID</td><td>Title</td><td>URL</td><td>Price</td>';
echo '</tr>';
foreach ($posts_array as $key => $value) 
{
    $product = wc_get_product($value->ID);
	$price = $product->get_price();
	$newprice = round(($price * 1.25) , 2);
	echo '<tr>';
	echo '<td>'.$value->ID.'</td>';
	echo '<td>'.$value->post_title.'</td>';
	echo '<td><a target="_blank" href="'.get_permalink($value->ID).'">View</a></td>';
	echo '<td>'.$price.'</td>';
	echo '</tr>';
}
echo '</table>';
?>
</body>
</html>