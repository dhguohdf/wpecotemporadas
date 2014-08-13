<?php

	global $post, $comment;
?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>

		<?php do_atomic( 'open_comment' ); ?>

		<header class="comment-header">
			<div class="avatar-container">
				<?php echo bon_avatar(); ?>
			</div>
		
			<?php echo apply_atomic_shortcode( 'comment_meta', '<div class="comment-meta">[comment-author] [comment-published] [comment-permalink before="&sdot; "] [comment-edit-link before="&sdot; "]</div>' ); ?>
		</header>
		<?php do_atomic( 'close_comment' );  ?>

	<?php /* No closing </li> is needed.  WordPress will know where to add it. */ ?>