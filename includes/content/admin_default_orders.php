<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$doID = tep_fill_variable('doID', 'get', tep_fill_variable('doID', 'post'));
	$service_level_id = tep_fill_variable('service_level_id', 'get', tep_fill_variable('service_level_id', 'post'));
	
	$agency_id = tep_fill_variable('agency_id', 'get', tep_fill_variable('agency_id', 'post'));
	$order_type_id = tep_fill_variable('order_type_id', 'get', tep_fill_variable('order_type_id', 'post'));
	$user_id = tep_fill_variable('user_id', 'get', tep_fill_variable('user_id', 'post'));
	
	$show_service_level_id = tep_fill_variable('show_service_level_id', 'get', tep_fill_variable('show_service_level_id', 'post'));
	$show_agency_id = tep_fill_variable('show_agency_id', 'get', tep_fill_variable('show_agency_id', 'post'));
	$show_order_type_id = tep_fill_variable('show_order_type_id', 'get', tep_fill_variable('show_order_type_id', 'post'));
	$show_user_id = tep_fill_variable('show_user_id', 'get', tep_fill_variable('show_user_id', 'post'));
	
	
	$submit_value = tep_fill_variable('submit_value_y');
	$page = tep_fill_variable('page', 'get', '1');


	$message = '';
	
	
		if (($page_action == 'edit_confirm') || ($page_action == 'add_confirm')) {
			$run = true;
				if  ($submit_value == '') {
					$page_action = 'edit';
					$run = false;
				}
				if ($run) {
			
					$insert_agency_id = tep_fill_variable('agency_id');
					$insert_user_id = tep_fill_variable('user_id');
					$insert_service_level_id = tep_fill_variable('service_level_id');
					$insert_order_type_id = tep_fill_variable('order_type_id');
					$insert_name = tep_fill_variable('name');
						if (!empty($insert_user_id)) {
							$insert_agency_id = '';
						}
					$insert_equipment_group_answer_id = tep_fill_variable('equipment_group_answer_id');
						if($page_action == 'add_confirm') {
							$database->query("insert into " . TABLE_DEFAULT_ORDERS . " (order_type_id, service_level_id, agency_id, user_id, name) values ('" . $insert_order_type_id . "', '" . $insert_service_level_id . "', '" . $insert_agency_id . "', '" . $insert_user_id . "', '" . $insert_name . "')");
							$default_order_id = $database->insert_id();
						} else {
							//Edit Time.
							$database->query("update " . TABLE_DEFAULT_ORDERS . " set order_type_id = '" . $insert_order_type_id . "', service_level_id = '" . $insert_service_level_id . "', agency_id = '" . $insert_agency_id . "', user_id = '" . $insert_user_id . "', name = '" . $insert_name . "' where default_order_id = '" . $doID . "' limit 1");
							$database->query("delete from " . TABLE_DEFAULT_ORDERS_ITEMS . " where default_order_id = '" . $doID . "'");
							
							$default_order_id = $doID;
						}
						if (is_array($insert_equipment_group_answer_id)) {
							reset($insert_equipment_group_answer_id);
								while(list($key, $val) = each($insert_equipment_group_answer_id)) {
									if (is_array($val)) {
										$items_string = '';
											for ($n = 0, $m = count($val); $n < $m; $n++) {
												if (!empty($val[$n])) {
														if (!empty($items_string)) {
															$items_string .= ', ';
														}
													$items_string .= $val[$n];
												}
											}
										$database->query("insert into " . TABLE_DEFAULT_ORDERS_ITEMS . " (default_order_id, equipment_group_id, equipment_group_answers) values ('" . $default_order_id . "', '" . $key . "', '" . $items_string . "')");
									}
								}
						}
					tep_redirect(FILENAME_ADMIN_DEFAULT_ORDERS.'?agency_id='.$agency_id.'&user_id='.$user_id.'&service_level_id='.$service_level_id.'&order_type_id='.$order_type_id.'&page='.$page);
				}
			
		} elseif ($page_action == 'delete_confirm') {
		
			$database->query("delete from " . TABLE_DEFAULT_ORDERS . " where default_order_id = '" . $doID . "' limit 1");
			$database->query("delete from " . TABLE_DEFAULT_ORDERS_ITEMS . " where default_order_id = '" . $doID . "' limit 1");
			tep_redirect(FILENAME_ADMIN_DEFAULT_ORDERS.'?agency_id='.$agency_id.'&user_id='.$user_id.'&service_level_id='.$service_level_id.'&order_type_id='.$order_type_id.'&page='.$page);
		}
		if ($page_action == 'edit') {
				if (is_numeric($doID)) {
					$edit_message = 'Please make the required changes and press "Update" to confirm.';
					$button_value = 'Update';
				} else {
					$edit_message = 'Please enter the required details and press "Add" to insert it into the database.';
					$button_value = 'Add';
				}
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_equipment_items')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_equipment_items'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
				<?php
						
					$egData = array();
					$listing_split = new split_page("select do.default_order_id, do.name, ud.user_id, ud.firstname, ud.lastname, a.name as agency_name, sld.name as service_level_name, ot.name as order_type_name from " . TABLE_DEFAULT_ORDERS . " do left join " . TABLE_USERS_DESCRIPTION . " ud on (do.user_id = ud.user_id) left join " . TABLE_AGENCYS . " a on (do.agency_id = a.agency_id) left join " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld on (do.service_level_id = sld.service_level_id), " . TABLE_ORDER_TYPES . " ot where do.order_type_id = ot.order_type_id" . ((!empty($show_order_type_id)) ? " and do.order_type_id = '" . $show_order_type_id . "'":'') . ((!empty($show_service_level_id)) ? " and do.service_level_id = '" . $show_service_level_id . "'":'') . ((!empty($show_agency_id)) ? " and do.agency_id = '" . $show_agency_id . "'" : '') . ((!empty($show_user_id)) ? " and do.user_id = '" . $show_user_id . "'":'') . " order by order_type_name, ud.firstname, ud.lastname, service_level_name, agency_name", '20', 'do.default_order_id');
						if ($listing_split->number_of_rows > 0) {
							?>
							<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
								<tr>
									<td class="pageBoxHeading">Name</td>
									<td class="pageBoxHeading">Order Type</td>
									<td class="pageBoxHeading" align="center">Match Condition</td>
									<td class="pageBoxHeading" align="left">Optional Items</td>
									<td class="pageBoxHeading" align="right">Action</td>
									<td width="10" class="pageBoxHeading"></td>
								</tr>
							<?php
								$result_loop = 0;
								$query = $database->query($listing_split->sql_query);
								    foreach($database->fetch_array($query) as $result){
											if ($result_loop > 0) {
												echo '<tr><td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td></tr>';
											}
										$result_loop++;
											if ($doID == $result['default_order_id']) {
												$doData = $result;
											}
										$answers_string = '';
										$sub_query = $database->query("select doi.equipment_group_id, doi.equipment_group_answers, eg.name from " . TABLE_DEFAULT_ORDERS_ITEMS . " doi, " . TABLE_EQUIPMENT_GROUPS . " eg where doi.default_order_id = '" . $result['default_order_id'] . "' and doi.equipment_group_id = eg.equipment_group_id order by eg.name");
										    foreach($database->fetch_array($sub_query) as $sub_result){
													if (!empty($answers_string)) {
														$answers_string .= '<br><br>';
													}
												$answers_string .= $sub_result['name'];
												$explode = explode(', ', $sub_result['equipment_group_answers']);
													for ($n = 0, $m = count($explode); $n < $m; $n++) {
														$item_query = $database->query("select name from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_answer_id = '" . $explode[$n] . "' limit 1");
														$item_result = $database->fetch_array($item_query);
														$answers_string .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'.$item_result['name'];
													}
											}
							?>
								<tr>
									<td class="pageBoxContent" valign="top"><?php echo $result['name']; ?></td>
									<td class="pageBoxContent" valign="top"><?php echo $result['order_type_name']; ?></td>
									<td class="pageBoxContent" align="center" valign="top"><?php echo (!empty($result['service_level_name']) ? $result['service_level_name'].'<br>' :'').((!empty($result['user_id'])) ? $result['firstname'] . ' ' . $result['lastname'].'<br>':'').(!empty($result['agency_name']) ? $result['agency_name'].'<br>':''); ?></td>
									<td class="pageBoxContent" align="left" valign="top"><?php echo $answers_string; ?></td>
									<td class="pageBoxContent" align="right"><a href="<?php echo PAGE_URL; ?>?page_action=edit&page=<?php echo $page; ?>&show_agency_id=<?php echo $show_agency_id; ?>&show_user_id=<?php echo $show_user_id; ?>&show_service_level_id=<?php echo $show_service_level_id; ?>&show_order_type_id=<?php echo $show_order_type_id; ?>&doID=<?php echo $result['default_order_id']; ?>">Edit</a> | <a href="<?php echo PAGE_URL; ?>?page_action=delete&page=<?php echo $page; ?>&show_agency_id=<?php echo $show_agency_id; ?>&show_user_id=<?php echo $show_user_id; ?>&show_service_level_id=<?php echo $service_level_id; ?>&show_order_type_id=<?php echo $show_order_type_id; ?>&doID=<?php echo $result['default_order_id']; ?>">Delete</a></td>
									<td width="10" class="pageBoxContent"></td>
								</tr>
							<?php
									}
									?>
									<tr>
										<td colspan="8">
											<table class="normaltable" cellspacing="0" cellpadding="2">
												<tr>
													<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> default order items)'); ?></td>
													<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<?php
						} else {
					?>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="pageBoxContent">No default orders could be found.  Please use the menu on the right to add on or change the options to try searching again.</td>
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
							if (is_numeric($doID)) {
								$query = $database->query("select order_type_id, name, service_level_id, agency_id, user_id, equipment_group_id, equipment_group_answer_id from " . TABLE_DEFAULT_ORDERS . " where default_order_id = '" . $doID . "' limit 1");
								$result = $database->fetch_array($query);
								
								$answers_array = array();
								$answers_query = $database->query("select equipment_group_id, equipment_group_answers from " . TABLE_DEFAULT_ORDERS_ITEMS . " where default_order_id = '" . $doID . "'");
								    foreach($database->fetch_array($answers_query) as $answers_result){
										$explode = explode(', ', $answers_result['equipment_group_answers']);
										$answers_array[$answers_result['equipment_group_id']] = $explode;
									}
								
								$result['equipment_group_answer_id'] = $answers_array;
							} else {
								$result = array('order_type_id' => $show_order_type_id,
														'name' => '',
														 'service_level_id' => $show_service_level_id,
														 'agency_id' => $show_agency_id,
														 'user_id' => $show_user_id,
														 'equipment_group_id' => '',
														 'equipment_group_answer_id' => array());
							}
						
							//Set the values.  Do it here again in case there was a failed submit.
						$order_type_id = tep_fill_variable('order_type_id', 'post', tep_fill_variable('order_type_id', 'get', $result['order_type_id']));
						$service_level_id = tep_fill_variable('service_level_id', 'post', tep_fill_variable('service_level_id', 'get', $result['service_level_id']));
						$agency_id = tep_fill_variable('agency_id', 'post', tep_fill_variable('agency_id', 'get', $result['agency_id']));
						$user_id = tep_fill_variable('user_id', 'post', tep_fill_variable('user_id', 'get', $result['user_id']));
						$name = tep_fill_variable('name', 'post', tep_fill_variable('name', 'get', $result['name']));
						
						$equipment_group_answer_id = tep_fill_variable('equipment_group_answer_id', 'post', $result['equipment_group_answer_id']);
							if (!empty($user_id) && !empty($agency_id)) {
								if ($agency_id != tep_fetch_user_agency_id($user_id)) {
									$agency_id = '';
								}
							}
							if (empty($order_type_id)) {
								$order_type_id = tep_fetch_default_order_type_id();
							}
							if (!empty($user_id) && empty($agency_id)) {
								//Manually fetch.
								$agency_id = tep_fetch_user_agency_id($user_id);
							}
							if (empty($service_level_id) && !empty($user_id)) {
								//$service_level_id = tep_get_service_level_id($user_id);
							}
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_DEFAULT_ORDERS . '?page_action='.((is_numeric($doID)) ? ('edit_confirm&doID='.$doID) : 'add_confirm').'&page='.$page; ?>">
							<tr>
								<td class="pageBoxContent" colspan="2"><?php echo $edit_message; ?></td>
							</tr>
							<tr>
								<td class="pageBoxContent" colspan="2"><i>Note: For information use the help link.</i></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">Name: </td><td><input type="text" name="name" value="<?php echo $name; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Order Type: </td><td><?php echo tep_draw_order_type_pulldown('order_type_id', $order_type_id); ?></td>
							</tr>
							<tr>
								<td class="main">Service Level: </td><td><?php echo tep_draw_service_level_pulldown('service_level_id', $service_level_id, ' onchange="this.form.submit();"', false, array(array('id' => '', 'name' => 'Any')), false); ?></td>
							</tr>
							<tr>
								<td class="main">Agency: </td><td><?php echo tep_draw_agency_pulldown('agency_id', $agency_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any')), false); ?></td>
							</tr>
							<?php
								if (!empty($agency_id)) {
								?>
								<tr>
									<td class="main">Agent: </td><td><?php echo tep_draw_agent_pulldown('user_id', $user_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any')), $agency_id); ?></td>
								</tr>
								<?php
								}
								
								//Now onto the items
							?>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
							</tr>
							<?php
						
								if (!empty($order_type_id)) {
								?>
								<tr>
									<td class="main"><strong>Optional Questions: </strong></td>
								</tr>
								<?php
									$query = $database->query("select equipment_group_id, name from " . TABLE_EQUIPMENT_GROUPS . " where order_type_id = '" . $order_type_id . "'");
									$items_array = array();
									$run_count = 0;
                                        foreach($database->fetch_array($query) as $result){
												if ($run_count > 0) {
													?>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
													</tr>
													<?php
												}
											$run_count ++;
										?>
										<tr>
											<td class="main" valign="top"><em>&nbsp;&nbsp;<?php echo $result['name']; ?></em></td><td class="main"><?php
											$item_query = $database->query("select equipment_group_answer_id, name from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_id = '" . $result['equipment_group_id'] . "' order by name");
											$found_count = 0;
											$string = '';
											    foreach($database->fetch_array($item_query) as $item_result){
													$found_count++;
														if (!empty($string)) {
															$string .= '<br>';
														}
														if (isset($equipment_group_answer_id[$result['equipment_group_id']]) && in_array($item_result['equipment_group_answer_id'], $equipment_group_answer_id[$result['equipment_group_id']])) {
															$checked = ' CHECKED';
														} else {
															$checked = '';
														}
													$string .= '<input type="checkbox" name="equipment_group_answer_id['.$result['equipment_group_id'].'][]" value="'.$item_result['equipment_group_answer_id'].'"'.$checked.'> '.$item_result['name'];
												}
												if ($found_count > 0) {
													echo $string;
												} else {
													echo 'There are no answers for this question.';
												}
												?>
											</td>
										</tr>
										<?php
										}
								}	
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit(strtolower(str_replace(' ', '_', $button_value)), '', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_DEFAULT_ORDERS.'?user_id='.$user_id.'&show_agency_id='.$show_agency_id.'&show_service_level_id='.$show_service_level_id.'&show_order_type_id='.$show_order_type_id.'&page='.$page; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
								<td class="pageBoxContent">Are you sure you wish to delete this Default Order?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											
											<td align="left"><form action="<?php echo PAGE_URL; ?>?page_action=delete_confirm&page=<?php echo $page; ?>&show_agency_id=<?php echo $show_agency_id; ?>&show_user_id=<?php echo $show_user_id; ?>&show_service_level_id=<?php echo $show_service_level_id; ?>&show_order_type_id=<?php echo $show_order_type_id; ?>&doID=<?php echo $doID; ?>" method="post"><?php echo tep_create_button_submit('delete', 'Delete Confirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form></td>
											<td align="right"><form action="<?php echo PAGE_URL; ?>?page_action=edit&page=<?php echo $page; ?>&show_agency_id=<?php echo $show_agency_id; ?>&show_user_id=<?php echo $show_user_id; ?>&show_service_level_id=<?php echo $show_service_level_id; ?>&show_order_type_id=<?php echo $show_order_type_id; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
											
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
							<td class="pageBoxHeading"><b>Default Order Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit an Default Order Item or press Add to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>" method="get">
						
						<tr>
							<td class="main">Order Type: <?php echo tep_draw_order_type_pulldown('show_order_type_id', $show_order_type_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any'))); ?></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="main">Service Level: <?php echo tep_draw_service_level_pulldown('show_service_level_id', $show_service_level_id, ' onchange="this.form.submit();"', false, array(array('id' => '', 'name' => 'Any')), false); ?></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="main">Agency: <?php echo tep_draw_agency_pulldown('show_agency_id', $show_agency_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any'))); ?></td>
						</tr>
						<?php
							if (!empty($agency_id)) {
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
							</tr>
							<tr>
								<td class="main">Agent: <?php echo tep_draw_agent_pulldown('show_user_id', $show_user_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any')), $agency_id); ?></td>
							</tr>
							<?php
							}
						?>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td><?php echo tep_create_button_submit('update', 'Update'); ?></td>
						</tr>
						</form>
						<tr>
							<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=edit&page=<?php echo $page; ?>&show_agency_id=<?php echo $show_agency_id; ?>&show_user_id=<?php echo $show_user_id; ?>&show_service_level_id=<?php echo $show_service_level_id; ?>&show_order_type_id=<?php echo $show_order_type_id; ?>" method="post">
						<tr>
							<td height="5">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="right"><?php echo tep_create_button_submit('add', 'Add'); ?></td>
										<td aign="right"></td>
									</tr>
								</table>
							</td>
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