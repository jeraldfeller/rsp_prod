<?php

// Updated 1/23/2013 brad@brgr2.com Added get_account_id() and formatted code.
// Updated 12/22/2012 brad@brgr2.com
// mjp 20120125 Added tep_is_personal_equipment
// mjp 20120125 Added parse_equipment_array2 because sometimes zero is a valid entry.

function tep_get_total_address_cost($address_id) {
    global $database;
    $total_cost = 0;
    //First get the install cost
    $query = $database->query("select order_total, deposit_cost, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $address_id . "' and order_type_id = '1'");
    $result = $database->fetch_array($query);

    if ($result['order_status_id'] != '6') { //Change to 4 later.
        $total_cost += $result['order_total'];
        if ($result['order_status_id'] == '1') {
            $total_cost -= $result['deposit_cost'];
        }
    }
    //Now get everythign else.
    $query = $database->query("select order_total, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $address_id . "' and order_type_id != '1'");
    foreach($query as $result){
        if ($result['order_status_id'] != '6') { //Change to 4 later.
            $total_cost += $result['order_total'];
        }
    }
    return $total_cost;
}

function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {

    if (SEND_EMAILS != 'true')
        return false;



    // Instantiate a new mail object

    $message = new email(array('X-Mailer: Realty Sign Post Mailer'));



    // Build the text version

    $text = strip_tags($email_text);
//
    //if (EMAIL_USE_HTML == 'true') {

    $message->add_html($email_text, $text);

    //} else {
    //$message->add_text($text);
    //}
    // Send message

    $message->build_message();

    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
}

function tep_not_null($value) {

    if (is_array($value)) {

        if (sizeof($value) > 0) {

            return true;
        } else {

            return false;
        }
    } else {

        if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {

            return true;
        } else {

            return false;
        }
    }
}

function tep_convert_linefeeds($from, $to, $string) {

    if ((PHP_VERSION < "4.0.5") && is_array($from)) {

        return ereg_replace('(' . implode('|', $from) . ')', $to, $string);
    } else {

        return str_replace($from, $to, $string);
    }
}

function tep_return_ip_address() {
    return $_SERVER['REMOTE_ADDR'];
}

function tep_return_timestamp() {
    return time();
}

function tep_redirect($location) {
    global $session;
    header("Location: " . $session->proccess_url($location));
    $session->php_session_close();
    die();
}

/* function tep_generate_post_type_pulldown_menu($name, $selected = '', $params = '', $zip4 = '') {
  global $database;
  $equipment_type_id = 1;
  $query = $database->query("select post_type_id, post_type_name from " . TABLE_POST_TYPES . " order by post_type_name");
  while($result = $database->fetch_array($query)) {
  $array[] = array('id' => $result['post_type_id'], 'name' => $result['post_type_name']);
  }
  return tep_generate_pulldown_menu($name, $array, $selected, $params);
  } */
//bogdan
function tep_generate_post_type_pulldown_menu($name, $selected = '', $params = '', $zip4 = '', $required = 1) {
    global $database;
    $equipment_type_id = 1;
    $array = array();
    $zip4_explode = tep_break_zip4_code($zip4);


    $zip4_start = $zip4_explode[0];
    $zip4_end = $zip4_explode[1];
    $sql = "select count(e.equipment_id) as count, e.equipment_id, e.name from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_WAREHOUSES . " w left join " . TABLE_INSTALLATION_AREAS . " ia on (w.warehouse_id = ia.warehouse_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (ica.installation_area_id = ia.installation_area_id) where CONCAT(ica.zip_4_first_break_start, ica.zip_4_first_break_end) <= CONCAT('" . $zip4_start . "', '" . $zip4_end . "') AND CONCAT(ica.zip_4_second_break_start, ica.zip_4_second_break_end) >= CONCAT('" . $zip4_start . "', '" . $zip4_end . "') AND e.equipment_type_id = '" . $equipment_type_id . "' and e.equipment_id = ei.equipment_id and ei.equipment_status_id = '0' and ei.warehouse_id = w.warehouse_id and (w.availability = '0' or ica.installation_area_id is not NULL) group by e.equipment_id order by e.name";

    if (empty($zip4)) {
        return;
    }

    $query = $database->query($sql);

    foreach($query as $result){
        if (($result['count'] > 0) && ($result['count'] >= $required)) {
            $array[] = array('id' => $result['equipment_id'], 'name' => $result['name']);
        }
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_generate_discount_pulldown_menu($name, $selected = '') {
    global $database;
    $array = array();
    $array[] = array('id' => '0', 'name' => 'None');
    $array[] = array('id' => '1', 'name' => 'Amount');
    $array[] = array('id' => '2', 'name' => 'Percentage');
    return tep_generate_pulldown_menu($name, $array, $selected);
}

function tep_generate_install_fail_pulldown_menu($name, $selected = '', $params = '') {
    global $database;
    $array = array();
    if (empty($selected)) {
        $array[] = array('id' => '', 'name' => 'Please Select');
    }
    $array[] = array('id' => '1', 'name' => 'Post Not Allowed');
    $array[] = array('id' => '2', 'name' => 'No Room to Install');
    $array[] = array('id' => '3', 'name' => 'Wrong House #');
    $array[] = array('id' => '4', 'name' => 'Stopped by Homeowner/Tenant');
    $array[] = array('id' => '5', 'name' => 'Unable to Find Address');
    $array[] = array('id' => '6', 'name' => 'Post Already Installed');
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_generate_post_leaning_reason_pulldown_menu($name, $selected = '', $params = '') {
    global $database;
    $array = array();
    if (empty($selected)) {
        $array[] = array('id' => '', 'name' => 'Please Select');
    }
    $array[] = array('id' => '1', 'name' => 'Weather');
    $array[] = array('id' => '2', 'name' => 'Improper Installation');
    $array[] = array('id' => '3', 'name' => 'Someone moved Post');
    $array[] = array('id' => '4', 'name' => 'Other');

    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_generate_post_leaning_reason_pulldown_menu_bgdn($name, $selected = '', $params = '') {
    global $database;
    $array = array();
    if (empty($selected)) {
        $array[] = array('id' => '', 'name' => 'Please Select');
    }
    $array[] = array('id' => '1', 'name' => 'Weather');
    $array[] = array('id' => '2', 'name' => 'Improper Installation');
    $array[] = array('id' => '3', 'name' => 'Someone moved Post');
    $array[] = array('id' => '4', 'name' => 'Other');

    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}


function tep_generate_service_call_pulldown_menu($name, $selected = '', $user_id = '', $address_id = '', $params = '') {
    global $database;
    $array = array();
    if (empty($selected)) {
        $array[] = array('id' => '', 'name' => 'Please Select');
    }
    //Need to work out what can actually happen here.
    //First check if there are any riders at the property already and if so show the exchange option.
    $query = $database->query("select count(eita.equipment_item_to_address_id) as count from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.equipment_id = e.equipment_id and eita.equipment_status_id = '2' and e.equipment_type_id = '2' and eita.address_id = '" . $address_id . "'");
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        $array[] = array('id' => '1', 'name' => 'Exchange Rider');
    }
    $zip4 = fetch_address_zip4($address_id);
    $warehouses = tep_get_sevicing_warehouse($zip4);
    if (tep_equipment_type_has_items('2', $user_id, $warehouses, $address_id) || tep_equipment_type_has_items('3', $user_id, $warehouses, $address_id)) {
        $array[] = array('id' => '2', 'name' => 'Install New Rider or BBox');
    }
    if (tep_equipment_type_has_items('4', $user_id, $warehouses, $address_id)) {
        $array[] = array('id' => '3', 'name' => 'Replace/Exchange Agent SignPanel');
    }
    $array[] = array('id' => '4', 'name' => 'Post Leaning/Straighten Post');
    $array[] = array('id' => '5', 'name' => 'Move Post');
    $array[] = array('id' => '6', 'name' => 'Install Equipment Forgotten at Install');
    $array[] = array('id' => '7', 'name' => 'Other');

    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_generate_service_call_pulldown_menu_bgdn($name, $selected = '', $user_id = '', $address_id = '', $params = '') {
    global $database;
    $array = array();
    if (empty($selected)) {
        $array[] = array('id' => '', 'name' => 'Please Select');
    }
    //Need to work out what can actually happen here.
    //First check if there are any riders at the property already and if so show the exchange option.
    $query = $database->query("select count(eita.equipment_item_to_address_id) as count from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.equipment_id = e.equipment_id and eita.equipment_status_id = '2' and e.equipment_type_id = '2' and eita.address_id = '" . $address_id . "'");
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        $array[] = array('id' => '1', 'name' => 'Exchange Rider');
    }
    $zip4 = fetch_address_zip4($address_id);
    $warehouses = tep_get_sevicing_warehouse($zip4);
    if (tep_equipment_type_has_items('2', $user_id, $warehouses, $address_id) || tep_equipment_type_has_items('3', $user_id, $warehouses, $address_id)) {
        $array[] = array('id' => '2', 'name' => 'Install New Rider or BBox');
    }
    if (tep_equipment_type_has_items('4', $user_id, $warehouses, $address_id)) {
        $array[] = array('id' => '3', 'name' => 'Replace/Exchange Agent SignPanel');
    }
    $array[] = array('id' => '4', 'name' => 'Post Leaning/Straighten Post');
    $array[] = array('id' => '5', 'name' => 'Move Post');
    $array[] = array('id' => '6', 'name' => 'Install Equipment Forgotten at Install');
    $array[] = array('id' => '7', 'name' => 'Other');

    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}

function tep_generate_yes_no_pulldown_menu($name, $selected = '', $params = '') {
    global $database;
    $array = array();
    if (empty($selected)) {
        $array[] = array('id' => '', 'name' => 'Please Select');
    }
    $array[] = array('id' => '1', 'name' => 'Yes');
    $array[] = array('id' => '2', 'name' => 'No');

    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_generate_yes_no_unsure_pulldown_menu($name, $selected = '', $params = '') {
    global $database;
    $array = array();
    if (empty($selected)) {
        $array[] = array('id' => '', 'name' => 'Please Select');
    }
    $array[] = array('id' => '1', 'name' => 'Yes');
    $array[] = array('id' => '2', 'name' => 'No');
    $array[] = array('id' => '3', 'name' => 'Unsure');

    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_generate_order_placement_type_pulldown_menu($name, $selected = '', $params = '') {
    global $database;

    $array[] = array('id' => '', 'name' => 'Any');
    $array[] = array('id' => '1', 'name' => 'Realtysignpost.net');
    $array[] = array('id' => '2', 'name' => 'Admin');
    $array[] = array('id' => '3', 'name' => 'Order Import');
    $array[] = array('id' => '4', 'name' => 'Realtysignpost.com');
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_generate_equipment_type_pulldown_menu($name, $selected = '', $array = array(), $params = '') {
    global $database;
    $query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_name");
    foreach($query as $result){
        $array[] = array('id' => $result['equipment_type_id'], 'name' => $result['equipment_type_name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_fetch_default_equipment_type_id() {
    global $database;

    $query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_name limit 1");
    $result = $database->fetch_array($query);

    return $result['equipment_type_id'];
}

function tep_fetch_default_equipment_group_id($order_type_id) {
    global $database;

    $query = $database->query("select equipment_group_id from " . TABLE_EQUIPMENT_GROUPS . " where order_type_id = '" . $order_type_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['equipment_group_id'];
}

function tep_fetch_default_order_type_id() {
    global $database;

    $query = $database->query("select order_type_id, name from " . TABLE_ORDER_TYPES . " limit 1");
    $result = $database->fetch_array($query);

    return $result['order_type_id'];
}

function tep_fetch_order_type_id($order_id) {
    global $database;

    $query = $database->query("select order_type_id from " . TABLE_ORDERS . " where order_id = '" . (int) $order_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['order_type_id'];
}

function tep_fetch_default_equipment_id($equipment_type_id) {
    global $database;

    $query = $database->query("select equipment_id, name from " . TABLE_EQUIPMENT . " where equipment_type_id = '" . $equipment_type_id . "' order by name limit 1");
    $result = $database->fetch_array($query);

    return $result['equipment_id'];
}

function tep_fetch_equipment_name($equipment_id) {
    global $database;

    $query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['name'];
}

function fetch_post_type_name($post_type_id) {
    global $database;

    $query = $database->query("select post_type_name from " . TABLE_POST_TYPES . " where post_type_id = '" . $post_type_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['post_type_name'];
}

function tep_generate_pulldown_menu($name, $contents = array(), $selected = '', $params = '') {
    $return_string = '<select class="font-control" name="' . $name . '"' . ((!empty($params)) ? ' ' . $params : '') . '>';
    $count = count($contents);
    $n = 0;
    while ($n < $count) {
        $this_selected = '';
        if ($contents[$n]['id'] === $selected) {
            $this_selected = ' SELECTED';
        }
        $return_string .= '<option value="' . $contents[$n]['id'] . '"' . $this_selected . '>' . $contents[$n]['name'] . '</option>';
        $n++;
    }
    $return_string .= '</select>';
    return $return_string;
}

function tep_fill_variable($id, $method = 'post', $default = '') {
    $return = $default;
    if ($method == 'post') {
        if (isset($_POST[$id])) {
            if (is_array($_POST[$id])) {
                $return = $_POST[$id];
            } else {
                $return = addslashes(stripslashes($_POST[$id]));
            }
        }
    } elseif ($method == 'get') {
        if (isset($_GET[$id])) {
            if (is_array($_GET[$id])) {
                $return = $_GET[$id];
            } else {
                $return = addslashes(stripslashes($_GET[$id]));
            }
        }
    } elseif ($method == 'session') {
        if (isset($_SESSION[$id])) {
            if (is_array($_SESSION[$id])) {
                $return = $_SESSION[$id];
            } elseif (is_object($_SESSION[$id])) {
                $return = $_SESSION[$id];
            } else {
                $return = addslashes(stripslashes($_SESSION[$id]));
            }
        }
    }
    return $return;
}

function tep_validate_phone_number($number = '') {
    if (empty($number) || (strlen($number) < 7)) {
        return false;
    } else {
        return true;
    }
}

function tep_validate_email_address($email = '') {
    if (empty($email) || (strpos($email, '@') === false)) {
        return false;
    } else {
        return true;
    }
}

function tep_email_address_exists($email = '', $ignore = '', $set_default = true) {
    global $database, $user;
    $extra_string = '';
    if ($set_default && $user->user_is_logged()) {
        $extra_string .= " and user_id != '" . $user->fetch_user_id() . "'";
    }
    if (!empty($ignore)) {
        $extra_string .= " and user_id != '" . $ignore . "'";
    }
    $query = $database->query("select count(user_id) as count from " . TABLE_USERS . " where email_address = '" . $email . "'" . $extra_string);
    $result = $database->fetch_array($query);
    if ($result['count'] == 0) {
        return false;
    } else {
        return true;
    }
}

function tep_get_order_type_name($id) {
    global $database;
    $query = $database->query("select name from " . TABLE_ORDER_TYPES . " where order_type_id = '" . $id . "' limit 1");



    $result = $database->fetch_array($query);

    return $result['name'];
}

function tep_fetch_email_data($user_id) {
    global $database;
    $query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $user_id . "' and u.user_id = ud.user_id limit 1");
    $result = $database->fetch_array($query);
    return $result;
}

function tep_get_service_level_name($id) {
    global $database;
    $query = $database->query("select name from " . TABLE_SERVICE_LEVELS . " where service_level_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['name'];
}

function tep_get_billing_method_name($id) {
    global $database;
    $query = $database->query("select name from " . TABLE_BILLING_METHODS . " where billing_method_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['name'];
}

function tep_get_equipment_name($id) {
    global $database;
    $query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['name'];
}

function tep_get_order_status_name($id) {
    global $database;
    $query = $database->query("select order_status_name from " . TABLE_ORDERS_STATUSES . " where order_status_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['order_status_name'];
}

function tep_draw_order_type_pulldown($name = '', $selected = '', $params = '', $array = array()) {
    global $database;
    $return = '';
    $query = $database->query("select order_type_id, name from " . TABLE_ORDER_TYPES . " order by name");
    foreach($query as $result){
        $array[] = array('id' => $result['order_type_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_order_type_pulldown_bgdn($name = '', $selected = '', $params = '', $array = array()) {
    global $database;
    $return = '';
    $query = $database->query("select order_type_id, name from " . TABLE_ORDER_TYPES . " order by name");
    foreach($query as $result){
        $array[] = array('id' => $result['order_type_id'], 'name' => $result['name']);
    }
    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_equipment_type_type_pulldown($name = '', $selected = '', $array = array(), $params = '') {
    global $database;
    $return = '';
    $query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_name");
    foreach($query as $result){
        $array[] = array('id' => $result['equipment_type_id'], 'name' => $result['equipment_type_name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_agent_pulldown($name = '', $selected = '', $params = '', $array = array(), $agency_id = '', $show_aom = false) {
    global $database;
    $return = '';
    $query = $database->query("select u.user_id, u.agent_id, ud.firstname, ud.lastname, utug.user_group_id from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where (utug.user_group_id = '1'" . (($show_aom) ? " or utug.user_group_id = '4'" : '') . ") and utug.user_id = u.user_id" . ((!empty($agency_id)) ? " and u.agency_id = '" . $agency_id . "'" : '') . " and u.active_status = 1 and u.user_id = ud.user_id order by ud.lastname, ud.firstname");
    foreach($query as $result){
        $array[] = array('id' => $result['user_id'], 'name' => ((($result['user_group_id'] == '4') ? 'AOM - ' : '') . $result['lastname'] . ', ' . $result['firstname'] . ' (' . $result['agent_id'] . ')'));
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_aom_agent_pulldown($name = '', $selected = '', $aom = '', $params = '', $array = array()) {
    global $database;
    $return = '';
    $agency_id = tep_fetch_order_manager_agency($aom);
    $query = $database->query("select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id" . ((!empty($agency_id)) ? " and u.agency_id = '" . $agency_id . "'" : '') . " and u.user_id = ud.user_id and u.active_status = '1' order by ud.firstname");
    foreach($query as $result){
        $array[] = array('id' => $result['user_id'], 'name' => ($result['firstname'] . ' ' . $result['lastname'] . ' (' . $result['agent_id'] . ')'));
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_aom_agent_pulldown_bgdn($name = '', $selected = '', $aom = '', $params = '', $array = array()) {
    global $database;
    $return = '';

    $agency_id = tep_fetch_order_manager_agency($aom);
    // echo $aom."<-aom1 ".$agency_id."<-agency_id<br/>";
    $query = $database->query("select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id" . ((!empty($agency_id)) ? " and u.agency_id = '" . $agency_id . "'" : '') . " and u.user_id = ud.user_id and u.active_status = '1' order by ud.firstname");
    //  echo "select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id" . ((!empty($agency_id)) ? " and u.agency_id = '" . $agency_id . "'" : '') . " and u.user_id = ud.user_id and u.active_status = '1' order by ud.firstname<br/>";
    foreach($query as $result){
        $array[] = array('id' => $result['user_id'], 'name' => ($result['firstname'] . ' ' . $result['lastname'] . ' (' . $result['agent_id'] . ')'));
    }
    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_aom_agent_pulldown_bgdn_pending_installs($name = '', $selected = '', $aom = '', $params = '', $array = array()) {
    global $database;
    $return = '';

    $agency_id = tep_fetch_order_manager_agency($aom);
    //echo $aom."<-aom2 ".$agency_id."<-agency_id<br/>";
    $sql2String = "SELECT o.`user_id` FROM ".TABLE_ORDERS." as o,`users` as u WHERE u.`user_id` = o.`user_id` and u.`agency_id`= '$agency_id' and o.`order_type_id`='1' and o.`order_status_id`='1'";
    $query = $database->query("select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id and u.agency_id = ".$agency_id." and u.user_id = ud.user_id and u.user_id IN (".$sql2String.") and u.active_status = '1' order by ud.firstname");

    // echo "select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id and u.agency_id = ".$agency_id." and u.user_id = ud.user_id and u.user_id IN (".$sql2String.") and u.active_status = '1' order by ud.firstname<br/>";

    foreach($query as $result){
      // echo "string <br/>";
        $array[] = array('id' => $result['user_id'], 'name' => ($result['firstname'] . ' ' . $result['lastname'] . ' (' . $result['agent_id'] . ')'));
    }
    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_aom_agent_pulldown_bgdn_pending_service_calls($name = '', $selected = '', $aom = '', $params = '', $array = array()) {
    global $database;
    $return = '';

    $agency_id = tep_fetch_order_manager_agency($aom);
    // echo $aom."<-aom3".$agency_id."<-agency_id<br/>";
    $sql2String = "SELECT o.`user_id` FROM ".TABLE_ORDERS." as o,`users` as u WHERE u.`user_id` = o.`user_id` and u.`agency_id`= '$agency_id' and o.`order_type_id`='2' and o.`order_status_id`='1'";
    $query = $database->query("select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id and u.agency_id = ".$agency_id." and u.user_id = ud.user_id and u.user_id IN (".$sql2String.") and u.active_status = '1' order by ud.firstname");
    // echo "select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id and u.agency_id = ".$agency_id." and u.user_id = ud.user_id and u.user_id IN (".$sql2String.") and u.active_status = '1' order by ud.firstname";
    // echo "select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id and u.agency_id = '7572' and u.user_id = ud.user_id and u.user_id IN (SELECT user_id FROM " . TABLE_ORDERS . " WHERE `order_type_id`='2' and `order_completed_status`='0' ) and u.active_status = '2' order by ud.firstname<br/>";
    foreach($query as $result){
        $array[] = array('id' => $result['user_id'], 'name' => ($result['firstname'] . ' ' . $result['lastname'] . ' (' . $result['agent_id'] . ')'));
    }
    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_aom_agent_pulldown_bgdn_pending_removals($name = '', $selected = '', $aom = '', $params = '', $array = array()) {
    global $database;
    $return = '';

    $agency_id = tep_fetch_order_manager_agency($aom);
    // echo $aom."<-aom4 ".$agency_id."<-agency_id<br/>";
    $sql2String = "SELECT o.`user_id` FROM ".TABLE_ORDERS." as o,`users` as u WHERE u.`user_id` = o.`user_id` and u.`agency_id`= '$agency_id' and o.`order_type_id`='3' and o.`order_status_id`='1'";
    $query = $database->query("select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id and u.agency_id = ".$agency_id." and u.user_id = ud.user_id and u.user_id IN (".$sql2String.") and u.active_status = '1' order by ud.firstname");
    //echo "select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id and u.agency_id = '7572' and u.user_id = ud.user_id and u.user_id IN (SELECT user_id FROM " . TABLE_ORDERS . " WHERE `order_type_id`='3' and `order_completed_status`='0' ) and u.active_status = '2' order by ud.firstname<br/>";
    foreach($query as $result){
        $array[] = array('id' => $result['user_id'], 'name' => ($result['firstname'] . ' ' . $result['lastname'] . ' (' . $result['agent_id'] . ')'));
    }
    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}



function tep_draw_order_manager_pulldown($name = '', $selected = '', $params = '', $array = array()) {
    global $database;
    $return = '';
    $query = $database->query("select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '4' and utug.user_id = u.user_id and u.user_id = ud.user_id order by ud.firstname");
    foreach($query as $result){
        $array[] = array('id' => $result['user_id'], 'name' => ($result['firstname'] . ' ' . $result['lastname'] . ' (' . $result['agent_id'] . ')'));
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_promo_pulldown($name = '', $selected = '', $array = array()) {
    global $database;
    $return = '';
    $query = $database->query("select promotional_code_id, code from " . TABLE_PROMOTIONAL_CODES . " order by code");
    foreach($query as $result){
        $array[] = array('id' => $result['promotional_code_id'], 'name' => $result['code']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, '');
}

function tep_draw_order_type_all_pulldown($name = '', $selected = '', $params = '') {
    global $database;
    $return = '';
    $array = array();
    $array[] = array('id' => '', 'name' => 'All');
    $query = $database->query("select order_type_id, name from " . TABLE_ORDER_TYPES . " order by name");
    foreach($query as $result){
        $array[] = array('id' => $result['order_type_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_user_pulldown($name = '', $selected = '', $params = '', $array = array(), $ignore_user = '') {
    global $database;
    $return = '';
    $query = $database->query("select user_id, email_address from " . TABLE_USERS . " where active_status = '1' AND users_status = '1' " . ((!empty($ignore_user)) ? " and user_id != '" . $ignore_user . "' " : '') . " order by email_address");
    foreach($query as $result){
        $array[] = array('id' => $result['user_id'], 'name' => $result['email_address']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_get_county_name($id) {
    global $database;
    $query = $database->query("select name from " . TABLE_COUNTYS . " where county_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['name'];
}

function tep_get_state_name($id) {
    global $database;
    $query = $database->query("select name from " . TABLE_STATES . " where state_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['name'];
}

function tep_get_payment_type_name($id) {
    global $database;
    $query = $database->query("select name from " . TABLE_BILLING_METHODS . " where billing_method_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['name'];
}

function tep_get_default_billing_method($user_id) {
    global $database;
    $query = $database->query("select billing_method_id from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['billing_method_id'];
}

function tep_get_service_level_id($user_id) {
    global $database;
    $query = $database->query("select service_level_id from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['service_level_id'];
}

function tep_get_agency_service_level_id($agency_id) {
    global $database;
    $query = $database->query("select service_level_id from " . TABLE_AGENCYS . " where agency_id = '" . $agency_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['service_level_id'];
}

function tep_get_agency_billing_method_id($agency_id) {
    global $database;
    $query = $database->query("select billing_method_id from " . TABLE_AGENCYS . " where agency_id = '" . $agency_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['billing_method_id'];
}

function tep_get_agency_name($agency_id) {
    global $database;
    $query = $database->query("select name, office from " . TABLE_AGENCYS . " where agency_id = '" . $agency_id . "' limit 1");
    $result = $database->fetch_array($query);
    if (!empty($result['office'])) {
        return "{$result['name']} ({$result['office']})";
    } else {
        return $result['name'];
    }
}
function tep_get_aom_agency_name($agency_id) {
    global $database;
    $query = $database->query("select name, office from " . TABLE_AGENCYS . " where agency_id = '" . $agency_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['name'];
}

function tep_get_user_name($user_id) {
    global $database;
    $query = $database->query("select firstname, lastname from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $user_id . "' limit 1");
    $result = $database->fetch_array($query);
    if (!empty($result['firstname']) && !empty($result['lastname'])) {
        return $result['lastname'] . ', ' . $result['firstname'];
    } else {
        return $result['lastname'] . $result['firstname'];
    }
}

function tep_get_user_group_name($group_id) {
    global $database;
    $query = $database->query("select name from " . TABLE_USER_GROUPS . " where user_group_id = '" . $group_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['name'];
}

function tep_get_agent_id($user_id) {
    global $database;
    $query = $database->query("select agent_id from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['agent_id'];
}

function tep_get_total_orders() {
    global $database, $user;
    $query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user->fetch_user_id() . "'");
    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_get_active_orders() {
    global $database, $user;
    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a where o.user_id = '" . $user->fetch_user_id() . "' and o.address_id = a.address_id and o.order_status_id != '3' and o.order_status_id != '4'");
    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_get_active_addresses() {
    global $database, $user;
    $query = $database->query("select count(a.address_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a where o.user_id = '" . $user->fetch_user_id() . "' and o.address_id = a.address_id and (a.status = '2' or a.status = '3')");
    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_get_pending_installs() {
    global $database, $user;
    $query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user->fetch_user_id() . "' and order_status_id = '1'");
    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_get_schedualed_installs() {
    global $database, $user;
    $query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user->fetch_user_id() . "'");
    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_draw_state_pulldown_bgdn($name = '', $selected = '', $params = '', $state_array = array(array('id' => '', 'name' => 'Please Select'))) {
    global $database;
    $return = '';

    if (!empty($selected)) {
        //$state_array = array();
    }
    $query = $database->query("select state_id, name from " . TABLE_STATES . " order by name");

    foreach($query as $result){
        $state_array[] = array('id' => $result['state_id'], 'name' => $result['name']);
    }
    if(empty($selected)){
		$queryC = $database->query("select  c.value from " . TABLE_CONFIGURATION . " c where c.configuration_id = 47");
		$items_resultC = $database->fetch_array($queryC);
		$selected = $items_resultC['value'];
	}
    return array('name'=>$name, 'contents'=>$state_array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_state_pulldown($name = '', $selected = '', $params = '', $state_array = array(array('id' => '', 'name' => 'Please Select'))) {
    global $database;
    $return = '';

    if (!empty($selected)) {
        //$state_array = array();
    }
    $query = $database->query("select state_id, name from " . TABLE_STATES . " order by name");

    foreach($query as $result){
        $state_array[] = array('id' => $result['state_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $state_array, $selected, $params);
}

function tep_get_invoice_status_string($id) {
    /*
      switch ($id) {
      case '1': return 'Current';
      case '2': return 'None Oweing';
      case '3': return 'Paid';
      case '4': return 'Partial Paid';
      case '5': return 'Not Paid';
      case '6': return 'Overdue';
      case '7': return 'Overdue (Partial Paid)';
      }
     */
    if ($id == 1) {
        return 'Current';
    } elseif ($id == 2 || $id == 3) {
        return 'Paid';
    } elseif ($id == 4 || $id == 5 || $id == 6 || $id == 7) {
        return 'Overdue';
    }
}

function tep_draw_invoice_status_pulldown($name = '', $selected = '', $params = '', $array = array()) {
    global $database;
    $already_used = array();
    for ($i = 1; $i <= 7; $i++) {
        $name = tep_get_invoice_status_string($i);
        if (!in_array($name, $already_used)) {
            $already_used[] = $name;
            $array[] = array('id' => $i, 'name' => tep_get_invoice_status_string($i));
        }
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_discount_type_pulldown($name = '', $selected = '') {
    global $database;
    $return = '';
    $array = array();
    $array[] = array('id' => '1', 'name' => 'Amount');
    $array[] = array('id' => '2', 'name' => 'Percentage');
    $count = count($array);
    $n = 0;
    while ($n < $count) {
        $result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
        $n++;
    }
    return tep_generate_pulldown_menu($name, $result_array, $selected);
}

function tep_draw_personalized_type_pulldown($name = '', $selected = '', $params = '') {
    global $database;
    $return = '';
    $array = array();
    $array[] = array('id' => '0', 'name' => 'No');
    $array[] = array('id' => '1', 'name' => 'Yes');
    $count = count($array);
    $n = 0;
    while ($n < $count) {
        $result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
        $n++;
    }
    return tep_generate_pulldown_menu($name, $result_array, $selected, $params);
}

function tep_draw_tracking_method_type_pulldown($name = '', $selected = '', $params = '') {
    global $database;
    $return = '';
    $array = array();
    $array[] = array('id' => '0', 'name' => 'None');
    $array[] = array('id' => '1', 'name' => 'Unique Code');
    $count = count($array);
    $n = 0;
    while ($n < $count) {
        $result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
        $n++;
    }
    return tep_generate_pulldown_menu($name, $result_array, $selected, $params);
}

function tep_draw_credit_card_type_pulldown($name = '', $selected = '', $params = '') {
    $return = '';
    $array = array();
    $array[] = array('id' => 'VISA', 'name' => 'Visa');
    $array[] = array('id' => 'MASTERCARD', 'name' => 'MasterCard');
    //$array[] = array('id' => 'DISCOVER', 'name' => 'Discover');
    //$array[] = array('id' => 'NOVA', 'name' => 'Nova');
    $array[] = array('id' => 'AMEX', 'name' => 'American Express');
    //$array[] = array('id' => 'DINERS', 'name' => 'Diners');
    //$array[] = array('id' => 'EUROCARD', 'name' => 'EuroCard');
    $count = count($array);
    $n = 0;
    while ($n < $count) {
        $result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
        $n++;
    }
    return tep_generate_pulldown_menu($name, $result_array, $selected, $params);
}

function tep_draw_credit_card_type_pulldown_bgdn($name = '', $selected = '', $params = '') {
    $return = '';
    $array = array();
    $array[] = array('id' => 'VISA', 'name' => 'Visa');
    $array[] = array('id' => 'MASTERCARD', 'name' => 'MasterCard');
    //$array[] = array('id' => 'DISCOVER', 'name' => 'Discover');
    //$array[] = array('id' => 'NOVA', 'name' => 'Nova');
    $array[] = array('id' => 'AMEX', 'name' => 'American Express');
    //$array[] = array('id' => 'DINERS', 'name' => 'Diners');
    //$array[] = array('id' => 'EUROCARD', 'name' => 'EuroCard');
    $count = count($array);
    $n = 0;
    while ($n < $count) {
        $result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
        $n++;
    }
    return array('name'=>$name, 'contents'=>$result_array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_month_pulldown($name = '', $selected = '', $params = '', $result_array = array()) {
    $n = 1;
    $selected = (int) $selected;
    while ($n <= 12) {
        $result_array[] = array('id' => $n, 'name' => date("M", mktime(0, 0, 0, $n, 1)));
        $n++;
    }
    return tep_generate_pulldown_menu($name, $result_array, $selected, $params);
}

function tep_draw_day_pulldown($name = '', $selected = '', $params = '') {
    $n = 1;
    $selected = (int) $selected;
    while ($n <= 31) {
        $result_array[] = array('id' => $n, 'name' => $n);
        $n++;
    }
    return tep_generate_pulldown_menu($name, $result_array, $selected, $params);
}

function tep_draw_year_pulldown($name = '', $selected = '', $params = '', $result_array = array()) {
    $n = 0;
    $selected = (int) $selected;
    $year = date("Y");
    while ($n < 7) {
        $result_array[] = array('id' => ($year + $n), 'name' => ($year + $n));
        $n++;
    }
    return tep_generate_pulldown_menu($name, $result_array, $selected, $params);
}

function tep_draw_backward_year_pulldown($name = '', $selected = '', $params = '', $result_array = array()) {
    $n = 0;
    $selected = (int) $selected;
    $year = date("Y");
    while ($n < 5) {
        $result_array[] = array('id' => ($year - $n), 'name' => ($year - $n));
        $n++;
    }

    return tep_generate_pulldown_menu($name, $result_array, $selected, $params);
}

function tep_draw_billing_method_pulldown($name = '', $selected = '', $params = '', $force = true, $billing_method_id = '', $array = array()) {
    global $database, $user;
    if (empty($selected)) {
        $selected = $user->fetch_billing_method_id();
    }
    $return = '';
    $str = "select billing_method_id, name from " . TABLE_BILLING_METHODS . (($force) ? (" where billing_method_id IN ('" . ((!empty($billing_method_id)) ? $billing_method_id : $user->fetch_billing_method_id()) . "', '1')") : '') . " order by billing_method_id";
    $query = $database->query($str);
    foreach($query as $result){
        $array[] = array('id' => $result['billing_method_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_billing_method_pulldown_bgdn($name = '', $selected = '', $params = '', $force = true, $billing_method_id = '', $array = array()) {
    global $database, $user;
    if (empty($selected)) {
        $selected = $user->fetch_billing_method_id();
    }
    $return = '';
    $str = "select billing_method_id, name from " . TABLE_BILLING_METHODS . (($force) ? (" where billing_method_id IN ('" . ((!empty($billing_method_id)) ? $billing_method_id : $user->fetch_billing_method_id()) . "', '1')") : '') . " order by billing_method_id";
    $query = $database->query($str);
    foreach($query as $result){
        $array[] = array('id' => $result['billing_method_id'], 'name' => $result['name']);
    }
    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_billing_method_pulldown_for_user($user_id, $name = '', $selected = '', $params = '', $force = true, $billing_method_id = '', $array = array()) {
    global $database;

    if (empty($selected)) {
        $selected = tep_get_default_billing_method($user_id);
    }
    $return = '';
    $str = "select billing_method_id, name from " . TABLE_BILLING_METHODS . (($force) ? (" where billing_method_id IN ('" . ((!empty($billing_method_id)) ? $billing_method_id : tep_get_default_billing_method($user_id)) . "', '1')") : '') . " order by billing_method_id";
    $query = $database->query($str);
    foreach($query as $result){
        $array[] = array('id' => $result['billing_method_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_billing_method_pulldown_for_user_bgdn($user_id, $name = '', $selected = '', $params = '', $force = true, $billing_method_id = '', $array = array()) {
    global $database;

    if (empty($selected)) {
        $selected = tep_get_default_billing_method($user_id);
    }
    $return = '';
    $str = "select billing_method_id, name from " . TABLE_BILLING_METHODS . (($force) ? (" where billing_method_id IN ('" . ((!empty($billing_method_id)) ? $billing_method_id : tep_get_default_billing_method($user_id)) . "', '1')") : '') . " order by billing_method_id";
    $query = $database->query($str);
    foreach($query as $result){
        $array[] = array('id' => $result['billing_method_id'], 'name' => $result['name']);
    }
    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_service_level_pulldown($name = '', $selected = '', $params = '', $force = true, $array = array(), $default_user = true) {
    global $database, $user, $language_id;
    if (empty($selected) && $default_user) {
        $selected = $user->fetch_service_level_id();
    }
    $return = '';
    $query = $database->query("select sl.service_level_id, sld.name from " . TABLE_SERVICE_LEVELS . " sl, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where sl.service_level_id = sld.service_level_id and sld.language_id = '" . $language_id . "' " . (($force) ? (" and sl.service_level_id <= '" . $user->fetch_billing_method_id() . "'") : '') . " order by sld.service_level_id");

    foreach($database->fetch_array($query) as $result){
        $array[] = array('id' => $result['service_level_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_group_pulldown($name = '', $selected = '', $params = '', $array = array()) {
    global $database, $user;
    $return = '';
    $query = $database->query("select user_group_id, name from " . TABLE_USER_GROUPS . " order by name");
    foreach($query as $result){
        $array[] = array('id' => $result['user_group_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_aom_group_pulldown($name = '', $selected = '', $params = '', $array = array()) {
    global $database, $user;
    $return = '';
    $query = $database->query("select user_group_id, name from " . TABLE_USER_GROUPS . " where user_group_id = '1' order by name");
    foreach($query as $result){
        $array[] = array('id' => $result['user_group_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_aom_group_pulldown_bgdn($name = '', $selected = '', $params = '', $array = array()) {
    global $database, $user;
    $return = '';
    $query = $database->query("select user_group_id, name from " . TABLE_USER_GROUPS . " where user_group_id = '1' order by name");
    foreach($query as $result){
        $array[] = array('id' => $result['user_group_id'], 'name' => $result['name']);
    }
    return array($name, $array, $selected, $params);
}

function tep_draw_help_group_pulldown($name = '', $selected = '', $params = '') {
    global $database, $user;
    $return = '';
    $array = array();
    $query = $database->query("select hg.help_group_id, hgd.help_group_name from " . TABLE_HELP_GROUPS . " hg, " . TABLE_HELP_GROUPS_DESCRIPTION . " hgd where hg.help_group_id = hgd.help_group_id order by hgd.help_group_name");
    foreach($query as $result){
        $array[] = array('id' => $result['help_group_id'], 'name' => $result['help_group_name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_get_default_language($type = 'language_id') {
    global $database;
    $query = $database->query("select language_id, code from " . TABLE_LANGUAGES . " where language_default = '1' limit 1");
    $result = $database->fetch_array($query);
    return $result[$type];
}

function tep_get_language_code($id) {
    global $database;
    $query = $database->query("select code from " . TABLE_LANGUAGES . " where language_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['code'];
}

function tep_get_language_id($code) {
    global $database;
    $query = $database->query("select id from " . TABLE_LANGUAGES . " where code = '" . $code . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['id'];
}

function tep_language_exists($name) {
    global $database;
    $query = $database->query("select language_id from " . TABLE_LANGUAGES . " where name = '" . $name . "' limit 1");
    $result = $database->fetch_array($query);
    if ($result['language_id'] == NULL) {
        return false;
    } else {
        return true;
    }
}

function tep_draw_language_pulldown($name = '', $selected = '', $params = '') {
    global $database;
    $return = '';
    $array = array();
    $query = $database->query("select language_id, name from " . TABLE_LANGUAGES . "");
    foreach($query as $result){
        $array[] = array('id' => $result['language_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_agency_pulldown($name = '', $selected = '', $params = '', $array = array(), $ignore_agency = '', $show_office = true, $show_test = true, $show_only_active = false) {
    global $database, $user;
    $return = '';

    $query = $database->query("select agency_id, name, office from " . TABLE_AGENCYS . " where parent_agency_id = '' " . ((!empty($ignore_agency)) ? " and agency_id != '" . $ignore_agency . "' " : '') . ((!$show_test) ? " and name NOT LIKE 'test%' and name NOT LIKE 'select%' " : '') . (($show_only_active) ? " and agency_status_id = '1' " : '') . " order by name, office");
    foreach($query as $result){
        $array[] = array('id' => $result['agency_id'], 'name' => $result['name'] . (($show_office && !empty($result['office'])) ? (' (' . $result['office'] . ')') : ''));
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_agency_pulldown_bgdn($name = '', $selected = '', $params = '', $array = array(), $ignore_agency = '', $show_office = true, $show_test = true, $show_only_active = false) {
    global $database, $user;
    $return = '';

    $query = $database->query("select agency_id, name, office from " . TABLE_AGENCYS . " where parent_agency_id = '' " . ((!empty($ignore_agency)) ? " and agency_id != '" . $ignore_agency . "' " : '') . ((!$show_test) ? " and name NOT LIKE 'test%' and name NOT LIKE 'select%' " : '') . (($show_only_active) ? " and agency_status_id = '1' " : '') . " order by name, office");
    foreach($query as $result){
        $array[] = array('id' => $result['agency_id'], 'name' => $result['name'] . (($show_office && !empty($result['office'])) ? (' (' . $result['office'] . ')') : ''));
    }
    return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}

function tep_fetch_default_agency_id() {
    global $database;
    $query = $database->query("select agency_id from " . TABLE_AGENCYS . " order by name limit 1");
    $result = $database->fetch_array($query);

    return $result['agency_id'];
}

function tep_draw_equipment_group_pulldown($name = '', $selected = '', $array = array(), $params = '') {
    global $database, $user;
    $return = '';
    $query = $database->query("select equipment_group_id, name from " . TABLE_EQUIPMENT_GROUPS . " order by name");
    foreach($query as $result){
        $array[] = array('id' => $result['equipment_group_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

/*
function tep_draw_equipment_pulldown($name = '', $selected = '', $group_id = '', $params = '', $array = array(), $equipment_type_id = '') {
    global $database, $user;
    $return = '';
    if (!empty($group_id)) {
        $query = $database->query("select e.equipment_id, e.name from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_EQUIPMENT_GROUPS . " eteg where e.equipment_id = eteg.equipment_id and eteg.equipment_group_id = '" . $group_id . "'" . ((!empty($equipment_type_id)) ? " and e.equipment_type_id = '" . $equipment_type_id . "'" : '') . " order by name");
    } else {
        $query = $database->query("select e.equipment_id, e.name from " . TABLE_EQUIPMENT . " e" . ((!empty($equipment_type_id)) ? " where e.equipment_type_id = '" . $equipment_type_id . "'" : '') . " order by e.name");
    }
    while ($result = $database->fetch_array($query)) {
        $array[] = array('id' => $result['equipment_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

*/

function tep_draw_equipment_pulldown($name = '', $selected = '', $group_id = '', $params = '', $array = array(), $equipment_type_id = '') {
    global $database, $user;
    $return = '';
    if (!empty($group_id)) {
        $query = $database->query("select e.equipment_id, e.name from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_EQUIPMENT_GROUPS . " eteg where e.equipment_id = eteg.equipment_id and e.available=0 and eteg.equipment_group_id = '" . $group_id . "'" . ((!empty($equipment_type_id)) ? " and e.equipment_type_id = '" . $equipment_type_id . "'" : '') . " order by name");
    } else {
        $query = $database->query("select e.equipment_id, e.name from " . TABLE_EQUIPMENT . " e where 1=1 " . ((!empty($equipment_type_id)) ? " and e.equipment_type_id = '" . $equipment_type_id . "'" : '') . " and e.available=0 order by e.name");
    }
    foreach($query as $result){
        $array[] = array('id' => $result['equipment_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_equipment_status_pulldown($name = '', $selected = '', $params = '', $array = array()) {
    global $database, $user;
    $return = '';
    $query = $database->query("select equipment_status_id, equipment_status_name from " . TABLE_EQUIPMENT_STATUSES . " order by equipment_status_id");
    foreach($query as $result){
        $array[] = array('id' => $result['equipment_status_id'], 'name' => $result['equipment_status_name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_preference_group_pulldown($name = '', $selected = '', $array = array()) {
    global $database, $user;
    $return = '';
    $query = $database->query("select agent_preference_group_id, name from " . TABLE_AGENT_PREFERENCE_GROUPS . " order by name");
    foreach($query as $result){
        $array[] = array('id' => $result['agent_preference_group_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected);
}

function tep_draw_page_group_pulldown($name = '', $selected = '', $array = array()) {
    global $database, $language_id;
    $return = '';
    $query = $database->query("select pg.page_group_id, pgd.name from " . TABLE_PAGES_GROUPS . " pg, " . TABLE_PAGES_GROUPS_DESCRIPTION . " pgd where pg.page_group_id = pgd.page_group_id and pgd.language_id = '" . $language_id . "'");
    foreach($query as $result){
        $array[] = array('id' => $result['page_group_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected);
}

function tep_draw_county_pulldown_bgdn($name = '', $selected_state = '', $selected = '', $array = array(array('id' => '', 'name' => 'Please Select')), $params = '', $keepAny = false) {
    global $database;
    $return = '';
    if (!empty($selected) && !$keepAny) {
        $array = array();
    }
    if (empty($selected_state)) {
        $query = $database->query("select c.county_id, c.name as county_name, s.name as state_name from " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where c.state_id = s.state_id order by c.name");
    } else {
        $query = $database->query("select county_id, name as county_name from " . TABLE_COUNTYS . " where state_id = '" . $selected_state . "' order by name");
    }
    foreach($query as $result){
        if (isset($result['state_name'])) {
            $insert_name = $result['county_name'] . ' (' . $result['state_name'] . ')';
        } else {
            $insert_name = $result['county_name'];
        }
        $array[] = array('id' => $result['county_id'], 'name' => $insert_name);
    }
    if(empty($selected)){
		$queryC = $database->query("select  c.value from " . TABLE_CONFIGURATION . " c where c.configuration_id = 46");
		$items_resultC = $database->fetch_array($queryC);
		$selected = $items_resultC['value'];
	}
     return array('name'=>$name, 'contents'=>$array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_county_pulldown($name = '', $selected_state = '', $selected = '', $array = array(array('id' => '', 'name' => 'Please Select')), $params = '', $keepAny = false) {
    global $database;
    $return = '';
    if (!empty($selected) && !$keepAny) {
        $array = array();
    }
    if (empty($selected_state)) {
        $query = $database->query("select c.county_id, c.name as county_name, s.name as state_name from " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where c.state_id = s.state_id order by c.name");
    } else {
        $query = $database->query("select county_id, name as county_name from " . TABLE_COUNTYS . " where state_id = '" . $selected_state . "' order by name");
    }
    foreach($query as $result){
        if (isset($result['state_name'])) {
            $insert_name = $result['county_name'] . ' (' . $result['state_name'] . ')';
        } else {
            $insert_name = $result['county_name'];
        }
        $array[] = array('id' => $result['county_id'], 'name' => $insert_name);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_write_file($filename, $content) {
    //if (is_file($filename)) {
    $fp = fopen($filename, "w");
    if (flock($fp, 2)) { // do an exclusive lock
        fwrite($fp, $content);
        flock($fp, 3); // release the lock
    } else {
        //Write an error.
    }
    fclose($fp);
    //}
}

function tep_delete_file($filename) {
    if (is_file($filename)) {
        unlink($filename);
    }
}

function tep_count_orders_of_type($user_id, $month, $year, $type = '', $find_sort = 'user') {
    global $database;
    $start = mktime(0, 0, -1, $month, 1, $year);
    $end = mktime(0, 0, 0, ($month + 1), 1, $year);

    $where = '';
    if (is_numeric($type)) {
        $where = " and order_type_id = '" . $type . "' ";
    }
    if ($find_sort == 'user') {
        $query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user_id . "' and date_added > '" . $start . "' and date_added < '" . $end . "'" . $where . "");
    } else {
        $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_USERS . " u where u.agency_id = '" . $user_id . "' and u.user_id = o.user_id and o.date_added > '" . $start . "' and o.date_added < '" . $end . "'" . $where . "");
    }
    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_db_remove_dir($dir) {
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (($file != '.') && ($file != '..')) {
                    if (is_file($dir . '/' . $file)) {
                        unlink($dir . '/' . $file);
                        //echo 'delete: ' . $dir.'/'.$file . '<br>';
                    } else {
                        tep_db_remove_dir($dir . '/' . $file);
                    }
                }
            }
            closedir($dh);
        }
    }
    rmdir($dir);
    //echo '<br>Remove: ' . $dir. '<br>';
}

function tep_db_copy_dir($source_dir, $destination_dir) {
    @mkdir($destination_dir);
    if (is_dir($source_dir)) {
        if ($dh = opendir($source_dir)) {
            while (($file = readdir($dh)) !== false) {
                if (($file != '.') && ($file != '..')) {
                    if (is_file($source_dir . '/' . $file)) {
                        copy($source_dir . '/' . $file, $destination_dir . '/' . $file);
                    } else {
                        tep_db_copy_dir($source_dir . '/' . $file, $destination_dir . '/' . $file);
                    }
                }
            }
            closedir($dh);
        }
    }
}

function tep_count_equipment_in_group($group_id) {
    global $database;
    $query = $database->query("select count(equipment_id) as count from " . TABLE_EQUIPMENT . " where equipment_group_id = '" . $group_id . "'");
    $result = $database->fetch_array($query);
    return $result['count'];
}

function tep_count_preferences_in_group($group_id) {
    global $database;
    $query = $database->query("select count(agent_preference_id) as count from " . TABLE_AGENT_PREFERENCES . " where agent_preference_group_id = '" . $group_id . "'");
    $result = $database->fetch_array($query);
    return $result['count'];
}

function tep_is_page($page_url) {
    global $database;
    $query = $database->query("select count(page_id) as count from " . TABLE_PAGES . " where page_url = '" . $page_url . "'");
    $result = $database->fetch_array($query);
    if ($result['count'] == '0') {
        return false;
    } else {
        return true;
    }
}

function tep_page_convert_to_internal_name($page_url) {
    $page_url = str_replace('/', '-', $page_url);
    if (strpos($page_url, '.') !== false) {
        $page_url = substr($page_url, 0, strpos($page_url, '.'));
    }
    return $page_url . '.php';
}

function tep_page_convert_to_external_name($page_url) {
    $page_url = str_replace('/', '-', $page_url);
    if (strpos($page_url, '.') !== false) {
        $page_url = substr($page_url, 0, strpos($page_url, '.'));
    }
    return $page_url;
}

function tep_get_page_url($id) {
    global $database;
    $query = $database->query("select page_url from " . TABLE_PAGES . " where page_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['page_url'];
}

//Check if page has a content file and int hat case it can not be as this would mess things up.  Other basic pages can.
function tep_page_can_be_deleted($page_url) {
    if (is_file(DIR_CONTENT . $page_url)) {
        return false;
    } else {
        if (tep_page_is_magic($page_url)) {
            return false;
        } else {
            return true;
        }
    }
}

//If page is a number then it is determined to be magic.
function tep_page_is_magic($page_url) {
    $page_url = tep_page_convert_to_external_name($page_url);
    if (is_numeric($page_url)) {
        return true;
    } else {
        return false;
    }
}

function tep_create_special_payment_string($cost, $special_type = '') {
    $return_string = '';
    if ($cost > 0) {
        $return_string = 'Cost: $' . $cost;
    } else {
        $return_string = '- No Charge';
    }
    if (!empty($special_type)) {
        $special_explode = explode(',', $special_type);
        $return_string .= ' or ' . $special_explode[0] . ' for $' . number_format($special_explode[1], 2);
    }
    return $return_string;
}

function tep_get_service_level_cost($id) {
    global $database;
    $query = $database->query("select cost from " . TABLE_SERVICE_LEVELS . " where service_level_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['cost'];
}

function tep_equipment_is_tracked($id) {
    global $database;
    $query = $database->query("select tracking_method_id from " . TABLE_EQUIPMENT . " where equipment_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);
    if ($result['tracking_method_id'] == '0') {
        return false;
    } elseif ($result['tracking_method_id'] == '1') {
        return true;
    }
}

function tep_equipment_is_personalized($id) {
    global $database;
    $query = $database->query("select personalized from " . TABLE_EQUIPMENT . " where equipment_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);
    if ($result['personalized'] == '0') {
        return false;
    } elseif ($result['personalized'] == '1') {
        return true;
    }
}

function tep_fetch_county_cost($county_id) {
    global $database;
    $query = $database->query("select sa.surcharge from " . TABLE_COUNTYS . " c, " . TABLE_SERVICE_AREAS . " sa where c.county_id = '" . $county_id . "' and c.service_area_id = sa.service_area_id limit 1");
    $result = $database->fetch_array($query);
    if ($result['surcharge'] != NULL) {
        return $result['surcharge'];
    } else {
        return 0;
    }
}

function tep_fetch_county_service_area_id($county_id) {
    global $database;
    $query = $database->query("select service_area_id from " . TABLE_COUNTYS . " where county_id = '" . $county_id . "' limit 1");
    $result = $database->fetch_array($query);
    if ($result['service_area_id'] != NULL) {
        return $result['service_area_id'];
    } else {
        return 0;
    }
}

function tep_fetch_county_window($county_id) {
    global $database;
    $query = $database->query("select sa.installation_window from " . TABLE_COUNTYS . " c, " . TABLE_SERVICE_AREAS . " sa where c.county_id = '" . $county_id . "' and c.service_area_id = sa.service_area_id limit 1");
    $result = $database->fetch_array($query);
    if ($result['installation_window'] != NULL) {
        return $result['installation_window'];
    } else {
        return 0;
    }
}

function tep_fetch_equipment_cost($array = array(), $service_level_id = '', $address_id = '', $order_type_id = '1') {
    global $database;
    if (!is_array($array)) {
        $array = array();
    }
    //Loop through the optional array and return the total of the equipment.
    $cost = 0;
    foreach ($array as $group_id => $values) {
        if (empty($values) || !is_array($values) || count($values) == 0) {
            continue;
        } else {
            //We have items.  Now work out the cost.
            $query = $database->query("select cost, discount from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $group_id . "' limit 1");
            $result = $database->fetch_array($query);
            if (tep_workout_special_price($group_id, $address_id, $service_level_id, $order_type_id)) {
                $result['cost'] = 0;
            }
            if (empty($result['discount']) || (count($values) == 1)) {
                //Either one or no discount, do normal cost.
                $cost += $result['cost'];
            } else {
                //Over one and a dicount, work it out.
                $discount_explode = explode(',', $result['discount']);
                if (count($values) == $discount_explode[0]) {
                    //Enough, use the discount price.
                    $cost += $discount_explode[1];
                } else {
                    //Not enough, use the normal cost.
                    $cost += count($values)*$result['cost'];
                }
            }
        }
    }
    return $cost;
}

function tep_fetch_equipment_item_name($item_id) {
    global $database;
    $query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $item_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['name'];
}

function tep_fetch_equipment_item_answer_names($item_id, $installer = false) {
    global $database;
    $query = $database->query("select install_equipment_id, remove_equipment_id from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_answer_id = '" . $item_id . "' limit 1");
    $result = $database->fetch_array($query);
    $return = '';
    if (!empty($result['install_equipment_id'])) {
        if (!empty($return)) {
            $return .= (($installer) ? '<br>&nbsp;&nbsp;' : ' and ');
        }
        if ($installer) {
            $return .= '"' . tep_fetch_equipment_item_name($result['install_equipment_id']) . '" was Installed.';
        } else {
            $return .= 'Install ' . tep_fetch_equipment_item_name($result['install_equipment_id']);
        }
    }
    if (!empty($result['remove_equipment_id'])) {
        if (!empty($return)) {
            $return .= (($installer) ? '<br>&nbsp;&nbsp;' : ' and ');
        }
        if ($installer) {
            $return .= '"' . tep_fetch_equipment_item_name($result['remove_equipment_id']) . '" was Removed.';
        } else {
            $return .= 'Remove ' . tep_fetch_equipment_item_name($result['remove_equipment_id']);
        }
    }
    return $return;
}

function tep_fetch_equipment_group_name($item_id) {
    global $database;
    $query = $database->query("select name from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $item_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['name'];
}

function tep_assign_post_to_order($order_id, $equipment_id, $status, $zip4, $quantity = 1, $direction = '1', $number_of_posts = 1) {
    global $database;

    if ($number_of_posts > 1) {
        tep_assign_post_to_order($order_id, $equipment_id, $status, $zip4, $quantity, $direction, $number_of_posts - 1);
    }

    $available_query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment_id . "' limit 1");
    $available_result = $database->fetch_array($available_query);

    //Get the latest equipment_id and if it exists we will assign it.
    for ($n = 0; $n < $quantity; $n++) {
        if ($direction == '1') {
            $equipment_item_id = tep_fetch_next_equipment_item_id($equipment_id, $zip4, '');
            //Only set the status here as its a install.  Don't on removal.
            $address_id = tep_fetch_order_address_id($order_id);
            //tep_add_equipment_item_history($equipment_item_id, $status, '', $order_id, $address_id);
        } else {
            $equipment_item_id = tep_fetch_address_equipment_id($equipment_id, $order_id, $n);
        }
        if ($equipment_item_id !== false) {
            //$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1");
            //$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1");
            $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1");
            //echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1" . '<br>';
            //$database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $equipment_id . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '', '', '', '', '', '1')");
            //$database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $equipment_id . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '', '', '', '', '', '".$direction."')");
            $database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $equipment_id . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '', '', '', '', '', '" . $direction . "')");
            //echo "insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $equipment_id . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '', '', '', '', '', '1')" . '<br>';
        }
    }
}

function tep_fetch_address_equipment_id($equipment_id, $order_id, $offset = 0) {
    global $database;

    $query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $order_id . "' limit 1");
    $result = $database->fetch_array($query);

    $address_id = $result['address_id'];

    $query = $database->query("select equipment_item_id from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . $address_id . "' and equipment_id = '" . $equipment_id . "' limit 1 offset " . $offset);
    $result = $database->fetch_array($query);

    return $result['equipment_item_id'];
}

function tep_assign_removal_item_to_order($order_id, $equipment_id, $equipment_item_id, $status = '2', $number_of_posts = 1) {
    global $database;

    if ($number_of_posts > 1) {
        tep_assign_removal_item_to_order($order_id, $equipment_id, $equipment_item_id, $status, $number_of_posts - 1);
    }

    $available_query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment_id . "' limit 1");
    $available_result = $database->fetch_array($available_query);

    $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1");
    $database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $equipment_id . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '', '', '', '', '', '0')");
}

//Function for assigning equipment to a specified order.  This is used in both checkout proccess and installer confirmation.
function tep_assign_equipment_to_order($order_id, $group_id, $equipment_id, $status, $zip4, $user_id, $address_id = '', $flag = '0', $number_of_posts = 1) {
    global $database;
    if (empty($equipment_id))
        return;
    $answer_query = $database->query("select install_equipment_id, remove_equipment_id from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_answer_id = '" . $equipment_id . "' limit 1");
    $answer_result = $database->fetch_array($answer_query);

    for($i = 0; $i < $number_of_posts; $i++) {
        if ($answer_result['install_equipment_id'] > 0) {

            $available_query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $answer_result['install_equipment_id'] . "' limit 1");
            $available_result = $database->fetch_array($available_query);

            //Get the latest equipment_id and if it exists we will assign it.
            if (($equipment_item_id = tep_fetch_next_equipment_item_id($answer_result['install_equipment_id'], $zip4, $user_id)) !== false) {  //mjp don't check if there is equipment in stock
                $query = $database->query("select cost, discount, name from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $group_id . "' limit 1");
                $result = $database->fetch_array($query);
                //$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1");
                $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1");
                //$database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $answer_result['install_equipment_id'] . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '" . $group_id . "', '" . $result['cost'] . "', '" . $result['discount'] . "', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '1')");
                $database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id, equipment_item_flag) values ('" . $answer_result['install_equipment_id'] . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '" . $group_id . "', '" . $result['cost'] . "', '" . $result['discount'] . "', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '1', '" . $flag . "')");
                //Set the status.
                tep_add_equipment_item_history($equipment_item_id, '1', '', $order_id, $address_id);

                $query = $database->query("select included_equipment_track_id, current_count from " . TABLE_INCLUDED_EQUIPMENT_TRACK . " where equipment_group_id = '" . $group_id . "' and address_id = '" . $address_id . "' limit 1");
                $result = $database->fetch_array($query);

                if (!empty($result['included_equipment_track_id'])) {
                    //$database->query("update " . TABLE_INCLUDED_EQUIPMENT_TRACK . " set current_count = '" . ($result['current_count'] + 1) . "' where included_equipment_track_id = '" . $result['included_equipment_track'] . "' limit 1");
                    $database->query("update " . TABLE_INCLUDED_EQUIPMENT_TRACK . " set current_count = '" . ($result['current_count'] + 1) . "' where included_equipment_track_id = '" . $result['included_equipment_track_id'] . "' limit 1");
                } else {
                    //$database->query("insert into " . TABLE_INCLUDED_EQUIPMENT_TRACK . " (current_count, address_id, equipment_group_id) values ('1', '" . $address_id . "', '" . $group_id . "')");
                    $database->query("insert into " . TABLE_INCLUDED_EQUIPMENT_TRACK . " (current_count, address_id, equipment_group_id) values ('1', '" . $address_id . "', '" . $group_id . "')");
                }
            }
        }
        if ($answer_result['remove_equipment_id'] > 0) {
            $available_query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $answer_result['remove_equipment_id'] . "' limit 1");
            $available_result = $database->fetch_array($available_query);

            $query = $database->query("select cost, discount, name from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $group_id . "' limit 1");
            $result = $database->fetch_array($query);
            //$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1");
            //$database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $answer_result['remove_equipment_id'] . "', '" . '' . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '" . $group_id . "', '" . $result['cost'] . "', '" . $result['discount'] . "', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '0')");
            $database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id, equipment_item_flag) values ('" . $answer_result['remove_equipment_id'] . "', '" . '' . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '" . $group_id . "', '" . $result['cost'] . "', '" . $result['discount'] . "', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '0', '" . $flag . "')");
        }
        if (empty($answer_result['install_equipment_id']) && empty($answer_result['remove_equipment_id'])) {
            $query = $database->query("select cost, discount, name from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $group_id . "' limit 1");
            $result = $database->fetch_array($query);

            //$database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $equipment_id . "', '0', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '0', '" . $group_id . "', '" . $result['cost'] . "', '0', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '0')");
            $database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id, equipment_item_flag) values ('" . $equipment_id . "', '0', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '0', '" . $group_id . "', '" . $result['cost'] . "', '0', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '0', '" . $flag . "')");
        }
    }
}

function tep_reassign_equipment_to_order($order_id, $group_id, $equipment_id, $status, $zip4, $user_id, $address_id = '', $number_of_posts = 1) {
    global $database;

    $contents = '';
    $contents .= $order_id . ' - ' . $group_id . ' - ' . $equipment_id . ' - ' . $status . ' - ' . $zip4 . ' - ' . $user_id . ' - ' . $address_id . "\n";
    $query = $database->query("select count(equipment_to_order_id) as count from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_group_answer_id = '" . $equipment_id . "' and order_id = '" . $order_id . "' limit 1");
    //echo "select count(equipment_to_order_id) as count from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_group_answer_id = '" . $equipment_id . "' and order_id = '" . $order_id . "' limit 1" . "<br>";
    $result = $database->fetch_array($query);
    if ($result['count'] >= $number_of_posts) {
        return;
    } else {
        if (!empty($result['count'])) {
            $number_of_posts = $number_of_posts - $result['count'];
        }
    }
    $answer_query = $database->query("select install_equipment_id, remove_equipment_id from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_answer_id = '" . $equipment_id . "' limit 1");
    $answer_result = $database->fetch_array($answer_query);

    for($i = 0; $i < $number_of_posts; $i++) {
        if ($answer_result['install_equipment_id'] > 0) {

            $available_query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $answer_result['install_equipment_id'] . "' limit 1");
            $available_result = $database->fetch_array($available_query);

            //Get the latest equipment_id and if it exists we will assign it.
            if (($equipment_item_id = tep_fetch_next_equipment_item_id($answer_result['install_equipment_id'], $zip4, $user_id)) !== false) {
                $query = $database->query("select cost, discount, name from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $group_id . "' limit 1");
                $result = $database->fetch_array($query);
                $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1");
                //echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1" . "<br>";
                $database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $answer_result['install_equipment_id'] . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '" . $group_id . "', '" . $result['cost'] . "', '" . $result['discount'] . "', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '1')");
                //echo "insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $answer_result['install_equipment_id'] . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '" . $group_id . "', '" . $result['cost'] . "', '" . $result['discount'] . "', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '1')" . "<br>";

                $query = $database->query("select included_equipment_track_id, current_count from " . TABLE_INCLUDED_EQUIPMENT_TRACK . " where equipment_group_id = '" . $group_id . "' and address_id = '" . $address_id . "' limit 1");
                $result = $database->fetch_array($query);

                if (!empty($result['included_equipment_track_id'])) {
                    $database->query("update " . TABLE_INCLUDED_EQUIPMENT_TRACK . " set current_count = '" . ($result['current_count'] + 1) . "' where included_equipment_track_id = '" . $result['included_equipment_track_id'] . "' limit 1");
                    //echo "update " . TABLE_INCLUDED_EQUIPMENT_TRACK . " set current_count = '" . ($result['current_count'] + 1) . "' where included_equipment_track_id = '" . $result['included_equipment_track_id'] . "' limit 1" . "<br>";
                } else {
                    $database->query("insert into " . TABLE_INCLUDED_EQUIPMENT_TRACK . " (current_count, address_id, equipment_group_id) values ('1', '" . $address_id . "', '" . $group_id . "')");
                    //echo "insert into " . TABLE_INCLUDED_EQUIPMENT_TRACK . " (current_count, address_id, equipment_group_id) values ('1', '" . $address_id . "', '" . $group_id . "')" . "<br>";
                }
            }
        }
        if ($answer_result['remove_equipment_id'] > 0) {
            $available_query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $answer_result['remove_equipment_id'] . "' limit 1");
            $available_result = $database->fetch_array($available_query);

            $query = $database->query("select cost, discount, name from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $group_id . "' limit 1");
            $result = $database->fetch_array($query);
            /* $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1"); */
            $database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $answer_result['remove_equipment_id'] . "', '" . '' . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '" . $group_id . "', '" . $result['cost'] . "', '" . $result['discount'] . "', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '0')");
            //echo "insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $answer_result['remove_equipment_id'] . "', '" . '' . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '" . $group_id . "', '" . $result['cost'] . "', '" . $result['discount'] . "', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '0')" . "<br>";
        }
        if (empty($answer_result['install_equipment_id']) && empty($answer_result['remove_equipment_id'])) {
        /*
          $query = $database->query("select cost, discount, name from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $group_id . "' limit 1");
          $result = $database->fetch_array($query);

          $database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $equipment_id . "', '0', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '0', '" . $group_id . "', '" . $result['cost'] . "', '0', '" . addslashes($result['name']) . "', '" . $equipment_id . "', '0')");
         */
        }
    }
}

function tep_create_warehouse_string($warehouse_id, $prefix = '') {
    $warehouse_string = '';
    if (!empty($prefix)) {
        $prefix .= '.';
    }
    if (!empty($warehouse_id)) {
        if (is_array($warehouse_id)) {
            $warehouse_string = ' and ((';
            $count = count($warehouse_id);
            $n = 0;
            while ($n < $count) {
                if ($n > 0) {
                    $warehouse_string .= ") or (";
                }
                $warehouse_string .= $prefix . "warehouse_id = '" . $warehouse_id[$n] . "'";
                $n++;
            }
            //$warehouse_string = " and FIND_IN_SET(warehouse_id, '" . $string . "') > 0";
            $warehouse_string .= '))';
        } else {
            $warehouse_string = " and " . $prefix . "warehouse_id = '" . $warehouse_id . "'";
        }
    }
    return $warehouse_string;
}

function tep_fetch_next_equipment_item_id($equipment_id, $zip4, $user_id) {
    global $database;
    $warehouse_array = tep_get_sevicing_warehouse($zip4, 'area');
    $user_string = '';
    if (!empty($user_id) && ($user_id != 'none')) {
        $user_string = " and ((user_id = '" . $user_id . "' or (user_id = '0' and agency_id = '0')) or (agency_id = '" . tep_fetch_user_agency_id($user_id) . "' and user_id = '0')) ";
    } elseif ($user_id != 'none') {
        $user_string = " and user_id = '0' ";
    }
    if (tep_fetch_available_equipment_count($equipment_id, $warehouse_array, $user_id) > 0) {
        //Got one.  Now get it.
        $warehouse_string = tep_create_warehouse_string($warehouse_array);
        /*$query = $database->query("select max(equipment_item_id) as max from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $equipment_id . "' and equipment_status_id = '0' " . $warehouse_string . $user_string);*/

		$query = $database->query("SELECT ei.equipment_item_id as max from equipment_items ei LEFT JOIN equipment_items_to_addresses eia ON ei.equipment_item_id=eia.equipment_item_id WHERE NOT EXISTS (SELECT * FROM orders o WHERE o.address_id = eia.address_id AND o.order_status_id IN (1,2)) AND ei.equipment_id = '" . $equipment_id . "' and ei.equipment_status_id = '0' " . $warehouse_string . $user_string." ORDER BY ei.equipment_item_id DESC LIMIT 1");

        $result = $database->fetch_array($query);
        return $result['max'];
    } else {
        $warehouse_array = tep_get_sevicing_warehouse($zip4, 'shared');
        if (tep_fetch_available_equipment_count($equipment_id, $warehouse_array, $user_id) > 0) {
            //Got one.  Now get it.
            $warehouse_string = tep_create_warehouse_string($warehouse_array);
            //$query = $database->query("select max(equipment_item_id) as max from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $equipment_id . "' and equipment_status_id = '0' " . $warehouse_string . $user_string);
            $query = $database->query("SELECT ei.equipment_item_id as max from equipment_items ei LEFT JOIN equipment_items_to_addresses eia ON ei.equipment_item_id=eia.equipment_item_id WHERE NOT EXISTS (SELECT * FROM orders o WHERE o.address_id = eia.address_id AND o.order_status_id IN (1,2)) AND ei.equipment_id = '" . $equipment_id . "' and ei.equipment_status_id = '0' " . $warehouse_string . $user_string." ORDER BY ei.equipment_item_id DESC LIMIT 1");
			$result = $database->fetch_array($query);
            return $result['max'];
        } else {
            return false;
        }
    }
}

function tep_get_item_warehouse($item_id) {
    global $database;
    $query = $database->query("select warehouse_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_item_id = '" . $item_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['warehouse_id'];
}

function tep_get_warehouse_name($warehouse_id) {
    global $database;
    $query = $database->query("select name from " . TABLE_WAREHOUSES_DESCRIPTION . " where warehouse_id = '" . $warehouse_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['name'];
}

function tep_get_equipment_type_name($id) {
    global $database;
    $query = $database->query("select equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " where equipment_type_id = '" . $id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['equipment_type_name'];
}

function tep_get_equipment_assigned_to_order($order_id) {
    global $database;
    $return_array = array();
    $query = $database->query("select equipment_id, equipment_item_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, method_id, equipment_group_answer_id, equipment_item_flag from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $order_id . "'");

    foreach($query as $result){
        $location_query = $database->query("select equipment_location from " . TABLE_EQUIPMENT . " where equipment_id = '" . $result['equipment_id'] . "' limit 1");
        $location_result = $database->fetch_array($location_query);

        if (!array_key_exists($result['equipment_group_id'], $return_array)) {
            $return_array[$result['equipment_group_id']] = array('items' => array(),
                'name' => $result['equipment_group_name'],
                'cost' => $result['cost'],
                'location' => $location_result['equipment_location'],
                'discount' => $result['discount']);
        }
        $return_array[$result['equipment_group_id']]['items'][] = array('id' => $result['equipment_id'],
            'status' => $result['equipment_status_id'],
            'reference_code' => tep_fetch_equipment_item_reference_code($result['equipment_item_id']),
            'warehouse_id' => tep_get_item_warehouse($result['equipment_item_id']),
            'equipment_item_id' => $result['equipment_item_id'],
            'method_id' => $result['method_id'],
            'equipment_group_answer_id' => $result['equipment_group_answer_id'],
            'equipment_group_id' => $result['equipment_group_id'],
            'name' => $result['equipment_name'],
            'location' => $location_result['equipment_location'],
            'flag' => $result['equipment_item_flag']);
    }
    return $return_array;
}

function tep_get_equipment_assigned_to_address($address_id) {
    global $database;
    $return_array = array();
    $query = $database->query("select eita.equipment_id, eita.equipment_item_id, e.name as equipment_name, e.equipment_type_id, e.equipment_location, et.equipment_type_name from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TYPES . " et where eita.address_id = '" . $address_id . "' and eita.equipment_status_id = '2' and eita.equipment_id = e.equipment_id and e.equipment_type_id = et.equipment_type_id");
    foreach($query as $result)
    {
        if (!isset($return_array[$result['equipment_type_id']])) {
            $return_array[$result['equipment_type_id']] = array('items' => array(),
                'name' => $result['equipment_type_name'],
                'location' => $result['equipment_location']);
        }
        $return_array[$result['equipment_type_id']]['items'][] = array('id' => $result['equipment_id'],
            'reference_code' => tep_fetch_equipment_item_reference_code($result['equipment_item_id']),
            'warehouse_id' => tep_get_item_warehouse($result['equipment_item_id']),
            'equipment_item_id' => $result['equipment_item_id'],
            'method_id' => '0',
            'name' => $result['equipment_name'],
            'location' => $result['equipment_location']);
    }
    return $return_array;
}

function tep_get_other_equipment_assigned_to_order($order_id) {
    global $database;
    $return_array = array();
    $query = $database->query("select equipment_id, equipment_item_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, method_id, equipment_group_answer_id, equipment_item_flag from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $order_id . "'");
    foreach($query as $result){
        $return_array[] = array('id' => $result['equipment_id'],
            'status' => $result['equipment_status_id'],
            'reference_code' => tep_fetch_equipment_item_reference_code($result['equipment_item_id']),
            'warehouse_id' => tep_get_item_warehouse($result['equipment_item_id']),
            'equipment_item_id' => $result['equipment_item_id'],
            'method_id' => $result['method_id'],
            'equipment_group_answer_id' => $result['equipment_group_answer_id'],
            'equipment_group_id' => $result['equipment_group_id'],
            'name' => $result['equipment_name'],
            'flag' => $result['equipment_item_flag']);
    }
    return $return_array;
}

//Function to check if promo code exists, is valid and has not already been used by customer.
function tep_promotional_code_is_valid($code) {
    global $database, $user;
    $return = false;
    $query = $database->query("select promotional_code_id, valid_start, valid_end, max_number from " . TABLE_PROMOTIONAL_CODES . " where code = '" . $code . "' limit 1");
    $result = $database->fetch_array($query);
    if ($result['promotional_code_id'] != NULL) {
        $time = time();
        if (($time > $result['valid_start']) && ($time < $result['valid_end'])) {
            $uses_query = $database->query("select count(user_id) as count from " . TABLE_PROMOTIONAL_CODES_TO_USERS . " where user_id = '" . $user->fetch_user_id() . "' and promotional_code_id = '" . $result['promotional_code_id'] . "'");
            $uses_result = $database->fetch_array($uses_query);
            if ($uses_result['count'] < $result['max_number']) {
                $return = true;
            }
        }
    }
    return $return;
}

function tep_fetch_promotional_details($promotional_code_id) {
    global $database, $user;
    $return = false;
    $query = $database->query("select discount_type, discount_amount from " . TABLE_PROMOTIONAL_CODES . " where promotional_code_id = '" . $promotional_code_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result;
}

function tep_fetch_promotional_id($code) {
    global $database;
    $query = $database->query("select promotional_code_id from " . TABLE_PROMOTIONAL_CODES . " where code = '" . $code . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['promotional_code_id'];
}

//Function to assign a code to an order.  This will also assign to the user.
function tep_assign_promotional_code_to_order($order_id, $promotional_code_id) {
    global $database, $user;
    $database->query("insert into " . TABLE_PROMOTIONAL_CODES_TO_ORDERS . " (promotional_code_id, order_id) values ('" . $promotional_code_id . "', '" . $order_id . "')");
    $database->query("insert into " . TABLE_PROMOTIONAL_CODES_TO_USERS . " (promotional_code_id, user_id) values ('" . $promotional_code_id . "', '" . $user->fetch_user_id() . "')");
}

function tep_get_all_get_params($exclude_array = '', $include_array = array()) {

    if (!is_array($exclude_array))
        $exclude_array = array();

    $get_url = '';
    $combined = array_merge($_GET, $include_array);

    if (is_array($combined) && (sizeof($combined) > 0)) {
        reset($_GET);
        while (list($key, $value) = each($combined)) {
            if (is_array($value)) {
                reset($value);
                while (list($sub_key, $sub_value) = each($value)) {
                    if (($sub_key != 'error') && (!in_array($sub_key, $exclude_array)) && ($sub_key != 'x') && ($sub_key != 'y')) {
                        $get_url .= $key . '[' . $sub_key . ']' . '=' . rawurlencode(stripslashes($sub_value)) . '&';
                    }
                }
            } else {
                if (($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y')) {
                    $get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';
                }
            }
        }
    }

    return $get_url;
}

function tep_db_get_order_promotional_code($order_id) {
    global $database;
    $query = $database->query("select pc.code from " . TABLE_PROMOTIONAL_CODES . " pc, " . TABLE_PROMOTIONAL_CODES_TO_ORDERS . " pcto where pcto.order_id = '" . $order_id . "' and pcto.promotional_code_id = pc.promotional_code_id limit 1");
    $result = $database->fetch_array($query);
    if ($result != NULL) {
        return $query['code'];
    } else {
        return '';
    }
}

function tep_fetch_order_history($order_id) {
    global $database;
    $history_result = array();
    $query = $database->query("select oh.order_status_id, oh.date_added, oh.user_notified, oh.comments, os.order_status_name from " . TABLE_ORDERS_HISTORY . " oh, " . TABLE_ORDERS_STATUSES . " os where oh.order_id = '" . $order_id . "' and oh.order_status_id = os.order_status_id order by oh.date_added");
    foreach($query as $result){
        $history_result[] = array('order_status_id' => $result['order_status_id'],
            'date_added' => $result['date_added'],
            'user_notified' => $result['user_notified'],
            'comments' => $result['comments'],
            'order_status_name' => $result['order_status_name']);
    }
    return $history_result;
}

function tep_create_order_history($order_id, $status_id, $comments, $user_notified = false) {
    global $database;
    //$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $order_id . "', '" . $order_status_id . "', '" . time() . "', '" . (($user_notified) ? '1' : '0') . "', '" . addslashes(stripslashes($comments)) . "')");
    $database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $order_id . "', '" . $status_id . "', '" . time() . "', '" . (($user_notified) ? '1' : '0') . "', '" . addslashes(stripslashes($comments)) . "')");
    //echo "insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $order_id . "', '" . $status_id . "', '" . time() . "', '" . (($user_notified) ? '1' : '0') . "', '" . addslashes(stripslashes($comments)) . "')". '<br>';
}

function tep_fetch_address_details($address_id) {
    global $database;
    $query = $database->query("select house_number, street_name, city, zip, zip4, adc_number, cross_street_directions, state_id, county_id, installer_comments as address_comments from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
    $result = $database->fetch_array($query);
    //error_log("SQL: select house_number, street_name, city, zip, zip4, adc_number, cross_street_directions, state_id, county_id, installer_comments as address_comments from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1,\nRESULT: ".var_export($query,true)."\nDATA: ".var_export($result,true));
	//error_log("BACKTRACE: ".var_export(debug_backtrace(),true));
	if($query!==false and is_array($result))
	{
	    $result['street_name'] = (isset($result['street_name']))?ucfirst($result['street_name']):'';
	    $result['city'] = (isset($result['city']))?ucfirst($result['city']):'';
	    $result['state_name'] = (isset($result['state_id']))?tep_get_state_name($result['state_id']):'';
	    $result['county_name'] = (isset($result['county_id']))?tep_get_county_name($result['county_id']):'';
	}
	else
	{
		$result['street_name'] = '';
	    $result['city'] = '';
	    $result['state_name'] = '';
	    $result['county_name'] = '';
	}
    return $result;
}

function tep_draw_service_areas_pulldown($name = '', $selected = '', $params = '', $service_area_array = array()) {
    global $database;
    $return = '';
    if (empty($selected) && empty($service_area_array)) {
        $service_area_array[] = array('id' => '', 'name' => 'Please Select');
    }
    $query = $database->query("select service_area_id, name from " . TABLE_SERVICE_AREAS . " order by name");

    foreach($query as $result){
        $service_area_array[] = array('id' => $result['service_area_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $service_area_array, $selected, $params);
}

function tep_draw_notify_user_pulldown($name = '', $selected = '') {
    global $database;
    $return = '';
    $array[] = array('id' => '0', 'name' => 'No');
    $array[] = array('id' => '1', 'name' => 'Yes');
    return tep_generate_pulldown_menu($name, $array, $selected);
}

function tep_draw_warehouse_availability_pulldown($name = '', $selected = '') {
    global $database;
    $return = '';
    $array[] = array('id' => '0', 'name' => 'Shared');
    $array[] = array('id' => '1', 'name' => 'Not Shared');
    return tep_generate_pulldown_menu($name, $array, $selected);
}

function tep_draw_today_tomorrow_pulldown($name = '', $selected = '', $params = '') {
    global $database;
    $return = '';
    $array[] = array('id' => 'today', 'name' => 'Today');
    $array[] = array('id' => 'tomorrow', 'name' => 'Tomorrow');
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_draw_detailed_overview_pulldown($name = '', $selected = '', $params = '') {
    global $database;
    $return = '';
    $array[] = array('id' => 'overview', 'name' => 'Overview');
    $array[] = array('id' => 'detailed', 'name' => 'Detailed');
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

function tep_equipment_group_has_items($group_id, $user_id, $warehouse_data = array(), $address_id = '', $exclude_array = array(), $include_out = false) {
    global $database;
    $found = false;
    $service_level_id = tep_get_service_level_id($user_id);
    $query = $database->query("select equipment_group_answer_id, install_equipment_id, remove_equipment_id from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $group_id . "' and service_level_id <= '" . ($service_level_id). "'"); //$service_level_id +1
    // echo "4 select equipment_group_answer_id, install_equipment_id, remove_equipment_id from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $group_id . "' and service_level_id <= '" . ($service_level_id) . "' <br/>";
    $i=0;
    // echo "exclude_array ".count($exclude_array)."- group_id ".$group_id."<br/>";
    foreach($query as $result){
      //echo "exclude_array ".count($exclude_array)."-".$group_id."<br/>";
        if (in_array($result['equipment_group_answer_id'], $exclude_array)) {
          //var_dump(count($exclude_array));
          // echo "install_equipment_id :- ".$result['install_equipment_id']." service_level_id :- ".$group_id."<br/>";

            continue;
        }
        // echo $result['remove_equipment_id'] . ' - ' . tep_equipment_is_at_address($address_id, $result['remove_equipment_id']) . '<br>';
        // echo "function output ".tep_fetch_available_equipment_count($result['install_equipment_id'], $warehouse_data, $user_id, $include_out)."<br/>";
        // echo empty($address_id)."<br/>";
        // echo $result['remove_equipment_id']."<br/>";
        // echo tep_equipment_is_at_address($address_id, $result['remove_equipment_id'])."<br/>";
        // echo "1 install_equipment_id :- ".$result['install_equipment_id']." group_id :- ".$group_id."<br/>";

        if ((tep_fetch_available_equipment_count($result['install_equipment_id'], $warehouse_data, $user_id, $include_out) > 0) && (empty($address_id) || ($result['remove_equipment_id'] == '0') || (tep_equipment_is_at_address($address_id, $result['remove_equipment_id'])))) {
            $i++;
            // echo "2 install_equipment_id :- ".$result['install_equipment_id']." group_id :- ".$group_id."<br/>";

          // echo "i =".$i."<br/>";


            $found = true;
            break;
        }
    }

    if ($found) {
        return true;
    } else {
        return false;
    }
}

function tep_fetch_installed_equipment_array($type_id, $address_id) {
    global $database;
    $return_array = array();
    $query = $database->query("select e.equipment_id from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.address_id = '" . $address_id . "' and eita.equipment_status_id = '2' and eita.equipment_id = e.equipment_id and e.equipment_type_id = '" . $type_id . "'");
    foreach($query as $result){
        $return_array[] = $result['equipment_id'];
    }
    return $return_array;
}

function tep_equipment_type_has_items($type_id, $user_id, $warehouse_data = array(), $address_id = '', $exclude_array = array()) {
    global $database;

    $found = false;
    $service_level_id = tep_get_service_level_id($user_id);
    $exclude_array = tep_fetch_installed_equipment_array($type_id, $address_id);
    $query = $database->query("select equipment_id from " . TABLE_EQUIPMENT . " where equipment_type_id = '" . $type_id . "'");
    foreach($query as $result){
        if (in_array($result['equipment_id'], $exclude_array)) {
            continue;
        }
        //echo $result['remove_equipment_id'] . ' - ' . tep_equipment_is_at_address($address_id, $result['remove_equipment_id']) . '<br>';
        if (tep_fetch_available_equipment_count($result['equipment_id'], $warehouse_data, $user_id) > 0) {
            $found = true;
            break;
        }
    }

    if ($found) {
        return true;
    } else {
        return false;
    }
}

function tep_equipment_is_at_address($address_id, $equipment_id) {
    global $database;
    $query = $database->query("select count(equipment_item_to_address_id) as count from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . $address_id . "' and equipment_id = '" . $equipment_id . "' and equipment_status_id = '2' limit 1");
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function tep_fetch_equipment_at_address($equipment_id, $address_id) {

}

//New equipment counting functions
function tep_fetch_total_equipment_count($equipment_id = '', $warehouse_id = '') {
    global $database;
    $query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where '0' = '0'" . ((!empty($equipment_id)) ? " and equipment_id = '" . $equipment_id . "'" : '') . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'" : ''));
    $result = $database->fetch_array($query);
    return $result['count'];
}

//unused equipment counting function by agency
function tep_fetch_total_unused_agency($date, $agency) {
    global $database;
    $sql = "SELECT " . TABLE_USERS . ".agency_id, " . TABLE_AGENCYS . ".name, " . TABLE_AGENCYS . ".office, max(date_schedualed) as max from " . TABLE_USERS . " LEFT JOIN ";
    $sql .= TABLE_ORDERS . " on " . TABLE_USERS . ".user_id = " . TABLE_ORDERS . ".user_id INNER JOIN " . TABLE_AGENCYS . " ON " . TABLE_USERS . ".agency_id = " . TABLE_AGENCYS;
    $sql .= ".agency_id WHERE " . TABLE_AGENCYS . ".agency_id =" . $agency;
    $query = $database->query($sql);

    foreach($query as $result){
        if ($result['max'] < $date) {
            $return = $result['name'] . " " . $result['office'];
            return $return;
        }
    }
    return "";
}

//New unused equipment counting functions
function tep_fetch_total_equipment_status_count($date, $status, $warehouse_id = '', $equipment_type_id = '', $equipment_id = '', $equipment_item_id = '') {
    global $database;
    $sql = "";
    $sqland = "";
    $sqlgrp = "";

    if (!empty($warehouse_id)) {
        $sqland = " AND " . TABLE_EQUIPMENT_ITEMS . ".warehouse_id = " . $warehouse_id;
        $sqlgrp = TABLE_EQUIPMENT_ITEMS . ".warehouse_id";
    }
    if (!empty($equipment_type_id)) {
        $sqland .= " AND " . TABLE_EQUIPMENT . ".equipment_type_id = " . $equipment_type_id;
        if (!empty($sqlgrp))
            $sqlgrp.= ", ";
        $sqlgrp .= TABLE_EQUIPMENT . ".equipment_type_id";
    }
    if (!empty($equipment_id)) {
        $sqland .= " AND " . TABLE_EQUIPMENT_ITEMS . ".equipment_id = " . $equipment_id;
        if (!empty($sqlgrp))
            $sqlgrp.= ", ";
        $sqlgrp .= TABLE_EQUIPMENT_ITEMS . ".equipment_id";
    }
    if (!empty($equipment_item_id)) {
        $sqland .= " AND " . TABLE_EQUIPMENT_ITEMS . ".equipment_item_id = " . $equipment_item_id;
        if (!empty($sqlgrp))
            $sqlgrp.= ", ";
        $sqlgrp .= TABLE_EQUIPMENT_ITEMS . ".equipment_item_id";
    }

    $sql = "SELECT count(" . TABLE_EQUIPMENT_ITEMS . ".equipment_item_id) as count";
    $sql .= " from " . TABLE_EQUIPMENT_ITEMS . " INNER JOIN " . TABLE_EQUIPMENT . " on " . TABLE_EQUIPMENT_ITEMS . ".equipment_id = " . TABLE_EQUIPMENT . ".equipment_id ";
    $sql .= " WHERE " . TABLE_EQUIPMENT_ITEMS . ".equipment_status_id = " . $status . " " . $sqland;
    if (!empty($sqlgrp))
        $sql.= ' GROUP BY ' . $sqlgrp;

    $query = $database->query($sql);
    $result = $database->fetch_array($query);
    if (!$result)
        return '0';
    else
        return $result['count'];
}

function tep_fetch_user_agency_id($user_id) {
    global $database;

    $query = $database->query("select agency_id from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['agency_id'];
}

function tep_fetch_available_equipment_count($equipment_id = '', $warehouse_id = '', $user_id = '', $include_out = false) {
    global $database;
    $warehouse_string = '';
    $user_string = '';
    if (!empty($warehouse_id)) {
        $warehouse_string = tep_create_warehouse_string($warehouse_id, 'ei');
    }
    if (!empty($user_id) && ($user_id != 'none')) {
        $user_string = " and (e.personalized = '0' or ((ei.user_id = '" . $user_id . "') or (ei.agency_id = '" . tep_fetch_user_agency_id($user_id) . "' and ei.user_id = '0'))) ";
        $i=0;
    } elseif ($user_id != 'none') {
        $user_string = " and ei.user_id = '0' ";
        $i=1;
    }
    //echo "user_string =".$user_string."<br/>";
    $query = $database->query("select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT . " e where ei.equipment_id = e.equipment_id " . ((!$include_out) ? " and ei.equipment_status_id = '0' " : '') . ((!empty($equipment_id)) ? " and ei.equipment_id = '" . $equipment_id . "'" : '') . ((!empty($user_string)) ? $user_string : '') . $warehouse_string);
    // echo "1x select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT . " e where ei.equipment_id = e.equipment_id " . ((!$include_out) ? " and ei.equipment_status_id = '0' " : '') . ((!empty($equipment_id)) ? " and ei.equipment_id = '" . $equipment_id . "'" : '') . ((!empty($user_string)) ? $user_string : '') . $warehouse_string."<br/>";
    $result = $database->fetch_array($query);
    // echo $equipment_id.'-'.$result['count']." select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT . " e where ei.equipment_id = e.equipment_id " . ((!$include_out) ? " and ei.equipment_status_id = '0' " : '') . ((!empty($equipment_id)) ? " and ei.equipment_id = '" . $equipment_id . "'" : '') . ((!empty($user_string)) ? $user_string : '') . $warehouse_string."<br/>";
    return $result['count'];
}

function tep_fetch_actual_available_equipment_count($equipment_id = '', $warehouse_id = '') {
    global $database;
    $warehouse_string = '';
    $user_string = '';
    if (!empty($warehouse_id)) {
        $warehouse_string = tep_create_warehouse_string($warehouse_id, 'ei');
    }

    $query = $database->query("select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT . " e where ei.equipment_status_id = '0' and ei.equipment_id = e.equipment_id " . ((!empty($equipment_id)) ? " and ei.equipment_id = '" . $equipment_id . "'" : '') . $warehouse_string);
    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_fetch_available_equipment_types_count($equipment_type_id = '', $warehouse_id = '') {
    global $database;
    $warehouse_string = '';
    $user_string = '';
    if (!empty($warehouse_id)) {
        $warehouse_string = tep_create_warehouse_string($warehouse_id);
    }

    $query = $database->query("select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT . " e where ei.equipment_status_id = '0' and ei.equipment_id = e.equipment_id and e.equipment_type_id = '" . $equipment_type_id . "' " . $warehouse_string);
    $result = $database->fetch_array($query);
    return $result['count'];
}

function tep_fetch_lost_equipment_count($equipment_id = '', $warehouse_id = '') {
    global $database;
    $query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where equipment_status_id = '4'" . ((!empty($equipment_id)) ? " and equipment_id = '" . $equipment_id . "'" : '') . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'" : ''));
    $result = $database->fetch_array($query);
    return $result['count'];
}

function tep_fetch_damaged_equipment_count($equipment_id = '', $warehouse_id = '') {
    global $database;
    $query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where  equipment_status_id = '5'" . ((!empty($equipment_id)) ? " and equipment_id = '" . $equipment_id . "'" : '') . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'" : ''));
    $result = $database->fetch_array($query);
    return $result['count'];
}

function tep_fetch_in_use_equipment_count($equipment_id = '', $warehouse_id = '') {
    global $database;
    $query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " whereequipment_status_id = '2'" . ((!empty($equipment_id)) ? " and equipment_id = '" . $equipment_id . "'" : '') . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'" : ''));
    $result = $database->fetch_array($query);
    return $result['count'];
}

//Count equipment items in a warehouse.
function tep_fetch_in_warehouse_equipment_count($warehouse_id) {
    global $database;
    $query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where warehouse_id = '" . $warehouse_id . "' and equipment_status_id = '0'" . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'" : ''));
    $result = $database->fetch_array($query);
    return $result['count'];
}

function tep_fetch_tracking_method($tracking_method_id) {
    if ($tracking_method_id == '0') {
        return 'None';
    } elseif ($tracking_method_id == '1') {
        return 'Unique Code';
    }
}

//Function for getting status of item.  Preferable use id (internal), otherwise code(external).
function fetch_equipment_status_id($equipment_id = '', $equipment_code = '') {
    global $database;
    $return_status = NULL;
    $query = false;
    if (!empty($equipment_code)) {
        $query = $database->query("select equipment_status_id from " . TABLE_EQUIPMENT_ITEMS . " where code = '" . $equipment_code . "' limit 1");
    }
    if (!empty($equipment_id)) {
        $query = $database->query("select equipment_status_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $equipment_id . "' limit 1");
    }
    if ($query) {
        $result = $database->fetch_array($query);
        $return_status = $result['equipment_status_id'];
    }
    return $return_status;
}

function tep_fetch_equipment_status_name($equipment_status_id) {
    global $database;
    $query = $database->query("select equipment_status_name from " . TABLE_EQUIPMENT_STATUSES . " where equipment_status_id = '" . $equipment_status_id . "' limit 1");
    $result = $database->fetch_array($query);
    if ($result['equipment_status_name'] == NULL) {
        return 'Unknowen';
    } else {
        return $result['equipment_status_name'];
    }
}

//End
//Work out the amount to add as the available for a equipment item.
function tep_fetch_equipment_available($equipment_id, $type, $total) {
    global $database;
    $available = 0;
    if ($type == 'add') {
        $available = $total;
    } else {
        $active_query = $database->query("select count(equipment_id) as count from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_id = '" . $equipment_id . "' and (equipment_status_id != '3')");
        $active_result = $database->fetch_array($active_query);
        $available = ($total - $active_result['count']);
    }
    return $available;
}

function parse_equipment_array($array) {
    if (!empty($array)) {
        while (list($group_id, $items) = each($array)) {
            $items_count = 0;
            $count = count($items);
            $n = 0;
            while ($n < $count) {
                if (empty($items[$n])) {
                    unset($array[$group_id][$n]);
                } else {
                    $items_count++;
                }
                $n++;
            }
            if ($items_count < 1) {
                unset($array[$group_id]);
            }
        }
    }
    return $array;
}

function parse_equipment_array2($array) { // zero is a valid entry
    if (!empty($array)) {
        while (list($group_id, $items) = each($array)) {
            $items_count = 0;
            $count = count($items);
            $n = 0;
            while ($n < $count) {
                if (empty($items[$n])) {
                    if ($items[$n] == 0)
                        $items_count++;
                    else
                        unset($array[$group_id][$n]);
                } else {
                    $items_count++;
                }
                $n++;
            }
            if ($items_count < 1) {
                unset($array[$group_id]);
            }
        }
    }
    return $array;
}

//Below are the functions for converting an equipment arrar intot a nice string.
function tep_create_confirmation_equipment_string($array) {
    global $database;

    reset($array);
    $return_string = '';
    if (empty($array)) {
        $return_string = 'No Optional Items Ordered.';
    } else {
        //Now we get to generate the nice little table.
        $return_string .= '<table width="100%" cellpadding="0" cellspacing="0">';
        while (list($group_id, $items) = each($array)) {
            $return_string .= '<tr><td class="main">' . tep_fetch_equipment_group_name($group_id) . '</td></tr>';
            $count = count($items);
            $n = 0;
            while ($n < $count) {

                $return_string .= '<tr><td class="main"> - ' . tep_fetch_equipment_item_answer_names($items[$n]) . '</td></tr>';
                $n++;
            }
            $return_string .= '<tr><td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>';
        }
        $return_string .= '</table>';
    }
    return $return_string;
}


function tep_create_confirmation_equipment_string_bgdn($array) {
    global $database;

    reset($array);
    //$return_string = '';
    if (empty($array)) {
        return array();
    } else {
		$eq_array = array();
        //Now we get to generate the nice little table.
       // $return_string .= '<table width="100%" cellpadding="0" cellspacing="0">';
        while (list($group_id, $items) = each($array)) {
			$eq_array[$group_id]['name'] = tep_fetch_equipment_group_name($group_id);
            //$return_string .= '<tr><td class="main">' . tep_fetch_equipment_group_name($group_id) . '</td></tr>';
            $count = count($items);
            $n = 0;
            while ($n < $count) {
				$eq_array[$group_id]['equipment'][] = tep_fetch_equipment_item_answer_names($items[$n]);
              //  $return_string .= '<tr><td class="main"> - ' . tep_fetch_equipment_item_answer_names($items[$n]) . '</td></tr>';
                $n++;
            }
            //$return_string .= '<tr><td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>';
        }
       // $return_string .= '</table>';
    }
    return $eq_array;
}

function tep_create_installer_confirmation_equipment_string($array) {
    global $database;
    $return_string = '';
    if (empty($array)) {

    } else {
        //Now we get to generate the nice little table.
        while (list($group_id, $items) = each($array)) {
            if (!empty($return_string)) {
                $return_string .= '<br>';
            }
            //$return_string .= '&nbsp;&nbsp;<u>'.tep_fetch_equipment_group_name($group_id).'</u>:';
            $count = count($items);
            $n = 0;
            while ($n < $count) {
                if ($n > 0) {
                    $return_string .= '<br>';
                }
                $return_string .= '&nbsp;&nbsp;' . tep_fetch_equipment_item_answer_names($items[$n], true);
                $n++;
            }
        }
    }
    return $return_string;
}

function tep_convert_view_equipment_array_to_edit($array) {
    $return_array = array();
    if (!empty($array)) {
        while (list($group_id, $details) = each($array)) {
            $return_array[$group_id] = array();
            $count = count($details['items']);
            $n = 0;
            while ($n < $count) {
                $return_array[$group_id][] = $details['items'][$n]['equipment_group_answer_id'];
                $n++;
            }
        }
    }
    return $return_array;
}

function tep_create_view_equipment_string($array, $show_id = false, $order_type_id = '', $order_status_id = '') {
    global $database;
    $return_string = '';
    if (empty($array)) {
        $return_string = 'No Optional Items Ordered.';
    } else {
        //Now we get to generate the nice little table.
        $return_string .= '<table width="100%" cellpadding="0" cellspacing="0">';
        while (list($group_id, $details) = each($array)) {
            if (empty($details['name'])) {
                $details['name'] = 'Signpost';
            }
            //$return_string .= '<tr><td class="main">'.$details['name'].'</td></tr>';
            $count = count($details['items']);
            $n = 0;
            while ($n < $count) {
                $return_string .= '<tr><td class="main">' . (($details['items'][$n]['method_id'] == '1') ? 'Install' : 'Remove') . ' ' . $details['items'][$n]['name'] . (($show_id && $details['items'][$n]['reference_code']) ? ' (' . $details['items'][$n]['reference_code'] . ')' : '') . ' </td></tr>';
                if ($order_type_id == '1') {
                    if (($details['items'][$n]['status'] == '1') || ($order_status_id == '1')) {
                        $return_string .= '<tr><td class="main">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Item is pending Installation</td></tr>';
                    } elseif ($details['items'][$n]['status'] == '2') {
                        $return_string .= '<tr><td class="main">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Item was' . (($details['items'][$n]['flag'] == '1') ? ' requested on installation and was' : '') . ' Successfully Installled</td></tr>';
                    } elseif (($details['items'][$n]['status'] == '0') && ($order_status_id != '1')) {
                        $return_string .= '<tr><td class="main">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - <span style="color:#FF0000;">Item was not Installed</span></td></tr>';
                    }
                }
                $n++;
            }
            $return_string .= '<tr><td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>';
        }
        $return_string .= '</table>';
    }
    return $return_string;
}

//A different version than the above, show in nice format with the group and items.
function tep_create_installer_view_equipment_string($array, $show_id = false) {
    global $database;
    $return_string = '';
    if (empty($array)) {
        $return_string = 'There is no equipment assigned to this order.';
    } else {
        //Now we get to generate the nice little table.
        $return_string .= '<table width="100%" cellpadding="0" cellspacing="0">';
        while (list($group_id, $details) = each($array)) {
            $return_string .= '<tr><td class="main">' . $details['name'] . '</td></tr>';
            $count = count($details['items']);
            $n = 0;
            while ($n < $count) {
                $return_string .= '<tr><td class="main"> - ' . $details['items'][$n]['name'] . (($show_id && $details['items'][$n]['reference_code']) ? ' (' . $details['items'][$n]['reference_code'] . ')' : '') . '</td></tr>';
                $n++;
            }
            $return_string .= '<tr><td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>';
        }
        $return_string .= '</table>';
    }
    return $return_string;
}

function tep_draw_service_areas_status_pulldown($name = '', $selected = '') {
    global $database;
    $return = '';
    $array = array();
    $array[] = array('id' => '0', 'name' => 'Active');
    $array[] = array('id' => '1', 'name' => 'Inactive');
    $count = count($array);
    $n = 0;
    while ($n < $count) {
        $result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
        $n++;
    }
    return tep_generate_pulldown_menu($name, $result_array, $selected);
}

function tep_draw_orders_status_pulldown($name = '', $selected = '', $return_array = array(), $params = '') {
    global $database;
    $query = $database->query("select order_status_id, order_status_name from " . TABLE_ORDERS_STATUSES . " order by order_status_id");
    foreach($query as $result){
        $return_array[] = array('id' => $result['order_status_id'], 'name' => $result['order_status_name']);
    }
    return tep_generate_pulldown_menu($name, $return_array, $selected, $params);
}

function tep_draw_orders_status_pulldown_bgdn($name = '', $selected = '', $return_array = array(), $params = '') {
    global $database;
    $query = $database->query("select order_status_id, order_status_name from " . TABLE_ORDERS_STATUSES . " order by order_status_id");
    foreach($query as $result){
        $return_array[] = array('id' => $result['order_status_id'], 'name' => $result['order_status_name']);
    }
    return array('name'=>$name, 'contents'=>$return_array, 'selected'=>$selected, 'params'=>$params);
}

function tep_draw_installer_pulldown($name = '', $selected = '', $return_array = array(), $params = '') {
    global $database;
    $query = $database->query("select u.user_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utg where utg.user_group_id = '3' and utg.user_id = u.user_id and u.user_id = ud.user_id and u.active_status = '1' order by ud.lastname");
    foreach($query as $result) {
        $return_array[] = array('id' => $result['user_id'], 'name' => $result['lastname'] . ', ' . $result['firstname']);
    }
    return tep_generate_pulldown_menu($name, $return_array, $selected, $params);
}

function tep_draw_warehouse_pulldown($name = '', $selected = '', $return_array = array(), $params = '') {
    global $database;
    $query = $database->query("select w.warehouse_id, wd.name from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = wd.warehouse_id order by wd.name");
    foreach($query as $result){
        $return_array[] = array('id' => $result['warehouse_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $return_array, $selected, $params);
}

function tep_create_orders_history($order_id, $order_status_id, $comments, $notify_user = false) {
    global $database;
    if ($notify_user) {
        $user_notified = '1';
    } else {
        $user_notified = '0';
    }
    $date_added = time();
    //Add to history.
    $database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $order_id . "', '" . $order_status_id . "', '" . $date_added . "', '" . $user_notified . "', '" . $comments . "')");
    //Update order.
    $database->query("update " . TABLE_ORDERS . " set order_status_id = '" . $order_status_id . "', last_modified = '" . time() . "' where order_id = '" . $order_id . "' limit 1");
}

function tep_fetch_orders_status_id($order_id) {
    global $database;
    $query = $database->query("select order_status_id from " . TABLE_ORDERS . " where order_id = '" . $order_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['order_status_id'];
}

function tep_zip4_is_valid($code) {
    $return = false;
    $explode = explode('-', $code);
    if (count($explode) == 2) {
        if ((strlen($explode[0]) == 5) && (strlen($explode[1]) == 4)) {
            if (is_numeric($explode[0]) && is_numeric($explode[1])) {
                $return = true;
            }
        }
    }
    return $return;
}

function tep_break_zip4_code($code) {
    $explode = explode('-', $code);
    if (count($explode) == 0) {
        return array();
    } elseif (count($explode) == 1) {
        $explode[] = '0';
        return $explode;
    } else {
        return $explode;
    }
}

function tep_zip4_is_assigned($code_from, $code_to, $ignore_user = array(), $ignore_id = false) {
    global $database;
    $code_from_explode = tep_break_zip4_code($code_from);
    $code_to_explode = tep_break_zip4_code($code_to);
    //Now work it out.
    $ignore_string = '';
    if (!empty($ignore_user)) {
        $count = count($ignore_user);
        $n = 0;
        while ($n < $count) {
            $ignore_string .= " and user_id != '" . $ignore_user[$n] . "' ";
            $n++;
        }
    }
    if ($ignore_id !== false) {
        $ignore_string .= " and installer_to_area_id != '" . $ignore_id . "' ";
    }
    $query = $database->query("select count(installer_to_area_id) as count from " . TABLE_INSTALLERS_TO_AREAS . " where zip_4_first_break_start >= '" . $code_from_explode[0] . "' and zip_4_first_break_end>= '" . $code_from_explode[1] . "' and zip_4_second_break_start <= '" . $code_to_explode[0] . "' and zip_4_second_break_end <= '" . $code_to_explode[1] . "'" . $ignore_string);
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function tep_draw_help_page_pulldown($name = '', $selected = '', $params = '') {
    global $database, $user;
    $return = '';
    $array = array();
    $query = $database->query("select pd.page_id, pd.name from " . TABLE_PAGES_DESCRIPTION . " pd where pd.language_id = '1' order by pd.name");
    foreach($query as $result){
        $array[] = array('id' => $result['page_id'], 'name' => $result['name']);
    }
    return tep_generate_pulldown_menu($name, $array, $selected, $params);
}

//Only fetch the installer if they are assigned not the default.
function tep_fetch_true_assigned_installer($order_id) {
    global $database;
    $query = $database->query("select installer_id from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $order_id . "' limit 1");
    $result = $database->fetch_array($query);
    if ($result['installer_id'] != NULL) {
        return $result['installer_id'];
    }
    return '';
}

function tep_fetch_assigned_order_installer($order_id) {
    global $database;
    //First check if the order has been re-assigned.
    $query = $database->query("select installer_id from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $order_id . "' limit 1");
    $result = $database->fetch_array($query);
    if ($result['installer_id'] != NULL) {
        return $result['installer_id'];
    }
    //Just get the zip co-ordinates and fetch the installer based on that.
    $query = $database->query("select a.zip4, o.date_schedualed from " . TABLE_ADDRESSES . " a, " . TABLE_ORDERS . " o where o.order_id = '" . $order_id . "' and o.address_id = a.address_id limit 1");
    $result = $database->fetch_array($query);
    //Now get the installer.
    if (empty($result['zip4'])) {
        return false;
    }
    $code_explode = tep_break_zip4_code($result['zip4']);

    $date_covering = mktime(0, 0, 0, date("n", $result['date_schedualed']), date("d", $result['date_schedualed']), date("Y", $result['date_schedualed']));
    $query = $database->query("select ia.installer_id as default_installer, itia.installer_id as assigned_installer from " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and date_covering = '" . $date_covering . "'), " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica where ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < '" . $code_explode[0] . "') or (ica.zip_4_first_break_start = '" . $code_explode[0] . "' and ica.zip_4_first_break_end <= '" . $code_explode[1] . "')) and ((ica.zip_4_second_break_start > '" . $code_explode[0] . "') or (ica.zip_4_second_break_start = '" . $code_explode[0] . "' and ica.zip_4_second_break_end >= '" . $code_explode[1] . "'))");
    $result = $database->fetch_array($query);
    if ($result['assigned_installer'] != NULL) {
        return $result['assigned_installer'];
    } elseif ($result['default_installer'] != NULL) {
        return $result['default_installer'];
    } else {
        return false;
    }
}

function tep_fetch_installer_name($installer_id) {
    global $database;
    $query = $database->query("select firstname, lastname from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $installer_id . "' limit 1");
    $result = $database->fetch_array($query);
    return ($result['lastname'] . ', ' . $result['firstname']);
}

function tep_fetch_time_type($timestamp, $allow_special = false) {
    //Works out if this is a weekend, we do this by checking the day number, 0 or 6 are weekends.
    $day_number = date("w", $timestamp);
    $weekend = array('0');
    if ($allow_special) {
        $weekend[] = '6';
    }
    if (in_array($day_number, $weekend)) {
        return 'weekend';
    } else {
        return 'weekday';
    }
}

function tep_fetch_next_available_day($timestamp, $allow_special = false) {
    //Works out if this is a weekend, we do this by checking the day number, 0 or 6 are weekends.
    if (tep_fetch_time_type($timestamp, $allow_special) == 'weekday') {
        return $timestamp;
    } else {
        //Now get the next day.
        $day_number = date("w", $timestamp);
        if ($day_number == 0) {
            $day_number = 7;
        }
        $days_needed = (8 - $day_number);
        $extra_time = (86400 * $days_needed);
        return ($timestamp + $extra_time);
    }
}

function tep_fetch_current_timestamp() {
    return time();
}

//Fetches agent information + agency information
function tep_fetch_agent_data($agent_id) {
    global $database;
    $return_array = array();
    //Get basic data.
    $agent_query = $database->query("select u.agent_id, u.agency_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $agent_id . "' and u.user_id = ud.user_id limit 1");
    $agent_result = $database->fetch_array($agent_query);

    $agency_result = array('name' => '', 'contact_phone' => '');
    if (is_numeric($agent_result['agency_id'])) {
        //If associated agency then get agency data.
        $agency_query = $database->query("select agency_id, name, contact_phone from " . TABLE_AGENCYS . " where agency_id = '" . $agent_result['agency_id'] . "' limit 1");
        $agency_result = $database->fetch_array($agency_query);
        if (empty($agency_result['agency_id'])) {
            $agency_result = array('name' => '', 'contact_phone' => '');
        }
    } else {
        $agent_result = array();
    }

    //Get agent phone numbers.
    $phone_numbers_array = array();
    $phone_query = $database->query("select phone_number from " . TABLE_USERS_PHONE_NUMBERS . " where user_id = '" . $agent_id . "' order by order_id");
    foreach($phone_query as $phone_result){
        $phone_numbers_array[] = $phone_result['phone_number'];
    }
    if (!empty($agency_result['contact_phone'])) {
        $phone_numbers_array[] = $agency_result['contact_phone'] . ' - Agency';
    }
    $return_array = array_merge($agent_result, $agency_result);
    $return_array['phone_numbers'] = $phone_numbers_array;

    return $return_array;
}

function tep_fetch_user_amount_reduction($user_id) {
    global $database;
    $query = $database->query("select discount_amount from " . TABLE_USERS . " where user_id = '" . $user_id . "' and discount_type = '1' limit 1");

    $result = $database->fetch_array($query);

    if (empty($result['discount_amount']) || $result['discount_amount'] == 0) {
        $aquery = $database->query("select agency_id from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
        $aresult = $database->fetch_array($aquery);
        if (!empty($aresult['agency_id'])) {
            $query = $database->query("select discount_amount from " . TABLE_AGENCYS . " where agency_id = '" . $aresult['agency_id'] . "' and discount_type = '1' limit 1");
            $result = $database->fetch_array($query);
        }
    }
    if (empty($result['discount_amount'])) {
        $amount = 0;
    } else {
        $amount = $result['discount_amount'];
    }
    return $amount;
}

function tep_fetch_user_percentage_reduction($user_id) {
    global $database;
    $query = $database->query("select discount_amount from " . TABLE_USERS . " where user_id = '" . $user_id . "' and discount_type = '2' limit 1");
    $result = $database->fetch_array($query);
    if (empty($result['discount_amount'])) {
        $aquery = $database->query("select agency_id from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
        $aresult = $database->fetch_array($aquery);
        if (!empty($aresult['agency_id'])) {
            $query = $database->query("select discount_amount from " . TABLE_AGENCYS . " where agency_id = '" . $aresult['agency_id'] . "' and discount_type = '2' limit 1");
            $result = $database->fetch_array($query);
        }
    }
    if (empty($result['discount_amount'])) {
        $amount = 0;
    } else {
        $amount = $result['discount_amount'];
    }
    return $amount;
}

function tep_agent_has_preferences($agent_id, $order_type_id = '1') {
    global $database;
    $type = '';
    if ($order_type_id == '1') {
        $type = 'install';
    } elseif ($order_type_id == '2') {
        $type = 'service_call';
    } else {
        $type = 'removal';
    }
    $query = $database->query("select count(user_id) as count from " . TABLE_AGENT_PREFERENCES . " where user_id = '" . $agent_id . "' and " . $type . "_preference != ''");
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        return true;
    } else {
        //Check out if the service_level has preferences.
        $query = $database->query("select count(service_level_id) as count from " . TABLE_SERVICE_LEVELS_DESCRIPTION . " where service_level_id = '" . tep_get_service_level_id($agent_id) . "' and default_" . $type . "_preferences != ''");
        $result = $database->fetch_array($query);
        if ($result['count'] > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function tep_create_agent_preferences_string($agent_id, $order_type_id = '1') {
    global $database;

    //Now got over data array and output the contents.
    $type = '';
    if ($order_type_id == '1') {
        $type = 'install';
    } elseif ($order_type_id == '2') {
        $type = 'service_call';
    } else {
        $type = 'removal';
    }

    //Get the agent, then if none get the default.
    $query = $database->query("select " . $type . "_preference from " . TABLE_AGENT_PREFERENCES . " where user_id = '" . $agent_id . "' limit 1");
    $result = $database->fetch_array($query);
    $preference = '';

    if (!empty($result[$type . '_preference'])) {
        $preference = $result[$type . '_preference'];
    } else {
        $query = $database->query("select default_" . $type . "_preferences from " . TABLE_SERVICE_LEVELS_DESCRIPTION . " where service_level_id = '" . tep_get_service_level_id($agent_id) . "' limit 1");
        $result = $database->fetch_array($query);

        $preference = $result["default_" . $type . "_preferences"];
    }

    $output_string = $preference;
    return $output_string;
}

function tep_fetch_warehouse_availability_name($availability) {
    if ($availability == '0') {
        return 'Shared';
    } elseif ($availability == '1') {
        return 'Not Shared';
    }
}

/*
  Function to get warehouses servicing a certain area.
  Arguments:
  $zip4

 */
//bogdan
function tep_get_sevicing_warehouse($zip4, $include_shared = 'all') {
    global $database;
    $return_array = array('0');
    if (tep_zip4_is_valid($zip4)) {
        $explode = tep_break_zip4_code($zip4);
        $zip4_start = $explode[0];
        $zip4_end = $explode[1];
    } else {
        return $return_array;
    }
    if ($include_shared == 'all' || $include_shared == 'shared') {
        //$query = $database->query("SELECT w.warehouse_id FROM " . TABLE_WAREHOUSES . " w LEFT JOIN " . TABLE_WAREHOUSES_TO_AREAS . " wta ON (w.warehouse_id = wta.warehouse_id) WHERE w.availability = '0' OR (CONCAT(wta.zip_4_first_break_start, wta.zip_4_first_break_end) <= CONCAT('" . $zip4_start . "', '" . $zip4_end . "') AND CONCAT(wta.zip_4_second_break_start, wta.zip_4_second_break_end) >= CONCAT('" . $zip4_start . "', '" . $zip4_end . "'))");
		$query = $database->query("SELECT w.warehouse_id FROM " . TABLE_WAREHOUSES . " w LEFT JOIN " . TABLE_INSTALLATION_AREAS . " ia ON (w.warehouse_id = ia.warehouse_id) LEFT JOIN " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica ON (ica.installation_area_id = ia.installation_area_id) WHERE w.availability = '0' OR (CONCAT(ica.zip_4_first_break_start, ica.zip_4_first_break_end) <= CONCAT('" . $zip4_start . "', '" . $zip4_end . "') AND CONCAT(ica.zip_4_second_break_start, ica.zip_4_second_break_end) >= CONCAT('" . $zip4_start . "', '" . $zip4_end . "'))");
    } elseif ($include_shared = 'area') {
        //$query = $database->query("SELECT w.warehouse_id FROM " . TABLE_WAREHOUSES . " w LEFT JOIN " . TABLE_WAREHOUSES_TO_AREAS . " wta ON (w.warehouse_id = wta.warehouse_id) WHERE CONCAT(wta.zip_4_first_break_start, wta.zip_4_first_break_end) <= CONCAT('" . $zip4_start . "', '" . $zip4_end . "') AND CONCAT(wta.zip_4_second_break_start, wta.zip_4_second_break_end) >= CONCAT('" . $zip4_start . "', '" . $zip4_end . "')");
		$query = $database->query("SELECT w.warehouse_id FROM " . TABLE_WAREHOUSES . " w LEFT JOIN " . TABLE_INSTALLATION_AREAS . " ia ON (w.warehouse_id = ia.warehouse_id) LEFT JOIN " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica ON (ica.installation_area_id = ia.installation_area_id) WHERE CONCAT(ica.zip_4_first_break_start, ica.zip_4_first_break_end) <= CONCAT('" . $zip4_start . "', '" . $zip4_end . "') AND CONCAT(ica.zip_4_second_break_start, ica.zip_4_second_break_end) >= CONCAT('" . $zip4_start . "', '" . $zip4_end . "')");
    } else {
        $query = $database->query("select w.warehouse_id from " . TABLE_WAREHOUSES . " where w.availability = '0'");
    }

    while ($result = $database->fetch_array($query)) {
        $return_array[] = $result['warehouse_id'];
    }
    return $return_array;
}

function tep_workout_special_price($equipment_group_id, $address_id, $service_level_id, $order_type_id = '1') {
    global $database;

    $query = $database->query("select free_for_level, allowed_free from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $equipment_group_id . "' limit 1");
    $result = $database->fetch_array($query);

    $allowed_free = $result['allowed_free'];
    $free_for_level = $result['free_for_level'];

    $count_query = $database->query("select current_count from " . TABLE_INCLUDED_EQUIPMENT_TRACK . " where address_id = '" . $address_id . "' and equipment_group_id = '" . $equipment_group_id . "' limit 1");

    $current_count = 0;
    if ($order_type_id != '1' && $count_result = $database->fetch_array($count_query)) {
        $current_count = $count_result['current_count'];
    }

    if ($free_for_level == $service_level_id && $current_count < $allowed_free) {
        return true;
    }

    return false;
}

/*
  Function to generate a list of available equipment .
  Arguments:
  $order_type - The type of order (given by the id).  Ie. Installer = 1;
  $service_level - The service level of the user (given by the id), a blank value is the manual override which allows for any.
  $selected - The old style array which shows the currently selected items.
  $zip4 - The zip4 number of the order.  This is used when working out what equipment is in aailable warehouses.

  To do this we would work out what equipment is available for an order type and is the same service level (or leave this out for\
  override.  We would also check if equipment were available in servicing warehouse or another open
  type warehouse.
  Installer can specify to change the warehous ethey want the equipment from.
 */

function tep_generate_available_equipment_string($order_type, $service_level, $user_id, $selected, $zip4, $address_id = '', $edit = false, $show_default = true, $exclude_array = array(), $show_price = true, $show_prevent = true, $admin_fade = false) {
    global $database;

    $return_string = '';
    $found = false;
    $warehouses = tep_get_sevicing_warehouse($zip4); // mjp 201201 - ehancements for personal inventory order by

    $query = $database->query("select equipment_group_id, color, name, description, selectable, cost, discount, display_order, personalized from " . TABLE_EQUIPMENT_GROUPS . " where order_type_id = '" . $order_type . "' ORDER BY display_order");
    $service_level_id = tep_get_service_level_id($user_id);
    foreach($query as $result){
        if (!empty($return_string)) {
            $return_string .= '<tr><td height="20"><img src="images/pixel_trans.gif" height="20" width="1"></td></tr>';
        }

        $color = ""; // mjp 201201 - ehancements for personal inventory order by
        $end_color = "";
        if (isset($selected[$result['equipment_group_id']]) && $selected[$result['equipment_group_id']] == array("0")) {
            $none_selected = " checked";
        } else {
            $none_selected = "";
        }

        if ($result['color']) {
            $color = ' <font color="' . $result['color'] . '">';
            $end_color = '</font>';
        }
        if (!tep_equipment_group_has_items($result['equipment_group_id'], $user_id, $warehouses, $address_id, $exclude_array, true)) {
            if ($result['display_order'] == "1") {
				if ($admin_fade == true) {
					$f_prefix = 'style="opacity:0.4; pointer-events: none;"';
					//$f_postfix = '';
				} else {
					$f_prefix = '';
					//$f_postfix = '';
				}
                $return_string .= '<tr '.$f_prefix.'>' . "\n" .
                        '<td class="main">&nbsp;&nbsp;<u>' . $result['name'] . '</u>' . (($show_price) ? '&nbsp;&nbsp;&nbsp;' . tep_create_special_payment_string($result['cost'], $result['discount']) : '') . '</td>' . "\n" .
                        '</tr>' . "\n" .
                        (($show_price) ? ('<tr '.$f_prefix.'>' . "\n" .
                                '<td class="main">' . $color . $result['description'] . $end_color . '</td>' . "\n" .
                                '</tr>') . "\n" : '') .
                        '<tr '.$f_prefix.'>' . "\n" .
                        '<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>' . "\n" .
                        '</tr>' . "\n";
                $return_string .= '<tr '.$f_prefix.'>' . "\n" .
                        '<td class="main"><input type="radio" name="optional[' . $result['equipment_group_id'] . '][]" value="0"' . $none_selected . '> None' . $color . 'No Panels are available from the Warehouse for this service area.' . $end_color . '</td></tr>';
            }
            //echo 'continueing<br>';
            if (!$edit || !isset($selected[$result['equipment_group_id']])) {
                continue;
            }
        }
        if (tep_workout_special_price($result['equipment_group_id'], $address_id, $service_level, $order_type)) {
            $result['cost'] = 0;
        }
//					echo 'found<br>';
        $found = true;
        $return_string .= '<tr>' . "\n" .
                '<td class="main">&nbsp;&nbsp;<u>' . $result['name'] . '</u>' . (($show_price) ? '&nbsp;&nbsp;&nbsp;' . tep_create_special_payment_string($result['cost'], $result['discount']) : '') . '</td>' . "\n" .
                '</tr>' . "\n" .
                (($show_price) ? ('<tr>' . "\n" .
                        '<td class="main">' . $color . $result['description'] . $end_color . '</td>' . "\n" .
                        '</tr>') . "\n" : '') .
                '<tr>' . "\n" .
                '<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>' . "\n" .
                '</tr>' . "\n";
        if ($show_price) {
            if ($result['selectable'] != '1') {
                $return_string .= '<script language="javascript">' . "\n" .
                        'function checkTwo_' . $result['equipment_group_id'] . '(theBox, message, limit){' . "\n" .
                        'if (limit < 1) { return true; }' . "\n" .
                        'boxName=theBox.name;' . "\n" .
                        'elm=theBox.form.elements;' . "\n" .
                        'count=0;' . "\n" .
                        ' for(i=0;i<elm.length;i++) {' . "\n" .
                        'if(elm[i].name==boxName && elm[i].checked==true) {' . "\n" .
                        ' count++' . "\n" .
                        ' }' . "\n" .
                        '}' . "\n" .
                        ' if(count > limit){' . "\n" .
                        'alert(\'Please select no more than two \'+message+\' items to be placed.\')' . "\n" .
                        'theBox.checked=false;' . "\n" .
                        ' }' . "\n" .
                        '}' . "\n" .
                        '</script>' . "\n";
            }
        }
        $optional_count_query = $database->query("select count(equipment_group_answer_id) as count from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $result['equipment_group_id'] . "' and service_level_id <= '" . $service_level_id . "'");
        $optional_count_result = $database->fetch_array($optional_count_query);

        //Do it first and count them.
        $count_for_rows = 0;
        $optional_query = $database->query("select equipment_group_answer_id, name, install_equipment_id, remove_equipment_id, checked from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $result['equipment_group_id'] . "' and service_level_id <= '" . $service_level_id . "'");
        foreach($optional_query as $optional_result){
            if ((tep_fetch_available_equipment_count($optional_result['install_equipment_id'], $warehouses, $user_id, true) < 1) || (!empty($address_id) && ($optional_result['remove_equipment_id'] != '0') && (!tep_equipment_is_at_address($address_id, $optional_result['remove_equipment_id']))) || in_array($optional_result['equipment_group_answer_id'], $exclude_array)) {
                if (!$edit || !isset($selected[$result['equipment_group_id']]) || !in_array($optional_result['equipment_group_answer_id'], $selected[$result['equipment_group_id']]) || in_array($optional_result['equipment_group_answer_id'], $exclude_array)) {
                    continue;
                }
            }
            $count_for_rows++;
        }
        $row = 0;
        if ($count_for_rows > 6) {
            $return_string .= '<tr><td width="600"><table width="600" cellspacing="0" cellpadding="0">' . "\n";
        }
        $optional_loop = 0;
        $optional_query = $database->query("select equipment_group_answer_id, name, install_equipment_id,
                                                remove_equipment_id, checked from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" .
                $result['equipment_group_id'] . "' and service_level_id <= '" . $service_level_id . "' order by name");
        $somethingSelected = false;
        foreach($optional_query as $optional_result){
            if ((tep_fetch_available_equipment_count($optional_result['install_equipment_id'], $warehouses, $user_id, true) < 1) || (!empty($address_id) && ($optional_result['remove_equipment_id'] != '0') && (!tep_equipment_is_at_address($address_id, $optional_result['remove_equipment_id']))) || in_array($optional_result['equipment_group_answer_id'], $exclude_array)) {
                if (!$edit || !isset($selected[$result['equipment_group_id']]) || !in_array($optional_result['equipment_group_answer_id'], $selected[$result['equipment_group_id']]) || in_array($optional_result['equipment_group_answer_id'], $exclude_array)) {
                    continue;
                }
            }

            $available_equipment_count = tep_fetch_available_equipment_count($optional_result['install_equipment_id'], $warehouses, $user_id, false);
            if ($available_equipment_count < 1) {
                $out_of_stock = true;
                $disabled = " disabled";
                $inventory_level = "";
            } else {
                $out_of_stock = false;
                $disabled = "";
                $inventory_level = " - <strong>({$available_equipment_count} available in this service area)</strong>";
            }

            if (!($result['equipment_group_id'] == 5 || $result['equipment_group_id'] == 9)) {
                $inventory_level = "";  // only show inventory levels for personal equipment and agent riders
            }

            if (isset($selected[$result['equipment_group_id']]) && is_array($selected[$result['equipment_group_id']]) && in_array($optional_result['equipment_group_answer_id'], $selected[$result['equipment_group_id']])) {
                $checked = ' CHECKED ';
                $somethingSelected = true;
            } elseif ($show_default && empty($selected) && (($optional_result['checked'] == '1') || tep_show_default_checked($order_type, $user_id, $result['equipment_group_id'], $optional_result['equipment_group_answer_id']))) {
                $checked = ' CHECKED ';
                $somethingSelected = true;
            } else {
                $checked = '';
            }
            if (($result['selectable'] == '1') && ($optional_count_result['count'] > 1)) {

                $check_box = '<input type="radio" name="optional[' . $result['equipment_group_id'] . '][]" value="' . $optional_result['equipment_group_answer_id'] . '"' . $checked . $disabled . '>' . "\n";
            } else {
                if (($result['selectable'] != '1') && ($show_price)) {
                    $string = ' onclick="checkTwo_' . $result['equipment_group_id'] . '(this, \'' . $result['name'] . '\', ' . $result['selectable'] . ');"';
                } else {
                    $string = '';
                }
                $check_box = '<input type="checkbox"' . $string . ' name="optional[' . $result['equipment_group_id'] . '][]" value="' . $optional_result['equipment_group_answer_id'] . '"' . $checked . $disabled . '>' . "\n";
            }
            if ($show_prevent && ($optional_loop == 0) && ($result['selectable'] == '1') && ($optional_count_result['count'] > 1)) {
                /* $return_string .= '<tr>'."\n".
                  '<td class="main"><input type="radio" name="optional['.$result['equipment_group_id'].'][]" value="">Do not install a Rider</td>'."\n".
                  '</tr>'."\n"; */
            }
//													if ($out_of_stock) {
//														$check_box = '';
//													}
            if ($count_for_rows > 6) {
                if ($row == 0) {
                    $return_string .= '<tr>' . "\n" .
                            '<td class="main" width="300">' . $check_box . ' ' . $optional_result['name'] . (($out_of_stock) ? ' - ' : '') . '</td>' . "\n";
                } else {
                    $return_string .= '<td class="main" width="300">' . $check_box . ' ' . $optional_result['name'] . (($out_of_stock) ? ' - Currently out of stock' : '') . '</td>' . "\n" .
                            '</tr>' . "\n";
                    //$return_string .= '<tr>'."\n".
                    //'<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>'."\n".
                    //'</tr>'."\n";
                    $row = -1;
                }
            } else {
                if ($row > 0) {
                    //$return_string .= '<tr>'."\n".
                    //'<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>'."\n".
                    //'</tr>'."\n";
                }
                //mjp Fix/Update Several Inventory Related Issues 201201
                if ($out_of_stock) {
                    $color = "<span style='color: red;'>";
                    $end_color = "</span>";
                    $return_string .= '<tr>' . "\n" .
                            '<td class="main">' . $color . $check_box . ' ' . $optional_result['name'] . ' (out of stock)' . $end_color . '</td>' . "\n" .
                            '</tr>' . "\n";
                } else {
                    $return_string .= '<tr>' . "\n" .
                            '<td class="main">' . $check_box . ' ' . $optional_result['name'] . $inventory_level . '</td>' . "\n" .
                            '</tr>' . "\n";
                }
            }
            $row++;
            $optional_loop++;
        }
        if (($result['selectable'] == '1') && ($optional_count_result['count'] > 1)) {
//mjp Fix/Update Several Inventory Related Issues 201201 '<td class="main"><input type="radio" name="optional['.$result['equipment_group_id'].'][]" value=""'.($somethingSelected ? '' : ' CHECKED').'> None</td>'."\n".
            $return_string .= '<tr>' . "\n" .
                    '<td class="main"><input type="radio" name="optional[' . $result['equipment_group_id'] . '][]" value="0"' . $none_selected . '> None</td></tr>';
        }
        if ($count_for_rows > 6) {
            if ($row != 0) {
                $return_string .= '</tr>' . "\n";
                $return_string .= '<tr>' . "\n" .
                        '<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>' . "\n" .
                        '</tr>' . "\n";
            }
            $return_string .='</table></td></tr>';
        }
    } // end result loop
    if (!$found && $show_price) {
        $return_string .= '<tr>' . "\n" .
                '<td class="main">There are no optional extras for your service level.  If you want these then please consider changing your level.</td>' . "\n" .
                '</tr>' . "\n";
    }
    return $return_string;
}


function tep_generate_available_equipment_string_bgdn($order_type, $service_level, $user_id, $selected, $zip4, $address_id = '', $edit = false, $show_default = true, $exclude_array = array(), $show_price = true, $show_prevent = true) {
    global $database;

    $return_string = '';
    $found = false;
    $warehouses = tep_get_sevicing_warehouse($zip4); // mjp 201201 - ehancements for personal inventory order by

    $query = $database->query("select equipment_group_id, color, name, description, selectable, cost, discount, display_order, personalized from " . TABLE_EQUIPMENT_GROUPS . " where order_type_id = '" . $order_type . "' ORDER BY display_order");

    //echo "1 select equipment_group_id, color, name, description, selectable, cost, discount, display_order, personalized from " . TABLE_EQUIPMENT_GROUPS . " where order_type_id = '" . $order_type . "' ORDER BY display_order<br/>";

    $service_level_id = tep_get_service_level_id($user_id);

    foreach($query as $result){


        if (isset($selected[$result['equipment_group_id']]) && $selected[$result['equipment_group_id']] == array("0")) {
           $result['none_selected'] = true;
        } else {
            $result['none_selected'] = false;
        }


        //var_dump($exclude_array);
        if($result['equipment_group_id'] != 5){
            if (!tep_equipment_group_has_items($result['equipment_group_id'], $user_id, $warehouses, $address_id, $exclude_array, true) ) {


                $result['has_items'] = 1;

                if ($result['display_order'] == "1") {            }
                // echo "equipment_group_id ".$result['equipment_group_id']."<br/>";
                if ((!$edit || !isset($selected[$result['equipment_group_id']])) ) {
                    //var_dump($edit);
                    continue;
                }

            }
        }

        if (tep_workout_special_price($result['equipment_group_id'], $address_id, $service_level, $order_type)) {
            $result['cost'] = 0;
        }

        $found = true;

        $optional_count_query = $database->query("select count(equipment_group_answer_id) as count from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $result['equipment_group_id'] . "' and service_level_id <= '" . ($service_level_id) . "'"); //
        //echo "2a select count(equipment_group_answer_id) as count from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $result['equipment_group_id'] . "' and service_level_id <= '" . ($service_level_id+1) . "'";
        $optional_count_result = $database->fetch_array($optional_count_query);

        //Do it first and count them.
        $count_for_rows = 0;
        $optional_query = $database->query("select equipment_group_answer_id, name, install_equipment_id, remove_equipment_id, checked from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $result['equipment_group_id'] . "' and service_level_id <= '" . ($service_level_id) . "'"); // $service_level_id + 1
        // echo "2 select equipment_group_answer_id, name, install_equipment_id, remove_equipment_id, checked from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $result['equipment_group_id'] . "' and service_level_id <= '" . ($service_level_id+1) . "' <br/>";
        foreach($optional_query as $optional_result){
            if ((tep_fetch_available_equipment_count($optional_result['install_equipment_id'], $warehouses, $user_id, true) < 1) || (!empty($address_id) && ($optional_result['remove_equipment_id'] != '0') && (!tep_equipment_is_at_address($address_id, $optional_result['remove_equipment_id']))) || in_array($optional_result['equipment_group_answer_id'], $exclude_array)) {
                if (!$edit || !isset($selected[$result['equipment_group_id']]) || !in_array($optional_result['equipment_group_answer_id'], $selected[$result['equipment_group_id']]) || in_array($optional_result['equipment_group_answer_id'], $exclude_array)) {
                  //  echo $result['equipment_group_id'].'-'.$optional_count_result['count']." select count(equipment_group_answer_id) as count from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $result['equipment_group_id'] . "' and service_level_id <= '" . ($service_level_id+1) . "'<br/>";
                  continue;
                }
            }
            $count_for_rows++;
        }
        // echo 'equipment_group_id :- '.$result['equipment_group_id'] .' - count_for_rows :- '.$count_for_rows.'<br/>';
        $row = 0;
        if ($count_for_rows > 6) {
            $return_string .= '<tr><td width="600"><table width="600" cellspacing="0" cellpadding="0">' . "\n";
        }
        $optional_loop = 0;
        $optional_query = $database->query("select equipment_group_answer_id, name, install_equipment_id,
                                                remove_equipment_id, checked from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" .
                $result['equipment_group_id'] . "' and service_level_id <= '" . ($service_level_id) . "' order by name"); //// $service_level_id + 1

        //echo "3 select equipment_group_answer_id, name, install_equipment_id,
          //                                      remove_equipment_id, checked from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" .
          //      $result['equipment_group_id'] . "' and service_level_id <= '" . $service_level_id . "' order by name <br/>";

        $somethingSelected = false;
        foreach($optional_query as $optional_result){
            if ((tep_fetch_available_equipment_count($optional_result['install_equipment_id'], $warehouses, $user_id, true) < 1) || (!empty($address_id) && ($optional_result['remove_equipment_id'] != '0') && (!tep_equipment_is_at_address($address_id, $optional_result['remove_equipment_id']))) || in_array($optional_result['equipment_group_answer_id'], $exclude_array)) {
                if (!$edit || !isset($selected[$result['equipment_group_id']]) || !in_array($optional_result['equipment_group_answer_id'], $selected[$result['equipment_group_id']]) || in_array($optional_result['equipment_group_answer_id'], $exclude_array)) {
                    continue;
                }
            }

            $available_equipment_count = tep_fetch_available_equipment_count($optional_result['install_equipment_id'], $warehouses, $user_id, false);
			$available_equipment_count_total = tep_fetch_available_equipment_count($optional_result['install_equipment_id'], $warehouses, $user_id, true);



            if ($available_equipment_count < 1) {
                $optional_result['out_of_stock'] = true;
                $optional_result['disabled'] = true;
               $optional_result['inventory_level'] = "";
            } else {
                $optional_result['out_of_stock'] = false;
                $optional_result['disabled'] = false;
                $optional_result['inventory_level'] = " - ({$available_equipment_count} of {$available_equipment_count_total} available in this service area)";
            }

            if (!($result['equipment_group_id'] == 5 || $result['equipment_group_id'] == 9)) {
                 $optional_result['inventory_level'] = "";  // only show inventory levels for personal equipment and agent riders
            }

            if (isset($selected[$result['equipment_group_id']]) && is_array($selected[$result['equipment_group_id']]) && in_array($optional_result['equipment_group_answer_id'], $selected[$result['equipment_group_id']])) {
                 $optional_result['checked'] = true;
                 $optional_result['somethingSelected'] = true;
            } elseif ($show_default && empty($selected) && (($optional_result['checked'] == '1') || tep_show_default_checked($order_type, $user_id, $result['equipment_group_id'], $optional_result['equipment_group_answer_id']))) {
                $optional_result['checked'] = true;
                 $optional_result['somethingSelected'] = true;
            } else {
                 $optional_result['checked'] = false;
            }
            if (($result['selectable'] == '1') && ($optional_count_result['count'] > 1)) {

               // $check_box = '<input type="radio" name="optional[' . $result['equipment_group_id'] . '][]" value="' . $optional_result['equipment_group_answer_id'] . '"' . $checked . $disabled . '>' . "\n";

            } else {
                if (($result['selectable'] != '1') && ($show_price)) {
                     $optional_result['string'] = ' onclick="checkTwo_' . $result['equipment_group_id'] . '(this, \'' . $result['name'] . '\', ' . $result['selectable'] . ');"';
                } else {
                     $optional_result['string'] = '';
                }
               // $check_box = '<input type="checkbox"' . $string . ' name="optional[' . $result['equipment_group_id'] . '][]" value="' . $optional_result['equipment_group_answer_id'] . '"' . $checked . $disabled . '>' . "\n";
            }
            if ($show_prevent && ($optional_loop == 0) && ($result['selectable'] == '1') && ($optional_count_result['count'] > 1)) {
                /* $return_string .= '<tr>'."\n".
                  '<td class="main"><input type="radio" name="optional['.$result['equipment_group_id'].'][]" value="">Do not install a Rider</td>'."\n".
                  '</tr>'."\n"; */
            }
//													if ($out_of_stock) {
//														$check_box = '';
//													}
            if ($count_for_rows > 6) {
                if ($row == 0) {
                   /* $return_string .= '<tr>' . "\n" .
                            '<td class="main" width="300">' . $check_box . ' ' . $optional_result['name'] . (($out_of_stock) ? ' - ' : '') . '</td>' . "\n";*/
                } else {
                 /*   $return_string .= '<td class="main" width="300">' . $check_box . ' ' . $optional_result['name'] . (($out_of_stock) ? ' - Currently out of stock' : '') . '</td>' . "\n" .
                            '</tr>' . "\n";*/
                    //$return_string .= '<tr>'."\n".
                    //'<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>'."\n".
                    //'</tr>'."\n";
                   /* $row = -1;*/
                }
            } else {
                if ($row > 0) {
                    //$return_string .= '<tr>'."\n".
                    //'<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>'."\n".
                    //'</tr>'."\n";
                }

                //mjp Fix/Update Several Inventory Related Issues 201201
                if ($optional_result['out_of_stock']) {
                    // $optional_result['color'] = "<span style='color: red;'>";
                   /* $end_color = "</span>";
                    $return_string .= '<tr>' . "\n" .
                            '<td class="main">' . $color . $check_box . ' ' . $optional_result['name'] . ' (out of stock)' . $end_color . '</td>' . "\n" .
                            '</tr>' . "\n";*/
                } else {
                  /*  $return_string .= '<tr>' . "\n" .
                            '<td class="main">' . $check_box . ' ' . $optional_result['name'] . $inventory_level . '</td>' . "\n" .
                            '</tr>' . "\n";*/
                }
            }
            $row++;
            $optional_loop++;


			$result['optional_result'][] = $optional_result;

        }
        if (($result['selectable'] == '1') && ($optional_count_result['count'] > 1)) {
//mjp Fix/Update Several Inventory Related Issues 201201 '<td class="main"><input type="radio" name="optional['.$result['equipment_group_id'].'][]" value=""'.($somethingSelected ? '' : ' CHECKED').'> None</td>'."\n".
           /* $return_string .= '<tr>' . "\n" .
                    '<td class="main"><input type="radio" name="optional[' . $result['equipment_group_id'] . '][]" value="0"' . $none_selected . '> None</td></tr>';*/
        }
        if ($count_for_rows > 6) {
           /* if ($row != 0) {
                $return_string .= '</tr>' . "\n";
                $return_string .= '<tr>' . "\n" .
                        '<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>' . "\n" .
                        '</tr>' . "\n";
            }
            $return_string .='</table></td></tr>';*/
        }



		$final[] = $result;

    } // end result loop
    if (!$found && $show_price) {
        /*$return_string .= '<tr>' . "\n" .
                '<td class="main">There are no optional extras for your service level.  If you want these then please consider changing your level.</td>' . "\n" .
                '</tr>' . "\n";*/
    }



    return $final; //$return_string;
}

function tep_show_default_checked($order_type_id, $user_id, $group_id, $group_answer_id) {
    //Work out what is default checked.  Do this in a falback motion.
    global $database;
    $found = false;
    $return = false;

    $default_order_id = '';
    //First get he result for the user.
    $query = $database->query("select default_order_id from " . TABLE_DEFAULT_ORDERS . " where order_type_id = '" . $order_type_id . "' and user_id = '" . $user_id . "' limit 1");
    $result = $database->fetch_array($query);
    if (!empty($result['default_order_id'])) {
        $found = true;
        $default_order_id = $result['default_order_id'];
    }
    //If can't find the above get the result for the agency.
    if (!$found) {
        $query = $database->query("select default_order_id from " . TABLE_DEFAULT_ORDERS . " where order_type_id = '" . $order_type_id . "' and agency_id = '" . tep_fetch_user_agency_id($user_id) . "' limit 1");
        $result = $database->fetch_array($query);
        if (!empty($result['default_order_id'])) {
            $found = true;
            $default_order_id = $result['default_order_id'];
        }
    }
    //If can't find the above get the result for the service level.
    if (!$found) {
        $query = $database->query("select default_order_id from " . TABLE_DEFAULT_ORDERS . " where order_type_id = '" . $order_type_id . "' and service_level_id = '" . tep_get_service_level_id($user_id) . "' limit 1");
        $result = $database->fetch_array($query);
        if (!empty($result['default_order_id'])) {
            $found = true;
            $default_order_id = $result['default_order_id'];
        }
    }
    //If still can't find it then just get a blank result for the type.
    if (!$found) {
        $query = $database->query("select default_order_id from " . TABLE_DEFAULT_ORDERS . " where order_type_id = '" . $order_type_id . "' and service_level_id = '0' and agency_id = '0' and user_id = '0' limit 1");
        $result = $database->fetch_array($query);
        if (!empty($result['default_order_id'])) {
            $found = true;
            $default_order_id = $result['default_order_id'];
        }
    }
    if ($found) {
        $query = $database->query("select 	equipment_group_answers from " . TABLE_DEFAULT_ORDERS_ITEMS . " where default_order_id = '" . $default_order_id . "' and equipment_group_id = '" . $group_id . "' limit 1");
        $result = $database->fetch_array($query);

        if (!empty($result['equipment_group_answers'])) {
            $explode = explode(', ', $result['equipment_group_answers']);
            if (in_array($group_answer_id, $explode)) {
                $return = true;
            }
        }
    }

    return $return;
}

function fetch_address_zip4($address_id) {
    global $database;
    $query = $database->query("select zip4 from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['zip4'];
}

function tep_fetch_equipment_item_reference_code($equipment_item_id) {
    global $database;
    $query = $database->query("select code from " . TABLE_EQUIPMENT_ITEMS . " where equipment_item_id = '" . $equipment_item_id . "' limit 1");
    $result = $database->fetch_array($query);
    if (!empty($result['code'])) {
        return $result['code'];
    } else {
        return false;
    }
}

function tep_fetch_equipment_item_status($equipment_item_id) {
    global $database;

    $query = $database->query("select equipment_status_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_item_id = '" . $equipment_item_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['equipment_status_id'];
}

function tep_fetch_equipment_item_order_id($equipment_item_id) {
    global $database;

    $query = $database->query("select order_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_item_id = '" . $equipment_item_id . "' and method_id = '1' order by order_id DESC limit 1");
    $result = $database->fetch_array($query);

    return $result['order_id'];
}

function tep_fetch_address_order_id($address_id) {
    global $database;

    $query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '" . $address_id . "' and order_type_id = '1' limit 1");
    $result = $database->fetch_array($query);

    return $result['order_id'];
}

function tep_fetch_order_address_id($order_id) {
    global $database;

    $query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $order_id . "' and order_type_id = '1' limit 1");
    $result = $database->fetch_array($query);

    return $result['address_id'];
}

function tep_fetch_order_date($order_id) {
    global $database;

    $query = $database->query("select date_schedualed, date_completed from " . TABLE_ORDERS . " where order_id = '" . $order_id . "' limit 1");
    $result = $database->fetch_array($query);

    if (!empty($result['date_completed'])) {
        return $result['date_completed'];
    } else {
        return $result['date_schedualed'];
    }
}

function tep_fetch_equipment_item_address($equipment_item_id) {
    global $database;

    $query = $database->query("select address_id from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where equipment_item_id = '" . $equipment_item_id . "' and equipment_status_id = '2' limit 1");
    $result = $database->fetch_array($query);

    return $result['address_id'];
}

function tep_fetch_equipment_item_install_date($address_id, $equipment_item_id) {
    global $database;

    $query = $database->query("select o.order_id, o.date_completed, o.date_schedualed from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.equipment_item_id = '" . $equipment_item_id . "' and eto.method_id = '1' and eto.order_id = o.order_id and o.address_id = a.address_id order by o.order_id limit 1");
    $result = $database->fetch_array($query);

    if (!empty($result['date_completed'])) {
        return $result['date_completed'];
    } else {
        return $result['date_schedualed'];
    }
}

function tep_fetch_equipment_item_removal_date($address_id, $equipment_item_id) {
    global $database;
    $query = $database->query("select o.order_id, o.date_completed, o.date_schedualed from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.equipment_item_id = '" . $equipment_item_id . "' and eto.method_id = '0' and eto.order_id = o.order_id and o.address_id = a.address_id order by o.order_id DESC limit 1");
    $result = $database->fetch_array($query);
    if ($result['order_id'] == NULL) {
        $query = $database->query("select o.order_id, o.date_completed, o.date_schedualed from " . TABLE_ORDERS . " o where o.address_id = '" . $address_id . "' and o.order_type_id = '3' order by o.order_id DESC limit 1");
        $result = $database->fetch_array($query);
    }
    if (!empty($result['date_completed'])) {
        return $result['date_completed'];
    } else {
        return $result['date_schedualed'];
    }
}

function tep_date_is_holiday($day, $month, $year) {
    global $database;
    $query = $database->query("select holiday_id from " . TABLE_HOLIDAYS . " where holiday_date = '".$year."-".$month."-".$day."'");
    $result = $database->fetch_array($query);
    if ($result['holiday_id'] != NULL) {
        return $result['holiday_id'];
    } else {
        return false;
    }
}

function tep_date_is_sunday($month, $day, $year) {
    $timestamp = mktime(0, 0, 0, $month, $day, $year);

    if (date("w", $timestamp) == 0) {
        return true;
    } else {
        return false;
    }
}

function tep_day_is_saturday($month, $day, $year) {
    $timestamp = mktime(0, 0, 0, $month, $day, $year);

    if (date("w", $timestamp) == 6) {
        return true;
    } else {
        return false;
    }
}

function tep_fetch_holiday_name($holiday_id) {
    // Depreciated, as TABLE_PUBLIC_HOLIDAYS is a stale table
    //global $database;
    //$query = $database->query("select name from " . TABLE_PUBLIC_HOLIDAYS . " where public_holiday_id = '" . $holiday_id . "' limit 1");
    //$result = $database->fetch_array($query);
    //return $result['name'];
    return "a holiday";
}

//Makes the list for the user to check off the items.
function tep_create_completed_order_equipment_string($order_id, $preview = false, $array = array(), $status_array = array(), $damaged = array(), $missing = array()) {
    global $database;
    if (!is_array($array)) {
        $array = array();
    }
    if (!is_array($status_array)) {
        $status_array = array();
    }
    if (!is_array($damaged)) {
        $damaged = array();
    }
    if (!is_array($missing)) {
        $missing = array();
    }

    $query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $order_id . "'");
    $return_string = '';
    foreach($query as $result){
        if (!empty($return_string)) {
            $return_string .= '<br>';
        }
        if (!$preview) {
            if (in_array($result['equipment_id'], $array)) {
                $checked = ' CHECKED';
            } else {
                $checked = '';
            }
            $return_string .= '<input type="checkbox" name="equipment[]"' . $checked . ' value="' . $result['equipment_id'] . '">&nbsp;&nbsp;' . (($result['method_id'] == '1') ? 'Installed' : 'Removed') . ' ' . $result['equipment_name'];
            if ($result['method_id'] == 0) {
                //Code to add lost/stolen equipment.
                $return_string .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="damaged[]" value="' . $result['equipment_id'] . '">&nbsp;&nbsp;Was Damaged';
                $return_string .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="missing[]" value="' . $result['equipment_id'] . '">&nbsp;&nbsp;Was Missing';
            }
        } else {
            if (!in_array($result['equipment_id'], $array)) {
                if ($result['method_id'] == '0') {
                    if (in_array($result['equipment_id'], $damaged)) {
                        $return_string .= $result['equipment_name'] . ' was found damaged at the property.';
                    }
                    if (in_array($result['equipment_id'], $missing)) {
                        $return_string .= $result['equipment_name'] . ' was not found at the property and is assumed missing.';
                    }
                } else {
                    $return_string .= '<span style="color:#FF0000;">' . $result['equipment_name'] . ' was not installed successfully and has been returned to the warehouse, details are provided below.</span>';
                }
            } else {
                if ($result['method_id'] == '0') {
                    $return_string .= $result['equipment_name'] . ' was Removed successfully and was returned to the relevent warehouse.';
                } else {
                    $return_string .= $result['equipment_name'] . ' was Installed successfully.';
                }
            }
        }
    }
    return $return_string;
}

//Makes the list for the user to check off the items.
function tep_create_completed_removal_equipment_string($order_id, $preview = false, $array = array(), $status_array = array(), $damaged = array(), $missing = array()) {
    global $database;

    if (!is_array($array)) {
        $array = array();
    }
    if (!is_array($status_array)) {
        $status_array = array();
    }
    if (!is_array($damaged)) {
        $damaged = array();
    }
    if (!is_array($missing)) {
        $missing = array();
    }
    $address_query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $order_id . "' limit 1");
    $address_result = $database->fetch_array($address_query);

    $query = $database->query("select eta.equipment_id, e.name from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eta, " . TABLE_EQUIPMENT . " e where eta.address_id = '" . $address_result['address_id'] . "' and eta.equipment_id = e.equipment_id");
    $return_string = '';
    foreach($query as $result){
        if (!empty($return_string)) {
            $return_string .= '<br><br><br>';
        }
        if (!$preview) {
            if (in_array($result['equipment_id'], $array)) {
                $checked = ' CHECKED';
            } else {
                $checked = '';
            }
            if (in_array($result['equipment_id'], $damaged)) {
                $checked = ' CHECKED';
            } else {
                $checked = '';
            }
            if (in_array($result['equipment_id'], $missing)) {
                $checked = ' CHECKED';
            } else {
                $checked = '';
            }
            $return_string .= '<input type="checkbox" id="removed_' . $result['equipment_id'] . '" name="equipment[]" value="' . $result['equipment_id'] . '"' . $checked . ' onclick="document.getElementById(\'damaged_' . $result['equipment_id'] . '\').checked = false; document.getElementById(\'missing_' . $result['equipment_id'] . '\').checked = false;">&nbsp;&nbsp;Removed ' . $result['name'] . ' Successfully';
            //Code to add lost/stolen equipment.
            $return_string .= '<br><input type="checkbox" id="damaged_' . $result['equipment_id'] . '" name="damaged[]" value="' . $result['equipment_id'] . '" onclick="document.getElementById(\'removed_' . $result['equipment_id'] . '\').checked = false; document.getElementById(\'missing_' . $result['equipment_id'] . '\').checked = false;">&nbsp;&nbsp;' . $result['name'] . ' Was Damaged';
            $return_string .= '<br><input type="checkbox" id="missing_' . $result['equipment_id'] . '" name="missing[]" value="' . $result['equipment_id'] . '" onclick="document.getElementById(\'damaged_' . $result['equipment_id'] . '\').checked = false; document.getElementById(\'removed_' . $result['equipment_id'] . '\').checked = false;">&nbsp;&nbsp;' . $result['name'] . ' Was Missing';
        } else {
            if (!in_array($result['equipment_id'], $array)) {
                if (in_array($result['equipment_id'], $damaged)) {
                    $return_string .= $result['name'] . ' was found damaged at the property.';
                }
                if (in_array($result['equipment_id'], $missing)) {
                    $return_string .= $result['name'] . ' was not found at the property and is assumed missing.';
                }
            } else {
                $return_string .= $result['name'] . ' was Removed successfully and was returned to the relevent warehouse.';
            }
        }
    }
    return $return_string;
}

function tep_repost_variables($array = array(), $parent_string = '') {
    $return_string = '';
    reset($array);
    while (list($key, $val) = each($array)) {
        if (!empty($parent_string)) {
            $current_name = $parent_string . '[' . $key . ']';
        } else {
            $current_name = $key;
        }
        if (is_array($val)) {
            $return_string .= tep_repost_variables($val, $current_name);
        } else {
            $return_string .= '<input type="hidden" name="' . $current_name . '" value="' . addslashes($val) . '">';
        }
    }
    return $return_string;
}

function tep_draw_user_status_pulldown($name = '', $selected = '') {
    global $database;
    $return = '';
    $array = array();
    $array[] = array('id' => '', 'name' => 'All');
    $array[] = array('id' => '1', 'name' => 'With Orders');
    $array[] = array('id' => '2', 'name' => 'No Orders');
    $array[] = array('id' => '3', 'name' => 'Signed Up');
    $count = count($array);
    $n = 0;
    while ($n < $count) {
        $result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
        $n++;
    }
    return tep_generate_pulldown_menu($name, $result_array, $selected);
}

function tep_zip4_is_assigned_to_area($code_from, $code_to, $ignore_installation_area = array()) {
    global $database;
    $code_from_explode = tep_break_zip4_code($code_from);
    $code_to_explode = tep_break_zip4_code($code_to);
    //Now work it out.
    $ignore_string = '';
    if (!empty($ignore_installation_area)) {
        $count = count($ignore_installation_area);
        $n = 0;
        while ($n < $count) {
            $ignore_string .= " and installation_coverage_area_id != '" . $ignore_installation_area[$n] . "' ";
            $n++;
        }
    }

    $query = $database->query("select count(installation_coverage_area_id) as count from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where zip_4_first_break_start >= '" . $code_from_explode[0] . "' and zip_4_first_break_end>= '" . $code_from_explode[1] . "' and zip_4_second_break_start <= '" . $code_to_explode[0] . "' and zip_4_second_break_end <= '" . $code_to_explode[1] . "'" . $ignore_string);
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function tep_zip4_is_assigned_to_area_bgdn($code_from, $code_to, $ignore_installation_area = array()) {
    global $database;
    $code_from_explode = tep_break_zip4_code($code_from);
    $code_to_explode = tep_break_zip4_code($code_to);
    //Now work it out.
    $ignore_string = '';
    if (!empty($ignore_installation_area)) {
        $count = count($ignore_installation_area);
        $n = 0;
        while ($n < $count) {
            $ignore_string .= " and installation_coverage_area_id != '" . $ignore_installation_area[$n] . "' ";
            $n++;
        }
    }

    $query = $database->query("select count(installation_coverage_area_id) as count from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where zip_4_first_break_start >= '" . $code_from_explode[0] . "' and ((zip_4_first_break_end <= '" . $code_from_explode[1] . "' and zip_4_second_break_end >= '" . $code_to_explode[1] . "') OR (zip_4_first_break_end >= '" . $code_from_explode[1] . "' and zip_4_second_break_end <= '" . $code_to_explode[1] . "')) and zip_4_second_break_start <= '" . $code_to_explode[0] . "' " . $ignore_string);
	//echo "select count(installation_coverage_area_id) as count from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where zip_4_first_break_start >= '" . $code_from_explode[0] . "' and zip_4_first_break_end >= '" . $code_from_explode[1] . "' and zip_4_second_break_start <= '" . $code_to_explode[0] . "' and zip_4_second_break_end <= '" . $code_to_explode[1] . "'" . $ignore_string;
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function tep_zip4_is_assigned_to_area_bgdn2($code_from, $code_to, $ignore_installation_area = array()) {
    global $database;
    $code_from_explode = tep_break_zip4_code($code_from);
    $code_to_explode = tep_break_zip4_code($code_to);
    //Now work it out.
    $ignore_string = '';
    if (!empty($ignore_installation_area)) {
        $count = count($ignore_installation_area);
        $n = 0;
        while ($n < $count) {
            $ignore_string .= " and installation_coverage_area_id != '" . $ignore_installation_area[$n] . "' ";
            $n++;
        }
    }

    $query = $database->query("select count(installation_coverage_area_id) as count from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where zip_4_first_break_start >= '" . $code_from_explode[0] . "' and ((zip_4_first_break_end <= '" . $code_from_explode[1] . "' and zip_4_second_break_end >= '" . $code_to_explode[1] . "') OR (zip_4_first_break_end >= '" . $code_from_explode[1] . "' and zip_4_second_break_end <= '" . $code_to_explode[1] . "')) and zip_4_second_break_start <= '" . $code_to_explode[0] . "' " . $ignore_string);
	//echo "select count(installation_coverage_area_id) as count from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where zip_4_first_break_start >= '" . $code_from_explode[0] . "' and zip_4_first_break_end <= '" . $code_from_explode[1] . "' and zip_4_second_break_start <= '" . $code_to_explode[0] . "' and zip_4_second_break_end >= '" . $code_to_explode[1] . "'" . $ignore_string;
    $result = $database->fetch_array($query);
    if ($result['count'] > 1) {
        return true;
    } else {
        return false;
    }
}

function tep_count_installer_orders_sort($installer_id, $day, $month, $year, $order_type_id = '', $order_status_id = '', $state_id = '', $limit = true, $sort_type = 'default') {
    global $database;
    $string = '';
    if (!empty($order_type_id)) {
        $string .= " and o.order_type_id = '" . $order_type_id . "'";
    }
    if (!empty($order_status_id)) {
        $string .= " and o.order_status_id = '" . $order_status_id . "'";
    }
    if ($limit) {
        $date_covering = mktime(0, 0, 0, $month, $day, $year);
    }
    $date_covering_end = mktime(0, 0, 0, $month, ($day + 1), $year);
    //count(o.order_id) as count
    //COUNT(DISTINCT o.order_id) as count
    //$query = $database->query("select COUNT(DISTINCT o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a left join " . TABLE_INSTALLATION_AREAS . " ia on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and itia.date_covering = '" . $date_covering . "'), " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) where o.date_schedualed >= '" . $date_covering . "' and o.date_schedualed <= '" . $date_covering_end . "'" . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " and o.address_id = a.address_id".$string." and  ((ito.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $installer_id . "')) group by o.order_id");
    if ($sort_type == 'default') {
        $query = $database->query("select count(distinct o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where " . ((!empty($date_covering)) ? " o.date_schedualed >= '" . $date_covering . "' and " : " o.order_status_id < '3' and ") . " o.order_status_id > '0' and o.date_schedualed < '" . $date_covering_end . "' and " . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " o.order_issue != '1'" . $string . " and o.address_id = a.address_id and (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $installer_id . "')");
    } elseif ($sort_type == 'day') {
        $query = $database->query("select count(distinct o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where " . ((!empty($date_covering)) ? " o.date_schedualed >= '" . $date_covering . "' and " : " o.order_status_id < '3' and ") . " o.order_status_id > '0' and o.date_schedualed < '" . $date_covering_end . "' and " . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " o.order_issue != '1'" . $string . " and o.address_id = a.address_id and (ito.installer_id IS NULL and itia.installer_id = '" . $installer_id . "')");
    } elseif ($sort_type == 'assigned') {
        $query = $database->query("select count(distinct o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where " . ((!empty($date_covering)) ? " o.date_schedualed >= '" . $date_covering . "' and " : " o.order_status_id < '3' and ") . " o.order_status_id > '0' and o.date_schedualed < '" . $date_covering_end . "' and " . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " o.order_issue != '1'" . $string . " and o.address_id = a.address_id and (ito.installer_id = '" . $installer_id . "')");
    }

    //echo "select count(distinct o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed)), " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where " . ((!empty($date_covering)) ? " o.date_schedualed >= '" . $date_covering . "' and " : " o.order_status_id < '3' and ") . " o.order_status_id > '0' and o.date_schedualed < '" . $date_covering_end . "' and " . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " o.order_issue != '1'".$string." and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $installer_id . "'))" . '<br>';
    $result = $database->fetch_array($query);


    if (!empty($result['count'])) {
        return $result['count'];
    } else {
        return '0';
    }
}

function tep_count_installer_orders($installer_id, $day, $month, $year, $order_type_id = '', $order_status_id = '', $state_id = '', $limit = true, $reverse=false) {
    global $database;
    $string = '';
    if (!empty($order_type_id)) {
        $string .= " and o.order_type_id = '" . $order_type_id . "'";
    }
    if (!empty($order_status_id)) {
        $string .= " and o.order_status_id = '" . $order_status_id . "'";
    }
    if ($limit) {
        $date_covering = mktime(0, 0, 0, $month, $day, $year);
    }
    $date_covering_end = mktime(0, 0, 0, $month, ($day + 1), $year);
    //$query = $database->query("select count(distinct o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica left join " . TABLE_INSTALLATION_AREAS . " ia on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) AND ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where " . ((!empty($date_covering)) ? " o.date_schedualed >= '" . $date_covering . "' and " : " o.order_status_id < '3' and ") . " o.order_status_id > '0' and o.date_schedualed < '" . $date_covering_end . "' and " . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " o.order_issue != '1'".$string." and o.address_id = a.address_id and ((ito.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $installer_id . "'))");
	if($reverse) {
		$query = $database->query("select count(distinct o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where " . ((!empty($date_covering)) ? " o.date_schedualed >= '" . $date_covering . "' and " : " o.order_status_id < '3' and ") . " o.order_status_id > '0' and o.date_schedualed >= '" . $date_covering_end . "' and " . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " o.order_issue != '1'" . $string . " and o.address_id = a.address_id and ((ito.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $installer_id . "'))");

	} else {
		$query = $database->query("select count(distinct o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where " . ((!empty($date_covering)) ? " o.date_schedualed >= '" . $date_covering . "' and " : " o.order_status_id < '3' and ") . " o.order_status_id > '0' and o.date_schedualed < '" . $date_covering_end . "' and " . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " o.order_issue != '1'" . $string . " and o.address_id = a.address_id and ((ito.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $installer_id . "'))");

	}


    //$query = $database->query("select count(distinct o.order_id) as count from " . TABLE_ORDERS . " o left join (" . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia, " . TABLE_INSTALLATION_AREAS . " ia, " . TABLE_INSTALLERS_TO_ORDERS . " ito, " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso) on ((((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) AND ica.installation_area_id = ia.installation_area_id) and (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) and (o.order_id = ito.order_id) and (o.order_id = otiso.order_id)) where " . ((!empty($date_covering)) ? " o.date_schedualed >= '" . $date_covering . "' and " : " o.order_status_id < '3' and ") . " o.order_status_id > '0' and o.date_schedualed < '" . $date_covering_end . "' and " . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " o.order_issue != '1'".$string." and o.address_id = a.address_id and ((ito.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $installer_id . "'))");
//echo "select count(distinct o.order_id) as count from " . TABLE_ORDERS . " o left join (" . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia, " . TABLE_INSTALLATION_AREAS . " ia, " . TABLE_INSTALLERS_TO_ORDERS . " ito, " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso) on ((((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) AND ica.installation_area_id = ia.installation_area_id) and (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) and (o.order_id = ito.order_id) and (o.order_id = otiso.order_id)) where " . ((!empty($date_covering)) ? " o.date_schedualed >= '" . $date_covering . "' and " : " o.order_status_id < '3' and ") . " o.order_status_id > '0' and o.date_schedualed < '" . $date_covering_end . "' and " . ((!empty($state_id)) ? " and ia.state_id = '" . $state_id . "'" : '') . " o.order_issue != '1'".$string." and o.address_id = a.address_id and ((ito.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $installer_id . "'))". '<br>';
    $result = $database->fetch_array($query);


    if (!empty($result['count'])) {
        return $result['count'];
    } else {
        return '0';
    }
}

function tep_count_area_orders($installation_area_id, $day, $month, $year, $order_type_id = '', $order_status_id = '') {
    global $database;
    $string = '';
    if (!empty($order_type_id)) {
        $string .= " and o.order_type_id = '" . $order_type_id . "'";
    }
    if (!empty($order_status_id)) {
        $string .= " and o.order_status_id = '" . $order_status_id . "'";
    }
    $date_covering = mktime(0, 0, 0, $month, $day, $year);
    $date_covering_end = mktime(0, 0, -1, $month, ($day + 1), $year);
    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_AREAS . " ia, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica where ia.installation_area_id = '" . $installation_area_id . "' and o.date_schedualed >= '" . $date_covering . "' and o.date_schedualed <= '" . $date_covering_end . "' and o.order_status_id > '0' and o.order_status_id < '3' and o.address_id = a.address_id" . $string . " and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))");

    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_address_post_is_allowed($house_number, $street_name, $city, $county_id, $state_id) {
    global $database;
    $street_name = str_replace('  ', ' ', $street_name);
    $query = $database->query("select count(post_not_allowed_id) as count from " . TABLE_POST_NOT_ALLOWED . " where ((house_number_range_start <= '" . $house_number . "' and house_number_range_end >= '" . $house_number . "') or (house_number_range = '')) and (street_name = '" . $street_name . "' or street_name = '') and (city = '" . $city . "' or city = '') and (county_id = '" . $county_id . "' or county_id = '0') and (state_id = '" . $state_id . "' or state_id = '0')");
    $result = $database->fetch_array($query);
    if ($result['count'] == 0) {
        return true;
    } else {
        return false;
    }
}

function tep_date_is_saturday($time_stamp) {
    if (date("w", $time_stamp) == 6) {
        return true;
    } else {
        return false;
    }
}

function tep_date_is_rush($time_stamp) {
    $day = date("d");
    $month = date("m");
    $year = date("Y");

    if (($time_stamp >= (mktime(0, 0, 0, $month, $day + 1, $year))) && ($time_stamp <= (mktime(0, 0, 0, $month, $day + 2, $year)))) {
        return true;
    } else {
        return false;
    }
}

function tep_address_is_assigned_to_user($address_id, $user_id) {
    global $database;
    $query = $database->query("select count(address_id) as count from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . $address_id . "' and user_id = '" . $user_id . "'");
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function tep_fetch_install_date($address_id) {
    global $database;
    $query = $database->query("select date_schedualed from " . TABLE_ORDERS . " where address_id = '" . $address_id . "' and order_type_id = '1' limit 1");
    $result = $database->fetch_array($query);
    return $result['date_schedualed'];
}

//Make this nice and secure.
function tep_secure_credit_card_number($number) {
    $length = strlen($number);
    $first4 = substr($number, 0, 4);
    $last2 = substr($number, -2);
    $xfill = ($length - 6);
    $n = 0;
    $return_string = $first4;
    while ($n < $xfill) {
        if (($n % 4) == 0) {
            $return_string .='&nbsp;';
        }
        $return_string.='X';
        $n++;
    }
    $return_string.= '&nbsp;' . $last2;
    return $return_string;
}

function tep_secure_credit_card_number_bgdn($number) {
    $length = strlen($number);
    $first4 = substr($number, 0, 4);
    $last2 = substr($number, -2);
    $xfill = ($length - 6);
    $n = 0;
    $return_string = $first4;
    while ($n < $xfill) {
        if (($n % 4) == 0) {
            $return_string .=' ';
        }
        $return_string.='X';
        $n++;
    }
    $return_string.= ' ' . $last2;
    return $return_string;
}

function tep_fetch_address_information($address_id) {
    global $database;
    $query = $database->query("select a.house_number, a.street_name, a.city, a.zip, a.zip4, a.zip4, c.name as county_name, s.name as state_name from " . TABLE_ADDRESSES . " a, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where a.address_id = '" . $address_id . "' and a.state_id = s.state_id and a.county_id = c.county_id limit 1");
    $result = $database->fetch_array($query);
    return $result;
}

function tep_fetch_month_total($year, $month, $user_id, $order_type_id = '0') {
    global $database;
    $start_of_month = mktime(0, 0, 0, $month, 0, $year);
    $end_of_month = mktime(0, 0, -1, $month + 1, 0, $year);
    if ($order_type_id != '0') {
        $type_string = " and order_type_id = '" . $order_type_id . "'";
        $type_select = 'type_running_total';
    } else {
        $type_string = '';
        $type_select = 'running_total';
    }
    $account = new account($user_id);

    $query = $database->query("select sum(total) as sum from " . TABLE_ACCOUNT_ITEMS . " where account_id = '" . $account->return_account_id() . "' and type = '1' " . ((!empty($order_type_id)) ? " and order_type_id = '" . $order_type_id . "' " : '') . " and status_id > '0' and month_added = '" . $month . "' and year_added = '" . $year . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['sum'];
}

function fetch_new_month_start($year, $month, $account_id) {
    global $database;
    $query = $database->query("select running_total from " . TABLE_ACCOUNT_ITEMS . " where account_id = '" . $account_id . "' and year_added = '" . $year . "' and month_added = '" . $month . "' order by account_item_id DESC limit 1");
    $result = $database->fetch_array($query);
    if (!empty($result['running_total'])) {
        return $result['running_total'];
    } else {
        return 0;
    }
}

function tep_fetch_installation_availability($zip4_code) {
    global $database;
    $return = false;
    if (tep_zip4_is_valid($zip4_code)) {
        $zip4_break = tep_break_zip4_code($zip4_code);
        $query = $database->query("select count(ic.installation_coverage_area_id) as count from " . TABLE_INSTALLATION_COVERAGE_AREAS . " ic, " . TABLE_INSTALLATION_AREAS . " ia where ((ic.zip_4_first_break_start < '" . $zip4_break[0] . "') or (ic.zip_4_first_break_start = '" . $zip4_break[0] . "' and ic.zip_4_first_break_end <= '" . $zip4_break[1] . "')) and ((ic.zip_4_second_break_start > '" . $zip4_break[0] . "') or (ic.zip_4_second_break_start = '" . $zip4_break[0] . "' and ic.zip_4_second_break_end >= '" . $zip4_break[1] . "')) and ic.installation_area_id = ia.installation_area_id and ia.active = 1  limit 1");
        $result = $database->fetch_array($query);
        if ($result['count'] > 0) {
            $return = true;
        }
    }
    return $return;
}

function tep_create_button_link($button_name, $alt = '', $params = '') {
    global $language;
    $return_string = '';
    $image_string = 'buttons/' . $language . '/button_' . $button_name . '.gif';
    if (is_file(DIR_IMAGES . $image_string)) {
        list($width, $height, $type, $attr) = getimagesize(DIR_IMAGES . $image_string);
        $return_string = '<img src="images/' . $image_string . '" height="' . $height . '" width="' . $width . '" alt="' . $alt . '" style="cursor:pointer;" border="0"' . $params . ' />';
    }
    return $return_string;
}

function tep_fetch_array_type($account_id) {
    global $database;
    $query = $database->query("select agency_id, user_id from " . TABLE_ACCOUNTS . " where account_id = '" . $account_id . "' limit 1");
    $result = $database->fetch_array($query);
    if (!empty($result['user_id'])) {
        return 'agent';
    } else {
        return 'agency';
    }
}

function tep_create_button_submit($button_name, $alt = '', $params = '') {

    global $language;
    $return_string = '';
    $image_string = 'buttons/' . $language . '/button_' . $button_name . '.gif';
    if (is_file(DIR_IMAGES . $image_string)) {
        list($width, $height, $type, $attr) = getimagesize(DIR_IMAGES . $image_string);
        $return_string = '<input type="image" src="images/' . $image_string . '" height="' . $height . '" width="' . $width . '" alt="' . $alt . '"' . $params . ' value="' . $alt . '" />';
    } else {
        $return_string = '<input name="'.$button_name.'" type="submit" value="' . $alt . '" ' . $params . ' />';
    }
    return $return_string;
}

function tep_count_news_items($user_group_id = '') {
    global $database;
    $query = $database->query("select count(news_item_id) as count from " . TABLE_NEWS_ITEMS . " where user_group_id = '0'" . ((!empty($user_group_id)) ? " or user_group_id = '" . $user_group_id . "'" : '') . " limit 1");
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function tep_page_has_help_items($page_url) {
    //Basically checks it he user can view the page.
    global $database, $user;
    if ($user->user_is_logged()) {
        $extra_string = " and ugtp.user_group_id = '" . $user->fetch_user_group_id() . "'";
    } else {
        $extra_string = '';
    }
    $page_name = substr($page_url, 0, strpos($page_url, '.')) . '.php';

    $query = $database->query("select count(hi.help_item_id) as count from " . TABLE_HELP_ITEMS . " hi, " . TABLE_PAGES . " p left join " . TABLE_USER_GROUPS_TO_PAGES . " ugtp on (p.page_id = ugtp.page_id" . $extra_string . ") where p.page_url = '" . $page_name . "' and p.page_id = hi.page_id and (p.page_lock_status = '0' or ugtp.page_id is not NULL)");
    $result = $database->fetch_array($query);
    if ($result['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function tep_help_item_exists($help_item_id) {
    global $database, $user;
    if ($user->user_is_logged()) {
        $extra_string = " or ugtp.user_group_id != '" . $user->fetch_user_group_id() . "'";
        $user_group_id = $user->fetch_user_group_id();
    } else {
        $extra_string = '';
        $user_group_id = '';
    }
    $query = $database->query("select count(hi.help_item_id) as count from " . TABLE_HELP_ITEMS . " hi left join " . TABLE_PAGES . " p on (hi.page_id = p.page_id) left join " . TABLE_USER_GROUPS_TO_PAGES . " ugtp on (hi.page_id = ugtp.page_id" . $extra_string . ") where hi.help_item_id = '" . $help_item_id . "'and (p.page_lock_status = '0' or (ugtp.page_id is not NULL and ugtp.user_group_id = '" . $user_group_id . "')) limit 1");
    $result = $database->fetch_array($query);
    if ($result['count'] < 1) {
        return false;
    } else {
        return true;
    }
}

function tep_help_group_exists($help_group_id) {
    global $database, $user;
    if ($user->user_is_logged()) {
        $extra_string = " or ugtp.user_group_id != '" . $user->fetch_user_group_id() . "'";
        $user_group_id = $user->fetch_user_group_id();
    } else {
        $extra_string = '';
        $user_group_id = '';
    }
    $query = $database->query("select count(hi.help_item_id) as count from " . TABLE_HELP_ITEMS . " hi left join " . TABLE_PAGES . " p on (hi.page_id = p.page_id) left join " . TABLE_USER_GROUPS_TO_PAGES . " ugtp on (hi.page_id = ugtp.page_id" . $extra_string . ") where hi.help_group_id = '" . $help_group_id . "'and (p.page_lock_status = '0' or (ugtp.page_id is not NULL and ugtp.user_group_id = '" . $user_group_id . "')) limit 1");
    $result = $database->fetch_array($query);
    if ($result['count'] < 1) {
        return false;
    } else {
        return true;
    }
}

function tep_help_group_has_items($help_group_id) {
    global $database, $user;
    if ($user->user_is_logged()) {
        $extra_string = " or ugtp.user_group_id != '" . $user->fetch_user_group_id() . "'";
        $user_group_id = $user->fetch_user_group_id();
    } else {
        $extra_string = '';
        $user_group_id = '';
    }
    $query = $database->query("select count(hi.help_item_id) as count from " . TABLE_HELP_ITEMS . " hi left join " . TABLE_PAGES . " p on (hi.page_id = p.page_id) left join " . TABLE_USER_GROUPS_TO_PAGES . " ugtp on (hi.page_id = ugtp.page_id" . $extra_string . ") where hi.help_group_id = '" . $help_group_id . "'and (p.page_lock_status = '0' or (ugtp.page_id is not NULL and ugtp.user_group_id = '" . $user_group_id . "')) limit 1");
    $result = $database->fetch_array($query);
    if ($result['count'] < 1) {
        return false;
    } else {
        return true;
    }
}

function count_help_items_in_group($help_group_id) {
    global $database, $user;
    if ($user->user_is_logged()) {
        $extra_string = " or ugtp.user_group_id != '" . $user->fetch_user_group_id() . "'";
        $user_group_id = $user->fetch_user_group_id();
    } else {
        $extra_string = '';
        $user_group_id = '';
    }
    $query = $database->query("select count(DISTINCT hi.help_item_id) as count from " . TABLE_HELP_ITEMS . " hi left join " . TABLE_PAGES . " p on (hi.page_id = p.page_id) left join " . TABLE_USER_GROUPS_TO_PAGES . " ugtp on (hi.page_id = ugtp.page_id" . $extra_string . ") where hi.help_group_id = '" . $help_group_id . "' and (p.page_lock_status = '0' or (ugtp.page_id is not NULL and ugtp.user_group_id = '" . $user_group_id . "'))");
    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_fetch_page_id($page_url) {
    global $database;
    $query = $database->query("select page_id from " . TABLE_PAGES . " where page_url = '" . $page_url . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['page_id'];
}

function tep_fetch_help_group_details($help_group_id) {
    global $database, $language_id;
    $query = $database->query("select help_group_name, help_group_description from " . TABLE_HELP_GROUPS_DESCRIPTION . " where help_group_id = '" . $help_group_id . "' and language_id = '" . $language_id . "' limit 1");
    $result = $database->fetch_array($query);

    $return_array = array('name' => $result['help_group_name'], 'description' => $result['help_group_description']);

    return $return_array;
}

function tep_fetch_page_name($page_id) {
    global $database, $language_id;
    $query = $database->query("select name from " . TABLE_PAGES_DESCRIPTION . " where page_id = '" . $page_id . "' and language_id = '" . $language_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['name'];
}

function fetch_allowed_page_group($page_id) {
    global $database;

    $query = $database->query("select p.page_lock_status, ug.user_group_id, ug.name as user_group_name from " . TABLE_PAGES . " p left join " . TABLE_USER_GROUPS_TO_PAGES . " ugtp on (p.page_id = ugtp.page_id) left join " . TABLE_USER_GROUPS . " ug on (ugtp.user_group_id = ug.user_group_id) where p.page_id = '" . $page_id . "' limit 1");
    $result = $database->fetch_array($query);

    if ($result['page_lock_status'] == '0') {
        return 'All';
    } else {
        return $result['user_group_name'];
    }
}

function tep_show_order_footer($prefix = '') {
    global $page;
    $stages = array($prefix . 'order_create_address.php' => 'Property Address',
        $prefix . 'order_create_special.php' => 'Special Instructions and Extras',
        $prefix . 'order_create_payment.php' => 'Payment Information',
        $prefix . 'order_create_confirmation.php' => 'Final Review',
        $prefix . 'order_create_success.php' => 'Finished');

    $page_name = $page->page_name;

    if ($page_name == 'order_create_success.php') {
        $found = true;
    } else {
        $found = false;
    }

    $return_string = '';
    reset($stages);
    while (list($key, $val) = each($stages)) {
        if (!empty($return_string)) {
            $return_string .= ' - ';
        }
        if ($found) {
            $return_string .= '<span class="spanOrderFooterFuture">' . $val . '</span>';
        } elseif ($key == $page_name) {
            $return_string .= '<span style="color:#000000; font-weight:bold;">' . $val . '</span>';
            $found = true;
        } else {
            $return_string .= '<a href="' . $key . '" class="spanOrderFooterPast">' . $val . '</a>';
        }
    }

    return $return_string;
}

function tep_show_pay_invoice_footer($prefix = '') {
    global $page;
    $stages = array($prefix . 'pay_invoice_payment.php' => 'Payment Information',
        $prefix . 'pay_invoice_confirmation.php' => 'Final Review',
        $prefix . 'pay_invoice_success.php' => 'Finished');

    $page_name = $page->page_name;

    if ($page_name == 'pay_invoice_success.php') {
        $found = true;
    } else {
        $found = false;
    }

    $return_string = '';
    reset($stages);
    while (list($key, $val) = each($stages)) {
        if (!empty($return_string)) {
            $return_string .= ' - ';
        }
        if ($found) {
            $return_string .= '<span class="spanOrderFooterFuture">' . $val . '</span>';
        } elseif ($key == $page_name) {
            $return_string .= '<span style="color:#000000; font-weight:bold;">' . $val . '</span>';
            $found = true;
        } else {
            $return_string .= '<a href="' . $key . '" class="spanOrderFooterPast">' . $val . '</a>';
        }
    }

    return $return_string;
}

function tep_show_deferred_footer($prefix = '') {
    global $page;
    $stages = array($prefix . 'order_deferred_payment.php' => 'Payment Information',
        $prefix . 'order_deferred_confirmation.php' => 'Final Review',
        $prefix . 'order_deferred_success.php' => 'Finished');

    $page_name = $page->page_name;

    if ($page_name == 'order_deferred_success.php') {
        $found = true;
    } else {
        $found = false;
    }

    $return_string = '';
    reset($stages);
    while (list($key, $val) = each($stages)) {
        if (!empty($return_string)) {
            $return_string .= ' - ';
        }
        if ($found) {
            $return_string .= '<span class="spanOrderFooterFuture">' . $val . '</span>';
        } elseif ($key == $page_name) {
            $return_string .= '<span style="color:#000000; font-weight:bold;">' . $val . '</span>';
            $found = true;
        } else {
            $return_string .= '<a href="' . $key . '" class="spanOrderFooterPast">' . $val . '</a>';
        }
    }

    return $return_string;
}

function zip4_is_deliverable($zip4) {
    global $database;
    if (tep_zip4_is_valid($zip4)) {
        $break = tep_break_zip4_code($zip4);

        $query = $database->query("select installation_coverage_area_id from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where (zip_4_first_break_start < '" . $break[0] . "' and '" . $break[0] . "' < zip_4_second_break_start) or (zip_4_first_break_start <= '" . $break[0] . "' and '" . $break[0] . "' < zip_4_second_break_start and zip_4_first_break_end <= '" . $break[1] . "' and zip_4_second_break_end >= '" . $break[1] . "') or (zip_4_first_break_start < '" . $break[0] . "' and '" . $break[0] . "' <= zip_4_second_break_start and zip_4_first_break_end <= '" . $break[1] . "' and zip_4_second_break_end >= '" . $break[1] . "') or (zip_4_first_break_start <= '" . $break[0] . "' and '" . $break[0] . "' <= zip_4_second_break_start and zip_4_first_break_end <= '" . $break[1] . "' and zip_4_second_break_end >= '" . $break[1] . "') or (zip_4_first_break_start <= '" . $break[0] . "' and zip_4_first_break_end <= '" . $break[1] . "' and '" . $break[0] . "' < zip_4_second_break_start) limit 1");
        $result = $database->fetch_array($query);

        if ($result['installation_coverage_area_id'] != NULL) {
            return true;
        }
    }
    return false;
}

function tep_fetch_zip4_service_area($zip4) {
    global $database;
    $break = tep_break_zip4_code($zip4);
    if (!is_array($break) || (count($break) != 2)) {
        return;
    }

    $zip4compare = sprintf("%05d%04d", (int) $break[0], (int) $break[1]);

    $query = $database->query("SELECT MIN(service_area_id) AS service_area_id FROM " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia WHERE CONCAT(zip_4_first_break_start, zip_4_first_break_end) <= '{$zip4compare}' AND '{$zip4compare}' <= CONCAT(zip_4_second_break_start, zip_4_second_break_end) AND ia.installation_area_id = ica.installation_area_id");
    $result = $database->fetch_array($query);

    return $result['service_area_id'];
}

function tep_fetch_service_area_cost($service_area_id) {
    global $database;

    $query = $database->query("select surcharge from " . TABLE_SERVICE_AREAS . " where service_area_id = '" . $service_area_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['surcharge'];
}

function tep_fetch_service_area_window($service_area_id) {
    global $database;

    $query = $database->query("select installation_window from " . TABLE_SERVICE_AREAS . " where service_area_id = '" . $service_area_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['installation_window'];
}

function tep_user_requires_deposit($user_id) {
    global $database;
    $query = $database->query("select require_deposit, deposit_remaining_count from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
    $result = $database->fetch_array($query);

    if ($result['require_deposit'] == '1') {
        return true;
    } else {
        return false;
    }
}

function tep_update_user_deposit($user_id) {
    global $database;
    $query = $database->query("select require_deposit, deposit_remaining_count from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
    $result = $database->fetch_array($query);

    if ($result['deposit_remaining_count'] > 0) {
        $deposit_remaining_count = ($result['deposit_remaining_count'] - 1);
        if ($deposit_remaining_count < 1) {
            $require_deposit = 0;
        } else {
            $require_deposit = 1;
        }
        $database->query("update " . TABLE_USERS . " set require_deposit = '" . $require_deposit . "', deposit_remaining_count = '" . $deposit_remaining_count . "' where user_id = '" . $user_id . "' limit 1");
    }
}

function tep_fetch_agent_billing_method_id($agent_id) {
    global $database;

    $query = $database->query("select billing_method_id from " . TABLE_USERS . " where user_id = '" . $agent_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['billing_method_id'];
}

function fetch_extra_cost_array($order_timestamp) {
    $return_array = array();
    if ((date("Y", $order_timestamp) == date("Y")) && (date("n", $order_timestamp) == date("n")) && (date("d", $order_timestamp) == date("d"))) {
        $return_array[] = 'rush_install';
    }
    if (date("w", $order_timestamp) == '6') {
        $return_array[] = 'saturday_install';
    }
    return $return_array;
}

function tep_fetch_extra_cost($extra_array) {
    global $database;
    if (!is_array($extra_array)) {
        $extra_array = fetch_extra_cost_array($extra_array);
    }
    $extra_cost = 0;
    if (in_array('rush_install', $extra_array)) {
        //Tis today.
        $query = $database->query("select installation_cost from " . TABLE_SPECIAL_INSTALLATION_COSTS . " where code = 'rush_install' limit 1");
        $result = $database->fetch_array($query);

        $extra_cost += $result['installation_cost'];
    }
    if (in_array('saturday_install', $extra_array)) {
        //Tis saturday.
        $query = $database->query("select installation_cost from " . TABLE_SPECIAL_INSTALLATION_COSTS . " where code = 'saturday_install' limit 1");
        $result = $database->fetch_array($query);

        $extra_cost += $result['installation_cost'];
    }
    return 0;
    return $extra_cost;
}

function tep_fetch_extra_cost_string($extra_array) {
    global $database;
    $extra_cost_string = '';
    if (!is_array($extra_array)) {
        $extra_array = fetch_extra_cost_array($extra_array);
    }

    if (in_array('rush_install', $extra_array)) {
        //Tis today.
        $query = $database->query("select name from " . TABLE_SPECIAL_INSTALLATION_COSTS . " where code = 'rush_install' limit 1");
        $result = $database->fetch_array($query);

        $extra_cost_string .= $result['name'];
    }
    if (in_array('saturday_install', $extra_array)) {
        //Tis saturday.
        $query = $database->query("select name from " . TABLE_SPECIAL_INSTALLATION_COSTS . " where code = 'saturday_install' limit 1");
        $result = $database->fetch_array($query);

        if (!empty($extra_cost_string)) {
            $extra_cost_string .= ' and ';
        }

        $extra_cost_string .= $result['name'];
    }
    return '';
    return $extra_cost_string;
}

function tep_fetch_extra_cost_code_string($extra_array) {
    global $database;
    $extra_cost_string = '';

    if (!is_array($extra_array)) {
        $extra_array = fetch_extra_cost_array($extra_array);
    }

    if (in_array('rush_install', $extra_array)) {
        //Tis today.
        $extra_cost_string .= 'rush_install';
    }
    if (in_array('saturday_install', $extra_array)) {
        //Tis saturday.
        $extra_cost_string .= 'saturday_install';
    }
    return '';
    return $extra_cost_string;
}

//Installer payment functions.
function tep_fetch_base_type_payment($installer_id, $order_type_id) {
    global $database;
    $query = $database->query("select installation_cost from " . TABLE_ORDER_TYPES . " where order_type_id = '" . $order_type_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['installation_cost'];
}

function tep_fetch_post_type_payment($installer_id, $method, $post_type_id) {
    global $database;
    if ($method == '1') {
        //Install.
        $string = 'installer_install_payment';
    } else {
        //Remove.
        $string = 'installer_remove_payment';
    }
    $query = $database->query("select " . $string . " as total from " . TABLE_EQUIPMENT . " where equipment_type_id = '" . $post_type_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['total'];
}

function tep_fetch_extended_area_modified($service_area_id, $total) {
    global $database;
    $query = $database->query("select installation_cost, installer_modifier from " . TABLE_SERVICE_AREAS . " where service_area_id = '" . $service_area_id . "' limit 1");
    $result = $database->fetch_array($query);

    if (!empty($result['installation_cost'])) {
        $total += $result['installation_cost'];
    }
    if (!empty($result['installer_modifier'])) {
        $total *= $result['installer_modifier'];
    }

    return $total;
}

function tep_fetch_equipment_type_id($equipment_id) {
    global $database;
    //Fetch the equipment_type_id from the equipment_id;
    $query = $database->query("select equipment_type_id from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['equipment_type_id'];
}

//Change the order to a service call and charge the amount requested.  Also remove any schedualed removals.
function tep_change_to_service_call($order_id, $charge_amount = '20')
{
    global $database;

    $order = new orders('fetch', $order_id);
    $user_id = $order->user_id;

    $data = $order->return_result();
    $data['order_type_id'] = ORDER_TYPE_SERVICE;
    $data['order_status_id'] = ORDER_STATUS_COMPLETED;
    $data['sc_reason'] = '7';

    $order = new orders('update', $order_id, $data, $user_id);

    $removal_query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '" . $order->fetch_data_item('address_id') . "' and order_type_id = '3' limit 1");
    $removal_result = $database->fetch_array($removal_query);

    $database->query("delete from " . TABLE_ORDERS . " where order_id = '" . $removal_result['order_id'] . "' limit 1");
    $database->query("delete from " . TABLE_ORDERS_DESCRIPTION . " where order_id = '" . $removal_result['order_id'] . "' limit 1");
    $database->query("delete from " . TABLE_ORDERS_HISTORY . " where order_id = '" . $removal_result['order_id'] . "' limit 1");
    $database->query("delete from " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " where order_id = '" . $removal_result['order_id'] . "' limit 1");
    $database->query("delete from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $removal_result['order_id'] . "' limit 1");

    tep_release_equipment($removal_result['order_id'], true);
    tep_release_equipment($order_id, false);
    tep_create_order_history($order_id, '3', 'This order was converted to a Service Call.', true);
}

//Release all equipment in said order.  For use when an order failed.
function tep_release_equipment($order_id, $remove_order_entrys = false) {
    global $database;
    $query = $database->query("select equipment_to_order_id, equipment_item_id,	equipment_status_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $order_id . "'");
    while ($result = $database->fetch_array($query)) {
        $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
        if ($remove_order_entrys) {
            $database->query("delete from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_to_order_id = '" . $result['equipment_to_order_id'] . "' limit 1");
        } else {
            $database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_to_order_id = '" . $result['equipment_to_order_id'] . "' limit 1");
        }
    }
}

function tep_draw_flag($flag_id) {
    if ($flag_id == '1') {
        return '<img src="images/redflag.gif" height="24" width="20">';
    }
}

function tep_add_equipment_item_history($equipment_item_id, $history_status_id, $history_status_description = '', $order_id = '', $address_id = '') {
    global $database;
    $status_name = tep_get_equipment_history_status_name($history_status_id);

    $database->query("insert into " . TABLE_EQUIPMENT_ITEMS_HISTORY . " (equipment_item_id, order_id, address_id, history_status_id, history_status_name, date_added, history_status_description) values ('" . $equipment_item_id . "', '" . $order_id . "', '" . $address_id . "', '" . $history_status_id . "', '" . $status_name . "', '" . time() . "', '" . $history_status_description . "')");
    //echo "insert into " . TABLE_EQUIPMENT_ITEMS_HISTORY . " (equipment_item_id, order_id, address_id, history_status_id, history_status_name, date_added, history_status_description) values ('" . $equipment_item_id . "', '" . $order_id . "', '" . $address_id . "', '" . $history_status_id . "', '" . $status_name . "', '" . time() . "', '" . $history_status_description . "')" . '<br>';
    //All done.
}

function tep_get_equipment_history_status_name($history_status_id) {
    global $database;
    $return_string = '';
    /* switch($history_status_id) {
      case '0':
      //Beginning case, its zero as its only ever used once.
      $return_string = 'Entered into System.';
      break;
      case '1':
      $return_string = 'Awaiting Install.';
      break;
      case '2':
      $return_string = 'Installed at Property.';
      break;
      case '3':
      $return_string = 'Successfully Removed from Property and awaiting next Install.';
      break;
      case '4':
      $return_string = 'Found Damaged at Property.';
      break;
      case '5':
      $return_string = 'Missing from Property.';
      break;
      case '6':
      $return_string = 'Successfully Inventoried.';
      break;
      case '7':
      $return_string = 'Unable to be Installed at Property and awaiting next Install.';
      break;
      } */
    $query = $database->query("select equipment_status_name from " . TABLE_EQUIPMENT_STATUSES . " where equipment_status_id = '" . $history_status_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['equipment_status_name'];
}

function tep_count_unassigned_orders() {
    global $database;

    $count = 0;
    $query = $database->query("select o.order_id, o.date_schedualed, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city, o.order_issue from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where  o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id > 0 and o.order_status_id != '3' and o.order_status_id != '4' and o.address_id = a.address_id");
    foreach($query as $result){
        if ((tep_fetch_true_assigned_installer($result['order_id']) == '') && (tep_fetch_assigned_order_installer($result['order_id']) == '')) {
            $count++;
        }
    }
    return $count;
}

function tep_fetch_address_county_id($address_id) {
    global $database;
    $query = $database->query("select county_id from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['county_id'];
}

function tep_fetch_order_payment_method($oID) {
    global $database;
    $query = $database->query("select payment_method from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
    $result = $database->fetch_array($query);

    return $result['payment_method'];
}

function tep_count_posts_of_status($status_id = '') {
    global $database;

    $total_count = 0;
    $query = $database->query("select equipment_id from " . TABLE_EQUIPMENT . " where equipment_type_id = '1'");
    foreach($query as $result){
        $count_query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $result['equipment_id'] . "'" . ((!empty($status_id)) ? " and equipment_status_id = '" . $status_id . "'" : ''));
        //echo "select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $result['equipment_id'] . "'" . ((!empty($status_id)) ? " and equipment_status_id = '" . $status_id . "'" : ''). '<br>';
        $count_result = $database->fetch_array($count_query);

        $total_count += $count_result['count'];
    }

    return $total_count;
}

function tep_count_agency_order_managers($agency_id) {
    global $database;
    $query = $database->query("select count(u.user_id) as count from " . TABLE_USERS . " u, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.agency_id = '" . $agency_id . "' and u.user_id = utug.user_id and utug.user_group_id = '4'");
    $result = $database->fetch_array($query);

    return $result['count'];
}

function tep_fetch_order_manager_agency($user_id) {
    global $database;
    $query = $database->query("select agency_id from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
    $result = $database->fetch_array($query);
    return $result['agency_id'];
}

function tep_fetch_installed_post_type($address_id) {
    global $database;
    $query = $database->query("select e.name, eita.equipment_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita where eita.address_id = '" . $address_id . "' and eita.equipment_id = e.equipment_id and e.equipment_type_id = '1' limit 1");
    $result = $database->fetch_array($query);

    return $result['name'];
}

function tep_max_removal_window($timestamp) {
    $max_time = mktime(date("G", $timestamp), date("i", $timestamp), date("s", $timestamp), date("n", $timestamp) + 8, date("d", $timestamp), date("Y", $timestamp));
    if ($max_time < mktime(date("G"), date("i"), date("s"), date("n") + 4, date("d"), date("Y"))) {
        $max_time = mktime(date("G"), date("i"), date("s"), date("n") + 4, date("d"), date("Y"));
    }
    return $max_time;
}

function change_color($color) {
    global $pdf;

    list($r, $g, $b) = explode(',', $color);
    $pdf->setColor($r, $g, $b);
}

function tep_fetch_order_address_zip4($order_id) { //MJP
    global $database;

    $query = $database->query("SELECT zip4 FROM " . TABLE_ORDERS . " inner join " . TABLE_ADDRESSES . " on orders.address_id = addresses.address_id WHERE order_id = " . $order_id . " limit 1");
    $result = $database->fetch_array($query);
    return $result['zip4'];
}

function tep_format_order_email($order_id, $template, $aom_id = "", $applied_credit = 0) {
    global $database;

    $order = new orders('fetch', $order_id);
    $data = $order->fetch_order();

    // Deferred Billing information is retrieved from the session
    $deferred_total = tep_fill_variable('deferred_total', 'session', 0);
    $deferred_credit = tep_fill_variable('deferred_credit', 'session', 0);
    $deferred_transactions = tep_fill_variable('deferred_transactions', 'session', array());

    $query = $database->query("select u.user_id, u.agent_id, u.email_address, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $data['user_id'] . "' and u.user_id = ud.user_id limit 1");
    $result = $database->fetch_array($query);

    $service_area_window = tep_fetch_service_area_window(tep_fetch_zip4_service_area($data['zip4']));
    if ($service_area_window == 0) {
        $service_area_window = 5;
    }

    $email_template = new email_template($template);
    $email_template->load_email_template();
    $order_type = $data['order_type_id'];
    $email_template->set_email_template_variable('ORDER_TYPE', tep_get_order_type_name($data['order_type_id']));

    $send_aom_email = false;
    if ($template == "aom_order_confirm") {
        $aom_query = $database->query("select u.user_id, u.email_address, ud.firstname, ud.lastname, utug.user_group_id from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = '" . $aom_id . "' and u.user_id = ud.user_id and utug.user_id = u.user_id limit 1");
        $aom_result = $database->fetch_array($aom_query);
        $email_template->set_email_template_variable('AOM_NAME', $aom_result['firstname'] . ' ' . $aom_result['lastname']);
        $send_aom_email = true;
        if ($aom_result['user_group_id'] == 4) {
            $email_template->set_email_template_variable('AOM_TITLE', 'AOM');
        } else {
            $email_template->set_email_template_variable('AOM_TITLE', 'Admin');
            $send_aom_email = false;
        }
    }

    $cost_line_item_template = "                    <tr>\n";
    $cost_line_item_template.= "                        <td width=\"200\"><b>&NAME: </b></td>\n";
    $cost_line_item_template.= "                        <td width=\"300\">\$&VALUE</td>\n";
    $cost_line_item_template.= "                    </tr>\n";

    $cost_line_items = "";

    if ($order_type == 1 && ($data['miss_utility_yes_no'] == 'yes' || ($data['lamp_yes_no'] == 'yes' && $data['lamp_use_gas'] != 'no'))) {
        $miss_utility_window = $cost_line_item_template;
        $miss_utility_window = str_replace("&NAME", "Miss Utility Marking Window", $miss_utility_window);
        $miss_utility_window = str_replace("\$&VALUE", date("n/d/Y", subtract_business_days($data['date_schedualed'], MISS_UTILITY_DELAY)) . " - " . date("n/d/Y", subtract_business_days($data['date_schedualed'], 1)), $miss_utility_window);
        $email_template->set_email_template_variable('UTILITY_WINDOW', $miss_utility_window);
        $email_template->set_email_template_variable('MISS_UTILITY', "<tr><td colspan=\"2\">&nbsp;&nbsp;&nbsp;<i>Due to the requirements of Miss Utility, the install has been delayed " . MISS_UTILITY_DELAY . " business days.</i></td><tr/>");
    } else {
        $email_template->set_email_template_variable('MISS_UTILITY', '');
        $email_template->set_email_template_variable('UTILITY_WINDOW', '');
    }

    if ($order_type == 1) {
        if ($data['miss_utility_yes_no'] == "yes") {
            $mu_reason_txt = "Miss Utility call requested.";
        } else if ($data['lamp_yes_no'] == "no") {
            $mu_reason_txt = "No lamp on property.";
        } else if ($data['lamp_use_gas'] == "yes") {
            $mu_reason_txt = "Gas lamp on property.";
        } else if ($data['lamp_use_gas'] == "unsure") {
            $mu_reason_txt = "Possible gas lamp on property.";
        } else if ($data['lamp_use_gas'] == "no") {
            $mu_reason_txt = "No gas lamp on property.";
        }

        $mu_reason = $cost_line_item_template;
        $mu_reason = str_replace("&NAME", "Miss Utility", $mu_reason);
        $mu_reason = str_replace("\$&VALUE", $mu_reason_txt, $mu_reason);
        $email_template->set_email_template_variable('MU_REASON', $mu_reason);
    } else {
        $email_template->set_email_template_variable('MU_REASON', '');
    }


    if (tep_date_is_saturday($data['date_schedualed'])) {
        $service_area_window++;
        $saturday_install_note = "<i>Our installers occasionally complete jobs on Saturdays, so that is why the Saturday date is included above.  Mostly likely, the job will be completed in the next two business days following Saturday.</i>";
    } else {
        $saturday_install_note = "";
    }

    $email_template->set_email_template_variable('HOUSE_NUMBER', $data['house_number']);
    $email_template->set_email_template_variable('AGENT_NAME', $result['firstname'] . ' ' . $result['lastname']);
    $email_template->set_email_template_variable('AGENT_ID', $result['agent_id']);
    $email_template->set_email_template_variable('AGENCY_NAME', $result['name']);
    $email_template->set_email_template_variable('STREET_NAME', $data['street_name']);
    $email_template->set_email_template_variable('DATE_ADDED', date("F j, Y, g:i a", $data['date_added']));

    $email_template->set_email_template_variable('CITY', $data['city']);
    $email_template->set_email_template_variable('DATE_SCHEDULED', date("n/d/Y", $data['date_schedualed']));
    $email_template->set_email_template_variable('SCHEDULED_START', date("n/d/Y", $data['date_schedualed']));
    $email_template->set_email_template_variable('SCHEDULED_END', date("n/d/Y", add_business_days($data['date_schedualed'], $service_area_window-1)));
    $email_template->set_email_template_variable('SATURDAY_INSTALL_NOTE', $saturday_install_note);

    // Reset service_area_window for email display (it may have been modified by a saturday install)
    $service_area_window = tep_fetch_service_area_window(tep_fetch_zip4_service_area($data['zip4']));
    if ($service_area_window == 0) {
        $service_area_window = 5;
    }
    $email_template->set_email_template_variable('SERVICE_AREA_WINDOW', $service_area_window);

    $email_template->set_email_template_variable('NUMBER_OF_POSTS', $data['number_of_posts']);
    $email_template->set_email_template_variable('SPECIAL_INSTRUCTIONS', $data['special_instructions']);
    $email_template->set_email_template_variable('BILLING_METHOD', tep_get_billing_method_name($data['billing_method_id']));
    $email_template->set_email_template_variable('CROSS_STREET_DIRECTIONS', $data['cross_street_directions']);
    $email_template->set_email_template_variable('COUNTY_NAME', tep_get_county_name($data['county_id']));
    $email_template->set_email_template_variable('STATE_NAME', tep_get_state_name($data['state_id']));
    $email_template->set_email_template_variable('AGENT_EMAIL', $result['email_address']);

    $email_template->set_email_template_variable('ADC_NUMBER', $data['adc_number']);

    $email_template->set_email_template_variable('ZIP4', $data['zip4']);
    if ($order->has_agent_panel()) {
        $agent_panel_string = "";
    } else {
        $agent_panel_string = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>Agent Panel Selection - None</td></tr></table>\n";
    }
    $email_template->set_email_template_variable('EQUIPMENT', $agent_panel_string . tep_create_view_equipment_string($data['optional']));

    $cost_line_items.= $cost_line_item_template;
    $cost_line_items = str_replace("&NAME", "Base Cost", $cost_line_items);
    $cost_line_items = str_replace("&VALUE", number_format($data['base_cost'], 2), $cost_line_items);

    foreach (array("Extended Cost" => $data['extended_cost'],
                   "Equipment Cost" => $data['equipment_cost'],
                   "Extra Cost" => $data['extra_cost'],
                   "Adjustment" => $data['discount_cost'],
                   "Account Credit" => -$applied_credit) as $key => $value) {
                        if ($value != 0) {
                            $cost_line_items.= $cost_line_item_template;
                            $cost_line_items = str_replace("&NAME", $key, $cost_line_items);
                            $cost_line_items = str_replace("&VALUE", number_format($value, 2), $cost_line_items);
                        }
                   }

    $email_template->set_email_template_variable('COST_LINE_ITEMS', $cost_line_items);
    $email_template->set_email_template_variable('CHARGE_TOTAL', number_format($data['order_total'] - $applied_credit, 2));

    $deferred_html =  DeferredBilling::applyEmailTemplate($data['order_total'] - $applied_credit, $deferred_total, $deferred_credit, $deferred_transactions, false, 'http://www.realtysignpost.com');
    $email_template->set_email_template_variable('DEFERRED_BILLING', $deferred_html);

    $email_template->parse_template();

    if (!empty($email_template->template_commands['SUBJECT'])) {
        $subject = $email_template->template_commands['SUBJECT'];
        reset($email_template->template_data);
        while (list($key, $value) = each($email_template->template_data)) {
//echo $key.strpos($subject, $key).".";
            if (strpos($subject, $key)) {
//echo "r";
                $subject = str_replace($key, $value, $subject);
            }
        }
    }
//print_r($email_template);
//echo $subject;
//die();
    $emailsSent = array();
    if ($send_aom_email) {
        $email_template->send_email($aom_result['email_address'], $aom_result['firstname'] . ' ' . $aom_result['lastname']);
        $emailsSent[] = $aom_result['email_address'];
    }

    $email_template->send_email($result['email_address'], $result['firstname'] . ' ' . $result['lastname']);
    $emailsSent[] = $result['email_address'];
    // $email_template->send_email('vegereviews@gmail.com', $result['firstname'] . ' ' . $result['lastname']);
    //Send any extras.
    $extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $result['user_id'] . "' and email_status = '1'");
    foreach($database->fetch_array($extra_query) as $extra_result){
        if(!in_array($extra_result['email_address'], $emailsSent)){
            $email_template->send_email($extra_result['email_address'], $result['firstname'] . ' ' . $result['lastname'], false);
            $emailsSent[] = $extra_result['email_address'];
        }
    }

}

function tep_format_deferred_billing_email($user_id) {
    global $database;

    $template = "deferred_confirm";

    // Deferred Billing information is retrieved from the session
    $deferred_total = tep_fill_variable('deferred_total', 'session', 0);
    $deferred_credit = tep_fill_variable('deferred_credit', 'session', 0);
    $deferred_transactions = tep_fill_variable('deferred_transactions', 'session', array());

    $query = $database->query("select u.user_id, u.agent_id, u.email_address, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $user_id . "' and u.user_id = ud.user_id limit 1");
    $result = $database->fetch_array($query);

    $email_template = new email_template($template);
    $email_template->load_email_template();

    $email_template->set_email_template_variable('AGENT_NAME', $result['firstname'] . ' ' . $result['lastname']);
    $email_template->set_email_template_variable('AGENT_ID', $result['agent_id']);
    $email_template->set_email_template_variable('AGENCY_NAME', $result['name']);
    $email_template->set_email_template_variable('AGENT_EMAIL', $result['email_address']);
    $email_template->set_email_template_variable('BILLING_METHOD', 'Credit Card');

    $deferred_html =  DeferredBilling::applyDeferredEmailTemplate($deferred_total, $deferred_credit, $deferred_transactions, 'http://www.realtysignpost.com');
    $email_template->set_email_template_variable('DEFERRED_BILLING', $deferred_html);

    $email_template->parse_template();

    if (!empty($email_template->template_commands['SUBJECT'])) {
        $subject = $email_template->template_commands['SUBJECT'];
        reset($email_template->template_data);
        while (list($key, $value) = each($email_template->template_data)) {
//echo $key.strpos($subject, $key).".";
            if (strpos($subject, $key)) {
//echo "r";
                $subject = str_replace($key, $value, $subject);
            }
        }
    }

    $email_template->send_email($result['email_address'], $result['firstname'] . ' ' . $result['lastname']);
    //Send any extras.
    $extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $result['user_id'] . "' and email_status = '1'");
    foreach($extra_query as $extra_result){
        $email_template->send_email($extra_result['email_address'], $result['firstname'] . ' ' . $result['lastname'], false);
    }
}

function tep_format_pay_invoice_email($user_id, $account_id, $invoice_id, $total) {
    global $database;

    $template = "pay_invoice_confirm";

    $query = $database->query("select u.user_id, u.agent_id, u.email_address, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $user_id . "' and u.user_id = ud.user_id limit 1");
    $result = $database->fetch_array($query);

    $email_template = new email_template($template);
    $email_template->load_email_template();

    $email_template->set_email_template_variable('ACCOUNT_NAME', account::getAccountName($account_id));
    $email_template->set_email_template_variable('INVOICE_ID', $invoice_id);
    $email_template->set_email_template_variable('INVOICE_TOTAL', '$'.number_format($total,2));
    $email_template->set_email_template_variable('BILLING_METHOD', 'Credit Card');

    $email_template->parse_template();

    if (!empty($email_template->template_commands['SUBJECT'])) {
        $subject = $email_template->template_commands['SUBJECT'];
        reset($email_template->template_data);
        while (list($key, $value) = each($email_template->template_data)) {
//echo $key.strpos($subject, $key).".";
            if (strpos($subject, $key)) {
//echo "r";
                $subject = str_replace($key, $value, $subject);
            }
        }
    }

    $email_template->send_email($result['email_address'], $result['firstname'] . ' ' . $result['lastname']);
    //Send any extras.
    $extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $result['user_id'] . "' and email_status = '1'");
    foreach($extra_query as $extra_result){
        $email_template->send_email($extra_result['email_address'], $result['firstname'] . ' ' . $result['lastname'], false);
    }
}

// TODO: Look for uses of this function in the code...  It doesn't do what its name implies
//
// tep_get_aom(user_id) returns first and lastname of the user
//
function tep_get_aom($user_id) {
    global $database;
    $aom_query = $database->query("select u.user_id, u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $user_id . "' and u.user_id = ud.user_id limit 1");
    $aom_result = $database->fetch_array($aom_query);
    if (!$aom_result)
        return "";
    else
        return ($aom_result['firstname'] . ' ' . $aom_result['lastname']);
}

function tep_is_personal_equipment($user_id, $agency_id) {
    global $database;

    $query2 = $database->query("select count(equipment_item_id) as items FROM " . TABLE_EQUIPMENT_ITEMS . " WHERE user_id = " . $user_id . " OR agency_id = " . $agency_id);
    $result2 = $database->fetch_array($query2);
    if ($result2['items'] > 0) {
        return $result2['items'];
    }
    return 0;
}

function get_account_id($agency_id) {
    global $database;
    $this_account_id = 0;
    // Get payments for AOMs
    $get_aid = $database->query("
              SELECT u.user_id, u.agency_id, ud.firstname, ud.lastname, o.order_id, ai.account_id
              FROM users u, users_description ud, orders o, account_items ai
              WHERE u.agency_id = {$agency_id}
              AND u.user_id = ud.user_id
              AND o.user_id = u.user_id
              AND o.order_id = ai.reference_id
              AND o.date_completed >1349049599
              LIMIT 1
      ");
    foreach($database->fetch_array($get_aid) as $r){
        $this_account_id = $r['account_id'];
    }
    return $this_account_id;
}

function get_account_id_from_user($user_id) {

    global $database;
    $this_account_id = 0;
    // Get payments for AOMs
    $get_aid = $database->query("
              SELECT u.user_id, u.agency_id, ud.firstname, ud.lastname, o.order_id, ai.account_id
              FROM users u, users_description ud, orders o, account_items ai
              WHERE u.user_id = {$user_id}
              AND u.user_id = ud.user_id
              AND o.user_id = u.user_id
              AND o.order_id = ai.reference_id
              AND o.date_completed >1349049599
              LIMIT 1
      ");
    foreach($database->fetch_array($get_aid) as $r){
        $this_account_id = $r['account_id'];
    }

    if(!$this_account_id) {
        // Try to get via the agency id.
        $q = $database->query("SELECT agency_id FROM users WHERE user_id = {$user_id}");
        foreach($database->fetch_array($q) as $r){
            $aid = get_account_id($r['agency_id']);
            if(!empty($aid)) {
                $this_account_id = $aid;
            } else {
                $q2 = $database->query("SELECT account_id FROM accounts WHERE agency_id = {$r['agency_id']}");
                foreach($database->fetch_array($q2) as $r2)
                {
                    $this_account_id = $r2['account_id'];
                }
            }
        }
    }

    return $this_account_id;
}

function add_business_days($time_stamp, $offset) {
    global $database;
    $holidays = array();
    $query = $database->query("select holiday_date from " . TABLE_HOLIDAYS . " where holiday_date >= now()");
    foreach($database->fetch_array($query) as $row){
      array_push($holidays, $row['holiday_date']);
    }
    $future = $time_stamp;
    $future_date = date('Y-m-d', $time_stamp);
    while ($offset > 0) {
      $future = strtotime("$future_date + 1 days");
      $day_of_week = date('N', $future);
      $future_date = date('Y-m-d', $future);
      if ($day_of_week == 6 || $day_of_week == 7) {
          // skip Saturday and Sunday
          continue;
      }
      if (in_array($future_date, $holidays)) {
          // skip holidays
          continue;
      }
      $offset--;
    }
    return $future;
}

function subtract_business_days($time_stamp, $offset) {
    global $database;
    $holidays = array();
    $query = $database->query("select holiday_date from " . TABLE_HOLIDAYS . " where holiday_date >= now()");
    foreach($database->fetch_array($query) as $row){
      array_push($holidays, $row['holiday_date']);
    }
    $past = $time_stamp;
    $past_date = date('Y-m-d', $time_stamp);
    while ($offset > 0) {
      $past = strtotime("$past_date - 1 days");
      $day_of_week = date('N', $past);
      $past_date = date('Y-m-d', $past);
      if ($day_of_week == 6 || $day_of_week == 7) {
          // skip Saturday and Sunday
          continue;
      }
      if (in_array($past_date, $holidays)) {
          // skip holidays
          continue;
      }
      $offset--;
    }
    return $past;
}

function to_float($num) {
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    }

    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
    );
}

function equipment_array_to_string($equipment) {
    global $database;

    $equipment_name = '';
    $last = count($equipment) - 1;
    foreach ($equipment as $i => $eq) {
        if ($i > 0) {
          if ($i === $last) {
            $equipment_name .= " and ";
          } else {
            $equipment_name .= ", ";
          }
      }
      $query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $eq . "' limit 1");
      $result = $database->fetch_array($query);

      $equipment_name .= "\"{$result['name']}\"";
    }

    return $equipment_name;
}

function check_subscribe_installer($id,$order_id,$credit){
	global $database;
	if(!empty($id)){
		$query = $database->query("select u.email_address, u.email_notification from " . TABLE_USERS . " u where u.user_id = " . $id);
		$result = $database->fetch_array($query);

		if($result['email_notification'] == 'yes'){
			tep_format_order_email($order_id, 'iorder_confirm', '', $credit,$result['email_address']);
		}
	}
	return true;

}

function get_usps_user_id(){
    global $database;
    $query = $database->query("SELECT * FROM ". TABLE_CONFIGURATION ." WHERE configuration_id = 46");
    $result = $database->fetch_array($query);
    return $result['value'];
}