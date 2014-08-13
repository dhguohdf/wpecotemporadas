<?php

/**
 * todo
 * - Support for Authorization header?
 * - Enforce maximum expiration?
 * - MVC pattern?
 * - support for JSONP
 * - API wrappers
 */


if (!defined('GFWEBAPI_REQUIRE_SIGNATURE'))
    define('GFWEBAPI_REQUIRE_SIGNATURE', true);

if (!defined('GFWEBAPI_SLUG'))
    define('GFWEBAPI_SLUG', "gfwebapi");

if (!defined('GFWEBAPI_ROUTE_VAR'))
    define('GFWEBAPI_ROUTE_VAR', 'gfapi_route');

if (!defined('GFWEBAPI_API_BASE_URL'))
    define('GFWEBAPI_API_BASE_URL', site_url(GFWEBAPI_SLUG));

if (class_exists("GFForms")) {
    GFForms::include_addon_framework();

    class GFWebAPI extends GFAddOn {
        protected $_version = "1.0";
        protected $_min_gravityforms_version = "1.7.9999";
        protected $_slug = "gravityformswebapi";
        protected $_path = "gravityformswebapi/webapi.php";
        protected $_full_path = __FILE__;
        protected $_url = "http://www.gravityforms.com";
        protected $_title = "Gravity Forms Web API";
        protected $_short_title = "Web API";

        private $_enabled;
        private $_private_key;
        private $_public_key;

        // Members plugin integration
        protected $_capabilities = array("gravityforms_webapi", "gravityforms_webapi_uninstall", "gravityforms_webapi_results");

        // Permissions
        protected $_capabilities_settings_page = "gravityforms_webapi";
        protected $_capabilities_form_settings = "gravityforms_webapi";
        protected $_capabilities_uninstall = "gravityforms_webapi_uninstall";

        public function __construct() {
            if (defined('DOING_CRON') && DOING_CRON) {
                add_action('gravityforms_results_cron_' . $this->_slug, array($this, 'results_cron'), 10, 3);

                return;
            }
            parent::__construct();
        }

        public function init_ajax() {
            parent::init_ajax();
            add_action('wp_ajax_gfwebapi_qrcode', array($this, 'ajax_qrcode'));
        }

        public function init_admin() {
            parent::init_admin();
            // update the cache meta
            add_action("gform_after_update_entry", array($this, 'entry_updated'), 10, 2);
            add_action("gform_update_status", array($this, 'update_entry_status'), 10, 2);
            add_action("gform_after_save_form", array($this, 'after_save_form'), 10, 2);

        }


        function init_frontend() {
            $settings           = $this->get_plugin_settings();
            $this->_enabled     = rgar($settings, "enabled");
            $this->_public_key  = rgar($settings, "public_key");
            $this->_private_key = rgar($settings, "private_key");

            if (false == $this->_enabled)
                return;

            add_filter('option_rewrite_rules', array($this, 'rewrite_rules'));

            add_filter('query_vars', array($this, 'query_vars'));

            add_filter('pre_get_posts', array($this, 'handle_page_request'));

            // update the cache
            add_action('gform_entry_created', array($this, 'entry_created'), 10, 2);
        }

        // Scripts
        public function scripts() {
            $scripts = array(
                array("handle"  => "gfwebapi_hmac_sha1",
                      "src"     => "http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/hmac-sha1.js",
                      "enqueue" => array(
                          array("admin_page" => array("plugin_settings"))
                      )
                ),
                array("handle"   => "gfwebapi_enc_base64",
                      "src"      => "http://crypto-js.googlecode.com/svn/tags/3.1.2/build/components/enc-base64-min.js",
                      "deps"     => array('gfwebapi_hmac_sha1'),
                      "callback" => array($this, "localize_form_settings_scripts"),
                      "enqueue"  => array(
                          array("admin_page" => array("plugin_settings"))
                      )
                ),
                array(
                    "handle"  => "gfwebapi_settings.js",
                    "src"     => GFCommon::get_base_url() . "/includes/webapi/js/gfwebapi_settings.js",
                    "version" => $this->_version,
                    "deps"    => array("jquery", 'thickbox'),
                    "enqueue" => array(
                        array("admin_page" => array("plugin_settings"))
                    )
                )
            );

            return array_merge(parent::scripts(), $scripts);
        }

        public function styles() {
            $styles = array(
                array("handle"  => "gfwebap_settings",
                      "src"     => GFCommon::get_base_url() . "/includes/webapi/css/gfwebapi_settings.css",
                      "version" => $this->_version,
                      "deps"    => array("thickbox"),
                      "enqueue" => array(
                          array("admin_page" => array("plugin_settings")),
                      )
                )
            );

            return array_merge(parent::scripts(), $styles);
        }

        public function render_uninstall() {
        }

        // ------- Plugin settings -------
        public function plugin_settings_fields() {
            $max_accounts    = apply_filters("gform_webapi_max_accounts_settings_page", 200);
            $accounts        = get_users(array("number" => $max_accounts));
            $account_choices = array();
            foreach ($accounts as $account) {
                $account_choices[] = array("label" => $account->user_login, "value" => $account->ID);
            }
            $permalink_structure = get_option('permalink_structure');
            if (!$permalink_structure) {
                return array(
                    array(
                        "title"       => "<span><i class='icon-cogs'></i> " . __("Web API Settings", "gravityforms") . "</span>",
                        "fields"      => array(
                            array(
                                "name"  => "requirements_check",
                                "label" => __("Requirements check", "gravityforms"),
                                "type"  => "requirements_check"
                            )
                        )
                    )
                );
            }


            return array(
                array(
                    "title"       => "<span><i class='icon-cogs'></i> " . __("Web API Settings", "gravityforms") . "</span>",
                    "description" => __('The Web API allows developers programmatic access to some of the functionality of Gravity Forms and its Add-Ons.', 'gravityforms'),
                    "fields"      => array(
                        array(
                            "type"    => "checkbox",
                            "label"   => "Enable access to the Web API",
                            "name"    => "activate",
                            "onclick" => 'gfapiToggleSettings(jQuery(this).prop("checked"))',
                            "choices" => array(
                                0 => array("label" => "Enabled", "name" => "enabled")
                            )
                        ),
                        array(
                            "name"              => "public_key",
                            "label"             => __("Public API Key", "gravityforms"),
                            "type"              => "text",
                            "default_value"     => substr(wp_hash(site_url()), 0, 10),
                            "class"             => "medium",
                            "feedback_callback" => array($this, "is_valid_public_key"),
                        ),
                        array(
                            "name"              => "private_key",
                            "label"             => __("Private API Key", "gravityforms"),
                            "type"              => "text",
                            "default_value"     => substr(wp_hash(get_bloginfo("admin_email")), 0, 15),
                            "class"             => "medium",
                            "feedback_callback" => array($this, "is_valid_private_key")
                        ),
                        array(
                            "name"       => "qrcode",
                            "label"      => __("QR Code", "gravityforms"),
                            "type"       => "qrcode",
                            "dependency" => array("field" => "private_key", "values" => array("_notempty_"))
                        ),
                        array(
                            "name"    => "impersonate_account",
                            "label"   => __("Impersonate account", "gravityforms"),
                            "type"    => "select",
                            "choices" => $account_choices

                        ),
                        array(
                            "name"       => "developer_tools",
                            "label"      => __("Developer tools", "gravityforms"),
                            "type"       => "developer_tools",
                            "dependency" => array("field" => "private_key", "values" => array("_notempty_"))
                        )
                    )
                )
            );
        }

        public function settings_requirements_check() {
            $permalinks_url  = admin_url("options-permalink.php");
            echo "<i class='icon-remove gf_invalid'></i> Permalinks are not in the correct format. <br />Change the <a href='{$permalinks_url}'>WordPress Permalink Settings</a> from default to any of the other options to get started.";
        }

        public function settings_qrcode() {
            ?>
            <button class="button-secondary"
                    id="gfwebapi-qrbutton"><?php _e("Show/hide QR Code", "gravityforms"); ?></button>
            <div id="gfwebapi-qrcode-container" style="display:none;">
                <img id="gfwebapi-qrcode" src="<?php echo GFCommon::get_base_url() ?>/images/spinner.gif"/>
            </div>

        <?php
        }

        public function settings_developer_tools() {

            ?>
            <script type="text/javascript">
                var gfapiBaseUrl = '<?php echo esc_attr(GFWEBAPI_API_BASE_URL) ?>';
            </script>

            <a title="Gravity Forms Web API: Developer Tools" class="thickbox"
               href="#TB_inline?width=300&height=550&inlineId=gfwebapi-dev-tools"><?php _e("Open developer tools", "gravityforms") ?></a>

            <div id="gfwebapi-dev-tools" style="display:none;">
                <div>
                    <h4>URL Generator</h4>

                    <div>This will generate a secure, expiring URL.</div>
                    <div>
                        <select id="gfapi-url-builder-method">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                        /<input type="text" id="gfapi-url-builder-route" value="forms/1/"
                                placeholder="route e.g. forms/1/"/>
                        <select id="gfapi-url-builder-expiration">
                            <option value="60">1 minute</option>
                            <option value="3600">1 hour</option>
                            <option value="86400">1 day</option>
                            <option value="604800">1 week</option>
                            <option value="2628000">1 month</option>
                        </select>
                        <button class="button-secondary" id="gfapi-url-builder-button">Generate URL</button>
                    </div>

                    <div>
                        <textarea id="gfapi-url-builder-generated-url" value="" style="width:100%"></textarea>
                    </div>

                    <h4>URL tester</h4>

                    <div>This tool tests the authentication/signature - it does not perform any operations.</div>
                    <select id="gfapi-url-tester-method">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                    </select>

                    <div>
                        <textarea id="gfapi-url-tester-url" value="" style="width:100%"
                                  placeholder="paste your url here"></textarea>
                    </div>
                    <button class="button-secondary" id="gfapi-url-tester-button">Test</button>
                    <div id="gfapi-url-tester-loading" style="display:none">
                        Loading....
                    </div>
                    <div id="gfapi-url-tester-results">
                        <!-- placeholder for results -->
                    </div>


                </div>
            </div>

        <?php
        }


        public function rewrite_rules($rules) {

            $gfapi_rules[GFWEBAPI_SLUG . '/(.*)'] = 'index.php?' . GFWEBAPI_ROUTE_VAR . '=$matches[1]';

            $rules = $gfapi_rules + $rules;

            return $rules;
        }

        public function query_vars($query_vars) {

            $query_vars[] = GFWEBAPI_ROUTE_VAR;

            return $query_vars;
        }


        public function handle_page_request() {

            $route = get_query_var(GFWEBAPI_ROUTE_VAR);
            if (false == $route)
                return;

            self::authenticate();
            $test_mode = rgget("test");
            if ($test_mode)
                die("test mode");

            $settings = get_site_option('gravityformsaddon_gravityformswebapi_settings');

            if (empty($settings))
                $this->die_not_authorized();
            $account_id = $settings["impersonate_account"];
            wp_set_current_user($account_id);

            $route_parts = pathinfo($route);

            $format = rgar($route_parts, 'extension');
            if ($format)
                $route = str_replace("." . $format, "", $route);

            $path_array = explode("/", $route);
            $collection = strtolower(rgar($path_array, 0));

            $id = rgar($path_array, 1);

            if (strpos($id, ";") !== false)
                $id = explode(";", $id);

            $collection2 = strtolower(rgar($path_array, 2));
            $id2         = rgar($path_array, 3);

            if (strpos($id2, ";") !== false)
                $id2 = explode(";", $id2);
            else
                $id2 = intval($id2);

            if (empty($format))
                $format = "json";

            $schema    = strtolower((rgget("schema")));
            $offset    = isset($_GET["offset"]) ? strtolower($_GET["offset"]) : 0;
            $page_size = isset($_GET["page_size"]) ? strtolower($_GET["page_size"]) : 10;

            $method        = strtoupper($_SERVER['REQUEST_METHOD']);
            $args          = compact("offset", "page_size", "schema");
            $filter_suffix = empty($collection2) ? $collection : "_" . $collection2;

            $output = apply_filters("gform_webapi_" . strtolower($method) . "_" . $filter_suffix, "", $id, $collection2, $id2, $format, $args);

            if ("" == $output) {
                $data = file_get_contents("php://input");
                $data = json_decode($data, true);
                switch ($collection) {
                    case "forms" :
                        switch ($collection2) {
                            case "results" :
                                switch ($method) {
                                    case 'GET' :
                                        $output = self::get_results($id);
                                        break;
                                    case 'DELETE':
                                    case 'PUT':
                                    case 'POST':
                                    default:
                                        self::die_bad_request();
                                }
                                break;
                            case "feeds" :
                                if (false == empty($id2))
                                    self::die_bad_request();
                                switch ($method) {
                                    case 'GET' :
                                        $output = self::get_feeds(null, $id);
                                        break;
                                    case 'DELETE' :
                                        $output = self::delete_feeds(null, $id);
                                        break;
                                    case 'PUT' :
                                        self::die_not_implemented();
                                        break;
                                    case 'POST' :
                                        $output = self::post_feeds($data, $id);
                                        break;
                                    default :
                                        self::die_bad_request();
                                }
                                break;
                            case "entries" :
                                if (false == empty($id2))
                                    self::die_bad_request();
                                switch ($method) {
                                    case 'GET' :
                                        $output = self::get_entries(null, $id, $schema);
                                        break;
                                    case 'POST' :
                                        $output = self::post_entries($data, $id);
                                        break;
                                    case 'PUT' :
                                    case 'DELETE' :
                                    case 'POST' :
                                        self::die_not_implemented();
                                        break;
                                    default:
                                        self::die_bad_request();
                                }
                                break;

                            case "" :
                                switch ($method) {
                                    case 'GET':
                                        $output = self::get_forms($id, $schema);
                                        break;
                                    case 'DELETE':
                                        $output = self::delete_forms($id);
                                        break;
                                    case 'PUT':
                                        $output = self::put_forms($data, $id, $id2);
                                        break;
                                    case 'POST':
                                        if (false === empty($id))
                                            self::die_bad_request();
                                        $output = self::post_forms($data, $id);
                                        break;
                                    default:
                                        self::die_bad_request();
                                }
                                break;
                            default :
                                self::die_bad_request();
                                break;

                        }
                        break;
                    case "entries" : //  route = /entries/{id}
                        switch ($method) {
                            case 'GET':
                                switch ($collection2) {
                                    case "fields" : // route = /entries/{id}/fields/{id2}
                                        switch ($method) {
                                            case 'GET' :
                                                $output = self::get_entries($id, null, $schema, $id2);
                                                break;
                                            case 'DELETE' :
                                            case 'PUT' :
                                            case 'POST' :
                                            default:
                                                self::die_bad_request();
                                        }
                                        break;
                                    case "" :
                                        $output = self::get_entries($id, null, $schema);
                                        break;
                                }

                                break;
                            case 'DELETE' :
                                $output = self::delete_entries($id);
                                break;
                            case 'PUT' :
                                $output = self::put_entries($data, $id);
                                break;
                            case 'POST' :
                                if (false === empty($id))
                                    self::die_bad_request();
                                $output = self::post_entries($data);
                                break;
                            default:
                                self::die_bad_request();
                        }
                        break;
                    case "feeds" :
                        switch ($method) {
                            case 'GET' :
                                $output = self::get_feeds($id);
                                break;
                            case 'DELETE' :
                                if (empty($id))
                                    self::die_bad_request();
                                $output = self::delete_feeds($id);
                                break;
                            case 'PUT' :
                                $output = self::put_feeds($data, $id);
                                break;
                            case 'POST' :
                                if (false === empty($id))
                                    self::die_bad_request();
                                $output = self::post_feeds($data);
                                break;
                            default :
                                self::die_bad_request();
                        }
                        break;
                    default :
                        self::die_bad_request();
                        break;
                }

                if ("json" === $format) {
                    header('Content-Type: application/json; charset=' . get_option('blog_charset'), true);
                    $output = json_encode($output);
                }
            }

            // PHP > 5.3
            if (function_exists("header_remove"))
                header_remove("X-Pingback");


            echo $output;
            die();
        }

        public static function authorize($caps = array()) {
            if (GFCommon::current_user_can_any($caps))
                return;

            $output["code"]    = "401";
            $output["message"] = __("Permission denied");
            $output["status"]  = "error";
            status_header(401);
            die();
        }

        //----- Feeds ------

        public static function get_feeds($feed_ids, $form_id = null) {

            self::authorize("gravityforms_edit_forms");

            $addon_slug = rgget("addon");
            $output     = GFAPI::get_feeds($feed_ids, $form_id, $addon_slug);
            if (is_wp_error($output))
                self::die_not_found();
            if (false === empty($feed_ids) && false === is_array($feed_ids) && is_array($output))
                $output = array_shift($output);

            return $output;

        }


        public static function delete_feeds($feed_ids, $form_id = null) {

            self::authorize("gravityforms_edit_forms");

            $count = 0;
            if (empty($feed_ids)) {
                $feeds = GFAPI::get_feeds(null, $form_id);
                foreach ($feeds as $feed) {
                    $result = GFAPI::delete_feed($feed["id"]);
                    if (is_wp_error($result))
                        break;
                    $count++;
                }
            } else {
                if (is_array($feed_ids)) {
                    foreach ($feed_ids as $feed_id) {
                        $result = GFAPI::delete_feed($feed_id);
                        if (is_wp_error($result))
                            break;
                        $count++;
                    }
                } else {
                    $result = GFAPI::delete_feed($feed_ids);
                    $count++;
                }
            }

            if (isset($result) && is_wp_error($result)) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $data              = $result->get_error_data();
                if ($data)
                    $output["data"] = $data;
                $output["status"] = "error";
                self::error_status($output);
            } else {
                $output["code"]    = "deleted";
                $output["message"] = sprintf(__("Feeds deleted successfully: %d", "gravityforms"), $count);
                $output["status"]  = "success";
            }

            return $output;
        }

        public static function put_feeds($feed_data, $feed_id = null) {

            self::authorize("gravityforms_edit_forms");

            $count = 0;
            if (empty($feed_id)) {
                foreach ($feed_data as $feed) {
                    //todo: validate feed id and form id
                    $result = GFAPI::update_feed($feed["id"], $feed["meta"], $feed["form_id"]);
                    if (is_wp_error($result))
                        break;
                    $count++;
                }
            } else {
                $result = GFAPI::update_feed($feed_id, $feed_data["meta"], $feed_data["form_id"]);
                $count++;
            }


            if (isset($results) && is_wp_error($result)) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $data              = $result->get_error_data();
                if ($data)
                    $output["data"] = $data;
                $output["status"] = "error";
                self::error_status($output);
            } else {
                $output["code"]    = "updated";
                $output["message"] = sprintf(__("Feeds updated successfully: %d", "gravityforms"), $count);
                $output["status"]  = "success";
            }

            return $output;
        }

        public static function post_feeds($feeds, $form_id = null) {

            self::authorize("gravityforms_edit_forms");

            $feed_ids = array();
            foreach ($feeds as $feed) {
                $addon_slug = isset($feed["addon_slug"]) ? $feed["addon_slug"] : rgget("addon");
                $f_id       = empty($form_id) ? $feed["form_id"] : $form_id;
                if (empty($f_id)) {
                    $result = new WP_Error("missing_form_id", __("Missing form id", "gravityforms"));
                    break;
                }
                $result = GFAPI::add_feed($f_id, $feed["meta"], $addon_slug);
                if (is_wp_error($result))
                    break;
                $feed_ids[] = $result;
            }
            if (is_wp_error($result)) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $output["data"]    = $result->get_error_data();
                $output["status"]  = "error";
                self::error_status($output);
            } else {
                $count             = count($feed_ids);
                $output["code"]    = "created";
                $output["data"]    = $feed_ids;
                $output["message"] = sprintf(__("Feeds created successfully: %d", "gravityforms"), $count);
                $output["status"]  = "success";
                status_header(201);
            }

            return $output;
        }

        //----- Forms ------

        public static function delete_forms($form_ids) {

            self::authorize("gravityforms_delete_forms");

            $count = 0;
            if (is_array($form_ids)) {
                foreach ($form_ids as $form_id) {
                    $result = GFAPI::delete_form($form_id);
                    if (is_wp_error($result))
                        break;
                    $count++;
                }
            } else {
                $result = GFAPI::delete_form($form_ids);
                $count++;
            }

            if (isset($result) && is_wp_error($result)) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $data              = $result->get_error_data();
                if ($data)
                    $output["data"] = $data;
                $output["status"] = "error";
                self::error_status($output);
            } else {
                $output["code"]    = "deleted";
                $output["message"] = sprintf(__("Forms deleted successfully: %d", "gravityforms"), $count);
                $output["status"]  = "success";
            }

            return $output;
        }

        public static function post_entries($data, $form_id = null) {

            self::authorize("gravityforms_edit_entries");

            $result = GFAPI::add_entries($data, $form_id);

            if (is_wp_error($result)) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $output["data"]    = $result->get_error_data();
                $output["status"]  = "error";
                self::error_status($output);
            } else {
                $output["code"]    = "created";
                $output["data"]    = $result;
                $output["message"] = __("Entries created successfully", "gravityforms");
                $output["status"]  = "success";
                status_header(201);
            }

            return $output;
        }

        public static function put_entries($data, $entry_id = null) {

            self::authorize("gravityforms_edit_entries");

            if (empty($entry_id)) {
                $result = GFAPI::update_entries($data);
            } else {
                $result = GFAPI::update_entry($data, $entry_id);
            }

            if (is_wp_error($result)) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $output["status"]  = "error";
                self::error_status($output);
            } else {
                $output["code"] = "updated";
                if (empty($entry_id))
                    $output["message"] = __("Entries updated successfully", "gravityforms");
                else
                    $output["message"] = __("Entry updated successfully", "gravityforms");
                $output["status"] = "success";
            }

            return $output;
        }

        public static function post_forms($data) {

            self::authorize("gravityforms_create_form");

            $result = GFAPI::add_forms($data);
            $output = array();

            if (is_wp_error($result) || count($result) == 0) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $output["status"]  = "error";
                self::error_status($output);
            } else {
                $form_ids       = $result;
                $output["code"] = "created";
                if (count($form_ids) == 1) {
                    $output["message"] = __("Form created successfully", "gravityforms");
                } else {
                    $output["message"] = __("Forms created successfully", "gravityforms");
                }
                $output["data"]   = $form_ids;
                $output["status"] = "success";
                status_header(201);
            }

            return $output;
        }

        public static function put_forms($data, $form_id = null) {

            self::authorize("gravityforms_create_form");

            if (empty($form_id))
                $result = GFAPI::update_forms($data);
            else
                $result = GFAPI::update_form($data, $form_id);
            $output = array();
            if (is_wp_error($result)) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $output["status"]  = "error";
                self::error_status($output);
            } else {
                $output["code"] = "updated";
                if (empty($form_id))
                    $output["message"] = __("Forms updated successfully", "gravityforms");
                else
                    $output["message"] = __("Form updated successfully", "gravityforms");
                $output["code"]   = "updated";
                $output["status"] = "success";
            }

            return $output;
        }

        public static function delete_entries($entry_ids) {

            self::authorize("gravityforms_delete_entries");

            $count = 0;
            if (is_array($entry_ids)) {
                foreach ($entry_ids as $entry_id) {
                    $result = GFAPI::delete_entry($entry_id);
                    if (is_wp_error($result))
                        break;
                    $count++;
                }
            } else {
                $result = GFAPI::delete_entry($entry_ids);
                $count++;
            }

            if (isset($result) && is_wp_error($result)) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $output["data"]    = $result->get_error_data();
                $output["status"]  = "error";
            } else {
                $output["code"]    = "deleted";
                $output["message"] = sprintf(__("Entries deleted successfully: %d", "gravityforms"), $count);
                $output["status"]  = "success";
            }

            return $output;
        }

        public function get_entries($entry_ids, $form_ids = null, $schema = "", $field_ids = array()) {

            self::authorize("gravityforms_view_entries");

            $output = array();
            if ($entry_ids) {

                if (is_array($entry_ids)) {
                    foreach ($entry_ids as $entry_id) {
                        $result = GFAPI::get_entry($entry_id);
                        if (!is_wp_error($result)) {
                            $output[$entry_id] = $result;
                            if (!empty($field_ids) && (!empty($output[$entry_id])))
                                $output[$entry_id] = $this->filter_entry_object($output[$entry_id], $field_ids);
                        }
                    }
                } else {
                    $result = GFAPI::get_entry($entry_ids);
                    if (!is_wp_error($result)) {
                        $output = $result;
                        if (!empty($field_ids) && (!empty($output)))
                            $output = $this->filter_entry_object($output, $field_ids);
                    }

                }

                if ($schema == "mtd")
                    $output = self::mtd_transform_entry_data($output);
            } else {

                //sorting parameters
                $sort_key = isset($_GET["sorting"]["key"]) && !empty($_GET["sorting"]["key"]) ? $_GET["sorting"]["key"] : "id";
                $sort_dir = isset($_GET["sorting"]["direction"]) && !empty($_GET["sorting"]["direction"]) ? $_GET["sorting"]["direction"] : "DESC";
                $sorting  = array('key' => $sort_key, 'direction' => $sort_dir);

                //paging parameters
                $page_size = isset($_GET["paging"]["page_size"]) ? intval($_GET["paging"]["page_size"]) : 10;
                if (isset($_GET["paging"]["current_page"])) {
                    $current_page = intval($_GET["paging"]["current_page"]);
                    $offset       = $page_size * ($current_page - 1);
                } else {
                    $offset = isset($_GET["paging[offset]"]) ? intval($_GET["paging[offset]"]) : 0;
                }

                $paging = array('offset' => $offset, 'page_size' => $page_size);

                $search = isset($_GET["search"]) ? $_GET["search"] : array();
                if (empty($form_ids))
                    $form_ids = 0; // all forms

                $entry_count = GFAPI::count_entries($form_ids, $search);

                $result = $entry_count > 0 ? GFAPI::get_entries($form_ids, $search, $sorting, $paging) : array();

                if (!is_wp_error($result)) {
                    $output = array("total_count" => $entry_count, "entries" => $result);

                    if ($schema == "mtd")
                        $output = $this->mtd_transform_entries_data($output, $form_ids);
                }

            }

            if (is_wp_error($result)) {
                $output["code"]    = $result->get_error_code();
                $output["message"] = $result->get_error_message();
                $output["status"]  = "error";
                self::error_status($output);
            }

            return $output;
        }

        public static function filter_entry_object($entry, $field_ids) {

            if (!is_array($field_ids))
                $field_ids = array($field_ids);
            $new_entry = array();
            foreach ($entry as $key => $val) {
                if (in_array($key, $field_ids) || (is_numeric($key) && in_array(intval($key), $field_ids)))
                    $new_entry[$key] = $val;
            }

            return $new_entry;
        }

        public function get_forms($form_ids = null, $schema = "") {

            self::authorize("gravityforms_edit_forms");

            if (empty($form_ids)) {
                $forms = RGFormsModel::get_forms(true);
                foreach ($forms as $form) {
                    $form_id          = $form->id;
                    $totals           = GFFormsModel::get_form_counts($form_id);
                    $form_info        = array(
                        "id"      => $form_id,
                        "title"   => $form->title,
                        "entries" => rgar($totals, "total")
                    );
                    $output[$form_id] = $form_info;
                }
                if ($schema == "mtd")
                    $output = $this->mtd_transform_forms_data($output);
            } else {
                if (is_array($form_ids)) {
                    $output = array();
                    foreach ($form_ids as $form_id) {
                        $output[$form_id] = GFAPI::get_form($form_id);
                    }
                } else {
                    $result = GFAPI::get_form($form_ids);
                    if (is_wp_error($result)) {
                        $output["code"]    = $result->get_error_code();
                        $output["message"] = $result->get_error_message();
                        $output["status"]  = "error";
                        self::error_status($output);
                    } elseif (!$result){
                        self::die_not_found();
                    } else
                        $output = $result;
                }

            }

            return $output;
        }


        // RESULTS
        // todo: move to GFAPI or GFResults once stable

        public function get_results_cache_key($form_id, $fields, $search_criteria) {

            $key = $this->get_results_cache_key_prefix($form_id);
            $key .= wp_hash(json_encode($fields) . json_encode($search_criteria));

            return $key;
        }

        public function get_results_cache_key_prefix($form_id) {
            global $blog_id;

            $key = is_multisite() ? $blog_id . '-' : "";

            $key .= sprintf("%s-cache-%s-", $this->_slug, $form_id);

            // The option_name column in the options table has a max length of 64 chars.
            // Truncate the key if it's too long for column and allow space for the "tmp" prefix
            $key = substr($key, 0, 60);

            return $key;
        }

        public function update_entry_status($lead_id) {
            $lead    = RGFormsModel::get_lead($lead_id);
            $form_id = $lead["form_id"];
            $form    = GFFormsModel::get_form_meta($form_id);
            $this->maybe_update_results_cache_meta($form);
        }

        public function entry_updated($form, $lead_id) {
            $this->maybe_update_results_cache_meta($form);
        }

        public function entry_created($entry, $form) {
            $this->maybe_update_results_cache_meta($form);
        }

        public function after_save_form($form, $is_new) {
            if ($is_new)
                return;
            $form_id = $form["id"];

            // only need cache meta when a cache exists
            if (false === $this->results_cache_exists($form_id))
                return;

            $fields              = rgar($form, "fields");
            $current_fields_hash = wp_hash(json_encode($fields));

            $cache_meta         = $this->get_results_cache_meta($form_id);
            $cached_fields_hash = rgar($cache_meta, "fields_hash");

            if ($current_fields_hash !== $cached_fields_hash) {
                // delete the meta for this form
                $this->delete_results_cache_meta($form_id);
                // delete all cached results for this form
                $this->delete_cached_results($form_id);
            }
        }

        public function results_cache_exists($form_id) {
            global $wpdb;

            $key = $this->get_results_cache_key_prefix($form_id);

            $key = "%" . like_escape($key) . "%";

            $sql = $wpdb->prepare("SELECT count(option_id) FROM $wpdb->options WHERE option_name LIKE %s", $key);

            $result = $wpdb->get_var($sql);

            return $result > 0;

        }

        public function delete_cached_results($form_id) {
            global $wpdb;

            $form = GFAPI::get_form($form_id);
            if (!($form) || !is_array($form))
                return;

            $key = $this->get_results_cache_key_prefix($form_id);

            $key = "%" . like_escape($key) . "%";

            $sql = $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE %s", $key);

            $result = $wpdb->query($sql);

            return $result;
        }

        // When entries are added or updated the cache needs to be expired and rebuilt.
        // This cache meta records the last updated time for each form and a hash of the fields array.
        // Each time results are requested this value is checked to make sure the cache is still valid.
        public function maybe_update_results_cache_meta($form) {
            $form_id = $form["id"];

            // only need to expire the cache when a cache already exists
            if (false === $this->results_cache_exists($form_id))
                return;

            $this->update_results_cache_meta($form_id, rgar($form, "fields"));
        }

        public function update_results_cache_meta($form_id, $fields, $expiry = null) {

            if (empty($expiry))
                $expiry = time();

            $data = array(
                "fields_hash" => wp_hash(json_encode($fields)),
                "timestamp"   => $expiry
            );

            $key = $this->get_results_cache_meta_key($form_id);

            $this->update_results_cache($key, $data);

        }

        public function delete_results_cache_meta($form_id) {

            $key = $this->get_results_cache_meta_key($form_id);

            delete_option($key);

        }

        public function get_results_cache_meta_key($form_id) {
            global $blog_id;

            $key = is_multisite() ? $blog_id . '-' : "";
            $key .= "gfresults-cache-meta-form-" . $form_id;

            return $key;
        }

        public function get_results_cache_meta($form_id) {

            $key        = $this->get_results_cache_meta_key($form_id);
            $cache_meta = get_option($key);

            return $cache_meta;
        }

        public function update_results_cache($key, $data) {

            delete_option($key);

            $result = add_option($key, $data, '', 'no');

            return $result;
        }

        // Recursive wp_cron task to continue the calculation of results
        public function results_cron($form, $fields, $search_criteria) {

            $form_id = $form["id"];
            $key     = $this->get_results_cache_key($form_id, $fields, $search_criteria);
            $key_tmp = "tmp" . $key;
            $state   = get_option($key_tmp, array());

            if (!empty($state)) {
                if (!class_exists("GFResults"))
                    require_once(GFCommon::get_base_path() . "/includes/addon/class-gf-results.php");
                $gf_results = new GFResults($this->_slug, array());
                $results    = $gf_results->get_results_data($form, $fields, $search_criteria, $state);
                if ("complete" == $results["status"]) {
                    if (isset($results["progress"]))
                        unset($results["progress"]);
                    $this->update_results_cache($key, $results);
                    if (false == empty($state))
                        delete_option($key_tmp);

                } else {
                    $this->update_results_cache($key_tmp, $results);

                    $data = get_option($key);
                    if ($data) {
                        $data["progress"] = $results["progress"];
                        $this->update_results_cache($key, $data);
                    }

                    $this->schedule_results_cron($form, $fields, $search_criteria);
                }
            }
        }

        // Returns an array with the results for all the fields in the form.
        // If the results can be calculated within the time allowed in GFResults then the results are returned and nothing is cached.
        // If the calculation has not finished then a single recursive wp_cron task will be scheduled for immediate execution.
        // While the cache is being built by the wp_cron task this function will return the expired cache results if available or the latest step in the cache build.
        // Add-On-specific results are not included e.g. grade frequencies in the Quiz Add-On.
        public function get_results($form_id) {

            self::authorize("gravityforms_view_entries");

            $s = rgget("s"); // search criteria

            $search_criteria = false === empty($s) && is_array($s) ? $s : array();

            $form = GFAPI::get_form($form_id);

            if(!$form)
                self::die_not_found();

            // for the Web API return all fields
            $fields = rgar($form, "fields");

            $form_id = $form["id"];
            $key     = $this->get_results_cache_key($form_id, $fields, $search_criteria);
            $key_tmp = "tmp" . $key;

            $data = get_option($key, array());

            $cache_meta = $this->get_results_cache_meta($form_id);

            // add the cache meta early so form editor updates can test for valid field hash
            if (empty($cache_meta))
                $this->update_results_cache_meta($form_id, $fields, 0);

            $cache_expiry    = rgar($cache_meta, "timestamp");
            $cache_timestamp = isset($data["timestamp"]) ? $data["timestamp"] : 0;
            $cache_expired   = $cache_expiry ? $cache_expiry > $cache_timestamp : false;

            // check for valid cached results first
            if (!empty($data) && "complete" == rgar($data, "status") && !$cache_expired) {
                $results = $data;
                if (isset($results["progress"]))
                    unset($results["progress"]);
            } else {

                $state = get_option($key_tmp);

                if (empty($state) || ("complete" == rgar($data, "status") && $cache_expired)) {
                    if (!class_exists("GFResults"))
                        require_once(GFCommon::get_base_path() . "/includes/addon/class-gf-results.php");
                    $gf_results         = new GFResults($this->_slug, array());
                    $max_execution_time = 5;
                    $results            = $gf_results->get_results_data($form, $fields, $search_criteria, $state, $max_execution_time);
                    if ("complete" == $results["status"]) {
                        if (false == empty($state))
                            delete_option($key_tmp);
                    } else {

                        if (false === empty($data) && "complete" == rgar($data, "status") && $cache_expired) {
                            $data["status"]   = "expired";
                            $data["progress"] = $results["progress"];
                            $this->update_results_cache($key, $data);
                        }

                        $this->update_results_cache($key_tmp, $results);

                        $this->schedule_results_cron($form, $fields, $search_criteria);

                        if ($data)
                            $results = $data;

                        status_header(202); // accepted
                    }
                } else {

                    // The cron task is recursive, not periodic, so system restarts, script timeouts and memory issues can prevent the cron from restarting.
                    // Check timestamp and kick off the cron again if it appears to have stopped
                    $state_timestamp = rgar($state, "timestamp");
                    $state_age       = time() - $state_timestamp;
                    if ($state_age > 180 && !$this->results_cron_is_scheduled($form, $fields, $search_criteria)) {
                        $this->schedule_results_cron($form, $fields, $search_criteria);
                    }

                    if (false === empty($data) && "expired" == rgar($data, "status"))
                        $results = $data;
                    else
                        $results = $state;
                    status_header(202); // accepted
                }
            }

            $fields = $results["field_data"];

            // add choice labels to the results so the client doesn't need to cross-reference with the form object
            $results["field_data"] = $this->results_data_add_labels($form, $fields);

            return $results;
        }

        public function schedule_results_cron($form, $fields, $search_criteria, $delay_in_seconds = 10) {
            // reduces problems with concurrency
            wp_cache_delete('alloptions', 'options');

            $args = array($form, $fields, $search_criteria);

            wp_schedule_single_event(time() + $delay_in_seconds, $this->get_results_cron_hook(), $args);
        }

        public function results_cron_is_scheduled($form, $fields, $search_criteria) {
            $args = array($form, $fields, $search_criteria);

            return wp_next_scheduled($this->get_results_cron_hook(), $args);
        }

        public function get_results_cron_hook() {
            return 'gravityforms_results_cron_' . $this->_slug;
        }

        public function results_data_add_labels($form, $fields) {

            // replace the values/ids with text labels
            foreach ($fields as $field_id => $choice_counts) {
                $field = GFFormsModel::get_field($form, $field_id);
                $type  = GFFormsModel::get_input_type($field);
                if (is_array($choice_counts)) {
                    $i = 0;
                    foreach ($choice_counts as $choice_value => $choice_count) {
                        if (class_exists("GFSurvey") && "likert" == $type && rgar($field, "gsurveyLikertEnableMultipleRows")) {
                            $row_text       = GFSurvey::get_likert_row_text($field, $i++);
                            $counts_for_row = array();
                            foreach ($choice_count as $col_val => $col_count) {
                                $text                     = GFSurvey::get_likert_column_text($field, $choice_value . ":" . $col_val);
                                $counts_for_row[$col_val] = array("text" => $text, "data" => $col_count);
                            }
                            $counts_for_row[$choice_value]["data"] = $counts_for_row;
                            $fields[$field_id][$choice_value]      = array("text" => $row_text, "value" => "$choice_value", "count" => $counts_for_row);

                        } else {
                            $text                             = GFFormsModel::get_choice_text($field, $choice_value);
                            $fields[$field_id][$choice_value] = array("text" => $text, "value" => "$choice_value", "count" => $choice_count);
                        }

                    }
                }

            }

            return $fields;
        }

        // ----- end RESULTS


        private function authenticate() {
            $authenticated = false;
            if (isset($_GET["api_key"])) {
                if (rgget("api_key") == $this->_public_key) {
                    if (self::check_signature())
                        $authenticated = true;
                }
            }

            if ($authenticated)
                return true;

            $this->die_not_authorized();
        }

        private function check_signature() {
            if (false === GFWEBAPI_REQUIRE_SIGNATURE)
                return true;

            $expires = (int)rgget("expires");

            $api_key = rgget("api_key");
            $path    = strtolower(get_query_var(GFWEBAPI_ROUTE_VAR));
            $method  = strtoupper($_SERVER['REQUEST_METHOD']);

            $signature = rgget("signature");

            $string_to_check = sprintf("%s:%s:%s:%s", $api_key, $method, $path, $expires);

            $calculated_sig = $this->calculate_signature($string_to_check);

            if (time() >= $expires)
                return false;

            $is_valid = $signature == $calculated_sig;

            return $is_valid;
        }

        private function calculate_signature($string) {
            $hash = hash_hmac("sha1", $string, $this->_private_key, true);
            $sig  = base64_encode($hash);

            return $sig;
        }

        public function die_not_authorized() {
            status_header(401);
            echo __("Not authorized", "gravityforms");
            die();
        }

        public static function die_bad_request() {
            status_header(400);
            echo __("Bad request", "gravityforms");
            die();
        }

        public static function die_not_found() {
            status_header(404);
            echo __("Not found", "gravityforms");
            die();
        }

        public static function die_not_implemented() {
            status_header(501);
            echo __("Not implemented", "gravityforms");
            die();
        }

        public static function die_error() {
            status_header(500);
            echo __("Internal Error", "gravityforms");
            die();
        }

        public static function error_status($output, $output_status = true) {
            $status = rgar($output, "status");
            if ($status != "error")
                return;
            $error_code = rgar($output, "code");
            $mappings   = array(
                "not_found"   => 404,
                "not_allowed" => 401
            );
            $http_code  = isset($mappings[$error_code]) ? $mappings[$error_code] : 400;
            if ($output_status)
                status_header(($http_code));

            return $http_code;
        }

        public static function get_form_metas() {
            $form_ids = array();
            $forms    = RGFormsModel::get_forms(true);
            foreach ($forms as $form) {
                $form_ids[] = $form->id;
            }
            $form_metas = GFFormsModel::get_form_meta_by_id($form_ids);

            return $form_metas;
        }

        // todo: require signature?
        public static function ajax_qrcode() {
            require_once "lib/phpqrcode/phpqrcode.php";
            $settings = get_option("gravityformsaddon_gravityformswebapi_settings");
            if (empty($settings))
                die();

            $data["site"]        = site_url();
            $data["public_key"]  = rgar($settings, "public_key");
            $data["private_key"] = rgar($settings, "private_key");

            QRcode::png(json_encode($data), false, QR_ECLEVEL_L, 4, 1, false);
            die();
        }

        /**
         * Support for MonoTouch.Dialog
         */
        // todo: support array of form ids
        public function mtd_transform_entries_data($output, $form_id) {
            $form                  = GFFormsModel::get_form_meta($form_id);
            $form_element          = array();
            $form_element["title"] = $form["title"];
            $form_element["type"]  = "root";
            $form_element["id"]    = "id-form-" . $form_id;
            $form_element["count"] = rgar($output, "total_count");
            $entries               = rgar($output, "entries");

            $section["header"] = "Entries";
            $entry_elements    = array();
            if (is_array($entries)) {
                foreach ($entries as $entry) {
                    $entry_element["type"]  = "root";
                    $entry_element["title"] = $entry["id"] . ": " . $entry["date_created"];
                    $entry_element["id"]    = $entry["id"];
                    $entry_element["url"]   = GFWEBAPI_API_BASE_URL . "/entries/" . rgar($entry, "id") . ".json?schema=mtd";
                    $entry_elements[]       = $entry_element;
                }
            }

            $section["elements"]        = $entry_elements;
            $form_element["sections"][] = $section;

            return $form_element;
        }

        public function mtd_transform_forms_data($forms) {
            $data          = array();
            $data["title"] = "Forms";
            $data["type"]  = "root";
            $data["id"]    = "forms";

            foreach ($forms as $form) {
                $element               = array();
                $element["title"]      = $form["title"];
                $element["type"]       = "root";
                $element["id"]         = "id-form-" . $form["id"];
                $element["url"]        = GFWEBAPI_API_BASE_URL . "/forms/" . $form["id"] . "/entries.json?schema=mtd";
                $section               = array();
                $section["elements"][] = $element;
                $data["sections"][]    = $section;
            }

            return $data;
        }

        public static function mtd_transform_entry_data($entry) {
            $data                  = array();
            $root_element["type"]  = "root";
            $root_element["title"] = $entry["id"] . ": " . $entry["date_created"];
            $root_element["id"]    = "id-entry-" . $entry["id"];

            $form_id = rgar($entry, "form_id");
            $form    = RGFormsModel::get_form_meta($form_id);
            $fields  = $form["fields"];

            foreach ($fields as $field) {
                $field_data           = array();
                $field_data["header"] = $field["label"];
                $elements             = array();
                $value                = RGFormsModel::get_lead_field_value($entry, $field);

                if (is_array($value) && isset($field["choices"])) {
                    $choices = rgar($field, "choices");

                    foreach ($choices as $choice) {
                        $found = false;
                        foreach ($value as $item) {
                            if ($item == rgar($choice, "value")) {
                                $found = true;
                                break;
                            }
                        }
                        $element = array();

                        $element["type"]    = "checkbox";
                        $element["caption"] = $choice["text"];
                        $element["value"]   = $found;
                        $elements[]         = $element;
                    }
                } else {
                    $element            = array();
                    $element["type"]    = "string";
                    $element["caption"] = GFFormsModel::get_choice_text($field, $value);

                    $elements[] = $element;
                }
                $field_data["elements"] = $elements;
                $data[]                 = $field_data;
            }
            $root_element["sections"] = $data;

            return $root_element;
        }

    }

    new GFWebAPI();
}