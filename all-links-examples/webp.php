<?php
include_once("../wp-load.php");
get_header();
//include(TEMPLATEPATH . '/include/navbar.php');

if($_SERVER['HTTP_HOST']=='www.carportcentral.com') {
	$host = 'https://www.carportcentral.com';
} 
else {
	$host = 'http://localhost/carportcentral';
}

?>
<style type="text/css">
	.webpcon { padding: 50px 0; }
</style>
<div class="container webpcon">
	<div class="row">
		<div class="col-sm-3"><img src="<?php echo $host; ?>/all-links/webp/1.webp" class="img-responsive"></div>
		<div class="col-sm-3"><img src="<?php echo $host; ?>/all-links/webp/2.webp" class="img-responsive"></div>
		<div class="col-sm-3"><img src="<?php echo $host; ?>/all-links/webp/3.webp" class="img-responsive"></div>
		<div class="col-sm-3"><img src="<?php echo $host; ?>/all-links/webp/4.webp" class="img-responsive"></div>
	</div>
</div>