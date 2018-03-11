<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$aID = tep_fill_variable('aID', 'get');
	$user_group_id = tep_fill_variable('user_group_id', 'get');
	$search_name = tep_fill_variable('search_name', 'get');
	$submit_value = tep_fill_variable('submit_value_y', 'post');
	$start_letter = tep_fill_variable('start_letter', 'get', '');
	$page = tep_fill_variable('page', 'get', '');
	
	$view = tep_fill_variable('view', 'get', '');

	$message = '';
	$pages = tep_fill_variable('pages', 'post', array());
		if (!empty($submit_value)) {
			$name = tep_fill_variable('name', 'post');
			$office = tep_fill_variable('office', 'post');
			$contact_name = tep_fill_variable('contact_name', 'post');
			$contact_phone = tep_fill_variable('contact_phone', 'post');
			$service_level_id = tep_fill_variable('service_level_id', 'post');
			$billing_method_id = tep_fill_variable('billing_method_id', 'post');
			$parent_agency_id = tep_fill_variable('agency_parent_id', 'post');
			$discount_type = tep_fill_variable('discount_type', 'post');
			$discount_amount = tep_fill_variable('discount_amount', 'post');
			$address = tep_fill_variable('address', 'post');
			$agency_status_id = tep_fill_variable('agency_status_id', 'post', '0');
			
				if ($discount_type > 0) {
					if (substr($discount_type, 0, 1) == '+') {
						$discount_type = substr($discount_type, 1);
					}
				} 
			
				
				if (!$error->get_error_status('admin_new_agencys')) {
						if ($page_action == 'edit') {
							
							$database->query("update " . TABLE_AGENCYS . " set name = '" . $name . "', office = '" . $office . "', service_level_id = '" . $service_level_id . "', billing_method_id = '" . $billing_method_id . "', address = '" . addslashes($address) . "', contact_name = '" . $contact_name . "', contact_phone = '" . $contact_phone . "', parent_agency_id = '" . $parent_agency_id . "', discount_type = '" . $discount_type . "', discount_amount = '" . $discount_amount . "', agency_status_id = '" . $agency_status_id . "' where agency_id = '" . $aID . "' limit 1");
							
							$message = 'Successfully Updated';
						} else {
							/*$password = substr(md5(mktime()), 4, 6);
							$database->query("insert into " . TABLE_AGENCYS . " (email_address, password, agent_id, billing_method_id, service_level_id, agency_id) values ('" . $email_address . "', '" . md5($password) . "', '" . $agent . "', '" . $billing_method_id . "', '" . $service_level_id . "', '" . $agency_id . "')");
							$aID = $database->insert_id();
							$database->query("insert into " . TABLE_AGENCYS_DESCRIPTION . " (agency_id, firstname, lastname, street_address, postcode, city, county_id, state_id) values ('" . $aID . "', '" . $firstname . "', '" . $lastname . "', '" . $street_address . "', '" . $postcode . "', '" . $city . "', '" . $county_id . "', '" . $state_id . "')");
							$database->query("insert into " . TABLE_AGENCYS_TO_USER_GROUPS . " (agency_id, user_group_id) values ('" . $aID . "', '" . $user_group_id . "')");
							
							$email_template = new email_template('account_create');
							$email_template->load_email_template();
							$email_template->set_email_template_variable('EMAIL_ADDRESS', $email_address);
							$email_template->set_email_template_variable('PASSWORD', $password);
							$email_template->parse_template();
							$email_template->send_email($email_address, $firstname.', '.$lastname);
							
							$message = 'Successfully Inserted, user has been emailed new password';*/
						}
					$page_action = '';
					$aID = '';
				}
			
		}
		if ($page_action == 'delete_confirm') {
			$merge_agency_id = tep_fill_variable('merge_agency_id');
			
			$database->query("delete from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "' limit 1");
			//echo"delete from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "' limit 1" . '<br>';
				if (!empty($merge_agency_id)) {
					$database->query("update " . TABLE_USERS . " set agency_id = '" . $merge_agency_id . "' where agency_id = '" . $aID . "'");
					//echo "update " . TABLE_USERS . " set agency_id = '" . $merge_agency_id . "' where agency_id = '" . $aID . "'" . '<br>';
				}
			$page_action = '';
			$aID = '';
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_agencys')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_agencys'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if (($page_action != 'edit')&&($page_action != 'add')) {
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td width="40%" class="pageBoxHeading">Agency Name</td>
						<td width="40%" class="pageBoxHeading" align="center">Agents</td>
						<td width="40%" class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$uData = array();
					$listing_split = new split_page("select agency_id, name, office, parent_agency_id from " . TABLE_AGENCYS . " where agency_status_id = '0' " . (($view == 'all') ? '': " and parent_agency_id = ''") . ((!empty($start_letter)) ? " and name like '".$start_letter."%'" : '') . ((!empty($search_name)) ? " and (name like '%" .$search_name."' or name like '".$search_name."%' or name = '" . $search_name . "') " : '') . " order by name", '20', 'agency_id');
					$query = $database->query($listing_split->sql_query);
					    foreach($database->fetch_array($query) as $result){
							$count_query = $database->query("select count(u.user_id) as count from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.users_status = '1'");
							$count_result = $database->fetch_array($count_query);
							$total_agents = $count_result['count'];
							$active_agents = $count_result['count'];
							
							$count_query = $database->query("select count(u.user_id) as count from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.users_status = '0'");
							$count_result = $database->fetch_array($count_query);
							$total_agents += $count_result['count'];
							$inactive_agents = $count_result['count'];
								if ($result['agency_id'] == $aID) {
									$uData = $result;
									$uData['count'] = $total_agents;
								}
						
						
				?>
					<tr>
						<td width="40%" class="pageBoxContent"><?php echo $result['name'] . ((!empty($result['office'])) ? (' (' . $result['office'] . ')') : ''); ?></td>
						<td width="40%" class="pageBoxContent" align="center"><?php echo $total_agents . ' (' .$active_agents.' Active & ' . $inactive_agents . ' New)'; ?></td>
						<td width="40%" class="pageBoxContent" align="right" NOWRAP>&nbsp;&nbsp;<a href="<?php echo FILENAME_ADMIN_USERS . '?user_group_id=1&show_agency_id='.$result['agency_id']; ?>">View Agents</a>&nbsp;|&nbsp;<a href="<?php echo FILENAME_ADMIN_NEW_AGENCYS . '?aID='.$result['agency_id'].'&page_action=edit&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_ORDERS . '?order_status=&agency_id='.$result['agency_id']; ?>">Orders</a>&nbsp;|&nbsp;<a href="<?php echo FILENAME_ADMIN_NEW_AGENCYS . '?aID='.$result['agency_id'].'&page_action=delete&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">Delete</a><br /><a href="<?php echo FILENAME_ADMIN_NEW_USERS . '?user_group_id=1&show_agency_id='.$result['agency_id']; ?>">View New Agents</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
					?>
					<tr>
						<td colspan="5">
							<table class="normaltable" cellspacing="0" cellpadding="2">
								<tr>
									<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> agencies)'); ?></td>
									<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			<?php
				} else {
					if (!empty($aID)) {
						//Edit
						$agency_query = $database->query("select name, office, service_level_id, billing_method_id, address, contact_name, contact_phone, parent_agency_id, discount_type, discount_amount from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "' limit 1");
						$agency_result = $database->fetch_array($agency_query);
					} else {
						//Add
						
					}
				
			?>
			<?php
				if($page_action=='edit') {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEW_AGENCYS . '?page_action=edit&aID='.$aID.'&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">
			<?php
				} else {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEW_AGENCYS . '?page_action=add'.'&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">
			<?php
				}
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxContent" width="120">Agency Name</td><td class="pageBoxContent"><textarea name="name"><?php echo $agency_result['name']; ?></textarea></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Office Name</td><td class="pageBoxContent"><input type="text" name="office" value="<?php echo $agency_result['office']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Agency Contact Name</td><td class="pageBoxContent"><input type="text" name="contact_name" value="<?php echo $agency_result['contact_name']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Agency Contact Phone</td><td class="pageBoxContent"><input type="text" name="contact_phone" value="<?php echo $agency_result['contact_phone']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" valign="top" width="120">Agency Address</td><td class="pageBoxContent"><textarea name="address"><?php echo $agency_result['address']; ?></textarea></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Default Billing Method</td><td class="pageBoxContent"><?php echo tep_draw_billing_method_pulldown('billing_method_id', $agency_result['billing_method_id'], '', false); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Default Service Level</td><td class="pageBoxContent"><?php echo tep_draw_service_level_pulldown('service_level_id', $agency_result['service_level_id'], '', false); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Discount Type</td><td class="pageBoxContent"><?php echo tep_generate_discount_pulldown_menu('discount_type', $agency_result['discount_type']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Discount Amount <br />(+ or - to add for amount or percent (under 100) for percent)</td><td valign="top" class="pageBoxContent"><input type="text" name="discount_amount" value="<?php echo $agency_result['discount_amount']; ?>" /></td>
					</tr>
					<tr>
						<td height="10" width="1"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2"><em>If this Agency is merely an incorrect spelling then select the real one below.</em></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">True Agency</td><td class="pageBoxContent"><?php echo tep_draw_agency_pulldown('agency_parent_id', $agency_result['parent_agency_id'], '', array(array('id' => '0', 'name' => 'None'))); ?></td>
					</tr>

				</table>
			<?php
				}
			?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
		<?php
			if (!empty($aID)) {
		?>
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
								<td class="pageBoxContent">Make your required changes and press "Update" below or press "Cancel" to cancel your changes.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">Set as Active: <input type="checkbox" name="agency_status_id" value="1" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_NEW_AGENCYS.'?page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					<?php
						} elseif ($page_action == 'delete') {
					?>
						<form action="<?php echo FILENAME_ADMIN_NEW_AGENCYS.'?page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name.'&aID='.$aID.'&page_action=delete_confirm'; ?>" method="post">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete "<?php echo $uData['name']; ?>"?  This action can not be undone.  <?php if ($uData['count'] > 0) { ?>As there are currently agents assigned to this agency you must specify another agency to move them to.<?php } ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								if ($uData['count'] > 0) {
							?>
							<tr>
								<td class="main">Merge with: <?php echo tep_draw_agency_pulldown('merge_agency_id', '', '', array(), $aID, true); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('delete', 'Delete'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_NEW_AGENCYS.'?page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					<?php
						} elseif (!empty($page_action)) {
							$extra_data_query = $database->query("select name from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "'");
							$extra_data_result = $database->fetch_array($extra_data_query);
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><b>Viewing <?php echo $uData['name']; ?></b></td>
							</tr>
							
							<?php
								if (!empty($uData['parent_agency_id'])) {
									$query = $database->query("select name from " . TABLE_AGENCYS . " where agency_id = '" . $uData['parent_agency_id'] . "' limit 1");
									$result = $database->fetch_array($query);
									
							?>
							<tr>
								<td class="pageBoxContent">Parent Agency: <?php echo $result['name']; ?></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Click Edit below to edit this Agency.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEW_AGENCYS . '?page_action=edit&aID='.$aID.'&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>"><?php echo tep_create_button_submit('edit', 'Edit'); ?><!--<input type="submit" value="Edit">--></form></td>
											<td align="right"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEW_AGENCYS.'?page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name ; ?>"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?><!--<input type="submit" value="Cancel">--></form></td>
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
		<?php
			} else {
				if (!empty($page_action)) {
		?>
				<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Insert the details and when you are done press the Create button below or press Cancel to go back to the previous page.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><?php echo tep_create_button_submit('create', 'Create', ' name="submit_value"'); ?><!--<input type="submit" value="Create" name="submit_value">--></form><form action="<?php echo FILENAME_ADMIN_NEW_AGENCYS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
						</table>
			<?php
				} else {
			?>
			<table width="250" cellspacing="0" celpadding="0" class="pageBox">
				<tr>
					<td class="pageBoxHeading"><b>Agency Options</b></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td class="pageBoxContent">Click edit to edit an agency.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<form action="<?php echo FILENAME_ADMIN_NEW_AGENCYS; ?>" method="get">
						<tr>
							<td class="main"><input type="checkbox" name="view" value="all"<?php echo (($view == 'all') ? ' CHECKED' : ''); ?> />&nbsp;&nbsp;Show All Agencys </td>
						</tr>
						<tr>
							<td class="main">Show agencies with name like: <input type="text" name="search_name" value="<?php echo $search_name; ?>" /></td>
						</tr>
						<tr>
							<td class="main">Show Agencies starting with <select name="start_letter"><?php
							$query = $database->query("select LEFT(name, 1) as letter from " . TABLE_AGENCYS . " " . (($view == 'all') ? '': " where parent_agency_id = ''") . " group by letter order by letter");
							echo '<option value="">Any</option>';
							    foreach($database->fetch_array($query) as $result){
										if ($start_letter == strtolower($result['letter'])) {
											$selected = ' SELECTED';
										} else {
											$selected = '';
										}
									echo '<option value="'.strtolower($result['letter']).'"' . $selected . '>'.strtoupper($result['letter']).'</option>';
								}
							?></select></td>
						</tr>
						<tr>
							<td align="right"><?php echo tep_create_button_submit('update', 'Update'); ?></td>
						</tr>
					</form>
				</tr>
			</table>
		<?php
				}
			}
		?>
		</td>
	</tr>
</table>