<?php

if (self::has_listings()) {
    
?>
 

 <?php
    
    $parts      = array();
    $pagination = self::get_global('paging_control');

    if (is_array($pagination)) {
        echo '<div class="pagination-container pagination-centered"><ul class="pagination">';
        if ($total = self::idx('total')) {
            global $wp;

            $output = '';
            
            extract($pagination);
            
            // Otherwise, it strips the <0> from the query vars
            if (!empty($_GET) && is_array($_GET)) {
                foreach ($_GET as $key => $val) {
                    $get[urlencode($key)] = urlencode($val);
                }
            } else {
                $get = false;
            }
            
            
            $total_pages = ceil(self::idx('total') / self::get('results_per_page'));

            if($total_pages > 1) {
                $args = array(
                    'total' => $total_pages,
                    'current' => self::idx('page'),
                    'format' => 'page-%#%',
                    'base' => self::get_pagenum_link(),
                    'mid_size' => 3,
                    'end_size' => 2,
                    'prev_text' => __('Prev','bon'),
                    'next_text' => __('Next','bon'),
                    'add_args' => $get,
                    'type' => 'array',
                );
                
                $paginations = paginate_links($args);

                if($pagination) {
                    foreach($paginations as $pagination) {
                        $output .= '<li>' . $pagination . '</li>';
                    }
                }
            }
            
            echo $output;
            
        }
        echo '</ul></div>';
    } 
?>

 
 <?php
}
?>