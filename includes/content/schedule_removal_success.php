<?php
@session_start();
	$user_id = tep_fill_variable('user_id', 'session');
	$aID = tep_fill_variable('aID', 'get');
    $order_id = tep_fill_variable('order_id', 'session');
	$order_type = 3;
	
	if (!$aID) {
		tep_redirect(FILENAME_AGENT_ACTIVE_ADDRESSES);
		exit();
	}
	
    if (!$order_id) {
        tep_redirect(FILENAME_AGENT_ACTIVE_ADDRESSES);
        exit();
    }
    
	$query = $database->query("select date_schedualed from " . TABLE_ORDERS . " where address_id = '" . $aID . "' and order_type_id = '3' limit 1");
	$result = $database->fetch_array($query);
	if (empty($result['date_schedualed'])) {
		$result['date_schedualed'] = time();
	}
	$dt=$result['date_schedualed'];

	$order_id = tep_fill_variable('order_id', 'session');

	$order = new orders('fetch', $order_id);
	$data = $order->fetch_order();

	//Send the emails (one to aom, one to agent main email address, and to any other email addresses)
	$query = $database->query("select u.user_id, u.agent_id, u.email_address, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id) INNER JOIN " . TABLE_USERS_DESCRIPTION . " ud ON u.user_id = ud.user_id where u.user_id = '" . $user_id . "' limit 1");
	$result = $database->fetch_array($query);

    $service_area_window = tep_fetch_service_area_window(tep_fetch_zip4_service_area($data['zip4']));
    if ($service_area_window == 0) {
        $service_area_window = 5;
    }    

	$email_template = new email_template('schedule_removal_confirm');
	$email_template->load_email_template();
	$email_template->set_email_template_variable('ORDER_TYPE',tep_get_order_type_name($data['order_type_id']));
	
	$email_template->set_email_template_variable('HOUSE_NUMBER',$data['house_number']);		
	$email_template->set_email_template_variable('AGENT_NAME',$result['firstname'].' '.$result['lastname']);		
	$email_template->set_email_template_variable('AGENT_ID',$result['agent_id']);		
	$email_template->set_email_template_variable('AGENCY_NAME',$result['name']);					
	$email_template->set_email_template_variable('STREET_NAME', $data['street_name']);
	$email_template->set_email_template_variable('DATE_ADDED', date("F j, Y, g:i a", $data['date_added']));
	
	$email_template->set_email_template_variable('CITY', $data['city']);
	//todo
	$email_template->set_email_template_variable('PREVIOUS_DATE', date("n/d/Y", tep_fill_variable('current_date_scheduled', 'session')));
    $email_template->set_email_template_variable('DATE_SCHEDULED', date("n/d/Y", $data['date_schedualed']));
    $email_template->set_email_template_variable('SCHEDULED_START', date("n/d/Y", $data['date_schedualed']));
    $email_template->set_email_template_variable('SCHEDULED_END', date("n/d/Y", add_business_days($data['date_schedualed'], $service_area_window-1)));

	$email_template->set_email_template_variable('SPECIAL_INSTRUCTIONS', $data['special_instructions']);
	$email_template->set_email_template_variable('NUMBER_OF_POSTS', $data['number_of_posts']);
	$email_template->set_email_template_variable('CROSS_STREET_DIRECTIONS', $data['cross_street_directions']);
	$email_template->set_email_template_variable('COUNTY_NAME', tep_get_county_name($data['county_id']));
	$email_template->set_email_template_variable('STATE_NAME', tep_get_state_name($data['state_id']));
	$email_template->set_email_template_variable('AGENT_EMAIL', $result['email_address']);
	
	$email_template->set_email_template_variable('ADC_NUMBER', $data['adc_number']);
						
	$email_template->set_email_template_variable('ZIP', $data['zip4']);
	
	$email_template->parse_template();
	
	$email_template->send_email($result['email_address'],$result['firstname'].' '.$result['lastname']);
	//Send any extras.
	$extra_query = $database->query("select DISTINCT email_address from emails_to_users where user_id = '" . $result['user_id'] . "' and email_status = '1'");
	while($extra_result = $database->fetch_array($extra_query)) {
		$email_template->send_email($extra_result['email_address'],$result['firstname'].' '.$result['lastname']);
	}

	$form['house_number'] = $data['house_number'];		
	$form['street_name'] = $data['street_name'];
	$form['date_added'] = date("F j, Y, g:i a", $data['date_added']);
	$form['city'] = $data['city'];
	$form['date_schedualed'] = $data['date_schedualed'];
	$form['special_instructions'] = $data['special_instructions'];
	$form['cross_street_directions'] = $data['cross_street_directions'];
	$form['number_of_posts'] = $data['number_of_posts'];
	$form['county_name'] = tep_get_county_name($data['county_id']);
	$form['state_name'] = tep_get_state_name($data['state_id']);
    $form['zip'] = $data['zip'];
	$form['zip4'] = $data['zip4'];
	
	//print_r($form);
	
	//$form['']
	
	$vars = array(
		'form'=>$form
	);
	$vars['delay_flag'] = 0;
	$service_area_id = tep_fetch_zip4_service_area($form['zip']);
	if (tep_fetch_service_area_window($service_area_id) > 0) {
		$vars['delay_flag'] = 1;
		$vars['service_area_window'] = tep_fetch_service_area_window($service_area_id);
	}
	$vars['aID'] = $aID;
	$vars['oid'] = $order_id;
	
	//register print sessions
$session->php_session_register('order_id_print', $order_id);



	echo $twig->render('agent/scheduled_removal_success.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
	
?>

<?php
//unregister old sessions
$session->php_session_unregister('address_id');
$session->php_session_unregister('house_number');
$session->php_session_unregister('street_name');
$session->php_session_unregister('city');
$session->php_session_unregister('zip');
$session->php_session_unregister('state');
$session->php_session_unregister('county');
$session->php_session_unregister('cross_street_directions');
$session->php_session_unregister('zip4');
$session->php_session_unregister('request_zip4');
$session->php_session_unregister('order_id');
$session->php_session_unregister('current_date_scheduled');

?>
