<?php

	$reg = shandora_get_meta($post->ID, 'listing_reg');
    $engine = shandora_get_meta($post->ID, 'listing_enginesize');
    $enginetype = shandora_get_meta($post->ID, 'listing_enginetype');
    $transmission = shandora_get_meta($post->ID, 'listing_transmission');
    $ancap = shandora_get_meta($post->ID, 'listing_ancap');
    $mileage = shandora_get_meta($post->ID, 'listing_mileage');
    $price = shandora_get_meta($post->ID, 'listing_price');
    $extcolor = shandora_get_meta($post->ID, 'listing_extcolor');
    $intcolor = shandora_get_meta($post->ID, 'listing_intcolor');
    $status = shandora_get_meta($post->ID, 'listing_status');
    $fuel = shandora_get_meta($post->ID, 'listing_fueltype');

    $height = shandora_get_meta($post->ID, 'listing_height');
    $width = shandora_get_meta($post->ID, 'listing_width');
    $length = shandora_get_meta($post->ID, 'listing_length');
    $wheelbase = shandora_get_meta($post->ID, 'listing_wheelbase');
    $trackrear = shandora_get_meta($post->ID, 'listing_trackrear');
    $trackfront = shandora_get_meta($post->ID, 'listing_trackfront');
    $ground = shandora_get_meta($post->ID, 'listing_ground');

    $seating = shandora_get_meta($post->ID, 'listing_seating');
    $steering = shandora_get_meta($post->ID, 'listing_steering');

    $length_measure = bon_get_option('length_measure', 'in.');
    $mileage_measure = bon_get_option('mileage_measure', 'in.');

    $trans_opt = shandora_get_car_search_option('transmission');
    if(array_key_exists($transmission, $trans_opt)) {
    	$transmission = $trans_opt[$transmission];
    }
    $status_opt = shandora_get_car_search_option('status');
    if(array_key_exists($status, $status_opt)) {
    	$status = $status_opt[$status];
    }
    
	$bodytype = get_the_term_list( $post->ID, 'body-type', '', ', ', '' );

	$location = get_the_term_list( $post->ID, 'dealer-location', '', ', ', '' );

	$terms = get_the_terms( $post->ID, 'manufacturer' );
	
	$manufacturer = array();		
	if ( $terms && ! is_wp_error( $terms ) ) 
	{														   														   
		   foreach ( $terms as $term )
		   {					
		   		if( $term->parent == '0' ) {										   
					$manufacturer[] = '<a href="'.get_term_link( $term->term_id, 'manufacturer' ).'" title="'.$term->name.'">' . $term->name . '</a>';
				}
		   }														   													   														   
	}
	$manufacturer = implode(', ', $manufacturer);


	$details = apply_atomic( 'car_details_tab_content', array(
		'reg' => __('Reg. Number #', 'bon'),
		'location' => __('Dealer Location','bon'),
		'manufacturer' => __('Manufacturer','bon'),
		'bodytype' => __('Body Type','bon'),
	));

	$specs = apply_atomic( 'car_specifications_tab_content', array(
		'engine' => __('Engine Size', 'bon'),
		'enginetype' => __('Engine Type', 'bon'),
		'transmission' => __('Transmission','bon'),
		'mileage' => __('Mileage', 'bon'),
		'extcolor' => __('Exterior Color', 'bon'),
		'intcolor' => __('Interior Color', 'bon'),
		'fuel' => __('Fuel Type', 'bon'),
		'status' => __('Status', 'bon'),
		'ancap' => __('ANCAP / Safety Rating', 'bon'),
		'seating' => __('Standard Seating', 'bon'),
		'steering' => __('Steering Type', 'bon'),
	));

	$dimension = apply_atomic( 'car_dimensions_tab_content', array(
		'height' => __('Height','bon'),
		'width' => __('Width','bon'),
		'length' => __('Length', 'bon'),
		'wheelbase' => __('Wheelbase', 'bon'),
		'trackrear' => __('Track Rear', 'bon'),
		'trackfront' => __('Track Front', 'bon'),
		'ground' => __('Ground Clearance', 'bon'),
	));

    
?>
<section>
	<nav class="tab-nav">
		<?php if( !empty( $details ) && is_array( $details ) ) { ?> 
		<a class="active" href="#tab-target-details"><?php _e('Details','bon'); ?></a>
		<?php } ?>
		<?php if( !empty( $specs ) && is_array( $specs ) ) { ?> 
		<a href="#tab-target-spec"><?php _e('Specifications','bon'); ?></a>
		<?php } ?>
		<?php if( !empty( $dimension ) && is_array( $dimension ) ) { ?> 
		<a href="#tab-target-dimension"><?php _e('Dimensions','bon'); ?></a>
		<?php } ?>

		<a href="#tab-target-features" class="<?php if( empty( $details ) || !is_array( $details ) ) { echo 'active' ; } ?>"><?php _e('Features','bon'); ?></a>
	</nav>
	<div class="tab-contents">

		<?php if( !empty( $details ) && is_array( $details) ) { ?> 
		<div id="tab-target-details" class="tab-content active">
			
			<ul class="car-detail property-details">
				<?php
					foreach($details as $key => $value) { ?>
						<?php if ( !empty( $$key ) ) { ?> 
						<li>
							<strong><?php echo $value; ?>: </strong>
							<span>
								<?php if(($$key) && !empty($$key)) { 
									echo $$key;

								 } else { 
									 	echo '-'; 
								 } ?>
							</span>
						</li>
						<?php } ?>
				<?php }
				?>
            </ul>
		</div>
		<?php } ?>

		<?php if( !empty( $specs ) && is_array( $specs) ) { ?> 
		<div id="tab-target-spec" class="tab-content">
			
			<ul class="car-spec property-details">
				<?php
					foreach($specs as $key => $value) { ?>
						<?php if( !empty( $$key ) ) { ?>
						<li>
							<strong><?php echo $value; ?>: </strong>
							<span>
								<?php if(($$key) && !empty($$key)) { 
								if($key == 'mileage') {
									echo $$key . ' ' . $mileage_measure;
								} else if($key == 'ancap') {
									if($$key <= 5) {
										for($i = 0; $i < $$key; $i++) {
											echo '<i class="awe-star"></i>';
										}
									}
								}
								else {
									echo $$key;
								}

							 } else { 
							 	echo '-'; 

							 } ?></span>
						</li>
						<?php } ?>
				<?php }
				?>
            </ul>
		</div>
		<?php } ?>

		<?php if( !empty( $dimension ) && is_array( $dimension) ) { ?> 
		<div id="tab-target-dimension" class="tab-content">
			<ul class="car-dimension property-details">
				<?php
					foreach($dimension as $key => $value) { ?>
						<?php if( !empty( $$key ) ) { ?>
						<li>
							<strong><?php echo $value; ?>: </strong>
							<span><?php if(($$key) && !empty($$key)) { echo $$key . ' ' . $length_measure; } else { echo '-'; } ?></span>
						</li>
						<?php } ?>
				<?php }
				?>
			</ul>
		</div>
		<?php } ?>
		<div id="tab-target-features" class="tab-content">

			<?php

			echo '<ul class="car-features">';
			echo get_the_term_list( get_the_ID(), 'car-feature', '<li>', ',</li><li>', '</li>' );
			echo '</ul>';

			?>

		</div>
	</div>
</section>