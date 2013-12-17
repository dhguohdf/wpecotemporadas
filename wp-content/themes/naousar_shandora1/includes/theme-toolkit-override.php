<?php


/**
 * =====================================================================================================
 *
 * Filtering the Bon Toolkit Builder and Extend the output
 *
 *
 * @since 1.0
 *
 * ======================================================================================================
 */

remove_filter('bon_tookit_builder_render_row_class', 'bon_toolkit_builder_row_class', 1);
remove_filter('bon_toolkit_builder_render_column_class', 'bon_toolkit_filter_builder_class', 1);

add_filter('bon_toolkit_builder_use_placement_class', 'shandora_use_placement_class');

function shandora_use_placement_class() {
	return false;
}


add_filter('bon_tookit_builder_render_row_class', 'shandora_builder_row_class', 1 , 1); 

function shandora_builder_row_class($args) {
	$args[] = 'entry-row';

	return $args;
}

add_filter('bon_toolkit_builder_render_column_class', 'shandora_builder_column_class');

function shandora_builder_column_class($class) {

	switch ($class[0]) {
		case 'span3':
			$c = shandora_column_class('large-3');
			return explode(" ", $c);
			break;
		case 'span4':
			$c = shandora_column_class('large-4');
			return explode(" ", $c);
			break;
		case 'span6':
			$c = shandora_column_class('large-6');
			return explode(" ", $c);
			break;
		case 'span8':
			$c = shandora_column_class('large-8');
			return explode(" ", $c);
			break;
		case 'span9':
			$c = shandora_column_class('large-9');
			return explode(" ", $c);
			break;
		case 'span12':
			$c = shandora_column_class('large-12');
			return explode(" ", $c);
			break;		
	}

}

add_filter( 'bon_toolkit_builder_element_service_filter', 'shandora_builder_service_element');

function shandora_builder_service_element($args) {

	$prefix = bon_get_prefix();

	if(function_exists('bon_toolkit_get_builder_suffix')) {
		$suffix = bon_toolkit_get_builder_suffix();
	} else {
		$suffix = '';
	}
	
	$args['icon_animation'] = array(
        'title' => __('Icon Animation', 'bon'),
        'name' => $prefix . $suffix['service'] . 'icon_animation',
        'type' => 'select',
        'options' => array(
           'bottom-top' => __('Bottom to Top', 'bon'),
           'top-bottom' => __('Top to Bottom', 'bon'),
           'left-right' => __('Left to Right', 'bon'),
           'right-left' => __('Right to Left', 'bon')
        ),
	);
	
	return $args;
}

add_filter( 'bon_toolkit_builder_options_filter', 'shandora_builder_options_filter', 10);

function shandora_builder_options_filter($args) {

	$prefix = bon_get_prefix();

	if(function_exists('bon_toolkit_get_builder_suffix')) {
		$suffix = bon_toolkit_get_builder_suffix();
	} else {
		$suffix = '';
	}

	$options_car = bon_get_option('enable_car_listing', 'no');
	$agentopt = bon_get_post_id_lists('agent');
	$salesopt = bon_get_post_id_lists('sales-representative');
	$list_cat = bon_get_categories('property-type');
	$list_body = bon_get_categories('body-type');
	$list_dealer_loc = bon_get_categories('dealer-location');
	$list_manufacturer = bon_get_categories('manufacturer');
	$list_car_feature = bon_get_categories('car-feature');
	$list_loc = bon_get_categories('property-location');
	$list_features = bon_get_categories('property-feature');

	if(isset($args['elements']['post'])) {
		unset($args['elements']['post']);
	}
	
	$prop_query = array(
	        		'latest' => __('Latest', 'bon'),
	        		'featured' => __('Featured', 'bon')
	        	);

	$prop_query = array_merge($prop_query, shandora_get_search_option('status'));
	$args['elements']['post_carousel'] = array(

		'header' => array(
            'title' => __('Title', 'bon'),
            'name' => $prefix . 'builder_post_carousel_text',
            'type' => 'text',
        ),
		'numberposts' => array(
            'title' => __('Number of Posts to Fetch', 'bon'),
            'name' => $prefix . 'builder_post_carousel_numberposts',
            'type' => 'text',
        ),
        'button_color' => array(
        	'title' => __('Button Color', 'bon'),
        	'name' => $prefix . 'builder_button_color',
        	'type' => 'select',
        	'options' => array(
        		'red' => __('Red', 'bon'),
        		'blue' => __('Blue','bon'),
        		'green' => __('Green', 'bon'),
        		'purple' => __('Purple', 'bon'),
        		'orange' => __('Orange','bon')
        	)
        ),
        'margin' => array(
            'title' => __('Bottom Margin', 'bon'),
            'name' => $prefix . 'builder_post_carousel_margin',
            'std' => '0',
            'hr' => 'none',
            'type' => 'text'
        ),
        'default_size' => 'span12',
        'allowed_size' => array(
                'span12'=>'1/1'
            ),
	);

	$args['elements']['listings'] = array(

		'numberposts' => array(
            'title' => __('Number of Posts to Fetch', 'bon'),
            'name' => $prefix . 'builder_listings_numberposts',
            'type' => 'text',
        ),
        'property_type' => array(
        	'title' => __('Property Type', 'bon'),
        	'name' => $prefix . 'builder_listings_cat',
        	'type' => 'select',
        	'options' => $list_cat,
        ),
        'property_location' => array(
        	'title' => __('Property Location', 'bon'),
        	'name' => $prefix . 'builder_listings_location',
        	'type' => 'select',
        	'options' => $list_loc,
        ),
        'property_feature' => array(
        	'title' => __('Property Feature', 'bon'),
        	'name' => $prefix . 'builder_listings_features',
        	'type' => 'select',
        	'options' => $list_features,
        ),
        'property_query' => array(
        	'title' => __('Property Query', 'bon'),
        	'name' => $prefix . 'builder_listings_query',
        	'type' => 'select',
        	'options' => $prop_query
        ),
        'margin' => array(
            'title' => __('Bottom Margin', 'bon'),
            'name' => $prefix . 'builder_listings_margin',
            'std' => '0',
            'hr' => 'none',
            'type' => 'text'
        ),
        'default_size' => 'span12',
        'allowed_size' => array(
                'span12'=>'1/1'
            ),
	);
	
	
	$args['elements']['agent'] = array(

		'header' => array(
            'title' => __('Title', 'bon'),
            'name' => $prefix . 'builder_agent_title',
            'type' => 'text',
        ),

		'agent_id' => array(
            'title' => __('Pick Agent', 'bon'),
            'name' => $prefix . 'builder_agent_id',
            'type' => 'select',
            'options' => $agentopt
        ),
       	
       	'color' => array(
        	'title' => __('Background Color', 'bon'),
        	'name' => $prefix . 'builder_agent_color',
        	'type' => 'select',
        	'options' => array(
        		'blue' => __('Blue', 'bon'),
        		'red' => __('Red','bon'),
        		'green' => __('Green', 'bon'),
        		'orange' => __('Orange','bon'),
        	),
        ),

        'margin' => array(
            'title' => __('Bottom Margin', 'bon'),
            'name' => $prefix . 'builder_agent_margin',
            'std' => '0',
            'hr' => 'none',
            'type' => 'text'
        ),
        'default_size' => 'span3',
        'allowed_size' => array(
            'span3'=>'1/4',
            'span4'=>'1/3',
            'span6'=>'1/2',
            'span8'=>'2/3',
            'span9'=>'3/4',
            'span12'=>'1/1'
        ),
	);
	
	if( $options_car == 'yes') {
	$args['elements']['car_listings'] = array(

			'numberposts' => array(
	            'title' => __('Number of Posts to Fetch', 'bon'),
	            'name' => $prefix . 'builder_car_listings_numberposts',
	            'type' => 'text',
	        ),
	        'body_type' => array(
	        	'title' => __('Body Type', 'bon'),
	        	'name' => $prefix . 'builder_car_listings_cat',
	        	'type' => 'select',
	        	'options' => $list_body,
	        ),
	        'dealer_location' => array(
	        	'title' => __('Dealer Location', 'bon'),
	        	'name' => $prefix . 'builder_car_listings_location',
	        	'type' => 'select',
	        	'options' => $list_dealer_loc,
	        ),
	        'car_feature' => array(
	        	'title' => __('Car Feature', 'bon'),
	        	'name' => $prefix . 'builder_car_listings_features',
	        	'type' => 'select',
	        	'options' => $list_car_feature,
	        ),
	        'manufacturer' => array(
	        	'title' => __('Manufacturer', 'bon'),
	        	'name' => $prefix . 'builder_car_listings_manufacturers',
	        	'type' => 'select',
	        	'options' => $list_manufacturer,
	        ),
	        'margin' => array(
	            'title' => __('Bottom Margin', 'bon'),
	            'name' => $prefix . 'builder_car_listings_margin',
	            'std' => '0',
	            'hr' => 'none',
	            'type' => 'text'
	        ),
	        'default_size' => 'span12',
	        'allowed_size' => array(
	                'span12'=>'1/1'
	            ),
		);

		$args['elements']['sales_rep'] = array(

				'header' => array(
		            'title' => __('Title', 'bon'),
		            'name' => $prefix . 'builder_sales_rep_title',
		            'type' => 'text',
		        ),

				'agent_id' => array(
		            'title' => __('Pick Sales Rep', 'bon'),
		            'name' => $prefix . 'builder_sales_rep_id',
		            'type' => 'select',
		            'options' => $salesopt
		        ),
		       	
		       	'color' => array(
		        	'title' => __('Background Color', 'bon'),
		        	'name' => $prefix . 'builder_sales_rep_color',
		        	'type' => 'select',
		        	'options' => array(
		        		'blue' => __('Blue', 'bon'),
		        		'red' => __('Red','bon'),
		        		'green' => __('Green', 'bon'),
		        		'orange' => __('Orange','bon'),
		        	),
		        ),

		        'margin' => array(
		            'title' => __('Bottom Margin', 'bon'),
		            'name' => $prefix . 'builder_sales_rep_margin',
		            'std' => '0',
		            'hr' => 'none',
		            'type' => 'text'
		        ),
		        'default_size' => 'span3',
		        'allowed_size' => array(
		            'span3'=>'1/4',
		            'span4'=>'1/3',
		            'span6'=>'1/2',
		            'span8'=>'2/3',
		            'span9'=>'3/4',
		            'span12'=>'1/1'
		        ),
			);
	}
	


	return $args;
}

add_filter( 'bon_tookit_builder_render_element', 'shandora_render_builder_element', 1, 2 );

function shandora_render_builder_element($type, $value) {
	
	global $bonbuilder;

	switch ($type) {
		
		case 'post_carousel' :
			return shandora_render_builder_element_postcarousel($value);
		break;

		case 'listings' :
			return shandora_render_builder_element_listing($value);
		break;

		case 'agent' :
			return shandora_render_builder_element_agent($value);
		break;

		case 'sales_rep' :
			return shandora_render_builder_element_sales_rep($value);
		break;

		case 'car_listings' :
			return shandora_render_builder_element_car_listing($value);
		break;

	} // end switch
		
}

function shandora_render_builder_element_postcarousel($value) {
	extract($value);

	$o = do_shortcode('[post-carousel title="'.$value['header'].'" button_color="'.$value['button_color'].'" numberposts="'.$value['numberposts'].'"]');
	
	return $o;
}
function shandora_render_builder_element_agent($value) {

	extract($value);
	

	$o = '';

	$prefix = bon_get_prefix();
	$post_id = $agent_id;

	$tw = get_post_meta( $post_id, $prefix . 'agenttw', true );
	$fb = get_post_meta( $post_id, $prefix . 'agentfb', true ); 
	$li = get_post_meta( $post_id, $prefix . 'agentlinkedin', true );
	$img_id = get_post_meta( $post_id, $prefix . 'agentpic', true );
	$img_src = wp_get_attachment_image_src($img_id, 'thumbnail');
	$img_src = $img_src[0];

	$the_title = get_the_title($post_id);

    $o .= '<section class="agent-block '.$color.'">';
    if(!empty($header)) {
		$o .= '<header class="agent-block"><h3>'.$header.'</h3></header>';
	}
	$o .= '<div class="agent-pic"><a href="'.get_permalink( $post_id ).'" title="'.$the_title.'"><img src="'.$img_src.'" alt="'.$the_title.'"/></a></div>';
	$o .= '<h3 class="agent-name">'.$the_title.'</h3>';
	$o .= '<div class="agent-social"><ul>';
	if(!empty($tw)) {
		$o .= '<li><a href="'.$tw.'" title="Twitter"><i class="sha-twitter icon"></i></a></li>';
	}
	if(!empty($fb)) {
		$o .= '<li><a href="'.$fb.'" title="Facebook"><i class="sha-facebook icon"></i></a></li>';
	} 
	if(!empty($li)) {
		$o .= '<li><a href="'.$li.'" title="LinkedIn"><i class="sha-linkedin icon"></i></a></li>';
	} 
	$o .= '</ul></div>';
	$o .= '</section>';
	return $o;
}

function shandora_render_builder_element_sales_rep($value) {
	
	return shandora_render_builder_element_agent($value);
}

function shandora_render_builder_element_listing($value) {

	extract($value);

	$o = '';
	
	if($property_type == 'all') {
		$property_type = '';
	}

	if($property_location == 'all') {
		$property_location = '';
	}

	if($property_feature == 'all') {
		$property_feature = '';
	}

	$meta_property_query = array();

	if(isset($property_query) && $property_query != 'latest') {
		$meta_property_query = array(
									'key' => 'shandora_listing_featured',
									'value' => true,
									'compare' => '=',
								);
	}

	if(isset($property_query) && $property_query !=' latest' ) {
		switch ($property_query) {

			case 'featured':
				$meta_property_query = array(
					'key' => 'shandora_listing_featured',
					'value' => true,
					'compare' => '=',
				);
			break;
			
			case 'none':
				$meta_property_query = array(
					'key' => 'shandora_listing_status',
					'value' => 'none',
					'compare' => '=',
				);
			break;

			case 'for-rent':
				$meta_property_query = array(
					'key' => 'shandora_listing_status',
					'value' => 'for-rent',
					'compare' => '=',
				);
			break;

			case 'for-sale':
				$meta_property_query = array(
					'key' => 'shandora_listing_status',
					'value' => 'for-sale',
					'compare' => '=',
				);
			break;

			case 'reduced':
				$meta_property_query = array(
					'key' => 'shandora_listing_status',
					'value' => 'reduced',
					'compare' => '=',
				);
			break;

			case 'new':
				$meta_property_query = array(
					'key' => 'shandora_listing_status',
					'value' => 'new',
					'compare' => '=',
				);
			break;

			case 'sold':
				$meta_property_query = array(
					'key' => 'shandora_listing_status',
					'value' => 'sold',
					'compare' => '=',
				);
			break;

			case 'on-show':
				$meta_property_query = array(
					'key' => 'shandora_listing_status',
					'value' => 'on-show',
					'compare' => '=',
				);
			break;
		}
	}

	$loop = array(
				'post_type'      => 'listing',
				'posts_per_page' => $numberposts,
				'property-type' => $property_type,
				'property-location' => $property_location,
				'property-feature' => $property_feature,
				'meta_query' => array(
					$meta_property_query
				)
			);

	$mobile = bon_get_option('mobile_layout', '2');
	
	$ul_class = "small-block-grid-".$mobile." large-block-grid-4";


	$compare_page = bon_get_option('compare_page');
	query_posts($loop);

	if ( have_posts() ) :

	$o .= '<div id="listings-container" class="row">';

	$o .= '<div class="' . shandora_column_class('large-12', false) . '">';

	$o .= '<ul class="listings '.$ul_class.'" data-compareurl="'.get_permalink($compare_page).'">';

		while ( have_posts() ) : the_post();

			$status = shandora_get_meta(get_the_ID(), 'listing_status'); 
			$bed = shandora_get_meta(get_the_ID(), 'listing_bed');
			$bath = shandora_get_meta(get_the_ID(), 'listing_bath');
			$lotsize = shandora_get_meta(get_the_ID(), 'listing_lotsize');
			$sizemeasurement = bon_get_option('measurement');
			$currency = bon_get_option('currency');
			
			$o .= '<li>';
			$o .= '<article id="post-'.get_the_ID().'" class="'.bon_entry_class($status, null, false).'" itemscope itemtype="http://schema.org/RealEstateAgent">';

				$o .= '<header class="entry-header"><div class="listing-hover"><span class="mask"></span>';
				$o .= shandora_get_listing_hover_action(get_the_ID());	
				$o .= '</div>';
					
						$terms = get_the_terms( get_the_ID(),"property-type" );
						
						if ( $terms && ! is_wp_error( $terms ) ) 
						{														   														   
							   foreach ( $terms as $term )
							   {															   
									$o .= '<a class="property-type" href="' . get_term_link($term->slug, "property-type" ) .'">'.$term->name.'</a>';
									break; // to display only one property type
							   }														   													   														   
						}
									
					if ( current_theme_supports( 'get-the-image' ) ) $o .= get_the_image( array( 'size' => 'listing_small', 'echo' => false ) );
					$o .= '<div class="badge '.$status.'"><span>';
					$status_opt = shandora_get_search_option('status');
					if($status != 'none') { if(array_key_exists($status, $status_opt)) { $o .= $status_opt[$status]; } }
					$o .= '</span></div>';

				$o .= '</header><!-- .entry-header -->';

				$o .= '<div class="entry-summary">' . apply_atomic_shortcode( 'entry_title', the_title( '<h1 class="entry-title" itemprop="name"><a href="'.get_permalink().'" title="'.the_title_attribute( array('before' => __('Permalink to ','bon'), 'echo' => false) ).'">', '</a></h1>', false ) );
				$o .= '<div class="entry-meta">';
				
				$o .= '<div class="icon bed"><i class="sha-bed"></i><span>';

				if(empty($bed)) { $o .= __('No Bed','bon'); } else { $o .= sprintf( _n('%s Bed','%s Beds', $bed , 'bon'), $bed ); }
				
				$o .= '</span></div>';

				$o .= '<div class="icon bath"><i class="sha-bath"></i><span>';

				if(empty($bath)) { $o .= __('No Bath','bon'); } else { $o .= sprintf(_n('%s Bath','%s Baths', $bath , 'bon'), $bath ); }
				$o .= '</span></div>';

				$o .= '<div class="icon size"><i class="sha-ruler"></i><span>';
				if($lotsize) { $o .= $lotsize . ' ' . $sizemeasurement; } else { $o .= __('Unspecified','bon'); }
				$o .= '</span></div>';

				$o .= '</div></div><!-- .entry-summary -->';

				$o .= '<footer class="entry-footer">';
					
					$price = shandora_get_meta(get_the_ID(), 'listing_price', true); 

				$o .= '<div class="property-price"><a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute( array('before' => __('Permalink to', 'bon'), 'echo' => false) ).'">'. shandora_get_listing_price(false) .'</a></div></footer><!-- .entry-footer -->';

			$o .= '</article></li>';

		endwhile;

	$o .= '</ul></div></div>';

	endif; wp_reset_query();

	return $o;
}

function shandora_render_builder_element_car_listing($value) {

	extract($value);

	$o = '';
	
	if($body_type == 'all') {
		$body_type = '';
	}

	if($dealer_location == 'all') {
		$dealer_location = '';
	}

	if($car_feature == 'all') {
		$car_feature = '';
	}

	if($manufacturer == 'all') {
		$manufacturer = '';
	}

	$car_loop = array(
				'post_type'      => 'car-listing',
				'posts_per_page' => $numberposts,
				'body-type' => $body_type,
				'dealer-location' => $dealer_location,
				'car-feature' => $car_feature,
				'manufacturer' => $manufacturer,
			);
	
	$mobile = bon_get_option('mobile_layout', '2');

	$ul_class = "small-block-grid-".$mobile." large-block-grid-4";


	$compare_page = bon_get_option('compare_page');
	query_posts($car_loop);

	if ( have_posts() ) :

	$o .= '<div id="listings-container" class="row">';

	$o .= '<div class="' . shandora_column_class('large-12', false) . '">';

	$o .= '<ul class="listings '.$ul_class.'" data-compareurl="'.get_permalink($compare_page).'">';

		while ( have_posts() ) : the_post();

			$suffix = 'listing_';
		    $transmission = shandora_get_meta(get_the_ID(), $suffix . 'transmission');
		    $engine = shandora_get_meta(get_the_ID(), $suffix . 'enginesize');
		    $mileage = shandora_get_meta(get_the_ID(), $suffix . 'mileage');
		    $badge = shandora_get_meta(get_the_ID(), $suffix . 'badge');
		    $badgeclr = shandora_get_meta(get_the_ID(), $suffix . 'badge_color');
			$trans_opt = shandora_get_car_search_option('transmission');
		    if(array_key_exists($transmission, $trans_opt)) {
		    	$transmission = $trans_opt[$transmission];
		    }
		    
			$o .= '<li>';
			$o .= '<article id="post-'.get_the_ID().'" class="'.bon_entry_class($badgeclr, null, false).'" itemscope itemtype="http://schema.org/RealEstateAgent">';

				$o .= '<header class="entry-header"><div class="listing-hover"><span class="mask"></span>';
				$o .= shandora_get_listing_hover_action(get_the_ID());	
				$o .= '</div>';
					
						$terms = get_the_terms( get_the_ID(),"body-type" );
						
						if ( $terms && ! is_wp_error( $terms ) ) 
						{														   														   
							   foreach ( $terms as $term )
							   {															   
									$o .= '<a class="body-type property-type" href="' . get_term_link($term->slug, "body-type" ) .'">'.$term->name.'</a>';
									break; // to display only one property type
							   }														   													   														   
						}
									
					if ( current_theme_supports( 'get-the-image' ) ) $o .= get_the_image( array( 'size' => 'listing_small', 'echo' => false ) );
					$o .= '<div class="badge '.$badgeclr.'"><span>';
					if($badge != 'none') { $o .= $badge; }
					$o .= '</span></div>';

				$o .= '</header><!-- .entry-header -->';

				$o .= '<div class="entry-summary">' . apply_atomic_shortcode( 'entry_title', the_title( '<h1 class="entry-title" itemprop="name"><a href="'.get_permalink().'" title="'.the_title_attribute( array('before' => __('Permalink to ','bon'), 'echo' => false) ).'">', '</a></h1>', false ) );
				$o .= '<div class="entry-meta">';
				
				$o .= '<div class="icon engine"><i class="sha-engine"></i><span>';

				if($engine){ $o .= $engine; } else { $o .= __('Unspecified','bon'); }
				
				$o .= '</span></div>';

				$o .= '<div class="icon transmission"><i class="sha-gear-shifter"></i><span>';

				if($transmission){ $o .= $transmission; } else { $o .= __('Unspecified','bon'); }

				$o .= '</span></div>';

				$o .= '<div class="icon mileage"><i class="awe-dashboard"></i><span>';
				if($mileage) { $o .= $mileage; } else { $o .= __('Unspecified','bon'); }
				$o .= '</span></div>';

				$o .= '</div></div><!-- .entry-summary -->';

				$o .= '<footer class="entry-footer">';
					
					$price = shandora_get_meta(get_the_ID(), 'listing_price', true); 

				$o .= '<div class="property-price"><a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute( array('before' => __('Permalink to', 'bon'), 'echo' => false) ).'">'. shandora_get_listing_price(false) .'</a></div></footer><!-- .entry-footer -->';

			$o .= '</article></li>';

		endwhile;

	$o .= '</ul></div></div>';

	endif; wp_reset_query();

	return $o;
}

function shandora_override_contact_form_widget($output, $email) {

	if(empty($email) || !is_email($email)) {
		return __('Failed rendering contact form. Please provide a correct <strong>Email Address</strong>.','bon-toolkit');
	}

	$o = '';
	$o .= '<form class="bon-builder-contact-forms"><div class="contact-form-wrapper">';

    $o .= '<div class="contact-form-input row collapse input-container">';
    $o .= '<div class="column large-2 small-1">';
    $o .= '<span class="attached-label prefix"><i class="sha-user"></i></span>';
    $o .= '</div>';
    $o .= '<div class="column large-10 small-11">';
    $o .= '<input type="text" value="" name="name" class="name attached-input required" placeholder="'.__('Full Name', 'bon').'" />';
    $o .= '<div class="contact-form-error">'.__('Please enter your name.','bon-toolkit').'</div>';
    $o .= '</div>';
    $o .= '</div>';

    $o .= '<div class="contact-form-input row collapse input-container">';
    $o .= '<div class="column large-2 small-1">';
    $o .= '<span class="attached-label prefix"><i class="sha-mail-2"></i></span>';
    $o .= '</div>';
    $o .= '<div class="column large-10 small-11">';
    $o .= '<input type="email" value="" name="email" class="email-address attached-input required" placeholder="'.__('Email Address', 'bon').'" />';
    $o .= '<div class="contact-form-error">'.__('Please enter valid email address.','bon-toolkit').'</div>';
    $o .= '</div>';
    $o .= '</div>';

    $o .= '<div class="contact-form-input row collapse input-container">';
    $o .= '<div class="column large-2 small-1">';
    $o .= '<span class="attached-label prefix"><i class="sha-paperclip"></i></span>';
    $o .= '</div>';
    $o .= '<div class="column large-10 small-11">';
    $o .= '<input type="text" value="" name="subject" class="subject" placeholder="'.__('Subject', 'bon').'" />';
    $o .= '</div>';
    $o .= '</div>';

 	$o .= '<div class="contact-form-input row collapse textarea-container input-container" data-match-height>';
    $o .= '<div data-height-watch class="column large-2 small-1">';
    $o .= '<span class="attached-label prefix"><i class="sha-pencil"></i></span>';
    $o .= '</div>';
    $o .= '<div data-height-watch class="column large-10 small-11">';
    $o .= '<textarea name="messages" class="messages required" placeholder="'.__('Messages', 'bon').'"></textarea>';
    $o .= '<div class="contact-form-error">'.__('Please enter your messages.','bon-toolkit').'</div>';
    $o .= '</div>';
    $o .= '</div>';

    $o .= '<input type="hidden" name="receiver" value="'.$email.'" />';

    $o .= '<div class="contact-form-input">';
    $o .= '<button type="submit" class="contact-form-submit flat button red radius" name="submit">'.__('Send Message','bon-toolkit').'</button>';
    $o .= '<span class="contact-form-ajax-loader"><img src="'.trailingslashit( BON_TOOLKIT_IMAGES ).'loader.gif" alt="loading..." /></span>';
    $o .= '</div>';

    $o .= '</div><div class="sending-result"><div class="green bon-toolkit-alert"></div></div></form>';

    return $o;
}

add_filter('bon_toolkit_contact_form_widget_filter', 'shandora_override_contact_form_widget', 1, 2);
?>