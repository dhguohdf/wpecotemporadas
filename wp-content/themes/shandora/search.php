<?php get_header(); ?>

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
   

        	 <?php if ( have_posts() ) : ?>

                <?php while ( have_posts() ) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" class="<?php bon_entry_class(); ?>">

						<header class="entry-header">
							<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
							<?php echo apply_atomic_shortcode( 'entry_byline', '<div class="entry-byline">' . __( '[entry-author] [entry-published format="M, d Y" text="Posted"] [entry-edit-link]', 'bon' ) . '</div>' ); ?>

						</header><!-- .entry-header -->

						<div class="entry-summary clear">
							<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array('image_scan' => true, 'before' => '<div class="featured-image">', 'after' => '</div>' ) ); ?>
							<?php the_excerpt(); ?>
						</div><!-- .entry-summary -->

						<footer class="entry-footer">
							<div class="entry-permalink">&mdash; 
								<code>
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute( array('before' => __('Permalink to:','bon'))); ?>">
										<?php the_permalink(); ?>
									</a>
								</code>
							</div>
						</footer><!-- .entry-footer -->

					</article><!-- .hentry -->

                <?php endwhile; ?>

            <?php else : ?>

                <?php bon_get_template_part( 'loop', 'error' ); // Loads the loop-error.php template. ?>

            <?php endif; ?>


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