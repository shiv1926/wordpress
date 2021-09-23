<?php
include_once("../wp-load.php");
global $wpdb;
$query = "SELECT distinct(meta_value) FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_wp_page_template'";
$citypages = $wpdb->get_results($query);
echo '<table border="1" cellpadding="10" cellspacing="0">';
$total = 0;
foreach($citypages as $currentpost)
{
    echo "<tr>";
    echo '<td>'.$currentpost->meta_value.'</td>';
    echo "</tr>";
}
echo "</table>";
?>