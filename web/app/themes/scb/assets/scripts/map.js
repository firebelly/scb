var SCB = window.SCB || {};

SCB.Map = (function() {
  var map,
      mapFeatureLayer,
      mapGeoJSON = [],
      mapIcon,
      mapTop,
      loadingTimer;

  function _init() {
    // Only init Mapbox if > breakpoint_medium, or on a body.single page (small sidebar maps)
    // if ($('#map').length && (breakpoint_medium || $('body.single').length)) {
    return;
    if ($('#map').length) {
      _initMap();
    }
  }

  function _initMap() {
    L.mapbox.accessToken = MAPBOX_KEY; // pulled from .env, set in \Firebelly\Map\mapbox_key()
    map = L.mapbox.map('map', 'firebellydesign.0238ce0b', { zoomControl: false, attributionControl: false }).setView([41.843, -88.075], 11);

    mapFeatureLayer = L.mapbox.featureLayer().addTo(map);

    // mapIcon = L.circleMarker(latlng, {
    //   color: '#fff',
    //   fill: false,
    //   radius: 10
    // });

    // Set custom icons
    // mapFeatureLayer.on('layeradd', function(e) {
    //   var marker = e.layer;
    //   var feature = marker.feature;
    //   marker.setIcon(feature.properties.icon);
    // });

    // Larger map behavior
    if ($('#map').hasClass('large')) {
      // Disable zoom/scroll
      map.dragging.disable();
      map.touchZoom.disable();
      map.doubleClickZoom.disable();
      map.scrollWheelZoom.disable();

      // Prevent the listeners from disabling default
      // actions (http://bingbots.com/questions/1428306/mapbox-scroll-page-on-touch)
      L.DomEvent.preventDefault = function(e) {return;};

      // Clicking on point opens URL
      mapFeatureLayer.on('click', function(e) {
        e.layer.closePopup();
        location.href = e.layer.feature.properties.url;
      });

    _getMapPoints();
  }

  function _getMapPoints() {
    var $mapPoints = $('.map-point:not(.mapped)');
    if ($mapPoints.length) {
      // Any map-points on page? add to map
      $mapPoints.each(function() {
        var id = $(this).data('id');
        var $point = $(this).addClass('mapped');
        if ($point.data('lng')) {
          mapGeoJSON.push({
              type: 'Feature',
              geometry: {
                  type: 'Point',
                  coordinates: [ $point.data('lng'), $point.data('lat') ]
              },
              properties: {
                  title: $point.data('title'),
                  id: $point.data('id'),
                  url: $point.data('url'),
                  description: $point.data('desc'),
                  icon: mapIconRed
              }
          });
        }
      });
      // Add the array of point objects
      mapFeatureLayer.setGeoJSON(mapGeoJSON);
      // Set bounds to US
      map.setView([39.9, -90.5], 7);
    }
  }

  return {
    init: _init
  };
})();

// Fire up the mothership
jQuery(document).ready(SCB.Map.init);

// Zig-zag the mothership
// jQuery(window).resize(SCB.Map.resize);
