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

header('Content-Type: application/json');

$save = array_key_exists('watchers', $_POST) ? $_POST['watchers'] : '';
$equipment_id = array_key_exists('equipment_id', $_REQUEST) ? (int) $_REQUEST['equipment_id'] : 0;
if ($equipment_id == 0) {
    echo "[]\n";
    exit;
}

$save_watchers = json_decode(stripslashes($save));
if (is_object($save_watchers) && property_exists($save_watchers, "user_ids") && is_array($save_watchers->user_ids)) {
    $database->query("DELETE FROM " . TABLE_INVENTORY_WATCHERS . " WHERE equipment_id = " . $equipment_id);
    foreach ($save_watchers->user_ids as $user_id) {
        $user_id = (int) $user_id;
        if ($user_id) {
            $database->query("INSERT INTO " . TABLE_INVENTORY_WATCHERS . " (equipment_id, user_id) VALUES (" . $equipment_id . ", " . $user_id . ")");
        }
    }
}

$query = $database->query("SELECT iw.user_id, ud.firstname, ud.lastname, u.email_address FROM " . TABLE_INVENTORY_WATCHERS . " iw, " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud WHERE u.user_id = iw.user_id AND ud.user_id = u.user_id AND iw.equipment_id = '" . $equipment_id . "'");

$watchers = array();
foreach($database->fetch_array($query) as $result){
    $name = stripslashes($result['firstname']) . " " . stripslashes($result['lastname']) . (!empty($result['email_address']) ? " (" . $result['email_address'] . ")" : "");
    $watchers[] = "  {\"name\": " . json_encode($name) . ", \"user_id\":" . $result['user_id'] . "}";
}

// Echo the JSON object
echo "[\n";
$i=0;
foreach ($watchers as $watcher) {
    if ($i > 0) {
        echo ",\n";
    }
    $i++;
    echo $watcher;
}
echo "\n]";
?>
