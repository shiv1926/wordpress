<?php include('header.php'); ?>
<ul class="index">
	<li><a href="<?php echo wp_url(''); ?>">Introduction</a></li>
</ul>

<?php 
$links = array();
$links[] = array('https://wpdatatables.com/crud-system-in-wordpress', '');
$links[] = array('https://developer.wordpress.org/reference/classes/wpdb', '');
echo refrences($links);
include('footer.php'); 
?>