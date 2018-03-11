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

$start_date = tep_fill_variable('start_date', 'get', '2014-01-01');
$end_date = tep_fill_variable('end_date', 'get', date('Y-m-d'));
$mode = tep_fill_variable('mode', 'get', 'weekly');

if ($mode == 'weekly') {
    $ticks = ".ticks(d3.time.day, 7)";
} elseif ($mode == "daily") {
    $ticks = ".ticks(d3.time.day, 1)";
} elseif ($mode == "monthly") {
    $ticks = ".ticks(d3.time.months, 1)";
}

?><!DOCTYPE html>
<head>
<meta charset="utf-8">
<style>
body {
  font: 10px sans-serif;
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
  stroke: forestgreen;
  stroke-width: 1.5px;
}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

<script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.4.6/d3.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.6.0/moment.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

<script>
$(document).ready(function () {
    $("#start_date").datepicker();
    $("#end_date").datepicker();

	function ordersChart(json_url, selector, mode) {
		margin = {top: 20, right: 20, bottom: 30, left: 50},
		    width = 800 - margin.left - margin.right,
		    height = 300 - margin.top - margin.bottom;
		
        x = d3.time.scale()
            .range([0, width]);
        x<?=$ticks?>;
		
		y = d3.scale.linear()
		    .range([height, 0]);
		
		xAxis = d3.svg.axis()
		    .scale(x)
		    .orient("bottom");
		
		yAxis = d3.svg.axis()
		    .scale(y)
		    .orient("left");
		
		line = d3.svg.line()
		    .x(function(d) { return x(d.date); })
		    .y(function(d) { return y(d.total); });
		
		svg = d3.select(selector).append("svg")
		    .attr("width", width + margin.left + margin.right)
		    .attr("height", height + margin.top + margin.bottom)
		  .append("g")
		    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
		
	    d3.json(json_url + '&mode=' + mode, function(error, data) {
	      if (error) return console.warn(error);
          data.forEach(function(d) {
		    d.date = moment(d.date, "GGGG-MM-DD", "GGGG-WW", "GG-MM");
		    d.total = +d.total;
		  });
		
		  x.domain(d3.extent(data, function(d) { return d.date; }));
		  y.domain(d3.extent(data, function(d) { return d.total; }));
		
		  svg.append("g")
		      .attr("class", "x axis")
		      .attr("transform", "translate(0," + height + ")")
		      .call(xAxis);
		
		  svg.append("g")
		      .attr("class", "y axis")
		      .call(yAxis)
		    .append("text")
		      .attr("transform", "rotate(-90)")
		      .attr("y", 6)
		      .attr("dy", ".71em")
		      .style("text-anchor", "end")
		      .text("Total ($)");
		
		  svg.append("path")
		      .datum(data)
		      .attr("class", "line")
		      .attr("d", line);

          //svg.selectAll(".x text")  // select all the text elements for the xaxis
          //    .attr("transform", function(d) {
          //        return "translate(" + this.getBBox().height*-2 + "," + this.getBBox().height + ")rotate(-45)";
          //  });
		});
	}
    ordersChart("/lib/orders/orders_stats_json.php5?start_date=<?=$start_date?>&end_date=<?=$end_date?>", ".chart3", "<?=$mode?>");
});
</script>

</head>
<body>

<div class="container">
<h1><?php echo ucfirst($mode);?> Order Value</h1>
<div style="height: 300px" class="chart3"></div>
<br>
<br>
<div>
    <form method="get">
    <label for="start_date">Start Date:</label> <input type="text" id="start_date" name="start_date" value="<?php echo $start_date;?>"> to 
    <label for="end_date">End Date:</label> <input type="text" id="end_date" name="end_date" value="<?php echo $end_date;?>">
    <label for="mode">Granularity:</label>
        <select name="mode">
            <option value="daily"<?php echo ($mode == "daily")?" selected":"";?>>Daily</option>
            <option value="weekly"<?php echo ($mode == "weekly")?" selected":"";?>>Weekly</option>
            <option value="monthly"<?php echo ($mode == "monthly")?" selected":"";?>>Monthly</option>
        </select>
    <input type="submit" value="Go">
    </form>
</div>

</div>
</body>
</html>
