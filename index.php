<!DOCTYPE html>
<html lang="en">
<head>
	
	<title>The Odin Network Tracker</title>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous">
    </script>

    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
        integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
        crossorigin="">
    </script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster-src.js"></script>
    <script src="https://unpkg.com/leaflet.featuregroup.subgroup"></script>
    <script src="js/leaflet-realtime.min.js"></script>

	<style>
		html, body {
			height: 100%;
			margin: 0;
		}
		.leaflet-container {
			height: 400px;
			width: 600px;
			max-width: 100%;
			max-height: 100%;
		}
        #map {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
	</style>
</head>

<body>
<?php

// Create or open (if exists) the database
$db = new SQLite3('tracks.sqlite');

// Create tables
$db->exec("CREATE TABLE IF NOT EXISTS locations (date TEXT, id TEXT, latitude TEXT, longitude TEXT, altitude TEXT, battery TEXT)");

// The Query
$stmt = $db->querySingle('SELECT date, id, latitude, longitude, battery FROM locations WHERE latitude IS NOT NULL AND date >= DateTime(\'now\',\'-5 minutes\') ORDER BY date DESC LIMIT 1', true);


?>
<div id="map" style="width: 100%; height: 100%;"></div>

<script>
    var dog;

    function update_position() {
        $.getJSON('https://map.theodin.network/downlink/', function(data) {
            var lat = data["location"]["latitude"];
            var log = data["location"]["longitude"];
            var batt = data["battery"];
            var datt = data["date"];
            if (!dog) {
                dog = L.marker([lat,log],{icon: dogBlackIcon}).bindPopup("I am Milo").addTo(map);
            }
            dog.setLatLng([lat,log]).update();
            setTimeout(update_position, 5000);
        });
    }
    
    update_position();

  	var neighbors = L.layerGroup();
  	var radius = L.layerGroup();

	var mBeaverDam = L.marker([46.341850, -92.755370]).bindPopup('The Beaver Dam').addTo(neighbors);
	var mMarkLydia = L.marker([46.343635, -92.753083]).bindPopup('Mark & Lydia\'s Cabin').addTo(neighbors);
	var mMikeSandy = L.marker([46.340010, -92.753600]).bindPopup('Mike & Sandy\'s House').addTo(neighbors);

	var mbAttr = 'The Odin Network GPS Tracker';
	var mbUrl = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';

	var outdoors = L.tileLayer(mbUrl, {id: 'mapbox/outdoors-v11', tileSize: 512, zoomOffset: -1, attribution: mbAttr,maxZoom:30,});
	var satellite = L.tileLayer(mbUrl, {id: 'mapbox/satellite-streets-v11', tileSize: 512, zoomOffset: -1, attribution: mbAttr,maxZoom:30,});
	var grayscale = L.tileLayer(mbUrl, {id: 'mapbox/light-v9', tileSize: 512, zoomOffset: -1, attribution: mbAttr,maxZoom:30,});
	var streets = L.tileLayer(mbUrl, {id: 'mapbox/streets-v11', tileSize: 512, zoomOffset: -1, attribution: mbAttr,maxZoom:30,});

	var map = L.map('map', {
		center: [<?php echo $stmt['latitude']; ?>, <?php echo $stmt['longitude']; ?>],
		zoom: 19,
		layers: [outdoors, neighbors, radius]
	});

	var baseLayers = {
        'Outdoors': outdoors,
        'Satellite': satellite,
		'Streets': streets,
		'Grayscale': grayscale
	};

	var overlays = {
		'Neighbors': neighbors,
        'Radius': radius
	};

    var dogBlackIcon = L.icon({
        iconUrl:      "images/dog-black.svg",
        iconSize:     [30, 30], // size of the icon
    });

    var dogRedkIcon = L.icon({
        iconUrl:      "images/dog-red.svg",
        iconSize:     [30, 30], // size of the icon
    });

    var dogYelloIcon = L.icon({
        iconUrl:      "images/dog-yellow.svg",
        iconSize:     [30, 30], // size of the icon
    });

    var dogGreenIcon = L.icon({
        iconUrl:      "images/dog-green.svg",
        iconSize:     [30, 30], // size of the icon
    });

    var homeIcon = L.icon({
        iconUrl:      "images/home.svg",
        iconSize:     [30, 30], // size of the icon
    });

    var mHome = L.marker([46.339887, -92.752064], {
        icon: homeIcon
    }).addTo(map).bindPopup('<b>The Cabin!</b><br />84275 Jackpine Ln.<br />Sturgeon Lake, MN 55783');

    /*
    var circle = L.circle([46.339384, -92.752046], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.10,
        radius:3 
    }).addTo(radius);
    */

	var layerControl = L.control.layers(baseLayers, overlays).addTo(map);

</script>
</body>
</html>

