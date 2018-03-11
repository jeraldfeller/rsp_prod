<?php

//This is now tomorrow.
$page_action = tep_fill_variable('page_action', 'get');
$view_type = tep_fill_variable('view_type', 'get');
$day_view = tep_fill_variable('day_view', 'get', 'today');
$display_view = tep_fill_variable('display_view', 'get', 'overview');
$submit_value = tep_fill_variable('submit_value');
		
if($page_action == 'update_order') {
	//Loop over orders and update the show_order.
	$order_id = tep_fill_variable('order_id', 'post', array());
	$count = count($order_id);
	$n = 0;

	while($n < $count) {
		$show_order = tep_fill_variable('order_'.$order_id[$n], 'post', '1');
			$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " where order_id = '" . $order_id[$n] . "' limit 1");
			$result = $database->fetch_array($query);
				if ($result['count'] > 0) {
					$database->query("update " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " set show_order_id = '" . $show_order . "' where order_id = '" . $order_id[$n] . "' limit 1");
				} else {
					$database->query("insert into " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " (order_id, show_order_id) values ('" . $order_id[$n] . "', '" . $show_order . "')");
				}
		$n++;
	}
} 

?>

<?php 

	$midnight_tonight = mktime(0, 0, 0, date("n"), (date("d") + 1), date("Y"));
	
	#Check if tomorrow was a sunday, if so then extend that date.
	if (date("w", ($midnight_tonight+1)) == 0) {
		$midnight_tonight += (60*60*24);
	}
	#Now get the next day and work out if it is a sunday, if so then extend the date.
	$midnight_tonight += (60*60*24);
		if (date("w", ($midnight_tonight+1)) == 0) {
			$midnight_tonight += (60*60*24);
		}
	
	$midnight_future = ($midnight_tonight + ((60*60*24) * 1));

	
	$installation_pending = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '1', '1', '', true);
	
	$installation_scheduled = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '1', '2', '', true);
	
	$service_call_pending =  tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '2', '1', '', true);
	$service_call_scheduled = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '2', '2', '', true);
	
	$removal_pending = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '3', '1', '', true);
	$removal_scheduled = tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '3', '2', '', true);
	
	
	#get order datas 
	$order_data = array();
	$where = '';
	$extra = '';
	$row_count = 0;

	if ($display_view == 'detailed') {
		//Fetch extra information,
		$extra = ', otiso.show_order_id, a.house_number, a.street_name,  a.cross_street_directions, a.number_of_posts, a.address_post_allowed, a.city, a.zip, s.name as state_name, c.name as county_name,sld.name as service_level_name, od.special_instructions, od.admin_comments';
	} else {
		$extra = ', a.house_number, a.street_name, a.city';
	}
	
	
			
	$query = $database->query("select o.order_id, o.date_schedualed, o.order_status_id, os.order_status_name, ot.name as order_type_name, otiso.show_order_id as order_column, a.zip4".$extra." from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and o.date_schedualed >= '" . $midnight_tonight . "' and o.order_issue != '1' and o.order_status_id < '3' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) order by " . (($display_view == 'detailed') ? 'order_column' : 'o.date_schedualed ASC'));	
	
	foreach($database->fetch_array($query) as $result){
		$row_count++;
		if (($display_view == 'detailed') && ($result['show_order_id'] == NULL)) {
			$result['show_order_id'] = '1';
		}	
		$order_data[] = $result;
	}
	
	$vars = array(
		'row_count' => $row_count,
		'display_view' => $display_view,
		'day_view' => $day_view ,
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
	echo $twig->render('installer/tomorrow_plus_one.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

