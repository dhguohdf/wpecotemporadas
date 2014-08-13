<?php
	global $post, $comment;
?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>

		<?php do_atomic( 'open_comment' );  ?>

		<header class="comment-header">
			<div class="avatar-container">
		<?php echo bon_avatar(); ?>
			</div>
		<?php echo apply_atomic_shortcode( 'comment_meta', '<div class="comment-meta">[comment-author] [comment-published] [comment-permalink before="&sdot; "] [comment-edit-link before="&sdot; "] [comment-reply-link before="&sdot; "]</div>' ); ?>

		</header>

		<div class="comment-content comment-text">
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<?php echo apply_atomic_shortcode( 'comment_moderation', '<p class="alert moderation">' . __( 'Your comment is awaiting moderation.', 'bon' ) . '</p>' ); ?>
			<?php endif; ?>

			<?php comment_text( $comment->comment_ID ); ?>
		</div><!-- .comment-content .comment-text -->

		<?php do_atomic( 'close_comment' );  ?>

	<?php /* No closing </li> is needed.  WordPress will know where to add it. */ ?>