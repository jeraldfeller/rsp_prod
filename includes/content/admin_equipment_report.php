<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$eID = tep_fill_variable('eID', 'get', tep_fill_variable('eID', 'post'));
	$submit_value = tep_fill_variable('submit_value_y');
	$equipment_type_id = tep_fill_variable('equipment_type_id', 'get');
	$warehouse_id = tep_fill_variable('warehouse_id', 'get');
	$equipment_id = tep_fill_variable('equipment_id', 'get');
	$equipment_item_id = tep_fill_variable('equipment_item_id', 'get');
	
	$return_type = tep_fill_variable('return_type', 'get');
		
	$message = '';
	
		
		
?>
<table width="100%" cellspacing="0" cellpadding="0">

	<tr>
		<td width="100%" valign="top">
				<?php
					if (empty($equipment_type_id) && empty($warehouse_id) && empty($equipment_id)) {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxHeading" align="left">Warehouse</td>
									<?php
										$query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_name");
											foreach($database->fetch_array($query) as $result){
											?>
												<td class="pageBoxHeading" align="center"><strong><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?equipment_type_id=<?php echo $result['equipment_type_id']; ?>"><?php echo $result['equipment_type_name']; ?></a></strong></td>
											<?php
									
											}
									?>
								<td width="10" class="pageBoxHeading"></td>
							</tr>
						<?php
							$egData = array();
							$query = $database->query("select w.warehouse_id, wd.name from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = wd.warehouse_id order by wd.name");
								foreach($database->fetch_array($query) as $result){
									
						?>
							<tr>
								<td class="pageBoxContent"><strong><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?warehouse_id=<?php echo $result['warehouse_id']; ?>"><?php echo $result['name']; ?></a></strong></td>
									<?php
										$tquery = $database->query("select equipment_type_id from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_name");
											foreach($database->fetch_array($tquery) as $tresult){
											?>
												<td class="pageBoxContent" align="center"><?php echo tep_fetch_available_equipment_types_count($tresult['equipment_type_id'], $result['warehouse_id']); ?></td>
											<?php
									
											}
									?>
								<td width="10" class="pageBoxContent"></td>
							</tr>
						<?php
								}
						} elseif (!empty($equipment_type_id) && empty($warehouse_id) && empty($equipment_id)) {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxHeading" align="left">Equipment Name</td>
									<?php
											
												$query = $database->query("select w.warehouse_id, wd.name from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = wd.warehouse_id order by wd.name");
													foreach($database->fetch_array($query) as $result){
														?>
														<td class="pageBoxHeading" align="center"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?warehouse_id=<?php echo $result['warehouse_id']; ?>"><?php echo $result['name']; ?></a> / In Field</td>
														<?php
													}
											
									?>
								<td width="10" class="pageBoxHeading"></td>
							</tr>
						<?php
							$egData = array();
                            $count = 0;
							$listing_split = new split_page("select equipment_id, name from " . TABLE_EQUIPMENT . " where equipment_type_id = '" . $equipment_type_id . "' order by name", 40, 'equipment_id');
								if ($listing_split->number_of_rows > 0) {
									$query = $database->query($listing_split->sql_query);
									    foreach($database->fetch_array($query) as $result){
                                            											
								?>
									<tr>
										<td class="pageBoxContent"><strong><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?equipment_type_id=<?php echo $equipment_type_id; ?>&equipment_id=<?php echo $result['equipment_id']; ?>"><?php echo $result['name']; ?></a></strong></td>
											<?php
												
												$tquery = $database->query("select w.warehouse_id from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = wd.warehouse_id order by wd.name");
													foreach($database->fetch_array($tquery) as $tresult){
														$total = tep_fetch_total_equipment_count($result['equipment_id'], $tresult['warehouse_id']);
														$in_warehouse = tep_fetch_available_equipment_count($result['equipment_id'], $tresult['warehouse_id']);
														$in_field = ($total-$in_warehouse);
													?>
														<td class="pageBoxContent" align="center"><?php echo $in_warehouse; ?> / <?php echo $in_field; ?></td>
													<?php
													}
											?>
										<td width="10" class="pageBoxContent"></td>
									</tr>
								<?php
                                                        $count += 1;                                            
                                                        check_dash($count);
										}
								?>
										<tr>
											<td colspan="3">
												<table class="normaltable" cellspacing="0" cellpadding="2">
													<tr>
														<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> items)'); ?></td>
														<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(40, tep_get_all_get_params(array('page', 'info', 'x', 'y'. 'oID', 'page_action'))); ?></td>
													</tr>
												</table>
											</td>
										</tr>
									<?php
								}
						} elseif (empty($equipment_type_id) && !empty($warehouse_id) && empty($equipment_id)) {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxHeading" align="left">Equipment Name</td>
								<td class="pageBoxHeading" align="center">In Warehouse</td>
								<td class="pageBoxHeading" align="center">In Field</td>
								<td width="10" class="pageBoxHeading"></td>
							</tr>
						<?php
							$egData = array();
                            $count =0;
                            $q = "select equipment_id, name from " . TABLE_EQUIPMENT . " WHERE (SELECT count(equipment_item_id) FROM " . TABLE_EQUIPMENT_ITEMS .
                                 " WHERE " . TABLE_EQUIPMENT_ITEMS . ".equipment_id = " . TABLE_EQUIPMENT . ".equipment_id AND " . TABLE_EQUIPMENT_ITEMS . 
                                 ".warehouse_id = ".$warehouse_id.") > 0 order by name";
							$listing_split = new split_page($q, 40, 'equipment_id');
								if ($listing_split->number_of_rows > 0) {
									$query = $database->query($listing_split->sql_query);
									    foreach($database->fetch_array($query) as $result){
												
											?>
												<tr>
                                                        <?php
                                                            $total = tep_fetch_total_equipment_count($result['equipment_id'], $warehouse_id);
                                                            $in_warehouse = tep_fetch_available_equipment_count($result['equipment_id'], $warehouse_id);
                                                            $in_field = ($total-$in_warehouse);
                                                            if ($total > 0) {
                                                            ?>
													<td class="pageBoxContent"><strong><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?warehouse_id=<?php echo $warehouse_id; ?>&equipment_id=<?php echo $result['equipment_id']; ?>"><?php echo $result['name']; ?></a></strong></td>
													<td class="pageBoxContent" align="center"><?php echo $in_warehouse; ?></td>
													<td class="pageBoxContent" align="center"><?php echo $in_field; ?></td>
													<td width="10" class="pageBoxContent"></td>
												</tr>
                                                    <?php
                                                        $count += 1;                                            
                                                        check_dash($count);
                                                            }
                                                    }
									?>
										<tr>
											<td colspan="3">
												<table class="normaltable" cellspacing="0" cellpadding="2">
													<tr>
														<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> items)'); ?></td>
														<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(40, tep_get_all_get_params(array('page', 'info', 'x', 'y'. 'oID', 'page_action'))); ?></td>
													</tr>
                                                    <tr>
                                                        <td class="smallText">Note equipment with two zero counts are not shown.</td>
                                                    </tr>
												</table>
											</td>
										</tr>
									<?php
								}
						} elseif (!empty($equipment_type_id) && !empty($equipment_id)) {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxHeading">Name</td>
									<td class="pageBoxHeading" align="center">Reference Code</td>
									<td class="pageBoxHeading" align="center">Status</td>
									<td class="pageBoxHeading" align="center">Last Checked</td>
									<td class="pageBoxHeading" align="right">Action</td>
							</tr>
						<?php
							$egData = array();
                            $count = 0;
							$listing_split = new split_page("select e.equipment_id, e.name as equipment_name, ei.equipment_item_id, ei.code, ei.date_last_checked, es.equipment_status_name from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT_STATUSES . " es where e.equipment_id = ei.equipment_id and ei.equipment_status_id = es.equipment_status_id and ei.equipment_id = '" . $equipment_id . "' ", '40', 'ei.equipment_item_id');
								if ($listing_split->number_of_rows > 0) {
									$query = $database->query($listing_split->sql_query);
									    foreach($database->fetch_array($query) as $result){
										?>
											<tr>
												<td class="pageBoxContent"><?php echo $result['equipment_name']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['code']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['equipment_status_name']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo (($result['date_last_checked'] > 0) ? date("n/d/Y", $result['date_last_checked']): 'Never'); ?></td>
												<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?eID='.$result['equipment_item_id'].'&page_action=edit&return_type=1&warehouse_id=&equipment_type_id='. $equipment_type_id; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?eID='.$result['equipment_item_id'].'&page_action=delete&return_type=1&warehouse_id=&equipment_type_id='. $equipment_type_id.'&equipment_id='.$equipment_id; ?>">Delete</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT . '?equipment_type_id='.$equipment_type_id.'&equipment_id='.$equipment_id.'&equipment_item_id='.$result['equipment_item_id']; ?>">Details</a></td>
												<td width="10" class="pageBoxContent"></td>
											</tr>
                                                    <?php
                                                        $count += 1;                                            
                                                        check_dash($count);
                                                    }
									?>
									<tr>
										<td colspan="6">
											<table class="normaltable" cellspacing="0" cellpadding="2">
												<tr>
													<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> equipment items)'); ?></td>
													<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(40, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
												</tr>
											</table>
										</td>
									</tr>
								<?php
								}
						} elseif (!empty($warehouse_id) && !empty($equipment_id)) {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxHeading">Name</td>
									<td class="pageBoxHeading" align="center">Reference Code</td>
									<td class="pageBoxHeading" align="center">Status</td>
									<td class="pageBoxHeading" align="center">Last Checked</td>
									<td class="pageBoxHeading" align="right">Action</td>
							</tr>
						<?php
							$egData = array();
                            $count = 0;
							$listing_split = new split_page("select e.equipment_id, e.name as equipment_name, ei.equipment_item_id, ei.code, ei.date_last_checked, es.equipment_status_name from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT_STATUSES . " es where e.equipment_id = ei.equipment_id and ei.equipment_status_id = es.equipment_status_id and ei.equipment_id = '" . $equipment_id . "' and ei.warehouse_id = '" . $warehouse_id . "' ", '40', 'ei.equipment_item_id');
								if ($listing_split->number_of_rows > 0) {
									$query = $database->query($listing_split->sql_query);
									    foreach($database->fetch_array($query) as $result){
										?>
											<tr>
												<td class="pageBoxContent"><?php echo $result['equipment_name']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['code']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['equipment_status_name']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo (($result['date_last_checked'] > 0) ? date("n/d/Y", $result['date_last_checked']): 'Never'); ?></td>
												<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?eID='.$result['equipment_item_id'].'&warehouse_id='.$warehouse_id.'&return_type=1&equipment_type_id=&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?eID='.$result['equipment_item_id'].'&warehouse_id='.$warehouse_id.'&equipment_id='.$equipment_id.'&return_type=1&equipment_type_id=&page_action=delete'; ?>">Delete</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT . '?warehouse_id='.$warehouse_id.'&equipment_id='.$equipment_id.'&equipment_item_id='.$result['equipment_item_id']; ?>">Details</a></td>
												<td width="10" class="pageBoxContent"></td>
											</tr>
                                                    <?php
                                                        $count += 1;                                            
                                                        check_dash($count);
                                                    }
										?>
									<tr>
										<td colspan="6">
											<table class="normaltable" cellspacing="0" cellpadding="2">
												<tr>
													<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> equipment items)'); ?></td>
													<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(40, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
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
				<tr>
					<td width="100%">
					<?php
						if (!empty($equipment_type_id) && empty($warehouse_id) && empty($equipment_id)) {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><?php echo tep_get_equipment_type_name($equipment_type_id); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>" method="get">
							<tr>
								<td>Show Equipment Type: <?php echo tep_draw_equipment_type_type_pulldown('equipment_type_id', $equipment_type_id, array(array('id' => '', 'name' => 'All')), ' onchange="this.form.submit();"'); ?></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>" method="post"><?php echo tep_create_button_submit('back', 'Back'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<?php
						}elseif (empty($equipment_type_id) && !empty($warehouse_id) && empty($equipment_id)) {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><?php echo tep_get_warehouse_name($warehouse_id); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>" method="get">
							<tr>
								<td>Show Warehouse: <?php echo tep_draw_warehouse_pulldown('warehouse_id', $warehouse_id, array(array('id' => '', 'name' => 'All')), ' onchange="this.form.submit();"'); ?></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>" method="post"><?php echo tep_create_button_submit('back', 'Back'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
					} elseif (!empty($equipment_type_id) && !empty($equipment_id)) {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><?php echo tep_get_equipment_name($equipment_id); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>

							<tr>
								<td></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS; ?>?page_action=edit&equipment_type_id=<?php echo $equipment_type_id; ?>&equipment_id=<?php echo $equipment_id; ?>" method="post"><?php echo tep_create_button_submit('add', 'Add'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?equipment_type_id=<?php echo $equipment_type_id; ?>" method="post"><?php echo tep_create_button_submit('back', 'Back'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
					} elseif (!empty($warehouse_id) && !empty($equipment_id)) {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><?php echo tep_get_equipment_name($equipment_id); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								if (!empty($equipment_item_id)) {
									//Work out its current location extra.
									
									/*
										Show Status.
										
										If installed or pending show the address its at or going to.
										Show the Install date and Expected return date.
										
									*/
									$equipment_status_id = tep_fetch_equipment_item_status($equipment_item_id);
									$address_id = tep_fetch_equipment_item_address($equipment_item_id);
										if ($equipment_status_id == 1) {
											$order_id = tep_fetch_equipment_item_order_id($equipment_item_id);
											$address_id = tep_fetch_order_address_id($order_id);
										}
							?>
							<tr>
								<td class="main">Equipment Status: <?php echo tep_fetch_equipment_status_name($equipment_status_id); ?></td>
							</tr>
								<?php
									if ($equipment_status_id > 0) {
										if ($equipment_status_id == 1) {
											$install_stamp = tep_fetch_order_date($order_id);
										} else {
											$install_stamp = tep_fetch_equipment_item_install_date($address_id, $equipment_item_id);
										}
								?>
									<tr>
										<td width="100%">
											<table width="100%" cellspacing="0" cellpadding="0">
												<tr>
													<td class="main"><?php echo (($equipment_status_id == 0) ? 'Proposed ' : ''); ?>Installation Date: </td>
													<td class="main"><?php echo date("n/d/Y", $install_stamp); ?></td>
												</tr>
												<tr>
													<td class="main">Proposed Return Date: </td>
													<td class="main"><?php echo date("n/d/Y", tep_fetch_equipment_item_removal_date($address_id, $equipment_item_id)); ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td height="5"></td>
									</tr>
									<tr>
										<td class="main">Address: </td>
									</tr>
									<?php
										$address_details = tep_fetch_address_details($address_id);
									?>
									<tr>
										<td class="main"><?php echo $address_details['house_number'].' '.$address_details['street_name'].'<br>'.$address_details['city'].'<br>'.$address_details['state_name']; ?></td>
									</tr>
								<?php
									}
								?>
							<?php
								}
							?>
							<tr>
								<td></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php if (empty($equipment_item_id)) { ?><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS; ?>?page_action=edit&warehouse_id=<?php echo $warehouse_id; ?>&equipment_id=<?php echo $equipment_id; ?>" method="post"><?php echo tep_create_button_submit('add', 'Add'); ?></form><?php } ?></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?warehouse_id=<?php echo $warehouse_id; ?>" method="post"><?php echo tep_create_button_submit('back', 'Back'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
					}elseif ($page_action == 'move') {
						$move_from = tep_fill_variable('move_from', 'post');
						$move_to = tep_fill_variable('move_to', 'post');
						
						$move_quantity = tep_fill_variable('move_quantity', 'post', '0');
						
						
						
						$max = 0;
							
						
							if (!empty($move_from)) {
								$max = tep_fetch_available_equipment_count($eID, $move_from);
							}
							if (empty($move_from)) {
								$from_array = array(array('id' => '', 'name' => 'Please Select'));
							} else {
								$from_array = array();
							}
							if (empty($move_to)) {
								$to_array = array(array('id' => '', 'name' => 'Please Select'));
							} else {
								$to_array = array();
							}
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>?eID=<?php echo $eID; ?>&page_action=move_confirm" method="post">
							<tr>
								<td class="pageBoxContent">To move "<?php echo tep_get_equipment_name($eID); ?>" Select the from warehouse, the to warehouse and the quantity.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="main">From Warehouse:</td>
											<td><?php echo tep_draw_warehouse_pulldown('move_from', $move_from, $from_array, ' onchange="this.form.submit();"'); ?></td>
										</tr>
										<tr>
											<td class="main">To Warehouse:</td>
											<td><?php echo tep_draw_warehouse_pulldown('move_to', $move_to, $to_array); ?></td>
										</tr>
									
									<?php
										if (!empty($move_from)) {
											if ($max < 1) {
											?>
											<tr>
												<td colspan="2" class="main">There is current no "<?php echo tep_get_equipment_name($eID); ?>" in your selected from warehouse.  Please try again.</td>
											</tr>
											<?php
											} else {
											?>
											<tr>
												<td class="main">Quantity:</td>
												<td class="main"><input type="text" name="move_quantity" value="<?php echo $move_quantity; ?>" onblur="if (this.value > <?php echo $max; ?>) { alert('There is not currently that number of stock in the From Warehouse.'); this.value = <?php echo $max; ?>; return false; }" /> (max <?php echo $max; ?>)</td>
											</tr>
											<?php
											}
										}
									?>
									</table>
								</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('move', 'Move Equipment', ' name="submit_value"'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
					} else {
					?>
					
				<?php
					}
				?>
		</td>
	</tr>
                            <tr>
                                <td height="15"><img src="images/pixel_trans.gif" height="15" width="1"></td>
                            </tr>
                            <tr>
                            <td><font size=3><a href="admin_equipment_report_print.php?equipment_type_id=<?php echo $equipment_type_id;?>&warehouse_id=<?php echo $warehouse_id;?>&equipment_id=<?php echo $equipment_id;?>" target="_blank">Print</a></font></td>
                            </tr>
</table>
</td>
</tr>
</table>
<?php
    function check_dash($count) {
        if ($count % 10 == 0) {
            ?>
                <tr>
                    <td colspan="5">
                    <?php
                        for ($i=0;$i<200;$i++) {
                            echo "-";
                        }
?>
                    </td>
                </tr>
            <?php
        }
    }
?>