<?php
/**
* The template for displaying search results pages
* @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
* @package garagebuildings
*/
get_header();
?>
<section class="blog-list">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumbs">
                    <?php include(TEMPLATEPATH.'/inc/breadcrumbs.php'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="h1 text-center">Search Results: <?php echo $s; ?></div>
            </div>
        </div>
        <div class="row blog_list_box">
            <div class="col-lg-8 left-section">
                <?php
                $s = get_query_var('s');
                $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1; 
                global $wpdb;
                if (isset($_GET['page'])) {
                    $paged = $_GET['page'];
                } else {
                    $paged = 1;
                }
                $post_per_page = 10;
                $offset = ($paged - 1)*$post_per_page;
                $search_query="SELECT * FROM $wpdb->posts JOIN $wpdb->postmeta m1 ON $wpdb->posts.ID = m1.post_id LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) WHERE ($wpdb->posts.post_title LIKE '%{$s}%' OR (meta_key='_sku' AND meta_value LIKE '%{$s}%') OR $wpdb->terms.name LIKE '%{$s}%') AND $wpdb->posts.post_type IN ('product', 'post') AND  $wpdb->posts.post_status = 'publish' GROUP BY $wpdb->posts.ID ORDER BY FIELD(post_type, 'product', 'post') ASC, $wpdb->posts.post_date DESC";
                $total_record = count($wpdb->get_results($search_query, ARRAY_A));
                $max_num_pages  = ceil($total_record/ $post_per_page);
                $wp_query->found_posts = $total_record;
                $wp_query->max_num_pages = $max_num_pages;
                $limit_query = " LIMIT ".$post_per_page." OFFSET ".$offset;
                $pageposts =  $wpdb->get_results($search_query.$limit_query, ARRAY_A);
                if($pageposts)
                {
                    foreach ($pageposts as $post)
                    {
                        $shop_single = wp_get_attachment_image_src(get_post_thumbnail_id($post['ID']),'medium_large');
                        echo '<article>';
                        echo '<div class="blog-container">';
                        if($shop_single[0])
                        {
                            echo '<div class="image-con">';
                            echo '<a href="'.get_permalink($post['ID']).'">';
                            echo '<img width="'.$shop_single[1].'" height="'.$shop_single[2].'" src="'.$shop_single[0].'" class="img-fluid" '.alt_title($post['post_title']).'>';
                            echo '</a>';
                            echo '<div class="blog-date">'.showdate($post['post_date'],'blog').'</div>';
                            echo '</div>';
                        }
                        echo '<div class="blog-info editor_content">';
                        echo '<a href="'.get_permalink($post['ID']).'"><div class="post-title h5">'.$post['post_title'].'</div></a>';
                        echo '<div class="post-desc"><p>'.SmallContent(strip_tags($post['post_content']), 100).'</p></div>';
                        echo '<a href="'.get_permalink($post['ID']).'" class="readmore">Read More'.get_svg('right_arrow').'</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</article>';
                    }
                    wp_reset_postdata();
                }
                else
                {
                    echo '<div class="nomorepost">Sorry, no post found.</div>';
                }

                $big = 999999999;
                $pages = paginate_links( array(
                    'base' => add_query_arg( 'page', '%#%' ),
                    'format' => '?page=%#%',
                    'current' => $paged,
                    'total' => ceil($total_record / $post_per_page),
                    'prev_next' => false,
                    'type'  => 'array',
                    'prev_next'   => TRUE,
                    'prev_text'    => __('Previous'),
                    'next_text'    => __('Next'),
                ));
                if(is_array($pages)) 
                {
                    echo "<nav class='blog-pagination'>";
                    echo '<ul class="pagination no_bullet_point">';
                    foreach($pages as $page)
                    {
                        echo '<li>'.$page.'</li>';
                    }
                    echo '</ul>';
                    echo "</nav>";
                }
                ?>
            </div>
            <div id="sidebar_box" class="col-lg-4 blog-sidebar">
                <?php include(TEMPLATEPATH."/sidebar-products.php"); ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</section>
<?php get_footer();