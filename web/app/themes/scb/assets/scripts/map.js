var SCB = window.SCB || {};
var MAPBOX_ID = 'firebellydesign.ipgc2fd2';

SCB.Map = (function() {
	var _map_loaded = false;
		_map_data,
		_markers,
		_map,
		_marker_clusterer,
		_center = { 'lat': 41.90, 'lng': -87.65 },
		_initial_map_bounds,
		_map_icon,
		_unique_latlngs = [],
		_icons = [],
		_popup_options = {
			maxWidth: 536,
			closeButton: false
		};

	function _init() {
		_init_map();
	}

	function _init_map() {
		var mapbox_url = 'http://{s}.tiles.mapbox.com/v3/' + MAPBOX_ID + '/{z}/{x}/{y}.png';
		var mapbox_attribution = 'Map data &copy; OpenStreetMap contributors, Imagery &copy; Mapbox';
		var mapbox_options = { maxZoom: 18, attribution: mapbox_attribution };
		var map_layer = new L.TileLayer(mapbox_url, mapbox_options, { styleId: 52528 });

		// initialize map
		_map = new L.Map('map', {
			center: new L.LatLng(_center.lat, _center.lng),
			zoom: 10,
			layers: [map_layer],
			doubleClickZoom: false
		});

		// hide filters when opening a popup
		_map.on('popupopen', function() {
			// _toggle_control_panel(true);
		});

		// _map.fitBounds(_initial_map_bounds);

		// initialize custom map marker
		_map_icon = L.Icon.extend({
			shadowUrl: 'images/marker-shadow.png',
			iconSize: new L.Point(73, 85),
			shadowSize: new L.Point(73, 85),
			iconAnchor: new L.Point(36, 85),
			popupAnchor: new L.Point(220, -78)
		});

		// initialize custom cluster markers (all the same yellow concentric circles)
		// three objects are required for all 3 density levels of clustering
		var clusterer_styles = [
			{
				url: 'images/marker-cluster.png',
				width: 64,
				height: 64,
				opt_textColor: '#ffffff',
				opt_textSize: 14
			},
			{
				url: 'images/marker-cluster.png',
				width: 64,
				height: 64,
				opt_textColor: '#ffffff',
				opt_textSize: 14
			},
			{
				url: 'images/marker-cluster.png',
				width: 64,
				height: 64,
				opt_textColor: '#ffffff',
				opt_textSize: 14
			}
		];

		// initialize marker clusterer
		_marker_clusterer = new LeafClusterer(_map, null, { styles: clusterer_styles });

		// bind close button when a popup opens
		_map.on('popupopen', function(e) {
			$('a.close').click(function(e) {
				e.preventDefault();
				_map.closePopup();
			});
		});

		// retrieve JSON data
		$.ajax({
			url: 'grants.json',
			dataType: 'json',
			success: function(data) {
				// store local copy of data to work with when filtering
				_map_data = data;

				// shortcut to currently displayed markers
				_markers = _map_data.markers;

				// place markers on the map
				_load_map_data();
			}
		});
	}

	function _load_map_data() {
		var ms = [];
		var m, m_lat, m_lng, latlng, icon, icon_url;

		_marker_clusterer.clearMarkers();

		// if first load of map, initialize bounds object
		// to set initial center and zoom level
		var bounds;
		if (!_map_loaded) {
			bounds = new L.LatLngBounds(0, 0);
		}

		for (var i = 0; i < _markers.length; i++) {
			m = _markers[i];
			m_lat = m.latitude;
			m_lng = m.longitude;

			// make sure this lat/lng is unique
			if ($.inArray(m_lat + ':' + m_lng, _unique_latlngs) !== -1) {
				m_lat = m_lat + 0.0002; // ??
				m_lng = m_lng + 0.0002;
			} else {
				_unique_latlngs.push(m_lat + ':' + m_lng);
			}
			latlng = new L.LatLng(m_lat, m_lng);

			// initialize icon for marker
			icon = new _map_icon('images/marker-' + m.category + '.png');

			// initialize marker
			marker = new L.Marker(latlng, { icon: icon });

			// retrieve icanhaz template for marker popup
			// popup_content = ich.popup(m, true);
			// marker.bindPopup(popup_content, _popup_options);

			ms.push(marker);

			// if initial load of map, do some set up
			if (!_map_loaded) {
				// add the markers lat/lng to the bounds
				bounds.extend(latlng);
			}
		}

		// put all the markers in our clusterer
		_marker_clusterer.addMarkers(ms);

		// if initial load of map, do some set up
		if (!_map_loaded) {
			// make map fit all initially loaded markers
			_map.fitBounds(bounds);

			_initial_map_bounds = bounds;

			_map_loaded = true;
		}
	}

	// stores subset of markers based on current collection of filters
	function _filter_data() {
		// start with all markers in this county
		_markers = _map_data.markers;

		// if (_filters['goals'].length > 0) {
		// 	_markers = $.grep(_markers, function(marker, iterator) {
		// 		var pass = false;

		// 		for (var i = 0; i < _filters['goals'].length; i++) {
		// 			if (marker.gt_community_goal_desc === _filters['goals'][i]) {
		// 				pass = true;
		// 				break;
		// 			}
		// 		}

		// 		return pass;
		// 	});
		// }

		// var low, high;

		// can only have two amounts filtered at a time - high and low
		// if (_filters['amounts'].length === 2) {
		// 	low = parseInt(_filters['amounts'][0], 10);
		// 	high = parseInt(_filters['amounts'][1], 10);

		// 	_markers = $.grep(_markers, function(marker, iterator) {
		// 		var amount = parseInt(marker.gt_approved_amt, 10);

		// 		var pass = (amount >= low && amount <= high);

		// 		return pass;
		// 	});
		// }

		// can only have two years filtered at a time - high and low
		// if (_filters['years'].length === 2) {
		// 	low = parseInt(_filters['years'][0], 10);
		// 	high = parseInt(_filters['years'][1], 10);

		// 	_markers = $.grep(_markers, function(marker, iterator) {
		// 		var year = parseInt(marker.year, 10);

		// 		var pass = (year >= low && year <= high);

		// 		return pass;
		// 	});
		// }

		// place filtered markers on the map
		_load_map_data();
	}

	function _get_url_parameter(name) {
		var regexS = "[\\?&]" + name + "=([^&#]*)";
		var regex = new RegExp(regexS);
		var results = regex.exec(window.location.search);
		
		if (results === null) {
			return "";
		} else {
			return decodeURIComponent(results[1].replace(/\+/g, " ")).toLowerCase();
		}
	}

	return {
		init: _init
	};
})();

$(document).ready(function() {
	SCB.Map.init();
});