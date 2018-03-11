<?php
	$live = true;
	$live_email = 'realtysp@yahoo.com'; //live email (admin)

	$page_action = tep_fill_variable('page_action', 'get');
	$uID = tep_fill_variable('uID', 'get');
	$show_user_group_id = tep_fill_variable('show_user_group_id', 'get', '1');
	$search_name = tep_fill_variable('search_name', 'get');
	$search_email = tep_fill_variable('search_email', 'get');
	$submit_value = tep_fill_variable('submit_value_y', 'post');
	$show_agency_id = tep_fill_variable('show_agency_id', 'get', '');
	$start_letter = tep_fill_variable('start_letter', 'get', '');
	$start_letter_type = tep_fill_variable('start_letter_type', 'get', '');
	$show_service_level_id = tep_fill_variable('show_service_level_id', 'get', '');
	$search_mrsid = tep_fill_variable('search_mrsid', 'get', '');
	$search_status = tep_fill_variable('search_status', 'get', '1');
	$pages = tep_fill_variable('pages', 'post', array());
	
	$message = array();
	$firstname = array();
	$lastname = array();
	$name = array();
	$user_email = array();
	$x=0;
	
	if ($page_action == 'sendemails') {
		//send emails
		$uData = array();
					$listing_split = new split_page("select u.user_id, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.agency_id, u.active_status, u.accounts_payable, u.order_hold, u.personal_invoice from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and ".((!empty($show_user_group_id)) ? (($show_user_group_id == '5') ? "(utug.user_group_id = '" . $show_user_group_id . "' or u.accounts_payable = '1') and " : "utug.user_group_id = '" . $show_user_group_id . "' and ") : '').((!empty($search_name)) ? ("((ud.firstname like '" . $search_name . "%' or ud.firstname = '" . $search_name . "' or ud.firstname like '%" . $search_name . "' or ud.firstname like '%" . $search_name . "%') or (ud.lastname = '" . $search_name . "' or ud.lastname like '" . $search_name . "%' or ud.lastname like '%" . $search_name . "' or ud.lastname like '%" . $search_name . "%')) and ") : '').((!empty($search_email)) ? ("(u.email_address like '" . $search_email . "%' or u.email_address = '" . $search_email . "' or u.email_address like '%" . $search_email . "' or u.email_address like '%" . $search_email . "%') and ") : '').((!empty($search_mrsid)) ? (($search_mrsid == 'none') ? "u.agent_id = '' and " : "(u.agent_id like '" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "' or u.agent_id = '" . $search_mrsid . "') and ") : '')."utug.user_group_id = ug.user_group_id" . ((!empty($show_agency_id)) ? " and (a.agency_id = '" . $show_agency_id . "' or a.parent_agency_id = '" . $show_agency_id . "')" : '') . ((!empty($start_letter)) ? (($start_letter_type == 'any') ? " and (ud.firstname like '".$start_letter."%' or ud.lastname like '".$start_letter."%')" : (($start_letter_type == 'first') ? " and (ud.firstname like '".$start_letter."%')" : " and (ud.lastname like '".$start_letter."%')")) : '') . ((!empty($show_service_level_id)) ? " and (utug.user_group_id != '1' or u.service_level_id = '" . $show_service_level_id . "')" : '') . ((!empty($search_status)) ? (($search_status == '1') ? " and (u.active_status = '1') " : " and (u.active_status = '0') ") : '') . " order by ud.lastname, ud.firstname", '20', 'u.user_id');
			if ($listing_split->number_of_rows > 0) {
				$query = $database->query($listing_split->sql_query);
				    foreach($database->fetch_array($query) as $result){
						if ($result['user_id'] == $uID) {
							$uData = $result;
						}

						//check for orders and send the emails
						$order_sql = "SELECT * FROM " . TABLE_USERS_DESCRIPTION . " as u, " . TABLE_ORDERS . " as o, 
						" . TABLE_ADDRESSES . " as a 
						WHERE u.user_id = o.user_id 
						AND o.user_id = " . $result['user_id'] . " 
						AND o.address_id = a.address_id 
						AND o.order_type_id = 1 
						AND o.address_id NOT IN (SELECT address_id FROM " . TABLE_ORDERS . " 
						WHERE (order_type_id = 3 AND order_status_id = 3) 
						OR (order_type_id = 3 AND order_status_id = 4)
						OR (order_type_id = 1 AND order_status_id = 4) 
						OR (order_type_id = 1 AND order_status_id = 5))
						ORDER BY o.order_id ASC";
						
						$order_query = $database->query($order_sql);
		
						if ($database->num_rows($order_query) > 0) {
						
							$x++;
							$message[$x] = '<table width="100%">';
							foreach($database->fetch_array($order_query) as $order_result){

								if ($live == true) {
									$user_email[$x] = $result['email_address'];
								} else {
									$user_email[$x] = 'realtysp@yahoo.com';
								}
						
								$user_id[$x] = $result['user_id'];
								$firstname[$x] = $order_result['firstname'];
								$lastname[$x] = $order_result['lastname'];
								$name[$x] = $firstname[$x] . " " . $lastname[$x];
								
								$message[$x] .= "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>";
								$message[$x] .= "<tr><td width='30%'>Order ID: </td><td width='70%'>" . $order_result['order_id'] . "</td></tr>";
								$message[$x] .= "<tr><td width='30%'>Address: </td><td width='70%'>" . $order_result['house_number'] . " " . $order_result['street_name'] . " " . $order_result['city'] . " " . $order_result['zip'] . "</td></tr>";
		
								if ($order_result['date_completed'] != '0') {
									$message[$x] .= "<tr><td width='30%'>Date Installed: </td><td width='70%'>" . date("Y-M-d",$order_result['date_completed']); 
								} else {
									$message[$x] .= "<tr><td width='30%'>Date Added: </td><td width='70%'>" . date("Y-M-d",$order_result['date_added']); 
								}
								
								$message[$x] .= "</td></tr><tr><td width='30%'>Date for Removal: </td><td width='70%'>";
								
								$remove_sql = "SELECT * FROM " . TABLE_ORDERS . " WHERE address_id = ". $order_result['address_id'] . " AND order_type_id = 3";
								$remove_query = $database->query($remove_sql);
								$remove_num = $database->num_rows($remove_query);
								
								if ($remove_num > 0) {
									$remove_result = $database->fetch_array($remove_query);
									if ($remove_result['date_schedualed'] != '0') {
										$message[$x] .= date("Y-M-d",$remove_result['date_schedualed']); 
									} else {
										$message[$x] .= "None Scheduled";
									}
								} else {
									$message[$x] .= "None Scheduled";
								}
			
								$message[$x] .=  "</td></tr>";
		
							} //end while orders

							$message[$x] .= '</table>';
						
						} else { //no emails/orders
							$msg = 'No Emails To Send!';
						} //end if
		
					} //end while
					
			} //end if

		//send emails
		for ($y=1;$y<=$x;$y++) {

			if ($message[$y] != '') {
			
				/*
				$email_template = new email_template('inventory_status');
				$email_template->load_email_template();
				$email_template->set_email_template_variable('INVENTORY', $message[$y]);
				$email_template->set_email_template_variable('NAME', $name[$y]);
	
				$email_template->parse_template();
				$email_template->send_email($user_email[$y], $firstname[$y].','.$lastname[$y]);
				*/
				
				$subject = date('F') . " Active Signpost Summary for " . $name[$y] . " from Realty SignPost";

				$new_message = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
					<tbody>
						<tr>
							<td>Greetings from Realty SignPost. Below is a list of addresses where you currently have a signpost installed. This list includes the date the signpost was installed, and the scheduled removal date, if a removal date has been scheduled for that signpost. 
							We are providing this list to help you manage your active signpost installations. If you no longer need the signpost at one of the addresses listed, please place a removal order for that address. 
							<br /><br />
							If you feel there are mistakes on this list, please contact us at <a href='info@realtysignpost.com'>info@realtysignpost.com</a>, and we will make the appropriate corrections.
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>".$message[$y]."</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>Thank you for your business.<br />
								Realty SignPost LLC<br />
								H. Douglas Myers and Ryan W. Myers, Brothers and Co-Owners<br />
								Complete Information: <a href='http://www.realtysignpost.com'>realtysignpost.com</a><br />
								Fax and Voicemail: 703-995-4567 or 202-478-2131<br />
								Emergency Issue Resolution: 202-256-0107.<br />
								'Wired for Your Future in Real Estate'
							</td>
						</tr>
					</tbody>
				</table>";

				mail($user_email[$y], $subject, 
					"<html><body>".$new_message . "</body></html>", 
					"From: " . EMAIL_FROM_NAME . " <".EMAIL_FROM_ADDRESS.">\n" . 
					"cc: " . EMAIL_FROM_NAME . " <" . $live_email . ">\n" . 
					"MIME-Version: 1.0\n" . 
					"Content-type: text/html; charset=iso-8859-1"); 

				//send emails to extra agent email addresses
				$extra_query = $database->query("select DISTINCT email_address from emails_to_users where user_id = '" . $user_id[$y] . "' and email_status = '1'");
				foreach($database->fetch_array($extra_query) as $extra_result){
					mail($extra_result['email_address'], $subject, 
						"<html><body>".$new_message . "</body></html>", 
						"From: " . EMAIL_FROM_NAME . " <".EMAIL_FROM_ADDRESS.">\n" . 
						"cc: " . EMAIL_FROM_NAME . " <" . $live_email . ">\n" . 
						"MIME-Version: 1.0\n" . 
						"Content-type: text/html; charset=iso-8859-1"); 
				}
				
				$msg = 'Emails Sent!';
			}
									
		}
		
	} //end if pageaction
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
			<?php
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td width="20%" class="pageBoxHeading">User Name</td>
						<td width="20%" class="pageBoxHeading" align="center">User Email</td>
						<td width="20%" class="pageBoxHeading" align="center">User Status</td>
						<td width="20%" class="pageBoxHeading" align="center">User Group</td>
						<td width="20%" class="pageBoxHeading" align="center">Agency</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
					<tr>
						<td colspan="5"><img src="images/pixel_trans.gif" height="1" width="90" /></td>
					</tr>
				<?php
					$uData = array();
					$listing_split = new split_page("select u.user_id, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.agency_id, u.active_status, u.accounts_payable, u.order_hold, u.personal_invoice from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and ".((!empty($show_user_group_id)) ? (($show_user_group_id == '5') ? "(utug.user_group_id = '" . $show_user_group_id . "' or u.accounts_payable = '1') and " : "utug.user_group_id = '" . $show_user_group_id . "' and ") : '').((!empty($search_name)) ? ("((ud.firstname like '" . $search_name . "%' or ud.firstname = '" . $search_name . "' or ud.firstname like '%" . $search_name . "' or ud.firstname like '%" . $search_name . "%') or (ud.lastname = '" . $search_name . "' or ud.lastname like '" . $search_name . "%' or ud.lastname like '%" . $search_name . "' or ud.lastname like '%" . $search_name . "%')) and ") : '').((!empty($search_email)) ? ("(u.email_address like '" . $search_email . "%' or u.email_address = '" . $search_email . "' or u.email_address like '%" . $search_email . "' or u.email_address like '%" . $search_email . "%') and ") : '').((!empty($search_mrsid)) ? (($search_mrsid == 'none') ? "u.agent_id = '' and " : "(u.agent_id like '" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "%' or u.agent_id like '%" . $search_mrsid . "' or u.agent_id = '" . $search_mrsid . "') and ") : '')."utug.user_group_id = ug.user_group_id" . ((!empty($show_agency_id)) ? " and (a.agency_id = '" . $show_agency_id . "' or a.parent_agency_id = '" . $show_agency_id . "')" : '') . ((!empty($start_letter)) ? (($start_letter_type == 'any') ? " and (ud.firstname like '".$start_letter."%' or ud.lastname like '".$start_letter."%')" : (($start_letter_type == 'first') ? " and (ud.firstname like '".$start_letter."%')" : " and (ud.lastname like '".$start_letter."%')")) : '') . ((!empty($show_service_level_id)) ? " and (utug.user_group_id != '1' or u.service_level_id = '" . $show_service_level_id . "')" : '') . ((!empty($search_status)) ? (($search_status == '1') ? " and (u.active_status = '1') " : " and (u.active_status = '0') ") : '') . " order by ud.lastname, ud.firstname", '20', 'u.user_id');
						if ($listing_split->number_of_rows > 0) {
							$query = $database->query($listing_split->sql_query);
							    foreach($database->fetch_array($query) as $result){
										if ($result['user_id'] == $uID) {
											$uData = $result;
										}
						?>
							<tr>
								<td width="20%" class="pageBoxContent"><?php echo $result['firstname'].' '.$result['lastname']; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $result['email_address']; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php if ($result['active_status'] == '1') { ?><img src="images/icon_status_green.gif" height="10" width="10" border="0" />&nbsp;&nbsp;<img src="images/icon_status_red_light.gif" height="10" width="10" border="0" /><?php } else { ?><img src="images/icon_status_green_light.gif" height="10" width="10" border="0" />&nbsp;&nbsp;<img src="images/icon_status_red.gif" height="10" width="10" border="0" /><?php } ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $result['name']; ?></td>
								<?php
									$string = '';
										if (($result['user_group_id'] == '1') || ($result['user_group_id'] == '4') || ($result['user_group_id'] == '5')) {
											$agency_query = $database->query("select agency_id, name from " . TABLE_AGENCYS . " where agency_id = '" . $result['agency_id'] . "' limit 1");
											$agency_result = $database->fetch_array($agency_query);
											
												if (!empty($agency_result['agency_id'])) {
													$string = $agency_result['name'];
												}
										}
								?>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $string; ?></td>
								<td width="10" class="pageBoxContent"></td>
							</tr>
						<?php
								}
							?>
							<tr>
								<td colspan="3">
									<table class="normaltable" cellspacing="0" cellpadding="2">
										<tr>
											<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> users)'); ?></td>
											<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
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
			<table width="250" cellspacing="0" celpadding="0" class="pageBox">
				<?php
					if(!empty($msg)) {
				?>
				<tr>
					<td class="mainSuccess"><?php echo $msg; ?></td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td class="pageBoxHeading"><b>User Options</b></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td class="pageBoxContent">Select options to Search or press Send Emails.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<form action="<?php echo FILENAME_ADMIN_INVENTORY_EMAILS; ?>" method="get">
				<tr>
					<td class="main">Show users of Group: <?php echo tep_draw_group_pulldown('show_user_group_id', $show_user_group_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'All'))); ?></td>
				</tr>
					<?php
						//if ($show_user_group_id == '1') {
						?>
						<tr>
							<td class="main">Show only Agency: <?php echo tep_draw_agency_pulldown('show_agency_id', $show_agency_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any'))); ?></td>
						</tr>
						<tr>
							<td class="main">Show only Level: <?php echo tep_draw_service_level_pulldown('show_service_level_id', $show_service_level_id, 'onchange="this.form.submit();"', false, array(array('id' => '', 'name' => 'Any')), false); ?></td>
						</tr>
						<?php
						
						
						//}
					?>
				<tr>
					<td class="main">Show users with name like: <input type="text" name="search_name" value="<?php echo $search_name; ?>" /></td>
				</tr>
				<tr>
					<td class="main">Show users with email like: <input type="text" name="search_email" value="<?php echo $search_email; ?>" /></td>
				</tr>
				<tr>
					<td class="main">MRIS ID like: <input type="text" name="search_mrsid" value="<?php echo $search_mrsid; ?>" /></td>
				</tr>
				<tr>
                    <td class="main">Only Status: <select name="search_status"><option value=""<?php echo (($search_status == '') ? ' SELECTED' : ''); ?>>Any</option><option value="1"<?php echo (($search_status == '1') ? ' SELECTED' : ''); ?>>Active</option><option value="2"<?php echo (($search_status == '2') ? ' SELECTED' : ''); ?>>Inactive</option></select></td>
				</tr>
				<tr>
					<td class="main">Show Users starting with <select name="start_letter"><?php
						$query = $database->query("select LEFT(ud.firstname, 1) as letter from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud , " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id " . ((!empty($show_user_group_id)) ? " and utug.user_group_id = '" . $show_user_group_id . "'" : '') . ((!empty($show_agency_id)) ? " and u.agency_id = '" . $show_agency_id . "'" : '') . " group by letter order by letter");
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
				<tr>
					<td class="main">First Letter of <select name="start_letter_type">
						<option value="any"<?php echo (($start_letter_type == 'any') ? ' SELECTED' : ''); ?>>Any</option>
						<option value="first"<?php echo (($start_letter_type == 'first') ? ' SELECTED' : ''); ?>>First Name</option>
						<option value="last"<?php echo (($start_letter_type == 'last') ? ' SELECTED' : ''); ?>>Last Name</option>
					</select></td>
				</tr>
				<tr>
					<td width="100%" align="right"><input type="submit" value="Search" /></td>
				</tr>
				</form>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td width="100%" align="right"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_INVENTORY_EMAILS . '?page_action=sendemails&'. tep_get_all_get_params(array('page_action', 'action', 'uID')); ?>"><input type="submit" value="Send Emails"></form></td>
				</tr>
			</table>
		</td>
	</tr>
</table>