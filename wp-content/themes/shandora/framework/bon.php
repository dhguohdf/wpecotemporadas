<?php

/*-----------------------------------------------------------------------------------*/
/* Check if WooCommerce is activated */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}
}

function bon_set_base_data() {
	
	$theme_supports = array(
		
		'theme-layouts' => '',
		
	);

	$setting_pages = array(

		array( 'slug' => 'bon_options', 'parent'=>'bon', 'title' => 'Theme Options', 'role' => 'manage_options'),
		//array( 'slug' => 'bon_backup', 'parent'=>'bon_backup', 'title' => 'Backup Settings', 'role' => 'manage_options' ),
		array( 'slug' => 'bon_framework', 'parent'=>'bon_framework', 'title' => 'Framework Settings', 'role' => 'superuser' ),	
								 
	);

	
	$bon_base_data['setting_pages'] = $setting_pages;
	$bon_base_data['supports'] = $theme_supports;
	return $bon_base_data;
}


require_once ( get_template_directory() . '/framework/classes/class-bon-main.php' );

$bon_base_data = bon_set_base_data();

$GLOBALS['bon'] = new BON_Main($bon_base_data);

?>