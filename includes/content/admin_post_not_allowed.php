<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$ptID = tep_fill_variable('ptID', 'get', tep_fill_variable('ptID', 'post'));

	$search_hoa_name = tep_fill_variable('search_hoa_name', 'get');
	$search_street_name = tep_fill_variable('search_street_name', 'get');
	
	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	$submit_value = tep_fill_variable('submit_value_y', 'post');
	
		if ($page_action == 'edit_confirm') {
			if (!empty($submit_value)) {
				$street_name = tep_fill_variable('street_name', 'post');
				$city = tep_fill_variable('city', 'post');
				$county_id = tep_fill_variable('county_id', 'post');
				$state_id = tep_fill_variable('state_id', 'post');
				$zip = tep_fill_variable('zip', 'post');
				$house_number_range = tep_fill_variable('house_number_range', 'post');
				$hoa_contact_info = tep_fill_variable('hoa_contact_info', 'post');
				$comments = tep_fill_variable('comments', 'post');
				$house_number_range_start = '';
				$house_number_range_end = '';
				
					if (!empty($house_number_range)) {
						$explode = explode('-', str_replace(' ', '', $house_number_range));
							if (count($explode) == 2) {
								$house_number_range_start = $explode[0];
								$house_number_range_end = $explode[1];
							}
					}
	
				$database->query("update " . TABLE_POST_NOT_ALLOWED . " set street_name = '" . $street_name . "', city = '" . $city . "', county_id = '" . $county_id . "', zip = '" . $zip . "', state_id = '" . $state_id . "', house_number_range = '" . $house_number_range . "', house_number_range_start = '" . $house_number_range_start . "', house_number_range_end = '" . $house_number_range_end . "', hoa_contact_info = '" . $hoa_contact_info . "', comments = '" . $comments . "' where post_not_allowed_id = '" . $ptID . "' limit 1");
				$message = 'Address Successfully Updated.';
				$page_action = '';
			} else {
				$page_action = 'edit';
			}
		} elseif ($page_action == 'add_confirm') {
			if (!empty($submit_value)) {
				$street_name = tep_fill_variable('street_name', 'post');
				$city = tep_fill_variable('city', 'post');
				$zip = tep_fill_variable('zip', 'post');
				$county_id = tep_fill_variable('county_id', 'post');
				$state_id = tep_fill_variable('state_id', 'post');
				$house_number_range = tep_fill_variable('house_number_range', 'post');
				$hoa_contact_info = tep_fill_variable('hoa_contact_info', 'post');
				$comments = tep_fill_variable('comments', 'post');
				$house_number_range_start = '';
				$house_number_range_end = '';
				
					if (!empty($house_number_range)) {
						$explode = explode('-', str_replace(' ', '', $house_number_range));
							if (count($explode) == 2) {
								$house_number_range_start = $explode[0];
								$house_number_range_end = $explode[1];
							}
					}
				
				
				$database->query("insert into " . TABLE_POST_NOT_ALLOWED . " (street_name, city, county_id, state_id, house_number_range, house_number_range_start, house_number_range_end, hoa_contact_info, comments, zip) values ('" . $street_name . "', '" . $city . "', '" . $county_id . "', '" . $state_id . "', '" . $house_number_range . "', '" . $house_number_range_start . "', '" . $house_number_range_end . "', '" . $hoa_contact_info . "', '" . $comments . "', '" . $zip . "')");
				$message = 'Post Type Successfully Added';
				$page_action = '';
			} else {
				$page_action = 'add';
			}
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_POST_NOT_ALLOWED . " where post_not_allowed_id = '" . $ptID . "' limit 1");
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
				$listing_split = new split_page("select pna.post_not_allowed_id, pna.street_name, pna.city, pna.county_id, pna.state_id, pna.zip, pna.house_number_range, pna.hoa_contact_info, pna.comments, c.name as county_name, s.name as state_name from " . TABLE_POST_NOT_ALLOWED . " pna, " . TABLE_COUNTYS . " c, " . TABLE_STATES . " s where pna.state_id = s.state_id and pna.county_id = c.county_id" . ((!empty($search_street_name)) ? " and (pna.street_name like '%" . $search_street_name . "' or pna.street_name like '" . $search_street_name . "%' or pna.street_name = '" . $search_street_name . "')" : '')  . ((!empty($search_hoa_name)) ? " and (pna.hoa_contact_info like '%" . $search_hoa_name . "' or pna.hoa_contact_info like '" . $search_hoa_name . "%' or pna.hoa_contact_info = '" . $search_hoa_name . "')" : ''), '20', 'pna.post_not_allowed_id');
					if ($listing_split->number_of_rows > 0) {
		?>			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">House Number Range</td>
						<td class="pageBoxHeading">Street</td>
						<td class="pageBoxHeading">City</td>
						<td class="pageBoxHeading">Zip</td>
						<td class="pageBoxHeading">HOA Contact Info</td>
						<td class="pageBoxHeading">Comments</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$ptData = array();
					$query = $database->query($listing_split->sql_query);
					    foreach($database->fetch_array($query) as $result){
							if ($ptID == $result['post_not_allowed_id']) {
								$ptData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent" valign="top"><?php echo $result['house_number_range']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['street_name']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['city']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['zip']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['hoa_contact_info']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['comments']; ?></td>
						<td class="pageBoxContent" align="right" valign="top"><a href="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED . '?ptID='.$result['post_not_allowed_id'].'&page_action=edit&'.tep_get_all_get_params(array('page_action', 'action', 'ptID')); ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED . '?ptID='.$result['post_not_allowed_id'].'&page_action=delete&'.tep_get_all_get_params(array('page_action', 'action', 'ptID')); ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
						}
						?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> entrys)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'x', 'y', 'page_action', 'action'))); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php
					} else {
					?>
					<tr>
						<td class="smallText">No addresses could be found.</td>
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
						
							$street_name = tep_fill_variable('street_name', 'post', $ptData['street_name']);
							$city = tep_fill_variable('city', 'post', $ptData['city']);
							$county_id = tep_fill_variable('county_id', 'post', $ptData['county_id']);
							$state_id = tep_fill_variable('state_id', 'post', $ptData['state_id']);
							$house_number_range = tep_fill_variable('house_number_range', 'post', $ptData['house_number_range']);
							$hoa_contact_info = tep_fill_variable('hoa_contact_info', 'post', $ptData['hoa_contact_info']);
							$comments = tep_fill_variable('comments', 'post', $ptData['comments']);
							$zip = tep_fill_variable('zip', 'post', $ptData['zip']);
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading" colspan="2">Editing <?php echo $ptData['street_name']; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED; ?>?page_action=edit_confirm&ptID=<?php echo $ptID; ?>&<?php echo tep_get_all_get_params(array('page_action', 'action')); ?>" method="post">
							<tr>
								<td class="main" NOWRAP>House Number Range: </td><td><input type="text" name="house_number_range" value="<?php echo $house_number_range; ?>" /></td>
							</tr>
							<tr>
								<td colspan="2" class="main"><i>(seperated by -, leave blank for all)</i></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Street Name: </td><td><input type="text" name="street_name" value="<?php echo $street_name; ?>" /></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>City: </td><td><input type="text" name="city" value="<?php echo $city; ?>" /></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Zip: </td><td><input type="text" name="zip" value="<?php echo $zip; ?>" /></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>State: </td><td><?php echo tep_draw_state_pulldown('state_id', $state_id, ' onchange="this.form.submit();"'); ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>County: </td><td><?php echo tep_draw_county_pulldown('county_id', $state_id, $county_id, array()); ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>HOA Contact Information: </td>
								<td><input type="text" name="hoa_contact_info" value="<?php echo $hoa_contact_info; ?>" /></td>
							</tr>
							<tr>
								<td class="main" valign="top" NOWRAP>Comments: </td>
								<td><textarea name="comments"><?php echo $comments; ?></textarea></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?><!--<input type="submit" value="Update">--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED; ?>?<?php echo tep_get_all_get_params(array('page_action', 'action')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxHeading">Are you sure you wish to delete this Address?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED; ?>?ptID=<?php echo $ptID; ?>&page_action=delete_confirm&<?php echo tep_get_all_get_params(array('page_action', 'action')); ?>" method="post"><?php echo tep_create_button_submit('delete', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED; ?>?<?php echo tep_get_all_get_params(array('page_action', 'action')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					<?php
						}elseif ($page_action == 'add') {
							$street_name = tep_fill_variable('street_name', 'post');
							$city = tep_fill_variable('city', 'post');
							$county_id = tep_fill_variable('county_id', 'post');
							$state_id = tep_fill_variable('state_id', 'post');
							$house_number_range = tep_fill_variable('house_number_range', 'post');
							$hoa_contact_info = tep_fill_variable('hoa_contact_info', 'post');
							$comments = tep_fill_variable('comments', 'post');
							$zip = tep_fill_variable('zip', 'post');
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading" colspan="2">Adding New Address</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED; ?>?page_action=add_confirm&<?php echo tep_get_all_get_params(array('page_action', 'action')); ?>" method="post">
							<tr>
								<td class="main" NOWRAP>House Number Range: </td><td><input type="text" name="house_number_range" value="<?php echo $house_number_range; ?>" /></td>
							</tr>
							<tr>
								<td colspan="2" class="main"><i>(seperated by -, leave blank for all)</i></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Street Name: </td><td><input type="text" name="street_name" value="<?php echo $street_name; ?>" /></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>City: </td><td><input type="text" name="city" value="<?php echo $city; ?>" /></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Zip: </td><td><input type="text" name="zip" value="<?php echo $zip; ?>" /></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>State: </td><td><?php echo tep_draw_state_pulldown('state_id', $state_id, ' onchange="this.form.submit();"'); ?></td>
							</tr>
							<tr>
								<td class="main">County: </td><td><?php echo tep_draw_county_pulldown('county_id', $state_id, $county_id, array()); ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>HOA Contact Information: </td>
								<td><input type="text" name="hoa_contact_info" value="<?php echo $hoa_contact_info; ?>" /></td>
							</tr>
							<tr>
								<td class="main" valign="top" NOWRAP>Comments: </td>
								<td><textarea name="comments"><?php echo $comments; ?></textarea></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('create', 'Create', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED; ?>?<?php echo tep_get_all_get_params(array('page_action', 'action')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
							<td class="pageBoxHeading"><b>Address Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit an Address or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED; ?>" method="get">
						<tr>
							<td width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="main">Search by Street: </td>
										<td><input type="text" name="search_street_name" value="<?php echo $search_street_name; ?>" /></td>
									</tr>
									<tr>
										<td class="main">Search by HOA: </td>
										<td><input type="text" name="search_hoa_name" value="<?php echo $search_hoa_name; ?>" /></td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td><?php echo tep_create_button_submit('update', 'Update'); ?></td>
						</tr>
						</form>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<form action="<?php echo FILENAME_ADMIN_POST_NOT_ALLOWED; ?>?page_action=add&<?php echo tep_get_all_get_params(array('page_action', 'action')); ?>" method="post">
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