<?php 
set_time_limit(0);
include("simple_html_dom.php"); 
?>
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

if(isset($_GET['start']) && $_GET['start']!='') {
	$start = $_GET['start'];
} else {
	$start = 0;
}

$querystr="SELECT ID,post_content FROM wp_posts where post_status='publish' and ( post_type='page' || post_type='post' || post_type='csr_gallery' || post_type='gallery' || post_type='infographics' || post_type='locations' || post_type='newsletter_archive' || post_type='product' || post_type='testimonial' ) order by ID asc";

$posts_array = $wpdb->get_results($querystr, OBJECT);
$counter = 1; $update_counter = 1;
foreach ($posts_array as $key => $value) 
{
	$html = new simple_html_dom();
	$string=$value->post_content;
	$html->load($string);
	$update = false;
	echo '<div data-permalink="'.get_permalink($value->post_id).'">';
	echo get_permalink($value->ID).'<br>';
	foreach($html->find('a[href*="www.carportcentral.com"]') as $e) 
	{
	    $last = substr(trim($e->href), -1);
	    if($last=='/') 
	    {
		    echo $e->href . '<br>';
	    	$update = true;
	    	$string = str_replace($e->href, substr($e->href,0,-1), $string);
	    }
	}
	if($update==true)
	{
		$my_post = array('ID'=>$value->ID, 'post_content'=>$string);
	  	//wp_update_post( $my_post );
	  	$update_counter++;
	}
	echo "<br>========================================<br>";
	$html->clear(); 
	unset($html);
	echo 'count = '.$counter.', update = '.$update_counter.'<br>';
	echo '</div>';
	$counter++;
}
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