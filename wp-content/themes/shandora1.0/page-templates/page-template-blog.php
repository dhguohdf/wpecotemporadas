<?php 
/* 
Template Name: Blog 
*/
get_header(); ?>

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
            
            <?php $wp_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => get_option('posts_per_page')
                ));

                ?>

            <?php if ( have_posts() ) : ?>

                <?php while ( have_posts() ) : the_post(); ?>

                    <?php bon_get_template_part( 'content', ( post_type_supports( get_post_type(), 'post-formats' ) ? get_post_format() : get_post_type() ) ); ?>

                    <?php if ( is_singular() && post_type_supports( get_post_type(), 'comments') ) { comments_template(); } // Loads the comments.php template. ?>

                <?php endwhile; ?>

            <?php else : ?>

                <?php bon_get_template_part( 'loop', 'error' ); // Loads the loop-error.php template. ?>

            <?php endif; ?>


                <?php wp_reset_query(); ?>

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
