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
$args = array( 'taxonomy' => 'product_cat', 'hide_empty' => 0);
$posts_array = get_categories($args);
foreach ($posts_array as $key => $value) 
{
	echo '<a href="all-product-by-category.php?catid='.$value->term_id.'">'.$value->name.'</a>';
	echo "<br>";
}

echo "<br>================================<br><br>";


$cateid = '';
$cateid = $_GET['catid'];

if($cateid!='')
{
	$args = array('post_type'=>'product', 'posts_per_page'=>-1, 'orderby'=>'ID', 'order'=>'ASC');
	$args['tax_query'] =  array('relation' => 'AND');
    $args['tax_query'][] = array('taxonomy'=>'product_cat', 'field'=>'term_id', 'terms'=>array($cateid));
	$query = new WP_Query($args);
	echo "Total Product : ".$query->found_posts;
	echo "<br>";
	echo "<br>";

	if($query->have_posts())
	{
	    foreach($query->posts as $currentpost)
	    {
	    	echo '<a href="'.get_permalink($currentpost->ID).'">'.$currentpost->post_title.'</a>';
	    	echo "<br>";
	    }
	}
}


?>
</body>
</html>