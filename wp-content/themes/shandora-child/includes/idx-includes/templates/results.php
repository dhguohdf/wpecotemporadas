 <?php

if (extension_loaded('newrelic')) {
    newrelic_name_transaction('results');
}

do_action('bon_idx_admin_panel');

?>
 <div id="dsidx" class="custom-idx-results">
 <?php

do_action('bon_idx_results_404_message');

if (self::has_listings()) {
        
    do_action('bon_idx_results_sorting_control');

    if(bon_get_option('show_listings_map') == 'show') {
      do_action('bon_idx_results_map');
    }
    
    do_action('bon_idx_results_listings');
    
    do_action('bon_idx_results_pagination');
    
} else {
    
    do_action('bon_idx_no_results');
    
}
  do_action('bon_idx_disclaimer');
  if(bon_get_option('show_listings_map') == 'show') {
    do_action('bon_idx_results_map_divs');
  }
  do_action('bon_idx_dsidx_javascript_details');
?>
<script>

jQuery(document).ready(function($){
	if($('#listings-map').length < 1) {
        if( ($('#dsidx-map-control').length > 0) && (!$.cookie('dsidx_map_open') || $.cookie('dsidx_map_open') == 0 ) ) {
            $('#dsidx-map-control a').trigger('click');
        }
    }
  
            if($('#listings-map').length > 0 ) {
                var mapContainer = document.getElementById('listings-map');
                var mapOptions = {
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: false,
                    scrollwheel: false,
                    disableDefaultUI: true,
                    disableDoubleClickZoom: true,
                    zoom: 8
                };
                var x = new google.maps.MarkerImage("<?php echo plugins_url() . '/bon-toolkit/assets/images/marker-blue.png'; ?>");
                var map = new google.maps.Map(mapContainer, mapOptions);
                var infowindow = new google.maps.InfoWindow();
                var marker, i;
                var bounds = new google.maps.LatLngBounds();
                var e = dsidx.dataSets['results'];
                for (i = 0; i < e.length; i++) {
                    var pos = new google.maps.LatLng(e[i].Latitude, e[i].Longitude);
                    bounds.extend(pos);
                    marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        icon: x,
                    });
                    google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
                        var cs = '';
                        var title = '';

                        title = (e[i].Address ? e[i].Address : "<i>no address</i>") + ", " + (e[i].City ? e[i].City : "<i>no city</i>");
                        cs += '<div class="listing-map-hover clear">';
                        cs += '<div class="listing-map-image">';
                        cs += '<img src="' + e[i].PhotoUriBase + '0-thumb.jpg" alt="' + title + '" />';
                        cs += '</div>';
                        cs += '<div class="listing-map-details"><h4 class="listing-map-title"><a href="' + e[i].PrettyUriForUrl + '" title="' + title + '">' + title + '</a></h4>';
                        cs += '<div class="listing-map-price">' + e[i].Price + '</div>';
                        cs += '<ul class="listing-map-meta">';
                        cs += '<li class="listing-map-beds">' + e[i].BedsShortString + '</li>';
                        cs += '<li class="listing-map-beds">' + e[i].BathsShortString + '</li>';
                        cs += '</ul></div>';
                        cs += '</div>';
                        return function () {
                            infowindow.setContent(cs);
                            infowindow.open(map, marker)
                        }
                    })(marker, i))
                }
                map.fitBounds(bounds);
            }
        
});
</script>
 </div>