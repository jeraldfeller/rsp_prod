<?php
	$srID = tep_fill_variable('srID', 'get', tep_fill_variable('srID', 'post'));

	$show_state_id = tep_fill_variable('show_state_id', 'get');
	$search_street_name = tep_fill_variable('search_street_name', 'get');
	
	$page_number = tep_fill_variable('page_number', 'post', 1);
	
	
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
		<?php
			if (empty($oID)) {
				$where = '';
				$listing_split = new split_page("select oosr.out_of_service_request_id, oosr.house_number, oosr.street_name, oosr.city, oosr.zip, oosr.zip4, oosr.date_added, s.name as state_name, c.name as county_name, ud.firstname, ud.lastname from " . TABLE_OUT_OF_SERVICE_REQUESTS . " oosr, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c, " . TABLE_USERS_DESCRIPTION . " ud where oosr.user_id = ud.user_id and oosr.state_id = s.state_id and oosr.county_id = c.county_id" . ((!empty($show_state_id)) ? " and oosr.state_id = '" . $show_state_id . "'": '') . ((!empty($search_street_name)) ? " and (oosr.street_name like '" . $search_street_name . "%' or oosr.street_name like '%" . $search_street_name . "' or oosr.street_name = '" . $search_street_name . "')": '') . " order by oosr.out_of_service_request_id DESC", '20', 'oosr.out_of_service_request_id');
					if ($listing_split->number_of_rows > 0) {
		?>			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">House Number</td>
						<td class="pageBoxHeading">Street</td>
						<td class="pageBoxHeading">City</td>
						<td class="pageBoxHeading">Zip</td>
						<td class="pageBoxHeading">County</td>
						<td class="pageBoxHeading">State</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$ptData = array();
					$query = $database->query($listing_split->sql_query);
					    foreach($database->fetch_array($query) as $result){
							if ($srID == $result['out_of_service_request_id']) {
								$ptData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent" valign="top"><?php echo $result['house_number']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['street_name']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['city']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['zip']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['county_name']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['state_name']; ?></td>
						<td class="pageBoxContent" align="right" valign="top"><a href="<?php echo FILENAME_ADMIN_SERVICE_REQUESTS . '?srID='.$result['out_of_service_request_id'].'&'.tep_get_all_get_params(array('page_action', 'action')); ?>">View</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
						}
						?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> service requests)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'x', 'y', 'page_action', 'action'))); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php
					} else {
					?>
					<tr>
						<td class="smallText">No requests could be found.</td>
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

				<tr>
					<td width="100%">
					<?php
						if (!empty($srID)) {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading" colspan="2">Viewing "<?php echo $ptData['house_number'] . ' ' . $ptData['street_name']; ?>"</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Street Address: </td><td class="main"><?php echo $ptData['house_number'] . ' ' . $ptData['street_name']; ?></td>
							</tr>

							<tr>
								<td class="main" NOWRAP>City: </td><td class="main"><?php echo $ptData['city']; ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Zip: </td><td class="main"><?php echo $ptData['zip']; ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>County: </td><td class="main"><?php echo $ptData['county_name']; ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>State: </td><td class="main"><?php echo $ptData['state_name']; ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Zip+4: </td><td class="main"><?php echo $ptData['zip4']; ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Agent: </td><td class="main"><?php echo $ptData['firstname'] . ' ' . $result['lastname']; ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Date Requested: </td><td class="main"><?php echo date("n/d/Y", $ptData['date_added']); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_SERVICE_REQUESTS; ?>?<?php echo tep_get_all_get_params(array('page_action', 'action')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
						<form action="<?php echo FILENAME_ADMIN_SERVICE_REQUESTS; ?>" method="get">
						<tr>
							<td width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="main" NOWRAP>Search by Street: </td>
										<td><input type="text" name="search_street_name" value="<?php echo $search_street_name; ?>" /></td>
									</tr>
									<tr>
										<td class="main" NOWRAP>Show only State: </td>
										<td><?php echo tep_draw_state_pulldown('show_state_id', $show_state_id, '', array(array('id' => '', 'name' => 'All States'))); ?></td>
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