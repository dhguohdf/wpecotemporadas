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
	