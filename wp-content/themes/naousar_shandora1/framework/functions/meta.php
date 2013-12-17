<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly
/**
 * Meta Functions
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

/* Register meta on the 'init' hook. */
add_action( 'init', 'bon_register_meta' );

/**
 * Registers the framework's custom metadata keys and sets up the sanitize callback function.
 *
 * @since 1.0.0
 * @return void
 */
function bon_register_meta() {

	/* Register meta if the theme supports the 'bon-core-seo' feature. */
	if ( current_theme_supports( 'bon-core-seo' ) ) {

		/* Register 'Title', 'Description', and 'Keywords' meta for posts. */
		register_meta( 'post', 'Title', 'bon_sanitize_meta' );
		register_meta( 'post', 'Description', 'bon_sanitize_meta' );
		register_meta( 'post', 'Keywords', 'bon_sanitize_meta' );

		/* Register 'Title', 'Description', and 'Keywords' meta for users. */
		register_meta( 'user', 'Title', 'bon_sanitize_meta' );
		register_meta( 'user', 'Description', 'bon_sanitize_meta' );
		register_meta( 'user', 'Keywords', 'bon_sanitize_meta' );
	}

	/* Register meta if the theme supports the 'bon-core-template-hierarchy' feature. */
	if ( current_theme_supports( 'bon-core-template-hierarchy' ) ) {

		$post_types = get_post_types( array( 'public' => true ) );

		foreach ( $post_types as $post_type ) {
			if ( 'page' !== $post_type )
				register_meta( 'post', "_wp_{$post_type}_template", 'bon_sanitize_meta' );
		}
	}
}

/**
 * Callback function for sanitizing meta when add_metadata() or update_metadata() is called by WordPress. 
 * If a developer wants to set up a custom method for sanitizing the data, they should use the 
 * "sanitize_{$meta_type}_meta_{$meta_key}" filter hook to do so.
 *
 * @since 1.0.0
 * @param mixed $meta_value The value of the data to sanitize.
 * @param string $meta_key The meta key name.
 * @param string $meta_type The type of metadata (post, comment, user, etc.)
 * @return mixed $meta_value
 */
function bon_sanitize_meta( $meta_value, $meta_key, $meta_type ) {
	return strip_tags( $meta_value );
}

?>