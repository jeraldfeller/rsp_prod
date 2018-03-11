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

$properties = array(
    "critical_number_available_at_warehouse" => 2, 
    "warning_number_available_at_warehouse" => 3, 
    "warning_percent_available_at_warehouse" => 4, 
    "retired_activity_date" => 1
);

$action = (array_key_exists("action", $_REQUEST) && !empty($_REQUEST["action"])) ? $_REQUEST["action"] : "";

if ($action == "update") {
    foreach ($properties as $var => $rule_id) {
        if ($rule_id && array_key_exists($var, $_REQUEST)) {
            $query = $database->query("UPDATE " . TABLE_INVENTORY_RULES . " SET param_1 = '" . (int) $_REQUEST[$var] . "' WHERE inventory_rule_id = '{$rule_id}'");
            error_log("UPDATE " . TABLE_INVENTORY_RULES . " SET param_1 = '" . (int) $_REQUEST[$var] . "' WHERE inventory_rule_id = '{$rule_id}'");
        }
    }
}

$query = $database->query("SELECT ia.name AS alert, irt.name, ir.param_1 FROM " . TABLE_INVENTORY_RULES . " ir JOIN " . TABLE_INVENTORY_RULE_TYPES . " irt ON (ir.inventory_rule_type_id = irt.inventory_rule_type_id) JOIN " . TABLE_INVENTORY_ALERTS . " ia ON (ia.inventory_alert_id = ir.inventory_alert_id) WHERE ir.inventory_ruleset_id = 1 ORDER BY ir.inventory_rule_order");
$defaults = array();
foreach($query as $result){
    $key = strtolower(str_replace(' ', '_', "{$result['alert']}_{$result['name']}"));
    $value = $result['param_1'];
    $defaults[$key] = $value;
}

// Echo the JSON object
echo "{\n";
$i=0;
foreach ($defaults as $key => $value) {
    if ($i > 0) {
        echo ",\n";
    }
    $i++;
    echo "  \"{$key}\": \"{$value}\"";
}
echo "\n}";
?>
