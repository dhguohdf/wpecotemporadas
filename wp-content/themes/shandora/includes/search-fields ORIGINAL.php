<?php

function shandora_get_search_option($option = 'status') {

	$val = array();

	switch ($option) {

		case 'status':

			$val = array(
					'none' => __('None', 'bon'),
					'for-rent' => __('For Rent', 'bon'),
					'for-sale' => __('For Sale', 'bon'),
					'reduced' => __('Reduced', 'bon'),
					'new' => __('New', 'bon'),
					'sold' => __('Sold', 'bon'),
					'rented' => __('Rented', 'bon'),
					'on-show' => __('On Show', 'bon')
				);

			break;

		case 'type':

			$terms = get_terms('property-type');
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }

		break;

		case 'location':

			$terms = get_terms('property-location');
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }

		break;

		case 'location1':

			$terms = get_terms('property-location', array('parent' => 0));
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }

		break;

		case 'feature':

			$terms = get_terms('property-feature');
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }

		break;

		case 'furnishing':

			$val =  array(
						'any' => __('Any','bon'),
						'unfurnished' => __('Unfurnished', 'bon'),
						'furnished' => __('Furnished', 'bon'),
					);

		break;

		case 'mortgage':

			$val = array(
				'any' => __('Any','bon'),
				'mortgage' => __('Mortgage', 'bon'),
				'nomortgage' => __('No Mortgage', 'bon'),
			);

		break;

		case 'agent':
			$val['any'] = __('Any','bon');
			$posts = get_posts( array( 'post_type' => 'agent', 'posts_per_page' => 50, 'orderby' => 'name', 'order' => 'ASC' ) );
			foreach ( $posts as $item )
				$val[$item->ID] = $item->post_title;
					

		break;	

		case 'period':

			$val = array(
				'per-month' => __('Per Month', 'bon'),
				'per-year' => __('Per Year', 'bon'),
				'per-week' => __('Per Week', 'bon'),
				'per-day' => __('Per Day', 'bon'),
			);

		break;
		
	}

	return $val;
}

function shandora_get_car_search_option($option = 'status') {
	$val = array();

	switch ($option) {

		case 'transmission':

			$val = array(
				'automatic' => __('Automatic','bon'),
				'manual' => __('Manual','bon'),
				'semi-auto' => __('Semi Auto','bon'),
				'other' => __('Other','bon'),
			);
		break;

		case 'status':

			$val = array(
				'new' => __('New','bon'),
				'used' => __('Used','bon'),
				'certified' => __('Certified Pre-Owned','bon'),
			);
		break;

		case 'dealer_location':

			$terms = get_terms('dealer-location');
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }

		break;

		case 'dealer_location1':

			$terms = get_terms('dealer-location', array('parent' => 0));
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }

		break;

		case 'car_feature':

			$terms = get_terms('car-feature');
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }

		break;

		case 'body_type':

			$terms = get_terms('body-type');
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }

		break;

		case 'manufacturer':

			$terms = get_terms('manufacturer');
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }

		break;

		case 'manufacturer1':

			$terms = get_terms('manufacturer', array('parent' => 0));
			$val['any'] = __('Any', 'bon');
		    foreach($terms as $term) {
		    	$val[$term->slug] = $term->name;
		    }
	}

	return $val;
}

function shandora_search_yearbuilt_field($value = array(), $class) {

	$o = apply_atomic('search_yearbuilt_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Year Built','bon'), 'yearbuilt');
	$o .= $form->form_input('yearbuilt', $value['yearbuilt'], 'class="'.$class.'"');

	return apply_atomic( 'search_yearbuilt_field_output', $o );
}

function shandora_search_title_field($value = array(), $class) {

	$o = apply_atomic('search_title_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}


	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Title','bon'), 'title');
	$o .= $form->form_input('title', $value['title'], 'class="'.$class.'"');

	return apply_atomic( 'search_title_field_output', $o );
}
/**
 * Used to output mls field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_mls_field($value = array(), $class) {

	$o = apply_atomic('search_mls_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('MLS #','bon'), 'property_mls');
	$o .= $form->form_input('property_mls', $value['property_mls'], 'class="'.$class.'"');

	return apply_atomic( 'search_mls_field_output', $o );
}

/**
 * Used to output zip field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_zip_field($value = array(), $class) {

	$o = apply_atomic('search_zip_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Zip Postal','bon'), 'property_zip');
	$o .= $form->form_input('property_zip', $value['property_zip'], 'class="'.$class.'"');
	
	return apply_atomic( 'search_zip_field_output', $o );
}

/**
 * Used to output status field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_status_field($value = array(), $class) {

	$o = apply_atomic('search_status_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$pstat = array(
		'any' => __('Any', 'bon')
	);
	$stat = wp_parse_args( shandora_get_search_option('status') , $pstat );

	$o = $form->form_label(__('Status','bon'), 'property_status');
	$o .= $form->form_dropdown('property_status', $stat, $value['property_status'], 'class=" '.$class.'"');

	return apply_atomic( 'search_status_field_output', $o );
}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_location_field($value = array(), $class) {

	$o = apply_atomic('search_location_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Location','bon'), 'property_location');
	$o .= $form->form_dropdown('property_location', shandora_get_search_option('location'), $value['property_location'], 'class=" '.$class.'"');

	return apply_atomic( 'search_location_field_output', $o );

}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_location_level1_field($value = array(), $class) {

	$o = apply_atomic('search_location_level1_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(bon_get_option('location_level1_label'), 'property_location');
	$o .= $form->form_dropdown('property_location_level1', shandora_get_search_option('location1'), $value['property_location_level1'], 'class=" '.$class.'"');

	return apply_atomic( 'search_location_level1_field_output', $o );

}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_location_level2_field($value = array(), $class) {

	$o = apply_atomic('search_location_level2_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['property_location_level1'] != '') {
		$parent = get_term_by('slug', $value['property_location_level1'], 'property-location');
		if($parent) {
			$terms = get_terms('property-location', array('parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('location_level2_label'), 'property_location');
	$o .= $form->form_dropdown('property_location_level2', $loc_opt, $value['property_location_level2'], 'class=" '.$class.'"');

	return apply_atomic( 'search_location_level2_field_output', $o );

}


/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_location_level3_field($value = array(), $class) {

	$o = apply_atomic('search_location_level3_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['property_location_level2'] != '') {
		$parent = get_term_by('slug', $value['property_location_level2'], 'property-location');
		if($parent) {
			$terms = get_terms('property-location', array('parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('location_level3_label'), 'property_location');
	$o .= $form->form_dropdown('property_location_level3', $loc_opt, $value['property_location_level3'], 'class=" '.$class.'"');

	return apply_atomic( 'search_location_level3_field_output', $o );

}


/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_dealer_location_level1_field($value = array(), $class) {

	$o = apply_atomic('search_dealer_location_level1_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label( bon_get_option('dealer_location_level1_label'), 'dealer_location_level1');
	$o .= $form->form_dropdown('dealer_location_level1', shandora_get_car_search_option('dealer_location1'), $value['dealer_location_level1'], 'class=" '.$class.'"');

	return apply_atomic( 'search_dealer_location_level1_field_output', $o );

}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_dealer_location_level2_field($value = array(), $class) {

	$o = apply_atomic('search_dealer_location_level2_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['dealer_location_level1'] != '') {
		$parent = get_term_by('slug', $value['dealer_location_level1'], 'dealer-location');
		if($parent) {
			$terms = get_terms('dealer-location', array('parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('dealer_location_level2_label'), 'dealer_location_level2');
	$o .= $form->form_dropdown('dealer_location_level2', $loc_opt, $value['dealer_location_level2'], 'class=" '.$class.'"');

	return apply_atomic( 'search_dealer_location_level2_field_output', $o );

}


/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_dealer_location_level3_field($value = array(), $class) {

	$o = apply_atomic('search_dealer_location_level3_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['dealer_location_level2'] != '') {
		$parent = get_term_by('slug', $value['dealer_location_level2'], 'dealer-location');
		if($parent) {
			$terms = get_terms('dealer-location', array('parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('dealer_location_level3_label'), 'dealer_location_level3');
	$o .= $form->form_dropdown('dealer_location_level3', $loc_opt, $value['dealer_location_level3'], 'class=" '.$class.'"');

	return apply_atomic( 'search_dealer_location_level3_field_output', $o );

}

/**
 * Used to output car manufacturer field in search panel
 * 
 * @since 1.2.4
 * @return string
 * @param string $value
 *
 */
function shandora_search_manufacturer_level1_field($value = array(), $class) {

	$o = apply_atomic('search_manufacturer_level1_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(bon_get_option('manufacturer_level1_label'), 'manufacturer_level1');
	$o .= $form->form_dropdown('manufacturer_level1', shandora_get_car_search_option('manufacturer1'), $value['manufacturer_level1'], 'class=" '.$class.'"');

	return apply_atomic( 'search_manufacturer_level1_field_output', $o );

}

/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_manufacturer_level2_field($value = array(), $class) {

	$o = apply_atomic('search_manufacturer_level2_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['manufacturer_level1'] != '') {
		$parent = get_term_by('slug', $value['manufacturer_level1'], 'manufacturer');
		if($parent) {
			$terms = get_terms('manufacturer', array('parent' => $parent->term_id, 'hide_empty' => true ) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('manufacturer_level2_label'), 'manufacturer_level2');
	$o .= $form->form_dropdown('manufacturer_level2', $loc_opt, $value['manufacturer_level2'], 'class=" '.$class.'"');

	return apply_atomic( 'search_manufacturer_level2_field_output', $o );

}


/**
 * Used to output property location field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_manufacturer_level3_field($value = array(), $class) {

	$o = apply_atomic('search_manufacturer_level3_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$loc_opt = array('any' => __('Any','bon'));

	if($value['manufacturer_level2'] != '') {
		$parent = get_term_by('slug', $value['manufacturer_level2'], 'manufacturer');
		if($parent) {
			$terms = get_terms('manufacturer', array('parent' => $parent->term_id, 'hide_empty' => true) );
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	$o = $form->form_label(bon_get_option('manufacturer_level3_label'), 'manufacturer_level3');
	$o .= $form->form_dropdown('manufacturer_level3', $loc_opt, $value['manufacturer_level3'], 'class=" '.$class.'"');

	return apply_atomic( 'search_manufacturer_level3_field_output', $o );

}
/**
 * Used to output property feature field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_feature_field($value = array(), $class) {

	$o = apply_atomic('search_feature_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Feature','bon'), 'property_feature');
	$o .= $form->form_dropdown('property_feature', shandora_get_search_option('feature'), $value['property_feature'], 'class=" '.$class.'"');

	return apply_atomic( 'search_feature_field_output', $o );
}

/**
 * Used to output lot size field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_lotsize_field($value = array(), $class) {

	$o = apply_atomic('search_lotsize_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$range_opt = shandora_get_size_range('lotsize');

	$slider = '<div class="price-slider-wrapper"><div class="range-slider" data-type="lotsize" data-step="'.$range_opt['step'].'" data-min="'.$range_opt['min'].'" data-max="'.$range_opt['max'].'" id="lotsize-slider-range"></div></div>';

	$o = '<label for="property_lotsize">'.__('Lot Size', 'bon');
		$o .= '<span class="price-text text-min" id="min_lotsize_text"></span>';
		$o .= '<span class="price-text text-max" id="max_lotsize_text"></span>';
	$o .= '</label>';

	$o .= $slider;
	$o .= '<div class="row">';
		$o .= '<div class="column large-6">' . $form->form_hidden('min_lotsize', $value['min_lotsize']) . '</div>';
		$o .= '<div class="column large-6">' . $form->form_hidden('max_lotsize', $value['max_lotsize']). '</div>';
	$o .= '</div>';

	return apply_atomic( 'search_lotsize_field_output', $o );
}

/**
 * Used to output building size field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_buildingsize_field($value = array(), $class) {

	$o = apply_atomic('search_buildingsize_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$range_opt = shandora_get_size_range('buildingsize');

	$slider = '<div class="price-slider-wrapper"><div class="range-slider" data-type="buildingsize" data-step="'.$range_opt['step'].'" data-min="'.$range_opt['min'].'" data-max="'.$range_opt['max'].'" id="buildingsize-slider-range"></div></div>';

	$o = '<label for="property_buildingsize">'.__('Building Size', 'bon');
		$o .= '<span class="price-text text-min" id="min_buildingsize_text"></span>';
		$o .= '<span class="price-text text-max" id="max_buildingsize_text"></span>';
	$o .= '</label>';

	$o .= $slider;
	$o .= '<div class="row">';
		$o .= '<div class="column large-6">' . $form->form_hidden('min_buildingsize', $value['min_buildingsize']) . '</div>';
		$o .= '<div class="column large-6">' . $form->form_hidden('max_buildingsize', $value['max_buildingsize']) . '</div>';
	$o .= '</div>';

	return apply_atomic( 'search_buildingsize_field_output', $o );
}

/**
 * Used to output floor field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_floor_field($value = array(), $class) {

	$o = apply_atomic('search_floor_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}


	global $bon;
	$form = $bon->form();

	$floor_opt = absint(bon_get_option('maximum_floor', 5));
	$floor_arr = array(  'any'=> __('Any', 'bon') );
	if(!is_int($floor_opt)) {
		$floor_opt = 5;
	}
	for($i = 1; $i <= $floor_opt; $i++) {
		$floor_arr[$i] = $i;
	}
	$o = $form->form_label(__('Floor','bon'), 'property_floor');
	$o .= $form->form_dropdown('property_floor', $floor_arr, $value['property_floor'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_floor_field_output', $o );
}

/**
 * Used to output garage field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_garage_field($value = array(), $class) {

	$o = apply_atomic('search_garage_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$garage_opt = absint(bon_get_option('maximum_garage', 5));
	$garage_arr = array(  'any'=> __('Any', 'bon')  );
	if(!is_int($garage_opt)) {
		$garage_opt = 5;
	}
	for($i = 1; $i <= $garage_opt; $i++) {
		$garage_arr[$i] = $i;
	}
	$o = $form->form_label(__('Garage','bon'), 'property_garage');
	$o .= $form->form_dropdown('property_garage', $garage_arr, $value['property_garage'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_garage_field_output', $o );
}

/**
 * Used to output basement field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_basement_field($value = array(), $class) {

	$o = apply_atomic('search_basement_field', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$basement_opt = absint(bon_get_option('maximum_basement', 5));
	$basement_arr = array(  'any'=> __('Any', 'bon')  );
	if(!is_int($basement_opt)) {
		$basement_opt = 5;
	}
	for($i = 1; $i <= $basement_opt; $i++) {
		$basement_arr[$i] = $i;
	}
	$o = $form->form_label(__('Basement','bon'), 'property_basement');
	$o .= $form->form_dropdown('property_basement', $basement_arr, $value['property_basement'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_basement_field_output', $o );
}

/**
 * Used to output mortgage field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_mortgage_field($value = array(), $class) {

	$o = apply_atomic('search_mortgage_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Mortgage','bon'), 'property_mortgage');
	$o .= $form->form_dropdown('property_mortgage', shandora_get_search_option('mortgage'), $value['property_mortgage'], 'class=" '.$class.'"');

	return apply_atomic( 'search_mortgage_field_output', $o );
}

/**
 * Used to output property type field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_type_field($value = array(), $class) {

	$o = apply_atomic('search_type_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Type','bon'), 'property_type');
	$o .= $form->form_dropdown('property_type', shandora_get_search_option('type'), $value['property_type'], 'class=" '.$class.'"');

	return apply_atomic( 'search_type_field_output', $o );
}


/**
 * Used to output price field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $min_val
 * @param string $max_val
 *
 */
function shandora_search_price_field($value = array(), $class) {

	$o = apply_atomic('search_price_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$range_opt = shandora_get_price_range();
	$range_rent_opt = shandora_get_price_range('rent');

	$price_slider = '<div class="price-slider-wrapper"><div data-type="price" data-step-r="'.$range_rent_opt['step'].'" data-min-r="'.$range_rent_opt['min'].'" data-max-r="'.$range_rent_opt['max'].'" data-step="'.$range_opt['step'].'" data-min="'.$range_opt['min'].'" data-max="'.$range_opt['max'].'" id="slider-range" class="range-slider"></div></div>';

	$o = '<label for="property_price">'.__('Price Range', 'bon');
		$o .= '<span class="price-text text-min" id="min_price_text"></span>';
		$o .= '<span class="price-text text-max" id="max_price_text"></span>';
	$o .= '</label>';

	$o .= $price_slider;
	$o .= '<div class="row">';
		$o .= '<div class="column large-6">' . $form->form_hidden('min_price', $value['min_price']) . '</div>';
		$o .= '<div class="column large-6">' . $form->form_hidden('max_price', $value['max_price']) . '</div>';
	$o .= '</div>';

	return apply_atomic( 'search_price_field_output', $o );
}

/**
 * Used to output bed field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_bed_field($value = array(), $class) {

	$o = apply_atomic('search_bed_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$bed_opt = absint(bon_get_option('maximum_bed', 5));
	$bed_arr = array(  'any'=> __('Any', 'bon') );
	if(!is_int($bed_opt)) {
		$bed_opt = 5;
	}
	for($i = 1; $i <= $bed_opt; $i++) {
		$bed_arr[$i] = $i;
	}
	$o = $form->form_label(__('Bed Room','bon'), 'property_bed');
	$o .= $form->form_dropdown('property_bed', $bed_arr, $value['property_bed'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_bed_field_output', $o );
}

/**
 * Used to output bath field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_bath_field($value = array(), $class) {
	
	$o = apply_atomic('search_bath_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$bath_opt = absint(bon_get_option('maximum_bath', 5));
	$bath_arr = array(  'any'=> __('Any', 'bon') );
	if(!is_int($bath_opt)) {
		$bath_opt = 5;
	}
	for($i = 1; $i <= $bath_opt; $i++) {
		$bath_arr[$i] = $i;
	}
	$o = $form->form_label(__('Bath Room','bon'), 'property_bath');
	$o .= $form->form_dropdown('property_bath', $bath_arr, $value['property_bath'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_bath_field_output', $o );
}

/**
 * Used to output agent field in search panel
 * 
 * @since 1.0.6
 * @return string
 * @param string $value
 *
 */
function shandora_search_agent_field($value = array(), $class) {

	$o = apply_atomic('search_agent_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Agent','bon'), 'property_agent');
	$o .= $form->form_dropdown('property_agent', shandora_get_search_option('agent'), $value['property_agent'], 'class=" '.$class.'"');

	return apply_atomic( 'search_agent_field_output', $o );
}


/**
 * Used to output reg field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_reg_field($value = array(), $class) {

	$o = apply_atomic('search_reg_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Reg. Number #','bon'), 'reg_number');
	$o .= $form->form_input('reg_number', $value['reg_number'], 'class="'.$class.'"');

	return apply_atomic( 'search_reg_field_output', $o );
}

/**
 * Used to output color field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_exterior_color_field($value = array(), $class) {

	$o = apply_atomic('search_exterior_color_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Ext. Color','bon'), 'exterior_color');
	$o .= $form->form_input('exterior_color', $value['exterior_color'], 'class="'.$class.'"');

	return apply_atomic( 'search_exteriour_color_field_output', $o );
}

/**
 * Used to output color field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_interior_color_field($value = array(), $class) {

	$o = apply_atomic('search_interior_color_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Int. Color','bon'), 'interior_color');
	$o .= $form->form_input('interior_color', $value['interior_color'], 'class="'.$class.'"');

	return apply_atomic( 'search_interior_color_field_output', $o );
}

/**
 * Used to output fuel type field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_fuel_type_field($value = array(), $class) {

	$o = apply_atomic('search_fuel_type_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Fuel Type','bon'), 'fuel_type');
	$o .= $form->form_input('fuel_type', $value['fuel_type'], 'class="'.$class.'"');

	return apply_atomic( 'search_fuel_type_field_output', $o );
}

/**
 * Used to output status field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_car_status_field($value = array(), $class) {

	$o = apply_atomic('search_car_status_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$pstat = array(
		'any' => __('Any', 'bon')
	);
	$stat = wp_parse_args( shandora_get_car_search_option('status') , $pstat );

	$o = $form->form_label(__('Status','bon'), 'car_status');
	$o .= $form->form_dropdown('car_status', $stat, $value['car_status'], 'class=" '.$class.'"');

	return apply_atomic( 'search_car_status_field_output', $o );
}

/**
 * Used to output car dealer location field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_dealer_location_field($value = array(), $class) {

	$o = apply_atomic('search_dealer_location_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Dealer Location','bon'), 'dealer_location');
	$o .= $form->form_dropdown('dealer_location', shandora_get_car_search_option('dealer_location'), $value['dealer_location'], 'class=" '.$class.'"');

	return apply_atomic( 'search_dealer_location_field_output', $o );

}

/**
 * Used to output car dealer location field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_body_type_field($value = array(), $class) {

	$o = apply_atomic('search_body_type_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Body Type','bon'), 'body_type');
	$o .= $form->form_dropdown('body_type', shandora_get_car_search_option('body_type'), $value['body_type'], 'class=" '.$class.'"');

	return apply_atomic( 'search_body_type_field_output', $o );

}

/**
 * Used to output car feature field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_car_feature_field($value = array(), $class) {

	$o = apply_atomic('search_car_feature_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Feature','bon'), 'car_feature');
	$o .= $form->form_dropdown('car_feature', shandora_get_car_search_option('car_feature'), $value['car_feature'], 'class=" '.$class.'"');

	return apply_atomic( 'search_car_feature_field_output', $o );

}

/**
 * Used to output car manufacturer field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_manufacturer_field($value = array(), $class) {

	$o = apply_atomic('search_manufacturer_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = $form->form_label(__('Manufacturer','bon'), 'manufacturer');
	$o .= $form->form_dropdown('manufacturer', shandora_get_car_search_option('manufacturer'), $value['manufacturer'], 'class=" '.$class.'"');

	return apply_atomic( 'search_manufacturer_field_output', $o );
}

/**
 * Used to output car transmission field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_transmission_field($value = array(), $class) {

	$o = apply_atomic('search_transmission_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$pstat = array(
		'any' => __('Any', 'bon')
	);
	$trans = wp_parse_args( shandora_get_car_search_option('transmission') , $pstat );

	$o = $form->form_label(__('Transmission','bon'), 'transmission');
	$o .= $form->form_dropdown('transmission', $trans, $value['transmission'], 'class=" '.$class.'"');

	return apply_atomic( 'search_transmission_field_output', $o );
}

/**
 * Used to output ancap field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_ancap_field($value = array(), $class) {

	$o = apply_atomic('search_ancap_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	
	$ancap_arr = array(  'any'=> __('Any', 'bon')  );
	
	for($i = 1; $i <= 5; $i++) {
		$ancap_arr[$i] = $i;
	}
	$o = $form->form_label(__('ANCAP / Safety','bon'), 'ancap');
	$o .= $form->form_dropdown('ancap', $ancap_arr, $value['ancap'], 'class="no-custom select-slider '.$class.'"');

	return apply_atomic( 'search_ancap_field_output', $o );
}


/**
 * Used to output price field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $min_val
 * @param string $max_val
 *
 */
function shandora_search_car_price_field($value = array(), $class) {

	$o = apply_atomic('search_car_price_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$o = shandora_search_price_field($value, $class);

	return apply_atomic( 'search_car_price_field_output', $o );
}

/**
 * Used to output mileage field in search panel
 * 
 * @since 1.0.7
 * @return string
 * @param string $value
 *
 */
function shandora_search_mileage_field($value = array(), $class) {

	$o = apply_atomic('search_mileage_field', '', $value, $class );

	if( $o != '' ) {
		return $o;
	}

	global $bon;
	$form = $bon->form();

	$range_opt = shandora_get_size_range('mileage');

	$slider = '<div class="price-slider-wrapper"><div class="range-slider" data-type="mileage" data-step="'.$range_opt['step'].'" data-min="'.$range_opt['min'].'" data-max="'.$range_opt['max'].'" id="mileage-slider-range"></div></div>';

	$o = '<label for="mileage">'.__('Mileage', 'bon');
		$o .= '<span class="price-text text-min" id="min_mileage_text"></span>';
		$o .= '<span class="price-text text-max" id="max_mileage_text"></span>';
	$o .= '</label>';

	$o .= $slider;
	$o .= '<div class="row">';
		$o .= '<div class="column large-6">' . $form->form_hidden('min_mileage', $value['min_mileage']) . '</div>';
		$o .= '<div class="column large-6">' . $form->form_hidden('max_mileage', $value['max_mileage']). '</div>';
	$o .= '</div>';

	return apply_atomic( 'search_mileage_field_output', $o );
}


function shandora_ajax_update_location_level($slug) {
	$loc_opt = array('any' => __('Any', 'bon'));

	if ( function_exists( 'check_ajax_referer' ) ) {				
		check_ajax_referer( 'search-panel-submit', 'nonce' );
	}

	$slug = $_POST['term_slug'];

	if(!empty($slug)) {
		$parent = get_term_by('slug', $slug, 'property-location');
		if($parent) {
			$terms = get_terms('property-location', array( 'hide_empty' => true, 'parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	wp_send_json($loc_opt);
}

add_action( 'wp_ajax_location-level', 'shandora_ajax_update_location_level' );
add_action( 'wp_ajax_nopriv_location-level', 'shandora_ajax_update_location_level' );

function shandora_ajax_update_manufacturer_level($slug) {
	$loc_opt = array('any' => __('Any', 'bon'));

	if ( function_exists( 'check_ajax_referer' ) ) {				
		check_ajax_referer( 'search-panel-submit', 'nonce' );
	}

	$slug = $_POST['term_slug'];

	if(!empty($slug)) {
		$parent = get_term_by('slug', $slug, 'manufacturer');
		if($parent) {
			$terms = get_terms('manufacturer', array( 'hide_empty' => true, 'parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	wp_send_json($loc_opt);
}

add_action( 'wp_ajax_manufacturer-level', 'shandora_ajax_update_manufacturer_level' );
add_action( 'wp_ajax_nopriv_manufacturer-level', 'shandora_ajax_update_manufacturer_level' );

function shandora_ajax_update_dealer_location_level($slug) {
	$loc_opt = array('any' => __('Any', 'bon'));

	if ( function_exists( 'check_ajax_referer' ) ) {				
		check_ajax_referer( 'search-panel-submit', 'nonce' );
	}

	$slug = $_POST['term_slug'];

	if(!empty($slug)) {
		$parent = get_term_by('slug', $slug, 'dealer-location');
		if($parent) {
			$terms = get_terms('dealer-location', array( 'hide_empty' => true, 'parent' => $parent->term_id));
		    if($terms) {
		    	foreach($terms as $term) {
			    	$loc_opt[$term->slug] = $term->name;
			    }
		    }
		}
	}

	wp_send_json($loc_opt);
}

add_action( 'wp_ajax_dealer-location-level', 'shandora_ajax_update_dealer_location_level' );
add_action( 'wp_ajax_nopriv_dealer-location-level', 'shandora_ajax_update_dealer_location_level' );
?>