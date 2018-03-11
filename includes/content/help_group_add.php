<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$hgID = tep_fill_variable('hgID', 'get', tep_fill_variable('hgID', 'post'));
	$submit_value = tep_fill_variable('submit_value', 'post');

	$message = '';
	
	$language_id = tep_fill_variable('language_id', 'post', tep_get_default_language());
		
	$language = tep_get_language_code($language_id);
	
		if (!empty($submit_value)) {
			
			$help_group_name = tep_fill_variable('help_group_name', 'post');
			$help_group_description = tep_fill_variable('help_group_description', 'post');
			
			if (is_numeric($hgID)) {
				$database->query("update " . TABLE_HELP_GROUPS_DESCRIPTION . " set help_group_name = '" . $help_group_name . "', help_group_description = '" . $help_group_description . "' where help_group_id = '" . $hgID . "' and language_id = '" . $language_id . "' limit 1");
				
				$message = 'Help Group successfully updated.';
			} else {
				$database->query("insert into " . TABLE_HELP_GROUPS . " (date_added) values ('" . mktime() . "')");
				$hgID = $database->insert_id();
				$database->query("insert into " . TABLE_HELP_GROUPS_DESCRIPTION . " (help_group_id, help_group_name, help_group_description, language_id) values ('" . $hgID . "', '" . $help_group_name . "', '" . $help_group_description . "', '" . $language_id . "')");
				
				$message = 'Help Group successfully created.';
			}
			$page_action = '';
			
		}
		if ($page_action == 'delete') {
			$query = $database->query("select count(help_item_id) as count from " . TABLE_HELP_ITEMS . " where help_group_id = '" . $hgID . "'");
			$result = $database->fetch_array($query);
				if ($result['count'] == 0) {
					$can_delete = true;
				} else {
					$can_delete = false;
				}
		}
		if ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_HELP_GROUPS . " where help_group_id = '" . $hgID . "' limit 1");
			$database->query("delete from " . TABLE_HELP_GROUPS_DESCRIPTION . " where help_group_id = '" . $hgID . "' limit 1");
			
			$message = 'Help Group successfully deleted.';
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
						<td class="pageBoxHeading">Help Group Name</td>
						<td class="pageBoxHeading" align="center">Assoicated Help Items</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$uData = array();
					$query = $database->query("select hg.help_group_id, hgd.help_group_name, hgd.help_group_description from " . TABLE_HELP_GROUPS . " hg, " . TABLE_HELP_GROUPS_DESCRIPTION . " hgd where hg.help_group_id = hgd.help_group_id and hgd.language_id = '1' order by hgd.help_group_name");
						foreach($database->fetch_array($query) as $result){
							$count_query = $database->query("select count(help_item_id) as count from " . TABLE_HELP_ITEMS . " where help_group_id = '" . $result['help_group_id'] . "'");
							$count_result = $database->fetch_array($count_query);
							
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['help_group_name']; ?></td>
						<td class="pageBoxContent" align="center"><?php echo $count_result['count']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_HELP_GROUP_ADD . '?hgID='.$result['help_group_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_HELP_GROUP_ADD . '?hgID='.$result['help_group_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
					?>
				</table>
			<?php
				} else {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_HELP_GROUP_ADD . '?page_action=edit'; ?>">
			<input type="hidden" name="submit_value" value="1" />
			<?php
				$edit_message = '';
				$button_value = '';
					if (is_numeric($hgID)) {
						$level_query = $database->query("select hg.help_group_id, hgd.help_group_name, hgd.help_group_description from " . TABLE_HELP_GROUPS . " hg, " . TABLE_HELP_GROUPS_DESCRIPTION . " hgd where hg.help_group_id = hgd.help_group_id and hg.help_group_id = '" . $hgID . "' and hgd.language_id = '" . $language_id . "' limit 1");
						$level_result = $database->fetch_array($level_query);

						$edit_message = 'Make your required changes and press "Update" below or press "Cancel" to cancel your changes.';
						$button_text = 'update';
						?>
						<input type="hidden" name="hgID" value="<?php echo $hgID; ?>" />
						<?php
					} else {
						$level_result['help_group_name'] = '';
						$level_result['help_group_description'] = '';
						$edit_message = 'Please enter the details below for your new Help Group.  When you are done press the "Create" button.';
						$button_text = 'create';
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
						<td class="pageBoxContent" width="130">Help Group Name:</td><td class="pageBoxContent"><input type="text" name="help_group_name" value="<?php echo $level_result['help_group_name']; ?>" /></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2">Help Group Description:</td>
					</tr>
					<tr>
						<td colspan="2"><?php
						$sBasePath = 'editor/';
						//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;
						$oFCKeditor = new FCKeditor('help_group_description') ;
						$oFCKeditor->BasePath = $sBasePath ;
						$oFCKeditor->Value	= $level_result['help_group_description'];
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
									<table width="100%" cellpadding="2" cellspacing="2">
										<tr>
											<td align="left"><?php echo tep_create_button_submit($button_text, ucfirst($button_text).' News Item'); ?></form></td>
											<td align="right"><a href="<?php echo FILENAME_HELP_GROUP_ADD.'?nID='.$nID.'&page_action=view'; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></td>
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
								<td class="pageBoxContent"><?php if ($can_delete) { ?>Are you sure you wish to delete this Help Group?<?php } else { ?>This Help Group still has items assigned, you can not remove this Group.<?php } ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellpadding="2" cellspacing="2">
										<tr>
											<td align="left"><?php if ($can_delete) { ?><a href="<?php echo FILENAME_HELP_GROUP_ADD; ?>?hgID=<?php echo $hgID; ?>&page_action=delete_confirm"><?php echo tep_create_button_link('delete', 'Delete this News Item'); ?></a><?php } ?></td>
											<td align="right"><a href="<?php echo FILENAME_HELP_GROUP_ADD; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></td>
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
					<td class="pageBoxHeading"><b>Help Group Options</b></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td class="pageBoxContent">Click edit to edit a Help Group or press Create to create a new Level.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td height="5"><a href="<?php echo PAGE_URL; ?>?page_action=edit"><?php echo tep_create_button_link('create', 'Create New'); ?></a></td>
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