<?php 
/*
* Template Name: Listings
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
                $orderby = bon_get_option('listing_orderby');
                $order = bon_get_option('listing_order', 'DESC');
                $key = '';

                switch ( $orderby ) {
                    case 'price':
                        $orderby = 'meta_value_num';
                        $key = bon_get_prefix() . 'listing_price';
                        break;
                    
                    case 'title':
                        $orderby = 'title';

                        break;

                    case 'size':
                        $orderby = 'meta_value_num';
                        $key = bon_get_prefix() . 'listing_buildingsize';

                        break;

                    default:
                        $orderby = 'date';
                        break;
                }
                
                if(isset($_GET['search_orderby'])) {
                    $orderby = $_GET['search_orderby'];
                }
                
                if(isset($_GET['search_order'])) {
                    $order = $_GET['search_order'];
                }
                
                $listing_args = array(
                        'post_type' => 'listing',
                        'posts_per_page' => $numberposts,
                        'paged' => $paged,
                        'meta_key' => $key,
                        'orderby' => $orderby,
                        'order' => $order
                    );

                $wp_query = new WP_Query($listing_args);

                bon_get_template_part('loop', 'listing');

                bon_get_template_part( 'loop','nav' ); // Loads the loop-nav.php template. ?>
                
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
