<?php
echo "<pre>";
//print_r($_SERVER);

$ipaddress = $_SERVER['REMOTE_ADDR'];
$ipaddress = "1.2.3.4";

$northern = array('PA', 'NY', 'NJ', 'NH', 'MD', 'CT', 'MA', 'ME', 'VT'); 
$flag = 'default';
$ipaddress = "35.237.30.239";
echo $url = "http://www.geoplugin.net/php.gp?ip=".$ipaddress;
$json = file_get_contents($url);
$json = unserialize($json);

if(isset($json['geoplugin_regionCode']) && $json['geoplugin_regionCode']!='')
{
	if(in_array($json['geoplugin_regionCode'], $northern))
	{
		$flag = 'northern';
	}
	else
	{
		$flag = 'default';
	}
}
else
{
	$flag = 'default';
}



echo "</pre>";




echo "<br>=============<br>";
echo date_default_timezone_get();
echo "<br>".date("Y-m-d H:i:s");
echo "<br>=============<br>";
date_default_timezone_set('America/Los_Angeles');
echo date_default_timezone_get();
echo "<br>".date("Y-m-d H:i:s");
echo "<br>=============<br>";
date_default_timezone_set('US/Eastern');
echo date_default_timezone_get();
echo "<br>".date("Y-m-d H:i:s");
echo "<br>=============<br>";
?>