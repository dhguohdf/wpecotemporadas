<?php
function bon_styles() {
    if (!is_admin()) {
        // register main stylesheet
        
        if(current_theme_supports('bootstrap')) {

        	wp_register_style( 'bootstrap', trailingslashit( BON_CSS ) . 'frontend/bootstrap.css', array(), '', 'all');
        	wp_register_style( 'bootstrap-responsive', trailingslashit( BON_CSS ) . 'frontend/bootstrap-responsive.css', array(), '', 'all' );
        	wp_enqueue_style( 'bootstrap');
        	wp_enqueue_style( 'bootstrap-responsive' );

        }
        wp_register_style( 'gallery-carousel', trailingslashit( BON_CSS ) . 'frontend/gallery-carousel.css', array(), '', 'all' );



        if(current_theme_supports('dynamic-style')) {
            $ds = get_theme_support('dynamic-style');

            if( isset($ds[0]) && is_array($ds[0]) ) {
              foreach( $ds[0] as $dynamic_array ) {
                if( is_array($dynamic_array) ) {
                  $name = (isset($dynamic_array['name'])) ? $dynamic_array['name'] : '';
                  $folder = (isset($dynamic_array['folder'])) ? $dynamic_array['folder'] : '';
                  $filename = (isset($dynamic_array['filename'])) ? $dynamic_array['filename'] : '';
                  $version = (isset($dynamic_array['version'])) ? $dynamic_array['version'] : '';
                  $dep = (isset($dynamic_array['dep'])) ? $dynamic_array['dep'] : '';
                  $media = (isset($dynamic_array['media'])) ? $dynamic_array['media'] : 'all';

                  wp_register_style( 'bon-'.$name, BON_THEME_URI . '/assets/css/' . $folder . $filename . '.css', $dep, $version, $media );
                  wp_enqueue_style( 'bon-'.$name);
                }

              }

            }
        }

        wp_register_style( 'font-awesome', trailingslashit( BON_CSS ) . 'frontend/font-awesome.css', array(), false, 'all' );

        wp_enqueue_style( 'font-awesome' );

        

        wp_register_style( 'bon-stylesheet', trailingslashit( BON_CHILD_THEME_URI ) . 'style.css', array(), '', 'all' );
        wp_enqueue_style( 'bon-stylesheet');
        
    }
}
add_action('wp_enqueue_scripts', 'bon_styles');
?>