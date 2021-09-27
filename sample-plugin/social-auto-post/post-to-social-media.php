<?php
require_once( 'classes/class-woo-socio.php' );
global $woosocio;
$woosocio = new Woo_Socio( __FILE__ );
$postedtype=$_GET['postedtype'];
//$woosocio->fbgetaccesstoken();
?>
<h3>Post To Social Media</h3>
<div class="order-tbl-wrap">
<table>
<tr>
	<td><a href="<?php echo add_query_arg(array('postedtype'=>'twitter')); ?>">Twitter</a></td>
	<td><a href="<?php echo add_query_arg(array('postedtype'=>'facebook')); ?>">Facebook</a></td>
</tr>
<tr>
	<td colspan="2">
		<?php
		if($postedtype=='facebook')
		{
			$woosocio->PostToFacebook();
		}

		if($postedtype=='twitter')
		{
			$woosocio->PostToTwitter();
		}
		?>		
	</td>
</tr>
</table>
</div>