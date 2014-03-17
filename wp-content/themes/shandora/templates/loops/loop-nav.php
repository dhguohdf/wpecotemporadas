<?php do_atomic('before_pagination'); ?>

	<?php if ( is_attachment() ) : ?>

		<div class="loop-nav">
			<?php previous_post_link( '%link', '<span class="previous">' . __( '<span class="meta-nav">&larr;</span> Return to entry', 'bon' ) . '</span>' ); ?>
		</div><!-- .loop-nav -->

	<?php elseif ( is_singular() ) : ?>

		<div class="loop-nav">
			<?php previous_post_link( '%link', '<span class="previous">' . __( '<span class="meta-nav">&larr;</span> Previous', 'bon' ) . '</span>' ); ?>
			<?php next_post_link( '%link', '<span class="next">' . __( 'Next <span class="meta-nav">&rarr;</span>', 'bon' ) . '</span>' ); ?>
		</div><!-- .loop-nav -->

	<?php elseif ( !is_singular() && current_theme_supports( 'bon-pagination' ) ) : bon_pagination(array('container_class'=> 'pagination-centered', 'disabled_class' => 'unavailable', 'current_class' => 'current')); ?>

	<?php elseif ( !is_singular() && $nav = get_posts_nav_link( array( 'sep' => '', 'prelabel' => '<span class="previous">' . __( '<span class="meta-nav">&larr;</span> Older Entries', 'bon' ) . '</span>', 'nxtlabel' => '<span class="next">' . __( 'Newer Entries <span class="meta-nav">&rarr;</span>', 'bon' ) . '</span>' ) ) ) : ?>

		<?php if(!empty($nav)) : ?>
		<div class="loop-nav">
			<?php echo $nav; ?>
		</div><!-- .loop-nav -->
		<?php endif; ?>
	<?php endif; ?>
	
<?php wp_reset_query(); do_atomic('after_pagination'); ?>