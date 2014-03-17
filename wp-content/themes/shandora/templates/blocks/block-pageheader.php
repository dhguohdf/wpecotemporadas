<?php global $post; ?>

<div id="page-header" class="show-for-medium-up">
	<div class="row">
		<div class="column large-4">
			<h3 class="page-title">
				<?php
					if(!is_home()) {
						bon_document_title();
					} else {
						_e('Home', 'bon'); 
					}
				?>
			</h3>
		</div>
		<div class="column large-8">
			<?php if ( current_theme_supports( 'bon-breadcrumb-trail' ) ) bon_breadcrumb_trail( array( 'show_browse'=> false, 'container' => 'nav', 'separator' => '&rsaquo;', 'before' => '' ) ); ?>
		</div>
	</div>
</div>