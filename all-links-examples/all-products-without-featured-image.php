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
    'meta_query' => array(
        array(
            'key' => '_thumbnail_id',
            'compare' => 'NOT EXISTS'
        )
    )
);
$the_pages = new WP_Query( $args );
// echo "<pre>";
// print_r($the_pages);
if($the_pages->have_posts())
{
	echo '<table>';
    foreach($the_pages->posts as $currentpost)
	{
		echo '<tr>';
		echo '<td><a href="https://www.carportcentral.com/wp-admin/post.php?post='.$currentpost->ID.'&action=edit">'.get_permalink($currentpost->ID).'</a></td>';
		echo '</tr>';
	}
	echo '</table>';
}
?>
</body>
</html>