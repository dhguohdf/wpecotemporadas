<?php

if (!class_exists("GFResults")) {

    class GFResults {
        protected $_slug;
        protected $_title;
        protected $_callbacks;
        protected $_capabilities;

        public function __construct($slug, $config) {
            $this->_slug         = $slug;
            $this->_title        = rgar($config, "title");
            $this->_callbacks    = isset($config["callbacks"]) ? $config["callbacks"] : array();
            $this->_capabilities = isset($config["capabilities"]) ? $config["capabilities"] : array();
        }

        public function init() {

            if (!GFCommon::current_user_can_any($this->_capabilities))
                return;

            //add top toolbar menu item
            add_filter("gform_toolbar_menu", array($this, 'add_toolbar_menu_item'), 10, 2);
            //add custom form action
            add_filter("gform_form_actions", array($this, 'add_form_action'), 10, 2);
            //add the results view
            add_action("gform_entries_view", array($this, 'add_view'), 10, 2);

            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

            require_once(GFCommon::get_base_path() . "/tooltips.php");

            add_filter('gform_tooltips', array($this, 'add_tooltips'));

        }

        public function enqueue_admin_scripts() {
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('jquery-ui-datepicker');

            wp_enqueue_script('google_charts');
            wp_enqueue_style('gaddon_results_css');
            wp_enqueue_script("gaddon_results_js");
            $this->localize_results_scripts();
        }

        public static function localize_results_scripts() {

            // Get current page protocol
            $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
            // Output admin-ajax.php URL with same protocol as current page

            $vars = array(
                'ajaxurl'         => admin_url('admin-ajax.php', $protocol),
                'imagesUrl'       => GFCommon::get_base_url() . "/images"
            );

            wp_localize_script('gaddon_results_js', 'gresultsVars', $vars);

            $strings = array(
                'ajaxError'         => __("Error retrieving results. If the problem persists, please contact support.", "gravityforms")
            );

            wp_localize_script('gaddon_results_js', 'gresultsStrings', $strings);

        }

        private function get_fields($form) {
            return isset($this->_callbacks["fields"]) ? call_user_func($this->_callbacks["fields"], $form) : $form["fields"];
        }

        public function add_form_action($actions, $form_id) {
            return $this->filter_menu_items($actions, $form_id, true);
        }

        public function add_toolbar_menu_item($menu_items, $form_id) {
            return $this->filter_menu_items($menu_items, $form_id, false);
        }

        public function filter_menu_items($menu_items, $form_id, $compact) {
            $form_meta      = GFFormsModel::get_form_meta($form_id);
            $results_fields = $this->get_fields($form_meta);
            if (false === empty($results_fields)) {
                $form_id    = $form_meta["id"];
                $link_class = "";
                if (rgget("page") == "gf_new_form")
                    $link_class = "gf_toolbar_disabled";
                else if (rgget("page") == "gf_entries" && rgget("view") == "gf_results_" . $this->_slug)
                    $link_class = "gf_toolbar_active";

                $sub_menu_items   = array();
                $sub_menu_items[] = array(
                    'label'        => $this->_title,
                    'title'        => __("View results generated by this form", "gravityforms"),
                    'link_class'   => $link_class,
                    'url'          => admin_url("admin.php?page=gf_entries&view=gf_results_{$this->_slug}&id={$form_id}"),
                    'capabilities' => $this->_capabilities
                );

                // If there's already a menu item with the key "results" then merge the two.
                if (isset($menu_items["results"])) {
                    $existing_link_class = $menu_items["results"]["link_class"];
                    $link_class == empty($existing_link_class) ? $link_class : $existing_link_class;
                    $existing_capabilities                   = $menu_items["results"]["capabilities"];
                    $merged_capabilities                     = array_merge($existing_capabilities, $this->_capabilities);
                    $existing_sub_menu_items                 = $menu_items["results"]["sub_menu_items"];
                    $merged_sub_menu_items                   = array_merge($existing_sub_menu_items, $sub_menu_items);
                    $menu_items["results"]["link_class"]     = $link_class;
                    $menu_items["results"]["capabilities"]   = $merged_capabilities;
                    $menu_items["results"]["sub_menu_items"] = $merged_sub_menu_items;
                    $menu_items["results"]["label"]          = __("Results", "gravityforms");

                } else {
                    // so far during the page cycle this is the only menu item for this key
                    $menu_items["results"] = array(
                        'label'          => $compact ? __("Results", "gravityforms") : $this->_title,
                        'title'          => __("View results generated by this form", "gravityforms"),
                        'url'            => "",
                        'onclick'        => "return false;",
                        'menu_class'     => 'gf_form_toolbar_results',
                        'link_class'     => $link_class,
                        'capabilities'   => $this->_capabilities,
                        'sub_menu_items' => $sub_menu_items,
                        'priority'       => 750
                    );
                }

            }

            return $menu_items;
        }


        public function add_view($view, $form_id) {
            if ($view == "gf_results_" . $this->_slug)
                GFResults::results_page($form_id, $this->_title, "gf_entries", $view);

        }

        public function results_page($form_id, $page_title, $gf_page, $gf_view) {
            if (empty($form_id)) {
                $forms = RGFormsModel::get_forms();
                if (!empty($forms)) {
                    $form_id = $forms[0]->id;
                }
            }
            $form = GFFormsModel::get_form_meta($form_id);
            $form = apply_filters("gform_form_pre_results_$form_id", apply_filters("gform_form_pre_results", $form));

            // set up filter vars
            $start_date = rgget("start");
            $end_date   = rgget("end");

            $all_fields = $form["fields"];

            $filter_settings = GFCommon::get_field_filter_settings($form);
            $filter_settings = apply_filters("gform_filters_pre_results", $filter_settings, $form);
            $filter_settings = array_values($filter_settings); // reset the numeric keys in case some filters have been unset

            $filter_fields    = rgget("f");
            $filter_operators = rgget("o");
            $filter_values    = rgget("v");
            $filters = array();

            if(!empty($filter_fields)){
                foreach($filter_fields as $i => $filter_field){
                    $filters[$i]["field"]=$filter_field;
                    $filters[$i]["operator"] = $filter_operators[$i];
                    $filters[$i]["value"] = $filter_values[$i];
                }
            }

            ?>
            <script type="text/javascript">
                var gresultsFields = <?php echo json_encode($all_fields); ?>;
                var gresultsFilterSettings = <?php echo json_encode($filter_settings); ?>;
                var gresultsFilters = <?php echo json_encode($filters); ?>;
                <?php GFCommon::gf_global() ?>
                <?php GFCommon::gf_vars() ?>
            </script>

            <link rel="stylesheet"
                  href="<?php echo GFCommon::get_base_url() ?>/css/admin.css?ver=<?php echo GFCommon::$version ?>"
                  type="text/css"/>
            <div class="wrap gforms_edit_form <?php echo GFCommon::get_browser_class() ?>">

                <div class="icon32" id="gravity-entry-icon"><br></div>

                <h2><?php echo empty($form_id) ? $page_title : $page_title . " : " . esc_html($form["title"]) ?></h2>

                <?php RGForms::top_toolbar(); ?>
                <?php if (false === empty($all_fields)) : ?>

                    <div id="poststuff" class="metabox-holder has-right-sidebar">
                        <div id="side-info-column" class="inner-sidebar">
                            <div id="gresults-results-filter" class="postbox">
                                <h3 style="cursor: default;"><?php _e("Results Filters", "gravityforms"); ?></h3>

                                <div id="gresults-results-filter-content">
                                    <form id="gresults-results-filter-form" action="" method="GET">
                                        <input type="hidden" id="gresults-page-slug" name="page"
                                               value="<?php echo esc_attr($gf_page); ?>">
                                        <input type="hidden" id="gresults-view-slug" name="view"
                                               value="<?php echo esc_attr($gf_view); ?>">
                                        <input type="hidden" id="gresults-form-id" name="id"
                                               value="<?php echo esc_attr($form_id); ?>">

                                        <div class='gresults-results-filter-section-label'>
                                            <?php _e("Filters", "gravityforms"); ?>
                                            &nbsp;<?php gform_tooltip("gresults_filters", "tooltip_bottomleft") ?></div>
                                        <div id="gresults-results-field-filters-container">

                                            <!-- placeholder populated by js -->

                                        </div>
                                        <div class='gresults-results-filter-section-label'>
                                            <?php _e("Date Range", "gravityforms"); ?>
                                            &nbsp;<?php gform_tooltip("gresults_date_range", "tooltip_left") ?>
                                        </div>
                                        <div style="width:90px; float:left; ">

                                            <label
                                                for="gresults-results-filter-date-start"><?php _e("Start", "gravityforms"); ?></label>
                                            <input type="text" id="gresults-results-filter-date-start" name="start"
                                                   style="width:80px"
                                                   class="gresults-datepicker"
                                                   value="<?php echo $start_date; ?>"/>
                                        </div>
                                        <div style="width:90px; float:left; ">
                                            <label
                                                for="gresults-results-filter-date-end"><?php _e("End", "gravityforms"); ?></label>
                                            <input type="text" id="gresults-results-filter-date-end" name="end"
                                                   style="width:80px"
                                                   class="gresults-datepicker"
                                                   value="<?php echo $end_date; ?>"/>
                                        </div>
                                        <br style="clear:both"/>

                                        <div id="gresults-results-filter-buttons">
                                            <input type="submit" id="gresults-results-filter-submit-button"
                                                   class="button button-primary button-large" value="Apply filters">
                                            <input type="button" id="gresults-results-filter-clear-button"
                                                   class="button button-secondary button-large" value="Clear"
                                                   onclick="gresults.clearFilterForm();">

                                            <div class="gresults-filter-loading"
                                                 style="display:none; float:right; margin-top:5px;">
                                                <img
                                                    src="<?php echo GFCommon::get_base_url() ?>/images/spinner.gif"
                                                    alt="loading..."/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gresults-filter-loading" style="display:none;margin:0 5px 10px 0;">
                        <img style="vertical-align:middle;"
                             src="<?php echo GFCommon::get_base_url() ?>/images/spinner.gif"
                             alt="loading..."/>&nbsp;
                        <a href="javascript:void(0);" onclick="javascript:gresultsAjaxRequest.abort()">Cancel</a>
                    </div>

                    <div id="gresults-results-wrapper">
                        <div id="gresults-results" >&nbsp;
                        </div>
                    </div>

                <?php else :
                    _e("This form does not have any fields that can be used for results", "gravityforms");
                endif ?>
            </div>


        <?php
        }

        public static function add_tooltips($tooltips) {
            $tooltips["gresults_total_score"] = "<h6>" . __("Total Score", "gravityforms") . "</h6>" . __("Scores are weighted calculations. Items ranked higher are given a greater score than items that are ranked lower. The total score for each item is the sum of the weighted scores.", "gravityforms");
            $tooltips["gresults_agg_rank"]    = "<h6>" . __("Aggregate Rank", "gravityforms") . "</h6>" . __("The aggregate rank is the overall rank for all entries based on the weighted scores for each item.", "gravityforms");
            $tooltips["gresults_date_range"]  = "<h6>" . __("Date Range", "gravityforms") . "</h6>" . __("Date Range is optional, if no date range is specified it will be ignored.", "gravityforms");
            $tooltips["gresults_filters"]     = "<h6>" . __("Filters", "gravityforms") . "</h6>" . __("Narrow the results by adding filters. Note that some field types support more options than others.", "gravityforms");

            return $tooltips;
        }


        public function ajax_get_results() {
            $output          = array();
            $html            = "";
            $form_id         = rgpost("id");
            $form            = GFFormsModel::get_form_meta($form_id);
            $form            = apply_filters("gform_form_pre_results_$form_id", apply_filters("gform_form_pre_results", $form));
            $search_criteria = array();
            $fields          = $this->get_fields($form);
            $total_entries   = GFAPI::count_entries($form_id);
            if ($total_entries == 0) {
                $html = __("No results.", "gravityforms");
            } else {

                $search_criteria["field_filters"] = GFCommon::get_field_filters_from_post();

                $start_date = rgpost("start");
                $end_date   = rgpost("end");
                if ($start_date)
                    $search_criteria["start_date"] = $start_date;
                if ($end_date)
                    $search_criteria["end_date"] = $end_date;

                $search_criteria["status"] = "active";
                $output["s"]       = http_build_query($search_criteria);
                $state_array       = null;
                if (isset($_POST["state"])) {
                    $state               = $_POST["state"];
                    $posted_check_sum    = rgpost("checkSum");
                    $generated_check_sum = self::generate_checksum($state);
                    $state_array         = json_decode(base64_decode($state), true);
                    if ($generated_check_sum !== $posted_check_sum) {
                        $output["status"] = "complete";
                        $output["html"]   = __('There was an error while processing the entries. Please contact support.', "gravityforms");
                        echo json_encode($output);
                        die();
                    }
                }
                $data        = $this->get_results_data($form, $fields, $search_criteria, $state_array);
                $entry_count = $data["entry_count"];

                if ("incomplete" === rgar($data, "status")) {
                    $state                 = base64_encode(json_encode($data));
                    $output["status"]      = "incomplete";
                    $output["stateObject"] = $state;
                    $output["checkSum"]    = self::generate_checksum($state);
                    $output["html"]        = sprintf(__('Entries processed: %1$d of %2$d', "gravityforms"), rgar($data, "offset"), $entry_count);
                    echo json_encode($output);
                    die();
                }

                if ($total_entries > 0) {
                    $html = isset($this->_callbacks["markup"]) ? call_user_func($this->_callbacks["markup"], $html, $data, $form, $fields) : "";
                    if (empty($html)) {
                        foreach ($fields as $field) {
                            $field_id = $field['id'];
                            $html .= "<div class='gresults-results-field' id='gresults-results-field-{$field_id}'>";
                            $html .= "<div class='gresults-results-field-label'>" . esc_html(GFCommon::get_label($field)) . "</div>";
                            $html .= "<div>" . self::get_field_results($form_id, $data, $field, $search_criteria) . "</div>";
                            $html .= "</div>";
                        }
                    }

                } else {
                    $html .= __("No results", "gravityforms");
                }
            }

            $output["html"]           = $html;
            $output["status"]         = "complete";
            $output["searchCriteria"] = $search_criteria;
            echo json_encode($output);
            die();
        }



        public static function ajax_get_more_results() {
            $form_id         = rgpost("form_id");
            $field_id        = rgpost("field_id");
            $offset          = rgpost("offset");
            $search_criteria = rgpost("search_criteria");

            if (empty($search_criteria))
                $search_criteria = array();
            $page_size = 10;

            $form                  = RGFormsModel::get_form_meta($form_id);
            $form_id               = $form["id"];
            $field                 = RGFormsModel::get_field($form, $field_id);
            $entry_count           = GFAPI::count_entries($form_id, $search_criteria);
            $html                  = self::get_default_field_results($form_id, $field, $search_criteria, $offset, $page_size);
            $remaining             = $entry_count - ($page_size + $offset);
            $remaining             = $remaining < 0 ? 0 : $remaining;
            $response              = array();
            $response["remaining"] = $remaining;
            $response['html']      = $html;

            echo json_encode($response);
            die();
        }

        private static function generate_checksum($data) {
            return wp_hash(crc32(($data)));
        }


        public static function get_total_entries($form) {
            $totals = RGFormsModel::get_form_counts($form["id"]);

            return $totals["total"];
        }

        public static function get_field_results($form_id, $data, $field, $search_criteria) {
            $field_data    = $data["field_data"];
            $entry_count   = $data["entry_count"];
            $field_results = "";
            if (empty($field_data[$field["id"]])) {
                $field_results .= __("No entries for this field", "gravityforms");

                return $field_results;
            }
            $field_type = GFFormsModel::get_input_type($field);
            switch ($field_type) {
                case "radio" :
                case "checkbox" :
                case "select" :
                case "rating" :
                case "multiselect" :
                    $results          = $field_data[$field["id"]];
                    $non_zero_results = is_array($results) ? array_filter($results) : $results;
                    if (empty($non_zero_results)) {
                        $field_results .= __("No entries for this field", "gravityforms");

                        return $field_results;
                    }
                    $choices = $field["choices"];

                    $data_table    = array();
                    $data_table [] = array(__('Choice', "gravityforms"), __('Frequency', "gravityforms"));

                    foreach ($choices as $choice) {
                        $text          = $choice["text"];
                        $val           = $results[$choice['value']];
                        $data_table [] = array($text, $val);
                    }

                    $bar_height        = 40;
                    $chart_area_height = (count($choices) * $bar_height);

                    $chart_options = array(
                        'isStacked' => true,
                        'height'    => ($chart_area_height + $bar_height),
                        'chartArea' => array(
                            'top'    => 0,
                            'left'   => 200,
                            'height' => $chart_area_height,
                            'width'  => '100%'
                        ),
                        'series'    => array(
                            '0' => array(
                                'color'           => 'silver',
                                'visibleInLegend' => 'false'
                            )
                        ),
                        'hAxis'     => array(
                            'viewWindowMode' => 'explicit',
                            'viewWindow'     => array('min' => 0),
                            'title'          => __('Frequency', "gravityforms")
                        )

                    );

                    $data_table_json = htmlentities(json_encode($data_table), ENT_QUOTES, 'UTF-8', true);
                    $options_json    = htmlentities(json_encode($chart_options), ENT_QUOTES, 'UTF-8', true);
                    $div_id          = "gresults-results-chart-field-" . $field["id"];
                    $height          = ""; //             = sprintf("height:%dpx", (count($choices) * $bar_height));

                    $field_results .= sprintf('<div class="gresults-chart-wrapper" style="width: 100%%;%s" id=%s data-datatable=\'%s\' data-options=\'%s\' data-charttype="bar" ></div>', $height, $div_id, $data_table_json, $options_json);


                    break;
                case "likert" :
                    $results       = $field_data[$field["id"]];
                    $multiple_rows = rgar($field, "gsurveyLikertEnableMultipleRows") ? true : false;

                    $n = 100;

                    $xr = 255;
                    $xg = 255;
                    $xb = 255;

                    $yr = 100;
                    $yg = 250;
                    $yb = 100;

                    $field_results .= "<div class='gsurvey-likert-field-results'>";
                    $field_results .= "<table class='gsurvey-likert'>";
                    $field_results .= "<tr>";
                    if ($multiple_rows)
                        $field_results .= "<td></td>";

                    foreach ($field["choices"] as $choice) {
                        $field_results .= "<td class='gsurvey-likert-choice-label'>" . $choice['text'] . "</td>";
                    }
                    $field_results .= "</tr>";

                    foreach ($field["gsurveyLikertRows"] as $row) {
                        $row_text  = $row["text"];
                        $row_value = $row["value"];
                        $max       = 0;
                        foreach ($field["choices"] as $choice) {
                            if ($multiple_rows) {
                                $choice_value       = rgar($choice, "value");
                                $results_row        = rgar($results, $row_value);
                                $results_for_choice = rgar($results_row, $choice_value);
                                $max                = max(array($max, $results_for_choice));
                            } else {
                                $max = max(array($max, $results[$choice['value']]));
                            }

                        }

                        $field_results .= "<tr>";

                        if ($multiple_rows)
                            $field_results .= "<td class='gsurvey-likert-row-label'>" . $row_text . "</td>";

                        foreach ($field["choices"] as $choice) {
                            $val     = $multiple_rows ? $results[$row_value][$choice['value']] : $results[$choice['value']];
                            $percent = $max > 0 ? round($val / $max * 100, 0) : 0;
                            $red     = (int)(($xr + (($percent * ($yr - $xr)) / ($n - 1))));
                            $green   = (int)(($xg + (($percent * ($yg - $xg)) / ($n - 1))));
                            $blue    = (int)(($xb + (($percent * ($yb - $xb)) / ($n - 1))));
                            $clr     = 'rgb(' . $red . ',' . $green . ',' . $blue . ')';
                            $field_results .= "<td class='gsurvey-likert-results' style='background-color:{$clr}'>" . $val . "</td>";
                        }
                        $field_results .= "</tr>";

                        if (false === $multiple_rows)
                            break;

                    }
                    $field_results .= "</table>";
                    $field_results .= "</div>";

                    if (rgar($field, "gsurveyLikertEnableScoring") && class_exists("GFSurvey")) {
                        $sum           = $results["sum_of_scores"];
                        $average_score = $sum == 0 ? 0 : round($sum / $entry_count, 3);
                        $field_results .= "<div class='gsurvey-likert-score'>" . __("Average score: ", "gravityforms") . $average_score . "</div>";
                    }

                    break;
                case "rank" :
                    $results = $field_data[$field["id"]];
                    arsort($results);
                    $field_results .= "<div class='gsurvey-rank-field-results'>";
                    $field_results .= " <table>";
                    $field_results .= "     <tr class='gresults-results-field-table-header'>";
                    $field_results .= "         <td class='gresults-rank-field-label'>";
                    $field_results .= __("Item", "gravityforms");
                    $field_results .= "         </td>";
                    $field_results .= "         <td class='gresults-rank-field-score'>";
                    $field_results .= __("Total Score", "gravityforms") . "&nbsp;" . gform_tooltip("gresults_total_score", null, true);
                    $field_results .= "         </td>";
                    $field_results .= "         <td class='gresults-rank-field-rank'>";
                    $field_results .= __("Aggregate Rank", "gravityforms") . "&nbsp;" . gform_tooltip("gresults_agg_rank", null, true);
                    $field_results .= "         </td>";
                    $field_results .= "     </tr>";

                    $agg_rank = 1;
                    foreach ($results as $choice_val => $score) {
                        $field_results .= "<tr>";
                        $field_results .= "      <td class='gresults-rank-field-label' style='text-align:left;'>";
                        $field_results .= RGFormsModel::get_choice_text($field, $choice_val);
                        $field_results .= "      </td>";
                        $field_results .= "      <td class='gresults-rank-field-score'>";
                        $field_results .= $score;
                        $field_results .= "      </td>";
                        $field_results .= "      <td class='gresults-rank-field-rank'>";
                        $field_results .= $agg_rank;
                        $field_results .= "      </td>";
                        $field_results .= "</tr>";
                        $agg_rank++;
                    }
                    $field_results .= "</table>";
                    $field_results .= "</div>";

                    break;
                default :
                    $page_size = 5;
                    $offset    = 0;
                    $field_id  = $field["id"];

                    $field_results .= "<div class='gresults-results-field-sub-label'>" . __("Latest entries:", "gravityforms") . "</div>";

                    $field_results .= "<ul id='gresults-results-field-content-{$field_id}' class='gresults-results-field-content' data-offset='{$page_size}'>";
                    $field_results .= self::get_default_field_results($form_id, $field, $search_criteria, $offset, $page_size);
                    $field_results .= "</ul>";

                    if ($entry_count > 5) {
                        $field_results .= "<a id='gresults-results-field-more-link-{$field_id}' class='gresults-results-field-more-link' href='javascript:void(0)' onclick='gresults.getMoreResults({$form_id},{$field_id})'>Show more</a>";
                    }
                    break;
            }

            return $field_results;

        }

        public function get_results_data($form, $fields, $search_criteria = array(), $state_array = array(), $max_execution_time = 15 /* seconds */) {
            // todo: add hooks to modify $max_execution_time and $page_size?

            $page_size = 30;

            $time_start = microtime(true);

            $form_id     = $form["id"];
            $data        = array();
            $offset      = 0;
            $entry_count = 0;
            $field_data  = array();


            if ($state_array) {
                //get counts from state
                $data   = $state_array;
                $offset = (int)rgar($data, "offset");

                unset($data["offset"]);
                $entry_count = $offset;
                $field_data  = rgar($data, "field_data");
            } else {
                //initialize counts
                foreach ($fields as $field) {
                    $field_type = GFFormsModel::get_input_type($field);
                    if (false === isset($field["choices"])) {
                        $field_data[$field["id"]] = 0;
                        continue;
                    }
                    $choices = $field["choices"];

                    if ($field_type == "likert" && rgar($field, "gsurveyLikertEnableMultipleRows")) {
                        foreach ($field["gsurveyLikertRows"] as $row) {
                            foreach ($choices as $choice) {
                                $field_data[$field["id"]][$row["value"]][$choice['value']] = 0;
                            }
                        }
                    } else {
                        foreach ($choices as $choice) {
                            $field_data[$field["id"]][$choice['value']] = 0;
                        }
                    }
                    if ($field_type == "likert" && rgar($field, "gsurveyLikertEnableScoring")) {
                        $field_data[$field["id"]]["sum_of_scores"] = 0;
                    }

                }
            }

            $count_search_leads  = GFAPI::count_entries($form_id, $search_criteria);
            $data["entry_count"] = $count_search_leads;

            $entries_left = $count_search_leads - $offset;

            while ($entries_left >= 0) {

                $paging = array(
                    'offset'    => $offset,
                    'page_size' => $page_size
                );

                $search_leads_time_start = microtime(true);
                $leads                   = GFFormsModel::search_leads($form_id, $search_criteria, null, $paging);
                $search_leads_time_end   = microtime(true);
                $search_leads_time       = $search_leads_time_end - $search_leads_time_start;

                $leads_in_search = count($leads);

                $entry_count += $leads_in_search;

                foreach ($leads as $lead) {
                    foreach ($fields as $field) {
                        $field_type = GFFormsModel::get_input_type($field);
                        $field_id   = $field["id"];
                        $value      = RGFormsModel::get_lead_field_value($lead, $field);

                        if ($field_type == "likert" && rgar($field, "gsurveyLikertEnableMultipleRows")) {

                            if (empty($value))
                                continue;
                            foreach ($value as $value_vector) {
                                if (empty($value_vector))
                                    continue;
                                list($row_val, $col_val) = explode(":", $value_vector, 2);
                                if (isset($field_data[$field["id"]][$row_val]) && isset($field_data[$field["id"]][$row_val][$col_val])) {
                                    $field_data[$field["id"]][$row_val][$col_val]++;
                                }
                            }
                        } elseif ($field_type == "rank") {
                            $score  = count(rgar($field, "choices"));
                            $values = explode(",", $value);
                            foreach ($values as $ranked_value) {
                                $field_data[$field["id"]][$ranked_value] += $score;
                                $score--;
                            }
                        } else {

                            if (false === isset($field["choices"])) {
                                if (false === empty($value))
                                    $field_data[$field_id]++;
                                continue;
                            }
                            $choices = $field["choices"];
                            foreach ($choices as $choice) {
                                $choice_is_selected = false;
                                if (is_array($value)) {
                                    $choice_value = rgar($choice, "value");
                                    if (in_array($choice_value, $value))
                                        $choice_is_selected = true;
                                } else {
                                    if (RGFormsModel::choice_value_match($field, $choice, $value))
                                        $choice_is_selected = true;
                                }
                                if ($choice_is_selected) {
                                    $field_data[$field_id][$choice['value']]++;
                                }
                            }

                        }
                        if ($field_type == "likert" && rgar($field, "gsurveyLikertEnableScoring")) {
                            $field_data[$field["id"]]["sum_of_scores"] += $this->get_likert_score($field, $lead);
                        }


                    }

                }
                $data["field_data"] = $field_data;
                if (isset($this->_callbacks["calculation"]))
                    $data = call_user_func($this->_callbacks["calculation"], $data, $form, $fields, $leads);

                $offset += $page_size;
                $entries_left -= $page_size;

                $time_end       = microtime(true);
                $execution_time = ($time_end - $time_start);

                if ($entries_left > 0 && $execution_time + $search_leads_time > $max_execution_time) {
                    $data["status"]   = "incomplete";
                    $data["offset"]   = $offset;
                    $progress         = $data["entry_count"] > 0 ? round($data["offset"] / $data["entry_count"] * 100) : 0;
                    $data["progress"] = $progress;
                    break;
                }

                if ($entries_left <= 0) {
                    $data["status"] = "complete";
                }
            }

            $data["timestamp"] = time();

            return $data;
        }

        public function get_likert_score($field, $entry) {
            return is_callable(array("GFSurvey", "get_field_score")) ? GFSurvey::get_field_score($field, $entry) : 0;
        }


        public static function get_default_field_results($form_id, $field, $search_criteria, $offset, $page_size) {
            $field_results = "";

            $paging = array('offset' => $offset, 'page_size' => $page_size);

            $sorting = array('key' => "date_created", 'direction' => "DESC");

            $leads = GFFormsModel::search_leads($form_id, $search_criteria, $sorting, $paging);

            foreach ($leads as $lead) {

                $value   = RGFormsModel::get_lead_field_value($lead, $field);
                $content = apply_filters("gform_entries_field_value", $value, $form_id, $field["id"], $lead);

                $field_results .= "<li>{$content}</li>";
            }

            return $field_results;

        }
    }
}
