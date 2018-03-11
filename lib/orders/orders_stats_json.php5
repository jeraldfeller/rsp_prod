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
header('Content-Type: application/json');

$start_date = tep_fill_variable('start_date', 'get', '2014-01-01');
$end_date = tep_fill_variable('end_date', 'get', 'today');
$mode = tep_fill_variable('mode', 'get', 'daily');

$start_date = strtotime($start_date);
$end_date = strtotime($end_date);

if ($mode == 'weekly') {
    $query = $database->query("SELECT DATE_FORMAT(DATE_SUB(FROM_UNIXTIME(o.date_added), INTERVAL 5 HOUR), '%Y-%U') AS odate, SUM(o.order_total) AS ototal FROM orders o WHERE (o.date_added-(5*60*60)) >= {$start_date} AND (o.date_added-(5*60*60)) <= {$end_date} GROUP BY odate");
} elseif ($mode == 'daily') {
    $query = $database->query("SELECT DATE_FORMAT(DATE_SUB(FROM_UNIXTIME(o.date_added), INTERVAL 5 HOUR), '%Y-%m-%d') AS odate, SUM(o.order_total) AS ototal FROM orders o WHERE (o.date_added-(5*60*60)) >= {$start_date} AND (o.date_added-(5*60*60)) <= {$end_date} GROUP BY odate");
} elseif ($mode == 'monthly') {
    $query = $database->query("SELECT DATE_FORMAT(DATE_SUB(FROM_UNIXTIME(o.date_added), INTERVAL 5 HOUR), '%y-%m') AS odate, SUM(o.order_total) AS ototal FROM orders o WHERE (o.date_added-(5*60*60)) >= {$start_date} AND (o.date_added-(5*60*60)) <= {$end_date} GROUP BY odate");
}


echo "[\n";
$i=0;
while ($result = $database->fetch_array($query)) {
    if ($i) {
        echo ",\n";
    }
    $i++;
    $odate = $result['odate'];
    $ototal = $result['ototal'];

    echo "  {\"date\": \"{$odate}\", \"total\": {$ototal}}";
}
echo "\n]";
?>
