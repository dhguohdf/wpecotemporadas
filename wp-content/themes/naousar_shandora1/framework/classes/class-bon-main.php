<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Main BonFramework Class
 *
 * Contains the main functions for BonFramework, stores variables, and handles error messages
 *
 * @class BON_Main
 * @version	1.1
 * @since 1.0
 * @package	BonFramework
 * @author Hermanto Lim
 */

if( !class_exists('BON_Main') ) {
	
class BON_Main {

	/**
	 * @var string
	 */
	public $version = '1.1';

	/**
	 * @var string
	 */
	public $prefix = 'bon_';

	/**
	 * @var string
	 */
	public $theme_version;

	/**
	 * @var string
	 */
	public $theme_prefix;

	/**
	 * @var string
	 */
	public $theme_shortname;

	/**
	 * @var string
	 */
	public $theme_url;

	/**
	 * @var string
	 */
	public $theme_path;

	/**
	 * @var string
	 */
	public $theme_name;

	/**
	 * @var string
	 */
	public $theme_supports = array();

	/**
	 * @var string
	 */
	public $theme_thumbnails = array();

	/**
	 * @var string
	 */
	public $option_pages = array();

	/**
	 * @var string
	 */
	public $option_page_data = array();

	/**
	 * @var string
	 */
	public $framework_page_data = array();

	/**
	 * @var string
	 */
	public $textdomain;

	/**
	 * @var string
	 */
	public $manual_url;

	/**
	 * @var BON_Metabox
	 */
	public $mb;

	/**
	 * @var BON_Cpt
	 */
	public $cpt;

	/**
	 * @var BON_Form
	 */
	public $form;

	/**
	 * @var BON_Validation
	 */
	public $validation;

	/**
	 * BonFramework Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($base_data = array() ) {

		global $shortname;

		if( isset($base_data['supports']) ) {
			$this->theme_supports = $base_data['supports'];
		}
		
		if( isset($base_data['thumbnails']) ) {
			$this->theme_thumbnail = $base_data['thumbnails'];
		}

		if( isset($base_data['setting_pages']) ) {
			$this->option_pages = $base_data['setting_pages'];
		}

		$this->shortname = $shortname;

		// get theme data via object
		$this->theme_obj = $this->theme_object();

		// init framework constants
		$this->constants();

		// include require classes
		$this->classes();

		// setup theme settings
		$this->settings();

		$this->manual_url = 'http://www.support.bonfirelab.com/docs/'. strtolower( $this->theme_name ) .'/';;

		// init framework activate
		$this->activate();

		// Loaded action
		do_action( 'bon_framework_loaded' );

	}

	/**
	 * activate function.
	 *
	 * @access public
	 * @return void
	 */

	public function activate() {

		add_action( 'after_setup_theme', array( &$this, 'init' ), 1 );

		/* Load the core functions required by the rest of the framework. */
		add_action( 'after_setup_theme', array( &$this, 'core' ), 2 );

		/* Initialize the framework's default actions and filters. */
		add_action( 'after_setup_theme', array( &$this, 'default_filters' ), 3 );

		/* Language functions and translations setup. */
		add_action( 'after_setup_theme', array( &$this, 'i18n' ), 4 );

		/* Handle theme supported features. */
		add_action( 'after_setup_theme', array( &$this, 'filter_theme_support' ), 12 );

		/* Load the framework functions. */
		add_action( 'after_setup_theme', array( &$this, 'functions' ), 13 );

		/* Load the framework extensions. */
		add_action( 'after_setup_theme', array( &$this, 'extensions' ), 14 );

		/* Load admin files. */
		add_action( 'after_setup_theme', array( &$this, 'admin' ), 20 );
		
		
	}

	public function admin() {
		
		
		/* Check if in the WordPress admin. */
		if ( is_admin() && current_theme_supports('bon-core-admin') ) {

			require_once( BON_THEME_DIR . '/includes/theme-options.php');
			require_once( BON_THEME_DIR . '/framework/options.php');
		
			$settings = array();
			$framework_settings = array();
			
			if(function_exists('bon_set_theme_options')) {
				$theme_opts = bon_set_theme_options();

				foreach($theme_opts as $theme_opt) {
					$settings[] = $theme_opt;
				}
			}

			if(function_exists('bon_set_framework_options')) {
				$fw_opts = bon_set_framework_options();
				foreach($fw_opts as $fw_opt) {
					$framework_settings[] = $fw_opt;
				}
			}

			$this->framework_page_data = $framework_settings;
			$this->option_page_data = $settings;
			
			new BON_Admin($this);
		}

		if( is_admin() ) {
			/* Load the SEO post meta box. */
			require_if_theme_supports( 'bon-core-seo', trailingslashit( BON_EXTENSIONS ) . 'seo-metabox.php' );
		}
	}

	

	/**
	 * bon_constants function.
	 *
	 * @access public
	 * @return void
	 */
	public function constants() {

		/* Sets the framework version number. */
		define( 'BON_FW_VERSION', $this->version );

		/* Sets the theme version number. */
		define( 'BON_THEME_VERSION', $this->theme_obj->Version);

		/* Sets the parent theme name. */
		if(!is_child_theme()) {
			define( 'BON_THEME_NAME', $this->theme_obj->Name);
		} else {
			define( 'BON_THEME_NAME', $this->theme_obj->Template);
		}
		

		/* Sets the path to the parent theme directory. */
		define( 'BON_THEME_DIR', get_template_directory() );

		/* Sets the path to the parent theme directory. */
		define( 'BON_THEME_URI', get_template_directory_uri() );

		/* Sets the path to the child theme directory. */
		define( 'BON_CHILD_THEME_DIR', get_stylesheet_directory() );

		/* Sets the path to the child theme directory URI. */
		define( 'BON_CHILD_THEME_URI', get_stylesheet_directory_uri() );

		/* Sets the path to the core framework directory. */
		define( 'BON_DIR', trailingslashit( BON_THEME_DIR ) . 'framework' );

		/* Sets the path to the core framework directory URI. */
		define( 'BON_URI', trailingslashit( BON_THEME_URI ) . 'framework' );

		define( 'BON_INC', trailingslashit( BON_THEME_DIR ) . 'includes' );

		/* Sets the path to the core framework admin directory. */
		define( 'BON_ADMIN', trailingslashit( BON_DIR ) . 'admin' );

		/* Sets the path to the core framework classes directory. */
		define( 'BON_CLASSES', trailingslashit( BON_DIR ) . 'classes' );

		/* Sets the path to the core framework extensions directory. */
		define( 'BON_EXTENSIONS', trailingslashit( BON_DIR ) . 'extensions' );

		/* Sets the path to the core framework functions directory. */
		define( 'BON_FUNCTIONS', trailingslashit( BON_DIR ) . 'functions' );

		/* Sets the path to the core framework languages directory. */
		define( 'BON_LANGUAGES', trailingslashit( BON_DIR ) . 'languages' );

		/* Sets the path to the core framework images directory URI. */
		define( 'BON_IMAGES', trailingslashit( BON_URI ) . 'assets/images' );

		/* Sets the path to the core framework CSS directory URI. */
		define( 'BON_CSS', trailingslashit( BON_URI ) . 'assets/css' );

		/* Sets the path to the core framework JavaScript directory URI. */
		define( 'BON_JS', trailingslashit( BON_URI ) . 'assets/js' );

		define( 'BON_TINYMCE', trailingslashit( BON_DIR ) . 'tinymce' );

	}


	/**
	 * load framework core files
	 *
	 * @access public
	 * @return void
	 */
	public function core() {


		/* Load the core framework functions. */
		require_once( trailingslashit( BON_FUNCTIONS ) . 'core.php' );

		/* Load the context-based functions. */
		require_once( trailingslashit( BON_FUNCTIONS ) . 'context.php' );

		/* Load the core framework internationalization functions. */
		require_once( trailingslashit( BON_FUNCTIONS ) . 'i18n.php' );
	}


	/**
	 * load framework classes if specific theme support is available.
	 *
	 * @access public
	 * @return void
	 */
	public function classes() {
		/* Load the all needed class. */

		require_once( trailingslashit( BON_CLASSES ) . 'class-bon-meta-box.php' );

		require_once( trailingslashit( BON_CLASSES ) . 'class-bon-cpt.php' );

		if( !is_admin() ) {
			require_once( trailingslashit( BON_CLASSES ) . 'class-bon-form.php' );

			require_once( trailingslashit( BON_CLASSES ) . 'class-bon-validation.php' );

			require_once( trailingslashit( BON_CLASSES ) . 'class-bon-media.php' );
		}

		if( is_admin() ) {

			require_once( trailingslashit( BON_CLASSES ) . 'class-bon-backup.php' );

			require_once( trailingslashit( BON_CLASSES ) . 'class-bon-admin.php' );

			require_once( trailingslashit( BON_CLASSES ) . 'class-bon-machine.php' );
		}

	}

	/**
	 * load framework function if specific theme support is available.
	 *
	 * @access public
	 * @return void
	 */
	public function functions() {

		/* Load the comments functions. */
		require_once( trailingslashit( BON_FUNCTIONS ) . 'comments.php' );

		/* Load media-related functions. */
		require_once( trailingslashit( BON_FUNCTIONS ) . 'media.php' );

		/* Load the metadata functions. */
		require_once( trailingslashit( BON_FUNCTIONS ) . 'meta.php' );

		/* Load the utility functions. */
		require_once( trailingslashit( BON_FUNCTIONS ) . 'utility.php' );


		/* Load the menus functions if supported. */
		require_if_theme_supports( 'bon-core-menus', trailingslashit( BON_FUNCTIONS ) . 'menus.php' );

		/* Load the core SEO component if supported. */
		require_if_theme_supports( 'bon-core-seo', trailingslashit( BON_FUNCTIONS ) . 'core-seo.php' );

		/* Load the shortcodes if supported. */
		require_if_theme_supports( 'bon-core-shortcodes', trailingslashit( BON_FUNCTIONS ) . 'shortcodes.php' );

		/* Load the sidebars if supported. */
		require_if_theme_supports( 'bon-core-sidebars', trailingslashit( BON_FUNCTIONS ) . 'sidebars.php' );

		/* Load the template hierarchy if supported. */
		require_if_theme_supports( 'bon-core-template-hierarchy', trailingslashit( BON_FUNCTIONS ) . 'template-hierarchy.php' );

		if(!is_admin()) {

			/* Load the styles if supported. */
			require_if_theme_supports( 'bon-core-styles', trailingslashit( BON_FUNCTIONS ) . 'styles.php' );

			/* Load the scripts if supported. */
			require_if_theme_supports( 'bon-core-scripts', trailingslashit( BON_FUNCTIONS ) . 'scripts.php' );
			
		}
		
		require_if_theme_supports( 'post-formats', trailingslashit( BON_FUNCTIONS ) . 'post-formats.php' );

	}


	/**
	 * bon_extensions function.
	 *
	 * @access public
	 * @return void
	 */

	public function extensions() {

		require_if_theme_supports( 'bon-head-cleanup', trailingslashit( BON_EXTENSIONS ) . 'the-janitor.php' );

		if(is_admin()) {
			require_once( trailingslashit( BON_EXTENSIONS ) . 'input-filter.php' );
		}

		/* Load the Breadcrumb Trail extension if supported. */
		require_if_theme_supports( 'bon-breadcrumb-trail', trailingslashit( BON_EXTENSIONS ) . 'breadcrumb-trail.php' );

		/* Load the Cleaner Gallery extension if supported. */
		require_if_theme_supports( 'cleaner-gallery', trailingslashit( BON_EXTENSIONS ) . 'cleaner-gallery.php' );

		/* Load the Get the Image extension if supported. */
		require_if_theme_supports( 'get-the-image', trailingslashit( BON_EXTENSIONS ) . 'get-the-image.php' );

		/* Load the Cleaner Caption extension if supported. */
		require_if_theme_supports( 'cleaner-caption', trailingslashit( BON_EXTENSIONS ) . 'cleaner-caption.php' );

		/* Load the Custom Field Series extension if supported. */
		require_if_theme_supports( 'custom-field-series', trailingslashit( BON_EXTENSIONS ) . 'custom-field-series.php' );

		/* Load the Loop Pagination extension if supported. */
		require_if_theme_supports( 'bon-pagination', trailingslashit( BON_EXTENSIONS ) . 'pagination.php' );

		/* Load the Entry Views extension if supported. */
		require_if_theme_supports( 'entry-views', trailingslashit( BON_EXTENSIONS ) . 'entry-views.php' );

		/* Load the Theme Layouts extension if supported. */
		require_if_theme_supports( 'theme-layouts', trailingslashit( BON_EXTENSIONS ) . 'theme-layouts.php' );

		/* Load the Post Stylesheets extension if supported. */
		require_if_theme_supports( 'post-stylesheets', trailingslashit( BON_EXTENSIONS ) . 'post-stylesheets.php' );

		/* Load the Featured Header extension if supported. */
		require_if_theme_supports( 'featured-header', trailingslashit( BON_EXTENSIONS ) . 'featured-header.php' );

		/* Load the Random Custom Background extension if supported. */
		require_if_theme_supports( 'random-custom-background', trailingslashit( BON_EXTENSIONS ) . 'random-custom-background.php' );

		/* Load the Theme Fonts extension if supported. */
		require_if_theme_supports( 'theme-fonts', trailingslashit( BON_EXTENSIONS ) . 'theme-fonts.php' );

	}
	

	/**
	 * Adds the default framework actions and filters.
	 *
	 * @access public
	 * @return void
	 */
	public function default_filters() {

		/* Remove bbPress theme compatibility if current theme supports bbPress. */
		if ( current_theme_supports( 'bbpress' ) )
			remove_action( 'bbp_init', 'bbp_setup_theme_compat', 8 );

		/* Move the WordPress generator to a better priority. */
		remove_action( 'wp_head', 'wp_generator' );
		add_action( 'wp_head', 'wp_generator', 1 );

		/* Add the theme info to the header (lets theme developers give better support). */
		add_action( 'wp_head', 'bon_meta_template', 1 );

		/* Filter the textdomain mofile to allow child themes to load the parent theme translation. */
		add_filter( 'load_textdomain_mofile', 'bon_load_textdomain_mofile', 10, 2 );

		/* Filter text strings for Hybrid Core and extensions so themes can serve up translations. */
		add_filter( 'gettext', 'bon_gettext', 1, 3 );
		add_filter( 'gettext', 'bon_extensions_gettext', 1, 3 );

		/* Make text widgets and term descriptions shortcode aware. */
		add_filter( 'widget_text', 'do_shortcode' );
		add_filter( 'term_description', 'do_shortcode' );
	}

	/**
	 * handle theme support.
	 *
	 * @access public
	 * @return void
	 */
	public function filter_theme_support() {

		/* Remove support for the core SEO component if the WP SEO plugin is installed. */
		if ( defined( 'WPSEO_VERSION' ) )
			remove_theme_support( 'bon-core-seo' );

		/* Remove support for the the Breadcrumb Trail extension if the plugin is installed. */
		if ( function_exists( 'breadcrumb_trail' ) )
			remove_theme_support( 'breadcrumb-trail' );

		/* Remove support for the the Cleaner Gallery extension if the plugin is installed. */
		if ( function_exists( 'cleaner_gallery' ) )
			remove_theme_support( 'cleaner-gallery' );

		/* Remove support for the the Get the Image extension if the plugin is installed. */
		if ( function_exists( 'get_the_image' ) )
			remove_theme_support( 'get-the-image' );

		/* Remove support for the Featured Header extension if the class exists. */
		if ( class_exists( 'Featured_Header' ) )
			remove_theme_support( 'featured-header' );

		/* Remove support for the Random Custom Background extension if the class exists. */
		if ( class_exists( 'Random_Custom_Background' ) )
			remove_theme_support( 'random-custom-background' );

		
	}

	/**
	 * handle theme textdomain and language.
	 *
	 * @access public
	 * @return void
	 */
	public function i18n() {

		/* Get parent and child theme textdomains. */
		$parent_textdomain = bon_get_parent_textdomain();
		$child_textdomain = bon_get_child_textdomain();

		/* Load the framework textdomain. */
		$bon->textdomain_loaded['bon'] = bon_load_framework_textdomain( 'bon' );

		/* Load theme textdomain. */
		$bon->textdomain_loaded[$parent_textdomain] = load_theme_textdomain( $parent_textdomain );

		/* Load child theme textdomain. */
		$bon->textdomain_loaded[$child_textdomain] = is_child_theme() ? load_child_theme_textdomain( $child_textdomain ) : false;

		/* Get the user's locale. */
		$locale = get_locale();

		/* Locate a locale-specific functions file. */
		$locale_functions = locate_template( array( "languages/{$locale}.php", "{$locale}.php" ) );

		/* If the locale file exists and is readable, load it. */
		if ( !empty( $locale_functions ) && is_readable( $locale_functions ) )
			require_once( $locale_functions );
	}
 	
 	/**
	 * looping each available theme support array and add it to the wordpress theme support.
	 *
	 * @access public
	 * @return void
	 */
	public function set_theme_support( $supports ) {
		if( $supports && is_array( $supports ) ) {
			foreach( $supports as $feature => $args ) {
				if(!empty($args)) {
					add_theme_support( $feature, $args );
				}
				else {
					add_theme_support( $feature );
				}
			}
		}
	}


	/**
	 * Set all the default data
	 *
	 * @access public
	 * @return void
	 */
	public function settings($data = null) {

		if( empty( $this->theme_version ) ) {
			$this->theme_version = $this->theme_obj->Version;
		}

		if( empty( $this->theme_name ) ) {
			$this->theme_name = BON_THEME_NAME;
		}

		if( empty( $this->theme_prefix ) ) {
			
			$this->theme_prefix = strtolower($this->theme_name) . '_';
			
		}

		if( empty( $this->theme_url ) ) {
			$this->theme_url = get_template_directory_uri();
		}

		if( empty( $this->theme_path ) ) {
			$this->theme_path = get_template_directory();
		}

	}


	/**
	 * Init the required support when activating class
	 *
	 * @access public
	 * @return void
	 */
 	public function init() {

 		do_action('before_bon_init');

 		add_theme_support('automatic-feed-links');

		$this->init_supports();

 		do_action('after_bon_init');

 	}
 	
	/**
	 * Setup theme supports
	 *
	 * @access public
	 * @return void
	 */
	public function init_supports() {
		
		$default_supports = array(
			'bon-core-scripts' => '',
			'bon-core-styles' => '',
			'bon-core-seo' => '',
			'bon-core-admin' => '',
			'bon-core-shortcodes' => '',
			'bon-pagination' => '',
			'bon-head-cleanup' => '',
			'post-thumbnails' => array('post','page'),
			'bon-core-menus' => array(
									'primary' => __('Primary', 'bon' ) ,
								),
				
		);

		// check theme_supports, there may be already defined support before init base data and merge with default
		$this->theme_supports = array_merge( $this->theme_supports, $default_supports );

		$this->theme_supports = apply_filters('bon_filter_theme_supports', $this->theme_supports);
		
		if(!empty($this->theme_supports) && $this->theme_supports != NULL ) {
			$this->set_theme_support($this->theme_supports);
		}
	}

	/**
	 * Get theme object
	 *
	 * @access public
	 * @return object
	 */
	public function theme_object() {
		return wp_get_theme();
	}

	/**
	 * Setup form class
	 *
	 * @access public
	 * @return BON_Form
	 */
	public function form() {
		
		$this->form = new BON_Form();

		return $this->form;
	}

	/**
	 * Setup Metabox Class
	 *
	 * @access public
	 * @return BON_Metabox
	 */
	
	public function mb( $id = '', $title = '', $fields = '', $page = '', $context = 'normal', $priority = 'high' ) {
		
		$this->mb = new BON_Metabox($id, $title, $fields, $page, $context, $priority);
		
		return $this->mb;
	}

	/**
	 * Setup Custom Post Type Class
	 *
	 * @access public
	 * @return BON_Cpt
	 */
	public function cpt() {
		
		$this->cpt = new BON_Cpt();
	
		return $this->cpt;
	}

	/**
	 * Setup Validation Class
	 *
	 * @access public
	 * @return BON_Validation
	 */
	public function validation() {
		
		$this->validation = new BON_Validation();
	
		return $this->validation;
	}

	
}


}
