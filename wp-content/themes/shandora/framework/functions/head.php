<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Head Functions
 *
 *
 *
 * @author      Hermanto Lim
 * @copyright   Copyright (c) Hermanto Lim
 * @link        http://bonfirelab.com
 * @since       Version 1.2
 * @package     BonFramework
 * @category    Fuctions
 *
 *
*/ 
/* Adds common theme items to <head>. */
add_action( 'wp_head', 'bon_meta_charset', 0 );
add_action( 'wp_head', 'bon_http_equiv', 0 );
add_action( 'wp_head', 'bon_doctitle', 0 );
add_action( 'wp_head', 'bon_meta_viewport', 1 );
add_action( 'wp_head', 'bon_meta_template', 1 );
add_action( 'wp_head', 'bon_link_pingback', 3 );

/* Filter the WordPress title. */
add_filter( 'wp_title', 'bon_wp_title', 1, 3 );

/**
* Generates the relevant template info. Adds template meta with theme version. Uses the theme
* name and version from style.css.
* filter hook.
*
* @since 1.0.0
* @access public
* @return void
*/
function bon_meta_template() {
        $theme = wp_get_theme( get_template() );
        $template = sprintf( '<meta name="template" content="%s %s" />' . "\n", esc_attr( $theme->get( 'Name' ) ), esc_attr( $theme->get( 'Version' ) ) );

        echo apply_filters( 'bon_meta_template', $template );
}


/**
* Adds the meta charset to the header.
*
* @since 1.2.0
* @access public
* @return void
*/
function bon_http_equiv() {
        echo '<meta http-equiv="Content-Type" content="' . get_bloginfo( 'html_type' ) . '; charset='.get_bloginfo( 'charset' ).'" />' . "\n";
}

/**
* Adds the meta charset to the header.
*
* @since 1.2.0
* @access public
* @return void
*/
function bon_meta_charset() {
        echo '<meta charset="' . get_bloginfo( 'charset' ) . '" />' . "\n";
}

/**
* Adds the title to the header.
*
* @since 1.2.0
* @access public
* @return void
*/
function bon_doctitle() {
        printf( "<title>%s</title>\n", wp_title( ':', false ) );
}

/**
* Adds the meta viewport to the header.
*
* @since 1.2.0
* @access public
*/
function bon_meta_viewport() {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
* Adds the pingback link to the header.
*
* @since 1.2.0
* @access public
* @return void
*/
function bon_link_pingback() {
        if ( 'open' === get_option( 'default_ping_status' ) )
                echo '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
}

/**
* Filters the `wp_title` output early.
*
* @since 1.2.0
* @access public
* @param string $title
* @param string $separator
* @param string $seplocation
* @return string
*/
function bon_wp_title( $doctitle = null, $separator  = null, $seplocation  = null ) {

        if ( is_front_page() )
                $doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );

        elseif ( is_home() || is_singular() )
                $doctitle = single_post_title( '', false );

        elseif ( is_category() )
                $doctitle = single_cat_title( '', false );

        elseif ( is_tag() )
                $doctitle = single_tag_title( '', false );

        elseif ( is_tax() )
                $doctitle = single_term_title( '', false );

        elseif ( is_post_type_archive() )
                $doctitle = post_type_archive_title( '', false );

        elseif ( is_author() )
                $doctitle = get_the_author_meta( 'display_name', get_query_var( 'author' ) );

        elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
                $doctitle = bon_single_minute_hour_title( '', false );

        elseif ( get_query_var( 'minute' ) )
                $doctitle = bon_single_minute_title( '', false );

        elseif ( get_query_var( 'hour' ) )
                $doctitle = bon_single_hour_title( '', false );

        elseif ( is_day() )
                $doctitle = bon_single_day_title( '', false );

        elseif ( get_query_var( 'w' ) )
                $doctitle = bon_single_week_title( '', false );

        elseif ( is_month() )
                $doctitle = single_month_title( ' ', false );

        elseif ( is_year() )
                $doctitle = bon_single_year_title( '', false );

        elseif ( is_archive() )
                $doctitle = bon_single_archive_title( '', false );

        elseif ( is_search() )
                $doctitle = bon_search_title( '', false );

        elseif ( is_404() )
                $doctitle = bon_404_title( '', false );

        /* If the current page is a paged page. */
        if ( ( ( $page = get_query_var( 'paged' ) ) || ( $page = get_query_var( 'page' ) ) ) && $page > 1 )
                /* Translators: 1 is the page title. 2 is the page number. */
                $doctitle = sprintf( __( '%1$s Page %2$s', 'bon' ), $doctitle . $separator, number_format_i18n( absint( $page ) ) );

        /* Trim separator + space from beginning and end. */
        $doctitle = trim( $doctitle, "{$separator} " );

        return $doctitle;
}