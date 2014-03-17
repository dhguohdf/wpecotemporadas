<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Bon Framework Option Machine Class
 * This class handle the output form for Options page and meta box options
 * THIS ARCHIVE HAS BEEN MODIFIED!!!!!!!!!11111111111
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

if(! class_exists('BON_Machine') ) {

	class BON_Machine {

		/**
		 * @var string 
		 */
		public $context = 'options_page';

		/**
		 * @var string 
		 */
		public $counter;

		/**
		 * @var string 
		 */
		public $group = '';


		public function __construct($options = array(), $context = '', $group = '') {

			// checking context 
			if($context != '') {
				$this->context = $context;
			}
			
			if($group != '') {
				$this->group = $group;
			}
			
			if( $options ) {
				
				$return = $this->options_machine($options, $group);
				$this->output = $return[0];
				$this->menu = $return[1];
				$this->menuitems = $return[2];

			}
				
		}

		/**
		 * Process options data and build option fields
		 *
		 * @uses get_option()
		 *
		 * @access public
		 * @since 1.0.0
		 *
		 * @return array
		 */
		public function options_machine($options, $group = '' ) {
		
		    global $allowedtags;

		    $this->counter = 0;
			$menu = '';
			$output = '';

			if($this->context == 'options_page') {
				$optionsframework_settings = get_option( $group );

				// Gets the unique option id
				if ( isset( $optionsframework_settings['id'] ) ) {
					$option_name = $optionsframework_settings['id'];
				}

				$settings = get_option($option_name);

			}
			// Create an array of menu items - multi-dimensional, to accommodate sub-headings.
			$menu_items = array();
			$headings = array();


			if($this->context == 'options_page') {
				foreach ( $options as $k => $v ) {
					if ( $v['type'] == 'heading' || $v['type'] == 'subheading' ) {
						$headings[] = $v;
					}
				}

				$prev_heading_key = 0;
				
				foreach ( $headings as $k => $v ) {
					$token = 'bon-option-' . preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( trim( str_replace( ' ', '', $v['label'] ) ) ) );
					
					// Capture the token.
					$v['token'] = $token;
					
					if ( $v['type'] == 'heading' ) {
						$menu_items[$token] = $v;
						$prev_heading_key = $token;
					}
					
					if ( $v['type'] == 'subheading' ) {
						$menu_items[$prev_heading_key]['children'][] = $v;
					}
				}
				
			}

			foreach ( $options as $value ) {

				$this->counter++;

				$val = '';

				//Start Heading

				if($this->context == 'options_page') {

					if ( $value['type'] != 'heading' && $value['type'] != 'subheading' ) {
						$class = ''; if( isset( $value['class'] ) ) { $class = ' ' . $value['class']; }
						$output .= '<div class="section section-' . esc_attr( $value['type'] ) . esc_attr( $class ) .'">'."\n";
						if( $value['type'] != 'info') {
							$output .= '<h3 class="heading">'. esc_html( $value['label'] ) .'</h3>'."\n";
						}
						if($value['type'] == 'editor') {
							$output .= '<div class="option">'."\n" . '<div class="controls with-editor">'."\n";
						} else {
							$output .= '<div class="option">'."\n" . '<div class="controls not-with-editor">'."\n";
						}

					} 

				} else {
					if ( $value['type'] == 'section' ) {
						$output .= '<tr><td colspan="2"><h2>' . $value['label'] . '</h2></td></tr>';
					} 
					else {
						$output .= '<tr class="'. ( isset($value['class']) ? $class = ' ' . $value['class'] : '' ) .'"><th><label for="' . $value['id'] . '">' . $value['label'] . '</label></th><td>';
					}
				}

				
				if($this->context == 'options_page') {
					
					// If the option is already saved, ovveride $val
					if (  $value['type'] != 'heading'  && $value['type'] != 'subheading'  && $value['type'] != 'info' ) {
						if ( isset( $settings[($value['id'])]) ) {
							$val = $settings[($value['id'])];
							// Striping slashes of non-array options
							if ( !is_array($val) ) {
								$val = stripslashes( $val );

							}
						}
					}

				}

				else {
					// Begin the field table and loop
					$val = get_post_meta( get_the_ID(), $value['id'], true);

				}
				
				$return = $this->render_element($value, $val);
				
				if($this->context == 'options_page') {
					$menu .= $return[1];
					$output .= $return[0];
				} else {
					$output .= $return[0];
				}
				

				// if TYPE is an array, formatted into smaller inputs... ie smaller values
				if ( is_array( $value['type'] ) ) {
					foreach( $value['type'] as $array ) {

						$id = $array['id'];
						$std = $array['std'];
						$saved_std = get_option( $id );
						if( $saved_std != $std ) {$std = $saved_std;}
						$meta = $array['meta'];

						if( $array['type'] == 'text' ) { // Only text at this point

							$output .= '<input class="input-text-small bon-input" name="'. esc_attr( $id ) .'" id="'. esc_attr( $id ) .'" type="text" value="'. esc_attr( $std ) .'" />';
							$output .= '<span class="meta-two">'. esc_html( $meta ) .'</span>';
						}
					}
				}

				if($this->context == 'options_page') {
					if ( $value['type'] != "heading" && $value['type'] != "subheading" ) {
						
						$explain_value = ( isset( $value['desc'] ) ) ? $value['desc'] : '';
						if ( !current_user_can( 'unfiltered_html' ) && isset( $value['id'] ) )
							$explain_value .= '<br /><br /><b>' . esc_html( __( 'You are not able to update this option because you lack the <code>unfiltered_html</code> capability.', 'bon' ) ) . '</b>';
						$output .= '</div><div class="explain">'. $explain_value .'</div>'."\n";
						$output .= '<div class="clear"> </div></div></div>'."\n";
					} 
				}
				else {
					if( $value['type'] != 'section' ) {
						$output .= '</td></tr>';
					}
				}

				
			}

			
			if($this->context == 'options_page') {
				// Override the menu with a new multi-level menu.
				if ( count( $menu_items ) > 0 ) {
					$menu = '';
					foreach ( $menu_items as $k => $v ) {
						$class = '';
						if ( isset( $v['icon'] ) && ( $v['icon'] != '' ) ) {
							$class = $v['icon'];
						}
						
						if ( isset( $v['children'] ) && ( count( $v['children'] ) > 0 ) ) {
							$class .= ' has-children';
						}
						
						$menu .= '<li class="top-level ' . $class . '">' . "\n" . '<div class="arrow"><div></div></div>'; 
						if ( isset( $v['icon'] ) && ( $v['icon'] != '' ) )
							$menu .= '<span class="icon"></span>';
						$menu .= '<a title="' . esc_attr( $v['label'] ) . '" href="#' . $v['token'] . '">' . esc_html( $v['label'] ) . '</a>' . "\n";
						
						if ( isset( $v['children'] ) && ( count( $v['children'] ) > 0 ) ) {
							$menu .= '<ul class="sub-menu">' . "\n";
								foreach ( $v['children'] as $i => $j ) {
									$menu .= '<li class="icon">' . "\n" . '<a title="' . esc_attr( $j['label'] ) . '" href="#' . $j['token'] . '">' . esc_html( $j['label'] ) . '</a></li>' . "\n";
								}
							$menu .= '</ul>' . "\n";
						}
						$menu .= '</li>' . "\n";

					}
				}
			}

			if ( isset( $_REQUEST['page'] ) ) {
				$output .= '</div>';
			}
		    return array($output, $menu, $menu_items);
		}

		
		/**
		 * recives data about a form field and spits out the proper html
		 *
		 * @param	array					$field			array with various bits of information about the field
		 * @param	string|int|bool|array	$meta			the saved data for this field
		 * @param	array					$repeatable		if is this for a repeatable field, contains parant id and the current integar
		 *
		 * @return	string									html for the field
		 */
		public function render_element( $field, $meta = null, $repeatable = null ) {
			if ( ! ( $field || is_array( $field ) ) )
				return;
			$menu = '';
			$output = '';
			$wrapper = '';

			global $allowedtags;
			
			if($this->context == 'options_page') {
				$optionsframework_settings = get_option( $this->group );

				// Gets the unique option id
				if ( isset( $optionsframework_settings['id'] ) ) {
					$option_name = $optionsframework_settings['id'];
				}

			}

			// get field data
			$type = isset( $field['type'] ) ? $field['type'] : null;
			$label = isset( $field['label'] ) ? $field['label'] : null;
			$desc = '';
			if($this->context == 'metabox') {
				$desc = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : null;
			}

			$place = isset( $field['place'] ) ? $field['place'] : null;
			$size = isset( $field['size'] ) ? $field['size'] : null;
			$post_type = isset( $field['post_type'] ) ? $field['post_type'] : null;
			$options = isset( $field['options'] ) ? $field['options'] : null;
			$settings = isset( $field['settings'] ) ? $field['settings'] : null;
			$repeatable_fields = isset( $field['repeatable_fields'] ) ? $field['repeatable_fields'] : null;
			$std = isset( $field['std'] ) ? $field['std'] : null;
			$icon = isset( $field['icon'] ) ? $field['icon'] : null;
			$class = isset( $field['class'] ) ? $field['class'] : null;
			$step = isset($field['step'] ) ? $field['step'] : null;
			$min = isset($field['min']) ? $field['min'] : null;
			$max = isset($field['max']) ? $field['max'] : null;
			if(empty($meta) && !empty($std)) {
				$meta = $std;
			}
			
			// the id and name for each field
			$id = isset( $field['id'] ) ? $field['id'] : null;
			if($this->context == 'options_page') {
				$name = isset( $field['id'] ) ? $option_name . "[".$field['id']."]" : null;
			}
			else {
				$name = isset( $field['id'] ) ? $field['id'] : null;
			}
			if ( $repeatable ) {
				if($this->context == 'options_page') {
					//$name = $option_name . '[' . $repeatable[0] . '[' . $repeatable[1] . '][' . $id .']]';
					$name = $option_name . '[' . $repeatable[0] . '][' . $repeatable[1] . '][' . $id . ']';
				} else {
					$name = $repeatable[0] . '[' . $repeatable[1] . '][' . $id .']';
				}
				
				$id = $repeatable[0] . '_' . $repeatable[1] . '_' . $id;
			}

			switch( $type ) {
				
				case 'text':
				case 'tel':
				case 'email':
				default:
					$output .= '<div id="input_' . esc_attr( $id ) . '"><input type="' . $type . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . esc_attr( $meta ) . '" class="regular-text bon-input" size="30" /></div>
							' . $desc;
				break;
				case 'iframe':
					$output .= '
					<iframe width="450" height="350" src="http://www.latlong.net/" frameborder="no" scrolling="no" 
					style="body > header {display: none;}">
				<p>Your browser does not support iframes.</p>
				</iframe>';
				break;
				case 'url':
					$output .= '<div id="input_' . esc_attr( $id ) . '"><input type="' . $type . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . esc_url( $meta ) . '" class="regular-text bon-input" size="30" /></div>
							' . $desc;
				break;
				case 'number':
					$output .= '<div id="input_' . esc_attr( $id ) . '"><input type="' . $type . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . intval( $meta ) . '" class="regular-text bon-input" size="30" /></div>
							' . $desc;
				break;
				// textarea
				case 'textarea':
					$output .= '<div id="input_' . esc_attr( $id ) . '"><textarea name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" cols="60" rows="4">' . esc_textarea( $meta ) . '</textarea></div>
							' . $desc;
				break;
				// editor
				case 'editor':
					$output .= '<div class="wp_editor_wrapper">';
					ob_start();
					wp_editor( $meta, $name, $settings );

					$output .= ob_get_clean();
					$output .=  '</div><div class="clear"></div><br />' . $desc . '';
				break;
				// checkbox
				case 'checkbox':
					$output .= '<input type="checkbox" class="bon-input" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" ' . checked( $meta, true, false ) . ' value="1" />
							<label for="' . esc_attr( $id ) . '">' . $desc . '</label>';
				break;
				// select, chosen
				case 'select':
				case 'chosen':
					$output .= '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '"' . ( $type == 'chosen' ? ' class="chosen"' : '') . (isset( $multiple ) && $multiple == true ? ' multiple="multiple"' : '') . '>'; 
					foreach ( $options as $val => $option )
						$output .= '<option' . selected( $meta, $val, false ) . ' value="' . $val . '">' . $option . '</option>';
					$output .= '</select><br />' . $desc;
				break;
				// radio
				case 'radio':
					$output .= '<ul class="meta_box_items">';
					foreach ( $options as $val => $option )
						$output .= '<li><input type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '-' . $val . '" value="' . $val . '" ' . checked( $meta, $val, false ) . ' />
								<label for="' . esc_attr( $id ) . '-' . $val . '">' . $option . '</label></li>';
					$output .= '</ul>' . $desc;
				break;
				// checkbox_group
				case 'multicheck':
					$output .= '<ul class="meta_box_items">';
					foreach ( $options as $val => $option )
						$output .= '<li><input type="checkbox" value="' . $val . '" name="' . esc_attr( $name ) . '[]" id="' . esc_attr( $id ) . '-' . $val . '"' . ( is_array( $meta ) && in_array( $val, $meta ) ? ' checked="checked"' : '' ) . ' /> 
								<label for="' . esc_attr( $id ) . '-' . $val . '">' . $option . '</label></li>';
					$output .= '</ul>' . $desc; 
				break;
				// color old (farbtastic)
				case 'color2':
					$meta = $meta ? $meta : '#';
					$output .= '<input type="text" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . $meta . '" size="10" />
						<br />' . $desc;
					$output .= '<div id="colorpicker-' . esc_attr( $id ) . '"></div>
						<script type="text/javascript">
						jQuery(function(jQuery) {
							jQuery("#colorpicker-' . esc_attr( $id ) . '").hide();
							jQuery("#colorpicker-' . esc_attr( $id ) . '").farbtastic("#' . esc_attr( $id ) . '");
							jQuery("#' . esc_attr( $id ) . '").bind("blur", function() { jQuery("#colorpicker-' . esc_attr( $id ) . '").slideToggle(); } );
							jQuery("#' . esc_attr( $id ) . '").bind("focus", function() { jQuery("#colorpicker-' . esc_attr( $id ) . '").slideToggle(); } );
						});
						</script>';
				break;
				// color wpColorPicker
				case 'color':
					$default_color = '';
					if ( isset($value['std']) ) {
						if ( $meta !=  $value['std'] )
							$default_color = ' data-default-color="' .$value['std'] . '" ';
					}
					$output .= '<input name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="bon-color"  type="text" value="' . esc_attr( $meta ) . '"' . $default_color .' />';
		 	
				break;


				case 'radio-img':
					$output .= '<ul class="meta_box_items">';

					foreach ( $options as $val => $option ) {

						$selected = '';
						if ( $meta != '' && $meta == $val ) {
							$selected = ' radio-img-selected';
						}
						$output .= '<li class="radio-img"><input class="radio-img-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '-' . $val . '" value="' . $val . '" ' . checked( $meta, $val, false ) . ' />
							<label class="radio-img-label" for="' . esc_attr( $id ) . '-' . $val . '">' . $val . '</label>
							<img src="' . esc_url( $option ) . '" alt="' . $val .'" class="radio-img-img ' . $selected .'" onclick="document.getElementById(\''. esc_attr( $id ) . '-' . $val .'\').checked=true;" />
								</li>';
					}
					$output .= '</ul>' . $desc;

				break;
				// post_select, post_chosen
				case 'post_select':
					$output = '</select><br/>';
				case 'post_list':
				case 'post_chosen': 
                    $output .= '<select data-placeholder="Escolha Um" name="' . esc_attr( $name ) . '[]" id="' . esc_attr( $id ) . '"' . ( $type == 'post_chosen' ? ' class="chosen"' : '' ) . ( isset( $multiple ) && $multiple == true ? ' multiple="multiple"' : '' ) . '> 
                            <option value="">'.__('Escolha Um','bon').'</option>'; // Escolha Um
                    $q = array(  
                        'post_type' => $post_type,  
                        'posts_per_page' => -1,  
                        'orderby' => 'name',  
                        'order' => 'ASC', 
                    ); 
  
                    if( isset($field['filter_author']) && $field['filter_author'] === true || current_user_can( 'manage_options' )) { 
                        $user_ID = get_current_user_id(); 
  
                        if($user_ID > 0 ) { 
                            $q['author'] = $user_ID; 
                        } 
                    } 
  
                    $posts = get_posts( $q ); 
  
                    foreach ( $posts as $item ) 
                        $output .= '<option value="' . $item->ID . '"' . selected( is_array( $meta ) && in_array( $item->ID, $meta ), true, false ) . '>' . $item->post_title . '</option>'; 
                      
                    $output .= '</select><br />' . $desc . '<div class="add-peco"><a href="'.admin_url('/post-new.php?post_type=' . $post_type ) .'" class="button button-primary primary">'.__('Criar Perfil ECO','bon').'</a></div>'; 
                break;
				// post_checkboxes
				case 'post_checkboxes':
					$posts = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1 ) );
					$output .= '<ul class="meta_box_items">';
					foreach ( $posts as $item ) 
						$output .= '<li><input type="checkbox" value="' . $item->ID . '" name="' . esc_attr( $name ) . '[]" id="' . esc_attr( $id ) . '-' . $item->ID . '"' . ( is_array( $meta ) && in_array( $item->ID, $meta ) ? ' checked="checked"' : '' ) . ' />
								<label for="' . esc_attr( $id ) . '-' . $item->ID . '">' . $item->post_title . '</label></li>';
					$post_type_object = get_post_type_object( $post_type );
					$output .= '</ul> ' . $desc . ' &nbsp;<span class="description"><a href="' . admin_url( 'edit.php?post_type=' . $post_type . '">Manage ' . $post_type_object->label ) . '</a></span>';
				break;
				// post_drop_sort
				case 'post_drop_sort':
					//areas
					$post_type_object = get_post_type_object( $post_type );
					$output .= '<p>' . $desc . ' &nbsp;<span class="description"><a href="' . admin_url( 'edit.php?post_type=' . $post_type . '">Manage ' . $post_type_object->label ) . '</a></span></p><div class="post_drop_sort_areas">';
					foreach ( $areas as $area ) {
						$output .= '<ul id="area-' . $area['id']  . '" class="sort_list">
								<li class="post_drop_sort_area_name">' . $area['label'] . '</li>';
								if ( is_array( $meta ) ) {
									$items = explode( ',', $meta[$area['id']] );
									foreach ( $items as $item ) {
										$output .= ( $display == 'thumbnail' ? get_the_post_thumbnail( $item, array( 204, 30 ) ) : get_the_title( $item ) ); 
										$output .= '<li id="' . $item . '">' . $output . '</li>';
									}
								}
						$output .= '</ul>
							<input type="hidden" name="' . esc_attr( $name ) . '[' . $area['id'] . ']" 
							class="store-area-' . $area['id'] . '" 
							value="' . ( $meta ? $meta[$area['id']] : '' ) . '" />';
					}
					$output .= '</div>';
					// source
					$exclude = null;
					if ( !empty( $meta ) ) {
						$exclude = implode( ',', $meta ); // because each ID is in a unique key
						$exclude = explode( ',', $exclude ); // put all the ID's back into a single array
					}
					$posts = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1, 'post__not_in' => $exclude ) );
					$output .= '<ul class="post_drop_sort_source sort_list">
							<li class="post_drop_sort_area_name">Available ' . $label . '</li>';
					foreach ( $posts as $item ) {
						$output .= ( $display == 'thumbnail' ? get_the_post_thumbnail( $item->ID, array( 204, 30 ) ) : get_the_title( $item->ID ) ); 
						$output .= '<li id="' . $item->ID . '">' . $output . '</li>';
					}
					$output .= '</ul>';
				break;
				// tax_select (meta box only)
				case 'tax_select':
					$output .= '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '">
							<option value="">Escolher Um</option>'; // Escolher Um
					$terms = get_terms( $id, 'get=all' );
					$post_terms = wp_get_object_terms( get_the_ID(), $id );
					$taxonomy = get_taxonomy( $id );
					$selected = $post_terms ? $taxonomy->hierarchical ? $post_terms[0]->term_id : $post_terms[0]->slug : null;
					foreach ( $terms as $term ) {
						$term_value = $taxonomy->hierarchical ? $term->term_id : $term->slug;
						$output .= '<option value="' . $term_value . '"' . selected( $selected, $term_value, false ) . '>' . $term->name . '</option>'; 
					}
					$output .= '</select> &nbsp;<span class="description"><a href="'.get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?taxonomy=' . $id . '">Manage ' . $taxonomy->label . '</a></span>
						<br />' . $desc;
				break;
				// tax_checkboxes (meta box only)
				case 'tax_checkboxes':
					$terms = get_terms( $id, 'get=all' );
					$post_terms = wp_get_object_terms( get_the_ID(), $id );
					$taxonomy = get_taxonomy( $id );
					$checked = $post_terms ? $taxonomy->hierarchical ? $post_terms[0]->term_id : $post_terms[0]->slug : null;
					foreach ( $terms as $term ) {
						$term_value = $taxonomy->hierarchical ? $term->term_id : $term->slug;
						$output .= '<input type="checkbox" value="' . $term_value . '" name="' . $id . '[]" id="term-' . $term_value . '"' . checked( $checked, $term_value, false ) . ' /> <label for="term-' . $term_value . '">' . $term->name . '</label><br />';
					}
					$output .= '<span class="description">' . $field['desc'] . ' <a href="'.get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?taxonomy=' . $id . '&post_type=' . $page . '">Manage ' . $taxonomy->label . '</a></span>';
				break;
				// category_select
				case 'cat_select':
					$categories_opt = $this->get_wp_select_options('categories');
					$output .= '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '"' . ( $type == 'chosen' ? ' class="chosen"' : '') . (isset( $multiple ) && $multiple == true ? ' multiple="multiple"' : '') . '>
							<option value="">Escolher Um</option>'; // Escolher Um
					if($categories_opt) {
						foreach ( $categories_opt as $val => $option )
							$output .= '<option' . selected( $meta, $val, false ) . ' value="' . $val . '">' . $option . '</option>';
					}
						$output .= '</select><br />' . $desc;
				break;
				case 'tag_select':
					$tags_opt = $this->get_wp_select_options('tags');
					$output .= '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '"' . ( $type == 'chosen' ? ' class="chosen"' : '') . (isset( $multiple ) && $multiple == true ? ' multiple="multiple"' : '') . '>
							<option value="">Escolher Um</option>'; // Escolher Um
					if($tags_opt) {
						foreach ( $tags_opt as $val => $option )
							$output .= '<option' . selected( $meta, $val, false ) . ' value="' . $val . '">' . $option . '</option>';
					}
					
						$output .= '</select><br />' . $desc;
				break;
				case 'page_select':
					$pages_opt = $this->get_wp_select_options('pages');
					$output .= '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '"' . ( $type == 'chosen' ? ' class="chosen"' : '') . (isset( $multiple ) && $multiple == true ? ' multiple="multiple"' : '') . '>
							<option value="">Escolher Um</option>'; // Escolher Um
					if($pages_opt) {
						foreach ( $pages_opt as $val => $option )
							$output .= '<option' . selected( $meta, $val, false ) . ' value="' . $val . '">' . $option . '</option>';
					}
						$output .= '</select><br />' . $desc;
				break;
				// date
				case 'date':
					$output .= '<input type="text" class="datepicker" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . $meta . '" size="30" />
							<br />' . $desc;
				break;
				// slider
				case 'slider':
				$value = $meta != '' ? intval( $meta ) : '0';
					$output .= '<div id="' . esc_attr( $id ) . '-slider" data-min="'.$min.'" data-max="'.$max.'" data-step="'.$step.'"></div>
							<input type="text" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . $value . '" size="5" />
							<br />' . $desc;
				break;
				// image
				case 'image':
					$image = BON_IMAGES . '/image.png';	
					$output .= '<div class="meta_box_image"><span class="meta_box_default_image" style="display:none">' . $image . '</span>';
					if ( $meta ) {
						$image = wp_get_attachment_image_src( intval( $meta ), 'medium' );
						$image = $image[0];
					}				
					$output .=	'<input name="' . esc_attr( $name ) . '" type="hidden" class="meta_box_upload_image" value="' . intval( $meta ) . '" />
								<img src="' . esc_attr( $image ) . '" class="meta_box_preview_image" alt="" />
									<a href="#" class="meta_box_upload_image_button button" rel="' . get_the_ID() . '">Escolha uma imagem</a>
									<small>&nbsp;<a href="#" class="meta_box_clear_image_button">Remover uma imagem</a></small></div>
									<br clear="all" />' . $desc;
				break;
				// gallery
				case 'gallery':
					$output .= '<div class="gallery-images-container">
						<ul class="gallery-images">';
							
							if ( $meta ) {
								$attachments = array_filter( explode( ',', $meta ) );
								foreach ( $attachments as $attachment_id ) {
									$src = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
									$src = $src[0];
									$output .= '<li class="image" data-attachment_id="' . $attachment_id . '">
										<img src="' . esc_attr($src) . '" alt="image" />
										<ul class="actions">
											<li><a href="#" class="delete" title="' . __( 'Deletar imagem', 'bon' ) . '">' . __( 'Delete', 'bon' ) . '</a></li>
										</ul>
									</li>';
								}
							 }
					$output .= '</ul>
						<input type="hidden" class="image-gallery-input" id="'. esc_attr( $name ).'" name="'. esc_attr( $name ).'" value="'. esc_attr( $meta ).'" />
					</div>';

					$output .= '<p class="add-gallery-images hide-if-no-js"><a href="#">'. __( 'Adicionar imagens, Limite de 20 fotos', 'bon' ) .'</a></p>';
				break;
				// file
				case 'file':		
					$iconClass = 'meta_box_file';
					if ( $meta ) $iconClass .= ' checked';
					$output .=	'<div class="meta_box_file_stuff"><input name="' . esc_attr( $name ) . '" type="hidden" class="meta_box_upload_file" value="' . esc_url( $meta ) . '" />
								<span class="' . $iconClass . '"></span>
								<span class="meta_box_filename">' . esc_url( $meta ) . '</span>
									<a href="#" class="meta_box_upload_file_button button" rel="' . get_the_ID() . '">Choose File</a>
									<small>&nbsp;<a href="#" class="meta_box_clear_file_button">Remover Arquivo</a></small></div>
									<br clear="all" />' . $desc;
				break;

				// Uploader
				case "upload":

					$output .= $this->options_uploader( $id, $meta, null );
		
					break;

				break;
				// repeatable
				case 'repeatable':
					$t = get_option('bon_optionsframework');
					$t = get_option( $t['id'] );
					$output .= '<table id="' . esc_attr( $id ) . '-repeatable" class="meta_box_repeatable" cellspacing="0">';


					$i = 0;
					// create an empty array
					if ( $meta == '' || $meta == array() ) {
						$keys = wp_list_pluck( $repeatable_fields, 'id' );
						$meta = array ( array_fill_keys( $keys, null ) );
					}
					$meta = array_values( $meta );


					foreach( $meta as $row ) {
						$output .= '<tr>
								<td><span class="sort hndle"></span></td><td>';
						foreach ( $repeatable_fields as $repeatable_field ) {
							if ( ! array_key_exists( $repeatable_field['id'], $meta[$i] ) )
								$meta[$i][$repeatable_field['id']] = null;
							$output .= '<fieldset><label>' . $repeatable_field['label']  . '</label>';
							
							$repeated_field = $this->render_element( $repeatable_field, $meta[$i][$repeatable_field['id']], array( $id, $i ) );
							$output .= $repeated_field[0];
							$output .= '</fieldset>';
						} // end each field
						$output .= '</td><td><a class="meta_box_repeatable_remove" href="#"></a></td></tr>';
						$i++;
					} // end each row
					$output .= '</tbody>';
					$output .= '
						<tfoot>
							<tr>
								<th><a class="meta_box_repeatable_add" href="#"></a></th>
							</tr>
						</tfoot>';
					$output .= '</table>
						' . $desc;
				break;

				case "heading":
					if( $this->counter >= 2 ) {
						$output .= '</div>'."\n";
					}

					$jquery_click_hook = preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( $label ) );
					$jquery_click_hook = str_replace( ' ', '', $jquery_click_hook );

					$jquery_click_hook = "bon-option-" . $jquery_click_hook;
					$menu .= '<li class="'.esc_attr( $icon ).'"><a title="'. esc_attr( $label ) .'" href="#'.  $jquery_click_hook  .'">'.  esc_html( $label ) .'</a></li>';
					$output .= '<div class="group" id="'. esc_attr( $jquery_click_hook ) .'"><h1 class="subtitle">'. esc_html( $label ) .'</h1>'."\n";
				break;
				
				case "subheading":
					if( $this->counter >= 2 ) {
						$output .= '</div>'."\n";
					}
					$jquery_click_hook = preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( $label ) );
					$jquery_click_hook = str_replace( ' ', '', $jquery_click_hook );

					$jquery_click_hook = "bon-option-" . $jquery_click_hook;
					$menu .= '<li><a title="' . esc_attr( $label ) . '" href="#' . $jquery_click_hook . '">' . esc_html( $label ) . '</a></li>';
					$output .= '<div class="group" id="'. esc_attr( $jquery_click_hook ) .'"><h1 class="subtitle">'. esc_html( $label ).'</h1>'."\n";
				break;

				case "info":
					$output .= $std;
				break;
				
			} //end switch
			

			return array($output, $menu);
			
		}

		public function get_wp_select_options( $type ) {

			$options = array();

			switch ( $type ) {
				case 'categories':
					// Pull all the categories into an array
					$options_categories = array();
					$options_categories_obj = get_categories();
					foreach ($options_categories_obj as $category) {
						$options_categories[$category->cat_ID] = $category->cat_name;
					}
					$options = $options_categories;
				break;
				case 'tags':
					// Pull all tags into an array
					$options_tags = array();
					$options_tags_obj = get_tags();
					foreach ( $options_tags_obj as $tag ) {
						$options_tags[$tag->term_id] = $tag->name;
					}
					$options = $options_tags;
				break;
				case 'pages':
					// Pull all the pages into an array
					$options_pages = array();
					$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
					$options_pages[''] = 'Select a page:';
					foreach ($options_pages_obj as $page) {
						$options_pages[$page->ID] = $page->post_title;
					}
					$options = $options_pages;
				break;
			}

			return $options;
		}

		public function options_uploader( $_id, $_value, $_desc = '', $_name = '' ) {

			if($this->context == 'options_page') {
				$optionsframework_settings = get_option( $this->group );

				// Gets the unique option id
				if ( isset( $optionsframework_settings['id'] ) ) {
					$option_name = $optionsframework_settings['id'];
				}

			}


			$output = '';
			$id = '';
			$class = '';
			$int = '';
			$value = '';
			$name = '';
			
			$id = strip_tags( strtolower( $_id ) );
			
			// If a value is passed and we don't have a stored value, use the value that's passed through.
			if ( $_value != '' && $value == '' ) {
				$value = $_value;
			}
			
			if ( $_name != '' ) {
				$name = $_name;
			}
			else {
				$name = $option_name.'['.$id.']';
			}
			
			if ( $value ) {
				$class = ' has-file';
			}
			$output .= '<input id="' . $id . '" class="upload' . $class . '" type="text" name="'.$name.'" value="' . $value . '" placeholder="' . __('No file chosen', 'options_framework_theme') .'" />' . "\n";
			if ( function_exists( 'wp_enqueue_media' ) ) {
				if ( ( $value == '' ) ) {
					$output .= '<input id="upload-' . $id . '" class="upload-button button" type="button" name="upload-button" value="' . __( 'Upload', 'options_framework_theme' ) . '" />' . "\n";
				} else {
					$output .= '<input id="remove-' . $id . '" class="remove-file button" type="button" name="remove-file" value="' . __( 'Remove', 'options_framework_theme' ) . '" />' . "\n";
				}
			} else {
				$output .= '<p><i>' . __( 'Upgrade your version of WordPress for full media support.', 'options_framework_theme' ) . '</i></p>';
			}
			
			if ( $_desc != '' ) {
				$output .= '<span class="of-metabox-desc">' . $_desc . '</span>' . "\n";
			}
			
			$output .= '<div class="screenshot" id="' . $id . '-image">' . "\n";
			
			if ( $value != '' ) { 
				$remove = '<a class="remove-image">Remove</a>';
				$image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
				if ( $image ) {
					$output .= '<img src="' . $value . '" alt="" />'.$remove.'';
				} else {
					$parts = explode( "/", $value );
					for( $i = 0; $i < sizeof( $parts ); ++$i ) {
						$title = $parts[$i];
					}

					// No output preview if it's not an image.			
					$output .= '';
				
					// Standard generic output if it's not an image.	
					$title = __( 'View File', 'options_framework_theme' );
					$output .= '<div class="no-image"><span class="file_link"><a href="' . $value . '" target="_blank" rel="external">'.$title.'</a></span></div>';
				}	
			}
			$output .= '</div>' . "\n";
			return $output;
		}

		
	} //end Machine class
}
?>