<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$wID = tep_fill_variable('wID', 'get', tep_fill_variable('wID', 'post'));

	$message = '';
	
		if ($page_action == 'edit_confirm') {
			$name = tep_fill_variable('name', 'post');
			$address = tep_fill_variable('address', 'post');
			$availability = tep_fill_variable('availability', 'post');
			$reference_code = tep_fill_variable('reference_code', 'post');
				if (empty($name)) {
					$error->add_error('admin_warehouses', 'You must enter at least a name for this warehouse.');
					$page_action = 'edit';
				} else {
					$database->query("update " . TABLE_WAREHOUSES . " set availability = '" . $availability . "' where warehouse_id = '" . $wID . "' limit 1");
					$database->query("update " . TABLE_WAREHOUSES_DESCRIPTION . " set name = '" . $name . "', address = '" . $address . "', reference_code = '" . $reference_code . "' where warehouse_id = '" . $wID . "' limit 1");
					$message = 'Warehouse Successfully Updated.';
				}
		} elseif ($page_action == 'add_confirm') {
			$name = tep_fill_variable('name', 'post');
			$address = tep_fill_variable('address', 'post');
			$availability = tep_fill_variable('availability', 'post');
			$reference_code = tep_fill_variable('reference_code', 'post');
				if (empty($name)) {
					$error->add_error('admin_warehouses', 'You must enter at least a name for this warehouse.');
					$page_action = 'edit';
				} else {
					$database->query("insert into " . TABLE_WAREHOUSES . " (availability) values ('" . $availability . "')");
					$warehouse_id = $database->insert_id();
					$database->query("insert into " . TABLE_WAREHOUSES_DESCRIPTION . " (warehouse_id, name, address, reference_code) values ('" . $warehouse_id . "', '" . $name . "', '" . $address . "', '" . $reference_code . "')");
					$message = 'Warehouse Successfully Added.';
				}
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_WAREHOUSES . " where warehouse_id = '" . $wID . "' limit 1");
			$database->query("delete from " . TABLE_WAREHOUSES_DESCRIPTION . " where warehouse_id = '" . $wID . "' limit 1");
			
			$wID = '';
			$page_action = '';
		} elseif ($page_action == 'delete') {
			if (tep_fetch_total_equipment_count('', $wID) != 0) {
				$error->add_error('admin_warehouses', 'This warehouse still has equipment assigned to it.  You can not delete it.');
				$page_action = '';
			}
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_warehouses')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_warehouses'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Warehouse Name</td>
						<td class="pageBoxHeading" align="center">In Stock</td>
						<td class="pageBoxHeading" align="center">Assigned</td>
						<td class="pageBoxHeading" align="center">Last Checked</td>
						<td class="pageBoxHeading" align="center">Warehouse Equipment Sharing</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$wData = array();
					$query = $database->query("select w.warehouse_id, w.availability, w.date_last_checked, wd.name from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = wd.warehouse_id order by wd.name");
						foreach($database->fetch_array($query) as $result) {
							if ($wID == $result['warehouse_id']) {
								$wData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['name']; ?></td>
						<td class="pageBoxContent" align="center"><?php echo tep_fetch_available_equipment_count('', $result['warehouse_id']); ?></td>
						<td class="pageBoxContent" align="center"><?php echo tep_fetch_total_equipment_count('', $result['warehouse_id']); ?></td>
						<td class="pageBoxContent" align="center"><?php echo (($result['date_last_checked'] > 0) ? date("n/d/Y", $result['date_last_checked']) : 'Never'); ?></td>
						<td class="pageBoxContent" align="center"><?php echo tep_fetch_warehouse_availability_name($result['availability']); ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_WAREHOUSES . '?wID='.$result['warehouse_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_WAREHOUSES . '?wID='.$result['warehouse_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
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
					?>
					<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_WAREHOUSES . '?page_action='.((is_numeric($wID)) ? 'edit_confirm': 'add_confirm'); ?>">
			<?php
				$edit_message = '';
				$button_value = '';
					if (is_numeric($wID)) {
						$level_query = $database->query("select w.availability, wd.name, wd.address, wd.reference_code from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = '" . $wID . "' and w.warehouse_id = wd.warehouse_id limit 1");
						$level_result = $database->fetch_array($level_query);
						$edit_message = 'Make your required changes and press "Update" below or press "Cancel" to cancel your changes.';
						$button_value = 'Update';
						?>
						<input type="hidden" name="wID" value="<?php echo $wID; ?>" />
						<?php
					} else {
						$level_result['availability'] = '0';
						$level_result['name'] = '';
						$level_result['address'] = '';
						$level_result['reference_code'] = '';

						$edit_message = 'Please enter the details below for your new Warehouse.  When you are done press the "Save" button.';
						$button_value = 'Insert';
					}
			?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<?php
								if (!empty($edit_message)) {
							?>
							<tr>
								<td class="pageBoxContent" colspan="2"><?php echo $edit_message; ?></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td class="pageBoxContent" width="150">Warehouse Name:</td><td class="pageBoxContent"><input type="text" name="name" value="<?php echo $level_result['name']; ?>" /></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Reference Code:</td><td class="pageBoxContent"><input type="text" name="reference_code" value="<?php echo $level_result['reference_code']; ?>" /></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Stock Availability:</td><td class="pageBoxContent"><?php echo tep_draw_warehouse_availability_pulldown('availability', $level_result['availability']); ?></td>
							</tr>
							<tr>
								<td colspan="2" width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="pageBoxContent">Address:</td>
										</tr>
										<tr>
											<td width="100%"><textarea name="address"><?php echo $level_result['address']; ?></textarea></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update',$button_value); ?><!--<input type="submit" value="<?php echo $button_value; ?>" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_WAREHOUSES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxContent">Are you sure you wish to delete this Warehouse?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_WAREHOUSES; ?>?wID=<?php echo $wID; ?>&page_action=delete_confirm" method="post"><?php echo tep_create_button_submit('Confirm', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form><form action="<?php echo FILENAME_ADMIN_WAREHOUSES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
							
						</table>
					<?php
					
					} else {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Warehouse Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a Warehouse or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=edit" method="post">
						<tr>
							<td height="5"><?php echo tep_create_button_submit('create', 'Create'); ?><!--<input type="submit" value="Create" />--></td>
						</tr>
						</form>
					</table>
				<?php
					}
				?>
		</td>
	</tr>
</table>
</td></tr>
</table>