<?php
/**
* The template for displaying search results pages
* @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
* @package garagebuildings
*/
get_header();
$s = get_query_var('s');
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1; 
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
                $params = array();
                $params['search']=$s;
                $products = getProducts('',$paged,10,$params);
                if($products['totalpost']>0)
                {
                    echo '<div class="blog-list-con">';
                    foreach($products['products'] as $singleproduct)
                    {
                        echo '<article>';
                        echo GetSingleBlogHTML($singleproduct,'product_cat');
                        echo '</article>';
                    }
                    echo '</div>';
                    if($products['totalpages']>1)
                    {
                        $firstpage = get_pagenum_link(1);
                        if(strpos($firstpage,'?')) 
                        {
                            $pagelink = explode("?",$firstpage);
                            $firstpage = $pagelink[0] . '%_%' .'?'.$pagelink[1];
                        }
                        else
                        {
                            $firstpage = $firstpage . '%_%';
                        }

                        echo '<div class="blog-pagination">';
                        $pagination_args = array(
                            'base'            => $firstpage,
                            'format'          => 'page/%#%',
                            'total'           => $products['totalpages'],
                            'current'         => $paged,
                            'show_all'        => False,
                            'end_size'        => 1,
                            'mid_size'        => 2,
                            'prev_next'       => true,
                            'prev_text'       => __('Prev'),
                            'next_text'       => __('Next'),
                            'type'            => 'array',
                            'add_args'        => false,
                            'add_fragment'    => ''
                        );
                        $paginate_links = paginate_links($pagination_args);
                        if(is_array($paginate_links)) {
                            echo '<ul class="pagination no_bullet_point">';
                            foreach ( $paginate_links as $page ) {
                                echo "<li>$page</li>";
                            }
                            echo '</ul>';
                        }

                        echo '</div>';
                    }
                    wp_reset_postdata();
                }
                else
                {
                    echo '<div class="nomorepost">Sorry, no post found.</div>';
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
<?php
get_footer();