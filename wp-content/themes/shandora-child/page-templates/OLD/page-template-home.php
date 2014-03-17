<?php 
/*
* Template Name: Home
*/

get_header(); ?>

<div id="inner-wrap" class="slide ">

    	<?php bon_get_template_part('loop', 'slider'); ?>

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

           
	            <?php if(have_posts()) : while(have_posts()) : the_post(); ?>

	            <?php the_content(); ?>
	            
	            <?php endwhile; endif; ?>
	       		
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