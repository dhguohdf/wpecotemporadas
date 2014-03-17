<?php
$options["Activated"] = false;
if(defined( "DSIDXPRESS_OPTION_NAME" )) {
	$options = get_option( DSIDXPRESS_OPTION_NAME );
}
$show_idx = bon_get_option('use_idx_search', 'no');

$class = '';
if($show_idx == 'yes' && $options["Activated"] ) {
	$class = 'dsidx-search-listing';
}

$row_1 = bon_get_option('search_row_1');
$row_2 = bon_get_option('search_row_2');
$row_3 = bon_get_option('search_row_3');

if($row_1 && $row_2 && $row_3) {
	$class = 'dsidx-search-listing';
}
?>
<div id="search-listing" class="search-listing <?php echo $class; ?>">
	<div class="row">
		<div id="zoom-icon" class="column large-1 show-for-medium-up">
			<i class="sha-zoom search-icon"></i>
		</div>
		<div class="column large-11 small-12 small-centered large-uncentered">
			<?php shandora_search_listing_form(); ?>
		</div>
	</div>
</div>