<?php
include_once("../wp-load.php");
$count = "SELECT * FROM 1muz7unx5g2t6sl_postmeta WHERE meta_key = '_wp_page_template' and meta_value='default'";
$count_result = $wpdb->get_results($count);
echo '<table border="1" cellpadding="10" cellspacing="0">';
foreach($count_result as $r)
{
    echo '<tr>';
    echo '<td>'.get_permalink($r->post_id).'</td>';
    echo '</tr>';
    
}
echo '</table>';
?>