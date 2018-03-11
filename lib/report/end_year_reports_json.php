<?php

// Created 1/10/2013 brad@brgr2.com
// Updated 1/14/2013 brad@brgr2.com
/*
 * AJAX data source for includes/content/end_year_reports.php
 */

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';
$u = null;

// Admins
if($_SESSION['user_group_id'] == 2) { // Admins
    if(! empty($_REQUEST['u'])) {
        $u = (int) $_REQUEST['u'];
    }
} elseif($_SESSION['user_group_id'] == 4) { // Agency order managers

    if(! empty($_REQUEST['u'])) {
        $u = (int) $_REQUEST['u'];
    }
    $found = false;
    $agency_id = tep_fetch_order_manager_agency($_SESSION['user_id']);
    $query = $database->query("select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id" . ((!empty($agency_id)) ? " and u.agency_id = '" . $agency_id . "'" : '') . " and u.user_id = ud.user_id order by ud.firstname");
    foreach($query as $result){
        if($result['user_id'] == $u) {
            $found = true;
        }
        $last_user_id = $result['user_id'];
    }

    if(!$found) {
        $u = $last_user_id;
    }

} else {
    $u = $_SESSION['user_id'];
}

if(!$u) {
    echo json_encode(array('u'=>$u));
} else {
    $result = array();
    $totals = array();
    $sql = "SELECT o.order_id, o.address_id, o.order_total, o.date_completed, a.house_number, a.street_name, a.city, a.zip, s.name AS state FROM orders o, addresses a, states s WHERE a.state_id = s.state_id AND o.address_id = a.address_id AND o.user_id = {$u} and o.date_completed > 0 ORDER BY o.date_completed ASC";
    $q = $database->query($sql);
    foreach($q as $r){

        $month = $r['month'] = date("m",$r['date_completed']);
        $year = $r['year'] = date("Y",$r['date_completed']);

        if(!isset($results[$year])) {
            $results[$year] = array();
        }
        $result[$year][] = $r;

        if(!isset($totals[$year])) {
            $totals[$year] = array('Total'=>0,'Month'=>array());
        }

        if(!isset($totals[$year]['Month'][date('F',$r['date_completed'])])) {
            $totals[$year]['Month'][date('F',$r['date_completed'])] = 0;
        }

        $totals[$year]['Total'] += $r['order_total'];
        $totals[$year]['Month'][date('F',$r['date_completed'])] += $r['order_total'];

    }

    echo json_encode(array('u'=>$u,'orders'=>$result,'totals'=>$totals));
}
?>
