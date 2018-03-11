<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$cID = tep_fill_variable('cID', 'get', tep_fill_variable('cID', 'post'));
	$state_id = tep_fill_variable('state_id', 'get', '');
	$start_letter = tep_fill_variable('start_letter', 'get', '');
	$page = tep_fill_variable('page', 'get', '');
	
	$message = '';

		if ($page_action == 'edit_confirm') {
			$name = tep_fill_variable('name', 'post');
			$state_id = tep_fill_variable('state', 'post');
			$service_area_id = tep_fill_variable('service_areas', 'post');
			//$database->query("update " . TABLE_COUNTYS . " s, " . TABLE_STATES . " s set c.name = '" . $name . "' c.state_id = '" . $state_id . "'  where county_id = '" . $cID . "' and c.state_id = s.state_id limit 1");
			$database->query("update " . TABLE_COUNTYS . " set name = '" . $name . "', state_id = '" . $state_id . "', service_area_id = '" . $service_area_id . "'  where county_id = '" . $cID . "' limit 1");
			
			$message = 'County Successfully Updated.';
		} elseif ($page_action == 'add_confirm') {
			$name = tep_fill_variable('name', 'post');
			$state_id = tep_fill_variable('state', 'post');
			$service_area_id = tep_fill_variable('service_areas', 'post');
			$database->query("insert into " . TABLE_COUNTYS . " (name, state_id, service_area_id) values ('" . $name . "', '" . $state_id . "', '" . $service_area_id . "')");
				
			$message = 'County Successfully Added';
			$action = '';
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_COUNTYS . " where county_id = '" . $cID . "' limit 1");
			$cID = '';
			$page_action = '';
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
		<?php
			if (empty($oID)) {
				$where = '';
				$listing_split = new split_page("select c.county_id, c.name as county_name, s.name as state_name, s.state_id, sa.name as service_area_name, sa.service_area_id from " . TABLE_COUNTYS . " c, " . TABLE_STATES . " s, " . TABLE_SERVICE_AREAS . " sa where " . ((!empty($state_id)) ? "s.state_id = '" . $state_id . "' and " : '') . ((!empty($start_letter)) ? " c.name like '" . $start_letter . "%' and " : '') . "s.state_id = c.state_id and c.service_area_id = sa.service_area_id order by c.name", '20', 'c.county_id');
					if ($listing_split->number_of_rows > 0) {
		?>	
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">County</td>
						<td class="pageBoxHeading">State</td>
						<!--<td class="pageBoxHeading">Service Area</td>-->
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$cData = array();
					$query = $database->query($listing_split->sql_query);
					    foreach($database->fetch_array($query) as $result){
							if ($cID == $result['county_id']) {
								$cData = $result;
							}						
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['county_name']; ?></td>
						<td class="pageBoxContent"><?php echo $result['state_name']; ?></td>
						<!--<td class="pageBoxContent"><?php echo $result['service_area_name']; ?></td>-->
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_COUNTYS . '?cID='.$result['county_id'].'&page_action=edit&page='.$page.'&start_letter='.$start_letter.'&state_id='.$state_id; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_COUNTYS . '?cID='.$result['county_id'].'&page_action=delete&page='.$page.'&start_letter='.$start_letter.'&state_id='.$state_id; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
						?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> counties)'); ?></td>
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
								<td class="pageBoxContent" colspan="2">Editing <?php echo $cData['county_name']; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_COUNTYS; ?>?page_action=edit_confirm&cID=<?php echo $cID; ?>&page=<?php echo $page; ?>&start_letter=<?php echo $start_letter; ?>&state_id=<?php echo $state_id; ?>" method="post">
							<tr>
								<td class="main">County Name: </td><td><input type="text" name="name" value="<?php echo $cData['county_name']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">State: </td><td><?php echo tep_draw_state_pulldown('state', $cData['state_id']); ?></td>
							</tr>
							<!--<tr>
								<td class="main">Service Area: </td><td><?php echo tep_draw_service_areas_pulldown('service_areas', $cData['service_area_id']); ?></td>
							</tr>-->
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update">--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_COUNTYS; ?>?page=<?php echo $page; ?>&start_letter=<?php echo $start_letter; ?>&state_id=<?php echo $state_id; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxContent">Are you sure you wish to delete this County?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_COUNTYS; ?>?cID=<?php echo $cID; ?>&page_action=delete_confirm&page=<?php echo $page; ?>&start_letter=<?php echo $start_letter; ?>&state_id=<?php echo $state_id; ?>" method="post"><?php echo tep_create_button_submit('delete', 'DeleteConfirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_COUNTYS; ?>?page=<?php echo $page; ?>&start_letter=<?php echo $start_letter; ?>&state_id=<?php echo $state_id; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxContent" colspan="2">Adding New County</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_COUNTYS; ?>?page_action=add_confirm&page=<?php echo $page; ?>&start_letter=<?php echo $start_letter; ?>&state_id=<?php echo $state_id; ?>" method="post">
							<tr>
								<td class="main">County Name: </td><td><input type="text" name="name" value="" /></td>
							</tr>
							<tr>
								<td class="main">State: </td><td><?php echo tep_draw_state_pulldown('state'); ?></td>
							</tr>
							<!--<tr>
								<td class="main">Service Area: </td><td><?php echo tep_draw_service_areas_pulldown('service_areas'); ?></td>
							</tr>-->
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('create', 'Create'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_COUNTYS; ?>?page=<?php echo $page; ?>&start_letter=<?php echo $start_letter; ?>&state_id=<?php echo $state_id; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
							<td class="pageBoxHeading"><b>County Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a County or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<form action="<?php echo FILENAME_ADMIN_COUNTYS; ?>">
										<td class="main">View Counties in State: </td><td><?php echo tep_draw_state_pulldown('state_id', $state_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'All'))); ?></td>
									</tr>
									<tr>
										<td class="main">Show Counties starting with: </td><td><select name="start_letter"><?php
											$query = $database->query("select LEFT(name, 1) as letter from " . TABLE_COUNTYS . ((!empty($state_id)) ? " where state_id = '" . $state_id . "'" : '') . " group by letter order by letter");
											echo '<option value="">Any</option>';
												foreach($database->fetch_array($query) as $result){
														if (empty($result['letter'])) {
															continue;
														}
														if ($start_letter == strtolower($result['letter'])) {
															$selected = ' SELECTED';
														} else {
															$selected = '';
														}
													echo '<option value="'.strtolower($result['letter']).'"' . $selected . '>'.strtoupper($result['letter']).'</option>';
												}
										?></select></td>
									</tr>
								</table>
							</td>
						
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td align="right"><?php echo tep_create_button_submit('update', 'Update'); ?></td>
						</tr>
						<tr>
						</form>
							<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=add&page=<?php echo $page; ?>&start_letter=<?php echo $start_letter; ?>&state_id=<?php echo $state_id; ?>" method="post">
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