<?php
/*$count = 0;
	$query = $database->query("select ei.equipment_item_id from  " . TABLE_EQUIPMENT_ITEMS . " ei where ei.equipment_status_id = '2' and ei.equipment_id >= '37' and ei.equipment_id <= '40' group by ei.equipment_item_id");
		while($result = $database->fetch_array($query)) {
				$last_order_query = $database->query("select o.address_id from " . TABLE_ORDERS . " o, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.equipment_item_id = '" . $result['equipment_item_id'] . "' and eto.order_id = o.order_id and o.order_type_id = '1' and o.order_status_id = '3' order by o.order_id DESC limit 1");
				$last_order_result = $database->fetch_array($last_order_query);
				
				$removal_query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '" . $last_order_result['address_id'] . "' and order_type_id = '3' and order_status_id = '3'");
					if ($database->num_rows($removal_query) > 0) {
						//$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
						
					}
			
		}
	die('<b>'.$count.'</b>');*/
	$page_action = tep_fill_variable('page_action', 'get');
	$eID = tep_fill_variable('eID', 'get', tep_fill_variable('eID', 'post'));
	$warehouse_id = tep_fill_variable('warehouse_id', 'get', tep_fill_variable('warehouse_id', 'post'));
	$equipment_type_id = tep_fill_variable('equipment_type_id', 'get', tep_fill_variable('equipment_type_id', 'post'));
	$equipment_id = tep_fill_variable('equipment_id', 'get', tep_fill_variable('equipment_id', 'post'));
	$submit_value = tep_fill_variable('submit_value_y');
	$exclude_returned = tep_fill_variable('exclude_returned', 'get');
	$exclude_disposed = tep_fill_variable('exclude_disposed', 'get');
		if (empty($_GET)) {
			$exclude_returned = '1';
			$exclude_disposed = '1';
		}
	$personalized_show = tep_fill_variable('personalized_show', 'get', tep_fill_variable('personalized_show', 'post'));
		if (tep_fetch_equipment_type_id($equipment_id) != $equipment_type_id) {
			$equipment_id = '';
		}
	$status_id = ((isset($_GET['status_id'])) ? $_GET['status_id'] : ((isset($_POST['status_id'])) ? $_POST['status_id'] : ''));
	$search = tep_fill_variable('search', 'get');
	//$equipment_type_id = tep_fill_variable('equipment_type_id', 'get');
	//$equipment_id = tep_fill_variable('equipment_id', 'get');
	$start_letter = tep_fill_variable('start_letter', 'get');
	$agent_id = tep_fill_variable('agent_id', 'get');
	$agency_id = tep_fill_variable('agency_id', 'get');
	$return_type = tep_fill_variable('return_type', 'get');

	$message = '';
	
	/*$query = $database->query("select warehouse_id from " . TABLE_WAREHOUSES . "");
		while ($result = $database->fetch_array($query)) {
			$sub_query = $database->query("select equipment_id from " . TABLE_EQUIPMENT . " where tracking_method_id = '0' and personalized = '0'");
				while($sub_result = $database->fetch_array($sub_query)) {
					for($n = 0, $m = 50; $n < $m; $n++) {
						$database->query("insert into " . TABLE_EQUIPMENT_ITEMS . " (equipment_id, equipment_status_id, date_added, warehouse_id) values ('" . $sub_result['equipment_id'] . "', '0', '" . mktime() . "', '" . $result['warehouse_id'] . "')");
					}
				}
		}*/
		if (($page_action == 'edit_confirm') || ($page_action == 'add_confirm')) {
			$run = true;
				if  ($submit_value == '') {
					$page_action = 'edit';
					$run = false;
				}
				if ($run) {
					$equipment_id = tep_fill_variable('equipment_id');
					$code = tep_fill_variable('code');
					$quantity = tep_fill_variable('quantity', 'post', 1);
						if (!is_numeric($quantity) || ($quantity == 0)) {
							$quantity = 1;
						}
					$warehouse_id = tep_fill_variable('warehouse_id');
					$equipment_status_id = tep_fill_variable('equipment_status_id', 'post', '0');
					$user_id = tep_fill_variable('user_id');
					$agency_id = tep_fill_variable('agency_id');
					$apply_to_all = tep_fill_variable('apply_to_all');
					$comments = tep_fill_variable('comments');
						if (empty($equipment_id)) {
							$error->add_error('admin_equipment_items', 'You must select a equipment type for this item.');
							$page_action = 'edit';
						} elseif (empty($warehouse_id)) {
							$error->add_error('admin_equipment_items', 'You must select a warehouse for this item.');
							$page_action = 'edit';
						} elseif ((tep_equipment_is_tracked($equipment_id)) && empty($code)) {
							$error->add_error('admin_equipment_items', 'This equipment type is tracked.  You must enter a code.');
							$page_action = 'edit';
						} else {
							if ($page_action == 'edit_confirm') {
								$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_id = '" . $equipment_id . "', code = '" . $code . "', warehouse_id = '" . $warehouse_id . "', equipment_status_id = '" . $equipment_status_id . "', user_id = '" . $user_id . "', agency_id = '" . $agency_id . "' where equipment_item_id = '" . $eID . "' limit 1");
								$message = 'Equipment Item successfully updated.';
								//Do the comments if needed.
									if (!empty($comments)) {
										tep_add_equipment_item_history($eID, $equipment_status_id, $comments);
									}
							} else {
								$n = 0;
									while($n < $quantity) {
										$database->query("insert into " . TABLE_EQUIPMENT_ITEMS . " (equipment_id, code, equipment_status_id, date_added, warehouse_id, user_id, agency_id) values ('" . $equipment_id . "', '" . $code . "', '".$equipment_status_id."', '" . mktime() . "', '" . $warehouse_id . "', '" . $user_id . "', '" . $agency_id . "')");
										//Add in the status.  If its a personalized panel trhen we will include more detail.
											if (!empty($comments)) {
												tep_add_equipment_item_history($database->insert_id(), $equipment_status_id, $comments);
											}
											
										$n++;
									}
								$message = 'Equipment Item successfully added.';
							}
							if (!empty($apply_to_all) && ($apply_to_all == '1')) {
								$all_query = $database->query("select equipment_item_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $equipment_id . "' and equipment_status_id > 2");
								    foreach($database->fetch_array($all_query) as $all_result){
										tep_add_equipment_item_history($all_result['equipment_item_id'], $equipment_status_id, $comments);
									}
								$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set user_id = '" . $user_id . "', agency_id = '" . $agency_id . "' where equipment_id = '" . $equipment_id . "'");
								$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $equipment_status_id . "' where equipment_id = '" . $equipment_id . "' and equipment_status_id > 2");

								//echo "update " . TABLE_EQUIPMENT_ITEMS . " set user_id = '" . $user_id . "', agency_id = '" . $agency_id . "' where equipment_id = '" . $equipment_id . "'" . '<br>';
								//die();
							}
						}
						if ($return_type == '1') {
							header('Location: ' . FILENAME_ADMIN_EQUIPMENT_REPORT . '?equipment_type_id='.$equipment_type_id.'&warehouse_id='.$warehouse_id.'&equipment_id='.$equipment_id);
							die();
						} else {
							header('Location: ' . FILENAME_ADMIN_EQUIPMENT_ITEMS . '?' . tep_get_all_get_params(array('page_action')));
							die();
						}
				}
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_EQUIPMENT_ITEMS . " where equipment_item_id = '" . $eID . "' limit 1");
			$eID = '';
			$page_action = '';
				if ($return_type == '1') {
					header('Location: ' . FILENAME_ADMIN_EQUIPMENT_REPORT . '?equipment_type_id='.$equipment_type_id.'&warehouse_id='.$warehouse_id.'&equipment_id='.$equipment_id);
					die();
				}
		} elseif ($page_action == 'delete') {

		} elseif ($page_action == 'move_confirm') {
				
				if  ($submit_value == '') {
					$page_action = 'move';
					$run = false;
				} else {
					$move_from = tep_fill_variable('move_from', 'post');
					$move_to = tep_fill_variable('move_to', 'post');
					$move_quantity = tep_fill_variable('move_quantity', 'post');
					
						if (empty($move_from)) {
							$error->add_error('admin_equipment', 'Please select a Move From warehouse.');
						}
						if (empty($move_to)) {
							$error->add_error('admin_equipment', 'Please select a Move To warehouse.');
						}
						if (empty($move_quantity)) {
							$error->add_error('admin_equipment', 'Please enter the Quantity to move.');
						}
						if ($error->get_error_status('admin_equipment')) {
							$page_action = 'move';
						} else {
							$query = $database->query("select equipment_item_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $equipment_id . "' and warehouse_id = '" . $move_from . "' and equipment_status_id = '0' limit " . $move_quantity . "");
							//echo "select equipment_item_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $equipment_id . "' and warehouse_id = '" . $move_from . "' and equipment_status_id = '0' limit " . $move_quantity . "" . '<br>';
							    foreach($database->fetch_array($query) as $result){
									$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set warehouse_id = '" . $move_to . "' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
									//echo "update " . TABLE_EQUIPMENT_ITEMS . " set warehouse_id = '" . $move_to . "' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
								}
							$error->add_error('admin_equipment', 'Equipment Successfully Moved.', 'success');
							tep_redirect(FILENAME_ADMIN_EQUIPMENT_ITEMS.'?equipment_type_id='.$equipment_type_id.'&equipment_id='.$equipment_id);
						}
				}
		}
		if ($page_action == 'edit') {
				if (is_numeric($eID)) {
					$edit_message = 'Please make the required changes and press "Update" to confirm.';
					$button_value = 'Update';
				} else {
					$edit_message = 'Please enter the required details and press "Add" to insert it into the database.';
					$button_value = 'Add';
				}
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_equipment_items')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_equipment_items'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
				<?php
						
					$egData = array();
                // Issue on group bu
                //$listing_split = new split_page("select e.equipment_id, e.name as equipment_name, ei.equipment_item_id, ei.code, ei.date_last_checked, es.equipment_status_id, es.equipment_status_name, wd.name as warehouse_name, o.order_id, o.order_status_id, o.order_type_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei left join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) left join " . TABLE_ORDERS . " o on (eto.order_id = o.order_id and o.order_type_id < 3), " . TABLE_EQUIPMENT_STATUSES . " es, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where e.equipment_id = ei.equipment_id and ".(($status_id !== '') ? " ei.equipment_status_id = '" . $status_id . "' and " : '')."".(($exclude_returned !== '') ? " ei.equipment_status_id != '3' and " : '')."".(($exclude_disposed !== '') ? " ei.equipment_status_id != '10' and " : '')."ei.equipment_status_id = es.equipment_status_id and ei.warehouse_id = wd.warehouse_id" . ((!empty($equipment_type_id)) ? " and e.equipment_type_id = '" . $equipment_type_id . "' " : '') . ((!empty($equipment_id)) ? " and ei.equipment_id = '" . $equipment_id . "' " : '') . ((!empty($search)) ? " and ((e.name like '%".$search."' or e.name like '".$search."%' or e.name like '%".$search."%') or (ei.code like '%".$search."' or ei.code like '".$search."%' or ei.code like '%".$search."%'))" : '') . ((!empty($start_letter)) ? " and (e.name like '".$start_letter."%')" : '') . (($agent_id !== '') ? " and ei.user_id = '" . $agent_id . "'" : ((($personalized_show == 'personalized') && ($agent_id == '0')) ? " and ei.user_id = '0'": '')) . ((!empty($agency_id)) ? " and ei.agency_id = '" . $agency_id . "'" : ((($personalized_show == 'personalized') && ($agency_id == '0')) ? " and ei.agency_id = '0'": '')) . (($personalized_show == 'personalized') ? " and e.personalized = '1'" : '') . (($personalized_show == 'nonpersonalized') ? " and e.personalized = '0'" : '') . ' group by ei.equipment_item_id order by o.order_id desc, equipment_name asc', '20', 'ei.equipment_item_id');
                $listing_split = new split_page("select e.equipment_id, e.name as equipment_name, ei.equipment_item_id, ei.code, ei.date_last_checked, es.equipment_status_id, es.equipment_status_name, wd.name as warehouse_name, o.order_id, o.order_status_id, o.order_type_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei left join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) left join " . TABLE_ORDERS . " o on (eto.order_id = o.order_id and o.order_type_id < 3), " . TABLE_EQUIPMENT_STATUSES . " es, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where e.equipment_id = ei.equipment_id and ".(($status_id !== '') ? " ei.equipment_status_id = '" . $status_id . "' and " : '')."".(($exclude_returned !== '') ? " ei.equipment_status_id != '3' and " : '')."".(($exclude_disposed !== '') ? " ei.equipment_status_id != '10' and " : '')."ei.equipment_status_id = es.equipment_status_id and ei.warehouse_id = wd.warehouse_id" . ((!empty($equipment_type_id)) ? " and e.equipment_type_id = '" . $equipment_type_id . "' " : '') . ((!empty($equipment_id)) ? " and ei.equipment_id = '" . $equipment_id . "' " : '') . ((!empty($search)) ? " and ((e.name like '%".$search."' or e.name like '".$search."%' or e.name like '%".$search."%') or (ei.code like '%".$search."' or ei.code like '".$search."%' or ei.code like '%".$search."%'))" : '') . ((!empty($start_letter)) ? " and (e.name like '".$start_letter."%')" : '') . (($agent_id !== '') ? " and ei.user_id = '" . $agent_id . "'" : ((($personalized_show == 'personalized') && ($agent_id == '0')) ? " and ei.user_id = '0'": '')) . ((!empty($agency_id)) ? " and ei.agency_id = '" . $agency_id . "'" : ((($personalized_show == 'personalized') && ($agency_id == '0')) ? " and ei.agency_id = '0'": '')) . (($personalized_show == 'personalized') ? " and e.personalized = '1'" : '') . (($personalized_show == 'nonpersonalized') ? " and e.personalized = '0'" : '') . ' group by ei.equipment_item_id, wd.name, o.order_id order by o.order_id desc, equipment_name asc', '20', 'ei.equipment_item_id');

                if ($listing_split->number_of_rows > 0) {
							?>
							<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
								<tr>
									<td class="pageBoxHeading">Name</td>
									<td class="pageBoxHeading" align="center">Reference Code</td>
									<td class="pageBoxHeading" align="center">Status</td>
									<td class="pageBoxHeading" align="center">Warehouse</td>
									<td class="pageBoxHeading" align="center">Last Checked</td>
									<td class="pageBoxHeading" align="right">Action</td>
									<td width="10" class="pageBoxHeading"></td>
								</tr>
							<?php
								$query = $database->query($listing_split->sql_query);
								    foreach($database->fetch_array($query) as $result){
										if ($eID == $result['equipment_item_id']) {
											$egData = $result;
										}
							?>
								<tr>
									<td class="pageBoxContent"><?php echo $result['equipment_name']; ?></td>
									<td class="pageBoxContent" align="center"><?php echo $result['code']; ?></td>
									<td class="pageBoxContent" align="center"><?php echo $result['equipment_status_name']; ?></td>
									<td class="pageBoxContent" align="center"><?php echo $result['warehouse_name']; ?></td>
									<td class="pageBoxContent" align="center"><?php echo (($result['date_last_checked'] > 0) ? date("n/d/Y", $result['date_last_checked']): 'Never'); ?></td>
									<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?eID='.$result['equipment_item_id'].'&status_id='.$status_id.'&page_action=edit&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned.((!empty($equipment_type_id)) ? '&equipment_type_id='.$equipment_type_id : '') . ((!empty($equipment_id)) ? '&equipment_id='.$equipment_id : ''); ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?eID='.$result['equipment_item_id'].'&status_id='.$status_id.'&page_action=delete&equipment_type_id='.$equipment_type_id.'&equipment_id='.$equipment_id; ?>">Delete</a><?php if (($result['equipment_status_id'] == 1) || ($result['equipment_status_id'] == 2)) { ?> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?'.tep_get_all_get_params(array('page_action', 'eID')).'page_action=view&eID='.$result['equipment_item_id']; ?>">view</a><?php } ?></td>
									<td width="10" class="pageBoxContent"></td>
								</tr>
							<?php
									}
									?>
									<tr>
										<td colspan="8">
											<table class="normaltable" cellspacing="0" cellpadding="2">
												<tr>
													<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> equipment items)'); ?></td>
													<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(10, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<?php
						} else {
					?>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="pageBoxContent">No equipment could be found.  Please use the menu on the right to add on or change the options to try searching again.</td>
						</tr>
					<?php
						}
					?>
				</table>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
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
							if (is_numeric($eID)) {
								$query = $database->query("select ei.equipment_id, ei.code, ei.warehouse_id, ei.equipment_status_id, e.personalized, ei.user_id, ei.agency_id, e.equipment_type_id from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT . " e where ei.equipment_item_id = '" . $eID . "' and ei.equipment_id = e.equipment_id limit 1");
								$result = $database->fetch_array($query);
							} else {
								$result = array('equipment_id' => '',
														 'code' => '',
														 'warehouse_id' => '',
														 'equipment_group_id' => '',
														 'equipment_type_id' => '',
														 'user_id' => '',
														 'personalized' => '',
														 'equipment_status_id' => '',
														 'agency_id' => '');
							}
						
						//Set the values.  Do it here again in case there was a failed submit.
						$equipment_type_id = tep_fill_variable('equipment_type_id', 'post', tep_fill_variable('equipment_type_id', 'get', $result['equipment_type_id']));
						$equipment_id = tep_fill_variable('equipment_id', 'post', tep_fill_variable('equipment_id', 'get', $result['equipment_id']));
						$code = tep_fill_variable('code', 'post', $result['code']);
						$quantity = tep_fill_variable('quantity', 'post', '1');
						$apply_to_all = tep_fill_variable('apply_to_all', 'post', '');
						$user_id = tep_fill_variable('code', 'post', $result['user_id']);
						$agency_id = tep_fill_variable('agency_id', 'post', $result['agency_id']);
						$comments = tep_fill_variable('comments', 'post');
							if (!is_numeric($eID)) {
								$comments = 'Your equipment has been successfully received and is now available for orders.';
							}
						$warehouse_id = tep_fill_variable('warehouse_id', 'post', tep_fill_variable('warehouse_id', 'get', $result['warehouse_id']));
						$equipment_status_id = tep_fill_variable('equipment_status_id', 'post', $result['equipment_status_id']);
							if (!empty($equipment_id) && empty($equipment_type_id)) {
								//Manually fetch.
								$equipment_type_id = tep_fetch_equipment_type_id($equipment_id);
							}
						
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
								<?php
									if ($return_type == '1') {
								?>
									<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?page_action='.((is_numeric($eID)) ? ('edit_confirm&eID='.$eID) : 'add_confirm&status_id='.$status_id.''); ?>&equipment_type_id=<?php echo $equipment_type_id; ?>&warehouse_id=<?php echo $warehouse_id; ?>&equipment_id=<?php echo $equipment_id; ?>&return_type=1">
								<?php
									} else {
								?>
									<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?page_action='.((is_numeric($eID)) ? ('edit_confirm&eID='.$eID) : 'add_confirm').'&equipment_type_id='.$equipment_type_id.'&status_id='.$status_id.'&equipment_id='.$equipment_id; ?>">
								<?php
									}
								?>
							<tr>
								<td class="pageBoxContent" colspan="2"><?php echo $edit_message; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">Equipment Type: </td><td><?php echo tep_generate_equipment_type_pulldown_menu('equipment_type_id', $equipment_type_id, array(array('id' => '', 'name' => 'Select')), ' onchange="this.form.submit();" '); ?></td>
							</tr>
							
							<?php
								if (!empty($equipment_type_id)) {
								?>
								<tr>
									<td class="main">Equipment Name: </td><td><?php echo tep_draw_equipment_pulldown('equipment_id', $equipment_id, '', ' onchange="this.form.submit();" ', array(), $equipment_type_id); ?></td>
								</tr>
								<?php
								}
								if (tep_equipment_is_tracked($equipment_id)) {
							?>
							<tr>
								<td class="main">Code: </td><td><input type="text" name="code" value="<?php echo $code; ?>" /></td>
							</tr>
							<?php
								} elseif (!is_numeric($eID)) {
									//If not tracked then allow to put in a number for quick entry.
							?>
							<tr>
								<td class="main">Quantity: </td><td><input type="text" name="quantity" value="<?php echo $quantity; ?>" /></td>
							</tr>
							<?php	
								}
							?>
							<?php
								if (empty($agency_id)) {
									//$agency_id = tep_fetch_default_agency_id();
								}
								if (tep_equipment_is_personalized($equipment_id)) {
							?>
							<tr>
								<td class="main">Assigned Agency: </td><td><?php echo tep_draw_agency_pulldown('agency_id', $agency_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'None')), '', true, true, true); ?></td>
							</tr>
								<?php
									if (!empty($agency_id)) {
								?>
								<tr>
									<td class="main">Assigned Agent: </td><td><?php echo tep_draw_agent_pulldown('user_id', $user_id, '', array(array('id' => '', 'name' => 'Any')), $agency_id); ?></td>
								</tr>
								<?php
									}
								?>
								<tr>
								<td class="main">Apply to all of this Type: </td><td><input type="checkbox" name="apply_to_all" value="1"<?php echo (($apply_to_all == '1') ? ' CHECKED' : ''); ?> /><br /><i>Does not work for
Installed or Pending items</i></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td class="main">Status: </td><td><?php echo tep_draw_equipment_status_pulldown('equipment_status_id', $equipment_status_id); ?></td>
							</tr>
							
							<tr>
								<td class="main">Warehouse: </td><td><?php echo tep_draw_warehouse_pulldown('warehouse_id', $warehouse_id); ?></td>
							</tr>
							<tr>
								<td class="main" colspan="2" align="left">Comment: </td>
							</tr>
							<tr>
								<td class="main" colspan="2" align="left"><textarea name="comments"><?php echo $comments; ?></textarea></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit(strtolower(str_replace(' ', '_', $button_value)), '', ' name="submit_value"'); ?></form></td>
												<?php
													if ($return_type == '1') {
												?>
													<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?equipment_type_id=<?php echo $equipment_type_id; ?>&warehouse_id=<?php echo $warehouse_id; ?>&equipment_id=<?php echo $equipment_id.'&status_id='.$status_id.'&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
												
												<?php
													} else {
												?>
													<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS.'?equipment_type_id='.$equipment_type_id.'&equipment_id='.$equipment_id.'&status_id='.$status_id.'&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
												<?php
													}
												?>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<?php
						}elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this Equipment Item?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											
											<?php
												if ($return_type == '1') {
											?>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS; ?>?equipment_type_id=<?php echo $equipment_type_id; ?>&warehouse_id=<?php echo $warehouse_id.'&status_id='.$status_id; ?>&equipment_id=<?php echo $equipment_id; ?>&return_type=1&eID=<?php echo $eID.'&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned; ?>&page_action=delete_confirm" method="post"><?php echo tep_create_button_submit('delete', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?equipment_type_id=<?php echo $equipment_type_id; ?>&warehouse_id=<?php echo $warehouse_id.'&status_id='.$status_id; ?>&equipment_id=<?php echo $equipment_id.'&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
											<?php
												} else {
											?>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS; ?>?eID=<?php echo $eID; ?>&page_action=delete_confirm&equipment_id=<?php echo $equipment_id.'&status_id='.$status_id; ?>&equipment_type_id=<?php echo $equipment_type_id.'&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned; ?>" method="post"><?php echo tep_create_button_submit('delete', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS; ?>?equipment_id=<?php echo $equipment_id; ?>&equipment_type_id=<?php echo $equipment_type_id.'&status_id='.$status_id.'&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
											<?php
												}
											?>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					<?php
					}elseif ($page_action == 'move') {
						$move_from = tep_fill_variable('move_from', 'post');
						$move_to = tep_fill_variable('move_to', 'post');
						
						$move_quantity = tep_fill_variable('move_quantity', 'post', '0');
						
						$max = 0;
						$equipment_type_id = tep_fill_variable('equipment_type_id', 'post', tep_fill_variable('equipment_type_id', 'get', ''));
							if (empty($equipment_type_id)) {
								$equipment_type_id = tep_fetch_default_equipment_type_id();
							}
						$equipment_id = tep_fill_variable('equipment_id', 'post', tep_fill_variable('equipment_id', 'get', ''));
							if (!empty($equipment_id) && empty($equipment_type_id)) {
								$equipment_type_id = tep_fetch_equipment_type_id($equipment_id);
							}
							if (empty($equipment_id)) {
								$equipment_id = tep_fetch_default_equipment_id($equipment_type_id);
							}
							if (!empty($move_from)) {
								$max = tep_fetch_available_equipment_count($equipment_id, $move_from);
							}
							if (empty($move_from)) {
								$from_array = array(array('id' => '', 'name' => 'Please Select'));
							} else {
								$from_array = array();
							}
							if (empty($move_to)) {
								$to_array = array(array('id' => '', 'name' => 'Please Select'));
							} else {
								$to_array = array();
							}
						
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<form action="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS; ?>?equipment_id=<?php echo $equipment_id.'&status_id='.$status_id; ?>&equipment_type_id=<?php echo $equipment_type_id.'&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned; ?>&page_action=move_confirm" method="post">
							<tr>
								<td class="pageBoxContent">To move <?php echo ((!empty($equipment_id)) ? '"'.tep_get_equipment_name($equipment_id).'"' : 'equipment'); ?> Select the from warehouse, the to warehouse and the quantity.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="main">Equipment Type: </td><td><?php echo tep_generate_equipment_type_pulldown_menu('equipment_type_id', $equipment_type_id, array(), ' onchange="this.form.submit();" '); ?></td>
										</tr>
										<?php
											if (!empty($equipment_type_id)) {
											?>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
											</tr>
											<tr>
												<td class="main">Equipment Name: </td><td><?php echo tep_draw_equipment_pulldown('equipment_id', $equipment_id, '', ' onchange="this.form.submit();" ', array(), $equipment_type_id); ?></td>
											</tr>
											<?php
											}
										?>
										<tr>
											<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
										</tr>
										<tr>
											<td class="main">From Warehouse:</td>
											<td><?php echo tep_draw_warehouse_pulldown('move_from', $move_from, $from_array, ' onchange="this.form.submit();"'); ?></td>
										</tr>
										<tr>
											<td class="main">To Warehouse:</td>
											<td><?php echo tep_draw_warehouse_pulldown('move_to', $move_to, $to_array); ?></td>
										</tr>
									
									<?php
										if (!empty($move_from)) {
											if ($max < 1) {
											?>
											<tr>
												<td colspan="2" class="main">There is current no "<?php echo tep_get_equipment_name($eID); ?>" in your selected from warehouse.  Please try again.</td>
											</tr>
											<?php
											} else {
											?>
											<tr>
												<td class="main">Quantity:</td>
												<td class="main"><input type="text" name="move_quantity" value="<?php echo $move_quantity; ?>" onblur="if (this.value > <?php echo $max; ?>) { alert('There is not currently that number of stock in the From Warehouse.'); this.value = <?php echo $max; ?>; return false; }" /> (max <?php echo $max; ?>)</td>
											</tr>
											<?php
											}
										}
									?>
									</table>
								</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('move', 'Move Equipment', ' name="submit_value"'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
											<td align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS; ?>?equipment_type_id=<?php echo $equipment_type_id.'&status_id='.$status_id.'&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
					}elseif ($page_action == 'view') {

						$query = $database->query("select a.house_number, a.street_name, a.city from ". TABLE_ADDRESSES . " a, " . TABLE_EQUIPMENT_TO_ORDERS . " eto, " . TABLE_ORDERS . " o where eto.equipment_item_id = '" . (int)$eID . "' and eto.order_id = o.order_id and o.address_id = a.address_id order by o.order_id DESC limit 1");
						$result = $database->fetch_array($query);
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent"><?php echo $result['house_number'].' '.$result['street_name'].', '.$result['city']; ?></td>
							</tr>
							
						</table>
					<?php
					} else {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Equipment Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit an Equipment Item or press Add to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>" method="get">
						<tr>
							<td class="main">Search By: <input type="text" name="search" value="<?php echo $search ?>" /></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td class="main">Equipment Type: <?php echo tep_generate_equipment_type_pulldown_menu('equipment_type_id', $equipment_type_id, array(array('id' => '', 'name' => 'Any')), ' onchange="this.form.submit();" '); ?></td>
						</tr>
						<?php
							if (!empty($equipment_type_id)) {
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
							</tr>
							<tr>
								<td class="main">Equipment Name: <?php echo tep_draw_equipment_pulldown('equipment_id', $equipment_id, '', ' onchange="this.form.submit();" ', array(array('id' => '', 'name' => 'Any')), $equipment_type_id); ?></td>
							</tr>
							<?php
							}
						?>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td class="main">Show Equipment starting with <select name="start_letter"><?php
								$query = $database->query("select LEFT(e.name, 1) as letter from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei where ei.equipment_id = e.equipment_id group by letter order by letter");
								echo '<option value="">Any</option>';
								    foreach($database->fetch_array($query) as $result){
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
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td class="main">Show Only: <select name="personalized_show" onchange="this.form.submit();"><option value=""<?php echo (($personalized_show == '') ? ' SELECTED' : '');?>>Any</option><option value="personalized"<?php echo (($personalized_show == 'personalized') ? ' SELECTED' : '');?>>Personalized</option><option value="nonpersonalized"<?php echo (($personalized_show == 'nonpersonalized') ? ' SELECTED' : '');?>>Non-Personalized</option></select></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td class="main">Show Agent: <?php echo tep_draw_agent_pulldown('agent_id', $agent_id, '', array(array('id' => '', 'name' => 'Any'), array('id' => '0', 'name' => 'None'))); ?></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td class="main">Show Agency: <?php echo tep_draw_agency_pulldown('agency_id', $agency_id, '', array(array('id' => '', 'name' => 'Any'), array('id' => '0', 'name' => 'None'))); ?></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td class="main">Status: <?php echo tep_draw_equipment_status_pulldown('status_id', $status_id, '', array(array('id' => '', 'name' => 'Any'))); ?></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td class="main">Exclude Returned: <input type="checkbox" name="exclude_returned" value="1"<?php echo (($exclude_returned == '1') ? ' CHECKED' : ''); ?> /></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td class="main">Exclude Disposed of: <input type="checkbox" name="exclude_disposed" value="1"<?php echo (($exclude_disposed == '1') ? ' CHECKED' : ''); ?> /></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td><?php echo tep_create_button_submit('update', 'Update'); ?></td>
						</tr>
						</form>
						<tr>
							<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=edit&equipment_id=<?php echo $equipment_id; ?>&equipment_type_id=<?php echo $equipment_type_id.'&exclude_disposed='.$exclude_disposed.'&exclude_returned='.$exclude_returned; ?>" method="post">
						<tr>
							<td height="5">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="left"><?php echo tep_create_button_submit('add', 'Add'); ?><!--<input type="submit" value="Create" />--></td>
										<td aign="right"><a href="<?php echo PAGE_URL; ?>?page_action=move&equipment_id=<?php echo $equipment_id; ?>&equipment_type_id=<?php echo $equipment_type_id; ?>"><?php echo tep_create_button_submit('move', 'Move Equipment'); ?></a></td>
									</tr>
								</table>
							</td>
						</tr>
						</form>
					</table>
				<?php
					}
				?>
		</td>
	</tr>
</table>
</td>
</tr>
</table>