<?php if ( have_posts() ) : $compare_page = bon_get_option('compare_page'); ?>

    <?php while ( have_posts() ) : the_post(); ?>

    	<?php
            $show_map = bon_get_option('show_listings_map');
            $sizemeasurement = bon_get_option('measurement');
            if(get_post_type() == 'listing' && !is_singular( 'listing' ) && $show_map == 'show') {

               $lat = shandora_get_meta($post->ID, 'listing_maplatitude');
               $long = shandora_get_meta($post->ID, 'listing_maplongitude');

               if(!empty($lat) && !empty($long)) {

					if (has_post_thumbnail( $post->ID ) ) :
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
						$image = $image[0];
					endif;

                    $bath = shandora_get_meta($post->ID, 'listing_bath');
                    $bed = shandora_get_meta($post->ID, 'listing_bed');

					$data_map[] = array(
					    'photo' => (!empty($image)) ? $image : '',
					    'baths' => (!empty($bath)) ? sprintf(_n('%s Bath','%s Baths', $bath, 'bon'), $bath) : __('0 Bath','bon'),
					    'beds' =>  (!empty($bed)) ? sprintf(_n('%s Bed','%s Beds', $bed, 'bon'), $bed) : __('0 Bed','bon'),
					    'lotsize' => (shandora_get_meta($post->ID, 'listing_lotsize') != '') ? shandora_get_meta($post->ID, 'listing_lotsize'). ' ' . $sizemeasurement : 'N\A',
					    'price' => shandora_get_listing_price(false),
					    'permalink' => get_permalink(),
					    'title' => get_the_title(),
					    'id' => get_the_ID(),
					    'latitude' => $lat,
					    'longitude' => $long,
					);
               }
            }

        ?>

        <?php bon_get_template_part( 'content', get_post_type() ); ?>

    <?php endwhile; 
        $count = $wp_query->found_posts;
        $content_count = sprintf(_n('%s Property Listed', '%s Properties Listed', $count, 'bon'), $count);
        $show_listing_count = bon_get_option('show_listing_count', 'no');
        if($content_count && $show_listing_count == 'yes') { ?>
             <script type="text/javascript">
                /* <![CDATA[ */
                var shandora_data_count = "<?php echo $content_count; ?>";
                /* ]]> */
                </script>
        <?php }

         if(!empty($data_map) && is_array($data_map)) { 
            $data_map = json_encode($data_map);?>
            <script type="text/javascript">
            /* <![CDATA[ */
            var shandora_data = {'results': <?php echo $data_map; ?>};
            /* ]]> */
            </script>
            <?php
        }

    	

    ?>

<?php else : ?>

    <?php bon_get_template_part( 'loop', 'error' ); // Loads the loop-error.php template. ?>

<?php endif; ?>