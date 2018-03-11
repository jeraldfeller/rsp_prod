<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$uID = tep_fill_variable('uID', 'get');

	$submit_value = tep_fill_variable('submit_string_y', 'post');

	$start_letter = tep_fill_variable('start_letter', 'get', '');
    
    $message = '';
    $agency_id = tep_fetch_order_manager_agency($user->fetch_user_id());

	$pages = tep_fill_variable('pages', 'post', array());
	if ($submit_value=="1") {
		
		//die();
		
		$email_address = tep_fill_variable('email_address', 'post');
		$agent_id = tep_fill_variable('agent_id', 'post');
		$billing_method_id = tep_fill_variable('billing_method_id', 'post');
		$service_level_id = tep_get_agency_service_level_id($agency_id);
		$firstname = tep_fill_variable('firstname', 'post');
		$lastname = tep_fill_variable('lastname', 'post');
		$street_address = tep_fill_variable('street_address', 'post');
		$postcode = tep_fill_variable('postcode', 'post');
		$city = tep_fill_variable('city', 'post');
		$county_id = tep_fill_variable('county_id', 'post');
		$require_deposit = tep_fill_variable('require_deposit', 'post');
		$agent_id = tep_fill_variable('agent_id', 'post');
		
		$deposit_remaining_count = tep_fill_variable('deposit_remaining_count', 'post');
		$state_id = tep_fill_variable('state', 'post');
		$user_group_id = 1;//tep_fill_variable('user_group_id', 'post');
		$discount_type = tep_fill_variable('discount_type', 'post');
		$discount_amount = tep_fill_variable('discount_amount', 'post');
		
		if (empty($email_address) ) {
			$error->add_error('aom_manage_agents', 'Please enter an Email Address.');
		}
		if (!empty($email_address) && !tep_validate_email_address($email_address)) {
			$error->add_error('aom_manage_agents', 'Please enter a valid Email Address.');
		}
		if(!empty($email_address) && tep_email_address_exists($email_address, $uID, false)) {
			$error->add_error('aom_manage_agents', 'That email address is already registered to another user.');
		}
		if (empty($agent_id)) {
			$error->add_error('aom_manage_agents', 'Please enter a Agent ID Number.');
		}
		if (empty($firstname) ) {
			$error->add_error('aom_manage_agents', 'Please enter a First Name.');
		}
		if (empty($lastname) ) {
			$error->add_error('aom_manage_agents', 'Please enter a Last Name.');
		}
		
		if (!$error->get_error_status('aom_manage_agents')) {
			$password = substr(md5(time()), 4, 6);
			$database->query("insert into " . TABLE_USERS . " (email_address, password, agent_id, date_created, billing_method_id, service_level_id, agency_id, require_deposit, deposit_remaining_count, discount_type, discount_amount) values ('" . $email_address . "', '" . md5($password) . "', '" . $agent_id . "', '" . time() . "', '" . $billing_method_id . "', '" . $service_level_id . "', '" . $agency_id . "', '" . $require_deposit . "', '" . $deposit_remaining_count . "', '" . $discount_type . "', '" . $discount_amount . "')");
			$uID = $database->insert_id();
			$database->query("insert into " . TABLE_USERS_DESCRIPTION . " (user_id, firstname, lastname, street_address, postcode, city, county_id, state_id) values ('" . $uID . "', '" . $firstname . "', '" . $lastname . "', '" . $street_address . "', '" . $postcode . "', '" . $city . "', '" . $county_id . "', '" . $state_id . "')");
			$database->query("insert into " . TABLE_USERS_TO_USER_GROUPS . " (user_id, user_group_id) values ('" . $uID . "', '" . $user_group_id . "')");
			
			//Now the phone numbers.
			$number = tep_fill_variable('number', 'post', array());
			for ($n = 0, $m = count($number); $n < $m; $n++) {
				if (!empty($number[$n])) {
					$database->query("insert into " . TABLE_USERS_PHONE_NUMBERS . " (user_id, phone_number, order_id) values ('" . $uID . "', '" . $number[$n] . "', '" . ($n+1) . "')");
				}
			}
			//Lets update the emails.
			$email = tep_fill_variable('email', 'post', array());
			$email_checked = tep_fill_variable('email_checked', 'post', array());
				for ($n = 0, $m = count($email); $n < $m; $n++) {
					if (!empty($email[$n])) {
							if (isset($email_checked[$n]) && ($email_checked[$n] == '1')) {
								$checked = '1';
							} else {
								$checked = '0';
							}
						$database->query("insert into " . TABLE_EMAILS_TO_USERS . " (user_id, email_address, email_status) values ('" . $uID . "', '" . $email[$n] . "', '" . $checked . "')");
					}
				}
				
			$email_template = new email_template('account_create');
			$email_template->load_email_template();
			$email_template->set_email_template_variable('EMAIL_ADDRESS', $email_address);
			$email_template->set_email_template_variable('PASSWORD', $password);
			$email_template->parse_template();
			$email_template->send_email($email_address, $firstname.', '.$lastname);
			
			$message = 'Successfully Inserted, user has been emailed new password';
			$page_action = '';
		
			$page_action = '';
			$uID = '';
		}
		
	}
	if ($page_action == 'update_status') {
        //Need to clear the old data and login as this user then redirect them.
	    $set_status = tep_fill_variable('set_status', 'get', '');
        $agent_id = tep_fill_variable('agent_id', 'get', ''); 
		$database->query("update " . TABLE_USERS . " set active_status = '" . $set_status . "' where user_id = '" . $agent_id . "' limit 1");
	}
	
	
	/*if ($page_action != 'add') {
	
	} else {
		$user_result = array('user_id' => tep_fill_variable('user_id', 'post'),
				 'email_address' => tep_fill_variable('email_address', 'post'),
				 'agent_id' => tep_fill_variable('agent_id', 'post'),
				 'billing_method_id' => tep_fill_variable('billing_method_id', 'post'),
				 'agency_id' => tep_fill_variable('agency_id', 'post'),
				 'firstname' => tep_fill_variable('firstname', 'post'),
				 'lastname' => tep_fill_variable('lastname', 'post'),
				 'gender' => tep_fill_variable('gender', 'post'),
				 'street_address' => tep_fill_variable('street_address', 'post'),
				 'postcode' => tep_fill_variable('postcode', 'post'),
				 'city' => tep_fill_variable('city', 'post'),
				 'county_id' => tep_fill_variable('county_id', 'post'),
				 'state_id' => tep_fill_variable('state_id', 'post'),
				 'deposit_remaining_count' => tep_fill_variable('deposit_remaining_count', 'post'),
				 'require_deposit' => tep_fill_variable('require_deposit', 'post'),
				 'user_group_id' => tep_fill_variable('user_group_id', 'post'),
				 'discount_type' => tep_fill_variable('discount_type', 'post'),
				 'discount_amount' => tep_fill_variable('discount_amount', 'post'));
		
	}	*/
	
			if ($page_action != 'add') {
				
				if(strlen($message)>0) {
					$vars['message'] = $message;
				}
				
				//list
					$uData = array();
					$listing_split = new split_page("select u.user_id, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.active_status from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()) . "' and utug.user_group_id = ug.user_group_id and (utug.user_group_id = '4' or (utug.user_group_id = '1' and u.users_status = '1'))" . ((!empty($start_letter) && ($start_letter != 'Any')) ? " and LEFT(ud.firstname, 1) = '" . $start_letter . "'" : ''). " order by ud.firstname, ud.lastname", '500', 'u.user_id');
						if ($listing_split->number_of_rows > 0) {
							$query = $database->query($listing_split->sql_query);
								foreach($database->fetch_array($query) as $result){
										if ($result['user_id'] == $uID) {
											$uData = $result;
											if ($result['active_status'] == '1') {
												$result['string'] = 'Active';
											} else {
												$result['string'] = 'Inactive';
											}
											
										}
										$vars['split_result'][] = $result;
					}
					$vars['listing_split'] = $listing_split;
				}		
				echo $twig->render('aom/aom_agents_list.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

			
			} else {
				
				//print_R($_POST);
				
			$user_result = array('user_id' => tep_fill_variable('user_id', 'post'),
				 'email_address' => tep_fill_variable('email_address', 'post'),
				 'agent_id' => tep_fill_variable('agent_id', 'post'),
				 'billing_method_id' => tep_fill_variable('billing_method_id', 'post'),
				 'agency_id' => tep_fill_variable('agency_id', 'post'),
				 'firstname' => tep_fill_variable('firstname', 'post'),
				 'lastname' => tep_fill_variable('lastname', 'post'),
				 'gender' => tep_fill_variable('gender', 'post'),
				 'street_address' => tep_fill_variable('street_address', 'post'),
				 'postcode' => tep_fill_variable('postcode', 'post'),
				 'city' => tep_fill_variable('city', 'post'),
				 'county_id' => tep_fill_variable('county_id', 'post'),
				 'state' => tep_fill_variable('state', 'post'),
				 'deposit_remaining_count' => tep_fill_variable('deposit_remaining_count', 'post'),
				 'require_deposit' => tep_fill_variable('require_deposit', 'post'),
				 //'user_group_id' => tep_fill_variable('user_group_id', 'post'),
				 'discount_type' => tep_fill_variable('discount_type', 'post'),
				 'discount_amount' => tep_fill_variable('discount_amount', 'post'));
				 
				 //tep_draw_aom_group_pulldown_bgdn('user_group_id', tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']), ' onchange="this.form.submit();"');
				 //tep_draw_aom_group_pulldown
				// tep_draw_state_pulldown_bgdn
				 
				 $form = $user_result;
				// $form['user_group_id'] = tep_draw_aom_group_pulldown_bgdn('user_group_id', tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']), 'change-submit');
				 $form['state'] = tep_draw_state_pulldown_bgdn('state', tep_fill_variable('state', 'post', $user_result['state']), 'change-submit');
				 $form['county_id'] = tep_draw_county_pulldown_bgdn('county_id', tep_fill_variable('state', 'post', $user_result['state']), 'change-submit');
				 $form['billing_method_id'] = tep_draw_billing_method_pulldown_bgdn('billing_method_id', tep_get_agency_billing_method_id($agency_id), '', false);
				 
				 $vars['form'] = $form;
				 /*echo "<pre>";
				 print_r($user_result);
				 echo "</pre>";*/
				 
				 echo $twig->render('aom/aom_create_agent.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
				 
			/*
			<form name="admin_config" method="post" action="<?php echo FILENAME_AOM_MANAGE_AGENTS. '?page_action=add&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxContent">Agent Email Address: </td><td class="pageBoxContent"><input type="text" name="email_address" value="<?php echo $user_result['email_address']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Agent First Name: </td><td class="pageBoxContent"><input type="text" name="firstname" value="<?php echo $user_result['firstname']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Agent Last Name: </td><td class="pageBoxContent"><input type="text" name="lastname" value="<?php echo $user_result['lastname']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Group: </td><td class="pageBoxContent"><?php echo tep_draw_aom_group_pulldown('user_group_id', tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']), ' onchange="this.form.submit();"'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Street Address: </td><td class="pageBoxContent"><input type="text" name="street_address" value="<?php echo $user_result['street_address']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">City: </td><td class="pageBoxContent"><input type="text" name="city" value="<?php echo $user_result['city']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">State: </td><td class="pageBoxContent"><?php echo tep_draw_state_pulldown('state_id', tep_fill_variable('state_id', 'post', $user_result['state_id']), ' onchange="this.form.submit();"'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Post Code: </td><td class="pageBoxContent"><input type="text" name="postcode" value="<?php echo $user_result['postcode']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">County: </td><td class="pageBoxContent"><?php echo tep_draw_county_pulldown('county_id', tep_fill_variable('state_id', 'post', $user_result['state_id']), $user_result['county_id']); ?></td>
					</tr>
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
									if ($n == 0) {
										$phone_num_title = "Cell Phone Number";
									} else if ($n == 2) {
										$phone_num_title = "Fax Number";
									} else {
										$phone_num_title = "Phone Number " . ($n + 1);
									}
								?>
								<tr>
									<td class="pageBoxContent"><?php echo $phone_num_title; ?>: </td><td class="pageBoxContent"><input type="text" name="number[]" value="<?php echo $number; ?>" /></td>
								</tr>
					<?php
							}
					?>
					<tr>
						<td class="pageBoxContent">Agent ID: </td><td class="pageBoxContent"><input type="text" name="agent_id" value="<?php echo $user_result['agent_id']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Billing Method: </td><td class="pageBoxContent"><?php echo                                  tep_draw_billing_method_pulldown('billing_method_id', tep_get_agency_billing_method_id($agency_id), '', false); ?></td>
					</tr>

					<?php
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
						<td class="pageBoxContent">Extra Email <?php echo ($n+1);?>: </td>
						<td class="pageBoxContent">
							<input type="text" name="email[]" value="<?php echo $email; ?>" /><br />
							<input type="checkbox" name="email_checked[<?php echo $n; ?>]" value="1"<?php echo ((!empty($emails_status[$n])) ? ' CHECKED' : ''); ?> />&nbsp;Receive emails on this Email Address
						</td>
					</tr>
						<?php
						}
						?>
					
				</table>
			<?php
			
			*/
			
			}
			?>
