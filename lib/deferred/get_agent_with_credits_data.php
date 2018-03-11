<?php
ini_set('memory_limit','-1');
require_once('../../includes/application_top.php');
Global $database;

$query = $database->query("SELECT ud.firstname, ud.lastname, a.running_total FROM " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud,  " . TABLE_USERS_TO_USER_GROUPS . " ug, " . TABLE_ACCOUNTS . " a WHERE u.user_id = ug.user_id AND u.user_id = ud.user_id AND ug.user_group_id = 1 AND u.user_id = a.user_id AND a.running_total > 0");

$item['data'] = array();
while($result = $database->fetch_array($query)) {

    $item['data'][] = array(
        $result['firstname'] . ' ' . $result['lastname'],
        '$'.$result['running_total'],
    );

}

echo json_encode($item);
