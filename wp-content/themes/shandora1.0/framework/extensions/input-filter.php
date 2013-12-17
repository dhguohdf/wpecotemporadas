<?php

/**
 * Finds any item in any level of an array
 *
 * @param	string	$needle 	field type to look for
 * @param	array	$haystack	an array to search the type in
 *
 * @return	bool				whether or not the type is in the provided array
 */
function bon_find_field_type( $needle, $haystack ) {
	foreach ( $haystack as $h ) {
		if($needle != 'repeatable') {
			if ( ( isset( $h['type'] ) && $h['type'] == $needle ) || ( isset( $h['repeatable_type'] ) && $h['repeatable_type'] == $needle ) ) {
				return true;
			}
		}
		
		else {
			if ( isset( $h['type'] ) && $h['type'] == 'repeatable' ) {
				return bon_find_field_type( $needle, $h['repeatable_fields'] );
			}
		}
	}

	return false;
}

/**
 * Find repeatable
 *
 * This function does almost the same exact thing that the above function 
 * does, except we're exclusively looking for the repeatable field. The 
 * reason is that we need a way to look for other fields nested within a 
 * repeatable, but also need a way to stop at repeatable being true. 
 * Hopefully I'll find a better way to do this later.
 *
 * @param	string	$needle 	field type to look for
 * @param	array	$haystack	an array to search the type in
 *
 * @return	bool				whether or not the type is in the provided array
 */
function bon_find_repeatable( $needle = 'repeatable', $haystack ) {
	foreach ( $haystack as $h )
		if ( isset( $h['type'] ) && $h['type'] == $needle )
			return true;
	return false;
}

/**
 * sanitize boolean inputs
 */
function bon_santitize_boolean( $string ) {
	if ( ! isset( $string ) || $string != 1 || $string != true )
		return false;
	else
		return true;
}

/**
 * outputs properly sanitized data
 *
 * @param	string	$string		the string to run through a validation function
 * @param	string	$function	the validation function
 *
 * @return						a validated string
 */
function bon_sanitize( $string, $function = 'sanitize_text_field' ) {
	switch ( $function ) {
		case 'intval':
			return intval( $string );
		case 'absint':
			return absint( $string );
		case 'wp_kses_post':
			return wp_kses_post( $string );
		case 'wp_kses_data':
			return wp_kses_data( $string );
		case 'esc_url_raw':
			return esc_url_raw( $string );
		case 'is_email':
			return is_email( $string );
		case 'sanitize_title':
			return sanitize_title( $string );
		case 'santitize_boolean':
			return santitize_boolean( $string );
		case 'sanitize_text_field':
		default:
			return sanitize_text_field( $string );
	}
}

add_filter( 'bon_sanitize_field', 'bon_sanitize', 10, 2 );


/**
 * Map a multideminsional array
 *
 * @param	string	$func		the function to map
 * @param	array	$meta		a multidimensional array
 * @param	array	$sanitizer	a matching multidimensional array of sanitizers
 *
 * @return	array				new array, fully mapped with the provided arrays
 */
function bon_array_map_r( $func, $meta, $sanitizer ) {
		
	$newMeta = array();
	$meta = array_values( $meta );
	
	foreach( $meta as $key => $array ) {
		if ( $array == '' )
			continue;
		/**
		 * some values are stored as array, we only want multidimensional ones
		 */
		if ( ! is_array( $array ) ) {
			return array_map( $func, $meta, (array)$sanitizer );
			break;
		}
		/**
		 * the sanitizer will have all of the fields, but the item may only 
		 * have valeus for a few, remove the ones we don't have from the santizer
		 */
		$keys = array_keys( $array );
		$newSanitizer = $sanitizer;
		if ( is_array( $sanitizer ) ) {
			foreach( $newSanitizer as $sanitizerKey => $value )
				if ( ! in_array( $sanitizerKey, $keys ) )
					unset( $newSanitizer[$sanitizerKey] );
		}
		/**
		 * run the function as deep as the array goes
		 */
		foreach( $array as $arrayKey => $arrayValue )
			if ( is_array( $arrayValue ) )
				$array[$arrayKey] = bon_array_map_r( $func, $arrayValue, $newSanitizer[$arrayKey] );
		
		$array = array_map( $func, $array, $newSanitizer );
		$newMeta[$key] = array_combine( $keys, array_values( $array ) );
	}
	return $newMeta;
}



/* Text */

add_filter( 'bon_sanitize_text', 'sanitize_text_field' );

/* Textarea */

function bon_sanitize_textarea($input) {
	global $allowedposttags, $allowedtags;

    $custom_allowedtags["embed"] = array(
      "src" => array(),
      "type" => array(),
      "allowfullscreen" => array(),
      "allowscriptaccess" => array(),
      "height" => array(),
          "width" => array()
      );
      $custom_allowedtags["script"] = array();
 
      $custom_allowedtags = array_merge($custom_allowedtags, $allowedposttags);
      
	$output = wp_kses( $input, $custom_allowedtags);
	return $output;
}


add_filter('bon_sanitize_info', 'bon_sanitize_textarea' );
add_filter('bon_sanitize_gallery', 'sanitize_text_field');
add_filter('bon_sanitize_image', 'sanitize_text_field');
add_filter('bon_sanitize_textarea', 'bon_sanitize_textarea');

/* Select */
add_filter( 'bon_sanitize_chosen', 'bon_sanitize_enum', 10, 2);
add_filter( 'bon_sanitize_select', 'bon_sanitize_enum', 10, 2);

/* Radio */

add_filter( 'bon_sanitize_radio', 'bon_sanitize_enum', 10, 2);
add_filter( 'bon_sanitize_radio-img', 'bon_sanitize_enum', 10, 2);
/* Images */

add_filter( 'bon_sanitize_images', 'bon_sanitize_enum', 10, 2);

/* Checkbox */

function bon_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = '1';
	} else {
		$output = false;
	}
	return $output;
}
add_filter( 'bon_sanitize_checkbox', 'bon_sanitize_checkbox' );

/* Multicheck */

function bon_sanitize_multicheck( $input, $option ) {
	$output = '';
	if ( is_array( $input ) ) {
		foreach( $option['options'] as $key => $value ) {
			$output[$key] = "0";
		}
		foreach( $input as $key => $value ) {
			if ( array_key_exists( $key, $option['options'] ) && $value ) {
				$output[$key] = "1";
			}
		}
	}
	return $output;
}
add_filter( 'bon_sanitize_multicheck', 'bon_sanitize_multicheck', 10, 2 );

/* Color Picker */

add_filter( 'bon_sanitize_color', 'bon_sanitize_hex' );

/* Uploader */

function bon_sanitize_upload( $input ) {
	$output = '';
	$filetype = wp_check_filetype($input);
	if ( $filetype["ext"] ) {
		$output = $input;
	}
	return $output;
}
add_filter( 'bon_sanitize_upload', 'bon_sanitize_upload' );

/* Editor */

function bon_sanitize_editor($input) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$output = $input;
	}
	else {
		global $allowedtags;
		$output = wpautop(wp_kses( $input, $allowedtags));
	}
	return $output;
}
add_filter( 'bon_sanitize_editor', 'bon_sanitize_editor' );

/* Allowed Tags */

function bon_sanitize_allowedtags($input) {
	global $allowedtags;
	$output = wpautop(wp_kses( $input, $allowedtags));
	return $output;
}
add_filter( 'bon_sanitize_allowedtags', 'bon_sanitize_notice' );
/* Allowed Post Tags */

function bon_sanitize_allowedposttags($input) {
	global $allowedposttags;
	$output = wpautop(wp_kses( $input, $allowedposttags));
	return $output;
}

add_filter( 'bon_sanitize_info', 'bon_sanitize_allowedposttags' );


/* Check that the key value sent is valid */

function bon_sanitize_enum( $input, $option ) {
	$output = '';
	if ( array_key_exists( $input, $option['options'] ) ) {
		$output = $input;
	}
	return $output;
}

/* Background */

function bon_sanitize_background( $input ) {
	$output = wp_parse_args( $input, array(
		'color' => '',
		'image'  => '',
		'repeat'  => 'repeat',
		'position' => 'top center',
		'attachment' => 'scroll'
	) );

	$output['color'] = apply_filters( 'bon_sanitize_hex', $input['color'] );
	$output['image'] = apply_filters( 'bon_sanitize_upload', $input['image'] );
	$output['repeat'] = apply_filters( 'bon_background_repeat', $input['repeat'] );
	$output['position'] = apply_filters( 'bon_background_position', $input['position'] );
	$output['attachment'] = apply_filters( 'bon_background_attachment', $input['attachment'] );

	return $output;
}
add_filter( 'bon_sanitize_background', 'bon_sanitize_background' );

function bon_sanitize_background_repeat( $value ) {
	$recognized = bon_recognized_background_repeat();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'bon_default_background_repeat', current( $recognized ) );
}
add_filter( 'bon_background_repeat', 'bon_sanitize_background_repeat' );

function bon_sanitize_background_position( $value ) {
	$recognized = bon_recognized_background_position();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'bon_default_background_position', current( $recognized ) );
}
add_filter( 'bon_background_position', 'bon_sanitize_background_position' );

function bon_sanitize_background_attachment( $value ) {
	$recognized = bon_recognized_background_attachment();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'bon_default_background_attachment', current( $recognized ) );
}
add_filter( 'bon_background_attachment', 'bon_sanitize_background_attachment' );


/* Typography */

function bon_sanitize_typography( $input, $option ) {

	$output = wp_parse_args( $input, array(
		'size'  => '',
		'face'  => '',
		'style' => '',
		'color' => ''
	) );

	if ( isset( $option['options']['faces'] ) && isset( $input['face'] ) ) {
		if ( !( array_key_exists( $input['face'], $option['options']['faces'] ) ) ) {
			$output['face'] = '';
		}
	}
	else {
		$output['face']  = apply_filters( 'bon_font_face', $output['face'] );
	}

	$output['size']  = apply_filters( 'bon_font_size', $output['size'] );
	$output['style'] = apply_filters( 'bon_font_style', $output['style'] );
	$output['color'] = apply_filters( 'bon_sanitize_color', $output['color'] );
	return $output;
}
add_filter( 'bon_sanitize_typography', 'bon_sanitize_typography', 10, 2 );

function bon_sanitize_font_size( $value ) {
	$recognized = bon_recognized_font_sizes();
	$value_check = preg_replace('/px/','', $value);
	if ( in_array( (int) $value_check, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'bon_default_font_size', $recognized );
}
add_filter( 'bon_font_size', 'bon_sanitize_font_size' );


function bon_sanitize_font_style( $value ) {
	$recognized = bon_recognized_font_styles();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'bon_default_font_style', current( $recognized ) );
}
add_filter( 'bon_font_style', 'bon_sanitize_font_style' );


function bon_sanitize_font_face( $value ) {
	$recognized = bon_recognized_font_faces();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'bon_default_font_face', current( $recognized ) );
}
add_filter( 'bon_font_face', 'bon_sanitize_font_face' );

/**
 * Get recognized background repeat settings
 *
 * @return   array
 *
 */
function bon_recognized_background_repeat() {
	$default = array(
		'no-repeat' => __( 'No Repeat', 'options_framework_theme' ),
		'repeat-x'  => __( 'Repeat Horizontally', 'options_framework_theme' ),
		'repeat-y'  => __( 'Repeat Vertically', 'options_framework_theme' ),
		'repeat'    => __( 'Repeat All', 'options_framework_theme' ),
		);
	return apply_filters( 'bon_recognized_background_repeat', $default );
}

/**
 * Get recognized background positions
 *
 * @return   array
 *
 */
function bon_recognized_background_position() {
	$default = array(
		'top left'      => __( 'Top Left', 'options_framework_theme' ),
		'top center'    => __( 'Top Center', 'options_framework_theme' ),
		'top right'     => __( 'Top Right', 'options_framework_theme' ),
		'center left'   => __( 'Middle Left', 'options_framework_theme' ),
		'center center' => __( 'Middle Center', 'options_framework_theme' ),
		'center right'  => __( 'Middle Right', 'options_framework_theme' ),
		'bottom left'   => __( 'Bottom Left', 'options_framework_theme' ),
		'bottom center' => __( 'Bottom Center', 'options_framework_theme' ),
		'bottom right'  => __( 'Bottom Right', 'options_framework_theme')
		);
	return apply_filters( 'bon_recognized_background_position', $default );
}

/**
 * Get recognized background attachment
 *
 * @return   array
 *
 */
function bon_recognized_background_attachment() {
	$default = array(
		'scroll' => __( 'Scroll Normally', 'options_framework_theme' ),
		'fixed'  => __( 'Fixed in Place', 'options_framework_theme')
		);
	return apply_filters( 'bon_recognized_background_attachment', $default );
}

/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 *
 */

function bon_sanitize_hex( $hex, $default = '' ) {
	if ( bon_validate_hex( $hex ) ) {
		return $hex;
	}
	return $default;
}

/**
 * Get recognized font sizes.
 *
 * Returns an indexed array of all recognized font sizes.
 * Values are integers and represent a range of sizes from
 * smallest to largest.
 *
 * @return   array
 */

function bon_recognized_font_sizes() {
	$sizes = range( 9, 71 );
	$sizes = apply_filters( 'bon_recognized_font_sizes', $sizes );
	$sizes = array_map( 'absint', $sizes );
	return $sizes;
}

/**
 * Get recognized font faces.
 *
 * Returns an array of all recognized font faces.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function bon_recognized_font_faces() {
	$default = array(
		'arial'     => 'Arial',
		'verdana'   => 'Verdana, Geneva',
		'trebuchet' => 'Trebuchet',
		'georgia'   => 'Georgia',
		'times'     => 'Times New Roman',
		'tahoma'    => 'Tahoma, Geneva',
		'palatino'  => 'Palatino',
		'helvetica' => 'Helvetica*'
	);
	return apply_filters( 'bon_recognized_font_faces', $default );
}

/**
 * Get recognized font styles.
 *
 * Returns an array of all recognized font styles.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function bon_recognized_font_styles() {
	$default = array(
		'normal'      => __( 'Normal', 'options_framework_theme' ),
		'italic'      => __( 'Italic', 'options_framework_theme' ),
		'bold'        => __( 'Bold', 'options_framework_theme' ),
		'bold italic' => __( 'Bold Italic', 'options_framework_theme' )
	);
	return apply_filters( 'bon_recognized_font_styles', $default );
}

/**
 * Is a given string a color formatted in hexidecimal notation?
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 *
 */

function bon_validate_hex( $hex ) {
	$hex = trim( $hex );
	/* Strip recognized prefixes. */
	if ( 0 === strpos( $hex, '#' ) ) {
		$hex = substr( $hex, 1 );
	}
	elseif ( 0 === strpos( $hex, '%23' ) ) {
		$hex = substr( $hex, 3 );
	}
	/* Regex match. */
	if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return false;
	}
	else {
		return true;
	}
}

?>