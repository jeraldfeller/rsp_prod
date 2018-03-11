<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$lID = tep_fill_variable('lID', 'get', tep_fill_variable('lID', 'post'));

	$message = '';
	
		if ($page_action == 'edit_confirm') {
			$name = tep_fill_variable('name', 'post');
			$language_default = tep_fill_variable('language_default', 'post');
			$database->query("update " . TABLE_LANGUAGES . " set name = '" . $name . "', '" . $language_default . "' where language_id = '" . $lID . "' limit 1");
			$message = 'Language Successfully Updated.';
		} elseif ($page_action == 'add_confirm') {
			$name = tep_fill_variable('name', 'post');
				if(tep_language_exists($name)) {
					$error->add_error('admin_languages', 'A language with this name already exists.  Please try again.');
					$action = 'add';
				} else {
					$code = strtolower($name);
					$database->query("insert into " . TABLE_LANGUAGES . " (name, code, language_default) values ('" . $name . "', '" . $code . "', '0')");
					//echo "insert into " . TABLE_LANGUAGES . " (name, code, language_default) values ('" . $name . "', '" . $code . "', '0')". '<br><br>';
					$query = $database->query("select page_id, title, keywords, description, name, page_order from " . TABLE_PAGES_DESCRIPTION ." where language_id = '" . tep_get_default_language() . "'");
					$id = $database->insert_id();
						while($result = $database->fetch_array($query)) {
							//echo "insert into " . TABLE_PAGES_DESCRIPTION . " (page_id, title, keywords, description, name, page_order, language_id) values ('" . $result['page_id'] . "', '" . $result['title'] . "', '" . $result['keywords'] . "', '" . $result['description'] . "', '" . $result['name'] . "', '" . $result['page_order'] . "', '" . $id . "')" . '<br>';
							$database->query("insert into " . TABLE_PAGES_DESCRIPTION . " (page_id, title, keywords, description, name, page_order, language_id) values ('" . $result['page_id'] . "', '" . $result['title'] . "', '" . $result['keywords'] . "', '" . $result['description'] . "', '" . $result['name'] . "', '" . $result['page_order'] . "', '" . $id . "')");
						}
					tep_db_copy_dir(DIR_LANGUAGES . tep_get_default_language('code'), DIR_LANGUAGES . $code);
				}
			$message = 'Language Successfully Added';
			$action = '';
		} elseif ($page_action == 'delete_confirm') {
			tep_db_remove_dir(DIR_LANGUAGES . tep_get_language_code($lID));
			$database->query("delete from " . TABLE_LANGUAGES . " where language_id = '" . $lID . "' limit 1");
			//echo "delete from " . TABLE_LANGUAGES . " where language_id = '" . $lID . "' limit 1" . '<br>';
			$database->query("delete from " . TABLE_PAGES_DESCRIPTION . " where language_id = '" . $lID . "'");
			//echo "delete from " . TABLE_PAGES_DESCRIPTION . " where language_id = '" . $lID . "' limit 1" . '<br>';
			
			$lID = '';
			$page_action = '';
		} elseif ($page_action == 'delete') {
			if (tep_get_default_language() == $lID) {
				$error->add_error('admin_languages', 'You can not delete the default Language.');
				$page_action = '';
			}
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_languages')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_languages'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">

				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Language Name</td>
						<td class="pageBoxHeading" align="center">Default</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$lData = array();
					$query = $database->query("select language_id, name, language_default from " . TABLE_LANGUAGES . " order by name");
						while($result = $database->fetch_array($query)) {
							if ($lID == $result['language_id']) {
								$lData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['name']; ?></td>
						<td class="pageBoxContent" align="center"><?php echo (($result['language_default'] == '0') ? 'No' : 'Yes'); ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_LANGUAGES . '?lID='.$result['language_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_LANGUAGES . '?lID='.$result['language_id'].'&page_action=delete'; ?>">Delete</a></td>
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
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Editing <?php echo $lData['name']; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_LANGUAGES; ?>?page_action=edit_confim&lID=<?php echo $lID; ?>" method="post">
							<tr>
								<td class="main">Language Name: </td><td><input type="text" name="name" value="<?php echo $lData['name']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Default: </td><td class="main"><input type="radio" name="language_default" value="1" <?php echo (($lData['language_default'] == '1') ? 'CHECKED ' : ''); ?> />&nbsp;Yes&nbsp;&nbsp;&nbsp;<input type="radio" name="language_default" value="0" <?php echo (($lData['language_default'] == '0') ? 'CHECKED ' : ''); ?> />&nbsp;No</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2"><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update">--></form><form action="<?php echo FILENAME_ADMIN_LANGUAGES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
							</tr>
							
						</table>
						<?php
						}elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this language?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_LANGUAGES; ?>?lID=<?php echo $lID; ?>&page_action=delete_confirm" method="post"><?php echo tep_create_button_submit('delete confirm', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form><form action="<?php echo FILENAME_ADMIN_LANGUAGES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
							</tr>
							
						</table>
					<?php
						}elseif ($page_action == 'add') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Adding New Language</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_LANGUAGES; ?>?page_action=add_confirm" method="post">
							<tr>
								<td class="main">Language Name: </td><td><input type="text" name="name" value="" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2"><?php echo tep_create_button_submit('create', 'Create'); ?></form><form action="<?php echo FILENAME_ADMIN_LANGUAGES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
							</tr>
						</table>
					<?php
					} else {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Language Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a Language or press Create to create a new one.</td>
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