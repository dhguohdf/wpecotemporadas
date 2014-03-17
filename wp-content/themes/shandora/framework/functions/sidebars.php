<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly
/**
 * Sidebar Settings Functions
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

/* Register widget areas. */
add_action( 'widgets_init', 'bon_register_sidebars' );
add_action( 'widgets_init', 'bon_sidebars_generator');
/**
 * Registers the default framework dynamic sidebars based on the sidebars the theme has added support 
 * for using add_theme_support().
 *
 * @since 1.0
 * @access public
 * @uses register_sidebar() Registers a sidebar with WordPress.
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 * @return void
 */
function bon_register_sidebars() {

	/* Get the theme-supported sidebars. */
	$sidebars = get_theme_support( 'bon-core-sidebars' );
	
	/* If the theme doesn't add support for any sidebars, return. */
	if ( !is_array( $sidebars[0] ) )
		return;


	/* Loop through the supported sidebars. */
	foreach ( $sidebars[0] as $key => $sidebar ) {

		$defaults = bon_get_default_widget_args();
		/* Allow developers to filter the default sidebar arguments. */
		$defaults = apply_filters( bon_get_prefix() . 'sidebar_defaults', $defaults, $sidebar );

		/* Parse the sidebar arguments and defaults. */
		$args = wp_parse_args( $sidebar, $defaults );

		/* If no 'id' was given, use the $sidebar variable and sanitize it. */
		$args['id'] = ( isset( $args['id'] ) ? sanitize_key( $args['id'] ) : sanitize_key( $sidebar ) );

		/* Allow developers to filter the sidebar arguments. */
		$args = apply_filters( bon_get_prefix() . 'sidebar_args', $args, $sidebar );

		/* Register the sidebar. */
		register_sidebar( $args );
		
	}
}

function bon_get_default_widget_args() {
	/* Set up some default sidebar arguments. */
	$defaults = array(
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget'  => '</div></div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	);

	return $defaults;
}

function bon_sidebars_generator() {

	global $wp_registered_sidebars;
	
	$sidebars = bon_get_option('sidebars_generator');

	$defaults = bon_get_default_widget_args();

	if(!empty($sidebars) && is_array($sidebars)) {
		
		foreach( $sidebars as $sidebar ) {
			
			if( !empty($sidebar['sidebar_name']) ) {

				$id = strtolower(str_replace(" ", "-", $sidebar['sidebar_name']));

				if(array_key_exists($id, $wp_registered_sidebars)) {

					continue;

				} else {

					$args = array(
						'name' => $sidebar['sidebar_name'],
						'id' => $id
					);

					/* Allow developers to filter the default sidebar arguments. */
					$defaults = apply_filters( bon_get_prefix() . 'sidebar_defaults', $defaults, $args );

					$args = wp_parse_args( $args, $defaults);
					
					register_sidebar( $args );
					
				}
			}
		}
	}
}
?>