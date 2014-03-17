<?php 
/*
* Template Name: Compare Listings
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
                    
                    $compare_id = '';

                    if($_GET['compare']) {
                        
                        $ex = explode(",", esc_html($_GET['compare']));
                        if( (isset($ex[0]) && is_numeric($ex[0])) && ( isset($ex[1]) && is_numeric($ex[1])) ) {
                            $compare_id = $ex;
                        }
                    }

                    $compare_args = array(
                                'post_type' => 'listing',
                                'posts_per_page' => 2,
                                'post__in' => $compare_id,                      
                    );
                    
                    // wp query
                    $wp_query = new WP_Query( $compare_args );
                    

                    ?>

                <?php if ( $wp_query->have_posts() ) : 

                    $output_price            = '';
                    $output_status           = '';
                    $output_bed              = '';
                    $output_bath             = ''; 
                    $output_garage           = '';
                    $output_basement         = '';
                    $output_furnish          = '';
                    $output_mortgage         = '';
                    $output_lotsize          = '';
                    $output_buildingsize     = '';
                    $output_date             = '';
                    $output_year             = '';
                    $output_floor            = '';
                    $output_totalroom        = '';
                    $output_type             = '';
                    $output_agent            = '';
                    $output_featured         = '';
                    $output_link             = '';
                    $output_title            = '';
                    $output_zip            = '';
                    $output_address            = '';

                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

                        <?php

                            $zip = shandora_get_meta($post->ID,'listing_zip');
                            $address = shandora_get_meta($post->ID,'listing_address');
                            $status = shandora_get_meta($post->ID, 'listing_status'); 
                            $bed = shandora_get_meta($post->ID, 'listing_bed');
                            $bath = shandora_get_meta($post->ID, 'listing_bath');
                            $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
                            $buildingsize = shandora_get_meta($post->ID, 'listing_buildingsize');
                            $furnish = shandora_get_meta($post->ID, 'listing_furnishing');
                            $mortgage = shandora_get_meta($post->ID, 'listing_mortgage');
                            $mortgage = ($mortgage == 'nomortgage') ? __('N/A','bon') : __('Available','bon');
                            $garage = shandora_get_meta($post->ID, 'listing_garage');
                            $basement = shandora_get_meta($post->ID,'listing_basement');
                            $date = shandora_get_meta($post->ID,'listing_dateavail');
                            $totalroom = shandora_get_meta($post->ID,'listing_totalroom');
                            $year = shandora_get_meta($post->ID,'listing_yearbuild');
                            $floor = shandora_get_meta($post->ID,'listing_floor');
                            $agent_ids = get_post_meta($post->ID,'shandora_listing_agentpointed', true);
                            $link = '<a class="button" href="'. get_permalink( $post->ID ) .'" title="'. get_the_title($post->ID ) . '">'.__('Ver Anúncio','bon'). '</a>';
                            $agent = '';
                            if(is_array($agent_ids)) {
                              $len = count($agent_ids);
                              $i = 1;
                              foreach($agent_ids as $agent_id ) {
                                if($len > 1 && $i < $len) {
                                  $agent .= '<a class="" href="'. get_permalink( $agent_id ) .'" title="'. get_the_title($agent_id) . '">'.get_the_title($agent_id). '</a>' . ', ';
                                  //$agent .= get_the_title($agent_id) . ', ';
                                } else {
                                  $agent .= '<a class="" href="'. get_permalink( $agent_id ) .'" title="'. get_the_title($agent_id) . '">'.get_the_title($agent_id). '</a>';
                                  //$agent .= get_the_title($agent_id);
                                }
                                $i++;
                              }
                            }
                            $sizemeasurement = bon_get_option('measurement');
                            $featured = '';

                            if ( current_theme_supports( 'get-the-image' ) ) $featured .= get_the_image( array( 'size' => 'listing_medium', 'echo' => false ) );

                            $terms = get_the_terms( get_the_ID(),"property-type" );
                            $type = '';

                            if ( $terms && ! is_wp_error( $terms ) ) 
                            {                                                                                                                  
                                   foreach ( $terms as $term )
                                   {    
                                        $type .= '<a class="property-type" href="' . get_term_link($term->slug, "property-type" ) .'">'.$term->name.'</a>';
                                        break; // to display only one property type
                                   }                                                                                                                                                                       
                            }
                            $output_zip .= '<td>' . $zip . '</td>';
                            $output_address .= '<td>' . $address . '</td>';
                            $output_price .= '<td>' . shandora_get_listing_price(false) . '</td>';
                            if($status != 'none') { 
                              $status_opt = shandora_get_search_option('status');
                              if(array_key_exists($status, $status_opt)) { 
                                $output_status .= '<td>' . $status_opt[$status] . '</td>';
                              } 
                            } else {
                              $output_status .= '<td>' . __('None','bon') . '</td>';
                            }
                            
                            $output_bed .= '<td>' . $bed . '</td>';
                            $output_bath .= '<td>' . $bath . '</td>';
                            $output_garage .= '<td>' . $garage . '</td>';
                            $output_basement .= '<td>' . $basement . '</td>';
                            $output_furnish .= '<td>' . $furnish . '</td>';
                            $output_mortgage .= '<td>' . $mortgage . '</td>';
                            $output_lotsize .= '<td>' . $lotsize . ' ' . $sizemeasurement . '</td>';
                            $output_buildingsize .= '<td>' . $buildingsize . ' ' . $sizemeasurement . '</td>';
                            $output_date .= '<td>' . $date . '</td>';
                            $output_year .= '<td>' . $year . '</td>';
                            $output_type .= '<td>' . $type . '</td>';
                            $output_floor .= '<td>' . $floor . '</td>';
                            $output_totalroom .= '<td>' . $totalroom . '</td>';
                            $output_agent .= '<td>' . $agent . '</td>';
                            $output_featured .= '<td>' . $featured . '</td>';
                            $output_link .= '<td>'.$link.'</td>';
                            $output_title .= '<td class="title">' . get_the_title( $post->ID ) . '</td>';
                        ?>

                    <?php endwhile; ?>

                    <table id="comparison-table">
                      <thead>
                        <tr>
                            <th></th>
                            <?php echo $output_featured; ?>
                        </tr>
                        <tr>
                            <th></th>
                            <?php echo $output_title; ?>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <th><?php _e('Endereço  ','bon'); ?></th>
                          <?php echo $output_address; ?>
                        </tr>
                        <tr>
                          <th><?php _e('CEP','bon'); ?></th>
                          <?php echo $output_zip; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Preço','bon'); ?></th>
                          <?php echo $output_price; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Mobiliado?','bon'); ?></th>
                          <?php echo $output_furnish; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Tipo','bon'); ?></th>
                          <?php echo $output_type; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Localidade','bon'); ?></th>
                          <?php echo $output_status; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Quartos','bon'); ?></th>
                          <?php echo $output_bed; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Banheiros','bon'); ?></th>
                          <?php echo $output_bath; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Garagens','bon'); ?></th>
                          <?php echo $output_garage; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Andares','bon'); ?></th>
                          <?php echo $output_floor; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Total de Cômodos','bon'); ?></th>
                          <?php echo $output_totalroom; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Tamanho do Lote','bon'); ?></th>
                          <?php echo $output_lotsize; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Tamanho da Construção','bon'); ?></th>
                          <?php echo $output_buildingsize; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Ano construído','bon'); ?></th>
                          <?php echo $output_year; ?>
                        </tr>
                        <tr>
                          <th><?php _e('Perfil ECO responsável','bon'); ?></th>
                          <?php echo $output_agent; ?>
                        </tr>

                      </tbody>
                      <tfoot>
                          <tr>
                          <th></th>
                          <?php echo $output_link; ?>
                          </tr>
                      </tfoot>
                    </table>

                <?php else : ?>

                    <?php bon_get_template_part( 'loop', 'error' ); // Loads the loop-error.php template. ?>

                <?php endif; ?>


                <?php wp_reset_query(); ?>

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
