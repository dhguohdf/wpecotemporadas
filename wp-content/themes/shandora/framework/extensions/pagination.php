<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Pagination Extensions
 *
 *
 * @author      Hermanto Lim
 * @copyright   Copyright (c) Hermanto Lim
 * @link        http://bonfirelab.com
 * @since       Version 1.0
 * @package     BonFramework
 * @category    Extensions
 *
 *
*/ 

// Numeric Page Navi (built into the theme by default)
function bon_page_navi($args = array()) {
    global $wpdb, $wp_query;

    $defaults = array(
        'before' => '',
        'after' => '',
        'disabled_class' => 'disabled',
        'current_class' => 'active',
        'first_class' => '',
        'last_class' => '',
        'container_class' => '',
    );

    $args = wp_parse_args( $args, $defaults );

    extract($args);

    $output = '';

    $request = $wp_query->request;

    $posts_per_page = intval(get_query_var('posts_per_page'));

    $paged = intval(get_query_var('paged'));

    $numposts = $wp_query->found_posts;

    $max_page = $wp_query->max_num_pages;

    if ( $numposts <= $posts_per_page ) { return; }
    if(empty($paged) || $paged == 0) {
        $paged = 1;
    }

    $pages_to_show = 4;

    $pages_to_show_minus_1 = $pages_to_show-1;

    $half_page_start = floor($pages_to_show_minus_1/2);

    $half_page_end = ceil($pages_to_show_minus_1/2);

    $start_page = $paged - $half_page_start;

    if($start_page <= 0) {
        $start_page = 1;
    }
    
    $end_page = $paged + $half_page_end;
    if(($end_page - $start_page) != $pages_to_show_minus_1) {
        $end_page = $start_page + $pages_to_show_minus_1;
    }
    if($end_page > $max_page) {
        $start_page = $max_page - $pages_to_show_minus_1;
        $end_page = $max_page;
    }
    if($start_page <= 0) {
        $start_page = 1;
    }
                        
    $output .= $before.'<div class="pagination-container '.$container_class.'"><ul class="pagination">'."";
    if ($start_page >= 2 && $pages_to_show < $max_page) {
        $first_page_text = __('First','bon');
        $output .= '<li class="bon-first-page-link '.$first_class.'"><a href="'.get_pagenum_link().'" title="'.$first_page_text.'"><i class="awe-angle-double-left"></i></a></li>';
    }

    if(get_previous_posts_link()) {
        $output .= '<li class="bon-prev-link">';
        $output .= get_previous_posts_link(__('Previous','bon'));
        $output .= '</li>';
    }
    else {
        $output .= '<li class="'.$disabled_class.'"><a href="#">' . __('Previous', 'bon') . '</a>';
    }
   
    for($i = $start_page; $i  <= $end_page; $i++) {
        if($i == $paged) {
            $output .= '<li class="page-link-number '.$current_class.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
        } else {
            $output .= '<li class="page-link-number"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
        }
    }

    if(get_next_posts_link())  {
        $output .= '<li class="bon-next-link">';
        $output .= get_next_posts_link(__('Next','bon'));
        $output .= '</li>';
    }
    else {
        $output .= '<li class="'.$disabled_class.'"><a href="#">' . __('Next', 'bon') . '</a>';
    }

    if ($end_page < $max_page) {
        $last_page_text = __('Last','bon');
        $output .= '<li class="bon-last-page-link '.$last_class.'"><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'"><i class="awe-angle-double-right"></i></a></li>';
    }
    $output .= '</ul></div>'.$after."";

    return $output;
} /* end page navi */


function bon_pagination($args = '', $echo = true) {
    if($echo) {
        echo bon_page_navi($args);
    } else {
        return bon_page_navi($args);
    }
}
?>