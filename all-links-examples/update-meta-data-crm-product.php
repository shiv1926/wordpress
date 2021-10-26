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
        $product_name = str_replace("'", "", $currentpost->post_title);
        
        $title ='Customize and Order '.$product_name.' online';
        $description = 'Order '.$product_name.' online at the Best price from Garage Buildings. Check out the product details and specifications of '.$product_name.'.';
        echo '<tr>';
        echo '<td>'.$currentpost->ID.'</td>';
        echo '<td><a href="'.get_permalink($currentpost->ID).'">'.get_permalink($currentpost->ID).'</a></td>';
        echo '<td>'.$title.'</td>';
        echo '<td>'.$description.'</td>';
        echo '</tr>';
        update_post_meta($currentpost->ID, '_yoast_wpseo_title', $title);
        update_post_meta($currentpost->ID, '_yoast_wpseo_metadesc', $description);
    }
    echo '</table>';
}
?>