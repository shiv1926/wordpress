<?php
/**
* The template for displaying Search Results pages
*
*/
get_header();
$s=get_search_query();
?>
<style>
  .search-input-area { position: relative; display: flex; border-collapse: separate; }
  .search-button { background-color: #D12029; color: #FFFFFF; border-top-left-radius: 0;
    border-bottom-left-radius: 0; }
</style>
<?php 
global $wpdb;
                    
    if (isset($_GET['page'])) {
    $paged = $_GET['page'];
    } else {
    $paged = 1;
    }

    $post_per_page = 10;
    $offset = ($paged - 1)*$post_per_page;

 $search_query="SELECT wp_posts.post_title, wp_posts.ID FROM $wpdb->posts JOIN $wpdb->postmeta m1 ON $wpdb->posts.ID = m1.post_id LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
    LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
    LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)
    WHERE ($wpdb->posts.post_title LIKE '%{$s}%' OR (meta_key='_sku' AND meta_value LIKE '%{$s}%') OR $wpdb->terms.name LIKE '%{$s}%') AND $wpdb->posts.post_type IN ('product', 'post') AND  $wpdb->posts.post_status = 'publish' GROUP BY $wpdb->posts.ID ORDER BY FIELD(post_type, 'product', 'post') ASC, $wpdb->posts.post_date DESC";


   $total_record = count($wpdb->get_results($search_query, ARRAY_A));

    $max_num_pages  = ceil($total_record/ $post_per_page);
    $wp_query->found_posts = $total_record;
    $wp_query->max_num_pages = $max_num_pages;

    $limit_query    =   " LIMIT ".$post_per_page." OFFSET ".$offset;

    $pageposts =   $wpdb->get_results($search_query.$limit_query, ARRAY_A);
?>

<main class="main" role="main">
   <section class="section-content section-page-header search-header triangle-pattern" data-aos="fade-in" data-aos-offset="0" data-aos-duration="600">
         <div class="container container-xl-sm">
            <div class="content-panel">
               <div class="content-heading text-left">
                  <?php if(!empty($s)) { ?>
                 <!--  <p class="mb-0">
                     <strong>Search Results for:</strong>
                  </p> -->                     
                  <h1 class="content-title mb-10">Search Results</h1>
                  <?php } ?> 
                  <div class="form-group form-search">
                     <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" class="input-icon right">
                        <div class="input-icon right search-input-area">
                           <input type="text" name="s" class="form-control" placeholder="Search Results" value="<?php if (isset($_GET["s"])) { $a = $_GET["s"]; echo $a; }?>">
                           <button type="submit" class="btn btn-search search-button">Search</button>
                        </div>
                     </form> 
                  </div>
                  
               </div>

            </div>
         </div>
      </section> 
        <section class="content-section section-result bg-white" data-aos="fade-in" data-aos-offset="0" data-aos-duration="600">          
            <div class="content-panel">  
                <div class="content-body">
                    <?php
                    if ($pageposts):
                    ?>
                    <div class="container container-xl-md">
                      <p class="number-search-result">Your search for <?php echo $s; ?> has returned <?php echo $total_record; ?> <?php if($total_record > 1) { echo 'results'; } else { echo 'result'; } ?></p>
                        <div class="card-group-search">
                           <?php
                            $cat_arr = array();
                            $cat_top_content='';
                            global $post; 
                            foreach ($pageposts as $post):

                            $post_title=$post['post_title'];
                            $post_excerpt=$post['post_excerpt'];

                            $product_name_arr=explode(" ",$post_title);
                            $product_size=$product_name_arr[0];

                            $post_id=$post['ID'];

                            $cat_terms = get_the_terms( $post_id, 'product_cat' );

                             if(!empty($cat_terms)) {
                             foreach ( $cat_terms as $cat_term ) {
                             $cat_arr[] = $cat_term->term_id;
                             }

                             $product_layout=get_post_meta($post_id, 'product_layout', true);

                            if($product_layout=='3d_product')
                              {
                               $cat_top_content = '<p>Are you in the market for a steel structure – top only or a garage building? Our standard and triple wide carport garages are apt for a wide spectrum of applications including residential car garages, repair workshops, automobile canopies, and more. On this page we look at some sample '.$post_title.' pictures and discuss the features, pricing, customization options, roofing styles, loads &amp; codes, and uses of a '.$product_size.' metal building. At Boss Buildings, we have choicest metal buildings and their latest updated pricing.</p>';
                              }
                            else if (in_array(118, $cat_arr)) {
                               $cat_top_content = '<p>Are you in the market for a steel structure that is extra wide? Our commercial steel buildings offer a wide range of steel buildings and their latest updated pricing. On this page we look at some sample '.$post_title.' pictures and discuss the features, pricing, customization options, roofing styles, loads &amp; codes, and uses of a '.$product_size.' metal building. We also answer your questions about a '.$product_size.' clear-span steel building, and answer some commonly-asked questions about these prefabricated steel structures.</p>';
                               }
                            }

                            $post_type = get_post_type( $post_id );
                            $price=get_post_meta( $post_id, '_regular_price', true );
                            $image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'full', false, '' );
                            $post_url=get_permalink($post_id);
                            if($post_type=='product')
                            {
                              echo '<div class="card card-search">'; 
                              echo '<div class="row">'; 
                              
                              echo '<div class="col-sm">'; 
                              echo '<div class="card-body ph-0">'; 
                              echo '<span class="badge badge-yellow">'.ucfirst($post_type).'</span>'; 
                              echo '<h5 class="card-title mb-0"><a href="'.$post_url.'">'.$post_title.'</a></h5>'; 
                              if(!empty($cat_top_content)) {
                                echo $cat_top_content;
                              }
                              /*
                              if(!empty($price)) {
                                 echo '<span class="price">Starting Price:<strong>$'.$price.'*</strong></span>';
                              }
                              */
                              echo '</div>'; 
                              echo '</div>';
                              echo '<div class="col-lg-3 col-md-4 col-sm-5">';
                              
                              echo '<figure class="img-panel">'; 
                              echo '<a class="block" href="'.$post_url.'">'; 
                              echo '<img src="'.get_template_directory_uri(). '/assets/img/blank-3x2.png" class="img-full">';
                              if(!empty($image))
                              {
                              echo '<img src="'.$image[0].'" class="main-img" alt="'.$post_title.'">';
                              }
                              else
                              {
                                echo '<img src="'.get_template_directory_uri().'/assets/img/no-image.jpg'.'" class="main-img" alt="'.$post_title.'">';
                              } 
                              echo '</a>'; 
                              echo '</figure>';
                              echo '</div>'; 
                              echo '</div>';
                              echo '</div>'; 
                              }
                              else
                              {
                              echo '<div class="card card-search">'; 
                              echo '<div class="card-body ph-0">'; 
                              if($post_type=='post')
                                 {
                                    echo '<span class="badge badge-secondary">Blog</span>';
                                 }
                              else if($post_type=='page')
                                 {
                                    echo '<span class="badge badge-primary">'.ucfirst($post_type).'</span>';
                                 }
                              else if($post_type=='locations')
                                 {
                                    echo '<span class="badge badge-green">'.ucfirst($post_type).'</span>';
                                 }
                              echo '<h5 class="card-title mb-0"><a href="'.$post_url.'">'.$post_title.'</a></h5>';
                              echo $post_excerpt; 
                              echo '</div>'; 
                              echo '</div>';
                              }

                            setup_postdata($post);
                            endforeach; ?>
                            
                       </div>
                       <?php 
                        else : 
                          echo '<div class="nodata" data-aos="fade-in" data-aos-duration="500" data-aos-easing="ease-in-sine">
                              <div class="nodata_body">
                                    <figure class="nodata_figure mb-0">
                                       <img src="'.get_template_directory_uri(). '/assets/img/no-search.png" alt="result not found" class="img-fluid">
                                    </figure>
                                    <h1 class="nodata_title nodata_title_bg h4">No Result Found Please Try With an Alternate Word.</h1>
                              </div>
                           </div></p>';
                        endif; 

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
                        echo "<nav class='pagination-nav' data-aos='fade-in-up'>";

                         echo '<ul class="pagination">';
                          foreach ( $pages as $page ) 
                          {
                            echo '<li class="page-item">';
                            echo $page;
                            echo '</li>';
                          }

                         echo '</ul>';

                        echo "</nav>\n";
                      }
                             
                       ?>
                    </div>
                </div>
            </div>
        </section>
          <?php include(TEMPLATEPATH."/inc/info.php"); ?>
      </main>
      <!-- main -->
<?php get_footer(); ?>