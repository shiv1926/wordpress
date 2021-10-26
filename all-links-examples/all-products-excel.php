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
$products = array();
echo '<table border="1" cellpadding="5" cellspacing="0">';
foreach ($posts_array as $key => $value) 
{
	$product = new WC_Product($value->ID);
	$sku = get_post_meta($value->ID, '_sku', true);
	$price = get_post_meta($value->ID, '_regular_price', true);

	echo "<tr>";
	echo '<td>'.$value->ID.'</td>';
	echo '<td>'.$sku.'</td>';
	echo '<td>'.$value->post_title.'</td>';
	echo '<td>'.get_permalink($value->ID).'</td>';
	echo "</tr>";
	$products[] = array($value->ID, $sku, $value->post_title, get_permalink($value->ID));
}
echo '</table>';

$file = fopen("products.csv","w");
foreach ($products as $line) {
  fputcsv($file, $line);
}
fclose($file);

?>
</body>
</html>