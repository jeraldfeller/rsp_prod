<?php
if (array_key_exists("show", $_POST)) {
    $show = $_POST["show"];
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Data Layer: Event Handling</title>
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
    #zip-display {
      background-color: white;
      border: 1px solid black;
      bottom: 20px;
      height: 25px;
      padding: 10px;
      position: absolute;
      right: 30px;
    }
    form {
      padding: 0px;
    }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
    <script src="http://d3js.org/topojson.v1.min.js"></script>
    <script>
var map;
var zip2sa;
var sa_ids = [];
var service_area;
var my_zip;
var my_color;

var color = function(zip) {
    my_color = "white";
	my_zip = zip;
	
    $.each(zip2sa, function (index, data) {
        if (data.high >= my_zip && data.low <= my_zip) {
           my_color = data.color;
        }
    });

    return my_color;
}

var classify = function(zip) {
    service_area = "No Service";
	my_zip = zip;
	
    $.each(zip2sa, function (index, data) {
        if (data.high >= my_zip && data.low <= my_zip) {
           service_area = data.area;
        }
    });

    return service_area;
}

function initialize() {
  // Create a simple map.
  map = new google.maps.Map(document.getElementById('map-canvas'), {
    zoom: 8,
    center: {lat: 38.8951, lng: -77.0367}
  });
  
  $.ajax({
    url: '/lib/addresses/zip_to_service_area_json.php5',
    async: false,
    dataType: 'json',
    success: function (data) {
        zip2sa = data;
        $.each(zip2sa, function (index, val) {
            if ($.inArray(val.service_area_id, sa_ids) === -1) {
                sa_ids.push(val.service_area_id);
            }
        });
	}
  });

<?php
if ($show != "region") {
?>
  console.log(sa_ids);
  sa_ids.forEach(function (element, index, array) {
    $.getJSON("/lib/charts/service_areas_json/service_area_" + element + ".json", function(data){
      map.data.addGeoJson(data); 
    }); 
  });
<?php
} else {
?>
  $.getJSON("/lib/charts/regional_json/region.json", function(data){
    map.data.addGeoJson(data); 
  }); 
<?php
}
?>

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
    document.getElementById('info-box').textContent = "?";
  });
  
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
    <div id="info-box">?</div>
    <div id="zip-display">
        <form method="post">
            Display Zip Codes for:
            <select name="show">
                <option value="service_areas">Service Areas Only</option>
                <option value="region"<?php if ($show=="region") echo " selected";?>>Entire Region (Slow)</option>
            </select>
            <input type="submit" name="submit" value="Update">
        </form>
    </div>
  </body>
</html>
