<?php
include('../wp-load.php');
$querystr = "select DISTINCT(meta_value) from gb17_postmeta where meta_key='category_name'";
$pageposts = $wpdb->get_results($querystr, OBJECT);
echo "<table>";
foreach($pageposts as $singlepost)
{
    echo '<tr><td>'.$singlepost->meta_value.'</td></tr>';
}
echo '</table>';
?>