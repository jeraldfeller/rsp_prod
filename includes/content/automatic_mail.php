<?php
	$first_name = tep_fill_variable('first_name');
	$last_name = tep_fill_variable('last_name');
	$agent_id = tep_fill_variable('agent_id');
	$email_address = tep_fill_variable('email_address');
	$password = tep_fill_variable('password');
	$password_confirmation = tep_fill_variable('password_confirmation');
	$phone_number = tep_fill_variable('phone_number');
	$second_phone_number = tep_fill_variable('second_phone_number');
	$optional_third_phone_number = tep_fill_variable('optional_third_phone_number');
	$optional_fourth_phone_number = tep_fill_variable('optional_fourth_phone_number');
	$agency_id = tep_fill_variable('agency_id');
	$agency_name = tep_fill_variable('agency_name');
	$agency_address = tep_fill_variable('agency_address');
	$contact_name = tep_fill_variable('contact_name');
	$contact_phone = tep_fill_variable('contact_phone');
	$service_level_id = tep_fill_variable('service_level_id');
	$billing_method_id = tep_fill_variable('billing_method_id');
	$referring_agent_name = tep_fill_variable('referring_agent_name');
	$referring_agent_mris_id = tep_fill_variable('referring_agent_mris_id');
	$referring_agent_email_address = tep_fill_variable('referring_agent_email_address');
	
	$submit_type = tep_fill_variable('submit_type_y');
		if (!empty($submit_type)) {
			//User has submitted form.  Now chech data and then insert into database.
				if (empty($first_name)) {
					$error->add_error('account_create', 'Please enter a First Name.');
				}
				if (empty($last_name)) {
					$error->add_error('account_create', 'Please enter a Last Name.');
				}
				if (empty($agent_id)) {
					$error->add_error('account_create', 'Please enter a Agent MRIS ID.');
				}
				if (empty($email_address) ) {
					$error->add_error('account_create', 'Please enter an Email Address.');
				}
				if (empty($password) ) {
					$error->add_error('account_create', 'Please enter your Password.');
				}
				if (empty($password_confirmation) ) {
					$error->add_error('account_create', 'Please enter your Password Confirmation.');
				}
				if (!empty($password) && !empty($password_confirmation) && ($password != $password_confirmation)) {
					$error->add_error('account_create', 'Your Password and Password Confirmation do not match.');
				}
				if (!tep_validate_email_address($email_address)) {
					$error->add_error('account_create', 'Please enter a valid Email Address.');
				}
				if(!empty($email_address) && tep_email_address_exists($email_address)) {
					$error->add_error('account_create', 'That email address is already registered to another user.');
				}
				if (empty($phone_number)) {
					$error->add_error('account_create', 'Please enter a Phone Number.');
				}
				if (!tep_validate_phone_number($phone_number)) {
					$error->add_error('account_create', 'Please enter a valid Phone Number.');
				}
				if (empty($second_phone_number)) {
					$error->add_error('account_create', 'Please enter a Second Phone Number.');
				}
				if (!tep_validate_phone_number($second_phone_number)) {
					$error->add_error('Please enter a valid Second Phone Number.');
				}
				if (!empty($optional_third_phone_number) && !tep_validate_phone_number($optional_third_phone_number)) {
					$error->add_error('account_create', 'Please enter a valid Third Phone Number or leave blank.');
				}
				if (!empty($optional_fourth_phone_number) && !tep_validate_phone_number($optional_fourth_phone_number)) {
					$error->add_error('account_create', 'Please enter a valid Fourth Phone Number or leave blank.');
				}
				if (empty($agency_id) || !is_numeric($agency_id)) {
					if (empty($agency_name) || empty($agency_address) || empty($contact_name) || empty($contact_phone)) {
						$error->add_error('account_create', 'Please either select an Agency or enter all the Agency Information to add a new one.');
					}
				}
				if (empty($service_level_id)) {
					$error->add_error('account_create', 'Please select a Service Level.');
				}
				if (empty($billing_method_id)) {
					$error->add_error('account_create', 'Please select a Billing Method.');
				}
				if (!$error->get_error_status('account_create')) {

					//No error. Add.
						if (empty($agency_id)) {
							//New Agency.  Add to database.
							$database->query("insert into " . TABLE_AGENCYS . " (name, address, contact_name, contact_phone) values ('" . $agency_name . "', '" . $agency_address . "', '" . $contact_name . "', '" . $contact_phone . "')");
							//Set agency as new agency_id.
							$agency_id = $database->insert_id();
						}
					//Now add as a new user.
						if (REQUIRE_NEW_AGENT_DEPOSIT == 'true') {
							$deposit_count = DEFAULT_DEPOSIT_COUNT;
							$require_deposit = '1';
						} else {
							$deposit_count = '0';
							$require_deposit = '0';
						}
					$database->query("insert into " . TABLE_USERS . " (date_created, email_address, password, agent_id, billing_method_id, service_level_id, agency_id, require_deposit, deposit_remaining_count) values ('" . mktime() . "', '" . $email_address . "', '" . md5($password) . "', '" . $agent_id . "', '" . $billing_method_id . "', '" . $service_level_id . "', '" . $agency_id . "', '" . $require_deposit . "', '" . $deposit_count . "')");
					$user_id = $database->insert_id();
					$database->query("insert into " . TABLE_USERS_DESCRIPTION . " (user_id, firstname, lastname) values ('" . $user_id . "', '" . $first_name . "', '" . $last_name . "')");
					$database->query("insert into " . TABLE_USERS_TO_USER_GROUPS . " (user_id, user_group_id) values ('" . $user_id . "', '1')");
					$phone_numbers_array = array();
					$phone_numbers_array[] = $phone_number;
					$phone_numbers_array[] = $second_phone_number;
						if (!empty($optional_third_phone_number)) {
							$phone_numbers_array[] = $optional_third_phone_number;
						}
						if (!empty($optional_fourth_phone_number)) {
							$phone_numbers_array[] = $optional_fourth_phone_number;
						}
					$count = count($phone_numbers_array);
					$n = 0;
						while($n < $count) {
							$database->query("insert into " . TABLE_USERS_PHONE_NUMBERS . " (user_id, phone_number, order_id) values ('" . $user_id . "', '" . $phone_numbers_array[$n] . "', '" . ($n + 1) . "')");
							$n++;
						}
						if (!empty($referring_agent_name)) {
							$database->query("insert into " . TABLE_REFERRALS . " (user_id, agent_name, agent_id, agent_email) values ('" . $user_id . "', '" . $referring_agent_name . "', '" . $referring_agent_mris_id . "', '" . $referring_agent_email . "')");
						}
					//Now send the email.
					$email_template = new email_template('account_create');
					$email_template->load_email_template();
					$email_template->set_email_template_variable('EMAIL_ADDRESS', $email_address);
					$email_template->set_email_template_variable('PASSWORD', $password);
					$email_template->parse_template();
					$email_template->send_email($email_address, $first_name.', '.$last_name);
					//Now set the new user id and log them in.
					$user->set_user_id($user_id);
					$user->login_user();
					//No redirect to the success page.
					tep_redirect('account_create_success.php');
				}
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td>Fill in the details below to create a new account.  If you have any problems then click here for the help file or use the contact form to contact us.</td>
	</tr>
	<tr>
		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
		<td valign="top">
		<form name="create_account" method="post" action="account_create.php">
			<table cellspacing="0" cellpadding="0" class="pageBox">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="3">
							<tr>
								<td class="subHeading" colspan="2">Sign up for your Account.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								
								if ($error->get_error_status('account_create')) {
							?>
							<tr>
								<td class="mainError" colspan="2"><?php echo $error->get_error_string('account_create'); ?></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td class="mainLarge" colspan="2">Personal Information</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
							</tr>
							<tr>
								<td class="main">First Name: </td><td><input type="text" name="first_name" value="<?php echo $first_name; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Last Name: </td><td><input type="text" name="last_name" value="<?php echo $last_name; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Agent MRIS ID: </td><td><input type="text" name="agent_id" value="<?php echo $agent_id; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Email Address: </td><td><input type="text" name="email_address" value="<?php echo $email_address; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Password: </td><td><input type="password" name="password" value="<?php echo $password; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Password Confirm: </td><td><input type="password" name="password_confirmation" value="<?php echo $password_confirmation; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Phone Number: </td><td><input type="text" name="phone_number" value="<?php echo $phone_number; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Second Phone Number: </td><td><input type="text" name="second_phone_number" value="<?php echo $second_phone_number; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Optional Third Phone Number: </td><td><input type="text" name="optional_third_phone_number" value="<?php echo $optional_third_phone_number; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Optional Fourth Phone Number: </td><td><input type="text" name="optional_fourth_phone_number" value="<?php echo $optional_fourth_phone_number; ?>" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="8" width="1"></td>
							</tr>
							<tr>
								<td class="mainLarge" colspan="2">Agency Information</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
							</tr>
							<tr>
								<td class="main" colspan="2">Select your agency below or fill in the details to create a new one.</td>
							</tr>
							<?php
								$agency_array = array();
								$agency_array[] = array('id' => '', 'name' => 'Please select or fill in details below');
								$query = $database->query("select agency_id, name from " . TABLE_AGENCYS . " order by name");
									while($result = $database->fetch_array($query)) {
										$agency_array[] = array('id' => $result['agency_id'], 'name' => $result['name']);
									}
							?>
							<tr>
								<td class="main">Select Agency: </td><td><?php echo tep_generate_pulldown_menu('agency_id', $agency_array, $agency_id, 'onchange="this.form.submit();"'); ?></td>
							</tr>
							<?php
								if (!empty($agency_id) && is_numeric($agency_id)) {
									$query = $database->query("select name, service_level_id, billing_method_id, address, contact_name, contact_phone from " . TABLE_AGENCYS . " where agency_id = '" . $agency_id . "' limit 1");
									$result = $database->fetch_array($query);
										if (empty($service_level_id)) {
											$service_level_id = $result['service_level_id'];
										}
										if (empty($billing_method_id)) {
											$billing_method_id = $result['billing_method_id'];
										}
							?>
							<tr>
								<td class="main">Agency Name: </td><td class="mainGrey"><?php echo $result['name']; ?></td>
							</tr>
							<tr>
								<td class="main">Agency Address: </td><td class="mainGrey"><?php echo $result['address']; ?></td>
							</tr>
							<tr>
								<td class="main">Managing Broker: </td><td class="mainGrey"><?php echo $result['contact_name']; ?></td>
							</tr>
							<tr>
								<td class="main">Contact Phone: </td><td class="mainGrey"><?php echo $result['contact_phone']; ?></td>
							</tr>
							<?php
								} else {
							?>
							<tr>
								<td class="main">Agency Name: </td><td class="main"><input type="text" name="agency_name" value="<?php echo $agency_name; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Agency Address: </td><td class="main"><input type="text" name="agency_address" value="<?php echo $agency_address; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Contact Name: </td><td class="main"><input type="text" name="contact_name" value="<?php echo $contact_name; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Contact Phone: </td><td class="main"><input type="text" name="contact_phone" value="<?php echo $contact_phone; ?>" /></td>
							</tr>
							<?php
								}
								
								$service_levels_array = array();
								$query = $database->query("select service_level_id, name from " . TABLE_SERVICE_LEVELS . " order by service_level_id");
									while($result = $database->fetch_array($query)) {
										$service_levels_array[] = array('id' => $result['service_level_id'], 'name' => $result['name']);
									}
									
									if (empty($billing_method_id)) {
										$billing_method_id = 1;
									}
								$billing_methods_array = array();
								$query = $database->query("select billing_method_id, name from " . TABLE_BILLING_METHODS . " where billing_method_id <= '" . $billing_method_id . "' order by billing_method_id");
									while($result = $database->fetch_array($query)) {
										$billing_methods_array[] = array('id' => $result['billing_method_id'], 'name' => $result['name']);
									}
							
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="8" width="1"></td>
							</tr>
							<tr>
								<td class="mainLarge" colspan="2">Billing Information</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
							</tr>
							<tr>
								<td class="main">Service Level: </td><td class="main"><?php echo tep_generate_pulldown_menu('service_level_id', $service_levels_array, $service_level_id); ?> <a href="service_plans.php" target="_blank">[View Service Plan Information]</a></td>
							</tr>
							<tr>
								<td class="main">Billing Method: </td><td class="main"><?php echo tep_generate_pulldown_menu('billing_method_id', $billing_methods_array, $billing_method_id); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="8" width="1"></td>
							</tr>
							<tr>
								<td class="mainLarge" colspan="2">Referral Information</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
							</tr>
							<tr>
								<td class="mainSmall" colspan="2"><i>If you were referred to us by an existing customer please enter their details below.</i></td>
							</tr>
							<tr>
								<td class="main">Referring Agent Name: </td><td class="main"><input type="text" name="referring_agent_name" value="<?php echo $referring_agent_name; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Referring Agent MRIS ID: </td><td class="main"><input type="text" name="referring_agent_mris_id" value="<?php echo $referring_agent_mris_id; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Referring Agent Email Address: </td><td class="main"><input type="text" name="referring_agent_email_address" value="<?php echo $referring_agent_email_address; ?>" /></td>
							</tr>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td colspan="2" align="center"><?php echo tep_create_button_submit('create_account', 'Create Account', ' name="submit_type" value="1"'); ?>&nbsp&nbsp;<?php echo tep_create_button_link('reset', 'Reset Form', ' onclick="document.all[\'create_account\'].reset()"'); ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
				</tr>
				<tr>
					<td class="mainSmall"></td>
				</tr>
			</table>
			</form>
		</td>
		<td valign="top" class="main">&PAGE_TEXT</td>
	</tr>
</table>