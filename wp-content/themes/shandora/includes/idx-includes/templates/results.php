 <?php

if (extension_loaded('newrelic')) {
    newrelic_name_transaction('results');
}

do_action('bon_idx_admin_panel');

echo '<div class="row"><div class="column large-12">';
do_action('bon_idx_before_results_content');
echo '</div></div>';
 
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

  do_action('bon_idx_after_results_content');
  do_action('bon_idx_dsidx_javascript_details');
?>
<script>

jQuery(document).ready(function($){
	if($('#dsidx-map').length > 0) {
        if( ($('#dsidx-map-control').length > 0) && (!$.cookie('dsidx_map_open') || $.cookie('dsidx_map_open') == 0 ) ) {
            $('#dsidx-map-control a').trigger('click');
        }
    }

            if($('#listings-map').length > 0 && typeof dsidx.dataSets.results !== "undefined") {
                //var mapContainer = document.getElementById('listings-map');
                var mapOptions = {
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: false,
                    scrollwheel: false,
                    disableDefaultUI: true,
                    disableDoubleClickZoom: true,
                    zoom: 8
                };
                var e = $("#listings-map").get(0),
                z = new google.maps.Size(21, 26),
                a = dsidx.dataSets.results,
                j = new google.maps.LatLngBounds,
                h, l, n = [],
                q = new google.maps.Point(0, 0),
                x = new google.maps.Size(21, 26),
                S = new google.maps.MarkerImage("http://cdn3.diverse-cdn.com/api/images/dsidxpress/markers/generic-shadow.png/bb8367", new google.maps.Size(35, 28), q, new google.maps.Point(11, 28)),
                T = new google.maps.MarkerImage("http://cdn1.diverse-cdn.com/api/images/dsidxpress/markers/short-single-house.png/5270c9", x, q),
                R = new google.maps.MarkerImage("http://cdn3.diverse-cdn.com/api/images/dsidxpress/markers/short-single-house-active.png/ce785e", x, q),
                z = 8;
                q = 0;

                for (x = a.length; q < x; ++q) a[q].Latitude == -1 || a[q].Latitude == 0 || j.extend(new google.maps.LatLng(a[q].Latitude, a[q].Longitude));
                if (a.length == 0 && dsidx.mapStart) {
                    j.extend(new google.maps.LatLng(dsidx.mapStart.latitude, dsidx.mapStart.longitude));
                    z = dsidx.mapStart.zoom
                }
                h = new google.maps.Map(e, {
                    center: j.getCenter(),
                    zoom: z,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: false,
                    scrollwheel: false,
                    draggable: false,
                    mapTypeControl: false,
                    disableDefaultUI: true,
                    disableDoubleClickZoom: true
                });
                a.length > 0 && h.fitBounds(j);
                $("#dsidx-map-hover").appendTo($("body"));
                q = 0;
                var infowindow = new google.maps.InfoWindow();
                for (x = a.length; q < x; ++q) {
                    e = a[q];
                    l = new google.maps.Marker({
                        position: new google.maps.LatLng(e.Latitude, e.Longitude),
                        map: h,
                        icon: T,
                        shadow: S
                    });
                    n.push(l);
                    (function (u) {

                        google.maps.event.addListener(l, 'mouseover', (function (l, x) {
                            var cs = '';
                            var title = '';
                            title = (u.Address ? u.Address : "<i>no address</i>") + ", " + (u.City ? u.City : "<i>no city</i>");
                            cs += '<div class="listing-map-hover clear">';
                            cs += '<div class="listing-map-image">';
                            cs += '<img src="' + u.PhotoUriBase + '0-thumb.jpg" alt="' + title + '" />';
                            cs += '</div>';
                            cs += '<div class="listing-map-details"><h4 class="listing-map-title"><a href="' + dsidx.idxActivationPath + u.PrettyUriForUrl + '" title="' + title + '">' + title + '</a></h4>';
                            cs += '<div class="listing-map-price">' + u.Price + '</div>';
                            cs += '<ul class="listing-map-meta">';
                            cs += '<li class="listing-map-beds">' + u.BedsShortString + '</li>';
                            cs += '<li class="listing-map-beds">' + u.BathsShortString + '</li>';
                            cs += '</ul></div>';
                            cs += '</div>';
                            return function () {
                                infowindow.setContent(cs);
                                infowindow.open(h, l);
                            }
                        })(l, x));

                        google.maps.event.addListener(l, "click", function () {
                            window.location = dsidx.idxActivationPath + u.PrettyUriForUrl
                        });
                       
                        google.maps.event.addListener(l, "mouseout", function () {
                            this.setIcon(T);
                            var y = $("#dsidx-map-hover");
                            y.attr("mls-number") == u.MlsNumber && y.css({
                                display: "none"
                            })
                        })
                    })(e)
                }
            }
        
});


</script>
 </div>