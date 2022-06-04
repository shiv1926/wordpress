<?php include('header.php'); ?>
<ul class="index">
	<li><a href="<?php echo wp_url(''); ?>">Introduction</a></li>
</ul>

<?php 
$links = array();
$links[] = array('https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/', '');
$links[] = array('https://developer.wordpress.org/plugins/security/securing-input/', '');
echo refrences($links);
include('footer.php'); 
?>