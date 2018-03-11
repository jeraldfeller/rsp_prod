<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$pID = tep_fill_variable('pID', 'get', tep_fill_variable('pID', 'post'));

	$message = '';
	
		if (($page_action == 'edit_confirm') || ($page_action == 'add_confirm')) {
			$name = tep_fill_variable('name', 'post');
			$agent_preference_group_id = tep_fill_variable('agent_preference_group_id', 'post');
				if (empty($name)) {
					$error->add_error('admin_preferences', 'You must enter at least a name for this group.');
					$page_action = 'edit';
				} else {
					if ($page_action == 'edit_confirm') {
						$database->query("update " . TABLE_AGENT_PREFERENCES . " set name = '" . $name . "', agent_preference_group_id = '" . $agent_preference_group_id . "' where agent_preference_id = '" . $pID . "' limit 1");
						$message = 'Agent Preference successfully updated.';
					} else {
						$database->query("insert into " . TABLE_AGENT_PREFERENCES . " (name, agent_preference_group_id) values ('" . $name . "', '" . $agent_preference_group_id . "')");
						$message = 'Agent Preference successfully added.';
					}
					
				}
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_AGENT_PREFERENCES . " where agent_preference_id = '" . $pID . "' limit 1");
			$pID = '';
			$page_action = '';
		} elseif ($page_action == 'delete') {

		} elseif ($page_action == 'edit') {
				if (is_numeric($pID)) {
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
		if ($error->get_error_status('admin_preferences')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_preferences'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Preference Name</td>
						<td class="pageBoxHeading" align="center">Preference Group Name</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$egData = array();
					$query = $database->query("select e.agent_preference_id, e.name as agent_preference_name, eg.name as agent_preference_group_name from " . TABLE_AGENT_PREFERENCES . " e, " . TABLE_AGENT_PREFERENCE_GROUPS . " eg where e.agent_preference_group_id = eg.agent_preference_group_id order by e.name");
						while($result = $database->fetch_array($query)) {
							if ($pID == $result['agent_preference_id']) {
								$egData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['agent_preference_name']; ?></td>
						<td class="pageBoxContent" align="center"><?php echo $result['agent_preference_group_name']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_PREFERENCES . '?pID='.$result['agent_preference_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_PREFERENCES . '?pID='.$result['agent_preference_id'].'&page_action=delete'; ?>">Delete</a></td>
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
							if (is_numeric($pID)) {
								$query = $database->query("select name, agent_preference_group_id from " . TABLE_AGENT_PREFERENCES . " where agent_preference_id = '" . $pID . "' limit 1");
								$result = $database->fetch_array($query);
							} else {
								$result = array('name' => '',
														 'agent_preference_group_id' => '');
							}
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_PREFERENCES . '?page_action='.((is_numeric($pID)) ? ('edit_confirm&pID='.$pID) : 'add_confirm'); ?>">
							<tr>
								<td class="pageBoxContent" colspan="2"><?php echo $edit_message; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">Name</td><td><input type="text" name="name" value="<?php echo $result['name']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Preference Group</td><td><?php echo tep_draw_preference_group_pulldown('agent_preference_group_id', $result['agent_preference_group_id']); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2"><input type="submit" value="<?php echo $button_value; ?>" name="submit_value"></form><form action="<?php echo FILENAME_ADMIN_PREFERENCES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
							</tr>
						</table>
						<?php
						}elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this Agent Preference?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_PREFERENCES; ?>?pID=<?php echo $pID; ?>&page_action=delete_confirm" method="post"><input type="submit" value="Delete Confirm" /></form><form action="<?php echo FILENAME_ADMIN_PREFERENCES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
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
							<td class="pageBoxContent">Click edit to edit an Agent Preference or press Create to create a new one.</td>
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