<?php
/*
 * Part of Realty Sign Post (c) 2014 Realty Sign Post.
 * Description: JSON interface for open orders
 *
 * Author: John Pelster <john.pelster@gmail.com>
 */

require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

if(substr_count('realtysignpost',$_SERVER['HTTP_HOST'])) {
    error_reporting(0);
    ini_set('error_reporting', 0);
    ini_set('display_errors', 'Off');
}

// These check permissions, but it's intended only in the context of this autocomplete widget.
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

if (array_key_exists("min", $_REQUEST)) {
    $min = (int) $_REQUEST["min"];
} else {
    $min = 0;
}

if (array_key_exists("max", $_REQUEST)) {
    $max = (int) $_REQUEST["max"];
} else {
    $max = 0;
}

if (array_key_exists("since", $_REQUEST)) {
    $since = strtotime($_REQUEST["since"]);
} else {
    $since = 0;
}

$query = $database->query("SELECT equipment_item_id FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 1 and ei.equipment_status_id = 2");
$posts = array();
foreach($database->fetch_array($query) as $result){
    $id = $result['equipment_item_id'];
    $p = new Post($id);
    if ($p->getInstalledTimestamp() > $since) {
        $posts[] = $p;
    }
}

echo "[\n";
$orders = array();
$addressStrings = array();
$i = 0;
foreach($posts as &$p) {
    $days = $p->getInstalledDays();
    if (empty($days)) continue;

    if ($days >= $min && ($days <= $max || $max == 0)) {
        $address = new Address($p->getInstallAddressId());
        $addressString = json_encode($address->toString());
        $equipmentId = $p->getEquipmentId();
        $equipmentTypeId = $p->getEquipmentTypeId();
        $equipmentItemId = $p->getEquipmentItemId();
        $name = json_encode($p->getName());
        $installOrderId = $p->getInstallOrderId();
        $removalOrderId = $p->getRemovalOrderId();

        if (empty($removalOrderId)) {
            $removalOrderId = 0;
        }

        if ($i > 0) {
            echo ",\n";
        }
        $i++;
        echo "  {\"install_order_id\": {$installOrderId}, \"removal_order_id\": {$removalOrderId}, \"address_string\": {$addressString}, \"equipment_type_id\": {$equipmentTypeId}, \"equipment_id\": {$equipmentId}, \"equipment_item_id\": {$equipmentItemId}, \"name\": {$name}}";
    }
}

echo "\n]";

foreach ($orders as $i => $o) {
    if ($i > 0) {
        echo ",\n";
    }
}
?>
