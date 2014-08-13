<?php

/**
 * =====================================================================================================
 *
 * Setting Up theme supports
 * To setup theme supports use a filter to filter the support since there are already some default support
 * initialize after theme setup such as post formats etc, to remove post format unset the post format
 * from $theme_supports array variable and filter it 
 *
 * @since 1.0
 * @return array()
 *
 * ======================================================================================================
 */

define('SC_CHAT_LICENSE_KEY', '05f8208c-3ac1-4176-8cd4-c62022d8e8ee');

add_action( 'after_setup_theme', 'shandora_setup' );

function shandora_setup() {

	bon_set_content_width( 620 );

	add_editor_style( 'assets/css/editor-styles.css' );

	update_option('sc_chat_validate_license', 1);
}

function shandora_embed_defaults( $args ) {

	if ( current_theme_supports( 'theme-layouts' ) && '1c' == get_theme_mod( 'theme_layout' ) )
		$args['width'] = 1170;

	return $args;

	add_filter( 'embed_defaults', 'shandora_embed_defaults' );
}

if( !function_exists('shandora_setup_theme_supports') ) {

	function shandora_setup_theme_support($theme_supports) {
		$theme_supports['bon-core-widgets'] = '';
		$theme_supports['bon-breadcrumb-trail'] = '';
		$theme_supports['post-formats'] = array(
										    'gallery',
										    'link',
										    'image',
										    'quote',
										    'video',
						 					);
		$theme_supports['get-the-image'] = '';
		$theme_supports['theme-fonts'] = array( 'callback' => 'shandora_custom_typo','customizer' => true );
		$theme_supports['bon-core-sidebars'] = array( 
												array(
													'name' => __('Sidebar Primary','bon'),
													'id' => 'primary'
												),
												array(
													'name' => __('Sidebar Listing', 'bon'),
													'id' => 'secondary'
												), 
												array(
													'name' => __('Footer 1', 'bon'),
													'id' => 'footer1'
												),
												array(
													'name' => __('Footer 2', 'bon'),
													'id' => 'footer2'
												),
												array(
													'name' => __('Footer 3', 'bon'),
													'id' => 'footer3'
												),
												array(
													'name' => __('Footer 4', 'bon'),
													'id' => 'footer4'
												),

											);
		$theme_supports['bon-featured-slider'] = '';
		$theme_supports['bon-poll'] = '';
		$theme_supports['bon-page-builder'] = '';
		$theme_supports['bon-quiz'] = '';
		$theme_supports['bon-core-menus'] = array(
									'primary' => __('Primary', 'bon' ) ,
									'topbar' => __('Top Bar Menu', 'bon' )
								);
		$theme_supports['cleaner-gallery'] = '';
		$theme_supports['zurb-foundation'] = array(
				'foundation',
		);

		$theme_supports['dynamic-script'] = apply_filters('shandora_dynamic_script', array(
			
			
			/*'flexslider' => array(
					'name' => 'flexslider',
					'version' => '2.1.0',
					'dep' => array( 'jquery'),
					'in_footer' => true,
					'folder' => 'libs/',
					'filename' => 'jquery.flexslider',
				), */

			/*'bx' => array(
					'name' => 'bx',
					'version' => null,
					'dep' => array( 'jquery'),
					'in_footer' => true,
					'folder' => 'libs/',
					'filename' => 'jquery.bxslider.min',
					'condition' => array(
							'value' => array(
								array(
									'key' => 'is_singular',
									'param' => 'listing'
								),
								array(
									'key' => 'is_singular',
									'param' => 'car-listing'
								)
							),
							'operator' => 'OR'
						)
				),*/

			/*'foundation-carousel' => array(
					'name' => 'foundation-carousel',
					'version' => '0.1',
					'dep' => array( 'jquery'),
					'in_footer' => true,
					'folder' => '',
					'filename' => 'foundation.carousel'
				),*/


			'selecttoui' => array(
					'name' => 'selecttoui',
					'version' => '2.0.0',
					'dep' => array( 'jquery', 'jquery-ui-slider'),
					'in_footer' => true,
					'folder' => 'libs/',
					'filename' => 'jquery.selecttoui.min'
				),

			/*'cookie' => array(
					'name' => 'cookie',
					'version' => '1.3.0',
					'dep' => array( 'jquery'),
					'in_footer' => true,
					'folder' => 'libs/',
					'filename' => 'jquery.cookie'
				), */

			/*'maginific' => array(
					'name' => 'magnific',
					'version' => '0.9.3',
					'dep' => array( 'jquery'),
					'in_footer' => true,
					'folder' => 'libs/',
					'filename' => 'jquery.magnific-popup.min'
				),

			'touchpunch' => array(
				'name' => 'touchpunch',
				'version' => '',
				'dep' => array('jquery'),
				'in_footer' => true,
				'folder' => 'libs/',
				'filename' => 'jquery.ui.touch-punch.min'
			)*/

			
		));
	

		$color = bon_get_option('main_color_style', 'green');
		
		$theme_supports['dynamic-style'] = apply_filters('shandora_dynamic_style', array(

						/*'jquery-ui-custom' => array(
								'name' => 'ui-custom',
								'version' => '',
								'dep' => '',
								'media' => 'screen',
								'folder' => '',
								'filename' => 'jquery-ui.custom'
						),

						'jquery-ui-extra' => array(
								'name' => 'ui-slider-extra',
								'version' => '',
								'dep' => '',
								'media' => 'screen',
								'folder' => '',
								'filename' => 'ui.slider.extras'
							),

						'flexslider' => array(
								'name' => 'flexslider',
								'version' => '',
								'dep' => '',
								'media' => 'screen',
								'folder' => '',
								'filename' => 'flexslider'

							),

						'fonts' => array(
								'name' => 'fonts',
								'version' => '',
								'dep' => '',
								'media' => 'all',
								'folder' => '',
								'filename' => 'fonts'
							),
						*/
						'app' => array(
								'name' => 'app',
								'version' => '',
								'dep' => '',
								'media' => 'all',
								'folder' => 'colors/',
								'filename' => $color,
							),
						/*
						'maginific' => array(
								'name' => 'maginific',
								'version' => '',
								'dep' => '',
								'media' => 'screen',
								'folder' => '',
								'filename' => 'magnific-popup'
							),
						'shandora_icon' => array(
								'name' => 'shandora_icon',
								'version' => '',
								'dep' => '',
								'media' => 'all',
								'folder' => '',
								'filename' => 'shandora-icons'
							),
						*/
						
						'all' => array(
								'name' => 'all',
								'version' => '',
								'dep' => '',
								'media' => 'all',
								'folder' => '',
								'filename' => 'all'
							),
						'print' => array(
								'name' => 'print',
								'version' => '',
								'dep' => '',
								'media' => 'print',
								'folder' => '',
								'filename' => 'print'
							),

						));
		
		$theme_supports = apply_filters('shandora_default_theme_supports', $theme_supports);

		foreach($theme_supports as $support_key => $support_args) {
			add_theme_support( $support_key, $support_args );
		}

	}

	add_action('after_setup_theme', 'shandora_setup_theme_support', 5);
}




if( !function_exists('shandora_layout_setup') ) {

	function shandora_layout_setup() {
		add_theme_support( 'theme-layouts', array( '1c', '2c-l', '2c-r' ), array( 'default' => '2c-l' ) );
	}

	add_action('after_setup_theme', 'shandora_layout_setup');
}

/**
 * =====================================================================================================
 *
 * Setting Up theme post thumbnails
 *
 * @since 1.0
 * @return array()
 *
 * ======================================================================================================
 */

if( !function_exists('shandora_setup_theme_thumbnails') ) {

	function shandora_setup_theme_thumbnails( $theme_thumbnails ) {
		
		$theme_thumbnails = array(
			'listing_small' => array('width'=>270, 'height'=>220, 'crop' => true ),							
			'listing_small_box'	=> array('width'=>300, 'height'=>300, 'crop' => true),
			'listing_list' => array( 'width' => 420, 'height' => 420, 'crop' => true ),
			'blog_small' => array('width' => 285, 'height' => 285, 'crop' => true),
			'listing_large' => array('width' => 800, 'height' => 400, 'crop' => true),
			'listing_medium' => array('width' => 400, 'height' => 200, 'crop' => true),
			'featured_slider' => array('width'=>1920, 'height'=> 1090, 'crop' => true),
		);

		foreach($theme_thumbnails as $key => $args) {
			add_image_size( $key, $args['width'], $args['height'], $args['crop'] );
		}
	}


	add_action('init', 'shandora_setup_theme_thumbnails');
}


function shandora_custom_typo( $theme_fonts ) {

	/* Register font settings. */

	$theme_fonts->add_setting(
		array(
			'id'        => 'primary',
			'label'     => __( 'Primary Font', 'example' ),
			'default'   => 'titilium-stack',
			'selectors' => 'body, #slider-container .slider-inner-container .flex-caption  .secondary-title, #slider-container .slider-inner-container .flex-caption .caption-content, .featured-listing-carousel h2, #main-navigation nav ul > li > ul li a,
							.bon-toolkit-posts-widget .item-title,
							article.listing .price,
							footer .widget-title,
							.bon-builder-element-calltoaction .panel.callaction h1,
							.bon-builder-element-calltoaction .panel.callaction h2,
							.bon-builder-element-calltoaction .panel.callaction h3,
							.bon-builder-element-calltoaction .panel.callaction h4',
		)
	);

	$theme_fonts->add_setting(
		array(
			'id'        => 'heading',
			'label'     => __( 'Heading & Menu Font', 'example' ),
			'default'   => 'bebeas-neue-stack',
			'selectors' => 'h1, h2, h3, h4, h5, h6, #main-navigation nav ul, .listings .entry-header .badge, #comparison-table td.title nav > ul > li > a',
		)
	);


	$theme_fonts->add_font(
		array(
			'handle' => 'titilium-stack',
			'label'  => __( 'Titilium Web (font stack)', 'example' ),
			'stack'  => '"Titillium Web", "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif',
		)
	);

	$theme_fonts->add_font(
		array(
			'handle' => 'helvetica-neue-stack',
			'label'  => __( 'Helvetica Neue (font stack)', 'example' ),
			'stack'  => '"HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif',
		)
	);

	$theme_fonts->add_font(
		array(
			'handle' => 'muli',
			'label'  => __( 'Muli', 'bon' ),
			'family' => 'Muli',
			'stack'  => "Muli, sans-serif",
			'type'   => 'google'
		)
	);

	$theme_fonts->add_font(
		array(
			'handle' => 'bebeas-neue-stack',
			'label' => 'Bebas Neue',
			'stack' => '"BebasNeue", sans-serif',

		)
	);

	
	$gfonts = shandora_google_web_fonts();

	foreach($gfonts as $key => $value ) {
		$new_font = array(
			'handle' => strtolower(str_replace(' ', '-', $key)),
			'label' => $value,
			'type' => 'google',
			'stack' => $key,
			'family' => $key,
		);

		$theme_fonts->add_font( $new_font );
	}



}


function shandora_google_web_fonts() {

	$google_faces = array(
                        "Abel" => "Abel",
                        "Abril Fatface" => "Abril Fatface",
                        "Aclonica" => "Aclonica",
                        "Acme" => "Acme",
                        "Actor" => "Actor",
                        "Adamina" => "Adamina",
                        "Advent Pro" => "Advent Pro",
                        "Aguafina Script" => "Aguafina Script",
                        "Aladin" => "Aladin",
                        "Aldrich" => "Aldrich",
                        "Alegreya" => "Alegreya",
                        "Alegreya SC" => "Alegreya SC",
                        "Alex Brush" => "Alex Brush",
                        "Alfa Slab One" => "Alfa Slab One",
                        "Alice" => "Alice",
                        "Alike" => "Alike",
                        "Alike Angular" => "Alike Angular",
                        "Allan" => "Allan",
                        "Allerta" => "Allerta",
                        "Allerta Stencil" => "Allerta Stencil",
                        "Allura" => "Allura",
                        "Almendra" => "Almendra",
                        "Almendra SC" => "Almendra SC",
                        "Amaranth" => "Amaranth",
                        "Amatic SC" => "Amatic SC",
                        "Amethysta" => "Amethysta",
                        "Andada" => "Andada",
                        "Andika" => "Andika",
                        "Angkor" => "Angkor",
                        "Annie Use Your Telescope" => "Annie Use Your Telescope",
                        "Anonymous Pro" => "Anonymous Pro",
                        "Antic" => "Antic",
                        "Antic Didone" => "Antic Didone",
                        "Antic Slab" => "Antic Slab",
                        "Anton" => "Anton",
                        "Arapey" => "Arapey",
                        "Arbutus" => "Arbutus",
                        "Architects Daughter" => "Architects Daughter",
                        "Arimo" => "Arimo",
                        "Arizonia" => "Arizonia",
                        "Armata" => "Armata",
                        "Artifika" => "Artifika",
                        "Arvo" => "Arvo",
                        "Asap" => "Asap",
                        "Asset" => "Asset",
                        "Astloch" => "Astloch",
                        "Asul" => "Asul",
                        "Atomic Age" => "Atomic Age",
                        "Aubrey" => "Aubrey",
                        "Audiowide" => "Audiowide",
                        "Average" => "Average",
                        "Averia Gruesa Libre" => "Averia Gruesa Libre",
                        "Averia Libre" => "Averia Libre",
                        "Averia Sans Libre" => "Averia Sans Libre",
                        "Averia Serif Libre" => "Averia Serif Libre",
                        "Bad Script" => "Bad Script",
                        "Balthazar" => "Balthazar",
                        "Bangers" => "Bangers",
                        "Basic" => "Basic",
                        "Battambang" => "Battambang",
                        "Baumans" => "Baumans",
                        "Bayon" => "Bayon",
                        "Belgrano" => "Belgrano",
                        "Belleza" => "Belleza",
                        "Bentham" => "Bentham",
                        "Berkshire Swash" => "Berkshire Swash",
                        "Bevan" => "Bevan",
                        "Bigshot One" => "Bigshot One",
                        "Bilbo" => "Bilbo",
                        "Bilbo Swash Caps" => "Bilbo Swash Caps",
                        "Bitter" => "Bitter",
                        "Black Ops One" => "Black Ops One",
                        "Bokor" => "Bokor",
                        "Bonbon" => "Bonbon",
                        "Boogaloo" => "Boogaloo",
                        "Bowlby One" => "Bowlby One",
                        "Bowlby One SC" => "Bowlby One SC",
                        "Brawler" => "Brawler",
                        "Bree Serif" => "Bree Serif",
                        "Bubblegum Sans" => "Bubblegum Sans",
                        "Buda" => "Buda",
                        "Buenard" => "Buenard",
                        "Butcherman" => "Butcherman",
                        "Butterfly Kids" => "Butterfly Kids",
                        "Cabin" => "Cabin",
                        "Cabin Condensed" => "Cabin Condensed",
                        "Cabin Sketch" => "Cabin Sketch",
                        "Caesar Dressing" => "Caesar Dressing",
                        "Cagliostro" => "Cagliostro",
                        "Calligraffitti" => "Calligraffitti",
                        "Cambo" => "Cambo",
                        "Candal" => "Candal",
                        "Cantarell" => "Cantarell",
                        "Cantata One" => "Cantata One",
                        "Cardo" => "Cardo",
                        "Carme" => "Carme",
                        "Carter One" => "Carter One",
                        "Caudex" => "Caudex",
                        "Cedarville Cursive" => "Cedarville Cursive",
                        "Ceviche One" => "Ceviche One",
                        "Changa One" => "Changa One",
                        "Chango" => "Chango",
                        "Chau Philomene One" => "Chau Philomene One",
                        "Chelsea Market" => "Chelsea Market",
                        "Chenla" => "Chenla",
                        "Cherry Cream Soda" => "Cherry Cream Soda",
                        "Chewy" => "Chewy",
                        "Chicle" => "Chicle",
                        "Chivo" => "Chivo",
                        "Coda" => "Coda",
                        "Coda Caption" => "Coda Caption",
                        "Codystar" => "Codystar",
                        "Comfortaa" => "Comfortaa",
                        "Coming Soon" => "Coming Soon",
                        "Concert One" => "Concert One",
                        "Condiment" => "Condiment",
                        "Content" => "Content",
                        "Contrail One" => "Contrail One",
                        "Convergence" => "Convergence",
                        "Cookie" => "Cookie",
                        "Copse" => "Copse",
                        "Corben" => "Corben",
                        "Cousine" => "Cousine",
                        "Coustard" => "Coustard",
                        "Covered By Your Grace" => "Covered By Your Grace",
                        "Crafty Girls" => "Crafty Girls",
                        "Creepster" => "Creepster",
                        "Crete Round" => "Crete Round",
                        "Crimson Text" => "Crimson Text",
                        "Crushed" => "Crushed",
                        "Cuprum" => "Cuprum",
                        "Cutive" => "Cutive",
                        "Damion" => "Damion",
                        "Dancing Script" => "Dancing Script",
                        "Dangrek" => "Dangrek",
                        "Dawning of a New Day" => "Dawning of a New Day",
                        "Days One" => "Days One",
                        "Delius" => "Delius",
                        "Delius Swash Caps" => "Delius Swash Caps",
                        "Delius Unicase" => "Delius Unicase",
                        "Della Respira" => "Della Respira",
                        "Devonshire" => "Devonshire",
                        "Didact Gothic" => "Didact Gothic",
                        "Diplomata" => "Diplomata",
                        "Diplomata SC" => "Diplomata SC",
                        "Doppio One" => "Doppio One",
                        "Dorsa" => "Dorsa",
                        "Dosis" => "Dosis",
                        "Dr Sugiyama" => "Dr Sugiyama",
                        "Droid Sans" => "Droid Sans",
                        "Droid Sans Mono" => "Droid Sans Mono",
                        "Droid Serif" => "Droid Serif",
                        "Duru Sans" => "Duru Sans",
                        "Dynalight" => "Dynalight",
                        "EB Garamond" => "EB Garamond",
                        "Eater" => "Eater",
                        "Economica" => "Economica",
                        "Electrolize" => "Electrolize",
                        "Emblema One" => "Emblema One",
                        "Emilys Candy" => "Emilys Candy",
                        "Engagement" => "Engagement",
                        "Enriqueta" => "Enriqueta",
                        "Erica One" => "Erica One",
                        "Esteban" => "Esteban",
                        "Euphoria Script" => "Euphoria Script",
                        "Ewert" => "Ewert",
                        "Exo" => "Exo",
                        "Expletus Sans" => "Expletus Sans",
                        "Fanwood Text" => "Fanwood Text",
                        "Fascinate" => "Fascinate",
                        "Fascinate Inline" => "Fascinate Inline",
                        "Federant" => "Federant",
                        "Federo" => "Federo",
                        "Felipa" => "Felipa",
                        "Fjord One" => "Fjord One",
                        "Flamenco" => "Flamenco",
                        "Flavors" => "Flavors",
                        "Fondamento" => "Fondamento",
                        "Fontdiner Swanky" => "Fontdiner Swanky",
                        "Forum" => "Forum",
                        "Francois One" => "Francois One",
                        "Fredericka the Great" => "Fredericka the Great",
                        "Fredoka One" => "Fredoka One",
                        "Freehand" => "Freehand",
                        "Fresca" => "Fresca",
                        "Frijole" => "Frijole",
                        "Fugaz One" => "Fugaz One",
                        "GFS Didot" => "GFS Didot",
                        "GFS Neohellenic" => "GFS Neohellenic",
                        "Galdeano" => "Galdeano",
                        "Gentium Basic" => "Gentium Basic",
                        "Gentium Book Basic" => "Gentium Book Basic",
                        "Geo" => "Geo",
                        "Geostar" => "Geostar",
                        "Geostar Fill" => "Geostar Fill",
                        "Germania One" => "Germania One",
                        "Give You Glory" => "Give You Glory",
                        "Glass Antiqua" => "Glass Antiqua",
                        "Glegoo" => "Glegoo",
                        "Gloria Hallelujah" => "Gloria Hallelujah",
                        "Goblin One" => "Goblin One",
                        "Gochi Hand" => "Gochi Hand",
                        "Gorditas" => "Gorditas",
                        "Goudy Bookletter 1911" => "Goudy Bookletter 1911",
                        "Graduate" => "Graduate",
                        "Gravitas One" => "Gravitas One",
                        "Great Vibes" => "Great Vibes",
                        "Gruppo" => "Gruppo",
                        "Gudea" => "Gudea",
                        "Habibi" => "Habibi",
                        "Hammersmith One" => "Hammersmith One",
                        "Handlee" => "Handlee",
                        "Hanuman" => "Hanuman",
                        "Happy Monkey" => "Happy Monkey",
                        "Henny Penny" => "Henny Penny",
                        "Herr Von Muellerhoff" => "Herr Von Muellerhoff",
                        "Holtwood One SC" => "Holtwood One SC",
                        "Homemade Apple" => "Homemade Apple",
                        "Homenaje" => "Homenaje",
                        "IM Fell DW Pica" => "IM Fell DW Pica",
                        "IM Fell DW Pica SC" => "IM Fell DW Pica SC",
                        "IM Fell Double Pica" => "IM Fell Double Pica",
                        "IM Fell Double Pica SC" => "IM Fell Double Pica SC",
                        "IM Fell English" => "IM Fell English",
                        "IM Fell English SC" => "IM Fell English SC",
                        "IM Fell French Canon" => "IM Fell French Canon",
                        "IM Fell French Canon SC" => "IM Fell French Canon SC",
                        "IM Fell Great Primer" => "IM Fell Great Primer",
                        "IM Fell Great Primer SC" => "IM Fell Great Primer SC",
                        "Iceberg" => "Iceberg",
                        "Iceland" => "Iceland",
                        "Imprima" => "Imprima",
                        "Inconsolata" => "Inconsolata",
                        "Inder" => "Inder",
                        "Indie Flower" => "Indie Flower",
                        "Inika" => "Inika",
                        "Irish Grover" => "Irish Grover",
                        "Istok Web" => "Istok Web",
                        "Italiana" => "Italiana",
                        "Italianno" => "Italianno",
                        "Jim Nightshade" => "Jim Nightshade",
                        "Jockey One" => "Jockey One",
                        "Jolly Lodger" => "Jolly Lodger",
                        "Josefin Sans" => "Josefin Sans",
                        "Josefin Slab" => "Josefin Slab",
                        "Judson" => "Judson",
                        "Julee" => "Julee",
                        "Junge" => "Junge",
                        "Jura" => "Jura",
                        "Just Another Hand" => "Just Another Hand",
                        "Just Me Again Down Here" => "Just Me Again Down Here",
                        "Kameron" => "Kameron",
                        "Karla" => "Karla",
                        "Kaushan Script" => "Kaushan Script",
                        "Kelly Slab" => "Kelly Slab",
                        "Kenia" => "Kenia",
                        "Khmer" => "Khmer",
                        "Knewave" => "Knewave",
                        "Kotta One" => "Kotta One",
                        "Koulen" => "Koulen",
                        "Kranky" => "Kranky",
                        "Kreon" => "Kreon",
                        "Kristi" => "Kristi",
                        "Krona One" => "Krona One",
                        "La Belle Aurore" => "La Belle Aurore",
                        "Lancelot" => "Lancelot",
                        "Lato" => "Lato",
                        "League Script" => "League Script",
                        "Leckerli One" => "Leckerli One",
                        "Ledger" => "Ledger",
                        "Lekton" => "Lekton",
                        "Lemon" => "Lemon",
                        "Lilita One" => "Lilita One",
                        "Limelight" => "Limelight",
                        "Linden Hill" => "Linden Hill",
                        "Lobster" => "Lobster",
                        "Lobster Two" => "Lobster Two",
                        "Londrina Outline" => "Londrina Outline",
                        "Londrina Shadow" => "Londrina Shadow",
                        "Londrina Sketch" => "Londrina Sketch",
                        "Londrina Solid" => "Londrina Solid",
                        "Lora" => "Lora",
                        "Love Ya Like A Sister" => "Love Ya Like A Sister",
                        "Loved by the King" => "Loved by the King",
                        "Lovers Quarrel" => "Lovers Quarrel",
                        "Luckiest Guy" => "Luckiest Guy",
                        "Lusitana" => "Lusitana",
                        "Lustria" => "Lustria",
                        "Macondo" => "Macondo",
                        "Macondo Swash Caps" => "Macondo Swash Caps",
                        "Magra" => "Magra",
                        "Maiden Orange" => "Maiden Orange",
                        "Mako" => "Mako",
                        "Marck Script" => "Marck Script",
                        "Marko One" => "Marko One",
                        "Marmelad" => "Marmelad",
                        "Marvel" => "Marvel",
                        "Mate" => "Mate",
                        "Mate SC" => "Mate SC",
                        "Maven Pro" => "Maven Pro",
                        "Meddon" => "Meddon",
                        "MedievalSharp" => "MedievalSharp",
                        "Medula One" => "Medula One",
                        "Megrim" => "Megrim",
                        "Merienda One" => "Merienda One",
                        "Merriweather" => "Merriweather",
                        "Metal" => "Metal",
                        "Metamorphous" => "Metamorphous",
                        "Metrophobic" => "Metrophobic",
                        "Michroma" => "Michroma",
                        "Miltonian" => "Miltonian",
                        "Miltonian Tattoo" => "Miltonian Tattoo",
                        "Miniver" => "Miniver",
                        "Miss Fajardose" => "Miss Fajardose",
                        "Modern Antiqua" => "Modern Antiqua",
                        "Molengo" => "Molengo",
                        "Monofett" => "Monofett",
                        "Monoton" => "Monoton",
                        "Monsieur La Doulaise" => "Monsieur La Doulaise",
                        "Montaga" => "Montaga",
                        "Montez" => "Montez",
                        "Montserrat" => "Montserrat",
                        "Moul" => "Moul",
                        "Moulpali" => "Moulpali",
                        "Mountains of Christmas" => "Mountains of Christmas",
                        "Mr Bedfort" => "Mr Bedfort",
                        "Mr Dafoe" => "Mr Dafoe",
                        "Mr De Haviland" => "Mr De Haviland",
                        "Mrs Saint Delafield" => "Mrs Saint Delafield",
                        "Mrs Sheppards" => "Mrs Sheppards",
                        "Muli" => "Muli",
                        "Mystery Quest" => "Mystery Quest",
                        "Neucha" => "Neucha",
                        "Neuton" => "Neuton",
                        "News Cycle" => "News Cycle",
                        "Niconne" => "Niconne",
                        "Nixie One" => "Nixie One",
                        "Nobile" => "Nobile",
                        "Nokora" => "Nokora",
                        "Norican" => "Norican",
                        "Nosifer" => "Nosifer",
                        "Nothing You Could Do" => "Nothing You Could Do",
                        "Noticia Text" => "Noticia Text",
                        "Nova Cut" => "Nova Cut",
                        "Nova Flat" => "Nova Flat",
                        "Nova Mono" => "Nova Mono",
                        "Nova Oval" => "Nova Oval",
                        "Nova Round" => "Nova Round",
                        "Nova Script" => "Nova Script",
                        "Nova Slim" => "Nova Slim",
                        "Nova Square" => "Nova Square",
                        "Numans" => "Numans",
                        "Nunito" => "Nunito",
                        "Odor Mean Chey" => "Odor Mean Chey",
                        "Old Standard TT" => "Old Standard TT",
                        "Oldenburg" => "Oldenburg",
                        "Oleo Script" => "Oleo Script",
                        "Open Sans" => "Open Sans",
                        "Open Sans Condensed" => "Open Sans Condensed",
                        "Orbitron" => "Orbitron",
                        "Original Surfer" => "Original Surfer",
                        "Oswald" => "Oswald",
                        "Over the Rainbow" => "Over the Rainbow",
                        "Overlock" => "Overlock",
                        "Overlock SC" => "Overlock SC",
                        "Ovo" => "Ovo",
                        "Oxygen" => "Oxygen",
                        "PT Mono" => "PT Mono",
                        "PT Sans" => "PT Sans",
                        "PT Sans Caption" => "PT Sans Caption",
                        "PT Sans Narrow" => "PT Sans Narrow",
                        "PT Serif" => "PT Serif",
                        "PT Serif Caption" => "PT Serif Caption",
                        "Pacifico" => "Pacifico",
                        "Parisienne" => "Parisienne",
                        "Passero One" => "Passero One",
                        "Passion One" => "Passion One",
                        "Patrick Hand" => "Patrick Hand",
                        "Patua One" => "Patua One",
                        "Paytone One" => "Paytone One",
                        "Permanent Marker" => "Permanent Marker",
                        "Petrona" => "Petrona",
                        "Philosopher" => "Philosopher",
                        "Piedra" => "Piedra",
                        "Pinyon Script" => "Pinyon Script",
                        "Plaster" => "Plaster",
                        "Play" => "Play",
                        "Playball" => "Playball",
                        "Playfair Display" => "Playfair Display",
                        "Podkova" => "Podkova",
                        "Poiret One" => "Poiret One",
                        "Poller One" => "Poller One",
                        "Poly" => "Poly",
                        "Pompiere" => "Pompiere",
                        "Pontano Sans" => "Pontano Sans",
                        "Port Lligat Sans" => "Port Lligat Sans",
                        "Port Lligat Slab" => "Port Lligat Slab",
                        "Prata" => "Prata",
                        "Preahvihear" => "Preahvihear",
                        "Press Start 2P" => "Press Start 2P",
                        "Princess Sofia" => "Princess Sofia",
                        "Prociono" => "Prociono",
                        "Prosto One" => "Prosto One",
                        "Puritan" => "Puritan",
                        "Quantico" => "Quantico",
                        "Quattrocento" => "Quattrocento",
                        "Quattrocento Sans" => "Quattrocento Sans",
                        "Questrial" => "Questrial",
                        "Quicksand" => "Quicksand",
                        "Qwigley" => "Qwigley",
                        "Radley" => "Radley",
                        "Raleway" => "Raleway",
                        "Rammetto One" => "Rammetto One",
                        "Rancho" => "Rancho",
                        "Rationale" => "Rationale",
                        "Redressed" => "Redressed",
                        "Reenie Beanie" => "Reenie Beanie",
                        "Revalia" => "Revalia",
                        "Ribeye" => "Ribeye",
                        "Ribeye Marrow" => "Ribeye Marrow",
                        "Righteous" => "Righteous",
                        "Rochester" => "Rochester",
                        "Rock Salt" => "Rock Salt",
                        "Rokkitt" => "Rokkitt",
                        "Ropa Sans" => "Ropa Sans",
                        "Rosario" => "Rosario",
                        "Rosarivo" => "Rosarivo",
                        "Rouge Script" => "Rouge Script",
                        "Ruda" => "Ruda",
                        "Ruge Boogie" => "Ruge Boogie",
                        "Ruluko" => "Ruluko",
                        "Ruslan Display" => "Ruslan Display",
                        "Russo One" => "Russo One",
                        "Ruthie" => "Ruthie",
                        "Sail" => "Sail",
                        "Salsa" => "Salsa",
                        "Sancreek" => "Sancreek",
                        "Sansita One" => "Sansita One",
                        "Sarina" => "Sarina",
                        "Satisfy" => "Satisfy",
                        "Schoolbell" => "Schoolbell",
                        "Seaweed Script" => "Seaweed Script",
                        "Sevillana" => "Sevillana",
                        "Shadows Into Light" => "Shadows Into Light",
                        "Shadows Into Light Two" => "Shadows Into Light Two",
                        "Shanti" => "Shanti",
                        "Share" => "Share",
                        "Shojumaru" => "Shojumaru",
                        "Short Stack" => "Short Stack",
                        "Siemreap" => "Siemreap",
                        "Sigmar One" => "Sigmar One",
                        "Signika" => "Signika",
                        "Signika Negative" => "Signika Negative",
                        "Simonetta" => "Simonetta",
                        "Sirin Stencil" => "Sirin Stencil",
                        "Six Caps" => "Six Caps",
                        "Slackey" => "Slackey",
                        "Smokum" => "Smokum",
                        "Smythe" => "Smythe",
                        "Sniglet" => "Sniglet",
                        "Snippet" => "Snippet",
                        "Sofia" => "Sofia",
                        "Sonsie One" => "Sonsie One",
                        "Sorts Mill Goudy" => "Sorts Mill Goudy",
                        "Special Elite" => "Special Elite",
                        "Spicy Rice" => "Spicy Rice",
                        "Spinnaker" => "Spinnaker",
                        "Spirax" => "Spirax",
                        "Squada One" => "Squada One",
                        "Stardos Stencil" => "Stardos Stencil",
                        "Stint Ultra Condensed" => "Stint Ultra Condensed",
                        "Stint Ultra Expanded" => "Stint Ultra Expanded",
                        "Stoke" => "Stoke",
                        "Sue Ellen Francisco" => "Sue Ellen Francisco",
                        "Sunshiney" => "Sunshiney",
                        "Supermercado One" => "Supermercado One",
                        "Suwannaphum" => "Suwannaphum",
                        "Swanky and Moo Moo" => "Swanky and Moo Moo",
                        "Syncopate" => "Syncopate",
                        "Tangerine" => "Tangerine",
                        "Taprom" => "Taprom",
                        "Telex" => "Telex",
                        "Tenor Sans" => "Tenor Sans",
                        "The Girl Next Door" => "The Girl Next Door",
                        "Tienne" => "Tienne",
                        "Tinos" => "Tinos",
                        "Titan One" => "Titan One",
                        "Trade Winds" => "Trade Winds",
                        "Trocchi" => "Trocchi",
                        "Trochut" => "Trochut",
                        "Trykker" => "Trykker",
                        "Tulpen One" => "Tulpen One",
                        "Ubuntu" => "Ubuntu",
                        "Ubuntu Condensed" => "Ubuntu Condensed",
                        "Ubuntu Mono" => "Ubuntu Mono",
                        "Ultra" => "Ultra",
                        "Uncial Antiqua" => "Uncial Antiqua",
                        "UnifrakturCook" => "UnifrakturCook",
                        "UnifrakturMaguntia" => "UnifrakturMaguntia",
                        "Unkempt" => "Unkempt",
                        "Unlock" => "Unlock",
                        "Unna" => "Unna",
                        "VT323" => "VT323",
                        "Varela" => "Varela",
                        "Varela Round" => "Varela Round",
                        "Vast Shadow" => "Vast Shadow",
                        "Vibur" => "Vibur",
                        "Vidaloka" => "Vidaloka",
                        "Viga" => "Viga",
                        "Voces" => "Voces",
                        "Volkhov" => "Volkhov",
                        "Vollkorn" => "Vollkorn",
                        "Voltaire" => "Voltaire",
                        "Waiting for the Sunrise" => "Waiting for the Sunrise",
                        "Wallpoet" => "Wallpoet",
                        "Walter Turncoat" => "Walter Turncoat",
                        "Wellfleet" => "Wellfleet",
                        "Wire One" => "Wire One",
                        "Yanone Kaffeesatz" => "Yanone Kaffeesatz",
                        "Yellowtail" => "Yellowtail",
                        "Yeseva One" => "Yeseva One",
                        "Yesteryear" => "Yesteryear",
                        "Zeyada" => "Zeyada",
                    );

    return $google_faces;
}

add_action('wp_enqueue_scripts', 'shandora_enqueue_scripts' );

function shandora_enqueue_scripts() {

	wp_register_script('googlemap3', 'http://maps.googleapis.com/maps/api/js?sensor=false', false, false, false);

	if( !wp_script_is('googlemap3', 'enqueued' ) ) {
		wp_enqueue_script( 'googlemap3' );
	}
	
}
?>
