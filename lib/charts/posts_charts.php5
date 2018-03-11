<?php
require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

function is_admin() {
  if (isset($_SESSION) && isset($_SESSION['user_group_id']) && $_SESSION['user_group_id'] == 2) {
    return true;
  }
  return false;
}

if (!is_admin()) {
    echo "Access Denied";
    die;
}

// Get a list of all the posts that are installed
$query = $database->query("SELECT equipment_item_id FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 1 and ei.equipment_status_id = 2");
$posts = array();
while ($result = $database->fetch_array($query)) {
    $id = $result['equipment_item_id'];
    $p = new Post($id);
    //if ($p->getInstalledTimestamp() > $average_since_ts) {
        $posts[] = $p;
    //}
}

$day_array = array();
foreach($posts as &$p) {
    $days = $p->getInstalledDays();
    if (empty($days)) continue;

    $days_array[] = $days;
}

$query = $database->query("SELECT od.number_of_posts, CEIL((o.date_completed-o2.date_completed)/(24*60*60)) AS days FROM " . TABLE_ORDERS . " o JOIN " . TABLE_ORDERS . " o2 ON (o.address_id = o2.address_id) JOIN " . TABLE_ORDERS_DESCRIPTION . " od ON (o2.order_id = od.order_id) WHERE o.order_type_id = 3 AND o.order_status_id = 3 AND o2.order_type_id = 1 AND o2.order_status_id = 3 AND o.date_completed > 0 AND o2.date_completed > 0");

$days_array2 = array();

while ($result = $database->fetch_array($query)) {
    $num_posts = $result['number_of_posts'];
    for ($i=0; $i<$num_posts; $i++) {
        $days_array2[] = $result['days'];
    }
}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<style>

body {
  font: 10px sans-serif;
}

.bar rect {
  fill: steelblue;
  shape-rendering: crispEdges;
}

.chart1 .bar rect {
  fill: forestgreen;
  shape-rendering: crispEdges;
}

.bar text {
  fill: #fff;
}

.axis path, .axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.x.axis path {
  display: none;
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 1.5px;
}

</style>
<body>
<h1>Days in the Field:  Currently Installed Posts</h1>
<div class="chart1"></div>

<h1>Days in the Field:  On Post Removal</h1>
<div class="chart2"></div>

<script src="http://d3js.org/d3.v3.min.js"></script>
<script>
function postsChart(values, breaks, selector) {
    max = breaks[breaks.length - 1];
	
	// A formatter for counts.
	formatCount = d3.format(",.0f");
	
	margin = {top: 10, right: 30, bottom: 30, left: 30},
	    width = 800 - margin.left - margin.right,
	    height = 300 - margin.top - margin.bottom;
	
	x = d3.scale.linear()
	    .domain([0, max])
	    .range([0, width]);
	
	// Generate a histogram using twenty uniformly-spaced bins.
	data = d3.layout.histogram()
	    .bins(breaks)
	    (values);
	
	y = d3.scale.linear()
	    .domain([0, d3.max(data, function(d) { return d.y; })])
	    .range([height, 0]);
	
	xAxis = d3.svg.axis()
	    .scale(x)
	    .tickValues(breaks)
	    .orient("bottom");
	
	svg = d3.select(selector).append("svg")
	    .attr("width", width + margin.left + margin.right)
	    .attr("height", height + margin.top + margin.bottom)
	  .append("g")
	    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
	bar = svg.selectAll(".bar")
	    .data(data)
	    .enter().append("g")
	    .attr("class", "bar")
	    .attr("transform", function(d) { return "translate(" + x(d.x) + "," + y(d.y) + ")"; });
	
	bar.append("rect")
	    .attr("x", 1)
	    .attr("width", x(data[0].dx) - 1)
	    .attr("height", function(d) { return height - y(d.y); });
	
	bar.append("text")
	    .attr("dy", ".75em")
	    .attr("y", 6)
	    .attr("x", x(data[0].dx) / 2)
	    .attr("text-anchor", "middle")
	    .text(function(d) { return formatCount(d.y); });
	
	svg.append("g")
	    .attr("class", "x axis")
	    .attr("transform", "translate(0," + height + ")")
        .call(xAxis);
}

var values = <?php echo json_encode($days_array); ?>;
var values2 = <?php echo json_encode($days_array2); ?>;
var breaks = [0,60,120,180,240,300,360,420,480,540,600,660,720,780];
var breaks2 = [0,30,60,90,120,150,180,210,240,270,300,330,360];

postsChart(values, breaks, ".chart1");
postsChart(values2, breaks2, ".chart2");
</script>


