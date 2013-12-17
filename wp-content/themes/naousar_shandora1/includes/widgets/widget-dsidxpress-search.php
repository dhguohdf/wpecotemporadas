<?php
class Shandora_dsIDXpress_Search extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'search-search-dsidx-listing',
			'description' => esc_html__( 'Search Listing from MLS dsIDXpress.', 'bon' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			
		);

		/* Create the widget. */
		$this->WP_Widget(
			'shandora-search-dsidx-listing',               // $this->id_base
			__( 'Shandora Search IDX', 'bon' ), // $this->name
			$widget_options,                 // $this->widget_options
			$control_options                 // $this->control_options
		);

		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_script') );
	}

	function enqueue_script($hook) {
		if ($hook == 'widgets.php') {
			wp_enqueue_script('dsidxpress_widget_search', DSIDXPRESS_PLUGIN_URL . 'js/widget-search.js', array('jquery'), DSIDXPRESS_PLUGIN_VERSION, true);
		}
	}

	function widget( $args, $instance ) {

		extract($args);
		extract($instance);

		$title = apply_filters("widget_title", $title);
		$options = get_option(DSIDXPRESS_OPTION_NAME);

		if (!$options["Activated"])
			return;
		

		$pluginUrl = plugins_url() . '/dsidxpress/';

		wp_enqueue_script('dsidxpress_widget_search_view', $pluginUrl . 'js/widget-client.js', array('jquery'), DSIDXPRESS_PLUGIN_VERSION, true);

		$formAction = get_home_url() . "/idx/";

		$defaultSearchPanels = dsSearchAgent_ApiRequest::FetchData("AccountSearchPanelsDefault", array(), false, 60 * 60 * 24);
		$defaultSearchPanels = $defaultSearchPanels["response"]["code"] == "200" ? json_decode($defaultSearchPanels["body"]) : null;
		$propertyTypes = dsSearchAgent_ApiRequest::FetchData("AccountSearchSetupFilteredPropertyTypes", array(), false, 60 * 60 * 24);
		$propertyTypes = $propertyTypes["response"]["code"] == "200" ? json_decode($propertyTypes["body"]) : null;

		$account_options = dsSearchAgent_ApiRequest::FetchData("AccountOptions", array(), false);
		$account_options = $account_options["response"]["code"] == "200" ? json_decode($account_options["body"]) : null;

		$num_location_dropdowns = 0;

		$autoload_options = bon_get_option('idx_enable_search_autoload', 'yes');

		if($searchOptions["show_cities"] == "yes" || !isset($instance["searchOptions"]["show_cities"])) {
			$num_location_dropdowns++;
		}

		if($searchOptions["show_cities"] == "yes") {
			if( $autoload_options == 'no') {
				$manual_city = explode("\n", trim(bon_get_option('idx_manual_city')));
				sort($manual_city);
				$searchOptions['cities'] = $manual_city;
			} else {
				$searchOptions['cities'] = shandora_get_idx_options('City', true);
			}
			
		}
		if($searchOptions["show_communities"] == "yes") {
			$num_location_dropdowns++;
			if( $autoload_options == 'no') {
				$manual_community = explode("\n", trim(bon_get_option('idx_manual_community')));
				sort($manual_community);
				$searchOptions['communities'] = $manual_community;
			} else {
				$searchOptions['communities'] = shandora_get_idx_options('Community', true);
			}
		}
		if($searchOptions["show_tracts"] == "yes") {
			$num_location_dropdowns++;
			if( $autoload_options == 'no') {
				$manual_tract = explode("\n", trim(bon_get_option('idx_manual_tract')));
				sort($manual_tract);
				$searchOptions['tracts'] = $manual_tract;
			} else {
				$searchOptions['tracts'] = shandora_get_idx_options('Tract', true);
			}
		}
		if($searchOptions["show_zips"] == "yes") {
			$num_location_dropdowns++;
			if( $autoload_options == 'no') {
				$manual_zip = explode("\n", trim(bon_get_option('idx_manual_zip')));
				sort($manual_zip);
				$searchOptions['zips'] = $manual_zip;
			} else {
				$searchOptions['zips'] = shandora_get_idx_options('Zip', true);
			}
		}
		if($searchOptions["show_mlsnumber"] == "yes") {
			$num_location_dropdowns++;
		}

		echo $before_widget;
		if ($title)
			echo $before_title . $title . $after_title;
        
		?>
			<div class="dsidx-search-widget dsidx-widget search-listing">
			<form action="<?php echo $formAction; ?>" method="get" class="custom" onsubmit="return dsidx_w.searchWidget.validate();" >
				<label for="idx-q-PropertyTypes"><?php _e('Property Type','bon'); ?></label>
				<select name="idx-q-PropertyTypes" class="dsidx-search-widget-propertyTypes">
								<option value=""><?php _e('All Property Types','bon'); ?></option>

		<?php 
		if (is_array($propertyTypes)) {
			foreach ($propertyTypes as $propertyType) {
				$name = htmlentities($propertyType->DisplayName); ?>
				<option value="<?php echo $propertyType->SearchSetupPropertyTypeID; ?>"><?php echo $name; ?></option>
			<?php }
		}
		?>
				</select>
				<label id="idx-search-invalid-msg" style="color:red"></label>
        

        <?php if($searchOptions["show_cities"] == "yes" || !isset($instance["searchOptions"]["show_cities"])) { ?>
					<label for="idx-q-Cities"><?php _e('City','bon'); ?></label>
					<select id="idx-q-Cities" name="idx-q-Cities" class="idx-q-Location-Filter">
						<?php if($num_location_dropdowns > 1) { ?>
								<option value=""><?php _e('Any','bon'); ?></option>
						<?php }
						if(!empty($searchOptions['cities'])) {
						foreach ($searchOptions["cities"] as $city) {
							$city = htmlentities(trim($city)); ?>
						<option value="<?php echo $city; ?>"><?php echo $city; ?></option>
						<?php } } ?>
					</select>
		<?php 
		} // end show_citites


		if($searchOptions["show_communities"] == "yes") { ?>
					<label for="idx-q-Communities"><?php _e('Community','bon'); ?></label>
					<select id="idx-q-Communities" name="idx-q-Communities" class="idx-q-Location-Filter">
						<?php if($num_location_dropdowns > 1) { ?>
								<option value=""><?php _e('Any','bon'); ?></option>
						<?php }
						foreach ($searchOptions["communities"] as $community) {
							// there's an extra trim here in case the data was corrupted before the trim was added in the update code below
							$community = htmlentities(trim($community)); ?>
							<option value="<?php echo $community; ?>"><?php echo $community; ?></option>
						<?php } ?>
					</select>
		<?php } // end show_communtities ?>



		<?php if($searchOptions["show_tracts"] == "yes") { ?>

				<label for="idx-q-TractIdentifiers"><?php _e('Tract','bon'); ?></label>
				<select id="idx-q-TractIdentifiers" name="idx-q-TractIdentifiers" class="idx-q-Location-Filter">
				<?php if($num_location_dropdowns > 1) { ?>
						<option value=""><?php _e('Any','bon'); ?></option>
				<?php }
					foreach ($searchOptions["tracts"] as $tract) {
						// there's an extra trim here in case the data was corrupted before the trim was added in the update code below
						$tract = htmlentities(trim($tract)); ?>
						<option value="<?php echo $tract; ?>"><?php echo $tract; ?></option>
					<?php } ?>
				</select>

		<?php } // end show_tracts ?>


		<?php if($searchOptions["show_zips"] == "yes") { ?>

				<label for="idx-q-ZipCodes"><?php _e('Zip','bon'); ?></label>
				<select id="idx-q-ZipCodes" name="idx-q-ZipCodes" class="idx-q-Location-Filter">

				<?php if($num_location_dropdowns > 1) { ?>
						<option value=""><?php _e('Any','bon'); ?></option>
				<?php } 
				foreach ($searchOptions["zips"] as $zip) {
					// there's an extra trim here in case the data was corrupted before the trim was added in the update code below
					$zip = htmlentities(trim($zip)); ?>
					<option value="<?php echo $zip; ?>"><?php echo $zip; ?></option>
					<?php } ?>
				</select>

		<?php } // end show_zips ?>


		<?php if($searchOptions["show_mlsnumber"] == "yes") { ?>
			<label for="idx-q-MlsNumbers"><?php _e('MLS #','bon'); ?></label>
			<input id="idx-q-MlsNumbers" name="idx-q-MlsNumbers" type="text" class="dsidx-mlsnumber" />

		<?php } ?>
			<label for="idx-q-PriceMin"><?php _e('Price Range','bon'); ?>
				<span class="price-text text-min" id="idx-min-price-text-widget"></span>
				<span class="price-text text-max" id="idx-max-price-text-widget"></span>
			</label>
			<div class="price-slider-wrapper ui-slider-wrapper-custom">
				<div id="idx-slider-range-widget"></div>
			</div>
			<input id="idx-q-PriceMin-widget" name="idx-q-PriceMin" type="hidden" class="dsidx-price" placeholder="min price" />
			<input id="idx-q-PriceMax-widget" name="idx-q-PriceMax" type="hidden" class="dsidx-price" placeholder="max price" />

		<?php if(isset($defaultSearchPanels)){
			foreach ($defaultSearchPanels as $key => $value) {
				if ($value->DomIdentifier == "search-input-home-size") {
		?>
		
			<label for="idx-q-ImprovedSqFtMin"><?php _e('Size','bon'); ?></label>
			<input id="idx-q-ImprovedSqFtMin" name="idx-q-ImprovedSqFtMin" type="text" class="dsidx-improvedsqft" placeholder="min sqft" />
						
		<?php break; } } } ?>

		
					<label for="idx-q-BedsMin-widget"><?php _e('Beds','bon'); ?></label>
					<div class="ui-slider-wrapper-custom beds-wrapper">
					<select name="idx-q-BedsMin" id="idx-q-BedsMin-widget" class="bon-dsidx-beds dsidx-beds no-custom select-slider">
						<option value=""><?php _e('Any','bon'); ?></option>
						<?php for($i = 1; $i <= 10; $i++) { ?>
							<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php } ?>
					</select>
					</div>

					<label for="idx-q-BathsMin-widget"><?php _e('Baths','bon'); ?></label>
					<div class="ui-slider-wrapper-custom baths-wrapper">
					<select name="idx-q-BathsMin" id="idx-q-BathsMin-widget" class="bon-dsidx-baths dsidx-baths no-custom select-slider">
						<option value=""><?php _e('Any','bon'); ?></option>
						<?php for($i = 1; $i <= 10; $i++) { ?>
							<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php } ?>
					</select>
					</div>
					
				
					<?php $button_color = bon_get_option('search_button_color', 'red'); ?>
					<input type="submit" class="button flat blue <?php echo $button_color; ?> radius submit" value="<?php _e('Search for Properties','bon'); ?>" />

		<?php if($options["HasSearchAgentPro"] == "yes" && $searchOptions["show_advanced"] == "yes") { ?>
		
					<?php _e('try our ','bon'); ?><a href="<?php echo $formAction; ?>advanced/"><img src="<?php echo $pluginUrl; ?>assets/adv_search-16.png" /><?php _e('Advanced Search','bon'); ?></a>
		<?php } ?>
		
		<?php if($account_options->EulaLink) { $eula_url = $account_options->EulaLink; ?>
		
		<p><?php _e('By searching, you agree to the','bon'); ?> <a href="<?php echo $eula_url; ?>" target="_blank"><?php _e('EULA','bon'); ?></a></p>

		<?php } ?>
		
		
				
			</form>
			</div>

		<?php
		
		echo $after_widget;
		dsidx_footer::ensure_disclaimer_exists("search");
	}

	function update($new_instance, $old_instance) {
		$new_instance["title"] = strip_tags($new_instance["title"]);
		

		if($new_instance["searchOptions"]["show_cities"] == "on") $new_instance["searchOptions"]["show_cities"] = "yes";
		else $new_instance["searchOptions"]["show_cities"] = "no";

		if($new_instance["searchOptions"]["show_communities"] == "on") $new_instance["searchOptions"]["show_communities"] = "yes";
		else $new_instance["searchOptions"]["show_communities"] = "no";

		if($new_instance["searchOptions"]["show_tracts"] == "on") $new_instance["searchOptions"]["show_tracts"] = "yes";
		else $new_instance["searchOptions"]["show_tracts"] = "no";

		if($new_instance["searchOptions"]["show_zips"] == "on") $new_instance["searchOptions"]["show_zips"] = "yes";
		else $new_instance["searchOptions"]["show_zips"] = "no";

		if($new_instance["searchOptions"]["show_mlsnumber"] == "on") $new_instance["searchOptions"]["show_mlsnumber"] = "yes";
		else $new_instance["searchOptions"]["show_mlsnumber"] = "no";

		if($new_instance["searchOptions"]["show_advanced"] == "on") $new_instance["searchOptions"]["show_advanced"] = "yes";
		else $new_instance["searchOptions"]["show_advanced"] = "no";

		return $new_instance;
	}
	function form($instance) {
		$pluginUrl = DSIDXPRESS_PLUGIN_URL;

		$options = get_option(DSIDXPRESS_OPTION_NAME);

		$instance = wp_parse_args($instance, array(
			"title" => "Search MLS Real Estate",
			"searchOptions" => array(
				"cities" => array(),
				"communities" => array(),
				"tracts" => array(),
				"zips" => array(),
				"show_cities" => "",
				"show_communities" => "",
				"show_zips" => "",
				"show_mlsnumber" => "",
				"show_tracts" => "",
				"show_advanced" => "",
			),

		));

		$title = htmlspecialchars($instance["title"]);

		$titleFieldId = $this->get_field_id("title");
		$titleFieldName = $this->get_field_name("title");
		$searchOptionsFieldId = $this->get_field_id("searchOptions");
		$searchOptionsFieldName = $this->get_field_name("searchOptions");

		$show_cities = $instance["searchOptions"]["show_cities"] == "yes" || !isset($instance["searchOptions"]["show_cities"]) ? "checked=\"checked\" " : "";
		$show_communities = $instance["searchOptions"]["show_communities"] == "yes" ? "checked=\"checked\" " : "";
		$show_tracts = $instance["searchOptions"]["show_tracts"] == "yes" ? "checked=\"checked\" " : "";
		$show_zips = $instance["searchOptions"]["show_zips"] == "yes" ? "checked=\"checked\" " : "";
		$show_mlsnumber = $instance["searchOptions"]["show_mlsnumber"] == "yes" ? "checked=\"checked\" " : "";
		$show_advanced = $instance["searchOptions"]["show_advanced"] == "yes" ? "checked=\"checked\" " : "";

		?>
			<p>
				<label for="{$titleFieldId}">Widget title</label>
				<input id="{$titleFieldId}" name="{$titleFieldName}" value="{$title}" class="widefat" type="text" />
			</p>

			<p>
				<h3>Fields to Display</h3>
				<div id="{$searchOptionsFieldId}-show_checkboxes" class="search-widget-searchOptions">
					<input type="checkbox" id="{$searchOptionsFieldId}-show_cities" name="{$searchOptionsFieldName}[show_cities]" {$show_cities} onclick="dsWidgetSearch.ShowBlock(this);"/>
					<label for="{$searchOptionsFieldId}-show_cities">Cities</label><br />
					<input type="checkbox" id="{$searchOptionsFieldId}-show_communities" name="{$searchOptionsFieldName}[show_communities]" {$show_communities} onclick="dsWidgetSearch.ShowBlock(this);"/>
					<label for="{$searchOptionsFieldId}-show_communities">Communities</label><br />
					<input type="checkbox" id="{$searchOptionsFieldId}-show_tracts" name="{$searchOptionsFieldName}[show_tracts]" {$show_tracts} onclick="dsWidgetSearch.ShowBlock(this);"/>
					<label for="{$searchOptionsFieldId}-show_tracts">Tracts</label><br />
					<input type="checkbox" id="{$searchOptionsFieldId}-show_zips" name="{$searchOptionsFieldName}[show_zips]" {$show_zips} onclick="dsWidgetSearch.ShowBlock(this);"/>
					<label for="{$searchOptionsFieldId}-show_zips">Zips</label><br />
					<input type="checkbox" id="{$searchOptionsFieldId}-show_mlsnumber" name="{$searchOptionsFieldName}[show_mlsnumber]" {$show_mlsnumber} onclick="dsWidgetSearch.ShowBlock(this);"/>
					<label for="{$searchOptionsFieldId}-show_mlsnumber">MLS #'s</label><br />
<?php
		if($options["HasSearchAgentPro"] == "yes") {
			?>
					<input id="{$searchOptionsFieldId}-show-advanced" name="{$searchOptionsFieldName}[show_advanced]" class="checkbox" type="checkbox" {$show_advanced} onclick="dsWidgetSearch.ShowBlock(this);"/>
					<label for="{$searchOptionsFieldId}-show-advanced">Show Advanced Option</label>
<?php
		} ?>
				</div>
			</p>
<?php
	}
}
?>