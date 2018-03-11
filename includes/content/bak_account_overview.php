<?php
/*
$order_id = '11';
$order = new orders('fetch', $order_id);
$data = $order->fetch_order();
 var_dump($data['optional']);
$query = $database->query("select u.email_address, u.agent_id, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id) where u.user_id = '" . $data['user_id'] . "' and u.user_id = ud.user_id limit 1");
$result = $database->fetch_array($query);

$email_template = new email_template('order_confirm');
$email_template->load_email_template();
$email_template->set_email_template_variable('ORDER_TYPE',tep_get_order_type_name($data['order_type_id']));

$email_template->set_email_template_variable('HOUSE_NUMBER',$data['house_number']);		
$email_template->set_email_template_variable('AGENT_NAME',$result['firstname'].' '.$result['lastname']);		
$email_template->set_email_template_variable('AGENT_ID',$result['agent_id']);		
$email_template->set_email_template_variable('AGENCY_NAME',$result['name']);					
$email_template->set_email_template_variable('STREET_NAME', $data['street_name']);
$email_template->set_email_template_variable('DATE_ADDED', date("F j, Y, g:i a", $data['date_added']));

$email_template->set_email_template_variable('CITY', $data['city']);
$email_template->set_email_template_variable('DATE_SCHEDULED', date("n/d/Y", $data['date_schedualed']));
$email_template->set_email_template_variable('NUMBER_OF_POSTS', $data['number_of_posts']);
$email_template->set_email_template_variable('SPECIAL_INSTRUCTIONS', $data['special_instructions']);
$email_template->set_email_template_variable('CROSS_STREET_DIRECTIONS', $data['cross_street_directions']);
$email_template->set_email_template_variable('COUNTY_NAME', tep_get_county_name($data['county_id']));
$email_template->set_email_template_variable('STATE_NAME', tep_get_state_name($data['state_id']));
$email_template->set_email_template_variable('AGENT_EMAIL', $result['email_address']);

$email_template->set_email_template_variable('ADC_NUMBER', $data['adc_number']);
					
$email_template->set_email_template_variable('ZIP', $data['zip']);
$email_template->set_email_template_variable('EQUIPMENT', tep_create_view_equipment_string($data['optional']));


//Now the equipment.

$email_template->parse_template();


					

					$email_template->send_email($result['email_address'],$result['firstname'].' '.$result['lastname']);


*/
/*$query = $database->query("select user_id from " . TABLE_USERS_TO_USER_GROUPS . " where user_group_id = '1'");
	while($result = $database->fetch_array($query)) {
		$database->query("delete from " . TABLE_USERS . " where user_id = '" . $result['user_id'] . "' limit 1");
		//echo "delete from " . TABLE_USERS . " where user_id = '" . $result['user_id'] . "' limit 1". '<br>';
		$database->query("delete from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $result['user_id'] . "' limit 1");
		//echo "delete from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $result['user_id'] . "' limit 1" . '<br>';
		$database->query("delete from " . TABLE_USERS_TO_USER_GROUPS . " where user_id = '" . $result['user_id'] . "' limit 1");
		//echo "delete from " . TABLE_USERS_TO_USER_GROUPS . " where user_id = '" . $result['user_id'] . "' limit 1" . '<br>';
	}*/
	/*$stamp = mktime(0, 0, 1, 12, 13, 2006);
	echo date("d/n/Y", $stamp) . '<br><br>';
	$count = 0;
	$query = $database->query("select count(o.order_id) as count, u.firstname, u.lastname from " . TABLE_ORDERS . " o, " . TABLE_USERS_DESCRIPTION . " u where o.date_added > '" . $stamp . "' and o.user_id = u.user_id group by u.user_id order by count DESC");
		while($result = $database->fetch_array($query)) {
			echo $result['firstname'].' '.$result['lastname'].' : ' . $result['count'] . '<br>';
			$count += $result['count'];
		}
	echo '<br>Total: ' . $count . '<br>';
	$query = $database->query("select order_id from " . TABLE_ORDERS . " where order_status_id = '1'");
		while($result = $database->fetch_array($query)) {
			$database->query("delete from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $result['order_id'] . "' limit 1");
		}*/
	//$query = $database->query("select order_id, address_id from orders where user_id = '944' and order_status_id = '3'");
		//while($result = $database->fetch_array($query)) {
		//	$database->query("update orders set user_id = '1013' where order_id = '" . $result['order_id'] . "' limit 1");
			//echo "update orders set user_id = '1013' where order_id = '" . $result['order_id'] . "' limit 1" . '<br>';
		//	$database->query("update addresses_to_users set user_id = '1013' where address_id = '" . $result['address_id'] . "' limit 1");
			//echo "update addresses set user_id = '1013' where address_id = '" . $result['address_id'] . "' limit 1" . '<br>';
	//	}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		$user_id = $user->fetch_user_id();
		
		
		//$account->set_debit_amount('60', 'Installation at test address', '77 test address', '1', '57');
		//$account->set_credit_amount('15', 'Equipment Cancelation', 'Canceled Brochire Box', '57');
		//$account->request_credit_refund('200');
		//$account->set_payment_received('65', '0', '60');
		$query = $database->query("select next_password_reminder, last_password_update from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
		$result = $database->fetch_array($query);
			if (($result['last_password_update']+(60*60*24*PASSWORD_REMINDER_DAYS)) < mktime()) {
				//Time to remind.
				?>
				<tr>
					<td width="100%" align="left">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td width="16" height="16"><img src="images/error.gif" height="16" width="16"></td>
								<td width="5"><img src="images/pixel_trans.gif" width="5" height="1"></td><td width="100%" align="left" height="16" valign="top" class="mainError">It's time to update your password.</td>
							</tr>
							<tr>
								<td colspan="2"></td>
							<?php
								if (empty($result['last_password_update'])) {
							?>
								<td class="main">You have never updated your password so its definately a good idea to do that now to keep your account secure.  Click the "Change Password" button below to do it now.</td>
							<?php
								} else {
									$day = date("n", $result['last_password_update']);
									$posttext = '';
										if (($day == 1) || ($day == 21) || ($day == 31)) {
											$posttext = 'st';
										} elseif (($day == 2) || ($day == 22)) {
											$posttext = 'nd';
										} elseif (($day == 3) || ($day == 23)) {
											$posttext = 'rd';
										} else {
											$posttext = 'th';
										}
							?>
								<td class="main">You last updated your password on the <?php echo $day.$posttext . ' of ' . date("F Y", $result['last_password_update']); ?> so we would recommend that you update it now to keep your account secure.  Click the "Change Password" button below to do it now.</td>
							<?php
								}
							?>
							</tr>
							<tr>
								<td colspan="2"></td>
								<td align="right"><a href="<?php echo FILENAME_ACCOUNT_CHANGE_PASSWORD; ?>"><?php echo tep_create_button_link('change_password', 'Change Password Now'); ?></a></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
				</tr>
				<?php
			}
			//Now the group specific items.
			$user_group_id = $user->fetch_user_group_id();
			
			switch($user_group_id) {
				case '1': 
					//Agent.
					$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user_id . "' and order_status_id = '1'");
					$result = $database->fetch_array($query);
					$pending_orders = $result['count'];
					
					$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user_id . "' and order_status_id = '2'");
					$result = $database->fetch_array($query);
					$schedualed_order = $result['count'];
					
					$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user_id . "' and order_type_id = '3' and order_status_id < '3'");
					$result = $database->fetch_array($query);
					$current_removals = $result['count'];
					?>
					<tr>
						<td align="left" class="main"><b>Your Account at a Glance</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="3" cellpadding="0">
								<tr>
									<td width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
									<td width="100%"></td>
								</tr>
								<tr>
									<td class="main" width="250">Pending Orders: </td>
									<td width="100%" align="left"><?php echo $pending_orders; ?></td>
								</tr>
								<tr>
									<td class="main" width="250">Scheduled Orders: </td>
									<td width="100%" align="left"><?php echo $schedualed_order; ?></td>
								</tr>
								<tr>
									<td class="main" width="250">Active Removals: </td>
									<td width="100%" align="left"><?php echo $current_removals; ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
				break;
				case '2': 
					//Admin.
					$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
					$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a where o.order_status_id = '1' and o.address_id = a.address_id and o.date_schedualed < '" . $midnight_future . "'");
					$result = $database->fetch_array($query);
					$current_pending_orders = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a where order_status_id = '2' and o.address_id = a.address_id and o.date_schedualed < '" . $midnight_future . "'");
					$result = $database->fetch_array($query);
					$current_schedualed_orders = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a where order_status_id < '3' and o.address_id = a.address_id and o.date_schedualed < '" . $midnight_future . "'");
					$result = $database->fetch_array($query);
					$current_orders = $result['count'];
					
					$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp())); 
					$midnight_future = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+2), date("Y", tep_fetch_current_timestamp())); 
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a where o.order_status_id = '1' and o.address_id = a.address_id and o.date_schedualed > '" . $midnight_tonight . "'");
					$result = $database->fetch_array($query);
					$future_pending_orders = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a where order_status_id = '2' and o.address_id = a.address_id and o.date_schedualed > '" . $midnight_tonight . "'");
					$result = $database->fetch_array($query);
					$future_schedualed_orders = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a where order_status_id < '3' and o.address_id = a.address_id and o.date_schedualed > '" . $midnight_tonight . "'");
					$result = $database->fetch_array($query);
					$future_orders = $result['count'];
					
					?>
					<tr>
						<td align="left" class="main"><b>Currently Active Orders</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="3" cellpadding="0">
								<tr>
									<td width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
									<td width="100%"></td>
								</tr>
								<tr>
									<td class="main" width="250">Current Pending Orders: </td>
									<td width="100%" align="left"><?php echo $current_pending_orders; ?></td>
								</tr>
								<tr>
									<td class="main" width="250">Current Scheduled Orders: </td>
									<td width="100%" align="left"><?php echo $current_schedualed_orders; ?></td>
								</tr>
								<tr>
									<td class="main" width="250">Current Total Orders: </td>
									<td width="100%" align="left"><?php echo $current_orders; ?></td>
								</tr>
								<tr>
									<td><br /></td>
								</tr>
								<tr>
									<td width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
									<td width="100%"></td>
								</tr>
								<tr>
									<td class="main" width="250">Future Pending Orders: </td>
									<td width="100%" align="left"><?php echo $future_pending_orders; ?></td>
								</tr>
								<tr>
									<td class="main" width="250">Future Scheduled Orders: </td>
									<td width="100%" align="left"><?php echo $future_schedualed_orders; ?></td>
								</tr>
								<tr>
									<td class="main" width="250">Future Total Orders: </td>
									<td width="100%" align="left"><?php echo $future_orders; ?></td>
								</tr>
								<tr>
									<td><br /></td>
								</tr>
								<tr>
									<td width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
									<td width="100%"></td>
								</tr>
								<tr>
									<td class="main" width="250"><strong>Unassigned Orders:</strong> </td>
									<td width="100%" align="left"><?php echo tep_count_unassigned_orders(); ?></td>
								</tr>
								<tr>
									<td><br /></td>
								</tr>
								<tr>
									<td width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
									<td width="100%"></td>
								</tr>
								<tr>
									<td class="main" width="250"><strong>Posts in Field/Total:</strong> </td>
									<td width="100%" align="left"><?php echo tep_count_posts_of_status('2').'/'.tep_count_posts_of_status(); ?></td>
								</tr>
								<tr>
									<td><br /></td>
								</tr>
								<tr>
									<td width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
									<td width="100%"></td>
								</tr>
								<?php
									$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where  o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_type_id = '1' and o.order_status_id > 0 and o.address_id = a.address_id");
									$result = $database->fetch_array($query);
								?>
								<tr>
									<td class="main" width="250"><strong>Total # of Installs:</strong> </td>
									<td width="100%" align="left"><?php echo $result['count']; ?></td>
								</tr>
								<?php
									$query = $database->query("select count(o.order_id) as count from " .  TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where  o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_type_id = '1' and o.order_status_id > 0 and o.address_id = a.address_id and o.order_type_id = '1' and (o.order_status_id = '3' or o.order_status_id = '0')");
									$result = $database->fetch_array($query);
								?>
								<tr>
									<td class="main" width="250"><strong>Total # of Completed Installs:</strong> </td>
									<td width="100%" align="left"><?php echo $result['count']; ?></td>
								</tr>
								<tr>
									<td><br /></td>
								</tr>
								<tr>
									<td width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
									<td width="100%"></td>
								</tr>
								<?php
									$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where  o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_type_id = '3' and o.order_status_id > 0 and o.address_id = a.address_id");
									$result = $database->fetch_array($query);
								?>
								<tr>
									<td class="main" width="250"><strong>Total # of Removals:</strong> </td>
									<td width="100%" align="left"><?php echo $result['count']; ?></td>
								</tr>
								<?php
									$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where  o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_type_id = '3' and o.order_status_id = '3' and o.order_status_id > 0 and o.address_id = a.address_id");
									$result = $database->fetch_array($query);
								?>
								<tr>
									<td class="main" width="250"><strong>Total # of Completed Removals:</strong> </td>
									<td width="100%" align="left"><?php echo $result['count']; ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<?
				break;
				case '3': 
					//Installer.
					$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
					$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
						
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '1' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$current_pend_installs = $result['count'];

					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '2' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$current_pend_service = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '3' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$current_pend_removal = $result['count'];
					
					$current_pend_total = ($current_pend_installs + $current_pend_service + $current_pend_removal);
					
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.order_type_id = '1' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$current_sched_installs = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.order_type_id = '2' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$current_sched_service = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.order_type_id = '3' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$current_sched_removal = $result['count'];
					
					$current_sched_total = ($current_sched_installs + $current_sched_service + $current_sched_removal);
					
					
					/*$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$current_sched_installs = $result['count'];
				
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id < '3' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$current_installs = $result['count'];*/
					
					
					$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp())); 
					$midnight_future = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+2), date("Y", tep_fetch_current_timestamp())); 

					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed > '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '1' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$future_pend_installs = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed > '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '2' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$future_pend_service = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed > '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '3' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$future_pend_removal = $result['count'];
					
					$future_pend_total = ($future_pend_installs + $future_pend_service + $future_pend_removal);
					
					/*$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed > '" . $midnight_tonight . "' and o.order_status_id = '1' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$future_pend_installs = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed > '" . $midnight_tonight . "' and o.order_status_id = '2' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$future_sched_installs = $result['count'];
					
					$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed > '" . $midnight_tonight . "' and o.order_status_id < '3' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
					$result = $database->fetch_array($query);
					$future_installs = $result['count'];*/
					?>
					<tr>
						<td align="left" class="main"><b>Assigned Jobs</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="3" cellpadding="0">
								<tr>
									<td width="100%" align="left">
										<table cellspacing="0" cellpadding="0">
											<tr>
												<td width="120"><img src="images/pixel_trans.gif" height="1" width="140" /></td>
												<td width="100%"></td>
											</tr>
											<tr>
												<td class="main" width="120"><u>Current Pending Orders: </u></td>
												<td width="100%" align="left"><?php echo $current_pend_total; ?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
								</tr>
								<tr>
									<td width="100%" align="left">
										<table  width="450" cellspacing="0" cellpadding="0">
											<tr>
												<td width="150" class="main">Installs: <?php echo $current_pend_installs; ?></td>
												<td width="150" class="main">Service Calls: <?php echo $current_pend_service; ?></td>
												<td width="150" class="main">Removals: <?php echo $current_pend_removal; ?></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="3" cellpadding="0">
								<tr>
									<td width="100%" align="left">
										<table cellspacing="0" cellpadding="0">
											<tr>
												<td width="120"><img src="images/pixel_trans.gif" height="1" width="140" /></td>
												<td width="100%"></td>
											</tr>
											<tr>
												<td class="main" width="120"><u>Current Scheduled Orders: </u></td>
												<td width="100%" align="left"><?php echo $current_sched_total; ?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
								</tr>
								<tr>
									<td width="100%" align="left">
										<table  width="450" cellspacing="0" cellpadding="0">
											<tr>
												<td width="150" class="main">Installs: <?php echo $current_sched_installs; ?></td>
												<td width="150" class="main">Service Calls: <?php echo $current_sched_service; ?></td>
												<td width="150" class="main">Removals: <?php echo $current_sched_removal; ?></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="3" cellpadding="0">
								<tr>
									<td width="100%" align="left">
										<table cellspacing="0" cellpadding="0">
											<tr>
												<td width="120"><img src="images/pixel_trans.gif" height="1" width="140" /></td>
												<td width="100%"></td>
											</tr>
											<tr>
												<td class="main" width="120"><u>Current Total Orders: </u></td>
												<td width="100%" align="left"><?php echo ($current_pend_total+$current_sched_total); ?></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="25"><img src="images/pixel_trans.gif" height="25" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="3" cellpadding="0">
								<tr>
									<td width="100%" align="left">
										<table cellspacing="0" cellpadding="0">
											<tr>
												<td width="120"><img src="images/pixel_trans.gif" height="1" width="140" /></td>
												<td width="100%"></td>
											</tr>
											<tr>
												<td class="main" width="120"><u>Future Pending Orders: </u></td>
												<td width="100%" align="left"><?php echo $future_pend_total; ?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
								</tr>
								<tr>
									<td width="100%" align="left">
										<table  width="450" cellspacing="0" cellpadding="0">
											<tr>
												<td width="150" class="main">Installs: <?php echo $future_pend_installs; ?></td>
												<td width="150" class="main">Service Calls: <?php echo $future_pend_service; ?></td>
												<td width="150" class="main">Removals: <?php echo $future_pend_removal; ?></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="3" cellpadding="0">
								<tr>
									<td width="100%" align="left">
										<table cellspacing="0" cellpadding="0">
											<tr>
												<td width="120"><img src="images/pixel_trans.gif" height="1" width="140" /></td>
												<td width="100%"></td>
											</tr>
											<tr>
												<td class="main" width="120"><u>Future Total Orders: </u></td>
												<td width="100%" align="left"><?php echo ($future_pend_total); ?></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<?
				break;
			}
	?>
</table>