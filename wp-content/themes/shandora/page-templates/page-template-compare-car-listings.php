<?php 
/*
* Template Name: Compare Car Listings
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
                        
                    $prefix = bon_get_prefix();    
                    
                    $compare_id = '';

                    if($_GET['compare']) {
                        
                        $ex = explode(",", esc_html($_GET['compare']));
                        if( (isset($ex[0]) && is_numeric($ex[0])) && ( isset($ex[1]) && is_numeric($ex[1])) ) {
                            $compare_id = $ex;
                        }
                    }

                    $compare_args = array(
                                'post_type' => 'car-listing',
                                'posts_per_page' => 2,
                                'post__in' => $compare_id,                      
                    );
                    
                    // wp query
                    $wp_query = new WP_Query( $compare_args );
                    

                    ?>

                <?php if ( $wp_query->have_posts() ) :  $tb = array(); $j = 0;

                    $output_link = '';
                    $output_title = '';
                    $output_featured = '';

                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

                        <?php

                            $reg = shandora_get_meta($post->ID, 'listing_reg');
                            $engine = shandora_get_meta($post->ID, 'listing_enginesize');
                            $enginetype = shandora_get_meta($post->ID, 'listing_enginetype');
                            $transmission = shandora_get_meta($post->ID, 'listing_transmission');
                            $ancap = shandora_get_meta($post->ID, 'listing_ancap');
                            $mileage = shandora_get_meta($post->ID, 'listing_mileage');
                            $price = shandora_get_meta($post->ID, 'listing_price');
                            $extcolor = shandora_get_meta($post->ID, 'listing_extcolor');
                            $intcolor = shandora_get_meta($post->ID, 'listing_intcolor');
                            $status = shandora_get_meta($post->ID, 'listing_status');
                            $fuel = shandora_get_meta($post->ID, 'listing_fueltype');

                            $height = shandora_get_meta($post->ID, 'listing_height');
                            $width = shandora_get_meta($post->ID, 'listing_width');
                            $length = shandora_get_meta($post->ID, 'listing_length');
                            $wheelbase = shandora_get_meta($post->ID, 'listing_wheelbase');
                            $trackrear = shandora_get_meta($post->ID, 'listing_trackrear');
                            $trackfront = shandora_get_meta($post->ID, 'listing_trackfront');
                            $ground = shandora_get_meta($post->ID, 'listing_ground');

                            $seating = shandora_get_meta($post->ID, 'listing_seating');
                            $steering = shandora_get_meta($post->ID, 'listing_steering');

                            $title = get_the_title($post->ID);

                            $length_measure = bon_get_option('length_measure', 'in.');
                            $mileage_measure = bon_get_option('mileage_measure', 'in.');

                            $trans_opt = shandora_get_car_search_option('transmission');
                            if(array_key_exists($transmission, $trans_opt)) {
                              $transmission = $trans_opt[$transmission];
                            }
                            $status_opt = shandora_get_car_search_option('status');
                            if(array_key_exists($status, $status_opt)) {
                              $status = $status_opt[$status];
                            }

                            $terms = get_the_terms( $post->ID, 'body-type' );

                            $bodytype = array();    
                            if ( $terms && ! is_wp_error( $terms ) ) 
                            {                                                            
                                 foreach ( $terms as $term )
                                 {                                 
                                  $bodytype[] = $term->name;
                                 }                                                                                         
                            }
                            $bodytype = implode(', ', $bodytype);

                            $terms = get_the_terms( $post->ID, 'car-feature' );

                            $feature = array();    
                            if ( $terms && ! is_wp_error( $terms ) ) 
                            {                                                            
                                 foreach ( $terms as $term )
                                 {                                 
                                  $feature[] = $term->name;
                                 }                                                                                         
                            }
                            $fe