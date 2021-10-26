<?php
include('../wp-load.php');
$args = array(
    'post_type' => 'page',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order'   => 'ASC',
    'meta_query' => array(
        array(
            'key' => '_wp_page_template',
            'value' => 'page-templates/prodcut-detail.php'
        )
    )
);
$the_pages = new WP_Query( $args );
echo $the_pages->found_posts;
echo "<br>";
if($the_pages->have_posts())
{
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<tr>';
    echo '<td>Product Title</td>';
    echo '<td>Description</td>';
    echo '<td>Price</td>';
    echo '<td>SKU</td>';
    echo '<td>Category Name</td>';
    echo '<td>Video URL</td>';
    echo '<td>Featured Image</td>';
    echo '<td>url</td>';
    echo '</tr>';
    foreach($the_pages->posts as $currentpost)
    {
        $shop_single = wp_get_attachment_image_src(get_post_thumbnail_id($currentpost->ID),'full');
        echo '<tr>';
        echo '<td>'.$currentpost->post_title.'</td>';
        echo '<td>'.$currentpost->post_content.'</td>';
        echo '<td>'.get_field("starting_price",$currentpost->ID).'</td>';
        echo '<td>'.get_field("product_sku",$currentpost->ID).'</td>';
        echo '<td>'.get_field("category_name",$currentpost->ID).'</td>';
        echo '<td>'.get_field("video_url",$currentpost->ID).'</td>';
        echo '<td><img src="'.$shop_single[0].'" style="width: 100px;"></td>';
        echo '<td><a href="'.get_permalink($currentpost->ID).'">'.get_permalink($currentpost->ID).'</a></td>';
        echo '</tr>';
    }
    echo '</table>';
}
?>