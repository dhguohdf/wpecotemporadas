<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly
/**
 * Menu Functions
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Fuctions
 *
 *
*/ 

/* Register nav menus. */
add_action( 'init', 'bon_register_menus' );

/**
 * Registers the the framework's default menus based on the menus the theme has registered support for.
 *
 * @since 1.0
 * @access private
 * @uses register_nav_menu() Registers a nav menu with WordPress.
 * @link http://codex.wordpress.org/Function_Reference/register_nav_menu
 * @return void
 */
function bon_register_menus() {
	global $bon;
	/* Get theme-supported menus. */
	$menus = get_theme_support( 'bon-core-menus' );
	
	/* If there is no array of menus IDs, return. */
	if ( !is_array( $menus[0] ) )
		return;

	if(!empty($menus[0]) && is_array($menus[0]) ) {
		foreach($menus[0] as $menu => $label ) {
			register_nav_menu( $menu, _x( $label, 'nav menu location', 'bon' ) );
		}
	}
	
}

?>