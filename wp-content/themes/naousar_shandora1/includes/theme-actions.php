<?php

function shandora_get_listing_price($echo = true) {
	global $post;

	$currency = bon_get_option('currency');
	$placement = bon_get_option('currency_placement');
	$price = shandora_get_meta($post->ID, 'listing_price', true); 


	$price = '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price">' . $price . '</span>';
	$price .= '<meta itemprop="priceCurrency" content="'.$currency.'" /></span>';
	$o = '';

	switch ($placement) {
		
		case 'left-space':
			$format = $currency . ' ' . $price;
		break;

		case 'right':
			$format = $price . $currency;
		break;

		case 'right-space':
			$format = $price . ' ' . $currency;
		break;

		default:
			$format = $currency . $price;
		break;
	}

	$o .= shandora_get_rent_period($format);

	if($echo) {
		echo $o;
	} else {
		return $o;
	}

}

function shandora_get_rent_period($price) {
	global $post;

	$status = shandora_get_meta($post->ID, 'listing_status');
	$period = shandora_get_meta($post->ID, 'listing_period'); 

	if($status === 'for-rent') {

		switch ($period) {
			case 'per-day':
				$price .= ' <span class="rent-period">/'.__('day','bon').'</span>';
			break;

			case 'per-week':
				$price .= ' <span class="rent-period">/'.__('week','bon').'</span>';
			break;

			case 'per-year':
				$price .= ' <span class="rent-period">/'.__('year','bon').'</span>';
			break;
			
			case 'per-month':
				$price .= ' <span class="rent-period">/'.__('month','bon').'</span>';
			break;

			default:
				$price;
			break;
		}
	}

	return $price;
}



/**
 * =====================================================================================================
 *
 * Setup a reuseable form
 * Created using global $bon->form() instance
 *
 *
 * @since 1.0
 *
 * ======================================================================================================
 */


function shandora_get_contact_form() {

	global $bon;

	$form = $bon->form();

	$output = $form->form_open( get_admin_url() );
	$output .= $form->form_label('Name :', 'contact_name');
	$output .= $form->form_input('contact_name', $form->set_value('contact_name'), 'class="required"');
	$output .= $form->form_label('Email :', 'contact_email');
	$output .= $form->form_email('contact_email', $form->set_value('contact_email'), 'class="required"');
	$output .= $form->form_label('Website :', 'contact_homepage');
	$output .= $form->form_input('contact_homepage', $form->set_value('contact_homepage'));
	$output .= $form->form_label('Subject :', 'contact_subject');
	$output .= $form->form_input('contact_subject', $form->set_select('contact_subject'), 'class="required"');
	$output .= $form->form_textarea('contact_message', $form->set_value('contact_message'), 'class="required"');
	$output .= $form->form_hidden('contact_submit', true);
	$output .= $form->form_decoy('contact_decoy');
	$output .= $form->form_submit('submit', 'Submit');
	$output .= $form->form_close();

	return $output;

}

function shandora_validate_form() {

	global $bon;

	$validation = $bon->validation();

	$validation->set_rules('contact_name', 'Name', 'required');
	$validation->set_rules('contact_email', 'Email Address', 'required|valid_email');
	$validation->set_rules('contact_homepage', 'Website Address', 'valid_url');
	$validation->set_rules('contact_subject', 'Subject', 'required');
	$validation->set_rules('contact_message', 'Message', 'required');
	$validation->set_rules('contact_decoy', '', 'is_decoy');

	if($validation->run() === FALSE) {
		return $bon->form->validation_errors();
	}
	else {
		$name = $_POST['contact_name'];
		$email = $_POST['contact_email'];
		$subject = $_POST['contact_subject'];
		$emailTo = 'nackle2k10@gmail.com';
		$message = $_POST['contact_message'];

		$subject_email = sprintf( 'Contact Form Submission from %s', $name );

		$body = "Name : " . $name . "\n\n Email : " . $email . "\n\n Subject : " . $subject . "\n\n Messages : " . $message;
		$headers = 'From: '.$email . "\r\n" . 'Reply-To: ' . $email;
		
		if(wp_mail( $emailTo, $subject_email, $body, $headers )) {
			return $bon->form->validation_errors();
		}
	}

}

function shandora_contact_form() {
	
	if(isset($_POST['contact_submit'])) {
		echo shandora_get_contact_form();
		echo shandora_validate_form();
	}
	else {
		echo shandora_get_contact_form();
		
	}
	
}

/**
 * =====================================================================================================
 *
 * Setup a search listing form
 * Created using global $bon->form() instance
 *
 *
 * @since 1.0
 *
 * ======================================================================================================
 */

function shandora_search_listing_form() {

	$show_idx = bon_get_option('use_idx_search');

	if(defined( "DSIDXPRESS_OPTION_NAME" ) && $show_idx == 'yes') {
		$options = get_option( DSIDXPRESS_OPTION_NAME );
		if($options["Activated"]) {
			shandora_get_search_listing_form_idx();
		} else {
			echo __('Please Activate Your IDX Account.','bon');
		}
	} else {
		echo shandora_get_search_listing_form();
	}
	
}

function shandora_get_search_listing_form( $is_widget = false ) {

	global $bon;

	$values = array();
	$values['property_type'] = isset($_COOKIE['property_type']) ? $_COOKIE['property_type'] : '';
	$values['property_location'] = isset($_COOKIE['property_location']) ? $_COOKIE['property_location'] : '';
	$values['property_location_level1'] = isset($_COOKIE['property_location_level1']) ? $_COOKIE['property_location_level1'] : '';
	$values['property_location_level2'] = isset($_COOKIE['property_location_level2']) ? $_COOKIE['property_location_level2'] : '';
	$values['property_location_level3'] = isset($_COOKIE['property_location_level3']) ? $_COOKIE['property_location_level3'] : '';
	$values['property_status'] = isset($_COOKIE['property_status']) ? $_COOKIE['property_status'] : '';
	$values['property_bath'] = isset($_COOKIE['property_bath']) ? $_COOKIE['property_bath'] : '';
	$values['property_bed'] = isset($_COOKIE['property_bed']) ? $_COOKIE['property_bed'] : '';
	$values['max_price'] = isset($_COOKIE['max_price']) ? $_COOKIE['max_price'] : '';
	$values['min_price'] = isset($_COOKIE['min_price']) ? $_COOKIE['min_price'] : '';
	$values['max_lotsize'] = isset($_COOKIE['max_lotsize']) ? $_COOKIE['max_lotsize'] : '';
	$values['min_lotsize'] = isset($_COOKIE['min_lotsize']) ? $_COOKIE['min_lotsize'] : '';
	$values['max_buildingsize'] = isset($_COOKIE['max_buildingsize']) ? $_COOKIE['max_buildingsize'] : '';
	$values['min_buildingsize'] = isset($_COOKIE['min_buildingsize']) ? $_COOKIE['min_buildingsize'] : '';
	$values['property_mls'] = isset($_COOKIE['property_mls']) ? $_COOKIE['property_mls'] : '';
	$values['property_zip'] = isset($_COOKIE['property_zip']) ? $_COOKIE['property_zip'] : '';
	$values['property_feature'] = isset($_COOKIE['property_feature']) ? $_COOKIE['property_feature'] : '';
	$values['property_agent'] = isset($_COOKIE['property_agent']) ? $_COOKIE['property_agent'] : '';
	$values['property_floor'] = isset($_COOKIE['property_floor']) ? $_COOKIE['property_floor'] : '';
	$values['property_basement'] = isset($_COOKIE['property_basement']) ? $_COOKIE['property_basement'] : '';
	$values['property_garage'] = isset($_COOKIE['property_garage']) ? $_COOKIE['property_garage'] : '';
	$values['property_mortgage'] = isset($_COOKIE['property_mortgage']) ? $_COOKIE['property_mortgage'] : '';

	$values['reg_number'] = isset($_COOKIE['reg_number']) ? $_COOKIE['reg_number'] : '';
	$values['dealer_location'] = isset($_COOKIE['dealer_location']) ? $_COOKIE['dealer_location'] : '';
	$values['car_feature'] = isset($_COOKIE['car_feature']) ? $_COOKIE['car_feature'] : '';
	$values['body_type'] = isset($_COOKIE['body_type']) ? $_COOKIE['body_type'] : '';
	$values['manufacturer'] = isset($_COOKIE['manufacturer']) ? $_COOKIE['manufacturer'] : '';
	$values['car_status'] = isset($_COOKIE['car_status']) ? $_COOKIE['car_status'] : '';
	$values['fuel_type'] = isset($_COOKIE['fuel_type']) ? $_COOKIE['fuel_type'] : '';
	$values['transmission'] = isset($_COOKIE['transmission']) ? $_COOKIE['transmission'] : '';
	$values['ancap'] = isset($_COOKIE['ancap']) ? $_COOKIE['ancap'] : '';
	$values['min_mileage'] = isset($_COOKIE['min_mileage']) ? $_COOKIE['min_mileage'] : '';
	$values['max_mileage'] = isset($_COOKIE['max_mileage']) ? $_COOKIE['max_mileage'] : '';
	$values['exterior_color'] = isset($_COOKIE['exterior_color']) ? $_COOKIE['exterior_color'] : '';
	$values['interior_color'] = isset($_COOKIE['interior_color']) ? $_COOKIE['interior_color'] : '';

	$button_color = bon_get_option('search_button_color', 'red');
	$form = $bon->form();

	$ro = '<div class="row search-listing-form">'; // row open
	$rc = '</div>';  // row close
	$cc = $rc; //column close

	if( !$is_widget ) {
		$co = '<div class="large-4 column form-column small-11 small-centered large-uncentered">'; // column open
	} else {
		$co = '<div class="large-12 column form-column small-11 small-centered large-uncentered">';
	}

	$row_1 = bon_get_option('search_row_1');
	$row_2 = bon_get_option('search_row_2');
	$row_3 = bon_get_option('search_row_3');
	$row_count = 3;

	$search_page = bon_get_option('search_listing_page');

	$permalink_active = get_option( 'permalink_structure' );

	if($permalink_active != '') {
		$search_permalink = get_permalink($search_page);
	} else {
		$search_permalink = esc_url( home_url( '/?page_id=' . $search_page ) );
	}

	$output = $form->form_open( $search_permalink, 'method="get" class="custom" id="search-listing-form"' );


	$output .= $ro;

	if( !$is_widget ) { 
		$output .= '<div class="column large-10 small-12 large-uncentered small-centered">';
	} else {
		$output .= '<div class="column large-12 small-12 large-uncentered small-centered">';
	}

	
	for($row_i = 1; $row_i <= $row_count; $row_i++ ) {

		if(${"row_{$row_i}"}) {

			for($col_i = 1; $col_i <= 3; $col_i++) {

				if($col_i == 1 ) {
					$output .= $ro;
				}

				$field_type = bon_get_option('search_row_'.$row_i.'_col_'.$col_i);
				$func = "shandora_search_".$field_type."_field";

				$class = '';

				$select_field = array(
					'status',
					'location',
					'location_level1',
					'location_level2',
					'location_level3',
					'feature',
					'mortgage',
					'type',
					'agent',
					'car_status',
					'dealer_location',
					'body_type',
					'car_feature',
					'manufacturer',
					'transmission',
				);

				if( !$is_widget ) {

					if($row_i >= 3) {
						$class = 'no-mbot';
					}

					if(in_array($field_type, $select_field)) {
						$class .= ' select-dark';
					}
				}

				$output .= $co;
				if(function_exists($func)) {
					$output .= $func($values, $class);
				}				
				$output .= $cc;

				if($col_i >= 3) {
					$output .= $rc;
				}
			}

			if($col_i < 3) {
				$output .= $rc;
			}
		
		}
		
	}


	$output .= $cc;

	$search_label = bon_get_option('search_button_label', __('Find Property','bon'));

	if( !$is_widget ) {
		$output .= '<div class="column large-2 small-11 large-uncentered small-centered" id="submit-button">';
		$output .= wp_nonce_field( 'search-panel-submit','search_nonce', true, false );
		$output .= $form->form_submit('', $search_label, 'class="button expand small flat '.$button_color.' radius"');
	} else {
		$output .= '<div class="column large-12 small-11 large-uncentered small-centered" style="margin-top: 1em;">'; 
		$output .= wp_nonce_field( 'search-panel-submit','search_nonce', true, false );
		$output .= $form->form_submit('', $search_label, 'class="button small flat '.$button_color.' radius"');
	}

	$output .= $cc . $rc;

	$output .= $form->form_close();

	return $output;
}




function shandora_get_search_listing_form_idx() {

	global $bon;

	$options = get_option(DSIDXPRESS_OPTION_NAME);

		if (!$options["Activated"])
			return;
		
		$pluginUrl = plugins_url() . '/dsidxpress/';

		wp_enqueue_script('dsidxpress_widget_search_view', $pluginUrl . 'js/widget-client.js', array('jquery'), DSIDXPRESS_PLUGIN_VERSION, true);

		$formAction = get_home_url() . "/idx/";

		$defaultSearchPanels = dsSearchAgent_ApiRequest::FetchData("AccountSearchPanelsDefault", array(), false, 60 * 60 * 24);
		$defaultSearchPanels = $defaultSearchPanels["response"]["code"] == "200" ? json_decode($defaultSearchPanels["body"]) : null;
		$propertyTypes = dsSearchAgent_ApiRequest::FetchData("AccountSearchSetupFilteredPropertyTypes", array(), false, 60 * 60 * 24);
		$propertyTypes = $propertyTypes["response"]["code"] == "200" ? json_decode($propertyTypes["body"]) : null;

		$account_options = dsSearchAgent_ApiRequest::FetchData("AccountOptions", array(), false);
		$account_options = $account_options["response"]["code"] == "200" ? json_decode($account_options["body"]) : null;
		$autoload_options = bon_get_option('idx_enable_search_autoload');

		if( $autoload_options == 'no') {

			$manual_city = explode("\n", bon_get_option('idx_manual_city'));
			sort($manual_city);

			$manual_community = explode("\n", bon_get_option('idx_manual_community'));
			sort($manual_community);

			$manual_tract = explode("\n", bon_get_option('idx_manual_tract'));
			sort($manual_tract);

			$manual_zip = explode("\n", bon_get_option('idx_manual_zip'));
			sort($manual_zip);



			$searchOptions = array(
				'cities' => $manual_city,
				'communities' => $manual_community,
				'tracts' => $manual_tract,
				'zips' => $manual_zip,
			);
		}

		else {
			$searchOptions = array(
				'cities' => shandora_get_idx_options('City'),
				'communities' => shandora_get_idx_options('Community'),
				'tracts' => shandora_get_idx_options('Tract'),
				'zips' => shandora_get_idx_options('Zip'),
			);
		}
		
		$ro = '<div class="row search-listing-form">'; // row open
		$rc = '</div>';  // row close
		$cc = $rc; //column close
		$co = '<div class="large-4 column form-column small-11 small-centered large-uncentered">'; // column open

		?>
			
			<form id="search-listing-form" action="<?php echo $formAction; ?>" method="get" class="custom" onsubmit="return dsidx_w.searchWidget.validate();" >
				<?php echo $ro . '<div class="column large-10 small-12 large-uncentered small-centered">' . $ro; ?>


				<?php echo $co; ?>
				<label for="idx-q-PropertyTypes"><?php _e('Property Type','bon'); ?></label>
				<select name="idx-q-PropertyTypes" class="select-dark dsidx-search-widget-propertyTypes">
					<option value=""><?php _e('All Property Types','bon'); ?></option>

					<?php  if (is_array($propertyTypes)) {
						foreach ($propertyTypes as $propertyType) {
							$name = htmlentities($propertyType->DisplayName); ?>
							<option value="<?php echo $propertyType->SearchSetupPropertyTypeID; ?>" <?php selected('idx-q-PropertyTypes', $propertyType->SearchSetupPropertyTypeID); ?>><?php echo $name; ?></option>
					<?php } } ?>
				</select>
				<label id="idx-search-invalid-msg" style="color:red"></label>
				<?php echo $cc; ?>
				<?php echo $co; ?>
					<label for="idx-q-Cities"><?php _e('City','bon'); ?></label>
					<select id="idx-q-Cities" name="idx-q-Cities" class="select-dark no-custom select-wrap idx-q-Location-Filter">
						<option value=""><?php _e('Any','bon'); ?></option>
						<?php
						foreach ($searchOptions["cities"] as $city) {
							// there's an extra trim here in case the data was corrupted before the trim was added in the update code below
							$city = ($autoload_options == 'no') ? htmlentities(trim($city)) : htmlentities(trim($city->Name)); ?>

						<option value="<?php echo $city; ?>" <?php selected('idx-q-Cities', $city); ?>><?php echo $city; ?></option>
						<?php } ?>
					</select>
				<?php echo $cc; ?>
				<?php echo $co; ?>
					<?php
					$bed_opt = absint(bon_get_option('maximum_bed', 5));
					if(!is_int($bed_opt)) {
						$bed_opt = 5;
					}

					?>
		
					<label for="idx-q-BedsMin"><?php _e('Beds','bon'); ?></label>
					<!--<input id="idx-q-BedsMin" name="idx-q-BedsMin" type="text" class="dsidx-beds" placeholder="min bedrooms" /> -->
					<div class="ui-slider-wrapper-custom beds-wrapper">
					<select name="idx-q-BedsMin" id="idx-q-BedsMin" class="bon-dsidx-beds2 dsidx-beds no-custom select-slider">
						<option value=""><?php _e('Any','bon'); ?></option>
						<?php for($i = 1; $i <= $bed_opt; $i++) { ?>
							<option value="<?php echo $i; ?>" <?php selected('idx-q-BedsMin', $i); ?>><?php echo $i; ?></option>
						<?php } ?>
					</select>
					</div>
				<?php echo $cc . $rc; ?>

				<?php echo $ro . $co; ?>
				<label for="idx-q-TractIdentifiers"><?php _e('Tract','bon'); ?></label>
				<select id="idx-q-TractIdentifiers" name="idx-q-TractIdentifiers" class="select-dark no-custom select-wrap idx-q-Location-Filter">
					<option value=""><?php _e('Any','bon'); ?></option>
					<?php 
					foreach ($searchOptions["tracts"] as $tract) {
						// there's an extra trim here in case the data was corrupted before the trim was added in the update code below
						$tract = ($autoload_options == 'no') ? htmlentities(trim($tract)) : htmlentities(trim($tract->Name)); ?>
						<option value="<?php echo $tract; ?>" <?php selected('idx-q-TractIdentifiers', $tract); ?>><?php echo $tract; ?></option>
					<?php } ?>
				</select>
				<?php echo $cc; ?>
				<?php echo $co; ?>
				<label for="idx-q-ZipCodes"><?php _e('Zip','bon'); ?></label>
				<select id="idx-q-ZipCodes" name="idx-q-ZipCodes" class="select-dark no-custom select-wrap idx-q-Location-Filter">
					<option value=""><?php _e('Any','bon'); ?></option>
					<?php 
					foreach ($searchOptions["zips"] as $zip) {
					// there's an extra trim here in case the data was corrupted before the trim was added in the update code below
					$zip = ($autoload_options == 'no') ? htmlentities(trim($zip)) : htmlentities(trim($zip->Name)); ?>
					<option value="<?php echo $zip; ?>" <?php selected('idx-q-ZipCodes', $zip); ?>><?php echo $zip; ?></option>
					<?php } ?>
				</select>
				<?php echo $cc; ?>
				<?php echo $co; ?>
				<?php
					$bath_opt = absint(bon_get_option('maximum_bath', 5));
					if(!is_int($bath_opt)) {
						$bath_opt = 5;
					}
					
					?>
					<label for="idx-q-BathsMin"><?php _e('Baths','bon'); ?></label>
					<div class="ui-slider-wrapper-custom baths-wrapper">
					<select name="idx-q-BathsMin" id="idx-q-BathsMin" class="bon-dsidx-baths2 dsidx-baths no-custom select-slider">
						<option value=""><?php _e('Any','bon'); ?></option>
						<?php for($i = 1; $i <= $bath_opt; $i++) { ?>
							<option value="<?php echo $i; ?>" <?php selected('idx-q-BathsMin', $i); ?>><?php echo $i; ?></option>
						<?php } ?>
					</select>
					</div>
				<?php echo $cc . $rc; ?>
				
				<?php echo $ro . $co; ?>
				<label for="idx-q-Communities"><?php _e('Community','bon'); ?></label>
				<select id="idx-q-Communities" name="idx-q-Communities" class="select-dark no-custom select-wrap idx-q-Location-Filter">
					<option value=""><?php _e('Any','bon'); ?></option>
					<?php 
					foreach ($searchOptions["communities"] as $community) {
						// there's an extra trim here in case the data was corrupted before the trim was added in the update code below
						$community = ($autoload_options == 'no') ? htmlentities(trim($community)) : htmlentities(trim($community->Name)); ?>
						<option value="<?php echo $community; ?>" <?php selected('idx-q-Communities', $community); ?>><?php echo $community; ?></option>
					<?php } ?>
				</select>
				<?php echo $cc; ?>
				<?php echo $co; ?>
				<label for="idx-q-MlsNumbers"><?php _e('MLS #','bon'); ?></label>
				<input id="idx-q-MlsNumbers" name="idx-q-MlsNumbers" type="text" class="dsidx-mlsnumber" value="<?php isset($_GET['idx-q-MlsNumbers']) ? $_GET['idx-q-MlsNumbers']: '';?>" />
					
				<?php echo $cc; ?>
				<?php echo $co; ?>

				<label for="idx-q-PriceMin"><?php _e('Price Range','bon'); ?>
					<span class="price-text" id="idx-min-price-text"></span>
					<span class="price-text" id="idx-max-price-text"></span>
				</label>
				<div class="price-slider-wrapper ui-slider-wrapper-custom">
					<div id="idx-slider-range2"></div>
				</div>
				<input id="idx-q-PriceMin" name="idx-q-PriceMin" type="hidden" class="dsidx-price bon-dsidx-price-min2" value="<?php isset($_GET['idx-q-PriceMin']) ? $_GET['idx-q-PriceMin']: '';?>" placeholder="min price" />
				<input id="idx-q-PriceMax" name="idx-q-PriceMax" type="hidden" class="dsidx-price bon-dsidx-price-max2" value="<?php isset($_GET['idx-q-PriceMax']) ? $_GET['idx-q-PriceMax']: '';?>" placeholder="max price" />
				
				<?php echo $cc . $rc . $cc; ?>
					<div class="column large-2 small-11 large-uncentered small-centered" id="submit-button">
					<?php 
					$button_color = bon_get_option('search_button_color', 'red'); 
					$search_label = bon_get_option('search_button_label', __('Find Property','bon'));
					?>
					<input type="submit" class="button flat <?php echo $button_color; ?> expand small radius submit" value="<?php echo $search_label; ?>" />
					</div>
				</div>
			</form>
			
	<?php 

}


function shandora_get_searchform($location = "", $button = false) {

	if($location != "header") {
		$output = "";
	} else {
		$output = '
		<div class="searchform">
		<form role="search" method="get" class="search hidden-phone" id="searchform" action="' . home_url( '/' ) . '" >
		<i class="icon sha-zoom"></i><input class="input-medium" type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="'.esc_attr(__('Search the Site...','bon')).'" />
		</form></div>';
	}

	echo $output;
}

function shandora_get_social_icons($header = true) {
	if($header) {
		$id = 'top-social-icons';
		$class = 'right social-icons';
		$navclass = 'large-6 column';
	} else {
		$id = 'footer-social-icons';
		$class = 'social-icons';
		$navclass = '';
	}
	$output = '<nav class="'.$navclass.'">
	    <ul class="'.$class.'" id="'.$id.'">';
	    if(bon_get_option('social_facebook')) {
			$output .=	'<li><a href="http://facebook.com/'.bon_get_option('social_facebook').'" title="'.__('Follow us on Facebook','bon').'"><span class="sha-facebook"></span></a></li>';
	    }
	    if(bon_get_option('social_twitter')) {
	    	$output .= '<li><a href="http://twitter.com/'.bon_get_option('social_twitter').'" title="'.__('Follow us on Twitter','bon').'"><span class="sha-twitter"></span></a></li>';
	    }
	    if(bon_get_option('social_google_plus')) {
	      	$output .= '<li><a href="http://plus.google.com/'.bon_get_option('social_google_plus').'" title="'.__('Follow us on Google Plus','bon').'"><span class="sha-googleplus"></span></a></li>';
	    }
		if(bon_get_option('social_pinterest')) {
	      	$output .= '<li><a href="http://pinterest.com/'.bon_get_option('social_pinterest').'" title="'.__('Follow us on Pinterest','bon').'"><span class="sha-pinterest"></span></a></li>';
		}
		if(bon_get_option('social_flickr')) {
	      	$output .= '<li><a href="http://flickr.com/photos/'.bon_get_option('social_flickr').'" title="'.__('Follow us on Flickr','bon').'"><span class="sha-flickr"></span></a></li>';
		}
		if(bon_get_option('social_vimeo')) {
	      	$output .= '<li><a href="http://vimeo.com/'.bon_get_option('social_vimeo').'" title="'.__('Find us on Vimeo','bon').'"><span class="sha-vimeo"></span></a></li>';
		}
		if(bon_get_option('social_youtube')) {
	      	$output .= '<li><a href="http://youtube.com/'.bon_get_option('social_youtube').'" title="'.__('Find us on YouTube','bon').'"><span class="sha-youtube"></span></a></li>';
		}
		if(bon_get_option('social_linkedin')) {
	      	$output .= '<li><a href="http://linkedin.com/in/'.bon_get_option('social_linkedin').'" title="'.__('Find us on LinkedIn','bon').'"><span class="sha-linkedin"></span></a></li>';
		}	
	
	$output .= '</ul></nav>';

	echo $output;
}

/**
 * =====================================================================================================
 *
 * Setup Helper Function
 *
 *
 * @since 1.0
 *
 * ======================================================================================================
 */
function shandora_get_meta($postID, $args, $is_number = false) {

	$prefix = bon_get_prefix();
	
	$price_format = bon_get_option('price_format', 'comma');

	if($is_number) {
		if($price_format == 'comma') {
			$meta = esc_attr( number_format( (double) get_post_meta($postID, $prefix . $args, true), 0, '', ',' ) );
		} else {
			$meta = esc_attr( number_format( (double) get_post_meta($postID, $prefix . $args, true), 0, ',', '.' ) );
		}
	} else {
		$meta = esc_attr(get_post_meta($postID, $prefix . $args, true));
	}
	
	return $meta;
}

function shandora_get_price_range() {

	$o = array();

	$min_val = bon_get_option('price_range_min', '0');
	$max_val = bon_get_option('price_range_max', '2000000');
	$step = bon_get_option('price_range_step', '5000');

	$o['min'] = $min_val;
	$o['max'] = $max_val;
	$o['step'] = $step;

	//wp_send_json($o);

	return $o;
}

function shandora_get_idx_price_range() {

	$o = array();

	$min_val = bon_get_option('price_range_min', '0');
	$max_val = bon_get_option('price_range_max', '2000000');
	$step = bon_get_option('price_range_step', '5000');

	$o['min_val'] = $min_val;
	$o['max_val'] = $max_val;
	$o['step'] = $step;

	wp_send_json($o);

}

function shandora_get_size_range($type = 'lotsize') {

	$o = array();

	$min_val = bon_get_option('minimum_'.$type, '0');
	$max_val = bon_get_option('maximum_'.$type, '10000');
	$step = bon_get_option('step_'.$type, '100');

	$o['min'] = $min_val;
	$o['max'] = $max_val;
	$o['step'] = $step;

	return $o;
}


add_action( 'wp_ajax_price-range', 'shandora_get_idx_price_range' );
add_action( 'wp_ajax_nopriv_price-range', 'shandora_get_idx_price_range' );

function shandora_column_class($large = 'large-12', $with_small = true) {

	$small = '';
	if($with_small) {
		$small = 'small-11 small-centered';
	}

	return 'column '.$large.' large-uncentered '.$small;

}

/* Filter the sidebar widgets. */
add_filter( 'sidebars_widgets', 'shandora_disable_sidebars' );
add_action( 'template_redirect', 'shandora_set_theme_column' );
/**
 * Function for deciding which pages should have a one-column layout.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function shandora_set_theme_column() {

	if ( !is_active_sidebar( 'primary' ) && !is_active_sidebar( 'secondary' ) ) {
		add_filter( 'theme_mod_theme_layout', 'shandora_theme_layout_one_column' );
	}

	else if( is_tax('property-type') ) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_property_type');
	}
	
	else if( is_tax('property-feature') ) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_property_feature');
	}

	else if( is_tax('property-location') ) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_property_location');
	}

	else if( is_tax('body-type') ) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_body_type');
	}
	
	else if( is_tax('car-feature') ) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_car_feature');
	}

	else if( is_tax('dealer-location') ) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_dealer_location');
	}

	else if( is_tax('manufacturer') ) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_manufacturer');
	}

	else if( is_category()) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_category');
	}

	else if( is_archive()) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_archive');
	}

	else if( is_tag()) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_tag');
	}

	else if( is_page_template('page-templates/page-template-idx.php') ) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_idx');
	}

	else if( is_page_template('page-templates/page-template-idx-details.php') ) {
		add_filter('theme_mod_theme_layout', 'shandora_theme_layout_idx_details');
	}

	else if ( is_page_template('page-templates/page-template-home.php') || 
			 is_page_template('page-templates/page-template-compare-listings.php') ) {
		add_filter( 'theme_mod_theme_layout', 'shandora_theme_layout_one_column' );
	}

	else if ( is_attachment() && wp_attachment_is_image() ) {
		add_filter( 'theme_mod_theme_layout', 'shandora_theme_layout_one_column' );
	}

	else if ( get_post_layout( get_queried_object_id() ) == 'default') {
		add_filter( 'theme_mod_theme_layout', 'shandora_default_column');
	}
}


function shandora_default_column() {
	return '2c-l';
}
/**
 * Filters 'get_theme_layout' by returning 'layout-1c'.
 *
 * @since  0.1.0
 * @param  string $layout The layout of the current page.
 * @return string
 */
function shandora_theme_layout_one_column( $layout ) {
	return '1c';
}
function shandora_theme_layout_property_type() {
	$layout = bon_get_option('property_type_layout');
	return $layout;
}
	
function shandora_theme_layout_property_feature() {
	$layout = bon_get_option('property_feature_layout');
	return $layout;
}
	
function shandora_theme_layout_property_location() {
	$layout = bon_get_option('property_location_layout');
	return $layout;
}
	
function shandora_theme_layout_category() {
	$layout = bon_get_option('category_layout');
	return $layout;
}
	
function shandora_theme_layout_archive() {
	$layout = bon_get_option('archive_layout');
	return $layout;
}

function shandora_theme_layout_tag() {
	$layout = bon_get_option('tag_layout');
	return $layout;
}

function shandora_theme_layout_idx() {
	$layout = bon_get_option('idx_layout');
	return $layout;
}

function shandora_theme_layout_idx_details() {
	$layout = bon_get_option('idx_details_layout');
	return $layout;
}

function shandora_theme_layout_dealer_location() {
	$layout = bon_get_option('dealer_location_layout');
	return $layout;
}

function shandora_theme_layout_car_feature() {
	$layout = bon_get_option('car_feature_layout');
	return $layout;
}

function shandora_theme_layout_manufacturer() {
	$layout = bon_get_option('manufacturer_layout');
	return $layout;
}

function shandora_theme_layout_body_type() {
	$layout = bon_get_option('body_type_layout');
	return $layout;
}
	
/**
 * Disables sidebars if viewing a one-column page.
 *
 * @since  0.1.0
 * @param  array $sidebars_widgets A multidimensional array of sidebars and widgets.
 * @return array $sidebars_widgets
 */
function shandora_disable_sidebars( $sidebars_widgets ) {
	global $wp_customize;

	$customize = ( is_object( $wp_customize ) && $wp_customize->is_preview() ) ? true : false;

	if ( !is_admin() && !$customize && '1c' == get_theme_mod( 'theme_layout' ) )
		$sidebars_widgets['primary'] = false;

	return $sidebars_widgets;
}


add_action( 'admin_enqueue_scripts' , 'shandora_admin_script', 10 );

function shandora_admin_script($hook) {

	if($hook == 'post-new.php' || $hook == 'post.php') {

		global $post;

		if($post->post_type === 'page') :

			wp_register_script( 'shandora-page-script', BON_THEME_URI . '/assets/js/admin/page.js', array('jquery'));

			wp_enqueue_script('shandora-page-script');

		endif;

	}
	
}


function shandora_get_video() {

	global $post;

	$prefix = bon_get_prefix();

	$embed = esc_url( get_post_meta($post->ID, $prefix . 'videoembed', true) );
	$poster = get_post_meta($post->ID, $prefix . 'videocover', true);

	if($poster) {
		$src = wp_get_attachment_image_src( $poster, 'large' );
	}

	$m4v = get_post_meta($post->ID, $prefix . 'videom4v', true);
	$ogv = get_post_meta($post->ID, $prefix . 'videoogv', true);

	$o = '';

	if(!empty($embed)) {
		$o .= '<div class="video-container">';
    	$o.= '<div class="video-embed">';
    	$embed_code = wp_oembed_get($embed);
    	$o.= $embed_code;
    	$o.= '</div>';
    	$o.= '</div>';

    	return $o;

    } else if(!empty($m4v) && !empty($ogv)) {
    	$o .= '<div class="video-container">';
		$o .= '<div id="jp-video-embed" class="bon-jplayer jp-jplayer jp-jplayer-video" data-poster="'.$poster.'" data-m4v="'.$m4v.'" data-ogv="'.$ogv.'"></div>';

		$o .= '<div class="jp-video-container">
	        <div class="jp-video">
	            <div class="jp-type-single">
	                <div id="jp-interface-video-embed" class="jp-interface">
	                    <div class="jp-controls">
	                        <div class="jp-play" tabindex="1">
	                            <span class="awe-play icon"></span>
	                        </div>
	                        <div class="jp-pause" tabindex="1">
	                            <span class="awe-pause icon"></span>
	                        </div>
	                        <div class="jp-progress-container">
	                            <div class="jp-progress">
	                                <div class="jp-seek-bar">
	                                    <div class="jp-play-bar"></div>
	                                </div>
	                            </div>
	                        </div>
	                        <div class="jp-mute" tabindex="1"><span class="awe-volume-up icon"></span></div>
	                        <div class="jp-unmute" tabindex="1"><span class="awe-volume-off icon"></span></div>
	                        <div class="jp-volume-bar-container">
	                            <div class="jp-volume-bar">
	                                <div class="jp-volume-bar-value"></div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>';

	    $o.= '</div>';
	}

    return $o;
}

add_action('wp_enqueue_scripts', 'shandora_register_script');

function shandora_register_script() {

	if(!wp_script_is( 'fitvids', 'registered' )) {
		wp_register_script( 'fitvids', trailingslashit( BON_JS ) . 'jquery.fitvids.js', array('jquery'), false, false );
	}
	if(!wp_script_is('fitvs', 'queue')) {
		wp_enqueue_script( 'fitvids' );
	}
	if(!wp_script_is( 'jplayer', 'registered' )) {
		wp_register_script( 'jplayer', trailingslashit( BON_JS ) . '/frontend/jplayer/jquery.jplayer.min.js', array('jquery'), false, false );
	}
	if(!wp_script_is('jplayer', 'queue')) {
		wp_enqueue_script( 'jplayer' );
	}
	
}

function shandora_listing_post_per_page( $query ) {
    if(!is_admin()) {

    	if ( ($query->is_tax('property-type') || $query->is_tax('property-location') || $query->is_tax('property-feature') ||
    		$query->is_tax('dealer-location') || $query->is_tax('body-type') || $query->is_tax('manufacturer') || $query->is_tax('car-feature')) && $query->is_main_query() ) {

	    	 $numberposts = (bon_get_option('listing_per_page')) ? bon_get_option('listing_per_page') : 8;

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


	         if( $query->is_tax('property-name')  || $query->is_tax('metro-station')  || $query->is_tax('property-type') || $query->is_tax('property-location') || $query->is_tax('property-feature') ) {
	         	$query->set('post_type', 'listing');
	         } else if ( $query->is_tax('dealer-location') || $query->is_tax('body-type') || $query->is_tax('manufacturer') || $query->is_tax('car-feature') ) {
	         	$query->set('post_type', 'car-listing');
	         }
	         
	         $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	         $query->set('meta_key', $key);
	         $query->set('orderby', $orderby);
	         $query->set('order', $order);
	         $query->set('paged', $paged);
	         $query->set( 'posts_per_page', $numberposts );
	    }
    }
}
add_action( 'pre_get_posts', 'shandora_listing_post_per_page' );

function shandora_block_grid_column_class($echo = true) {

	$layout = get_theme_mod('theme_layout');
    if(empty($layout)) {
        $layout = get_post_layout(get_queried_object_id());
    }

    $mobile = bon_get_option('mobile_layout', '2');
    if($layout == '1c') {
        $class = 'small-block-grid-'.$mobile.' large-block-grid-4';
    } else {
        $class = 'small-block-grid-'.$mobile.' large-block-grid-3';
    }

    if($echo) {
    	echo $class;
    } else {
    	return $class;
    }
}

add_action('wp_ajax_process_agent_contact','shandora_process_agent_contact');
add_action('wp_ajax_nopriv_process_agent_contact','shandora_process_agent_contact');

function shandora_process_agent_contact() {

	if(!isset($_POST) || empty($_POST)) {
		$return_data['value'] = __('Cannot send email to destination. No parameter receive form AJAX call.','bon');	
		die ( json_encode($return_data) );
	}

	$name = esc_html( $_POST['name'] );

	if(empty($name)) {
		$return_data['value'] = __('Please enter your name.','bon');
		die ( json_encode($return_data) );
	}

	$email = sanitize_email( $_POST['email'] );

	if(empty($email)){
		$return_data['value'] = __('Please enter a valid email address.','bon');
		die ( json_encode($return_data) );		
	}


	$subject = esc_html( $_POST['subject'] );

	$messages = esc_textarea( $_POST['messages'] );

	if(empty($messages)){ 
		$return_data['value'] = 'Please enter your messages.';
		die ( json_encode($return_data) );				
	}

	if(function_exists('akismet_http_post') && trim(get_option('wordpress_api_key')) != '' ) {
		global $akismet_api_host, $akismet_api_port;
		$c['user_ip']			= preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
		$c['blog']			= home_url();
		$c['comment_author']	= $name;
		$c['comment_author_email'] = $email;
		$c['comment_content'] 	= $messages;

		$query_string = '';
		foreach ( $c as $key => $data ) {
			if( is_string($data) )
				$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';
		}
		
		$response = akismet_http_post($query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);
		
		if ( 'true' == $response[1] ) { // Akismet says it's SPAM
			$return_data['value'] = __('Cheatin Huh?!', 'bon');
			die( json_encode($return_data) );
		}
	}


	$receiver = $_POST['receiver'];

	$body = "You have received a new contact form message via ".get_bloginfo('name')." \n";
	$body .= 'Name : ' . $name . " \n";
	$body .= 'Email : ' . $email . " \n";
	$body .= 'Subject : ' . $subject . " \n";
	$body .= 'Message : ' . $messages;

	$header = "From: " . $name . " <" . $email . "> \r\n";
	$header .= "Reply-To: " . $email;

	$subject_email = "[".get_bloginfo('name')." Contact Form] ".$subject;

	if( wp_mail($receiver, $subject_email , $body, $header) ){
		$return_data['success'] = '1';
		$return_data['value'] = __('Email was sent successfully.','bon');
		die( json_encode($return_data) );
	} else {
		$return_data['value'] = __('There is an error sending email.','bon');
		die( json_encode($return_data) );	
	}

}

function shandora_get_listing_hover_action($post_id = '') {

	if(empty($post_id)) {
		$post_id = get_the_ID();
	}

	$args = array(
	   'post_type' => 'attachment',
	   'numberposts' => 5,
	   'post_parent' => $post_id,
	  );

	$listing_gal = shandora_get_meta($post_id, 'listing_gallery');
	$data_imageset = array();
    $imageset = '';

	if ( $listing_gal ) {
		$attachments = array_filter( explode( ',', $listing_gal ) );
		if($attachments) {
			$i = 0;
			foreach ( $attachments as $attachment_id ) {
				
				$image_src = wp_get_attachment_image_src( $attachment_id, 'full' );
				$data_imageset[$i]['src'] = $image_src[0];
				$data_imageset[$i]['title'] = get_the_title($attachment_id);
				$i++;
			}
		}
	 }

	$imageset = json_encode($data_imageset);				   
	

	$o = '<div class="hover-icon-wrapper">';
	$o .= '<a data-tooltip data-options="disable-for-touch: true" title="' . sprintf( __('Permalink to %s', 'bon' ), get_the_title( $post_id ) ) . '" href="'.get_permalink( $post_id ).'" class="hover-icon has-tip tip-top tip-centered-top"><i class="sha-link"></i></a>';
		if(!empty($data_imageset)) :
			$o .= '<a data-tooltip data-options="disable-for-touch: true" data-imageset=\''. $imageset . '\' title="'. __('View Image','bon'). '" class="has-tip tip-top top-centered-top hover-icon listing-gallery"><i class="sha-zoom"></i></a>';
		endif;
	$o .= '<a data-tooltip data-options="disable-for-touch: true" title="'.__('Compare Listing','bon').'" data-id='.$post_id.' class="hover-icon has-tip tip-top tip-centered-top listing-compare"><i class="sha-paperclip"></i></a></div>';

	return $o;
}

add_filter('cleaner_gallery_defaults', 'shandora_cleaner_gallery_defaults', 10);

function shandora_cleaner_gallery_defaults($args) {

	$args['size'] = 'listing_small_box';

	return $args;
}

function shandora_remove_attachment_comment() {
	remove_post_type_support( 'attachment', 'comments' );
}
add_action('init', 'shandora_remove_attachment_comment');

function shandora_get_idx_options($type = '', $r_array = false) {

	if(empty($type)) {
		return;
	}

	$limit = bon_get_option('idx_search_option_limit', 100);
	$options = get_option( DSIDXPRESS_OPTION_NAME );
	$setup_id = $options['SearchSetupID'];

	$url = 'http://api-c.idx.diversesolutions.com/api/';

	$results = wp_remote_get( $url . 'LocationsByType?searchSetupID='. $setup_id . '&type='. $type . '&minListingCount=1');
	
	$return = $results["response"]["code"] == "200" ? array_slice(json_decode($results["body"]), 0, $limit) : null;

	if($return) {
		if($r_array) {
			$new_return = array();
			foreach($return as $r) {
				$new_return[] = $r->Name;
			}
			return $new_return;	
		} else {
			return $return;
		}
	} else {
		return;
	}
	
		
}


?>