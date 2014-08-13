<?php
/**
 * Specialist Add-On class designed for use by Add-Ons that collect payment
 *
 * @package GFPaymentAddOn
 *
 * NOTE: This class is still undergoing development and is not ready to be used on live sites.
 */

require_once('class-gf-feed-addon.php' );
abstract class GFPaymentAddOn extends GFFeedAddOn {

    private $_payment_version = "0.2";

    protected $authorization = array();


    //--------- Initialization ----------
    public function pre_init(){
        parent::pre_init();

        // Intercepting callback requests
        add_action('parse_request', array($this, "maybe_process_callback"));

        if ($this->payment_method_is_overridden("check_status"))
            $this->setup_cron();

    }

    public function init_admin() {

        parent::init_admin();

        //enables credit card field
        add_filter("gform_enable_credit_card_field", "__return_true");

        add_filter("gform_currencies", array($this, "supported_currencies"));

        if(rgget("page") == "gf_entries"){
            add_action('gform_entry_info',array($this, "entry_info"), 10, 2);
        }
    }

    public function init_frontend(){

        parent::init_frontend();

        add_filter("gform_confirmation", array($this, "confirmation"), 20, 4);

        add_filter("gform_validation", array($this, "validation"));
        add_action("gform_entry_post_save", array($this, "entry_post_save"), 10, 2);
    }

    public function init_ajax(){

        add_action('wp_ajax_gaddon_cancel_subscription', array($this, 'start_cancel_subscription'));

    }

    protected function setup(){
        parent::setup();

        //upgrading Feed Add-On base class
        $installed_version = get_option("gravityformsaddon_payment_version");
        if ($installed_version != $this->_payment_version)
            $this->upgrade_payment($installed_version);

        update_option("gravityformsaddon_payment_version", $this->_payment_version);
    }

    private function upgrade_payment($previous_version) {
        global $wpdb;

        $charset_collate = GFFormsModel::get_db_charset();

        $sql = "CREATE TABLE {$wpdb->prefix}gf_addon_payment_transaction (
                  id int(10) unsigned not null auto_increment,
                  lead_id int(10) unsigned not null,
                  transaction_type varchar(30) not null,
                  transaction_id varchar(50),
                  is_recurring tinyint(1) not null default 0,
                  amount decimal(19,2),
                  date_created datetime,
                  PRIMARY KEY  (id),
                  KEY lead_id (lead_id),
                  KEY trasanction_type (transaction_type),
                  KEY type_lead (lead_id,transaction_type)
                ) $charset_collate;";

        GFFormsModel::dbDelta($sql);
    }

    //--------- Submission Process ------
    public function confirmation($confirmation, $form, $entry, $ajax){

        if(!$this->payment_method_is_overridden('redirect_url'))
            return $confirmation;

        $feed = $this->get_payment_feed($entry, $form);

        if(!$feed)
            return $confirmation;

        $submission_data = $this->get_submission_data($feed, $form, $entry);

        $url = $this->redirect_url($feed, $submission_data, $form, $entry);

        if($url)
            $confirmation = array("redirect" => $url);

        return $confirmation;
    }

    /**
     * Override this function to specify a URL to the third party payment processor. Useful when developing a payment gateway that processes the payment outsite of the website (i.e. PayPal Standard).
     * @param $feed - Active payment feed containing all the configuration data
     * @param $submission_data - Contains form field data submitted by the user as well as payment information (i.e. payment amount, setup fee, line items, etc...)
     * @param $form - Current form array containing all form settings
     * @param $entry - Current entry array containing entry information (i.e data submitted by users)
     * @return string - Return a full URL (inlucing http:// or https://) to the payment processor
     */
    protected function redirect_url($feed, $submission_data, $form, $entry){

    }

    public function validation($validation_result){

        if(!$this->payment_method_is_overridden('authorize') && !$this->payment_method_is_overridden('authorize_capture') && !$this->payment_method_is_overridden('authorize_subscribe'))
            return $validation_result;

        //Getting submission data
        $form = $validation_result["form"];
        $entry = GFFormsModel::create_lead($form);
        $feed = $this->get_payment_feed($entry, $form);

        if(!$feed)
            return $validation_result;

        $submission_data = $this->get_submission_data($feed, $form, $entry);

        //Running an authorization only transaction if function is implemented
        if($this->payment_method_is_overridden('authorize'))
            $this->authorization = $this->authorize($feed, $submission_data, $form, $entry);

//        //If authorization was bypassed by previous step, try authorize_capture or authorize_subscribe
//        if(empty($this->authorization)){
//
//            if($feed["meta"]["transactionType"] == "product" && $this->method_is_overridden('authorize_capture')){
//                $this->authorization = $this->authorize_capture($feed, $submission_data, $form, $entry);
//            }
//            else if($feed["meta"]["transactionType"] == "subscription" && $this->method_is_overridden('authorize_subscribe')){
//                $this->authorization = $this->authorize_subscribe($feed, $submission_data, $form, $entry);
//            }
//
//            $this->authorization["is_captured"] = true;
//        }

        $this->authorization["feed"] = $feed;
        $this->authorization["submission_data"] = $submission_data;

        if(!$this->authorization["is_authorized"]){
            $validation_result = $this->get_validation_result($validation_result, $this->authorization);

            //Setting up current page to point to the credit card page since that will be the highlighted field
            GFFormDisplay::set_current_page($validation_result["form"]["id"], $validation_result["credit_card_page"]);
        }

        return $validation_result;
    }

    /**
     * Override this method to add integration code to the payment processor in order to authorize a credit card without capturing payment.
     * @param $feed - Current configured payment feed
     * @param $submission_data - Contains form field data submitted by the user as well as payment information (i.e. payment amount, setup fee, line items, etc...)
     * @param $form - Current form array containing all form settings
     * @param $entry - Current entry array containing entry information (i.e data submitted by users). NOTE: the entry hasn't been saved to the database at this point, so this $entry object does not have the "ID" property and is only a memory representation of the entry.
     * @return array - Return an $authorization array in the following format:
     * [
     *  "is_authorized" => true|false,
     *  "error_message" => "Error message",
     *  "captured_payment" => ["is_success"=>true|false, "error_message" => "error message", "transaction_id" => "xxx", "amount" => 20]
     *  "subscription" => ["is_success"=>true|false, "error_message" => "error message", "subscription_id" => "xxx", "amount" => 10]
     * ]
     */
    protected function authorize($feed, $submission_data, $form, $entry){

    }

    protected function get_validation_result($validation_result, $authorization_result){

        $credit_card_page = 0;
        foreach($validation_result["form"]["fields"] as &$field)
        {
            if($field["type"] == "creditcard")
            {
                $field["failed_validation"] = true;
                $field["validation_message"] = $authorization_result["error_message"];
                $credit_card_page = $field["pageNumber"];
                break;
            }
        }

        $validation_result["credit_card_page"] = $credit_card_page;
        $validation_result["is_valid"] = false;

        return $validation_result;

    }

    public function entry_post_save($entry, $form){

        //Abort if authorization wasn't done.
        if(empty($this->authorization))
            return $entry;

        $feed = $this->authorization["feed"];

        if($feed["meta"]["transactionType"] == "product"){

            if($this->payment_method_is_overridden('capture') && rgempty("captured_payment", $this->authorization)){
                $capture_response = $this->capture($this->authorization, $feed, $this->authorization["submission_data"], $form, $entry);
                $this->authorization["captured_payment"] = $capture_response;
            }

            $this->process_capture($this->authorization, $feed, $this->authorization["submission_data"], $form, $entry);

        }
        else if($feed["meta"]["transactionType"] == "subscription"){

            if($this->payment_method_is_overridden('subscribe') && rgempty("subscription", $this->authorization)){
                $subscription_response = $this->subscribe($this->authorization, $feed, $this->authorization["submission_data"], $form, $entry);
                $this->authorization["subscription"] = $subscription_response;
            }

            $this->process_subscription($this->authorization, $feed, $this->authorization["submission_data"], $form, $entry);
        }

        return $entry;
    }

    protected function process_capture($authorization, $feed, $submission_data, $form, $entry){

        $payment = rgar($authorization,"captured_payment");
        if(empty($payment))
            return;

        if($payment["is_success"]){

            $entry["transaction_id"] = $payment["transaction_id"];
            $entry["transaction_type"] = "1";
            $entry["is_fulfilled"] = true;
            $entry["currency"] = GFCommon::get_currency();
            $entry["payment_amount"] = $payment["amount"];
            $entry["payment_status"] = "Approved";
            $entry["payment_date"] = gmdate("Y-m-d H:i:s");

            $this->insert_transaction($entry["id"], "payment", $entry["transaction_id"], $entry["payment_amount"]);

            GFFormsModel::add_note($entry["id"], 0, "System", sprintf(__("Payment has been captured successfully. Amount: %s. Transaction Id: %s", "gravityforms"), GFCommon::to_money($payment["amount"], $entry["currency"]),$payment["transaction_id"]));
        }
        else{
            $entry["payment_status"] = "Failed";
            GFFormsModel::add_note($entry["id"], 0, "System", sprintf( __("Payment failed to be captured. Reason: %s", "gravityforms") , $payment["error_message"] ));
        }

        GFFormsModel::update_lead($entry);

        return $entry;

    }

    protected function process_subscription($authorization, $feed, $submission_data, $form, $entry){

        $subscription = rgar($authorization,"subscription");
        if(empty($subscription))
            return;

        //If setup fee / trial is captured as part of a separate transaction
        $payment = rgar($authorization,"captured_payment");
        $payment_name = rgempty("name", $payment) ? __("Initial payment", "gravityforms") : $payment["name"];
        if($payment && $payment["is_success"]){

            $this->insert_transaction($entry["id"], "payment", $payment["transaction_id"], $payment["amount"]);
            GFFormsModel::add_note($entry["id"], 0, "System", sprintf(__("%s has been captured successfully. Amount: %s. Transaction Id: %s", "gravityforms"), $payment_name, GFCommon::to_money($payment["amount"], $entry["currency"]),$payment["transaction_id"]));

        }
        else if($payment && !$payment["is_success"]){

            GFFormsModel::add_note($entry["id"], 0, "System", sprintf(__("Failed to capture %s. Reason: %s.", "gravityforms"), $payment("error_message"), $payment_name));
        }

        //Updating subscription information
        if($subscription["is_success"]){

            $entry["transaction_id"] = $subscription["subscription_id"];
            $entry["transaction_type"] = "2";
            $entry["is_fulfilled"] = true;
            $entry["currency"] = GFCommon::get_currency();
            $entry["payment_amount"] = $subscription["amount"];
            $entry["payment_status"] = "Active";
            $entry["payment_date"] = gmdate("Y-m-d H:i:s");

            GFFormsModel::add_note($entry["id"], 0, "System", sprintf(__("Subscription successfully created. Subscription Id: %s.", "gravityforms"), $subscription["subscription_id"]));

        }
        else{
            $entry["payment_status"] = "Failed";

            GFFormsModel::add_note($entry["id"], 0, "System", sprintf( __("Subscription failed to be created. Reason: %s", "gravityforms") , $subscription["error_message"]));
        }

        GFFormsModel::update_lead($entry);

        return $entry;
    }

    protected function capture($authorization, $feed, $submission_data, $form, $entry){

    }

    protected function subscribe($authorization, $feed, $submission_data, $form, $entry){

    }

    protected function insert_transaction($entry_id, $transaction_type, $transaction_id, $amount){
        global $wpdb;

        $payment_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) FROM {$wpdb->prefix}gf_addon_payment_transaction WHERE lead_id=%d", $entry_id));
        $is_recurring = $payment_count > 0 && $transaction_type == "payment" ? 1 : 0;

        $sql = $wpdb->prepare(" INSERT INTO {$wpdb->prefix}gf_addon_payment_transaction (lead_id, transaction_type, transaction_id, amount, is_recurring, date_created)
                                values(%d, %s, %s, %f, %d, utc_timestamp())", $entry_id, $transaction_type, $transaction_id, $amount, $is_recurring);
        $wpdb->query($sql);

        return $wpdb->insert_id;
    }

    public function get_payment_feed($entry, $form) {
        $submission_feed = GFCache::get("payment_feed");

        if(!$submission_feed){

            if(!empty($entry["id"])){
                $feeds = $this->get_processed_feeds($entry["id"]);
                $submission_feed = $this->get_feed($feeds[0]);
            }
            else{
                // getting all active feeds
                $feeds =  $this->get_feeds( $form['id'] );

                foreach ( $feeds as $feed ) {
                    if ( $this->is_feed_condition_met( $feed, $form, $entry ) ){
                        $submission_feed = $feed;
                        break;
                    }
                }
            }
            GFCache::set("payment_feed", $submission_feed);
        }

        return $submission_feed;
    }

    protected function is_payment_gateway($entry_id){
        $feeds = $this->get_processed_feeds($entry_id);
        return is_array($feeds) && count($feeds) > 0;
    }

    protected function get_submission_data($feed, $form, $entry){

        $form_data = array();

        $form_data["form_title"] = $form["title"];

        //getting mapped field data
        $billing_fields = $this->billing_info_fields();
        foreach($billing_fields as $billing_field){
            $field_name = $billing_field["name"];
            $form_data[$field_name] = rgpost('input_'. str_replace(".", "_", rgar($feed["meta"],"billingInformation_{$field_name}") ));
        }

        //getting credit card field data
        $card_field = $this->get_creditcard_field($form);
        if($card_field){

            $form_data["card_number"] = rgpost("input_{$card_field["id"]}_1");
            $form_data["card_expiration_date"] = rgpost("input_{$card_field["id"]}_2");
            $form_data["card_security_code"] = rgpost("input_{$card_field["id"]}_3");
            $form_data["card_name"] = rgpost("input_{$card_field["id"]}_5");

//            $names = explode(" ", $form_data["card_name"]);
//            $form_data["card_first_name"] = rgar($names,0);
//            $form_data["card_last_name"] = rgempty(1,$names) ? rgar($names,0) : rgar($names,1);

        }

        //getting product field data
        $order_info = $this->get_order_data($feed, $form, $entry);
        $form_data = array_merge($form_data, $order_info);

        return $form_data;
    }

    protected function get_creditcard_field($form){
        $fields = GFCommon::get_fields_by_type($form, array("creditcard"));
        return empty($fields) ? false : $fields[0];
    }

    private function get_order_data($feed, $form, $entry){

        $products = GFCommon::get_product_fields($form, $entry);

        $payment_field = $feed["meta"]["transactionType"] == "product" ? $feed["meta"]["paymentAmount"] : $feed["meta"]["recurringAmount"];
        $setup_fee_field = rgar($feed["meta"],"setupFee_enabled") ? $feed["meta"]["setupFee_product"] : false;
        $trial_field = rgar($feed["meta"], "trial_enabled") ? $feed["meta"]["trial_product"] : false;

        $amount = 0;
        $line_items = array();
        $fee_amount = 0;
        $trial_amount = 0;
        foreach($products["products"] as $field_id => $product)
        {

            $quantity = $product["quantity"] ? $product["quantity"] : 1;
            $product_price = GFCommon::to_number($product['price']);

            $options = array();
            if(is_array(rgar($product, "options"))){
                foreach($product["options"] as $option){
                    $options[] = $option["option_name"];
                    $product_price += $option["price"];
                }
            }

            if(!empty($trial_field) && $trial_field == $field_id){
                $trial_amount = $product_price * $quantity;
            }
            else if(!empty($setup_fee_field) && $setup_fee_field == $field_id){
                $fee_amount = $product_price * $quantity;
            }
            else
            {
                if(is_numeric($payment_field) && $payment_field != $field_id)
                    continue;

                $amount += $product_price * $quantity;

                $description = "";
                if(!empty($options))
                    $description = __("options: ", "gravityformsauthorizenet") . " " . implode(", ", $options);

                if($product_price >= 0){
                    $line_items[] = array("id" => $field_id, "name"=>$product["name"], "description" =>$description, "quantity" =>$quantity, "unit_price"=>GFCommon::to_number($product_price));
                }
            }
        }

        if(!empty($products["shipping"]["name"]) && !is_numeric($payment_field)){
            $line_items[] = array("id" => "", "name"=>$products["shipping"]["name"], "description" =>"", "quantity" =>1, "unit_price"=>GFCommon::to_number($products["shipping"]["price"]));
            $amount += $products["shipping"]["price"];
        }

        return array("payment_amount" => $amount, "setup_fee" => $fee_amount, "trial" => $trial_amount, "line_items" => $line_items);
    }


    //--------- Callback ----------------
    public function maybe_process_callback(){

        //Ignoring requests that are not this addon's callbacks
        if( rgget("callback") != $this->_slug || !$this->payment_method_is_overridden("callback"))
            return;

        $result = $this->callback();

        if(is_wp_error($result)){
            status_header(500);
            echo $result->get_error_message();
        }
        else{
            status_header(200);
            echo "Callback processed successfully.";
        }

        die();
    }

    protected function callback(){

    }


    // -------- Cron --------------------
    protected function setup_cron()
    {
        // Setting up cron
        $cron_name = "{$this->_slug}_cron";

        add_action($cron_name, array($this, "check_status"));

        if (!wp_next_scheduled($cron_name))
            wp_schedule_event(time(), "daily", $cron_name);


    }

    public function check_status(){

    }

    //--------- List Columns ------------
    protected function feed_list_columns() {
        return array(
            'transactionType' => __('Transaction Type', 'gravityforms'),
            'amount' => __('Amount', 'gravityforms')
        );
    }

    public function get_column_value_transactionType($feed){
        switch(rgar($feed["meta"], "transactionType")){
            case "subscription" :
                return __("Subscription", "gravityforms");
                break;
            case "product" :
                return __("Products and Services", "gravityforms");
                break;

        }
        return __("Unsupported transaction type", "gravityforms");
    }

    public function get_column_value_amount($feed){
        $form = $this->get_current_form();
        $field_id = $feed["meta"]["transactionType"] == "subscription" ? $feed["meta"]["recurringAmount"] : $feed["meta"]["paymentAmount"];
        if($field_id == "form_total"){
            $label = __("Form Total", "gravityforms");
        }
        else{
            $field = GFFormsModel::get_field($form, $field_id);
            $label = GFCommon::get_label($field);
        }

        return $label;
    }


    //--------- Feed Settings ----------------
    public function feed_settings_fields() {
        return array(
            array(
                "title" => __("Feed Settings", "gravityforms"),
                "description" => '',
                "fields" => array(
                    array(
                        "name"=> "transactionType",
                        "label" => __("Transaction Type", "gravityforms"),
                        "type" => "select",
                        "onchange" => "jQuery(this).parents('form').submit();", //TODO: move this to base class
                        "choices" => array(
                            array("label" => __("Select a transaction type", "gravityforms"), "value" => ""),
                            array("label" => __("Products and Services", "gravityforms"), "value" => "product"),
                            array("label" => __("Subscription", "gravityforms"), "value" => "subscription")
                        )

                    ),
                    array(
                        "name" => "recurringAmount",
                        "label" => __("Recurring Amount", "gravityforms"),
                        "dependency" => array("field" => "transactionType", "values" => array("subscription")),
                        "type" => "select",
                        "choices" => $this->recurring_amount_choices()
                    ),
                    array(
                        "name" => "paymentAmount",
                        "label" => __("Payment Amount", "gravityforms"),
                        "dependency" => array("field" => "transactionType", "values" => array("product")),
                        "type" => "select",
                        "choices" => $this->product_amount_choices()
                    ),
                    array(
                        "name" => "billingCycle",
                        "label" => __("Billing Cycle", "gravityforms"),
                        "dependency" => array("field" => "transactionType", "values" => array("subscription")),
                        "type" => "billing_cycle",
                    ),
                    array(
                        "name" => "recurringTimes",
                        "label" => __("Recurring Times", "gravityforms"),
                        "dependency" => array("field" => "transactionType", "values" => array("subscription")),
                        "type" => "select",
                        "choices" => array(array("label" => "infinite", "value" => "0")) + $this->get_numeric_choices(1,100)
                    ),
                    array(
                        "name" => "setupFee",
                        "label" => __("Setup Fee", "gravityforms"),
                        "dependency" => array("field" => "transactionType", "values" => array("subscription")),
                        "type" => "setup_fee",
                    ),
                    array(
                        "name" => "trial",
                        "label" => __("Trial", "gravityforms"),
                        "dependency" => array("field" => "transactionType", "values" => array("subscription")),
                        "type" => "trial",
                        "hidden" => $this->get_setting("setupFee_enabled")
                    ),
                    array(
                        "name" => "billingInformation",
                        "label" => __("Billing Information", "gravityforms"),
                        "type" => "field_map",
                        "field_map" => $this->billing_info_fields()
                    ),
                    array(
                        "name" => "options",
                        "label" => __("Options", "gravityforms"),
                        "type" => "checkbox",
                        "choices" => $this->option_choices()
                    ),
                    array(
                        "name" => "conditionalLogic",
                        "label" => __("Conditional Logic", "gravityforms"),
                        "type" => "feed_condition"
                    ),

                    array( "type" => "save" )
                )
            )
        );
    }

    public function settings_billing_cycle( $field, $echo = true ) {

        $intervals = $this->supported_billing_intervals();

        //Length drop down
        $interval_keys = array_keys($intervals);
        $first_interval = $intervals[$interval_keys[0]];
        $length_field = array(
            "name" => $field["name"] . "_length",
            "type" => "select",
            "choices" => $this->get_numeric_choices($first_interval["min"], $first_interval["max"])
        );

        $html = $this->settings_select( $length_field, false );

        //Unit drop down
        $choices = array();
        foreach($intervals as $unit => $interval){
            if(!empty($interval))
                $choices[] = array("value" => $unit, "label" => $interval["label"]);
        }

        $unit_field = array(
            "name" => $field["name"] . "_unit",
            "type" => "select",
            "onchange" => "loadBillingLength('" . esc_attr($field["name"]) . "')",
            "choices" => $choices
        );

        $html .= "&nbsp" . $this->settings_select( $unit_field, false );

        $html .= "<script type='text/javascript'>var " . $field["name"] . "_intervals = " . json_encode($intervals) . ";</script>";

        if( $echo )
            echo $html;

        return $html;
    }

    public function settings_setup_fee( $field, $echo = true ) {

        $enabled_field = array(
            "type" => "checkbox",
            "horizontal" => true,
            "choices" => array(
                array(  "label" => __("Enabled", "gravityforms"),
                    "name" => $field["name"] . "_enabled",
                    "value"=>"1",
                    "onchange" => "if(jQuery(this).prop('checked')){jQuery('#{$field["name"]}_product').show('slow'); jQuery('#gaddon-setting-row-trial').hide('slow');} else {jQuery('#{$field["name"]}_product').hide('slow'); jQuery('#gaddon-setting-row-trial').show('slow');}"
                ))
        );

        $html = $this->settings_checkbox( $enabled_field, false );

        $form = $this->get_current_form();

        $is_enabled = $this->get_setting("{$field["name"]}_enabled");

        $product_field = array(
            "name" => $field["name"] . "_product",
            "type" => "select",
            "class" =>  $is_enabled ? "" : "hidden",
            "choices" => $this->get_payment_choices($form)
        );

        $html .= "&nbsp" . $this->settings_select( $product_field, false );

        if( $echo )
            echo $html;

        return $html;
    }

    public function settings_trial( $field, $echo = true ) {

        //--- Enabled field ---
        $enabled_field = array(
            "type" => "checkbox",
            "horizontal" => true,
            "choices" => array(
                array(  "label" => __("Enabled", "gravityforms"),
                    "name" => $field["name"] . "_enabled",
                    "value"=>"1",
                    "onchange" => "if(jQuery(this).prop('checked')){jQuery('#{$field["name"]}_product').show('slow'); } else {jQuery('#{$field["name"]}_product').hide('slow');}"
                ))
        );

        $html = $this->settings_checkbox( $enabled_field, false );

        //--- Select Product field ---
        $form = $this->get_current_form();
        $payment_choices = array_merge($this->get_payment_choices($form), array(array("label" => __("Enter an amount", "gravityforms"), "value" => "enter_amount")));

        $product_field = array(
            "name" => $field["name"] . "_product",
            "type" => "select",
            "class" =>  $this->get_setting("{$field["name"]}_enabled") ? "" : "hidden",
            "onchange" => "if(jQuery(this).val() == 'enter_amount'){jQuery('#{$field["name"]}_amount').show('slow');} else {jQuery('#{$field["name"]}_amount').hide('slow');}",
            "choices" => $payment_choices
        );

        $html .= "&nbsp" . $this->settings_select( $product_field, false );

        //--- Trial Amount field ----
        $amount_field = array(
            "type" => "text",
            "name" => "{$field["name"]}_amount",
            "class" =>  $this->get_setting("{$field["name"]}_product") == "enter_amount" ? "" : "hidden",
        );

        $html .= "&nbsp;" . $this->settings_text($amount_field, false);


        if( $echo )
            echo $html;

        return $html;
    }

    protected function recurring_amount_choices(){
        $form = $this->get_current_form();
        $recurring_choices = $this->get_payment_choices($form);
        $recurring_choices[] = array("label" => __("Form Total", "gravityforms"), "value" => "form_total");

        return $recurring_choices;
    }

    protected function product_amount_choices(){
        $form = $this->get_current_form();
        $product_choices = $this->get_payment_choices($form);
        $product_choices[] = array("label" => __("Form Total", "gravityforms"), "value" => "form_total");

        return $product_choices;
    }

    protected function option_choices(){

        $option_choices = array(
                            array("label" => __("Sample Option", "gravityforms"), "name" => "sample_option", "value" => "sample_option")
        );

        return $option_choices;
    }

    protected function billing_info_fields() {

        $fields = array(
            array("name" => "email", "label" => __("Email", "gravityforms"), "required" => false),
            array("name" => "address", "label" => __("Address", "gravityforms"), "required" => false),
            array("name" => "address2", "label" => __("Address 2", "gravityforms"), "required" => false),
            array("name" => "city", "label" => __("City", "gravityforms"), "required" => false),
            array("name" => "state", "label" => __("State", "gravityforms"), "required" => false),
            array("name" => "zip", "label" => __("Zip", "gravityforms"), "required" => false),
            array("name" => "country", "label" => __("Country", "gravityforms"), "required" => false),
        );

        return $fields;
    }

    public function get_numeric_choices($min, $max){
        $choices = array();
        for($i = $min; $i<=$max; $i++){
            $choices[] = array("label" => $i, "value" => $i);
        }
        return $choices;
    }

    protected function supported_billing_intervals(){
        $billing_cycles = array(
            "day" => array("label" => "day(s)", "min" => 1, "max" => 365),
            "week" => array("label" => "week(s)", "min" => 1, "max" => 52),
            "month" => array("label" => "month(s)", "min" => 1, "max" => 12),
            "year" => array("label" => "year(s)", "min" => 1, "max" => 10));

        return $billing_cycles;
    }

    private function get_payment_choices($form){
        $fields = GFCommon::get_fields_by_type($form, array("product"));
        $choices = array(
            array("label" => __("Select a product field", "gravityforms"), "value" => "")
        );

        foreach($fields as $field){
            $field_id = $field["id"];
            $field_label = RGFormsModel::get_label($field);
            $choices[] = array("value" => $field_id, "label" => $field_label);
        }

        return $choices;
    }

    //-------- Uninstall ---------------------
    protected function uninstall(){
        global $wpdb;

        // deleting transactions
        $sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}gf_addon_payment_transaction
                                WHERE feed_id IN (SELECT id FROM {$wpdb->prefix}gf_addon_feed WHERE addon_slug=%s)", $this->_slug);
        $wpdb->query($sql);

        //clear cron
        wp_clear_scheduled_hook($this->_slug . "_cron");

        parent::uninstall();
    }

    //-------- Scripts -----------------------
    public function scripts() {

        $scripts = array(
            array(
                'handle' => 'gaddon_payment',
                "src" => $this->get_gfaddon_base_url() . "/js/gaddon_payment.js",
                "version" => GFCommon::$version,
                "strings" => array(
                        "subscriptionCancelWarning" => __("Warning! This Authorize.Net Subscription will be canceled. This cannot be undone. 'OK' to cancel subscription, 'Cancel' to stop", "gravityforms"),
                        "subscriptionCancelNonce" => wp_create_nonce('gaddon_cancel_subscription'),
                        "subscriptionCanceled" => __("Canceled", "gravityforms"),
                        "subscriptionError" => __("The subscription could not be canceled. Please try again later.", "gravityforms")
                        ),
                'enqueue' => array( array( "admin_page" => array("form_settings"), "tab" => "gravityformspaypal" ),
                                    array( "admin_page" => array("entry_view") )
                             )
            )
        );

        return array_merge( parent::scripts(), $scripts );
    }


    //-------- Currency ----------------------
    /**
     * Override this function to add or remove currencies from the list of supported currencies
     * @param $currencies - Currently supported currencies
     * @return mixed - A filtered list of supported currencies
     */
    public function supported_currencies($currencies){
        return $currencies;
    }


    //-------- Cancel Subscription -----------
    public function entry_info($form_id, $entry) {

        //abort if subscription cancelation isn't supported by the addon
        if(!$this->payment_method_is_overridden("cancel_subscription"))
            return;

        // adding cancel subscription button and script to entry info section
        $cancelsub_button = "";
        if($entry["transaction_type"] == "2" && $entry["payment_status"] <> "Canceled" && $this->is_payment_gateway($entry["id"]))
        {
            ?>
            <input id="cancelsub" type="button" name="cancelsub" value="<?php _e("Cancel Subscription", "gravityforms") ?>" class="button" onclick="cancel_subscription(<?php echo $entry["id"] ?>);"/>
            <img src="<?php echo GFCommon::get_base_url() ?>/images/spinner.gif" id="subscription_cancel_spinner" style="display: none;"/>

            <script type="text/javascript">

            </script>

            <?php
        }
    }

    public function start_cancel_subscription() {
        check_ajax_referer("gaddon_cancel_subscription","gaddon_cancel_subscription");

        $entry_id = $_POST["entry_id"];
        $entry = GFAPI::get_entry($entry_id);

        $form = GFAPI::get_form($entry["form_id"]);
        $feed = $this->get_payment_feed($entry, $form);

        if($this->cancel_subscription($entry, $form, $feed))
        {
            $this->process_subscription_canceled($entry, $feed);
            die("1");
        }
        else{
            die("0");
        }

    }

    protected function process_subscription_canceled($entry, $feed){

        //Updating entry payment status
        $entry["payment_status"] = "Canceled";
        RGFormsModel::update_lead($entry);

        //Delete post or mark it as a draft based on feed configuration
        if(rgars($feed, "meta/update_post_action") == "draft" && !rgempty("post_id", $entry)){
            $post = get_post($entry["post_id"]);
            $post->post_status = 'draft';
            wp_update_post($post);
        }
        else if(rgars($feed, "meta/update_post_action") == "delete" && !rgempty("post_id", $entry)){
            wp_delete_post($entry["post_id"]);
        }

    }

    protected function cancel_subscription($entry, $form, $feed){
        return true;
    }

    //-------- Helpers -----------------------
    private function payment_method_is_overridden($method_name, $base_class='GFPaymentAddOn'){
        return parent::method_is_overridden($method_name, $base_class);
    }



}
