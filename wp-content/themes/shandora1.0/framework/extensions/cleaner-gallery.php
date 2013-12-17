<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Cleaner Gallery Extensions
 *
 *
 * @author      Hermanto Lim
 * @copyright   Copyright (c) Hermanto Lim
 * @link        http://bonfirelab.com
 * @since       Version 1.0
 * @package     BonFramework
 * @category    Extensions
 *
 *
*/ 

/* Filter the post gallery shortcode output. */
add_filter( 'post_gallery', 'cleaner_gallery', 10, 2 );

add_action( 'wp_enqueue_scripts', 'cleaner_gallery_style' );

function cleaner_gallery_style() {

	if(!is_admin()) {
		wp_enqueue_style( 'cleaner-gallery', trailingslashit( BON_CSS ) . 'frontend/gallery.css' , array(), false, 'all' );
	}
}
/**
 * Overwrites the default WordPress [gallery] shortcode's output.  This function removes the invalid 
 * HTML and inline styles.  It adds the number of columns used as a class attribute, which allows 
 * developers to style the gallery more easily.
 *
 * @since  1.0
 * @access private
 * @param  string $output The output of the gallery shortcode.
 * @param  array  $attr   The arguments for displaying the gallery.
 * @return string $output
 */
function cleaner_gallery( $output, $attr ) {
	global $_wp_additional_image_sizes;
	
	static $cleaner_gallery_instance = 0;
	$cleaner_gallery_instance++;

	/* We're not worried about galleries in feeds, so just return the output here. */
	if ( is_feed() )
		return $output;

	/* Orderby. */
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	/* Default gallery settings. */
	$defaults = array(
		'order'       => 'ASC',
		'orderby'     => 'menu_order ID',
		'id'          => get_the_ID(),
		'mime_type'   => 'image',
		'link'        => '',
		'itemtag'     => 'figure',
		'icontag'     => 'div',
		'captiontag'  => 'figcaption',
		'columns'     => 3,
		'size'        => isset( $_wp_additional_image_sizes['post-thumbnail'] ) ? 'post-thumbnail' : 'thumbnail',
		'ids'         => '',
		'include'     => '',
		'exclude'     => '',
		'numberposts' => -1,
		'offset'      => ''
	);

	/* Apply filters to the default arguments. */
	$defaults = apply_filters( 'cleaner_gallery_defaults', $defaults );

	/* Apply filters to the arguments. */
	$attr = apply_filters( 'cleaner_gallery_args', $attr );

	/* Merge the defaults with user input.  */
	$attr = shortcode_atts( $defaults, $attr );
	extract( $attr );
	$id = intval( $id );

	/* Arguments for get_children(). */
	$children = array(
		'post_status'      => 'inherit',
		'post_type'        => 'attachment',
		'post_mime_type'   => wp_parse_args( $mime_type ),
		'order'            => $order,
		'orderby'          => $orderby,
		'exclude'          => $exclude,
		'include'          => $include,
		'numberposts'      => $numberposts,
		'offset'           => $offset,
		'suppress_filters' => true
	);

	/* Get image attachments. If none, return. */
	if ( empty( $include ) )
		$attachments = get_children( array_merge( array( 'post_parent' => $id ), $children ) );
	else
		$attachments = get_posts( $children );

	if ( empty( $attachments ) )
		return '';

	/* Properly escape the gallery tags. */
	$itemtag    = tag_escape( $itemtag );
	$icontag    = tag_escape( $icontag );
	$captiontag = tag_escape( $captiontag );
	$i = 0;

	$link_class = 'gallery-link-' . $attr['link'];


	/* Count the number of attachments returned. */
	$attachment_count = count( $attachments );

	/* Allow developers to overwrite the number of columns. This can be useful for reducing columns with with fewer images than number of columns. */
	//$columns = ( ( $columns <= $attachment_count ) ? intval( $columns ) : intval( $attachment_count ) );
	$columns = apply_filters( 'cleaner_gallery_columns', intval( $columns ), $attachment_count, $attr );

	/* Open the gallery <div>. */
	$output = "\n\t\t\t<div id='gallery-{$id}-{$cleaner_gallery_instance}' class='gallery gallery-{$id}'>";

	/* Loop through each attachment. */
	foreach ( $attachments as $attachment ) {

		/* Open each gallery row. */
		if ( $columns > 0 && $i % $columns == 0 )
			$output .= "\n\t\t\t\t<div class='gallery-row gallery-col-{$columns} gallery-clear'>";

		/* Open each gallery item. */
		$output .= "\n\t\t\t\t\t<{$itemtag} class='gallery-item col-{$columns} {$link_class}'>";

		/* Get the image attachment meta. */
		$image_meta  = wp_get_attachment_metadata( $id );

		/* Get the image orientation (portrait|landscape) based off the width and height. */
		$orientation = '';

		if ( isset( $image_meta['height'], $image_meta['width'] ) )
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

		/* Open the element to wrap the image. */
		$output .= "\n\t\t\t\t\t\t<{$icontag} class='gallery-icon {$orientation}'>";

		/* Get the image. */
		if ( isset( $attr['link'] ) && 'file' == $attr['link'] ) 
			$image = wp_get_attachment_link( $attachment->ID, $size, false, true );

		elseif ( isset( $attr['link'] ) && 'none' == $attr['link'] )
			$image = wp_get_attachment_image( $attachment->ID, $size, false );

		else
			$image = wp_get_attachment_link( $attachment->ID, $size, true, true );

		/* Apply filters over the image itself. */
		$output .= apply_filters( 'cleaner_gallery_image', $image, $attachment->ID, $attr, $cleaner_gallery_instance );

		/* Close the image wrapper. */
		$output .= "</{$icontag}>";

		/* Get the caption. */
		$caption = apply_filters( 'cleaner_gallery_caption', wptexturize( $attachment->post_excerpt ), $attachment->ID, $attr, $cleaner_gallery_instance );

		/* If image caption is set. */
		if ( !empty( $caption ) )
			$output .= "\n\t\t\t\t\t\t<{$captiontag} class='gallery-caption'>{$caption}</{$captiontag}>";

		/* Close individual gallery item. */
		$output .= "\n\t\t\t\t\t</{$itemtag}>";

		/* Close gallery row. */
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= "\n\t\t\t\t</div>";
	}

	/* Close gallery row. */
	if ( $columns > 0 && $i % $columns !== 0 )
		$output .= "\n\t\t\t</div>";

	/* Close the gallery <div>. */
	$output .= "\n\t\t\t</div><!-- .gallery -->\n";

	/* Return out very nice, valid HTML gallery. */
	return $output;
}
?>