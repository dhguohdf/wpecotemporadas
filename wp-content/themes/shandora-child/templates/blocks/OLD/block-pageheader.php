<?php global $post; ?>

<div id="page-header" class="show-for-medium-up">
	<div class="row">
		<div class="column large-4">
			<h3 class="page-title">
				<?php
					$type = get_the_term_list($post->ID, 'property-type');
					if(!is_home()) {
						echo $type; 
					} else {
						_e('Home', 'bon'); 
					}
				?>
			</h3>
		</div>
	
		<div class="column large-8">
			<?php if ( function_exists('yoast_breadcrumb') ) {
yoast_breadcrumb('<p id="breadcrumbs" class="breadcrumb-trail breadcrumbs">','</p>');
} ?>
		</div>
		
	</div>
</div>