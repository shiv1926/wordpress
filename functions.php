<?php 
function wp_url($file='')
{
    if($_SERVER['HTTP_HOST']=='shiv-pc') {
        return 'http://shiv-pc/all_tutorials/wordpress/'.$file;
    } else {
        return 'http://localhost/all_tutorial/wordpress/'.$file;
    }
}

function refrences($links)
{
    $return='<div>&nbsp;</div>';
    $return.='<h4>Refrences</h4>';
    foreach($links as $link)
    {
        $return.='<div>'.$link.'</div>';
    }
    return $return;
}
?>