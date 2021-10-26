<?php
include_once("../wp-load.php");
$args = array();
$args['post_type'] = 'product';
$args['post_status'] = 'publish';
$args['posts_per_page'] = -1;
$args['orderby'] = 'id';
$args['order'] = 'DESC';
$args['tax_query'][] = array('taxonomy'=>'product_cat', 'field'=>'term_id', 'terms'=>array(416));
$the_pages = new WP_Query( $args );
echo $the_pages->found_posts.'<br>';
if($the_pages->have_posts())
{
	echo '<table cellpadding="5" cellspacing="0" border="1">';
    foreach($the_pages->posts as $currentpost)
	{
	    $product = wc_get_product($currentpost->ID);
	    $price = $product->get_price();
	    $newprice = ($price * 1.2);
		echo '<tr>';
		echo '<td>'.$currentpost->ID.'</td>';
		echo '<td><a href="'.get_permalink($currentpost->ID).'">'.get_permalink($currentpost->ID).'</a></td>';
		echo '<td>'.$price.'</td>';
		echo '<td>'.$newprice.'</td>';
		echo '</tr>';
		//update_post_meta($currentpost->ID, '_regular_price', $newprice);
		//update_post_meta($currentpost->ID, '_price', $newprice);
	}
	echo '</table>';
}
?>