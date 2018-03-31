<?php
/*
 * Part of Realty Sign Post (c) 2014 Realty Sign Post.
 * Description: JSON interface to inventory supply levels
 * 
 * Author: John Pelster <john.pelster@gmail.com>
 */

require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

if(substr_count('realtysignpost',$_SERVER['HTTP_HOST'])) {
      error_reporting(0);
        ini_set('error_reporting', 0);
        ini_set('display_errors', 'Off');
}

header('Content-Type: application/json');

// Check to see if a specific equipment_id is being called
// or a list of equipment ID's
$equip_array = array();
if (array_key_exists("equipment_id", $_REQUEST)) {
    if (is_array($_REQUEST["equipment_id"])) {
        $only_equipment_id = 0;
        $equip_array = $_REQUEST["equipment_id"];
    } else {
        $only_equipment_id = $_REQUEST["equipment_id"];
    }
} else {
    $only_equipment_id = 0;
}

if (array_key_exists("summary", $_REQUEST)) {
    $summary = (int) $_REQUEST["summary"];
} else {
    $summary = 0;
}

if ($only_equipment_id) {
    $where = "e.equipment_id = '{$only_equipment_id}'";
} elseif (count($equip_array) > 0) {
    $where = "e.equipment_id IN (";
    $i=0;
    foreach ($equip_array as $equip_id) {
        if ($i>0) {
            $where .= ", ";
        }
        $where .= (int) $equip_id;
        $i++;
    }
    $where .= ")";
} elseif ($summary) {
    $where = "e.equipment_type_id = 1";
} else {
    $where = "TRUE";
}

// Find the total number of equipment items
$query = $database->query("SELECT e.equipment_id, e.equipment_type_id, e.name, e.inventory_ruleset_id, count( ei.equipment_item_id ) AS count FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON ( e.equipment_id = ei.equipment_id ) WHERE {$where} GROUP BY e.equipment_id, e.equipment_type_id, e.name");

$agent_equip_count = 0;
$agent_equip = array();
foreach($database->fetch_array($query) as $result){
    $agent_equip[$result['equipment_id']] = array();
    $agent_equip[$result['equipment_id']]['name'] = $result['name'];
    $agent_equip[$result['equipment_id']]['inventory_ruleset_id'] = $result['inventory_ruleset_id'];
    $agent_equip[$result['equipment_id']]['equipment_type_id'] = $result['equipment_type_id'];
    $agent_equip[$result['equipment_id']]['total'] = (int) $result['count'];
    $agent_equip[$result['equipment_id']]['available'] = 0;
    $agent_equip[$result['equipment_id']]['last_activity_date'] = '1/1/2000';
    $agent_equip[$result['equipment_id']]['warehouses'] = '{}';
    $agent_equip[$result['equipment_id']]['urgency'] = 1;
    $agent_equip[$result['equipment_id']]['rule'] = 'None';
    $agent_equip_count += $result['count'];
}

// Find the number of available equipment items
$query = $database->query("SELECT e.equipment_id, count( ei.equipment_item_id ) AS count FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON ( e.equipment_id = ei.equipment_id ) WHERE ei.equipment_status_id = 0 AND {$where} GROUP BY e.equipment_id");
foreach($database->fetch_array($query) as $result){
    $agent_equip[$result['equipment_id']]['available'] = (int) $result['count'];
}

// Find the latest activity date
$query = $database->query("SELECT ei.equipment_id, DATE_FORMAT(DATE(FROM_UNIXTIME(MAX(eih.date_added) - 18000)),'%c/%e/%Y') AS last_activity_date FROM " . TABLE_EQUIPMENT_ITEMS_HISTORY . " eih JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( ei.equipment_item_id = eih.equipment_item_id ) JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) WHERE {$where} GROUP BY e.equipment_id");
foreach($database->fetch_array($query) as $result){
    $agent_equip[$result['equipment_id']]['last_activity_date'] = $result['last_activity_date'];
}

// Find the warehouse locations and status
$query = $database->query("SELECT e.equipment_id, e.equipment_type_id, es.equipment_status_name, wd.name, COUNT( ei.equipment_item_id ) AS count FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) JOIN " . TABLE_EQUIPMENT_STATUSES . " es ON (ei.equipment_status_id = es.equipment_status_id) JOIN " . TABLE_WAREHOUSES_DESCRIPTION . " wd ON (wd.warehouse_id = ei.warehouse_id) WHERE {$where} GROUP BY e.equipment_id, e.equipment_type_id, es.equipment_status_name, wd.name");
$equip_warehouses = array();

$fairfax_posts_total = 0;
$fairfax_posts_avail = 0;
$fairfax_posts_installed = 0;

	$all_posts_total = 0;
	$all_posts_avail = 0;
	$all_posts_installed = 0;

foreach($database->fetch_array($query) as $result){
    if (!array_key_exists($result['equipment_id'], $equip_warehouses)) {
        $equip_warehouses[$result['equipment_id']] = array();
    }
    if (!array_key_exists($result['name'], $equip_warehouses[$result['equipment_id']])) {
        $equip_warehouses[$result['equipment_id']][$result['name']] = array();
    }
    $equip_warehouses[$result['equipment_id']][$result['name']][$result['equipment_status_name']] = $result['count'];
	

    if ($result['equipment_type_id'] == 1) {
		
		// Count the stats for FFX, MD and PA warehouses. We only need installed and totals for MD and PA summary
        if ($result['name'] == "Fairfax Warehouse") {
            switch($result['equipment_status_name']) {
            case "Available":
                $fairfax_posts_avail += $result['count'];
                $fairfax_posts_total += $result['count'];
                break;
            case "Pending Install":
                $fairfax_posts_total += $result['count'];
                break;
            case "Installed":
                $fairfax_posts_total += $result['count'];
                $fairfax_posts_installed += $result['count'];
                break;
            default:
                break;
            }

        }
		elseif($result['name'] == "MD Warehouse") {
			switch($result['equipment_status_name']) {
            case "Available":
            case "Pending Install":
                $posts_total += $result['count'];
                break;
            case "Installed":
                $posts_total += $result['count'];
                $md_posts_installed += $result['count'];
                break;
            default:
                break;
            }
		}
		elseif($result['name'] == "PA Warehouse") {
			switch($result['equipment_status_name']) {
            case "Available":
			case "Pending Install":
                $posts_total += $result['count'];
                break;
            case "Installed":
                $posts_total += $result['count'];
                $pa_posts_installed += $result['count'];
                break;
            default:
                break;
            }
		}
    }

}

$posts_total += $fairfax_posts_total;

// If it's only a summary, we are done
if ($summary) {
	$output = array('posts_total' => $posts_total,
					'posts_avail' => $fairfax_posts_avail,
					'ffx_posts_installed' => $fairfax_posts_installed,
					'md_posts_installed' => $md_posts_installed,
					'pa_posts_installed' => $pa_posts_installed
					);
    echo json_encode($output);
    exit;
}

foreach ($equip_warehouses as $eid => $ewh) {
    $agent_equip[$eid]['warehouses'] = json_encode($ewh);
}

// Fetch all the rules and rulesets from the database
$query = $database->query("SELECT inventory_ruleset_id, inventory_rule_type_id, inventory_rule_id, inventory_alert_id, param_1, param_2 FROM " . TABLE_INVENTORY_RULES . " ORDER BY inventory_rule_order ASC");
$inventory_rulesets = array();
foreach($database->fetch_array($query) as $result){
    if (!array_key_exists($result['inventory_ruleset_id'], $inventory_rulesets)) {
        $inventory_rulesets[$result['inventory_ruleset_id']] = array();
    }
    $inventory_rulesets[$result['inventory_ruleset_id']][$result['inventory_rule_id']] = array();
    $inventory_rulesets[$result['inventory_ruleset_id']][$result['inventory_rule_id']]['inventory_rule_type_id'] = $result['inventory_rule_type_id'];
    $inventory_rulesets[$result['inventory_ruleset_id']][$result['inventory_rule_id']]['inventory_alert_id'] = $result['inventory_alert_id'];
    $inventory_rulesets[$result['inventory_ruleset_id']][$result['inventory_rule_id']]['param_1'] = $result['param_1'];
    $inventory_rulesets[$result['inventory_ruleset_id']][$result['inventory_rule_id']]['param_2'] = $result['param_2'];
}

// Fetch all the alerts so we can use them
$query = $database->query("SELECT inventory_alert_id, severity FROM " . TABLE_INVENTORY_ALERTS);
$inventory_alerts = array();
foreach($database->fetch_array($query) as $result){
    $inventory_alerts[$result['inventory_alert_id']] = $result['severity'];
}

// Fetch all the rule types so we can refer to them
$query = $database->query("SELECT inventory_rule_type_id, name FROM " . TABLE_INVENTORY_RULE_TYPES);
$inventory_rule_types = array();
foreach($database->fetch_array($query) as $result){
    $inventory_rule_types[$result['inventory_rule_type_id']] = $result['name'];
}

// Run the ruleset on each equipment item, return the current severity
foreach ($agent_equip as $equipment_id => $equip) {
    $ruleset = $inventory_rulesets[$equip['inventory_ruleset_id']];
    $urgency = 1;
    foreach ($ruleset as $rule) {
        $ruletype = $rule['inventory_rule_type_id'];
        $ruletype_name = $inventory_rule_types["{$ruletype}"];

        switch ($ruletype_name) {
        case "Activity Date":
            $num_months = (int) $rule['param_1'];
            if (strtotime($equip['last_activity_date']) < mktime() - ( $num_months * 365 * 24 * 60 * 60 / 12 )) {
                $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                break 2;
            }
            break 1;
        case "Number Available":
            $min = (int) $rule['param_1'];
            if ( $equip['available'] <= $min ) {
                $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                break 2;
            }
            break 1;
        case "Number Available at Warehouse":
            $min = (int) $rule['param_1'];
            $wh_name = $rule['param_2'];
            $wh_inventory = json_decode($equip['warehouses']);
            if (property_exists($wh_inventory, $wh_name)) {
                // There is equipment associated with the warehouse
                if (property_exists($wh_inventory->$wh_name, 'Available')) {
                    // There are some available, let's check the amount
                    if ($wh_inventory->$wh_name->Available <= $min) {
                        $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                        break 2;
                    }
                } else {
                    // None available
                    $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                    break 2;
                }
            } else {
                // None at the warehouse
                $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                break 2;
            }
            break 1;
        case "Percent Available":
            $min_percent = (float) $rule['param_1'];
            $percent = 100 * $equip['available'] / $equip['total'];
            if ( $percent <= $min_percent ) {
                $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                break 2;
            }
            break 1;
        case "Percent Available at Warehouse":
            $min_percent = (float) $rule['param_1'];
            $wh_name = $rule['param_2'];
            $wh_inventory = json_decode($equip['warehouses']);
            if (property_exists($wh_inventory, $wh_name)) {
                // There is equipment associated with the warehouse
                if (property_exists($wh_inventory->$wh_name, 'Available')) {
                    // There are some available, let's check the amount
                    $avail = 0;
                    $total = 0;
                    foreach ($wh_inventory->$wh_name as $status => $count) {
                        if ($status == 'Available') {
                            $avail = (int) $count;
                        }
                        // Only include the following statuses in the total
                        if ($status == 'Available' || $status == 'Installed' || $status == 'Pending Install') {
                            $total = $total + (int) $count;
                        }
                    }
                    if (100 * $avail / $total <= $min_percent) {
                        $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                        break 2;
                    }
                } else {
                    // None available
                    $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                    break 2;
                }
            } else {
                // None at the warehouse
                $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                break 2;
            }
            break 1;
        case "Not at Warehouse":
            $wh_name = $rule['param_1'];
            $wh_inventory = json_decode($equip['warehouses']);
            if (property_exists($wh_inventory, $wh_name)) {
                // There is equipment associated with the warehouse
                break 1;
            } else {
                // There is not equipment at this Warehouse, set alert
                $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                break 2;
            }
            break 1;
        case "OK":
            $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
            break 2;
        case "Excess":
            $month = date('n');
            if ($month < '7') {
                break 1;
            }
            $max = (int) $rule['param_1'];
            if ( $equip['available'] > $max ) {
                $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                break 2;
            }
            break 1;
        case "Excess at Warehouse":
            $month = date('n');
            if ($month < '7') {
                break 1;
            }
            $max = (int) $rule['param_1'];
            $wh_name = $rule['param_2'];
            $wh_inventory = json_decode($equip['warehouses']);
            if (property_exists($wh_inventory, $wh_name)) {
                // There is equipment associated with the warehouse
                if (property_exists($wh_inventory->$wh_name, 'Available')) {
                    // There are some available, let's check the amount
                    if ($wh_inventory->$wh_name->Available > $max) {
                        $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                        break 2;
                    }
                }
            }
            break 1;
        case "No Inventory at Warehouse":
            $wh_name = $rule['param_1'];
            $wh_inventory = json_decode($equip['warehouses']);
            if (property_exists($wh_inventory, $wh_name)) {
                // There is equipment associated with the warehouse
                if (property_exists($wh_inventory->$wh_name, 'Available') || property_exists($wh_inventory->$wh_name, 'Active')) {
                    // There are some available
                    break 1;
                } else {
                    $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                    break 2;
                }
            } else {
                // None at the warehouse
                $urgency = $inventory_alerts["{$rule['inventory_alert_id']}"];
                break 2;
            }
        }
    }
    $agent_equip[$equipment_id]['urgency'] = $urgency;
    $agent_equip[$equipment_id]['rule'] = $ruletype_name;
}

if (!$only_equipment_id) {
    $output  = "{\n";
    $output .= "  \"posts_total\": {$fairfax_posts_total},\n";
    $output .= "  \"posts_avail\": {$fairfax_posts_avail},\n";
    $output .= "  \"posts_installed\": {$fairfax_posts_installed},\n";
    $output .= "  \"equipment\": [\n";
} else {
    $output = "";
}
$i = 0;
foreach ($agent_equip as $equipment_id => $equip) {
    if ($i > 0) {
        $output .= ",\n";
    }
    $i++;
    $output .= "    { \n";
    $output .= "      \"equipment_id\": {$equipment_id},\n";
    $output .= "      \"equipment_type_id\": {$equip['equipment_type_id']},\n";
    $output .= "      \"name\": " . json_encode($equip['name']) . ",\n";
    $output .= "      \"last_activity_date\": " . json_encode($equip['last_activity_date']) . ",\n";
    $output .= "      \"available\": {$equip['available']},\n";
    $output .= "      \"total\": {$equip['total']},\n";
    $output .= "      \"urgency\": {$equip['urgency']},\n";
    $output .= "      \"rule\": " . json_encode($equip['rule']) . ",\n";
    $output .= "      \"warehouses\": {$equip['warehouses']}\n";
    $output .= "    }";
}
if (!$only_equipment_id) {
    $output .= "\n  ]\n";
    $output .= "}";
}

echo $output;
?>
