<?php
/*
 * Part of Realty Sign Post (c) 2014 Realty Sign Post.
 * Description:i Order counts by zipcode (based on scheduled time)
 *
 * Author: John Pelster <john.pelster@gmail.com>
 */

require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

if(substr_count('realtysignpost',$_SERVER['HTTP_HOST'])) {
    error_reporting(0);
    ini_set('error_reporting', 0);
    ini_set('display_errors', 'Off');
}

// REQUIRE: Admin
function is_admin() {
  if (isset($_SESSION) && isset($_SESSION['user_group_id']) && $_SESSION['user_group_id'] == 2) {
    return true;
  }
  return false;
}

if (!is_admin()) {
    die;
}

header('Content-Type: application/json');

if (array_key_exists("month", $_REQUEST)) {
    $start_month = (int) $_REQUEST["month"];
} else {
    $start_month = (int) date('n');
}

if (array_key_exists("year", $_REQUEST)) {
    $start_year = (int) $_REQUEST["year"];
} else {
    $start_year = (int) date('Y');
}

$end_month = $start_month + 1;
$end_year = $start_year;

if ($end_month > 12) {
    $end_month = 1;
    $end_year++;
}

$start_ts = strtotime("{$start_year}-{$start_month}-01 00:00:00");
$end_ts = strtotime("{$end_year}-{$end_month}-01 00:00:00");

$sql = "SELECT SUBSTRING(a.zip4, 1, 5) AS zip, COUNT(*) AS count FROM " . TABLE_ORDERS . " o JOIN " . TABLE_ADDRESSES . " a ON (a.address_id = o.address_id) WHERE o.date_schedualed >= '{$start_ts}' AND o.date_schedualed < '{$end_ts}' GROUP BY SUBSTRING(a.zip4, 1, 5)";
$query = $database->query($sql);

echo "{\n";
$i=0;
foreach($database->fetch_array($query) as $result){
    $zip = $result['zip'];
    $count = $result['count'];
    if ($i>0) {
        echo ", \n";
    }
    $i++;
    echo " \"zip{$zip}\": {$count}";
}
echo "\n}";
?>
