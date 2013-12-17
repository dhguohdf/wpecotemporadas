<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------*/
/* Start BonThemes Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/

// BonFramework init
require_once ( get_template_directory() . '/framework/bon.php' );

/*-----------------------------------------------------------------------------------*/
/* Load the theme-specific files, with support for overriding via a child theme.
/*-----------------------------------------------------------------------------------*/


$includes = array(
				'includes/theme-supports.php', // register / setup theme supports
				'includes/theme-posttypes.php',  // Custom theme posttypes
				'includes/search-fields.php',
				'includes/theme-actions.php',  // Theme actions & user defined hooks
				'includes/theme-toolkit-override.php',
				'includes/theme-hooks.php',
				'includes/theme-plugins.php',
				'includes/theme-shortcodes.php',
				'includes/custom-header.php',
				'includes/custom-background.php',
				'includes/theme-widgets.php',
				'includes/theme-head.php',
				);

// Allow child themes/plugins to add widgets to be loaded.

$includes = apply_filters( 'shandora_includes', $includes );



foreach ( $includes as $i ) {
	require_once( $i );
}

function shandora_admin_init() {
	
	if(!defined( "DSIDXPRESS_OPTION_NAME" )) {
		return;
	}

	$idx_opt = get_option(DSIDXPRESS_OPTION_NAME);

	if(is_plugin_active('dsidxpress/dsidxpress.php') && isset($idx_opt['Activated'])) {
		require_once( 'includes/theme-dsidxpress.php' );
	}
}



add_action( 'after_setup_theme', 'shandora_admin_init');
// this function for checking update in ThemeForest! Please do not edit the code
function shandora_updater() {
	if(bon_get_framework_option('bon_framework_update_notification') == true) {
		
		require_once( trailingslashit( BON_CLASSES ) . 'class-pixelentity-theme-update.php');

		$username = bon_get_framework_option('bon_framework_envato_username');

		$apikey = bon_get_framework_option('bon_framework_envato_api');

		$author = 'Hermanto Lim';

		PixelentityThemeUpdate::init($username,$apikey,$author); 
	}
}

add_action('admin_init', 'shandora_updater');

/*-----------------------------------------------------------------------------------*/
/* You can add custom functions below */
/*-----------------------------------------------------------------------------------*/

function shandora_first_and_last_menu_class($items) {
    $items[1]->classes[] = 'first';
    $items[count($items)]->classes[] = 'last';
    return $items;
}
add_filter('wp_nav_menu_objects', 'shandora_first_and_last_menu_class');


class Shandora_Navigation_Menu extends Walker_Nav_Menu {
    function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
        $id_field = $this->db_fields['id'];
        if ( !empty( $children_elements[ $element->$id_field ] ) ) {
            $element->classes[] = 'menu-has-children';
        }
        Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    function end_el(&$output, $item, $depth=0, $args=array()) {
    	if(in_array('menu-has-children', $item->classes) )  {
    		$output .= '<i class="icon awe-angle-down menu-toggle"></i>';
    	}
    	parent::end_el($output, $item, $depth, $args);
    }
}

function shandora_add_listing_class($class) {
	if(is_singular('car-listing')) {
		$class .= ' singular-listing';
		return $class;
	} else {
		return $class;
	}
}
add_filter('shandora_body_class', 'shandora_add_listing_class');