<?php
include_once("../wp-load.php");
$args = array();
$args['post_type'] = 'product';
$args['post_status'] = 'publish';
$args['posts_per_page'] = -1;
$args['orderby'] = 'id';
$args['order'] = 'DESC';
$args['tax_query'][] = array('taxonomy'=>'product_cat', 'field'=>'term_id', 'terms'=>array(416), 'operator'=>'NOT IN');
$the_pages = new WP_Query( $args );
echo $the_pages->found_posts.'<br>';
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr>';
echo '<td>ID</td>';
echo '<td>Title</td>';
echo '<td>Link</td>';
echo '<td>Price</td>';
//echo '<td>New Price</td>';
echo '</tr>';
foreach ($the_pages->posts as $currentpost) 
{
    $product = wc_get_product($currentpost->ID);
    $price = $product->get_price();
    $newprice = ($price * 1.2);
	echo '<tr>';
	echo '<td>'.$currentpost->ID.'</td>';
	echo '<td>'.$currentpost->post_title.'</td>';
	echo '<td><a target="_blank" href="'.get_permalink($currentpost->ID).'">'.get_permalink($currentpost->ID).'</a></td>';
	echo '<td>'.$price.'</td>';
	//echo '<td>'.$newprice.'</td>';
	echo '</tr>';
	//update_post_meta($currentpost->ID, '_regular_price', $newprice);
	//update_post_meta($currentpost->ID, '_price', $newprice);
}
echo '</table>';
?>