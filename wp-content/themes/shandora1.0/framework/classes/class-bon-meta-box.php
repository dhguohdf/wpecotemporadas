<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Custom Meta Box Class
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


/**
 * takes in a few peices of data and creates a custom meta box
 *
 * @param	string			$id			meta box id
 * @param	string			$title		title
 * @param	array			$fields		array of each field the box should include
 * @param	string|array	$page		post type to add meta box to
 */
class BON_Metabox {
	
	public $id;
	public $title;
	public $fields;
	public $page;
	public $context;
	public $priority;
	
	public function __construct( $id = '', $title = '', $fields = '', $page = '', $context = 'normal', $priority = 'high' ) {

		if( !empty($id) && !empty($title) && !empty($fields) && !empty($page) ) {
			
			$this->create_box($id, $title, $fields, $page, $context, $priority );
		}

	}

    public function create_box( $id, $title, $fields, $page, $context = 'normal', $priority = 'high' ) {
		$this->id = $id;
		$this->title = $title;
		$this->fields = $fields;
		$this->page = $page;
		$this->context = $context;
		$this->priority = $priority;
		
		if( ! is_array( $this->page ) )
			$this->page = array( $this->page );
		
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_head',  array( &$this, 'admin_head' ) );
		add_action( 'add_meta_boxes', array( &$this, 'add_box' ) );
		add_action( 'save_post',  array( &$this, 'save_box' ));
    }
	
	/**
	 * enqueue necessary scripts and styles
	 */
	function admin_enqueue_scripts() {
		global $pagenow;

		if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && in_array( get_post_type(), $this->page ) ) {
			// js
			$deps = array( 'jquery' );
			if ( bon_find_field_type( 'date', $this->fields ) )
				$deps[] = 'jquery-ui-datepicker';
			if ( bon_find_field_type( 'slider', $this->fields ) )
				$deps[] = 'jquery-ui-slider';
			if ( bon_find_field_type( 'color2', $this->fields ) )
				$deps[] = 'farbtastic';
			if ( in_array( true, array(
				bon_find_field_type( 'chosen', $this->fields ),
				bon_find_field_type( 'post_chosen', $this->fields )
			) ) ) {
				wp_register_script( 'bon-chosen', BON_JS . '/chosen.js', array( 'jquery' ) );
				$deps[] = 'chosen';
				wp_enqueue_style( 'bon-chosen', BON_CSS . '/chosen.css' );
			}
			if ( bon_find_field_type( 'color', $this->fields ) ) {

				wp_enqueue_script( 'iris', BON_JS . '/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ));
				wp_enqueue_script( 'wp-color-picker', BON_JS . '/color-picker.min.js', array( 'jquery', 'iris' ) );
				wp_enqueue_style( 'wp-color-picker', BON_CSS . '/color-picker.min.css' );

				$colorpicker_l10n = array(
					'clear' => __( 'Clear','bon' ),
					'defaultString' => __( 'Default', 'bon' ),
					'pick' => __( 'Select Color', 'bon' )
				);
				wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
				
				$deps[] = 'jquery-ui-draggable';
				$deps[] = 'jquery-ui-slider';
				$deps[] = 'jquery-touch-punch';
				$deps[] = 'iris';
				$deps[] = 'wp-color-picker';
			}

			if( bon_find_repeatable( 'repeatable', $this->fields) ) {
				foreach($this->fields as $field) {
					if(isset($field['repeatable_fields']) && is_array($field['repeatable_fields'])) {
						if ( bon_find_field_type('slider', $field['repeatable_fields'] ) ) {
							$deps[] = 'jquery-ui-slider';
						}
						if (  bon_find_field_type('date', $field['repeatable_fields'] ) ) {
							$deps[] = 'jquery-ui-datepicker';
						}
						if (  bon_find_field_type('color', $field['repeatable_fields'] ) ) {
							$deps[] = 'jquery-ui-draggable';
							$deps[] = 'jquery-ui-slider';
							$deps[] = 'jquery-touch-punch';
							$deps[] = 'iris';
							$deps[] = 'wp-color-picker';
						}
					}
				}
			}



			if ( in_array( true, array( 
				bon_find_field_type( 'date', $this->fields ), 
				bon_find_field_type( 'slider', $this->fields ),
				bon_find_field_type( 'color', $this->fields ),
				bon_find_field_type( 'color2', $this->fields ),
				bon_find_field_type( 'chosen', $this->fields ),
				bon_find_field_type( 'post_chosen', $this->fields ),
				bon_find_field_type( 'image', $this->fields ),
				bon_find_field_type( 'file', $this->fields ),
				bon_find_field_type( 'radio-img', $this->fields ),
				bon_find_repeatable( 'repeatable', $this->fields ),
			) ) )
				wp_enqueue_script( 'bon-meta-box', BON_JS . '/metabox.js', $deps );
			

			// css
			$css_deps = array();

			wp_register_style( 'bon-jqueryui', BON_CSS . '/jqueryui.css' );

			if ( bon_find_field_type( 'date', $this->fields ) || bon_find_field_type( 'slider', $this->fields ) || bon_find_repeatable('repeatable', $this->fields ) )
			{
				$css_deps[] = 'bon-jqueryui';
			}	
			if ( bon_find_field_type( 'color2', $this->fields ) )
				$css_deps[] = 'farbtastic';
			if( bon_find_field_type( 'color', $this->fields ) ) {
				$css_deps[] = 'wp-color-picker';
			}
			
			wp_enqueue_style( 'bon-jqueryui' );
			wp_enqueue_style( 'bon-meta-box', BON_CSS . '/metabox.css', $css_deps, '1.0', 'all' ); 
		}
	}
	
	/**
	 * adds scripts to the head for special fields with extra js requirements
	 */
	function admin_head() {
		global $pagenow;
		if ( in_array( get_post_type(), $this->page ) && ( $pagenow == 'post-new.php' || $pagenow == 'post.php') && ( bon_find_field_type( 'date', $this->fields ) || bon_find_field_type( 'slider', $this->fields ) || bon_find_repeatable('repeatable', $this->fields) ) ) {
			
			echo '<script type="text/javascript">
						jQuery(function( $) {';
						
			foreach ( $this->fields as $field ) {
				$this->custom_script($field);
			
			}

			echo '});
				</script>';
		
		}
	}

	function custom_script($field) {
		
			switch( $field['type'] ) {
				// date
				case 'date' :
					echo '$("#' . $field['id'] . '").datepicker({
							dateFormat: \'dd-mm-yy\'
						});';
				break;
				// slider
				case 'slider' :
				$value = get_post_meta( get_the_ID(), $field['id'], true );
				if ( $value == '' )
					$value = $field['min'];
				echo '
						$( "#' . $field['id'] . '-slider" ).slider({
							value: ' . $value . ',
							min: ' . $field['min'] . ',
							max: ' . $field['max'] . ',
							step: ' . $field['step'] . ',
							slide: function( event, ui ) {
								$( "#' . $field['id'] . '" ).val( ui.value );
							}
						});';
				break;

				case 'repeatable' :

					$repeatable_fields = $field['repeatable_fields']; 
					$value = get_post_meta( get_the_ID(), $field['id'], true );
					$number_of_options = count($value);

					foreach($repeatable_fields as $repeatable_field) {
						for($i = 0; $i < $number_of_options; $i++) {
							switch ($repeatable_field['type']) {
								case 'date' :
									echo '$("#' .$field['id'] . '_' . $i . '_' . $repeatable_field['id'] . '").datepicker({
											dateFormat: \'dd-mm-yy\'
										});';
								break;
								// slider
								case 'slider' :

									//for($i = 0; $i < $number_of_options; $i++) {

										if(is_array($value)) {
											$repeat_field_value = (isset($value[$i][$repeatable_field['id']]) ? $value[$i][$repeatable_field['id']] : '');
										} else {
											$repeat_field_value = $value;
										}
										if ( $repeat_field_value == '' ) {
											$repeat_field_value = $repeatable_field['min'];
										}

										echo '
												$( "#' . $field['id'] . '_' . $i . '_' . $repeatable_field['id'] . '-slider" ).slider({
													value: ' . $repeat_field_value . ',
													min: ' . $repeatable_field['min'] . ',
													max: ' . $repeatable_field['max'] . ',
													step: ' . $repeatable_field['step'] . ',
													slide: function( event, ui ) {
														$( "#' . $field['id'] . '_' . $i . '_' . $repeatable_field['id'] . '" ).val( ui.value );
													}
												});';
									
								break;
							}
						}
					}

				break;
			}
		
	}
	
	/**
	 * adds the meta box for every post type in $page
	 */
	function add_box() {
		foreach ( $this->page as $page ) {
			add_meta_box( $this->id, $this->title, array( &$this, 'meta_box_callback' ), $page, $this->context, $this->priority );
		}
	}
	
	/**
	 * outputs the meta box
	 */
	function meta_box_callback() {
		
		// Use nonce for verification
		wp_nonce_field( 'bon_meta_box_nonce_action', 'bon_meta_box_nonce_field' );
		
		// Begin the field table and loop
		echo '<table class="form-table meta_box">';

		$machine = new BON_Machine($this->fields, 'metabox');

		echo $machine->output;

		echo '</table>'; // end table
	}
	
	/**
	 * saves the captured data
	 */
	function save_box( $post_id ) {
		$post_type = get_post_type();
		
		// verify nonce
		if ( ! isset( $_POST['bon_meta_box_nonce_field'] ) )
			return $post_id;
		if ( ! ( in_array( $post_type, $this->page ) || wp_verify_nonce( $_POST['bon_meta_box_nonce_field'],  'bon_meta_box_nonce_action' ) ) ) 
			return $post_id;
		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		// check permissions
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		
		// loop through fields and save the data
		foreach ( $this->fields as $field ) {
			if( $field['type'] == 'section' ) {
				$sanitizer = null;
				continue;
			}
			if( in_array( $field['type'], array( 'tax_select', 'tax_checkboxes' ) ) ) {
				// save taxonomies
				if ( isset( $_POST[$field['id']] ) ) {
					$term = $_POST[$field['id']];
					wp_set_object_terms( $post_id, $term, $field['id'] );
				}
			}
			else {
				// save the rest
				$old = get_post_meta( $post_id, $field['id'], true );
				if ( isset( $_POST[$field['id']] ) )
					$new = $_POST[$field['id']];
				if ( isset( $new ) && '' == $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				}
				else if ( isset( $new ) && $new != $old ) {
					$sanitizer = isset( $field['sanitizer'] ) ? $field['sanitizer'] : 'sanitize_text_field';
					if ( is_array( $new ) )
						$new = bon_array_map_r( 'bon_sanitize', $new, $sanitizer );
					else
						$new = bon_sanitize( $new, $sanitizer );
					update_post_meta( $post_id, $field['id'], $new );
				}


			}
		} // end foreach
	}
	
}