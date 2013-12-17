<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Cleaner Head Extensions
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


  add_action( 'init', 'bon_head_cleanup' );
  add_filter( 'the_generator', 'bon_rss_version' );
  add_filter( 'wp_head', 'bon_remove_wp_widget_recent_comments_style', 1 );
  add_action( 'wp_head', 'bon_remove_recent_comments_style', 1 );
  add_filter( 'the_content', 'bon_filter_ptags_on_images' );
  add_filter( 'excerpt_more', 'bon_excerpt_more' );
  add_filter( 'the_generator', 'bon_rss_version' );
  add_action( 'admin_menu', 'disable_default_dashboard_widgets' );
  add_action( 'login_head', 'bon_login_css');
  add_filter( 'login_headerurl', 'bon_login_url' );
  add_filter( 'login_headertitle', 'bon_login_title' );
  
  add_filter( 'admin_footer_text', 'bon_custom_admin_footer' );


function bon_head_cleanup() {

	remove_action( 'wp_head', 'wp_generator' );
  remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'index_rel_link' );
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	add_filter( 'style_loader_src', 'bon_remove_wp_ver_css_js', 9999 );
	add_filter( 'script_loader_src', 'bon_remove_wp_ver_css_js', 9999 );
	add_filter( 'wp_head', 'bon_remove_wp_widget_recent_comments_style', 1 );
  add_action( 'wp_head', 'bon_remove_recent_comments_style', 1);
  add_filter ( 'wp_tag_cloud', 'bon_no_inline_style_tag_cloud' ); 

     
  if( bon_get_framework_option('bon_framework_remove_admin_bar') == 'true' ) {
    remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
    add_filter( 'wp_head','remove_admin_bar_style_frontend', 99 ); 
  } 
}


// remove Inline style from tag cloud
function bon_no_inline_style_tag_cloud( $list ) { 
    $list = preg_replace('/style=("|\')(.*?)("|\')/','',$list); 
    return $list; 
}

// remove WP version from RSS
function bon_rss_version() { return ''; }

// remove WP version from scripts
function bon_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}

// remove injected CSS for recent comments widget
function bon_remove_wp_widget_recent_comments_style() {
   if ( has_filter('wp_head', 'wp_widget_recent_comments_style') ) {
      remove_filter('wp_head', 'wp_widget_recent_comments_style' );
   }
}

// remove injected CSS from recent comments widget
function bon_remove_recent_comments_style() {
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  }
}

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function bon_filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function bon_excerpt_more($more) {
    global $post;

    return '&hellip;';
}

function bon_trunc_word($text, $max=100) {
   if (strlen($text) <= $max) return $text;
   $out = substr($text,0,$max);
   if (strpos($text,' ') === FALSE) return $out.$append;
   return preg_replace('/\w+$/','',$out).$append;
}

// disable default dashboard widgets
function disable_default_dashboard_widgets() {
  if(is_admin()) {
    remove_meta_box('dashboard_plugins', 'dashboard', 'core');         // Plugins Widget
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'core');   // Recent Drafts Widget
    remove_meta_box('dashboard_primary', 'dashboard', 'core');         //
    remove_meta_box('dashboard_secondary', 'dashboard', 'core');       //
    remove_meta_box('yoast_db_widget', 'dashboard', 'normal');         // Yoast's SEO Plugin Widget
  }
}

// changing the logo link from wordpress.org to your site
function bon_login_url() {  

  $opt = esc_url(bon_get_framework_option('bon_framework_custom_login_logo_url', home_url()));
  return $opt; 

}

// changing the alt text on the logo to show your site name
function bon_login_title() { 

  $opt = bon_get_framework_option('bon_framework_custom_login_logo_title', get_bloginfo( 'name' ));
  return $opt;

}

// Custom Backend Footer
function bon_custom_admin_footer() {
  echo '<span id="footer-thankyou">Developed by <a href="http://bonfirelab.com" target="_blank">Bonfirelab</a> Powered by <a href="http://wordpress.org" target="_blank">wordpress</a></span>.';
}

// adding it to the admin area
function remove_admin_bar_style_frontend() { // css override for the frontend  
    echo '<style type="text/css" media="screen"> 
    html { margin-top: 0px !important; } 
    * html body { margin-top: 0px !important; } 
    </style>';  
}  

function bon_login_css() {
  $logo = bon_get_framework_option('bon_framework_custom_login_logo');
  if(!empty($logo)) {
  echo '<style type="text/css">
            h1 a { background-image:url('.$logo.') !important; }
        </style>';
  }
}
?>