<?php
/*
 * Part of Realty Sign Post (c) 2014 Realty Sign Post.
 * Description: JSON interface to fix equipment items stuck in installed status
 * 
 * Author: John Pelster <john.pelster@gmail.com>
 */
$ip_whitelist = array(
    '50.23.231.176',
    '173.245.50.98',
    '68.186.3.154',
    '127.0.0.1',
    '103.241.1.121',
    '103.241.1.72'
);

error_log("Free Inventory request from {$_SERVER['REMOTE_ADDR']}");
if (!in_array($_SERVER['REMOTE_ADDR'], $ip_whitelist)) {
    error_log("Access denied from {$_SERVER['REMOTE_ADDR']}");
    die("Access denied");
}

require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

if(substr_count('realtysignpost',$_SERVER['HTTP_HOST'])) {
      error_reporting(0);
        ini_set('error_reporting', 0);
        ini_set('display_errors', 'Off');
}

header('Content-Type: application/json');

if (array_key_exists("address_id", $_REQUEST)) {
    $address_id = (int) $_REQUEST["address_id"];
} else {
    echo "{}";
    exit;
}


$sql1 = "UPDATE " . TABLE_EQUIPMENT_ITEMS . " SET equipment_status_id = 0 WHERE equipment_item_id IN (SELECT equipment_item_id FROM " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " WHERE address_id = '" . $address_id . "')";
$sql2 = "UPDATE " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " SET equipment_status_id = 0 WHERE address_id = '" . $address_id . "'";

$query = $database->query($sql1);
$num_rows = $database->affected_rows($query);
$query = $database->query($sql2);
$num_rows += $database->affected_rows($query);

echo "{\"count\": {$num_rows}}";
?>
