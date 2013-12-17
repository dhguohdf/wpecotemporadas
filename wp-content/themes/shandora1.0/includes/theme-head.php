<?php


if(!function_exists('shandora_print_dynamic_styles')) {

	function shandora_print_dynamic_styles() {

		$styles = bon_get_option('custom_css');
		
		if(!empty($styles)) { ?>
			<style type="text/css" id="shandora-custom-styles">
			
				<?php echo $styles; ?>

			</style>

			<?php 
		}

	}

	add_action('wp_head', 'shandora_print_dynamic_styles', 100);

}


if(!function_exists('shandora_print_dynamic_scripts')) {

	function shandora_print_dynamic_scripts() {

		$scripts = bon_get_option('custom_js');
		
		if(!empty($scripts)) { ?>
			<script>
			
				<?php echo $scripts; ?>

			</script>

			<?php 
		}

	}

	add_action('wp_head', 'shandora_print_dynamic_scripts', 100);

}


if(!function_exists('shandora_print_tracking_code')) {

	function shandora_print_tracking_code() {

		$scripts = bon_get_option('google_analytics');
		
		if(!empty($scripts)) { ?>
			
				<?php echo $scripts; ?>


			<?php 
		}

	}

	add_action('wp_head', 'shandora_print_tracking_code', 101);

}

?>