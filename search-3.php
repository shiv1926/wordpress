<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package amb
 */
get_header();
global $post;
$pageName=$post->post_title;
?>
<style type="text/css">
.search-page-top { text-align: center; margin-top: 50px; margin-bottom: 50px; }
.search-page-top .search-box { width: 100%; max-width: 650px; margin: 0 auto; }
.search-page-top .search-box #header-search-input { padding-top: 18px; padding-bottom: 18px; }
.pagination { margin-bottom: 0; display: flex; padding-left: 0; list-style: none; border-radius: 4px;
    }
.page-link, .page-numbers, .pagination>a {  position: relative; display: flex; align-items: center;
    justify-content: center; padding: 5px; margin: 0; line-height: 1.25; color: #282221;
    background-color: #F5F7F9; border: 1px solid #D9D9D9; text-align: center; min-width: 46px;
    min-height: 46px; }
nav.pagination-nav {
    margin-top: 40px;
}
li.page-item a {
    background: #3c3a6e;
    color: #fff;
    font-weight: bold;
    border: 0.5px solid #fff;
}
.pagination { margin-bottom: 0; display: flex; padding-left: 0; list-style: none; border-radius: 4px;
    }
.page-link, .page-numbers, .pagination>a {  position: relative; display: flex; align-items: center;
    justify-content: center; padding: 5px; margin: 0; line-height: 1.25; color: #282221;
    background-color: #F5F7F9; border: 1px solid #D9D9D9; text-align: center; min-width: 46px;
    min-height: 46px; }
ul.pagination li.page-item span.page-numbers.current { border-color: #b00020; }
.search-box {
    max-width: 400px;
    width: 100%;
    margin: auto;
}
</style>
<section class="home-product-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center section-heading">
                <h2>Search Results for - <b><?php echo $_GET['s'];?></b></h2>
                <div class="search-box">
                     <div class="form-group pmd-textfield pmd-textfield-filled pmd-textfield-floating-label">
                         <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">  
                            <div class="pmd-textfield-filled-wrapper">
                                <input type="text" class="form-control" name="s" placeholder="Search Your Building" id="header-search-input" autocomplete="off" value="<?php if (isset($_GET["s"])) { $a = $_GET["s"]; echo $a; }?>">
                                <button type="button" class="btn btn-primary search-btn header-search-submit d-none" data-toggle="modal"><span class="icon-magnifier icon"></span></button>
                             </div>
                         </form>  
                     </div>
                 </div>
            </div>
            
        </div>
        <div class="row product-style-2" id="productBlock">
            <div id="productHtml" class="w-100">
<?php  
		global $wpdb;
		if (isset($_GET['page'])) {
		$paged = $_GET['page'];
		} else {
		$paged = 1;
		}

		$post_per_page = 6;
		$offset = ($paged - 1)*$post_per_page;
		$search_query='';
		$query=$_GET['s'];
		$data=array(); 
		$product_list='';  
		$no_product='';
		if(!empty($query))
		{
		
		$search_query="SELECT amb_posts.post_title, amb_posts.ID FROM $wpdb->posts JOIN $wpdb->postmeta m1 ON $wpdb->posts.ID = m1.post_id LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
			LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
			LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)
			WHERE ($wpdb->posts.post_title LIKE '%{$query}%' OR (meta_key='_sku' AND meta_value LIKE '%{$query}%') OR $wpdb->terms.name LIKE '%{$query}%') AND $wpdb->posts.post_type = 'product' AND  $wpdb->posts.post_status = 'publish' AND $wpdb->term_taxonomy.taxonomy = 'product_cat'  GROUP BY $wpdb->posts.ID ORDER BY $wpdb->posts.post_date DESC";
		
		$total_record = count($wpdb->get_results($search_query, ARRAY_A));
		$max_num_pages  = ceil($total_record/ $post_per_page);
		$wp_query->found_posts = $total_record;
		$wp_query->max_num_pages = $max_num_pages;

		$limit_query    =   " LIMIT ".$post_per_page." OFFSET ".$offset;

		$pageposts =   $wpdb->get_results($search_query.$limit_query, ARRAY_A);
		$num_rows=$wpdb->num_rows;
		
		if($num_rows > 0 )
		{
		  foreach($pageposts as $products)
		  {
			$product_id=$products['ID'];
			$product = wc_get_product($product_id);
			$product_list .= createProductLayout($product);
		   } 
                   
		}
		else
		{
		
			$no_product .= '<div class="container">';
			$no_product .= '<div class="row">';
			$no_product .= '<div class="col-lg-12">';
			$no_product .= '<div class="search-product-not-found-wrapper">';
			$no_product .= '<div class="search-product-not-found mx-auto">';
			$no_product .= '<img src="'.get_template_directory_uri().'/img/search-not-found.svg" class="img-fluid" "'.alt_title().'">';
			$no_product .= '<h5>No Product Found</h5>';
			$no_product .= "<p>Didn't find what you are looking for, try our <a class='text-secondary' href='https://americanmetalbuildings.sensei3d.com'>3D Building Designer</a> to design a building for your needs here</p>";
			$no_product .= '<a class="btn btn-primary btn-lg" href="https://americanmetalbuildings.sensei3d.com/">Design in 3D Estimator</a>';
			$no_product .= '</div>';
			$no_product .= '</div>';
			$no_product .= '</div>';
			$no_product .= '</div>';
			$no_product .= '</div>';
		
		}
		}
		echo $product_list.$no_product;
?>
            </div>
         <div class="clearfix" id="inView"></div><br />
        </div>
          <?php 
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
                        ) );

                        if( is_array( $pages ) ) {
                        echo "<center><nav class='pagination-nav'>";

                         echo '<ul class="pagination justify-content-center">';
                          foreach ( $pages as $page ) 
                          {
                            echo '<li class="page-item">';
                            echo $page;
                            echo '</li>';
                          }

                         echo '</ul>';

                         echo "</nav></center>\n";
                       } 
					   ?>
        <div class="clearfix" id="inView"></div>
    </div>
</section>
<?php get_footer(); ?>
