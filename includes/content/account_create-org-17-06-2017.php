<?php 
error_reporting(0);
$first_name = tep_fill_variable('first_name');
$last_name = tep_fill_variable('last_name');
$agent_id = tep_fill_variable('agent_id');
$email_address = tep_fill_variable('email_address');
$alternateemail_address=tep_fill_variable('alternate_email_address');
$alternateemail_address2=tep_fill_variable('alternate_email_address2');
$alternateemail_address3=tep_fill_variable('alternate_email_address3');
$alternateemail_address4=tep_fill_variable('alternate_email_address4');
$password = tep_fill_variable('password');
$password_confirmation = tep_fill_variable('password_confirmation');
$phone_number = tep_fill_variable('phone_number');
$second_phone_number = tep_fill_variable('second_phone_number');
$optional_third_phone_number = tep_fill_variable('optional_third_phone_number');
$optional_fourth_phone_number = tep_fill_variable('optional_fourth_phone_number');
$create_agency = tep_fill_variable('create_agency');
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
$chk1 = $chk2 = $chk3 = $chk4 = '';
$submit_type = tep_fill_variable('submit_type_y');
if (!empty($submit_type)) 
{
	$chk1=isset($_POST['chkexpert1']) ? $_POST['chkexpert1'] : '';
	$chk2=isset($_POST['chkexpert2']) ? $_POST['chkexpert2'] : '';
	$chk3=isset($_POST['chkexpert3']) ? $_POST['chkexpert3'] : '';
	$chk4=isset($_POST['chkexpert4']) ? $_POST['chkexpert4'] : '';


//User has submitted form.  Now chech data and then insert into database.
	if (empty($first_name)) 
	{
		$error->add_error('account_create', 'Please enter a First Name.');
	}
	if (empty($last_name)) 
	{
		$error->add_error('account_create', 'Please enter a Last Name.');
	}
	if (empty($agent_id)) 
	{
		$error->add_error('account_create', 'Please enter a Agent MRIS ID.');
	}
	if (empty($email_address) ) 
	{
		$error->add_error('account_create', 'Please enter an Email Address.');
	}
	if (empty($password) ) 
	{
		$error->add_error('account_create', 'Please enter your Password.');
	}
	if (empty($password_confirmation) ) 
	{
		$error->add_error('account_create', 'Please enter your Password Confirmation.');
	}
	if (!empty($password) && !empty($password_confirmation) && ($password != $password_confirmation)) 
	{
		$error->add_error('account_create', 'Your Password and Password Confirmation do not match.');
	}
	if (!tep_validate_email_address($email_address)) 
	{
		$error->add_error('account_create', 'Please enter a valid Email Address.');
	}
	if(!empty($email_address) && tep_email_address_exists($email_address)) 
	{
		$error->add_error('account_create', 'That email address is already registered to another user.');
	}
	if (empty($phone_number)) 
	{
		$error->add_error('account_create', 'Please enter a Cell Phone Number.');
	} 
	elseif (!tep_validate_phone_number($phone_number)) 
	{
		$error->add_error('account_create', 'Please enter a valid Cell Phone Number.');
	}
	if (empty($second_phone_number)) 
	{
		$error->add_error('account_create', 'Please enter a Phone Number.');
	}
	if (!tep_validate_phone_number($second_phone_number)) 
	{
		$error->add_error('Please enter a valid Phone Number.');
	}
	if (!empty($optional_third_phone_number) && !tep_validate_phone_number($optional_third_phone_number)) 
	{
		$error->add_error('account_create', 'Please enter a valid Third Phone Number or leave blank.');
	}
	if (!empty($optional_fourth_phone_number) && !tep_validate_phone_number($optional_fourth_phone_number)) 
	{
		$error->add_error('account_create', 'Please enter a valid Fourth Phone Number or leave blank.');
	}
	if (empty($agency_id) || !is_numeric($agency_id)) 
	{
		if (empty($create_agency) || empty($agency_name) || empty($agency_address) || empty($contact_name) || empty($contact_phone)) 
		{
			$error->add_error('account_create', 'Please either select an Agency or enter all the Agency Information and check the create box to add a new one.');
		}
	}
	if (empty($service_level_id)) 
	{
		$error->add_error('account_create', 'Please select a Service Level.');
	}
	if (empty($billing_method_id)) 
	{
		$error->add_error('account_create', 'Please select a Billing Method.');
	}
	if (!$error->get_error_status('account_create')) 
	{

		//No error. Add.
		if (empty($agency_id) && !empty($create_agency)) 
		{
			//New Agency.  Add to database.
			$database->query("insert into " . TABLE_AGENCYS . " (name, address, contact_name, contact_phone, agency_status_id) values ('" . $agency_name . "', '" . $agency_address . "', '" . $contact_name . "', '" . $contact_phone . "', '0')");
			//Set agency as new agency_id.
			$agency_id = $database->insert_id();
		}
		//Now add as a new user.
		if (REQUIRE_NEW_AGENT_DEPOSIT == 'true') 
		{
			$deposit_count = DEFAULT_DEPOSIT_COUNT;
			$require_deposit = '1';
		} 
		else 
		{
			$deposit_count = '0';
			$require_deposit = '0';
		}
		$database->query("insert into " . TABLE_USERS . " (date_created, email_address, password, agent_id, billing_method_id, service_level_id, agency_id, users_status, require_deposit, deposit_remaining_count) values ('" . mktime() . "', '" . $email_address . "', '" . md5($password) . "', '" . $agent_id . "', '" . $billing_method_id . "', '" . $service_level_id . "', '" . $agency_id . "', '1', '".$require_deposit."', '".$deposit_count."')");
		$user_id = $database->insert_id();

		//start add 08.01.2014 DrTech76, hook teh user to agency change log
		$sql="INSERT INTO `agencies_to_users`(`user_id`,`agency_id`,`action_date`,`account_action_type`,`account_action_from`) VALUES (".$user_id.",".$agency_id.",NOW(),'create','own')";
		$database->query($sql);
		//end add 08.01.2014 DrTech76, hook teh user to agency change log
		
		if(!empty($alternateemail_address)&&($chk1!=""))
		{
			$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user_id . "','" . $alternateemail_address . "','" .$chk1. "')");
		}
		if(!empty($alternateemail_address2)&&($chk2!=""))
		{
			$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user_id . "','" . $alternateemail_address2 . "','" .$chk2. "')");
		}
		if(!empty($alternateemail_address3)&&($chk3!=""))
		{
			$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user_id . "','" . $alternateemail_address3 . "','" .$chk3. "')");
		}
		if(!empty($alternateemail_address4)&&($chk4!=""))
		{
			$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user_id . "','" . $alternateemail_address4 . "','" .$chk4. "')");
		}

		if(!empty($alternateemail_address)&&($chk1==""))
		{
			$database->query("insert into emails_to_users (user_id,email_address) values('" . $user_id . "','" . $alternateemail_address . "')");
		}
		if(!empty($alternateemail_address2)&&($chk2==""))
		{
			$database->query("insert into emails_to_users (user_id,email_address) values('" . $user_id . "','" . $alternateemail_address2 . "')");
		}
		if(!empty($alternateemail_address3)&&($chk3==""))
		{
			$database->query("insert into emails_to_users (user_id,email_address) values('" . $user_id . "','" . $alternateemail_address3 . "')");
		}
		if(!empty($alternateemail_address4)&&($chk4==""))
		{
			$database->query("insert into emails_to_users (user_id,email_address) values('" . $user_id . "','" . $alternateemail_address4 . "')");
		}
		$database->query("insert into " . TABLE_USERS_DESCRIPTION . " (user_id, firstname, lastname) values ('" . $user_id . "', '" . $first_name . "', '" . $last_name . "')");
		$database->query("insert into " . TABLE_USERS_TO_USER_GROUPS . " (user_id, user_group_id) values ('" . $user_id . "', '1')");
		$phone_numbers_array = array();
		$phone_numbers_array[] = $phone_number;
		$phone_numbers_array[] = $second_phone_number;
		if (!empty($optional_third_phone_number)) 
		{
			$phone_numbers_array[] = $optional_third_phone_number;
		}
		if (!empty($optional_fourth_phone_number)) 
		{
			$phone_numbers_array[] = $optional_fourth_phone_number;
		}
		$count = count($phone_numbers_array);
		$n = 0;
		while($n < $count) 
		{
			$database->query("insert into " . TABLE_USERS_PHONE_NUMBERS . " (user_id, phone_number, order_id) values ('" . $user_id . "', '" . $phone_numbers_array[$n] . "', '" . ($n + 1) . "')");
			$n++;
		}
		if (!empty($referring_agent_name)) 
		{
			$database->query("insert into " . TABLE_REFERRALS . " (user_id, agent_name, agent_id, agent_email) values ('" . $user_id . "', '" . $referring_agent_name . "', '" . $referring_agent_mris_id . "', '" . $referring_agent_email . "')");
		}
		//Now send the email.

		$email_template = new email_template('account_create');
		$email_template->load_email_template();
		$email_template->set_email_template_variable('EMAIL_ADDRESS', $email_address);
		$email_template->set_email_template_variable('PASSWORD', $password);
        $email_template->set_email_template_variable('FIRST', $first_name);
        $email_template->set_email_template_variable('LAST', $last_name);
        $email_template->set_email_template_variable('AGENT_NAME', $first_name.' '.$last_name);
		$email_template->set_email_template_variable('AGENCY_NAME', tep_get_agency_name($agency_id));
		$email_template->set_email_template_variable('SERVICE_LEVEL', tep_get_service_level_name($service_level_id));
		$email_template->parse_template();
		$email_template->send_email($email_address, $first_name.', '.$last_name);
		//Now set the new user id and log them in.

		$user->set_user_id($user_id);
		$user->login_user();

		//No redirect to the success page.
		tep_redirect('account_create_success.php');
	}
}
							//$email_template = new email_template('account_create');
					//$email_template->load_email_template();
					//$email_template->set_email_template_variable('EMAIL_ADDRESS', 'jon@dmnetwork.co.nz');
					//$email_template->set_email_template_variable('PASSWORD', '12324');
					//$email_template->set_email_template_variable('AGENT_NAME', 'test name');
					//$email_template->parse_template();
//echo $email_template->template_commands['SUBJECT'] . '<br>';
		//$email_template->send_email('laughland@xtra.co.nz', 'test name');
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
						<table cellpadding="0" cellspacing="3" border="0">
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
								<td class="main">Alternate Email 1: </td><td><input type="text" name="alternate_email_address" value="<?php echo $alternateemail_address; ?>" /><input type="checkbox" name="chkexpert1" value="1" <?php if($chk1) echo 'checked="true"'; ?> />Receive emails on this Email Address</td>
							</tr>
							<tr>
								<td class="main">Alternate Email 2: </td><td><input type="text" name="alternate_email_address2" value="<?php echo $alternateemail_address2; ?>" /><input type="checkbox" name="chkexpert2" value="1" <?php if($chk2) echo 'checked="true"'; ?> />Receive emails on this Email Address</td>
							</tr>
							<tr>
								<td class="main">Alternate Email 3: </td><td><input type="text" name="alternate_email_address3" value="<?php echo $alternateemail_address3; ?>" /><input type="checkbox" name="chkexpert3" value="1" <?php if($chk3) echo 'checked="true"'; ?> />Receive emails on this Email Address</td>
							</tr>
							<tr>
								<td class="main">Alternate Email 4: </td><td><input type="text" name="alternate_email_address4" value="<?php echo $alternateemail_address4; ?>" /><input type="checkbox" name="chkexpert4" value="1" <?php if($chk4) echo 'checked="true"'; ?> />Receive emails on this Email Address</td>
							</tr>
							<tr>
								<td class="main">Password: </td><td><input type="password" name="password" value="<?php echo $password; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Password Confirm: </td><td><input type="password" name="password_confirmation" value="<?php echo $password_confirmation; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Cell Phone Number: </td><td><input type="text" name="phone_number" value="<?php echo $phone_number; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Phone Number: </td><td><input type="text" name="second_phone_number" value="<?php echo $second_phone_number; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Fax Number: </td><td><input type="text" name="optional_third_phone_number" value="<?php echo $optional_third_phone_number; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Optional Phone Number: </td><td><input type="text" name="optional_fourth_phone_number" value="<?php echo $optional_fourth_phone_number; ?>" /></td>
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
							
							?>
							<tr>
								<td class="main">Select Agency: </td><td><?php echo tep_draw_agency_pulldown('agency_id', $agency_id, ' onchange="this.form.submit();"', $agency_array, '', true, false); ?></td>
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
									$service_level_id = $result['service_level_id'];
									$billing_method_id = $result['billing_method_id'];
							?>
							<tr>
								<td class="main">Agency Name: </td><td class="mainGrey" ><?php echo $result['name']; ?></td>
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
								<td class="main">Create Agency: </td><td class="main"><input type="checkbox" name="create_agency" onclick="javascript:toggle_fields_status();" value="1" <?php echo (($create_agency == '1') ? 'CHECKED' : ''); ?> /></td>
							</tr>
							<tr>
								<td class="main">Agency Name: </td><td class="main"><input type="text" id="agency_name" name="agency_name" value="<?php echo $agency_name; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Agency Address: </td><td class="main"><input type="text" id="agency_address" name="agency_address" value="<?php echo $agency_address; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Managing Broker: </td><td class="main"><input type="text" id="contact_name" name="contact_name" value="<?php echo $contact_name; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Contact Phone: </td><td class="main"><input type="text" id="contact_phone" name="contact_phone" value="<?php echo $contact_phone; ?>" /></td>
							</tr>
							<script language="javascript">
									function toggle_fields_status() {
										document.getElementById('agency_name').disabled = fields_status;
										document.getElementById('agency_address').disabled = fields_status;
										document.getElementById('contact_name').disabled = fields_status;
										document.getElementById('contact_phone').disabled = fields_status;
										
											if (fields_status) {
												fields_status = false;
											} else {
												fields_status = true;
											}
									}
								
								var fields_status = "<?php echo (($create_agency == '1') ? 'false' : 'true'); ?>";
								toggle_fields_status();
							</script>
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
								<td class="main">Service Level: </td><td class="main"><?php echo tep_generate_pulldown_menu('service_level_id', $service_levels_array, $service_level_id); ?>&nbsp;<a href="service_plans.php">Service plans</a></td>
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
								<td colspan="2" align="center"><?php echo tep_create_button_submit('create_account', 'Create Account', ' name="submit_type" value="1"'); ?> &nbsp &nbsp;<?php echo tep_create_button_link('reset', 'Reset Form', ' onclick="document.all[\'create_account\'].reset()"'); ?></td>
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
