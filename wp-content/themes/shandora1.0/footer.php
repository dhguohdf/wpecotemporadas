<footer id="footer-container" class="container">
	<?php
	if(is_singular('listing')) {
				bon_get_template_part('block','searchlisting'); 
			}
	?>
	<?php 
	/**
	 * Shandora Footer Container Action Hook
	 *
	 * @hooked shandora_get_footer_backtop - 1
	 * @hooked shandora_get_footer_widget - 5
	 * @hooked shandora_get_fotoer_copyright - 10
	 *
	 */
	
	do_atomic('footer_widget'); ?>



</footer>
</div>
</div>


<?php 
	/**
	 *
	 * Shandora Footer Hook
	 *
	 * @hooked shandora_get_footer - 1
	 *
	 */
	do_atomic('footer');
?>
