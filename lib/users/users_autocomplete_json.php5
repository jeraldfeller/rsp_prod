<?php
/*
 * Part of Realty Sign Post (c) 2014 Realty Sign Post.
 * Description: JSON interface for user autocomplete jQuery-UI widget
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

$term = array_key_exists('term', $_GET) ? $_GET['term'] : '';
if (strlen($term) < 2) {
    echo "[]\n";
    exit;
}
$term = $term;

$query = $database->query("SELECT u.user_id, ud.firstname, ud.lastname, u.email_address, u.agency_id, ug.name FROM " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug, " . TABLE_USER_GROUPS . " ug WHERE u.user_id = ud.user_id AND u.user_id = utug.user_id AND utug.user_id = u.user_id AND ug.user_group_id = utug.user_group_id AND ( ud.firstname LIKE '" . $term . "%' OR ud.lastname LIKE '" . $term . "%' OR u.email_address LIKE '" . $term . "%' OR CONCAT(ud.firstname, ' ', ud.lastname) LIKE '" . $term . "%') ORDER BY ug.name, ud.firstname, ud.lastname");
$users = array();
foreach($database->fetch_array($query) as $result){
    $name = stripslashes($result['firstname']) . " " . stripslashes($result['lastname']) . " (" . $result['email_address'] . ")";
    $users[] = "  {\"label\": " . json_encode($name) . ", \"category\": " . json_encode($result['name']) . ", \"user_id\":" . $result['user_id'] . "}";
}

// Echo the JSON object
echo "[\n";
$i=0;
foreach ($users as $user) {
    if ($i > 0) {
        echo ",\n";
    }
    $i++;
    echo $user;
}
echo "\n]";
?>
