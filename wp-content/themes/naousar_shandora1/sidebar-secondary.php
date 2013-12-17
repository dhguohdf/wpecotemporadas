<?php if ( is_active_sidebar( 'secondary' ) ) : ?>

	<aside id="sidebar-secondary" class="sidebar <?php echo shandora_column_class('large-4'); ?>">

		<?php dynamic_sidebar( 'secondary' ); ?>

	</aside><!-- #sidebar-primary .aside -->

<?php endif; ?>