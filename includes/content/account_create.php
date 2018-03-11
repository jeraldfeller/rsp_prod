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

$service_level_id = tep_fill_variable('service_level_id');
$billing_method_id = tep_fill_variable('billing_method_id');
$referring_agent_name = tep_fill_variable('referring_agent_name');
$referring_agent_mris_id = tep_fill_variable('referring_agent_mris_id');
$referring_agent_email_address = tep_fill_variable('referring_agent_email_address');
$chk1 = $chk2 = $chk3 = $chk4 = '';
$submit_type = tep_fill_variable('submit_type_y');

#Start Added By Mukesh 
$is_new = tep_fill_variable('is_new');
$agency_name = tep_fill_variable('agency_name');
$agency_address = tep_fill_variable('agency_address');
$contact_name = tep_fill_variable('contact_name');
$contact_phone = tep_fill_variable('contact_phone');
$addr_street = tep_fill_variable('addr_street');
$addr_city = tep_fill_variable('addr_city');
$state_id = tep_fill_variable('state_id');
$county_id = tep_fill_variable('county_id');

#$addr_state = tep_fill_variable('state_id');
#$addr_county = tep_fill_variable('addr_county');

$addr_zip = tep_fill_variable('addr_zip');

$ajaxAction = tep_fill_variable('ajaxAction', 'post'); #added by Mukesh


#Start Added By Mukesh 
if(isset($ajaxAction) && !empty($ajaxAction) ){
	
	if($ajaxAction == 'get-agency-address'){
	
		$agency_id = tep_fill_variable('agency_id', 'post');
	
		$query = $database->query("select name, service_level_id, billing_method_id, contact_name, contact_phone, address, addr_street, addr_city, addr_state, addr_county, addr_zip from " . TABLE_AGENCYS . " where agency_id = " . $agency_id );
		
		$result = $database->fetch_array($query);
		
		if(!empty($result['addr_street'])){
			$address = $result['addr_street'];
			$address .= (!empty($result['addr_city']) ? ', '.$result['addr_city'] : null);
			$address .= (!empty($result['addr_state']) ? ', '.tep_get_state_name($result['addr_state']) : null);
			$address .= (!empty($result['addr_county']) ? ', '.tep_get_county_name($result['addr_county']) : null);
			$address .= (!empty($result['addr_zip']) ? ', '.$result['addr_zip'] : null);
		}else {
			if(!empty($result['address']))
				$address = $result['address'];
			else
				$address = null;	
		}		
		
		$data = array(
			'agency_name'=> $result['name'],
			'agency_address'=> $address,
			'managing_broker'=>$result['contact_name'],
			'contact_phone'=> $result['contact_phone'],
			'service_level_id' => $result['service_level_id'],
			'billing_method_id' => $result['billing_method_id']
		);
		
		echo json_encode(  array('status'=>'success','data'=>$data)); die;
	
	}
	
	if($ajaxAction == 'get-state-county'){
		
		$state_id = tep_fill_variable('state_id', 'post');
		
		$query = $database->query("select county_id, name as county_name from " . TABLE_COUNTYS . " where state_id = '" . $state_id . "' order by name");
		$data[] = 'Please Select';
		foreach($database->fetch_array($query) as $result){
			
			$data[$result['county_id']] = $result['county_name'];
			
		}		
		echo json_encode(  array('status'=>'success','data'=>$data)); die;

		#print_r($array);die;
	} 
}
#End Added By Mukesh




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
		if (empty($agency_name) || empty($contact_name) || empty($contact_phone) || empty($addr_street) || empty($addr_city) || empty($state_id) || empty($county_id) || empty($addr_zip)) 
		{
			$error->add_error('account_create', 'Please either select an Agency or Fill all the Agency Information to add a new one.');
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
		
		
		if(isset($is_new) && $is_new ==1){
			

			$database->query("insert into " . TABLE_AGENCYS . " (name, address, contact_name, contact_phone, addr_street, addr_city, addr_state, addr_county, addr_zip,  agency_status_id) values ('" . $agency_name . "', '" . $agency_address . "', '" . $contact_name . "', '" . $contact_phone . "', '".$addr_street."', '".$addr_city."', '".$state_id."', '".$county_id."', '".$addr_zip."', '0')");
			
			$agency_id = $database->insert_id(); #Set agency as new agency_id.
			
			
		}
			
		
		#No error. Add.
		/*if (empty($agency_id) && !empty($create_agency)){
			
			$database->query("insert into " . TABLE_AGENCYS . " (name, address, contact_name, contact_phone, agency_status_id) values ('" . $agency_name . "', '" . $agency_address . "', '" . $contact_name . "', '" . $contact_phone . "', '0')");
			
			$agency_id = $database->insert_id(); #Set agency as new agency_id.
		}*/
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
	/*	$email_template = new email_template('account_create');
		$email_template->load_email_template();
		$email_template->set_email_template_variable('EMAIL_ADDRESS', 'jon@dmnetwork.co.nz');
		$email_template->set_email_template_variable('PASSWORD', '12324');
		$email_template->set_email_template_variable('AGENT_NAME', 'test name');
		$email_template->parse_template();
		echo $email_template->template_commands['SUBJECT'] . '<br>';
		$email_template->send_email('laughland@xtra.co.nz', 'test name');
	*/
?>
<style>
	td.mainGrey{ padding-left:71px!important; font-size:14px!important;}
	.new-agency-info td.main input, .new-agency-info td.main select{ margin-left:71px!important;}
</style>
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
                            
                            <!-- Start Edited By Mukesh -->
                            
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
							</tr>
                            <tr>
                                <td class="pageBoxContent">Agency Options: </td>
                                <td class="pageBoxContent">
                                    <input type="radio" name="is_new" value="0" CHECKED />&nbsp;Select Existing Agency&nbsp;&nbsp;
                                    <input type="radio" name="is_new" value="1" />&nbsp;Create New Agency<br />
                                    <i>Please choose Agency or "Create New Agency" & fill in details below!</i></td>
                            </tr>    
                            <tr><td height="6"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>                        
							<!--<tr><td class="main" colspan="2">Select your agency below or fill in the details to create a new one.</td></tr>-->
							<?php
								$agency_array = array();
								$agency_array[] = array('id' => '', 'name' => 'Please select or fill in details below');
							
							?>
							<tr>
								<td class="main">Select Agency: </td>
                                <td><?php echo tep_draw_agency_pulldown('agency_id', $agency_id, '', $agency_array, '', true, false); ?></td>
							</tr>
							
                            <tr>
                                <td colspan="2">
                                	<table cellpadding="0" cellspacing="0" border="0" class="show-agency-info" style="display:none">
                                        <tr><td class="main">Agency Name: </td><td class="mainGrey agency-name">&nbsp;</td></tr>
                                        <tr><td class="main">Agency Address: </td><td class="mainGrey agency-address">&nbsp;</td></tr>
                                        <tr><td class="main">Managing Broker: </td><td class="mainGrey managing-broker">&nbsp;</td></tr>
                                        <tr><td class="main">Contact Phone: </td><td class="mainGrey contact-phone">&nbsp;</td></tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                	<table cellpadding="0" cellspacing="0" border="0" class="new-agency-info" style="display:none">
                                    <tr>
                                        <td class="main">Agency Name: </td><td class="main"><input type="text" name="agency_name" value="" /></td>
                                    </tr>
                                    <tr>
                                        <td class="main">Managing Broker: </td><td class="main"><input type="text" name="contact_name" value="" /></td>
                                    </tr>
                                    <tr>
                                        <td class="main">Contact Phone: </td><td class="main"><input type="text" name="contact_phone" value="" /></td>
                                    </tr>                                    
                                    <tr>
                                        <td class="main">Street Address</td><td class="main"><input type="text" name="addr_street" value="" /></td>
                                    </tr>
                                    <tr>
                                        <td class="main">City</td><td class="main"><input type="text" name="addr_city" value="" /></td>
                                    </tr>
                                    <tr>
                                        <td class="main">State</td>
                                        <td class="main"><?php echo tep_draw_state_pulldown('state_id', tep_fill_variable('state_id', 'post'), '');?></td>
                                    </tr>
                                    <tr>
                                        <td class="main">County</td>
                                        <td class="main"><?php echo tep_draw_county_pulldown('county_id', tep_fill_variable('county_id', 'post','' ),'');?></td>
                                    </tr>
                                    <tr>
                                        <td class="main">Post Code</td><td class="main"><input type="text" name="addr_zip" value="" /></td>
                                    </tr>                                    
                                    
                                    
                                    </table>
                                </td>
                            </tr>
							
							
							<!-- End Edited By Mukesh -->
							
                            <?php 	

								$service_levels_array = array();
								$query = $database->query("select service_level_id, name from " . TABLE_SERVICE_LEVELS . " order by service_level_id");
								foreach($database->fetch_array($query) as $result){
									$service_levels_array[] = array('id' => $result['service_level_id'], 'name' => $result['name']);
								}
								
								if (empty($billing_method_id)) {
									$billing_method_id = 3;
								}

								$billing_methods_array = array();
								//$query = $database->query("select billing_method_id, name from " . TABLE_BILLING_METHODS . " where billing_method_id <= '" . $billing_method_id . "' order by billing_method_id");
								$query = $database->query("select billing_method_id, name from " . TABLE_BILLING_METHODS . " order by billing_method_id"); // edit by jerald
								#$query = $database->query("select billing_method_id, name from " . TABLE_BILLING_METHODS . " order by name");

                                foreach($database->fetch_array($query) as $result){
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
								<td class="main">Billing Method: </td><td class="main"><?php echo tep_generate_pulldown_menu('billing_method_id', $billing_methods_array, "$billing_method_id"); ?></td>
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

<script type="text/javascript">
	var action = null;
	var agency_id = null;
	var state_id = null;
	var page_url = "<?php echo FILENAME_ACCOUNT_CREATE?>";
	
	$(document).ready(function() {	
		
		$(document).on("click", "input[name='is_new']", function () {	
			action = $(this).val();
			
			if(action==1){
				$('.new-agency-info').show();				
				$('.show-agency-info').hide();	
				$("select[name='agency_id']").val(null).prop("disabled", true);
				$("select[name='service_level_id']").val(1);
				$("select[name='billing_method_id']").val(1);
			} else if(action==0){
				$('.new-agency-info').hide();	
				$('.show-agency-info').show();				
				$("select[name='agency_id']").prop("disabled", false);
			}
			
		});
		
		
		$(document).on("change", "select[name='agency_id']", function () {	
			
			agency_id = $(this).val();
			$.ajax({
				type: 'POST',
				url: page_url,
				dataType: "json",
				data:{'ajaxAction': 'get-agency-address','agency_id':agency_id},
				cache: false,
				beforeSend:function(){

					$('.show-agency-info').hide();
				},				
				success: function(res){
					if(res.status=='success'){

						$(".agency-name").html(res.data.agency_name);
						$(".agency-address").html(res.data.agency_address);
						$(".managing-broker").html(res.data.managing_broker);
						$(".contact-phone").html(res.data.contact_phone);
						$('.show-agency-info').show();
						$("select[name='service_level_id']").val(res.data.service_level_id);
						$("select[name='billing_method_id']").val(res.data.billing_method_id);
						//alert(res.data.service_level_id + " : "+res.data.billing_method_id);
					} 

				}
			});				
			
			
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
				
				$("input[name='street_address']").val(null);
				$("input[name='city']").val(null);
				$("input[name='postcode']").val(null);				
			}
			
		});
		
		
	})
</script>