<?php include("simple_html_dom.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="">
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body>
<?php
include_once("../../wp-load.php");

if(isset($_GET['start']) && $_GET['start']!='') {
	$start = $_GET['start'];
} else {
	$start = 0;
}


// THERE ARE ONLY 40 TERM TO UPDATE, THEREFORE UPDATE THESE MANUALLY
$querystr="SELECT term_taxonomy_id, term_id, taxonomy, description FROM wp_term_taxonomy where description!='' order by term_taxonomy_id asc";
$posts_array = $wpdb->get_results($querystr, OBJECT);
$counter = 1; $update_counter = 1;
foreach ($posts_array as $key => $value) 
{
	
	echo $value->term_id.'<br>';
	$html = new simple_html_dom();
	$string=$value->description;
	$html->load($string);
	$update = false;
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
		$execut= $wpdb->query("UPDATE wp_term_taxonomy SET description = '".$string."' WHERE term_taxonomy_id = '".$value->term_taxonomy_id."'");
		$update_counter++;
	}
	echo "<br>========================================<br>";
	$html->clear(); 
	unset($html);
	echo 'count = '.$counter.', update = '.$update_counter;
	$counter++;
}
?>
</body>
</html>