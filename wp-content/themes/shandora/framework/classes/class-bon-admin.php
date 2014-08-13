<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Admin page Class
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Core
 *
 *
*/ 
 
if( ! class_exists( 'BON_Admin' ) )
{
	class BON_Admin
	{
		/**
		 * @var obj
		 */
		public $bon;
		
		/**
		 * @var obj
		 */
		public $of_page;


		/**
		 * @var obj
		 */
		public $backup_token = 'bon_backup';

		/**
		 * @var obj
		 */
		public $framework_token = 'bon_framework';
		
		/**
		 * BON_Machine main constructor
		 *
		 */
		public function __construct(&$bon)
		{
			

			$this->bon = $bon;

			add_action('init', array(&$this, 'rolescheck'));

			do_action( 'bon_admin_init' );

		}

		/**
		 * Rolescheck for initing admin page only for specific user role. 
		 * 
		 * @access public
		 * @return void
		 */
		public function rolescheck() {
			if ( current_user_can( 'edit_theme_options' ) ) {
				// If the user can edit theme options, let the fun begin!
				add_action( 'admin_menu', array(&$this, 'add_page') );
				add_action( 'admin_init', array(&$this, 'init') );
				
			}
		}
	
		public function init() {
			
			
			// Load settings
			$optionsframework_settings = get_option('bon_optionsframework' );

			$optionsframework_frameworksettings = get_option('bon_framework_optionsframework');

			// Updates the unique option id in the database if it has changed
			$this->option_name();
			
			// Gets the unique id, returning a default if it isn't defined
			if ( isset( $optionsframework_settings['id'] ) ) {
				$option_name = $optionsframework_settings['id'];
			}
			else {
				$option_name = 'bon_optionsframework_options';
			}

			// If the option has no saved data, load the defaults
			if ( ! get_option($option_name) ) {
				$this->option_setdefaults();
			}

			if ( isset( $optionsframework_frameworksettings['id'] ) ) {
				$framework_name = $optionsframework_frameworksettings['id'];
			}
			else {
				$framework_name = 'bon_framework_optionsframework_options';
			}



			// If the option has no saved data, load the defaults
			if ( ! get_option($framework_name) ) {
				$this->option_setdefaults($this->framework_token);
			}

			
			add_action( 'bon_optionsframework_after_validate', array($this, 'save_options_notice') );

			// Registers the framework settings fields and callback
			register_setting( 'bon_framework_optionsframework', $framework_name, array($this, 'option_validate') );

			// Registers the settings fields and callback
			register_setting( 'bon_optionsframework', $option_name, array($this, 'option_validate') );


			// Change the capability required to save the 'optionsframework' options group.
			add_filter( 'option_page_capability_bon_options', array($this, 'option_page_capability') );

			add_filter( 'option_page_capability_bon_framework', array($this, 'option_page_capability') );


		}

		public function option_validate( $input ) {

			$option_group = 'bon_optionsframework';
			$framework_group = 'bon_framework_optionsframework';

			$token = 'bon_options';
			$option_page = '';
			if(isset($_POST['option_page'])) {
				$option_page = $_POST['option_page'];
			}
			
			$options = $this->bon->option_page_data;

			if( $option_page === $framework_group ) {
				$token = $this->framework_token;
				$options = $this->get_framework_options();
			}

			/*
			 * Restore Defaults.
			 *
			 * In the event that the user clicked the "Restore Defaults"
			 * button, the options defined in the theme's options.php
			 * file will be added to the option for the active theme.
			 */

			if ( isset( $_POST['reset'] ) ) {
				add_settings_error( $token , 'restore_defaults', __( 'Default options restored.', 'bon' ), 'updated fade' );
				return $this->_get_default_values( $token );
			}
			
			/*
			 * Update Settings
			 *
			 * This used to check for $_POST['update'], but has been updated
			 * to be compatible with the theme customizer introduced in WordPress 3.4
			 */
			 
			$clean = array();
			foreach ( $options as $option ) {
				if ( ! isset( $option['id'] ) ) {
					continue;
				}

				if ( ! isset( $option['type'] ) ) {
					continue;
				}


				//$id = strtolower( $option['id'] );
				$id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

				// Set checkbox to false if it wasn't sent in the $_POST
				if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) {
					$input[$id] = false;
				}
		
				// Set each item in the multicheck to false if it wasn't sent in the $_POST
				if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) {
					foreach ( $option['options'] as $key => $value ) {
						$input[$id][$key] = false;
					}
				}


				if( 'repeatable' != $option['type']) {
					if ( has_filter( 'bon_sanitize_' . $option['type'] ) ) {
						$clean[$id] = apply_filters( 'bon_sanitize_' . $option['type'], $input[$id], $option );
					}
				} else {
					$sanitizer = isset( $option['sanitizer'] ) ? $option['sanitizer'] : 'sanitize_text_field';
					$clean[$id] = bon_array_map_r( 'bon_sanitize', $input[$id], $option );
				}
				
			}
			// Hook to run after validation
			do_action( 'bon_optionsframework_after_validate', $clean );

			return $clean;
		}

		public function save_options_notice() {

			$option_group = 'bon_optionsframework';
			$framework_group = 'bon_framework_optionsframework';

			$token = 'bon_options';
			$option_page = '';
			
			if(isset($_POST['option_page'])) {
				$option_page = $_POST['option_page'];
			}

			if( $option_page === $framework_group ) {
				$token = $this->framework_token;
			}

			add_settings_error( $token , 'save_options', __( 'Options saved.', 'bon' ), 'updated fade' );
		}

		public function option_name() {

			
			$manualurl = $this->bon->manual_url;
			$optionsframework_settings = get_option( 'bon_optionsframework' );
			$optionsframework_frameworksettings = get_option( 'bon_framework_optionsframework' );
			$themename = $this->bon->theme_name;

			//for theme settings

			$optionsframework_settings['id'] = preg_replace("/\W/", "_", strtolower($themename) );

			update_option( 'bon_optionsframework', $optionsframework_settings );

			//for framework settings

			$optionsframework_frameworksettings['id'] = preg_replace("/\W/", "_", strtolower($themename) ) . '_framework';

			update_option( 'bon_framework_optionsframework', $optionsframework_frameworksettings );
			
			if ( get_option( 'bon_manual') != $manualurl) update_option( 'bon_manual', $manualurl);

		}


		public function option_page_capability( $capability ) {
			return 'edit_theme_options';
		}

		public function option_setdefaults( $type = '' ) {

			$opt_group = 'bon_optionsframework';
			$default_token = '';

			if($type == $this->framework_token) {
				$opt_group = 'bon_framework_optionsframework';
				$default_token = $this->framework_token;
			}

			$optionsframework_settings = get_option( $opt_group );

			// Gets the unique option id
			$option_name = $optionsframework_settings['id'];
			

			if ( isset( $optionsframework_settings['knownoptions'] ) ) {
				$knownoptions =  $optionsframework_settings['knownoptions'];
				if ( !in_array( $option_name, $knownoptions ) ) {
					array_push( $knownoptions, $option_name );
					$optionsframework_settings['knownoptions'] = $knownoptions;
					update_option( $opt_group , $optionsframework_settings );
				}
			} 

			else {
				$newoptionname = array( $option_name );
				$optionsframework_settings['knownoptions'] = $newoptionname;
				update_option( $opt_group , $optionsframework_settings );
			}
			

			// If the options haven't been added to the database yet, they are added now
			$values = $this->_get_default_values($default_token);

			if ( isset( $values ) ) {
				add_option( $option_name, $values ); // Add option with default settings
			}

		}
		
		/**
		 * Format Configuration Array.
		 *
		 * Get an array of all default values as set in
		 * options.php. The 'id','std' and 'type' keys need
		 * to be defined in the configuration array. In the
		 * event that these keys are not present the option
		 * will not be included in this function's output.
		 *
		 * @return    array     Rey-keyed options configuration array.
		 *
		 * @access    private
		 */
		 
		private function _get_default_values($slug = '') {

			$output = array();
			if( !empty($slug) && ($slug == $this->framework_token) ) {
				$config = $this->get_framework_options();
			} else {
				$config = $this->bon->option_page_data;
			}
			
			foreach ( (array) $config as $option ) {

				if ( ! isset( $option['id'] ) ) {
					continue;
				}
				if ( ! isset( $option['std'] ) ) {
					continue;
				}
				if ( ! isset( $option['type'] ) ) {
					continue;
				}
					
				if( 'repeatable' != $option['type']) {
					if ( has_filter( 'bon_sanitize_' . $option['type'] ) ) {
						$output[$option['id']] = apply_filters( 'bon_sanitize_' . $option['type'], $option['std'], $option );
					}
				} else {
					$sanitizer = isset( $option['sanitizer'] ) ? $option['sanitizer'] : 'sanitize_text_field';
					$output[$option['id']] = bon_array_map_r( 'bon_sanitize', $output[$option['id']], $option );
				}
			}
			return $output;
		}


		/**
		 * This is the function that is called when a framework option page gets opened. 
		 * It checks the current page slug and based on that slug filters the $this->bon->option_page_data options array.
		 * @access public
		 * @return string
		 */
		public function add_page()
		{	
			if(!isset($this->bon->option_pages)) return;
			

			$add_page = 'add_object_page';

			if(!function_exists($add_page)) { $add_page = 'add_menu_page'; }
		    
		    global $current_user;
			$current_user_id = $current_user->user_login;

			$icon = bon_get_framework_option('bon_framework_backend_icon');
			if(empty($icon)) {
				$icon = BON_IMAGES . '/bon-icon.png';
			}
			
			$super_user = bon_get_framework_option('bon_framework_super_user');

			foreach( $this->bon->option_pages as $key => $data_set )
			{
			
				//if its the very first option item make it a main menu with theme name, then as first submenu add it again with real menu name 
				if($key === 0)
				{	
					$title = ucwords($this->bon->theme_name);
				
					$level = $data_set['slug'];
					$of_page = $add_page(	
										$title,
										$title,
										'manage_options',
										$level,
										array(&$this, 'render_page'),
										$icon
								);
				}
				
				if($data_set['parent'] == $data_set['slug'])
				{	
					if($data_set['role'] === 'superuser') {
						if( !empty($super_user) && $super_user != $current_user_id ) {
							continue;
						}
					}
					else if($data_set['slug'] == $this->backup_token && bon_get_framework_option('bon_framework_backupmenu_disable') == true) {
						continue;
					}

					$this->of_page = $of_page = add_submenu_page (	$level,									
													$data_set['title'],
													$data_set['title'],
													'manage_options',
													$data_set['slug'],
													array(&$this, 'render_page'));


				}


				if(!empty($of_page))
				{
					//add scripts and styles to all option pages
					add_action('admin_print_scripts-' . $of_page, array($this, 'load_scripts'));
					add_action('admin_print_styles-'  . $of_page, array($this, 'load_styles'));
				
				}
			}
		}

		/**
		 * This function filter all $this->bon->option_page_data and filter it based on current page slug
		 *
		 * @access public
		 * @return array
		 */
		public function get_option_based_on_slug($slug) {

			$opts = $this->bon->option_page_data;
			$newOpt = array();
			foreach($opts as $opt) {
				if($opt['slug'] && $opt['slug'] == $slug) {
					$newOpt[] = $opt;
				}
			}
			return $newOpt;
		}

		public function get_framework_options() {

			$opts = $this->bon->framework_page_data;
			
			return $opts;
		}

		/**
		 * This is the function that is called when a framework option page gets opened. 
		 * It checks the current page slug and based on that slug filters the $this->bon->option_page_data options array.
		 * @access public
		 * @return string
		 */
		public function render_page( $screen )
		{	
			$current_slug = $_GET['page'];

			if($current_slug != $this->backup_token && $current_slug != $this->framework_token ) {

				$this->render_element();
				
			}
			/*else if($current_slug == $this->backup_token) {

					$backup = new BON_Backup();
					$backup->init( $this->of_page );
					$backup->admin_screen();
				
			}*/
			else if($current_slug == $this->framework_token) {

				$this->render_element($this->framework_token);
			}
						
		}

		public function render_element($type = '') {

			$current_slug = $_GET['page'];

			$group = 'bon_optionsframework';

			if($type == $this->framework_token) {
				$options = $this->get_framework_options();
				$group = 'bon_framework_optionsframework';
			}
			else {
				$options = $this->get_option_based_on_slug($current_slug);
			}


			$html = new BON_Machine($options, 'options_page', $group);
			
			if($html && isset($html->output)) {
				echo $this->page_header($current_slug);
				echo '<div id="main">';
					if(isset($html->menu)) {

					echo '<div id="bon-nav">
			    	    	<div id="bon-nav-shadow"></div>' .
								'<ul>' .
									$html->menu .
								'</ul>
						  </div>';
					}
				 
				 	echo '<div id="content">';
					echo $html->output;
					echo '</div>';
				
					
				
				echo '<div class="clear"></div></div>';
				echo $this->page_footer($current_slug);
			} 
		}
		/**
		 * Ouput admin page header such as logo, nonce field, etc
		 *
		 * @access public
		 * @return string
		 */
		public function page_header($slug) {
			
			if(!$slug) return;

			$option_group = 'bon_optionsframework';

			if($slug == $this->framework_token) {
				$option_group = 'bon_framework_optionsframework';
			}

			$slug = strtolower( str_replace( array( ' ', '_' ), '-', $slug ) );


			$output = '';			

			

			$output .= '<div class="wrap" id="bon_container">';
			$output .= settings_errors();

			$output .= '<form action="options.php" id="bonform" method="post">';

			// Add nonce for added security.
			$output .= '<input type="hidden" name="option_page" value="' . esc_attr($option_group) . '"/>';

			$output .= '<input type="hidden" name="action" value="update" />';

			if ( function_exists( 'wp_nonce_field' ) ) { $output .= wp_nonce_field( "$option_group-options", '_wpnonce', true, false ); }

			
	        $output .= '<div id="header">';
	        $output .= '<div class="logo">';
	           
			if( bon_get_framework_option( 'bon_framework_backend_header_image' ) ) {

				$output .= '<img alt="" src="' . esc_url( bon_get_framework_option( 'bon_framework_backend_header_image' ) ) . '"/>';
	        
	        } else {

	        	$output .= '<img alt="bonfirelab" src="' . esc_url( BON_IMAGES . '/logo.png' ) . '"/>';
	        }      
	        
	        $output .= '</div>';
	        $output .= '<div class="theme-info">';
	        $output .= $this->display_theme_version_data();
	        $output .= '</div>';
	        $output .= '<div class="clear"></div></div>';

	        $output .= $this->support_link();

	        return $output;
		}

		public function page_footer($slug) {

			if(!$slug) return;

			$option_group = 'bon_optionsframework';
			$output = '';
			$slug = strtolower( str_replace( array( ' ', '_' ), '-', $slug ) );
			$output .= '<div class="save_bar_top">';
			$output .= '<input type="submit" value="Reset Options" style="float: left" class="button button-secondary" name="reset" onclick="return confirm( \'' . esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'bon' ) ) . ' \');"/>';
		    $output .= '<input type="submit" value="Save All Changes" class="button button-primary submit-button" name="update" />';
		    $output .= '</div>';
		    $output .= '</form></div>';

			return $output;
		}


		/**
		 * Get Bonfirelab specific link for support, changelog and theme docs
		 *
		 * @access public
		 * @return string
		 */
		public function support_link() {
			$manualurl = get_option('bon_manual');

			$pos = strpos( $manualurl, 'documentation' );
			$theme_slug = str_replace( "/", "", substr( $manualurl, ( $pos + 13 ) ) ); //13 for the word documentation

			$output = '';

			$output .= '<div id="support-links"><ul>';
				
			$output .= '<li class="docs"><a title="Theme Documentation" href="'.esc_url( $manualurl ).'">' . __( 'View Theme Documentation', 'bon' ) . '</a></li>';
			
			$output .= '<li class="forum"><a href="'. esc_url( 'http://support.bonfirelab.com/' ) .'" target="_blank">' . __( 'Visit Support Forum', 'bon' ) . '</a></li>';
            
            $output .= '<li class="right">
            			<input type="submit" value="Save All Changes" class="button button-primary submit-button" name="update" />
            			</li>';
			$output .= '</ul></div>';
					
			return $output;
		}
		
		/**
		 * Load Admin Javscript
		 *
		 * @access public
		 * @return void
		 */
		public function load_scripts() {

			$deps = array( 'jquery', 'jquery-ui-sortable' );

			if ( bon_find_field_type( 'date', $this->bon->option_page_data ) )
				$deps[] = 'jquery-ui-datepicker';
			if ( bon_find_field_type( 'slider', $this->bon->option_page_data ) )
				$deps[] = 'jquery-ui-slider';
			if ( bon_find_field_type( 'color2', $this->bon->option_page_data ) )
				$deps[] = 'farbtastic';
			
			if ( in_array( true, array(
				bon_find_field_type( 'chosen', $this->bon->option_page_data ),
				bon_find_field_type( 'post_chosen', $this->bon->option_page_data )
			) ) ) {
				wp_register_script( 'bon-chosen', BON_JS . '/chosen.js', array( 'jquery' ) );
				$deps[] = 'bon-chosen';
				wp_enqueue_style( 'bon-chosen', BON_CSS . '/chosen.css' );
			}
			
			if ( bon_find_field_type( 'upload', $this->bon->option_page_data ) ) {
				if ( function_exists( 'wp_enqueue_media' ) )
					wp_enqueue_media();
					wp_register_script( 'bon-media-uploader', BON_JS . '/media-uploader.js', array( 'jquery' ) );

					wp_enqueue_script( 'bon-media-uploader' );

					wp_localize_script( 'bon-media-uploader', 'optionsframework_l10n', array(
					'upload' => __( 'Upload', 'bon' ),
					'remove' => __( 'Remove', 'bon' )
				) );
				$deps[] = 'bon-media-uploader';
			}

			if ( bon_find_field_type( 'color', $this->bon->option_page_data ) ) {

				wp_register_script( 'iris', BON_JS . '/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
				
				wp_register_script( 'wp-color-picker', BON_JS . '/color-picker.min.js' );

				wp_enqueue_script('wp-color-picker');

				wp_enqueue_script('iris');
				
				$colorpicker_l10n = array(
					'clear' => __( 'Clear','bon' ),
					'defaultString' => __( 'Default', 'bon' ),
					'pick' => __( 'Select Color', 'bon' )
				);
				wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
				
				$deps[] = 'iris';
				$deps[] = 'wp-color-picker';
				
			}
				
			wp_enqueue_script( 'bon-cm', BON_URI . '/assets/codemirror/lib/codemirror.js');
			wp_enqueue_script('bon-cm-mode', BON_URI . '/assets/codemirror/mode/css/css.js', array('jquery'), '2.24.0');
			wp_enqueue_script('bon-cm-js', BON_URI . '/assets/codemirror/mode/javascript/javascript.js', array('jquery'),'2.24.0');
		
			
			wp_enqueue_script( 'bon-admin', BON_JS . '/admin.js', $deps );

		}

		/**
		 * Admin action hook for custom scripts
		 *
		 * @access public
		 * @return void
		 */
		public function admin_head() {
			do_action( 'bon_admin_custom_scripts' );
		} 

		/**
		 * Load Admin Stylesheet
		 *
		 * @access public
		 * @return void
		 */
		public function load_styles() {

				if ( bon_find_field_type( 'color2', $this->bon->option_page_data ) ) {
					wp_enqueue_style( 'farbtastic' );
				}

				if ( bon_find_field_type( 'color', $this->bon->option_page_data ) ) {
					
					wp_register_style( 'wp-color-picker', BON_CSS .'/color-picker.min.css' );
					
					wp_enqueue_style( 'wp-color-picker' );
				}

				wp_enqueue_style( 'bon-cm', BON_URI . '/assets/codemirror/lib/codemirror.css');

				wp_enqueue_style( 'bon-admin', BON_CSS . '/admin.css' );
				
		}
		


		/**
		 * Display the version data for the currently active theme.
		 * @access public
		 * @return void
		 */
		public function display_theme_version_data ( $echo = false ) {
			$data = $this->get_theme_version_data();
			$html = '';

			// Theme Version
			if ( true == $data['is_child'] ) {
				$html .= '<span class="theme">' . esc_html( $data['child_theme_name'] . ' ' . $data['child_theme_version'] ) . '</span>' . "\n";
				$html .= '<span class="parent-theme">' . esc_html( $data['theme_name'] . ' ' . $data['theme_version'] ) . '</span>' . "\n";
			} else {
				$html .= '<span class="theme">' . esc_html( $data['theme_name'] . ' ' . $data['theme_version'] ) . '</span>' . "\n";
			}
			
			// Framework Version
			$html .= '<span class="framework">' . esc_html( sprintf( __( 'BonFramework %s', 'bon' ), $data['framework_version'] ) ) . '</span>' . "\n";

			if ( true == $echo ) { echo $html; } else { return $html; }
		} 

		/**
		 * Get the version data for the currently active theme.
		 * @access  public
		 * @return array [theme_version, theme_name, framework_version, is_child, child_theme_version, child_theme_name]
		 */
		public function get_theme_version_data () {

			global $bon;

			$response = array(
							'theme_version' => '', 
							'theme_name' => '', 
							'framework_version' => $bon->version, 
							'is_child' => is_child_theme(), 
							'child_theme_version' => '', 
							'child_theme_name' => ''
							);

			if ( function_exists( 'wp_get_theme' ) ) {
				$theme_data = wp_get_theme();
				if ( true == $response['is_child'] ) {
					$response['theme_version'] = $theme_data->parent()->Version;
					$response['theme_name'] = $theme_data->parent()->Name;

					$response['child_theme_version'] = $theme_data->Version;
					$response['child_theme_name'] = $theme_data->Name;
				} else {
					$response['theme_version'] = $theme_data->Version;
					$response['theme_name'] = $theme_data->Name;
				}
			} 

			return $response;
		}
		


	}
}




