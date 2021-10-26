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
            'value' => 'page template/roofstyles.php'
        )
    )
);
$the_pages = new WP_Query( $args );
echo $the_pages->found_posts;
echo "<br>";
if($the_pages->have_posts())
{
    echo "<table>";
    foreach($the_pages->posts as $currentpost)
    {
        echo '<tr><td>'.$currentpost->post_title.'</td><td><a href="'.get_permalink($currentpost->ID).'">'.get_permalink($currentpost->ID).'</a></td></tr>';
    }
    echo '</table>';
}
?>