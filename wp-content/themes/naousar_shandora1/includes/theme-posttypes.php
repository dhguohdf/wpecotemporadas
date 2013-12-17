<?php
/**
 * ======================================================================================================
 *
 * Check custom post type supports
 * This function check if theme supports specific custom post type or not if supported
 * register required custom post type
 *
 * @since 1.0
 * @return void
 *
 * ======================================================================================================
 */


add_action('init', 'shandora_setup_listing_post_type', 1);
add_action('init', 'shandora_setup_agent_post_type', 1);


add_action( 'after_setup_theme', 'shandora_add_car_listing',2 );

function shandora_add_car_listing(){
	if(bon_get_option('enable_car_listing') == 'yes') {
		add_action('init', 'shandora_setup_car_dealer_post_type', 1);
		add_action('init', 'shandora_setup_sales_rep_post_type', 1);
	}
}

if( !function_exists('shandora_setup_listing_post_type') ) {

	function shandora_setup_listing_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$suffix = 'listing_';

		$cpt = $bon->cpt();

		$cpt->create('Listing', array( 'supports' => array('editor','title', 'excerpt', 'thumbnail'), 'menu_position' => 6));

		$gallery_opts = array(

			array( 

				'label'	=> __('Listings Gallery', 'bon'),
				'desc'	=> __('Choose image to use in this listing gallery.', 'bon'), 
				'id'	=> $prefix . $suffix . 'gallery',
				'type'	=> 'gallery',
			),

		);

		$prop_options = array(

			array(
				'label'	=> __('MLS Number', 'bon'),
				'desc'	=> __('The property MLS Number #', 'bon'), 
				'id'	=> $prefix . $suffix .'mls',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Property Status', 'bon'),
				'desc'	=> __('The status for the property, used for badge, etc.', 'bon'), 
				'id'	=> $prefix . $suffix . 'status',
				'type'	=> 'select',
				'options' => shandora_get_search_option()
			),

			array( 
				'label'	=> __('For Rent Period', 'bon'),
				'desc'	=> __('Choose the period for the rent. Only show if status is for rent.', 'bon'), 
				'id'	=> $prefix . $suffix . 'period',
				'type'	=> 'select',
				'options' => shandora_get_search_option('period'),
			),

			array(

				'label'	=> __('Address', 'bon'),
				'desc'	=> __('The Property Address.', 'bon'), 
				'id'	=> $prefix . $suffix .'address',
				'type'	=> 'textarea',

			),

			array(
				'label'	=> __('Zip Postal', 'bon'),
				'desc'	=> __('Address Zip Postal', 'bon'), 
				'id'	=> $prefix . $suffix .'zip',
				'type'	=> 'text',
			),

			array(

				'label'	=> __('Price', 'bon'),
				'desc'	=> __('The Property Price. Fill with numeric only, eq: 123456', 'bon'), 
				'id'	=> $prefix . $suffix .'price',
				'type'	=> 'text',

			),

			array( 

				'label'	=> __('Bed Rooms', 'bon'),
				'desc'	=> __('How Many Bedroom? Fill with numeric only', 'bon'), 
				'id'	=> $prefix . $suffix .'bed',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Bath Rooms', 'bon'),
				'desc'	=> __('How Many Bathroom? Fill with numeric only', 'bon'), 
				'id'	=> $prefix . $suffix .'bath',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Garage', 'bon'),
				'desc'	=> __('How Many Garage? Fill with numeric only', 'bon'), 
				'id'	=> $prefix . $suffix .'garage',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Basement', 'bon'),
				'desc'	=> __('How many basement?', 'bon'), 
				'id'	=> $prefix . $suffix .'basement',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Floors / Stories', 'bon'),
				'desc'	=> __('The total floors or stories.', 'bon'), 
				'id'	=> $prefix . $suffix .'floor',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Total Rooms', 'bon'),
				'desc'	=> __('The total rooms. Fill with numeric only', 'bon'), 
				'id'	=> $prefix . $suffix .'totalroom',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Lot Size', 'bon'),
				'desc'	=> __('The Lot Size', 'bon'), 
				'id'	=> $prefix . $suffix .'lotsize',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Building Size', 'bon'),
				'desc'	=> __('The Building Size', 'bon'), 
				'id'	=> $prefix . $suffix .'buildingsize',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Furnishing', 'bon'),
				'desc'	=> __('The Property is Furnished or unfurnised?', 'bon'), 
				'id'	=> $prefix . $suffix .'furnishing',
				'type'	=> 'select',
				'options' => shandora_get_search_option('furnishing')
			),


			array( 

				'label'	=> __('Mortgage Availability', 'bon'),
				'desc'	=> __('The Property is Available for mortgage or not?', 'bon'), 
				'id'	=> $prefix . $suffix .'mortgage',
				'type'	=> 'select',
				'options' => shandora_get_search_option('mortgage')
			),


			array( 

				'label'	=> __('Date of Availability', 'bon'),
				'desc'	=> __('When is the property available?', 'bon'), 
				'id'	=> $prefix . $suffix .'dateavail',
				'type'	=> 'date',
				
			),

			array( 

				'label'	=> __('Year Built', 'bon'),
				'desc'	=> __('When is the property build? eq: 2013', 'bon'), 
				'id'	=> $prefix . $suffix .'yearbuild',
				'type'	=> 'text',
				
			),



			array( 

				'label'	=> __('Map Latitude', 'bon'),
				'desc'	=> __('The Map Latitude. You can easily find it <a href="http://www.itouchmap.com/latlong.html">here</a>. Copy and paste the latitude value generated there', 'bon'), 
				'id'	=> $prefix . $suffix .'maplatitude',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Map Longitude', 'bon'),
				'desc'	=> __('The Map Longitude. You can easily find it <a href="http://www.itouchmap.com/latlong.html">here</a>. Copy and paste the longitude value generated there', 'bon'), 
				'id'	=> $prefix . $suffix .'maplongitude',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Featured Property', 'bon'),
				'desc'	=> __('Make the property featured for featured property widget', 'bon'), 
				'id'	=> $prefix . $suffix .'featured',
				'type'	=> 'checkbox',
				
			),

			array( 

				'label'	=> __('Agent for this listing', 'bon'),
				'desc'	=> __('The agent pointed for this property listing', 'bon'), 
				'id'	=> $prefix . $suffix .'agentpointed',
				'type'	=> 'post_select',
				'post_type' => 'agent', 
				
			),


			
		);

		$video_opts = array(

			array( 

				'label'	=> __('Embed Code', 'bon'),
				'desc'	=> __('Use the third party embed code such Youtube, Vimeo, etc? Please input the embed code', 'bon'), 
				'id'	=> $prefix.'videoembed',
				'type'	=> 'textarea',
			),

			array( 

				'label'	=> __('Use Self Hosted Video', 'bon'),
				'desc'	=> __('Using self uploaded and hosted video', 'bon'), 
				'id'	=> $prefix.'videoself',
				'type'	=> 'checkbox',
				'class' => 'collapsed',
			),


			array(
				'label' => __('M4V Video File', 'framework'),
				'desc' => __('The URL to the .m4v video file (required for use both in html5 / flash player)', 'bon'),
				'type' => 'file',
				'id' => $prefix . 'videom4v',
				'class' => 'hidden',
			),

			array(
				'label' => __('OGV Video File', 'framework'),
				'desc' => __('The URL to the .ogv video file ( HTML5 Only )', 'bon'),
				'type' => 'file',
				'id' => $prefix . 'videoogv',
				'class' => 'hidden',
			),

			array(
				'label' => __('Video Cover', 'framework'),
				'desc' => __('The image use for the video thumbnail', 'bon'),
				'type' => 'image',
				'id' => $prefix . 'videocover',
				'class' => 'hidden last',
			),
			
		);


		$cpt->add_taxonomy("Property Type", array('hierarchical' => true, 'label' => __('Property Types','bon'), 'labels' => array('menu_name' => __('Types','bon') ) ) );

		$cpt->add_taxonomy("Property Location", array('hierarchical' => true, 'label' => __('Property Locations','bon'), 'labels' => array('menu_name' => __('Locations','bon') ) ) );

		$cpt->add_taxonomy("Property Feature", array( 'label' => __('Property Features','bon'), 'labels' => array('menu_name' => __('Features','bon') ) ) );

		$cpt->add_meta_box(   
		    'gallery-options',
		    'Gallery Options',
		    $gallery_opts
		);

		$cpt->add_meta_box(   
		    'property-options',
		    'Property Options',
		    $prop_options  
		);

		$cpt->add_meta_box(   
		    'video-options',
		    'Video Options',
		    $video_opts  
		);
	}

}


if( !function_exists('shandora_setup_car_dealer_post_type') ) {

	function shandora_setup_car_dealer_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$suffix = 'listing_';

		$cpt = $bon->cpt();

		$cpt->create('Car Listing', array('supports' => array('editor','title', 'excerpt', 'thumbnail'), 'menu_position' => 8 ));

		$gallery_opts = array(

			array( 

				'label'	=> __('Listings Gallery', 'bon'),
				'desc'	=> __('Choose image to use in this listing gallery.', 'bon'), 
				'id'	=> $prefix . $suffix . 'gallery',
				'type'	=> 'gallery',
			),

		);

		$prop_options = array(

			array(
				'label'	=> __('Reg Number', 'bon'),
				'desc'	=> __('The Car Registry Number #', 'bon'), 
				'id'	=> $prefix . $suffix .'reg',
				'type'	=> 'text',
			),

			array(
				'label'	=> __('Badge', 'bon'),
				'desc'	=> __('badge text to show in listings view', 'bon'), 
				'id'	=> $prefix . $suffix .'badge',
				'type'	=> 'text',
			),

			array(
				'label'	=> __('Badge color', 'bon'),
				'desc'	=> __('badge text to show in listings view', 'bon'), 
				'id'	=> $prefix . $suffix .'badge_color',
				'type'	=> 'select',
				'options' => array(
						'none' => __('None','bon'),
						'badge-red' => __('Red','bon'),
						'badge-orange' => __('Orange','bon'),
						'badge-green' => __('Green','bon'),
						'badge-blue' => __('Blue','bon'),
						'badge-purple' => __('Purple','bon'),
						'badge-gray' => __('Gray','bon'),
					)
			),


			array(
				'label'	=> __('Car Status', 'bon'),
				'desc'	=> __('Car sale status', 'bon'), 
				'id'	=> $prefix . $suffix .'status',
				'type'	=> 'select',
				'options' => shandora_get_car_search_option('status')
			),


			array(
				'label'	=> __('Mileage', 'bon'),
				'desc'	=> __('Car mileage', 'bon'), 
				'id'	=> $prefix . $suffix .'mileage',
				'type'	=> 'text',
			),

			array(
				'label'	=> __('Exterior Color', 'bon'),
				'desc'	=> __('Car exterior color', 'bon'), 
				'id'	=> $prefix . $suffix .'extcolor',
				'type'	=> 'text',
			),

			array(
				'label'	=> __('Interior Color', 'bon'),
				'desc'	=> __('Car interior color', 'bon'), 
				'id'	=> $prefix . $suffix .'intcolor',
				'type'	=> 'text',
			),

			array(
				'label'	=> __('Fuel Type', 'bon'),
				'desc'	=> __('Car fuel type', 'bon'), 
				'id'	=> $prefix . $suffix .'fueltype',
				'type'	=> 'text',
			),

			array(
				'label'	=> __('Transmission', 'bon'),
				'desc'	=> __('Car transmission', 'bon'), 
				'id'	=> $prefix . $suffix .'transmission',
				'type'	=> 'select',
				'options' => shandora_get_car_search_option('transmission')
			),

			array(

				'label'	=> __('Price', 'bon'),
				'desc'	=> __('The Property Price. Fill with numeric only, eq: 123456', 'bon'), 
				'id'	=> $prefix . $suffix .'price',
				'type'	=> 'text',

			),

			array(
				'label'	=> __('Engine Type', 'bon'),
				'desc'	=> __('Car engine type', 'bon'), 
				'id'	=> $prefix . $suffix .'enginetype',
				'type'	=> 'text',
			),


			array(
				'label'	=> __('Engine Size', 'bon'),
				'desc'	=> __('Car engine size', 'bon'), 
				'id'	=> $prefix . $suffix .'enginesize',
				'type'	=> 'text',
			),

			
			array(

				'label'	=> __('Overall Height', 'bon'),
				'desc'	=> __('The overall car height', 'bon'), 
				'id'	=> $prefix . $suffix .'height',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Overall Width', 'bon'),
				'desc'	=> __('The overall car width', 'bon'), 
				'id'	=> $prefix . $suffix .'width',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Overall Length', 'bon'),
				'desc'	=> __('The overall car length', 'bon'), 
				'id'	=> $prefix . $suffix .'length',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Wheelbase', 'bon'),
				'desc'	=> __('The wheelbase size', 'bon'), 
				'id'	=> $prefix . $suffix .'wheelbase',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Track Front', 'bon'),
				'desc'	=> __('The track front size', 'bon'), 
				'id'	=> $prefix . $suffix .'trackfront',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Track Rear', 'bon'),
				'desc'	=> __('The track front size', 'bon'), 
				'id'	=> $prefix . $suffix .'trackrear',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Ground Clearance', 'bon'),
				'desc'	=> __('The ground clearance size', 'bon'), 
				'id'	=> $prefix . $suffix .'ground',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Standard Seating', 'bon'),
				'desc'	=> __('How many standard seating available', 'bon'), 
				'id'	=> $prefix . $suffix .'seating',
				'type'	=> 'text',

			),

			array(

				'label'	=> __('Steering Type', 'bon'),
				'desc'	=> __('The car steering type', 'bon'), 
				'id'	=> $prefix . $suffix .'steering',
				'type'	=> 'text',

			),

			array(
				'label'	=> __('ANCAP Rating / Safety Rating', 'bon'),
				'desc'	=> __('Australasian New Car Assessment Program Rating. see http://ancap.com.au', 'bon'), 
				'id'	=> $prefix . $suffix .'ancap',
				'type'	=> 'slider',
				'step' => '1',
				'min' => '0',
				'max' => '5'
			),


			array( 

				'label'	=> __('Year Built', 'bon'),
				'desc'	=> __('When is the car year build? eq: 2013', 'bon'), 
				'id'	=> $prefix . $suffix .'yearbuild',
				'type'	=> 'text',
			),


			array( 

				'label'	=> __('Featured Car', 'bon'),
				'desc'	=> __('Make the listing featured for featured listing widget', 'bon'), 
				'id'	=> $prefix . $suffix .'featured',
				'type'	=> 'checkbox',
				
			),

			array( 

				'label'	=> __('Sales Representative for this listing', 'bon'),
				'desc'	=> __('The sales rep pointed for this car listing', 'bon'), 
				'id'	=> $prefix . $suffix .'agentpointed',
				'type'	=> 'post_select',
				'post_type' => 'sales-representative', 
				
			),

			
		);

		$video_opts = array(

			array( 

				'label'	=> __('Embed Code', 'bon'),
				'desc'	=> __('Use the third party embed code such Youtube, Vimeo, etc? Please input the embed code', 'bon'), 
				'id'	=> $prefix.'videoembed',
				'type'	=> 'textarea',
			),

			array( 

				'label'	=> __('Use Self Hosted Video', 'bon'),
				'desc'	=> __('Using self uploaded and hosted video', 'bon'), 
				'id'	=> $prefix.'videoself',
				'type'	=> 'checkbox',
				'class' => 'collapsed',
			),


			array(
				'label' => __('M4V Video File', 'framework'),
				'desc' => __('The URL to the .m4v video file (required for use both in html5 / flash player)', 'bon'),
				'type' => 'file',
				'id' => $prefix . 'videom4v',
				'class' => 'hidden',
			),

			array(
				'label' => __('OGV Video File', 'framework'),
				'desc' => __('The URL to the .ogv video file ( HTML5 Only )', 'bon'),
				'type' => 'file',
				'id' => $prefix . 'videoogv',
				'class' => 'hidden',
			),

			array(
				'label' => __('Video Cover', 'framework'),
				'desc' => __('The image use for the video thumbnail', 'bon'),
				'type' => 'image',
				'id' => $prefix . 'videocover',
				'class' => 'hidden last',
			),

			
		);


		$cpt->add_taxonomy("Manufacturer", array('hierarchical' => true ) );

		$cpt->add_taxonomy("Body Type", array('hierarchical' => true ) );

		$cpt->add_taxonomy("Dealer Location", array('hierarchical' => true ) );

		$cpt->add_taxonomy("Car Feature", array( 'label' => __('Car Features','bon'), 'labels' => array('menu_name' => __('Features','bon') ) ) );

		$cpt->add_meta_box(   
		    'gallery-options',
		    'Gallery Options',
		    $gallery_opts
		);

		$cpt->add_meta_box(   
		    'car-options',
		    'Detail Options',
		    $prop_options  
		);

		$cpt->add_meta_box(   
		    'video-options',
		    'Video Options',
		    $video_opts  
		);
	}

}


if( !function_exists('shandora_setup_agent_post_type') ) {

	function shandora_setup_agent_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$cpt = $bon->cpt();

		$cpt->create('Agent', array('supports' => array('editor', 'title') ,'exclude_from_search' => true, 'menu_position' => 7 ));


		$agent_opt1 = array(

			array( 
				'label'	=> __('Facebook Username', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentfb',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Twitter Username', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agenttw',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('LinkedIn Username', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentlinkedin',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Agent Profile Photo', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentpic',
				'type'	=> 'image',
			),

			array( 
				'label'	=> __('Email Address', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentemail',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Office Phone Number', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentofficephone',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Mobile Phone Number', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentmobilephone',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Fax Number', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentfax',
				'type'	=> 'text',
			),

			
			
		);


		$cpt->add_meta_box(   
		    'agent-options',
		    'Agent Options',
		    $agent_opt1  
		);

	
	}

}

if( !function_exists('shandora_setup_sales_rep_post_type') ) {

	function shandora_setup_sales_rep_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$cpt = $bon->cpt();

		$cpt->create('Sales Representative', array('supports' => array('editor', 'title') , 'exclude_from_search' => true, 'menu_position' => 9 ));


		$agent_opt1 = array(

			array( 
				'label'	=> __('Facebook Username', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentfb',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Twitter Username', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agenttw',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('LinkedIn Username', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentlinkedin',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Agent Profile Photo', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentpic',
				'type'	=> 'image',
			),

			array( 
				'label'	=> __('Email Address', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentemail',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Office Phone Number', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentofficephone',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Mobile Phone Number', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentmobilephone',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Fax Number', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentfax',
				'type'	=> 'text',
			),

			
			
		);


		$cpt->add_meta_box(   
		    'agent-options',
		    'Agent Options',
		    $agent_opt1  
		);

	
	}

}

add_action( 'init', 'shandora_page_meta');
if( !function_exists('shandora_page_meta') ) {

	function shandora_page_meta() {
		if(is_admin()) {
			global $bon;

			$mb = $bon->mb();

			$fields = array(
				array(
					'id' => 'shandora_status_query',
					'type' => 'select',
					'label' => __('Property Status to Query','bon'),
					'options' => shandora_get_search_option('status')
				)
			);

			$mb->create_box('status-opt', __('Property Status', 'bon'), $fields, array('page'));
		}
	}

}

?>
