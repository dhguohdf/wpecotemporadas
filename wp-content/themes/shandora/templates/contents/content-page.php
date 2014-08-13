<?php

global $bonbuilder;
$builder = get_post_meta(get_the_ID(), bon_get_prefix() . 'builder', true);
if($builder) {
	the_content();
} else {

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_singular( get_post_type() ) ) { ?>

		<header class="entry-header clear">
			<?php echo apply_atomic_shortcode( 'entry_title', the_title( '<h1 class="entry-title">', '</h1>', false ) ); ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<p class="page-links">' . '<span class="before">' . __( 'Pages:', 'bon' ) . '</span>', 'after' => '</p>' ) ); ?>
		</div><!-- .entry-content -->

		<?php if(!is_page_template( 'page-templates/page-template-idx.php' ) && !is_page_template( 'page-templates/page-template-idx-details.php' ) ) : ?>
		<footer class="entry-footer">
			<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">[entry-edit-link]</div>' ); ?>
		</footer><!-- .entry-footer -->
		<?php endif; ?>

	<?php } else { ?>

		<header class="entry-header">
			<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-summary">
			<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'size' => 'post-thumbnail' ) ); ?>
			<?php the_excerpt(); ?>
			<?php wp_link_pages( array( 'before' => '<p class="page-links">' . '<span class="before">' . __( 'Pages:', 'bon' ) . '</span>', 'after' => '</p>' ) ); ?>
		</div><!-- .entry-summary -->

	<?php } ?>

</article><!-- .hentry -->

<?php } ?>