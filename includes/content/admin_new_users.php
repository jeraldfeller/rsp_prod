<?php
$page_action = tep_fill_variable('page_action', 'get');
$uID = tep_fill_variable('uID', 'get');
$show_user_group_id = tep_fill_variable('show_user_group_id', 'get', '1');
$search_name = tep_fill_variable('search_name', 'get');
$submit_value = tep_fill_variable('submit_value_y', 'post');
$show_agency_id = tep_fill_variable('show_agency_id', 'get', '');
$start_letter = tep_fill_variable('start_letter', 'get', '');

$message = '';
$pages = tep_fill_variable('pages', 'post', array());
if (!empty($submit_value)) 
{
	$email_address = tep_fill_variable('email_address', 'post');
	$agent_id = tep_fill_variable('agent_id', 'post');
	$billing_method_id = tep_fill_variable('billing_method_id', 'post');
	$service_level_id = tep_fill_variable('service_level_id', 'post');
	$agency_id = tep_fill_variable('agency_id', 'post');
	$firstname = tep_fill_variable('firstname', 'post');
	$lastname = tep_fill_variable('lastname', 'post');
	$street_address = tep_fill_variable('street_address', 'post');
	$postcode = tep_fill_variable('postcode', 'post');
	$city = tep_fill_variable('city', 'post');
	$county_id = tep_fill_variable('county_id', 'post');
	$require_deposit = tep_fill_variable('require_deposit', 'post');
	$deposit_remaining_count = tep_fill_variable('deposit_remaining_count', 'post');
	$state_id = tep_fill_variable('state_id', 'post');
	$user_group_id = tep_fill_variable('user_group_id', 'post');
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
				
				$database->query("update " . TABLE_USERS . " set email_address = '" . $email_address . "', agent_id = '" . $agent_id . "', billing_method_id = '" . $billing_method_id . "', service_level_id = '" . $service_level_id . "', agency_id = '" . $agency_id . "', require_deposit = '" . $require_deposit . "', deposit_remaining_count = '" . $deposit_remaining_count . "' where user_id = '" . $uID . "' limit 1");
				
				//start add 08.01.2014 DrTech76, hook teh user to agency change log
				if($old_agency!=(int)$agency_id)
				{
					$sql="INSERT INTO `agencies_to_users`(`user_id`,`agency_id`,`action_date`,`account_action_type`,`account_action_from`) VALUES (".$uID.",".$agency_id.",NOW(),'update','admin')";
					$database->query($sql);
                    $sql="UPDATE " . TABLE_ACCOUNTS . " SET agency_id = '{$agency_id}' WHERE user_id = '{$uID}' LIMIT 1";
                    $database->query($sql);
				}
				//end add 08.01.2014 DrTech76, hook teh user to agency change log
				
				$database->query("update " . TABLE_USERS_DESCRIPTION . " set firstname = '" . $firstname . "', lastname = '" . $lastname . "', street_address = '" . $street_address . "', postcode = '" . $postcode . "', city = '" . $city . "', county_id = '" . $county_id . "', state_id = '" . $state_id . "' where user_id = '" . $uID . "' limit 1");
				$database->query("update " . TABLE_USERS_TO_USER_GROUPS . " set user_group_id = '" . $user_group_id . "' where user_id = '" . $uID . "' limit 1");
				
				//Now the phone numbers.
				$number = tep_fill_variable('number', 'post', array());
				$database->query("delete from " . TABLE_USERS_PHONE_NUMBERS . " where user_id = '" . $uID . "'");
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
					
				$message = 'Successfully Updated';
				$page_action = '';
				
			} 
			else 
			{
				$password = substr(md5(mktime()), 4, 6);
				$database->query("insert into " . TABLE_USERS . " (email_address, password, agent_id, billing_method_id, service_level_id, agency_id, require_deposit, deposit_remaining_count) values ('" . $email_address . "', '" . md5($password) . "', '" . $agent . "', '" . $billing_method_id . "', '" . $service_level_id . "', '" . $agency_id . "', '" . $require_deposit . "', '" . $deposit_remaining_count . "')");
				$uID = $database->insert_id();
				
				
				//start add 08.01.2014 DrTech76, hook teh user to agency change log
				$sql="INSERT INTO `agencies_to_users`(`user_id`,`agency_id`,`action_date`,`account_action_type`,`account_action_from`) VALUES (".$uID.",".$agency_id.",NOW(),'create','admin')";
				$database->query($sql);
				//end add 08.01.2014 DrTech76, hook teh user to agency change log
				
				
				$database->query("insert into " . TABLE_USERS_DESCRIPTION . " (user_id, firstname, lastname, street_address, postcode, city, county_id, state_id) values ('" . $uID . "', '" . $firstname . "', '" . $lastname . "', '" . $street_address . "', '" . $postcode . "', '" . $city . "', '" . $county_id . "', '" . $state_id . "')");
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
				
				$email_template = new email_template('account_create');
				$email_template->load_email_template();
				$email_template->set_email_template_variable('EMAIL_ADDRESS', $email_address);
                $email_template->set_email_template_variable('PASSWORD', $password);
                $email_template->set_email_template_variable('AGENCY_NAME', tep_get_agency_name($agency_id));
                $email_template->set_email_template_variable('AGENT_NAME', $firstname & " " & $lastname);
				$email_template->set_email_template_variable('TYPE', tep_get_user_group_name($user_group_id));
				$email_template->parse_template();
				$email_template->send_email($email_address, $firstname.', '.$lastname);
				$message = 'Successfully Inserted, user has been emailed new password';
				$page_action = '';
			}
		$page_action = '';
		$uID = '';
	}
	
}
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
if ($page_action == 'enable_confirm') 
{
	$database->query("update " . TABLE_USERS . " set users_status = '1', user_verified_date = '" . mktime() . "' where user_id = '" . $uID . "' limit 1");
					
	$message = 'Successfully Updated';
	$page_action = '';

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
				if (($page_action != 'edit')&&($page_action != 'add')) {
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td width="20%" class="pageBoxHeading">User Name</td>
						<td width="20%" class="pageBoxHeading" align="center">User Email</td>
						<td width="20%" class="pageBoxHeading" align="center">User Group</td>
						<td width="20%" class="pageBoxHeading" align="center">Agency</td>
						<td width="20%" class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$uData = array();
					$listing_split = new split_page("select u.user_id, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.agency_id from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and  u.users_status=0 and ".((!empty($show_user_group_id)) ? ("utug.user_group_id = '" . $show_user_group_id . "' and ") : '').((!empty($search_name)) ? ("((ud.firstname like '%" . $search_name . "' or ud.firstname = '" . $search_name . "') or (ud.lastname = '" . $search_name . "' or ud.lastname like '%" . $search_name . "')) and ") : '')."utug.user_group_id = ug.user_group_id" . ((!empty($show_agency_id)) ? " and (a.agency_id = '" . $show_agency_id . "' or a.parent_agency_id = '" . $show_agency_id . "')" : '') . ((!empty($start_letter)) ? " and ud.firstname like '".$start_letter."%'" : '') . " order by ud.firstname", '20', 'u.user_id');
						
						if ($listing_split->number_of_rows > 0) {
							$query = $database->query($listing_split->sql_query);
								while($result = $database->fetch_array($query)) {
										if ($result['user_id'] == $uID) {
											$uData = $result;
										}
									
						?>
							<tr>
								<td width="20%" class="pageBoxContent"><?php echo $result['firstname'].', ',$result['lastname']; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $result['email_address']; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $result['name']; ?></td>
								<?php
									$string = '';
										if ($result['user_group_id'] == '1') {
											$agency_query = $database->query("select agency_id, name from " . TABLE_AGENCYS . " where agency_id = '" . $result['agency_id'] . "' limit 1");
											$agency_result = $database->fetch_array($agency_query);
											
												if (!empty($agency_result['agency_id'])) {
													$string = '<a href="'.FILENAME_ADMIN_AGENCYS.'?aID='.$result['agency_id'].'&page_action=edit">'.$agency_result['name'].'</a>';
												}
										}
								?>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $string; ?></td>
								<td width="20%" class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_NEW_USERS . '?uID='.$result['user_id'].'&page_action=edit&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_NEW_USERS . '?uID='.$result['user_id'].'&page_action=delete&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">Delete</a>| <a href="<?php echo FILENAME_ADMIN_NEW_USERS . '?uID='.$result['user_id'].'&page_action=enable&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">Enable</a>


								</td>
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
						$user_data_query = $database->query("select u.user_id, u.email_address, u.agent_id, u.billing_method_id, u.service_level_id, u.agency_id, u.require_deposit, u.deposit_remaining_count, u.discount_type, u.discount_amount, ud.firstname, ud.lastname, ud.gender, ud.street_address, ud.postcode, ud.city, ud.county_id, ud.state_id, utug.user_group_id from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = '" . $uID . "' and u.user_id = ud.user_id and u.user_id = utug.user_id limit 1");
						$user_data_result = $database->fetch_array($user_data_query);
							$user_result = array('email_address' => tep_fill_variable('email_address', 'post', $user_data_result['email_address']),
															 'agent_id' => tep_fill_variable('agent_id', 'post', $user_data_result['agent_id']),
															 'billing_method_id' => tep_fill_variable('billing_method_id', 'post', $user_data_result['billing_method_id']),
															 'service_level_id' => tep_fill_variable('service_level_id', 'post', $user_data_result['service_level_id']),
															 'agency_id' => tep_fill_variable('agency_id', 'post', $user_data_result['agency_id']),
															 'firstname' => tep_fill_variable('firstname', 'post', $user_data_result['firstname']),
															 'lastname' => tep_fill_variable('lastname', 'post', $user_data_result['lastname']),
															 'gender' => tep_fill_variable('gender', 'post', $user_data_result['gender']),
															 'street_address' => tep_fill_variable('street_address', 'post', $user_data_result['street_address']),
															 'postcode' => tep_fill_variable('postcode', 'post', $user_data_result['postcode']),
															 'city' => tep_fill_variable('city', 'post', $user_data_result['city']),
															 'county_id' => tep_fill_variable('county_id', 'post', $user_data_result['county_id']),
															 'state_id' => tep_fill_variable('state_id', 'post', $user_data_result['state_id']),
															 'deposit_remaining_count' => tep_fill_variable('deposit_remaining_count', 'post', $user_data_result['deposit_remaining_count']),
															 'require_deposit' => tep_fill_variable('require_deposit', 'post', $user_data_result['require_deposit']),
															 'user_group_id' => tep_fill_variable('user_group_id', 'post', $user_data_result['user_group_id']),
															 'discount_type' => tep_fill_variable('discount_type', 'post', $user_data_result['discount_type']),
															 'discount_amount' => tep_fill_variable('discount_amount', 'post', $user_data_result['discount_amount']));
					} else {
						//Add
						$user_result = array('user_id' => tep_fill_variable('user_id', 'post'),
														 'email_address' => tep_fill_variable('email_address', 'post'),
														 'agent_id' => tep_fill_variable('agent_id', 'post'),
														 'billing_method_id' => tep_fill_variable('billing_method_id', 'post'),
														 'service_level_id' => tep_fill_variable('service_level_id', 'post'),
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
														 'user_group_id' => tep_fill_variable('user_group_id', 'post'));
					}
				
			?>
			<?php
				if($page_action=='edit') {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEW_USERS . '?page_action=edit&uID='.$uID.'&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">
			<?php
				//}



}
else {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEW_USERS . '?page_action=add&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>">
			<?php
				}
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxContent">User Email Address</td><td class="pageBoxContent"><input type="text" name="email_address" value="<?php echo $user_result['email_address']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">User First Name</td><td class="pageBoxContent"><input type="text" name="firstname" value="<?php echo $user_result['firstname']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">User Last Name</td><td class="pageBoxContent"><input type="text" name="lastname" value="<?php echo $user_result['lastname']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Group</td><td class="pageBoxContent"><?php echo tep_draw_group_pulldown('user_group_id', tep_fill_variable('user_group_id', 'post', $user_result['user_group_id']), ' onchange="this.form.submit();"'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Street Address</td><td class="pageBoxContent"><input type="text" name="street_address" value="<?php echo $user_result['street_address']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">City</td><td class="pageBoxContent"><input type="text" name="city" value="<?php echo $user_result['city']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">State</td><td class="pageBoxContent"><?php echo tep_draw_state_pulldown('state_id', tep_fill_variable('state_id', 'post', $user_result['state_id']), ' onchange="this.form.submit();"'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Post Code</td><td class="pageBoxContent"><input type="text" name="postcode" value="<?php echo $user_result['postcode']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">County</td><td class="pageBoxContent"><?php echo tep_draw_county_pulldown('county_id', tep_fill_variable('state_id', 'post', $user_result['state_id']), $user_result['county_id']); ?></td>
					</tr>
					<?php
						//Phone numbers.
						$numbers = tep_fill_variable('number', 'post', array());
							if (empty($numbers)) {
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
									<td class="pageBoxContent">Phone Number <?php echo ($n+1);?>: </td><td class="pageBoxContent"><input type="text" name="number[]" value="<?php echo $number; ?>" /></td>
								</tr>
								<?php
							}
						//Extra email addresses.
						$emails = tep_fill_variable('email', 'post', array());
						$emails_status = tep_fill_variable('email_checked', 'post', array());
							if (empty($emails) && empty($emails_status)) {
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
					?>
					<tr>
						<td class="pageBoxContent">Require Deposit: </td>
						<td class="pageBoxContent"><input type="radio" name="require_deposit" vaue="1"<?php echo (($user_result['require_deposit'] == '1') ? ' CHECKED' : ''); ?> />&nbsp;True&nbsp;&nbsp;<input type="radio" name="require_deposit" vaue="0"<?php echo (($user_result['require_deposit'] == '0') ? ' CHECKED' : ''); ?> />&nbsp;False</td>
					</tr>
					<tr>
						<td class="pageBoxContent">Remaining Deposit Count: </td>
						<td class="pageBoxContent"><input type="text" value="<?php echo $user_result['deposit_remaining_count']; ?>" /> (to set as unlimited set this as 0 and the above as true)</td>
					</tr>
					<tr>
						<td class="pageBoxContent">Agent ID</td><td class="pageBoxContent"><input type="text" name="agent_id" value="<?php echo $user_result['agent_id']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Agency</td><td class="pageBoxContent"><?php echo tep_draw_agency_pulldown('agency_id', $user_result['agency_id']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Billing Method</td><td class="pageBoxContent"><?php echo tep_draw_billing_method_pulldown('billing_method_id', $user_result['billing_method_id'], '', false); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Service Level</td><td class="pageBoxContent"><?php echo tep_draw_service_level_pulldown('service_level_id', $user_result['service_level_id'], '', false); ?></td>
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
						if ($page_action == 'edit') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Make your required changes and press "Update" below or press "Cancel" to cancel your changes.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_NEW_USERS. '?' .  tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
						} elseif ($page_action == 'delete') {
							
					?>
					<form action="<?php echo FILENAME_ADMIN_NEW_USERS. '?page_action=delete_confirm&uID='.$uID .  '&' . tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you want to delete "<?php echo $uData['firstname'] . ' ' . $uData['lastname']; ?>"?  This action can not be undone.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">Assign Orders to: <?php echo tep_draw_user_pulldown('merge_user_id', '', '', array(array('id' => '', 'name' => 'None'))); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('delete', 'Delete'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_NEW_USERS. '?' .  tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
						} elseif ($page_action == 'enable') {
							
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you want to activate "<?php echo $uData['firstname'] . ' ' . $uData['lastname']; ?>"?  This action can not be undone.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_NEW_USERS. '?page_action=enable_confirm&uID='.$uID .  '&' . tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post"><?php echo tep_create_button_submit('enable', 'Enable'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_NEW_USERS. '?' .  tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>



										</tr>
									</table>
								</td>
							</tr>
						</table>
<?
}
elseif (!empty($page_action)) {
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
											<td align="left"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEW_USERS . '?page_action=edit&uID='.$uID; ?>"><?php echo tep_create_button_submit('edit', 'Edit'); ?><!--<input type="submit" value="Edit">--></form></td>
											<td align="right"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEW_USERS ; ?>"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?><!--<input type="submit" value="Cancel">--></form></td>
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
											<td align="left"><input type="submit" value="Create" name="submit_value"></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_NEW_USERS.'?'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
				<form action="<?php echo FILENAME_ADMIN_NEW_USERS; ?>" method="get">
				<tr>
					<td class="main">Show users of Group: <?php echo tep_draw_group_pulldown('show_user_group_id', $show_user_group_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'All'))); ?></td>
				</tr>
					<?php
						if ($show_user_group_id == '1') {
						?>
						<tr>
							<td class="main">Show only Agency: <?php echo tep_draw_agency_pulldown('show_agency_id', $show_agency_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any'))); ?></td>
						</tr>
						<?php
						}
					?>
				<tr>
					<td class="main">Show users with name like: <input type="text" name="search_name" value="<?php echo $search_name; ?>" /></td>
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
					<td width="100%" align="right"><input type="submit" value="Search" /></td>
				</tr>
				</form>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td width="100%" align="right"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEW_USERS . '?page_action=add&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>"><input type="submit" value="Add User"></form></td>
				</tr>
			</table>
		<?php
				}
			}
		?>
		</td>
	</tr>
</table>
