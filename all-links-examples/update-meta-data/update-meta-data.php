<?php 
include('../../wp-load.php');
$args = array(
    'post_type' => 'page',
    'posts_per_page' => -1,
    'orderby' => 'id',
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
    foreach($the_pages->posts as $currentpost)
    {
        $stateid = get_post_meta($currentpost->ID,'state_of_city',true);
        $city_title = get_post_meta($currentpost->ID,'city_title',true);
        $term = get_term_by( 'id', $stateid, 'state');

        $statename = ucwords($term->name);
        $state_abbr = strtoupper(get_field('state_abbr', 'state_'.$term->term_id));

        $title = 'Carports '.ucwords($city_title).' '.$state_abbr.' | '.ucwords($city_title).' Carports | Metal Carport Prices in '.$statename;
        echo 'update wp_postmeta set meta_value="'.$title.'" where post_id = "'.$currentpost->ID.'" and meta_key="_yoast_wpseo_title";<br>';

        $description = 'Carports in '.ucwords($city_title).' '.$state_abbr.' - Huge range of carports, metal garages, RV covers, barns & steel buildings at the best prices in '.ucwords($city_title).', '.$statename.'. Buy your Carport Now.';
        echo 'update wp_postmeta set meta_value="'.$description.'" where post_id = "'.$currentpost->ID.'" and meta_key="_yoast_wpseo_metadesc";<br>';
    }

    echo "<br>=============================================<br>";
}
?>