<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Utility Functions
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Fuctions
 *
 *
*/ 

/* Add extra support for post types. */
add_action( 'init', 'bon_add_post_type_support' );

/* Add extra file headers for themes. */
add_filter( 'extra_theme_headers', 'bon_extra_theme_headers' );

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but 
 * they're not registered.
 *
 * @since 1.0
 * @access public
 * @return void
 */
function bon_add_post_type_support() {

	/* Add support for excerpts to the 'page' post type. */
	add_post_type_support( 'page', array( 'excerpt' ) );

	/* Add support for trackbacks to the 'attachment' post type. */
	add_post_type_support( 'attachment', array( 'trackbacks' ) );
}

/**
 * Creates custom theme headers.  This is the information shown in the header block of a theme's 'style.css' 
 * file.  Themes are not required to use this information, but the framework does make use of the data for 
 * displaying additional information to the theme user.
 *
 * @since 1.0
 * @access public
 * @link http://codex.wordpress.org/Theme_Review#Licensing
 * @param array $headers Array of extra headers added by plugins/themes.
 * @return array $headers
 */
function bon_extra_theme_headers( $headers ) {

	/* Add support for 'Template Version'. This is for use in child themes to note the version of the parent theme. */
	if ( !in_array( 'Template Version', $headers ) )
		$headers[] = 'Template Version';

	/* Add support for 'License'.  Proposed in the guidelines for the WordPress.org theme review. */
	if ( !in_array( 'License', $headers ) )
		$headers[] = 'License';

	/* Add support for 'License URI'. Proposed in the guidelines for the WordPress.org theme review. */
	if ( !in_array( 'License URI', $headers ) )
		$headers[] = 'License URI';

	/* Add support for 'Support URI'.  This should be a link to the theme's support forums. */
	if ( !in_array( 'Support URI', $headers ) )
		$headers[] = 'Support URI';

	/* Add support for 'Documentation URI'.  This should be a link to the theme's documentation. */
	if ( !in_array( 'Documentation URI', $headers ) )
		$headers[] = 'Documentation URI';

	/* Return the array of custom theme headers. */
	return $headers;
}


/**
 * Generates the relevant template info.  Adds template meta with theme version.  Uses the theme 
 * name and version from style.css.  In 0.6, added the bon_meta_template 
 * filter hook.
 *
 * @since 1.0
 * @access public
 * @return void
 */
function bon_meta_template() {
	$theme = wp_get_theme( get_template(), get_theme_root( get_template_directory() ) );
	$template = '<meta name="template" content="' . esc_attr( $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ) ) . '" />' . "\n";
	echo apply_atomic( 'meta_template', $template );
}

/**
 * Dynamic element to wrap the site title in.  If it is the front page, wrap it in an <h1> element.  One other 
 * pages, wrap it in a <div> element. 
 *
 * @since 1.0
 * @access public
 * @return void
 */
function bon_site_title() {

	/* If viewing the front page of the site, use an <h1> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h1' : 'div';

	/* Get the site title.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $title = get_bloginfo( 'name' ) )
		$title = sprintf( '<%1$s id="site-title"><a href="%2$s" title="%3$s" rel="home"><span>%4$s</span></a></%1$s>', tag_escape( $tag ), home_url(), esc_attr( $title ), $title );

	/* Display the site title and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_title', $title );
}

/**
 * Dynamic element to wrap the site description in.  If it is the front page, wrap it in an <h2> element.  
 * On other pages, wrap it in a <div> element.
 *
 * @since 1.0
 * @access public
 * @return void
 */
function bon_site_description() {

	/* If viewing the front page of the site, use an <h2> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h2' : 'div';

	/* Get the site description.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $desc = get_bloginfo( 'description' ) )
		$desc = sprintf( '<%1$s id="site-description"><span>%2$s</span></%1$s>', tag_escape( $tag ), $desc );

	/* Display the site description and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_description', $desc );
}

/**
 * Standardized function for outputting the footer content.
 *
 * @since 1.0
 * @access public
 * @return void
 */
function bon_footer_content() {

	/* Only run the code if the theme supports the Framework Core theme settings. */
	if ( current_theme_supports( 'bon-core-theme-settings' ) )
		echo apply_atomic_shortcode( 'footer_content', bon_get_setting( 'footer_insert' ) );
}

/**
 * Checks if a post of any post type has a custom template.  This is the equivalent of WordPress' 
 * is_page_template() function with the exception that it works for all post types.
 *
 * @since 1.0
 * @access public
 * @param string $template The name of the template to check for.
 * @return bool Whether the post has a template.
 */
function bon_has_post_template( $template = '' ) {

	/* Assume we're viewing a singular post. */
	if ( is_singular() ) {

		/* Get the queried object. */
		$post = get_queried_object();

		/* Get the post template, which is saved as metadata. */
		$post_template = get_post_meta( get_queried_object_id(), "_wp_{$post->post_type}_template", true );

		/* If a specific template was input, check that the post template matches. */
		if ( !empty( $template) && ( $template == $post_template ) )
			return true;

		/* If no specific template was input, check if the post has a template. */
		elseif ( empty( $template) && !empty( $post_template ) )
			return true;
	}

	/* Return false for everything else. */
	return false;
}

/**
 * Retrieves the file with the highest priority that exists.  The function searches both the stylesheet 
 * and template directories.  This function is similar to the locate_template() function in WordPress 
 * but returns the file name with the URI path instead of the directory path.
 *
 * @since 1.0
 * @access public
 * @link http://core.trac.wordpress.org/ticket/18302
 * @param array $file_names The files to search for.
 * @return string
 */
function bon_locate_theme_file( $file_names ) {

	$located = '';

	/* Loops through each of the given file names. */
	foreach ( (array) $file_names as $file ) {

		/* If the file exists in the stylesheet (child theme) directory. */
		if ( is_child_theme() && file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$located = trailingslashit( get_stylesheet_directory_uri() ) . $file;
			break;
		}

		/* If the file exists in the template (parent theme) directory. */
		elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$located = trailingslashit( get_template_directory_uri() ) . $file;
			break;
		}
	}

	return $located;
}


/**
 * @return array
 * @param string $post_type
 */
if( !function_exists('bon_get_post_id_lists') ) {
	function bon_get_post_id_lists( $post_type, $numberposts = 100 ){
		
		$posts = get_posts(array('post_type' => $post_type, 'numberposts'=> $numberposts));
		
		$posts_title = array();

		if(!empty($posts)) {
			foreach ($posts as $post) {
				$posts_title[$post->ID] = $post->post_title;
			}

		}
		return $posts_title;

	}
}

/**
 * Get post type terms
 * @return array
 * @param string $name
 * @param string $parent
 */
if( !function_exists('bon_get_categories') ) {
	function bon_get_categories( $name, $parent = '' ){
			
		if( empty($parent) ){ 
			$get_category = get_categories( array( 'taxonomy' => $name, 'hide_empty' => 0	));
			$category_list = array();
			$category_list['all'] = 'All';
			if( !empty($get_category) ){
				foreach( $get_category as $category ){
					$category_list[$category->slug] = $category->name;
				}
			}
				
			return $category_list;

		} else {
			$parent_id = get_term_by('slug', $parent, $category_name);
			$get_category = get_categories( array( 'taxonomy' => $name, 'child_of' => $parent_id->term_id, 'hide_empty' => 0	));
			$category_list = array( '0' => $parent );
			
			if( !empty($get_category) ){
				foreach( $get_category as $category ){
					$category_list[$category->slug] = $category->name;
				}
			}
				
			return $category_list;		
		}
	}
}

/**
 *  calculates a darker or lighter color variation of a color
 *  @param string $color hex color code
 *  @param string $opacity the opacity level ( default false )
 *  @return string returns the converted string
 */

if( !function_exists('bon_hex_to_rgba') ) {

	function bon_hex_to_rgba($color, $opacity = false) {

		/* Convert hexdec color string to rgb(a) string */

		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color))
	          return $default; 

		//Sanitize $color if "#" is provided 
	        if ($color[0] == '#' ) {
	        	$color = substr( $color, 1 );
	        }

	        //Check if color has 6 or 3 characters and get values
	        if (strlen($color) == 6) {
	                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	        } elseif ( strlen( $color ) == 3 ) {
	                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	        } else {
	                return $default;
	        }

	        //Convert hexadec to rgb
	        $rgb =  array_map('hexdec', $hex);

	        //Check if opacity is set(rgba or rgb)
	        if($opacity){
	        	if(abs($opacity) > 1)
	        		$opacity = 1.0;
	        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
	        } else {
	        	$output = 'rgb('.implode(",",$rgb).')';
	        }

	        //Return rgb(a) color string
	        return $output;
	}
}

/**
 *  calculates a darker or lighter color variation of a color
 *  @param string $color hex color code
 *  @param string $shade darker or lighter
 *  @param int $amount how much darker or lighter
 *  @return string returns the converted string
 */

if( !function_exists('bon_color_mod') ) {
	
 	function bon_color_mod($color, $shade, $amount) {
 		//remove # from the begiining if available and make sure that it gets appended again at the end if it was found
 		$newcolor = "";
 		$prepend = "";
 		if(strpos($color,'#') !== false) 
 		{ 
 			$prepend = "#";
 			$color = substr($color, 1, strlen($color)); 
 		}
 		
 		//iterate over each character and increment or decrement it based on the passed settings
 		$nr = 0;
		while (isset($color[$nr])) 
		{
			$char = strtolower($color[$nr]);
			
			for($i = $amount; $i > 0; $i--)
			{
				if($shade == 'lighter')
				{
					switch($char)
					{
						case 9: $char = 'a'; break;
						case 'f': $char = 'f'; break;
						default: $char++;
					}
				}
				else if($shade == 'darker')
				{
					switch($char)
					{
						case 'a': $char = '9'; break;
						case '0': $char = '0'; break;
						default: $char = chr(ord($char) - 1 );
					}
				}
			}
			$nr ++;
			$newcolor.= $char;
		}
 		
		$newcolor = $prepend.$newcolor;
		return $newcolor;
	}
}
?>