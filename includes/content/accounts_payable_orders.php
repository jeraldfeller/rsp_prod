<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get');
		
	$order_view = tep_fill_variable('order_view', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '');
	$order_type = tep_fill_variable('order_type', 'get', '');
	$agent_id = tep_fill_variable('agent_id', 'get', '');
	$show_house_number = tep_fill_variable('show_house_number', 'get', '');
	$show_street_name = tep_fill_variable('show_street_name', 'get', '');
	$show_city = tep_fill_variable('show_city', 'get', '');
	
		if (!empty($oID)) {
			$query = $database->query("select o.order_id from " . TABLE_ORDERS . " o, " . TABLE_USERS . " u where o.order_id = '" . $oID . "' and o.user_id = u.user_id and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()). "' limit 1");
			$result = $database->fetch_array($query);
				if (empty($result['order_id'])) {
					$oID = '';
					$page_action = '';
				}
		}
	
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('order_view')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('order_view'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if (empty($oID)) {
					$where = '';
						//$where .= " and o.order_status_id = '3'";
						if(empty($order_status)){
							$where .= "";
						}elseif (!empty($order_status)) {
							$where .= " and o.order_status_id = '" . $order_status . "'";
						}
						if (!empty($order_type)) {
							$where .= " and o.order_type_id = '" . $order_type . "'";
						}
//print "select o.order_id, o.order_total, ot.name as order_type_name, os.order_status_name, a.house_number, a.street_name, a.city from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a where o.user_id = '" . $user->fetch_user_id() . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id " . $where . " order by o.date_schedualed DESC, '20', 'o.order_id'";//exit;
			
$listing_split = new split_page("select o.order_id, o.order_total, ot.name as order_type_name, os.order_status_name, o.order_status_id, a.house_number, a.street_name, a.city, ud.firstname, ud.lastname from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where o.user_id = u.user_id and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()) . "'" . ((!empty($agent_id)) ? " and u.user_id = '" . $agent_id . "'" : '') . " " . (!empty($show_house_number) ? " and a.house_number = '" . $show_house_number . "'" : '') . " " . (!empty($show_street_name) ? " and (a.street_name = '" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "%' or a.street_name like '" . $show_street_name . "%')" : '') . " " . (!empty($show_city) ? " and (a.city = '" . $show_city . "' or a.city like '%" . $show_city . "' or a.city like '%" . $show_city . "%' or a.city like '" . $show_city . "%')" : '') . " and u.user_id = ud.user_id and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id " . $where . " order by o.date_schedualed DESC", '20', 'o.order_id');
						if ($listing_split->number_of_rows > 0) {
				?>
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr><td class="pageBoxHeading" align="center" width="15%">Order Type</td>
							<td class="pageBoxHeading" align="center" width="40%">Address</td>
							<td class="pageBoxHeading" align="center" width="15%">Agent</td>
							<td class="pageBoxHeading" align="center" width="15%">Order Status</td>
							<td class="pageBoxHeading" align="right" width="15%">Action</td>
							<td width="10" class="pageBoxHeading">&nbsp;</td>
						</tr>
					<?php
						$query = $database->query($listing_split->sql_query);
							while($result = $database->fetch_array($query)) {
								
					?>
						<tr><td class="pageBoxContent" align="left"><?php echo $result['order_type_name']; ?></td>
							<td class="pageBoxContent" align="left"><?php echo $result['house_number']; ?> <?php echo $result['street_name']; ?>, <?php echo $result['city']; ?></td>
							<td class="pageBoxContent" align="left"><?php echo $result['firstname'].' '.$result['lastname']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $result['order_status_name']; ?></td>
							<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ACCOUNTS_PAYABLE_ORDERS . '?oID='.$result['order_id'].'&page_action=view'; ?>">View</a></td>
							<td width="10" class="pageBoxContent"></td>
						</tr>
					<?php
							}
							?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> orders)'); ?></td>
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
								<td class="pageBoxContent" valign="top">Sorry no order could be found.  Please change the options on the right.</td>
							</tr>
						</table>
						<?php
						}
					?>
				</table>
			<?php
				} else {
					
					$order = new orders('fetch', $oID);
					
					$order_data = $order->return_result();

					//tep_create_orders_history($oID, '1', 'Thank you for your order.  It has now been received and is awaiting acceptance.');
						$user_query = $database->query("select u.agent_id, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $order_data['user_id'] . "' and u.user_id = ud.user_id limit 1");
						$user_result = $database->fetch_array($user_query);
			?>
			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<table cellspacing="0" cellpadding="2">
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
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Address Information</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Address</td><td class="pageBoxContent"><?php echo $order_data['house_number']; ?>, <?php echo $order_data['street_name']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">City</td><td class="pageBoxContent"><?php echo $order_data['city']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">County</td><td class="pageBoxContent"><?php echo $order_data['county_name']; ?></td>
					</tr>
					<?php
						/*
					<tr>
						<td class="pageBoxContent">State</td><td class="pageBoxContent"><?php echo $order_data['state_name']; ?></td>
					</tr>
						*/
					?>
					<tr>
						<td class="pageBoxContent">Zip</td><td class="pageBoxContent"><?php echo $order_data['zip']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Number of Posts</td><td class="pageBoxContent"><?php echo $order_data['number_of_posts']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Cross Street/Directions</td>
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
						<td class="main">Job Status: </td><td class="pageBoxContent"><b><?php echo $order_data['order_status_name']; ?></b></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Order Type: </td><td class="pageBoxContent"><?php echo $order_data['order_type_name']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Job Total: </td><td class="pageBoxContent">$<?php echo number_format($order_data['order_total'], 2); ?></td>
					</tr>
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
						<td class="pageBoxContent">Special Instructions: </td>
						<td class="pageBoxContent"><?php echo $order_data['special_instructions']; ?></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<?php
						if (($order_data['order_type_id'] == '1') || (($order_data['order_type_id'] == '3') && ($order_data['order_status_id'] == '3'))) {
					?>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Equipment</b></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%" colspan="2"><?php echo tep_create_view_equipment_string($order_data['optional']); ?></td>
					</tr>
					<?php
						} elseif ($order_data['order_type_id'] == '2') {
					?>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Reason and Details</b></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
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
					<?php
						}
					?>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Order Cost</b></td>
					</tr>
					<?php
						if ($order_data['base_cost'] != $order_data['order_total']) {
					?>
					<tr>
						<td class="pageBoxContent">Base Cost: </td><td class="pageBoxContent">$<?php echo number_format($order_data['base_cost'], 2); ?></td>
					</tr>
						<?php
							if ($order_data['extended_cost'] > 0) {
						?>
							<tr>
								<td class="pageBoxContent">Extended Cost: </td><td class="pageBoxContent">$<?php echo number_format($order_data['extended_cost'], 2); ?></td>
							</tr>
						<?php
							}
						?>
						<?php
							if ($order_data['equipment_cost'] > 0) {
						?>
							<tr>
								<td class="pageBoxContent">Equipment Cost: </td><td class="pageBoxContent">$<?php echo number_format($order_data['equipment_cost'], 2); ?></td>
							</tr>
						<?php
							}
						?>
						<?php
							if ($order_data['extra_cost'] > 0) {
						?>
							<tr>
								<td class="pageBoxContent">Extra Cost: </td><td class="pageBoxContent">$<?php echo number_format($order_data['extra_cost'], 2); ?> <em>(<?php echo $order_data['extra_cost_description']; ?>)</em></td>
							</tr>
						<?php
							}
						?>
						<?php
							if ($order_data['discount_cost'] > 0) {
						?>
							<tr>
								<td class="pageBoxContent">Discount Cost: </td><td class="pageBoxContent">$<?php echo number_format($order_data['discount_cost'], 2); ?></td>
							</tr>
						<?php
							}
						?>
					<?php
						}
					?>
					<tr>
						<td class="pageBoxContent"><b>Order Total</b></td><td class="pageBoxContent"><b>$<?php echo number_format($order_data['order_total'], 2); ?></b></td>
					</tr>
					
					
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Installer Comments</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2"><?php echo $order_data['installer_comments']; ?></td>
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
						<td class="pageBoxContent">Comments: </td>
						<td class="pageBoxContent"><?php echo $status_history[$n]['comments']; ?></td>
					</tr>
					<tr>
						<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
					</tr>
					<?php
								$n++;
							}
					?>	
					</table>
						</td>
					</tr>				
				</table>
			<?php
					}

			?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
		<?php
			if (!empty($oID)) {
		?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press cancel to go back to the previous page<?php echo (($order_data['order_status_id'] == '1') ? ' or use the button below to edit the order': ''); ?>.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left" valign="top"><form action="<?php echo FILENAME_ACCOUNTS_PAYABLE_ORDERS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
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
							<form action="<?php echo FILENAME_ACCOUNTS_PAYABLE_ORDERS; ?>" method="get">
							<tr>
								<td class="pageBoxContent">Click on an order to get more details on it or edit it of use the PullDown menu below to specify what orders you wish to view.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" class="pageBoxContent">Show only Agent <?php echo tep_draw_aom_agent_pulldown('agent_id', $agent_id, $user->fetch_user_id(), ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any'))); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" class="pageBoxContent">Show only orders of type <?php echo tep_draw_order_type_pulldown('order_type', $order_type, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'All Orders'))); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" class="pageBoxContent">Show only orders with status <?php echo tep_draw_orders_status_pulldown('order_status', $order_status, array(array('id' => '', 'name' => 'All')), ' onchange="this.form.submit();"'); ?></td>
							</tr>
							
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="left">
									<table width="100%" cellspacing="0" cellpadding="0">
										
										<tr>
											<td class="main">Show House Number: </td>
											<td class="main"><input type="text" name="show_house_number" value="<?php echo $show_house_number; ?>" /></td>
										</tr>
										<tr>
											<td class="main">Show Street Name: </td>
											<td class="main"><input type="text" name="show_street_name" value="<?php echo $show_street_name; ?>" /></td>
										</tr>
										<tr>
											<td class="main">Show City: </td>
											<td class="main"><input type="text" name="show_city" value="<?php echo $show_city; ?>" /></td>
										</tr>
										
									</table>
								</td>
							</tr>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td align="right"><?php echo tep_create_button_submit('update', 'Update'); ?></td>
							</tr>
							</form>
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