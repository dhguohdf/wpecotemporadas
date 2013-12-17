<?php
if(!function_exists('shandora_add_shortcodes')) {

	function shandora_add_shortcodes() {
		/* Add theme-specific shortcodes. */
		add_shortcode( 'col-title', 'shandora_column_title_shortcode' );
		add_shortcode( 'post-carousel', 'shandora_post_carousel_shortcode' );
		add_shortcode( 'row', 'shandora_row_shortcode');
		add_shortcode( 'column', 'shandora_column_shortcode');
		add_shortcode( 'divider', 'shandora_divider_shortcode');
		add_shortcode( 'affix', 'shandora_affix_shortcode');
		add_shortcode( 'affix-item', 'shandora_affix_item_shortcode');
	}

	/* Register shortcodes. */
	add_action( 'init', 'shandora_add_shortcodes' );
}

add_filter( 'init', 'shandora_filter_process_shortcodes' ); 

function shandora_filter_process_shortcodes() {
    add_filter('the_content', 'shandora_process_shortcodes', 7);
}

function shandora_process_shortcodes($content) {
        global $shortcode_tags;
        
        // Backup current registered shortcodes and clear them all out
        $orig_shortcode_tags = $shortcode_tags;
        $shortcode_tags = array();
        remove_all_shortcodes();

        shandora_add_shortcodes();
     
        // Do the shortcode (only the one above is registered)
        $content = do_shortcode( $content );
     
        // Put the original shortcodes back
        $shortcode_tags = $orig_shortcode_tags;
     
        return $content;
    }

function shandora_divider_shortcode( $attr ) {
	$attr = shortcode_atts(
		array(
			'style' => 'bold',
			'text' => '',
		), $attr
	);

	
	$o = '';

	if($attr['style'] == 'bold' || $attr['style'] == 'light') {
		$class = ($attr['style'] == 'light') ? 'divider-2' : 'divider-1';
		$o = do_shortcode('[row][column]<hr class="'.$class.'" />[/column][/row]');
	} 
	else if($attr['style'] == 'hr-text' && $attr['text'] != '') {
			$o = do_shortcode('
			[row][column]
				<div class="hr hr-text">
					<div class="custom-hr-text">'.$attr['text'].'</div>
				</div>
			[/column][/row]');
	}
	

	return $o;

}

function shandora_row_shortcode( $attr, $content = null ) {

	$attr = shortcode_atts(
		array(
			'class' => '',
			'id' => '',
		), $attr
	);

	
	return '<div id="'.$attr['id'].'" class="row '.$attr['class'].'">'. do_shortcode( $content ) . '</div>';
}


function shandora_column_shortcode( $attr, $content = null ) {

	$attr = shortcode_atts(
		array(
			'class' => '',
			'id' => '',
			'size' => 'large-12',
		), $attr
	);

	return '<div class="' . shandora_column_class($attr['size']) . $attr['class'] . '">'. do_shortcode( $content ) . '</div>';
}


function shandora_post_carousel_shortcode( $attr ) {
	$attr = shortcode_atts(
		array(
			'title'            => '',
			'numberposts'	   => 4,
			'button_color'	   => 'red', 
		), $attr
	);
	$o = '';


	if($attr['title']) {
		$o .= apply_atomic_shortcode('col_title', '[col-title icon="sha-pencil" title="'.$attr['title'].'" nav_on=true]');
	}
	$o .= '<div class="post-carousel"><div class="post-carousel-control"></div><div class="post-carousel-slides row" data-index="0" data-num="1" data-width="600" data-max="'.$attr['numberposts'].'">';

     
    $loop = array(
				'post_type' => 'post',
				'posts_per_page' => $attr['numberposts'],
				'ignore_sticky_posts' => true
	        );
    query_posts($loop);
   
        
    if( have_posts() ) : while( have_posts() ) : the_post();

    	$temp_title = get_the_title();
	    $temp_link = get_permalink();
	    $temp_id = get_the_ID();

	    if( has_excerpt( $temp_id ) ) {
	    	$temp_ex = wpautop( wptexturize( wp_trim_words( get_the_excerpt(), 30, '' ) ) );
	    } else {
	    	$temp_ex = wpautop( wptexturize( wp_trim_words( strip_shortcodes( get_the_content( $temp_id ) ), 30, '' ) ) );
	    }
    	
    	$o .= '<article id="post-'.$temp_id.'" class="post-item entry column large-6"><div class="post-carousel-container clear"><div class="entry-block">';
        $o .= apply_atomic_shortcode( 'entry_title', the_title( '<h2>', '</h2>', false ) );

        $o .= '<div class="entry-summary hide-for-desktop">';

        $o .= $temp_ex;

        $o .= '</div>';
        
        $o .= apply_atomic_shortcode( 'entry-permalink', '[entry-permalink text="'.__('Read More','bon').'" class="button '.$attr['button_color'].' radius small flat"]');
        
        $o .= '</div><div class="image-block">';
        
        $o .= ( current_theme_supports( 'get-the-image' ) )  ? get_the_image( array( 'size' => 'blog_small', 'echo' => false ) ) : '';
        
        $o .= '</div></div></article>';
     
     endwhile; endif; wp_reset_query();

 		$o .= '</div></div>';


	return $o;
}
function shandora_column_title_shortcode ( $attr ) {
	$attr = shortcode_atts(
		array(
			'title'         => '',
			'icon'			=> '',
			'nav_on'		=> false,
		), $attr
	);

	$icon = '';
	$class = '';

	if($attr['icon']) {
		$icon = '<i class="'.$attr['icon'].'"></i>';
	}

	if($attr['nav_on']) {
		$class .= 'navigation-on';
	}

	return '<div class="column-header '.$class.'">'.$icon.'<h3 class="column-title">'.$attr['title'].'</h3><div class="column-divider"></div><div class="clear"></div></div>';
}

function shandora_affix_shortcode( $attr, $content = null ) {

	if( is_null($content) )
		return $content;

	static $affix;
	$affix++;

	wp_enqueue_script( 'scrollspy', trailingslashit( get_template_directory_uri() ) .'assets/js/libs/scrollspy.js', array('jquery'), '3.0', true );
	wp_enqueue_script( 'affix', trailingslashit( get_template_directory_uri() ) .'assets/js/libs/affix.js', array('jquery'), '3.0', true );

	if( isset($GLOBALS['affix_count']) )
            $GLOBALS['affix_count']++;
    else
        $GLOBALS['affix_count'] = 0;

	// Extract the tab titles for use in the tab widget.
    preg_match_all( '/affix-item title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );

    $items = array();
    if( isset($matches[1]) ){ 
        $items = $matches[1]; 
    }


    if( count($items) ){


    	$o = '<div class="row affix-container">';

        $o .= '<div class="column large-4 hide-for-small affix-nav-container"><ul data-spy="affix" class="nav nav-list affix-top affix-nav" id="affix-nav-' . $affix . '">';

        $i = 0;
		foreach( $items as $item ) {
			$class = '';
			if($i == 0 ) $class = 'active';

		    $o .= '<li class="'.$class.'"><a href="#'.sanitize_title( $item[0] ).'">'. esc_attr( $item[0] ) .'<i class="awe-chevron-right"></i></a></li>';
		    $i++;
		}

        $o .= '</ul></div>';

        $o .= '<div class="column large-8 small-12">';
        $o .= do_shortcode( $content );
        $o .= '</div>';
        $o .= '</div>';
        $o .= '<script>jQuery(document).ready(function($){
        	var $window = $(window);
        	  setTimeout(function () { 
        	  	$(\'#affix-nav-'.$affix.'\').affix({
        	  	offset: {
        	  		top: function() {
        	  			var $parent = $(\'#affix-nav-'.$affix.'\').parents(\'.entry-content\');
			        	var poff = $parent.offset();
			        	return poff.top;
        	  		},
        	  		bottom: function() {
        				return (this.bottom = $("#footer-container").outerHeight(true) + 700)
        	  		},
        	  	}
        	  })
			}, 100);
$(window).resize(function(){
var w = $(".affix-nav-container").width();
$(".affix-nav").css({ width : w  + "px" });
}).resize();
});
</script>';
    } else {

        $o .= do_shortcode( $content );

    }

    

	return $o;
}

function shandora_affix_item_shortcode( $attr, $content = null ) {

    $defaults = array( 'title' => 'Item' );
    extract( shortcode_atts( $defaults, $attr ) );

   	static $affix_count = 0;
   	

    
    $o = '<section id="'.sanitize_title( $title).'"><div class="row"><div class="large-12 columns">';
	$o .= '<h3>'. esc_attr( $title ) . '</h3>';
	$o .= do_shortcode( $content );
	$o .= '</div></div></section>';
	$affix_count++;
	return $o;
}
?>