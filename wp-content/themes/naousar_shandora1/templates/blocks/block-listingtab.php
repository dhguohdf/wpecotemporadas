<?php
	$status = shandora_get_meta($post->ID, 'listing_status'); 
    $bed = shandora_get_meta($post->ID, 'listing_bed');
    $bath = shandora_get_meta($post->ID, 'listing_bath');
    $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
    $sizemeasurement = bon_get_option('measurement');
	$buildingsize = shandora_get_meta($post->ID, 'listing_buildingsize');
    $furnish = shandora_get_meta($post->ID, 'listing_furnishing');
    $mortgage = shandora_get_meta($post->ID, 'listing_mortgage');
    $garage = shandora_get_meta($post->ID, 'listing_garage');
    $basement = shandora_get_meta($post->ID,'listing_basement');
    $date = shandora_get_meta($post->ID,'listing_dateavail');
    $totalroom = shandora_get_meta($post->ID,'listing_totalroom');
    $year = shandora_get_meta($post->ID,'listing_yearbuild');
    $floor = shandora_get_meta($post->ID,'listing_floor');
    $agent_ids = get_post_meta($post->ID,'shandora_listing_agentpointed', true);
    $address = shandora_get_meta($post->ID, 'listing_address');
    $location = get_the_term_list( $post->ID, 'property-location' );
    $type = get_the_term_list($post->ID, 'property-type');
    $mls = shandora_get_meta($post->ID, 'listing_mls');
    $zip = shandora_get_meta($post->ID, 'listing_zip');

    $status_opt = shandora_get_search_option('status');

    if(array_key_exists($status, $status_opt)) {
    	$status = $status_opt[$status];
    }
?>
<section>
	<nav class="tab-nav">
		<a class="active" href="#tab-target-details"><?php _e('Details','bon'); ?></a>
		<a class="" href="#tab-target-features"><?php _e('Features','bon'); ?></a>
		<a class="" href="#tab-target-spec"><?php _e('Specification','bon'); ?></a>
	</nav>
	<div class="tab-contents">
		<div id="tab-target-details" class="tab-content active">
			<?php if((!empty($mortgage))) {
				if($mortgage =='nomortgage') {
					$mortgage = __('N/A','bon');
				} else {
					$mortgage = __('Available','bon');
				}
			} else {
				$mortgage = '-';
			}?>
			<ul class="property-details">
				<?php if(!empty($mls)) { ?><li><strong><?php _e('MLS:','bon'); ?></strong><span><?php echo $mls; ?></span></li><?php } ?>
				<?php if(!empty($address)) { ?><li><strong><?php _e('Address:','bon'); ?></strong><span><?php echo (!empty($address)) ? $address  : '-'; ?></span></li><?php } ?>
				<?php if(!empty($location)) { ?><li><strong><?php _e('Location:','bon'); ?></strong><span><?php echo (!empty($location)) ? $location : '-'; ?></span></li><?php } ?>
				<?php if(!empty($zip)) { ?><li><strong><?php _e('Zip:','bon'); ?></strong><span><?php echo (!empty($zip)) ? $zip  : '-'; ?></span></li><?php } ?>
                <?php if(!empty($status)) { ?><li><strong><?php _e('Status:','bon'); ?></strong><span><?php echo (!empty($status)) ? ucwords( str_replace('-',' ', $status) ) : '-'; ?></span></li><?php } ?>
                <?php if(!empty($type)) { ?><li><strong><?php _e('Property Type:','bon'); ?></strong><span><?php echo (!empty($type)) ? $type : '-'; ?></span></li><?php } ?>
                <?php if(!empty($mortgage)) { ?><li><strong><?php _e('Mortgage:','bon'); ?></strong><span><?php echo $mortgage; ?></span></li><?php } ?>
                <?php if(!empty($date)) { ?><li><strong><?php _e('Available:','bon'); ?></strong><span><?php echo (!empty($date)) ? $date : '-'; ?></span></li><?php } ?>
               <?php if(!empty($year)) { ?> <li><strong><?php _e('Year Built:','bon'); ?></strong><span><?php echo (!empty($year)) ? $year : '-'; ?></span></li><?php } ?>
            </ul>
		</div>
		<div id="tab-target-features" class="tab-content">

			<?php

			echo '<ul class="property-features">';
			echo get_the_term_list( get_the_ID(), 'property-feature', '<li>', ',</li><li>', '</li>' );
			echo '</ul>';

			?>

		</div>
		<div id="tab-target-spec" class="tab-content">
			<ul class="property-spec">
				<li><strong><?php _e('Bedrooms:','bon'); ?></strong><span><?php echo (!empty($bed)) ? $bed  : '-'; ?></span></li>
                <li><strong><?php _e('Bathrooms:','bon'); ?></strong><span><?php echo (!empty($bath)) ? $bath  : '-'; ?></span></li>
                <li><strong><?php _e('Garage:','bon'); ?></strong><span><?php echo (!empty($garage)) ? $garage  : '-'; ?></span></li>
				<li><strong><?php _e('Lot Size:','bon'); ?></strong><span><?php echo (!empty($lotsize)) ? $lotsize . ' ' . $sizemeasurement : '-'; ?></span></li>
                <li><strong><?php _e('Building Size:','bon'); ?></strong> <span><?php echo (!empty($buildingsize)) ? $buildingsize. ' ' . $sizemeasurement : '-'; ?></span></li>
                <li><strong><?php _e('Basement:','bon'); ?></strong><span><?php echo (!empty($basement)) ? $basement  : '-'; ?></span></li>
                <li><strong><?php _e('Floors:','bon'); ?></strong><span><?php echo (!empty($floor)) ? $floor  : '-'; ?></span></li>
                <li><strong><?php _e('Total Rooms:','bon'); ?></strong><span><?php echo (!empty($totalroom)) ? $totalroom  : '-'; ?></span></li>
			</ul>
		</div>
	</div>
</section>