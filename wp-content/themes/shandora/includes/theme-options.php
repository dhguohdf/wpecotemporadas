<?php

/**
 * =====================================================================================================
 *
 * Setting Up the main theme options array
 * 
 * @since 1.0
 * @return array()
 *
 * ======================================================================================================
 */

if( !function_exists('bon_set_theme_options') ) {

	function bon_set_theme_options() {

		//Stylesheets Reader
		$alt_stylesheet_path = get_template_directory() . '/assets/css/colors/';
		$alt_stylesheets = array();
		if ( is_dir($alt_stylesheet_path) ) {
		    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) {
		        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
		            if(stristr($alt_stylesheet_file, '.css') !== false) {
		            	$stylesheet = str_replace('.css', '', $alt_stylesheet_file);
		                $alt_stylesheets[$stylesheet] = trailingslashit( BON_THEME_URI ) . 'assets/images/colors/'.$stylesheet.'.png';
		            }
		        }
		    }
		}

		$color_options = array(
			'blue' => __('Blue', 'bon'),
			'red' => __('Red', 'bon'),
			'green' => __('Green', 'bon'),
			'orange' => __('Orange', 'bon'),
			'purple' => __('Purple', 'bon'),
		);
		
		$search_fields = array(
			'mls' => __('MLS Text Field', 'bon'),
			'zip' => __('Zip Text Field', 'bon'),
			'status' => __('Status Dropdown', 'bon'),
			'location' => __('Location Dropdown', 'bon'),
			'location_level1' => __('Level 1 Location Only', 'bon'),
			'location_level2' => __('Level 2 Location Only', 'bon'),
			'location_level3' => __('Level 3 Location Only', 'bon'),
			'feature' => __('Feature Dropdown', 'bon'),
			'lotsize' => __('Lot Size Slider', 'bon'),
			'buildingsize' => __('Building Size Slider', 'bon'),
			'floor' => __('Floor Slider', 'bon'),
			'agent' => __('Agent Dropdown', 'bon'),
			'garage' => __('Garage Slider', 'bon'),
			'basement' => __('Basement Slider', 'bon'),
			'mortgage' => __('Mortgage Availability Dropdown', 'bon'),
			'type' => __('Property Type Dropdown', 'bon'),
			'price' => __('Price Range Slider', 'bon'),
			'bed' => __('Beds Slider', 'bon'),
			'bath' => __('Baths Slider', 'bon'),
		);

		$car_search_fields = array(
			'reg' => __('Reg Number Field **Car Listing**', 'bon'),
			'car_status' => __('Car Status Dropdown **Car Listing**','bon'),
			'mileage' => __('Mileage Slider **Car Listing**', 'bon'),
			'exterior_color' => __('Exterior Color Field **Car Listing**','bon'),
			'interior_color' => __('Interior Color Field **Car Listing**', 'bon'),
			'fuel_type' => __('Fuel Type Field **Car Listing**', 'bon'),
			'transmission' => __('Transmission Dropdown **Car Listing**', 'bon'),
			'car_price' => __('Price Range Slider **Car Listing**','bon'),
			'ancap' => __('ANCAP or Safety Slider **Car Listing**','bon'),
			'dealer_location' => __('Dealer Location Dropdown **Car Listing**', 'bon'),
			'car_feature' => __('Car Feature Dropdown **Car Listing**', 'bon'),
			'body_type' => __('Body Type Dropdown **Car Listing**', 'bon'),
			'manufacturer' => __('Manufacturer Dropdown **Car Listing**', 'bon'),
			'manufacturer_level1' => __('Manufacturer Dropdown Level 1 **Car Listing**', 'bon'),
			'manufacturer_level2' => __('Manufacturer Dropdown Level 2 **Car Listing**', 'bon'),
			'manufacturer_level3' => __('Manufacturer Dropdown Level 3 **Car Listing**', 'bon')
		);

		if(bon_get_option('enable_car_listing') == 'yes') {
			$search_fields = array_merge($search_fields, $car_search_fields);
		}
		

		$layouts = get_theme_support( 'theme-layouts' );
		$args = theme_layouts_get_args();

		/* Set up an array for the layout choices and add in the 'default' layout. */
		$layout_choices = array();

		/* Only add 'default' if it's the actual default layout. */
		if ( 'default' == $args['default'] )
			$layout_choices['default'] = theme_layouts_get_string( 'default' );

		/* Loop through each of the layouts and add it to the choices array with proper key/value pairs. */
		foreach ( $layouts[0] as $layout ) {
			$layout_choices[$layout] = theme_layouts_get_image_string( $layout );
			if($layout != '1c') {
				$layout_choices_2[$layout] = theme_layouts_get_image_string( $layout );
			}
		}
			

		
		// More Options
		$slide_options = array();
		$total_possible_slides = 10;
		for ( $i = 1; $i <= $total_possible_slides; $i++ ) { $slide_options[] = $i; }

		// Setup an array of numbers.
		$numbers = array();
		for ( $i = 1; $i <= 20; $i++ ) {
		    $numbers[$i] = $i;
		}

		// Pull all the categories into an array
		$options_categories = array();
		$options_categories_obj = get_categories();
		foreach ($options_categories_obj as $category) {
			$options_categories[$category->cat_ID] = $category->cat_name;
		}
		
		// Pull all tags into an array
		$options_tags = array();
		$options_tags_obj = get_tags();
		foreach ( $options_tags_obj as $tag ) {
			$options_tags[$tag->term_id] = $tag->name;
		}


		// Pull all the pages into an array
		$options_pages = array();
		$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
		$options_pages[''] = 'Select a page:';
		foreach ($options_pages_obj as $page) {
			$options_pages[$page->ID] = $page->post_title;
		}


		$options = array();
		/* General */


		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'General Settings', 'bon' ),
		    				'type' => 'heading',
		    				'icon' => 'general' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Quick Start', 'bon' ),
		    				'type' => 'subheading' );

		
		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Custom Logo', 'bon' ),
		    				'desc' => __( 'Upload a logo for your theme, or specify an image URL directly. The best size is <strong>270x80 px</strong>', 'bon' ),
		    				'id' => 'logo',
		    				'std' => '',
		    				'type' => 'upload' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Custom Favicon', 'bon' ),
		    				'desc' => __( 'Upload a Favicon', 'bon' ),
		    				'id' => 'favicon',
		    				'std' => '',
		    				'type' => 'upload' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Slider Posts Limit', 'bon' ),
		    				'desc' => __( 'How many slideshow to show?', 'bon' ),
		    				'id' => 'slider_post_per_page',
		    				'std' => '',
		    				'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Show BreadCrumb', 'bon' ),
		    				'desc' => __( 'Show or hide breadcrumb page header', 'bon' ),
		    				'id' => 'show_page_header',
		    				'std' => 'show',
		    				'options' => array(
		    					'show' => __('Show','bon'),
		    					'hide' => __('Hide', 'bon')
		    				),
		    				'type' => 'select' );


		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Tracking Code', 'bon' ),
		    				'desc' => __( 'Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.', 'bon' ),
		    				'id' => 'google_analytics',
		    				'std' => '',
		    				'class' => 'code_mirror',
		    				'type' => 'textarea' );
		    				

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Display Options', 'bon' ),
		    				'type' => 'subheading' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Custom CSS', 'bon' ),
		    				'desc' => __( 'Quickly add some CSS to your theme by adding it to this block. Do not use &lt;style&gt; tag', 'bon' ),
		    				'id' => 'custom_css',
		    				'std' => '',
		    				'class' => 'code_mirror',
		    				'type' => 'textarea' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Custom JS', 'bon' ),
		    				'desc' => __( 'Quickly add some Javascript to your theme by adding it to this block. Do not use &lt;script&gt; tag ', 'bon' ),
		    				'id' => 'custom_js',
		    				'std' => '',
		    				'class' => 'code_mirror',
		    				'type' => 'textarea' );

		/**
		 * =====================================================================================================
		 *
		 * Header Settings
		 * 
		 * @category Header
		 *
		 * ======================================================================================================
		 */

		$options[] = array( 'slug' => 'bon_options', 
							'label' => __( 'Header Settings', 'bon' ),
		    				'type' => 'heading',
		    				'icon' => 'header' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => '',
		                    'desc' => '',
		                    'std' =>  __( 'This section will handle the columns in the header, the area beside logo. The "Phone Number" Group and the "Address Group"', 'bon' ),
		                    'type' => 'info' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Header Column 1 Title', 'bon' ),
		                    'desc' => __( 'The title for header group 1. eq. <strong>Need help from us? Feel free to call us</strong>', 'bon' ),
		                    'id' => 'hgroup1_title',
		                    'std' =>  '',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Phone Number', 'bon' ),
		                    'desc' => __( 'The phone number eq. <strong>123-456-789-01</strong>', 'bon' ),
		                    'id' => 'hgroup1_content',
		                    'std' =>  '',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Header Column 2 Title', 'bon' ),
		                    'desc' => __( 'The title for header group 2. eq. <strong>Want to Meet & Talk Directly? Find us here</strong>', 'bon' ),
		                    'id' => 'hgroup2_title',
		                    'std' =>  '',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Address', 'bon' ),
		                    'desc' => __( 'The address eq. <strong>999 Folsom Avenue, Suite 1111 - Los Angeles, CA</strong>', 'bon' ),
		                    'id' => 'hgroup2_line1',
		                    'std' =>  '',
		                    'type' => 'text' ); 

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Opening Hours', 'bon' ),
		                    'desc' => __( 'The opening hour eq. <strong>Monday - Saturday (9AM - 5PM)</strong>', 'bon' ),
		                    'id' => 'hgroup2_line2',
		                    'std' =>  '',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Header Search Field', 'bon' ),
		                    'desc' => __( 'Show the search field in the header / menu.', 'bon' ),
		                    'id' => 'show_header_search',
		                    'std' =>  'yes',
		                    'options' => array(
		                    	'yes' => __('Yes', 'bon'),
		                    	'no' => __('No', 'bon'),
		                    ),
		                    'type' => 'select' );                    


		/**
		 * =====================================================================================================
		 *
		 * Color Settings
		 * 
		 * @category Color
		 *
		 * ======================================================================================================
		 */

		$options[] = array( 'slug' => 'bon_options', 
							'label' => __( 'Color Settings', 'bon' ),
		    				'type' => 'heading',
		    				'icon' => 'color' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Main Color Style', 'bon' ),
		                    'desc' => __('Choose colorization stylesheet.','bon'),
		                    'id' => 'main_color_style',
		                    'type' => 'radio-img',
		                    'options' => $alt_stylesheets,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Main header color Style', 'bon' ),
		                    'desc' => __('Choose main header style.','bon'),
		                    'id' => 'main_header_style',
		                    'type' => 'select',
		                    'options' => array(
		                    	'dark' => __('Dark','bon'),
		                    	'light' => __('Light','bon')
		                    	),
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Main header navigation color Style', 'bon' ),
		                    'desc' => __('Choose main header navigation style.','bon'),
		                    'id' => 'main_header_nav_style',
		                    'type' => 'select',
		                    'options' => array(
		                    	'dark' => __('Dark','bon'),
		                    	'light' => __('Light','bon')
		                    	),
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Search Listing Button Color', 'bon' ),
		                    'desc' => __('Choose colorization stylesheet.','bon'),
		                    'id' => 'search_button_color',
		                    'type' => 'select',
		                    'options' => $color_options,
		                    );


		/**
		 * =====================================================================================================
		 *
		 * Listing Settings
		 * 
		 * @category Listing
		 *
		 * ======================================================================================================
		 */

		$options[] = array( 'slug' => 'bon_options', 
							'label' => __( 'Listing Settings', 'bon' ),
		    				'type' => 'heading',
		    				'icon' => 'listing' );


		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'General Options', 'bon' ),
		    				'type' => 'subheading' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Search Panel', 'bon' ),
		                    'desc' => __('If enable there will show the main search panel in all listing related page','bon'),
		                    'id' => 'enable_search_panel',
		                    'type' => 'select',
		                    'options' => array(
		                    	'yes' => __('Yes','bon'),
		                    	'no' => __('No','bon')
		                    	),
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Enable Car Dealership Listing', 'bon' ),
		                    'desc' => __('If enable there will be new menu in admin','bon'),
		                    'id' => 'enable_car_listing',
		                    'type' => 'select',
		                    'options' => array(
		                    	'yes' => __('Yes','bon'),
		                    	'no' => __('No','bon')
		                    	),
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Compare Page', 'bon' ),
		                    'desc' => __('Page where the listing compare result will be.','bon'),
		                    'id' => 'compare_page',
		                    'type' => 'select',
		                    'options' => $options_pages,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Search Result Page', 'bon' ),
		                    'desc' => __('Page where the listing search result will be.','bon'),
		                    'id' => 'search_listing_page',
		                    'type' => 'select',
		                    'options' => $options_pages,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Number of Listings to Show', 'bon' ),
		                    'desc' => __('How many of listing you want to show for search listing page and browse listing page.','bon'),
		                    'id' => 'listing_per_page',
		                    'type' => 'text',
		                    'std' => '8',
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Map', 'bon' ),
		                    'desc' => __('Show map browse on listings page.','bon'),
		                    'id' => 'show_listings_map',
		                    'type' => 'select',
		                    'options' => array(
		                    		'hide' => __('No','bon'),
		                    		'show' => __('Yes', 'bon')
		                    	),
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Map Zoom Button', 'bon' ),
		                    'desc' => __('Show map zoom button on listings page.','bon'),
		                    'id' => 'show_listings_map_zoom',
		                    'type' => 'select',
		                    'options' => array(
		                    		'hide' => __('No','bon'),
		                    		'show' => __('Yes', 'bon')
		                    	),
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Map Type Button', 'bon' ),
		                    'desc' => __('Show map type button on listings page.','bon'),
		                    'id' => 'show_listings_map_type',
		                    'type' => 'select',
		                    'options' => array(
		                    		'hide' => __('No','bon'),
		                    		'show' => __('Yes', 'bon')
		                    	),
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Listing Gallery Thumbnail', 'bon' ),
		                    'desc' => __('Show the listing gallery thumbnail','bon'),
		                    'id' => 'listing_gallery_thumbnail',
		                    'type' => 'select',
		                    'std' => 'yes',
		                    'options' => array(
		                    	'yes' => __('Thumbnail Only','bon'),
		                    	'no' => __('Controller Only','bon'),
		                    	'both' => __('Both Controller and Thumbnail','bon')
		                    ) );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Size Measurement', 'bon' ),
		                    'desc' => __('The size measurement that will be used in lot size and building size.','bon'),
		                    'id' => 'measurement',
		                    'type' => 'text',
		                    'std' => 'Sq Ft'
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Length Measurement', 'bon' ),
		                    'desc' => __('The length measurement that will be used in car dimension.','bon'),
		                    'id' => 'length_measure',
		                    'type' => 'text',
		                    'std' => 'in.'
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Mileage Measurement', 'bon' ),
		                    'desc' => __('The mileage measurement that will be used in car mileage.','bon'),
		                    'id' => 'mileage_measure',
		                    'type' => 'text',
		                    'std' => 'miles'
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Price As Text Label', 'bon' ),
		                    'desc' => __('Use this as text placeholder for place you want to set as text only','bon'),
		                    'id' => 'price_text',
		                    'std' => __('Call For Quote','bon'),
		                    'type' => 'text',
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Price Currency', 'bon' ),
		                    'desc' => __('Input the size currency that will be used in listing price.','bon'),
		                    'id' => 'currency',
		                    'type' => 'text',
		                    'std' => '$'
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Currency symbol placement', 'bon' ),
		                    'desc' => __('Choose which format of the currency symbol placement.','bon'),
		                    'id' => 'currency_placement',
		                    'type' => 'select',
		                    'options' => array(
		                    	'left' => '$1,234,567',
		                    	'left-space' => '$ 1,234,567 (with space)',
		                    	'right' => '1,234,567$',
		                    	'right-space' => '1,234,567 $ (with space)'
		                    )
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Price Format', 'bon' ),
		                    'desc' => __('Choose the price format.','bon'),
		                    'id' => 'price_format',
		                    'type' => 'select',
		                    'options' => array(
		                    	'comma' => '$1,234,567.00 (with comma)',
		                    	'dot' => '$1.234.567.00 (with dot)',
		                    )
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Listing Count in Listing Page', 'bon' ),
		                    'desc' => __('Show the listing count before the listing list','bon'),
		                    'id' => 'show_listing_count',
		                    'type' => 'select',
		                    'options' => array(
		                    	'yes' => __('Yes','bon'),
		                    	'no' => __('No','bon'),
		                    )
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Contact Form in Agent Page', 'bon' ),
		                    'desc' => __('Show Contact form when viewing agent page?','bon'),
		                    'id' => 'show_agent_form',
		                    'type' => 'select',
		                    'options' => array(
		                    	'yes' => __('Yes','bon'),
		                    	'no' => __('No','bon'),
		                    )
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Agent Latest Listings', 'bon' ),
		                    'desc' => __('Show Agent Latest Listings in Single Agent Page','bon'),
		                    'id' => 'show_agent_listing',
		                    'type' => 'select',
		                    'options' => array(
		                    	'yes' => __('Yes','bon'),
		                    	'no' => __('No','bon'),
		                    )
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Show Related Listings', 'bon' ),
		                    'desc' => __('Show Related Listings in Single Listing Detail Page','bon'),
		                    'id' => 'show_related',
		                    'type' => 'select',
		                    'options' => array(
		                    	'yes' => __('Yes','bon'),
		                    	'no' => __('No','bon'),
		                    )
		                    );


		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Search Panel Options', 'bon' ),
		    				'type' => 'subheading' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Price Slider Minimum Value', 'bon' ),
		                    'desc' => __('Minimum Value for the Price Range slider in the Search Listing Options','bon'),
		                    'id' => 'price_range_min',
		                    'std' =>  '0',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Price Slider Maximum Value', 'bon' ),
		                    'desc' => __('Maximum Value for the Price Range slider in the Search Listing Options','bon'),
		                    'id' => 'price_range_max',
		                    'std' =>  '1000000',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Price Slider Minimum Value (Rent Only)', 'bon' ),
		                    'desc' => __('Minimum Value for the Price Range slider in the Search Listing Options when user choosing For Rent Status.','bon'),
		                    'id' => 'price_range_min_rent',
		                    'std' =>  '0',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Price Slider Maximum Value (Rent Only)', 'bon' ),
		                    'desc' => __('Maximum Value for the Price Range slider in the Search Listing Options when user choosing For Rent Status.','bon'),
		                    'id' => 'price_range_max_rent',
		                    'std' =>  '10000',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Price Slider Step Value', 'bon' ),
		                    'desc' => __('Step Value for the Price Range slider when the Slide Event fired','bon'),
		                    'id' => 'price_range_step',
		                    'std' =>  '5000',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Price Slider Step Value (Rent Only)', 'bon' ),
		                    'desc' => __('Step Value for the Price Range slider when the Slide Event fired, for when user choosing Rent Status only.','bon'),
		                    'id' => 'price_range_step_rent',
		                    'std' =>  '50',
		                    'type' => 'text' );
		

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Maximum Bed Options', 'bon' ),
		                    'desc' => __('The maximum bed available for user to select','bon'),
		                    'id' => 'maximum_bed',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Maximum Bath Options', 'bon' ),
		                    'desc' => __('The maximum bath available for user to select','bon'),
		                    'id' => 'maximum_bath',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Minimum Lot Size Options', 'bon' ),
		                    'desc' => __('The minimum lot size available for user to select','bon'),
		                    'id' => 'minimum_lotsize',
		                    'std' => '0',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Maximum Lot Size Options', 'bon' ),
		                    'desc' => __('The maximum lot size available for user to select','bon'),
		                    'id' => 'maximum_lotsize',
		                    'std' => '10000',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Lot Size Slider Step', 'bon' ),
		                    'desc' => __('The step for lot size slider','bon'),
		                    'id' => 'step_lotsize',
		                    'std' => '100',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Minimum Building Size Options', 'bon' ),
		                    'desc' => __('The minimum building size available for user to select','bon'),
		                    'id' => 'minimum_buildingsize',
		                    'std' => '0',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Maximum Building Size Options', 'bon' ),
		                    'desc' => __('The maximum building size available for user to select','bon'),
		                    'id' => 'maximum_buildingsize',
		                    'std' => '10000',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Building Size Slider Step', 'bon' ),
		                    'desc' => __('The step for building size slider','bon'),
		                    'id' => 'step_buildingsize',
		                    'std' => '100',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Maximum Floor Options', 'bon' ),
		                    'desc' => __('The maximum floor available for user to select','bon'),
		                    'id' => 'maximum_floor',
		                    'std' => '5',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Maximum Garage Options', 'bon' ),
		                    'desc' => __('The maximum garage available for user to select','bon'),
		                    'id' => 'maximum_garage',
		                    'std' => '5',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Maximum Basement Options', 'bon' ),
		                    'desc' => __('The maximum basement available for user to select','bon'),
		                    'id' => 'maximum_basement',
		                    'std' => '5',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Minimum Mileage Options', 'bon' ),
		                    'desc' => __('The minimum mileage available for user to select','bon'),
		                    'id' => 'minimum_mileage',
		                    'std' => '0',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Maximum Mileage Options', 'bon' ),
		                    'desc' => __('The maximum mileage available for user to select','bon'),
		                    'id' => 'maximum_mileage',
		                    'std' => '10000',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Mileage Slider Step', 'bon' ),
		                    'desc' => __('The step for mileage slider','bon'),
		                    'id' => 'step_mileage',
		                    'std' => '100',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Search Button Label', 'bon' ),
		                    'desc' => __('The search button label','bon'),
		                    'id' => 'search_button_label',
		                    'std' => 'Find Property',
		                    'type' => 'text' );

		
		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Custom Search Field', 'bon' ),
		    				'type' => 'subheading' );

		for($i = 1; $i < 4; $i++ ) {
			$std = 0;
			$options[] = array( 'slug' => 'bon_options',
							'label' => sprintf(__( 'Enable Search Row %s', 'bon' ), $i),
		                    'desc' => __('Enable the search row','bon'),
		                    'id' => 'search_row_'.$i,
		                    'std' => $std,
		                    'class' => 'collapsed',
		                    'type' => 'checkbox' );

			for($j = 1; $j < 4; $j++ ) {
				$class = 'hidden';
				if($j == 3) {
					$class = 'hidden last';
				}
				$options[] = array( 'slug' => 'bon_options',
							'label' => sprintf(__( 'Row %1s Column %2s Field', 'bon' ), $i, $j),
		                    'desc' => __('Choose the field for the search panel','bon'),
		                    'id' => 'search_row_'.$i.'_col_'.$j,
		                    'type' => 'select',
		                    'class' => $class,
		                    'options' => $search_fields
		                     );
			}
		}

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Slider Label', 'bon' ),
		                    'desc' => __('If you are using multilevel location define the label here.','bon'),
		                    'id' => 'test',
		                    'min' => '0',
		                    'max' => '100',
		                    'step' => '5',
		                    'type' => 'slider' );


		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Location Level 1 Label', 'bon' ),
		                    'desc' => __('If you are using multilevel location define the label here.','bon'),
		                    'id' => 'location_level1_label',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Location Level 2 Label', 'bon' ),
		                    'desc' => __('If you are using multilevel location define the label here.','bon'),
		                    'id' => 'location_level2_label',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Location Level 3 Label', 'bon' ),
		                    'desc' => __('If you are using multilevel location define the label here.','bon'),
		                    'id' => 'location_level3_label',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Manufacturer Level 1 Label', 'bon' ),
		                    'desc' => __('If you are using multilevel manufacturer define the label here. eq: Make','bon'),
		                    'id' => 'manufacturer_level1_label',
		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Manufacturer Level 2 Label', 'bon' ),
		                    'desc' => __('If you are using multilevel manufacturer define the label here. eq: Model','bon'),
		                    'id' => 'manufacturer_level2_label',

		                    'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Manufacturer Level 3 Label', 'bon' ),
		                    'desc' => __('If you are using multilevel manufacturer define the label here. eq: Sub Model','bon'),
		                    'id' => 'manufacturer_level3_label',
		                    'type' => 'text' );


		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'IDX Search Options', 'bon' ),
		    				'type' => 'subheading' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Use IDX Search Form', 'bon' ),
		                    'desc' => __('If dsIDXpress Plugin is installed, use the searchform instead.','bon'),
		                    'id' => 'use_idx_search',
		                    'std' => 'no',
		                    'options' => array(
		                    	'no' => __('No', 'bon'),
		                    	'yes' => __('Yes', 'bon'),
		                    ),
		                    'type' => 'select' );


		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Enabled autoload options from IDX', 'bon' ),
		                    'desc' => __('If you do not want to specify the city please set this to yes. If set to no you need to specify the city.','bon'),
		                    'id' => 'idx_enable_search_autoload',
		                    'options' => array(
		                    	'yes' => __('Yes', 'bon'),
		                    	'no' => __('No', 'bon'),
		                    ),
		                    'type' => 'select' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'IDX Search Options Limit', 'bon' ),
		                    'desc' => __('If auto load options is enabled the idx will automatically query the city, zip, tract, community options by default. Set the limit for the options. This is to prevent resource overload since the queried city can be more than 1000. Default is set to 100','bon'),
		                    'id' => 'idx_search_option_limit',
		                    'type' => 'text' );


		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Manual IDX City Options (one per line)', 'bon' ),
		                    'desc' => __('The list of idx city for the search options panel.','bon'),
		                    'id' => 'idx_manual_city',
		                    'type' => 'textarea' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Manual IDX Tract Options (one per line)', 'bon' ),
		                    'desc' => __('The list of idx tract for the search options panel.','bon'),
		                    'id' => 'idx_manual_tract',
		                    'type' => 'textarea' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Manual IDX Zip Options (one per line)', 'bon' ),
		                    'desc' => __('The list of idx zip for the search options panel.','bon'),
		                    'id' => 'idx_manual_zip',
		                    'type' => 'textarea' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Manual IDX Community Options (one per line)', 'bon' ),
		                    'desc' => __('The list of idx community for the search options panel.','bon'),
		                    'id' => 'idx_manual_community',
		                    'type' => 'textarea' );


		/**
		 * =====================================================================================================
		 *
		 * Layout Settings
		 * 
		 * @category Layout
		 *
		 * ======================================================================================================
		 */

		$options[] = array( 'slug' => 'bon_options', 
							'label' => __( 'Layout Settings', 'bon' ),
		    				'type' => 'heading',
		    				'icon' => 'layout' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'How many listing per row in mobile view?', 'bon' ),
		                    'desc' => __('Set the listing per row in mobile view.','bon'),
		                    'id' => 'mobile_layout',
		                    'std' => '2',
		                    'type' => 'select',
		                    'options' => array(
		                    	'1' => '1',
		                    	'2' => '2',
		                    ),
		                    );



		$options[] = array( 'slug' => 'bon_options',
							'label' => '',
		                    'desc' => '',
		                    'std' =>  __( 'This section will handle the layout for categories, archives and post type taxonomy archives layout. Layout for page and single post can be set in the post/page edit page.', 'bon' ),
		                    'type' => 'info' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Archive Layout', 'bon' ),
		                    'desc' => __('Layout for archive page.','bon'),
		                    'id' => 'archive_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Category Layout', 'bon' ),
		                    'desc' => __('Layout for category archive page.','bon'),
		                    'id' => 'category_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Tags Archive Layout', 'bon' ),
		                    'desc' => __('Layout for tags archive page.','bon'),
		                    'id' => 'tag_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Property Type Archive Layout', 'bon' ),
		                    'desc' => __('Layout for property-type listing archive page.','bon'),
		                    'id' => 'property_type_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Property Location Archive Layout', 'bon' ),
		                    'desc' => __('Layout for property-location listing archive page.','bon'),
		                    'id' => 'property_location_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Property Feature Archive Layout', 'bon' ),
		                    'desc' => __('Layout for property-feaure listing archive page.','bon'),
		                    'id' => 'property_feature_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Car Feature Archive Layout', 'bon' ),
		                    'desc' => __('Layout for car-feature listing archive page.','bon'),
		                    'id' => 'car_feature_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Dealer Location Archive Layout', 'bon' ),
		                    'desc' => __('Layout for dealer-location listing archive page.','bon'),
		                    'id' => 'dealer_location_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Body Type Archive Layout', 'bon' ),
		                    'desc' => __('Layout for body-type listing archive page.','bon'),
		                    'id' => 'body_type_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Manufactuere Archive Layout', 'bon' ),
		                    'desc' => __('Layout for manufacturer listing archive page.','bon'),
		                    'id' => 'manufacturer_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'IDX Page Layout', 'bon' ),
		                    'desc' => __('Layout for idx page.','bon'),
		                    'id' => 'idx_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices,
		                    );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'IDX Details page Layout', 'bon' ),
		                    'desc' => __('Layout for idx details page.','bon'),
		                    'id' => 'idx_details_layout',
		                    'std' => '2c-l',
		                    'type' => 'radio-img',
		                    'options' => $layout_choices_2,
		                    );




		/**
		 * =====================================================================================================
		 *
		 * Footer Settings
		 * 
		 * @category Footer
		 *
		 * ======================================================================================================
		 */

		$options[] = array( 'slug' => 'bon_options', 
							'label' => __( 'Footer Settings', 'bon' ),
		    				'type' => 'heading',
		    				'icon' => 'footer' );

		$options[] = array( 'slug' => 'bon_options',
							'label' => __( 'Custom Copyright Text', 'bon' ),
		                    'desc' => __( 'Custom HTML and Text that will appear in the footer of your theme.', 'bon' ),
		                    'id' => 'footer_copyright',
		                    'std' =>  '',
		                    'settings' => array(
		                    	'media_buttons' => false,
		                    	'tinymce' => array( 'plugins' => 'wordpress' )
		                    ),
		                    'type' => 'editor' );

		


		/**
		 * =====================================================================================================
		 *
		 * Social Settings
		 * 
		 * @category Social
		 *
		 * ======================================================================================================
		 */


		$options[] = array( 'slug' => 'bon_options', 
							'label' => __( 'Social Settings', 'bon' ),
		    				'type' => 'heading',
		    				'icon' => 'connect' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Enable Header Social', 'bon' ),
		    				'desc' => __( 'Enable Social Icons in Header.', 'bon' ),
		    				'id' => 'enable_header_social',
		    				'options' => array(
		    					'yes' => __('Yes', 'bon'),
		    					'no' => __('No', 'bon')
		    				),
		    				'type' => 'select' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Enable Footer Social', 'bon' ),
		    				'desc' => __( 'Enable Social Icons in Footer.', 'bon' ),
		    				'id' => 'enable_footer_social',
		    				'options' => array(
		    					'yes' => __('Yes', 'bon'),
		    					'no' => __('No', 'bon')
		    				),
		    				'type' => 'select' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Facebook Username', 'bon' ),
		    				'desc' => __( 'Your Facebook username.', 'bon' ),
		    				'id' => 'social_facebook',
		    				'std' => '',
		    				'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Twitter Username', 'bon' ),
		    				'desc' => __( 'Your Twitter username.', 'bon' ),
		    				'id' => 'social_twitter',
		    				'std' => '',
		    				'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'YouTube Username', 'bon' ),
		    				'desc' => __( 'Your YouTube username.', 'bon' ),
		    				'id' => 'social_youtube',
		    				'std' => '',
		    				'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Vimeo Username', 'bon' ),
		    				'desc' => __( 'Your Vimeo username.', 'bon' ),
		    				'id' => 'social_vimeo',
		    				'std' => '',
		    				'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Google Plus Username', 'bon' ),
		    				'desc' => __( 'Your Google Plus username.', 'bon' ),
		    				'id' => 'social_google_plus',
		    				'std' => '',
		    				'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Flickr Username', 'bon' ),
		    				'desc' => __( 'Your Flickr username.', 'bon' ),
		    				'id' => 'social_flickr',
		    				'std' => '',
		    				'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Pinterest Username', 'bon' ),
		    				'desc' => __( 'Your Pinterest username.', 'bon' ),
		    				'id' => 'social_pinterest',
		    				'std' => '',
		    				'type' => 'text' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'LinkedIn Username', 'bon' ),
		    				'desc' => __( 'Your LinkedIn username.', 'bon' ),
		    				'id' => 'social_linkedin',
		    				'std' => '',
		    				'type' => 'text' );


		$options[] = array( 'slug' => 'bon_options', 
							'label' => __( 'Sidebar Generator', 'bon' ),
		    				'type' => 'heading',
		    				'icon' => 'box' );

		$options[] = array( 'slug' => 'bon_options', 'label' => __( 'Custom Sidebar Name', 'bon' ),
		    				'id' => 'sidebars_generator',
		    				'type' => 'repeatable',
		    				'repeatable_fields' => array(
		    					array(
		    						'type' => 'text',
		    						'label' => '',
		    						'id' => 'sidebar_name'
		    					)
		    				) );

		return $options;
	}
}



?>