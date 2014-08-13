
jQuery(document).ready( function($) {
    $('#shandora_listing_zip').mask('00000-000');
    $('#shandora_listing_price').mask('000.000.000,00', {reverse: true});
	$('#shandora_listing_bed, #shandora_listing_bath, #shandora_listing_basement, #shandora_listing_floor, #shandora_listing_totalroom, #shandora_listing_lotsize, #shandora_listing_buildingsize, #shandora_listing_yearbuild').mask('0000');
    
});