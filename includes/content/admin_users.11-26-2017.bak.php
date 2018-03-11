<?php
$page_action = tep_fill_variable('page_action', 'get');

$ajaxAction = tep_fill_variable('ajaxAction', 'post'); #added by Mukesh

$uID = tep_fill_variable('uID', 'get');
$show_user_group_id = tep_fill_variable('show_user_group_id', 'get', '1');
$search_name = tep_fill_variable('search_name', 'get');
$search_email = tep_fill_variable('search_email', 'get');
$submit_value = tep_fill_variable('submit_value_y', 'post');
$show_agency_id = tep_fill_variable('show_agency_id', 'get', '');
$start_letter = tep_fill_variable('start_letter', 'get', '');
$start_letter_type = tep_fill_variable('start_letter_type', 'get', '');
$show_service_level_id = tep_fill_variable('show_service_level_id', 'get', '');
$search_mrsid = tep_fill_variable('search_mrsid', 'get', '');
$search_status = tep_fill_variable('search_status', 'get', '1');
$billing_method = tep_fill_variable('billing_method', 'get', 'any');
$show_user = tep_fill_variable('show_user', 'get', 'any');

$message = '';
$pages = tep_fill_variable('pages', 'post', array());

#Start Added By Mukesh 
if(isset($ajaxAction) && !empty($ajaxAction) ){
	#echo 'I am here :: '.$ajaxAction.'<br>';
	
	if($ajaxAction == 'get-state-county'){
		
		$state_id = $_POST['state_id'];
		
		$query = $database->query("select county_id, name as county_name from " . TABLE_COUNTYS . " where state_id = '" . $state_id . "' order by name");
		$data[] = 'Please Select';
		while ($result = $database->fetch_array($query)) {
			
			$data[$result['county_id']] = $result['county_name'];
			
		}		
		echo json_encode(  array('status'=>'success','data'=>$data)); die;

		#print_r($array);die;
	} 
	if($ajaxAction == 'get-agency-address'){
	
		$agency_id = tep_fill_variable('agency_id', 'post');
	
		$query = $database->query("select address,addr_street,addr_city,addr_state,addr_county,addr_zip from " . TABLE_AGENCYS . " where agency_id = " . $agency_id );
		
		$result = $database->fetch_array($query);
	
		if(!empty($result['addr_street'])){
			$address = $result['addr_street'];
			
		}else {
			if(!empty($result['address']))
				$address = $result['address'];
			else
				$address = null;	
		}	
		
		$data = array(
			'addr_street'=> $address ,
			'addr_city'=> (!empty($result['addr_city']) ? $result['addr_city'] : null) ,
			'addr_state'=> (!empty($result['addr_state']) ? $result['addr_state'] : null) ,
			'addr_county'=> (!empty($result['addr_county']) ? $result['addr_county'] : null) ,
			'addr_zip'=> (!empty($result['addr_zip']) ? $result['addr_zip'] : null) 
		);
		
		echo json_encode(  array('status'=>'success','data'=>$data)); die;
			
	
	}
	if($ajaxAction == 'get-personal-address'){
		
		$user_id = tep_fill_variable('user_id', 'post');
	
		$query = $database->query("select street_address,addr_street, city, county_id, state_id, postcode from " . TABLE_USERS_DESCRIPTION . " where user_id = " . $user_id );
		
		$result = $database->fetch_array($query);
		
		$data = array(
			'addr_street'=> (!empty($result['street_address']) ? $result['street_address'] : null) ,
			'addr_city'=> (!empty($result['city']) ? $result['city'] : null) ,
			'addr_state'=> (!empty($result['state_id']) ? $result['state_id'] : null) ,
			'addr_county'=> (!empty($result['county_id']) ? $result['county_id'] : null) ,
			'addr_zip'=> (!empty($result['postcode']) ? $result['postcode'] : null) 
		);
		
		echo json_encode(  array('status'=>'success','data'=>$data)); die;
			
	
	}	
	
	if($ajaxAction == 'load-agents-email'){

		ob_start();
		include('load-agents-email.php');
		$contents = ob_get_contents(); // data is now in here
		ob_end_clean();
	
		echo $contents; die;
	}
}
#End Added By Mukesh


if (!empty($submit_value)) {
	
	$email_address = tep_fill_variable('email_address', 'post');
	$agent_id = tep_fill_variable('agent_id', 'post');
	$billing_method_id = tep_fill_variable('billing_method_id', 'post');
	$service_level_id = tep_fill_variable('service_level_id', 'post');
	$agency_id = tep_fill_variable('agency_id', 'post');
	$firstname = tep_fill_variable('firstname', 'post');
	$lastname = tep_fill_variable('lastname', 'post');
	$street_address = tep_fill_variable('street_address', 'post');
	$addr_street = tep_fill_variable('addr_street', 'post');
	$postcode = tep_fill_variable('postcode', 'post');
	$city = tep_fill_variable('city', 'post');
	$accounts_payable = tep_fill_variable('accounts_payable', 'post');
	$use_address = tep_fill_variable('use_address', 'post');
	$is_recieve_inventory = tep_fill_variable('is_recieve_inventory', 'post');
	
	
	$county_id = tep_fill_variable('county_id', 'post');
	$require_deposit = tep_fill_variable('require_deposit', 'post');
	$personal_invoice = tep_fill_variable('personal_invoice', 'post');
	//agent prefs		
	$install_preference = tep_fill_variable('install_preference', 'post');		
	$service_call_preference = tep_fill_variable('service_call_preference', 'post');		
	$removal_preference = tep_fill_variable('removal_preference', 'post');
	
	$deposit_remaining_count = tep_fill_variable('deposit_remaining_count', 'post');
	$state_id = tep_fill_variable('state_id', 'post');
	$user_group_id = tep_fill_variable('user_group_id', 'post');
	$discount_type = tep_fill_variable('discount_type', 'post');
	$discount_amount = tep_fill_variable('discount_amount', 'post');
	if (empty($email_address) ) 
	{
		$error->add_error('admin_users', 'Please enter an Email Address.');
	}
	if (!empty($email_address) && !tep_validate_email_address($email_address)) 
	{
		$error->add_error('admin_users', 'Please enter a valid Email Address.');
	}
	if(!empty($email_address) && tep_email_address_exists($email_address, $uID)) 
	{
		$error->add_error('admin_users', 'That email address is already registered to another user.');
	}
	if (empty($firstname) ) 
	{
		$error->add_error('admin_users', 'Please enter a First Name.');
	}
	if (empty($lastname) ) 
	{
		$error->add_error('admin_users', 'Please enter a Last Name.');
	}
	$email = tep_fill_variable('email', 'post', array());
	for ($n = 0, $m = count($email); $n < $m; $n++) 
	{
		if (!empty($email[$n])) 
		{
			if (!tep_validate_email_address($email[$n])) 
			{
				$error->add_error('admin_users', 'Email '.($n+1).' is not a valid Email Address.');
			} 
			elseif (tep_email_address_exists($email[$n], $uID)) 
			{
				$error->add_error('admin_users', 'Email '.($n+1).' is already registered to another user.');
			}
		}
	}
	if (!$error->get_error_status('admin_users')) 
	{
		if (!empty($uID)) 
		{
			//start add 08.01.2014 DrTech76, hook teh user to agency change log
			$sql="SELECT `agency_id` FROM `".TABLE_USERS."` WHERE `user_id`=".$uID;
			$old_agency_res=$database->query($sql);
			$old_agency=$database->fetch_array($old_agency_res);
			$old_agency=(int)$old_agency["agency_id"];
			//end add 08.01.2014 DrTech76, hook teh user to agency change log
			
			$database->query("update " . TABLE_USERS . " set email_address = '" . $email_address . "', agent_id = '" . $agent_id . "', billing_method_id = '" . $billing_method_id . "', service_level_id = '" . $service_level_id . "', agency_id = '" . $agency_id . "', require_deposit = '" . $require_deposit . "', deposit_remaining_count = '" . $deposit_remaining_count . "', discount_type = '" . $discount_type . "', discount_amount = '" . $discount_amount . "', accounts_payable = '" . $accounts_payable . "', use_address='".$use_address."', personal_invoice = '" . $personal_invoice . "', is_recieve_inventory = '" . $is_recieve_inventory . "' where user_id = '" . $uID . "' limit 1");
			
			//start add 08.01.2014 DrTech76, hook teh user to agency change log
			if($old_agency!=(int)$agency_id)
			{
                $sql="INSERT INTO `agencies_to_users`(`user_id`,`agency_id`,`action_date`,`account_action_type`,`account_action_from`) VALUES (".$uID.",".$agency_id.",NOW(),'update','admin')";
				$database->query($sql);
				$sql="UPDATE " . TABLE_ACCOUNTS . " SET agency_id = '{$agency_id}' WHERE user_id = '{$uID}' LIMIT 1";
				$database->query($sql);
			}
			//end add 08.01.2014 DrTech76, hook teh user to agency change log
			
			$database->query("update " . TABLE_USERS_DESCRIPTION . " set firstname = '" . $firstname . "', lastname = '" . $lastname . "', street_address = '" . $street_address . "',addr_street='".$addr_street."', postcode = '" . $postcode . "', city = '" . $city . "', county_id = '" . $county_id . "', state_id = '" . $state_id . "' where user_id = '" . $uID . "' limit 1");
			$database->query("update " . TABLE_USERS_TO_USER_GROUPS . " set user_group_id = '" . $user_group_id . "' where user_id = '" . $uID . "' limit 1");
			
			//Now the phone numbers.
			$number = tep_fill_variable('number', 'post', array());
			$database->query("delete from " . TABLE_USERS_PHONE_NUMBERS . " where user_id = '" . $uID . "'");
			for ($n = 0, $m = count($number); $n < $m; $n++) 
			{
				//if (!empty($number[$n])) {
					$database->query("insert into " . TABLE_USERS_PHONE_NUMBERS . " (user_id, phone_number, order_id) values ('" . $uID . "', '" . $number[$n] . "', '" . ($n+1) . "')");
				//}
			}
			//Lets update the emails.
			$email = tep_fill_variable('email', 'post', array());
			$email_checked = tep_fill_variable('email_checked', 'post', array());
			$database->query("delete from " . TABLE_EMAILS_TO_USERS . " where user_id = '" . $uID . "'");
			for ($n = 0, $m = count($email); $n < $m; $n++) 
			{
				if (!empty($email[$n])) 
				{
					if (isset($email_checked[$n]) && ($email_checked[$n] == '1')) 
					{
						$checked = '1';
					} 
					else 
					{
						$checked = '0';
					}
					$database->query("insert into " . TABLE_EMAILS_TO_USERS . " (user_id, email_address, email_status) values ('" . $uID . "', '" . $email[$n] . "', '" . $checked . "')");
				}
			}
			
			//preferences		
			$sql_user_group_id="SELECT `user_group_id` FROM `users_to_user_groups` WHERE `user_id`=".$uID;		
			$user_group_res=$database->query($sql_user_group_id);		
			$user_group=$database->fetch_array($user_group_res);		
			$ug=(int)$user_group["user_group_id"];		
					
			if($ug == 1) {		
				$database->query("update " . TABLE_AGENT_PREFERENCES . " set install_preference = '" . $install_preference . "', service_call_preference = '" . $service_call_preference . "', removal_preference = '" . $removal_preference . "' where user_id = '" . $uID . "' limit 1");		
			}		
			/*$install_preference		
			$service_call_preference		
			$removal_preference*/
			
			$message = 'Successfully Updated';
			$page_action = '';
			
		} 
		else 
		{
			$password = substr(md5(mktime()), 4, 6);
			$database->query("insert into " . TABLE_USERS . " (email_address, password, agent_id, billing_method_id, service_level_id, agency_id, require_deposit, deposit_remaining_count, discount_type, discount_amount, accounts_payable, personal_invoice, use_address,is_recieve_inventory) values ('" . $email_address . "', '" . md5($password) . "', '" . $agent_id . "', '" . $billing_method_id . "', '" . $service_level_id . "', '" . $agency_id . "', '" . $require_deposit . "', '" . $deposit_remaining_count . "', '" . $discount_type . "', '" . $discount_amount . "', '" . $accounts_payable . "', '" . $personal_invoice . "', '".$use_address."', '".$is_recieve_inventory."')");
			$uID = $database->insert_id();
			
            //start add 08.01.2014 DrTech76, hook teh user to agency change log
            if (!empty($agency_id)) {
			    $sql="INSERT INTO `agencies_to_users`(`user_id`,`agency_id`,`action_date`,`account_action_type`,`account_action_from`) VALUES (".$uID.",".$agency_id.",NOW(),'create','admin')";
                $database->query($sql);
            }
			//end add 08.01.2014 DrTech76, hook teh user to agency change log
			
			
			$database->query("insert into " . TABLE_USERS_DESCRIPTION . " (user_id, firstname, lastname, street_address,addr_street, postcode, city, county_id, state_id) values ('" . $uID . "', '" . $firstname . "', '" . $lastname . "', '" . $street_address . "','".$addr_street."', '" . $postcode . "', '" . $city . "', '" . $county_id . "', '" . $state_id . "')");
			$database->query("insert into " . TABLE_USERS_TO_USER_GROUPS . " (user_id, user_group_id) values ('" . $uID . "', '" . $user_group_id . "')");
			
			//Now the phone numbers.
			$number = tep_fill_variable('number', 'post', array());
			for ($n = 0, $m = count($number); $n < $m; $n++) 
			{
				if (!empty($number[$n])) 
				{
					$database->query("insert into " . TABLE_USERS_PHONE_NUMBERS . " (user_id, phone_number, order_id) values ('" . $uID . "', '" . $number[$n] . "', '" . ($n+1) . "')");
				}
			}
			
			//Lets update the emails.
			$email = tep_fill_variable('email', 'post', array());
			$email_checked = tep_fill_variable('email_checked', 'post', array());
			for ($n = 0, $m = count($email); $n < $m; $n++) 
			{
				if (!empty($email[$n])) 
				{
					if (isset($email_checked[$n]) && ($email_checked[$n] == '1')) 
					{
						$checked = '1';
					} 
					else 
					{
						$checked = '0';
					}
					$database->query("insert into " . TABLE_EMAILS_TO_USERS . " (user_id, email_address, email_status) values ('" . $uID . "', '" . $email[$n] . "', '" . $checked . "')");
				}
			}
			
			if($user_group_id == 1) {		
				$database->query("insert into " . TABLE_AGENT_PREFERENCES . " (user_id, install_preference, service_call_preference, removal_preference) VALUES ('" . $uID . "', '" . $install_preference . "','" . $service_call_preference . "', '" . $removal_preference . "')");		
			}
				
			$email_template = new email_template('account_create');
			$email_template->load_email_template();
			$email_template->set_email_template_variable('EMAIL_ADDRESS', $email_address);
			$email_template->set_email_template_variable('PASSWORD', $password);
			$email_template->set_email_template_variable('AGENT_NAME', $firstname.' '.$lastname);
			$email_template->set_email_template_variable('FIRST', $firstname);
			$email_template->set_email_template_variable('LAST', $lastname);
			$email_template->set_email_template_variable('AGENCY_NAME', tep_get_agency_name($agency_id));
			$email_template->set_email_template_variable('SERVICE_LEVEL', tep_get_service_level_name($service_level_id));
			$email_template->parse_template();
			$email_template->send_email($email_address, $firstname.', '.$lastname);
			
			$message = 'Successfully Inserted, user has been emailed new password';
			$page_action = '';
		}
		$page_action = '';
		$uID = '';
	}
}

//test		
if ($page_action == 'sendemails') {


		
		
		$agntId = tep_fill_variable('agntId', 'post');
		$section = tep_fill_variable('section', 'post');
		
		//send emails		
		$x = 0;		
		$live_email = 'realtysp@yahoo.com';		
		$live = true;		
		$uData = array();		
		
		if(isset($section) && $section == 'sendToAgent'){
			#echo 'I am in if'; die;
			#echo "select u.user_id, a.name as 'ag_name', a.address, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.agency_id, u.active_status, u.accounts_payable, u.order_hold, u.personal_invoice from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and utug.user_group_id='1' and  utug.user_group_id = ug.user_group_id and (u.user_id = '".$agntId."')" ;
			
			#die;

$listing_split = new split_page("select u.user_id, a.name as 'ag_name', a.address, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.agency_id, u.active_status, u.accounts_payable, u.order_hold, u.personal_invoice , u.is_recieve_inventory from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and utug.user_group_id='1' and  utug.user_group_id = ug.user_group_id and (u.user_id = '".$agntId."')", '20', 'u.user_id');	


		}else{
			#echo 'I am in else'; die;
			#echo 'I am in else<br>'; die;
			$listing_split = new split_page("select u.user_id, a.name as 'ag_name', a.address, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.agency_id, u.active_status, u.accounts_payable, u.order_hold, u.personal_invoice, u.is_recieve_inventory from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and ".((!empty($show_user_group_id)) ? (($show_user_group_id == '5') ? "(utug.user_group_id = '" . $show_user_group_id . "' or u.accounts_payable = '1') and " : "utug.user_group_id = '" . $show_user_group_id . "' and ") : '').((!empty($search_name)) ? ("((ud.firstname like '" . $search_name . "%' or ud.firstname = '" . $search_name . "' or ud.firstname like '%" . $search_name . "' or ud.firstname like '%" . $search_name . "%') or (ud.lastname = '" . $search_name . "' or ud.lastname like '" . $search_name . "%' or ud.lastname like '%" . $search_name . "' or ud.lastname like '%" . $search_name . "%')) and ") : '').((!empty($search_email)) ? ("(u.email_address like '" . $search_email . "%' or u.email_address = '" . $search_email . "' or u.email_address like '%" . $search_email . "' or u.email_address like '%" . $search_email . "%') and ") : '').((!empty($search_mrsid)) ? (($search_mrsid == 'none') ? "u.agent_id = '' and " : "(u.agent_id like '" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "' or u.agent_id = '" . $search_mrsid . "') and ") : '')."utug.user_group_id = ug.user_group_id" . ((!empty($show_agency_id)) ? " and (a.agency_id = '" . $show_agency_id . "' or a.parent_agency_id = '" . $show_agency_id . "')" : '') . ((!empty($start_letter)) ? (($start_letter_type == 'any') ? " and (ud.firstname like '".$start_letter."%' or ud.lastname like '".$start_letter."%')" : (($start_letter_type == 'first') ? " and (ud.firstname like '".$start_letter."%')" : " and (ud.lastname like '".$start_letter."%')")) : '') . ((!empty($show_service_level_id)) ? " and (utug.user_group_id != '1' or u.service_level_id = '" . $show_service_level_id . "')" : '') . ((!empty($search_status)) ? (($search_status == '1') ? " and (u.active_status = '1') " : " and (u.active_status = '0') ") : '') . " order by ud.lastname, ud.firstname", '20', 'u.user_id');	
			
		}
		
			
		if ($listing_split->number_of_rows > 0) {		
			
			$query = $database->query($listing_split->sql_query);		
			
			while($result = $database->fetch_array($query)) {		
				if ($result['user_id'] == $uID) {		
					$uData = $result;		
				}		
				$x = $result['user_id'];
				$user_id[$x] = $x;
				
				$opt_recieve_inventory[$x] = $result['is_recieve_inventory'];
				#($result['is_recieve_inventory'] ==1 ? $opt_recieve_inventory = true : $opt_recieve_inventory = false);
				#echo "Member who opt reciev inventory : ".$result['user_id']." -> ".$result['is_recieve_inventory']." :: ".$opt_recieve_inventory."<br>";
				# Start added by Mukesh for inventory part of email.
				if($opt_recieve_inventory){
					
					// Conditionally setup test environment
					if ($live) {
						$inventory_server = "realtysignpost.com";
					} else {
						putenv("SERVER_MODE=TEST");
						$inventory_server = "realtysignpost.com";
						
					}
					$warehouses = array("Fairfax Warehouse", "MD Warehouse");
					$available  = "Available";
					$pending    = "Pending Install";
					$installed  = "Installed";

					$date_pretty = date('F j, Y');

					$equip_sql = "SELECT DISTINCT ei.equipment_id FROM " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_USERS . " u " . 
								 "WHERE u.user_id = '{$result['user_id']}' AND (ei.user_id = '{$result['user_id']}' OR (ei.user_id = '0' AND ei.agency_id = u.agency_id))";
					#echo $equip_sql; die;			 
					$equip_query = $database->query($equip_sql);
					$eqs = "";
					$ecount = 0;
					while ($eres = $database->fetch_array($equip_query)) {
						if ($ecount > 0) {
							$eqs .= "&";
						} else {
							$eqs .= "?";
						}
						$ecount++;
						$eqs .= "equipment_id[]=" . $eres['equipment_id'];
					}
					$inventory_url = "http://{$inventory_server}/lib/inventory/inventory_json.php5{$eqs}";
					$inv_message[$x] = "<table width=\"100%\">\n";
					if ($ecount) {
						
						// Pull the inventory JSON from the API
						$contents = file_get_contents($inventory_url);
						$inventory = json_decode($contents);
						if (is_object($inventory) && property_exists($inventory, "equipment")) {
							//echo 'I am in if again'; die;
							$equipment = $inventory->equipment;
							foreach ($equipment as $equip) {
								$whs = $equip->warehouses;
								$equip_name = $equip->name;
								$avail = 0;
								$active = 0;
								foreach ($warehouses as $warehouse) {
									if (is_object($whs) && property_exists($whs, $warehouse)) {
										$wh = $whs->$warehouse;
										if (property_exists($wh, $available)) {
											$avail += $wh->$available;
											$active += $wh->$available;
										} 
										if (property_exists($wh, $installed)) {
											$active += $wh->$installed;
										} 
										if (property_exists($wh, $pending)) {
											$active += $wh->$pending;
										}
									}
								}
								$inv_message[$x] .= "<tr><td width=\"50%\">{$equip_name}:</td><td width=\"50%\">{$avail} of {$active} Available</td></tr>\n";
							}
						}
					} else {
						$inv_message[$x] .= "<tr><td>You have no equipment stored in our warehouse.</td></tr>\n";
					}
					$inv_message[$x] .= "</table>\n";

					#echo "<pre>";echo($inv_message[$x]); die;
				}
				
				#End added by Mukesh for inventory part of email
							
				//check for orders and send the emails		
				$order_sql = "SELECT * FROM " . TABLE_USERS_DESCRIPTION . " as u, " . TABLE_ORDERS . " as o,  " . TABLE_ADDRESSES . " as a 		
					WHERE u.user_id = o.user_id 		
						AND o.user_id = " . $result['user_id'] . " 		
						AND o.address_id = a.address_id 		
						AND o.order_type_id = 1 		
						AND o.address_id NOT IN (SELECT address_id FROM " . TABLE_ORDERS . " 		
							WHERE (order_type_id = 3 AND order_status_id = 3) 		
								OR (order_type_id = 3 AND order_status_id = 4)		
								OR (order_type_id = 1 AND order_status_id = 4) 		
								OR (order_type_id = 1 AND order_status_id = 5))		
					ORDER BY o.order_id ASC";		
						
				$order_query = $database->query($order_sql);		
		
				if ($database->num_rows($order_query) > 0) {		
						
					//$x++;		
					$message[$x] = '<table width="100%">';		
							
					while ($order_result = $database->fetch_array($order_query)) {		
						
						if ($live == true) {		
							$user_email[$x] = $result['email_address'];		
						} else {		
							#$user_email[$x] = 'mail2mukeshrai@gmail.com';		
							$user_email[$x] = 'Ryan_Myers@yahoo.com';		
						}		
						
						//$user_id[$x] = $result['user_id'];	
						$firstname[$x] = $order_result['firstname'];		
						$lastname[$x] = $order_result['lastname'];		
						$name[$x] = $firstname[$x] . " " . $lastname[$x];		
								
						$agency[$x] = $result['ag_name'];		
								
						$agency_addr[$x] = $result['address'];		
								
						$message[$x] .= "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>";		
						$message[$x] .= "<tr><td width='30%'>Order ID: </td><td width='70%'>" . $order_result['order_id'] . "</td></tr>";		
						$message[$x] .= "<tr><td width='30%'>Address: </td><td width='70%'>" . $order_result['house_number'] . " " . $order_result['street_name'] . " " . $order_result['city'] . " " . $order_result['zip'] . "</td></tr>";		
		
						if ($order_result['date_completed'] != '0') {		
							$message[$x] .= "<tr><td width='30%'>Date Installed: </td><td width='70%'>" . date("Y-M-d",$order_result['date_completed']); 		
						} else {		
							$message[$x] .= "<tr><td width='30%'>Date Added: </td><td width='70%'>" . date("Y-M-d",$order_result['date_added']); 		
						}		
								
						$message[$x] .= "</td></tr><tr><td width='30%'>Date for Removal: </td><td width='70%'>";		
								
						$remove_sql = "SELECT * FROM " . TABLE_ORDERS . " WHERE address_id = ". $order_result['address_id'] . " AND order_type_id = 3";		
						$remove_query = $database->query($remove_sql);		
						$remove_num = $database->num_rows($remove_query);		
								
						if ($remove_num > 0) {		
							$remove_result = $database->fetch_array($remove_query);		
							if ($remove_result['date_schedualed'] != '0') {		
								$message[$x] .= date("Y-M-d",$remove_result['date_schedualed']); 		
							} else {		
								$message[$x] .= "None Scheduled";		
							}		
						} else {		
							$message[$x] .= "None Scheduled";		
						}		
						$message[$x] .=  "</td></tr>";		
					} //end while orders		
					$message[$x] .= '</table>';		
				} else { //no emails/orders		
					$msg = 'No Emails To Send!';		
				} //end if	
					
			} 
			#die;
			 //end while		
		} //end if		
		#echo 'I am in <pre>';print_r($inv_message); echo '<br>------------------<br>';print_r($user_id);echo '<br>################<br>';print_r($message);die;
		//send emails
		#echo '<pre>'; print_r($opt_recieve_inventory); die;		
		foreach($user_id as $y => $uid) {	
			if (array_key_exists($y,$message)) {		
					
						
				$subject = date('F') . " Active Signpost Summary for " . $name[$y] . " from ".BUSINESS_NAME;		
				
				$new_message = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">		
					<tbody>		
						<tr>
							<td>Greetings from ".BUSINESS_NAME.". This e-mail is for ".$name[$y]." with ".$agency[$y]." at ".$agency_addr[$y].". If you are now with a different Agency or the Agency is at a different address, please let us know and we will make the appropriate updates. Below is a list of addresses where you currently have a signpost installed. 
							This list includes the date the signpost was installed, and the scheduled removal date. We are providing this 
							list to help you manage your active signpost installations. If you no longer need the signpost at one of the 
							addresses listed, please reschedule the removal order for that address at your earliest convenience.
							</td>
						</tr>	

						<tr><td>&nbsp;</td></tr>		
						<tr>		
							<td>".$message[$y]."</td>		
						</tr>		
						<tr><td>&nbsp;</td></tr>";		
				
				if($opt_recieve_inventory[$y]){
					$new_message .= "
					<tr>
						<td>Additionally, we are testing a new inventory management feature to help you manage the supply of your panels 
						in our warehouse. This feature is still in active development, so please bear with us as we work out any issues 
						we encounter. As of {$date_pretty}, your inventory is as follows:
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>".$inv_message[$y]."</td></tr>
					<tr><td>&nbsp;</td></tr>
					
					<tr>
						<td>If you prefer to not receive the monthly signpanel/rider inventory update, you can stop receiving them by 
						logging into your account, then selecting \"Update Account Information\" and then unchecking the option \"Receive Monthly Signpanel Inventory and Active Signpost Summary e-mails.\" for this e-mail.
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>";
				
				}
				
				$new_message .= "		
						<tr>		
							<td>Thank you for your business.<br />		
								".BUSINESS_NAME_FULL."<br />		
								".BUSINESS_PARTNER."<br />		
								Complete Information: <a href='".HTTP_URL."'>".WEB_DOMAIN."</a><br />		
								Fax and Voicemail: ".FAX_VOICE."<br />		
								Emergency Issue Resolution: ".EMERGENCY_NUMBER.".<br />		
								'".BUSINESS_TAG_LINE."'		
							</td>		
						</tr>		
					</tbody>		
				</table>";		
				
				#echo $new_message.'<br>==============<br>'.$user_email[$y]; die;
				
				mail($user_email[$y], $subject, 		
					"<html><body>".$new_message . "</body></html>", 		
					"From: " . EMAIL_FROM_NAME . " <".EMAIL_FROM_ADDRESS.">\n" . 		
					"cc: " . EMAIL_FROM_NAME . " <" . $live_email . ">\n" . 		
					"MIME-Version: 1.0\n" . 		
					"Content-type: text/html; charset=iso-8859-1");
				
				//send emails to extra agent email addresses		
				$extra_query = $database->query("select DISTINCT email_address from emails_to_users where user_id = '" . $user_id[$y] . "' and email_status = '1'");		

				while($extra_result = $database->fetch_array($extra_query)) {		

					mail($extra_result['email_address'], $subject, 		
						"<html><body>".$new_message . "</body></html>", 		
						"From: " . EMAIL_FROM_NAME . " <".EMAIL_FROM_ADDRESS.">\n" . 		
						"cc: " . EMAIL_FROM_NAME . " <" . $live_email . ">\n" . 		
						"MIME-Version: 1.0\n" . 		
						"Content-type: text/html; charset=iso-8859-1");
				}		
						
				$msg = 'Emails Sent!';		
			}		
											
		}		
		$page_action = '';			
		$message = $msg;		
} //end if pageaction		
			
//eotest


if ($page_action == 'delete_confirm') 
{
	$merge_user_id = tep_fill_variable('merge_user_id');
	if (!empty($merge_user_id)) 
    {
        $last_modified_by = tep_fill_variable('user_id', 'session', 0);
		$database->query("update " . TABLE_ORDERS . " set user_id = '" . $merge_user_id . "', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where user_id = '" . $uID . "'");
		//echo "update " . TABLE_ORDERS . " set user_id = '" . $merge_user_id . "' where user_id = '" . $uID . "'". '<br>';
		$database->query("update " . TABLE_ADDRESSES_TO_USERS . " set user_id = '" . $merge_user_id . "' where user_id = '" . $uID . "'");
		//echo "update " . TABLE_ADDRESSES_TO_USERS . " set user_id = '" . $merge_user_id . "' where user_id = '" . $uID . "'" . '<br>';
	}

	$database->query("delete from " . TABLE_USERS . " where user_id = '" . $uID . "' limit 1");
	//echo "delete from " . TABLE_USERS . " where user_id = '" . $uID . "' limit 1". '<br>';
	$database->query("delete from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $uID . "' limit 1");
	//echo "delete from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $uID . "' limit 1" . '<br>';
	$message = 'User Successfully Deleted';
	$page_action = '';
	$uID = '';
} 
elseif ($page_action == 'change_status') 
{
	$database->query("update " . TABLE_USERS . " set active_status = '" . tep_fill_variable('status', 'get') . "' where user_id = '" . $uID . "' limit 1");
	$page_action = '';
	$uID = '';
}
elseif ($page_action == 'inactive_change_status') 
{
	$database->query("update " . TABLE_USERS . " set active_status = '" . tep_fill_variable('status', 'get') . "' where user_id = '" . $uID . "' limit 1");
	$page_action = '';
	$uID = '';
	tep_redirect(HTTP_PREFIX . '/' .'admin_users.php?page_action=inactive');
} 
elseif ($page_action == 'order_hold') 
{
	$database->query("update " . TABLE_USERS . " set order_hold = '" . tep_fill_variable('status', 'get') . "' where user_id = '" . $uID . "' limit 1");
	$page_action = '';
	$uID = '';
}
if ($page_action == 'login_confirm') 
{
	//Need to clear the old data and login as this user then redirect them.
	$user->logout_user();
	$user->set_user_id($uID);
	$user->login_user();
	if($user->user_group_id == 1) {
		tep_redirect(HTTP_PREFIX . '/' .'agent_active_addresses.php');
	} elseif($user->user_group_id == 4) {
		tep_redirect(HTTP_PREFIX . '/' .'aom_active_addresses.php');
	} else {
		tep_redirect(FILENAME_ACCOUNT_OVERVIEW);
	}
	
}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_users')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_users'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if(($page_action == 'inactive')) {
					$last30 = strtotime("-".INACTIVE_MONTHS." months");
					//echo $last30;
					 ?>
					 <h4>Agents to be made Inactive</h4>
					<table id="myTable" width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<thead>
						<th width="20%" class="pageBoxHeading">User Name</th>
						<th width="20%" class="pageBoxHeading" align="center">User Email</th>
						<th width="20%" class="pageBoxHeading" align="center">User Status</th>
						<th width="20%" class="pageBoxHeading" align="center">Last Login</th>
						<th width="20%" class="pageBoxHeading" align="center">Last Order</th>

						<th width="90" class="pageBoxHeading" align="right">Action</th>
						<th width="10" class="pageBoxHeading"></th>
					</thead>
				<tbody>
				<?php
					$uData = array();
					$listing_split = new split_page("select DISTINCT u.user_id, max(o.date_added) as mxorder, u.last_login, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.agency_id, u.active_status from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join orders o on (o.user_id=u.user_id) left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and u.active_status=1 and ug.user_group_id=1 and u.last_login<=".$last30." GROUP BY u.user_id HAVING max(o.date_added)<=".$last30." order by u.last_login", '1000', 'u.user_id');
					//echo $listing_split->number_of_rows;
					$z=0;
						if ($listing_split->number_of_rows > 0) {
							$query = $database->query($listing_split->sql_query);
								while($result = $database->fetch_array($query)) {
									$z++;
										if ($result['user_id'] == $uID) {
											$uData = $result;
										}
						?>
						
							<tr>
								<td width="20%" class="pageBoxContent"><?php echo $result['firstname'].' '.$result['lastname']; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $result['email_address']; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php if ($result['active_status'] == '1') { ?><img src="images/icon_status_green.gif" height="10" width="10" border="0" />&nbsp;&nbsp;<a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=inactive_change_status&status=0&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>"><img src="images/icon_status_red_light.gif" height="10" width="10" border="0" /></a><?php } else { ?><a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=inactive_change_status&status=1&'. tep_get_all_get_params(array('page_action', 'action', 'uID', 'status')); ?>"><img src="images/icon_status_green_light.gif" height="10" width="10" border="0" /></a>&nbsp;&nbsp;<img src="images/icon_status_red.gif" height="10" width="10" border="0" /><?php } ?></td>
								<td width="20%" class="pageBoxContent" align="center">
									<? if ($result['last_login']==0) {
										echo '<span style="display:none;">'.$result['last_login'].'</span>'.'Never';
									} else {
										echo '<span style="display:none;">'.$result['last_login'].'</span>'.date('m/d/Y', $result['last_login']);
									}
									?>
								</td>
								<td width="20%" class="pageBoxContent" align="center">
									<? echo date('m/d/Y', $result['mxorder']); ?>
								</td>
								<td width="90" class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=edit&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=delete&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">Delete</a><?php if ($result['user_group_id'] == '1') { ?><br /><a href="<?php echo FILENAME_ADMIN_ORDERS . '?agent_id='.$result['user_id']; ?>&order_status=">Orders</a><?php } ?><?php if (!empty($promo_code_result['count'])) { ?><br /><a href="<?php echo FILENAME_ADMIN_PROMO_TRACK . '?user_id='.$result['user_id']; ?>">View Used Promo Codes</a><?php } ?> | <a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=login&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">Login</a></td>
								<td width="10" class="pageBoxContent"></td>
							</tr>
							
						<?php
								}
								
							?>
							</tbody>

						<?php
							}
						?>
				</table>
						    <script>
							$(document).ready(function(){
								$('#myTable').DataTable( {
									"aaSorting": [[ 3, "asc" ]]
								});
							});
							</script>
				<?
				}		
				elseif (($page_action != 'edit')&&($page_action != 'add')) {
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td width="20%" class="pageBoxHeading">User Name</td>
						<td width="20%" class="pageBoxHeading" align="center">User Email</td>
						<td width="20%" class="pageBoxHeading" align="center">User Status</td>
						<td width="20%" class="pageBoxHeading" align="center">Service Level</td>
						<td width="20%" class="pageBoxHeading" align="center">User Group</td>
						<td width="20%" class="pageBoxHeading" align="center">Agency</td>
						<td width="20%" class="pageBoxHeading" align="center">Assigned Preferences</td>
						<td width="90" class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$uData = array();
					
		#-- Count Total Number of Agents to whom Inventory email send					

		$inventorySplit = new split_page("select u.user_id from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and ".((!empty($show_user_group_id)) ? (($show_user_group_id == '5') ? "(utug.user_group_id = '" . $show_user_group_id . "' or u.accounts_payable = '1') and " : "utug.user_group_id = '" . $show_user_group_id . "' and ") : '').((!empty($search_name)) ? ("((ud.firstname like '" . $search_name . "%' or ud.firstname = '" . $search_name . "' or ud.firstname like '%" . $search_name . "' or ud.firstname like '%" . $search_name . "%') or (ud.lastname = '" . $search_name . "' or ud.lastname like '" . $search_name . "%' or ud.lastname like '%" . $search_name . "' or ud.lastname like '%" . $search_name . "%')) and ") : '').((!empty($search_email)) ? ("(u.email_address like '" . $search_email . "%' or u.email_address = '" . $search_email . "' or u.email_address like '%" . $search_email . "' or u.email_address like '%" . $search_email . "%') and ") : '').((!empty($search_mrsid)) ? (($search_mrsid == 'none') ? "u.agent_id = '' and " : "(u.agent_id like '" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "' or u.agent_id = '" . $search_mrsid . "') and ") : '')."utug.user_group_id = ug.user_group_id" . ((!empty($show_agency_id)) ? " and (a.agency_id = '" . $show_agency_id . "' or a.parent_agency_id = '" . $show_agency_id . "')" : '') . ((!empty($start_letter)) ? (($start_letter_type == 'any') ? " and (ud.firstname like '".$start_letter."%' or ud.lastname like '".$start_letter."%')" : (($start_letter_type == 'first') ? " and (ud.firstname like '".$start_letter."%')" : " and (ud.lastname like '".$start_letter."%')")) : '') . ((!empty($show_service_level_id)) ? " and (utug.user_group_id != '1' or u.service_level_id = '" . $show_service_level_id . "')" : '') . ((!empty($search_status)) ? (($search_status == '1') ? " and (u.active_status = '1') " : " and (u.active_status = '0') ") : '') . " and u.is_recieve_inventory ='1' and ug.user_group_id ='1' ", '20', 'u.user_id');		
		
		$agentFullName = null;
		$inventoryEmailCount = 	$inventorySplit->number_of_rows;
		
		#-- @End count Total Number of Agents to whom Inventory email send		
		

					$listing_split = new split_page("select u.user_id, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.agency_id, u.active_status, u.accounts_payable, u.service_level_id, u.order_hold, u.personal_invoice, ap.install_preference, ap.service_call_preference, ap.removal_preference from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id) left join agent_preferences ap ON ap.user_id=u.user_id, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and ".((!empty($show_user_group_id)) ? (($show_user_group_id == '5') ? "(utug.user_group_id = '" . $show_user_group_id . "' or u.accounts_payable = '1') and " : "utug.user_group_id = '" . $show_user_group_id . "' and ") : '').((!empty($search_name)) ? ("((ud.firstname like '" . $search_name . "%' or ud.firstname = '" . $search_name . "' or ud.firstname like '%" . $search_name . "' or ud.firstname like '%" . $search_name . "%') or (ud.lastname = '" . $search_name . "' or ud.lastname like '" . $search_name . "%' or ud.lastname like '%" . $search_name . "' or ud.lastname like '%" . $search_name . "%')) and ") : '').((!empty($search_email)) ? ("(u.email_address like '" . $search_email . "%' or u.email_address = '" . $search_email . "' or u.email_address like '%" . $search_email . "' or u.email_address like '%" . $search_email . "%') and ") : '').((!empty($search_mrsid)) ? (($search_mrsid == 'none') ? "u.agent_id = '' and " : "(u.agent_id like '" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "' or u.agent_id = '" . $search_mrsid . "') and ") : '')."utug.user_group_id = ug.user_group_id" . ((!empty($show_agency_id)) ? " and (a.agency_id = '" . $show_agency_id . "' or a.parent_agency_id = '" . $show_agency_id . "')" : '') . ((!empty($start_letter)) ? (($start_letter_type == 'any') ? " and (ud.firstname like '".$start_letter."%' or ud.lastname like '".$start_letter."%')" : (($start_letter_type == 'first') ? " and (ud.firstname like '".$start_letter."%')" : " and (ud.lastname like '".$start_letter."%')")) : '') . ((!empty($show_service_level_id)) ? " and (utug.user_group_id != '1' or u.service_level_id = '" . $show_service_level_id . "')" : '') . ((!empty($search_status)) ? (($search_status == '1') ? " and (u.active_status = '1') " : " and (u.active_status = '0') ") : '') . ((($billing_method != 'any')) ?  " and u.billing_method_id = '" . (int)$billing_method . "' " : '')  . (($show_user == 'with') ? (" and (ap.install_preference != '' or ap.service_call_preference != '' or ap.removal_preference != '')") : '') . (($show_user == 'without') ? (" and ap.install_preference='' and ap.service_call_preference='' and ap.removal_preference=''") : '') . " order by ud.lastname, ud.firstname", '20', 'u.user_id');
						if ($listing_split->number_of_rows > 0) {
							$query = $database->query($listing_split->sql_query);
								while($result = $database->fetch_array($query)) {
										if ($result['user_id'] == $uID) {
											$uData = $result;
										}
									$promo_code_query = $database->query("select count(promotional_code_id) as count from " . TABLE_PROMOTIONAL_CODES_TO_USERS . " where user_id = '" . $result['user_id'] . "' limit 1");
									$promo_code_result = $database->fetch_array($promo_code_query);
										if ($result['accounts_payable'] == '1') {
											$result['name'] .= '/Accounts Payable';
										}
									$level = "";			
									if ($result['user_group_id'] == 1 || $result['user_group_id'] == 4) {
											if ($result['service_level_id'] == 1) {
												$level = 'Silver';
											} elseif ($result['service_level_id'] == 2) {
												$level = 'Gold';
											} else {
												$level = 'Platinum';
											}
										}
									$other_string = '';		
									$pref_result_count = 0;		
									//if($show_user == 'with' || $show_user == 'without') {		
										if (($result['user_group_id'] == '1') || ($result['user_group_id'] == '4') || ($result['user_group_id'] == '5')) {		
											$pref_query = $database->query("select install_preference, service_call_preference, removal_preference from agent_preferences where user_id = '" . $result['user_id'] . "' limit 1");		
											$pref_result = $database->fetch_array($pref_query);		
											//print_r($pref_result);		
													
											if (!empty($pref_result['install_preference'])) {		
												$pref_result_count += 1;		
											}		
											if (!empty($pref_result['service_call_preference'])) {		
												$pref_result_count += 1;		
											}		
											if (!empty($pref_result['removal_preference'])) {		
												$pref_result_count += 1;		
											}		
											$other_string = $pref_result_count;		
										}		
							//}		
									
						?>
							<tr class="userrow <?php echo strtolower($level) ?>">
								<td width="20%" class="pageBoxContent"><?php echo $result['firstname'].' '.$result['lastname']; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $result['email_address']; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php if ($result['active_status'] == '1') { ?><img src="images/icon_status_green.gif" height="10" width="10" border="0" />&nbsp;&nbsp;<a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=change_status&status=0&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>"><img src="images/icon_status_red_light.gif" height="10" width="10" border="0" /></a><?php } else { ?><a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=change_status&status=1&'. tep_get_all_get_params(array('page_action', 'action', 'uID', 'status')); ?>"><img src="images/icon_status_green_light.gif" height="10" width="10" border="0" /></a>&nbsp;&nbsp;<img src="images/icon_status_red.gif" height="10" width="10" border="0" /><?php } ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php
									echo $level;
									/*if ($result['personal_invoice'] == '1') {
										?>
										<?php if ($result['order_hold'] == '1') { ?><img src="images/icon_status_green.gif" height="10" width="10" border="0" />&nbsp;&nbsp;<a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=order_hold&status=0&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>"><img src="images/icon_status_red_light.gif" height="10" width="10" border="0" /></a><?php } else { ?><a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=order_hold&status=1&'. tep_get_all_get_params(array('page_action', 'action', 'uID', 'status')); ?>"><img src="images/icon_status_green_light.gif" height="10" width="10" border="0" /></a>&nbsp;&nbsp;<img src="images/icon_status_red.gif" height="10" width="10" border="0" /><?php } ?>
										<?php
									} else {
										$agency_query = $database->query("select order_hold from " . TABLE_AGENCYS . " where agency_id = '" . $result['agency_id'] . "' limit 1");
										$agency_result = $database->fetch_array($agency_query);
											if ($agency_result['order_hold'] == '1') {
												echo 'Agency on hold';
											} else {
												echo 'False';
											}
									}*/
								
								
								?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $result['name']; ?></td>
								<?php
									$string = '';
										if (($result['user_group_id'] == '1') || ($result['user_group_id'] == '4') || ($result['user_group_id'] == '5')) {
											$agency_query = $database->query("select agency_id, name from " . TABLE_AGENCYS . " where agency_id = '" . $result['agency_id'] . "' limit 1");
											$agency_result = $database->fetch_array($agency_query);
											
												if (!empty($agency_result['agency_id'])) {
													$string = '<a href="'.FILENAME_ADMIN_AGENCYS.'?aID='.$result['agency_id'].'&page_action=edit">'.$agency_result['name'].'</a>';
												}
										}
								?>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $string; ?></td>
								<td width="20%" class="pageBoxContent" align="center">		
										<?php		
											echo $other_string;		
										?>		
								</td>
								<td width="90" class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=edit&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=delete&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">Delete</a><?php if ($result['user_group_id'] == '1') { ?><br /><a href="<?php echo FILENAME_ADMIN_ORDERS . '?agent_id='.$result['user_id']; ?>&order_status=">Orders</a><?php } ?><?php if (!empty($promo_code_result['count'])) { ?><br /><a href="<?php echo FILENAME_ADMIN_PROMO_TRACK . '?user_id='.$result['user_id']; ?>">View Used Promo Codes</a><?php } ?> | <a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id'].'&page_action=login&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">Login</a></td>
								<td width="10" class="pageBoxContent"></td>
							</tr>
						<?php
								}
							?>
							<tr>
								<td colspan="5">
									<table class="normaltable" cellspacing="0" cellpadding="2">
										<tr>
											<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> users)'); ?></td>
											<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
										</tr>
									</table>
								</td>
							</tr>
						<?php
							}
						?>
				</table>
			<?php
				} else {
					if (!empty($uID)) {
						//Edit
						$user_data_query = $database->query("select u.user_id, u.accounts_payable,u.use_address, u.date_created, u.last_login, u.user_verified_date, u.email_address, u.agent_id, u.billing_method_id, u.service_level_id, u.agency_id, u.require_deposit, u.deposit_remaining_count, u.discount_type, u.discount_amount,u.is_recieve_inventory, ud.firstname, ud.lastname, ud.gender, ud.street_address, ud.addr_street, ud.postcode, ud.city, ud.county_id, ud.state_id, utug.user_group_id, u.personal_invoice from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = '" . $uID . "' and u.user_id = ud.user_id and u.user_id = utug.user_id limit 1");
						$user_data_result = $database->fetch_array($user_data_query);
						
						#echo '<pre>'; print_r($user_data_result);
						
						$user_result = array(
							'email_address' => tep_fill_variable('email_address', 'post', $user_data_result['email_address']),
							'user_id' => $user_data_result['user_id'],
							'agent_id' => tep_fill_variable('agent_id', 'post', $user_data_result['agent_id']),
							'billing_method_id' => tep_fill_variable('billing_method_id', 'post', $user_data_result['billing_method_id']),
							'service_level_id' => tep_fill_variable('service_level_id', 'post', $user_data_result['service_level_id']),
							'agency_id' => tep_fill_variable('agency_id', 'post', $user_data_result['agency_id']),
							'firstname' => tep_fill_variable('firstname', 'post', $user_data_result['firstname']),
							'lastname' => tep_fill_variable('lastname', 'post', $user_data_result['lastname']),
							'gender' => tep_fill_variable('gender', 'post', $user_data_result['gender']),
							'street_address' => tep_fill_variable('street_address', 'post', $user_data_result['street_address']),
							'addr_street' => tep_fill_variable('street_address', 'post', $user_data_result['addr_street']),
							'postcode' => tep_fill_variable('postcode', 'post', $user_data_result['postcode']),
							'city' => tep_fill_variable('city', 'post', $user_data_result['city']),
							'county_id' => tep_fill_variable('county_id', 'post', $user_data_result['county_id']),
							'state_id' => tep_fill_variable('state_id', 'post', $user_data_result['state_id']),
							'date_created' => $user_data_result['date_created'],
							'last_login' => $user_data_result['last_login'],
							'accounts_payable' => $user_data_result['accounts_payable'],
							'user_verified_date' => $user_data_result['user_verified_date'],
							'deposit_remaining_count' => tep_fill_variable('deposit_remaining_count', 'post', $user_data_result['deposit_remaining_count']),
							'require_deposit' => tep_fill_variable('require_deposit', 'post', $user_data_result['require_deposit']),
							'user_group_id' => tep_fill_variable('user_group_id', 'post', $user_data_result['user_group_id']),
							'discount_type' => tep_fill_variable('discount_type', 'post', $user_data_result['discount_type']),
							'personal_invoice' => tep_fill_variable('personal_invoice', 'post', $user_data_result['personal_invoice']),
							'discount_amount' => tep_fill_variable('discount_amount', 'post', $user_data_result['discount_amount']),
							'use_address' => $user_data_result['use_address'],
							'is_recieve_inventory' => $user_data_result['is_recieve_inventory']
							
						);
						
					} else {
						//Add
						$user_result = array(
							'user_id' => tep_fill_variable('user_id', 'post'),
							'email_address' => tep_fill_variable('email_address', 'post'),
							'agent_id' => tep_fill_variable('agent_id', 'post'),
							'billing_method_id' => tep_fill_variable('billing_method_id', 'post'),
							'service_level_id' => tep_fill_variable('service_level_id', 'post'),
							'agency_id' => tep_fill_variable('agency_id', 'post'),
							'firstname' => tep_fill_variable('firstname', 'post'),
							'lastname' => tep_fill_variable('lastname', 'post'),
							'gender' => tep_fill_variable('gender', 'post'),
							'street_address' => tep_fill_variable('street_address', 'post'),
							'addr_street' => tep_fill_variable('addr_street', 'post'),
							'postcode' => tep_fill_variable('postcode', 'post'),
							'city' => tep_fill_variable('city', 'post'),
							'accounts_payable' => tep_fill_variable('accounts_payable', 'post'),
							'county_id' => tep_fill_variable('county_id', 'post'),
							'state_id' => tep_fill_variable('state_id', 'post'),
							'deposit_remaining_count' => tep_fill_variable('deposit_remaining_count', 'post'),
							'require_deposit' => tep_fill_variable('require_deposit', 'post'),
							'user_group_id' => tep_fill_variable('user_group_id', 'post'),
							'personal_invoice' => tep_fill_variable('personal_invoice', 'post'),
							'discount_type' => tep_fill_variable('discount_type', 'post'),
							'discount_amount' => tep_fill_variable('discount_amount', 'post'),
							'use_address' => tep_fill_variable('use_address', 'post'),
							'is_recieve_inventory' => tep_fill_variable('is_recieve_inventory', 'post')
						);
					}
					
					if(!empty($user_result['addr_street'])){
						$addr_street = $user_result['addr_street'];
					}else {
						if(!empty($user_result['street_address']))
							$addr_street = $user_result['street_address'];
						else
							$addr_street = null;	
					}	
					
					$usrGrp = tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']);
			?>
			<?php
				if($page_action=='edit') {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_USERS . '?page_action=edit&uID='.$uID.'&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">
			<?php
				} else {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_USERS . '?page_action=add&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">
			<?php
				}
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2" >
					<?php
						if ($page_action == 'edit') {
					?>
					<tr>
						<td class="pageBoxContent">User ID: </td><td class="pageBoxContent"><?php echo $user_result['user_id']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Date Created: </td><td class="pageBoxContent"><?php echo date("n/d/Y", $user_result['date_created']); ?></td>
					</tr>

					<tr>
						<td class="pageBoxContent">Last Login: </td><td class="pageBoxContent"><?php echo (($user_result['last_login'] > 0) ? date("n/d/Y", $user_result['last_login']) : 'Never'); ?></td>
					</tr>
							<?php
							if ($user_result['user_verified_date'] > 0) {
							?>
							<tr>
								<td class="pageBoxContent">Verified Date: </td><td class="pageBoxContent"><?php echo date("n/d/Y", $user_result['user_verified_date']); ?></td>
							</tr>
							<?php
							}
						}
					?>
					<tr>
						<td class="pageBoxContent">User Email Address: </td><td class="pageBoxContent"><input type="text" name="email_address" value="<?php echo $user_result['email_address']; ?>" /></td>
					</tr>
                    <?php if($usrGrp == '1'){?>
                    <tr>
	                    <td class="pageBoxContent">&nbsp;</td>
                        <td class="pageBoxContent" style="padding-bottom:12px;">
                        	<?php (($user_result['is_recieve_inventory']==1) ? $isChkd = 'checked' : $isChkd = null);?>
                            <input type="checkbox" name="is_recieve_inventory" value="1" <?php echo $isChkd?>>
                            Receive Monthly Signpanel Inventory and Active Signpost Summary on this e-Mails.
                        </td>
                    </tr>                    
                    <?php }?>
					<tr>
						<td class="pageBoxContent">User First Name: </td><td class="pageBoxContent"><input type="text" name="firstname" value="<?php echo $user_result['firstname']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">User Last Name: </td><td class="pageBoxContent"><input type="text" name="lastname" value="<?php echo $user_result['lastname']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Group: </td><td class="pageBoxContent"><?php echo tep_draw_group_pulldown('user_group_id', tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']), ' onchange="this.form.submit();"'); ?></td>
					</tr>
					<?php
						if ($user_result['user_group_id'] != 2) {
							?>
							<tr>
								<td class="pageBoxContent">Accounts Payable: </td><td class="pageBoxContent"><input type="radio" name="accounts_payable" value="1"<?php echo (($user_result['accounts_payable'] == '1') ? ' CHECKED' : ''); ?> />&nbsp;True&nbsp;&nbsp;<input type="radio" name="accounts_payable" value="0"<?php echo (($user_result['accounts_payable'] != '1') ? ' CHECKED' : ''); ?> />&nbsp;False<br /><i>Use when not a user of the accounts payable group.  Will set as accounts payable for assigned agency.</i></td>
							</tr>
							<?php
							
						}
						
					?>
                    
                    <!-- Start Changes by Mukesh-->
                    <?php #$usrGrp = tep_fill_variable('user_group_id', 'post', $user_result['user_group_id'])?>
                    
					<?php if($usrGrp == '1' || $usrGrp == '5' || $usrGrp == '4'){?>
                    <tr><td class="pageBoxContent" colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td class="pageBoxContent">Agency: </td>
                        <td class="pageBoxContent"><?php echo tep_draw_agency_pulldown('agency_id', $user_result['agency_id']); ?></td>
                    </tr>      
                    <tr>
                        <td class="pageBoxContent">Address: </td>
                        <td class="pageBoxContent">
                        	<input type="radio" name="use_address" value="1"<?php echo (($user_result['use_address'] == '1') ? ' CHECKED' : ''); ?> />&nbsp;Use Personal Address&nbsp;&nbsp;
                            <input type="radio" name="use_address" value="0"<?php echo (($user_result['use_address'] != '1') ? ' CHECKED' : ''); ?> />&nbsp;Use Agency Address<br /><i>If "Use Agency Address" than choose which agency.</i></td>
                    </tr>                    
                    
                    <?php }?>
                    
					<tr>
						<td class="pageBoxContent">Street Address: </td><td class="pageBoxContent"><input type="text" name="street_address" value="<?php echo $addr_street; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">City: </td><td class="pageBoxContent"><input type="text" name="city" value="<?php echo $user_result['city']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">State: </td><td class="pageBoxContent"><?php echo tep_draw_state_pulldown('state_id', tep_fill_variable('state_id', 'post', $user_result['state_id']), ''); ?></td>
					</tr>
					
					<tr>
						<td class="pageBoxContent">County: </td><td class="pageBoxContent"><?php echo tep_draw_county_pulldown('county_id', tep_fill_variable('state_id', 'post', $user_result['state_id']), $user_result['county_id']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Post Code: </td><td class="pageBoxContent"><input type="text" name="postcode" value="<?php echo $user_result['postcode']; ?>" /></td>
					</tr>
                    
                    <!-- End Changes by Mukesh-->
					
					<?php
						//Phone numbers.
						$numbers = tep_fill_variable('number', 'post', array());
							if (empty($numbers) && !empty($uID)) {
								$query = $database->query("select phone_number from " . TABLE_USERS_PHONE_NUMBERS . " where user_id = '" . $uID . "' order by order_id");
									while($result = $database->fetch_array($query)) {
										$numbers[] = $result['phone_number'];
									}
							}
							for ($n = 0, $m = 4; $n < $m; $n++) {
									if (isset($numbers[$n])) {
										$number = $numbers[$n];
									} else {
										$number = '';
									}
								?>
								<tr>
									<td class="pageBoxContent"><?php if ($n == 0) { ?>Cell Phone Number:<?php } elseif ($n == 2) { ?>Fax Number:<?php } else { ?>Phone Number <?php echo ($n+1);?><?php } ?>: </td><td class="pageBoxContent"><input type="text" name="number[]" value="<?php echo $number; ?>" /></td>
								</tr>
								<?php
							}
						//Extra email addresses.
						$emails = tep_fill_variable('email', 'post', array());
						$emails_status = tep_fill_variable('email_checked', 'post', array());
							if (empty($emails) && empty($emails_status) && !empty($uID)) {
								$query = $database->query("select email_address, email_status from " . TABLE_EMAILS_TO_USERS . " where user_id = '" . $uID . "'");
									while($result = $database->fetch_array($query)) {
										$emails[] = $result['email_address'];
										$emails_status[] = $result['email_status'];
									}
							}
							for ($n = 0, $m = 4; $n < $m; $n++) {
									if (isset($emails[$n])) {
										$email = $emails[$n];
									} else {
										$email = '';
									}
								?>
								<tr>
									<td class="pageBoxContent">Extra Email <?php echo ($n+1);?>: </td><td class="pageBoxContent"><input type="text" name="email[]" value="<?php echo $email; ?>" />&nbsp;<input type="checkbox" name="email_checked[<?php echo $n; ?>]" value="1"<?php echo ((!empty($emails_status[$n])) ? ' CHECKED' : ''); ?> />&nbsp;Receive emails on this Email Address</td>
								</tr>
								<?php
							}
					?>
					<?php
                            if (tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']) == '1') {
                                $agent_account = new account($uID, '', 3);
                                $agent_available_credit = $agent_account->fetch_available_credit();
                                if ($agent_available_credit < 0) {
                                    $agent_available_credit = 0;
                                }
                                $agent_available_credit = number_format($agent_available_credit, 2);
                                $agency_account = new account($uID, '', 2);
                                $agency_available_credit = $agency_account->fetch_available_credit();
                                if ($agency_available_credit < 0) {
                                    $agency_available_credit = 0;
                                }
                                $agency_available_credit = number_format($agency_available_credit, 2);

					?>
					<tr>
						<td class="pageBoxContent">Require Deposit: </td>
						<td class="pageBoxContent"><input type="radio" name="require_deposit" value="1"<?php echo (($user_result['require_deposit'] == '1') ? ' CHECKED' : ''); ?> />&nbsp;True&nbsp;&nbsp;<input type="radio" name="require_deposit" value="0"<?php echo (($user_result['require_deposit'] == '0') ? ' CHECKED' : ''); ?> />&nbsp;False</td>
					</tr>
					<tr>
						<td class="pageBoxContent">Remaining Deposit Count: </td>
						<td class="pageBoxContent"><input type="text" value="<?php echo $user_result['deposit_remaining_count']; ?>" /> (to set as unlimited set this as 0 and the above as true)</td>
					</tr>
					<tr>
						<td class="pageBoxContent">Agent ID: </td><td class="pageBoxContent"><input type="text" name="agent_id" value="<?php echo $user_result['agent_id']; ?>" /></td>
					</tr>
					
					<tr>
						<td class="pageBoxContent">Billing Method: </td><td class="pageBoxContent"><?php echo tep_draw_billing_method_pulldown('billing_method_id', $user_result['billing_method_id'], '', false); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Service Level: </td><td class="pageBoxContent"><?php echo tep_draw_service_level_pulldown('service_level_id', $user_result['service_level_id'], '', false); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Adjustment Type: </td><td class="pageBoxContent"><?php echo tep_generate_discount_pulldown_menu('discount_type', $user_result['discount_type']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Adjustment Amount: <br />(+ or - for amount or normal percent)</td><td valign="top" class="pageBoxContent"><input type="text" name="discount_amount" value="<?php echo $user_result['discount_amount']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Agent Credit: </td><td class="pageBoxContent">$<?php echo $agent_available_credit; ?> (Credit Card or Agent Invoice)</td>
					</tr>
					<tr>
						<td class="pageBoxContent">Agency Credit: </td><td class="pageBoxContent">$<?php echo $agency_available_credit; ?> (Agency Invoice Orders)</td>
					</tr>
					<?php
						} elseif (tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']) == '5') {
					?>
					<tr>
						<td class="pageBoxContent">Agency: </td><td class="pageBoxContent"><?php echo tep_draw_agency_pulldown('agency_id', $user_result['agency_id']); ?></td>
					</tr>
					<?php
						} elseif (tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']) == '4') {
					?>
					<tr>
						<td class="pageBoxContent">Agency: </td><td class="pageBoxContent"><?php echo tep_draw_agency_pulldown('agency_id', $user_result['agency_id']); ?></td>
					</tr>
					<?php	
						}
					if (tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']) == '1') {		
							?>		
								<?php		
									$query = $database->query("select agent_preference_id, install_preference, service_call_preference, removal_preference from " . TABLE_AGENT_PREFERENCES . " where user_id = '" . $uID . "'");		
									$result = $database->fetch_array($query);		
										if (empty($result['agent_preference_id'])) {		
											$result['install_preference'] = '';		
											$result['service_call_preference'] = '';		
											$result['removal_preference'] = '';		
										}		
								?>		
								<tr>		
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>		
							</tr>		
														<tr>		
															<td class="pageBoxContent">Installation Preferences: </td>		
															<td class="pageBoxContent"><textarea style="width: 300px; height:200px;" name="install_preference"><?php echo $result['install_preference']; ?></textarea></td>		
														</tr>		
														<tr>		
															<td class="pageBoxContent">Service Call Preferences: </td>		
															<td class="pageBoxContent"><textarea style="width: 300px; height:200px;" name="service_call_preference"><?php echo $result['service_call_preference']; ?></textarea></td>		
														</tr>		
														<tr>		
															<td class="pageBoxContent">Removal Preferences: </td>		
															<td class="pageBoxContent"><textarea style="width: 300px; height:200px;" name="removal_preference"><?php echo $result['removal_preference']; ?></textarea></td>		
														</tr>		
						<?php		
						}	
					?>
				</table>
			<?php
				}
			?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
		<?php
			if (!empty($uID)) {
		?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<?php
					if(!empty($message)) {
				?>
				<tr>
					<td class="mainSuccess"><?php echo $message; ?></td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td width="100%">
					<?php
						if ($page_action == 'edit') { $inventoryEmailCount =1; $agentFullName = $user_result['firstname'].' '.$user_result['lastname'];
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Make your required changes and press "Update" below or press "Cancel" to cancel your changes.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main"><a href="<?php echo FILENAME_ADMIN_USER_PREFERENCES . '?page_action=view&uID='.$uID; ?>">View User Preferences</a> </td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_USERS. '?' .  tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
                                        <tr><td height="15"><img src="images/pixel_trans.gif" height="15" width="1"></td></tr>
                                        <tr>
                                        	<td colspan="2">
                                                <form id="#frmSendMail2" onsubmit="return mailConfirm2()" method="post" action="<?php echo FILENAME_ADMIN_USERS . '?page_action=sendemails&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">
                                                <input type="hidden" name="agntId" value="<?php echo $user_result['user_id']; ?>" />
                                                <input type="hidden" name="section" value="sendToAgent" />
                                                <input class="" style="font-size:12px;" type="submit" value="Send Agent Monthly Summary E-mails">
                                                </form>
                                            </td>
                                        </tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
						} elseif ($page_action == 'delete') {
							
					?>
					<form action="<?php echo FILENAME_ADMIN_USERS. '?page_action=delete_confirm&uID='.$uID .  '&' . tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you want to delete "<?php echo $uData['firstname'] . ' ' . $uData['lastname']; ?>"?  This action can not be undone.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">Assign Orders to: <?php echo tep_draw_user_pulldown('merge_user_id', '', '', array(array('id' => '', 'name' => 'None')), $uID); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">

										<tr>
											<td align="left"><?php echo tep_create_button_submit('delete', 'Delete'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_USERS. '?' .  tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
						} elseif ($page_action == 'login') {
							
					?>
					<form action="<?php echo FILENAME_ADMIN_USERS. '?page_action=login_confirm&uID='.$uID .  '&' . tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you want to login as "<?php echo $uData['firstname'] . ' ' . $uData['lastname']; ?>"?  You will need to relogin as an admin again after.</td>
							</tr>
							
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('sign_in', 'Sign in As User'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_USERS. '?' .  tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
						} elseif (!empty($page_action)) {
								if ($uData['user_group_id'] == '1') {
									$extra_data_query = $database->query("select a.name from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id");
									$extra_data_result = $database->fetch_array($extra_data_query);
								}
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><b>Viewing <?php echo $uData['firstname'].' '.$result['lastname']; ?></b></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Group: <?php echo $uData['name']; ?></td>
							</tr>
							<?php
								if ($uData['user_group_id'] == '1') {
							?>
							<tr>
								<td class="pageBoxContent">Agency: <?php echo $extra_data_result['name']; ?></td>
							</tr>
							
							<?php
								}
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Click Edit below to edit this User.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_USERS . '?page_action=edit&uID='.$uID; ?>"><?php echo tep_create_button_submit('edit', 'Edit'); ?><!--<input type="submit" value="Edit">--></form></td>
											<td align="right"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_USERS ; ?>"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?><!--<input type="submit" value="Cancel">--></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
						}
					?>
					</td>
				</tr>
			</table>
		<?php
			} else {
				if (!empty($page_action)) {
		?>
				<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Insert the details and when you are done press the Create button below or press Cancel to go back to the previous page.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('create', 'Create', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_USERS.'?'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
			<?php
				} else {
			?>
			<table width="250" cellspacing="0" celpadding="0" class="pageBox">
				<?php
					if(!empty($message)) {
				?>
				<tr>
					<td class="mainSuccess"><?php echo $message; ?></td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td class="pageBoxHeading"><b>User Options</b></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td class="pageBoxContent">Click edit to edit a user or click add (below) to add a new user.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<form action="<?php echo FILENAME_ADMIN_USERS; ?>" method="get">
				<tr>
					<td class="main">Show users of Group: <?php echo tep_draw_group_pulldown('show_user_group_id', $show_user_group_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'All'))); ?></td>
				</tr>
					<?php
						//if ($show_user_group_id == '1') {
						?>
						<tr>
							<td class="main">Show only Agency: <?php echo tep_draw_agency_pulldown('show_agency_id', $show_agency_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any'))); ?></td>
						</tr>
						<tr>
							<td class="main">Show only Level: <?php echo tep_draw_service_level_pulldown('show_service_level_id', $show_service_level_id, 'onchange="this.form.submit();"', false, array(array('id' => '', 'name' => 'Any')), false); ?></td>
						</tr>
						<?php
						
						
						//}
					?>
				<tr>
					<td class="main">Show users with name like: <input type="text" name="search_name" value="<?php echo $search_name; ?>" /></td>
				</tr>
				<tr>
					<td class="main">Show users with email like: <input type="text" name="search_email" value="<?php echo $search_email; ?>" /></td>
				</tr>
				<tr>
					<td class="main">MRIS ID like: <input type="text" name="search_mrsid" value="<?php echo $search_mrsid; ?>" /></td>
				</tr>
				<tr>
					<td class="main">Only Status: <select name="search_status"><option value=""<?php echo (($search_status == '') ? ' SELECTED' : ''); ?>>Any</option><option value="1"<?php echo (($search_status == '1') ? ' SELECTED' : ''); ?>>Active</option><option value="2"<?php echo (($search_status == '2') ? ' SELECTED' : ''); ?>>Inactive</option></option></td>
				</tr>
				<tr>
					<td class="main">Show Users starting with <select name="start_letter"><?php
						$query = $database->query("select LEFT(ud.firstname, 1) as letter from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud , " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id " . ((!empty($show_user_group_id)) ? " and utug.user_group_id = '" . $show_user_group_id . "'" : '') . ((!empty($show_agency_id)) ? " and u.agency_id = '" . $show_agency_id . "'" : '') . " group by letter order by letter");
						echo '<option value="">Any</option>';
							while($result = $database->fetch_array($query)) {
									if (empty($result['letter'])) {
										continue;
									}
									if ($start_letter == strtolower($result['letter'])) {
										$selected = ' SELECTED';
									} else {
										$selected = '';
									}
								echo '<option value="'.strtolower($result['letter']).'"' . $selected . '>'.strtoupper($result['letter']).'</option>';
							}
					?></select></td>
				</tr>
				<tr>
					<td class="main">First Letter of <select name="start_letter_type">
						<option value="any"<?php echo (($start_letter_type == 'any') ? ' SELECTED' : ''); ?>>Any</option>
						<option value="first"<?php echo (($start_letter_type == 'first') ? ' SELECTED' : ''); ?>>First Name</option>
						<option value="last"<?php echo (($start_letter_type == 'last') ? ' SELECTED' : ''); ?>>Last Name</option>
					</select></td>
				</tr>
				<tr>
					<td class="main">Payment Type: <?php echo tep_draw_billing_method_pulldown('billing_method', $billing_method, 'onchange="this.form.submit();"', false, '', array(array('id' => 'any', 'name' => 'Any'))); ?></td>
				</tr>
				<tr>
					<td class="main">Show Users <select name="show_user">		
							<option value="any"<?php echo (($show_user == 'any') ? ' SELECTED' : ''); ?>>With or Without Preferences</option>		
							<option value="with"<?php echo (($show_user == 'with') ? ' SELECTED' : ''); ?>>With Preferences</option>		
							<option value="without"<?php echo (($show_user == 'without') ? ' SELECTED' : ''); ?>>Without Preferences</option>		
							</select>		
						</td>		
				</tr>		
				<tr>
					<td width="100%" align="right"><input type="submit" value="Search" /></td>
				</tr>
				</form>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td width="100%" align="right"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_USERS . '?page_action=add&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>"><input type="submit" value="Add User"></form></td>
				</tr>		
				<tr>		
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>		
				</tr>		
				<tr>		
					<td width="100%" align="right">
                    <form id="#frmSendMail" onsubmit="return mailConfirm()" method="post" action="<?php echo FILENAME_ADMIN_USERS . '?page_action=sendemails&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">
                    
                    <input class="" style="font-size:12px;" type="submit" value="Send Agent Monthly Summary E-mails"><br />
					</form>
                    
                    
					<!--<a href="load-agents-email.php" class="btnEmailSummary" data-toggle="modal" data-target="#emailSummaryModal">Send Agent Monthly Summary E-mails</a>-->
                        
                    </td>
				</tr>
			</table>
		<?php
				}
			}
		?>
		</td>
	</tr>
</table>

<!-- Modal Box to send EmailSummary -->
<style>
	a.btnEmailSummary { background-color:#e1e1e1; color:#000000; border:1px solid #a1a1a1; font-size:12px; padding:5px 10px;}
	a:hover.btnEmailSummary { color:#000000; text-decoration:none;}
	a.btnEmailSummary2 { background-color:#e1e1e1; color:#000000; border:1px solid #a1a1a1; font-size:13px; padding:5px 11px;}
	a:hover.btnEmailSummary2 { color:#000000; text-decoration:none;}	
</style>
<!--
<div id="emailSummaryModal" class="modal fade" style="width:65%; left:40%">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="./js/bootbox.min.js"></script>
-->


<script type="text/javascript">
	var action = null;
	var use_address = null;
	var agency_id = null;
	var state_id = null;
	var user_id = "<?php echo $uID?>";
	var page_url = "<?php echo FILENAME_ADMIN_USERS?>";
	var agentCount = "<?php echo $inventoryEmailCount;?>";
	var agentFullName = "<?php echo $agentFullName?>";


	function mailConfirm() {
	  if(confirm('Are you sure to Send Monthly Summary E-mails to '+agentCount+' Agents?'))
		document.getElementById("frmSendMail").submit();
	  else
		return false;
	}
	function mailConfirm2() {
	  if(confirm('Are you sure to Send Monthly Summary E-mails to '+agentFullName+'?'))
		document.getElementById("frmSendMail2").submit();
	  else
		return false;
	}

	$(document).ready(function() {	


		/*$(document).on("click", ".btnEmailSummary", function () {
			
			var current = $(this);
			var agentCount = current.attr('data-agent-count');
			
			bootbox.confirm('Send Monthly Summary E-mails to '+agentCount+' Agents?', "No way!", "Yes, definitely!", function(rs) {
				if(rs == true){
					alert('All is good');
					$('#frmSendMail').submit();
					//document.admin_config2.submit();
					//return true;
				}		
			});		
			return false;
		});*/


		$(document).on("change", "select[name='agency_id']", function () {	
		
			use_address  = $('input[name=use_address]:checked').val();
		
			if(use_address==0){

				agency_id = $(this).val();
			
				$.ajax({
					type: 'POST',
					url: page_url,
					dataType: "json",
					data:{'ajaxAction': 'get-agency-address','agency_id':agency_id},
					cache: false,
					beforeSend:function(){
	
						$("input[name='street_address']").val('Please wait, while updating...');
						$("input[name='city']").val('Please wait, while updating...');
						$("input[name='postcode']").val('Please wait, while updating...');
					},				
					success: function(res){
						if(res.status=='success'){
							
							$("input[name='street_address']").val(res.data.addr_street).prop("readonly", true);
							$("input[name='city']").val(res.data.addr_city).prop("readonly", true);
							$("select[name='state_id']").val(res.data.addr_state).prop("disabled", true);
							$("select[name='county_id']").val(res.data.addr_county).prop("disabled", true);
							$("input[name='postcode']").val(res.data.addr_zip).prop("readonly", true);
							
						} 
	
					}
				});				
			}
			
		});
			   
		$(document).on("change", "select[name='state_id']", function () {	
			
			state_id = $(this).val();
			
			$.ajax({
				type: 'POST',
				url: page_url,
				dataType: "json",
				data:{'ajaxAction': 'get-state-county','state_id':state_id},
				cache: false,
				beforeSend:function(){
									
					$("select[name='county_id']").empty();
					
				},				
				success: function(res){
					
					var options = $("select[name='county_id']");
					$.each(res.data, function(key, value) {
						options.append(new Option(value, key));
					});					

				}
			});				
			
		});
		
		$(document).on("click", "input[name='use_address']", function () {	
			action = $(this).val();
			
			if(action==1){
				
				$.ajax({
					type: 'POST',
					url: page_url,
					dataType: "json",
					data:{'ajaxAction': 'get-personal-address','user_id':user_id},
					cache: false,
					beforeSend:function(){
				
					},				
					success: function(res){
						if(res.status=='success'){
							
							$("input[name='street_address']").val(res.data.addr_street).prop("readonly", false);
							$("input[name='city']").val(res.data.addr_city).prop("readonly", false);
							$("select[name='state_id']").val(res.data.addr_state).prop("disabled", false);
							$("select[name='county_id']").val(res.data.addr_county).prop("disabled", false);
							$("input[name='postcode']").val(res.data.addr_zip).prop("readonly", false);
							
						} 
				
					}
				});			
				
			} else if(action==0){
				
				$("select[name='agency_id']").trigger('change');
				
				
			}
		});
		
		
	})
</script>