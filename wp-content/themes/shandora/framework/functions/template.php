<?php
/**
* Outputs the site title.
*
* @since 0.1.0
* @access public
* @return void
*/
function bon_site_title() {
        echo bon_get_site_title();
}

/**
* Returns the linked site title wrapped in an `<h2>` tag.
*
* @since 1.2.0
* @access public
* @return string
*/
function bon_get_site_title() {

        if ( $title = get_bloginfo( 'name' ) )
                $title = sprintf( '<h1 %s><a href="%s" rel="home">%s</a></h1>', bon_get_attr( 'site-title' ), home_url(), $title );

        return apply_filters( 'bon_site_title', $title );
}


/**
* Outputs the site description.
*
* @since 0.1.0
* @access public
* @return void
*/
function bon_site_description() {
        echo bon_get_site_description();
}

/**
* Returns the site description wrapped in an `<h2>` tag.
*
* @since 1.2.0
* @access public
* @return string
*/
function bon_get_site_description() {

        if ( $desc = get_bloginfo( 'description' ) )
                $desc = sprintf( '<h2 %s>%s</h2>', bon_get_attr( 'site-description' ), $desc );

        return apply_filters( 'bon_site_description', $desc );
}

/**
* Outputs the loop title.
*
* @since 1.2.0
* @access public
* @return void
*/
function bon_loop_title() {
        echo bon_get_loop_title();
}

/**
* Gets the loop title. This function should only be used on archive-type pages, such as archive, blog, and
* search results pages. It outputs the title of the page.
*
* @link http://core.trac.wordpress.org/ticket/21995
* @since 1.2.0
* @access public
* @return string
*/
function bon_get_loop_title() {

        $loop_title = '';

        if ( is_home() && !is_front_page() )
                $loop_title = get_post_field( 'post_title', get_queried_object_id() );

        elseif ( is_category() )
                $loop_title = single_cat_title( '', false );

        elseif ( is_tag() )
                $loop_title = single_tag_title( '', false );

        elseif ( is_tax() )
                $loop_title = single_term_title( '', false );

        elseif ( is_author() )
                $loop_title = get_the_author();

        elseif ( is_search() )
                $loop_title = bon_search_title( '', false );

        elseif ( is_post_type_archive() )
                $loop_title = post_type_archive_title( '', false );

        elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
                $loop_title = bon_single_minute_hour_title( '', false );

        elseif ( get_query_var( 'minute' ) )
                $loop_title = bon_single_minute_title( '', false );

        elseif ( get_query_var( 'hour' ) )
                $loop_title = bon_single_hour_title( '', false );

        elseif ( is_day() )
                $loop_title = bon_single_day_title( '', false );

        elseif ( get_query_var( 'w' ) )
                $loop_title = bon_single_week_title( '', false );

        elseif ( is_month() )
                $loop_title = single_month_title( ' ', false );

        elseif ( is_year() )
                $loop_title = bon_single_year_title( '', false );

        elseif ( is_archive() )
                $loop_title = bon_single_archive_title( '', false );

        return apply_filters( 'bon_loop_title', $loop_title );
}

/**
* Outputs the loop description.
*
* @since 1.2.0
* @access public
* @return void
*/
function bon_loop_description() {
        echo bon_get_loop_description();
}

/**
* Gets the loop description. This function should only be used on archive-type pages, such as archive, blog, and
* search results pages. It outputs the description of the page.
*
* @link http://core.trac.wordpress.org/ticket/21995
* @since 1.2.0
* @access public
* @return string
*/
function bon_get_loop_description() {

        $loop_desc = '';

        if ( is_home() && !is_front_page() )
                $loop_desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );

        elseif ( is_category() )
                $loop_desc = get_term_field( 'description', get_queried_object_id(), 'category', 'raw' );

        elseif ( is_tag() )
                $loop_desc = get_term_field( 'description', get_queried_object_id(), 'post_tag', 'raw' );

        elseif ( is_tax() )
                $loop_desc = get_term_field( 'description', get_queried_object_id(), get_query_var( 'taxonomy' ), 'raw' );

        elseif ( is_author() )
                $loop_desc = get_the_author_meta( 'description', get_query_var( 'author' ) );

        elseif ( is_search() )
                $loop_desc = sprintf( __( 'You are browsing the search results for &#8220;%s&#8221;', 'bon' ), get_search_query() );

        elseif ( is_post_type_archive() )
                $loop_desc = get_post_type_object( get_query_var( 'post_type' ) )->description;

        elseif ( is_time() )
                $loop_desc = __( 'You are browsing the site archives by time.', 'bon' );

        elseif ( is_day() )
                $loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'bon' ), bon_single_day_title( '', false ) );

        elseif ( is_month() )
                $loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'bon' ), single_month_title( ' ', false ) );

        elseif ( is_year() )
                $loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'bon' ), bon_single_year_title( '', false ) );

        elseif ( is_archive() )
                $loop_desc = __( 'You are browsing the site archives.', 'bon' );

        return apply_filters( 'bon_loop_description', $loop_desc );
}

/**
* Retrieve the general archive title.
*
* @since 1.2.0
* @access public
* @param string $prefix
* @param bool $display
* @return string
*/
function bon_single_archive_title( $prefix = '', $display = true ) {

        $title = $prefix . __( 'Archives', 'bon' );

        if ( false === $display )
                return $title;

        echo $title;
}

/**
* Retrieve the year archive title.
*
* @since 1.2.0
* @access public
* @param string $prefix
* @param bool $display
* @return string
*/
function bon_single_year_title( $prefix = '', $display = true ) {

        $title = $prefix . get_the_date( _x( 'Y', 'yearly archives date format', 'bon' ) );

        if ( false === $display )
                return $title;

        echo $title;
}

/**
* Retrieve the week archive title.
*
* @since 1.2.0
* @access public
* @param string $prefix
* @param bool $display
* @return string
*/
function bon_single_week_title( $prefix = '', $display = true ) {

        /* Translators: 1 is the week number and 2 is the year. */
        $title = $prefix . sprintf( __( 'Week %1$s of %2$s', 'bon' ), get_the_time( _x( 'W', 'weekly archives date format', 'bon' ) ), get_the_time( _x( 'Y', 'yearly archives date format', 'bon' ) ) );

        if ( false === $display )
                return $title;

        echo $title;
}

/**
* Retrieve the day archive title.
*
* @since 1.2.0
* @access public
* @param string $prefix
* @param bool $display
* @return string
*/
function bon_single_day_title( $prefix = '', $display = true ) {

        $title = $prefix . get_the_date( _x( 'F j, Y', 'daily archives date format', 'bon' ) );

        if ( false === $display )
                return $title;

        echo $title;
}

/**
* Retrieve the hour archive title.
*
* @since 1.2.0
* @access public
* @param string $prefix
* @param bool $display
* @return string
*/
function bon_single_hour_title( $prefix = '', $display = true ) {

        $title = $prefix . get_the_time( _x( 'g a', 'hour archives time format', 'bon' ) );

        if ( false === $display )
                return $title;

        echo $title;
}

/**
* Retrieve the minute archive title.
*
* @since 1.2.0
* @access public
* @param string $prefix
* @param bool $display
* @return string
*/
function bon_single_minute_title( $prefix = '', $display = true ) {

        /* Translators: Minute archive title. %s is the minute time format. */
        $title = $prefix . sprintf( __( 'Minute %s', 'bon' ), get_the_time( _x( 'i', 'minute archives time format', 'bon' ) ) );

        if ( false === $display )
                return $title;

        echo $title;
}

/**
* Retrieve the minute + hour archive title.
*
* @since 1.2.0
* @access public
* @param string $prefix
* @param bool $display
* @return string
*/
function bon_single_minute_hour_title( $prefix = '', $display = true ) {

        $title = $prefix . get_the_time( _x( 'g:i a', 'minute and hour archives time format', 'bon' ) );

        if ( false === $display )
                return $title;

        echo $title;
}

/**
* Retrieve the search results title.
*
* @since 1.2.0
* @access public
* @param string $prefix
* @param bool $display
* @return string
*/
function bon_search_title( $prefix = '', $display = true ) {

        /* Translators: %s is the search query. The HTML entities are opening and closing curly quotes. */
        $title = $prefix . sprintf( __( 'Search results for &#8220;%s&#8221;', 'bon' ), get_search_query() );

        if ( false === $display )
                return $title;

        echo $title;
}

/**
* Retrieve the 404 page title.
*
* @since 1.2.0
* @access public
* @param string $prefix
* @param bool $display
* @return string
*/
function bon_404_title( $prefix = '', $display = true ) {

        $title = __( '404 Not Found', 'bon' );

        if ( false === $display )
                return $title;

        echo $title;
}