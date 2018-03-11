<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$slID = tep_fill_variable('slID', 'get', tep_fill_variable('slID', 'post'));
	$submit_value = tep_fill_variable('submit_value_y', 'post');

	$message = '';
	
	$language_id = tep_fill_variable('language_id', 'post', tep_get_default_language());
		
	$language = tep_get_language_code($language_id);
	
		if (!empty($submit_value)) {
			
			$name = tep_fill_variable('name', 'post');
			$cost = tep_fill_variable('cost', 'post');
			$description = tep_fill_variable('description', 'post');
			$default_install_preferences = tep_fill_variable('default_install_preferences', 'post');
			$default_service_call_preferences = tep_fill_variable('default_service_call_preferences', 'post');
			$default_removal_preferences = tep_fill_variable('default_removal_preferences', 'post');
			
			if (is_numeric($slID)) {
				$database->query("update " . TABLE_SERVICE_LEVELS . " set cost = '" . $cost . "' where service_level_id = '" . $slID . "' limit 1");
				$database->query("update " . TABLE_SERVICE_LEVELS_DESCRIPTION . " set name = '" . $name . "', description = '" . $description . "', default_install_preferences = '" . $default_install_preferences . "', default_service_call_preferences = '" . $default_service_call_preferences . "', default_removal_preferences = '" . $default_removal_preferences . "' where service_level_id = '" . $slID . "' and language_id = '" . $language_id . "' limit 1");
				
				$message = 'Service Level successfully updated.';
			} else {
				$database->query("insert into " . TABLE_SERVICE_LEVELS . " (cost) values ('" . $cost . "')");
				$slID = $database->insert_id();
				$database->query("insert into " . TABLE_SERVICE_LEVELS_DESCRIPTION . " (service_level_id, name, description, language_id, default_install_preferences, default_service_call_preferences, default_removal_preferences) values ('" . $slID . "', '" . $name . "', '" . $description . "', '" . $language_id . "', '" . $default_install_preferences . "', '" . $default_service_call_preferences . "', '" . $default_removal_preferences . "')");
				
				$message = 'Service Level successfully created.';
			}
			$page_action = '';
			
		}
		if ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_SERVICE_LEVELS . " where service_level_id = '" . $slID . "' limit 1");
			$database->query("delete from " . TABLE_SERVICE_LEVELS_DESCRIPTION . " where service_level_id = '" . $slID . "' limit 1");
			
			$message = 'Service Level successfully deleted.';
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_service_levels')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_service_levels'); ?></td>
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
						<td class="pageBoxHeading">Service Level Name</td>
						<td class="pageBoxHeading" align="center">Price</td>
						<td class="pageBoxHeading" align="center">Associated Users</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$uData = array();
					$query = $database->query("select sl.service_level_id, sl.cost, sld.name from " . TABLE_SERVICE_LEVELS . " sl, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where sl.service_level_id = sld.service_level_id and sld.language_id = '1' order by sl.service_level_id");
						foreach($database->fetch_array($query) as $result){
							$count_query = $database->query("select count(user_id) as count from " . TABLE_USERS . " where service_level_id = '" . $result['service_level_id'] . "'");
							$count_result = $database->fetch_array($count_query);
							
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['name']; ?></td>
						<td class="pageBoxContent" align="center">$<?php echo number_format($result['cost'], 2); ?></td>
						<td class="pageBoxContent" align="center"><?php echo $count_result['count']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_SERVICE_LEVELS . '?slID='.$result['service_level_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_SERVICE_LEVELS . '?slID='.$result['service_level_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
					?>
				</table>
			<?php
				} else {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_SERVICE_LEVELS . '?page_action=edit'; ?>">
			<?php
				$edit_message = '';
				$button_value = '';
					if (is_numeric($slID)) {
						$level_query = $database->query("select sl.cost, sld.name, sld.description, sld.default_install_preferences, sld.default_service_call_preferences, sld.default_removal_preferences from " . TABLE_SERVICE_LEVELS . " sl, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where sl.service_level_id = sld.service_level_id and sl.service_level_id = '" . $slID . "' and sld.language_id = '" . $language_id . "' limit 1");
						$level_result = $database->fetch_array($level_query);

						$edit_message = 'Make your required changes and press "Update" below or press "Cancel" to cancel your changes.';
						$button_value = 'Update';
						?>
						<input type="hidden" name="slID" value="<?php echo $slID; ?>" />
						<?php
					} else {
						$level_result['name'] = '';
						$level_result['cost'] = 0;
						$level_result['description'] = '';
						$level_result['default_install_preferences'] = '';
						$level_result['default_service_call_preferences'] = '';
						$level_result['default_removal_preferences'] = '';
						$edit_message = 'Please enter the details below for your new service level.  When you are done press the "Save" button.';
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
						<td class="pageBoxContent" width="130">Service Level Name: </td><td class="pageBoxContent"><input type="text" name="name" value="<?php echo $level_result['name']; ?>" />  <i>(This shows up on all pages)</i></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Service Level Cost: </td><td class="pageBoxContent"><input type="text" name="cost" value="<?php echo $level_result['cost']; ?>" />  <i>(The cost used for this service level, does not modify existing orders)</i></td>
					</tr>
					<tr>
						<td class="main" valign="top">Default Installation Preferences: </td>
						<td><textarea name="default_install_preferences"><?php echo $level_result['default_install_preferences']; ?></textarea></td>
					</tr>
					<tr>
						<td class="main" valign="top">Default Service Call Preferences: </td>
						<td><textarea name="default_service_call_preferences"><?php echo $level_result['default_service_call_preferences']; ?></textarea></td>
					</tr>
					<tr>
						<td class="main" valign="top">Default Removal Preferences: </td>
						<td><textarea name="default_removal_preferences"><?php echo $level_result['default_removal_preferences']; ?></textarea></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2">Service Level Description: <i>(The description used on the service levels page)</i></td>
					</tr>
					<tr>
						<td colspan="2"><?php
						$sBasePath = 'editor/';
						//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;
						$oFCKeditor = new FCKeditor('description') ;
						$oFCKeditor->BasePath = $sBasePath ;
						$oFCKeditor->Value	= $level_result['description'];
						$oFCKeditor->Height	= '400';
						$oFCKeditor->Create() ;
						?></td>
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
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?><!--<input type="submit" value="<?php /*echo $button_value;*/ ?>" name="submit_value">--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_SERVICE_LEVELS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxContent">Are you sure you wish to delete this service level?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_SERVICE_LEVELS; ?>?slID=<?php echo $slID; ?>&page_action=delete_confirm" method="post"><input type="submit" value="Delete Confirm" /></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_SERVICE_LEVELS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
					<td class="pageBoxHeading"><b>Service Level Options</b></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td class="pageBoxContent">Click edit to edit a Service Level or press Create to create a new Level.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>?page_action=edit" method="post">
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