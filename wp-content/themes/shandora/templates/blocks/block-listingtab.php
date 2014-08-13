<?php
	$status = shandora_get_meta($post->ID, 'listing_status'); 
    $bed = shandora_get_meta($post->ID, 'listing_bed');
    $bath = shandora_get_meta($post->ID, 'listing_bath');
    $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
    $sizemeasurement = bon_get_option('measurement');
	$buildingsize = shandora_get_meta($post->ID, 'listing_buildingsize');
    $furnish = shandora_get_meta($post->ID, 'listing_furnishing');
    $mortgage = shandora_get_meta($post->ID, 'listing_mortgage') != 'nomortgage' ?  __('N/A','bon') : __('Available','bon');
    $garage = shandora_get_meta($post->ID, 'listing_garage');
    $basement = shandora_get_meta($post->ID,'listing_basement');
    $date = shandora_get_meta($post->ID,'listing_dateavail');
    $totalroom = shandora_get_meta($post->ID,'listing_totalroom');
    $year = shandora_get_meta($post->ID,'listing_yearbuild');
    $floor = shandora_get_meta($post->ID,'listing_floor');
    $agent_ids = get_post_meta($post->ID,'shandora_listing_agentpointed', true);
    $address = shandora_get_meta($post->ID, 'listing_address');
    $location = get_the_term_list( $post->ID, 'property-location', '', ', ', '' );
    $type = get_the_term_list($post->ID, 'property-type');
    $mls = shandora_get_meta($post->ID, 'listing_mls');
    $zip = shandora_get_meta($post->ID, 'listing_zip');

    $status_opt = shandora_get_search_option('status');

    if(array_key_exists($status, $status_opt)) {
    	$status = $status_opt[$status];
    }

    $details = apply_atomic( 'property_details_tab_content', array(
		'mls' => __('MLS:', 'bon'),
		'address' => __('Address:','bon'),
		'location' => __('Location:','bon'),
		'zip' => __('Zip:','bon'),
		'status' => __('Status:','bon'),
		'type' => __('Property Type:','bon'),
		'mortgage' => __('Mortgage:','bon'),
		'date' => __('Date Available:','bon'),
		'year' => __('Year Built:','bon'),
	));

	$specs = apply_atomic( 'property_specifications_tab_content', array(
		'bed' => __('Bedrooms:', 'bon'),
		'bath' => __('Bathrooms:', 'bon'),
		'lotsize' => __('Lot Size:', 'bon'),
		'buildingsize' => __('Building Size:', 'bon'),
		'garage' => __('Garage:', 'bon'),
		'basement' => __('Basement:', 'bon'),
		'floor' => __('Floors:', 'bon'),
		'totalroom' => __('Total Rooms:', 'bon')
	));
?>
<section>
	<nav class="tab-nav">
		<?php if( !empty( $details ) && is_array( $details ) ) { ?> 
			<a class="active" href="#tab-target-details"><?php _e('Details','bon'); ?></a>
		<?php } ?>

			<a class="<?php if( empty( $details ) || !is_array( $details ) ) { echo 'active' ; } ?>" href="#tab-target-features"><?php _e('Features','bon'); ?></a>
		<?php if( !empty( $specs ) && is_array( $specs ) ) { ?> 
			<a href="#tab-target-spec"><?php _e('Specifications','bon'); ?></a>
		<?php } ?>
	</nav>
	<div class="tab-contents">

		<?php if( !empty( $details ) && is_array( $details) ) { ?> 
		<div id="tab-target-details" class="tab-content active">

			<ul class="property-details">
				<?php
					foreach($details as $key => $value) { ?>
						<?php if ( !empty( $$key ) ) { ?> 
						<li>
							<strong><?php echo $value; ?> </strong>
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

		<div id="tab-target-features" class="tab-content">

			<?php

			echo '<ul class="property-features">';
			echo get_the_term_list( get_the_ID(), 'property-feature', '<li>', ',</li><li>', '</li>' );
			echo '</ul>';

			?>

		</div>

		<?php if( !empty( $specs ) && is_array( $specs) ) { ?> 
		<div id="tab-target-spec" class="tab-content">
			<ul class="property-spec">
				<?php
					foreach($specs as $key => $value) { ?>
						<?php if ( !empty( $$key ) ) { ?> 
						<li>
							<strong><?php echo $value; ?> </strong>
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
	</div>
</section>