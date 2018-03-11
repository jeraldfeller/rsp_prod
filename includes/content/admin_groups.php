<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$gID = tep_fill_variable('gID', 'get');
	$message = '';
	$pages = tep_fill_variable('pages', 'post', array());
		if (($page_action == 'edit_confirm') && is_numeric($gID)) {
			$database->query("delete from " . TABLE_USER_GROUPS_TO_PAGES . " where user_group_id = '" . $gID . "'");
				while(list($id, $status) = each($pages)) {
					if ($status == '1') {
						$database->query("insert into " . TABLE_USER_GROUPS_TO_PAGES . " (user_group_id, page_id) values ('" . $gID . "', '" . $id . "')");
					}
				}
			$message = 'Successfully Updated';
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
			<?php
				if ($page_action != 'edit') {
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td width="40%" class="pageBoxHeading">Group Name</td>
						<td width="40%" class="pageBoxHeading" align="center">Group Members</td>
						<td width="40%" class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$gData = array();
					$query = $database->query("select user_group_id,name from " . TABLE_USER_GROUPS . "  order by name");
					    foreach($database->fetch_array($query) as $result){
							$count_query = $database->query("select count(user_id) as count from " . TABLE_USERS_TO_USER_GROUPS . " where user_group_id = '" . $result['user_group_id'] . "'");
							$count_result = $database->fetch_array($count_query);
							
								if ($result['user_group_id'] == $gID) {
									$gData = $result;
									$gData['count'] = $count_result['count'];
								}
							
				?>
					<tr>
						<td width="40%" class="pageBoxContent"><?php echo $result['name']; ?></td>
						<td width="40%" class="pageBoxContent" align="center"><?php echo $count_result['count']; ?></td>
						<td width="40%" class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_GROUPS . '?gID='.$result['user_group_id'].'&page_action=view'; ?>">Edit</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
					?>
				</table>
			<?php
				} else {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_GROUPS . '?page_action=edit_confirm&gID='.$gID; ?>">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td width="100%" class="pageBoxHeading" NOWRAP>Page File Name</td>
						<td width="100%" class="pageBoxHeading" NOWRAP>Page Status</td>
						<td width="100%" class="pageBoxHeading" NOWRAP>Viewable</td>
					</tr>
					<?php
						$query = $database->query("select p.page_id, p.page_url, p.page_lock_status from " . TABLE_PAGES . " p order by p.page_url");
							foreach($database->fetch_array($query) as $result){
								$status_query = $database->query("select count(user_group_id) as count from " . TABLE_USER_GROUPS_TO_PAGES . " where page_id = '" . $result['page_id'] . "' and user_group_id = '" . $gID . "'");
								$status_result = $database->fetch_array($status_query);
									if ($status_result['count'] > 0) {
										$checked = '  CHECKED';
									} else {
										$checked = '';
									}
								$string = '';
									if ($result['page_lock_status'] == '1') {
										$string = '<input type="checkbox" name="pages[' . $result['page_id'] . ']" value="1"' . $checked . '>';
									} else {
										$string = 'Open';
									}
								?>
								<tr>
									<td width="100%" class="pageBoxContent"><?php echo $result['page_url']; ?></td>
									<td width="100%" class="pageBoxContent"><?php echo (($result['page_lock_status'] == '1') ? 'Locked' : 'Open'); ?></td>
									<td width="100%" class="pageBoxContent"><?php echo $string; ?></td>
								</tr>
								<?php
							}
					?>
				</table>
			<?php
				}
			?>
		</td>
		<?php
			if (!empty($gID)) {
		?>
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
					$pages_query = $database->query("select count(page_id) as count from " . TABLE_USER_GROUPS_TO_PAGES . " where user_group_id = '" . (int)$gID . "'");
					$pages_result = $database->fetch_array($pages_query);
				?>
				<tr>
					<td width="100%">
					<?php
						if ($page_action == 'edit') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Make your required changes and press "Update" below or press "Cancel" to cancel your changes.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_GROUPS.'?gID='.$gID.'&page_action=view'; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					<?php
						} elseif (!empty($page_action)) {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><b>Viewing <?php echo $gData['name']; ?></b></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Number of Users: <?php echo $gData['count']; ?></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Allowed Pages: <?php echo $pages_result['count']; ?> (excluding open pages)</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Click Edit below to edit the pages allowed by this User Group.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_GROUPS . '?page_action=edit&gID='.$gID; ?>">
							<tr>
								<td width="100%" align="right"><?php echo tep_create_button_submit('edit', 'Edit'); ?><!--<input type="submit" value="Edit">-->&nbsp;&nbsp;</td>
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
		<?php
			}
		?>
	</tr>
</table>