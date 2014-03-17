<?php
/**
 * Singleton class for the plugin.
 *
 */
class Bon_IDX {

	
	/**
	 * Key used to store and generate listing transients.
	 * @var string
	 */
	public $listingsKey = 'idx-listings';
	
	/**
	 * Key used to store and generate search transients.
	 * @var string
	 */
	public $searchesKey = 'idx-searches';
	
	/**
	 * The dsIDXpress package. Either 'lite' or 'pro'
	 * @var string
	 */
	public $dsIDXPressPackage = 'lite';
	
	/**
	 * dsIDXpress Account ID setting
	 * @var string
	 */
	static public $AccountID = '';
	
	/**
	 * dsIDXpress SearchSetupID setting
	 * @var string
	 */
   static public $SearchSetupID = '';

	/**
	 * default setting
	 * @var array
	 */
	public $default_settings = array();

	public $helper = OBJECT;
	
	/**
	 * dsIDXpress PrivateApiKey setting
	 * @var string
	 */
	static public $PrivateApiKey = '';

	public $idxopts = array();
	
	
	function __construct() {
		global $wp_version, $pagenow, $current_user;
		
		$this->load();

		require(trailingslashit( BON_IDX_DIR ) . '/class-dsapi.php');
		
		
		add_action('init', array($this,'setup_filters'));
		add_action('cron-dsidxpress-flush-cache', array($this,'flush_exipired_transients'));

		add_action('after_setup_theme', array($this,'add_filters'), 11);

		add_action('wp', array($this,'remove_filters'), 1);
		add_action('wp', array($this,'process_idx_data'), 3);

		add_filter('shandora_entry_title', array($this,'replace_post_title'), 99999);

		$this->add_template_actions();
	}

  	
	/**
	 * Instantiates the class
	 */
	public function load() {
	  
		$this->set_account_settings();
		
		$this->default_settings = $this->set_settings();

		$this->require_files();
	}

	

	/**
	 * Require files required by idx
	 *
	 */
	public function require_files() {
		
		if (!class_exists('Bon_IDX_Helper')) {

			require( trailingslashit( BON_IDX_DIR ) . 'helper.php');

			global $bonidxhelper;

			$this->helper = $bonidxhelper;
		}
	 
		if (!function_exists('qp')) {
			
			// This fixes an issue with WP Engine hosting
			define('QP_NO_AUTOLOADER', true);
			
			require( trailingslashit( BON_IDX_DIR ) . 'querypath/qp.php');
		}
		
	  
	}
	
   	

	/**
	 * Get the dsIDXpress account and plugin configuration settings
	 * This saves a few database calls.
	 */
	function set_account_settings() {
		// Set ds-specific settings to prevent DB access multiple times.
		$option_name              = defined("DSIDXPRESS_OPTION_NAME") ? DSIDXPRESS_OPTION_NAME : 'dsidxpress';

		$this->idxopts            = get_option($option_name);

		if(is_array($this->idxopts) && !empty($this->idxopts)) {

			if (isset($this->idxopts['dsIDXPressPackage'])) {
				$this->dsIDXPressPackage = $this->idxopts['dsIDXPressPackage'];
			}

			self::$AccountID         = $this->idxopts['AccountID'];

			self::$SearchSetupID     = $this->idxopts['SearchSetupID'];

			self::$PrivateApiKey     = $this->idxopts['PrivateApiKey'];

		}

		
	}
		
	/**
	 * Get AJAX ready by defining AJAX constants and sending proper headers.
	 * @param string $content_type Type of content to be set in header.
	 * @param boolean $cache Do you want to cache the results?
	 */
	function do_ajax($content_type = 'text/plain', $cache = false) {
		// If it's already been defined, that means we don't need to do it again.
		if (defined('IDX_AJAX_IS_SETUP')) {
			return;
		} else {
			define('IDX_AJAX_IS_SETUP', true);
		}
		
		if (!defined('ZP_NO_REDIRECT')) {
			define('ZP_NO_REDIRECT', true);
		}
		if (!defined('ZP_NO_REDIRECT')) {
			define('DOING_AJAX', true);
		}
		
		
		send_nosniff_header();
		@header('Content-Type: ' . $content_type . ';');
		@header('Accept-Encoding: gzip, deflate');
		
		if ($cache) {
			header('Cache-Control: public, store, post-check=10000000, pre-check=100000;');
			header('Expires: Thu, 15 Apr 2030 20:00:00 GMT;');
			header('Vary: Accept-Encoding');
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", strtotime('-2 months')) . " GMT");
		}
		
		@header('Status Code: 200 OK;');
		@header('X-Robots-Tag:noindex;');
	}
	
	/**
	 * Forces global search defaults
	 * This includes requiring a photo, setting a minimum price, distress types, etc.
	 *
	 * @see $this->remove_filters()
	 */
	function setup_filters() {
		
		if ($this->may_redirect()) {
			return;
		}
		
		$minprice = $this->helper->trim($this->get('minimum_price'));
		if (!empty($minprice)) {
			
			// When someone asks for a lower price, show it.
			// Also, show specific listings if requested.
			if ((empty($_GET['idx-q-PriceMin']) && is_int($_GET['idx-q-PriceMin'])) && empty($_GET['idx-q-MlsNumbers'])) {
				$_GET['idx-q-PriceMin'] = floatval($minprice);
				$globals['PriceMin']    = true;
			}
		}
		
		$onlywithphotos = $this->get('only_with_photos');
		if (!empty($onlywithphotos)) {
			$_GET['idx-q-PhotoCountMin'] = (int) $onlywithphotos;
			$globals['PhotoCountMin']    = true;
		}
		
		if (!empty($globals)) {
			$this->helper->set_global('BonIDXFilters', apply_filters('bon_idx_globalfilters', $globals));
		}
	}
	
	/**
	 * Will dsIDXpress redirect this URL?
	 * @param string $requested URL to check. Defaults to current URL if not specified.
	 * @param array $get You can send an array with query params to check.
	 * @return boolean Whether or not dsIDXpress will redirect the current page.
	 */
	function may_redirect($requested = false, $get = array()) {
		$requested = $requested ? $requested : add_query_arg(array());
		
		if (empty($get)) {
			$get = $_GET;
		}
		
		unset($get['idx-q-Search']);
		unset($get['viewType']);
		
		// Cities, etc.
		if (preg_match('/\/idx\/(city|area|community|zip|tract)\/(.+)?/ism', $requested, $matches)) {
			
			// A single city overrides all other types and will redirect.
			if (isset($get['idx-q-Cities'])) {
				return true;
			}
			
			// If DS senses an issue with bad encoding, they'll take over.
			if (isset($matches[2])) {
				$location = str_replace('/', '', $matches[2]);
				if ($location !== esc_attr($location) || strpos($location, '(') || strpos($location, ')') || strpos($location, ',')) {
					return true;
				}
			}
			
			return false;
		}
		
		// For custom links with dsSearchAgent
		elseif (preg_match('/\/idx\/([0-9]+)(.+?\/)?/ism', $requested, $matches)) {
			
			// This is the full url, like /idx/12345-luxury-real-estate/
			if (isset($matches[2])) {
				return false;
			}
			
			// Otherwise, this is the shortcut URL and will def. redirect
			return true;
			
		}
		
		// We are in the /idx/ territory now.
		$may_redirect = array(
			'idx-q-Cities',
			'idx-q-ZipCodes',
			'idx-q-Areas',
			'idx-q-Communities'
		);
		foreach ($may_redirect as $val) {
			if (isset($get[$val])) {
				return true;
			}
		}
		
		// It's a complex search. No simple answers here!
		return false;
		
	}
	
	function get_global($args) {
		return $this->helper->get_global($args);
	}
	/**
	 * Remove the global settings filters that have been added by $this->setup_filters()
	 *
	 * You can also use remove_filters to remove global settings from an array of search data to only return
	 * the request.
	 *
	 * @see $this->setup_filters()
	 * @param WP $wp The WP object
	 * @param array $arrayToModify Optionally pass an array to remove filters from the search data.
	 * @return [type] [description]
	 */
	function remove_filters($wp = false, $arrayToModify = false) {
		if (!$arrayToModify) {
			$array = $_GET;
		} else {
			$array = $arrayToModify;
		}
		
		// This is to make sure that all filters are shown after a search.
		// DS removes custom query args when redirecting. Ugh.
		if (isset($_GET['idx-q-DaysOnMarketMin']) && (int) $_GET['idx-q-DaysOnMarketMin'] === -1) {
			unset($_GET['idx-q-DaysOnMarketMin']);
			$_GET['idx-q-Search'] = 0;
		}
		
		$globals = $this->get_global('BonIDXFilters');
		
		if (empty($globals)) {
			return;
		}
		
		$newglobals = array_keys($globals);
		
		// If the min price is set and it's the same as the idx settings and it's been set by idx, remove it.
		if (isset($_GET['idx-q-PriceMin']) && floatval($_GET['idx-q-PriceMin']) === floatval($this->get('minimum_price')) && !empty($globals['PriceMin'])) {
			unset($array['idx-q-PriceMin']);
			unset($globals['PriceMin']);
		}
		
		if (isset($_GET['idx-q-PhotoCountMin']) && !empty($globals['PhotoCountMin']) && (int) $_GET['idx-q-PhotoCountMin'] === 1) {
			unset($array['idx-q-PhotoCountMin']);
			unset($globals['PhotoCountMin']);
		}
		
	   
		do_action('bon_idx_remove_filters', $newglobals, $globals);
		
		if (empty($arrayToModify)) {
			$_GET = $array;
		} else {
			$arrayToModify = $array;
		}
		
		if (!empty($wp)) {
			return $array;
		}
	}
	
	/**
	 * Add content to template actions
	 * Template files trigger actions to load content blocks. This function matches actions to the methods.
	 */
	protected function add_template_actions() {

		
		add_action('bon_idx_no_results', array( $this, 'template_no_results' ));

		add_action('bon_idx_results_pagination', array( $this, 'template_results_pagination' ));

		add_action('bon_idx_results_sorting_control', array( $this, 'template_results_sorting_control' ));

		add_action('bon_idx_results_map', array( $this, 'template_results_map' ));

		add_action('bon_idx_results_listings', array( $this, 'template_results_listings' ));

		add_action('bon_idx_results_map_divs', array( $this, 'template_results_map_divs' ));

		add_action('bon_idx_dsidx_javascript_details', array( $this,'template_dsidx_javascript_details'));

		add_action('bon_idx_disclaimer', array( $this,'template_site_disclaimer'));

	}
	
	function template_site_disclaimer() {
        global $wp;
        
        // If we already showed the disclaimer, do this
        if (did_action('bon_idx_after_disclaimer')) {
            return;
        }
        
        echo apply_filters('bon_idx_site_disclaimer_hr', '<hr />');
        
        do_action('bon_idx_before_disclaimer');
        
        // If the listing agent hasn't been shown yet
        if (!did_action('idx_plus_listing_source')) {
            do_action('idx_plus_listing_source');
        }
        
        echo $this->get_global('disclaimer');
        
        do_action('bon_idx_after_disclaimer');
    }

	function template_dsidx_javascript_details() {
        echo $this->helper->get_global('dsidx_javascript_details');
    }
	
	//! Results includes
	function template_no_results() {
		echo sprintf('%s%s%s', '<h3>', __('No Results', 'bon'), '</h3>');
		echo wpautop($this->replace_vars( __('Sorry, we were unable to find any listings that match your request.', 'bon') ));
	}
	
	function template_results_listings() {
		$this->idx_include('results-listings.php', false);
	}
	
	function is_404() {
		global $wp, $wp_query, $post;
		return (is_404() || !empty($_GET['mls-removed']) || !empty($wp->wp_idx_content['is_404']) || !empty($wp_query->is_404) || (isset($wp_query->posts[0]) && preg_match('/<h1>Server Error<\/h1>/ism', $wp_query->posts[0]->post_content)) || (is_object($post) && isset($post->post_content) && isset($post->post_title) && preg_match('/((not-found-dsidx)|Property\sNot\sFound)/ism', $post->post_content) || rtrim(trim($post->post_title)) === 'Property Not Found') // Check for the new dsIDXpress 404 pages
			);
	}
	
	/**
	 * Handle the message shown upon redirection
	 */
	function template_show_404_message() {
		global $wp;
		
		if (!$this->is_404()) {
			return;
		}
		
		$message = $this->get('404_message');
		
		$before_error = '<div class="idx-plus-error">';
		$after_error  = '</div>';
		
		$data  = $this->get_404_data();
		$error = $before_error . wpautop(stripslashes_deep($this->replace_vars($message, $data))) . $after_error;
		
		/**
		 * @param $error The existing error message
		 * @param $data Array: 'data': array of 404 data, 'message': existing 404 message, 'before_error': Start of container div, 'after_error': Close div
		 * @see $this->get_404_data()
		 */
		echo apply_filters('bon_idx_results_404_message_output', $error, compact("data", "message", "before_error", "after_error"));
	}
	
	/**
	 * Process IDX data from 404 URL
	 * @return array All available data for the removed listing
	 */
	function get_404_data() {
		$url  = !empty($_GET['mls-removed']) ? $_GET['mls-removed'] : '';
		$data = $this->process_data_from_url(array(), $url);
		return $data;
	}
	
	function template_results_map() {
		if(!$this->is_pro()) {
		echo apply_filters('bon_idx_results_map_output', '<div id="dsidx-map-control">&nbsp;</div><div id="dsidx-map"></div>');
		} else {
			echo '<div id="listings-map"></div>';
		}
	}
	
	function template_results_sorting_control() {
		$this->idx_include('results-sorting-control.php', false);
	}
	
	/**
	 * Get the current sort order of the search result.
	 *
	 * @return array Array with array(0 => sortorder, 1 => sortby, 2 => notdefault); notdefault is boolean to show whether the current sort order is not the default sort order.
	 */
	function get_sort() {
		$sortbydefault    = apply_filters('bon_idx_default_sortby', 'DateAdded');
		$sortorderdefault = apply_filters('bon_idx_default_sortorder', 'DESC');
		$sortorder        = isset($_GET["idx-d-SortOrders<0>-Direction"]) ? $_GET["idx-d-SortOrders<0>-Direction"] : $sortorderdefault;
		$sortby           = isset($_GET["idx-d-SortOrders<0>-Column"]) ? $_GET["idx-d-SortOrders<0>-Column"] : $sortbydefault;
		
		$notdefault = true;
		if (($sortby === $sortbydefault) && ($sortorderdefault === $sortorder)) {
			$notdefault = true;
		} else if (($sortby !== $sortbydefault) && ($sortorderdefault !== $sortorder)) {
			$notdefault = false;
		} else if ($sortby === $sortbydefault) {
			$notdefault = 'sortby';
		} else {
			$notdefault = 'sortorder';
		}
		return array(
			$sortorder,
			$sortby,
			$notdefault
		);
	}
	
	function template_results_pagination() {
		if(!$this->is_pro()) {
			$this->idx_include('results-pagination.php', false);
		} else {
			$this->idx_include('results-pagination-pro.php', false);
		}
	}
	
	
	/**
	 * Add the empty divs for search result map hover infoboxes
	 */
	function template_results_map_divs() {
?>
  <div id="dsidx-map-hover">
  <div class="dsidx-top-left dsidx-edge"></div>
  <div class="dsidx-top-center dsidx-edge"></div>
  <div class="dsidx-top-right dsidx-edge"></div>
  <div class="dsidx-middle-right dsidx-edge"></div>
  <div class="dsidx-bottom-right dsidx-edge"></div>
  <div class="dsidx-bottom-center dsidx-edge"></div>
  <div class="dsidx-bottom-left dsidx-edge"></div>
  <div class="dsidx-middle-left dsidx-edge"></div>
  <div class="dsidx-container">
  <div class="dsidx-inner-container">
  <div class="dsidx-photo"></div>
  <div class="dsidx-text">
  <div class="dsidx-line-1 dsidx-header"></div>
  <div class="dsidx-line-2 dsidx-header"></div>
  <div class="dsidx-line-3"></div>
  <div class="dsidx-line-4"></div>
  <div class="dsidx-line-5"></div>
  <div class="dsidx-line-6"></div>
  </div>
  <div class="dsidx-icon-container"></div>
  <div class="dsidx-clear"></div>
  </div>
  </div>
  </div>
  <?php
	}
	
	
	/**
	 * wp_redirect was applying filters that were screwing up what should have been a simple redirect.
	 * Taken from /wp-/pluggable.php
	 * @see wp_redirect()
	 */
	function redirect($location, $status = 301, $source = '') {
		global $is_IIS;
		
		$location = apply_filters('bon_idx_redirect', $location, $status, $source);
		$status   = apply_filters('bon_idx_redirect_status', $status, $location, $source);
		
		if (!$location)
			return false;
		
		$location = str_replace(array(
			'<',
			'>'
		), array(
			'%3C',
			'%3E'
		), $location);
		
		$location = wp_sanitize_redirect($location);
		
		if (!$is_IIS && php_sapi_name() != 'cgi-fcgi') {
			status_header($status); // This causes problems on IIS and some FastCGI setups
		}
		
		header("Location: $location", true, $status);
		exit();
	}
	
	/**
	 * Add the content filters that enable idx
	 *
	 * Sets up the template system by replacing `the_content` with `$this->the_content_filter()`.
	 *
	 * Processes globals in the `wp` hook.
	 */
	public function add_filters() {
		global $post, $wp;

		add_filter('body_class', array($this,'body_class'), 500);
		

		add_action('wp', array($this,'process_globals'));

		// This is the worker for the whole template system.
		add_filter('the_content', array($this,'the_content_filter'), 20);
		
		if ($this->get('listings_view_photosize')) {
			add_filter('the_content', array($this,'listings_view_photosize'), 999);
		}

	}
	
	/**
	 * Change the heading of the page
	 *
	 * This modifies the page title (the `H1` or `H2`, not the `title`) of the current page.
	 * @global WP Holds `$wp->wp_idx_content` data
	 * @global WP_Post
	 */
	function replace_post_title($title) {
		global $wp, $post;
		
		if (empty($wp->wp_idx_content) || !isset($wp->wp_idx_content['type'])) {
			return $title;
		}

		if ($this->get_mls()) {
			$title .= '<br />';
			return $title;
		} else {
			return $title;
		}
	}
	

	function process_paging_control($content = '') {
		preg_match($this->get_regex('process-paging-control'), $content, $matches);
		if (!isset($matches[1])) {
			$pagination = isset($matches[0]) ? $matches[0] : '';
		} else {
			
			$original = $matches[1];

			$firstlink = '';
			$prevlink  = '';
			$nextlink  = '';
			$lastlink  = '';
			
			$paging_control = explode('|', $matches[1]);
			
			
			if (isset($paging_control[1])) {
				preg_match($this->get_regex('paging-control-link'), $paging_control[1], $matches);
				$firstlink = isset($matches[1]) ? $matches[1] : false;
			}
			
			if (isset($paging_control[2])) {
				preg_match($this->get_regex('paging-control-link'), $paging_control[2], $matches);
				$prevlink = isset($matches[1]) ? $matches[1] : false;
			}
			
			if (isset($paging_control[3])) {
				preg_match($this->get_regex('paging-control-link'), $paging_control[3], $matches);
				$nextlink = isset($matches[1]) ? $matches[1] : false;
			}
			
			if (isset($paging_control[4])) {
				preg_match($this->get_regex('paging-control-link'), $paging_control[4], $matches);
				$lastlink = isset($matches[1]) ? $matches[1] : false;
			}
			
			$pagination = array(
				'start' => $this->idx('start'),
				'end' => $this->idx('end'),
				'total' => $this->idx('total'),
				'original' => $original,
				'first' => isset($paging_control[1]) ? $paging_control[1] : '',
				'firstlink' => $firstlink,
				'previous' => isset($paging_control[2]) ? $paging_control[2] : '',
				'prevlink' => $prevlink,
				'next' => isset($paging_control[3]) ? $paging_control[3] : '',
				'nextlink' => $nextlink,
				'last' => isset($paging_control[4]) ? $paging_control[4] : '',
				'lastlink' => $lastlink
			);

		}
		
		return $pagination;
	}
	
	/**
	 * Is this installation running dsIDXpress Pro or lite?
	 *
	 * You can force pro behavior by being logged in as an admin and adding `?pro` to the URL
	 *
	 * @return boolean True: running pro; false: running lite
	 */
	function is_pro() {
		return (!empty($this->dsIDXPressPackage) && $this->dsIDXPressPackage === 'pro') || (current_user_can('administrator') && isset($_GET['pro']));
	}
	
	/**
	 * Process the listings inside search results.
	 * @param string $content The HTML of the search results.
	 * @return array listings array
	 */
	function process_results_listings($content = '') {
		preg_match($this->get_regex('dsidx-listings'), $content, $matches);
		
		if (isset($matches[1])) {
			
			preg_match_all($this->get_regex('dsidx-listing', 'pro_listing'), $matches[1], $listings);
			
			$result = array();
			$i      = 0;
			
			// This is the data from the javascript. Very accurate.
			$listingsdata = $this->idx('listingsdata');
			
			foreach ($listings[1] as $key => $value) {
				preg_match($this->get_regex('listing-data', 'pro_listing-data'), $value, $listing);
				
				foreach ($listing as $k => $v) {
					if (!is_numeric($k)) {
						$result[$i][$k] = $this->helper->trim($v);
					}
				}
				
				if (isset($listingsdata[$i])) {
					foreach ($listingsdata[$i] as $key => $val) {
						$result[$i][$key]             = $val;
						$result[$i][strtolower($key)] = $val;
					}
				}
				
				if (!empty($result[$i]['photo'])) {
					preg_match($this->get_regex('title_from_photo'), $result[$i]['photo'], $match);
					$result[$i]['title'] = $result[$i]['original_title'] = isset($match[1]) ? $this->helper->trim($match[1]) : '';
				}
				
				if (!empty($result[$i]['secondary'])) {
					$secondary = $result[$i]['secondary'];
					
					$beds_regex = $this->is_pro() ? 'pro_beds' : 'beds';
					preg_match($this->get_regex($beds_regex), $secondary, $match);
					
					$result[$i]['beds']  = isset($listingsdata[$i]->BedsShortString) ? $listingsdata[$i]->BedsShortString : (isset($match[1]) ? $this->helper->trim($match[1]) : '');
					$result[$i]['baths'] = isset($listingsdata[$i]->BathsShortString) ? $listingsdata[$i]->BathsShortString : (isset($match[2]) ? $this->helper->trim($match[2]) : '');
					
					if (isset($listingsdata[$i]->ImprovedSqFt)) {
						$result[$i]['homesizeraw']    = $this->helper->raw_number($listingsdata[$i]->ImprovedSqFt);
						$result[$i]['homesize_title'] = '';
						$result[$i]['homesizetype']   = apply_filters('bon_idx_homesize_type', 'sqft');
						$result[$i]['homesize']       = sprintf('%s %s', $listingsdata[$i]->ImprovedSqFt, $result[$i]['homesizetype']);
					} else {
						preg_match($this->get_regex('homesize'), $secondary, $match);
						if (isset($match[1])) {
							$result[$i]['homesizeraw']    = $this->helper->raw_number($match[1]);
							$result[$i]['homesize_title'] = $this->helper->trim($match[1]);
							$result[$i]['homesize']       = $this->helper->trim($match[2]);
						} else {
							$result[$i]['homesize'] = '';
						}
					}
					
					
					if (isset($listingsdata[$i]->LotSqFt)) {
						$result[$i]['lotsizeraw']  = $this->helper->raw_number($listingsdata[$i]->LotSqFt);
						$result[$i]['lotsizetype'] = apply_filters('bon_idx_lot_size_type', 'sqft');
						$result[$i]['lotsize']     = sprintf('%s %s', $this->helper->number_format($result[$i]['lotsizeraw']), $result[$i]['lotsizetype']);
					} else {
						preg_match($this->get_regex('lotsize'), $secondary, $match);
						if (isset($match[1])) {
							$result[$i]['lotsizeraw']  = $this->helper->raw_number($match[1]);
							$result[$i]['lotsize']     = $this->helper->number_format($result[$i]['lotsizeraw']);
							$result[$i]['lotsizetype'] = strip_tags($this->helper->trim(preg_replace('/[0-9,\.]/ism', '', $this->helper->trim($match[1]))));
						}
					}
					
					$result[$i]['yearbuilt']    = $this->helper->match($this->get_regex('yearbuilt'), $secondary);
					$result[$i]['totalparking'] = $this->helper->match($this->get_regex('totalparking'), $secondary);
					
					$result[$i]['daysonmarket'] = $this->helper->match($this->get_regex('daysonmarket'), $secondary);
					
					preg_match($this->get_regex('walkscore'), $secondary, $match);
					$result[$i]['walkscore'] = isset($match[1]) ? $this->helper->trim(str_replace('<a ', '<a rel="nofollow" ', $match[1])) : '';
					
					$result[$i]['sqfootsource'] = $this->helper->match($this->get_regex('sqfootsource'), $secondary);
					
					$result[$i]['listingsource'] = $listingsdata[$i]->ListingAttributionText;
					preg_match($this->get_regex('listing_source_backup'), '>' . $result[$i]['listingsource'] . '<', $match);
					$result[$i]['listingtext']   = isset($match['listingtext']) ? strip_tags($this->helper->trim($match['listingtext'])) : '';
					$result[$i]['listingoffice'] = isset($match['listingoffice']) ? strip_tags($this->helper->trim($match['listingoffice'])) : '';
				}
				
				$result[$i]['idxicon'] = !$this->helper->is_empty($listingsdata[$i]->IdxIconUri) ? '<img src="' . $listingsdata[$i]->IdxIconUri . '" alt="" width="120" height="40" class="dsidx-idx-icon" />' : NULL;
				
				$result[$i] = $this->process_data_from_url($result[$i], $result[$i]['url']);
				ksort($result[$i]);
				$result[$i] = apply_filters('bon_idx_process_results_listing', $result[$i]);
				$i++;
			}
		} elseif (isset($matches[0])) {
			$result = $matches[0];
		}
		
		return isset($result) ? $result : array();
	}
	
	
	
	/**
	 * An array of regular expression patterns for grabbing page data
	 *
	 * idx grabs most of its data using regex patterns. This is a list of those patterns.
	 *
	 * @todo Convert most regex to QueryPath
	 *
	 * @return array Array of all regex patterns
	 */
	function get_regexes() {
		
		$regexes = apply_filters('bon_idx_get_regexes', array(
			'listing_source' => '/<p\s+?id="dsidx-listing-source">(.*?)<\/p>/ism',
			'listing_source_backup' => '/>(?:\s+)?((?P<listingtext>(?:Brought to you|Listing|Listed)(?:\s+)?(?:with|by|from|provided by|provided courtesy of|provided via))(?P<listingoffice>.*?))(?:\s+)?</ism',
			'dsidx_javascript_details' => '/<script(?:.*?)>(\s+?dsidx.activate.*?)<\/script>/ism',
			'dsidx-listings' => '/<ol id="dsidx-listings"(?:.*?)>(.*?)<\/ol>/ism',
			'dsidx-listing' => '/<li\ class="dsidx-listing">(.*?)<\/li>(?:.*?)<li><hr\s?\/><\/li>/ism',
			'process_listing_title' => '/(?:\s+)?(.*?\,?(\s?Unit.*?)?),\s(.*?),\s([A-Z]{2})\s([0-9_-]{5,11})\s\(MLS\s?\#\s?(.*?)\)\s?(.+)?/xism',
			'photo_unavailable' => '/(photo\-unavailable|no-photos-available)/ism',
			'listing-data' => '/(?:.*?)<div\ class="dsidx-photo">(?:\s+)?<a(?:.*?)>(?P<photo>.*?)<\/a>(?:.*?)<\/div>(?:.*?)<div\ class="dsidx-address">(?:.*?)?<a href="(?P<url>.*?)"(?:.*?)?>(?P<address>.*?),(?:.*?)<\/div>(?:.*?)<div\ class="dsidx-price">(?P<price>.*?)<\/div>(?:.*?)<div\ class="dsidx-secondary-data">(?P<secondary>.+)<\/div>/ism',
			'title_from_photo' => '/(?:.*?title=[\'"].*? of )(.*?)[\'"]/ism',
			'list_explode' => '/<li(.*?)>(.*?)<\/li>/ism',
			'related_details' => '/(?:.*?)<b>(?P<price>.*?):(?:.*?)<a href="(?P<url>.*?)"(?:.*?)?>(?P<title>.*?)<\/a>(?:.*?)?<\/b>(?:.*?)(?P<image><img(?:.*?)>)/ism',
			'beds_related' => '/([0-9]{1,3})\ beds?,(.*?)<\/div>/ism',
			'beds' => '/([0-9]{1,3})\ beds?,(.*?)<\/div>/ism',
			'homesize_related' => '/(Finished sq\.ft|Home size)\s?:(.*?)<\/div>/ism',
			'homesize' => '/(Finished sq\.ft|Home size)\s?:(.*?)<\/div>/ism',
			'lotsize_related' => '/Lot\ size\:(.*?)<\/div>/ism',
			'lotsize' => '/Lot size\:(.*?)?<\/div>/ism',
			'yearbuilt' => '/Year built\:(.*?)?<\/div>/ism',
			'totalparking' => '/Parking spots\:(.*?)?<\/div>/ism',
			'dsidx-secondary-data' => '/(<table.*?id="dsidx-secondary-data".*?>.*?<\/table>)/ism',
			'latitude' => '/details\.latitude\s?\=.*?([0-9\-\.]+).*?;/ism',
			'longitude' => '/details\.longitude\s?\=.*?([0-9\-\.]+).*?;/ism',
			'photocount' => '/details\.photoCount\s?\=.*?([0-9]+).*?;/ism',
			'photouribase' => '/details\.photoUriBase\s?\=\s?\'(.*?)\';/ism',
			'photocaption' => '/details\.fullSizePhotosCaption\s?\=\s?\'(.*?)\';/ism',
			'contentdomid' => '/details\.contentDomId\s?\=\s?[\'"](.*?)[\'"];/ism',
			'propertyid' => '/name\="propertyID"\ value="(.*?)"/ism',
			'title_city' => '/Real estate in the city of/ism',
			'listingsdata' => "/dsidx.dataSets\['results'\]\ =\s?\[(\{.*?\})\]/ism",
			'daysonmarket' => '/Days on market\:(.*?)?<\/div>/ism',
			'propertytypes' => '/<strong>Property Type\(s\)(?:\s+?)?<\/strong>:\s+?(.*?)<\/div>/ism',
			'homesize_source' => '/(.*?)(?:Source:\s?)(.+)/ism',
			'walkscore' => '/Walk Score(?:.*?)\:(.*?)<\/div>/ism',
			'sqfootsource' => '/Square foot source\:(?:.*?\:)?(.*?)<\/div>/ism',
			'dsidx-paging-control' => '/<div class="dsidx-paging-control"(?:.*?)>(?:.*?)(\d+)(?:.*?)(\d+)(?:.*?)(\d.*?)\s/ism',
			'process-paging-control' => '/<div class="dsidx-paging-control"(?:.*?)>(.*?)<\/div>/ism',
			'paging-control-link' => '/<a(?:.*)href\s?=\s?[\'"](.*?)?[\'"](?:.*?)(?:\s?class="(.*?)\s?")?>(.*?)<\/a>/ism',			
			'process_table' => '/(?:.*?)<tr(?:.*?)?>(?:.*?)?<th(?:.*?)?>(.*?)<\/th>(?:.*?)?<td(?:.*?)?>(.*?)<\/td>(?:.*?)?<\/tr>/ism',
			'sorting_control' => '/(<div class="dsidx-sorting-control"(?:.*?)>)(.*?)<\/div>/ism',
			'disclaimer' => '/<div\s+?id="dsidx-disclaimer">(.*?20[0-9]{2}.(?:<\/p>)?)(?:\s+)?<\/div>/ism',
			'disclaimer_backup' => '/<div\s+?id="dsidx-disclaimer">(.*?)<\/div>/ism',
			'data_from_url' => '/mls\-(?P<MlsNumber>.*?)\-(?P<address>.*?)_(?P<streettype>%s)_(?P<city>.*?)_(?P<state>%s)_(?P<zip>[0-9_]+)/ism',
			'data_from_url_zip' => '/mls\-(.*?)\-(.+)/ism',
			'pro_search_tabs' => '/(<ul class="dsidx-tabs(?:.*?)>\s+?.+?\s+?<\/ul>)/ism',
            'pro_visitor_javascript' => '/(dsidx.visitor\s?=\s?.*?;)/ism',
            'pro_midx_javascript' => '/<script(?:.*?)>(?:\s+?)?(var\s+?\_ds\_midx.*?)<\/script>/ism',
            'pro_call_to_action' => '/<div class="dsidx\-call\-to\-action"(?:.*?)>\s+?(.*?)\s+?<\/div>/ism',
            'pro_listing_tag' => '/<div class="(dsidx\-listing\-tag.*?)"(?:.*?)>(.*?)<\/div>/ism',
            'pro_walkscore' => '/<div.*?id="dsidx-walkscore-notice" class="dsidx-alert-label">\s+?(.*?)\s+?<\/div>(?:.*)<div class="dsidx-alert-message">(<a href="(http:\/\/www.walkscore.com\/(?:.*?))"(?:.*?)?>([0-9]+?)<\/a>(?:.*?)<span>(?P<walkscoredescription>.*?)<\/span>.+?)<\/div>/ism',
            'pro_enticement' => '/<div\sclass="dsidx-enticement">(?:.*?)(<div\sclass="dsidx-enticement-data">(?P<data>.*?)<\/div>(?:.*?)<\/div>)(?:.*?)?<div\sclass="dsidx-enticement-footer">(?P<footer>.*?)<\/div>(?:.*?)?<\/div>/ism',
            'pro_enticement_count_and_location' => '/We found (?P<count>\d+) listings? for you in the ((?P<area>.*?)area|city of (?P<city>.*?)|(?P<zip>\d+)\szip code|(?P<community>.*?)community|(?P<tract>.*?)\stract)$/ism',
            'pro_enticement_listing' => '/(.*?)\shas\s?(?P<beds>\d{0,10})\s?beds? and\s?(?P<baths>\d{0,10})?\s?baths?/ism',
            'pro_listing' => '/<li\ class="dsidx-listing-container".*?>(.*?)<\/li>/ism',
            'pro_listing-data' => '/(?:.*?)<div\ class=[\'"]dsidx-photo[\'"]>\s+?<a(?:.*?)>(?P<photo>.*?)<\/a>(?:.*?)<\/div>(?:.*?)<div\ class=[\'"]dsidx-address[\'"]>(?:.*?)?<a href=[\'"](?P<url>.*?)[\'"](?:.*?)?>(?P<address>.*?)\s?<span>(?P<city>.*?)?,(?:(?P<state>.*?)<\/span>)?(?:<\/a>.*)?<\/div>(?:.*?)<div(?:.*?)?class=[\'"]dsidx-price[\'"](?:.*?)?>(?P<price>.*?)<\/div>(?:.*?)<div(?:.*?)?class=[\'"]dsidx-secondary-data[\'"](?:.*?)?>(?P<secondary>.+)<\/div>(?:.+<\/li>)?/ism',
            'pro_alert' => '/<div.*?id="(.*?)" class="dsidx-alert-color">(.*?)<div id="(.*?)" class="dsidx-alert-label"><span.*>(?P<alertlabel>.*?)<\\/span><\\/div>.*?<div class="dsidx-alert-message">(?P<alertmessage>.*?)<\/div>\\s+<\/div>/uism',
            'pro_paging-control' => '/Page\s(\d+)\sof\s(\d+)\s/ism',
            'pro_beds' => '/([0-9]{1,3})(?:<\/span>)\ beds\&nbsp;(.*?)<\/div>/ism',
            'pro_zestimate' => '/\s+?<div.*?id="dsidx-zestimate".*?<div class="dsidx-alert-message">\s+?(?P<span><span>(?P<spantext>.*?)(?:\&nbsp\;)?<\/span>\s+?(?P<link><a href="(?P<linkurl>.*?)".*?>(?P<linktext>.*?)<\/a>)?)?\s+?<\/div>\s+<\/div>/is',
            'pro_rentzestimate' => '/\s+?<div.*?id="dsidx-rentzestimate".*?<div class="dsidx-alert-message">\s+?(?P<span><span>(?P<spantext>.*?)(?:\&nbsp\;)?<\/span>\s+?(?P<link><a href="(?P<linkurl>.*?)".*?>(?P<linktext>.*?)<\/a>)?)?\s+?<\/div>\s+<\/div>/is',
            'pro_openhouse' => '/<div id="dsidx-open-house".*?dsidx-alert-message">(.*?)<\/div>\s+<\/div>/is'
		));
		
		return $regexes;
	}
	
	/**
	 * Get a specific regex pattern
	 * @uses $this->get_regexes()
	 * @param string $name key for the regex
	 * @param string $pro_name If using pro, use this regex instead.
	 * @return string|bool If isset, returns regex pattern. If doesn't exist, returns false.
	 *
	 */
	function get_regex($name = '', $pro_name = '') {
		$regexes = $this->get_regexes();
		
		if ($this->is_pro() && !empty($pro_name)) {
			if (isset($regexes[$pro_name])) {
				return $regexes[$pro_name];
			}
		}
		if (isset($regexes[$name])) {
			return $regexes[$name];
		}
		return false;
	}
	
	/**
	 * Set some global variables so that they're available in template files.
	 *
	 * Uses <a href="http://api.querypath.org/docs/">QueryPath</a> for parsing HTML
	 * @uses htmlqp()
	 * @param string $content Page content buffer used to grab data
	 * @return string Page content buffer
	 */
	function process_globals($content = null) {
		global $wp, $post;
		if (!is_idx(true)) {
			return $content;
		}

		if(!$post) {
			return $content;
		}
		
		$content = $post->post_content;

		$qp = htmlqp($content);
		
		// Easier to hook in
		do_action('bon_idx_process_globals', $content, $qp);
		
		if ($this->helper->is_listing()) {

			
		} else {
			
			// Listings
			$this->helper->set_global('results_listings', $this->process_results_listings($content));
			
			// Pagination
			$this->helper->set_global('paging_control', $this->process_paging_control($content));
			
			// Sorting Controls
			$this->helper->set_global('results_sorting_control', str_replace('Sorted by', $this->get('text_sorting_control'), $qp->find('.dsidx-sorting-control')->xhtml()));
		}
		

		 // Javascript Details
        preg_match($this->get_regex('dsidx_javascript_details'), $content, $matches);
        $this->helper->set_global('dsidx_javascript_details', isset($matches[1]) ? '<script type="text/javascript">' . $matches[1] . '</script>' : $matches[0]);
        
        // Disclaimer
        preg_match($this->get_regex('disclaimer'), $content, $matches);
        if (!isset($matches[1])) {
            preg_match($this->get_regex('disclaimer_backup'), $content, $matches);
        }
        if (isset($matches[1])) {
            $this->helper->set_global('disclaimer', $matches[0]);
        }


		return $content;
	}
	

	/**
	 * Add CSS classes to the body tag
	 *
	 * You can do cool stuff, like show a different background image for different ZIPs or cities.
	 *
	 * @param array $classes Array of existing body class values
	 * @return array Modified array of classes
	 */
	function body_class($classes = '') {
		global $wp;
		
		#$this->r($wp);
		
		$addClasses = $this->get_params();
		
		$unset = array(
			'url',
			'photo',
			'title',
			'original_title',
			'excerpt',
			'longitude',
			'latitude',
			'lotsize',
			'homesize',
			'photouribase',
			'daysonmarket',
			'sqfeetmin',
			'sqfootsource',
			'photocount',
			'lotsizetype',
			'lotsizeraw',
			'price',
			'lastupdated',
			'yearbuilt',
			'address',
			'photolink',
			'transient',
			'virtualtour',
			'lastupdates',
			'sqfeetmin',
			'ListingOfficeID',
			'MlsNumbers',
			'description',
			'Cities',
			'SortOrders<0>-Direction',
			'SortOrders<0>-Column'
		);
		
		foreach ($unset as $un) {
			unset($addClasses[$un]);
		}
		
		foreach ($addClasses as $idxClass => $empty) {
			if (!empty($wp->wp_idx_content[$idxClass])) {
				
				$value = $wp->wp_idx_content[$idxClass];
				

				if (is_array($value)) {
					continue;
				}
				
				switch ($idxClass) {
					case 'homesize':
					case 'lotsize':
					case 'baths':
					case 'totalparking':
						$value = $this->helper->raw_number($value);
						break;
					
					case 'page':
						if (empty($value) || (int) $value == 1) {
							continue;
						}
						$value = (int) $value;
						break;
					
					case 'price':
						$value = preg_replace('/[^A-Za-z0-9-\s]/ism', '', $value);
						break;
				}
				
				$values = is_array($value) ? $value : explode(',', $value);
				foreach ($values as $idxc) {
					if (!empty($idxc)) {
						$classes[] = strtolower('idx-' . $idxClass . '-' . sanitize_title($idxc));
					}
				}
			}
		}
		
		$classes[] = 'idx-listings';
		
		return $classes;
	}
	
   
	
	/**
	 * Calculate the longitude and latitude or a radius around a location
	 *
	 * Returns an array with the following keys: `idx-q-LatitudeMax`, `idx-q-LongitudeMax`, `idx-q-LatitudeMin`, `idx-q-LongitudeMin`.
	 * All of these are required for dsIDXpress to produce a radius search.
	 *
	 * @param string|float $longitude The longitude to calculate from
	 * @param string|float $latitude The latitude to calculate from
	 * @param integer $miles Number of miles for the radius
	 * @return array Array with key being the dsIDXpress search parameter for longs/lats, the value being the radius calculation.
	 */
	function get_radius($longitude, $latitude, $miles = 5) {
		
		# A degree of latitude is approximately 69.172 miles, and a minute of latitude is approximately 1.15 miles
		$degrees = $miles / 69.172 / 100;
		$minutes = $miles / 1.15 / 100;
		
		$settings = array(
			'idx-q-LatitudeMax' => floatval($latitude) + $degrees,
			'idx-q-LongitudeMax' => floatval($longitude) + $minutes,
			'idx-q-LatitudeMin' => floatval($latitude) - $degrees,
			'idx-q-LongitudeMin' => floatval($longitude) - $minutes
		);
		
		return apply_filters('bon_idx_get_radius', $settings, array(
			'longitude' => $longitude,
			'latitude' => $latitude,
			'miles' => $miles
		));
	}
	
	
	/**
	 * Get the base URL for pagination links
	 * @param null|integer $page The current page number
	 * @return string URL of search result base.
	 */
	function get_pagenum_link($page = null) {
		global $wp;
		
		if (is_null($page)) {
			return preg_match('/page-[0-9]+/ism', $wp->request) ? preg_replace('/\/page-[0-9]+/ism', '/%_%', add_query_arg(array())) : add_query_arg(array(), site_url(untrailingslashit($wp->request) . '/%_%'));
		} else {
			$append = !empty($page) ? '/page-' . $page : '/';
			return preg_match('/page-[0-9]+/ism', $wp->request) ? preg_replace('/\/page-[0-9]+/ism', $append, add_query_arg(array())) : add_query_arg(array(), site_url(untrailingslashit($wp->request) . $append));
		}
	}
	
	/**
	 * Is the current traffic from a referral?
	 * @return boolean Yes: it is referred; No, is not a referrral
	 */
	function is_referred() {
		return ($this->get('first_click_free') && (isset($_SERVER['HTTP_REFERER']) && !preg_match('/' . preg_quote($_SERVER['SERVER_NAME']) . '/ism', $_SERVER['HTTP_REFERER'])));
	}
	
	
	/**
	 * Get the registration key for idx
	 * @return string The key
	 */
	public function get_key() {
		return $this->get('registrationkey');
	}
	
	/**
	 * 
	 *
	 * @see Bon_IDX_Settings::get()
	 */
	public function get($key = false, $trim = false) {
		 if(array_key_exists($key, $this->default_settings)) {
			return $this->default_settings[$key];
		}
	}
	
	function listings_view_photosize($content) {
		global $wp;
		
		if (isset($wp->wp_idx_content) && $wp->wp_idx_content['type'] !== 'listing') {
			$content = str_replace('0-medium.jpg"', '0-' . $this->get('listings_view_photosize') . '.jpg" class="idx-plus-custom-size"', $content);
		}
		return $content;
	}
	
	public function listing_show_box($name, $boxes = array()) {
		if (empty($boxes)) {
			$boxes = $this->get('listing_show_boxes');
		}
		$boxes = is_array($boxes) ? $boxes : array(
			$boxes
		);
		$name  = sanitize_title($this->helper->trim($name));
		return in_array($name, $boxes);
	}
	
	/**
	 * Get an array of all property types
	 *
	 * @param boolean $cache Use cached data?
	 * @return StdObject Object. Keys include: `SearchSetupPropertyTypeID` (int), `IsSearchedByDefault` (bool), `DisplayName` (string)
	 */
	function get_property_types($cache = true, $apply_filters = true) {
		$propertyTypes = null;
		
		if ($cache && !(current_user_can('administrator') && isset($_GET['cache']))) {
			$propertyTypes = get_transient("bon_idx_property_types");
			if (!empty($propertyTypes)) {
				$propertyTypes = maybe_unserialize($propertyTypes);
				return $apply_filters ? apply_filters('bon_idx_property_types', $propertyTypes) : $propertyTypes;
			}
		}
		
		$propertyTypes = dsSearchAgent_ApiRequest::FetchData("AccountSearchSetupPropertyTypes", array(), false, 60 * 60 * 24);
		$propertyTypes = $propertyTypes["response"]["code"] == "200" ? json_decode($propertyTypes["body"]) : null;
		
		set_transient("bon_idx_property_types", maybe_serialize($propertyTypes), 60 * 60 * 24 * 120);
		
		return $apply_filters ? apply_filters('bon_idx_property_types', $propertyTypes) : $propertyTypes;
	}
	
	//! The Content Filter
	function the_content_filter($content) {

		global $wp, $post;
		$output = '';
		
		$post->post_title = $this->idx('original_title');
		
		$output .= apply_filters('bon_idx_before_content', '', 'before_content');
		
		if (isset($wp->wp_idx_content['type']) && $wp->wp_idx_content['type'] == 'listing') {

			$output = $content;

		} elseif (isset($wp->wp_idx_content['type'])) {
			$content = $this->idx_include('results.php');
			$content = $this->replace_vars($content);
			$output .= apply_filters('bon_idx_results', $content, $wp);
		} else {
			$output = $content;
		}
		
		$output .= apply_filters('bon_idx_after_content', '', 'after_content');
		
		return apply_filters('bon_idx_content', $output);
	}

	
   
	/**
	 * Localizes a script
	 *
	 * Taken from class.wp-scripts.php so that we can get the code
	 * without adding it to the footer - this way we can add necessary code that
	 * prevents the script from being cached.
	 *
	 * @param string $handle Handle of the script
	 * @param string $object_name Name of the object to be generated
	 * @param array $l10n Data to pass to the script as an object
	 * @return string JS var
	 */
	function localize($handle, $object_name, $l10n) {
		if (is_array($l10n) && isset($l10n['l10n_print_after'])) { // back compat, preserve the code in 'l10n_print_after' if present
			$after = $l10n['l10n_print_after'];
			unset($l10n['l10n_print_after']);
		}
		
		foreach ((array) $l10n as $key => $value) {
			if (!is_scalar($value))
				continue;
			
			$l10n[$key] = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
		}
		
		$script = "var $object_name = " . json_encode($l10n) . ';';
		
		if (!empty($after))
			$script .= "\n$after;";
		
		return $script;
	}
	
	
	/**
	 * Include template files
	 *
	 * Include template files from either the idx `/template/` folder or, if the location exists, from the user's theme `/idx-plus/` directory.
	 *
	 * @param string $file The name of the file
	 * @param boolean $ob_start Return instead of echo the contents of the file
	 * @param string $path Override the path going by default to the templates sub-directory. No trailing slash!
	 * @param mixed $obhect If you would like to pass additional data to the included file, pass here.
	 * @return mixed If $ob_start, returns the contents of the file. Otherwise, nothing's returned, only echoed.
	 */
	public function idx_include($file, $ob_start = true) {
		if ($ob_start) {
			ob_start();
		}
		
		include( trailingslashit( BON_IDX_DIR ) . "templates/{$file}");
		 
		if ($ob_start) {
			$content = ob_get_clean();
			return do_shortcode($content);
		}
	}
	
	
	
	/**
	 * Get the data about the listing or the search.
	 *
	 * <code>
	 * $city = $this->idx('city');
	 * </code>
	 *
	 * @param string $key The name of the data
	 * @param array $idx You can pass your own array of data, or get the data from $wp variable.
	 * @return mixed Return the value of the array matching the key, or if it doesn't exist, return false.
	 */
	public function idx($key = '', $idx = array()) {
		global $wp;

		if (empty($idx) && !empty($wp->wp_idx_content)) {
			$idx = $wp->wp_idx_content;
		}
		
		$data = (!empty($idx["{$key}"]) && $idx["{$key}"] != '0') ? $idx["{$key}"] : false;
		
		// process special cases
		switch ($key) {
			case 'pages':
				$data = str_replace('%%page%%', $idx['page'], $this->get('pagination_template'));
				break;
			case 'sitename':
				$data = get_bloginfo('name');
				break;
		}
		
		$data = $data ? $data : $this->get_global(false, $key);
		
		return $data;
	}

    
	/**
	 * Alias for the $this->helper->replace_vars() method
	 *
	 * String with %%{data key}%% replaced with the data
	 *
	 *
	 * @see $this->helper->replace_vars()
	 * @param string $text Text to replace
	 * @param boolean $format Format the output (numbers, etc.)
	 * @return string String with %%{data key}%% replaced with the data
	 */
	function replace_vars($text = '', $idx = array(), $format = false) {
		return $this->helper->replace_vars($text, $idx, $format);
	}
	
	
	
	private function replace_distress_types($code = '') {
		
		$distressText = $this->get_foreclosure_types();
		
		$code = (int) $code;
		return isset($distressText[$code]) ? $distressText[$code] : $distressText[0];
	}
	
	private function replace_property_types($code = '') {
		$propertyTypes = $this->get_property_types(true, false);
		foreach ($propertyTypes as $propertyType) {
			if ((int) $propertyType->SearchSetupPropertyTypeID === (int) $code) {
				return $propertyType->DisplayName;
			}
		}
		return $code;
	}
	
	
	function format_value($value = '', $key = '') {
		
		if (in_array(strtolower($key), Bon_IDX_DSAPI::get_price_params())) {
			setlocale(LC_MONETARY, apply_filters('bon_idx_money_locale', 'en_US'));
			return money_format(apply_filters('bon_idx_money_format', '%(.0n'), $this->helper->raw_number($value));
		}
		
		if (in_array($key, Bon_IDX_DSAPI::get_numeric_params())) {
			return $this->helper->number_format(floatval($value));
		}
		
		if ($key === 'Cities' || $key === 'city' || $key == 'area' || $key == 'Areas' || $key == 'Communities' || $key == 'community') {
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					$value[$k] = ucwords($v);
				}
			} else {
				$value = ucwords($value);
			}
		}
		
		switch (strtolower($key)) {
			case 'sortby':
				$value = preg_replace('/(?<=[^A-Z])(?=[A-Z])/sm', ' ', $value);
				break;
			case 'propertytypes':
				$value = $this->replace_property_types($value);
				break;
			case 'distresstypes':
				$value = $this->replace_distress_types($value);
				break;
		}
		
		return $value;
	}
	
	
	
	function get_params($type = '') {
		
		$both = array(
			'url' => '',
			'sitename' => '',
			'title' => '',
			'description' => '',
			'original_title' => '',
			'excerpt' => '',
			'type' => '',
			'transient' => ''
		);
		
		$listing = array(
			
			'address' => '',
			'city' => '',
			'community' => '',
			'county' => '',
			'price' => '',
			'zip' => '',
			'state' => '',
			'homesize' => '',
			'lotsize' => '',
			'lotsizetype' => '',
			'photo' => '',
			'photocount' => '',
			'photolink' => '',
			'photocaption' => '',
			'photouribase' => '',
			'latitude' => '',
			'longitude' => '',
			'mls' => '',
			'daysonmarket' => '',
			'sqfootsource' => '',
			'walkscore' => '',
			'tract' => '',
			'baths' => '',
			'beds' => '',
			'virtualtour' => '',
			'area' => '',
			'lastupdated' => '',
			'garagespaces' => '',
			'unit' => '',
			'PropertyID' => '',
			'status' => '',
			'yearbuilt' => '',
			'singular-listings' => ''
		);
		
		$search = $widget = array(
			'page' => '',
			'DaysOnMarketMin' => '',
			'DaysOnMarketMax' => '',
			'PriceMin' => '',
			'PriceMax' => '',
			'BedsMin' => '',
			'BathsMin' => '',
			'ResultPage' => '',
			'ImprovedSqFtMin' => '',
			'ImprovedSqFtMax' => '',
			'LotSqFtMin' => '',
			'LotSqFtMax' => '',
			'PriceDropDays' => '',
			'PriceDropPercent' => '',
			'WalkScoreMin' => '',
			'ListingAgentID' => '',
			'ListingOfficeID' => '',
			'MlsNumbers' => '',
			'Schools' => '',
			'DistressTypes' => '',
			'PropertyTypes' => '',
			'PropertyFeatures' => '',
			'AddressMasks' => '',
			'Cities' => '',
			'States' => '',
			'ZipCodes' => '',
			'Communities' => '',
			'TractIdentifiers' => '',
			'Areas' => '',
			'Locations' => '',
			'Counties' => '',
			'LatitudeMin' => '',
			'LatitudeMax' => '',
			'LongitudeMin' => '',
			'LongitudeMax' => '',
			'SortOrders<0>-Direction' => '',
			'SortOrders<0>-Column' => '',
			// Undocumented, but real
			'ListingStatuses' => '',
			'PhotoCountMin' => '',
			'YearBuiltMin' => '',
			'YearBuiltMax' => '',
			'SimilarToPropertyID' => '',
			'LinkID' => '',
			'PreForeclosureStatus' => '', // ExcludeAll or Confine (only show Pre-Foreclosures)
			'LenderOwnedStatus' => '' // ExcludeAll or Confine (only show Lender Owned)
		);
		
		$search['results-type'] = '';
		
		switch ($type) {
			case 'searchonly':
				return $search;
				break;
			case 'search':
			case 'listings':
				return array_merge($search, $both);
				break;
			case 'saved-search':
			case 'widget':
			case 'link':
				return $widget;
				break;
			case 'listingonly':
				return $listing;
				break;
			case 'listing':
				return array_merge($listing, $both);
				break;
			case 'transient':
			default:
				$data = array_merge($listing, $search, $both);
				return $data;
				break;
		}
	}
	
	//! Get Transient Key
	function get_transient_key($idx_arr = false, $append = '') {
		global $wp;
		
		if (empty($wp->wp_idx_content) && empty($idx_arr)) {
			return false;
		}
		
		
		if (empty($idx_arr)) {
			$idx_arr = $wp->wp_idx_content;
		}
		
		unset($idx_arr['transient']);
		unset($idx_arr['excerpt'], $idx_arr['original_title'], $idx_arr['title'], $idx_arr['description'], $idx_arr['excerpt'], $idx_arr['listingsdata']);
		unset($idx_arr['ListingStatuses'], $idx_arr['PhotoCountMin'], $idx_arr['PreForeclosureStatus'], $idx_arr['LenderOwnedStatus'], $idx_arr['LenderOwnedStatus'], $idx_arr['DistressTypes']);
		
		if ((isset($idx_arr['PriceMax']) && ($idx_arr['PriceMax'] * 1) === -1) || (isset($idx_arr['pricemax']) && ($idx_arr['pricemax'] * 1) === -1)) {
			unset($idx_arr['pricemax'], $idx_arr['PriceMax']);
		}
		if ((isset($idx_arr['PriceMin']) && ($idx_arr['PriceMin'] * 1) === -1) || (isset($idx_arr['pricemin']) && ($idx_arr['pricemin'] * 1) === -1)) {
			unset($idx_arr['pricemin'], $idx_arr['PriceMin']);
		}
		
		$params = $this->get_params('transient');
		
		$idx = '';
		
		foreach ($params as $key => $value) {
			if (!empty($idx_arr[$key])) {
				if ($key == 'url') {
					$idx_arr[$key] = preg_replace('/(\/page-[0-9]+)/ism', '/', $idx_arr[$key]);
				}
				$idx .= is_array($idx_arr[$key]) ? implode(',', $idx_arr[$key]) : $idx_arr[$key];
			}
		}
		
		if (empty($append)) {
			$idx .= '_' . $append;
		}
		
		$transient = sha1($idx); // 40 characters; 58 total character length
		# $transient = hash('tiger192,4', $idx); // 48 characters; 64 (max) character length
		
		return $transient;
	}
	
	/**
	 * Get Listing and Search Data
	 *
	 * Processes the current page request to get the data about the listing or search result.
	 * Sets a variable in $wp->wp_idx_content with the data.
	 *
	 * @uses htmlqp()
	 * @uses $this->get_mls()
	 * @uses Bon_IDX_DSAPI::get_api_params()
	 */
	function process_idx_data() {
		global $post, $posts, $wp_query, $wp, $wp_rewrite;
		
		// This is largely for debugging, but could also be helpful for some reason
		do_action('bon_idx_modify_post_content');
		
		$idx    = $this->get_params();
		$params = Bon_IDX_DSAPI::get_api_params(true);
		if (empty($params) && !is_idx()) {
			return false;
		}
		foreach ((array) $params as $k => $param) {
			if (preg_match('/directive|ResultsPerPage/ism', $k)) {
				continue;
			}
			$idx[$k]             = $param;
			$param               = (array) $param;
			$idx[strtolower($k)] = implode(', ', $param);
		}
		
		$idx['mls']            = $this->get_mls();
		$idx['url']            = $wp->request;
		$idx['title']          = isset($post->post_title) ? $post->post_title : '';
		$idx['original_title'] = $idx['title'];
		$idx['excerpt']        = isset($post->post_excerpt) ? html_entity_decode($post->post_excerpt) : '';
		
		if (!empty($idx['mls'])) {
			$idx['type'] = 'listing';
		} elseif (!empty($wp_query->query_vars['idx-action'])) {
			if ($wp_query->query_vars['idx-action'] === 'results') {
				$idx['type'] = 'listings';
			} elseif ($wp_query->query_vars['idx-action'] === 'details') {
				$idx['type'] = 'listing';
			} elseif ($wp_query->query_vars['idx-action'] === 'framed') {
				$idx['type'] = 'advanced';
			}
		} elseif (!empty($post->post_name) && $post->post_name === 'dsidxpress-data') {
			if (strpos($post->post_name, 'MLS #')) {
				$idx['type'] = 'listing';
			} else {
				$idx['type'] = 'listings';
			}
		} else {
			return false;
		}
		
		// Is this an expired listing?
		$idx['is_404'] = $this->is_404();
		
		$qp = htmlqp($post->post_content);
		
		switch ($idx['type']) {
			case 'listing':
				preg_match($this->get_regex('process_listing_title'), $idx['title'], $pieces);
				$idx['address']     = !empty($pieces[1]) ? apply_filters('bon_idx_title_address', @$pieces[1]) : apply_filters('bon_idx_backup_address', 'MLS #' . $idx['mls'], $pieces);
				$idx['street']      = $this->helper->get_street_from_address($idx['address']);
				$idx['unit']        = apply_filters('bon_idx_title_city', @$pieces[2]);
				$idx['city']        = apply_filters('bon_idx_title_city', $this->helper->trim(@$pieces[3]));
				$idx['state']       = apply_filters('bon_idx_title_state', @$pieces[4]);
				$idx['zip']         = apply_filters('bon_idx_title_zip', str_replace('_', '-', @$pieces[5]));
				$idx['sitename']    = apply_filters('bon_idx_title_sitename', @$pieces[7]);
				$idx['title']       = $this->get('listing_title_template');
				$idx['description'] = $this->get('listing_description_template');
				
				// Primary Data
				$databox = $qp->find('#dsidx-header');
				
				
				$idx['daysonmarket'] = $databox->find('th:contains(Days on Market)')->next('td')->text();
				
				$idx['lotsize']     = $databox->find('th:contains(Lot Size)')->next('td')->text();
				$idx['lotsizetype'] = $this->helper->trim(preg_replace('/[0-9,\.]/ism', '', $this->helper->trim($idx['lotsize'])));
				$idx['lotsizeraw']  = $this->helper->raw_number($idx['lotsize']);
				$idx['lotsize']     = $this->helper->number_format($idx['lotsizeraw']);
				
				$idx['price'] = $databox->find('#dsidx-price td')->text();
				
				$homesize_th = $databox->find('th:contains(Home size),th:contains(Finished sq.ft)');
				$homesize_td = $homesize_th->next('td')->text();
				
				$idx['homesize_title'] = $this->helper->trim($homesize_th->text());
				$idx['homesize']       = $this->helper->trim($homesize_td);
				$idx['homesizeraw']    = !empty($homesize_td) ? $this->helper->raw_number($homesize_td) : '';
				$idx['homesizesource'] = $this->helper->match($this->get_regex('homesize_source'), $homesize_td, 2);
				
				$idx['baths']  = $databox->find('th:contains(Baths)')->next('td')->text();
				$idx['baths']  = $this->helper->trim(preg_replace('/baths?/ism', '', $idx['baths']));
				$idx['beds']   = $databox->find('th:contains(Beds)')->next('td')->text();
				$idx['status'] = $databox->find('#dsidx-status td')->text();
				
				// If there's a message about the listing, set it here. This should be the last because it removes the table
				$idx['notice'] = '';
				if ($qp->find('#dsidx-primary-data')->length) {
					$idx['notice'] = $qp->find('#dsidx-primary-data')->remove()->top()->find('.shortsale-notice')->parent()->innerHtml();
				}
				
				$secondary            = $qp->top()->find('#dsidx-secondary-data');
				$idx['community']     = $secondary->find('th:contains(Community)')->next('td')->text();
				$idx['county']        = $secondary->find('th:contains(County)')->next('td')->text();
				$idx['garagespaces']  = $secondary->find('th:contains(Garage Spaces)')->next('td')->text();
				$idx['lastupdated']   = $secondary->find('th:contains(Last Updated)')->next('td')->text();
				$idx['yearbuilt']     = $secondary->find('th:contains(Year Built)')->next('td')->text();
				$idx['totalparking']  = $secondary->find('th:contains(Total Parking)')->next('td')->text();
				$idx['tract']         = $secondary->find('th:contains(Tract)')->next('td')->text();
				$idx['propertytypes'] = preg_replace('/(?:.*?:)(.+)/ism', '$1', $qp->find('#dsidx-property-types')->text());
				$idx['status']        = empty($idx['status']) ? $secondary->find('th:contains(Status)')->next('td')->text() : $idx['status'];
				$idx['daysonmarket']  = empty($idx['daysonmarket']) ? $secondary->find('th:contains(Days on Market)')->next('td')->text() : $idx['daysonmarket'];
				$idx['walkscoredata'] = array(
					'th' => $secondary->find('th:contains(Walk Score)')->innerHtml(),
					'td' => $secondary->find('th:contains(Walk Score)')->next('td')->innerHtml()
				);
				$idx['walkscore']     = $idx['walkscoredata']['score'] = $this->helper->raw_number($idx['walkscoredata']['td']);
				
				$idx['virtualtour'] = $qp->find('#dsidx-virtual-tour-container')->innerHtml();
				
				// Set other data using regex
				$idx['PropertyID']   = $this->helper->raw_number($this->helper->match($this->get_regex('propertyid'), $post->post_content, 1, $idx['mls']));
				$idx['latitude']     = $this->helper->match($this->get_regex('latitude'), $post->post_content);
				$idx['longitude']    = $this->helper->match($this->get_regex('longitude'), $post->post_content);
				$idx['photocount']   = $this->helper->raw_number($this->helper->match($this->get_regex('photocount'), $post->post_content));
				$idx['photouribase'] = $this->helper->match($this->get_regex('photouribase'), $post->post_content);
				$idx['photocaption'] = $this->helper->match($this->get_regex('photocaption'), $post->post_content);
				$idx['contentdomid'] = $this->helper->match($this->get_regex('contentdomid'), $post->post_content);
				
				
				// No photos.
				if (empty($idx['photocount'])) {
					$idx['photolink'] = get_template_directory_uri() . '/assets/images/nophoto.png';
					$idx['photo']     = $this->get_unavailable_photo();
				}
				// Yes photos
				else {
					$idx['photolink'] = $idx['photouribase'] . '0-full.jpg';
					$idx['photo']     = $this->process_photo('<img src="' . $idx['photouribase'] . '0-medium.jpg" alt="' . $idx['title'] . '" />', $idx);
				}
				
				foreach ($this->get_params('searchonly') as $key => $val) {
					unset($idx[$key]);
				}
				
				break;
			
			case 'listings':
			default:
				
				$idx['sqfeetmin']   = isset($idx['ImprovedSqFtMin']) ? $idx['ImprovedSqFtMin'] : '';
				$idx['page']        = isset($params['directive']['ResultPage']) ? $params['directive']['ResultPage'] : 1;
				$idx['zip']         = isset($idx['ZipCodes']) ? implode(', ', (array) $idx['ZipCodes']) : '';
				$idx['zip']         = str_replace('_', '-', $idx['zip']);
				$idx['Communities'] = $idx['community'] = isset($idx['Communities']) ? ucwords(implode(', ', (array) $idx['Communities'])) : '';
				$idx['city']        = isset($idx['Cities']) ? implode(', ', (array) $idx['Cities']) : '';
				$idx['area']        = isset($idx['Areas']) ? implode(', ', (array) $idx['Areas']) : '';
				$idx['tract']       = isset($idx['TractIdentifiers']) ? ucwords(implode(', ', (array) $idx['TractIdentifiers'])) : '';
				list($idx['SortOrders<0>-Direction'], $idx['SortOrders<0>-Column'], $notdefault) = $this->get_sort();
				
				$idx['pricemax']      = isset($params['query.PriceMax']) ? $params['query.PriceMax'] : '';
				$idx['pricemin']      = isset($params['query.PriceMin']) ? $params['query.PriceMin'] : '';
				$idx['bedsmin']       = isset($params['query.BedsMin']) ? $params['query.BedsMin'] : '';
				$idx['sqfeetmin']     = isset($params['query.ImprovedSqFtMin']) ? $params['query.ImprovedSqFtMin'] : '';
				$idx['bathsmin']      = isset($params['query.BathsMin']) ? $params['query.BathsMin'] : '';
				$idx['propertytypes'] = isset($params['query.PropertyTypes']) ? $params['query.PropertyTypes'] : '';
				
				preg_match('/\/?idx\/(.+?)\//ism', $wp->request, $matches);
				$idx['results-type'] = isset($matches[1]) ? $matches[1] : '';
				
				$sitename = '';
				$titletag = isset($post->post_title) ? $post->post_title : '';
				if (sizeof($params) === 1) {
					if (!empty($idx['city'])) {
						$idx['title']        = $this->get('city_title_template');
						$idx['description']  = $this->get('city_description_template');
						$idx['results-type'] = 'city';
					} else if (!empty($idx['area'])) {
						$idx['title']        = $this->get('area_title_template');
						$idx['description']  = $this->get('area_description_template');
						$idx['results-type'] = 'area';
					} else if (!empty($idx['community'])) {
						$idx['title']        = $this->get('community_title_template');
						$idx['description']  = $this->get('community_description_template');
						$idx['results-type'] = 'community';
					} else if (!empty($idx['zip'])) {
						$idx['title']        = $this->get('zip_title_template');
						$idx['description']  = $this->get('zip_description_template');
						$idx['results-type'] = 'zip';
					} else if (!empty($idx['tract'])) {
						$idx['title']        = $this->get('tract_title_template');
						$idx['description']  = $this->get('tract_description_template');
						$idx['results-type'] = 'tract';
					} else if (preg_match('/\/?idx\/[0-9]{4-9}-(.+)/ism', $wp->request)) {
						// This is a link
						$idx['title']       = apply_filters('bon_idx_link_title_template', $idx['original_title']);
						$idx['description'] = $idx['excerpt'];
					} else {
						$idx['title']       = $this->get('search_title_template');
						$idx['description'] = $this->get('search_description_template');
					}
					
				} else {
					$idx['title']       = $this->get('search_title_template');
					$idx['description'] = $this->get('search_description_template');
				}
				
				// Map JS has lots of good data.
				preg_match($this->get_regex('listingsdata'), $post->post_content, $matches);
				$idx['listingsdata'] = isset($matches[1]) ? json_decode('[' . $matches[1] . ']') : '';
				
				
				$multiples = array();
				foreach ((array) $params as $key => $value) {
					if (preg_match('/query\.(.*?)\[([0-9])\]/ism', $key, $matches)) {
						$multiples[$matches[1]][$matches[2]] = $value;
					}
				}
				
				foreach ($multiples as $key => $value) {
					$idx[$key] = $value;
				}
				
				preg_match($this->get_regex('dsidx-paging-control', 'pro_paging-control'), $post->post_content, $matches);
				if (isset($matches[3])) {
					$idx['original_pagination'] = $this->helper->trim($matches[0]) . '</div>';
					$idx['pagination_text']     = $this->helper->trim(strip_tags($matches[0]));
					$idx['start']               = $matches[1];
					$idx['end']                 = $this->is_pro() ? '' : $matches[2];
					$idx['total']               = $this->is_pro() ? $matches[2] : $matches[3];
				}
				
				break;
		}
		
		// If the title's blank, use the default from dsIDXpress
		$idx['title'] = $this->helper->trim($idx['title']);
		if (empty($idx['title'])) {
			$idx['title'] = $idx['original_title'];
		}
		
		// If the description is blank, use the default from dsIDXpress
		$idx['description'] = $this->helper->trim($idx['description']);
		if (empty($idx['description'])) {
			$idx['description'] = $idx['excerpt'];
		}
		
		// This will only over-write non-existing fields.
		// That shouldn't happen unless we're dealing with forced reg, a 404, or a blow-up.
		$idx = $this->process_data_from_url($idx);
		
		$idx['title']     = apply_filters('bon_idx_title_template', $idx['title'], $wp);
		$idx['transient'] = $this->get_transient_key($idx);
		
		ksort($idx);
		$idx = $this->helper->trim($idx);
		
		$wp->wp_idx_content = apply_filters('bon_idx_process_idx_data_filter', $idx);
	}
	
	
	
	function process_photo($photo, $idx = array(), $size = array()) {
		global $wp;
		
		if (empty($idx) && isset($wp->wp_idx_content)) {
			$idx = $wp->wp_idx_content;
		}
		
		#if(empty($idx)) { return; }
		
		$qpphoto = htmlqp($photo)->find('img');
		
		// Get the alt tag (if failing, use the specified no photo match, replace the vars on the filtered alt tag
		$alt = $this->replace_vars(apply_filters('photo_alt_tag', $this->helper->match('/alt=[\'"](.*?)[\'"]/ism', $photo, 1, "No photo available for {$idx['address']}"), $idx, $photo), $idx);
		
		// Is the photo missing?
		$nophoto = preg_match($this->get_regex('photo_unavailable'), $photo);
		
		$streetview = false;
		if (apply_filters('bon_idx_results_use_street_view', 0, $idx) || ($nophoto && $this->get('replace_with_streetview'))) {
			$streetview = $this->get_street_view_photo($idx, $size);
			$photo      = !empty($streetview) ? $streetview : $photo;
		}

		$attr = '';
		
		# If there's no image result, show the replacement photo
		if (!$streetview && strlen($this->get('replace_no_photos_available')) > 5 && $nophoto) {
			$photo = '<img src="' . $this->get('replace_no_photos_available') . '" alt="' . $alt . '" attr="' . $attr . '" class="no-photo" />';
		} elseif (!$streetview && isset($altmatch[1]) && $alt !== $altmatch[1]) {
			$photo = preg_replace('/alt=[\'"](.*?)[\'"]/ism', 'alt="' . $alt . '"', $photo);
		}
		
		$attr = '';
		if (!empty($size['height']) || !empty($size['width'])) {
			$height = !empty($size['height']) ? $size['height'] : '';
			$width  = !empty($size['width']) ? $size['width'] : '';
		} elseif (!empty($size['h']) || !empty($size['w'])) {
			$height = !empty($size['h']) ? $size['h'] : '';
			$width  = !empty($size['w']) ? $size['w'] : '';
		} elseif (!empty($size)) {
			list($width, $height, $attr) = $this->image_width($size);
		}
		
		if (preg_match('/width=/ism', $photo) && !empty($width)) {
			$photo = preg_replace('/width=[\'"](.*?)[\'"]/ism', 'width="' . $width . '"', $photo);
		} elseif (!empty($width)) {
			$photo = str_replace('<img', '<img width="' . $width . '"', $photo);
		}
		
		if (preg_match('/height=/ism', $photo) && !empty($height)) {
			$photo = preg_replace('/height=[\'"](.*?)[\'"]/ism', 'height="' . $height . '" ', $photo);
		} elseif (!empty($height)) {
			$photo = str_replace('<img', '<img height="' . $height . '"', $photo);
		}
		
		// Resize street view images
		if ($streetview) {
			if (!empty($width)) {
				$photo = preg_replace('/;w=([0-9]+)/ism', ';w=' . $width, $photo);
			}
			if (!empty($height)) {
				$photo = preg_replace('/;h=([0-9]+)/ism', ';h=' . $height, $photo);
			}
		}
		
		if (isset($size['stripstyle'])) {
			$photo = preg_replace('/(style=[\'"](?:.*?)[\'"])/ism', '', $photo);
		}
		
		return $photo;
	}
	
	/**
	 * Get all available data from the URL of a listing.
	 * @param array $idx Send existing listing data array so if it exists, it's not overwritten.
	 * @param string $url The URL to gleam the data from.
	 * @return array A new array of listing data
	 *
	 */
	function process_data_from_url($idx = array(), $url = '') {
		global $wp;
		
		if (empty($url)) {
			$url = $wp->request;
		}
		
		$states = implode('|', array(
			'al',
			'ak',
			'az',
			'ar',
			'ca',
			'co',
			'ct',
			'de',
			'dc',
			'fl',
			'ga',
			'hi',
			'id',
			'il',
			'in',
			'ia',
			'ks',
			'ky',
			'la',
			'me',
			'md',
			'ma',
			'mi',
			'mn',
			'ms',
			'mo',
			'mt',
			'ne',
			'nv',
			'nh',
			'nj',
			'nm',
			'ny',
			'nc',
			'nd',
			'oh',
			'ok',
			'or',
			'pa',
			'ri',
			'sc',
			'sd',
			'tn',
			'tx',
			'ut',
			'vt',
			'va',
			'wa',
			'wv',
			'wi',
			'wy',
			'ab',
			'bc',
			'mb',
			'nb',
			'nl',
			'ns',
			'nt',
			'nu',
			'on',
			'pe',
			'qc',
			'sk',
			'yt'
		));
		
		$suffixes = implode('|', $this->helper->get_street_types());
		
		preg_match(sprintf($this->get_regex('data_from_url'), $suffixes, $states), $url, $pieces);
		
		$idx['mls'] = (empty($idx['mls']) && !empty($pieces[1])) ? $pieces[1] : (empty($idx['mls']) ? '' : $idx['mls']);
		
		$addresstype    = !empty($pieces[3]) ? ucwords($pieces[3]) : '';
		$idx['address'] = (empty($idx['address']) || preg_match('/MLS/i', $idx['address'])) ? (!empty($pieces[2]) ? ucwords(str_replace('_', ' ', $pieces[2])) . ' ' . $addresstype : '') : $idx['address'];
		
		// Convert addresses ending in directions
		$idx['address'] = str_replace(array(
			'_ne',
			'_nw',
			'_s',
			'_n',
			'_w',
			'_se',
			'_sw'
		), array(
			' NE',
			' NW',
			' S',
			' N',
			' W',
			' SE',
			' SW'
		), $idx['address']);
		
		// Apply filter
		$idx['address'] = apply_filters('bon_idx_title_address', $idx['address']);
		
		$idx['city'] = empty($idx['city']) ? (!empty($pieces[4]) ? ucwords(str_replace('_', ' ', $pieces[4])) : '') : $idx['city'];
		
		if (empty($idx['state'])) {
			$idx['state'] = !empty($pieces[5]) ? strtoupper($pieces[5]) : '';
		}
		
		if (empty($idx['zip'])) {
			$idx['zip'] = !empty($pieces[6]) ? $pieces[6] : '';
			$idx['zip'] = str_replace('_', '-', $idx['zip']);
		}
		
		if (empty($idx['zip'])) {
			preg_match($this->get_regex('data_from_url_zip'), $url, $pieces);
			$idx['mls']  = (empty($idx['mls']) && !empty($pieces[1])) ? $pieces[1] : $idx['mls'];
			$idx['city'] = empty($idx['city']) && !empty($pieces[2]) ? ucwords(str_replace('_', ' ', $pieces[2])) : $idx['city'];
		}
		
		// @since 2.0 alpha 12
		if (empty($idx['title'])) {
			$idx['title'] = $this->replace_vars($this->get('listing_title_template'), $idx);
			$idx['title'] = str_replace(get_bloginfo('sitename'), '', $idx['title']);
			$idx['title'] = preg_replace('/\)\s+[^A-Za-z]\s+?/ism', ')', $idx['title']);
		}
		
		return $idx;
	}
	
	
	/**
	 * Does the search result have any listings?
	 * @return boolean
	 */
	public function has_listings() {
		$listings = $this->get_global('results_listings');
		return !empty($listings);
	}
	
	
	/**
	 * Get the MLS number of the current listing, if exists.
	 * @global $wp_query
	 * @uses dsSearchAgent_Client::GetApiParams()
	 * @return mixed MLS number if exists; false if not.
	 */
	function get_mls() {
		global $wp_query;
		if (!class_exists('dsSearchAgent_Client')) {
			return false;
		}
		if (isset($wp_query->query_vars['idx-q-MlsNumber'])) {
			return $wp_query->query_vars['idx-q-MlsNumber'];
		} else {
			$apiParams = @dsSearchAgent_Client::GetApiParams(stripslashes_deep(@$_GET));
			return isset($apiParams['query.MlsNumber']) ? $apiParams['query.MlsNumber'] : '';
		}
		return false;
	}
	
	

	/**
	 * Clear all the cached data related to idx by running a query
	 * @global $wpdb
	 */
	private function flush_transients() {
		global $wpdb;
		$query = $wpdb->prepare("DELETE FROM {$wpdb->prefix}options WHERE `option_name` LIKE %s OR `option_name` LIKE %s OR `option_name` LIKE %s", '%idxrpst%', '%transient_bon_idx%', '%idxrp%');
		$wpdb->query($query);
	}
	
	/**
	 * Remove all expired transients every day to help keep database size down.
	 *
	 * Taps into dsIDXpress-generated scheduled event.
	 *
	 * @see dsIDXpress_Cron::FlushCache()
	 * @global $wpdb
	 */
	function flush_exipired_transients() {
		global $wpdb;
		$query = "DELETE a, b FROM `{$wpdb->prefix}options` a, `{$wpdb->prefix}options` b WHERE a.option_name LIKE '%_transient_%' AND a.option_name NOT LIKE '%_transient_timeout_%' AND ( b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, CHAR_LENGTH('_transient_') + 1 ) ) OR b.option_name = CONCAT( '_site_transient_timeout_', SUBSTRING( a.option_name, CHAR_LENGTH('_site_transient_') + 1 ) ) ) AND b.option_value < UNIX_TIMESTAMP()";
		$wpdb->query($query);
	}

	function set_settings() {
		
		$defaults = apply_filters('bon_idx_default_settings', array(
			'general' => '',
			'registrationkey' => '',
			'alpha' => false,
			'debug' => false,
			'enable_debug_form' => true,
			'pro' => false,
			'prowidgets' => false,
			'saved_favorites' => true,
			'saved_searches' => true,
			'always_show_save_link' => true,
			'replace_no_photos_available' => get_template_directory_uri() . '/assets/images/nophoto.png',
			'replace_with_streetview' => false,
			'no_photo_available_url' => '',
			'no_photo_available_width' => '',
			'no_photo_available_height' => '',
			'listing' => '',
			'listing_show_boxes' => array(
				'listing_header',
				'secondary_data',
				'social_share',
				'price_history',
				'schools',
				'additional_details',
				'google_map',
				'birds_eye',
				'contact_form',
				'related_properties',
				'listing_source',
				'disclaimer'
			),
			'listing_show_boxes_sortorder' => '',
			'replace_details_box_add_price' => true,
			'details_box_gallery_width' => 280,
			'listing_header_slideshow_style' => 'light',
			'listing_view_photosize' => 'full',
			'listing_logged_out_image' => true,
			'listing_logged_out_image_count' => '0',
			'use_walkscore' => false,
			'walkscore_id' => '',
			'formatting' => null,
			'format_additional_details' => 'table',
			'format_secondary_data' => 'table',
			'disclaimer' => true,
			'listing_autolink' => array(
				'tract',
				'community',
				'county'
			),
			'related_posts' => true,
			'related_posts_type' => array(
				'post_tag',
				'category'
			),
			'related_posts_tags' => array(
				'city',
				'zip',
				'address',
				'tract',
				'community',
				'propertytypes'
			),
			'listings' => '',
			'listings_view_photosize' => 'full',
			'results_per_page' => bon_get_option('listing_per_page', 8),
			'minimum_price' => false,
			'only_with_photos' => false,
			'price_grouping' => "Under $150,000\n$150,000 - $200,000\n$200,000 - $250,000\n$250,000 - $300,000\n$400,000 - $750,000\n$750,000 - $1,000,000\nOver $1,000,000",
			'text' => '',
			'tract_replace' => true,
			'registration_box_title' => __('Register', 'bon'),
			'text_sorting_control' => __('Sorted by', 'bon'),
			'text_pagination_properties' => __('Properties %%start%% - %%end%% of %%total%%', 'bon'),
			'text_map_link_show' => __('Show these %%count%% properties on a map', 'bon'),
			'text_map_link_hide' => __('Hide map', 'bon'),
			'text_results_listing_address' => __('[if-idx data="address" else="MLS #%%mls%%"]%%address%%[if-idx data="city"], %%city%%[/if-idx][/if-idx]', 'bon'),
			'text_results_listing_pro_address' => __('%%address%%', 'bon'),
			'redirect_404_to' => 'zip',
			'404_message' => sprintf(__("%sSorry, the [idx-if data='mls' else='listing']MLS listing #%%%%mls%%%%[/idx-if] at [idx-if data='address']%%%%address%%%%[/idx-if] [idx-if data='city']in %%%%city%%%%[/idx-if] is not available on this site.%s%s%sShowing real estate near the listing.%s", 'bon'), '<h3>', '</h3>', "\n\n", '<p>', '<strong>', '</strong>', '</p>'),
			'listing_title_template' => '%%address%%, %%city%%, %%state%% %%zip%% (MLS # %%mls%%) - %%sitename%%',
			'search_title_template' => 'Real estate matching your search %%pages%%',
			'search_description_template' => 'Find homes for sale in [idx-if data="zip"] %%zip%%[/idx-if][idx-if data="city"] %%city%%[/idx-if][idx-if data="community"] %%community%%[/idx-if][idx-if data="tract"] %%tract%%[/idx-if][idx-if data="area"] %%area%%[/idx-if][idx-if data="state"] %%state%%[/idx-if].',
			'propertytypes_template' => '%%propertytypes%%',
			'pagination_template' => ' (Page %%page%%)',
			'wordpress_seo_sitemap' => true,
			'wordpress_seo_sitemap_types' => true,
			'listing_description_template' => '%%excerpt%%',
			'results_description_template' => '%%pagination_text%%',
			
		));

		return $defaults;
	}

	function get_unavailable_photo($size = false) {
        $sizeatts = '';
        $url = '';
      
        if (empty($url)) {
            $url = get_template_directory_uri() . '/assets/images/nophoto.png';
        }
        $photo = '<img src="' . $url . '" alt="No photo available" />';
        $photo = $this->process_photo($photo, false);

        return $photo;
    }
}

$GLOBALS['bonidx'] = new Bon_IDX();


if (!function_exists('is_idx')) {
	
	/**
	 * Is the current page running dsIDXpress and idx?
	 *
	 * URL structure.
	 * @global $wp
	 * @return boolean Is idx loaded
	 * @param $check_wp If used to check if the `$wp->wp_idx_content` var is set
	 */
	function is_idx($check_wp = false) {
		global $wp;
		
		$url       = add_query_arg(array());
		$url_match = preg_match('/\/idx\//i', $url);
		
		if (!empty($wp)) {
			if (isset($wp->query_vars['idx-action'])) {
				return true;
			}
			if (!empty($wp->wp_idx_content['type'])) {
				return true;
			}
		}
		
		return $url_match;
	}
	
}

function idx_template_include($file, $ob_start = true, $path = NULL, &$object = NULL) {
	if ($ob_start) {
		ob_start();
	}
	
	if (!empty($path)) {
		include( trailingslashit( BON_IDX_DIR ) . untrailingslashit(ltrim($path, '/')) . "/{$file}");
	} else {
		include($this->template_path . "/{$file}");
	}
	
	if ($ob_start) {
		$content = ob_get_clean();
		return do_shortcode($content);
	}
}