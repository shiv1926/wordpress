<?php
set_time_limit(0);
include_once("../wp-load.php");
$args = array(
	'posts_per_page'   => -1,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'attachment',
);
$posts_array = get_posts( $args );

$count = 0;
echo '<table border="0">';
$folder = 'E:/xampp/htdocs/amb/project/wp-content/uploads';
foreach ($posts_array as $key => $value) 
{
	$post_date = explode("-",$value->post_date);
	$year      = $post_date[0];
	$month     = $post_date[1];
	$dir       = trim(strtolower($folder.'/'.$year.'/'.$month));
	$guid      = trim(strtolower($value->guid));
	$filename  = basename($guid);
	$dest_file = trim(strtolower($dir.'/'.$filename));
	if(is_dir($dir))
	{
		if(!is_file($dest_file)) {
			copy($guid, $dest_file);
		}
	}
	else
	{
		mkdir($dir);
		copy($guid, $dest_file);
	}
	echo '<tr>';
	echo '<td>'.get_permalink($value->ID).'</td>';
	echo '</tr>';
	$count++;
}
echo '</table>';
echo "Total Images : ".$count;
?>
