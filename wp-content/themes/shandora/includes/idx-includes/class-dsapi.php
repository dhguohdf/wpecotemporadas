<?php
/**
 * For interacting with the DS API
 * @version 2.0.51
 */

if (!class_exists('Bon_IDX_DSAPI')) {
    /**
     * For interacting with the DS API
     */
    class Bon_IDX_DSAPI {


        public static $enable_filter = true;
        
        function __construct() {

            add_filter('http_request_args', array(&$this,'filter_request'), 1, 2);
        }

        static function client_assist($action = '', $args = array(), $request = array()) {
            
            self::$enable_filter = false;
            
            $_REQUEST['action'] = esc_html($action);
            $_POST              = $args;
            
            ob_start();
            if (!class_exists('Bon_IDX_ClientAssist')) {
                include(plugin_dir_path(Bon_IDX::$file) . 'client-assist.php');
            } else {
                call_user_func(array(
                    'Bon_IDX_ClientAssist',
                    $_REQUEST['action']
                ));
            }
            $return = ob_get_clean();
            
            self::$enable_filter = true;
            
            return $return;
        }
       
        function fetch_data($type = 'Results', $atts) {
            if (Bon_IDX::is_debug()) {
                Bon_IDX::r($atts, true, 'fetch_data: ' . $type, false);
            }
            $apiHttpResponse = dsSearchAgent_ApiRequest::FetchData($type, $atts, false, 0);
            $response        = $apiHttpResponse["body"];
            $decoded         = json_decode($response);
            $response        = is_null($decoded) ? $response : $decoded;
            return $response;
        }
        
        function get_search_setup_id() {
            $id = Bon_IDX::$SearchSetupID;
            
            if (!empty($id)) {
                return $id;
            }
            
            $idxpress_options = get_option(DSIDXPRESS_OPTION_NAME);
            
            if (!empty($idxpress_options) && !empty($idxpress_options['SearchSetupID'])) {
                return $idxpress_options;
            }
            
            
            return false;
        }
        
        static function get_api_params($all = false) {
            if (!class_exists('dsSearchAgent_Client')) {
                return false;
            }
            
            $apiParams = @dsSearchAgent_Client::GetApiParams(stripslashes_deep(@$_GET));
            
            // dsIDXpress doesn't add a param when showing all listings
            if (isset($_GET['idx-q-DistressTypes'])) {
                $apiParams['query.DistressTypes'] = (int) $_GET['idx-q-DistressTypes'];
            }
            
            if (isset($_GET['idx-q-PhotoCountMin'])) {
                $apiParams['query.PhotoCountMin'] = (int) $_GET['idx-q-PhotoCountMin'];
            }
            
            if (isset($_GET['idx-q-ListingStatuses'])) {
                $apiParams['query.ListingStatuses'] = (int) $_GET['idx-q-ListingStatuses'];
            }
            
            # if($apiParams['query.DistressTypes'] === 'all') {
            # unset($apiParams['query.DistressTypes']);
            # }
            
            if (empty($apiParams) || !is_array($apiParams)) {
                return false;
            }
            
            foreach ($apiParams as $key => $value) {
                if (!$all && preg_match('/directive|ResultsPerPage/ism', $key)) {
                    continue;
                }
                
                if (strpos($key, 'Cities') || strpos($key, 'Areas')) {
                    $value = ucwords($value);
                }
                
                $params[str_replace('query.', '', $key)] = $value;
            }
            
            foreach (self::get_numeric_params() as $key) {
                if (!empty($params) && is_array($params) && array_key_exists($key, $params)) {
                    $params[$key] = floatval(str_replace(",", "", $params[$key]));
                }
            }
            
            if (empty($params) || !is_array($params)) {
                return false;
            }
            
            
            foreach ($params as $key => $value) {
                
                // Process array of results
                if (preg_match('/(.*?)\[([0-9]+)\](?:\.(.+))?/ism', $key, $matches)) {
                    if (isset($matches[3])) {
                        $params[$matches[1]][$matches[3]][$matches[2]] = $value;
                    } else {
                        $params[$matches[1]][$matches[2]] = $value;
                    }
                    unset($params[$key]);
                } else {
                    $matches = explode('.', $key);
                    if (!empty($matches[1])) {
                        $params[$matches[0]][$matches[1]] = $value;
                        unset($params[$key], $params[$matches[1]]);
                    }
                }
            }
            
            ksort($params);
            
            return $params;
        }
        
        static function get_price_params() {
            return array(
                'pricemin',
                'pricemax',
                'price'
            );
        }
        
        static function get_numeric_params() {
            return array(
                "improvedsqftmin",
                "bedsmin",
                "bathsmin"
            );
        }
        
        /**
         * Get and set transients for DS locations by type.
         * @param string $type Type of location: 'city', 'zip', 'community', 'tract'
         * @return array Array of locations
         */
        static function get_locations($type = '') {
            if ((!is_string($type) && !is_array($type)) || !class_exists('dsSearchAgent_ApiRequest')) {
                return false;
            }
            
            if (empty($type) && isset($_REQUEST["type"])) {
                $type = $_REQUEST["type"];
            }
            if (empty($type)) {
                return array();
            }
            
            if ($type == 'all' || is_array($type)) {
                if (is_array($type)) {
                    $types = $type;
                }
                $types = array(
                    'city',
                    'zip',
                    'community',
                    'tract'
                );
                foreach ($types as $type) {
                    $locations[$type] = self::get_locations($type);
                }
                return $locations;
            }
            
            $locations = get_transient('idx_plus_locations_' . $type);
            if ($locations && !is_wp_error($locations) && !(isset($_REQUEST['refresh']) && $_REQUEST['refresh'] === 'idx_plus_locations')) {
                return $locations;
            }
            
            $requestUri = dsSearchAgent_ApiRequest::$ApiEndPoint . "LocationsByType";
            
            $apiHttpResponse = (array) wp_remote_post($requestUri, array(
                "body" => array(
                    'searchSetupID' => self::get_search_setup_id(),
                    'type' => $type
                ),
                "httpversion" => "1.1",
                "redirection" => "0"
            ));
            $locations       = json_decode($apiHttpResponse["body"]);
            
            set_transient('idx_plus_locations_' . $type, $locations, apply_filters('bon_idx_locations_cache_time', 60 * 60 * 24 * 7 * 8));
            
            return $locations;
        }
        
        function filter_request($r, $url) {
            
            global $bonidx;

            if (!self::$enable_filter) {
                return $r;
            }
            
            // @todo Can we fix their crappy errors? TBD.
            #empty($apiHttpResponse["body"]) || !empty($apiHttpResponse["errors"]) || substr($apiHttpResponse["response"]["code"], 0, 1) == "5"
            
            if (isset($r['body']) && (!empty($r['body']['directive.ResultsPerPage']) && preg_match('/results/ism', $url))) {
                $body = $r['body'];
                if (@$r['body']['responseDirective.ViewNameSuffix'] != 'shortcode' && @$r['body']['responseDirective.ViewNameSuffix'] != 'widget') {
                    $body['directive.ResultsPerPage'] = $bonidx->get('results_per_page');
                    if ($bonidx->listing_show_box('price_history')) {
                        $body['responseDirective.ShowPriceHistory'] = true;
                    }
                } elseif (isset($r['body']['query.LinkID']) && preg_match('/(idx_plus_custom|relatedproperties)/ism', $r['body']['query.LinkID'])) {
                    $body['directive.ResultsPerPage'] = (int) $body['directive.ResultsPerPage'] + 1;
                    unset($body['query.LinkID']);
                }
                if (isset($_REQUEST['idx-q-ResultsPerPage'])) {
                    $body['directive.ResultsPerPage'] = (int) $_REQUEST['idx-q-ResultsPerPage'];
                }
                
                $r['body'] = self::regenerate_request($body);
                
            }
            
            $r = apply_filters('bon_idx_request_filter', $r, $url);
            
            return $r;
        }
        
        /**
         * When you change any data using the DS API, you need to regenerate the signature; this is how DS verifies that the request is authentic.
         * @param array $body The request that's going to be made.
         * @return array The regenerated request
         */
        function regenerate_request($body = array()) {
            
            // Fix PHP warnings
            $body = is_array($body) ? $body : maybe_unserialize($body);
            $body = is_array($body) ? $body : array();
            
            unset($body['requester.Signature']);
            
            $stringToSign = '';
            foreach ($body as $key => $value) {
                $stringToSign .= "{$key}:{$value}\n";
                if (!isset($body[$key])) {
                    $body["{$key}"] = '';
                }
            }
            
            ksort($body);
            
            $body['requester.Signature'] = hash_hmac('sha1', rtrim($stringToSign, "\n"), Bon_IDX::$PrivateApiKey);
            
            return $body;
        }
    }
}

$Bon_IDX_DSAPI = new Bon_IDX_DSAPI();