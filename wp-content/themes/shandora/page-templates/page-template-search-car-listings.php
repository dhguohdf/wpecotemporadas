<?php 
/*
* Template Name: Search Car Listings
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
                    if(isset($_GET['body_type']) && !empty($_GET['body_type']) )
                    {
                        $body_type = $_GET['body_type'];
                        if( $body_type != 'any' )
                        {                               
                            $tax_query[] = array(
                                                'taxonomy' => 'body-type',
                                                'field' => 'slug',
                                                'terms' => $body_type
                                            );
                        }
                    }


                    
                    // if city is set add it to taxonomy query
                    if(isset($_GET['dealer_location']) && !empty($_GET['dealer_location']))
                    {
                        $location = $_GET['dealer_location'];  
                        if( $location != 'any' )
                        {                           
                            $tax_query[] = array(
                                            'taxonomy' => 'dealer-location',
                                            'field' => 'slug',
                                            'terms' => $location
                                        );
                        }
                    }

                    // if city is set add it to taxonomy query
                    if(isset($_GET['dealer_location_level1']) && !empty($_GET['dealer_location_level1']))
                    {

                        if(isset($_GET['dealer_location_level2']) && !empty($_GET['dealer_location_level2']) && $_GET['dealer_location_level2'] != 'any') {

                            if(isset($_GET['dealer_location_level3']) && !empty($_GET['dealer_location_level3']) && $_GET['dealer_location_level3'] != 'any') {
                                  $tax_query[] = array(
                                    'taxonomy' => 'dealer-location',
                                    'field' => 'slug',
                                    'terms' => $_GET['dealer_location_level3']
                                );
                                
                            } else {
                       
                                $tax_query[] = array(
                                    'taxonomy' => 'dealer-location',
                                    'field' => 'slug',
                                    'terms' => $_GET['dealer_location_level2']
                                );
                                
                            }

                        } else {
                            $location = $_GET['dealer_location_level1'];  
                            if( $location != 'any' )
                            {                           
                                $tax_query[] = array(
                                                'taxonomy' => 'dealer-location',
                                                'field' => 'slug',
                                                'terms' => $location
                                            );
                            }
                        }
                       
                    }

                    // if city is set add it to taxonomy query
                    if(isset($_GET['manufacturer']) && !empty($_GET['manufacturer']))
                    {
                        $manufacturer = $_GET['manufacturer'];  
                        if( $manufacturer != 'any' )
                        {                           
                            $tax_query[] = array(
                                            'taxonomy' => 'manufacturer',
                                            'field' => 'slug',
                                            'terms' => $manufacturer
                                        );
                        }
                    }

                    // if city is set add it to taxonomy query
                    if(isset($_GET['manufacturer_level1']) && !empty($_GET['manufacturer_level1']))
                    {

                        if(isset($_GET['manufacturer_level2']) && !empty($_GET['manufacturer_level2']) && $_GET['manufacturer_level2'] != 'any') {

                            if(isset($_GET['manufacturer_level3']) && !empty($_GET['manufacturer_level3']) && $_GET['manufacturer_level3'] != 'any') {
                                  $tax_query[] = array(
                                    'taxonomy' => 'manufacturer',
                                    'field' => 'slug',
                                    'terms' => $_GET['manufacturer_level3']
                                );
                                
                            } else {
                       
                      