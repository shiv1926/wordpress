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
	'order'            => 'ASC',
	'post_type'        => 'product',
	'post_status'      => 'publish',
);
$posts_array = get_posts( $args );
//echo '<table border="1" cellpadding="5" cellspacing="0">';
foreach ($posts_array as $key => $value) 
{
	// echo "<pre>";
	// print_r($value);
	// $product = new WC_Product($post_id);
 //    $sku = get_post_meta($post_id, '_sku', true);
 //    $price = get_post_meta($post_id, '_regular_price', true);

	//echo "<tr>";
	//echo '<td>'.$value->ID.'</td>';
	// echo '<td>'.$value->post_title.'</td>';
	// echo '<td>'.$value->post_excerpt.'</td>';
	// echo '<td>'.$value->post_content.'</td>';
	// echo '<td>'.$value->post_date.'</td>';
	//echo '<td>'.get_permalink($value->ID).'</td>';
	//echo "</tr>";
	echo '<div>'.get_permalink($value->ID).'</div>';
}
//echo '</table>';
?>
</body>
</html>