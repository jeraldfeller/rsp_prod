<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$apID = tep_fill_variable('apID', 'get', tep_fill_variable('apID', 'post'));

	$message = '';
	
		if ($page_action == 'edit_confirm') {
			$name = tep_fill_variable('name', 'post');
			$selectable = tep_fill_variable('selectable', 'post');
				if (empty($name)) {
					$error->add_error('admin_preference_groups', 'You must enter at least a name for this group.');
					$page_action = 'edit';
				} else {
					$database->query("update " . TABLE_AGENT_PREFERENCE_GROUPS . " set name = '" . $name . "', selectable = '" . $selectable . "' where agent_preference_group_id = '" . $apID . "' limit 1");
					$message = 'Agent Preference Group Successfully Updated.';
				}
		} elseif ($page_action == 'add_confirm') {
			$name = tep_fill_variable('name', 'post');
			$selectable = tep_fill_variable('selectable', 'post');
				if (empty($name)) {
					$error->add_error('admin_preference_groups', 'You must enter at least a name for this group.');
					$page_action = 'edit';
				} else {
					$database->query("insert into " . TABLE_AGENT_PREFERENCE_GROUPS . " (name, selectable) values ('" . $name . "', '" . $selectable . "')");
					$message = 'Language Successfully Updated.';
				}
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_AGENT_PREFERENCE_GROUPS . " where agent_preference_group_id = '" . $apID . "' limit 1");
			
			$apID = '';
			$page_action = '';
		} elseif ($page_action == 'delete') {
			if (tep_count_preferences_in_group($apID) != 0) {
				$error->add_error('admin_preference_groups', 'This group still has preferences assigned to it.  You can not delete it.');
				$page_action = '';
			}
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_preference_groups')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_preference_groups'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
		<?php
			if (($page_action != 'edit')) {
		?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Group Name</td>
						<td class="pageBoxHeading" align="center">Items</td>
						<td class="pageBoxHeading" align="center">Selectable</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$pgData = array();
					$query = $database->query("select agent_preference_group_id, name, selectable from " . TABLE_AGENT_PREFERENCE_GROUPS . " order by name");
						while($result = $database->fetch_array($query)) {
							if ($apID == $result['agent_preference_group_id']) {
								$pgData = $result;
							}
							$count_query = $database->query("select count(agent_preference_id) as count from " . TABLE_AGENT_PREFERENCES . " where agent_preference_group_id = '" . $result['agent_preference_group_id'] . "'");
							$count_result = $database->fetch_array($count_query);
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['name']; ?></td>
						<td class="pageBoxContent" align="center"><?php echo $count_result['count']; ?></td>
						<td class="pageBoxContent" align="center"><?php echo $result['selectable']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_PREFERENCE_GROUPS . '?apID='.$result['agent_preference_group_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_PREFERENCE_GROUPS . '?apID='.$result['agent_preference_group_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
					?>
				</table>
			<?php
				} else {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_PREFERENCE_GROUPS . '?page_action='.((is_numeric($apID)) ? 'edit_confirm': 'add_confirm'); ?>">
			<?php
				$edit_message = '';
				$button_value = '';
					if (is_numeric($apID)) {
						$level_query = $database->query("select name, selectable from " . TABLE_AGENT_PREFERENCE_GROUPS . " where agent_preference_group_id = '" . $apID . "' limit 1");
						$level_result = $database->fetch_array($level_query);
						$edit_message = 'Make your required changes and press "Update" below or press "Cancel" to cancel your changes.';
						$button_value = 'Update';
						?>
						<input type="hidden" name="apID" value="<?php echo $apID; ?>" />
						<?php
					} else {
						$level_result['name'] = '';
						$level_result['selectable'] = '';
						$edit_message = 'Please enter the details below for your new Agent Preference Group.  When you are done press the "Save" button.';
						$button_value = 'Save';
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
						<td class="pageBoxContent" width="150">Agent Preference Group Name:</td><td class="pageBoxContent"><input type="text" name="name" value="<?php echo $level_result['name']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Selectable:</td><td class="pageBoxContent"><input type="text" name="selectable" value="<?php echo $level_result['selectable']; ?>" />  <i>(The number of items in this group that can be selected in one go, leave blank for no limit)</i></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
				</table>
			<?php
				}
			?>
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
								<td class="pageBoxContent"><?php echo $edit_message; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><input type="submit" value="<?php echo $button_value; ?>" name="submit_value"></form><form action="<?php echo FILENAME_ADMIN_PREFERENCE_GROUPS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
							</tr>
							
						</table>
						<?php
						}elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this Agent Preference Group?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_PREFERENCE_GROUPS; ?>?apID=<?php echo $apID; ?>&page_action=delete_confirm" method="post"><input type="submit" value="Delete Confirm" /></form><form action="<?php echo FILENAME_ADMIN_PREFERENCE_GROUPS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
							</tr>
							
						</table>
					<?php
					} else {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Agent Preference Group Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a Group or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=edit" method="post">
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