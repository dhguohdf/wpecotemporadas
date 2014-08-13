<?php
/**
 * Adding Iconic Menu Functionality into WordPress Navigation Menu for backend and frontend 
 * 
 * 
 *
 * @package default
 * @author 
 **/

if( !defined( 'BON_ADVANCED_MENU_KEY' ) ) {
	define( 'BON_ADVANCED_MENU_KEY', '_bon_nav_options' );
}

class BON_Advanced_Menu {

	protected $default_keys;
	protected $options_val;

	public $menu_color_mod;

	/**
	 * Constructor
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      void
	*/
	function __construct() {


		$this->menu_color_mod = apply_filters( 'bon_advanced_menu_color_mod', 'light' );

		// add custom menu fields to menu
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'add_field' ) );

		// save menu custom fields
		add_action( 'wp_update_nav_menu_item', array( $this, 'update_field' ), 10, 3 );

		// edit menu walker
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_walker' ), 10, 2 );

		add_action( 'init', array( $this, 'init' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 1000 );

		add_filter( 'wp_nav_menu_args' , array( $this, 'menu_args' ), 2000 );

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		add_action( 'admin_footer', array( $this, 'menu_footer' ), 1000 );

		add_action( 'admin_head', array( $this , 'add_meta_box' ) );

		add_action( 'wp_ajax_bon_update_advanced_menu_metabox', array( $this , 'update_metabox' ) );

		add_action( 'init', array( $this, 'mce_init' ) );

		add_filter( 'bon_advanced_menu_custom_content', 'wptexturize' );

		add_filter( 'bon_advanced_menu_custom_content', 'do_shortcode' );

		add_filter( 'bon_advanced_menu_custom_content', 'convert_smilies' );

		add_filter( 'bon_advanced_menu_custom_content', 'convert_chars' );

		add_filter( 'bon_advanced_menu_custom_content', 'shortcode_unautop' );

		add_shortcode( 'bm-video', array( $this, 'shortcode_video') );

		add_shortcode( 'bm-post', array( $this, 'shortcode_post') );

	}

	/**
	 * Register available menu option fields to the wp menu backend
	 * 
	 *
	 * @access public
	 * @since 1.0 
	 * @return void
	*/

	function register_options_key() {

		global $wp_registered_sidebars;

		$widget_options = array( '' => __('Select One', 'bon' ) );

		$sidebars_opt = bon_get_option('sidebars_generator');

		if(!empty($sidebars_opt) && is_array($sidebars_opt)) {
		
			foreach( $sidebars_opt as $sidebar ) {
			
				if( !empty($sidebar['sidebar_name'] ) && isset( $sidebar['is_menu'] ) ) {

					$id = strtolower(str_replace(" ", "-", $sidebar['sidebar_name']));

					// Check whether sidebar already registered or not
					if( array_key_exists($id, $wp_registered_sidebars) ) {

						$widget_options[$id] = $sidebar['sidebar_name'];

					} 
				}
			}
		}

		$this->default_keys = array(

			'separator' => array(
				'id' => 'separator',
				'level'	=> '1-plus', 
				'desc' => __( 'Turn this menu into a separator, any options will be ignored, unless there is a custom content / widget had been set' , 'bon' ), 
				'label' => __( 'Use As Divider' , 'bon' ), 
				'type' 	=> 'checkbox',
				'std' => 0
			),
			'ismega' => array(
				'id' => 'ismega',
				'level'	=> '0', 
				'desc' => __( 'Make this item\'s submenu a mega menu. Leave unchecked to use a normal menu. Note: if the menu location doesn\'t supporting mega menu this setting will be ignored.' , 'bon' ), 
				'label' => __( 'Activate Mega Menu' , 'bon' ), 
				'type' 	=> 'checkbox',
				'std' => 1
			),
			'fullwidth' => array(
				'id' => 'fullwidth',
				'level'	=> '0', 
				'desc' => __( 'Set the submenu to take full width of the menu bar' , 'bon' ), 
				'label' => __( 'Full Width Submenu' , 'bon' ), 
				'type' 	=> 'checkbox',
				'std' => 0
			),
			'notext' => array(
				'id' => 'notext',
				'level'	=> '0-plus', 
				'desc' => __( 'Remove the text/label from the menu item, very useful for image only menu.' , 'bon' ), 
				'label' => __( 'Disable Text' , 'bon' ), 
				'type' 	=> 'checkbox',
				'std' => 0
			),
			'nolink' =>  array(
				'id' => 'nolink',
				'level'	=> '0-plus', 
				'desc' => __( 'Remove link from this menu item, Can be useful when using widget or content.' , 'bon' ), 
				'label' => __( 'Disable Link' , 'bon' ), 
				'type' 	=> 'checkbox',
				'std' => 0
			),
			'newrow' => array(
				'id' => 'newrow',
				'level'	=> '1', 
				'desc' => __( 'Start a new row with this item. Always check this for the first item in your new row. Do not check this on fullwidth with defined number of column' , 'bon' ), 
				'label' => __( 'New Row' , 'bon' ), 
				'type' 	=> 'checkbox',
				'std' => 0
			),

			'align' => array(
				'id' => 'align',
				'level'	=> '0-plus', 
				'desc' => __( 'Set the label alignment.' , 'bon' ), 
				'label' => __( 'Alignment' , 'bon' ), 
				'type' 	=> 'select',
				'std' => '',
				'options' => array(
					'left' => __('Left', 'bon'),
					'center' => __('Center', 'bon'),
					'right' => __('Right', 'bon'),
				)
			),

			'trigger' => array(
				'id' => 'trigger',
				'level'	=> '0-plus', 
				'desc' => __( 'Set which action will trigger submenu display. Note: <strong>On click</strong> will prevent the link (if exists) to trigger.' , 'bon' ), 
				'label' => __( 'Event Trigger' , 'bon' ), 
				'type' 	=> 'select',
				'options' => array(
					'hover' => __('On Hover', 'bon'),
					'click' => __('On Click', 'bon'),
				),
				'std' => 'hover'
			),

			'numcols' => array(
				'id' => 'numcols',
				'level'	=> '0', 
				'desc' => __( 'Set number or column to be calculated for each row. Only work if Full Width Submenu is checked' , 'bon' ), 
				'label' => __( 'Submenu Column (Full Width Only)' , 'bon' ), 
				'type' 	=> 'select',
				'options' => array(
					'auto' => __('Auto', 'bon'),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8'
				),
				'std' => 'auto'
			),
			
			'icon' => array(
				'id' => 'icon',
				'level'	=> '0-plus', 
				'label' => __( 'Use Icon' , 'bon' ), 
				'desc' => __( 'Set a predefined icon from icon lists.' , 'bon' ), 
				'type' 	=> 'icon',
				'std' => ''
			),
			'iconsize' => array(
				'id' => 'iconsize',
				'level'	=> '0-plus', 
				'label' => __( 'Icon Size' , 'bon' ), 
				'desc' => __( 'Set the icon size.' , 'bon' ), 
				'type' 	=> 'select',
				'std' => '1x',
				'options' => array(
					'tiny' => '1/2x',
					'sml' => '3/4x',
					'1x' => '1x',
					'2x' => '2x',
					'3x' => '3x',
					'4x' => '4x',
					'5x' => '5x',
					'6x' => '6x',
					'7x' => '7x',
					'8x' => '8x',
					'9x' => '9x',
					'10x' => '10x'
				)
			),
			'thumbnail' => array(
				'id' => 'thumbnail',
				'level'	=> '0-plus', 
				'desc' => __( 'Set a thumbnail/image to be displayed.' , 'bon' ), 
				'label' => __( 'Set Thumbnail' , 'bon' ), 
				'type' 	=> 'upload',
				'std' => ''
			),
			'thumbpos' => array(
				'id' => 'thumbpos',
				'level'	=> '0-plus', 
				'desc' => __( 'Set the thumbnail/icon position.' , 'bon' ), 
				'label' => __( 'Thumbnail/Icon Position' , 'bon' ), 
				'type' 	=> 'select',
				'std' => '',
				'options' => array(
					'left' => __('Before Text', 'bon'),
					'top' => __('Above Text', 'bon'),
					'bottom' => __('Below Text', 'bon'),
					'right' => __('After Text', 'bon'),
				)
			),

			'widget' => array(
				'id' => 'widget',
				'level'	=> '1-plus', 
				'desc' => __( 'Choose a widget slot to display widget based content.' , 'bon' ), 
				'label' => __( 'Select Sidebar' , 'bon' ), 
				'type' 	=> 'select',
				'options' => $widget_options,
				'std' => ''
			),

			'widgetcol' => array(
				'id' => 'widgetcol',
				'level'	=> '1-plus', 
				'desc' => __( 'Select the widget column.' , 'bon' ), 
				'label' => __( 'Widget Column' , 'bon' ), 
				'type' 	=> 'select',
				'options' => array(
					'auto' => __('Auto (Depend on widget length)', 'bon'),
					'parent' => __('Same as parent', 'bon'),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12'
				),
				'std' => ''
			),

			'content' => array(
				'id' => 'content',
				'level'	=> '1-plus', 
				'desc' => __( 'Display a custom content. Input accept shortcodes and allowed html tags.' , 'bon' ), 
				'label' => __( 'Custom Content' , 'bon' ), 
				'type' 	=> 'editor',
				'std' => '',
			),
		);


		$this->default_keys = apply_filters( 'bon_nav_menu_option_key_defaults' , $this->default_keys );
	}

	/**
	 * This method basically serves as a wrapper on 'init' to allow themes to know when to 
	 * register their custom settings.  It also passes the object so that theme developers can 
	 * interact with it.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	function init() {

		$this->register_options_key();

		add_action( 'bon_walker_nav_menu_edit', array( $this, 'menu_options_start' ), 5 );

		add_action( 'bon_walker_nav_menu_edit', array( $this, 'register_menu_options' ), 10 );

		add_action( 'bon_walker_nav_menu_edit', array( $this, 'menu_options_end' ), 50 );

		$supports = get_theme_support( 'bon-core-menus' );

		do_action( 'bon_nav_menu_register', $this );

		add_shortcode( 'bm-video', array( $this, 'shortcode_video') );
	}


	/**
	 * This method is used to register all the options to show in the menu options
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	function register_menu_options( $item ) { 

		$default_opts = $this->default_keys;

		foreach( $default_opts as $key => $value ) {
			$this->add_option( $key, $item, $value );
		}

	}
	
	/**
	 * Rendering the menu options wrapper start
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	function menu_options_start( $item ) { ?>
		<div class="clear"></div>
		<div class="bon-nav-menu-edit">
			<strong class="bon-nav-menu-handle">
				<i class="dashicons dashicons-admin-generic"></i> <?php _e('Show/Hide Advance Options' ,'bon'); ?>
				<a class="bon-nav-menu-action"></a>
			</strong>
			<div class="bon-nav-menu-inside hide">

	<?php 	}
	

	/**
	 * Rendering the menu options wrapper end
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	function menu_options_end() {
		echo '</div></div>';
	}


	/**
	 * Adding option field to options block
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	function add_option( $key, $item, $args ) {

		$adv_key = BON_ADVANCED_MENU_KEY;

		$defaults = array(
			'level'	=> '0-plus',
			'desc' => '',
			'label' => '',
			'type'	=> 'text',
			'options'	=>	array(),
			'std'=> '',
		);

		apply_filters( 'bon_nav_menu_option_defaults', $defaults );

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		$val = $this->get_option( $item->ID, $key );

		if( $val == '' ) {
			$val = $std;
		}
		$desc = $this->get_desc( $label, $desc ); 
		$id = 'edit-menu-item-'.$key.'-'.$item->ID;
		$name = $adv_key.'['.$key.']['.$item->ID.']';

		?>

		
		<div class="bon-nav-menu-field description description-wide level-<?php echo $level; ?>">
            <label for="<?php echo $id; ?>">

        <?php
	               
					
		switch( $type ) {

			case 'text':
				
				echo $desc;
				echo '<input class="widefat bon-edit-menu-item" type="text" value="'.esc_attr( $val ).'" id="'.$id.'" name="'.$name.'" />';

			break;

			case 'textarea':

				echo $desc;

				echo '<textarea class="widefat bon-edit-menu-item" id="'.$id.'" name="'.$name.'">'. wp_kses_data( $val ) . '</textarea>';

			break;

			case 'select':

				echo $desc;

				echo '<select class="widefat bon-edit-menu-item" id="'.$id.'" name="'.$name.'">';

					foreach( $options as $opt_key => $opt_label ):
						
						echo '<option value="'.$opt_key.'" '. selected( $val, $opt_key, false ) . ' >'. $opt_label .'</option>';

					endforeach;

				echo '</select>';
			break;

			case 'checkbox':

				echo '<input type="checkbox" class="bon-edit-menu-item" id="'.$id.'" name="'.$name.'" value="1" ' . checked( $val, 1, false ) . '/>';
				echo $desc;

			break;

			case 'upload' :

				echo $desc;

				$class = '';

				if ( $val ) {
					$class = ' has-file';
				}

				echo '<span class="bon-edit-menu-fieldset">';
				echo '<input type="text" class="bon-edit-menu-item bon-edit-menu-upload'.$class.'" value="'.esc_attr( $val ).'" id="'.$id.'" placeholder="' . __('No file chosen', 'bon') .'" name="'.$name.'" />';

				if ( function_exists( 'wp_enqueue_media' ) ) {
					if ( ( $val == '' ) ) {
						echo '<input data-choose="'.__('Choose an image','bon').'" data-update="'.__('Use this image','bon').'" id="upload-'.$id.'" class="upload-button button" type="button" value="' . __( 'Upload', 'bon' ) . '" />' . "\n";
					} else {
						echo '<input id="remove-edit-menu-item-' . $item->ID . '" class="remove-file button" type="button" value="' . __( 'Remove', 'bon' ) . '" />' . "\n";
					}
				}

				echo '<span class="bon-edit-menu-screenshot" id="edit-menu-item-' . $item->ID . '-image">' . "\n";

				
				$image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $val );
				$img_cls = '';

				if( $val == '' ) {
					$img_cls = 'no-image';
				}
				$remove = '<a class="remove-image '.$img_cls.'"><i class="dashicons dashicons-no"></i></a>';
				echo '<img class="'.$img_cls.'" src="' . $val . '" alt="" />'.$remove.'';

				echo '</span>' . "\n";
				echo '</span>';

			break;

			case 'icon' :

				echo $desc;

				echo bon_icon_select_field( $id, $name, 'li#menu-item-'.$item->ID, esc_attr( $val ), array('bon-edit-menu-fieldset'), array( 'bon-edit-menu-item', 'bon-edit-menu-icon' ) );

			break;

			case 'editor':

				echo $desc;

				echo '<input type="button" class="button bon-menu-open-editor" data-button-submit="'.__('Save Content','bon').'" data-dialog-title="'.__('Edit Custom Content','bon').'" data-id="'.$item->ID.'" value="'.__('Edit Custom Content','bon').'"/>';

				echo '<textarea class="widefat bon-edit-menu-item bon-textarea-editor" id="'.$id.'" name="'.$name.'">'. $val . '</textarea>';

			break;

		}

		?>

		</label></div>

		<?php
	}

	/**
	 * Get Option description
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 *
	 */
	function get_desc( $label = '', $desc = '' ) {

		if( empty( $label ) || empty( $desc ) ) {
			return '';
		}

		return '<span class="bon-nav-menu-title"><span class="bon-nav-menu-label">'.$label.'</span><span class="bon-nav-menu-desc"><span class="bon-nav-menu-desc-icon">?</span><span class="bon-nav-menu-desc-content">'.$desc.'</span></span></span>'; 
	}

	/**
	 * Get option for specific menu key
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      option value
	 *
	 */
	function get_option( $item_id, $key ) {

		if( !isset( $this->options_val[ $item_id ] ) ){
			
			$options = get_post_meta( $item_id , BON_ADVANCED_MENU_KEY , true );

			if( $options ) {
				$this->options_val[ $item_id ] = $options;
			}
			
		}

		return isset( $this->options_val[ $item_id ][ $key ] ) ? stripslashes( $this->options_val[ $item_id ][ $key ] ) : '';

	}

	/**
	 * Add custom fields to $item nav object
	 * in order to be used in custom Walker
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      void
	 *
	 */
	function add_field( $menu_item ) {

	    $menu_item->bon_nav_options = get_post_meta( $menu_item->ID, BON_ADVANCED_MENU_KEY , true );
	    
	    return $menu_item;
	    
	}

	/**
	 * Save menu custom fields
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      void
	 *
	 */
	function update_field( $menu_id, $menu_item_db_id, $args ) {

		$options = array();
		$_nav_key = BON_ADVANCED_MENU_KEY;
		//file_put_contents( 'debug' . time() . '.log', var_export( $_POST, true));
		foreach( $this->default_keys as $key => $value ) {
			
			//file_put_contents( 'debug-'.$key. '-'. $menu_item_db_id . time() . '.log', var_export( $_REQUEST[$_nav_key][$key][$menu_item_db_id], true));

			// Set checkbox to false if it wasn't sent in the $_POST
			if ( 'checkbox' == $value['type'] && !isset( $_REQUEST[$_nav_key][$key][$menu_item_db_id] ) && ( $menu_id ) ) {
				$options[$key] = '0';
			} 

			if( 'icon' == $value['type'] && isset( $_REQUEST[$_nav_key][$key][$menu_item_db_id] ) ) {
				$options[$key] = apply_filters( 'bon_sanitize_text', $_REQUEST[$_nav_key][$key][$menu_item_db_id], $value );
			}

			if( 'editor' == $value['type'] && isset( $_REQUEST[$_nav_key][$key][$menu_item_db_id] ) ) {

				if ( current_user_can( 'unfiltered_html' ) ) {
					$options[$key] = $_REQUEST[$_nav_key][$key][$menu_item_db_id];
				}
				else {
					global $allowedtags;
					$options[$key] = wp_kses( $_REQUEST[$_nav_key][$key][$menu_item_db_id], $allowedtags );
				}
			}

			else {

				if ( has_filter( 'bon_sanitize_' . $value['type'] ) && isset( $_REQUEST[$_nav_key][$key][$menu_item_db_id] ) ) {
					$options[$key] = apply_filters( 'bon_sanitize_' . $value['type'],  $_REQUEST[$_nav_key][$key][$menu_item_db_id], $value );
				}

			}
		}
		update_post_meta( $menu_item_db_id, $_nav_key, $options );
	}


	/**
	 * Define new Walker edit
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      void
	 *
	 */
	function edit_walker( $walker ) {
		
	    return 'BON_Walker_Nav_Menu_Edit';
	    
	}

	/**
	 * Register admin css and js for wp menu backend
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      void
	 *
	 */
	function admin_scripts( $hook ) {
		if($hook == 'nav-menus.php') {
			global $is_IE;
			
			wp_enqueue_style( 'bon-admin-menu', trailingslashit( BON_CSS ) . 'admin-menu.css', array('wp-jquery-ui-dialog') );
			wp_enqueue_media();
			wp_enqueue_script( 'bon-admin-menu', trailingslashit( BON_JS ) . 'admin-menu.js', array('jquery', 'jquery-ui-dialog'), '1.0.0', true );
			wp_localize_script( 'bon-admin-menu', 'bon_admin_menu_ajax', array(
				'url' => admin_url('admin-ajax.php'),
				'upload' => __( 'Upload', 'bon' ),
				'remove' => __( 'Remove', 'bon' ),
			) );
		}
	}
	

	/**
	 * Set Custom walker for each menu which support advanced field
	 *
	 * @access public
	 * @since 1.3 
	 * @return void
	 *
	 */

	function menu_args( $args ) {

		$menus_on = get_option( BON_ADVANCED_MENU_KEY );
		$menu_locations = get_nav_menu_locations();

		if( !isset( $args['theme_location'] ) || empty( $args['theme_location'] ) ) {

			/* FOR SIDEBAR */

			if( in_array( $args['menu']->term_id, $menus_on ) ) {
				$args['walker'] 			= new BON_Advanced_Menu_Walker();
				$args['container_id'] 		= 'bon-mega-menu-'. $args['menu']->slug;
				$args['container_class'] 	= 'bon-mega-menu-container ';
				$args['menu_class']			= ' bon-mega-menu-items clearfix ';
				$args['items_wrap']			= '<ul id="%1$s" class="%2$s">%3$s</ul>';
			}
			
		} else {

			/* FOR THEME MENU LOCATION */

			$supports = get_theme_support( 'bon-core-menus' );

			if( is_array( $supports[0] ) && !empty( $supports[0]['menus'] ) && is_array( $menus_on ) ) {

				foreach( $supports[0]['menus'] as $menu ) {

					$current_location = $menu_locations[ $args['theme_location'] ];

					if( !in_array( $current_location, $menus_on ) ) {
						unset( $menu['advanced'] );
					}

					$color_mod = '';

					if (strpos($this->menu_color_mod,'bon-menu-') === false) {
					    $color_mod = 'bon-menu-'.$this->menu_color_mod;
					}
					
					if( isset( $menu['advanced'] ) && $menu['advanced'] == true ) {
						if( $menu['id'] == $args['theme_location'] ) {
							$args['walker'] 			= new BON_Advanced_Menu_Walker();
							$args['container_id'] 		= 'bon-mega-menu-'. $menu['id'];
							$args['container_class'] 	= 'bon-mega-menu-container ';
							$args['menu_class']			= ' bon-mega-menu-items clearfix '.$color_mod.' ';
							$args['items_wrap']			= '<ul id="%1$s" class="%2$s">%3$s</ul>';
						}
					}
				}
			}
		}

		return $args;
	}


	/**
	 * Register and enqueue the javascript needed for the menu.js
	 *
	 * @access public
	 * @since 1.3 
	 * @return void
	 *
	 */
	function frontend_scripts() {

		wp_register_style( 'bon-advanced-menu', trailingslashit( BON_CSS ) . 'frontend/menu.css', '', '1.0', 'screen' );
		wp_enqueue_style( 'bon-advanced-menu' );

		wp_register_script( 'bon-advanced-menu', trailingslashit( BON_JS ) . 'frontend/menu.js', array('jquery', 'hoverIntent'), '1.0', true );
		wp_enqueue_script( 'bon-advanced-menu' );

	}


	/**
	 * Create an editor in the wp footer for later used in custom content editor
	 *
	 * @access public
	 * @since 1.3 
	 * @return void
	 *
	 */
	function menu_footer() {

		global $pagenow;

		if( is_admin() && $pagenow === 'nav-menus.php' ) {

			$settings = array(
				'textarea_name' => 'bon-menu-editor-dialog',
				'media_buttons' => true,
				'wpautop' => false,
				'textarea_rows' => 10,
				'quicktags' => false,
				'editor_class' => 'bon-menu-editor-dialog',
				'tinymce' => array(
					'forced_root_block' => '',
					'plugins' => 'wordpress, wplink',
					'toolbar1' => 'bold, italic, strikethrough, bullist, numlist, blockquote, hr, alignleft, aligncenter, alignright, alignjustify, link, unlink, spellchecker, bon_toolkit_button, bon_advanced_menu_button, wp_adv',
					'toolbar2' => 'formatselect, underline, forecolor, pastetext, removeformat, charmap, outdent, indent, undo, redo, wp_help',
					'toolbar3' => '',
					'toolbar4' => '',
				)
			);

			$settings = apply_filters( 'bon_advanced_menu_editor_dialog_settings', $settings );

			echo '<div style="display: none;" class="bon-menu-editor-dialog-wrapper">';
			wp_editor( '', 'bon-menu-editor-dialog', $settings );
			echo '</div>';
		}

	}


	/**
	 * Add metabox to the nav-menus.php sidebar
	 *
	 * @access public
	 * @since 1.3 
	 * @return void
	 *
	 */
	function add_meta_box() {
		if ( wp_get_nav_menus() )
			add_meta_box( 'nav-menu-theme-bon-menu', __( 'Activate Advanced Menu' , 'bon' ), array( $this , 'activate_metabox' ) , 'nav-menus', 'side', 'high' );
	}


	/**
	 * Metabox Callback
	 *
	 * @access public
	 * @since 1.3 
	 * @return void
	 *
	 */
	function activate_metabox() {
		
		$nonce_action = 'bon_activate_advanced_menu_action';
		$nonce_name = 'bon_activate_advanced_menu_nonce';

		$nonce = isset( $_POST[$nonce_name] ) ? $_POST[$nonce_name] : false;

		/* This is just in case JS is not working.  It'll only save the last checked box */
		if( isset( $_POST['submit_bon_activate_advanced_menu'] ) && wp_verify_nonce( $nonce, $nonce_action ) ) {

			$data = isset( $_POST['bon_activate_advanced_menu'] ) ? $_POST['bon_activate_advanced_menu'] : array();

			if( !empty( $data ) && is_array( $data ) ) {

				if ( get_option( BON_ADVANCED_MENU_KEY ) !== false ) {
				    // The option already exists, so we just update it.
				    update_option( BON_ADVANCED_MENU_KEY, $data );

				    echo '<div class="updated"><p>' . __('Data Saved.','bon') . '</p></div>';

				} else {
				    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
				    $deprecated = null;
				    $autoload = 'no';
				    add_option( BON_ADVANCED_MENU_KEY, $data, $deprecated, $autoload );

				    echo '<div class="updated"><p>' . __('Data Saved.','bon') . '</p></div>';
				}

			}

		} else if( isset( $_POST['submit_bon_activate_advanced_menu'] ) && ! wp_verify_nonce( $nonce, $nonce_action ) ) {
			echo '<p>' . __('Failed saving data due to security trouble.', 'bon') . '</p>';
		}

		$active = get_option( BON_ADVANCED_MENU_KEY, array());

		$nav_menus = wp_get_nav_menus( array('orderby' => 'name') );

		?>

		<div class="bon-advanced-menu-metabox">
			<p class="bon-advanced-menu-info"><?php _e('Select the menu to activate the advanced menu.', 'bon' ); ?></p>

				<?php wp_nonce_field( $nonce_action, $nonce_name ); ?>

				<?php foreach( (array) $nav_menus as $key => $_nav_menu ) : ?>
					<div class="bon-advanced-menu-checkbox">
					<label class="menu-item-title">
						<input id="bon-activate-advanced-menu-<?php echo $_nav_menu->term_id; ?>" type="checkbox" <?php checked( in_array( $_nav_menu->term_id, $active ), true ); ?> value="<?php echo $_nav_menu->term_id; ?>" name="bon_activate_advanced_menu[]" class="menu-item-checkbox"/> <?php echo $_nav_menu->name; ?>
					</label>
					</div>
				
				<?php endforeach; ?>

				<p class="button-controls">
					<span class="list-controls">
						<a class="bon-activate-advanced-menu-all" href="#"><?php _e('Select All','bon'); ?></a>
					</span>
					<span class="add-to-menu">
						<input type="submit" id="submit-bon-activate-advanced-menu" name="submit_bon_activate_advanced_menu" value="<?php echo __('Save','bon'); ?>" class="button-primary right">
						<span class="spinner"></span>
					</span>
				</p>

		</div>
		<?php
		
	}


	/**
	 * Update metabox Callback
	 *
	 * @access public
	 * @since 1.3 
	 * @return void
	 *
	 */
	function update_metabox() {
		if ( !function_exists( 'check_admin_referer' ) ) {				
			return false;
			exit;
		}

		if( check_admin_referer( 'bon_activate_advanced_menu_action', 'bon_activate_advanced_menu_nonce' ) ) {
			if( isset( $_POST['checked'] ) && !empty( $_POST['checked'] ) ) {
				$data = json_decode( stripslashes( $_POST['checked'] ) );

				if( !empty( $data ) && is_array( $data ) ) {

					if ( get_option( BON_ADVANCED_MENU_KEY ) !== false ) {
					    // The option already exists, so we just update it.
					    update_option( BON_ADVANCED_MENU_KEY, $data );

					    die( __('Data Saved.','bon') );

					} else {
					    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
					    $deprecated = null;
					    $autoload = 'no';
					    add_option( BON_ADVANCED_MENU_KEY, $data, $deprecated, $autoload );

					    die( __('Data Saved.','bon') );
					}

				}
			}
		}
	}


	/**
	 * Init MCE Button only for nav-menus.php page
	 * @use global $pagenow
	 *
	 * @access public
	 * @since 1.3 
	 * @return void
	 *
	 */
	function mce_init() {

		global $pagenow;

		if( $pagenow != 'nav-menus.php' )
			return;

		if ( get_user_option('rich_editing') == 'true' ) {
			add_filter( 'mce_external_plugins', array($this, 'register_mce_external_plugins') );
		}
	}

	/**
	 * Register TinyMCE Button array
	 *
	 * @access public
	 * @since 1.3 
	 * @return void
	 *
	 */
	function register_mce_external_plugins( $plugin_array ) {

		$plugin_array['bonAdvancedMenuShortcode'] = trailingslashit( BON_JS ) . 'tinymce.js';
		return $plugin_array;

	}

	/**
	 * Register Menu Shortcode for Video
	 *
	 * @access public
	 * @since 1.3 
	 * @return string
	 *
	 */
	function shortcode_video( $attr, $content = null) {

		extract( shortcode_atts( array(
            'width' => '',
            'height' => '',
            'flexible' => 'yes',
        ), $attr ) );

		$url = esc_url( $content );

		$video = wp_oembed_get( $url, array( 'width' => intval( $width ), 'height' => intval( $height ) ) );

		if( $flexible == 'yes' ) {
			$video = '<div class="video-embed">' . $video . '</div>';
		}

		return $video;
	}

	/**
	 * Register Menu Shortcode for Video
	 *
	 * @access public
	 * @since 1.3 
	 * @return string
	 *
	 */
	function shortcode_post( $attr ) {


		extract( shortcode_atts( array(
            'numberposts' => 4,
            'post_type' => 'post',
            'term_slug' => '',
            'taxonomy_slug' => '',
            'orderby' => 'date',
            'order' => 'DESC',
            'show_excerpt' => 'no',
            'show_thumbnail' => 'yes',
        ), $attr ) );

		$o = apply_filters( 'bon_advanced_menu_recent_post_shortcode', '', $attr );

		if( !empty( $o ) )
			return $o;

	    static $instance = 0;
		$instance++;

		$tq = array();

		$q = array(
			'numberposts' => !empty( $numberposts ) ? absint( $numberposts ) : 4,
			'post_type' => !empty( $post_type ) ? $post_type : 'post',
			'orderby' => !empty( $orderby ) ? $orderby: 'date',
			'order' => !empty( $order ) ? $order : 'DESC',
			'ignore_sticky_posts' => true,
			'post_status' => 'publish'
		);



		if( $taxonomy_slug && $term_slug ) {

			$new_tax_slug = explode(',', $taxonomy_slug);

			if( count( $new_tax_slug ) > 1 ) {

				$i = 0;

				$term_slug = explode('|', $term_slug );

				foreach( $new_tax_slug as $tax_slug ) {

					$new_term_slug = explode(',', $term_slug[$i] );

					$tq[] = array(
						'terms' => $new_term_slug,
						'taxonomy' => $new_tax_slug[$i],
						'field' => 'slug',
					);

					$i++;
				}

			} else {

				$term_slug = explode(',', $term_slug );

				$tq[] = array(
					'terms' => $term_slug,
					'taxonomy' => $taxonomy_slug,
					'field' => 'slug',
				);

			}

			if( count( $tq ) > 1 ) {
				$tq['relation'] = 'AND';
			}

			$q['tax_query'] = $tq;
		}

		$menu_posts = get_posts( $q );


		if( $menu_posts ) : 

			$o .= '<div id="bon-menu-post-'.$instance.'" class="bt-col-row bon-menu-post" >';

			foreach( $menu_posts as $menu_post ) :

				$o .= '<div class="bon-menu-post-content bt-col bt-col-lg-'.( 12 / $numberposts ).' bt-col-sm-12">';

					if( $show_thumbnail == 'yes' && has_post_thumbnail( $menu_post->ID ) ) {
						$o .= '<div class="bon-menu-post-thumbnail">';
						$o .= get_the_post_thumbnail( $menu_post->ID, 'medium' );
						$o .= '</div>';
					}

					$o .= '<h3 class="bon-menu-post-title"><a href="'.get_permalink( $menu_post->ID ).'" title="'.__('Read More', 'bon').'">'.get_the_title( $menu_post->ID ).'</a></h3>';
					
					if( $show_excerpt == 'yes' ) {
						$o .= '<div class="bon-menu-post-excerpt">'. ( ( $menu_post->post_excerpt != '' ) ? $menu_post->post_excerpt : bon_trim_excerpt( $menu_post->post_content, 25 ) ) .'</div>';
					}
				
				$o .= '</div>';

			endforeach; 

			wp_reset_postdata();
			
			$o .= '</div>';

		endif; 

		return $o;
	}

	

} // END class 


require_once( trailingslashit( BON_CLASSES ) . 'menus/class-bon-walker-menu-edit.php' );

$GLOBALS['bon_advanced_menu'] = new BON_Advanced_Menu();


?>