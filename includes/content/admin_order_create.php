<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get');
	$order_view = tep_fill_variable('order_view', 'get', 'open');
 	$order_edit = tep_fill_variable('order_edit', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '');
	$order_type = tep_fill_variable('order_type', 'get', '');

	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
		if ($page_action == 'edit_confirm') {
			//Editing the order, load the variables.
			$house_number = tep_fill_variable('house_number');
			$street_name = tep_fill_variable('street_name');
			$city = tep_fill_variable('city');
			$state_id = tep_fill_variable('state_id');
			$county_id = tep_fill_variable('county_id');
			$zip = tep_fill_variable('zip');
			$cross_street_directions = tep_fill_variable('cross_street_directions');
			$order_total = tep_fill_variable('order_total');
			$number_of_posts = tep_fill_variable('number_of_posts');
			$special_instructions = tep_fill_variable('special_instructions');
			
			$admin_comments = tep_fill_variable('admin_comments');
			$installer_comments = tep_fill_variable('installer_comments');
			
			$new_order_status_id = tep_fill_variable('new_order_status_id');
			$new_user_notified = tep_fill_variable('new_user_notified');
			$new_comment = tep_fill_variable('new_comment');
			
			//Now work out new zip4 code.
				$zip4_class=new zip4($house_number.' '.$street_name,tep_get_state_name($state_id), $city, $zip);
					if ($zip4_class->search()) {
						$zip4_code = $zip4_class->return_zip_code();
					} else {
//						$error->add_error('admin_orders', 'Either the address is invalid or there is a problem with the system.  The zip 4 address was not able to be fetched.');
					}
					
					if (!$error->get_error_status('admin_orders')) {
						//Update the order.
						$data = array('house_number' => $house_number,
												'street_name' => $street_name,
												'city' => $city,
												'state_id' => $state_id,
												'county_id' => $county_id,
												'zip' => $zip,
												'zip4' => $zip4_code,
												'cross_street_directions' => $cross_street_directions,
												'order_total' => $order_total,
												'number_of_posts' => $number_of_posts,
												'special_instructions' => $special_instructions,
												'admin_comments' => $admin_comments,
												'installer_comments' => $installer_comments);
												
						
						$order = new orders('update', $oID, $data);
							if (!empty($new_comment) || ($new_order_status_id != tep_fetch_orders_status_id($oID))) {
								tep_create_orders_history($oID, $new_order_status_id, $new_comment, $new_user_notified);
							}
						$page_action = '';
						$oID = '';
					}
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_orders')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_orders'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if (empty($oID)) {
					$where = '';
					$listing_split = new split_page("select o.order_id, o.order_total, ot.name as order_type_name, os.order_status_name, a.house_number, a.street_name, a.city from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a where  o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id " . $where . " order by o.date_added DESC", '20', 'o.order_id');
						if ($listing_split->number_of_rows > 0) {
				?>
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td class="pageBoxHeading">Order Id</td>
							<td class="pageBoxHeading" align="center">Address</td>
							<td class="pageBoxHeading" align="center">Order Total</td>
							<td class="pageBoxHeading" align="center">Order Type</td>
							<td class="pageBoxHeading" align="center">Order Status</td>
							<td class="pageBoxHeading" align="right">Action</td>
							<td width="10" class="pageBoxHeading"></td>
						</tr>
					<?php
						$query = $database->query($listing_split->sql_query);
							while($result = $database->fetch_array($query)) {
								
					?>
						<tr>
							<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;<?php echo $result['order_id']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $result['house_number']; ?> <?php echo $result['street_name']; ?>, <?php echo $result['city']; ?></td>
							<td class="pageBoxContent" align="center">$<?php echo $result['order_total']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $result['order_type_name']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $result['order_status_name']; ?></td>
							<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_ORDERS . '?oID='.$result['order_id'].'&page_action=view'; ?>">View</a> | <a href="<?php echo FILENAME_ADMIN_ORDERS . '?oID='.$result['order_id'].'&page_action=edit'; ?>">Edit</a></td>
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
					$order = new orders('fetch', $oID);
					$order_data = $order->return_result();
						if ($page_action == 'view') {
			?>
			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
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
					<tr>
						<td class="pageBoxContent">State</td><td class="pageBoxContent"><?php echo $order_data['state_name']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Zip</td><td class="pageBoxContent"><?php echo $order_data['zip']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Zip4</td><td class="pageBoxContent"><?php echo $order_data['zip4']; ?></td>
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
						<td class="pageBoxContent">Order Type</td><td class="pageBoxContent"><?php echo $order_data['order_type_name']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Date Added</td><td class="pageBoxContent"><?php echo date("n/d/Y", $order_data['date_added']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Last Modified</td><td class="pageBoxContent"><?php echo (($order_data['last_modified'] > 0) ? date("n/d/Y", $order_data['last_modified']) : 'Never'); ?></td>
					</tr>
					<tr>
Agent Scheduled Date						<td class="pageBoxContent">Date Scheduled</td><td class="pageBoxContent"><?php echo (($order_data['date_schedualed'] > 0) ? date("n/d/Y", $order_data['date_schedualed']) : 'Never'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Number of Posts</td><td class="pageBoxContent"><?php echo $order_data['number_of_posts']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Special Instructions</td>
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
					} else {
			?>
			<form action="<?php echo FILENAME_ADMIN_ORDERS; ?>?page_action=edit_confirm&oID=<?php echo $oID; ?>" method="post">
			<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Address Information</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">House Number</td><td class="pageBoxContent"><input type="text" name="house_number" value="<?php echo $order_data['house_number']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Street Name</td><td class="pageBoxContent"><input type="text" name="street_name" value="<?php echo $order_data['street_name']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">City</td><td class="pageBoxContent"><input type="text" name="city" value="<?php echo $order_data['city']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">County</td><td class="pageBoxContent"><?php echo tep_draw_county_pulldown('county_id', '', $order_data['county_id']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">State</td><td class="pageBoxContent"><?php echo tep_draw_state_pulldown('state_id', $order_data['state_id']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Zip</td><td class="pageBoxContent"><input type="text" name="zip" value="<?php echo $order_data['zip']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Cross Street/Directions</td>
						<td class="pageBoxContent"><textarea name="cross_street_directions"><?php echo $order_data['cross_street_directions']; ?></textarea></td>
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
						<td class="pageBoxContent">Order Type</td><td class="pageBoxContent"><?php echo $order_data['order_type_name']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Order Status</td><td class="pageBoxContent"><?php echo $order_data['order_status_name']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Job Total</td><td class="pageBoxContent"><input type="text" name="order_total" value="<?php echo $order_data['order_total']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Date Added</td><td class="pageBoxContent"><?php echo date("n/d/Y", $order_data['date_added']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Last Modified</td><td class="pageBoxContent"><?php echo (($order_data['last_modified'] > 0) ? date("n/d/Y", $order_data['last_modified']) : 'Never'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Agent Scheduled Date</td><td class="pageBoxContent"><?php echo (($order_data['date_schedualed'] > 0) ? date("n/d/Y", $order_data['date_schedualed']) : 'Never'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Number of Posts</td><td class="pageBoxContent"><input type="text" name="number_of_posts" value="<?php echo $order_data['number_of_posts']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Special Instructions</td>
						<td class="pageBoxContent"><textarea name="special_instructions"><?php echo $order_data['special_instructions']; ?></textarea></td>
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
						<td colspan="2" class="pageBoxContent"><b>Order Comments</b> <i>(These are only viewable for admins and installers)</i></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Admin Comments</td>
						<td class="pageBoxContent"><textarea name="admin_comments"><?php echo $order_data['admin_comments']; ?></textarea></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Installer Comments</td>
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
						<td height="20"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
					</tr>
					<?php
								$n++;
							}
					?>
					<tr>
						<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
					</tr>
					<tr>
						<td class="main" colspan="2"><b>Add a New Comment</b></td>
					</tr>
					<tr>
						<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2" NOWRAP>Status: <?php echo tep_draw_orders_status_pulldown('new_order_status_id', $order_data['order_status_id']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2" NOWRAP>Notify User: <?php echo tep_draw_notify_user_pulldown('new_user_notified', '0'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2">Comments: </td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2"><textarea name="new_comment"></textarea></td>
					</tr>				
				</table>
			<?php
					}
				}
			?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
		<?php
			if (!empty($oID)) {
				if ($page_action == 'add') {
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
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_ORDERS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
							<tr>
								<td class="pageBoxContent">Press cancel to go back to the previous page or press update to confirm changes.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><input type="submit" value="Update" /></form><form action="<?php echo FILENAME_ADMIN_ORDERS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
						</table>
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