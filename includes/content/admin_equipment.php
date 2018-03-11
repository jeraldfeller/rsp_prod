<?php

	/*$query = $database->query("select equipment_item_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_status_id = '1'");
		while($result = $database->fetch_array($query)) {
			echo 'equipment item: ' . $result['equipment_item_id'] . '<br>';
			
			$equipment_order_query = $database->query("select order_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_item_id = '" . $result['equipment_item_id'] . "' order by equipment_to_order_id DESC");
			$equipment_order_result = $database->fetch_array($equipment_order_query);
			
			echo 'last order: ' . $equipment_order_result['order_id'] . '<br>';
			
			$order_query = $database->query("select order_status_id, order_type_id, address_id from " . TABLE_ORDERS . " where order_id = '" . $equipment_order_result['order_id'] . "' limit 1");
			$order_result = $database->fetch_array($order_query);
			
			echo 'order type: ' . $order_result['order_type_id'] . '<br>';
			echo 'order status: ' . $order_result['order_type_id'] . '<br>';
			
			$address_query = $database->query("select house_number, street_name from " . TABLE_ADDRESSES . " where address_id = '" . $order_result['address_id'] . "' limit 1");
			$address_result = $database->fetch_array($address_query);
			
			echo 'address: ' . $address_result['house_number'] . ' ' . $address_result['street_name'] . '<br>';
				if (empty($equipment_order_result['order_id']) || empty($order_result['order_type_id'])) {
					//$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
					//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
				}
			echo '<br><br><br>';
			
			
		}
		
	die(); */
	$page_action = tep_fill_variable('page_action', 'get');
	$eID = tep_fill_variable('eID', 'get', tep_fill_variable('eID', 'post'));
	$submit_value = tep_fill_variable('submit_value_y');
	$equipment_type_id = tep_fill_variable('equipment_type_id', 'get', '4');
	$start_letter = tep_fill_variable('start_letter', 'get', '');
	$search_name = tep_fill_variable('search_name', 'get', '');
	$out_of_stock = tep_fill_variable('out_of_stock', 'get', '');

	$message = '';
	
	/*$query = mysql_query("select equipment_id, name from " . TABLE_EQUIPMENT . " where personalized = '1'");
		while($result = mysql_fetch_array($query)) {
			$database->query("delete from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where install_equipment_id = '" . $result['equipment_id'] . "' limit 1");
			//$database->query("insert into " . TABLE_EQUIPMENT_GROUP_ANSWERS . " (equipment_group_id, name, service_level_id, install_equipment_id, remove_equipment_id, checked) values ('5', 'Install " . addslashes($name) . "', '1', '" . (int)$result['equipment_id'] . "', '0', '0')");
			$database->query("insert into " . TABLE_EQUIPMENT_GROUP_ANSWERS . " (equipment_group_id, name, service_level_id, install_equipment_id, remove_equipment_id, checked) values ('5', 'Install " . addslashes($result['name']) . "', '1', '" . (int)$result['equipment_id'] . "', '0', '0')");
		}*/
		if (($page_action == 'edit_confirm') || ($page_action == 'add_confirm')) {
			$run = true;
				if  ($submit_value == '') {
					$page_action = 'edit';
					$run = false;
				}
				if ($run) {
					$name = tep_fill_variable('name', 'post');
					//$total = tep_fill_variable('total', 'post');
					$equipment_group_id = tep_fill_variable('equipment_group_id', 'post');
					$replace_cost = tep_fill_variable('replace_cost', 'post');
					$personalized = tep_fill_variable('personalized', 'post');
					$tracking_method_id = tep_fill_variable('tracking_method_id', 'post');
					$equipment_type_id = tep_fill_variable('equipment_type_id', 'post');
					$installer_install_payment = tep_fill_variable('installer_install_payment', 'post');
					$installer_remove_payment = tep_fill_variable('installer_remove_payment', 'post');
					$equipment_location = tep_fill_variable('equipment_location', 'post');
					$equipment_size = tep_fill_variable('equipment_size', 'post');
					
					$error_status = false;
						if (empty($name)) {
							$error->add_error('admin_equipment', 'You must enter at least a name for this group.');
							$page_action = 'edit';
							$error_status= true;
						} elseif ($page_action == 'add_confirm') {
							$query = $database->query("select equipment_id from " . TABLE_EQUIPMENT . " where name = '" . $name . "' limit 1");
							$result = $database->fetch_array($query);
								if (!empty($result['equipment_id'])) {
									$error->add_error('admin_equipment', 'There is already a group in the database with that name.');
									$page_action = 'edit';
									$error_status= true;
											
								}
						}
						
						if (!$error_status) {
							
							if ($page_action == 'edit_confirm') {
								$database->query("update " . TABLE_EQUIPMENT . " set name = '" . $name . "', replace_cost = '" . $replace_cost . "', personalized = '" . $personalized . "', tracking_method_id = '" . $tracking_method_id . "', equipment_type_id = '" . $equipment_type_id . "', installer_install_payment = '" . $installer_install_payment . "', installer_remove_payment = '" . $installer_remove_payment . "', equipment_location = '" . $equipment_location . "', equipment_size = '" . $equipment_size . "' where equipment_id = '" . $eID . "' limit 1");
								/*$database->query("delete from " . TABLE_EQUIPMENT_TO_EQUIPMENT_GROUPS . " where equipment_id = '" . $eID . "'");
								$n =0;
								$count = count($equipment_group_id);
									while($n < $count) {
											if (!empty($equipment_group_id[$n])) {
												$database->query("insert into " . TABLE_EQUIPMENT_TO_EQUIPMENT_GROUPS . " (equipment_id, equipment_group_id) values ('" . $eID . "', '" . $equipment_group_id[$n] . "')");
											}
										$n++;
									}*/
								$message = 'Equipment Item successfully updated.';
							} else {
								if ($equipment_type_id == '4') {
									$database->query("insert into " . TABLE_EQUIPMENT . " (name, replace_cost, personalized, tracking_method_id, equipment_type_id, installer_install_payment, installer_remove_payment, equipment_location, equipment_size, equipment_group_id) values ('" . $name . "', '" . $replace_cost . "', '" . $personalized . "', '" . $tracking_method_id . "', '" . $equipment_type_id . "', '" . $installer_install_payment . "', '" . $installer_remove_payment . "', '" . $equipment_location . "', '" . $equipment_size . "', '37')");
								} else {
									$database->query("insert into " . TABLE_EQUIPMENT . " (name, replace_cost, personalized, tracking_method_id, equipment_type_id, installer_install_payment, installer_remove_payment, equipment_location, equipment_size) values ('" . $name . "', '" . $replace_cost . "', '" . $personalized . "', '" . $tracking_method_id . "', '" . $equipment_type_id . "', '" . $installer_install_payment . "', '" . $installer_remove_payment . "', '" . $equipment_location . "', '" . $equipment_size . "')");
								}
							$eID = $database->insert_id();
								/*$equipment_id = $database->insert_id();
								$n =0;
								$count = count($equipment_group_id);
									while($n < $count) {
											if (!empty($equipment_group_id[$n])) {
												$database->query("insert into " . TABLE_EQUIPMENT_TO_EQUIPMENT_GROUPS . " (equipment_id, equipment_group_id) values ('" . $equipment_id . "', '" . $equipment_group_id[$n] . "')");
											}
										$n++;
									}*/
								$message = 'Equipment Item successfully added.';
							}
							if ($personalized == '1' && $equipment_type_id == 5) {
								$database->query("delete from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where install_equipment_id = '" . $eID . "' limit 1");
								$database->query("insert into " . TABLE_EQUIPMENT_GROUP_ANSWERS . " (equipment_group_id, name, service_level_id, install_equipment_id, remove_equipment_id, checked) values ('9', 'Install " . addslashes($name) . "', '3', '" . (int)$eID . "', '0', '0')");
                            } elseif ($personalized == '1') {
								$database->query("delete from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where install_equipment_id = '" . $eID . "' limit 1");
								$database->query("insert into " . TABLE_EQUIPMENT_GROUP_ANSWERS . " (equipment_group_id, name, service_level_id, install_equipment_id, remove_equipment_id, checked) values ('5', 'Install " . addslashes($name) . "', '2', '" . (int)$eID . "', '0', '0')");
							}
						}
				}
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_EQUIPMENT . " where equipment_id = '" . $eID . "' limit 1");
			//die("delete from " . TABLE_EQUIPMENT . " where equipment_id = '" . $eID . "' limit 1");
			//$database->query("delete from " . TABLE_EQUIPMENT_TO_EQUIPMENT_GROUPS . " where equipment_id = '" . $eID . "'");
			$eID = '';
			$page_action = '';
		} elseif ($page_action == 'delete') {

		}	elseif ($page_action == 'move_confirm') {
				if (empty($submit_value)) {
					$page_action = 'move';
				} else {
					//actually do the move.
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
							$query = $database->query("select equipment_item_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $eID . "' and warehouse_id = '" . $move_from . "' and equipment_status_id = '0' limit " . $move_quantity . "");
								foreach ($database->fetch_array($query) as $result){
									$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set warehouse_id = '" . $move_to . "' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
								}
							$error->add_error('admin_equipment', 'Equipment Successfully Moved.', 'success');
						}
				}
		}
		if ($page_action == 'edit') {
				if (is_numeric($eID)) {
					$edit_message = 'Please make the required changes and press "Update" to confirm.';
					$button_value = 'Update';
				} else {
					$edit_message = 'Please enter the required details and press "Insert" to insert it into the database.';
					$button_value = 'Add';
				}
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_equipment')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_equipment'); ?></td>
	</tr>
	<?php
		}
	?>
	<?php
		if ($error->get_error_status('admin_equipment', 'success')) {
	?>
	<tr>
		<td class="mainSuccess" colspan="2"><?php echo $error->get_error_string('admin_equipment', 'success'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Equipment Name</td>
						<td class="pageBoxHeading" align="center">Total</td>
						<td class="pageBoxHeading" align="center">Available</td>
						<td class="pageBoxHeading" align="center">Size</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$egData = array();
					$where_string = '';
						if (!empty($equipment_type_id)) {
								if (empty($where_string)) {
									$where_string .= ' where ';
								} else {
									$where_string .= ' and ';
								}
							$where_string .= "e.equipment_type_id = '" . $equipment_type_id . "'";
						}
						
						if (!empty($start_letter)) {
								if (empty($where_string)) {
									$where_string .= ' where ';
								} else {
									$where_string .= ' and ';
								}
							$where_string .= "LEFT(e.name, 1) = '" . $start_letter . "'";
						}
						
						if (!empty($search_name)) {
								if (empty($where_string)) {
									$where_string .= ' where ';
								} else {
									$where_string .= ' and ';
								}
							$where_string .= "(e.name = '" . $search_name . "' or e.name like '%" . $search_name . "' or e.name like '" . $search_name . "%' or e.name like '%" . $search_name . "%')";
						}
						if ($out_of_stock == '1') {
								if (empty($where_string)) {
									$where_string .= ' where ';
								} else {
									$where_string .= ' and ';
								}
							$where_string .= "ei.equipment_item_id is NULL";
						}						
					$listing_split = new split_page("select e.equipment_id, e.tracking_method_id,  e.name as equipment_name, e.equipment_size, eg.name as equipment_group_name, count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e left join " . TABLE_EQUIPMENT_GROUPS . " eg on (e.equipment_group_id = eg.equipment_group_id) left join " . TABLE_EQUIPMENT_ITEMS . " ei on (e.equipment_id = ei.equipment_id and ei.equipment_status_id = '0') " . $where_string . " group by e.equipment_id order by e.name", '20', 'e.equipment_id');
						if ($listing_split->number_of_rows > 0) {
							$query = $database->query($listing_split->sql_query);
							    foreach ($database->fetch_array($query) as $result){
									$available_items = $result['count'];
		
										if ($eID == $result['equipment_id']) {
											$egData = $result;
										}
									
									
						?>
							<tr>
								<td class="pageBoxContent"><?php echo stripslashes($result['equipment_name']); ?></td>
								<td class="pageBoxContent" align="center"><?php echo tep_fetch_total_equipment_count($result['equipment_id']); ?></td>
								<td class="pageBoxContent" align="center"><?php echo $available_items; ?></td>
								<td class="pageBoxContent" align="center"><?php echo $result['equipment_size']; ?></td>
								<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT . '?eID='.$result['equipment_id'].'&page_action=edit&equipment_type_id='.$equipment_type_id.'&start_letter='.$start_letter.'&out_of_stock='.$out_of_stock.'&search_name='.$search_name; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT . '?eID='.$result['equipment_id'].'&page_action=delete'; ?>&equipment_type_id=<?php echo $equipment_type_id; ?>&start_letter=<?php echo $start_letter; ?>&out_of_stock=<?php echo $out_of_stock; ?>&search_name=<?php echo $search_name; ?>">Delete</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT . '?eID='.$result['equipment_id'].'&page_action=move&equipment_type_id='.$equipment_type_id.'&start_letter='.$start_letter.'&out_of_stock='.$out_of_stock.'&search_name='.$search_name; ?>">Move</a></td>
								<td width="10" class="pageBoxContent"></td>
							</tr>
						<?php
								}
							?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> equipment)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'x', 'y'. 'eID', 'page_action'))); ?></td>
									</tr>
								</table>
							</td>
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
								$query = $database->query("select name, equipment_type_id, equipment_size, equipment_group_id, replace_cost, personalized, tracking_method_id, equipment_location, installer_install_payment, installer_remove_payment from " . TABLE_EQUIPMENT . " where equipment_id = '" . $eID . "' limit 1");
								$result = $database->fetch_array($query);
							} else {
								$result = array('name' => '',
														 'personalized' => '1',
														// 'user_id' => '',
														 'tracking_method_id' => '',
														 'replace_cost' => '',
														 'installer_install_payment' => '',
														 'installer_remove_payment' => '',
														 'equipment_location' => '',
														 'equipment_type_id' => '',
														 'equipment_size' => '');
							}
							//Set the values.  Do it here again in case there was a failed submit.
							//$total = tep_fill_variable('total', 'post', $result['total']);
							$name = tep_fill_variable('name', 'post', $result['name']);
							$personalized = tep_fill_variable('personalized', 'post', $result['personalized']);
							$equipment_type_id = tep_fill_variable('equipment_type_id', 'post', $result['equipment_type_id']);
							//$user_id = tep_fill_variable('user_id', 'post', $result['user_id']);
							$replace_cost = tep_fill_variable('replace_cost', 'post', $result['replace_cost']);
							$installer_install_payment = tep_fill_variable('installer_install_payment', 'post', $result['installer_install_payment']);
							$installer_remove_payment = tep_fill_variable('installer_remove_payment', 'post', $result['installer_remove_payment']);
							$tracking_method_id = tep_fill_variable('tracking_method_id', 'post', $result['tracking_method_id']);
							$equipment_location = tep_fill_variable('equipment_location', 'post', $result['equipment_location']);
							$equipment_size = tep_fill_variable('equipment_size', 'post', $result['equipment_size']);
						
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_EQUIPMENT . '?page_action='.((is_numeric($eID)) ? ('edit_confirm&eID='.$eID) : 'add_confirm'); ?>&equipment_type_id=<?php echo $equipment_type_id; ?>&start_letter=<?php echo $start_letter; ?>&out_of_stock=<?php echo $out_of_stock; ?>&search_name=<?php echo $search_name; ?>">
							<tr>
								<td class="pageBoxContent" colspan="2"><?php echo $edit_message; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="120"><img src="images/pixel_trans.gif" height="1" width="120"></td>
								<td width="100%"></td>
							</tr>
							<tr>
								<td class="main">Name: </td><td><input type="text" name="name" value="<?php echo stripslashes($name); ?>" /></td>
							</tr>
							<tr>
								<td class="main">Equipment Type: </td><td><?php echo tep_draw_equipment_type_type_pulldown('equipment_type_id', $equipment_type_id); ?></td>
							</tr>
							<tr>
								<td class="main">Replace Cost: </td><td><input type="text" name="replace_cost" value="<?php echo $replace_cost; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Install Payment: </td><td><input type="text" name="installer_install_payment" value="<?php echo $installer_install_payment; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Remove Payment: </td><td><input type="text" name="installer_remove_payment" value="<?php echo $installer_remove_payment; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Tracking Method: </td><td><?php echo tep_draw_tracking_method_type_pulldown('tracking_method_id', $tracking_method_id); ?></td>
							</tr>
							<tr>
								<td class="main">Personalized: </td><td><?php echo tep_draw_personalized_type_pulldown('personalized', $personalized); ?></td>
							</tr>
							<tr>
								<td class="main">Location: </td><td><input type="text" name="equipment_location" value="<?php echo $equipment_location; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Size: </td><td><input type="text" name="equipment_size" value="<?php echo $equipment_size; ?>" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit(strtolower($button_value), $button_value, ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT; ?>?equipment_type_id=<?php echo $equipment_type_id; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<?php
						}elseif ($page_action == 'delete') {
							//Need to check if there are any items of this type.
							$query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $eID . "' and equipment_status_id < '9'");
							$result = $database->fetch_array($query);
								if ($result['count'] > 0) {
								?>
									<table width="250" cellspacing="0" celpadding="0" class="pageBox">
										<tr>
											<td class="pageBoxContent">There is currently active equipment assigned to this type.  Please remove them or mark them as return/disposed of before deleting this type.</td>
										</tr>
										<tr>
											<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
										</tr>
										<tr>
											<td width="100%" align="right">
												<table width="100%" cellspacing="0" cellpadding="0">
													<tr>
														<td align="left"></td>
														<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT; ?>?equipment_type_id=<?php echo $equipment_type_id; ?>&start_letter=<?php echo $start_letter; ?>&out_of_stock=<?php echo $out_of_stock; ?>&search_name=<?php echo $search_name; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								<?php
								} else {
								?>
									<table width="250" cellspacing="0" celpadding="0" class="pageBox">
										<tr>
											<td class="pageBoxContent">Are you sure you wish to delete this Equipment Type?</td>
										</tr>
										<tr>
											<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
										</tr>
										<tr>
											<td width="100%" align="right">
												<table width="100%" cellspacing="0" cellpadding="0">
													<tr>
														<td align="left"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT; ?>?eID=<?php echo $eID; ?>&page_action=delete_confirm&equipment_type_id=<?php echo $equipment_type_id; ?>&start_letter=<?php echo $start_letter; ?>&out_of_stock=<?php echo $out_of_stock; ?>&search_name=<?php echo $search_name; ?>" method="post"><?php echo tep_create_button_submit('delete', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
														<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT; ?>?equipment_type_id=<?php echo $equipment_type_id; ?>&start_letter=<?php echo $start_letter; ?>&out_of_stock=<?php echo $out_of_stock; ?>&search_name=<?php echo $search_name; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								<?php
								}
					}elseif ($page_action == 'move') {
						$move_from = tep_fill_variable('move_from', 'post');
						$move_to = tep_fill_variable('move_to', 'post');
						
						$move_quantity = tep_fill_variable('move_quantity', 'post', '0');
						
						
						
						$max = 0;
							
						
							if (!empty($move_from)) {
								$max = tep_fetch_available_equipment_count($eID, $move_from, 'none');
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
							<form action="<?php echo FILENAME_ADMIN_EQUIPMENT; ?>?eID=<?php echo $eID; ?>&page_action=move_confirm&equipment_type_id=<?php echo $equipment_type_id; ?>&start_letter=<?php echo $start_letter; ?>&out_of_stock=<?php echo $out_of_stock; ?>&search_name=<?php echo $search_name; ?>" method="post">
							<tr>
								<td class="pageBoxContent">To move "<?php echo tep_get_equipment_name($eID); ?>" Select the from warehouse, the to warehouse and the quantity.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
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
											<td align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT; ?>?equipment_type_id=<?php echo $equipment_type_id; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></td>
										</tr>
									</table>
								</td>
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
							<td class="pageBoxContent">Click edit to edit an Equipment Item or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>" method="get">
						<tr>
							<td class="pageBoxContent">Show only: <?php echo tep_generate_equipment_type_pulldown_menu('equipment_type_id', $equipment_type_id, array(array('id' => '', 'name' => 'Any')), ' onchange="this.form.submit();"'); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Show out of Stock: <input type="checkbox" name="out_of_stock" value="1"<?php echo (($out_of_stock == '1') ? ' CHECKED' : ''); ?> /></td>
						</tr>
						<tr>
							<td class="main">Show equipment types with name like: <input type="text" name="search_name" value="<?php echo $search_name; ?>" /></td>
						</tr>
						<tr>
							<td class="main">Show equipment types starting with <select name="start_letter" onchange="this.form.submit();"><?php
								$query = $database->query("select LEFT(name, 1) as letter from " . TABLE_EQUIPMENT . "  group by letter order by letter");
								echo '<option value="" oncjhan>Any</option>';
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
							<td height="5" align="right"></td>
						</tr>
						<tr>
							<td width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></td>
										<td align="right"><a href="<?php echo PAGE_URL; ?>?page_action=edit&equipment_type_id=<?php echo $equipment_type_id; ?>&start_letter=<?php echo $start_letter; ?>&out_of_stock=<?php echo $out_of_stock; ?>&search_name=<?php echo $search_name; ?>&start_letter=<?php echo $start_letter; ?>&out_of_stock=<?php echo $out_of_stock; ?>&search_name=<?php echo $search_name; ?>"><?php echo tep_create_button_link('create', 'Create'); ?></a></td>
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
