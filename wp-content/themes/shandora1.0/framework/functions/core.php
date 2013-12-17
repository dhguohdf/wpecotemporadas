<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Core Functions
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
function bon_get_option( $name, $default = false ) {
	$config = get_option( 'bon_optionsframework' );

	if ( ! isset( $config['id'] ) ) {
		return $default;
	}

	$options = get_option( $config['id'] );

	if ( isset( $options[$name] ) && !empty($options[$name]) ) {
		return $options[$name];
	}

	return $default;
}

function bon_get_framework_option( $name, $default = false ) {
	$config = get_option( 'bon_framework_optionsframework' );

	if ( ! isset( $config['id'] ) ) {
		return $default;
	}

	$options = get_option( $config['id'] );

	if ( isset( $options[$name] ) ) {
		return $options[$name];
	}

	return $default;
}

function bon_get_prefix() {
	global $bon;

	/* If the global prefix isn't set, define it. Plugin/theme authors may also define a custom prefix. */
	if ( empty( $bon->theme_prefix ) )
		$bon->theme_prefix = sanitize_key( apply_filters( 'bon_prefix', get_template() ) );

	return $bon->theme_prefix;
}

/**
 * Adds contextual action hooks to the theme.  This allows users to easily add context-based content 
 * without having to know how to use WordPress conditional tags.  The theme handles the logic.
 *
 * An example of a basic hook would be 'bon_header'.  The do_atomic() function extends that to 
 * give extra hooks such as 'bon_singular_header', 'bon_singular-post_header', and 
 * 'bon_singular-post-ID_header'.
 *
 * @author Justin Tadlock <justin@justintadlock.com>
 * @author Ptah Dunbar <pt@ptahd.com>
 * @link http://ptahdunbar.com/wordpress/smarter-hooks-context-sensitive-hooks
 *
 * @since 1.0
 * @access public
 * @uses bon_get_prefix() Gets the theme prefix.
 * @uses bon_get_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $arg,... Optional additional arguments which are passed on to the functions hooked to the action.
 */
function do_atomic( $tag = '', $arg = '' ) {

	if ( empty( $tag ) )
		return false;

	/* Get the theme prefix. */
	$pre = bon_get_prefix();

	/* Get the args passed into the function and remove $tag. */
	$args = func_get_args();
	array_splice( $args, 0, 1 );
	/* Do actions on the basic hook. */
	do_action_ref_array( "{$pre}{$tag}", $args );

	/* Loop through context array and fire actions on a contextual scale. */
	foreach ( (array) bon_get_context() as $context )
	do_action_ref_array( "{$pre}{$context}_{$tag}", $args );
}

/**
 * Adds contextual filter hooks to the theme.  This allows users to easily filter context-based content 
 * without having to know how to use WordPress conditional tags.  The theme handles the logic.
 *
 * An example of a basic hook would be 'bon_entry_meta'.  The apply_atomic() function extends 
 * that to give extra hooks such as 'bon_singular_entry_meta', 'bon_singular-post_entry_meta', 
 * and 'bon_singular-post-ID_entry_meta'.
 *
 * @since 1.0
 * @access public
 * @uses bon_get_prefix() Gets the theme prefix.
 * @uses bon_get_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $value The value on which the filters hooked to $tag are applied on.
 * @param mixed $var,... Additional variables passed to the functions hooked to $tag.
 * @return mixed $value The value after it has been filtered.
 */
function apply_atomic( $tag = '', $value = '' ) {

	if ( empty( $tag ) )
		return false;

	/* Get theme prefix. */
	$pre = bon_get_prefix();

	/* Get the args passed into the function and remove $tag. */
	$args = func_get_args();
	array_splice( $args, 0, 1 );

	/* Apply filters on the basic hook. */
	$value = $args[0] = apply_filters_ref_array( "{$pre}{$tag}", $args );

	/* Loop through context array and apply filters on a contextual scale. */
	foreach ( (array) bon_get_context() as $context )
		$value = $args[0] = apply_filters_ref_array( "{$pre}{$context}_{$tag}", $args );

	/* Return the final value once all filters have been applied. */
	return $value;
}

/**
 * Wraps the output of apply_atomic() in a call to do_shortcode(). This allows developers to use 
 * context-aware functionality alongside shortcodes. Rather than adding a lot of code to the 
 * function itself, developers can create individual functions to handle shortcodes.
 *
 * @since 1.0
 * @access public
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $value The value to be filtered.
 * @return mixed $value The value after it has been filtered.
 */
function apply_atomic_shortcode( $tag = '', $value = '' ) {
	return do_shortcode( apply_atomic( $tag, $value ) );
}

/**
 * The theme can save multiple things in a transient to help speed up page load times. We're
 * setting a default of 12 hours or 43,200 seconds (60 * 60 * 12).
 *
 * @since 1.0
 * @access public
 * @return int Transient expiration time in seconds.
 */
function bon_get_transient_expiration() {
	return apply_filters( bon_get_prefix() . 'transient_expiration', 43200 );
}

/**
 * Function for formatting a hook name if needed. It automatically adds the theme's prefix to 
 * the hook, and it will add a context (or any variable) if it's given.
 *
 * @since 1.0
 * @access public
 * @param string $tag The basic name of the hook (e.g., 'before_header').
 * @param string $context A specific context/value to be added to the hook.
 */
function bon_format_hook( $tag, $context = '' ) {
	return bon_get_prefix() . ( ( !empty( $context ) ) ? "{$context}" : "" ). "_{$tag}";
}

/**
 * Function for setting the content width of a theme.  This does not check if a content width has been set; it 
 * simply overwrites whatever the content width is.
 *
 * @since 1.0
 * @access public
 * @global int $content_width The width for the theme's content area.
 * @param int $width Numeric value of the width to set.
 */
function bon_set_content_width( $width = '' ) {
	global $content_width;

	$content_width = absint( $width );
}

/**
 * Function for getting the theme's content width.
 *
 * @since 1.0
 * @access public
 * @global int $content_width The width for the theme's content area.
 * @return int $content_width
 */
function bon_get_content_width() {
	global $content_width;

	return $content_width;
}


/**
 * Get template part (for templates like the content-postformat).
 * This function will search in templates/ directory
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function bon_get_template_part( $slug, $name = '' ) {
	global $bon;

	$template = '';

	$slug_url = trailingslashit( $slug );
	$slugs_url = trailingslashit( $slug . 's' ) ;
	$theme_path = get_template_directory();
	$template_url = trailingslashit( "templates" );

	// Look in themefolder/slug-name.php and 
	if ( $name )
		$template = locate_template( array( 
										"{$slug}-{$name}.php", 
										"{$template_url}{$slug_url}-{$name}.php",
										"{$template_url}{$slug_url}{$slug}-{$name}.php",
										"{$template_url}{$slugs_url}{$slug}-{$name}.php",
										"{$template_url}{$slug_url}{$name}.php",
										"{$template_url}{$slugs_url}{$name}.php", 
									) );


											
	// Look in themefolder/templates/slug-name.php
	if ( !$template && $name && file_exists( $theme_path . "templates/{$slug}-{$name}.php" ) )
		$template = $theme_path . "/templates/{$slug}-{$name}.php";

	/** 
	 * If template file doesn't exist, look in themefolder/slug.php 
	 * themefolder/templates/slug/slug.php
	 * themefolder/templates/slugs/slug.php
	 * themefolder/templates/slug/name.php
	 * themefolder/templates/slugs/name.php
	 * themefolder/templates/slug/slug-name.php
	 * themefolder/templates/slugs/slug-name.php
	 *
	 */

	if ( !$template )
		$template = locate_template( array( 
											"{$template_url}{$slug_url}{$slug}.php",
											"{$template_url}{$slugs_url}{$slug}.php",
											"{$template_url}{$slug}.php",
											"{$template_url}{$slug}s.php"
											) );

  
	if ( $template )
		load_template( $template, false );
}


?>