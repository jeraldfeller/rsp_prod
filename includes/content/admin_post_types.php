<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$ptID = tep_fill_variable('ptID', 'get', tep_fill_variable('ptID', 'post'));

	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	
		if ($page_action == 'edit_confirm') {
			$post_type_name = tep_fill_variable('post_type_name', 'post');
			$installation_cost = tep_fill_variable('installation_cost', 'post');

			$database->query("update " . TABLE_POST_TYPES . " set post_type_name = '" . $post_type_name . "', installation_cost = '" . $installation_cost . "' where post_type_id = '" . $ptID . "' limit 1");
			$message = 'Post Type Successfully Updated.';
		} elseif ($page_action == 'add_confirm') {
			$post_type_name = tep_fill_variable('post_type_name', 'post');
			$installation_cost = tep_fill_variable('installation_cost', 'post');
			
			$database->query("insert into " . TABLE_POST_TYPES . " (post_type_name, installation_cost) values ('" . $post_type_name . "', '" . $installation_cost . "')");
			$message = 'Post Type Successfully Added';
			$action = '';
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_POST_TYPES . " where post_type_id = '" . $ptID . "' limit 1");
			$ptID = '';
			$page_action = '';

		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
		<?php
			if (empty($oID)) {
				$where = '';
				$listing_split = new split_page("select post_type_id, post_type_name, installation_cost from " . TABLE_POST_TYPES . " order by post_type_name", '20', 'post_type_id');
					if ($listing_split->number_of_rows > 0) {
		?>			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Name</td>
						<td class="pageBoxHeading">Installation Cost</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$ptData = array();
					$query = $database->query($listing_split->sql_query);
					    foreach($database->fetch_array($query) as $result){
							if ($ptID == $result['post_type_id']) {
								$ptData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['post_type_name']; ?></td>
						<td class="pageBoxContent">$<?php echo $result['installation_cost']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_POST_TYPES . '?ptID='.$result['post_type_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_POST_TYPES . '?ptID='.$result['post_type_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
						}
						?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php //echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> states)'); ?></td>
										<td class="smallText" style="text-align: right"><?php //echo 'Page: ' . $listing_split->display_links(10, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php
					}
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
								<td class="pageBoxContent" colspan="2">Editing <?php echo $ptData['post_type_name']; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_POST_TYPES; ?>?page_action=edit_confirm&ptID=<?php echo $ptID; ?>" method="post">
							<tr>
								<td class="main">Name: </td><td><input type="text" name="post_type_name" value="<?php echo $ptData['post_type_name']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Installation Cost: $</td><td><input type="text" name="installation_cost" value="<?php echo $ptData['installation_cost']; ?>" /></td>
							</tr>
							
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update">--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_POST_TYPES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
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
								<td class="pageBoxContent">Are you sure you wish to delete this Post Type?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_POST_TYPES; ?>?ptID=<?php echo $ptID; ?>&page_action=delete_confirm" method="post"><?php echo tep_create_button_submit('delete', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_POST_TYPES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					<?php
						}elseif ($page_action == 'add') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Adding New Post Type</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_POST_TYPES; ?>?page_action=add_confirm" method="post">
							<tr>
								<td class="main">Name: </td><td><input type="text" name="post_type_name" value="" /></td>
							</tr>
							<tr>
								<td class="main">Installation Cost: $</td><td><input type="text" name="installation_cost" value="" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('create', 'Create'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_POST_TYPES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
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
							<td class="pageBoxHeading"><b>Post Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a Post Type or press Create to create a new one.</td>
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