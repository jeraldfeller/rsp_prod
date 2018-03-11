<?php 
require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

function is_admin() {
  if (isset($_SESSION) && isset($_SESSION['user_group_id']) && $_SESSION['user_group_id'] == 2) {
    return true;
  }
  return false;
}

if (!is_admin()) {
  die("Permission denied.");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Order Map (Monthly, by Zip Code)</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
    #info-box {
      background-color: white;
      border: 1px solid black;
      bottom: 30px;
      height: 20px;
      padding: 10px;
      position: absolute;
      left: 30px;
    }
    #range-box {
      background-color: white;
      border: 1px solid black;
      bottom: 20px;
      padding: 10px;
      position: absolute;
      right: 30px;
    }
    #current-month {
      font-weight: bold;
      text-align: center;
    }
    form {
      padding: 0px;
    }
    </style>
    <script src="//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
    <script src="/js/simple-statistics.js"></script>
    <script>
var map;
var zipstats;
var sa_ids = [5, 7, 8, 9, 6, 15, 17, 16];
var quantiles;
var service_area;
var my_zip;
var my_color;
var play = false;
var delay = 1000;
var order_total = 0;

var info_str = "Mouse over colored region to view info";

var color = function(zip) {
    my_color = "white";
    metric = 0;

    if (zipstats.hasOwnProperty(zip)) {
        metric = 1000 * zipstats[zip]["count"] / zipstats[zip]["population"];
    }

    if (metric == 0) {
        my_color = "grey";
    } else if (metric < quantiles[0]) {
        my_color = "purple";
    } else if (metric < quantiles[1]) {
        my_color = "navy";
    } else if (metric < quantiles[2]) {
        my_color = "teal";
    } else if (metric < quantiles[3]) {
        my_color = "orange";
    } else {
        my_color = "red";
    }

    return my_color;
}

var classify = function(zip) {
	my_zip = zip;
    count = 0;
    metric = 0;

    if (zipstats.hasOwnProperty(zip)) {
        count = zipstats[zip]["count"];
        metric = 1000 * zipstats[zip]["count"] / zipstats[zip]["population"];
    }
	
    return count + " Total Installs (" + metric.toFixed(1) + " Installs per 1K Residents)";
}

function initialize() {
  // Create a simple map.
  map = new google.maps.Map(document.getElementById('map-canvas'), {
    zoom: 9,
    center: {lat: 38.8951, lng: -77.0367},
    minZoom: 7,
    maxZoom: 12,
    streetViewControl: false
  });
  
  $.ajax({
    url: '/lib/orders/orders_demographics_json.php5?year=current',
    async: false,
    dataType: 'json',
    success: function (data) {
        zipstats = data;
        order_total = 0;
        zip_metrics = [];
        for (var zip in zipstats) {
            order_total += zipstats[zip]["count"];
            zip_metrics.push(1000 * zipstats[zip]["count"] / zipstats[zip]["population"]);
        }
        console.log("Quantiles:");
        quantiles = [ss.quantile(zip_metrics, 0.20), ss.quantile(zip_metrics, 0.40), ss.quantile(zip_metrics, 0.60), ss.quantile(zip_metrics, 0.80)];
        console.log(quantiles);
	}
  });

  //console.log(sa_ids);
  sa_ids.forEach(function (element, index, array) {
    $.getJSON("/lib/charts/service_areas_json/service_area_" + element + ".json", function(data){
      map.data.addGeoJson(data); 
    }); 
  });

  // Add some style.
  map.data.setStyle(function(feature) {
    zipcode = feature.getProperty('ZCTA5CE10');
    return /** @type {google.maps.Data.StyleOptions} */({
      fillColor: color(zipcode),
      strokeWeight: 0
    });
  });
  
  // Set mouseover event for each feature.
  map.data.addListener('mouseover', function(event) {
    zipcode = event.feature.getProperty('ZCTA5CE10');
    document.getElementById('info-box').textContent =
        "Zip " + zipcode + ": " + classify(zipcode);
  });
  
  // Set mouseout event for each feature.
  map.data.addListener('mouseout', function(event) {
    document.getElementById('info-box').textContent = info_str;
  });
  
}

google.maps.event.addDomListener(window, 'load', initialize);

$(document).ready(function () {
    $("#info-box").html(info_str);
});

    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
    <div id="info-box">?</div>
  </body>
</html>
