<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Entry Views Extensions
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

/* Add post type support for 'entry-views'. */
add_action( 'init', 'bon_entry_views_post_type_support' );

/* Add the [entry-views] shortcode. */
add_shortcode( 'entry-views', 'bon_entry_views_get' );

/* Registers the entry views extension scripts if we're on the correct page. */
add_action( 'template_redirect', 'bon_entry_views_load' );

/* Add the entry views AJAX actions to the appropriate hooks. */
add_action( 'wp_ajax_bon_entry_views', 'bon_entry_views_update_ajax' );
add_action( 'wp_ajax_nopriv_bon_entry_views', 'bon_entry_views_update_ajax' );


function bon_entry_views_post_type_support() {

    /* Add support for entry-views to the 'post' post type. */
    add_post_type_support( 'post', array( 'entry-views' ) );

    /* Add support for entry-views to the 'post' post type. */
    add_post_type_support( 'portfolio', array( 'entry-views' ) );

    /* Add support for entry-views to the 'page' post type. */
    add_post_type_support( 'page', array( 'entry-views' ) );

    /* Add support for entry-views to the 'attachment' post type. */
    add_post_type_support( 'attachment', array( 'entry-views' ) );
}


function bon_entry_views_load() {
    global $_bon_entry_views_post_id;

    /* Check if we're on a singular post view. */
    if ( is_singular() ) {

        /* Get the post object. */
        $post = get_queried_object();

        /* Check if the post type supports the 'entry-views' feature. */
        if ( post_type_supports( $post->post_type, 'entry-views' ) ) {

            /* Set the post ID for later use because we wouldn't want a custom query to change this. */
            $_bon_entry_views_post_id = get_queried_object_id();

            /* Enqueue the jQuery library. */
            wp_enqueue_script( 'jquery' );

            /* Load the entry views JavaScript in the footer. */
            add_action( 'wp_footer', 'bon_entry_views_load_scripts' );
        }
    }
}


function bon_entry_views_update( $post_id = '' ) {

    /* If we're on a singular view of a post, calculate the number of views. */
    if ( !empty( $post_id ) ) {

        /* Allow devs to override the meta key used. By default, this is '_entry_views'. */
        $meta_key = apply_filters( 'bon_entry_views_meta_key', '_entry_views' );

        /* Get the number of views the post currently has. */
        $old_views = get_post_meta( $post_id, $meta_key, true );

        /* Add +1 to the number of current views. */
        $new_views = absint( $old_views ) + 1;

        /* Update the view count with the new view count. */
        update_post_meta( $post_id, $meta_key, $new_views, $old_views );
    }
}


function bon_entry_views_get( $attr = '' ) {

    /* Merge the defaults and the given attributes. */
    $attr = shortcode_atts( array( 'before' => '', 'after' => '', 'post_id' => get_the_ID() ), $attr );

    /* Allow devs to override the meta key used. */
    $meta_key = apply_filters( 'bon_entry_views_meta_key', '_entry_views' );

    /* Get the number of views the post has. */
    $views = intval( get_post_meta( $attr['post_id'], $meta_key, true ) );

    /* Returns the formatted number of views. */
    return $attr['before'] . number_format_i18n( $views ) . $attr['after'];
}

function bon_the_entry_views() {
    echo bon_entry_views_get();
}

function bon_entry_views_update_ajax() {

    /* Check the AJAX nonce to make sure this is a valid request. */
    check_ajax_referer( 'bon_entry_views_ajax' );

    /* If the post ID is set, set it to the $post_id variable and make sure it's an integer. */
    if ( isset( $_POST['post_id'] ) )
        $post_id = absint( $_POST['post_id'] );

    /* If $post_id isn't empty, pass it to the bon_entry_views_update() function to update the view count. */
    if ( !empty( $post_id ) )
        bon_entry_views_update( $post_id );
}


function bon_entry_views_load_scripts() {
    global $_bon_entry_views_post_id;

    /* Create a nonce for the AJAX request. */
    $nonce = wp_create_nonce( 'bon_entry_views_ajax' );

    /* Display the JavaScript needed. */
    echo '<script type="text/javascript">/* <![CDATA[ */ jQuery(document).ready( function() { jQuery.post( "' . admin_url( 'admin-ajax.php' ) . '", { action : "bon_entry_views", _ajax_nonce : "' . $nonce . '", post_id : ' . $_bon_entry_views_post_id . ' } ); } ); /* ]]> */</script>' . "\n";
}

?>