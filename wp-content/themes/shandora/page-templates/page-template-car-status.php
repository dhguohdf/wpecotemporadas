<?php 
/*
* Template Name: Car Status
*/
get_header(); 
?>
<div id="inner-wrap" class="slide ">

    <div id="body-container" class="container">

        <?php 

        /**
         * Shandora Before Loop Hook
         *
         * @hooked shandora_get_page_header - 1
         * @hooked shandora_search_get_listing - 2
         * @hooked shandora_open_main_content_row - 5
         * @hooked shandora_get_left_sidebar - 10
         * @hooked shandora_open_main_content_column - 15
         *
         */

        do_atomic('before_loop'); ?>

                <?php
                $numberposts = (bon_get_option('listing_per_page')) ? bon_get_option('listing_per_page') : 8;
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $status_val = get_post_meta(get_the_ID(), 'shandora_car_status_query', true ); 
                $orderby = '';
                $key = '';
                if(isset($_GET['search_orderby'])) {
                    $orderby = $_GET['search_orderby'];
                }
                $order = 'DESC';
                if(isset($_GET['search_order'])) {
                    $order = $_GET['search_order'];
                }
				
                if($orderby == 'price') {
                    $key = bon_get_prefix() . 'listing_price';
                    $orderby = 'meta_value_num';
                }

                $meta_query = array();

                if( $status_val != 'featured' ) {
                    $meta_query[] = array(
                        'key' => bon_get_prefix() . 'listing_status',
                        'value' => $status_val,
                        'compare' => '='
                    );
                } else {
                    $meta_query[] = array(
                        'key' => bon_get_prefix() . 'listing_featured',
                        'value' => true,
                        'compare' => '=',
                    );
                }

                $listing_args = array(
                        'post_type' => 'car-listing',
                        'posts_per_page' => $numberposts,
                        'paged' => $paged,
                        'meta_key' => $key,
                        'orderby' => $orderby,
                        'order' => $order,
                        'meta_query' => $meta_query
                    );

                $wp_query = new WP_Query($listing_args);

                bon_get_template_part('loop', 'car-listing'); ?>

                <?php bon_get_template_part( 'loop','nav' ); // Loads the loop-nav.php template. ?>
        <?php 

        /**
         * Shandora After Loop Hook
         *
         * @hooked shandora_close_main_content_column - 1
         * @hooked shandora_get_right_sidebar - 5
         * @hooked shandora_close_main_content_row - 10
         *
         */

        do_atomic('after_loop'); ?>

    </div>


<?php get_footer(); ?>
