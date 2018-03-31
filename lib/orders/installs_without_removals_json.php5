<?php
/*
 * Part of Realty Sign Post (c) 2014 Realty Sign Post.
 * Description: JSON interface for installs without removals report
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

// This matches AOM's and also accounts payable.
function is_aom() {
  if (isset($_SESSION) && isset($_SESSION['user_group_id']) && $_SESSION['user_group_id'] >= 4) {
    return true;
  }
  return false;
}


if (!is_admin()) {
    die;
}

header('Content-Type: application/json');

if (array_key_exists("months", $_REQUEST)) {
    $months = (int) $_REQUEST["months"];
} else {
    $months = 18;
}

$ts = mktime() - (60*60*24*30*$months);
$query = $database->query("SELECT address_id, order_type_id, order_id, date_schedualed FROM " . TABLE_ORDERS . " WHERE date_added >= $ts AND order_status_id != 4"); 
$addresses = array();

foreach($database->fetch_array($query) as $result){

    $aID = $result['address_id'];
    $oID = $result['order_id'];
    $oType = $result['order_type_id'];
    $scheduledStart = $result['date_schedualed'];

    if (!array_key_exists("{$aID}", $addresses)) {
        $address = new Address($aID);
    } else {
        $address = $addresses["{$aID}"];
    }

    if ($oType == 1) {
        $address->setInstallOrderId($oID);
        $address->setInstallStart($scheduledStart);
    } elseif ($oType == 3) {
        $address->setRemovalOrderId($oID);
    }

    $addresses["{$aID}"] = $address;
}

if (array_key_exists("fix", $_REQUEST)) {
    if (is_array($_REQUEST["fix"])) {
        $fix = $_REQUEST["fix"];
    } else {
        $fix = array($_REQUEST["fix"]);
    }
} else {
    $fix = array();
}

$addressesWithoutRemovals = array();
foreach ($addresses as &$address) {
    if ($address->isInstallWithoutRemoval()) {
        if (in_array($address->getAddressId(), $fix)) {
            $address->createRemoval();
        } else {
            $addressesWithoutRemovals[] = $address;
        }
    }
}

// Echo the JSON object
echo "[\n";
$i=0;
foreach ($addressesWithoutRemovals as &$address) {
    if ($i > 0) {
        echo ",\n";
    }
    $i++;
    echo $address->toJSON();
}
echo "\n]";
?>
