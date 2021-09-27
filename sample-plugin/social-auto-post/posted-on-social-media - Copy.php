<?php
include_once("includes/social-auto-post-function.php");
require_once('classes/class-woo-socio.php');
global $woosocio;
global $wpdb;
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit = 5;
$offset = ( $pagenum - 1 ) * $limit;
$entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts LIMIT $offset, $limit" );
?>
<h3>Posted On Social Media</h3>
<div class="order-tbl-wrap">
<table>
<?php
global $wpdb;
$productsql="select entityid, postedtofb, postedtofbdate, postedtotwitter, postedtotwitterdate, 
CASE
	when postedtofb=1 then 'Yes' else 'No' 
END as postedtofbstatus,
CASE
	when postedtotwitter=1 then 'Yes' else 'No' 
END as postedtotwitterstatus


from postedtosocialmedia 
";
$runproductsql=mysql_query($productsql);
if(mysql_num_rows($runproductsql))
{
	echo '<tr><td>Title</td><td>Type</td><td>Facebook</td><td>Date of facebook</td><td>Twitter</td><td>Date of Title</td></tr>';
	while($runproductsqlresult=mysql_fetch_assoc($runproductsql))
	{
		$postdata=get_post($runproductsqlresult['entityid']);
		echo "<tr>";
		echo "<td>".$postdata->post_title."</td>";
		echo "<td>".$postdata->post_type."</td>";
		echo "<td>".$runproductsqlresult['postedtofbstatus']."</td>";
		echo "<td>".getShowDateFormat($runproductsqlresult['postedtofbdate'])."</td>";
		echo "<td>".$runproductsqlresult['postedtotwitterstatus']."</td>";
		echo "<td>".getShowDateFormat($runproductsqlresult['postedtotwitterdate'])."</td>";
		echo "</tr>";
	}
}	
?>
</table>
</div>