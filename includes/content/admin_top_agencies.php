<?php
if (!isset($_SESSION) || !isset($_SESSION['user_group_id']) || $_SESSION['user_group_id'] != 2) {
  exit(json_encode(array('error' => true, 'msg' => 'Access denied.')));
}

$t = new DateTime();
$t->sub(new DateInterval('P1Y'));
$date_start = $date_end = $t->getTimestamp();
if (!empty($_REQUEST['date_start']) && strtotime($_REQUEST['date_start'])) {
  $date_start = strtotime($_REQUEST['date_start']);
}
$date_end = time();
if (!empty($_REQUEST['date_end']) && strtotime($_REQUEST['date_end'])) {
  $date_end = strtotime($_REQUEST['date_end']);
}

$orders = 1;
$total = 1;
if (!empty($_REQUEST['orders']) && (int) $_REQUEST['orders']) {
  $orders = $_REQUEST['orders'];
}
if (!empty($_REQUEST['total']) && (int) $_REQUEST['total']) {
  $total = $_REQUEST['total'];
}

$only_agents = false;
$only_agencies = false;
$all_groups = true;
if (!empty($_REQUEST['user_type'])) {
  if($_REQUEST['user_type'] == "agents") {
    $only_agents = true;
    $all_groups = false;
  } elseif($_REQUEST['user_type'] == "agencies") { 
    $only_agencies = true;
    $all_groups = false;
  }
}
?>
<script src="/js/cjs.min.js"></script>
<script>window.data = [];</script>

<div class="row-fluid">
  <div class="span2">
    <div class="well well-small" style="text-align: center;">
      <form action="/admin_top_agencies.php" method="POST">
        <label for="date_start"><strong>From:</strong></label>
        <input class="datepicker input-block-level" placeholder="From" type="text" name="date_start" pattern="[0-9]{1,2}[-/]+[0-9]{1,2}[-/]+[0-9]{2,4}" required="required" value="<?= date('n/d/Y', $date_start) ?>" style="margin-bottom: 1em; text-align: center;">
        <label for="date_end"><strong>To:</strong></label>
        <input class="datepicker input-block-level" placeholder="To" type="text" name="date_end" pattern="[0-9]{1,2}[-/]+[0-9]{1,2}[-/]+[0-9]{2,4}" required="required" value="<?= date('n/d/Y', $date_end) ?>" style="margin-bottom: 1em; text-align: center;">
        <label for="orders"><strong>Minimum # of Orders:</strong></label>
        <input class="input-block-level" placeholder="Minimum # of orders" type="text" name="orders" pattern="[0-9]{1,4}" value="<?=$orders?>" style="margin-bottom: 1em; text-align: center;">
        <label for="orders"><strong>Minimum $ Total:</strong></label>
        <input class="input-block-level" placeholder="Minimum $ Total" type="text" name="total" pattern="[0-9]{1,4}" value="<?=$total?>" style="margin-bottom: 1em; text-align: center;">
        <label for="orders"><strong>Agencies or Agents?</strong></label>
        <select name="user_type" class="input-block-level">
          <optgroup label="Agencies or Agents?">
            <option value="all" <?php echo $all_groups ? "selected" : ""; ?>>Both</option>
            <option value="agencies" <?php echo $only_agencies ? "selected" : ""; ?>>Agencies Only</option>
            <option value="agents" <?php echo $only_agents ? "selected" : ""; ?>>Agents Only</option>
          </optgroup>
        </select>
        <div style="margin-bottom: 3em;">
          <input type="checkbox" name="show_chart" value="1" <?php if(!empty($_REQUEST['show_chart'])) { echo "checked='checked'"; }?>> Show Chart?
        </div>
        <button type="submit" class="btn btn-block" style="margin-top: -0.75em;"><i class="icon-search">&nbsp;</i> Go</button>
      </form>
    </div>
  </div>
  <div class="span10">
    <?php
    if(!empty($_REQUEST['show_chart'])) {
      ?>
      <div id="chartContainer" style="width: 100%; height: 400px;"></div>
      <div class="well well-small" style="text-align: center;">Select a portion of the chart to zoom in for more details</div>  
      <?php
    }
    ?>
    
    <table class="table table-striped datatable">
      <thead>
        <tr><th>Agency/Agent<th>Orders<th>Total Amount ($), All Orders
      </thead>
      <tbody>
        <?php
          $s = "
            SELECT 
              o.order_total, o.billing_method_id,o.user_id,
              a.name AS agency_name,a.agency_id,a.office,
              ud.firstname, ud.lastname
            FROM orders o
            LEFT OUTER JOIN users u ON o.user_id = u.user_id
            LEFT OUTER JOIN users_description ud ON o.user_id = ud.user_id
            LEFT OUTER JOIN agencys a ON u.agency_id = a.agency_id
            WHERE 
              o.date_completed >= {$date_start} AND o.date_completed <= {$date_end} AND o.order_total > 0
          ";
          $min_amount = $min_count = 1000000;
          $max_amount = $max_count = 0;
          
          if($only_agencies) {
            $s .= " AND o.billing_method_id <= 2";
          } elseif($only_agents) {
            $s .= " AND o.billing_method_id = 3";
          }
              
          $q = $database->query($s);
          $result = array('agencies'=>array(),'agents'=>array());
          foreach($database->fetch_array($q) as $r){
            
            //echo "<pre>". print_r($r,1) ."</pre><br>";
            if($r['billing_method_id'] >=3) {
              if(empty($result['agents'][$r['user_id']])) {
                $result['agents'][$r['user_id']] = array(
                    'name' => $r['firstname'] . " " . $r['lastname'],
                    'total' => 0,
                    'count' => 0
                );
              }
              $result['agents'][$r['user_id']]['total'] += $r['order_total'];
              $result['agents'][$r['user_id']]['count']++;
            } else {
              if(empty($result['agencies'][$r['agency_id']])) {
                $result['agencies'][$r['agency_id']] = array(
                    'name' => $r['agency_name'] . " " . $r['office'],
                    'total' => 0,
                    'count' => 0
                );
              }
              $result['agencies'][$r['agency_id']]['total'] += $r['order_total'];
              $result['agencies'][$r['agency_id']]['count']++;
            }
          }
          
          if(!empty($result['agencies'])) {
            foreach($result['agencies'] as $agency_id => $v) {
              if($v['count'] < $orders || $v['total'] < $total) {
                continue;
              }
              echo "<tr><td>{$v['name']}<td>{$v['count']}<td>{$v['total']}";
              echo "<script>window.data.push({ x: {$v['count']}, y: {$v['total']}, name: '{$v['name']}'});</script>";
            }
          }
          if(!empty($result['agents'])) {
            foreach($result['agents'] as $agency_id => $v) {
              if($v['count'] < $orders || $v['total'] < $total) {
                continue;
              }
              echo "<tr><td>{$v['name']}<td>{$v['count']}<td>{$v['total']}";
              echo "<script>window.data.push({ x: {$v['count']}, y: {$v['total']}, name: '{$v['name']}'});</script>";
            }
          }
        ?>
      </tbody>
      <tfoot>
        <tr><th>Agency/Agent<th>Orders<th>Total Amount ($), All Orders
      </tfoot>
    </table>
  </div>
</div>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
var jq = jq || [];
jq.push(function(){
  
  $('table.datatable th.header').hover(
    function() {
      $(this).css('cursor','pointer');
    },
    function() {
      $(this).css('cursor','auto');
    }
  );
  $('.datepicker').datepicker();
  window.otable = $('.datatable').dataTable({
    "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span5'i><'span7'p>>",
    "sPaginationType": "bootstrap",
    "bStateSave": true,
    "sScrollX": "100%",
    "oLanguage": {
      "sEmptyTable": "<span class='label label-important'>No top agencies/agents from <?= date('n/d/Y', $date_start) ?> to <?= date('n/d/Y', $date_end) ?>.</span>"
    }
  });
  <?php 
    if(!empty($_REQUEST['show_chart'])) {
      ?>
      window.axisX = {
        title:"# Orders"
      };
      window.axisY = {
        title: "$ Total of All Orders",
        valueFormatString:"0 USD",
        titleFontSize: 14
      }

      window.chart = new CanvasJS.Chart("chartContainer",
      {
        theme: "theme2",
        backgroundColor: "transparent",
        zoomEnabled: true,
        axisX: window.axisX,
        axisY: window.axisY,
        data: [{
          markerSize: 16,
          type: "scatter",  
          toolTipContent: "<span style='\"'color: {color};'\"'><strong>{name}</strong></span> <br/> <strong>Order Total $</strong>{y}<br/><strong># Orders</strong> {x} ",
          dataPoints: window.data
        }]
      });            
      $('#chartContainer').fadeIn('slow',function() { 
        chart.render();
      });
      <?php
    }
  ?>
  
});
</script>