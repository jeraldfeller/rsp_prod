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
   /*
   bottom: 30px;
   height: 20px;
   margin-top: 10px;
   */
   padding: 10px;
   margin-right: 40px;
   border-radius: 4px;
   -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   position: absolute;
   max-width: 400px;
}
form {
   padding: 0px;
}

.controls {
    margin-top: 16px;
    border: 1px solid transparent;
    border-radius: 2px 0 0 2px;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    height: 32px;
    outline: none;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

#pac-input {
    background-color: #fff;
    padding: 0 11px 0 13px;
    width: 400px;
    font-family: Roboto;
    font-size: 15px;
    font-weight: 300;
    text-overflow: ellipsis;
}

#pac-input:focus {
    border-color: #4d90fe;
    margin-left: -1px;
    padding-left: 14px;  /* Regular padding-left + 1. */
    width: 401px;
}

.pac-container {
    font-family: Roboto;
}

#type-selector {
    color: #fff;
    background-color: #4d90fe;
    padding: 5px 11px 0px 11px;
}

#type-selector label {
    font-family: Roboto;
    font-size: 13px;
    font-weight: 300;
}

#target {
    width: 345px;
}
</style>
<script data-cfasync="false" src="//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<script data-cfasync="false" language="javascript">
var map;
var zip2sa;
var sa_ids = [];
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

function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function combineColors(bg, color) {
    var a = color.a;

    return {
        r: parseInt((1 - a) * bg.r + a * color.r),
        g: parseInt((1 - a) * bg.g + a * color.g),
        b: parseInt((1 - a) * bg.b + a * color.b)
    };
}

function initialize() {
  var markers = [];
  var map = new google.maps.Map(document.getElementById('map-canvas'), {
    zoom: 8,
    center: {lat: 39.05, lng: -77.0367},
    minZoom: 7,
    maxZoom: 12,
    streetViewControl: false
  });
  var defaultBounds = map.getBounds();
  
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
        sa_window = "5";
        sa_ext_fee = "0";
    }

    info_str = "<b>Service Area: " + classify(sa) + "</b><br>";
    info_str = info_str + "There is a " + sa_window + " day service window in this Service Area.";
    if (sa == '5' || sa == '6') {
        info_str = info_str + "<br><i>NOTE:  90+% of jobs are completed the NEXT BUSINESS DAY in this area if the order is received by 5PM.</i>";
    } else {
        if (sa_ext_fee == "0") {
            info_str = info_str + "<br>Orders in this area are <i>NOT</i> subject to an Extended Area surcharge.";
        } else {
            info_str = info_str + "<br>Orders in this area are subject to a $" + sa_ext_fee + " Extended Area surcharge.";
        }
    }

    this_color = color(sa);
    rgb = hexToRgb(this_color);
    rgb.a = .4;
    light_rgb = combineColors({r: 255,g: 255, b: 255}, rgb);
    light_color = "rgb(" + light_rgb.r + "," + light_rgb.g + "," + light_rgb.b + ")"; 
    $('#info-box').html(info_str);
    //$('#info-msg').html(info_str);
    //$('#info-alert').css('background-color', light_color).css('border-color', this_color);
    
    xOffset=Math.max(document.documentElement.scrollLeft,document.body.scrollLeft);
    yOffset=Math.max(document.documentElement.scrollTop,document.body.scrollTop);

    info_x = event.Qa.clientX + xOffset;
    info_y = event.Qa.clientY + yOffset;

    $("#info-box").css('top', info_y).css('border-color', this_color).css('background-color', light_color).removeClass('hidden');
    if (window.innerWidth - info_x > 300) {
        $("#info-box").css('left', info_x + 10).css('right', '');
    } else { 
      $("#info-box").css('position', 'absolute')
        $("#info-box").css('right', window.innerWidth - info_x - 10).css('left', '');
    }
    
  });

  // Create the search box and link it to the UI element.
  var input = /** @type {HTMLInputElement} */(
      document.getElementById('pac-input'));
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

  var searchBox = new google.maps.places.SearchBox(
    /** @type {HTMLInputElement} */(input));

  // Listen for the event fired when the user selects an item from the
  // pick list. Retrieve the matching places for that item.
  google.maps.event.addListener(searchBox, 'places_changed', function() {
    var places = searchBox.getPlaces();

    if (places.length == 0) {
      return;
    }
    for (var i = 0, marker; marker = markers[i]; i++) {
      marker.setMap(null);
    }

    // For each place, get the icon, place name, and location.
    markers = [];
    var bounds = new google.maps.LatLngBounds();
    for (var i = 0, place; place = places[i]; i++) {
      var image = {
        url: place.icon,
        size: new google.maps.Size(71, 71),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(25, 25)
      };

      // Create a marker for each place.
      var marker = new google.maps.Marker({
        map: map,
        icon: image,
        title: place.name,
        position: place.geometry.location
      });

      markers.push(marker);

      bounds.extend(place.geometry.location);
    }

    map.fitBounds(bounds);
  });

  // Bias the SearchBox results towards places that are within the bounds of the
  // current map's viewport.
  google.maps.event.addListener(map, 'bounds_changed', function() {
    var bounds = map.getBounds();
    searchBox.setBounds(bounds);
  });
  
  // Set mouseout event for each feature.
  map.data.addListener('mouseout', function(event) {
    $('#info-box').html(default_msg);
    //$('#info-msg').html(default_msg);
    //$('#info-alert').css('background-color', '#d9edf7').css('border-color', '#bce8f1');
    $("#info-box").addClass('hidden');
  });
}

$(document).ready(function () {
    $('#info-box').html(default_msg);
    initialize();
});

</script>

<div id="info-alert" class="alert alert-info">
  <span><strong id="info-msg" style="color: black">Please mouse over a colored region in the map to see the service options for that area.</strong></span>
</div>

<input id="pac-input" class="controls" type="text" placeholder="Search Box">
<div id="map-canvas"></div>
<div id="info-box" class="hidden"></div>
