<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------*/
/* Start BonThemes Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/

// BonFramework init
//require_once ( get_template_directory() . '/framework/bon.php' );


/*				
foreach ( $includes as $i ) {
	locate_template( $i, true );
}
*/

/* =============================
 * PUT YOUR FUNCTIONS HERE
 * =============================
 */

/**
 *
 * Here is an example to completely remove parent style
 * Uncomment the functions below to look what happen
 */

/*
function my_child_theme_remove_parent_styles() {
	remove_theme_support( 'dynamic-style' );
}

add_action( 'init', 'my_child_theme_remove_parent_style' );
*/

// OR using filter to remove style from parent
/*
function my_child_theme_remove_single_parent_style($parent_styles) {

	//this remove flexslider.css from parent
	unset($parent_styles['flexslider']);

	echo "<pre>", print_r($parent_styles), "</pre>";

}
add_filter('shandora_dynamic_style', 'my_child_theme_remove_single_parent_style', 1, 1 );
*/
/* Reload your page and WHOAAA!! superr!!! :) */


?>