<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Archive
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Widgets
 *
 *
*/ 

/**
 * Archives widget class.
 *
 * @since 1.0
 */
class Shandora_Related_Listing_Widget extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'related-listing',
			'description' => esc_html__( 'Show related property listing.', 'bon' )
		);

		/* Set up the widget control options. */
		$control_options = array(
		);

		/* Create the widget. */
		$this->WP_Widget(
			'shandora-related-listing',               // $this->id_base
			__( 'Shandora Related Listing', 'bon' ), // $this->name
			$widget_options,                 // $this->widget_options
			$control_options                 // $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 1.0
	 */
	function widget( $sidebar, $instance ) {
		extract( $sidebar );

		/* Set the $args for wp_get_archives() to the $instance array. */
		$args = $instance;

		/* Overwrite the $echo argument and set it to false. */
		$args['echo'] = false;

		

		if(!is_singular('listing')) {
			return;
		}
		/* Output the theme's $before_widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		?>
		
		<div class="row">
			<div class="column large-12 featured-listing-carousel">
				<?php
					global $post;

					$tax_query = array();
					$meta_query = array();
					

					foreach($instance['related_to'] as $related_key) {
						switch ($related_key) {

							case 'bed':
								$beds = shandora_get_meta($post->ID,'listing_bed');
								if(!empty($beds)) {
									$meta_query[] = array(
										'key' => 'shandora_listing_bed',
		                                'value' => $beds,
		                                'compare' => '<=',
		                                'type'=> 'NUMERIC'
									);
								}
							break;

							case 'bath':
								$baths = shandora_get_meta($post->ID,'listing_bath');
								if(!empty($baths)) {
									$meta_query[] = array(
										'key' => 'shandora_listing_bath',
		                                'value' => $baths,
		                                'compare' => '<=',
		                                'type'=> 'NUMERIC'
									);
								}
							break;

							case 'lotsize':
								$lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
								if(!empty($lotsize)) {
									$meta_query[] = array(
										'key' => 'shandora_listing_lotsize',
		                                'value' => $lotsize,
		                                'compare' => '<=',
		                                'type'=> 'NUMERIC'
									);
								}
							break;

							case 'status':
								$status = shandora_get_meta($post->ID,'listing_status');
								if(!empty($status)) {
									$meta_query[] = array(
		                                'key' => 'shandora_listing_status',
		                                'value' => $status,
		                                'compare' => '=',
		                            );
		                        }
							break;

							case 'price':
								$price = shandora_get_meta($post->ID,'listing_price');
								if(!empty($price)) {
									$min_price = 0;
									if($price > 0) {
										$min_price = round($price / 2);
									}
									$status = shandora_get_meta($post->ID,'listing_price');
									$meta_query[] = array(
		                                'key' => 'shandora_listing_price',
		                                'value' => array($min_price, $price),
		                                'compare' => 'BETWEEN',
		                                'type'=> 'NUMERIC'
		                            );
								}
							break;
							
							case 'type':

								$property_types = wp_get_object_terms( $post->ID, 'property-type', array('fields' => 'slugs'));

								$tax_query[] = array(
                                    'taxonomy' => 'property-type',
                                    'field' => 'slug',
                                    'terms' => $property_types
                                );
								
							break;

							case 'location':

								$property_locations = wp_get_object_terms( $post->ID, 'property-location', array('fields' => 'slugs'));

								$tax_query[] = array(
                                    'taxonomy' => 'property-location',
                                    'field' => 'slug',
                                    'terms' => $property_locations
                                );
								
							break;

							case 'feature':

								$property_features = wp_get_object_terms( $post->ID, 'property-feature', array('fields' => 'slugs'));

								$tax_query[] = array(
                                    'taxonomy' => 'property-feature',
                                    'field' => 'slug',
                                    'terms' => $property_features
                                );
								
							break;

						}
					}

					$meta_count = count($meta_query);

                    if($meta_count > 1) { $meta_query['relation'] = 'AND'; }

                    $tax_count = count($tax_query);
                    if($tax_count > 1) { $tax_query['relation'] = 'OR'; }

					$query = array(
						'post_type' => 'listing',
						'posts_per_page' => $args['limit'],
						'meta_query' => $meta_query,
						'post__not_in' => array($post->ID),
						'tax_query' => $tax_query
					);

					$related_query = new WP_Query($query); 

					if($related_query->have_posts()) : $i = 0; ?>

					<script>
						jQuery(document).ready(function($){
							$('#<?php echo $this->id; ?>-slider').flexslider({
								animation: "slide",
								controlNav: false,
								controlsContainer: "#<?php echo $this->id; ?>-nav"
							});
						});
					</script>
					<div id="<?php echo $this->id; ?>-nav" class="featured-listing-nav">
					</div>
					<div id="<?php echo $this->id; ?>-slider">

	        			<ul class="slides">

					<?php while($related_query->have_posts()) : $related_query->the_post();
					?>
						<?php if( $i == 0 ) : ?>
							<li>
						<?php endif; ?>
						
						<div class="featured-item">
							<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'size' => 'listing_medium', 'before' => '<div class="featured-image">', 'after' => '</div>' ) ); ?>
							<?php 
								$bed = shandora_get_meta(get_the_ID(), 'listing_bed');
								$bath = shandora_get_meta(get_the_ID(), 'listing_bath');
							?>
							<span class="featured-item-meta hide-for-medium bed"><i class="sha-bed"></i><?php echo $bed; ?></span>
							<span class="featured-item-meta hide-for-medium bath"><i class="sha-bath"></i><?php echo $bath; ?></span>
							<div class="featured-item-title">
								<h2 class="title"><i class="awe-link"></i><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" ><?php the_title(); ?></a></h2>
							</div>
						</div>
						
	                    <?php 
	                    $i++;
	                    if($i == 3 ) : $i = 0; ?>
	                    	</li>
	                	<?php endif; ?>
					<?php
						endwhile; 
					?>
						<?php if ($i > 0 ) : ?>
							</li>
						<?php endif; ?>
						</ul>
						
					</div>

					<?php

					else: 

						echo '<p>' . __('No Related listing were found', 'bon') . '</p>';

					endif; wp_reset_query();
				?>
			</div>
		</div>

	<?php

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $new_instance;

		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['limit']  = strip_tags( $new_instance['limit'] );
		$instance['related_to'] = $new_instance['related_to'];

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title'           => esc_attr__( 'Related Listing', 'bon' ),
			'limit'           => 10,
			'related_to'	  => array('bed'),
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<div class="bon-widget-controls">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><code><?php _e( 'Title:', 'bon' ); ?></code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><code><?php _e('Number of Posts','bon'); ?></code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'related_to' ); ?>"><code><?php _e('Related to','bon'); ?></code></label>
			<select multiple="multiple" class="widefat" id="<?php echo $this->get_field_id( 'related_to' ); ?>" name="<?php echo $this->get_field_name( 'related_to' ); ?>[]">
				<option value="status" <?php selected( in_array( 'status', (array)$instance['related_to'] )); ?>><?php _e('Status','bon'); ?></option>
				<option value="bed" <?php selected( in_array( 'bed', (array)$instance['related_to'] )); ?>><?php _e('Bed','bon'); ?></option>
				<option value="bath" <?php selected( in_array( 'bath', (array)$instance['related_to'] )); ?>><?php _e('Bath','bon'); ?></option>
				<option value="price" <?php selected( in_array( 'price', (array)$instance['related_to'] )); ?>><?php _e('Price','bon'); ?></option>
				<option value="lotsize" <?php selected( in_array( 'lotsize', (array)$instance['related_to'] )); ?>><?php _e('Size','bon'); ?></option>
				<option value="type" <?php selected( in_array( 'type', (array)$instance['related_to'] )); ?>><?php _e('Property Type','bon'); ?></option>
				<option value="location" <?php selected( in_array( 'location', (array)$instance['related_to'] )); ?>><?php _e('Property Location','bon'); ?></option>
				<option value="feature" <?php selected( in_array( 'feature', (array)$instance['related_to'] )); ?>><?php _e('Property Feature','bon'); ?></option>
			</select>
		</p>
		<p><?php _e('Hold CTRL to select more than 1 options','bon'); ?></p>

		</div>
	<?php
	}
}

?>