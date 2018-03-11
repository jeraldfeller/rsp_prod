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

if (!is_admin()) {
    die;
}

header('Content-Type: application/json');

array_key_exists('data', $_POST) ? $_POST['data'] : '';
$properties = array("equipment_id", "critical_qty", "critical_pct", "warning_qty", "warning_pct", "excess_qty", "ruleset_name", "action");
foreach ($properties as $var) {
    $$var = (array_key_exists($var, $_REQUEST) && !empty($_REQUEST[$var]))? $_REQUEST[$var] : "na";
}

$equipment_id = (int) $equipment_id;
if ($equipment_id == 0) {
    echo "{}\n";
    exit;
}

if ($action != "update") {
    $query = $database->query("SELECT irs.name FROM " . TABLE_INVENTORY_RULESETS . " irs JOIN " . TABLE_EQUIPMENT . " e ON (e.inventory_ruleset_id = irs.inventory_ruleset_id) WHERE equipment_id = '{$equipment_id}' LIMIT 1");
    if ($result = $database->fetch_array($query)) {
        $ruleset_name = $result['name'];
    }
} else if ($ruleset_name == "custom" && $excess_qty == "na") {
    $ruleset_name = "autogen-crit-{$critical_qty}-{$critical_pct}-warn-{$warning_qty}-{$warning_pct}";
} else if ($ruleset_name == "custom") {
    $ruleset_name = "autogen-crit-{$critical_qty}-{$critical_pct}-warn-{$warning_qty}-{$warning_pct}-excess-{$excess_qty}";
}

// Set ruleset
if ($action == "update") {
    $ruleset_id = 0;
    $query = $database->query("SELECT inventory_ruleset_id FROM " . TABLE_INVENTORY_RULESETS . " WHERE name = '{$ruleset_name}' LIMIT 1");
    if ($result = $database->fetch_array($query)) {
        // Existing ruleset
        $ruleset_id = $result['inventory_ruleset_id'];
        $database->query("UPDATE " . TABLE_EQUIPMENT . " SET inventory_ruleset_id = '{$ruleset_id}' WHERE equipment_id = '{$equipment_id}' LIMIT 1");
    } else {
        // New ruleset
        $database->query("INSERT INTO " . TABLE_INVENTORY_RULESETS . " (inventory_ruleset_id, name) VALUES (NULL, '" . $ruleset_name . "')");
        $query = $database->query("SELECT MAX(inventory_ruleset_id) as ruleset_id FROM " . TABLE_INVENTORY_RULESETS);
        $result = $database->fetch_array($query);
        $ruleset_id = $result['ruleset_id'];

        // Rule 1: Ignore after 24 months of inactivity
        $database->query("INSERT INTO " . TABLE_INVENTORY_RULES . " (inventory_rule_type_id, inventory_alert_id, inventory_rule_order, inventory_ruleset_id, param_1, param_2, description) VALUES (1, 1, 1, {$ruleset_id}, '24', NULL, 'Retire after 24 months of inactivity')");
        // Rule 2: Only pay attention to Fairfax Warehouse
        $database->query("INSERT INTO " . TABLE_INVENTORY_RULES . " (inventory_rule_type_id, inventory_alert_id, inventory_rule_order, inventory_ruleset_id, param_1, param_2, description) VALUES (7, 1, 2, {$ruleset_id}, 'Fairfax Warehouse', NULL, 'Only monitor Fairfax Warehouse')");
        // Rule 3: Critical Qty
        $critical_qty = (int) $critical_qty;
        $database->query("INSERT INTO " . TABLE_INVENTORY_RULES . " (inventory_rule_type_id, inventory_alert_id, inventory_rule_order, inventory_ruleset_id, param_1, param_2, description) VALUES (3, 4, 3, {$ruleset_id}, '{$critical_qty}', 'Fairfax Warehouse', 'Critical QTY at Fairfax Warehouse')");
        // Rule 4: Critical Percent
        if ($critical_pct != "na") {
            $critical_pct = (int) $critical_pct;
            $database->query("INSERT INTO " . TABLE_INVENTORY_RULES . " (inventory_rule_type_id, inventory_alert_id, inventory_rule_order, inventory_ruleset_id, param_1, param_2, description) VALUES (5, 4, 4, {$ruleset_id}, '{$critical_pct}', 'Fairfax Warehouse', 'Critical % at Fairfax Warehouse')");
        }
        // Rule 5: Warning Qty
        $warning_qty = (int) $warning_qty;
        $database->query("INSERT INTO " . TABLE_INVENTORY_RULES . " (inventory_rule_type_id, inventory_alert_id, inventory_rule_order, inventory_ruleset_id, param_1, param_2, description) VALUES (3, 3, 5, {$ruleset_id}, '{$warning_qty}', 'Fairfax Warehouse', 'Warning QTY at Fairfax Warehouse')");
        // Rule 6: Warning Percent
        if ($warning_pct != "na") {
            $warning_pct = (int) $warning_pct;
            $database->query("INSERT INTO " . TABLE_INVENTORY_RULES . " (inventory_rule_type_id, inventory_alert_id, inventory_rule_order, inventory_ruleset_id, param_1, param_2, description) VALUES (5, 3, 6, {$ruleset_id}, '{$warning_pct}', 'Fairfax Warehouse', 'Warning % at Fairfax Warehouse')");
        }
        // Rule 7: Excess Qty
        if ($excess_qty != "na") {
            $excess_qty = (int) $excess_qty;
            $database->query("INSERT INTO " . TABLE_INVENTORY_RULES . " (inventory_rule_type_id, inventory_alert_id, inventory_rule_order, inventory_ruleset_id, param_1, param_2, description) VALUES (9, 5, 7, {$ruleset_id}, '{$excess_qty}', 'Fairfax Warehouse', 'Excess Warning QTY at Fairfax Warehouse')");
        }
        $query = $database->query("UPDATE " . TABLE_EQUIPMENT . " SET inventory_ruleset_id = '{$ruleset_id}' WHERE equipment_id = '{$equipment_id}' LIMIT 1");
    }
}

$query = $database->query("SELECT irs.name as ruleset_name, ia.name as alert, irt.name, param_1 FROM inventory_rulesets irs, inventory_alerts ia, inventory_rule_types irt, inventory_rules ir, equipment e WHERE e.inventory_ruleset_id = ir.inventory_ruleset_id AND ir.inventory_rule_type_id = irt.inventory_rule_type_id AND ia.inventory_alert_id = ir.inventory_alert_id AND irs.inventory_ruleset_id = ir.inventory_ruleset_id AND ia.name IN ('Warning', 'Critical', 'Excess') AND e.equipment_id = '{$equipment_id}'");

while ($result = $database->fetch_array($query)) {
    $ruleset_name = $result['ruleset_name'];
    if ($result['alert'] == "Warning") {
        if ($result['name'] == "Number Available at Warehouse") {
            $warning_qty = (int) $result['param_1'];
        }
        if ($result['name'] == "Percent Available at Warehouse") {
            $warning_pct = (int) $result['param_1'];
        }
    } elseif ($result['alert'] == "Critical") {
        if ($result['name'] == "Number Available at Warehouse") {
            $critical_qty = (int) $result['param_1'];
        }
        if ($result['name'] == "Percent Available at Warehouse") {
            $critical_pct = (int) $result['param_1'];
        }
    } elseif ($result['alert'] == "Excess") {
        if ($result['name'] == "Excess at Warehouse") {
            $excess_qty = (int) $result['param_1'];
        }
    }
}

// Echo the JSON object
echo "{\n";
$i=0;
foreach ($properties as $var) {
    if ($i++ != 0) echo ",\n";
    if ($$var == "na") $$var = "";
    echo "  \"{$var}\": \"{$$var}\"";
}   
echo "\n}";
?>
