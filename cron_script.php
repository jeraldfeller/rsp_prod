<?php
	include('includes/application_top.php');
	
//Auto removal email cron.
//AUTOMATIC_REMOVAL_TIME

$start_time = strtotime("Midnight +7 Days");
$end_time = strtotime("Midnight +8 Days - 1 Second");

$query = $database->query("select order_id, address_id, date_schedualed, user_id from " . TABLE_ORDERS . " where date_schedualed >= '" . $start_time . "' and date_schedualed <= '". $end_time . "' and order_type_id = '3' and order_status_id <= '2'");
	foreach($database->fetch_array($query) as $result){

		$user_query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $result['user_id'] . "' and u.user_id = ud.user_id limit 1");
		$user_result = $database->fetch_array($user_query);
		//	if (($user_result['email_address'] == 'hdmyers@excite.com') || ($user_result['email_address'] == 'ryan_myers@yahoo.com')) {
				$address_query = $database->query("select a.house_number, a.street_name, a.city, a.zip, c.name as county_name, s.name as state_name from " . TABLE_ADDRESSES . " a, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where a.address_id = '" . $result['address_id'] . "' and a.state_id = s.state_id and a.county_id = c.county_id limit 1");
				$address_result = $database->fetch_array($address_query);

							$email_template = new email_template('automatic_removal_7_days');
							$email_template->load_email_template();
							$email_template->set_email_template_variable('HOUSE_NUMBER', $address_result['house_number']);
							$email_template->set_email_template_variable('STREET_NAME', $address_result['street_name']);
							$email_template->set_email_template_variable('CITY', $address_result['city']);
							$email_template->set_email_template_variable('STATE_NAME', $address_result['state_name']);
							$email_template->set_email_template_variable('COUNTY_NAME', $address_result['county_name']);
							$email_template->set_email_template_variable('DATE_SCHEDUALED', date("l jS \of F Y", $result['date_schedualed']));
		
							$email_template->parse_template();
							//$email_template->send_email($user_result['email_address'], $user_result['firstname'].','.$user_result['lastname']);

							$email_template->send_email($user_result['email_address'], $user_result['firstname'].','.$user_result['lastname']);
							$extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $result['user_id'] . "' and email_status = '1'");
								foreach($database->fetch_array($extra_query) as $extra_result){
									$email_template->send_email($extra_result['email_address'],$user_result['firstname'].' '.$user_result['lastname']);
								}
			//}
	}


$start_time = strtotime("Midnight +15 Days");
$end_time = strtotime("Midnight +16 Days - 1 Second");

$query = $database->query("select order_id, address_id, date_schedualed, user_id from " . TABLE_ORDERS . " where date_schedualed >= '" . $start_time . "' and date_schedualed <= '". $end_time . "' and order_type_id = '3' and order_status_id <= '2'");
	foreach($database->fetch_array($query) as $result){
		$user_query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $result['user_id'] . "' and u.user_id = ud.user_id limit 1");
		$user_result = $database->fetch_array($user_query);
			//if (($user_result['email_address'] == 'hdmyers@excite.com') || ($user_result['email_address'] == 'ryan_myers@yahoo.com')) {
				$address_query = $database->query("select a.house_number, a.street_name, a.city, a.zip, c.name as county_name, s.name as state_name from " . TABLE_ADDRESSES . " a, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where a.address_id = '" . $result['address_id'] . "' and a.state_id = s.state_id and a.county_id = c.county_id limit 1");
				$address_result = $database->fetch_array($address_query);



							$email_template = new email_template('automatic_removal_15_days');
							$email_template->load_email_template();
							$email_template->set_email_template_variable('HOUSE_NUMBER', $address_result['house_number']);
							$email_template->set_email_template_variable('STREET_NAME', $address_result['street_name']);
							$email_template->set_email_template_variable('CITY', $address_result['city']);
							$email_template->set_email_template_variable('STATE_NAME', $address_result['state_name']);
							$email_template->set_email_template_variable('COUNTY_NAME', $address_result['county_name']);
							$email_template->set_email_template_variable('DATE_SCHEDUALED', date("l jS \of F Y", $result['date_schedualed']));
		
							$email_template->parse_template();
							//$email_template->send_email($user_result['email_address'], $user_result['firstname'].','.$user_result['lastname']);
							$email_template->send_email($user_result['email_address'], $user_result['firstname'].','.$user_result['lastname']);
							$extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $result['user_id'] . "' and email_status = '1'");
								foreach($database->fetch_array($extra_query) as $extra_result){
									$email_template->send_email($extra_result['email_address'],$user_result['firstname'].' '.$user_result['lastname']);
								}
			//}
	}

?>