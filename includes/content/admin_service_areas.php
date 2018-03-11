<?php
	$page_action = tep_fill_variable('page_action', 'get');

	$sID = tep_fill_variable('sID', 'get', tep_fill_variable('sID', 'post'));

	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	
		if (($page_action == 'edit_confirm') && !empty($_POST)) {
			$name = tep_fill_variable('name', 'post');
			$admin_comment = tep_fill_variable('admin_comment', 'post');
			$surcharge = tep_fill_variable('surcharge', 'post');
			$installation_window = tep_fill_variable('installation_window', 'post');
			$installation_cost = tep_fill_variable('installation_cost', 'post');
			$map_color = tep_fill_variable('map_color', 'post');
			$status = tep_fill_variable('status', 'post');
			$installer_modifier = tep_fill_variable('installer_modifier');
			$database->query("update " . TABLE_SERVICE_AREAS . " set name = '" . $name . "', surcharge = '" . $surcharge . "', installation_window = '" . $installation_window . "', installation_cost = '" . $installation_cost . "', status = '" . $status . "', admin_comment = '" . $admin_comment . "', installer_modifier = '" . $installer_modifier . "', map_color = '" . $map_color . "' where service_area_id = '" . $sID . "' limit 1");
			//die("update " . TABLE_SERVICE_AREAS . " set name = '" . $name . "', surcharge = '" . $surcharge . "', installation_window = '" . $installation_window . "', installation_cost = '" . $installation_cost . "', status = '" . $status . "', admin_comment = '" . $admin_comment . "', installer_modifier = '" . $installer_modifier . "' where service_area_id = '" . $sID . "' limit 1");

			$message = 'Service Area Successfully Updated.';
		} elseif (($page_action == 'add_confirm') && !empty($_POST)) {
			$name = tep_fill_variable('name', 'post');
			$admin_comment = tep_fill_variable('admin_comment', 'post');
			$surcharge = tep_fill_variable('surcharge', 'post');
			$installation_cost = tep_fill_variable('installation_cost', 'post');
			$installation_window = tep_fill_variable('installation_window', 'post');
			$map_color = tep_fill_variable('map_color', 'post');
			$status = tep_fill_variable('status', 'post');
			$installer_modifier = tep_fill_variable('installer_modifier');
			$database->query("insert into " . TABLE_SERVICE_AREAS . " (name, surcharge, installation_cost, installation_window, status, admin_comment, installer_modifier, map_color) values ('" . $name . "', '" . $surcharge . "', '" . $installation_cost . "', '" . $installation_window . "', '" . $status . "', '" . $admin_comment . "', '" . $installer_modifier . "', '" . $map_color . "')");
			$message = 'Service Area Successfully Added';
			$action = '';
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_SERVICE_AREAS . " where service_area_id = '" . $sID . "' limit 1");
			$sID = '';
			$page_action = '';

		}
		
?>
<script src='/js/spectrum.js'></script>
<link rel='stylesheet' href='/css/spectrum.css' />
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
		<?php
			if (empty($oID)) {
				$where = '';
				$listing_split = new split_page("select service_area_id, name, surcharge, installation_cost, installation_window, status, admin_comment, installer_modifier, map_color from " . TABLE_SERVICE_AREAS . " order by name", '20', 'service_area_id');
					if ($listing_split->number_of_rows > 0) {
		?>			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Name</td>
						<td class="pageBoxHeading">Admin Comment</td>
						<td class="pageBoxHeading">Surcharge</td>
						<td class="pageBoxHeading">Installer Payment</td>
						<td class="pageBoxHeading">Installation Window</td>
						<td class="pageBoxHeading">Status</td>
						<td class="pageBoxHeading">Map Color</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$sData = array();
					$query = $database->query($listing_split->sql_query);
					    foreach($database->fetch_array($query) as $result){
							if ($sID == $result['service_area_id']) {
								$sData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent" valign="top"><?php echo $result['name']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['admin_comment']; ?></td>
						<td class="pageBoxContent" valign="top">$<?php echo $result['surcharge']; ?></td>
						<td class="pageBoxContent" valign="top">$<?php echo $result['installation_cost']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['installation_window']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo (($result['status'] == 0) ? 'Active' : 'Inactive'); ?></td>
                        <td class="pageBoxContent" <?php echo (!empty($result['map_color'])) ? "style=\"background-color: {$result['map_color']}\"" : "" ?> " valign="top">&nbsp;</td>
						<td class="pageBoxContent" align="right" valign="top"><a href="<?php echo FILENAME_ADMIN_SERVICE_AREAS . '?sID='.$result['service_area_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_SERVICE_AREAS . '?sID='.$result['service_area_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
					<tr>
						<td height="5"><img src"images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
			<?php
						}
						?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> states)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
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
								<td class="pageBoxContent" colspan="2">Editing <?php echo $sData['name']; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_SERVICE_AREAS; ?>?page_action=edit_confirm&sID=<?php echo $sID; ?>" method="post">
							<tr>
								<td width="130"><img src="images/pixel_trans.gif" height="1" width="130" /></td>
								<td width="100%"></td>
							</tr>
							<tr>
								<td class="main">Name: </td><td><input type="text" name="name" value="<?php echo $sData['name']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Admin Comment: </td><td><textarea name="admin_comment"><?php echo $sData['admin_comment']; ?></textarea></td>
							</tr>
							<tr>
								<td class="main">Surcharge: </td><td class="main">$<input type="text" name="surcharge" value="<?php echo $sData['surcharge']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Installer Payment: </td><td class="main">$<input type="text" name="installation_cost" value="<?php echo $sData['installation_cost']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Installer Modifier:<br /><i>(in % of total order)</i> </td><td class="main" valign="top"><input type="text" name="installer_modifier" value="<?php echo $sData['installer_modifier']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Installation Window: </td><td><input type="text" name="installation_window" value="<?php echo $sData['installation_window']; ?>" /></td>
							</tr>
							<tr>
                                <td class="main">Map Color: </td><td><input type="color" name="map_color" value="<?php echo $sData['map_color']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Status: </td><td><?php echo tep_draw_service_areas_status_pulldown('status', $sData['status']); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update">--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_SERVICE_AREAS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxContent">Are you sure you wish to delete this Service Area?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_SERVICE_AREAS; ?>?sID=<?php echo $sID; ?>&page_action=delete_confirm" method="post"><input type="submit" value="Delete Confirm" /></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_SERVICE_AREAS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxContent" colspan="2">Adding New Service Area</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_SERVICE_AREAS; ?>?page_action=add_confirm" method="post">
							<tr>
								<td width="130"><img src="images/pixel_trans.gif" height="1" width="130" /></td>
								<td width="100%"></td>
							</tr>
							<tr>
								<td class="main">Name: </td><td><input type="text" name="name" value="" /></td>
							</tr>
							<tr>
								<td class="main">Admin Comment: </td><td><textarea name="admin_comment"></textarea></td>
							</tr>
							<tr>
								<td class="main">Surcharge: </td><td class="main">$<input type="text" name="surcharge" value="" /></td>
							</tr>
							<tr>
								<td class="main">Installation Payment: </td><td class="main">$<input type="text" name="installation_cost" value="" /></td>
							</tr>
							<tr>
								<td class="main">Installation Window: </td><td><input type="text" name="installation_window" value="" /></td>
							</tr>
							<tr>
                                <td class="main">Map Color: </td><td><input type="color" name="map_color" value="" /></td>
							</tr>
							<tr>
								<td class="main">Status: </td><td><?php echo tep_draw_service_areas_status_pulldown('status', '0'); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('create', 'Create'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_SERVICE_AREAS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
							<td class="pageBoxHeading"><b>Service Area Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a Service Area or press Create to create a new one.</td>
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
