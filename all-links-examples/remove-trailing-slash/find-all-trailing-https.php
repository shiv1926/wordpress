<?php include("simple_html_dom.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="">
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<?php
include_once("../../wp-load.php");
	
$querystr = "SELECT * FROM  wp_postmeta where meta_key like '%http://www.carportcentral.com%' or meta_value like '%http://www.carportcentral.com%' ";
$posts_array = $wpdb->get_results($querystr, OBJECT);
$counter = 1;
$html = new simple_html_dom();
foreach ($posts_array as $key => $value) 
{
	echo $string=$value->meta_value;
	$html->load($string);
	echo '<div data-permalink="'.get_permalink($value->post_id).'">';
	echo '<a href="'.get_permalink($value->post_id).'">'.$value->post_id.'   ,  '.get_permalink($value->post_id).'</a><br>';
	foreach($html->find('a[href*="http://www.carportcentral.com"]') as $e) 
	{
	    echo $e->href . '<br>';
	}
	echo "<br>========================================<br>";
	echo '</div>';
	$counter++;
}
echo $counter;
$html->clear(); 
unset($html);
?>
<script>
$(document).ready(function(){
	//$("div[data-permalink*='-revision-']").css({'background-color':'yellow'});
	$("div[data-permalink*='-revision-']").hide();
	$("div[data-permalink*='-autosave-']").hide();
})
</script>

</body>
</html>