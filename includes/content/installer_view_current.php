<?php

//This is now tomorrow.
$page_action = tep_fill_variable('page_action', 'get');
$view_type = tep_fill_variable('view_type', 'get');
$day_view = tep_fill_variable('day_view', 'get', 'today');
$display_view = tep_fill_variable('display_view', 'get', 'overview');
$submit_value = tep_fill_variable('submit_value');
$show_only_scheduled = tep_fill_variable('show_only_scheduled', 'get');
//$accept_jobs = tep_fill_variable('accept_jobs');
$sort_by_status = ((isset($_GET['sort_by_status'])) ? $_GET['sort_by_status'] : ((!empty($_GET)) ? '' : '1'));

$order_type = tep_fill_variable('order_type', 'get', ''); // Added by Mukesh
#echo '<pre>'; print_r($_REQUEST);	die;

// added by jerald
if(isset($_POST['accept_jobs_confirm'])){
	$page_action = $_POST['accept_jobs_confirm'];
}
if(isset($_POST['update_job_order'])){
	$page_action = $_POST['update_job_order'];
}

/*
if (!empty($accept_jobs)) {
	$page_action = 'accept_jobs';
}
*/
// Perform different actions...

if ($page_action == 'csv_export') {
	$file = '';
	$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));
	$midnight_future = ($midnight_tonight + ((60*60*24) * 1));

	if (date("w", ($midnight_tonight+1)) == 0) {
		$midnight_tonight += (60*60*24);
		$midnight_future += (60*60*24);
	}

	$query = $database->query("select a.house_number, a.city, a.street_name, c.name as county_name, a.zip, otiso.show_order_id as order_column from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by order_column, date_schedualed ASC");
	foreach($database->fetch_array($query) as $result){
		if (!empty($file)) {
			$file .= "\n";
		}
		$file .= $result['house_number'].' '.$result['street_name'].','.$result['city'].','.$result['county_name'].','.$result['zip'];
	}
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="RSPC_orders_' . date("n_d_Y", ($midnight_tonight+1)).'.csv"');
	header('Content-Length: '.strlen($file));
	echo $file;
	die();

}
elseif($page_action == 'update_job_order') {

	//Loop over orders and update the show_order.
	$order_id = tep_fill_variable('order_id', 'post', array());

	#echo '<pre>';
	#print_r($_REQUEST); die;
	$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));

	$midnight_future = ($midnight_tonight + ((60*60*24) * 1));

	if (date("w", ($midnight_tonight+1)) == 0) {
	$midnight_tonight += (60*60*24);
	$midnight_future += (60*60*24);
	}

	if(!empty($order_type)){
		$order_type_cond = 'ot.order_type_id='.$order_type.' and ';
	}else{
		$order_type_cond = '';
	}


	$query = $database->query("select o.order_id, otiso.show_order_id as order_column  from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where ".$order_type_cond."o.date_schedualed < '" . $midnight_future . "' and " . (($show_only_scheduled == '1') ? " o.order_status_id = '2' " : " o.order_status_id < '3' ") . " and o.order_issue != '1' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.order_status_id = os.order_status_id and o.service_level_id = sld.service_level_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by " . (($sort_by_status == '1') ? ' o.order_status_id, ' : '') . (($display_view == 'detailed') ? 'order_column' : 'o.date_schedualed ASC'));


#echo "select o.order_id, otiso.show_order_id as order_column  from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where ".$order_type_cond."o.date_schedualed < '" . $midnight_future . "' and " . (($show_only_scheduled == '1') ? " o.order_status_id = '2' " : " o.order_status_id < '3' ") . " and o.order_issue != '1' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.order_status_id = os.order_status_id and o.service_level_id = sld.service_level_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by " . (($sort_by_status == '1') ? ' o.order_status_id, ' : '') . (($display_view == 'detailed') ? 'order_column' : 'o.date_schedualed ASC');

	$order_default_order = 1;
	#echo '<pre>';
	#print_r($_REQUEST);
	foreach($database->fetch_array($query) as $result){

		//$order_data[] = $result;
		#$show_order = tep_fill_variable('order_'.$result['order_id'], 'post', '1');
		$oIds = $result['order_id'];

		$qry = $database->query("select count(order_id) as count,show_order_id from ".TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER." where order_id = '" . $oIds . "' limit 1");
		$rst = $database->fetch_array($qry);

		if ($rst['count'] > 0) {
			#$shOrd = (int) $_REQUEST['order_'.$oIds];
			$shOrd = (!empty($_REQUEST['order_'.$oIds]) ? $_REQUEST['order_'.$oIds] : 0);
			if($shOrd >0){
				#echo '<br> All is ok now I am in this case<br>';
				$database->query("update " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " set show_order_id = '" . $shOrd . "' where order_id = '" . $oIds . "' limit 1");
			}
			else{
				if ($rst['show_order_id'] == null || $rst['show_order_id'] ==0) {
					#echo '<br> I am in this case<br>';
					$database->query("update " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " set show_order_id = '' where order_id = '" . $oIds . "' limit 1");
				}
			}
		}else{
			$database->query("insert into " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " (order_id, show_order_id) values ('" . $oIds . "', '0')");
		}
	}

}
elseif ($page_action == 'accept_jobs_confirm') {

	$order_id = tep_fill_variable('accepted_jobs', 'post', array());

	#echo '<pre>I am in else if -2 :: '.$page_action; print_r($order_id); echo '================'; print_r($_REQUEST); die;

	$count = count($order_id);
	$n = 0;
	while($n < $count) {
		$query = $database->query("select order_status_id from " . TABLE_ORDERS . " where order_id = '" . $order_id[$n] . "' limit 1");
		$result = $database->fetch_array($query);
		if ($result['order_status_id'] == '1') {
				$last_modified_by = tep_fill_variable('user_id', 'session', 0);
				$database->query("update " . TABLE_ORDERS . " set order_status_id = '2', date_accepted = '" . mktime() . "', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where order_id = '" . $order_id[$n] . "' and order_status_id = '1' limit 1");
				$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $order_id[$n] . "', '2', '" . mktime() . "', '0', 'Your order has been scheduled.  You can no longer edit this order.')");
				$check_query = $database->query("select count(installer_id) as count from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $order_id[$n] . "' limit 1");
				$check_result = $database->fetch_array($check_query);
					if ($check_result['count'] > 0) {
						$database->query("update " . TABLE_INSTALLERS_TO_ORDERS . " set installer_id = '" . $user->fetch_user_id() . "' where order_id = '" . $order_id[$n] . "' limit 1");
					} else {
						$database->query("insert into " . TABLE_INSTALLERS_TO_ORDERS . " (installer_id, order_id) values ('" . $user->fetch_user_id() . "', '" . $order_id[$n] . "')");
					}
			}
		$n++;
	}
}

?>

<?php


$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));

$midnight_future = ($midnight_tonight + ((60*60*24) * 1));

if (date("w", ($midnight_tonight+1)) == 0) {
	$midnight_tonight += (60*60*24);
	$midnight_future += (60*60*24);
}


$installation_pending = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '1', '1', '', false);
$installation_scheduled = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '1', '2', '', false);

$service_call_pending =  tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '2', '1', '', false);
$service_call_scheduled = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '2', '2', '', false);

$removal_pending = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '3', '1', '', false);
$removal_scheduled = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '3', '2', '', false);


#get order datas
$order_data = array();
$where = '';
$extra = '';
$row_count = 0;


$accepted_jobs = tep_fill_variable('accepted_jobs', 'post', array());

if ($display_view == 'detailed') {
	//Fetch extra information,
	$extra = ', otiso.show_order_id, a.house_number, a.street_name,  a.cross_street_directions, a.number_of_posts, a.address_post_allowed, a.city, a.zip, s.name as state_name, c.name as county_name,sld.name as service_level_name, od.special_instructions, od.admin_comments';
} else {
	$extra = ', otiso.show_order_id, a.house_number, a.street_name, a.city';
}

if(!empty($order_type)){
	$order_type_cond = 'ot.order_type_id='.$order_type.' and ';
}else{
	$order_type_cond = '';
}

#echo 'Order Type : '.$order_type.'<br><br>';


$ordBy = (($sort_by_status == '1') ? ' o.order_status_id, ' : '') . (($display_view == 'detailed') ? 'if(order_column=0,1,0),order_column' : 'o.date_schedualed ASC');

$query = $database->query("select o.order_id, o.date_schedualed, o.order_status_id, os.order_status_name, ot.name as order_type_name, otiso.show_order_id as order_column, a.zip4".$extra." from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where ".$order_type_cond."o.date_schedualed < '" . $midnight_future . "' and " . (($show_only_scheduled == '1') ? " o.order_status_id = '2' " : " o.order_status_id < '3' ") . " and o.order_issue != '1' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.order_status_id = os.order_status_id and o.service_level_id = sld.service_level_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by " . $ordBy);

$order_default_order = 1;
//echo '<pre>';
foreach($database->fetch_array($query) as $result){
	$row_count++;

	#print_r($result);


	/*if (($display_view == 'detailed') && ($result['show_order_id'] == NULL)) {
		$result['show_order_id'] = $order_default_order;
		$order_default_order++;
	}*/

	if (!empty($accepted_jobs)) {

		if (in_array($result['order_id'], $accepted_jobs) ) {
			$accepted = true;
		}else {
			$accepted = false;
		}
	}else if($result['order_status_id'] == '2'){
		$accepted = true;
	}
	else {
		$accepted = false;
	}

	$result['accepted'] = $accepted;

	$order_data[] = $result;

}
//echo '<pre>';print_r($order_data); die;

$current_hour = date("H.i", mktime());
$limit_time = str_replace(':', '.', INSTALLER_MARK_SCHEDUALED_TIME);

((date("w", ($midnight_tonight+1)) != 0) ? $is_accept_job = true : $is_accept_job = false );


	///////////////////////////////////////////////////////

	$vars = array(
		'page_action' => $page_action,
		'row_count' => $row_count,
		'display_view' => $display_view,
		'order_type' => $order_type,
		'is_accept_job' => $is_accept_job,
		'day_view' => $day_view ,
		'sort_by_status' => $sort_by_status,
		'show_only_scheduled' => $show_only_scheduled,
		'jobs_for' => date("l dS \of F Y", ($midnight_tonight+1)),
		'installation_pending' => $installation_pending ,
		'installation_scheduled' => $installation_scheduled ,
		'service_call_pending' => $service_call_pending ,
		'service_call_scheduled' => $service_call_scheduled ,
		'removal_pending' => $removal_pending ,
		'removal_scheduled' => $removal_scheduled,
		'table' =>$order_data

	);
	//echo '<pre>'; print_r($vars['table']); #die;

	echo $twig->render('installer/view_current.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
