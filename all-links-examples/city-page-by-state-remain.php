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
            'value' => 'page template/city-page.php'
        )
    )
);
$the_pages = new WP_Query( $args );
if($the_pages->have_posts())
{
    echo "<table>";
    foreach($the_pages->posts as $currentpost)
    {
        $query = "SELECT meta_id FROM wp_postmeta WHERE post_id = '".$currentpost->ID."' and meta_key = 'state_of_city' ";
        $citypages = $wpdb->get_results($query);
        if(count($citypages)>0) {
            //echo '<tr><td>'.$currentpost->post_title.'</td><td>'.get_permalink($currentpost->ID).'"></td><td> exist </td></tr>';
        } else {
            echo '<tr><td>'.$currentpost->post_title.'</td><td>'.$currentpost->ID.'</td><td> notexist </td></tr>';
        }
    }
    echo '</table>';
}
?>