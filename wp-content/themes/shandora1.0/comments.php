<?php
/* If a post password is required or no comments are given and comments/pings are closed, return. */
if ( post_password_required() || ( !have_comments() && !comments_open() && !pings_open() ) )
	return;
?>

<section id="comments">

	<?php if ( have_comments() ) : ?>

		<div class="entry-byline show-for-large">
			<a class="entry-icon-meta entry-post-meta" title="<?php comments_number( __( 'Sem Avaliações', 'bon' ), __( 'Uma Avaliação', 'bon' ), __( '% Avaliações', 'bon' ) ); ?>">
				<i class="sha-talk-bubble-2"></i>
			</a>
		</div>
		<h2 class="show-for-medium-down"><?php comments_number( __( 'Sem Avaliações', 'bon' ), __( 'Uma Avaliação', 'bon' ), __( '% Avaliações', 'bon' ) ); ?></h2>
		<?php if ( get_option( 'page_comments' ) && 1 < get_comment_pages_count() ) : ?>

			<div class="comments-nav">
				<?php previous_comments_link( __( '&larr; Anterior', 'bon' ) ); ?>
				<span class="page-numbers"><?php printf( __( 'Page %1$s of %2$s', 'bon' ), ( get_query_var( 'cpage' ) ? absint( get_query_var( 'cpage' ) ) : 1 ), get_comment_pages_count() ); ?></span>
				<?php next_comments_link( __( 'Próximo &rarr;', 'bon' ) ); ?>
			</div><!-- .comments-nav -->

		<?php endif; ?>

		<ol class="comment-list">
			<?php wp_list_comments( bon_list_comments_args() ); ?>
		</ol><!-- .comment-list -->

	<?php endif; ?>

	<?php if ( pings_open() && !comments_open() ) : ?>

		<p class="comments-closed pings-open">
			<?php printf( __( 'Comments are closed, but <a href="%s" title="Trackback URL for this post">trackbacks</a> and pingbacks are open.', 'bon' ), esc_url( get_trackback_url() ) ); ?>
		</p><!-- .comments-closed .pings-open -->

	<?php elseif ( !comments_open() ) : ?>

		<p class="comments-closed">
			<?php _e( 'Comments are closed.', 'bon' ); ?>
		</p><!-- .comments-closed -->

	<?php endif;

	if ( comments_open() ) : ?>

	<?php comment_form(); ?>

	<?php endif; // if you delete this the sky will fall on your head ?>

</section><!-- #comments -->