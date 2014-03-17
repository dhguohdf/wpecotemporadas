<?php

function shandora_setup_theme_hook() {

	$prefix = bon_get_prefix();
	$show_search = bon_get_option('enable_search_panel', 'yes');
	if(!is_admin()) {

		add_action("{$prefix}head", "shandora_document_info", 1);

		add_action("{$prefix}before_loop", "shandora_get_page_header", 1);

		if( $show_search == 'yes' ) {
			add_action("{$prefix}before_loop", "shandora_search_get_listing", 2);
		}

		add_action("{$prefix}before_loop", "shandora_open_main_content_row", 5);

		add_action("{$prefix}before_loop", "shandora_get_left_sidebar", 10);

		add_action("{$prefix}before_loop", "shandora_open_main_content_column", 15 );

		add_action("{$prefix}before_loop", "shandora_listing_open_ul", 50 );

		add_action("{$prefix}before_pagination", "shandora_listing_close_ul", 1 );

		add_action("{$prefix}after_loop", "shandora_close_main_content_column", 1);

		add_action("{$prefix}after_loop", "shandora_get_right_sidebar", 5);

		add_action("{$prefix}after_loop", "shandora_close_main_content_row", 10);

		add_action("{$prefix}header_content", "shandora_get_topbar_navigation", 1);

		add_action("{$prefix}header_content", "shandora_get_main_header", 5);

		add_action("{$prefix}header_content", "shandora_get_main_navigation", 10);

		add_action("{$prefix}after_header", "shandora_get_custom_header", 1);

		add_action("{$prefix}footer", "shandora_get_footer", 1);
		
		add_action("{$prefix}footer_widget", "shandora_get_footer_backtop", 1);

		add_action("{$prefix}footer_widget", "shandora_get_footer_widget", 5);

		add_action("{$prefix}footer_widget", "shandora_get_footer_copyright", 10);


	}

}

add_action( 'after_setup_theme', 'shandora_setup_theme_hook', 100);

if( !function_exists('shandora_document_info') ) {
	
	function shandora_document_info() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="utf-8" />
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />

			<?php if ( is_singular() && !is_singular( 'page' ) ) : ?>
				<?php 
					 
					global $post;
					$img = get_the_image( array( 'post_id' => $post->ID, 'attachment' => true, 'image_scan' => true, 'size' => 'thumbnail', 'format' => 'array', 'echo' => false ) );
					$content = wp_trim_words( $post->post_content, $num_words = 50, $more = null );
					$title = $post->post_title;
					if( $post->post_type == 'listing' ) {
						$price = shandora_get_price_meta($post->ID);
						$title = $title . ' - ' . $price;
					}
				?>
				<meta property="og:title" content="<?php echo $title; ?>" />
				<meta property="og:description" content="<?php echo strip_tags( strip_shortcodes( $content ) ); ?>" />
				<meta property="og:url" content="<?php the_permalink(); ?>" />
				<meta property="og:type" content="article" />
			<?php endif; ?>

			<title><?php bon_document_title(); ?></title>
			
			<link rel="profile" href="http://gmpg.org/xfn/11" />
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
			<?php $favico = bon_get_option('favicon', trailingslashit( BON_THEME_URI ) . 'assets/images/icon.png'); ?>
			<link rel="shortcut icon" href="<?php echo $favico; ?>" type="image/x-icon" />

			<?php wp_head(); // wp_head ?>

		</head>
		<?php
	}

}


if( !function_exists('shandora_get_page_header') ) {

	function shandora_get_page_header() {
		if(!is_page_template('page-templates/page-template-home.php')) {
			$show_page_header = bon_get_option('show_page_header');

			if($show_page_header == 'show') {
				bon_get_template_part('block', 'pageheader');
			}
		}
	}

}

if( !function_exists('shandora_search_get_listing') ) {

	function shandora_search_get_listing() {

		if(is_page_template('page-templates/page-template-home.php') ||
			is_singular('listing') || is_singular('agent') || is_singular('car-listing') ||
			is_page_template('page-templates/page-template-all-agent.php') ||
			is_page_template('page-templates/page-template-all-listings.php') ||
			is_page_template('page-templates/page-template-all-car-listings.php') ||
			is_page_template('page-templates/page-template-compare-car-listings.php') ||
			is_page_template('page-templates/page-template-search-car-listings.php') ||
			is_page_template( 'page-templates/page-template-idx.php' ) ||
			is_page_template( 'page-templates/page-template-idx-details.php' ) ||
			is_page_template('page-templates/page-template-search-listings.php') ||
			is_page_template('page-templates/page-template-compare-listings.php') ||
			is_page_template('page-templates/page-template-property-status.php') ||
			is_page_template('page-templates/page-template-car-status.php') ||
		 	is_tax('property-type') || 
		 	is_tax('property-location') || 
		 	is_tax('property-feature') ||
		 	is_tax('body-type') || 
		 	is_tax('car-feature') || 
		 	is_tax('manufacturer') || 
		 	is_tax('dealer-location')) {
			bon_get_template_part('block','searchlisting'); 
		}
	}

}

if( !function_exists('shandora_open_main_content_row') ) {


	function shandora_open_main_content_row() {

		echo '<div id="main-content" class="row">';
	}

}

if( !function_exists('shandora_get_left_sidebar') ) {


	function shandora_get_left_sidebar() {
		$layout = get_theme_mod('theme_layout');
		if(empty($layout)) {
			$layout = get_post_layout(get_queried_object_id());
		}
		if( $layout == '2c-r') {
			if( get_post_type() == 'listing' || get_post_type() == 'car-listing' || 
				is_page_template('page-templates/page-template-all-listings.php') ||
				is_page_template('page-templates/page-template-all-car-listings.php') || 
				is_page_template('page-templates/page-template-search-car-listings.php') ||
				is_page_template('page-templates/page-template-property-status.php') ||
				is_page_template('page-templates/page-template-car-status.php') ||
				is_page_template('page-templates/page-template-search-listings.php') ) {
				get_sidebar('secondary');
			}  else {
				get_sidebar('primary');
			}
		}
	}
}

if( !function_exists('shandora_open_main_content_column') ) {
	
	
	function shandora_open_main_content_column() {

		if(is_page_template( 'page-templates/page-template-home.php' ) ) {
			echo '<div class="column large-12">';
		} else {

			$layout = get_theme_mod('theme_layout');
			if(empty($layout)) {
				$layout = get_post_layout(get_queried_object_id());
			}
			if( $layout == '1c') {
				echo '<div class="'.shandora_column_class().'">';
			} else {				
				echo '<div class="'.shandora_column_class('large-8').'">';
			}
		}
	}
}


if( !function_exists('shandora_listing_open_ul') ) {

	
	function shandora_listing_open_ul() {
		$compare_page = bon_get_option('compare_page');
		
		
		if( ( is_page_template('page-templates/page-template-property-status.php') ||  is_page_template('page-templates/page-template-car-status.php') || get_post_type() == 'listing' || get_post_type() == 'car-listing' || is_page_template('page-templates/page-template-all-listings.php') ||
			is_page_template('page-templates/page-template-all-car-listings.php') || is_page_template('page-templates/page-template-search-listings.php')
			|| is_page_template('page-templates/page-template-search-car-listings.php')) && !is_singular('listing') && !is_singular( 'car-listing' ) && !is_search() ) {
			
			$show_map = 'no';
			$show_listing_count = bon_get_option('show_listing_count', 'no');

			if( ( is_page_template('page-templates/page-template-property-status.php') || get_post_type() == 'listing' || is_page_template('page-templates/page-template-all-listings.php')
			|| is_page_template('page-templates/page-template-search-listings.php')) && !is_singular('listing') &&  !is_singular( 'car-listing' )) {
				$show_map = bon_get_option('show_listings_map');
			}
		?>
		<div class="listing-header">
		<div class="row">
		<?php
		if($show_listing_count) {
			echo '<div class="column large-6"><h3 id="listed-property"></h3></div>';
		} 
		$search_order = isset($_GET['search_order']) ? $_GET['search_order'] : 'DESC';
		$search_orderby = isset($_GET['search_orderby']) ? $_GET['search_orderby'] : 'date';
		?>

			<div class="column large-5 right">
				<form class="custom" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="get" id="orderform" name="orderform">
		            
		            <div class="row">
		                <div class="column large-6 search-order">
		                    <select class="no-mbot" name="search_order" onChange="document.forms['orderform'].submit()">
		                        <option value="ASC" <?php selected( $search_order, 'ASC' );?> ><?php _e('Ascending','bon'); ?></option>
		                        <option value="DESC" <?php selected( $search_order, 'DESC' );?> ><?php _e('Descending','bon'); ?></option>
		                    </select>
		                </div>
		                <div class="column large-6 search-order">
		                    <select class="no-mbot" name="search_orderby" onChange="document.forms['orderform'].submit()">
		                        <option value="price" <?php selected( $search_orderby, 'price' );?> ><?php _e('Price','bon'); ?></option>
		                        <option value="date" <?php selected( $search_orderby, 'date' );?> ><?php _e('Date','bon'); ?></option>
		                    </select>
		                </div>
			                <?php 

				                foreach($_GET as $name => $value) {
							  	  if($name != 'search_order' && $name != 'search_orderby') {
							  	  	$name = htmlspecialchars($name);
									  $value = htmlspecialchars($value);
									  echo '<input type="hidden" name="'. $name .'" value="'. $value .'">';
							  	  }
								}
							?>
		            </div>
		        </form>
			</div>
    	</div>
    	</div>
		<?php	
		if($show_map == 'show') {
			$show_zoom = bon_get_option('show_listings_map_zoom', 'false' );
			if( $show_zoom == 'show' ) { $show_zoom = 'true'; }

			$show_type = bon_get_option('show_listings_map_type', 'false');
			if( $show_type == 'show' ) { $show_type = 'true'; }

	        echo '<div id="listings-map" data-show-zoom="'.$show_zoom.'" data-show-map-type="'.$show_type.'"></div>';
	    }
	    ?>
		<ul class="listings <?php shandora_block_grid_column_class(); ?>" data-compareurl="<?php echo get_permalink($compare_page); ?>">
		<?php
		}
	}
}

if( !function_exists('shandora_listing_close_ul') ) {


	
	function shandora_listing_close_ul() {

		if( (get_post_type() == 'listing' || get_post_type() == 'car-listing' || is_page_template('page-templates/page-template-all-listings.php') ||
			is_page_template('page-templates/page-template-all-car-listings.php') || is_page_template('page-templates/page-template-car-status.php') || is_page_template('page-templates/page-template-property-status.php') ||
			is_page_template('page-templates/page-template-search-car-listings.php') 
		|| is_page_template('page-templates/page-template-search-listings.php')) && !is_singular('listing') && !is_singular( 'car-listing' ) && !is_search() ) {
		
		?>
		</ul>

		<?php
		
		}
	}
}


if( !function_exists('shandora_close_main_content_column') ) {



	function shandora_close_main_content_column() {
		echo '</div><!-- close column -->';
	}
}

if( !function_exists('shandora_get_right_sidebar') ) {

	function shandora_get_right_sidebar() {
		$layout = get_theme_mod('theme_layout');
		if(empty($layout)) {
			$layout = get_post_layout(get_queried_object_id());
		}
		if( $layout == '2c-l' ) {
			if( get_post_type() == 'listing' || get_post_type() == 'car-listing' || 
				is_page_template('page-templates/page-template-all-listings.php') ||
				is_page_template('page-templates/page-template-all-car-listings.php') || 
				is_page_template('page-templates/page-template-search-car-listings.php') ||
				is_page_template('page-templates/page-template-property-status.php') ||
				is_page_template('page-templates/page-template-car-status.php') ||
				is_page_template('page-templates/page-template-search-listings.php')  ) {
				get_sidebar('secondary');
			}  else {
				get_sidebar('primary');
			}
		}
	}
}

if( !function_exists('shandora_close_main_content_row') ) {




	function shandora_close_main_content_row() {
		
		echo '</div><!-- close row -->';
	}

}


if( !function_exists('shandora_get_topbar_navigation') ) {




	function shandora_get_topbar_navigation() {
		?>

		<hgroup id="topbar-navigation" class="hide-for-small">
			<div class="row">
				<?php bon_get_template_part( 'menu', 'topbar' ); // Loads the menu-primary.php template. ?>
				<?php 
					$enable_header_social = bon_get_option('enable_header_social', 'yes');

					if($enable_header_social == 'yes') {
						shandora_get_social_icons();
					}
					 
				?>
			</div>
		</hgroup>

		<?php
	}
}

if( !function_exists('shandora_get_main_header') ) {

	function shandora_get_main_header() {
		$header_style = bon_get_option('main_header_style', 'dark');
	?>
		<hgroup id="main-header" class="<?php echo $header_style; ?> slide">
			<div class="row">
				
				<div class="large-3 column small-centered large-uncentered" id="logo">
					<div id="nav-toggle" class="navbar-handle show-for-small"></div>
					<h1 itemprop="name"><a href="<?php echo home_url(); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><img itemprop="image" src="<?php echo bon_get_option('logo', get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"/></a></h1>
				</div>
				
				<div class="large-9 column hide-for-desktop hide-for-small" id="company-info">
					<div class="row">
						<div class="large-6 column">
							<div class="icon">
								<span class="sha-phone"></span>
							</div>
							<span class="info-title"><?php echo esc_attr(bon_get_option('hgroup1_title')); ?></span>
							<span class="phone"><strong><?php echo esc_attr(bon_get_option('hgroup1_content')); ?></strong></span>
						</div>
						<div class="large-6 column">
							<div class="icon">
								<span class="sha-map"></span>
							</div>
							<span class="info-title"><?php echo bon_get_option('hgroup2_title'); ?></span>
							<address>
								<p><span class="awe-home"></span><?php echo esc_attr(bon_get_option('hgroup2_line1')); ?></p>
								<p><span class="awe-time"></span><?php echo esc_attr(bon_get_option('hgroup2_line2')); ?></p>
							</address>
							
						</div>
					</div>
				</div>
			</div>
		</hgroup> 
	<?php
	}
}

if( !function_exists('shandora_get_main_navigation') ) {



	function shandora_get_main_navigation() {

		$nav_style = bon_get_option('main_header_nav_style', 'dark');
		?>
			<hgroup id="main-navigation" class="<?php echo $nav_style; ?> slide close">
				<?php if( bon_get_option('show_header_search', 'yes') == 'yes' ) { ?>
					<div class="searchform-container">
						<?php shandora_get_searchform('header'); ?>
					</div>
				<?php } ?>
				<div class="nav-block">
					<?php bon_get_template_part( 'menu', 'primary' ); // Loads the menu-primary.php template. ?>
				</div>
				<div class="header-toggler hide-for-small"><div class="toggler-button"></div></div>
			</hgroup>

		<?php
	}

}

if( !function_exists('shandora_get_custom_header') ) {

	function shandora_get_custom_header() {
		if(!is_page_template('page-templates/page-template-home.php')) :
		?>
			<div id="header-background" class="show-for-medium-up"></div>
		<?php
		endif;
	}

}

if( !function_exists('shandora_get_footer') ) {


	function shandora_get_footer() {
		?>
		<div id="action-compare" class="action-compare" data-count="0" data-compare=""></div>

		<?php wp_footer(); ?>

		</body>
		</html>
		<?php
	}

}

if( !function_exists('shandora_get_footer_backtop') ) {


	function shandora_get_footer_backtop() {
		?>

		<a href="#totop" class="backtop" id="backtop" title="<?php _e('Back to Top', 'bon'); ?>"><i class="icon awe-chevron-up"></i></a>

		<?php
	}

}

if( !function_exists('shandora_get_footer_widget') ) {



	function shandora_get_footer_widget() {

		?>
		<div class="footer-widgets footer-inner">

			<div class="row">

				<?php for($i = 1; $i <= 4; $i++ ) { ?>

					<div id="footer-widget-<?php echo $i; ?>" class="<?php echo shandora_column_class("large-3"); ?>">
						
					<?php if ( is_active_sidebar( 'footer'.$i ) ) : ?>

						<?php dynamic_sidebar( 'footer'.$i ); ?>

					<?php else : ?>

						<!-- This content shows up if there are no widgets defined in the backend. -->
						
						<p><?php _e("Please activate some Widgets.", "framework");  ?></p>

					<?php endif; ?>

					</div>

				<?php } ?>

			</div>

		</div>

		<?php
	}

}

if( !function_exists('shandora_get_footer_copyright') ) {


	function shandora_get_footer_copyright() {
		?>
		<div class="footer-copyright footer-inner">

			<div class="row">
				<div class="column large-12 footer-column"><div class="row">
					<div id="social-icon-footer" class="large-4 column large-uncentered small-11 small-centered">
						<?php 

							$enable_footer_social = bon_get_option('enable_footer_social', 'yes');
							
							if($enable_footer_social == 'yes') {
								shandora_get_social_icons(false);
							} else {
								echo "&nbsp;";

							}

						?>
					</div>

					<div id="copyright-text" class="large-8 column large-uncentered small-11 small-centered">
						<div><?php echo bon_get_option('footer_copyright', apply_atomic_shortcode( 'footer_content', '<div class="credit">' . __( 'Copyright &copy; [the-year] [site-link]. Powered by [wp-link] and [theme-link].', 'bon' ) . '</div>') ); ?></div>
					</div>
				</div></div>
			</div>

		</div>
		<?php
	}
}

?>