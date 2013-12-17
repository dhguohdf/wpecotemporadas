<?php
/* If a post password is required or no comments are given and comments/pings are closed, return. */
if ( post_password_required() || ( !have_comments() && !comments_open() && !pings_open() ) )
	return;
?>
                        
<section id="comments">

	<?php if ( have_comments() ) : ?>

		<div class="entry-byline show-for-large">
			<a class="entry-icon-meta entry-post-meta" title="<?php comments_number( __( 'No Responses', 'bon' ), __( 'One Response', 'bon' ), __( '% Responses', 'bon' ) ); ?>">
				<i class="sha-talk-bubble-2"></i>
			</a>
		</div>
		<h2 class="show-for-medium-down"><?php comments_number( __( 'No Responses', 'bon' ), __( 'One Response', 'bon' ), __( '% Responses', 'bon' ) ); ?></h2>
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

	<div id="respond">

		<div class="entry-byline show-for-large">
			<a class="entry-icon-meta entry-post-meta" title="<?php comment_form_title( __('Leave a Reply', 'bon'), __('Leave a Reply to %s', 'bon') ); ?>">
				<i class="sha-talk-bubble-2"></i>
			</a>
		</div>

		<h3 class="show-for-medium-down"><?php comment_form_title( __('Leave a Reply', 'bon'), __('Leave a Reply to %s', 'bon') ); ?></h3>
	
		<div class="cancel-comment-reply">
			<?php cancel_comment_reply_link(); ?>
		</div>
	
		<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
		<p><?php printf(__('You must be %1$slogged in%2$s to post a comment.', 'bon'), '<a href="'.get_option('siteurl').'/wp-login.php?redirect_to='.urlencode(get_permalink()).'">', '</a>') ?></p>
		<?php else : ?>
	
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
	
			<?php if ( is_user_logged_in() ) : ?>
		
			<p><?php printf(__('Logado como %1$s. %2$sLog out &raquo;%3$s', 'bon'), '<a href="'.get_option('siteurl').'/wp-admin/profile.php">'.$user_identity.'</a>', '<a href="'.(function_exists('wp_logout_url') ? wp_logout_url(get_permalink()) : get_option('siteurl').'/wp-login.php?action=logout" title="').'" title="'.__('Deslogar', 'bon').'">', '</a>') ?></p>
		
			<?php else : ?>
		
			<div class="row collapse input-container">
				<div class="column large-1 small-1"><span class="attached-label prefix"><i class="sha-user"></i></span></div>
				<div class="column large-6 small-11">
					<input class="attached-input" type="text" name="author" id="author" placeholder="Nome" value="<?php echo esc_attr($comment_author); ?>" size="22" tabindex="1" />
				</div>
			</div>
		
			<div class="row collapse input-container">
				<div class="column large-1 small-1"><span class="attached-label prefix"><i class="sha-mail-2"></i></span></div>
				<div class="column large-6 small-11">
					<input class="attached-input" type="text" name="email" id="email" placeholder="Email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" tabindex="2" />
				</div>
			</div>
				
			<?php endif; ?>
		
			<div class="row collapse textarea-container input-container" data-match-height>
				<div data-height-watch class="column large-1 small-1"><span class="attached-label prefix"><i class="sha-pencil"></i></span></div>
				<div data-height-watch class="column large-11 small-11">
					<textarea name="comment" class="attached-input" id="comment" placeholder="Digite seu comentário" cols="58" rows="10" tabindex="4"></textarea>
				</div>
			</div>
			
			<!--<p class="allowed-tags"><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->
		
			<div><input class="flat button red radius" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Enviar Comentário', 'bon') ?>" />
			<?php comment_id_fields(); ?>
			</div>
			<?php do_action('comment_form', $post->ID); ?>
	
		</form>

	<?php endif; // If registration required and not logged in ?>
	</div>

	<?php endif; // if you delete this the sky will fall on your head ?>

</section><!-- #comments -->