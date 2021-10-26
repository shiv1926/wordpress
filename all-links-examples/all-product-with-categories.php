<?php
include('../wp-load.php');
$querystr = "select DISTINCT(meta_value) from gb17_postmeta where meta_key='category_name'";
$pageposts = $wpdb->get_results($querystr, OBJECT);
echo "<table>";
foreach($pageposts as $singlepost)
{
	$pcount = 0; $plist='';
    $args = array();
    $args['post_type'] = 'page';
    $args['post_status'] = 'publish';
    $args['posts_per_page'] = -1;
    $args['orderby'] = 'ID';
    $args['order']   = 'DESC';
    $args['meta_query'] = array('relation' => 'AND');
    $args['meta_query'][] = array('key' => 'category_name','value' => $singlepost->meta_value);
    $query = new WP_Query($args);
    if($query->have_posts())
    {
        foreach($query->posts as $currentpost)
        {
        	$plist.='<tr><td>'.$pcount.'&nbsp;&nbsp;<a target="_blank" href="http://shiv-pc/garagebuildings/garagebuildings/project-new/wp-admin/post.php?post='.$currentpost->ID.'&action=edit">'.$currentpost->post_title.'</td></tr>';
        	$pcount++;
        }
    }
    echo '<tr><td>'.$singlepost->meta_value.'( '.$pcount.' )'.'</td></tr>';
    echo $plist;
    echo "<tr><td>==========================================</td></tr>";
}
echo '</table>';
?>