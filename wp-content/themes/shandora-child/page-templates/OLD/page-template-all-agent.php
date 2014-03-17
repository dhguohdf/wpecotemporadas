<?php 
/*
* Template Name: Agents
*/
get_header(); 
        
?>
<div id="inner-wrap" class="slide ">

    <?php if(is_home()) { bon_get_template_part('loop', 'slider'); } ?>

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
                <ul class="listings <?php shandora_block_grid_column_class(); ?>">
                <?php
                $numberposts = (bon_get_option('listing_per_page')) ? bon_get_option('listing_per_page') : 8;
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                
                $wp_query = new WP_Query(
                    array(
                        'post_type' => 'agent',
                        'posts_per_page' => $numberposts,
                        'paged' => $paged 
                    )
                );
                ?>

                <?php if ( $wp_query->have_posts() ) : ?>


                    <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

                        <?php bon_get_template_part( 'content', get_post_type() ); ?>

                    <?php endwhile; ?>


                <?php else : ?>
                 
                    <?php bon_get_template_part( 'loop', 'error' ); // Loads the loop-error.php template. ?>

                <?php endif; ?>

                <?php if( current_theme_supports( 'bon-pagination' ) ){ bon_pagination(array('container_class'=> 'pagination-centered', 'disabled_class' => 'unavailable', 'current_class' => 'current')); } ?>

                <?php wp_reset_query(); ?>
                </ul>
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
