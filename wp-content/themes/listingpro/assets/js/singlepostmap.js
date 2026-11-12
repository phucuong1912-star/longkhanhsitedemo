$siteURL = jQuery('#page').data("site-url");
$pinicon = jQuery('#singlepostmap').data('pinicon');
$mapboxDesign = jQuery('#page').data("mstyle");
if ($pinicon === "") {
    $pinicon = "<i class='fa-solid fa-star'></i>";
}else{
	$pinicon = "<img alt='image' src='" + $pinicon + "'  />";
}
if (jQuery('.singlebigmaptrigger').length != 0) {
    $lat = jQuery('.singlebigmaptrigger').data("lat");
    $lan = jQuery('.singlebigmaptrigger').data("lan");
    "use strict";
    $mtoken = jQuery('#page').data("mtoken");
    $mtype = jQuery('#page').data("mtype");
    if ($mtype == 'mapbox' || $mtype == 'openstreet') {
        //new code 2.6.15
        var mapzoom_opn = 17;
        if (jQuery('body').is('.single-listing')) {
            mapzoom_opn = jQuery('body').data('mapzoom');
        }
        //end new code 2.6.15
        L.HtmlIcon = L.Icon.extend({
            options: {
                /*
                html: (String) (required)
                iconAnchor: (Point)
                popupAnchor: (Point)
                */
            },
            initialize: function (options) {
                L.Util.setOptions(this, options);
            },
            createIcon: function () {
                var div = document.createElement('div');
                div.innerHTML = this.options.html;
                if (div.classList)
                    div.classList.add('leaflet-marker-icon');
                else
                    div.className += ' ' + 'leaflet-marker-icon';
                return div;
            },
            createShadow: function () {
                return null;
            }
        });
        if ($mtoken != '' && $mtype == 'mapbox') {
            L.mapbox.accessToken = $mtoken;
            var map = L.mapbox.map('singlepostmap')
                .setView([$lat, $lan], mapzoom_opn); //new code 2.6.15
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                id: jQuery('#page').data("mstyle"),
                accessToken: jQuery('#page').data("mtoken")
            }).addTo(map);
        } else if ($mtype == 'openstreet') {
            var map = new L.Map('singlepostmap', '').setView(new L.LatLng($lat, $lan), mapzoom_opn); //new code 2.6.15
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        }
        var markers = new L.MarkerClusterGroup();
        var markerLocation = new L.LatLng($lat, $lan); // London
        var CustomHtmlIcon = L.HtmlIcon.extend({
            options: {
                html: "<div class='lpmap-icon-shape pin '><div class='lpmap-icon-contianer'>" + $pinicon + "</div></div>",
            }
        });
        var customHtmlIcon = new CustomHtmlIcon();
        var marker = new L.Marker(markerLocation, { icon: customHtmlIcon }).bindPopup('').addTo(map);
        markers.addLayer(marker);
        map.fitBounds(markers.getBounds());
        map.scrollWheelZoom.disable();
        map.invalidateSize();
        map.setZoom(mapzoom_opn); //new code 2.6.15

    } else {
    	$pinicon = jQuery('#singlepostmap').data('pinicon');
        if ($pinicon === "") {
            $pinicon = $siteURL + "wp-content/themes/listingpro/assets/images/pins/pin.png";
        }
        if ($lan != '' && $lat != '') {
            function init() {
                var e = {
                    zoom: 17,
                    scrollwheel: !1,
                    center: new google.maps.LatLng($lat, $lan),
                    styles: [{
                        featureType: "administrative",
                        elementType: "labels.text.fill",
                        stylers: [{
                            color: "#444444"
                        }]
                    },
                    {
                        featureType: "landscape",
                        elementType: "all",
                        stylers: [{
                            color: "#f2f2f2"
                        }]
                    },
                    {
                        featureType: "poi",
                        elementType: "all",
                        stylers: [{
                            visibility: "off"
                        }]
                    },
                    {
                        featureType: "road",
                        elementType: "all",
                        stylers: [{
                            saturation: -100
                        },
                        {
                            lightness: 45
                        }
                        ]
                    },
                    {
                        featureType: "road.highway",
                        elementType: "all",
                        stylers: [{
                            visibility: "simplified"
                        }]
                    },
                    {
                        featureType: "road.arterial",
                        elementType: "labels.icon",
                        stylers: [{
                            visibility: "off"
                        }]
                    },
                    {
                        featureType: "transit",
                        elementType: "all",
                        stylers: [{
                            visibility: "off"
                        }]
                    },
                    {
                        featureType: "water",
                        elementType: "all",
                        stylers: [{
                            color: "#b7ecf0"
                        },
                        {
                            visibility: "on"
                        }
                        ]
                    }
                    ]
                },
                    l = document.getElementById("singlepostmap"),
                    t = new google.maps.Map(l, e);
                new google.maps.Marker({
                    position: new google.maps.LatLng($lat, $lan),
                    icon: "" + $pinicon + "",
                    map: t,
                    title: ""
                })
            }
            google.maps.event.addDomListener(window, "load", init);
        }
    }
}
if (jQuery('#lp-map-hide-show').length != 0) {
    $elat = jQuery('#eventmap-popup').data("lat");
    $elan = jQuery('#eventmap-popup').data("lan");
    $pinicon = jQuery('#eventmap-popup').data("pinicon");
    "use strict";
    $mtoken = jQuery('#page').data("mtoken");
    $mtype = jQuery('#page').data("mtype");
    if ($mtype == 'mapbox' || $mtype == 'openstreet') {
        L.HtmlIcon = L.Icon.extend({
            options: {
                /*
                html: (String) (required)
                iconAnchor: (Point)
                popupAnchor: (Point)
                */
            },
            initialize: function (options) {
                L.Util.setOptions(this, options);
            },
            createIcon: function () {
                var div = document.createElement('div');
                div.innerHTML = this.options.html;
                if (div.classList)
                    div.classList.add('leaflet-marker-icon');
                else
                    div.className += ' ' + 'leaflet-marker-icon';
                return div;
            },
            createShadow: function () {
                return null;
            }
        });
        if ($mtoken != '' && $mtype == 'mapbox') {
            L.mapbox.accessToken = $mtoken;
            var map = L.mapbox.map('eventmap-popup')
                .setView([$elat, $lean], 17);
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                id: jQuery('#page').data("mstyle"),
                accessToken: jQuery('#page').data("mtoken")
            }).addTo(map);
        } else {
            var map = new L.Map('eventmap-popup', '').setView(new L.LatLng($lat, $lan), 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        }
        var markers = new L.MarkerClusterGroup();
        var markerLocation = new L.LatLng($elat, $elan); // London
        var CustomHtmlIcon = L.HtmlIcon.extend({
            options: {
                html: "<div class='lpmap-icon-shape pin '><div class='lpmap-icon-contianer'>" + $pinicon + "</div></div>",
            }
        });
        var customHtmlIcon = new CustomHtmlIcon();
        var marker = new L.Marker(markerLocation, { icon: customHtmlIcon }).bindPopup('').addTo(map);
        markers.addLayer(marker);
        map.fitBounds(markers.getBounds());
        map.scrollWheelZoom.disable();
        map.invalidateSize();
    } else {
        if ($elan != '' && $elat != '') {
            function init_event() {
                var e = {
                    zoom: 17,
                    scrollwheel: !1,
                    center: new google.maps.LatLng($elat, $elan),
                    styles: [{
                        featureType: "administrative",
                        elementType: "labels.text.fill",
                        stylers: [{
                            color: "#444444"
                        }]
                    },
                    {
                        featureType: "landscape",
                        elementType: "all",
                        stylers: [{
                            color: "#f2f2f2"
                        }]
                    },
                    {
                        featureType: "poi",
                        elementType: "all",
                        stylers: [{
                            visibility: "off"
                        }]
                    },
                    {
                        featureType: "road",
                        elementType: "all",
                        stylers: [{
                            saturation: -100
                        },
                        {
                            lightness: 45
                        }
                        ]
                    },
                    {
                        featureType: "road.highway",
                        elementType: "all",
                        stylers: [{
                            visibility: "simplified"
                        }]
                    },
                    {
                        featureType: "road.arterial",
                        elementType: "labels.icon",
                        stylers: [{
                            visibility: "off"
                        }]
                    },
                    {
                        featureType: "transit",
                        elementType: "all",
                        stylers: [{
                            visibility: "off"
                        }]
                    },
                    {
                        featureType: "water",
                        elementType: "all",
                        stylers: [{
                            color: "#b7ecf0"
                        },
                        {
                            visibility: "on"
                        }
                        ]
                    }
                    ]
                },
                    l = document.getElementById("eventmap-popup"),
                    t = new google.maps.Map(l, e);
                new google.maps.Marker({
                    position: new google.maps.LatLng($elat, $elan),
                    icon: "" + $pinicon + "",
                    map: t,
                    title: ""
                })
            }
            google.maps.event.addDomListener(window, "load", init_event);
        }
    }
}