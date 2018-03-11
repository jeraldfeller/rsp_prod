<style>
#map-canvas {
   height: 600px;
   border: 1px solid black;
}
#map-canvas img {
   max-width: none;
   max-height: none;
}
#info-box {
   background-color: white;
   border: 1px solid black;
   bottom: 30px;
   height: 20px;
   padding: 10px;
   margin-top: 10px;
}
form {
   padding: 0px;
}
</style>
<script data-cfasync="false" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script data-cfasync="false" language="javascript">
var map;
var zip2sa;
var sa_ids;
var service_area;
var my_zip;
var my_color;
var default_msg = "Please mouse over a colored region in the map to see the service options for that area.";

var color = function(sa) {
    my_color = "white";
	my_sa = sa;
	
    $.each(zip2sa, function (index, data) {
        if (data.service_area_id == my_sa) {
           my_color = data.color;
        }
    });

    return my_color;
}

var classify = function(sa) {
    service_area = "No Service";
	my_sa = sa;
	
    $.each(zip2sa, function (index, data) {
        if (data.service_area_id == my_sa) {
           service_area = data.area;
        }
    });

    return service_area;
}

function initialize() {
  // Create a simple map.
  map = new google.maps.Map(document.getElementById('map-canvas'), {
    zoom: 9,
    center: {lat: 39.1, lng: -77.0367}
  });
  
  $.ajax({
    url: '/lib/addresses/zip_to_service_area_json.php5',
    async: false,
    dataType: 'json',
    success: function (data) {
        zip2sa = data;
	}
  });

  sa_ids = [5,6];
  console.log(sa_ids);
  sa_ids.forEach(function (element, index, array) {
    $.getJSON("/lib/charts/service_areas_json/min/service_area_" + element + "_min.json?v=2014.07.20", function(data){
      map.data.addGeoJson(data); 
    }); 
  });

  // Add some style.
  map.data.setStyle(function(feature) {
    sa = feature.getProperty('SA');
    return /** @type {google.maps.Data.StyleOptions} */({
      fillColor: color(sa),
      strokeWeight: 0
    });
  });
  
  // Set mouseover event for each feature.
  map.data.addListener('mouseover', function(event) {
    sa = event.feature.getProperty('SA');

    re = /(\d+)-(\d+)$/;
    matches = re.exec(classify(sa));
    if (matches.length == 3) {
        sa_window = matches[1];
        sa_ext_fee = matches[2];
    } else {
        sa_window = 5;
        sa_ext_fee = 0;
    }

    info_str = "Service Area: " + classify(sa) + ".  There is a " + sa_window + " day service window in this area.  <i>NOTE:  90+% of jobs are completed the NEXT BUSINESS DAY in this area if the order is received by 5PM.</i>";
    $('#info-box').html(info_str);

  });
  
  // Set mouseout event for each feature.
  map.data.addListener('mouseout', function(event) {
    $('#info-box').html(default_msg);
  });
  
}

$(document).ready(function () {
    $('#info-box').html(default_msg);
    initialize();
});

</script>


<div id="map-canvas"></div>
<div id="info-box"></div>
