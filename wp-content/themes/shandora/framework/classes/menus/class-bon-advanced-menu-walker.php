<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Class Bon Mega Menu
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.3
 * @package 	BonFramework
 * @category 	Core
 *
 *
*/ 

 
class BON_Advanced_Menu_Walker extends Walker_Nav_Menu {

	public $navopt;
	public $menu_index = 0;
	public $sidebar_lists = array();
	private $parent_ismega = false;
	private $parent_numcols = '';
	private $parent_fullwidth = false;
	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 1.3
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$adv_settings = $item->bon_nav_options;
		
		$this->navopt = array(
			'ismega'         => isset( $adv_settings['ismega'] ) && $adv_settings['ismega'] != '0' && $depth == 0   ? true : false,
			'fullwidth'      => isset( $adv_settings['fullwidth'] ) && $adv_settings['fullwidth'] != '0' && $depth == 0 ? true : false,
			'numcols'        => isset( $adv_settings['numcols'] ) && $depth == 0  ? $adv_settings['numcols']   : '',
			'notext'         => isset( $adv_settings['notext'] )     ? $adv_settings['notext']    : false,
			'nolink'         => isset( $adv_settings['nolink'] )     ? $adv_settings['nolink']    : false,
			'newrow'		 => isset( $adv_settings['newrow'] )     ? $adv_settings['newrow']    : false,
			'icon'           => isset( $adv_settings['icon'] )       ? $adv_settings['icon']      : '',
			'thumb'          => isset( $adv_settings['thumbnail'] )  ? $adv_settings['thumbnail'] : '',
			'widget'         => isset( $adv_settings['widget'] )     ? $adv_settings['widget']    : '',
		    'widgetcol'      => isset( $adv_settings['widgetcol'] )  ? $adv_settings['widgetcol'] : '',
		    'newcols'        => isset( $adv_settings['newcols'] )    ? $adv_settings['newcols']   : '',
		    'custom_content' => isset( $adv_settings['content'] )    ? $adv_settings['content']   : '',
		    'thumbpos' 		 => isset( $adv_settings['thumbpos'] )   ? $adv_settings['thumbpos']  : '',
		    'iconsize' 		 => isset( $adv_settings['iconsize'] )   ? $adv_settings['iconsize']  : '',
		    'separator'		 => isset( $adv_settings['separator'] )  ? $adv_settings['separator'] : '',
		    'trigger'		 => isset( $adv_settings['trigger'] )    ? $adv_settings['trigger']   : 'hover',
		    'align'		 	 => isset( $adv_settings['align'] )      ? $adv_settings['align']     : 'left',
		    'item_label'	 => $item->title,
		    'item_title'	 => $item->attr_title,
		    'desc'			 => $item->description,
		    'ID'			 => $item->ID
		);
		
		extract( $this->navopt );

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = '';

		$link_tag = apply_filters( 'bon_advanced_menu_no_link_tag', 'span' );
		

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		$classes[] = 'menu-item-depth-' . $depth;

		if( $this->menu_index == 0 ) $classes[] = 'menu-item-first';
		
		if( !empty( $icon ) || !empty( $thumb ) ) {

			if( $thumbpos == 'top' || $thumbpos == 'bottom' )
				$classes[] = 'menu-icon-vertical';

			if( $thumbpos == 'left' || $thumbpos == 'right' )
				$classes[] = 'menu-icon-horizontal';
		}

		if( $nolink )
			$classes[] = 'bon-menu-nolink';

		if( $notext )
			$classes[] = 'bon-menu-notext';

		if( !$icon )
			$classes[] = 'bon-menu-noicon';

		if( $depth == 0 ) {

			$classes[] = 'menu-item-'. $this->menu_index++;

			if( $ismega == true ) {

				$this->parent_ismega = true;

				$classes[] = 'bon-mega-menu-item';
				
				if( $fullwidth == true ) {

					$this->parent_fullwidth = true;

					$classes[] = 'bon-menu-full';

					if( is_numeric( $numcols ) && $numcols > 0 ) {

						$this->parent_numcols = $numcols;

						$numcols_mid = $numcols;

						if( $numcols > 2 ) {

							if( $numcols % 2 <= 0 ) {
								$numcols_mid = $numcols - 2;
							} else {
								$numcols_mid = $numcols - 1;
							}
						}

						$classes[] = 'lg-menu-col-'.$numcols.' md-menu-col-'.$numcols_mid. ' sm-menu-col-1';

					} else {

						$this->parent_numcols = 0;
					}

				} else {
					$this->parent_fullwidth = false;
				}

			} else {

				$classes[] = 'bon-normal-menu-item';
				$this->parent_ismega = false;
			}

		}

		if($depth == 1 && $this->parent_ismega ) {

			if( $newrow == true ) $classes[] = 'bon-menu-newrow';
		}

		if( $widget ) $classes[] = 'bon-menu-has-widget';
		if( $custom_content ) $classes[] = 'bon-menu-has-content';


		if( !empty( $item->description ) ) {
			$classes[] = 'bon-menu-has-desc';
		}
		/**
		 * Filter the CSS class(es) applied to a menu item's <li>.
		 *
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's <li>.
		 *
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $menu_id The ID that is applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		if( $depth > 0 && $separator && empty( $custom_content ) ){ 
			$output.= '<span class="bon-menu-divider"></span>';
			return; 
		}


		$atts = array();

		if( !$nolink ) {
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
			$atts['href']   = ! empty( $item->url )    	   ? $item->url        : '';
			$atts['class']  = 'bon-menu-label-link bon-menu-label';
			$link_tag = 'a';
		} else if ( !$notext ) {
			$atts['class'] = 'bon-menu-label-nolink bon-menu-label';
		} else if ( $notext && ( $thumb || $icon ) ) {
			$atts['class'] = 'bon-menu-thumb-only bon-menu-label';
		}

		if( $align ) {
			if( isset( $atts['class'] ) ) {
				$atts['class'] .= ' align-'.$align;
			} else {
				$atts['class'] = ' align-'.$align;
			}
		}


		/**
		 * Filter the HTML attributes applied to a menu item's <a>.
		 *
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item The current menu item.
		 * @param array  $args An array of wp_nav_menu() arguments.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;

		if( !$notext || ( $notext && ( $thumb || $icon ) ) ) {
			$item_output .= '<'. $link_tag . $attributes .'>';
			$item_output .= $args->link_before;
		}

		if( !$notext ) {
			$item_output .= $this->start_text() . apply_filters( 'the_title', $item->title, $item->ID ) . $this->end_text();
		} else if( $notext && ( $thumb || $icon ) ) {
			$item_output .= $this->process_icon_thumb();
		}

		if( !$notext || ( $notext && ( $thumb || $icon ) ) ) {	
			$item_output .= $args->link_after;
			$item_output .= '</'. $link_tag .'>';
		}

		$item_output .= $args->after;
		
		
		if( $depth > 0 && ( $custom_content || $widget ) && $this->parent_ismega == true ) {
			
			$item_output .= $this->process_custom_content_widget( $depth );

		}

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes $args->before, the opening <a>,
		 * the menu item's title, the closing </a>, and $args->after. Currently, there is
		 * no filter for modifying the opening and closing <li> for a menu item.
		 *
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu sub-menu-" . ( $depth + 1 ) . "\">\n";
	}

    /**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method should not be called directly, use the walk() method instead.
	 *
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {

		$id_field = $this->db_fields['id'];

        if ( !empty( $children_elements[ $element->$id_field ] ) ) {
            $element->classes[] = 'menu-has-children';

            $ismega = isset( $element->bon_nav_options['ismega'] ) && $depth == 0 && $element->bon_nav_options['ismega'] != '0' ? true : false;
            $trigger = isset( $element->bon_nav_options['trigger'] ) ? $element->bon_nav_options['trigger'] : 'hover';

            if( $depth == 0 ) {
				if( !empty( $trigger ) ) {
					$element->classes[] = 'bon-menu-'.$trigger; // always put trigger in ancestor item
				}
			}

			if( $depth > 0 && $this->parent_ismega == false ) {
				if( !empty( $trigger ) ) {
					$element->classes[] = 'bon-menu-'.$trigger; // trigger removed for non mega ancestor item
				}
			}
			
        }

        Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

    /**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Page data object. Not used.
	 * @param int    $depth  Depth of page. Not Used.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */

    function end_el( &$output, $item, $depth = 0, $args = array() ) {
    	if( in_array( 'menu-has-children', $item->classes ) )  {
    		$output .= '<i class="menu-toggle bonicons bi-angle-down menu-toggle"></i>';
    	}
		parent::end_el( $output, $item, $depth, $args );
	}


	/**
	 * Start the text output.
	 *
	 */
	function start_text() {

		if( $this->navopt['thumbpos'] == 'top' || $this->navopt['thumbpos'] == 'left' ) {
			return $this->process_icon_thumb() . '<span class="bon-menu-text-wrap"><span class="bon-menu-text">';
		}
		
		return '<span class="bon-menu-text-wrap"><span class="bon-menu-text">';
	}


	/**
	 * Ends the text output.
	 *
	 */
	function end_text() {

		extract( $this->navopt );

		$desc  = !empty( $this->navopt['desc'] ) ? '<span class="bon-menu-desc">'. $this->navopt['desc'] .'</span>' : '';

		if( $thumbpos == 'bottom' || $thumbpos == 'right' ) {
			
			return '</span>'.$desc.'</span>' . $this->process_icon_thumb();
		}

		return '</span>'.$desc.'</span>';
	}

	function display_icon() {

		extract( $this->navopt );

		$class = 'bi-pos-'.$thumbpos;

		if( $thumbpos == 'top' || $thumbpos == 'bottom' ) {
			$class .= ' bi-block';
		}

		if( $thumbpos == 'left' || $thumbpos == 'right' ) {
			$class .= ' bi-fw';
		}

		return '<i class="bon-menu-icon bonicons '.$icon.' bi-'.$iconsize.' ' . $class. '"></i> ';
	}

	function display_thumb() {

		extract( $this->navopt );

		$class = 'img-pos-'.$thumbpos;

		if( $thumbpos == 'top' || $thumbpos == 'bottom' ) {
			$class .= ' img-block img-center';
		}

		$alt = ! empty( $item_title ) ? $item_title : $item_label;

		return '<img class="bon-menu-thumb '. $class .'" src="'.$thumb.'" alt="'.$alt.'"/>';
	}

	function process_icon_thumb() {

		extract( $this->navopt );

		if( !empty( $icon ) ) {

			return $this->display_icon();

		} else if( !empty( $thumb ) ) {

			return $this->display_thumb();
		}
	}


	function start_menu_widget() {
		return apply_filters( 'bon_start_menu_widget', '<ul class="bon-menu-widget-container" id="bon-menu-widget-'. sanitize_title( $this->navopt['widget'] ). ' ">', $this->navopt['widget'] );
	}

	function end_menu_widget() {
		return apply_filters( 'bon_end_menu_widget', '</ul>' , $this->navopt['widget'] );
	}

	function process_custom_content_widget( $depth = 1 ) {

		$output = '';

		$content_class = '';

		extract( $this->navopt );

		if ( $widget && is_active_sidebar( $widget ) ) {

			$content_class = 'bon-menu-widgets';

			$output = $this->start_menu_widget();

        	ob_start();
	       
			dynamic_sidebar( $widget );

			$output .= ob_get_clean();

			$output .= $this->end_menu_widget();
		
		} else if( $custom_content ) {

			$additional_class = '';

			if( has_shortcode( $custom_content, 'bt-col' ) == true || has_shortcode( $custom_content, 'bm-post' ) == true ) {
				$additional_class = ' bt-col-row';
			}
			$content_class = 'bon-menu-content' . $additional_class;

			$output = apply_filters('bon_advanced_menu_custom_content', $custom_content );
		
		}

		$count = 0;

		if( $this->navopt['widget'] ) {

			if( $this->navopt['widgetcol'] == 'parent' && $this->parent_fullwidth == true && $this->parent_numcols > 0 ) {
				$count = $this->parent_numcols;
			} else if( is_numeric( $this->navopt['widgetcol'] ) ) {
				$count = $this->navopt['widgetcol'];
			} else {
				$count = $this->count_widget( $this->navopt['widget'] ); 
			}

		} 

		if( $count > 12 ) {
			$count = 12;
		}

		if( $count && $depth <= 1 ) {

			$numcols_mid = $count;

			if( $count > 2 ) {

				if( $count % 2 <= 0 ) {
					$numcols_mid = $count - 2;
				} else {
					$numcols_mid = $count - 1;
				}
			}

			$content_class .= ' lg-menu-col-'.$count. ' md-menu-col'.$numcols_mid.' sm-menu-col-1';
		}
		
		$output = '<div class="bon-menu-content-container content-depth-'.$depth.'"><div class="'.$content_class.'">'. $output .'</div></div>';

		return $output;

	}

	/**
	 * Get option for specific menu key
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      option value
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

	function count_widget( $sidebar_name ) {
		
		global $wp_registered_sidebars;

	    $sidebars_widgets = wp_get_sidebars_widgets();
	    
	    if ( empty($wp_registered_sidebars[$sidebar_name]) || !array_key_exists($sidebar_name, $sidebars_widgets) || !is_array($sidebars_widgets[$sidebar_name]) || empty($sidebars_widgets[$sidebar_name]) )
	        return 0;

	    return count( (array) $sidebars_widgets[$sidebar_name] );
	}

}