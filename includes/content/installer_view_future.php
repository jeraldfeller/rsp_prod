<?php
	$order_type = tep_fill_variable('order_type', 'get', '');
	$oID = tep_fill_variable('oID', 'get', '');
	$page_action = tep_fill_variable('page_action', 'get', '');

	$show_house_number = tep_fill_variable('show_house_number', 'get', '');
	$show_street_name = tep_fill_variable('show_street_name', 'get', '');
	$show_between_type = tep_fill_variable('show_between_type', 'get', 'added');
	$show_between_start = tep_fill_variable('show_between_start', 'get', date("n/d/Y", mktime()));
	$show_between_end = tep_fill_variable('show_between_end', 'get', '');
?>

<?php if ($error->get_error_status('installer_view_future')) {?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr><td class="mainError" colspan="2"><?php echo $error->get_error_string('installer_view_future'); ?></td></tr>
</table>
<?php }?>

		<?php
			if (empty($page_action)) {
				$where = '';
						if (!empty($order_type)) {
							$where .= " and o.order_type_id = '" . $order_type . "' ";
						}
						if (!empty($show_house_number)) {
							$where .= ' and ';
							$where .= " (a.house_number = '" . $show_house_number . "' or a.house_number like '" . $show_house_number . "%'  or a.house_number like '%" . $show_house_number . "')";
						}
						if (!empty($show_street_name)) {
							$where .= ' and ';
							$where .= " (a.street_name = '" . $show_street_name . "' or a.street_name like '" . $show_street_name . "%'  or a.street_name like '%" . $show_street_name . "')";
						}
						if (!empty($show_between_type)) {
							if (!empty($show_between_start)) {
								$start_timestamp = @strtotime($show_between_start);
							} else {
								$start_timestamp = 0;
							}
							if (!empty($show_between_end)) {
								$end_timestamp = @strtotime($show_between_end);
									if ($end_timestamp > 0) {
										$end_timestamp += ((60*60*24) - 1); //End as opposed to start of day.
									}
							} else {
								$end_timestamp = 0;
							}
							if ($show_between_type == 'accepted') {
								$where .= " and o.date_accepted > 0 ";
									if ($start_timestamp > 0) {
										$where .= " and o.date_accepted >= '" . $start_timestamp . "' ";
									}
									if ($end_timestamp > 0) {
										$where .= " and o.date_accepted <= '" . $end_timestamp . "' ";
									}
							}
							if ($show_between_type == 'ordered') {
								$where .= " and o.date_added > 0 ";
									if ($start_timestamp > 0) {
										$where .= " and o.date_added >= '" . $start_timestamp . "' ";
									}
									if ($end_timestamp > 0) {
										$where .= " and o.date_added <= '" . $end_timestamp . "' ";
									}
							}
							if ($show_between_type == 'scheduled') {
								$where .= " and o.date_schedualed > 0 ";
									if ($start_timestamp > 0) {
										$where .= " and o.date_schedualed >= '" . $start_timestamp . "' ";
									}
									if ($end_timestamp > 0) {
										$where .= " and o.date_schedualed <= '" . $end_timestamp . "' ";
									}
							}
							if ($show_between_type == 'completed') {
								$where .= " and o.date_completed > 0 ";
									if ($start_timestamp > 0) {
										$where .= " and o.date_completed >= '" . $start_timestamp . "' ";
									}
									if ($end_timestamp > 0) {
										$where .= " and o.date_completed <= '" . $end_timestamp . "' ";
									}
							}
						}
				
				//We dont want jobs schedualed for either today or tomorrow.
				$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp())); 
				$midnight_future = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+3), date("Y", tep_fetch_current_timestamp())); 
				
				$get_string = tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y', 'oID'));
				
				/*$listing_split = new split_page("select o.order_id, o.date_schedualed, ot.name as order_type_name, a.house_number, a.street_name, a.city, a.zip4  from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDER_TYPES . " ot where o.order_type_id = ot.order_type_id and o.date_schedualed >= '" . $midnight_future . "' " . $where . " and o.address_id = a.address_id and  ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by o.date_schedualed ASC", '20', 'o.order_id');*/

$listing_split = new split_page("select o.order_id, o.date_schedualed, ot.name as order_type_name, a.house_number, a.street_name, a.city, a.zip4  from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDER_TYPES . " ot where o.order_type_id = ot.order_type_id and o.order_status_id != 4 and o.date_schedualed >= '" . $midnight_future . "' " . $where . " and o.address_id = a.address_id and  ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by o.date_schedualed ASC", '500', 'o.order_id');

		if ($listing_split->number_of_rows > 0) {
		}else{}

		if ($listing_split->number_of_rows > 0) {
			$query = $database->query($listing_split->sql_query);
	            foreach($database->fetch_array($query) as $result){
					$vars['table'][] = $result;
					
			}

		}  else {
			$vars['table'] = array();
		}
	
		
		
		echo $twig->render('installer/future_jobs_list.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
		
		?>            
					
					
		<?php	} else {
				//Details.
				$order = new orders('fetch', $oID);
					$order_data = $order->return_result();
					
					$user_query = $database->query("select u.agent_id, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $order_data['user_id'] . "' and u.user_id = ud.user_id limit 1");
					$user_result = $database->fetch_array($user_query);
					?>
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Order Id: <?php echo $oID; ?></b></td>
						</tr>
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Order Status: <?php echo tep_get_order_status_name($order_data['order_status_id']); ?></b></td>
						</tr>
						
						<tr>
							<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
						</tr>
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Agent Information</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Agent Name: </td><td class="pageBoxContent"><?php echo $user_result['firstname']; ?> <?php echo $user_result['lastname']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Agency Name: </td><td class="pageBoxContent"><?php echo ((!empty($user_result['name'])) ? $user_result['name'] : 'None'); ?></td>
						</tr>
						<tr>
							<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
						</tr>
						
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Address Information</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Address: </td><td class="pageBoxContent"><?php echo $order_data['house_number']; ?>, <?php echo $order_data['street_name']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">City: </td><td class="pageBoxContent"><?php echo $order_data['city']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">County: </td><td class="pageBoxContent"><?php echo $order_data['county_name']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">State: </td><td class="pageBoxContent"><?php echo $order_data['state_name']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Zip: </td><td class="pageBoxContent"><?php echo ((!empty($order_data['zip'])) ? $order_data['zip'] : ''); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Zip4: </td><td class="pageBoxContent"><?php echo $order_data['zip4']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Cross Street/Directions: </td>
							<td class="pageBoxContent"><?php echo $order_data['cross_street_directions']; ?></td>
						</tr>
						<tr>
							<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
						</tr>
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Job Description</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Order Type: </td><td class="pageBoxContent"><?php echo $order_data['order_type_name']; ?></td>
						</tr>
						<?php
							$installer_id = tep_fetch_assigned_order_installer($oID);
								if ($installer_id !== false) {
						?>
						<tr>
							<td class="pageBoxContent">Assigned Installer: </td><td class="pageBoxContent"><?php echo tep_fetch_installer_name($installer_id); ?></td>
						</tr>
						<?php
								}
						?>
						<tr>
							<td class="pageBoxContent">Date Added: </td><td class="pageBoxContent"><?php echo date("n/d/Y", $order_data['date_added']); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Last Modified: </td><td class="pageBoxContent"><?php echo (($order_data['last_modified'] > 0) ? date("n/d/Y", $order_data['last_modified']) : 'Never'); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Date Scheduled: </td><td class="pageBoxContent"><?php echo (($order_data['date_schedualed'] > 0) ? date("n/d/Y", $order_data['date_schedualed']) : 'Never'); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Date Completed: </td><td class="pageBoxContent"><?php echo (($order_data['date_completed'] > 0) ? date("n/d/Y", $order_data['date_completed']) : 'Never'); ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Number of Posts: </td><td class="pageBoxContent"><?php echo $order_data['number_of_posts']; ?></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Special Instructions: </td>
							<td class="pageBoxContent"><?php echo $order_data['special_instructions']; ?></td>
						</tr>
						<tr>
							<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
						</tr>

						<?php
						if ($order_data['order_type_id'] == '1') {
					?>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Equipment</b></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%" colspan="2"><?php echo tep_create_view_equipment_string($order_data['optional']); ?></td>
					</tr>
					<?php
						} elseif ($order_data['order_type_id'] == '2') {
					?>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Reason and Details</b></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%" colspan="2">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td class="main"><b>Reason: </b></td>
								</tr>
								<tr>
									<td class="main"><?php
									
										if ($order_data['service_call_reason_id'] == '1') {
											echo 'Exchange Rider';
												for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
													echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($order_data['equipment'][$n]['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$order_data['equipment'][$n]['name'];
												}
										} elseif ($order_data['service_call_reason_id'] == '2') {
											echo 'Install New Rider or BBox';
											
												for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
													echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
												}
										} elseif ($order_data['service_call_reason_id'] == '3') {
											echo 'Replace/Exchange Agent SignPanel';
												for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
													echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
												}
										} elseif ($order_data['service_call_reason_id'] == '4') {
											echo 'Post Leaning/Straighten Post';
												if ($order_data['service_call_detail_id'] == '1') {
													echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weather';
												} elseif ($order_data['service_call_detail_id'] == '2') {
													echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Improper Installation';
												} elseif ($order_data['service_call_detail_id'] == '3') {
													echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Someone moved Post';
												} elseif ($order_data['service_call_detail_id'] == '4') {
													echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other';
												}
										} elseif ($order_data['service_call_reason_id'] == '5') {
											echo 'Move Post';
												//Check if any posts were marked as lost and jot themdown.
												for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
													echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'] . ' was missing and was replaced.';
												}
										} elseif ($order_data['service_call_reason_id'] == '6') {
											echo 'Install equipment forgotten at install';
												for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
													echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
												}
										} elseif ($order_data['service_call_reason_id'] == '7') {
											echo 'Other';
										}
									?></td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
						}
					?>
						<tr>
							<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
						</tr>
						<tr>
							<td colspan="2" class="pageBoxContent"><b>Agent Preferences</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td width="100%" colspan="2"><?php echo ((tep_agent_has_preferences($order_data['user_id'], $order_data['order_type_id'])) ? tep_create_agent_preferences_string($order_data['user_id'], $order_data['order_type_id']) : 'Agent has no personal preferences.'); ?></td>
						</tr>
						<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Comments</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" valign="top">Admin Comments: </td>
						<td class="pageBoxContent"><?php echo $order_data['admin_comments']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" valign="top">Installer Comments: </td>
						<td class="pageBoxContent"><?php echo $order_data['address_comments']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" valign="top">Installer Comments for Agent: </td>
						<td class="pageBoxContent"><?php echo $order_data['installer_comments']; ?></td>
					</tr>
					
					</table>
					<?php
			}
			?>
			
