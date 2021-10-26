<?php
include_once("../wp-load.php");
$query = "SELECT meta_value FROM wp_postmeta WHERE meta_key = '_wp_page_template' and meta_value!='page template/city-page.php' group by meta_value";
$citypages = $wpdb->get_results($query);
echo '<table border="1" cellpadding="10" cellspacing="0">';
$total = 0;
foreach($citypages as $currentpost)
{
    $count = "SELECT * FROM wp_postmeta WHERE meta_key = '_wp_page_template' and meta_value='".$currentpost->meta_value."'";
    $count_result = $wpdb->get_results($count);
    $count_page = 1;
    echo "<tr>";
    echo '<td style="vertical-align: top;">'.$currentpost->meta_value.'</td>';
    echo '<td style="vertical-align: top;">'.$count_page.'</td>';
    echo '<td>';
	foreach($count_result as $count_result_page)
	{
		echo '<div><a href="'.get_permalink($count_result_page->post_id).'">'.get_permalink($count_result_page->post_id).'</a></div>';
		$count_page++;
	}
    echo '</td>';
    echo "</tr>";
}
echo "</table>";
?>