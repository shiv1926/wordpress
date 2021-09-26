<?php
/**
* Template Name: Blog
*/
get_header(); 
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'paged' => $paged,
    'posts_per_page' => 3
);
query_posts($args);
if(have_posts()) 
{
    while(have_posts())
    {
        the_post();
        ?>
            <div class="entry">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="entrycontent"><?php the_content('Read more...'); ?>
                </div>
            </div>
        <?php
    }
    ?>    
    <div class="navigation">
    <div class="alignright"><?php next_posts_link('Next') ?></div>
    <div class="alignleft"><?php previous_posts_link('Previous') ?></div>
    </div>
    <?php
}
?>