<?php 
/*
* Template Name: Search Listings
*/
get_header(); 
        
?>
<div id="inner-wrap" class="slide ">

    <div id="body-container" class="container">

        <?php 

        /**
         * Shandora Before Loop Hook
         *
         * @hooked shandora_get_page_header - 1
         * @hooked shandora_search_get_listing - 2
         * @hooked shandora_open_main_content_row - 5
         * @hooked shandora_get_left_sidebar - 10
         * @hooked shandora_open_main_content_column - 15
         *
         */

        do_atomic('before_loop'); ?>


                <?php 


                    $prefix = bon_get_prefix();    
                    // taxonomy query and meta query arrays
                    $tax_query = array();
                    $meta_query = array();
                    
                    // if property-type is set add it to taxonomy query
                    if(isset($_GET['property_type']) && !empty($_GET['property_type']) )
                    {
                        $property_type = $_GET['property_type'];
                        if( $property_type != 'any' )
                        {                               
                            $tax_query[] = array(
                                                'taxonomy' => 'property-type',
                                                'field' => 'slug',
                                                'terms' => $property_type
                                            );
                        }
                    }


                    
                    // if city is set add it to taxonomy query
                    if(isset($_GET['property_location']) && !empty($_GET['property_location']))
                    {
                        $location = $_GET['property_location'];  
                        if( $location != 'any' )
                        {                           
                            $tax_query[] = array(
                                            'taxonomy' => 'property-location',
                                            'field' => 'slug',
                                            'terms' => $location
                                        );
                        }
                    }

                     // if city is set add it to taxonomy query
                    if(isset($_GET['property_location_level1']) && !empty($_GET['property_location_level1']))
                    {

                        if(isset($_GET['property_location_level2']) && !empty($_GET['property_location_level2']) && $_GET['property_location_level2'] != 'any') {

                            if(isset($_GET['property_location_level3']) && !empty($_GET['property_location_level3']) && $_GET['property_location_level3'] != 'any') {
                                  $tax_query[] = array(
                                    'taxonomy' => 'property-location',
                                    'field' => 'slug',
                                    'terms' => $_GET['property_location_level3']
                                );
                                
                            } else {
                       
                                $tax_query[] = array(
                                    'taxonomy' => 'property-location',
                                    'field' => 'slug',
                                    'terms' => $_GET['property_location_level2']
                                );
                                
                            }

                        } else {
                            $location = $_GET['property_location_level1'];  
                            if( $location != 'any' )
                            {                           
                                $tax_query[] = array(
                                                'taxonomy' => 'property-location',
                                                'field' => 'slug',
                                                'terms' => $location
                                            );
                            }
                        }
                       
                    }

                    if(isset($_GET['property_feature']) && !empty($_GET['property_feature']))
                    {
                        $feature = $_GET['property_feature'];  
                        if( $feature != 'any' )
                        {                           
                            $tax_query[] = array(
                                            'taxonomy' => 'property-feature',
                                            'field' => 'slug',
                                            'terms' => $feature
                                        );
                        }
                    }
                    
                    // if beds are set add it to meta query
                    if(isset($_GET['property_bed']) && !empty($_GET['property_bed']) )
                    {
                        $beds = $_GET['property_bed'];
                        if( $beds != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => 'shandora_listing_bed',
                                'value' => $beds,
                                'compare' => '=',
                                'type'=> 'NUMERIC'
                            );
                        }
                    }

                    // if baths are set and not any then add it to meta query
                    if(isset($_GET['property_bath']) && !empty($_GET['property_bath']) )
                    {
                        $baths = $_GET['property_bath'];
                        if( $baths != 'any' )
                        {                               
                                $meta_query[] = array(
                                                'key' => 'shandora_listing_bath',
                                                'value' => $baths,
                                                'compare' => '=',
                                                'type'=> 'NUMERIC'
                                            );
                        }
                    }
                    
                    // if baths are set and not any then add it to meta query
                    if(isset($_GET['property_basement']) && !empty($_GET['property_basement']) )
                    {
                        $basement = $_GET['property_basement'];
                        if( $basement != 'any' )
                        {                               
                                $meta_query[] = array(
                                                'key' => 'shandora_listing_basement',
                                                'value' => $basement,
                                                'compare' => '=',
                                                'type'=> 'NUMERIC'
                                            );
                        }
                    }

                     // if baths are set and not any then add it to meta query
                    if(isset($_GET['property_garage']) && !empty($_GET['property_garage']) )
                    {
                        $garage = $_GET['property_garage'];
                        if( $garage != 'any' )
                        {                               
                                $meta_query[] = array(
                                                'key' => 'shandora_listing_garage',
                                                'value' => $garage,
                                                'compare' => '=',
                                                'type'=> 'NUMERIC'
                                            );
                        }
                    }

                     // if baths are set and not any then add it to meta query
                    if(isset($_GET['property_floor']) && !empty($_GET['property_floor']) )
                    {
                        $floor = $_GET['property_floor'];
                        if( $floor != 'any' )
                        {                               
                                $meta_query[] = array(
                                                'key' => 'shandora_listing_floor',
                                                'value' => $floor,
                                                'compare' => '=',
                                                'type'=> 'NUMERIC'
                                            );
                        }
                    }

                    if(isset($_GET['property_status']) && !empty($_GET['property_status']) )
                    {
                        $status = $_GET['property_status'];
                        if( $status != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => 'shandora_listing_status',
                                'value' => $status,
                                'compare' => '=',
                            );
                        }
                    }

                    if(isset($_GET['property_zip']) && !empty($_GET['property_zip']) )
                    {
                        $zip = $_GET['property_zip'];
                        if( $zip != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => 'shandora_listing_zip',
                                'value' => $zip,
                                'compare' => '=',
                            );
                        }
                    }

                    if(isset($_GET['property_mls']) && !empty($_GET['property_mls']) )
                    {
                        $mls = $_GET['property_mls'];
                        if( $mls != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => 'shandora_listing_mls',
                                'value' => $mls,
                                'compare' => '=',
                            );
                        }
                    }

                    if(isset($_GET['property_mortgage']) && !empty($_GET['property_mortgage']) )
                    {
                        $mortgage = $_GET['property_mortgage'];
                        if( $mortgage != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => 'shandora_listing_mortgage',
                                'value' => $mortgage,
                                'compare' => '=',
                            );
                        }
                    }

                    if(isset($_GET['property_agent']) && !empty($_GET['property_agent']) )
                    {
                        $agent = $_GET['property_agent'];
                        if( $agent != 'any' )
                        {                               
                            $meta_query[] = array(
                                'key' => 'shandora_listing_agentpointed',
                                'value' => serialize(array(strval($agent))),
                                'compare' => '=',
                            );
                        }
                    }


                    // if both of the min and max prices are specified then add them to meta query
                    if(isset($_GET['min_price']) && isset($_GET['max_price']) )
                    {

                        $min_price = intval($_GET['min_price']);
                        $max_price = intval($_GET['max_price']);
                        $the_max = intval(bon_get_option('price_range_max'));
                        //ignore max price
                        if( $min_price >= 0 && $max_price == $the_max ) {
                            $meta_query[] = array(
                                    'key' => 'shandora_listing_price',
                                    'value' => $min_price,
                                    'type' => 'NUMERIC',
                                    'compare' => '>='
                                );
                        }
                        
                        else if( $min_price >= 0 && $max_price > $min_price )
                        {                               
                            $meta_query[] = array(
                                    'key' => 'shandora_listing_price',
                                    'value' => array( $min_price, $max_price ),
                                    'type' => 'NUMERIC',
                                    'compare' => 'BETWEEN'
                                );
                        } 
                    }

                     // if both of the min and max prices are specified then add them to meta query
                    if(isset($_GET['min_lotsize']) && isset($_GET['max_lotsize']) )
                    {

                        $min = intval($_GET['min_lotsize']);
                        $max = intval($_GET['max_lotsize']);

                        if( $min >= 0 && $max > $min )
                        {                               
                            $meta_query[] = array(
                                                'key' => 'shandora_listing_lotsize',
                                                'value' => array( $min, $max ),
                                                'type' => 'NUMERIC',
                                                'compare' => 'BETWEEN'
                                            );
                        }
                    }

                    // if both of the min and max prices are specified then add them to meta query
                    if(isset($_GET['min_buildingsize']) && isset($_GET['max_buildingsize']) )
                    {

                        $min = intval($_GET['min_buildingsize']);
                        $max = intval($_GET['max_buildingsize']);
                        if( $min >= 0 && $max > $min )
                        {                               
                            $meta_query[] = array(
                                                'key' => 'shandora_listing_buildingsize',
                                                'value' => array( $min, $max ),
                                                'type' => 'NUMERIC',
                                                'compare' => 'BETWEEN'
                                            );
                        }
                    }
                                                
                    
                    // if two taxonomies exist then specify the relation
                    $tax_count = count($tax_query);
                    if($tax_count > 1)
                    {               
                        $tax_query['relation'] = 'AND';
                    }
                    
                    $meta_count = count($meta_query);
                    if($meta_count > 1)
                    {               
                        $meta_query['relation'] = 'AND';
                    }
                    
                    $numberposts = (bon_get_option('listing_per_page')) ? bon_get_option('listing_per_page') : 8;
                    
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                    $search_args = array(
                                'post_type' => 'listing',
                                'posts_per_page' => $numberposts,
                                'paged' => $paged                          
                    );
                    
                    if($tax_count > 0)
                    {
                        $search_args['tax_query'] = $tax_query;
                    }
                    
                    if($meta_count > 0)
                    {
                        $search_args['meta_query'] = $meta_query;
                    }

                    $orderby = '';
                    $key = '';
                    if(isset($_GET['search_orderby'])) {
                        $orderby = $_GET['search_orderby'];
                    }
                    $order = 'DESC';
                    if(isset($_GET['search_order'])) {
                        $order = $_GET['search_order'];
                    }
                    if($orderby == 'price') {
                        $key = 'shandora_listing_price';
                        $orderby = 'meta_value_num';
                    }
                    $search_args['meta_key'] = $key;
                    $search_args['orderby'] = $orderby;
                    $search_args['order'] = $order;
                    // wp query
                    $wp_query = new WP_Query( $search_args );

                    ?>

                <?php bon_get_template_part('loop', 'listing'); ?>

                <?php bon_get_template_part( 'loop','nav' ); // Loads the loop-nav.php template. ?>
            <?php 

            /**
             * Shandora After Loop Hook
             *
             * @hooked shandora_close_main_content_column - 1
             * @hooked shandora_get_right_sidebar - 5
             * @hooked shandora_close_main_content_row - 10
             *
             */

            do_atomic('after_loop'); ?>

    </div>


<?php get_footer(); ?>
