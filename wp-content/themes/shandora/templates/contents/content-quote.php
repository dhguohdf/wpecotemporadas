<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_singular( get_post_type() ) ) { ?>

		<header class="entry-header">
			<?php echo apply_atomic_shortcode( 'entry_title', the_title( '<h1 class="entry-title">', '</h1>', false ) ); ?>
			<?php echo apply_atomic_shortcode( 'entry_byline', '<div class="entry-byline">' . __( '[entry-icon class="show-for-large"] [entry-author] [entry-published format="M, d Y" text="'.__('Posted on ','bon').'"] [entry-terms taxonomy="category"] [entry-edit-link]', 'bon' ) . '</div>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-content clear">
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<p class="page-links">' . '<span class="before">' . __( 'Pages:', 'bon' ) . '</span>', 'after' => '</p>' ) ); ?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<?php echo apply_atomic_shortcode( 'entry_author_avatar', '[entry-author-avatar]'); ?>
			<?php echo apply_atomic_shortcode( 'entry_tag', '<div class="entry-tag">'.__('[entry-terms text="'.__('Tagged in:','bon').'"]', 'bon') . '</div>'); ?>
		</footer><!-- .entry-footer -->

	<?php } else { ?>

		<header class="entry-header">
			<?php echo apply_atomic_shortcode( 'entry_title', the_title( '<h3 class="entry-title"><a href="'.get_permalink().'" title="'.the_title_attribute( array('before' => 'Permalink to: ', 'echo' => false) ).'">', '</a></h3>', false ) ); ?>
			<?php echo apply_atomic_shortcode( 'entry_byline', '<div class="entry-byline">' . __( '[entry-icon class="show-for-large"] [entry-author] [entry-published format="M, d Y" text="'.__('Posted on ','bon').'"] [entry-edit-link]', 'bon' ) . '</div>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-summary">
			<?php the_content( __( 'Read more &rarr;', 'bon' ) ); ?>
			<?php wp_link_pages( array( 'before' => '<p class="page-links">' . '<span class="before">' . __( 'Pages:', 'bon' ) . '</span>', 'after' => '</p>' ) ); ?>
		</div><!-- .entry-summary -->


	<?php } ?>

</article><!-- .hentry -->