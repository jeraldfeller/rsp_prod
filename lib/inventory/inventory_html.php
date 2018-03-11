<?php
/*
 * Part of Realty Sign Post (c) 2014 Realty Sign Post.
 * Description: JSON interface for inventory watchers
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


if (!(is_admin() || is_aom())) {
    die;
}

// First clear the inventory cache.  We never want to use cached data for this report.
$database->query("TRUNCATE TABLE " . TABLE_INVENTORY_CACHE);

// Now pull the inventory JSON from the API
$url = "http://" . $_SERVER['SERVER_NAME'] . "/lib/inventory/inventory_json.php5";
$contents = file_get_contents($url);
$inventory = json_decode($contents);
$show_hidden = array_key_exists("show_hidden", $_REQUEST) ? $_REQUEST["show_hidden"] : "false";

$rsp_table = '<table id="rider_inventory_tbl" class="table table-condensed">';
$agent_table = '<table id="agent_owned_inventory_tbl" class="table table-condensed">';

foreach (array('rsp_table', 'agent_table') as $table) {
    $$table.= '<thead>';
    $$table.= '<th class="urgency"></th>';
    $$table.= '<th>Equipment Name</th>';
    $$table.= '<th>Last Activity</th>';
    $$table.= '<th>% Available</th>';
    $$table.= '<th>Available</th>';
    $$table.= '<th>Active</th>';
    $$table.= '</thead>';
    $$table.= '<tbody>';
    $$table.= "\n\n";
}

foreach ($inventory->equipment as $equip) {
    if ($equip->equipment_type_id >= 4) {
        $table = "agent_table";
    } else {
        $table = "rsp_table";
    }
    $percent_avail = round(100 * $equip->available / $equip->total, 2);
    $fairfax_avail = 0;
    $fairfax_total = 0;
    foreach ($equip->warehouses as $wh_name => $wh_inventory) {
        if ($wh_name == 'Fairfax Warehouse') {
            foreach ($wh_inventory as $status => $count) {
                if ($status == "Available") {
                    $fairfax_avail = (int) $count;
                }
                if ($status == "Available" || $status == "Installed" || $status == "Pending Install") {
                    $fairfax_total = $fairfax_total + (int) $count;
                }
            }
        }
    }
    if ($fairfax_total != 0) {
        $fairfax_percent = round(100 * $fairfax_avail / $fairfax_total, 2) . "%";
    } else {
        $fairfax_percent = "-";
    }
    $warehouses = json_encode($equip->warehouses);
    $class = "";
    switch ($equip->urgency) {
    case 0:
        $class = " muted";
        if ($show_hidden != "true") {
            continue 2;
        }
        break;
    case 1:
        $class = " text-success";
        break;
    case 3:
        $class = " text-warning";
        break;
    case 5:
        $class = " text-error";
        break;
    }
	
	/*
	$$table .= "<tr class='inventory{$class}' data-id='{$equip->equipment_id}' data-type-id='{$equip->equipment_type_id}' data-sort='{$equip->urgency}' data-warehouses='{$warehouses}'>\n";
    $$table .= "<td class='action'><span class='hidden'>{$equip->urgency}</span></td>\n";
    $$table .= "<td class='name'><strong>{$equip->name}</strong></td><td>{$equip->last_activity_date}</td><td>{$percent_avail}</td><td>{$equip->available}</td><td>{$equip->total}</td></tr>\n\n";*/

    $$table .= "<tr class='inventory{$class}' data-id='{$equip->equipment_id}' data-type-id='{$equip->equipment_type_id}' data-sort='{$equip->urgency}' data-warehouses='{$warehouses}'>\n";
    $$table .= "<td class='action'><span class='hidden'>{$equip->urgency}</span></td>\n";
    $$table .= "<td class='name'><strong>{$equip->name}</strong></td><td>{$equip->last_activity_date}</td><td>{$fairfax_percent}</td><td>{$fairfax_avail}</td><td>{$fairfax_total}</td></tr>\n\n";
}

foreach (array('rsp_table', 'agent_table') as $table) {
    $$table .= "  </tbody>\n";
    $$table .= "</table>\n";
}

echo "<h4>RSP Equipment</h4>\n";
echo $rsp_table;
echo "<h4>Agent Owned Equipment</h4>\n";
echo $agent_table;

?>
