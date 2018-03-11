<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$eID = tep_fill_variable('eID', 'get', tep_fill_variable('eID', 'post'));
	$submit_value = tep_fill_variable('submit_value_y');
	$show_equipment_type_id = tep_fill_variable('show_equipment_type_id', 'get');
	$show_service_level_id = tep_fill_variable('show_service_level_id', 'get');
	
	$message = '';
	
		if (($page_action == 'edit_confirm') || ($page_action == 'add_confirm')) {
			$run = true;
		
				if  ($submit_value == '') {
				//	$page_action = 'edit';
					//$run = false;
				}
				if ($run) {
					$name = tep_fill_variable('name', 'post');
					$service_level_id = tep_fill_variable('service_level_id', 'post');
					$install_equipment_id = tep_fill_variable('install_equipment_id', 'post');
					$remove_equipment_id = tep_fill_variable('remove_equipment_id', 'post');
					$equipment_group_id = tep_fill_variable('equipment_group_id', 'post');
					$checked = tep_fill_variable('checked', 'post');
						if (empty($name)) {
							$error->add_error('admin_equipment', 'You must enter at least a name for this answer.');
							$page_action = 'edit';
						} else {
							if ($page_action == 'edit_confirm') {
								$database->query("update " . TABLE_EQUIPMENT_GROUP_ANSWERS . " set name = '" . $name . "', equipment_group_id = '" . $equipment_group_id . "', service_level_id = '" . $service_level_id . "', install_equipment_id = '" . $install_equipment_id . "', remove_equipment_id = '" . $remove_equipment_id . "', checked = '" . $checked . "' where equipment_group_answer_id = '" . $eID . "' limit 1");
								$message = 'Answer successfully updated.';
							} else {
								$database->query("insert into " . TABLE_EQUIPMENT_GROUP_ANSWERS . " (name, equipment_group_id, service_level_id, install_equipment_id, remove_equipment_id, checked) values ('" . $name . "', '" . $equipment_group_id . "', '" . $service_level_id . "', '" . $install_equipment_id . "', '" . $remove_equipment_id . "', '" . $checked . "')");
								$message = 'Answer successfully added.';
							}
							
						}
				}
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_answer_id = '" . $eID . "' limit 1");
			$eID = '';
			$page_action = '';
		} elseif ($page_action == 'delete') {

		}
		if ($page_action == 'edit') {
				if (is_numeric($eID)) {
					$edit_message = 'Please make the required changes and press "Update" to confirm.';
					$button_value = 'Update';
				} else {
					$edit_message = 'Please enter the required details and press "Insert" to insert it into the database.';
					$button_value = 'Insert';
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
	<tr>
		<td width="100%" valign="top">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Equipment Answer</td>
						<td class="pageBoxHeading" align="center">Service Level</td>
						<td class="pageBoxHeading" align="center">Install</td>
						<td class="pageBoxHeading" align="center">Remove</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$egData = array();
					$query = $database->query("select ega.equipment_group_answer_id, ega.name, ega.service_level_id, ega.install_equipment_id, ega.remove_equipment_id from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " ega, " . TABLE_EQUIPMENT . " e where ega.install_equipment_id = e.equipment_id" . ((!empty($show_service_level_id)) ? " and ega.service_level_id = '" . (int)$show_service_level_id . "'" : '') . "" . ((!empty($show_equipment_type_id)) ? " and e.equipment_type_id = '" . (int)$show_equipment_type_id . "'" : '') . " order by e.name");
						foreach($database->fetch_array($query) as $result){
							if ($eID == $result['equipment_group_answer_id']) {
								$egData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo stripslashes($result['name']); ?></td>
						<td class="pageBoxContent" align="center"><?php echo tep_get_service_level_name($result['service_level_id']); ?></td>
						<td class="pageBoxContent" align="center"><?php echo tep_get_equipment_name($result['install_equipment_id']); ?></td>
						<td class="pageBoxContent" align="center"><?php echo tep_get_equipment_name($result['remove_equipment_id']); ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUP_ANSWERS . tep_get_all_get_params(array('eID', 'action', 'page_action')) . '?eID='.$result['equipment_group_answer_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUP_ANSWERS . tep_get_all_get_params(array('eID', 'action', 'page_action')) . 'eID='.$result['equipment_group_answer_id'].'&page_action=delete'; ?>">Delete</a></td>
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
							if (is_numeric($eID)) {
								$query = $database->query("select name, equipment_group_id, service_level_id, install_equipment_id, remove_equipment_id, checked from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_answer_id = '" . $eID . "' limit 1");
								$result = $database->fetch_array($query);
							} else {
								$result = array('name' => '',
														 'service_level_id' => array(),
														 'install_equipment_id' => '',
														 'equipment_group_id' => '',
														 'remove_equipment_id' => '',
														 'checked' => '0');
							}
							//Set the values.  Do it here again in case there was a failed submit.
							//$total = tep_fill_variable('total', 'post', $result['total']);
							$name = tep_fill_variable('name', 'post', $result['name']);
							$service_level_id = tep_fill_variable('service_level_id', 'post', $result['service_level_id']);
							$install_equipment_id = tep_fill_variable('install_equipment_id', 'post', $result['install_equipment_id']);
							$remove_equipment_id = tep_fill_variable('remove_equipment_id', 'post', $result['remove_equipment_id']);
							$equipment_group_id = tep_fill_variable('equipment_group_id', 'post', $result['equipment_group_id']);
							$checked = tep_fill_variable('checked', 'post', $result['checked']);

					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUP_ANSWERS . '?page_action='.((is_numeric($eID)) ? ('edit_confirm&eID='.$eID) : 'add_confirm'); ?>">
							<tr>
								<td class="pageBoxContent" colspan="2"><?php echo $edit_message; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">Name</td><td><input type="text" name="name" value="<?php echo stripslashes($name); ?>" /></td>
							</tr>
							<tr>
								<td class="main">Service Level</td><td><?php echo tep_draw_service_level_pulldown('service_level_id', $service_level_id, '', false); ?></td>
							</tr>
							<tr>
								<td class="main">Question</td><td><?php echo tep_draw_equipment_group_pulldown('equipment_group_id', $equipment_group_id, '', false); ?></td>
							</tr>
							<tr>
								<td class="main">Install Equipment</td><td><?php echo tep_draw_equipment_pulldown('install_equipment_id', $install_equipment_id, '', '', array(array('id' => '', 'name' => 'None'))); ?></td>
							</tr>
							<tr>
								<td class="main">Remove Equipment</td><td><?php echo tep_draw_equipment_pulldown('remove_equipment_id', $remove_equipment_id, '', '', array(array('id' => '', 'name' => 'None'))); ?></td>
							</tr>
							<tr>
								<td class="main">Default Checked</td><td><input type="checkbox" name="checked" value="1"<?php echo (($checked == '1') ? ' CHECKED' : ''); ?> /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?if($button_value=='Update'){?><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?><?}else if($button_value=='Insert'){?><?php echo tep_create_button_submit('Insert', 'Insert', ' name="submit_value"'); ?><?}?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUP_ANSWERS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxContent">Are you sure you wish to delete this Answer?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUP_ANSWERS; ?>?eID=<?php echo $eID; ?>&page_action=delete_confirm" method="post"><?php echo tep_create_button_submit('delete', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUP_ANSWERS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
							
						</table>
					<?php
					} else {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Answer Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit an Optional Answer or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>" method="get">
						<tr>
							<td class="pageBoxContent">Show only: <?php echo tep_generate_equipment_type_pulldown_menu('show_equipment_type_id', $show_equipment_type_id, array(array('id' => '', 'name' => 'Any')), ' onchange="this.form.submit();"'); ?></td>
						</tr>
						<tr>
							<td class="main">Show only Level: <?php echo tep_draw_service_level_pulldown('show_service_level_id', $show_service_level_id, 'onchange="this.form.submit();"', false, array(array('id' => '', 'name' => 'Any')), false); ?></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						
						<tr>
							<td width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></td>
										</form>
										<form action="<?php echo PAGE_URL; ?>?page_action=edit" method="post">
										<td height="5"><?php echo tep_create_button_submit('create', 'Create'); ?><!--<input type="submit" value="Create" />--></td>
										</form>
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
		</td>
	</tr>
</table>