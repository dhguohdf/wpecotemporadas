<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly
/**
 * Shortocode Functions
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

/* Register shortcodes. */
add_action( 'init', 'bon_add_shortcodes' );

/**
 * Creates new shortcodes for use in any shortcode-ready area.  This function uses the add_shortcode() 
 * function to register new shortcodes with WordPress.
 *
 * @since 1.0
 * @access public
 * @uses add_shortcode() to create new shortcodes.
 * @link http://codex.wordpress.org/Shortcode_API
 * @return void
 */
function bon_add_shortcodes() {

	/* Add theme-specific shortcodes. */
	add_shortcode( 'the-year',      'bon_the_year_shortcode' );
	add_shortcode( 'site-link',     'bon_site_link_shortcode' );
	add_shortcode( 'wp-link',       'bon_wp_link_shortcode' );
	add_shortcode( 'theme-link',    'bon_theme_link_shortcode' );
	add_shortcode( 'child-link',    'bon_child_link_shortcode' );
	add_shortcode( 'loginout-link', 'bon_loginout_link_shortcode' );
	add_shortcode( 'query-counter', 'bon_query_counter_shortcode' );
	add_shortcode( 'nav-menu',      'bon_nav_menu_shortcode' );

	/* Add entry-specific shortcodes. */
	add_shortcode( 'entry-title',         'bon_entry_title_shortcode' );
	add_shortcode( 'entry-author',        'bon_entry_author_shortcode' );
	add_shortcode( 'entry-author-avatar', 'bon_entry_author_avatar_shortcode' );
	add_shortcode( 'entry-terms',         'bon_entry_terms_shortcode' );
	add_shortcode( 'entry-comments-link', 'bon_entry_comments_link_shortcode' );
	add_shortcode( 'entry-published',     'bon_entry_published_shortcode' );
	add_shortcode( 'entry-edit-link',     'bon_entry_edit_link_shortcode' );
	add_shortcode( 'entry-shortlink',     'bon_entry_shortlink_shortcode' );
	add_shortcode( 'entry-permalink',     'bon_entry_permalink_shortcode' );
	add_shortcode( 'entry-icon',          'bon_entry_icon_shortcode' );
	add_shortcode( 'post-format-link',    'bon_post_format_link_shortcode' );

	/* Add comment-specific shortcodes. */
	add_shortcode( 'comment-published',  'bon_comment_published_shortcode' );
	add_shortcode( 'comment-author',     'bon_comment_author_shortcode' );
	add_shortcode( 'comment-edit-link',  'bon_comment_edit_link_shortcode' );
	add_shortcode( 'comment-reply-link', 'bon_comment_reply_link_shortcode' );
	add_shortcode( 'comment-permalink',  'bon_comment_permalink_shortcode' );

	add_shortcode( 'gallery-carousel' , 'bon_gallery_carousel_shortcode' );
}

/**
 * Shortcode to display the current year.
 *
 * @since 1.0
 * @access public
 * @uses date() Gets the current year.
 * @return string
 */
function bon_the_year_shortcode() {
	return date( __( 'Y', 'bon' ) );
}

/**
 * Shortcode to display a link back to the site.
 *
 * @since 1.0
 * @access public
 * @uses get_bloginfo() Gets information about the install.
 * @return string
 */
function bon_site_link_shortcode() {
	return '<a class="site-link" href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home"><span>' . get_bloginfo( 'name' ) . '</span></a>';
}

/**
 * Shortcode to display a link to WordPress.org.
 *
 * @since 1.0
 * @access public
 * @return string
 */
function bon_wp_link_shortcode() {
	return '<a class="wp-link" href="http://wordpress.org" title="' . esc_attr__( 'State-of-the-art semantic personal publishing platform', 'bon' ) . '"><span>' . __( 'WordPress', 'bon' ) . '</span></a>';
}

/**
 * Shortcode to display a link to the parent theme page.
 *
 * @since 1.0
 * @access public
 * @uses get_theme_data() Gets theme (parent theme) information.
 * @return string
 */
function bon_theme_link_shortcode() {
	$theme = wp_get_theme( get_template(), get_theme_root( get_template_directory() ) );
	return '<a class="theme-link" href="' . esc_url( $theme->get( 'ThemeURI' ) ) . '" title="' . sprintf( esc_attr__( '%s WordPress Theme', 'bon' ), $theme->get( 'Name' ) ) . '"><span>' . esc_attr( $theme->get( 'Name' ) ) . '</span></a>';
}

/**
 * Shortcode to display a link to the child theme's page.
 *
 * @since 1.0
 * @access public
 * @uses get_theme_data() Gets theme (child theme) information.
 * @return string
 */
function bon_child_link_shortcode() {
	$theme = wp_get_theme( get_stylesheet(), get_theme_root( get_stylesheet_directory() ) );
	return '<a class="child-link" href="' . esc_url( $theme->get( 'ThemeURI' ) ) . '" title="' . esc_attr( $theme->get( 'Name' ) ) . '"><span>' . esc_html( $theme->get( 'Name' ) ) . '</span></a>';
}

/**
 * Shortcode to display a login link or logout link.
 *
 * @since 1.0
 * @access public
 * @uses is_user_logged_in() Checks if the current user is logged into the site.
 * @uses wp_logout_url() Creates a logout URL.
 * @uses wp_login_url() Creates a login URL.
 * @return string
 */
function bon_loginout_link_shortcode() {
	if ( is_user_logged_in() )
		$out = '<a class="logout-link" href="' . esc_url( wp_logout_url( site_url( $_SERVER['REQUEST_URI'] ) ) ) . '" title="' . esc_attr__( 'Deslogar', 'bon' ) . '">' . __( 'Log out', 'bon' ) . '</a>';
	else
		$out = '<a class="login-link" href="' . esc_url( wp_login_url( site_url( $_SERVER['REQUEST_URI'] ) ) ) . '" title="' . esc_attr__( 'Logar', 'bon' ) . '">' . __( 'Logar', 'bon' ) . '</a>';

	return $out;
}

/**
 * Displays query count and load time if the current user can edit themes.
 *
 * @since 1.0
 * @access public
 * @uses current_user_can() Checks if the current user can edit themes.
 * @return string
 */
function bon_query_counter_shortcode() {
	if ( current_user_can( 'edit_theme_options' ) )
		return sprintf( __( 'This page loaded in %1$s seconds with %2$s database queries.', 'bon' ), timer_stop( 0, 3 ), get_num_queries() );
	return '';
}

/**
 * Displays a nav menu that has been created from the Menus screen in the admin.
 *
 * @since 1.0
 * @access public
 * @uses wp_nav_menu() Displays the nav menu.
 * @return string
 */
function bon_nav_menu_shortcode( $attr ) {

	$attr = shortcode_atts(
		array(
			'menu'            => '',
			'container'       => 'div',
			'container_id'    => '',
			'container_class' => 'nav-menu',
			'menu_id'         => '',
			'menu_class'      => '',
			'link_before'     => '',
			'link_after'      => '',
			'before'          => '',
			'after'           => '',
			'fallback_cb'     => 'wp_page_menu',
			'walker'          => ''
		),
		$attr
	);
	$attr['echo'] = false;

	return wp_nav_menu( $attr );
}

/**
 * Displays the edit link for an individual post.
 *
 * @since 1.0
 * @access public
 * @param array $attr
 * @return string
 */
function bon_entry_edit_link_shortcode( $attr ) {

	$post_type = get_post_type_object( get_post_type() );

	if ( !current_user_can( $post_type->cap->edit_post, get_the_ID() ) )
		return '';

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );

	return $attr['before'] . '<span class="entry-edit-meta entry-post-meta"><a class="post-edit-link" href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '" title="' . sprintf( esc_attr__( 'Editar %1$s', 'bon' ), $post_type->labels->singular_name ) . '">' . __( 'Editar', 'bon' ) . '</a></span>' . $attr['after'];
}

/**
 * Displays the published date of an individual post.
 *
 * @since 1.0
 * @access public
 * @param array $attr
 * @return string
 */
function bon_entry_published_shortcode( $attr ) {
	$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('Postado em:','bon'), 'format' => get_option( 'date_format' ) ), $attr );

	$published = '<span class="entry-published-meta entry-post-meta"><strong class="published-text entry-meta-title">'.$attr['text'].'</strong> <abbr title="' . get_the_time( esc_attr__( 'l, F jS, Y, g:i a', 'bon' ) ) . '">' . get_the_time( $attr['format'] ) . '</abbr></span>';
	return $attr['before'] . $published . $attr['after'];
}

/**
 * Displays a post's number of comments wrapped in a link to the comments area.
 *
 * @since 1.0
 * @access public
 * @param array $attr
 * @return string
 */
function bon_entry_comments_link_shortcode( $attr ) {

	$comments_link = '';
	$number = doubleval( get_comments_number() );
	$attr = shortcode_atts( array( 'zero' => __( 'Comentário:', 'bon' ), 'one' => __( 'Comentário:', 'bon' ), 'more' => __( 'Comentários:', 'bon' ), 'css_class' => 'comments-link', 'none' => '', 'before' => '', 'after' => '' ), $attr );

	if ( 0 == $number && !comments_open() && !pings_open() ) {
		if ( $attr['none'] )
			$comments_link = '<span class="' . esc_attr( $attr['css_class'] ) . '">' . sprintf( $attr['none'], number_format_i18n( $number ) ) . '</span>';
	}
	elseif ( 0 == $number )
		$comments_link = '<span class="entry-comment-meta entry-post-meta"><strong class="comment-text entry-meta-title">'.$attr['zero'].'</strong> <a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_permalink() . '#respond" title="' . sprintf( esc_attr__( 'Comentar em %1$s', 'bon' ), the_title_attribute( 'echo=0' ) ) . '">' . number_format_i18n( $number ) . '</a></span>';
	elseif ( 1 == $number )
		$comments_link = '<span class="entry-comment-meta entry-post-meta"><strong class="comment-text entry-meta-title">'.$attr['one'].'</strong> <a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'Comentar em %1$s', 'bon' ), the_title_attribute( 'echo=0' ) ) . '">' . number_format_i18n( $number ) . '</a></span>';
	elseif ( 1 < $number )
		$comments_link = '<span class="entry-comment-meta entry-post-meta"><strong class="comment-text entry-meta-title">'.$attr['more'].'</strong> <a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'Comentar em %1$s', 'bon' ), the_title_attribute( 'echo=0' ) ) . '">' . number_format_i18n( $number ) . '</a></span>';

	if ( $comments_link )
		$comments_link = $attr['before'] . $comments_link . $attr['after'];

	return $comments_link;
}

/**
 * Displays an individual post's author with a link to his or her archive.
 *
 * @since 1.0
 * @access public
 * @param array $attr
 * @return string
 */
function bon_entry_author_shortcode( $attr ) {
	$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('Autor:','bon') ), $attr );
	$author = '<span class="entry-author-meta entry-post-meta"><strong class="author-text entry-meta-title">'.$attr['text'].'</strong> <a class="url fn n" rel="author" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
	return $attr['before'] . $author . $attr['after'];
}

/**
 * Displays an individual post's author with avatar.
 *
 * @since 1.0
 * @access public
 * @param array $attr
 * @return string
 */
function bon_entry_author_avatar_shortcode( $attr ) {
	$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('Sobre %1s','bon') ), $attr );
	$author = '<figure class="author-bio vcard clear">'.get_avatar(get_the_author_meta('ID')).'<figcaption><span class="author-link"><a class="url fn n" rel="author" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . sprintf( $attr['text'] , get_the_author_meta( 'display_name' )) . '</a></span>'. get_the_author_meta( 'description' ).'</figcaption></figure>';
	return $attr['before'] . $author . $attr['after'];
}


/**
 * Displays a list of terms for a specific taxonomy.
 *
 * @since 1.0
 * @access public
 * @param array $attr
 * @return string
 */
function bon_entry_terms_shortcode( $attr ) {
	$attr = shortcode_atts( array( 'text' => 'Categoria:', 'exclude_child' => false, 'id' => get_the_ID(), 'limit' => -1, 'taxonomy' => 'post_tag', 'separator' => ', ', 'before' => '', 'after' => '' ), $attr );
	$termlists = '';
	$the_terms = get_the_terms($attr['id'], $attr['taxonomy'] );
	$len = count($the_terms);
	$i = 1 ;
	if($the_terms) {
		foreach($the_terms as $term) {
			if($attr['exclude_child'] == true) {
				if($term->parent != 0) {
					continue;
				}
			}

			if($i > $attr['limit'] && $attr['limit'] != -1 ) {
				break;
			} 

			$termlists .= '<a ref="' . $term->taxonomy . '" href="'.  get_term_link( $term->slug, $term->taxonomy ) .'">'.$term->name.'</a>';
			$termlists .= ', ';
					
			$i++;
		}
	}
	

	if(substr($termlists, -2) == ', ') {
		$termlists = substr($termlists, 0 , -2);
	}
		

	$attr['before'] = ( empty( $attr['before'] ) ? '<span class="' . $attr['taxonomy'] . '">' : '<span class="' . $attr['taxonomy'] . '"><span class="before">' . $attr['before'] . '</span>' );
	$attr['after'] = ( empty( $attr['after'] ) ? '</span>' : '<span class="after">' . $attr['after'] . '</span></span>' );

	if(!empty($termlists)){
	return '<span class="entry-term-meta entry-post-meta">' . '<strong class="term-text entry-meta-title">' . $attr['text'] . '</strong> ' . $termlists . '</span>';
	} else {
		return '';
	}
}

/**
 * Displays a post's title with a link to the post.
 *
 * @since 1.0
 * @access public
 * @return string
 */
function bon_entry_title_shortcode( $attr ) {

	$attr = shortcode_atts(
		array( 
			'permalink' => true, 
			'tag'       => is_singular() ? 'h1' : 'h2' 
		), 
	$attr );

	$tag = tag_escape( $attr['tag'] );
	$class = sanitize_html_class( get_post_type() ) . '-title entry-title';

	if ( false == (bool)$attr['permalink'] )
		$title = the_title( "<{$tag} class='{$class}'>", "</{$tag}>", false );
	else
		$title = the_title( "<{$tag} class='{$class}'><a href='" . get_permalink() . "'>", "</a></{$tag}>", false );

	if ( empty( $title ) && !is_singular() )
		$title = "<{$tag} class='{$class}'><a href='" . get_permalink() . "'>" . __( '(Sem título)', 'bon' ) . "</a></{$tag}>";

	return $title;
}

/**
 * Displays the shortlink of an individual entry.
 *
 * @since 1.0
 * @access public
 * @return string
 */
function bon_entry_shortlink_shortcode( $attr ) {

	$attr = shortcode_atts(
		array(
			'text' => __( 'Link', 'bon' ),
			'title' => the_title_attribute( array( 'echo' => false ) ),
			'before' => '',
			'after' => ''
		),
		$attr
	);

	$shortlink = esc_url( wp_get_shortlink( get_the_ID() ) );

	return "{$attr['before']}<a class='shortlink' href='{$shortlink}' title='" . esc_attr( $attr['title'] ) . "' rel='shortlink'>{$attr['text']}</a>{$attr['after']}";
}

/**
 * Returns the output of the [entry-permalink] shortcode, which is a link back to the post permalink page.
 *
 * @since 1.0
 * @param array $attr The shortcode arguments.
 * @return string A permalink back to the post.
 */
function bon_entry_permalink_shortcode( $attr ) {

	$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => 'Read More', 'class' => '' ), $attr );

	return $attr['before'] . '<a href="' . esc_url( get_permalink() ) . '" class="permalink '.$attr['class'].'" title="'. the_title_attribute(array( 'before' => __('Link para ', 'bon'), 'echo' => 0)) .'">' . sprintf(__( '%s', 'bon' ), $attr['text'] ) . '</a>' . $attr['after'];
}

/**
 * Returns the output of the [post-format-link] shortcode.  This shortcode is for use when a theme uses the 
 * post formats feature.
 *
 * @since 1.0
 * @param array $attr The shortcode arguments.
 * @return string A link to the post format archive.
 */
function bon_post_format_link_shortcode( $attr ) {

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );
	$format = get_post_format();
	$url = ( empty( $format ) ? get_permalink() : get_post_format_link( $format ) );

	return $attr['before'] . '<a href="' . esc_url( $url ) . '" class="post-format-link">' . get_post_format_string( $format ) . '</a>' . $attr['after'];
}

/**
 * Displays the published date and time of an individual comment.
 *
 * @since 1.0
 * @access public
 * @return string
 */
function bon_comment_published_shortcode() {
	$link = '<span class="published">' . sprintf( __( '%1$s em %2$s', 'bon' ), '<abbr class="comment-date" title="' . get_comment_date( esc_attr__( 'l, F jS, Y, g:i a', 'bon' ) ) . '">' . get_comment_date() . '</abbr>', '<abbr class="comment-time" title="' . get_comment_date( esc_attr__( 'l, F jS, Y, g:i a', 'bon' ) ) . '">' . get_comment_time() . '</abbr>' ) . '</span>';
	return $link;
}

/**
 * Displays the comment author of an individual comment.
 *
 * @since 1.0
 * @access public
 * @global $comment The current comment's DB object.
 * @return string
 */
function bon_comment_author_shortcode( $attr ) {
	global $comment;

	$attr = shortcode_atts(
		array(
			'before' => '',
			'after' => '',
			'tag' => 'span' // @deprecated 1.2.0 Back-compatibility. Please don't use this argument.
		),
		$attr
	);

	$author = esc_html( get_comment_author( $comment->comment_ID ) );
	$url = esc_url( get_comment_author_url( $comment->comment_ID ) );

	/* Display link and cite if URL is set. Also, properly cites trackbacks/pingbacks. */
	if ( $url )
		$output = '<cite class="fn" title="' . $url . '"><a href="' . $url . '" title="' . esc_attr( $author ) . '" class="url" rel="external nofollow">' . $author . '</a></cite>';
	else
		$output = '<cite class="fn">' . $author . '</cite>';

	$output = '<' . tag_escape( $attr['tag'] ) . ' class="comment-author vcard">' . $attr['before'] . apply_filters( 'get_comment_author_link', $output ) . $attr['after'] . '</' . tag_escape( $attr['tag'] ) . '><!-- .comment-author .vcard -->';

	return $output;
}

/**
 * Displays the permalink to an individual comment.
 *
 * @since 1.0
 * @access public
 * @return string
 */
function bon_comment_permalink_shortcode( $attr ) {
	global $comment;

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );
	$link = '<a class="permalink" href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" title="' . sprintf( esc_attr__( 'Link para comentar em %1$s', 'bon' ), $comment->comment_ID ) . '">' . __( 'Link', 'bon' ) . '</a>';
	return $attr['before'] . $link . $attr['after'];
}

/**
 * Displays a comment's edit link to users that have the capability to edit the comment.
 *
 * @since 1.0
 * @access public
 * @return string
 */
function bon_comment_edit_link_shortcode( $attr ) {
	global $comment;

	$edit_link = get_edit_comment_link( $comment->comment_ID );

	if ( !$edit_link )
		return '';

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );

	$link = '<a class="comment-edit-link" href="' . esc_url( $edit_link ) . '" title="' . sprintf( esc_attr__( 'Editar %1$s', 'bon' ), $comment->comment_type ) . '"><span class="edit">' . __( 'Editar', 'bon' ) . '</span></a>';
	$link = apply_filters( 'edit_comment_link', $link, $comment->comment_ID );

	return $attr['before'] . $link . $attr['after'];
}

/**
 * Displays a reply link for the 'comment' comment_type if threaded comments are enabled.
 *
 * @since 1.0
 * @access public
 * @return string
 */
function bon_comment_reply_link_shortcode( $attr ) {

	if ( !get_option( 'thread_comments' ) || 'comment' !== get_comment_type() )
		return '';

	$defaults = array(
		'reply_text' => __( 'Responder', 'bon' ),
		'login_text' => __( 'Logue para responder.', 'bon' ),
		'depth' => intval( $GLOBALS['comment_depth'] ),
		'max_depth' => get_option( 'thread_comments_depth' ),
		'before' => '',
		'after' => ''
	);
	$attr = shortcode_atts( $defaults, $attr );

	return get_comment_reply_link( $attr );
}

/**
 * Displays post format icon.
 *
 * @since 1.0
 * @access public
 * @return string
 */
function bon_entry_icon_shortcode( $attr ) {

	$defaults = array(
		'before' => '',
		'after' => '',
		'class' => '',
	);

	$attr = shortcode_atts( $defaults, $attr );

	$format = get_post_format();

	$o = '<a class="entry-post-meta entry-icon-meta '.$attr['class'].'" href="'.get_permalink().'" title="'.the_title_attribute( array( 'before' => __('Link to ', 'bon'), 'echo' => 0) ).'">';

	switch ($format) {
		case 'link':
			$o .= '<i class="sha-link"></i>';
		break;

		case 'video':
			$o .= '<i class="awe-play"></i>';
		break;

		case 'gallery':
			$o .= '<i class="sha-polaroid"></i>';
		break;

		case 'image':
			$o .= '<i class="sha-camera-3"></i>';
		break;

		case 'audio':
			$o .= '<i class="sha-headset-2"></i>';
		break;

		case 'quote':
			$o .= '<i class="sha-double-quote"></i>';
		break;

		case 'chat':
			$o .= '<i class="sha-talk-bubble"></i>';
		break;

		case 'aside':
			$o .= '<i class="awe-pencil"></i>';
		break;
		
		default:
			$o .= '<i class="awe-file"></i>';
		break;
	}

	$o .= '</a>';

	return $attr['before'] . $o . $attr['after'];

}

function bon_gallery_carousel_shortcode( $attr ) {

	if( wp_script_is('bootstrap', 'queue') === false ) {
		wp_enqueue_style( 'gallery-carousel' );
	}

    $post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) )
			$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	}

	// Allow plugins/themes to override the default gallery template.
	$o = apply_filters('post_gallery_carousel', '', $attr);

	if ( $o != '' )
		return $o;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	
	$defaults = array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'size'       => 'large',
		'include'    => '',
		'exclude'    => ''
	);

	$attr = shortcode_atts( $defaults, $attr );

	extract($attr);

	$id = intval($id);

	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';


	$i = 0;
	$item = '';
	foreach ( $attachments as $id => $attachment ) {
		if ( ! empty( $attr['link'] ) && 'file' === $attr['link'] )
			$image_output = wp_get_attachment_link( $id, $size, false, false );
		elseif ( ! empty( $attr['link'] ) && 'none' === $attr['link'] )
			$image_output = wp_get_attachment_image( $id, $size, false );
		else
			$image_output = wp_get_attachment_link( $id, $size, true, false );

		$item .= '<div class="gallery-carousel-item item '. ($i == 0 ? 'active' : '') .'">';
		$item .= $image_output;

		if( !empty($attachment->post_excerpt) ) {
			$item .= '<div class="carousel-caption gallery-carousel-caption">';
			$item .= wptexturize( $attachment->post_excerpt );
			$item .= '</div>'; // close caption
		}
		
		$item .= '</div>'; // close gallery-carousel-item

		$i++;
	}
	

	$o .= '<div id="bon-gallery-carousel-'.$instance.'" class="bon-gallery-carousel carousel slide">';
    $o .= '<div class="carousel-inner">';

    $o .= $item;
   
    $o .= '</div>'; // close carousel inner
    $o .= '<a class="left carousel-control" href="#bon-gallery-carousel-'.$instance.'" data-slide="prev"><i class="awe-angle-left"></i></a>';
    $o .= '<a class="right carousel-control" href="#bon-gallery-carousel-'.$instance.'" data-slide="next"><i class="awe-angle-right"></i></a>';
    $o .= '</div>'; //close carousel slide

    return $o;
}
?>