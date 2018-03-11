<?php

	$page_action = tep_fill_variable('page_action', 'get');
	$aID = tep_fill_variable('aID', 'get');
	$order_view = tep_fill_variable('order_view', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '');
	$order_type = tep_fill_variable('order_type', 'get', '');
	$job_start_date = tep_fill_variable('job_start_date', 'post', '');
//$error->add_error('agent_active_addresses', 'Please enter the Number of Posts.');
//if (!$error->get_error_status('agent_active_addresses')) {

//}
		if ($page_action == 'reschedule_removal_success') {
				
				if (empty($job_start_date)) {
					$error->add_error('agent_active_addresses', 'Please enter a date in the proper format.');
				} else {
					$job_start_date=$_POST['job_start_date'];
					
					$schedualed_start = strtotime($job_start_date);
						if ($schedualed_start < mktime()) {
							$error->add_error('agent_active_addresses', 'The new date must be in the future.');
						} elseif ($schedualed_start < tep_fetch_install_date($aID)) {
							$error->add_error('agent_active_addresses', 'The new date must be greater than the install date.');
						}
				}
				if ($error->get_error_status('agent_active_addresses')) {
					$page_action = 'reschedule_removal';
				} else {
					//Check if this day is a weekend and if so change it to the next monday.
					$dow = date("w", $schedualed_start);
					$move = 0;
						if ($dow == 0) {
							$move = 1;
						} elseif ($dow > 5) {
							$move = (8 - $dow);
						}
					$schedualed_start += ($move * 86400);
					
					//We also need to check if this is beyond the 240 day window.
					$query = $database->query("select date_completed from " . TABLE_ORDERS . "  where address_id = '" . $aID . "' and order_type_id = '1' limit 1");
					$result = $database->fetch_array($query);
					
					
					
						if (($schedualed_start - $result['date_completed']) > (MAX_FREE_REMOVAL_TIME * 60 * 60 * 24)) {
							//Extended!!!
							//Add an entry to the extended retals and flag the address, no bill is needed as the monthly cron does this.
							$database->query("update " . TABLE_ADDRESSES . " set extended_removal = '1' where address_id = '" . $aID . "' limit 1");
							
							$check_entry_query = $database->query("select extended_removal_address_id from " . TABLE_EXTENDED_REMOVAL_ADDRESSES . " where address_id = '" . $aID . "' limit 1");
							$check_entry_result = $database->fetch_array($check_entry_query);
							
								if (empty($check_entry_result['extended_removal_address_id'])) {
									$extended_date = ($result['date_completed'] + (MAX_FREE_REMOVAL_TIME * 60 * 60 * 24));
									$database->query("insert into " . TABLE_EXTENDED_REMOVAL_ADDRESSES . " (address_id, user_id, date_added, date_extended, day_extended, month_extended, year_extended) values ('" . $aID . "', '" . $user->fetch_user_id() . "', '" . mktime() . "', '" . $extended_date . "', '" . date("d", $extended_date) . "', '" . date("n", $extended_date) . "', '" . date("Y", $extended_date) . "')");
								}
						}
						
                    //If the order dosnt exist then create it.
					$check_removal_query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '" . $aID . "' and order_type_id = '3'");
					$check_removal_result = $database->fetch_array($check_removal_query);
						if (!empty($check_removal_result['order_id'])) {
                            $last_modified_by = tep_fill_variable('user_id', 'session', 0);
							$database->query("update " . TABLE_ORDERS . " set date_schedualed = '" . $schedualed_start . "', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where address_id = '" . $aID . "' and order_type_id = '3' limit 1");
						} else {
							$query = $database->query("select billing_method_id from " . TABLE_ORDERS . "  where address_id = '" . $aID . "' and order_type_id = '1' limit 1");
							$result = $database->fetch_array($query);
							
							$data = array('address_id' => $aID,
												  'order_type_id' => '3',
												  'schedualed_start' => $schedualed_start,
												  'county' => tep_fetch_address_county_id($aID),
												  'promo_code' => '',
												  'billing_method_id' => $result['billing_method_id']);
								
							$order = new orders('insert', '', $data, '', false, '1');
						}
					$page_action = '';
					tep_redirect(FILENAME_AGENT_ACTIVE_ADDRESSES.'?aID='.$aID.'&page_action=view');
					die();
				}
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('agent_active_addresses')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('agent_active_addresses'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if (($page_action != 'view_equipment') && ($page_action != 'view_history')) {
					$listing_split = new split_page("select a.address_id, a.house_number, a.street_name, a.city, a.zip, a.status, s.name as state_name, c.name as county_name, a.zip4, a.status from " . TABLE_ADDRESSES . " a, " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c left join " . TABLE_ORDERS . " o on (a.address_id = o.address_id and o.order_type_id = '3' and o.order_status_id != '3') where atu.user_id = '" . $user->fetch_user_id() . "' and atu.address_id = a.address_id and a.state_id = s.state_id and a.county_id = c.county_id and (o.order_status_id != '3' or (o.order_id is NULL and a.status < '3')) order by a.address_id DESC", '20', 'a.address_id');
						if ($listing_split->number_of_rows > 0) {
				?>
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td width="10" class="pageBoxHeading">&nbsp;</td>
							<td class="pageBoxHeading" align="center">Address</td>
							<td class="pageBoxHeading" align="center">Install Date</td>
							<td class="pageBoxHeading" align="center">Removal Date</td>
							<td class="pageBoxHeading" align="center">Current Status</td>
							<td class="pageBoxHeading" align="center">Service Calls</td>
							<td class="pageBoxHeading" align="right">Action</td>
							<td width="10" class="pageBoxHeading">&nbsp;</td>
						</tr>
					<?php
						$query = $database->query($listing_split->sql_query);
							while($result = $database->fetch_array($query)) {
								
								$install_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '1' limit 1");
								$install_date = $database->fetch_array($install_date_query);
								$removal_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '3' limit 1");
								$removal_date = $database->fetch_array($removal_date_query);
								$service_count_query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '2'");
								$service_count = $database->fetch_array($service_count_query);
								$status = 'Unknown';
									if ($install_date['order_status_id'] == '3') {
										$status = 'Installed';
									} elseif ($install_date['order_status_id'] == '1') {
										$status = 'Pending Installation';
									} elseif ($install_date['order_status_id'] == '2') {
										$status = 'Installation Scheduled';
									}
									if ($removal_date['order_status_id'] == '3') {
										$status = 'Removed';
									}
									
					?>
						<tr>
							<td width="10" class="pageBoxContent"></td>
							<td class="pageBoxContent" align="left" valign="top"><?php echo $result['house_number'].'<br>'.$result['street_name'].'<br>'.$result['city'].'<br>'.$result['state_name']; ?></td>
							<td class="pageBoxContent" align="center" valign="top"><?php echo ((($install_date['date_schedualed'] != NULL) && ($install_date['date_schedualed'] > 0)) ? date("n/d/Y", $install_date['date_schedualed']) : 'Never'); ?></td>
							<td class="pageBoxContent" align="center" valign="top"><?php echo (($removal_date['date_schedualed'] > 0) ? date("n/d/Y", $removal_date['date_schedualed']) : 'None Scheduled'); ?></td>
							<td class="pageBoxContent" align="center" valign="top"><?php echo $status; ?></td>
							<td class="pageBoxContent" align="center" valign="top"><?php echo $service_count['count']; ?></td>
							<td class="pageBoxContent" align="right" valign="top"><a href="<?php echo FILENAME_AGENT_ACTIVE_ADDRESSES . '?aID='.$result['address_id'].'&page_action=view'; ?>">Schedule</a> | <a href="<?php echo FILENAME_AGENT_ACTIVE_ADDRESSES . '?aID='.$result['address_id'].'&page_action=view_history'; ?>">History</a></td>
							<td width="10" class="pageBoxContent"></td>
						</tr>
					<?php
							}
							?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> addresses)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(10, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php
						} else {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxContent" valign="top">Sorry no order could be found.  Please either schedual a new one or change the options on the right.</td>
							</tr>
						</table>
						<?php
						}
					?>
				</table>
			<?php
				} elseif ($page_action == 'view_equipment') {
					//View equipment.
				} elseif ($page_action == 'view_history') {
					$address_information = tep_fetch_address_information($aID);
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td width="100%" align="left">
							<table cellspacing="0" cellpadding="0">
								<tr>
									<td class="pageBoxContent" align="left" valign="top"><b>Viewing Order History for: </b></td>
									<td class="pageBoxContent" valign="top"><?php echo $address_information['house_number'].' ' . $address_information['street_name'].'<br>'.$address_information['city'].'<br>'.$address_information['county_name'].'<br>'.$address_information['zip'].'<br>'.$address_information['state_name']; ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
					</tr>
					<?php
						//Now list the orders.
						$query = $database->query("select o.order_id, o.date_added, o.date_schedualed, o.date_completed, o.order_issue, o.order_status_id, os.order_status_name, ot.name as order_type_name from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.address_id = '" . $aID . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id order by date_schedualed");
							while($result = $database->fetch_array($query)) {
					?>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td class="pageBoxContent"><b>Type: <?php echo $result['order_type_name']; ?></b> <a href="<?php echo FILENAME_ORDER_VIEW; ?>?oID=<?php echo $result['order_id']; ?>&page_action=view">[full details]</a></td>
								</tr>
								<tr>
									<td class="pageBoxContent">Status: <?php echo $result['order_status_name']; ?></td>
								</tr>
								<?php
									if ($result['order_status_id'] == '3') {
								?>
								<tr>
									<td class="pageBoxContent">Date Completed: <?php echo date("n/d/Y", $result['date_completed']); ?></td>
								</tr>
								<?php
										if ($result['order_issue'] == '1') {
										?>
										<tr>
											<td  class="pageBoxContent">There were issues associated with this order.  Please click on "full details" above for more information.</td>
										</tr>
										<?php
										}
									} elseif ($result['order_status_id'] != '4') {
										
								?>
								<tr>
									<td class="pageBoxContent">Date Scheduled: <?php echo date("n/d/Y", $result['date_schedualed']); ?></td>
								</tr>
								<?php
									}
								?>
							</table>
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
			if (!empty($aID)) {
				if ($page_action == 'view') {
		?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press cancel to go back to the previous page or use the options below that relate to this order.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent" align="center"><a href="<?php echo FILENAME_ORDER_CREATE; ?>?address_id=<?php echo $aID; ?>&order_type=2">Schedule a new Service Call</a></td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent" align="center"><a href="<?php echo FILENAME_AGENT_ACTIVE_ADDRESSES; ?>?aID=<?php echo $aID; ?>&page_action=reschedule_removal">Re-scheduale Removal Date</a></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_AGENT_ACTIVE_ADDRESSES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
				} elseif ($page_action == 'view_equipment') {
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press cancel to go back to the previous page.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_AGENT_ACTIVE_ADDRESSES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
				} elseif ($page_action == 'view_history') {
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press cancel to go back to the previous page.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_AGENT_ACTIVE_ADDRESSES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
				} elseif ($page_action == 'reschedule_removal') {
					$query = $database->query("select date_schedualed from " . TABLE_ORDERS . " where address_id = '" . $aID . "' and order_type_id = '3' limit 1");
					$result = $database->fetch_array($query);
						if (empty($result['date_schedualed'])) {
							$result['date_schedualed'] = mktime();
						}
					$dt=$result['date_schedualed'];
					$myDate =date('d-M-Y',$dt);
//print "mydate is:$myDate ";
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<form action="<?php echo FILENAME_AGENT_ACTIVE_ADDRESSES; ?>?page_action=reschedule_removal_success&aID=<?php echo $aID; ?>" method="post">
							<tr>
								<td class="pageBoxContent">Press cancel to go back to the or fill in the details below to reschedule the removal.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								
							?>
							<tr>
								<td class="main">Removal Date: <? echo("<script>DateInput('job_start_date', true, 'DD-MON-YYYY','$myDate');</script>")?><noscript><input type="text" name="job_start_date" value="<?php echo date("n/d/Y", $result['date_schedualed']); ?>" /></noscript></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_AGENT_ACTIVE_ADDRESSES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
				}
			} else {
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Click on SCHEDULE to change the post removal date or to schedule a service call.  Click on HISTORY to get order details or to Change  an install or service call date.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
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
