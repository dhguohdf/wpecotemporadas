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
 * 321: agent > perfil-eco
 * ======================================================================================================
 */



add_action( 'after_setup_theme', 'shandora_add_post_type', 2 );

function shandora_add_post_type(){

	if(bon_get_option('enable_property_listing', 'yes') == 'yes') {
		add_action('init', 'shandora_setup_listing_post_type', 1);
		add_action('init', 'shandora_setup_agent_post_type', 1);
	}

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

		$use_rewrite = bon_get_option( 'use_rewrite', 'no' );

		$settings = array();
		$slug = '';

		$settings['rewrite_root'] = bon_get_option( 'rewrite_root' );
		$settings['realestate_root'] = bon_get_option( 'realestate_root', 'real-estate' );

		$settings['realestate_property_type_root'] = bon_get_option( 'realestate_property_type_root', 'manufacturer' );
		$settings['realestate_property_location_root'] = bon_get_option( 'realestate_property_location_root', 'body-type' );
		$settings['realestate_property_feature_root'] = bon_get_option( 'realestate_property_feature_root', 'dealer-location' );


		


		if( !empty( $settings['rewrite_root'] ) ) {
			$slug = "{$settings['rewrite_root']}/{$settings['realestate_root']}";
		} else {
			$slug = "{$settings['realestate_root']}";
		}

		$property_type_slug = "{$settings['realestate_root']}/{$settings['realestate_property_type_root']}";
		$property_location_slug = "{$settings['realestate_root']}/{$settings['realestate_property_location_root']}";
		$property_feature_slug = "{$settings['realestate_root']}/{$settings['realestate_property_feature_root']}";

		$has_archive = ( $use_rewrite == 'no' ) ? false : $slug;

		$rewrite_var = array(
				'slug'       => $slug,
				'with_front' => false,
				'pages'      => true,
				'feeds'      => true,
				'ep_mask'    => EP_PERMALINK,
			);
		
		$rewrite = ( $use_rewrite == 'no' ) ? true : $rewrite_var;



		$name = __('Anuncio', 'bon');
		$plural = __('Anuncios', 'bon');
		$labels = array(
			'name' 					=> _x( $plural, 'post type general name' ),
			'singular_name' 		=> _x( $name, 'post type singular name' ),
			'add_new' 				=> _x( 'Add New', strtolower( $name ) ),
			'add_new_item' 			=> sprintf(__( 'Add New %s', 'bon' ), $name ),
			'edit_item' 			=> sprintf(__( 'Edit %s', 'bon' ), $name ),
			'new_item' 				=> sprintf(__( 'New %s', 'bon' ), $name ),
			'all_items' 			=> sprintf(__( 'All %s', 'bon' ), $plural ),
			'view_item' 			=> sprintf(__( 'View %s', 'bon' ), $name ),
			'search_items' 			=> sprintf(__( 'Search %s', 'bon' ), $plural),
			'not_found' 			=> sprintf(__( 'No %s found', 'bon' ), strtolower( $plural )),
			'not_found_in_trash' 	=> sprintf(__( 'No %s found in Trash', 'bon' ), strtolower( $plural ) ), 
			'parent_item_colon' 	=> '',
			'menu_name' 			=> $plural
		);

		$cpt->create('Listing', array( 'has_archive' => $has_archive, 'rewrite' => $rewrite, 'labels' => $labels, 'supports' => array('editor','title', 'excerpt', 'thumbnail'), 'menu_position' => 6));

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

				'label'	=> __('Price as Text', 'bon'),
				'desc'	=> __('Set price to use text. Text Options can be filled in theme Options, Shandora > Listing Settings > Price as Text.', 'bon'), 
				'id'	=> $prefix . $suffix .'pricetext',
				'type'	=> 'checkbox',

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
				'post_type' => 'perfil-eco', 
				
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
		

		/* The rewrite handles the URL structure. */
		$property_type_rewrite_var = array(
			'slug'         => $property_type_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		
		/* The rewrite handles the URL structure. */
		$property_location_rewrite_var = array(
			'slug'         => $property_location_slug,
			'with_front'   => false,
			'hierarchical' => true,
			'ep_mask'      => EP_NONE
		);

		/* The rewrite handles the URL structure. */
		$property_feature_rewrite_var = array(
			'slug'         => $property_feature_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		if( $use_rewrite == 'no' ) {

			$property_feature_rewrite = true;
			$property_location_rewrite = true;
			$property_type_rewrite = true;

		} else {

			$property_feature_rewrite = $property_feature_rewrite_var;
			$property_location_rewrite = $property_location_rewrite_var;
			$property_type_rewrite = $property_type_rewrite_var;

		}

		$cpt->add_taxonomy("Property Type", array( 'rewrite' => $property_type_rewrite, 'hierarchical' => true, 'label' => __('Property Types','bon'), 'labels' => array('menu_name' => __('Types','bon') ) ) );

		$cpt->add_taxonomy("Property Location", array( 'rewrite' => $property_location_rewrite, 'hierarchical' => true, 'label' => __('Property Locations','bon'), 'labels' => array('menu_name' => __('Locations','bon') ) ) );

		$cpt->add_taxonomy("Property Feature", array( 'rewrite' => $property_feature_rewrite, 'label' => __('Property Features','bon'), 'labels' => array('menu_name' => __('Features','bon') ) ) );

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


		$use_rewrite = bon_get_option( 'use_rewrite', 'no' );

		$settings = array();
		$slug = '';

		$settings['rewrite_root'] = bon_get_option( 'rewrite_root' );
		$settings['car_root'] = bon_get_option( 'car_root', 'car' );

		$settings['car_manufacturer_root'] = bon_get_option( 'car_manufacturer_root', 'manufacturer' );
		$settings['car_body_type_root'] = bon_get_option( 'car_body_type_root', 'body-type' );
		$settings['car_dealer_location_root'] = bon_get_option( 'car_dealer_location_root', 'dealer-location' );
		$settings['car_feature_root'] = bon_get_option( 'car_feature_root', 'feature' );


		if( !empty( $settings['rewrite_root'] ) ) {
			$slug = "{$settings['rewrite_root']}/{$settings['car_root']}";
		} else {
			$slug = "{$settings['car_root']}";
		}

		$manufacturer_slug = "{$settings['car_root']}/{$settings['car_manufacturer_root']}";
		$body_type_slug = "{$settings['car_root']}/{$settings['car_body_type_root']}";
		$dealer_location_slug = "{$settings['car_root']}/{$settings['car_dealer_location_root']}";
		$feature_slug = "{$settings['car_root']}/{$settings['car_feature_root']}";

		$has_archive = ( $use_rewrite == 'no' ) ? false : $slug;

		$rewrite_var = array(
			'slug'       => $slug,
			'with_front' => false,
			'pages'      => true,
			'feeds'      => true,
			'ep_mask'    => EP_PERMALINK,
		);

		$rewrite = ( $use_rewrite == 'no' ) ? true : $rewrite_var;

			$name = __('Car Listing', 'bon');
			$plural = __('Car Listings', 'bon');
			$labels = array(
				'name' 					=> _x( $plural, 'post type general name' ),
				'singular_name' 		=> _x( $name, 'post type singular name' ),
				'add_new' 				=> _x( 'Add New', strtolower( $name ) ),
				'add_new_item' 			=> sprintf(__( 'Add New %s', 'bon' ), $name ),
				'edit_item' 			=> sprintf(__( 'Edit %s', 'bon' ), $name ),
				'new_item' 				=> sprintf(__( 'New %s', 'bon' ), $name ),
				'all_items' 			=> sprintf(__( 'All %s', 'bon' ), $plural ),
				'view_item' 			=> sprintf(__( 'View %s', 'bon' ), $name ),
				'search_items' 			=> sprintf(__( 'Search %s', 'bon' ), $plural),
				'not_found' 			=> sprintf(__( 'No %s found', 'bon' ), strtolower( $plural )),
				'not_found_in_trash' 	=> sprintf(__( 'No %s found in Trash', 'bon' ), strtolower( $plural ) ), 
				'parent_item_colon' 	=> '',
				'menu_name' 			=> $plural
			);

		$cpt->create('Car Listing', array( 'has_archive' => $has_archive, 'rewrite' => $rewrite, 'labels' => $labels, 'supports' => array('editor','title', 'excerpt', 'thumbnail'), 'menu_position' => 8 ));

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

				'label'	=> __('Price as Text', 'bon'),
				'desc'	=> __('Set price to use text. Text Options can be filled in theme Options, Shandora > Listing Settings > Price as Text.', 'bon'), 
				'id'	=> $prefix . $suffix .'pricetext',
				'type'	=> 'checkbox',

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
		

		/* The rewrite handles the URL structure. */
		$manufacturer_rewrite_var = array(
			'slug'         => $manufacturer_slug,
			'with_front'   => false,
			'hierarchical' => true,
			'ep_mask'      => EP_NONE
		);

		
		/* The rewrite handles the URL structure. */
		$body_type_rewrite_var = array(
			'slug'         => $body_type_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		/* The rewrite handles the URL structure. */
		$dealer_location_rewrite_var = array(
			'slug'         => $dealer_location_slug,
			'with_front'   => false,
			'hierarchical' => true,
			'ep_mask'      => EP_NONE
		);

		/* The rewrite handles the URL structure. */
		$feature_rewrite_var = array(
			'slug'         => $feature_slug,
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		);

		if( $use_rewrite == 'no' ) {

			$feature_rewrite = true;
			$dealer_location_rewrite = true;
			$body_type_rewrite = true;
			$manufacturer_rewrite = true;

		} else {

			$feature_rewrite = $feature_rewrite_var;
			$dealer_location_rewrite = $dealer_location_rewrite_var;
			$body_type_rewrite = $body_type_rewrite_var;
			$manufacturer_rewrite = $manufacturer_rewrite_var;

		}

		$cpt->add_taxonomy("Manufacturer", array( 'rewrite' => $manufacturer_rewrite, 'label' => __('Manufacturers','bon'), 'labels' => array('menu_name' => __('Manufacturers','bon') ), 'hierarchical' => true ) );

		$cpt->add_taxonomy("Body Type", array( 'rewrite' => $body_type_rewrite, 'label' => __('Body Types','bon'), 'labels' => array('menu_name' => __('Body Types','bon') ), 'hierarchical' => true ) );

		$cpt->add_taxonomy("Dealer Location", array( 'rewrite' => $dealer_location_rewrite, 'label' => __('Dealer Locations','bon'), 'labels' => array('menu_name' => __('Dealer Locations','bon') ),  'hierarchical' => true ) );

		$cpt->add_taxonomy("Car Feature", array( 'rewrite' => $feature_rewrite, 'label' => __('Car Features','bon'), 'labels' => array('menu_name' => __('Features','bon') ) ) );

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

		$name = __('Perfil ECO', 'bon');
		$plural = __('Perfis ECO', 'bon');
		$labels = array(
			'name' 					=> _x( $plural, 'post type general name' ),
			'singular_name' 		=> _x( $name, 'post type singular name' ),
			'add_new' 				=> _x( 'Add New', strtolower( $name ) ),
			'add_new_item' 			=> sprintf(__( 'Add New %s', 'bon' ), $name ),
			'edit_item' 			=> sprintf(__( 'Edit %s', 'bon' ), $name ),
			'new_item' 				=> sprintf(__( 'New %s', 'bon' ), $name ),
			'all_items' 			=> sprintf(__( 'All %s', 'bon' ), $plural ),
			'view_item' 			=> sprintf(__( 'View %s', 'bon' ), $name ),
			'search_items' 			=> sprintf(__( 'Search %s', 'bon' ), $plural),
			'not_found' 			=> sprintf(__( 'No %s found', 'bon' ), strtolower( $plural )),
			'not_found_in_trash' 	=> sprintf(__( 'No %s found in Trash', 'bon' ), strtolower( $plural ) ), 
			'parent_item_colon' 	=> '',
			'menu_name' 			=> $plural
		);

		$cpt->create('Perfil ECO', array( 'labels' => $labels, 'supports' => array('editor', 'title' ) ,'exclude_from_search' => true, 'menu_position' => 7 ));


		$agent_opt1 = array(

			array( 
				'label'	=> __('Job Title', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentjob',
				'type'	=> 'text',
			),

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

		$name = __('Sales Representative', 'bon');
		$plural = __('Sales Representatives', 'bon');
		$labels = array(
			'name' 					=> _x( $plural, 'post type general name' ),
			'singular_name' 		=> _x( $name, 'post type singular name' ),
			'add_new' 				=> _x( 'Add New', strtolower( $name ) ),
			'add_new_item' 			=> sprintf(__( 'Add New %s', 'bon' ), $name ),
			'edit_item' 			=> sprintf(__( 'Edit %s', 'bon' ), $name ),
			'new_item' 				=> sprintf(__( 'New %s', 'bon' ), $name ),
			'all_items' 			=> sprintf(__( 'All %s', 'bon' ), $plural ),
			'view_item' 			=> sprintf(__( 'View %s', 'bon' ), $name ),
			'search_items' 			=> sprintf(__( 'Search %s', 'bon' ), $plural),
			'not_found' 			=> sprintf(__( 'No %s found', 'bon' ), strtolower( $plural )),
			'not_found_in_trash' 	=> sprintf(__( 'No %s found in Trash', 'bon' ), strtolower( $plural ) ), 
			'parent_item_colon' 	=> '',
			'menu_name' 			=> $plural
		);

		$cpt->create('Sales Representative', array( 'labels' => $labels, 'supports' => array('editor', 'title') , 'exclude_from_search' => true, 'menu_position' => 9 ));


		$agent_opt1 = array(

			array( 
				'label'	=> __('Job Title', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentjob',
				'type'	=> 'text',
			),

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

		$prefix = bon_get_prefix();

		if(is_admin()) {

			global $bon;

			$mb = $bon->mb();

			$opts = shandora_get_search_option( 'status' );
			$opts['featured'] = __('Featured', 'bon');

			$fields = array(
				array(
					'id' => 'shandora_status_query',
					'type' => 'select',
					'label' => __('Property Status to Query','bon'),
					'options' => $opts
				)
			);

			$fields = array(

				array(
					'id' => $prefix . 'slideshow_type',
					'type' => 'select',
					'label' => __('Slide Show Type', 'bon'),
					'options' => array(
						'full' => __('Full', 'bon'),
						'boxed' => __('Boxed', 'bon')
					)
				),


				array(
					'id' => $prefix . 'slideshow_ids',
					'type' => 'text',
					'label' => __('Slide show IDs to Show', 'bon'),
					'desc' => __('Input the slideshow ids you want to show separated by commas', 'bon')
				),

			);

			$mb->create_box('slider-opt', __('Slider Options', 'bon'), $fields, array('page'));
		}
	}
}

add_action('init', 'shandora_property_page_meta' );
if( !function_exists( 'shandora_property_page_meta' ) ) {
	function shandora_property_page_meta() {
		if(is_admin()) {

			global $bon;

			$mb = $bon->mb();

			$opts = shandora_get_search_option( 'status' );
			$opts['featured'] = __('Featured', 'bon');

			$fields = array(
				array(
					'id' => 'shandora_status_query',
					'type' => 'select',
					'label' => __('Property Status to Query','bon'),
					'options' => $opts
				)
			);

			$mb->create_box('status-opt', __('Property Status', 'bon'), $fields, array('page'));

		}
	}
}

add_action( 'init', 'shandora_car_page_meta' );
if( !function_exists('shandora_car_page_meta') ) {
	function shandora_car_page_meta() {
		if(is_admin()) {
			global $bon;

			$mb = $bon->mb();

			$opts = shandora_get_car_search_option( 'status' );
			$opts['featured'] = __('Featured', 'bon');

			$fields = array(
				array(
					'id' => 'shandora_car_status_query',
					'type' => 'select',
					'label' => __('Car Status to Query','bon'),
					'options' => $opts
				)
			);

			$mb->create_box('car-status-opt', __('Car Status', 'bon'), $fields, array('page'));
		}
	}
}
?>
