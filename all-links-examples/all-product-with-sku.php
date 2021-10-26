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
echo '<td>S. No.</td><td>ID</td><td>SKU</td><td>Title</td><td>Product Source</td><td>URL</td>';
echo '</tr>';
$counter = 1;
foreach ($posts_array as $key => $value) 
{
    $product = wc_get_product($value->ID);
    $sku = $product->get_sku();
    $permalink = get_permalink($value->ID);
    $product_type = get_field('product_source',$product->get_ID());
    if($product_type=='sensei') {
        $product_type = 'Sensei CRM';
    } else {
        $product_type = 'Website';
    }
    echo '<tr>';
    echo '<td>'.$counter.'</td>';
    echo '<td>'.$value->ID.'</td>';
    echo '<td>'.$sku.'</td>';
    echo '<td>'.$value->post_title.'</td>';
    echo '<td>'.$product_type.'</td>';
    echo '<td><a target="_blank" href="'.$permalink.'">'.$permalink.'</a></td>';
    echo '</tr>';
    $counter++;
}
echo '</table>';
?>