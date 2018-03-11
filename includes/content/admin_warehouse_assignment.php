<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$aID = tep_fill_variable('aID', 'get', tep_fill_variable('aID', 'post'));

	$message = '';
	
		if ($page_action == 'edit_confirm') {
			
			$warehouse_id = tep_fill_variable('warehouse_id');
			$zip_start = tep_fill_variable('zip_start');
			$zip_end = tep_fill_variable('zip_end');
			$warehouse_id_old = tep_fill_variable('warehouse_id_old');
			$zip_start_old = tep_fill_variable('zip_start_old');
			$zip_end_old = tep_fill_variable('zip_end_old');
				if (empty($warehouse_id)) {
					$error->add_error('admin_warehouse_assignment', 'Please select a Warehouse.');
				}
				if (empty($zip_start) || !tep_zip4_is_valid($zip_start)) {
					$error->add_error('admin_warehouse_assignment', 'Zip4 from is either empty or invalid.  Please enter a valid zip4 number.');
				}
				if (empty($zip_end) || !tep_zip4_is_valid($zip_end)) {
					$error->add_error('admin_warehouse_assignment', 'Zip4 to is either empty or invalid.  Please enter a valid zip4 number.');
				}
				if (!$error->get_error_status('admin_warehouse_assignment')) {
					$zip_start_break = tep_break_zip4_code($zip_start);
					$zip_end_break = tep_break_zip4_code($zip_end);
					$database->query("update " . TABLE_WAREHOUSES_TO_AREAS . " set warehouse_id = '" . $warehouse_id . "', zip_4_first_break_start = '" . $zip_start_break[0] . "', zip_4_first_break_end = '" . $zip_start_break[1] . "', zip_4_second_break_start = '" . $zip_end_break[0] . "', zip_4_second_break_end = '" . $zip_end_break[1] . "' where warehouse_to_area_id = '" . $aID . "' limit 1");
					//echo "update " . TABLE_WAREHOUSES_TO_AREAS . " set warehouse_id = '" . $warehouse_id . "', zip_4_first_break_start = '" . $zip_start_break[0] . "', zip_4_first_break_end = '" . $zip_start_break[1] . "', zip_4_second_break_start = '" . $zip_end_break[0] . "', zip_4_second_break_end = '" . $zip_end_break[1] . "' where warehouse_to_area_id = '" . $aID . "' limit 1";
					$message = 'Agent Area Successfully Updated.';
					$page_action = '';
				} else {
					$page_action = 'edit';
				}
		} elseif ($page_action == 'add_confirm') {
			$warehouse_id = tep_fill_variable('warehouse_id');
			$zip_start = tep_fill_variable('zip_start');
			$zip_end = tep_fill_variable('zip_end');
				if (empty($warehouse_id)) {
					$error->add_error('admin_warehouse_assignment', 'Please select an Warehouse.');
				}
				if (empty($zip_start) || !tep_zip4_is_valid($zip_start)) {
					$error->add_error('admin_warehouse_assignment', 'Zip4 from is either empty or invalid.  Please enter a valid zip4 number.');
				}
				if (empty($zip_end) || !tep_zip4_is_valid($zip_end)) {
					$error->add_error('admin_warehouse_assignment', 'Zip4 to is either empty or invalid.  Please enter a valid zip4 number.');
				}
				if (!$error->get_error_status('admin_warehouse_assignment')) {
					$zip_start_break = tep_break_zip4_code($zip_start);
					$zip_end_break = tep_break_zip4_code($zip_end);
					$database->query("insert into " . TABLE_WAREHOUSES_TO_AREAS . " (warehouse_id, zip_4_first_break_start, zip_4_first_break_end, zip_4_second_break_start, zip_4_second_break_end) values ('" . $warehouse_id . "', '" . $zip_start_break[0] . "', '" . $zip_start_break[1] . "' , '" . $zip_end_break[0] . "', '" . $zip_end_break[1] . "')");
					$message = 'Installer Assignment successfully added.';
					$page_action = '';
				} else {
					$page_action = 'add';
				}
			
			
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_WAREHOUSES_TO_AREAS . " where warehouse_to_area_id = '" . $aID . "' limit 1");
			 $message = 'Installer Assignment successfully deleted.';
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_warehouse_assignment')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_warehouse_assignment'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
				<tr>
					<td class="pageBoxHeading" align="center">Warehouse Name</td>
					<td class="pageBoxHeading">Zip4 From</td>
					<td class="pageBoxHeading" align="center">Zip4 To</td>
					<td class="pageBoxHeading" align="right">Action</td>
					<td width="10" class="pageBoxHeading"></td>
				</tr>
				<?php
					$aData = array();
					$query = $database->query("select warehouse_id, name from " . TABLE_WAREHOUSES_DESCRIPTION . " order by name");
					    foreach($database->fetch_array($query) as $result){
							$range_count = 0;
							$from_string = '';
							$to_string = '';
							$action_string = '';
							$range_query = $database->query("select warehouse_to_area_id, zip_4_first_break_start, zip_4_first_break_end, zip_4_second_break_start, zip_4_second_break_end from " . TABLE_WAREHOUSES_TO_AREAS . " where warehouse_id = '" . $result['warehouse_id'] . "' order by zip_4_first_break_start, zip_4_second_break_start");
								foreach($database->fetch_array($range_query) as $range_result){
										if ($aID == $range_result['warehouse_to_area_id']) {
											$aData = array_merge($result, $range_result);
											
										}
										if ($range_count > 0) {
											$from_string.='<br>';
											$to_string.='<br>';
											$action_string.='<br>';
										}
									$from_string .= $range_result['zip_4_first_break_start'].'-'. $range_result['zip_4_first_break_end'];
									$to_string .= $range_result['zip_4_second_break_start'].'-'. $range_result['zip_4_second_break_end'];
									$action_string .= '<a href="'. FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT . '?aID='.$range_result['warehouse_to_area_id'].'&page_action=edit">Edit</a> | <a href="'.FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT . '?aID='.$range_result['warehouse_to_area_id'].'&page_action=delete">Delete</a>';
									
									$range_count++;
								}
								
							?>
							<tr>
								<td class="pageBoxContent" valign="top"><?php echo $result['name']; ?></td>
								<?php
									if ($range_count > 0) {
								?>
								<td class="pageBoxContent" align="center"><?php echo $from_string; ?></td>
								<td class="pageBoxContent" align="center"><?php echo $to_string; ?></td>
								<td class="pageBoxContent" align="center"><?php echo $action_string; ?></td>
								<?php
									} else {
								?>
								<td class="pageBoxContent" align="center" colspan="2">No ranges assigned for this warehouse.</td>
								<td class="pageBoxContent" align="center"></td>
								<?php
									}
								?>
								<td width="10" class="pageBoxContent"></td>
							</tr>
							<?php
						}
				/*
					$listing_split = new split_page("select wd.warehouse_id, wd.name, wta.warehouse_to_area_id, wta.zip_4_first_break_start, wta.zip_4_first_break_end, wta.zip_4_second_break_start, wta.zip_4_second_break_end from " . TABLE_WAREHOUSES_TO_AREAS . " wta, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where wta.warehouse_id = wd.warehouse_id order by wta.zip_4_first_break_start, wta.zip_4_second_break_start", '20', 'wta.warehouse_to_area_id');
							if ($listing_split->number_of_rows > 0) {
							?>
							<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
								<tr>
									<td class="pageBoxHeading">Zip4 From</td>
									<td class="pageBoxHeading" align="center">Zip4 To</td>
									<td class="pageBoxHeading" align="center">Warehouse Name</td>
									<td class="pageBoxHeading" align="right">Action</td>
									<td width="10" class="pageBoxHeading"></td>
								</tr>
							<?php
								$aData = array();
								$query = $database->query($listing_split->sql_query);
									while($result = $database->fetch_array($query)) {
										if ($aID == $result['warehouse_to_area_id']) {
											$aData = $result;
										}
										?>
											<tr>
												<td class="pageBoxContent"><?php echo $result['zip_4_first_break_start']; ?>-<?php echo $result['zip_4_first_break_end']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['zip_4_second_break_start']; ?>-<?php echo $result['zip_4_second_break_end']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['name']; ?></td>
												<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT . '?aID='.$result['warehouse_to_area_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT . '?aID='.$result['warehouse_to_area_id'].'&page_action=delete'; ?>">Delete</a></td>
												<td width="10" class="pageBoxContent"></td>
											</tr>
										<?php
										}
										?>
										<tr>
											<td colspan="5">
												<table class="normaltable" cellspacing="0" cellpadding="2">
													<tr>
														<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> assignments)'); ?></td>
														<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
													</tr>
												</table>
											</td>
										</tr>
										<?php
						} else {
					?>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="pageBoxContent">No available associations can be found, please use the menu on the right to assign a Warehouse to a zip4 code.</td>
						</tr>
					<?php
						}
						*/
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
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Edit this Warehouse Assignment</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT; ?>?page_action=edit_confirm&aID=<?php echo $aID; ?>" method="post">
							<tr>
								<td class="main">Warehouse: </td><td><input type="hidden" name="warehouse_id_old" value="<?php echo $aData['warehouse_id']; ?>" /><?php echo tep_draw_warehouse_pulldown('warehouse_id', $aData['warehouse_id']); ?></td>
							</tr>
							<tr>
								<td class="main">Zip4 Start: </td><td class="main"><input type="hidden" name="zip_start_old" value="<?php echo $aData['zip_4_first_break_start']; ?>-<?php echo $aData['zip_4_first_break_end']; ?>" /><input type="text" name="zip_start" value="<?php echo $aData['zip_4_first_break_start']; ?>-<?php echo $aData['zip_4_first_break_end']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Zip4 End: </td><td class="main"><input type="hidden" name="zip_end_old" value="<?php echo $aData['zip_4_second_break_start']; ?>-<?php echo $aData['zip_4_second_break_end']; ?>" /><input type="text" name="zip_end" value="<?php echo $aData['zip_4_second_break_start']; ?>-<?php echo $aData['zip_4_second_break_end']; ?>" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>

							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update">--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxContent">Are you sure you wish to delete this Warehouse Assignment?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT; ?>?aID=<?php echo $aID; ?>&page_action=delete_confirm" method="post"><?php echo tep_create_button_submit('delete', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
						}elseif ($page_action == 'add') {
							$warehouse_id = tep_fill_variable('warehouse_id');
							$zip_start = tep_fill_variable('zip_start', 'post', 'xxxxx-xxxx');
							$zip_end = tep_fill_variable('zip_end', 'post', 'xxxxx-xxxx');
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Set a new Warehouse Assignment</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT; ?>?page_action=add_confirm" method="post">
							<tr>
								<td class="main">Warehouse: </td><td><?php echo tep_draw_warehouse_pulldown('warehouse_id', $warehouse_id, array(array('id' => '', 'name' => 'Please Select'))); ?></td>
							</tr>
							<tr>
								<td class="main">Zip4 Start: </td><td class="main"><input type="text" name="zip_start" value="<?php echo $zip_start; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Zip4 End: </td><td class="main"><input type="text" name="zip_end" value="<?php echo $zip_end; ?>" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('create', 'Create'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_WAREHOUSE_ASSIGNMENT; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
							<td class="pageBoxHeading"><b>Warehouse Assignment Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit an Assignment or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=add" method="post">
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
		</td>
	</tr>
</table>
