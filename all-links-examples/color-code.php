<?php
$color_array = array();
$color_array['345a70'] = 'Slate Blue';
$color_array['8b543a'] = 'Copper';
$color_array['14402b'] = 'Evergreen';
$color_array['0d3c26'] = 'MC Green';
$color_array['6f2026'] = 'Barn Red';
$color_array['970e1e'] = 'Crimson Red';
$color_array['060606'] = 'Black';
$color_array['2f0e13'] = 'Burgundy';
$color_array['d0d0d0'] = 'Galvalume';
$color_array['9b9b9b'] = 'Clay';
$color_array['9f9f9f'] = 'Mc Gray';
$color_array['5b5b5b'] = 'Quaker Gray';
$color_array['898989'] = 'Pewter Gray';
$color_array['aa9179'] = 'Tan';
$color_array['492e28'] = 'Brown';
$color_array['d1ceba'] = 'Sandstone';
$color_array['ebe0d3'] = 'Beige';
$color_array['fbfbfb'] = 'White';

$new = array();
foreach($color_array as $key=>$value)
{
	$new[$key] = $value;
}
print_r($new);

echo json_encode($new);

?>