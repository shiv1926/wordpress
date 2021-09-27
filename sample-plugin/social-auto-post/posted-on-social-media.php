<?php
include_once("includes/social-auto-post-function.php");
require_once('classes/class-woo-socio.php');
global $woosocio;
global $wpdb;
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit = 20;
$offset = ( $pagenum - 1 ) * $limit;
$productsql="select entityid, postedtofb, postedtofbdate, postedtotwitter, postedtotwitterdate, 
CASE
	when postedtofb=1 then 'Yes' else 'No' 
END as postedtofbstatus,
CASE
	when postedtotwitter=1 then 'Yes' else 'No' 
END as postedtotwitterstatus
from postedtosocialmedia order by entityid desc ";
$productsql="SELECT * FROM wp_posts LIMIT $offset, $limit";
$entries = $wpdb->get_results($productsql);
?>
<h3>Posted On Social Media</h3>
<div class="wrap">
<table class="widefat">
<thead>
<tr>
<th scope="col" class="manage-column column-name">Title</th>
<th scope="col" class="manage-column column-name">Type</th>
<th scope="col" class="manage-column column-name">Facebook</th>
<th scope="col" class="manage-column column-name">Date of facebook</th>
<th scope="col" class="manage-column column-name">Twitter</th>
<th scope="col" class="manage-column column-name">Date of twitter</th>
</tr>
</thead>
<tfoot>
<tr>
<th scope="col" class="manage-column column-name">Title</th>
<th scope="col" class="manage-column column-name">Type</th>
<th scope="col" class="manage-column column-name">Facebook</th>
<th scope="col" class="manage-column column-name">Date of facebook</th>
<th scope="col" class="manage-column column-name">Twitter</th>
<th scope="col" class="manage-column column-name">Date of twitter</th>
</tr>
</tfoot>
<tbody>
<?php 
if($entries) 
{ 
	$count = 1;
	$class = '';
	foreach($entries as $entry) 
	{
		$class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
		$postdata=get_post($entry->entityid);
		echo "<tr".$class.">";
		echo "<td>".$postdata->post_title."</td>";
		echo "<td>".$postdata->post_type."</td>";
		echo "<td>".$entry->postedtofbstatus."</td>";
		echo "<td>".getShowDateFormat($entry->postedtofbdate)."</td>";
		echo "<td>".$entry->postedtotwitterstatus."</td>";
		echo "<td>".getShowDateFormat($entry->postedtotwitterdate)."</td>";
		echo "</tr>";
		$count++;
	}
}
else 
{ 
?>
<tr><td colspan="2">No posts yet</td></tr>
<?php } ?>
</tbody>
</table>
<?php
$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$wpdb->prefix}posts" );
$num_of_pages = ceil( $total / $limit );
$page_links = paginate_links( array(
	'base' => add_query_arg( 'pagenum', '%#%' ),
	'format' => '',
	'prev_text' => __( '&laquo;', 'aag' ),
	'next_text' => __( '&raquo;', 'aag' ),
	'total' => $num_of_pages,
	'current' => $pagenum
) );
if ( $page_links ) {
	echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
echo '</div>';
?>