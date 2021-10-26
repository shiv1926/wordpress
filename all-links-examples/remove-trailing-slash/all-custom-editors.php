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
//$querystr = "SELECT *  FROM `wp_postmeta` WHERE `meta_value` LIKE '%www.carportcentral.com%' ORDER BY `wp_postmeta`.`post_id` ASC";
/*
'roof_style_content', 
'verticle_roof_style_-_best_content', 
'boxed_eave_style_content', 
'regular_good_content', 
'choosing_the_right_size_width_content', 
'choosing_the_right_size_length_content', 
'choosing_the_right_size_height_content', 
'difference_between_14_gauge_and_12_gauge_framing_content', 
'certified_v._non-certified_buildings_content', 
'barn_style_aframe_content', 
'barn_style_vertical_content', 
'enclosing_the_garage', 
'why_choose_a_steel_workshop', 
'additional_option_content', 
'colors_content', 
'installation_and_site_prep', 
'topics_in_this_barn_buying_guide', 
'short_description', 
'get_in__touch_description', 
'description', 
'dealer_description', 
'social_link', 
'home_top_text', 
'top_selling_products', 
'contact', 
'text_above_slider', 
'text_below_slider', 
'short_description', 
'get_in_touch_description', 
'bottom_description', 
'price_include', 
'metal_price_bottom_content', 
'category_second_para_content', 
'category_introduction_text', 
'applicable_area', 
'banner_content',
'team_bios',
'carport_central_process_description',
'content',
'answer',
'description',
*/

$querystr = "SELECT meta_id, post_id, meta_key, meta_value FROM wp_postmeta where meta_key like '%www.carportcentral.com%' or meta_value like '%www.carportcentral.com%' ";
//$querystr = "SELECT meta_id, post_id, meta_key, meta_value FROM wp_postmeta where meta_key='dealer_description' ";

$posts_array = $wpdb->get_results($querystr, OBJECT);
$counter = 1; $update_counter = 1;
$html = new simple_html_dom();
foreach ($posts_array as $key => $value) 
{
	$string=$value->meta_value;
	$html->load($string);
	$update = false;
	echo '<div data-permalink="'.get_permalink($value->post_id).'">';
	echo '<a href="'.get_permalink($value->post_id).'">'.get_permalink($value->post_id).'</a><br>';
	foreach($html->find('a[href*="www.carportcentral.com"]') as $e) 
	{
	    echo $e->href . '<br>';
	    $last = substr(trim($e->href), -1);
	    if($last=='/') {
	    	$update = true;
	    	$string = str_replace($e->href, substr($e->href,0,-1), $string);
	    }
	}
	if($update==true)
	{
		//update_post_meta($value->post_id, $value->meta_key, $string);
	  	$update_counter++;
	}
	echo "<br>========================================<br>";
	echo 'count = '.$counter.', update = '.$update_counter.'<br>';
	echo '</div>';
	$counter++;
}
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