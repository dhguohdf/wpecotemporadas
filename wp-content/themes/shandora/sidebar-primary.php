<?php if ( is_active_sidebar( 'primary' ) ) : ?>

	<aside id="sidebar-primary" class="sidebar <?php echo shandora_column_class('large-4'); ?>">

		<?php dynamic_sidebar( 'primary' ); ?>

	</aside><!-- #sidebar-primary .aside -->

<?php endif; ?>