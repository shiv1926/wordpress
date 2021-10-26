<?php
include('../wp-load.php');
$args = array(
    'post_type' => 'page',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order'   => 'ASC',
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => '_wp_page_template',
            'value' => 'page template/city-page.php'
        ),
        array(
            'key' => '_yoast_wpseo_metadesc',
            'value' => 'best prices',
            'compare' => 'like',
        )
)
);
$the_pages = new WP_Query( $args );
echo $the_pages->found_posts;
$counter = 1;
echo "<br>";
if($the_pages->have_posts())
{
	global $wpdb;
    echo "<table>";
    foreach($the_pages->posts as $currentpost)
    {
        //echo '<tr><td>'.$currentpost->post_title.'</td><td><a href="'.get_permalink($currentpost->ID).'">'.get_permalink($currentpost->ID).'</a></td></tr>';
        //Carports Altamonte Springs FL | Altamonte Springs Carports | Metal Carport Prices in Florida
        //Carports in Adamsville AL - Huge range of carports, metal garages, RV covers, barns & steel buildings at the best prices in Adamsville, Alabama. Buy your Carport Now.
	 	$querystr = " SELECT * FROM wp_postmeta where post_id = '".$currentpost->ID."' AND meta_key = '_yoast_wpseo_metadesc' and meta_value like '%best prices%'";
		$pageposts = $wpdb->get_row($querystr, OBJECT);
		$meta_value = $pageposts->meta_value;
		$meta_value = str_replace("best prices","competitive prices",$meta_value);
        echo '<tr><td>update wp_postmeta set meta_value="'.$meta_value.'" where post_id="'.$currentpost->ID.'" AND meta_key = "_yoast_wpseo_metadesc";</td></tr>';
        //echo '<tr><td>'.$currentpost->post_title.'</td><td><a href="'.get_permalink($currentpost->ID).'">'.get_permalink($currentpost->ID).'</a></td></tr>';
        $counter++;
        if(($counter%500)==0) {
            echo "<tr><td>========================================================</td></tr>";
        }
    }
    echo '</table>';
}
?>