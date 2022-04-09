<?php
include('../../wp-load.php');
$templates = wp_get_theme()->get_page_templates();
$list = '';
echo '<table border="1" cellpadding="0" cellspacing="0">';
foreach($templates as $key=>$value)
{
$args = array(
   'post_type' => 'page',
   'posts_per_page' => -1,
   'orderby' => 'title',
   'order'   => 'ASC',
   'meta_query' => array(
       array(
           'key' => '_wp_page_template',
           'value' => $key
       )
   )
);
$the_pages = new WP_Query( $args );

echo '<tr>';
echo '<td>'.$key.'</td>';
echo '<td>'.str_replace("page template/","",$value).'</td>';
echo '<td>'.$the_pages->found_posts.'</td>';
echo '<tr>';

/*
$cdata = file_get_contents('sample.php');
$cdata = str_replace("template_name_here",$key,$cdata);

$filename = strtolower(trim(str_replace("page template/","",$key)));
$fp = fopen("template-".$filename,"w");
    fwrite($fp,$cdata);
    fclose($fp);

    $list.= '<div><a href="template-'.$filename.'" target="_blank">'.$filename.'</a></div>';
$fp = fopen("index.php","w");
    fwrite($fp,$list);
    fclose($fp);
    */
}
echo '</table>';
?>