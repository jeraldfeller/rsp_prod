<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get');
	
	$order_type = tep_fill_variable('order_type', 'get', '');

	$show_house_number = tep_fill_variable('show_house_number', 'get', '');
	$show_street_name = tep_fill_variable('show_street_name', 'get', '');
	$show_between_type = tep_fill_variable('show_between_type', 'get', 'added');
	$show_between_start = tep_fill_variable('show_between_start', 'get');
	$show_between_end = tep_fill_variable('show_between_end', 'get', date("n/d/Y", mktime()));
	$page = tep_fill_variable('page', 'get', '1');
	
		if (!empty($oID) && (tep_fetch_assigned_order_installer($oID) != $user->fetch_user_id())) {
			$oID = '';
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('installer_view_completed')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('installer_view_completed'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
		<?php
			if (empty($oID)) {
				$where = '';
				//Here we work out if it is today or tomorrow and change the where to match.
						$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp())); 
						$midnight_two_days_ago = ($midnight_tonight - (86400 * 2));
				//We only want the orders for the specifed day.
		?>			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Date Completed</td>
						<td class="pageBoxHeading">Type</td>
						<td class="pageBoxHeading">House #</td>
						<td class="pageBoxHeading">Street</td>
						<td class="pageBoxHeading">City</td>
						<td class="pageBoxHeading">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$extra = '';
					$where = '';
						if (!empty($order_type)) {
							$where .= " and o.order_type_id = '" . $order_type . "' ";
						}
						if (!empty($show_house_number)) {
							$where .= ' and ';
							$where .= " (a.house_number = '" . $show_house_number . "' or a.house_number like '" . $show_house_number . "%'  or a.house_number like '%" . $show_house_number . "')";
						}
						if (!empty($show_street_name)) {
							$where .= ' and ';
							$where .= " (a.street_name = '" . $show_street_name . "' or a.street_name like '" . $show_street_name . "%'  or a.street_name like '%" . $show_street_name . "')";
						}
						if (!empty($show_between_type)) {
							if (!empty($show_between_start)) {
								$start_timestamp = @strtotime($show_between_start);
							} else {
								$start_timestamp = 0;
							}
							if (!empty($show_between_end)) {
								$end_timestamp = @strtotime($show_between_end);
									if ($end_timestamp > 0) {
										$end_timestamp += ((60*60*24) - 1); //End as opposed to start of day.
									}
							} else {
								$end_timestamp = 0;
							}
							if ($show_between_type == 'accepted') {
								$where .= " and o.date_accepted > 0 ";
									if ($start_timestamp > 0) {
										$where .= " and o.date_accepted >= '" . $start_timestamp . "' ";
									}
									if ($end_timestamp > 0) {
										$where .= " and o.date_accepted <= '" . $end_timestamp . "' ";
									}
							}
							if ($show_between_type == 'ordered') {
								$where .= " and o.date_added > 0 ";
									if ($start_timestamp > 0) {
										$where .= " and o.date_added >= '" . $start_timestamp . "' ";
									}
									if ($end_timestamp > 0) {
										$where .= " and o.date_added <= '" . $end_timestamp . "' ";
									}
							}
							if ($show_between_type == 'scheduled') {
								$where .= " and o.date_schedualed > 0 ";
									if ($start_timestamp > 0) {
										$where .= " and o.date_schedualed >= '" . $start_timestamp . "' ";
									}
									if ($end_timestamp > 0) {
										$where .= " and o.date_schedualed <= '" . $end_timestamp . "' ";
									}
							}
							if ($show_between_type == 'completed') {
								$where .= " and o.date_completed > 0 ";
									if ($start_timestamp > 0) {
										$where .= " and o.date_completed >= '" . $start_timestamp . "' ";
									}
									if ($end_timestamp > 0) {
										$where .= " and o.date_completed <= '" . $end_timestamp . "' ";
									}
							}
						}
					//o.date_schedualed > '" . $midnight_two_days_ago . "' and o.date_schedualed < '" . $midnight_tonight . "' and
					$listing_split = new split_page("select o.order_id, o.date_completed, a.house_number, a.street_name, a.city, o.order_status_id, os.order_status_name, ot.name as order_type_name, a.zip4".$extra." from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_INSTALLERS_TO_ORDERS . " ito, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_tonight . "' and o.order_issue != '1' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and o.order_id = ito.order_id " . $where . " and ito.installer_id =  '" . $user->fetch_user_id() . "' and o.order_status_id > '2' group by o.order_id order by o.date_completed DESC", '20', 'o.order_id');
						if ($listing_split->number_of_rows > 0) {
							$query = $database->query($listing_split->sql_query);
							    foreach($database->fetch_array($query) as $result){
				?>
					<tr>
						<td class="pageBoxContent"><?php echo date("n/d/Y", $result['date_completed']); ?></td>
						<td class="pageBoxContent"><?php echo $result['order_type_name']; ?></td>
						<td class="pageBoxContent"><?php echo $result['house_number']; ?></td>
						<td class="pageBoxContent"><?php echo $result['street_name']; ?></td>
						<td class="pageBoxContent"><?php echo $result['city']; ?></td>
						<td class="pageBoxContent"><a href="<?php echo FILENAME_INSTALLER_VIEW_COMPLETED; ?>?oID=<?php echo $result['order_id']; ?>&show_house_number=<?php echo $show_house_number; ?>&show_street_name=<?php echo $show_street_name; ?>&show_between_type=<?php echo $show_between_type; ?>&show_between_start=<?php echo urlencode($show_between_start); ?>&show_between_end=<?php echo urlencode($show_between_end); ?>&page=<?php echo $page; ?>">View</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
								}
								?>
						<tr>
							<td colspan="5">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> completed orders)'); ?></td>
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
					//Show the order.
					$order = new orders('fetch', $oID);
					$order_data = $order->return_result();
					
					$user_query = $database->query("select u.agent_id, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $order_data['user_id'] . "' and u.user_id = ud.user_id limit 1");
					$user_result = $database->fetch_array($user_query);
					?>
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Order Id: <?php echo $oID; ?></b></td>
						</tr>
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Order Status: <?php echo tep_get_order_status_name($order_data['order_status_id']); ?></b></td>
						</tr>
						
						<tr>
							<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
						</tr>
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Agent Information</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Agent Name: </td><td class="pageBoxContent"><?php echo $user_result['firstname']; ?> <?php echo $user_result['lastname']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Agency Name: </td><td class="pageBoxContent"><?php echo ((!empty($user_result['name'])) ? $user_result['name'] : 'None'); ?></td>
						</tr>
						<tr>
							<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
						</tr>
						
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Address Information</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Address: </td><td class="pageBoxContent"><?php echo $order_data['house_number']; ?>, <?php echo $order_data['street_name']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">City: </td><td class="pageBoxContent"><?php echo $order_data['city']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">County: </td><td class="pageBoxContent"><?php echo $order_data['county_name']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">State: </td><td class="pageBoxContent"><?php echo $order_data['state_name']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Zip: </td><td class="pageBoxContent"><?php echo ((!empty($order_data['zip'])) ? $order_data['zip'] : ''); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Zip4: </td><td class="pageBoxContent"><?php echo $order_data['zip4']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Cross Street/Directions: </td>
							<td class="pageBoxContent"><?php echo $order_data['cross_street_directions']; ?></td>
						</tr>
						<tr>
							<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
						</tr>
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Job Description</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Order Type: </td><td class="pageBoxContent"><?php echo $order_data['order_type_name']; ?></td>
						</tr>
						<?php
							$installer_id = tep_fetch_assigned_order_installer($oID);
								if ($installer_id !== false) {
						?>
						<tr>
							<td class="pageBoxContent">Assigned Installer: </td><td class="pageBoxContent"><?php echo tep_fetch_installer_name($installer_id); ?></td>
						</tr>
						<?php
								}
						?>
						<tr>
							<td class="pageBoxContent">Date Added: </td><td class="pageBoxContent"><?php echo date("n/d/Y", $order_data['date_added']); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Last Modified: </td><td class="pageBoxContent"><?php echo (($order_data['last_modified'] > 0) ? date("n/d/Y", $order_data['last_modified']) : 'Never'); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Date Scheduled: </td><td class="pageBoxContent"><?php echo (($order_data['date_schedualed'] > 0) ? date("n/d/Y", $order_data['date_schedualed']) : 'Never'); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Date Completed: </td><td class="pageBoxContent"><?php echo (($order_data['date_completed'] > 0) ? date("n/d/Y", $order_data['date_completed']) : 'Never'); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Number of Posts: </td><td class="pageBoxContent"><?php echo $order_data['number_of_posts']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Special Instructions: </td>
							<td class="pageBoxContent"><?php echo $order_data['special_instructions']; ?></td>
						</tr>
						<tr>
							<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
						</tr>


					<tr>
						<td colspan="2" class="pageBoxContent"><b>Equipment</b></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%" colspan="2"><?php echo tep_create_view_equipment_string($order_data['optional']); ?></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Reason and Details</b></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<?php
						if (isset($order_data['service_call_reason_id'])) {
							?>
							<tr>
								<td width="100%" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="main"><b>Reason: </b></td>
										</tr>
										<tr>
											<td class="main"><?php
												if ($order_data['service_call_reason_id'] == '1') {
													echo 'Exchange Rider';
														for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
															echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($order_data['equipment'][$n]['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$order_data['equipment'][$n]['name'];
														}
												} elseif ($order_data['service_call_reason_id'] == '2') {
													echo 'Install New Rider or BBox';
													
														for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
															echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
														}
												} elseif ($order_data['service_call_reason_id'] == '3') {
													echo 'Replace/Exchange Agent SignPanel';
														for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
															echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
														}
												} elseif ($order_data['service_call_reason_id'] == '4') {
													echo 'Post Leaning/Straighten Post';
														if ($order_data['service_call_detail_id'] == '1') {
															echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weather';
														} elseif ($order_data['service_call_detail_id'] == '2') {
															echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Improper Installation';
														} elseif ($order_data['service_call_detail_id'] == '3') {
															echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Someone moved Post';
														} elseif ($order_data['service_call_detail_id'] == '4') {
															echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other';
														}
												} elseif ($order_data['service_call_reason_id'] == '5') {
													echo 'Move Post';
														//Check if any posts were marked as lost and jot themdown.
														for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
															echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'] . ' was missing and was replaced.';
														}
												} elseif ($order_data['service_call_reason_id'] == '6') {
													echo 'Install equipment forgotten at install';
														for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
															echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
														}
												} elseif ($order_data['service_call_reason_id'] == '7') {
													echo 'Other';
												}
											?></td>
										</tr>
									</table>
								</td>
							</tr>
		
								<tr>
									<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
								</tr>
								<?php
							}
						?>
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Agent Preferences</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td width="100%" colspan="2"><?php echo ((tep_agent_has_preferences($order_data['user_id'], $order_data['order_type_id'])) ? tep_create_agent_preferences_string($order_data['user_id'], $order_data['order_type_id']) : 'Agent has no personal preferences.'); ?></td>
						</tr>
						<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Comments</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" valign="top">Admin Comments: </td>
						<td class="pageBoxContent"><?php echo $order_data['admin_comments']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" valign="top">Installer Comments: </td>
						<td class="pageBoxContent"><?php echo $order_data['address_comments']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" valign="top">Installer Comments for Agent: </td>
						<td class="pageBoxContent"><?php echo $order_data['installer_comments']; ?></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Order History</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<?php
							$status_history = tep_fetch_order_history($oID);
							$status_history_count = count($status_history);
							$n = 0;
								while($n < $status_history_count) {
						?>
						<tr>
							<td class="pageBoxContent" colspan="2" NOWRAP>Date: <?php echo date("n/d/Y", $status_history[$n]['date_added']); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent" colspan="2" NOWRAP>Status: <?php echo $status_history[$n]['order_status_name']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent" colspan="2" NOWRAP>User Notified: <?php echo (($status_history[$n]['user_notified'] == '1') ? 'Yes' : 'No'); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent" colspan="2">Comments: </td>
						</tr>
						<tr>
							<td class="pageBoxContent" colspan="2"><?php echo $status_history[$n]['comments']; ?></td>
						</tr>
						<tr>
							<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
						</tr>
						<?php
									$n++;
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
				if (empty($oID)) {
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Click on an order to either edit or view.  To create a new order press the "Create" button below.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_INSTALLER_VIEW_COMPLETED; ?>" method="get">
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="2" cellpadding="2">
										<tr>
											<td class="main">Show only Type:</td><td class="main"><?php echo tep_draw_order_type_pulldown('order_type', $order_type, '', array(array('id' => '', 'name' => 'Any'))); ?></td>
										</tr>
										<tr>
											<td class="main">Show House Number: </td>
											<td class="main"><input type="text" name="show_house_number" value="<?php echo $show_house_number; ?>" /></td>
										</tr>
										<tr>
											<td class="main">Show Street Name: </td>
											<td class="main"><input type="text" name="show_street_name" value="<?php echo $show_street_name; ?>" /></td>
										</tr>
										<tr>
											<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
										</tr>
										<tr>
											<td class="main">Show When: </td>
											<td class="main"><select name="show_between_type"><option value=""<?php echo (($show_between_type == '') ? ' SELECTED' : ''); ?>>Any</option><option value="ordered"<?php echo (($show_between_type == 'ordered') ? ' SELECTED' : ''); ?>>Ordered</option><option value="scheduled"<?php echo (($show_between_type == 'scheduled') ? ' SELECTED' : ''); ?>>Scheduled</option><option value="accepted"<?php echo (($show_between_type == 'accepted') ? ' SELECTED' : ''); ?>>Accepted</option><option value="completed"<?php echo (($show_between_type == 'completed') ? ' SELECTED' : ''); ?>>Completed</option></select></td>
										</tr>
										<tr>
											<td class="main" colspan="2">Between: <input type="text" name="show_between_start" value="<?php echo $show_between_start; ?>" size="7" /> and <input type="text" name="show_between_end" value="<?php echo $show_between_end; ?>" size="7" /></td>
										</tr>
										<tr>
											<td colspan="2"><i>(mm/dd/YY)</i></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?></td>
							</tr>
							</form>
						</table>
					</td>
				</tr>
			</table>
			<?php
				} else {
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press "back" to go back top the previous page.</td>
							</tr>
							<tr>
								<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
							</tr>
							<tr>
								<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_INSTALLER_VIEW_COMPLETED; ?>?show_house_number=<?php echo $show_house_number; ?>&show_street_name=<?php echo $show_street_name; ?>&show_between_type=<?php echo $show_between_type; ?>&show_between_start=<?php echo urlencode($show_between_start); ?>&show_between_end=<?php echo urlencode($show_between_end); ?>&page=<?php echo $page; ?>"><?php echo tep_create_button_link('back', 'Back'); ?></a></td>
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