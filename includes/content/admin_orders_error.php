<?php

	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get');
	$order_view = tep_fill_variable('order_view', 'get', 'open');
 	$order_edit = tep_fill_variable('order_edit', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '');
	$order_type = tep_fill_variable('order_type', 'get', '');

	$show_house_number = tep_fill_variable('show_house_number', 'get', '');
	$show_street_name = tep_fill_variable('show_street_name', 'get', '');
	$show_between_type = tep_fill_variable('show_between_type', 'get', 'added');
	$show_between_start = tep_fill_variable('show_between_start', 'get', date("n/d/Y", mktime()));
	$show_between_end = tep_fill_variable('show_between_end', 'get', '');
		
	$installer_id = tep_fill_variable('installer_id', 'get', '');
	$button_action = tep_fill_variable('button_action_y');

	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	$page = tep_fill_variable('page', 'post', 1);
		
		if ($page_action == 'update_run') {
			$delete = tep_fill_variable('delete');
				
			$similar_address_id = tep_fill_variable('similar_address_id');
			$all_address_id = tep_fill_variable('all_address_id');
				if (!empty($similar_address_id)) {
					$address_id = $similar_address_id;
				} else {
					$address_id = $all_address_id;
				}
				
			
				if ($delete == '1') {
					$query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
					$result = $database->fetch_array($query);
					
					$database->query("delete from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "' limit 1");
					$database->query("delete from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . $result['address_id'] . "' limit 1");
					
					$database->query("delete from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
					$database->query("delete from " . TABLE_ORDERS_DESCRIPTION . " where order_id = '" . $oID . "' limit 1");
					$database->query("delete from " . TABLE_ORDERS_HISTORY . " where order_id = '" . $oID . "' limit 1");
				
				} elseif (!empty($address_id)) {
					$query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
					$result = $database->fetch_array($query);
                    $last_modified_by = tep_fill_variable('user_id', 'session', 0);

					$database->query("update " . TABLE_ORDERS . " set address_id = '" . $address_id . "', order_status_id = '1', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where order_id = '" . $oID . "' limit 1");
					$database->query("delete from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "' limit 1");
					$database->query("delete from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . $result['address_id'] . "' limit 1");
				} else {
					$page_action = 'update';
				}
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">

	<tr>
		<td width="100%" valign="top">
			<?php

					$listing_split = new split_page("select o.order_id, o.order_total, ot.name as order_type_name, a.house_number, a.street_name, a.city from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a where  o.order_type_id = ot.order_type_id and o.order_status_id = '0' and o.address_id = a.address_id order by a.street_name, o.date_added DESC", '20', 'o.order_id');

						if ($listing_split->number_of_rows > 0) {
				?>
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td class="pageBoxHeading" align="center">Address</td>
							<td class="pageBoxHeading" align="center">Order Total</td>
							<td class="pageBoxHeading" align="center">Order Type</td>
							<td class="pageBoxHeading" align="right">Action</td>
							<td width="10" class="pageBoxHeading"></td>
						</tr>
					<?php
						$query = $database->query($listing_split->sql_query);
							while($result = $database->fetch_array($query)) {
								
					?>
						<tr>
							<td class="pageBoxContent" align="center"><?php echo $result['house_number']; ?> <?php echo $result['street_name']; ?>, <?php echo $result['city']; ?></td>
							<td class="pageBoxContent" align="center">$<?php echo $result['order_total']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $result['order_type_name']; ?></td>
							<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_ORDERS_ERROR . '?oID='.$result['order_id'].'&page_action=update'; ?>&page=<?php echo $page; ?>">Edit</a></td>
							<td width="10" class="pageBoxContent"></td>
						</tr>
					<?php
							}
							?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> orders)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php
						}
					?>
				</table>
			
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
		<?php
				if ($page_action == 'update') {
					$query = $database->query("select o.order_type_id, o.user_id, a.house_number, a.street_name, a.city, a.zip, c.name as county_name, s.name as state_name from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " .TABLE_COUNTYS . " c on (a.county_id = c.county_id) where o.order_id = '" . $oID . "' and o.address_id = a.address_id limit 1");
					$result = $database->fetch_array($query);
		?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<form action="<?php echo FILENAME_ADMIN_ORDERS_ERROR; ?>?oID=<?php echo $oID; ?>&page=<?php echo $page; ?>&page_action=update_run" method="post">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="pageBoxContent" valign="top">Current Address: </td>
											<td class="pageBoxContent" valign="top"><?php echo $result['house_number'] . ' ' . $result['street_name'] . '<br>' . $result['zip'] . '<br>' . $result['county_name'] . '<br>' . $result['state_name']; ?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main"><strong><strong>Select an address from below to assign to:</strong></strong></td>
							</tr>
							<?php
								$similar_address_count = 0;
								$option_string = '';

								$aquery = $database->query("select a.address_id, a.house_number, a.street_name, a.city, a.zip, c.name as county_name, s.name as state_name from " . TABLE_ADDRESSES . " a left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id), " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES_TO_USERS . " atu where atu.user_id = '" . $result['user_id'] . "' and a.address_id = o.address_id and o.order_type_id = '1' and atu.address_id = a.address_id and (a.house_number = '" . $result['house_number'] . "'" . ((!empty($result['street_name'])) ? (" or a.street_name like '%" . $result['street_name'] . "' or a.street_name like '" . $result['street_name'] . "%' or a.street_name like '%" . $result['street_name'] . "%' or a.street_name like '%" . $result['street_name'] . "%'") : '') . ") order by a.street_name");
									while($aresult = $database->fetch_array($aquery)) {
										$squery = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where address_id = '" . $aresult['address_id'] . "' and order_type_id = '3'");
										$sresult = $database->fetch_array($squery);
											if ($sresult['count'] > 0) {
												continue;
												}
										$similar_address_count++;
										$option_string .= '<option value="'.$aresult['address_id'].'">'.$aresult['house_number'] . ' ' . $aresult['street_name'] . ', ' . $aresult['city'] . ', ' . $aresult['zip'] . ', ' . $aresult['county_name'] . ', ' . $aresult['state_name'].'</option>';
									}
									if ($similar_address_count > 0) {
									?>
									<tr>
										<td class="main">Similar Addresses:</td>
									</tr>
									<tr>
										<td width="100%"><select name="similar_address_id" size="5">
											<?php
												echo $option_string;
											?>
										</select></td>
									</tr>
									<?php
									}
							?>
							<tr>
								<td class="main">All Addresses:</td>
							</tr>
							<tr>
								<td width="100%"><select name="all_address_id" size="5">
									<?php
										$aquery = $database->query("select a.address_id, a.house_number, a.street_name, a.city, a.zip, c.name as county_name, s.name as state_name from " . TABLE_ADDRESSES . " a left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id), " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES_TO_USERS . " atu where atu.user_id = '" . $result['user_id'] . "' and a.address_id = o.address_id and o.order_type_id = '1' and atu.address_id = a.address_id order by a.street_name");
											while($aresult = $database->fetch_array($aquery)) {
												$squery = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where address_id = '" . $aresult['address_id'] . "' and order_type_id = '3'");
												$sresult = $database->fetch_array($squery);
													if ($sresult['count'] > 0) {
														continue;
													}
												?><option value="<?php echo $aresult['address_id']; ?>"><?php echo $aresult['house_number'] . ' ' . $aresult['street_name'] . ', ' . $aresult['city'] . ', ' . $aresult['zip'] . ', ' . $aresult['county_name'] . ', ' . $aresult['state_name']; ?></option><?php
											}
									?>
								</select></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>	
								<td align="right"><input type="reset" value="Reset Form"/></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">Or click here to delete: <input type="checkbox" name="delete" value="1" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_ORDERS_ERROR; ?>?oID=<?php echo $oID; ?>&page=<?php echo $page; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
				<?php
				} elseif ($page_action == 'update_confirm')  {
				?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to assign this address to this order?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table cellspacing="0" cellpadding="0" width="100%">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_ORDERS_ERROR; ?>?oID=<?php echo $oID; ?>&page=<?php echo $page; ?>&page_action=update_run" method="post"><input type="hidden" name="address_id" value="<?php echo tep_fill_variable('address_id'); ?>" /><input type="hidden" name="delete" value="<?php echo tep_fill_variable('address_id'); ?>" /><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_ORDERS_ERROR; ?>?page=<?php echo $page; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
				} elseif ($page_action == 'add')  {
				?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press cancel to go back to the previous page or press update to Insert the Order.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('insert_order', 'Insert Order', ' name="button_action"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_ORDERS_ERROR; ?>?order_status=<?php echo $order_status; ?>&order_type=<?php echo $order_type; ?>&installer_id=<?php echo $installer_id; ?>&page=<?php echo $page; ?>&show_house_number=<?php echo $show_house_number; ?>&show_street_name=<?php echo $show_street_name; ?>&show_between_type=<?php echo $show_between_type; ?>&show_between_start=<?php echo urlencode($show_between_start); ?>&show_between_end=<?php echo urlencode($show_between_end); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
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
<?php
	
?>
