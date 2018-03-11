<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$view_type = tep_fill_variable('view_type', 'get');
	$day_view = tep_fill_variable('day_view', 'get', 'today');
	$display_view = tep_fill_variable('display_view', 'get', 'overview');
	$submit_value = tep_fill_variable('submit_value');
		if (!isset($_GET['test']) || ($_GET['test'] != 'true')) {
				$this->change_template_file('print.tpl');
				
				
				
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="main">&PAGE_TEXT</td>
				</tr>
				<tr>
					<td><img src="images/pixel_trans.gif" height="10" width="1" /></td>
				</tr>
				
				<tr>
					<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
				</tr>
				<tr>
					<td width="100%" valign="top">
					<?php
							$where = '';
							//Here we work out if it is today or tomorrow and change the where to match.
								if ($day_view == 'tomorrow') {
									$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
				
									//Check if tomorrow was a sunday, if so then extend that date.
										if (date("w", ($midnight_tonight+1)) == 0) {
											$midnight_tonight += (60*60*24);
										}
									//Now get the next day and work out if it is a sunday, if so then extend the date.
									//$midnight_tonight += (60*60*24);
										//if (date("w", ($midnight_tonight+1)) == 0) {
											//$midnight_tonight += (60*60*24);
										//}
									
									$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
									
									$midnight_tonight = 0;
								} elseif ($day_view == 'tomorrow1') {
									$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
				
									//Check if tomorrow was a sunday, if so then extend that date.
										if (date("w", ($midnight_tonight+1)) == 0) {
											$midnight_tonight += (60*60*24);
										}
									//Now get the next day and work out if it is a sunday, if so then extend the date.
									$midnight_tonight += ((60*60*24));
										if (date("w", ($midnight_tonight+1)) == 0) {
											$midnight_tonight += (60*60*24);
										}
									
									$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
									
									$midnight_tonight = 0;
								} else {
									$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp())); 
									$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
								
										//if (date("w", ($midnight_tonight+1)) == 0) {
										//	$midnight_tonight += (60*60*24);
										//	$midnight_future += (60*60*24);
										//}
									$midnight_tonight = 0;
								}
							//We only want the orders for the specifed day.
					?>			
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td class="main"><b>Jobs for <?php echo date("l dS \of F Y", ($midnight_future-1)); ?></b></td>
								</tr>
								<tr>
									<td class="main">
										<table width="100%" cellspacing="2" cellpadding="2">
											<tr>
												<td class="main">Installations: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '1', '2', '', false); ?></td>
												<td class="main">Service Calls: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '2', '2', '', false); ?></td>
												<td class="main">Removals: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '3', '2', '', false); ?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
								</tr>
								
							</table>
							
							<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
			<?php
				/*					
								<tr>
									<td class="pageBoxHeading">Date to Preform<br />Date Called</td>
									<td class="pageBoxHeading">Service Required<br />Order #</td>
									<td class="pageBoxHeading" align="left" width="120">Number of Posts<br />Extra Equipment (item id if available)</td>
									<td class="pageBoxHeading">Agent/Agency<br />Personal Preferences</td>
									<td class="pageBoxHeading">Where/Notes</td>
									<td class="pageBoxHeading">Service Level</td>
									<td class="pageBoxHeading" align="right">Map Info(ADC)</td>
									<td width="10" class="pageBoxHeading"></td>
								</tr>
							<?php
								$query = $database->query("select o.order_id, o.date_schedualed, o.date_added, o.user_id, os.order_status_name, o.order_type_id, ot.name as order_type_name, a.zip4, otiso.show_order_id, a.house_number, a.street_name, a.cross_street_directions, a.number_of_posts, a.city, a.address_post_allowed, a.zip, a.adc_number, s.name as state_name, c.name as county_name, sld.name as service_level_name, od.special_instructions, od.admin_comments, otiso.show_order_id as order_column from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica left join " . TABLE_INSTALLATION_AREAS . " ia on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) AND ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where " . ((!empty($midnight_tonight)) ? "o.date_schedualed >= '" . $midnight_tonight . "' and " : '') . "o.date_schedualed < '" . $midnight_future . "' " . ((empty($midnight_tonight)) ? " and o.order_status_id < '3' " : '') . " and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by order_column");
								$loop = 0;
									while($result = $database->fetch_array($query)) {
											if ($loop > 0) {
											?>
											<tr>
												<td height="4"><img src="images/pixel_trans.gif" height="4" width="1" /></td>
											</tr>
											<tr>
												<td height="1" colspan="8"><img src="images/pixel_grey.gif" height="1" width="100%" /></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<?php
											}
										$agent_data = tep_fetch_agent_data($result['user_id']);
										$order = new orders('fetch', $result['order_id']);
										$order_data = $order->return_result();
											$adc = str_replace('_', ' ', $result['adc_number']);
										
										$order_description = $result['house_number'].' ' .$result['street_name'].'<br>'.$result['city'].' '.$result['state_name'].' '.$result['zip'].'<br />'.$result['cross_street_directions'].'<br />'.$result['special_instructions'].'<br />'.$result['admin_comments'] . (($result['address_post_allowed'] == '0') ? '<br><b>Posts may not be allowed at this address.</b>' : '');
											if ($order_data['order_type_id'] == '2') {
												$sub_query = $database->query("select service_call_reason_id, service_call_detail_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $result['order_id'] . "' limit 1");
												$sub_result = $database->fetch_array($sub_query);
												$string = '<br><br>Service_call Reasion:';
													if ($sub_result['service_call_reason_id'] == '1') {
															$string.= '<br>Exchange Rider';
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '2') {
															$string.= '<br>Install New Rider or BBox';
															
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '3') {
															$string.= '<br>Replace/Exchange Agent SignPanel';
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '4') {
															$string.= '<br>Post Leaning/Straighten Post';
																if ($sub_result['service_call_detail_id'] == '1') {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weather';
																} elseif ($sub_result['service_call_detail_id'] == '2') {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Improper Installation';
																} elseif ($sub_result['service_call_detail_id'] == '3') {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Someone moved Post';
																} elseif ($sub_result['service_call_detail_id'] == '4') {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other';
																}
														} elseif ($sub_result['service_call_reason_id'] == '5') {
															$string.= '<br>Move Post';
															//Check if any posts were marked as lost and jot them down.
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '6') {
															$string.= '<br>Install equipment forgotten at install';
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '7') {
															$string.= '<br>Other';
														}
												$order_description .= $string;
											}
							?>
								<tr>
									<td class="pageBoxContent" valign="top"><?php echo date("n/d/Y", $result['date_schedualed']); ?><br /><?php echo date("n/d/Y", $result['date_added']); ?></td>
									<td class="pageBoxContent" valign="top"><?php echo $result['order_type_name']; ?><br />Order #: <?php echo $result['order_id']; ?></td>
									<td class="pageBoxContent" valign="top" align="left">Posts: <?php echo $result['number_of_posts']; ?><br /><br /><?php echo tep_create_view_equipment_string($order_data['optional'], true); ?></td>
									<td class="pageBoxContent" valign="top"><?php echo $agent_data['firstname'] .' ' . $agent_data['lastname'] . ' - ' . $agent_data['agent_id']; ?><br /><br /><?php if (is_numeric($agent_data['agency_id'])) { echo $agent_data['name'] .'<br>Agency Ph. ' . $agent_data['contact_phone'].'<br><br>'; } ?>Contact Numbers: <?php $n = 0; $count = count($agent_data['phone_numbers']); while($n < $count) { echo $agent_data['phone_numbers'][$n].'<br>'; $n++; } ?><br /><br /><?php echo ((tep_agent_has_preferences($result['user_id'], $result['order_type_id'])) ? tep_create_agent_preferences_string($result['user_id'], $result['order_type_id']) : 'No Preferences'); ?></td>
									<td class="pageBoxContent" valign="top"><?php echo $order_description; ?></td>
									<td class="pageBoxContent" valign="top"><?php echo $result['service_level_name']; ?></td>
									<td class="pageBoxContent" align="right" valign="top"><?php echo ((!empty($adc)) ? $adc . '<br>' : '') . $agent_data['agent_id']; ?></td>
									<td width="10" class="pageBoxContent"></td>
								</tr>
						<?php
										$loop++;
									}
						?>
						<?php
							*/
							?>
							
								<tr>
									<td class="pageBoxHeading" valign="top" width="15%">Service Type<br />Order Dates<br />Order #<br />Order Extra Items/Panels<br /># of Posts</td>
									<td class="pageBoxHeading" valign="top" width="35%">Agent/Agency, Svc Level, Preferences</td>
									<td class="pageBoxHeading" valign="top" width="35%">Address, ADC map coord, Directions, Agent Comments</td>
									<td class="pageBoxHeading" valign="top" width="15%">Contact #s</td>
									
								</tr>
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
								</tr>
								<tr>
									<td colspan="4"><hr /></td>
								</tr>
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
								</tr>
							<?php
								$query = $database->query("select o.order_id, o.date_schedualed, o.date_added, o.user_id, os.order_status_name, o.order_type_id, ot.name as order_type_name, a.zip4, otiso.show_order_id, a.house_number, a.street_name, a.cross_street_directions, a.number_of_posts, a.city, a.address_post_allowed, a.zip, a.post_type_id, a.adc_number, s.name as state_name, c.name as county_name, sld.name as service_level_name, od.special_instructions, od.admin_comments, otiso.show_order_id as order_column from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where " . ((!empty($midnight_tonight)) ? "o.date_schedualed >= '" . $midnight_tonight . "' and " : '') . "o.date_schedualed < '" . $midnight_future . "' " . ((empty($midnight_tonight)) ? " and o.order_status_id < '3' " : '') . " and o.order_status_id = '2' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by order_column");
								$loop = 0;
									while($result = $database->fetch_array($query)) {
											if ($loop > 0) {
											?>
											<tr>
												<td height="4"><img src="images/pixel_trans.gif" height="4" width="1" /></td>
											</tr>
											<tr>
												<td height="1" colspan="8"><img src="images/pixel_grey.gif" height="1" width="100%" /></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<?php
											}
										$agent_data = tep_fetch_agent_data($result['user_id']);
										$order = new orders('fetch', $result['order_id']);
										$order_data = $order->return_result();
											$adc = str_replace('_', ' ', $result['adc_number']);
										
										$order_description = $result['house_number'].' ' .$result['street_name'].'<br>'.$result['city'].' '.$result['state_name'].' '.$result['zip4'].'<br />'.((!empty($adc)) ? $adc . '<br>' : '') . $result['cross_street_directions'].'<br />'.$result['special_instructions'].'<br />'.$result['admin_comments'] . (($result['address_post_allowed'] == '0') ? '<br><b>Posts may not be allowed at this address.</b>' : '');
											if ($order_data['order_type_id'] == '2') {
												$sub_query = $database->query("select service_call_reason_id, service_call_detail_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $result['order_id'] . "' limit 1");
												$sub_result = $database->fetch_array($sub_query);
												$string = '<br><br>Service Call Reason:';
													if ($sub_result['service_call_reason_id'] == '1') {
															$string.= '<br>Exchange Rider';
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($equip_result = $database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '2') {
															$string.= '<br>Install New Rider or BBox';
															
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($equip_result = $database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '3') {
															$string.= '<br>Replace/Exchange Agent SignPanel';
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($equip_result = $database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '4') {
															$string.= '<br>Post Leaning/Straighten Post';
																if ($sub_result['service_call_detail_id'] == '1') {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weather';
																} elseif ($sub_result['service_call_detail_id'] == '2') {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Improper Installation';
																} elseif ($sub_result['service_call_detail_id'] == '3') {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Someone moved Post';
																} elseif ($sub_result['service_call_detail_id'] == '4') {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other';
																}
														} elseif ($sub_result['service_call_reason_id'] == '5') {
															$string.= '<br>Move Post';
															//Check if any posts were marked as lost and jot them down.
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($equip_result = $database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '6') {
															$string.= '<br>Install equipment forgotten at install';
															$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
																while($equip_result = $database->fetch_array($equip_query)) {
																	$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
																}
														} elseif ($sub_result['service_call_reason_id'] == '7') {
															$string.= '<br>Other';
														}
												$order_description .= $string;
                                            }

                            if ($order_data['lamp_yes_no'] == "yes" && $order_data['lamp_use_gas'] == "yes") {
                                $miss_utility_string = "Property has a gas lamppost. Miss Utility has marked the property.";
                            } else if ($order_data['lamp_yes_no'] == "yes" && $order_data['lamp_use_gas'] == "no") {
                                $miss_utility_string = "Agent indicates that the property has a yard lamp, but that it is not ";
                                $miss_utility_string.= "a gas lamp. But still be cautious installing the signpost near the yard lamp.";
                            } else if ($order_data['lamp_yes_no'] == "yes" && $order_data['lamp_use_gas'] == "unsure") {
                                $miss_utility_string = "Property may have a gas lamppost. Miss Utility has marked the property.";
                            } else if ($order_data['miss_utility_yes_no'] == "yes") {
                                $miss_utility_string = "Miss Utility has marked the property.";
                            } else {
                                $miss_utility_string = "";
                            } 

                            if ($miss_utility_string) {
                                $order_description .= "<br />";
                                $order_description .= $miss_utility_string;
                            }
							?>
								<tr>
								
									<td class="pageBoxContent" valign="top" width="15%"><strong><?php echo $result['order_type_name']; ?></strong><br />Complete : <?php echo date("n/d/Y", $result['date_schedualed']); ?><br />Date Ordered : <?php echo date("n/d/Y", $result['date_added']); ?><br />Order #: <?php echo $result['order_id']; ?><br /><?php echo tep_create_view_equipment_string($order_data['optional'], true); ?><br /># of Posts : <?php echo $result['number_of_posts']; ?><?php if ($result['order_type_id'] > 1) { ?><br /><?php echo tep_fetch_equipment_name($result['post_type_id']); ?><?php } ?></td>
									<td class="pageBoxContent" valign="top" width="35%"><?php echo $agent_data['firstname'] .' ' . $agent_data['lastname']; ?> / <?php echo $agent_data['name']; ?><br /><?php echo $result['service_level_name']; ?><br /><?php echo ((tep_agent_has_preferences($result['user_id'], $result['order_type_id'])) ? tep_create_agent_preferences_string($result['user_id'], $result['order_type_id']) : ''); ?></td>
									<td class="pageBoxContent" valign="top" width="35%"><?php echo $order_description; ?></td>
									<td class="pageBoxContent" valign="top" width="15%"><?php $n = 0; $count = count($agent_data['phone_numbers']); while($n < $count) { echo $agent_data['phone_numbers'][$n] . (($n == 0) ? ' - Cell' : (($n == 2) ? ' - Fax' : '')).'<br>'; $n++; } ?></td>
									
									<?php
									/*
									<td class="pageBoxContent" valign="top"><?php echo date("n/d/Y", $result['date_schedualed']); ?><br /><?php echo date("n/d/Y", $result['date_added']); ?></td>
									<td class="pageBoxContent" valign="top"><?php echo $result['order_type_name']; ?><br />Order #: <?php echo $result['order_id']; ?></td>
									<td class="pageBoxContent" valign="top" align="left">Posts: <?php echo $result['number_of_posts']; ?><br /><br /><?php echo tep_create_view_equipment_string($order_data['optional'], true); ?></td>
									<td class="pageBoxContent" valign="top"><?php echo $agent_data['firstname'] .' ' . $agent_data['lastname'] . ' - ' . $agent_data['agent_id']; ?><br /><br /><?php if (is_numeric($agent_data['agency_id'])) { echo $agent_data['name'] .'<br>Agency Ph. ' . $agent_data['contact_phone'].'<br><br>'; } ?>Contact Numbers: <?php $n = 0; $count = count($agent_data['phone_numbers']); while($n < $count) { echo $agent_data['phone_numbers'][$n].'<br>'; $n++; } ?><br /><br /><?php echo ((tep_agent_has_preferences($result['user_id'], $result['order_type_id'])) ? tep_create_agent_preferences_string($result['user_id'], $result['order_type_id']) : 'No Preferences'); ?></td>
									<td class="pageBoxContent" valign="top"><?php echo $order_description; ?></td>
									<td class="pageBoxContent" valign="top"><?php echo $result['service_level_name']; ?></td>
									<td class="pageBoxContent" align="right" valign="top"><?php echo ((!empty($adc)) ? $adc . '<br>' : '') . $agent_data['agent_id']; ?></td>
									*/
									?>
									
								</tr>
						<?php
										$loop++;
									}
						?>
			
						</table>
					</td>
				</tr>
			</table>
			<?php
		} else {
				//Running off the new pdf template.
				error_reporting(0);
				include(DIR_CLASSES . 'class.ezpdf.php');
				include(DIR_TEMPLATE . 'JobList.php');
				
				$pdf_code = $pdf->ezOutput();
			
				$filename='Invoice.pdf';
	
				$filesize = strlen($pdf_code);
				
				header("Pragma: public");
				header("Expires: 0"); // set expiration time
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			
				header("Content-Type: application/pdf");
				header("Content-Length: ".$filesize);
				header("Content-Disposition: inline; filename=$filename");
				header("Content-Transfer-Encoding: binary");
			
				echo $pdf_code;
				exit();
		}
?>
