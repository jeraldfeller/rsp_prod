<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$aID = tep_fill_variable('aID', 'get', tep_fill_variable('aID', 'post'));

	$message = '';
	
		if ($page_action == 'edit_confirm') {
			
			$installer_id = tep_fill_variable('installer_id');
			$zip_start = tep_fill_variable('zip_start');
			$zip_end = tep_fill_variable('zip_end');
			$installer_id_old = tep_fill_variable('installer_id_old');
			$zip_start_old = tep_fill_variable('zip_start_old');
			$zip_end_old = tep_fill_variable('zip_end_old');
				if (empty($installer_id)) {
					$error->add_error('admin_installer_assignment', 'Please select an Installer.');
				}
				if (empty($zip_start) || !tep_zip4_is_valid($zip_start)) {
					$error->add_error('admin_installer_assignment', 'Zip4 from is either empty or invalid.  Please enter a valid zip4 number.');
				}
				if (empty($zip_end) || !tep_zip4_is_valid($zip_end)) {
					$error->add_error('admin_installer_assignment', 'Zip4 to is either empty or invalid.  Please enter a valid zip4 number.');
				}
				//Now for the tricky part.  Check if things have changed and if so then can the id be assigned to an agent, this checks for overlap.
				if (!$error->get_error_status('admin_installer_assignment')) {
					if (tep_zip4_is_assigned($zip_start, $zip_end, array($installer_id, $installer_id_old), $aID)) {
						$error->add_error('admin_installer_assignment', 'That Zip range or part thereof is already assigned to another installer.');
					}
				}
				if (!$error->get_error_status('admin_installer_assignment')) {
					$zip_start_break = tep_break_zip4_code($zip_start);
					$zip_end_break = tep_break_zip4_code($zip_end);
					$database->query("update " . TABLE_INSTALLERS_TO_AREAS . " set user_id = '" . $installer_id . "', zip_4_first_break_start = '" . $zip_start_break[0] . "', zip_4_first_break_end = '" . $zip_start_break[1] . "', zip_4_second_break_start = '" . $zip_end_break[0] . "', zip_4_second_break_end = '" . $zip_end_break[1] . "' where installer_to_area_id = '" . $aID . "' limit 1");
					//echo "update " . TABLE_INSTALLERS_TO_AREAS . " set user_id = '" . $installer_id . "', zip_4_first_break_start = '" . $zip_start_break[0] . "', zip_4_first_break_end = '" . $zip_start_break[1] . "', zip_4_second_break_start = '" . $zip_end_break[0] . "', zip_4_second_break_end = '" . $zip_end_break[1] . "' where installer_to_area_id = '" . $aID . "' limit 1";
					$message = 'Agent Area Successfully Updated.';
					$page_action = '';
				} else {
					$page_action = 'edit';
				}
		} elseif ($page_action == 'add_confirm') {
			$installer_id = tep_fill_variable('installer_id');
			$zip_start = tep_fill_variable('zip_start');
			$zip_end = tep_fill_variable('zip_end');
				if (empty($installer_id)) {
					$error->add_error('admin_installer_assignment', 'Please select an Installer.');
				}
				if (empty($zip_start) || !tep_zip4_is_valid($zip_start)) {
					$error->add_error('admin_installer_assignment', 'Zip4 from is either empty or invalid.  Please enter a valid zip4 number.');
				}
				if (empty($zip_end) || !tep_zip4_is_valid($zip_end)) {
					$error->add_error('admin_installer_assignment', 'Zip4 to is either empty or invalid.  Please enter a valid zip4 number.');
				}
				if (!$error->get_error_status('admin_installer_assignment')) {
					if (tep_zip4_is_assigned($zip_start, $zip_end, array($installer_id))) {
						$error->add_error('admin_installer_assignment', 'That Zip range or part thereof is already assigned to another installer.');
					}
				}
				if (!$error->get_error_status('admin_installer_assignment')) {
					$zip_start_break = tep_break_zip4_code($zip_start);
					$zip_end_break = tep_break_zip4_code($zip_end);
					$database->query("insert into " . TABLE_INSTALLERS_TO_AREAS . " (user_id, zip_4_first_break_start, zip_4_first_break_end, zip_4_second_break_start, zip_4_second_break_end) values ('" . $installer_id . "', '" . $zip_start_break[0] . "', '" . $zip_start_break[1] . "' , '" . $zip_end_break[0] . "', '" . $zip_end_break[1] . "')");
					$message = 'Installer Assignment successfully added.';
					$page_action = '';
				} else {
					$page_action = 'add';
				}
			
			
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_INSTALLERS_TO_AREAS . " where installer_to_area_id = '" . $aID . "' limit 1");
			 $message = 'Installer Assignment successfully deleted.';
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_installer_assignment')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_installer_assignment'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
				<?php
					$listing_split = new split_page("select ud.user_id, ud.firstname, ud.lastname, ita.installer_to_area_id, ita.zip_4_first_break_start, ita.zip_4_first_break_end, ita.zip_4_second_break_start, ita.zip_4_second_break_end from " . TABLE_INSTALLERS_TO_AREAS . " ita, " . TABLE_USERS_DESCRIPTION . " ud where ita.user_id = ud.user_id order by ita.zip_4_first_break_start, ita.zip_4_second_break_start", '20', 'ita.installer_to_area_id');
							if ($listing_split->number_of_rows > 0) {
							?>
							<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
								<tr>
									<td class="pageBoxHeading">Zip4 From</td>
									<td class="pageBoxHeading" align="center">Zip4 To</td>
									<td class="pageBoxHeading" align="center">Installer Name</td>
									<td class="pageBoxHeading" align="right">Action</td>
									<td width="10" class="pageBoxHeading"></td>
								</tr>
							<?php
								$aData = array();
								$query = $database->query($listing_split->sql_query);
									while($result = $database->fetch_array($query)) {
										if ($aID == $result['installer_to_area_id']) {
											$aData = $result;
										}
										?>
											<tr>
												<td class="pageBoxContent"><?php echo $result['zip_4_first_break_start']; ?>-<?php echo $result['zip_4_first_break_end']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['zip_4_second_break_start']; ?>-<?php echo $result['zip_4_second_break_end']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['lastname']; ?>, <?php echo $result['firstname']; ?></td>
												<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT . '?aID='.$result['installer_to_area_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT . '?aID='.$result['installer_to_area_id'].'&page_action=delete'; ?>">Delete</a></td>
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
							<td class="pageBoxContent">No available associations can be found, please use the menu on the right to assign an instaler to a zip4 code.</td>
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
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Edit this Installer Assignment</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT; ?>?page_action=edit_confirm&aID=<?php echo $aID; ?>" method="post">
							<tr>
								<td class="main">Installer: </td><td><input type="hidden" name="installer_id_old" value="<?php echo $aData['user_id']; ?>" /><?php echo tep_draw_installer_pulldown('installer_id', $aData['user_id']); ?></td>
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
								<td width="100%" align="right" colspan="2"><input type="submit" value="Update"></form><form action="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
						</table>
						<?php
						}elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this Installer Assignment?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT; ?>?aID=<?php echo $aID; ?>&page_action=delete_confirm" method="post"><input type="submit" value="Delete Confirm" /></form><form action="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
							
						</table>
					<?php
						}elseif ($page_action == 'add') {
							$installer_id = tep_fill_variable('installer_id');
							$zip_start = tep_fill_variable('zip_start', 'post', 'xxxxx-xxxx');
							$zip_end = tep_fill_variable('zip_end', 'post', 'xxxxx-xxxx');
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Set a new Installer Assignment</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT; ?>?page_action=add_confirm" method="post">
							<tr>
								<td class="main">Installer: </td><td><?php echo tep_draw_installer_pulldown('installer_id', $installer_id, array(array('id' => '', 'name' => 'Please Select'))); ?></td>
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
								<td width="100%" align="right" colspan="2"><?php echo tep_create_button_submit('create', 'Create'); ?></form><form action="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
						</table>
					<?php
					} else {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Installer Assignment Options</b></td>
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
							<td height="5"><input type="submit" value="Create" /></td>
						</tr>
						</form>
					</table>
				<?php
					}
				?>
		</td>
	</tr>
</table>