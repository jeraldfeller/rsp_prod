<?php
	//This is now tomorrow.
	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get', tep_fill_variable('oID', 'session'));
	$back_button = tep_fill_variable('back_button_y');
	$submit_button = tep_fill_variable('submit_button_y');
	$installer_id = tep_fill_variable('user_id', 'session');
	
		if ($page_action == 'edit') {
			$step = '1';
			
			$query = $database->query("select order_type_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
			$result = $database->fetch_array($query);
			$order_type_id = $result['order_type_id'];
			$order_status = '1';
			
			//Might just clear it all.
			
			$session->php_session_unregister('order_status');
			$session->php_session_unregister('step');
			$session->php_session_unregister('equipment');
			$session->php_session_unregister('post_type_id');
			$session->php_session_unregister('optional');
			$session->php_session_unregister('other_items');
			$session->php_session_unregister('agent_comments');
			$session->php_session_unregister('install_fail_reason');
			$session->php_session_unregister('install_fail_3_yes_no');
			$session->php_session_unregister('install_fail_3_house_number');
			$session->php_session_unregister('install_fail_3_street_name');
			$session->php_session_unregister('install_fail_4_new_date');
			$session->php_session_unregister('remove_equipment');
			$session->php_session_unregister('sc_success_3');
			$session->php_session_unregister('sc_fail_5');
			$session->php_session_unregister('metal_stakes');
			$session->php_session_unregister('removal_fail_new_date');
			$session->php_session_unregister('address_comments');
			$session->php_session_unregister('installer_mark_complete');
			$session->php_session_unregister('change_address');


			$session->php_session_register('order_type_id', $order_type_id);
			$session->php_session_register('order_status', $order_status);
			$session->php_session_register('step', $step);
			$session->php_session_register('oID', $oID);
			
			
			
				if ($order_type_id == '2') {
					//Check if a reason has been set and if not then set this one as other as its the only one compadible with the auto merge.
					//4
					$query = $database->query("select service_call_option_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $oID . "' limit 1");
					$result = $database->fetch_array($query);
					
						if (empty($result['service_call_option_id'])) {
							$database->query("insert into " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " (order_id, service_call_reason_id) values ('" . $oID . "', '7')");
						}
				}
		} elseif(!empty($back_button)) {
			$step = tep_fill_variable('step', 'session');
			
			$step-=1;

				if ($step < 1) {
					$session->php_session_unregister('oID');
					$session->php_session_unregister('step');
					$session->php_session_unregister('order_type_id');
					$session->php_session_unregister('order_status');
					$session->php_session_unregister('equipment');
					$session->php_session_unregister('post_type_id');
					$session->php_session_unregister('other_items');
					$session->php_session_unregister('address_comments');
					$session->php_session_unregister('agent_comments');
					$session->php_session_unregister('install_fail_reason');
					$session->php_session_unregister('install_fail_3_yes_no');
					$session->php_session_unregister('install_fail_3_house_number');
					$session->php_session_unregister('install_fail_3_street_name');
					$session->php_session_unregister('install_fail_4_yes_no');
					$session->php_session_unregister('install_fail_4_new_date');
					tep_redirect(FILENAME_INSTALLER_VIEW_TODAY);
				} else {
					
					$session->php_session_register('step', $step);
					tep_redirect(FILENAME_INSTALLER_MARK_COMPLETE);
				}
			
		} else {
			$step = tep_fill_variable('step', 'session');
			$order_type_id = tep_fill_variable('order_type_id', 'session');
			//var_dump($_SESSION);
			//echo '<br><br>';
			$order_status = tep_fill_variable('order_status', 'session');
			//echo $step . ' - ' . $order_status . '<br>';
		}
		if (($page_action == 'submit') && empty($submit_button)) {
			$page_action = '';
		}
	
	$order_data_query = $database->query("select a.deposit_cost, a.installer_comments, o.address_id, o.order_id, o.user_id, o.service_level_id, o.date_schedualed, o.billing_method_id, a.house_number, a.street_name, a.city, a.zip, a.zip4, a.number_of_posts, a.cross_street_directions, ot.name as order_type_name, s.name as state_name, c.name as county_name, ud.firstname, ud.lastname, ag.name as agency_name, od.special_instructions, od.admin_comments, od.installer_comments as installertoagent, sl.name as service_level_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_ADDRESSES . " a left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " ag on (u.agency_id = ag.agency_id), " . TABLE_SERVICE_LEVELS . " sl, " . TABLE_ORDER_TYPES . " ot where o.order_id = '" . $oID . "' and o.order_id = od.order_id and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.user_id = ud.user_id and ud.user_id = u.user_id and u.service_level_id = sl.service_level_id limit 1");
    $order_data_result = $database->fetch_array($order_data_query);
    $house_number = $order_data_result['house_number'];
    $street_name = $order_data_result['street_name'];
    $city = $order_data_result['city'];
    $address_id = $order_data_result['address_id'];
    $service_level_id = $order_data_result['service_level_id'];

    if (empty($house_number)) {
        $house_number = "No house number";
    }

    $address_name = "{$house_number} {$street_name}, {$city}";

	$error_string = '';
			
		switch($step) {
			case '1': 
				//Success or not.
				if ($page_action == 'submit') {
					//Check the data and move if ok.
					$error_status = false;
					$order_status = tep_fill_variable('order_status');

					$session->php_session_register('order_status', $order_status);
					
						if (!$error_status) {
							$step = '2';
							$session->php_session_register('step', $step);
							tep_redirect(FILENAME_INSTALLER_MARK_COMPLETE);
						}
				} else {
					//Get the data.  May be a return to the page or a first hit.
					$order_status = tep_fill_variable('order_status', 'session', '1');
				}
			break;
			case '2': 
				//Details. Ie if so then what was installed. If not then why.
				if ($page_action == 'submit') {
				
					//Check the data and move if ok.
					$error_status = false;
						if ($order_type_id == '1') {
							if ($order_status == '1') {
								$equipment = tep_fill_variable('equipment', 'post', array());
								$session->php_session_register('equipment', $equipment);
								$post_type_id = tep_fill_variable('post_type_id', 'post', '');
								$session->php_session_register('post_type_id', $post_type_id);
								$other_items = tep_fill_variable('optional', 'post', array());
								$other_items = parse_equipment_array($other_items);
								$session->php_session_register('other_items', $other_items);
								$address_comments = tep_fill_variable('address_comments', 'post', '');
								$session->php_session_register('address_comments', $address_comments);
								$agent_comments = tep_fill_variable('agent_comments', 'post','');
								
								$session->php_session_register('agent_comments', $agent_comments);
							} else {
								$address_comments = tep_fill_variable('address_comments');
								$agent_comments = tep_fill_variable('agent_comments');
									
								$install_fail_reason = tep_fill_variable('install_fail_reason');
								$install_fail_3_yes_no = tep_fill_variable('install_fail_3_yes_no');
								$install_fail_3_house_number = tep_fill_variable('install_fail_3_house_number');
								$install_fail_3_street_name = tep_fill_variable('install_fail_3_street_name');
								$install_fail_4_yes_no = tep_fill_variable('install_fail_4_yes_no');
								$install_fail_4_new_date = tep_fill_variable('install_fail_4_new_date');
								
									//Do a wee bit of error checking then say good bye.
										if ($install_fail_reason == '4') {
											//Change of date or mark.
											if (!empty($install_fail_4_new_date)) {
												$date_stamp = strtotime($install_fail_4_new_date);
													if ($date_stamp == -1) {
														//Incorrect.
														$error_status = true;
														$error->add_error('installer_mark_complete', 'The entered date is either incorrect or in a wrong format.  Please check it and try again.');
														$error->add_error('installer_mark_complete', 'Alternatively if you do not know the requested date then please leave the date box blank.', 'warning');
													} elseif ($date_stamp < mktime()) {
														//In the past.
														$error_status = true;
														$error->add_error('installer_mark_complete', 'The entered date is in the past. Please check it and try again.');
														$error->add_error('installer_mark_complete', 'Alternatively if you do not know the requested date then please leave the date box blank.', 'warning');
													}
											}
										}
										if (!$error_status) {
											$session->php_session_register('address_comments', $address_comments);
											$session->php_session_register('agent_comments', $agent_comments);
											
											$session->php_session_register('install_fail_reason', $install_fail_reason);
											
											$session->php_session_register('install_fail_3_yes_no', $install_fail_3_yes_no);
											$session->php_session_register('install_fail_3_house_number', $install_fail_3_house_number);
											$session->php_session_register('install_fail_3_street_name', $install_fail_3_street_name);
											
											$session->php_session_register('install_fail_4_yes_no', $install_fail_4_yes_no);
											$session->php_session_register('install_fail_4_new_date', $install_fail_4_new_date);
										}
							}
						} elseif ($order_type_id == '2') {
							$query = $database->query("select service_call_reason_id, service_call_detail_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $oID . "' limit 1");
							$result = $database->fetch_array($query);
								if ($order_status == '1') {
									if ($result['service_call_reason_id'] == '1') {
										//Exchange Rider:
										$equipment = tep_fill_variable('equipment', 'post', array());	
										$session->php_session_register('equipment', $equipment);
									} elseif ($result['service_call_reason_id'] == '2') {
										//Install New Rider or BBox:
										$equipment = tep_fill_variable('equipment', 'post', array());	
										$session->php_session_register('equipment', $equipment);
									} elseif ($result['service_call_reason_id'] == '3') {
										//Replace/Exchange Agent SignPanel:
										$equipment = tep_fill_variable('equipment', 'post', array());	
										$session->php_session_register('equipment', $equipment);
										$remove_equipment = tep_fill_variable('remove_equipment', 'post', array());	
										$session->php_session_register('remove_equipment', $remove_equipment);
									} elseif ($result['service_call_reason_id'] == '4') {
										//Post Leaning/Straighten Post:
										$sc_success_3 = tep_fill_variable('sc_success_3', 'post', '1');
										$session->php_session_register('sc_success_3', $sc_success_3);
									} elseif ($result['service_call_reason_id'] == '5') {
										//Move Post:
										
										
									} elseif ($result['service_call_reason_id'] == '6') {
										//Install equipment forgotten at install:
										$equipment = tep_fill_variable('equipment', 'post', array());	
										$session->php_session_register('equipment', $equipment);
									} elseif ($result['service_call_reason_id'] == '7') {
										//Other:
										
									}
									$other_items = tep_fill_variable('optional', 'post', array());
									$other_items = parse_equipment_array($other_items);
									$session->php_session_register('other_items', $other_items);
								} else {
									//Failure.
										//if ($result['service_call_reason_id'] == '5') {
											$sc_fail_5 = tep_fill_variable('sc_fail_5', 'post', '1');
											$session->php_session_register('sc_fail_5', $sc_fail_5);
										//}
								
								}
							$address_comments = tep_fill_variable('address_comments', 'post', '');
							$agent_comments = tep_fill_variable('agent_comments', 'post', '');
							$session->php_session_register('address_comments', $address_comments);
							$session->php_session_register('agent_comments', $agent_comments);
						} elseif ($order_type_id == '3') {
							if ($order_status == '1') {
								$equipment = tep_fill_variable('equipment', 'post', array());
								$address_comments = tep_fill_variable('address_comments', 'post', '');
								$agent_comments = tep_fill_variable('agent_comments', 'post', '');
								$metal_stakes = tep_fill_variable('metal_stakes', 'post', '0');
								
								$session->php_session_register('equipment', $equipment);
								$session->php_session_register('address_comments', $address_comments);
								$session->php_session_register('agent_comments', $agent_comments);
								$session->php_session_register('metal_stakes', $metal_stakes);
								
							} else {
								//Check the date.
								$removal_fail_new_date = tep_fill_variable('removal_fail_new_date', 'post', '');
								$address_comments = tep_fill_variable('address_comments', 'post', '');
								$agent_comments = tep_fill_variable('agent_comments', 'post', '');
									if (!empty($removal_fail_new_date)) {
										$date_stamp = strtotime($removal_fail_new_date);
											if ($date_stamp == -1) {
												//Incorrect.
												$error_status = true;
												$error->add_error('installer_mark_complete', 'The entered date is either incorrect or in a wrong format.  Please check it and try again.');
												$error->add_error('installer_mark_complete', 'Alternatively if you do not know the requested date then please leave the date box blank.', 'warning');
											} elseif ($date_stamp < mktime()) {
												//In the past.
												$error_status = true;
												$error->add_error('installer_mark_complete', 'The entered date is in the past. Please check it and try again.');
												$error->add_error('installer_mark_complete', 'Alternatively if you do not know the requested date then please leave the date box blank.', 'warning');
											}
									}
									if (!$error_status) {
										$session->php_session_register('removal_fail_new_date', $removal_fail_new_date);
										$session->php_session_register('address_comments', $address_comments);
										$session->php_session_register('agent_comments', $agent_comments);
									}
							}
						}

						if (!$error_status) {
							$step = '3';
							$session->php_session_register('step', $step);
							tep_redirect(FILENAME_INSTALLER_MARK_COMPLETE);
						}
				} else {
					//Get the data.  May be a return to the page or a first hit.
						if ($order_type_id == '1') {
							if ($order_status == '1') {
								$equipment = tep_fill_variable('equipment', 'session', array());
								$post_type_id = tep_fill_variable('post_type_id', 'session', '');
								$other_items = tep_fill_variable('other_items', 'session', array());
								$address_comments = tep_fill_variable('address_comments', 'session', '');
                                $agent_comments = tep_fill_variable('agent_comments', 'session', '');
							} else {
								$address_comments = tep_fill_variable('address_comments', 'session', '');
								$agent_comments = tep_fill_variable('agent_comments', 'session', '');
								
								$install_fail_reason = tep_fill_variable('install_fail_reason', 'post', tep_fill_variable('install_fail_reason', 'session', ''));
								
								$install_fail_3_yes_no = tep_fill_variable('install_fail_3_yes_no', 'post', tep_fill_variable('install_fail_3_yes_no', 'session', ''));
								$install_fail_3_house_number = tep_fill_variable('install_fail_3_house_number', 'post', tep_fill_variable('install_fail_3_house_number', 'session', $order_data_result['house_number']));
								$install_fail_3_street_name = tep_fill_variable('install_fail_3_street_name', 'post', tep_fill_variable('install_fail_3_street_name', 'session', $order_data_result['street_name']));
							
								$install_fail_4_yes_no = tep_fill_variable('install_fail_4_yes_no', 'post', tep_fill_variable('install_fail_4_yes_no', 'session', ''));
								$install_fail_4_new_date = tep_fill_variable('install_fail_4_new_date', 'post', tep_fill_variable('install_fail_4_new_date', 'session', ''));
							}
						} elseif ($order_type_id == '2') {
							
								if ($order_status == '1') {
									$equipment = tep_fill_variable('equipment', 'session', array());
									
									$remove_equipment = tep_fill_variable('remove_equipment', 'session', array());
									$sc_success_3 = tep_fill_variable('sc_success_3', 'session', '1');
									$other_items = tep_fill_variable('other_items', 'session', array());
								} else {
									$sc_fail_5 = tep_fill_variable('sc_fail_5', 'session', '1');
								}
							$address_comments = tep_fill_variable('address_comments', 'session', '');
							$agent_comments = tep_fill_variable('agent_comments', 'session', '');
						} elseif($order_type_id == '3') {
							if ($order_status == '1') {
								
								$equipment = tep_fill_variable('equipment', 'session', array());
								
								$address_comments = tep_fill_variable('address_comments', 'session', '');
								$agent_comments = tep_fill_variable('agent_comments', 'session', '');
								
								$metal_stakes = tep_fill_variable('metal_stakes', 'session', $order_data_result['number_of_posts']);
							} else {
								$removal_fail_new_date = tep_fill_variable('removal_fail_new_date', 'session', '');
							
								$address_comments = tep_fill_variable('address_comments', 'post', tep_fill_variable('agent_comments', 'session'));
								$agent_comments = tep_fill_variable('agent_comments', 'post', tep_fill_variable('agent_comments', 'session'));
							}
						}
				}
			break;
			case '3': 
				//Confirmation.
				if ($page_action == 'submit') {
					$error_status = false;
					
					$order_info_query = $database->query("select o.order_id, o.user_id, o.service_level_id, o.date_schedualed, a.house_number, a.street_name, a.city, a.zip, a.zip4, a.number_of_posts, a.state_id, a.county_id, a.cross_street_directions, a.address_id, o.equipment_cost, o.order_total, o.billing_method_id from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_ADDRESSES . " a left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_ORDER_TYPES . " ot where o.order_id = '" . $oID . "' and o.order_id = od.order_id and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.user_id = ud.user_id limit 1");
					$order_info_result = $database->fetch_array($order_info_query);
						if ($order_type_id == '1') {
							//Install.
								if ($order_status == '1') {
									
									$change_address = tep_fill_variable('change_address', 'session');
									
									$equipment = tep_fill_variable('equipment', 'session', array());
									$post_type_id = tep_fill_variable('post_type_id', 'session', '');
									$other_items = tep_fill_variable('other_items', 'session', array());
									$address_comments = tep_fill_variable('address_comments', 'session', '');
									$agent_comments = tep_fill_variable('agent_comments', 'session', '');
									
									//Time to mark as done and set all the equipment as sorted.
									//Assign the post to the order.
									$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '2', post_type_id = '" . $post_type_id . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
									$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', date_completed = '" . mktime() . "', last_modified = '" . mktime() . "', last_modified_by = '" . $installer_id . "', order_completed_status = '1' where order_id = '" . $oID . "' limit 1");
									$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
									tep_create_order_history($oID, '3', 'Your order has now been completed successfully.', true);
									
									//Post is assigned after or else problems happen from above.
									tep_assign_post_to_order($oID, $post_type_id, '2', fetch_address_zip4($order_info_result['address_id']), $order_info_result['number_of_posts']);
									$equipment[] = $post_type_id;
									//Now lets loop over the extra items and add them to the order, can do it now as we can manually set the status here.
									
										if (!empty($other_items)) {
											reset($other_items);
												while(list($group_id, $items) = each($other_items)) {
													$count= count($items);
													$n = 0;
														while($n < $count) {
															tep_assign_equipment_to_order($oID, $group_id, $items[$n], '2', $order_info_result['zip4'], $order_info_result['user_id'], $order_info_result['address_id'], '1');
															$answer_query = $database->query("select install_equipment_id, remove_equipment_id from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_answer_id = '" . $items[$n] . "' limit 1");
															$answer_result = $database->fetch_array($answer_query);
																if (!empty($answer_result['install_equipment_id'])) {
																	$equipment[] = $answer_result['install_equipment_id'];
																}
																if (!empty($answer_result['install_equipment_id'])) {
																	$equipment[] = $answer_result['install_equipment_id'];
																}
															
															$n++;
														}
												}
                                            $extra_equipment_cost = tep_fetch_equipment_cost($other_items, $service_level_id, $address_id, '2');
                                            if ($extra_equipment_cost > 0) {
												$account = new account($order_data_result['user_id'], '', $order_data_result['billing_method_id']);
												$account->set_debit_credit_amount($extra_equipment_cost, 'Extra Equipment', $address_name, $order_type_id, $oID);
                                            }
										}
									
									//Now lets go over the requested items and mark them either installed or release them.
									$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
										foreach($database->fetch_array($query) as $result){
												if (in_array($result['equipment_id'], $equipment)) {
													//Mark as installed.
													//$database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')");
													$database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')");
													//$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
													$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
													//$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
													$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
													tep_add_equipment_item_history($result['equipment_item_id'], '2', '', $oID, $order_info_result['address_id']);
												} else {
													//Mark as not installed and release the hold.
													//$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
													$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
													//$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
													$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
													tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
												}
										}
										
									//Should be all done.  Now lets make the email and then its all sorted :).	
									$change_address = tep_fill_variable('change_address', 'session');
										if (!empty($change_address) && ($change_address == true)) {
											$order_email = new order_email($oID, true, 'install_success_wrong_house', $post_type_id, $equipment, $other_items);
										} else {
											$order_email = new order_email($oID, true, 'install_success_complete', $post_type_id, $equipment, $other_items);
										}
									$session->php_session_unregister('equipment');
									$session->php_session_unregister('post_type_id');
									$session->php_session_unregister('other_items');
									$session->php_session_unregister('address_comments');
									$session->php_session_unregister('agent_comments');
									$session->php_session_unregister('order_status');	
									$session->php_session_unregister('change_address');	
								} else {
									//Was not installed successfully.  This can get a bit tricky with a few of them.
									$email_name = '';
									
									$install_fail_reason = tep_fill_variable('install_fail_reason', 'session', '');
									$address_comments = tep_fill_variable('address_comments', 'session', '');
									$agent_comments = tep_fill_variable('agent_comments', 'session', '');
									
									$install_fail_3_yes_no = tep_fill_variable('install_fail_3_yes_no', 'session', '');
									$install_fail_3_house_number = tep_fill_variable('install_fail_3_house_number', 'session', '');
									$install_fail_3_street_name = tep_fill_variable('install_fail_3_street_name', 'session', '');
									
									$install_fail_4_yes_no = tep_fill_variable('install_fail_4_yes_no', 'session', '');
									$install_fail_4_new_date = tep_fill_variable('install_fail_4_new_date', 'session', '');
										
										if ($install_fail_reason == '1') {
											//Posts Not Allowed
											//Add to the post not allowed list.
											//Change to service call.
											//Release equipment.
											tep_change_to_service_call($oID);
											$email_name = 'install_failed_post_not_allowed';
											$database->query("insert into " . TABLE_POST_NOT_ALLOWED . " (street_name, city, county_id, state_id, house_number_range, house_number_range_start, house_number_range_end, hoa_contact_info, comments, zip, zip4) values ('" . $order_info_result['street_name'] . "', '" . $order_info_result['city'] . "', '" . $order_info_result['county_id'] . "', '" . $order_info_result['state_id'] . "', '', '', '', '', 'Automatically added from Installer Report.', '" . $order_info_result['zip'] . "', '" . $order_info_result['zip4'] . "')");
											
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '5' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
												
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
													foreach($database->fetch_array($query) as $result){
														$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
														$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
														tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
													}
										
										} elseif ($install_fail_reason == '2') {
											//No Room to Install
											//Change to service call.
											//Release equipment.
											tep_change_to_service_call($oID);
											$email_name = 'install_failed_no_room';
											
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '5' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
													foreach($database->fetch_array($query) as $result){
														$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
														$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
														tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
													}
										} elseif ($install_fail_reason == '3') {
											//Wrong House #
											if ($install_fail_3_yes_no == '1') {
												//Could work out correct.
												//Change house number.
												//Add on extra fee.
												//Do a session variable.
												//Set as step 2 and installed and let it run.
												$database->query("update " . TABLE_ADDRESSES . " set house_number = '" . $install_fail_3_house_number . "', street_name = '" . $install_fail_3_street_name . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
												$database->query("update " . TABLE_ORDERS . " set extra_cost = '10', order_total = order_total + 10, extra_cost_description = 'Incorrect Address Entered', completed_details = 'Wrong house entered.<br>Installer found correct address.' where order_id = '" . $oID . "' limit 1");
											
												$session->php_session_unregister('install_fail_reason');
												//$session->php_session_unregister('address_comments');
												//$session->php_session_unregister('agent_comments');
												$session->php_session_unregister('install_fail_3_yes_no');
												$session->php_session_unregister('install_fail_3_house_number');
												$session->php_session_unregister('install_fail_3_street_name');
												$session->php_session_unregister('install_fail_4_yes_no');
												$session->php_session_unregister('install_fail_4_new_date');	
												$session->php_session_unregister('order_status');
												
												
												$session->php_session_register('change_address', true);
												$session->php_session_register('order_status', '1');
												$session->php_session_register('step', '2');
												tep_redirect(FILENAME_INSTALLER_MARK_COMPLETE);
											} elseif ($install_fail_3_yes_no == '2') {
												//Could not work out correct.
												//Change to service call.
												
												tep_change_to_service_call($oID);
												$email_name = 'install_failed_wrong_house_could_not_find';
												
												$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '5' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
												$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
													foreach($database->fetch_array($query) as $result){
														$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
														$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
														tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
													}
											}
										} elseif ($install_fail_reason == '4') {
											//Stopped by Homeowner/Tenant
											if ($install_fail_4_yes_no == '1') {
												//New install date.
												//Create a service call and charge $20 (mark as done - its this one).
												//Change the date on this order and set as pending or leave date and mark as on hold.
												$email_name = 'install_failed_stopped_by_owner_new_date';
												$query = $database->query("select user_id, address_id, order_type_id, base_cost, extended_cost, equipment_cost, discount_cost, deposit_cost, order_total, date_added, date_schedualed, last_modified, date_accepted, date_completed, order_status_id, billing_method_id, service_level_id, order_issue, extra_cost, extra_cost_description, special_conditions, inserted_order_type_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
                                                $result = $database->fetch_array($query);
                                                $billing_method_id = $result['billing_method_id'];
                                                $order_user_id = $result['user_id'];
												
												$database->query("insert into " . TABLE_ORDERS . " (user_id, address_id, order_type_id, base_cost, extended_cost, equipment_cost, discount_cost, deposit_cost, order_total, date_added, date_schedualed, last_modified, date_accepted, date_completed, order_status_id, billing_method_id, service_level_id, order_issue, extra_cost, extra_cost_description, special_conditions, inserted_order_type_id, order_completed_status, completed_details) values ('" . $result['user_id'] . "', '" . $result['address_id'] . "', '2', '20', '', '', '', '', '20', '" . $result['date_added'] . "', '" . $result['date_schedualed'] . "', '" . mktime() . "', '" . $result['date_accepted'] . "', '" . mktime() . "', '3', '" . $result['billing_method_id'] . "', '" . $result['service_level_id'] . "', '', '0', '', '', '" . $result['inserted_order_type_id'] . "', '1', 'Install stopped by homeowner<br>.Has been rescheduled.')");
												$new_order_id = $database->insert_id();
												
												$query = $database->query("select installer_id from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $oID . "' limit 1");
												$result = $database->fetch_array($query);
												
												$database->query("insert into " . TABLE_INSTALLERS_TO_ORDERS . " (installer_id, order_id) values ('" . $result['installer_id'] . "', '" . $new_order_id . "')"); 
												
												$query = $database->query("select number_of_posts, special_instructions, admin_comments, installer_comments from " . TABLE_ORDERS_DESCRIPTION . " where order_id = '" . $oID . "' limit 1");
												$result = $database->fetch_array($query);
												
												$database->query("insert into " . TABLE_ORDERS_DESCRIPTION . " (order_id, number_of_posts, special_instructions, admin_comments, installer_comments) values ('" . $new_order_id . "', '" . $result['number_of_posts'] . "', '" . $result['special_instructions'] . "', '" . $result['admin_comments'] . "', '" . $agent_comments . "')");
											
												$query = $database->query("select order_status_id, date_added, user_notified, comments from " . TABLE_ORDERS_HISTORY . " where order_id = '" . $oID . "'");
													foreach($database->fetch_array($query) as $result){
														$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $new_order_id . "', '" . $result['order_status_id'] . "','" . $result['date_added'] . "', '" . $result['user_notified'] . "', '" . $result['comments'] . "')");
													}
													
												//Bill them for the new order.
												$account = new account($order_user_id, '', $billing_method_id);
												$account->set_debit_amount('20', tep_get_order_type_name('2'), $result['house_number']. ' ' .$result['street_name'], '2', $new_order_id);

												tep_create_order_history($new_order_id, '3', 'Your order was not able to be completed successfully.', true);
												
												//Update the old order with the new date or leave and mark on hold.
													if (!empty($install_fail_4_new_date)) {
														$date_stamp = strtotime(stripslashes($install_fail_4_new_date));
														$database->query("update " . TABLE_ORDERS . " set order_status_id = '1', date_schedualed = '" . $date_stamp . "', order_completed_status = '0', completed_details = 'Install stopped by homeowner.<br>Post required and install rescheduled.' where order_id = '" . $oID . "' limit 1");
														tep_create_order_history($oID, '1', 'Your order could not be completed.  It has been rescheduled for ' . stripslashes($install_fail_4_new_date) . '.', true);
													} else {
														//Mark as on hold.
														tep_create_order_history($oID, '4', 'Your order could not be completed.  It has not yet been rescheduled.  Please contact Realty Sign Post.', true);
                                                        $hold_order = new orders('other', $oID);
                                                        $hold_order->flag_and_hold();
													}
											} elseif ($install_fail_4_yes_no == '2') {
												//No new install date.
												//Change to service call
												tep_change_to_service_call($oID);
												$email_name = 'install_failed_stopped_by_owner_no_new_date';
												
												$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '5' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
												$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
													foreach($database->fetch_array($query) as $result){
														$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
														$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
														tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
													}
											} elseif ($install_fail_4_yes_no == '3') {
												//Unsure of new date or not.
												//Red flag and mark on hold.
                                                tep_create_order_history($oID, '5', 'Your order could not be completed.  It has not yet been rescheduled.  Please contact Realty Sign Post.', true);
                                                $hold_order = new orders('other', $oID);
                                                $hold_order->flag_and_hold();
												$email_name = 'install_failed_stopped_by_owner_unsure';
											}
										} elseif ($install_fail_reason == '5') {
											//Unable to Find Address
											//Change to service call.
											tep_change_to_service_call($oID);
											$email_name = 'install_failed_unable_to_find';
											
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '5' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
										
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												    foreach($database->fetch_array($query) as $result)	{
														$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
														$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
														tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
													}
										} elseif ($install_fail_reason == '6') {
											//Post Already Installed
											//Change to service call.
											tep_change_to_service_call($oID);
											$email_name = 'install_failed_already_installed';
											
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '5' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
										
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
													foreach($database->fetch_array($query) as $result){
														$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
														$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
														tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
													}
										}
									
									$session->php_session_unregister('install_fail_reason');
									$session->php_session_unregister('address_comments');
									$session->php_session_unregister('agent_comments');
									$session->php_session_unregister('install_fail_3_yes_no');
									$session->php_session_unregister('install_fail_3_house_number');
									$session->php_session_unregister('install_fail_3_street_name');
									$session->php_session_unregister('install_fail_4_yes_no');
									$session->php_session_unregister('install_fail_4_new_date');	
									$session->php_session_unregister('order_status');		
									
									$order_email = new order_email($oID, true, $email_name);
									
								}
						} elseif ($order_type_id == '2') {
							$query = $database->query("select service_call_reason_id, service_call_detail_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $oID . "' limit 1");
							$result = $database->fetch_array($query);
							$equipment = tep_fill_variable('equipment', 'session', array());
							$remove_equipment = tep_fill_variable('remove_equipment', 'session', array());
							$address_comments = tep_fill_variable('address_comments', 'session', '');
							$agent_comments = tep_fill_variable('agent_comments', 'session', '');
							$sc_success_3 = tep_fill_variable('sc_success_3', 'session', '');
							$sc_fail_5 = tep_fill_variable('sc_fail_5', 'session', '');
							
							$email_name = 'service_success_complete'; //mjp 20111111
								if ($order_status == '1') {
									//Success.
									$other_items = tep_fill_variable('other_items', 'session', array());
									$other_items_equipment = array();
										
										if (!empty($other_items)) {
											reset($other_items);
												while(list($group_id, $items) = each($other_items)) {
													$count= count($items);
													$n = 0;
														while($n < $count) {
															tep_assign_equipment_to_order($oID, $group_id, $items[$n], '2', $order_info_result['zip4'], $order_info_result['user_id'], $order_info_result['address_id'], '1');
															$answer_query = $database->query("select install_equipment_id, remove_equipment_id from " . TABLE_EQUIPMENT_GROUP_ANSWERS . " where equipment_group_answer_id = '" . $items[$n] . "' limit 1");
															$answer_result = $database->fetch_array($answer_query);
																if (!empty($answer_result['install_equipment_id'])) {
																	$other_items_equipment[] = $answer_result['install_equipment_id'];
																}
																
															
															$n++;
														}
												}
                                            $extra_equipment_cost = tep_fetch_equipment_cost($other_items, $service_level_id, $address_id, '2');
                                            if ($extra_equipment_cost > 0) {
												$account = new account($order_data_result['user_id'], '', $order_data_result['billing_method_id']);
												$account->set_debit_credit_amount($extra_equipment_cost, 'Extra Equipment', $address_name, $order_type_id, $oID);
                                            }
										}
									
                                        if (!empty($other_items_equipment)) {
											$equip_query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												foreach($database->fetch_array($equip_query) as $equip_result){
														if (in_array($equip_result['equipment_id'], $other_items_equipment)) {
																//Mark as installed.
																$database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $equip_result['equipment_id'] . "', '" . $equip_result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')");
																//echo "insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')". '<br>';
																$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $equip_result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
																//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1". '<br>';
																$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $equip_result['equipment_item_id'] . "' limit 1");
																//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
															
																tep_add_equipment_item_history($equip_result['equipment_item_id'], '2', '', $oID, $order_info_result['address_id']);
														}
												}
										}	

										if ($result['service_call_reason_id'] == '1') {
											//Exchange Rider:
											//Assign the extra equipment.  Add the comments.  Mark as done and send the email.  Easy as pie.
											$email_name = 'service_success_rider_exchange';
											
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												foreach($database->fetch_array($query) as $result){
														if (in_array($result['equipment_id'], $equipment)) {
															if ($result['method_id'] == '1') {
																//Mark as installed.
																$database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')");
																//echo "insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')". '<br>';
																$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
																//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1". '<br>';
																$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
																//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
															
																tep_add_equipment_item_history($result['equipment_item_id'], '2', '', $oID, $order_info_result['address_id']);
															} else {
																//Mark as removed.
																$database->query("update " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " set equipment_status_id = '0' where address_id = '" . $order_info_result['address_id'] . "' and equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
																//echo "update " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " set equipment_status_id = '0' where address_id = '" . $order_info_result['address_id'] . "' and equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
																$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
																//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1" . '<br>';
																$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
																//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
																tep_add_equipment_item_history($result['equipment_item_id'], '3', '', $oID, $order_info_result['address_id']);
															}
														} else {
															//Mark as not installed and release the hold.
															$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1". '<br>';

																if ($result['method_id'] == '1') {
																	$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
																	//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
																}
															tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
														}
												}
											
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											//echo "update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1" . '<br>';

											//die();
											tep_create_order_history($oID, '3', 'Your order has now been completed successfully.', true);
										
										
										} elseif ($result['service_call_reason_id'] == '2') {
											//Install New Rider or BBox:
											
											$email_name = 'service_success_new_equipment';
											
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												foreach($database->fetch_array($query) as $result){
														if (in_array($result['equipment_id'], $equipment)) {
															//Mark as installed.
															$database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')");
															//echo "insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')". '<br>';
															$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1". '<br>';
															$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
															tep_add_equipment_item_history($result['equipment_item_id'], '2', '', $oID, $order_info_result['address_id']);
														} else {
															//Mark as not installed and release the hold.
															$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1".'<br>';
															$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
															tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
														}
												}
												
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											//echo "update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '2' where address_id = '" . $order_info_result['address_id'] . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS . " set order_status_id = '3', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1". '<br>';
											//die();
											tep_create_order_history($oID, '3', 'Your order has now been completed successfully.', true);
										
										} elseif ($result['service_call_reason_id'] == '3') {
											//Replace/Exchange Agent SignPanel:
											//Need to first set it as done then assign items and unassign removed items.
											$email_name = 'service_success_exchange_signpanel';
											
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												foreach($database->fetch_array($query) as $result){
														if (in_array($result['equipment_id'], $equipment)) {
															//Mark as installed.
															$database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')");
															//echo "insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')". '<br>';
															$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1". '<br>';
															$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
															tep_add_equipment_item_history($result['equipment_item_id'], '2', '', $oID, $order_info_result['address_id']);
														} else {
															//Mark as not installed and release the hold.
															$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1".'<br>';
															$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
															tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
														}
												}
												
											//Get rid of removed.
											$query = $database->query("select eita.equipment_item_id, e.equipment_id, e.name as equipment_name from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.equipment_id = e.equipment_id and eita.address_id = '" . $order_data_result['address_id'] . "' and eita.equipment_status_id = '2' and e.equipment_type_id = '4'");
                                                foreach($database->fetch_array($query) as $result){
													if (in_array($result['equipment_id'], $remove_equipment)) {
														//Assign it to the order and unassign it from the address.
														$database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, method_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '" . $oID . "', '" . addslashes($result['equipment_name']) . "', '0', '0')");
														//echo "insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, method_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '" . $oID . "', '" . $result['equipment_name'] . "', '0', '0')". '<br>';
														$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
														//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
														$database->query("update " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and address_id = '" . $order_data_result['address_id'] . "' limit 1");
														//echo "update " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and address_id = '" . $order_data_result['address_id'] . "' limit 1". '<br>';
														tep_add_equipment_item_history($result['equipment_item_id'], '3', '', $oID, $order_info_result['address_id']);
													}
												}
												
												//Now update the order.
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											//echo "update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1". '<br>';

											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', completed_details = '" . addslashes($details) . "', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1". '<br>';
											
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1". '<br>';
											
											tep_create_order_history($oID, '3', 'Your order has now been completed successfully.', true);

										} elseif ($result['service_call_reason_id'] == '4') {
											//Post Leaning/Straighten Post:
												if ($sc_success_3 == '1') {
													$details = 'The post was found leaning and was fixed.';
												} elseif($sc_success_3 == '2') {
													$details = 'The post was not found leaning.';
												}
											$email_name = 'service_success_leaning_post';
											
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											//echo "update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1". '<br>';

											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', completed_details = '" . addslashes($details) . "', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', completed_details = '" . $details . "', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1". '<br>';
											
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1". '<br>';

											
											tep_create_order_history($oID, '3', 'Your order has now been completed successfully.', true);

										} elseif ($result['service_call_reason_id'] == '5') {
											//Move Post:
											$email_name = 'service_success_move_post';
												
											
												
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");

											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											
											tep_create_order_history($oID, '3', 'Your order has now been completed successfully.', true);
										} elseif ($result['service_call_reason_id'] == '6') {
											//Install equipment forgotten at install:
											
											$email_name = 'service_success_new_equipment';
												
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											
											
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												foreach($database->fetch_array($query) as $result){
														if (in_array($result['equipment_id'], $equipment)) {
															//Mark as installed.
															$database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')");
															$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
															$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
															tep_add_equipment_item_history($result['equipment_item_id'], '2', '', $oID, $order_info_result['address_id']);
														} else {
															//Mark as not installed and release the hold.
															$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
															$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
															tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
														}
												}
												
											tep_create_order_history($oID, '3', 'Your order has now been completed successfully.', true);
										
										} elseif ($result['service_call_reason_id'] == '7') {
											//Other:
											$email_name = 'service_success_other';
												
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");

											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											
											tep_create_order_history($oID, '3', 'Your order has now been completed successfully.', true);
										}
								} else {
									//Failure.
									if ($result['service_call_reason_id'] == '1') {
											//Exchange Rider:
											$reason = '';
											$update_string = '';
												if ($sc_fail_5 == '1') {
													$reason ='Original Post Missing';
													$update_string = 'Original Post Missing.';
												} elseif ($sc_fail_5 == '2') {
													$reason = 'Other';
													$update_string = 'Other.';
												}
											//Release the hold on any install items and mark as issued.
											$email_name = 'service_failed_rider_exchange';
											//Need to release all equipment.
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												foreach($database->fetch_array($query) as $result){
													//Mark as not installed and release the hold.
													$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
													//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1".'<br>';
														if ($result['method_id'] == '1') {
															$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
															tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
														}
													
												}
												
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '0', completed_details = 'Service call not completed.<br>".$reason."', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");

											tep_create_order_history($oID, '3', 'Your order was not able to be completed as the post was missing.', true);
											
										    if ($order_info_result['equipment_cost'] > 0) {
												$account = new account($order_data_result['user_id'], '', $order_data_result['billing_method_id']);
												$account->set_credit_amount($order_info_result['equipment_cost'], 'Refund for Equipment', 'Equipment was not able to be installed for an order.  The hire cost has been returned.', $oID, 'refund');
											}
										} elseif ($result['service_call_reason_id'] == '2') {
											//Install New Rider or BBox:
											$reason = '';
											$update_string = '';
												if ($sc_fail_5 == '1') {
													$reason ='Original Post Missing';
													$update_string = 'Original Post Missing.';
												} elseif ($sc_fail_5 == '2') {
													$reason = 'Other';
													$update_string = 'Other.';
												}
											$email_name = 'service_failed_new_equipment';
											//Need to release all equipment.
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												foreach($database->fetch_array($query) as $result){
													//Mark as not installed and release the hold.
													$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
													//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1".'<br>';
													$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
													//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
													tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
												}
												
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											//echo "update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '2' where address_id = '" . $order_info_result['address_id'] . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '0', completed_details = 'Service call not completed.<br>".$reason."', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS . " set order_status_id = '3', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1". '<br>';
											//die();
											tep_create_order_history($oID, '3', 'Your order was not able to be completed as the post was missing.', true);
										
										} elseif ($result['service_call_reason_id'] == '3') {
											//Replace/Exchange Agent SignPanel:
											$reason = '';
											$update_string = '';
												if ($sc_fail_5 == '1') {
													$reason ='Original Post Missing';
													$update_string = 'Original Post Missing.';
												} elseif ($sc_fail_5 == '2') {
													$reason = 'Other';
													$update_string = 'Other.';
												}
											$email_name = 'service_failed_exchange';
											
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											//echo "update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '2' where address_id = '" . $order_info_result['address_id'] . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '0', completed_details = 'Service call not completed.<br>".$reason."', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS . " set order_status_id = '3', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1". '<br>';
											//die();
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												foreach ($database->fetch_array($query) as $result){
														
													//Mark as not installed and release the hold.
													$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
													//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1". '<br>';
													$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
													//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1" . '<br>';
													tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
												}
												//die();
											tep_create_order_history($oID, '3', 'Your order was not able to be completed as the post was missing.', true);
										} elseif ($result['service_call_reason_id'] == '4') {
											//Post Leaning/Straighten Post:
											$reason = '';
											$update_string = '';
												if ($sc_fail_5 == '1') {
													$reason ='Original Post Missing';
													$update_string = 'Original Post Missing.';
												} elseif ($sc_fail_5 == '2') {
													$reason = 'Other';
													$update_string = 'Other.';
												}
											$email_name = 'service_failed_leaning_post';

											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											//echo "update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', status = '2' where address_id = '" . $order_info_result['address_id'] . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '0', completed_details = 'Service call not completed.<br>".$reason."', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS . " set order_status_id = '3', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1". '<br>';
											//die();
											tep_create_order_history($oID, '3', 'Your order was not able to be completed as the post was missing.', true);
										} elseif ($result['service_call_reason_id'] == '5') {
											//Move Post:
											$reason = '';
											$update_string = '';
												if ($sc_fail_5 == '1') {
													$reason ='No Marker/Bad location directions';
													$update_string = 'No Marker/Bad location directions.';
												} elseif ($sc_fail_5 == '2') {
													$reason = 'Unable to Install where agent requests';
													$update_string = 'Unable to install where specified.';
												} elseif ($sc_fail_5 == '3') {
													$reason = 'Original Post Missing';
													$update_string = 'Original post was missing.  A new one has been installed.';
												}
												
											$email_name = 'service_failed_move_post';
												
											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											//echo "update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '0', completed_details = 'Service call not completed.<br>".$reason."', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '0', completed_details = 'Service call not completed.<br>".$reason.", date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1". '<br>';
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1". '<br>';
												if ($sc_fail_5 == '3') {
													//Mark the exisitng post as missing and assign a new one, plus of course charge some $.
													$post_type_query = $database->query("select equipment_id, replace_cost from " . TABLE_EQUIPMENT . " where equipment_type_id = '1'");
													$post_count = 0;
													$replace_cost = 0;
													$post_type = '';
													    foreach($database->fetch_array($post_type_query) as $post_type_result){
															$query = $database->query("select count(equipment_item_to_address_id) as count from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . $order_info_result['address_id'] . "' and equipment_id = '" . $post_type_result['equipment_id'] . "'");
															
															$result = $database->fetch_array($query);
																if ($result['count'] > 0) {
																	//Found it.
																	$replace_cost = $post_type_result['replace_cost'];
																	$post_type = $post_type_result['equipment_id'];
																	$post_count = $result['count'];
																	break;
																}
														}
													//Unassing the old ones.
													$query = $database->query("select equipment_item_id from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . $order_info_result['address_id'] . "' and equipment_id = '" . $post_type . "'");
														foreach($database->fetch_array($query) as $result){
															$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '4' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '4' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1". '<br>';
															$database->query("update " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " set equipment_status_id = '4' where equipment_item_id = '" . $result['equipment_item_id'] . "' and address_id = '" . $order_data_result['address_id'] . "' limit 1");
															//echo "update " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " set equipment_status_id = '4' where equipment_item_id = '" . $result['equipment_item_id'] . "' and address_id = '" . $order_data_result['address_id'] . "' limit 1". '<br>';
															tep_add_equipment_item_history($result['equipment_item_id'], '5', '', $oID, $order_info_result['address_id']);
														}
														
													
													//Assign the new ones.
													
													tep_assign_post_to_order($oID, $post_type, '2', fetch_address_zip4($order_info_result['address_id']), $post_count);
													
													$query = $database->query("select equipment_item_id, equipment_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "' and equipment_id = '" . $post_type . "'");
														foreach($database->fetch_array($query) as $result){
															$database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $order_info_result['address_id'] . "')");
															tep_add_equipment_item_history($result['equipment_item_id'], '2', '', $oID, $order_info_result['address_id']);
														}
													
													$extra_cost = ($replace_cost*$post_count);
													$extra_cost_reason = 'Charge for missing posts.';
													$database->query("update " . TABLE_ORDERS . " set extra_cost = '" . $extra_cost . "', extra_cost_description = '" . addslashes($extra_cost_reason) . "', order_total = '" . ($order_info_result['order_total']+$extra_cost) . "' where order_id = '" . $oID . "' limit 1");
													
													$account = new account($order_data_result['user_id'], '', $order_data_result['billing_method_id']);
													$account->set_debit_credit_amount($extra_cost, 'Missing/Damaged items.', $extra_cost_reason, '2', $oID);
												}
												
												
											tep_create_order_history($oID, '3', 'Your order was not able to be completed.  '.$update_string, true);
										
										} elseif ($result['service_call_reason_id'] == '6') {
											//Install equipment forgotten at install:
											$reason = '';
											$update_string = '';
												if ($sc_fail_5 == '1') {
													$reason ='Original Post Missing';
													$update_string = 'Original Post Missing.';
												} elseif ($sc_fail_5 == '2') {
													$reason = 'Other';
													$update_string = 'Other.';
												}
											$email_name = 'service_failed_forgotten_equipment';

											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
											//echo "update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1". '<br>';

											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '0', completed_details = 'Service call not completed.<br>".$reason."', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '0', completed_details = 'Service call not completed.<br>Post was missing.', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1". '<br>';
											
											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											//echo "update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1". '<br>';
											
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												while($result = $database->fetch_array($query)) {
														
													//Mark as not installed and release the hold.
													$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
													//echo "update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1". '<br>';
													$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
													//echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1" . '<br>';
													tep_add_equipment_item_history($result['equipment_item_id'], '7', '', $oID, $order_info_result['address_id']);
												}
												
											tep_create_order_history($oID, '3', 'Your order was not able to be completed as the post was missing.', true);
											
											
										} elseif ($result['service_call_reason_id'] == '7') {
											//Other:
											$reason = '';
											$update_string = '';
												if ($sc_fail_5 == '1') {
													$reason ='Original Post Missing';
													$update_string = 'Original Post Missing.';
												} elseif ($sc_fail_5 == '2') {
													$reason = 'Other';
													$update_string = 'Other.';
												}
											$email_name = 'service_failed_other';

											$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "' where address_id = '" . $order_info_result['address_id'] . "' limit 1");

											$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '0', completed_details = 'Service call not completed.<br>".$reason."', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
											

											$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
											
											tep_create_order_history($oID, '3', 'Your order was not able to be completed as the post was missing.', true);
										}
								}
							
							$session->php_session_unregister('equipment');
							$session->php_session_unregister('sc_success_3');
							$session->php_session_unregister('remove_equipment');
							$session->php_session_unregister('address_comments');
							$session->php_session_unregister('agent_comments');

							$order_email = new order_email($oID, true, $email_name);

						} elseif($order_type_id == '3') {
							//Time to do the removal.
								if ($order_status == '1') {
									//Success.
									/*
										Removals are a unique kind of order in that no equipment is currently assigned. 
										First we need to loop over and assign all the address equiupment to the order.
										Second we need to loop over what was removed, missing damaged and mark accordingly (in both order, address and in general).
										Thirdly we need to work out if extra charges are needed and charge.
										Fourth we need to work out if the deposit can be returned (if any) and do that.
									*/
									$equipment = tep_fill_variable('equipment', 'session', array());
									$metal_stakes = tep_fill_variable('metal_stakes', 'session', '0');
								
									$address_comments = tep_fill_variable('address_comments', 'session', '');
									$agent_comments = tep_fill_variable('agent_comments', 'session', '');
									
									$query = $database->query("select eita.equipment_id, eita.equipment_item_id, e.name as equipment_name, e.replace_cost, e.equipment_type_id from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.address_id = '" . $order_data_result['address_id'] . "' and eita.equipment_status_id = '2' and eita.equipment_id = e.equipment_id");
									$extra_cost = 0;
									$extra_cost_reason = '';
									$something_missing = false;   // choose a different template
                                        foreach($database->fetch_array($query) as $result){
											//Time to assign.
												// double-assignment removal fix
												
												if (isset($equipment[$result['equipment_item_id']]) && ($equipment[$result['equipment_item_id']] == '1')) {
													$equipment_status = '0'; //default status (available)
													/*	
														run through all orders assigned to those addresses
														get count of all pending removals
														if count>=2 then this item is bugged - we keep it's status as Installed
													*/
													$orders_count =  $database->query('SELECT count(o.`order_id`) as `pending_count` FROM orders o JOIN equipment_items_to_addresses eia ON eia.address_id = o.address_id WHERE o.`order_type_id`=3 AND o.`order_status_id`=1 AND eia.`equipment_item_id`='.$result['equipment_item_id']);
													$orders_count_result = $database->fetch_array($orders_count);
													$bugged = intval($orders_count_result['pending_count']); //bugged orders count
													
													if ($bugged>=2){
														/*
														this item is bugged
														we keep equipment_status_id as Installed in EQUIPMENT_ITEMS. It should've been set to 2 already, but I do it just in case something was wrong
														*/
														$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
													}
													else {
														//this item is OK
														$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $equipment_status . "' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
													}
													
													//write to history that this item was returned
													tep_add_equipment_item_history($result['equipment_item_id'], '3', '', $oID, $order_info_result['address_id']);
													
												//end of double-assignment fix											
													
												} elseif ($equipment[$result['equipment_item_id']] == '2') {
													//Damaged.
													$extra_cost += $result['replace_cost'];
														if (!empty($extra_cost_reason)) {
															$extra_cost_reason .= '<br>';
														}
													$extra_cost_reason .= $result['equipment_name'] . ' was found damaged at ' . $address_name . '.';
  													tep_add_equipment_item_history($result['equipment_item_id'], '4', '', $oID, $order_info_result['address_id']);
													$equipment_status = '5';
													$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $equipment_status . "' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
												} elseif ($equipment[$result['equipment_item_id']] == '3') {
													//Missing.
													$extra_cost += $result['replace_cost'];
															if (!empty($extra_cost_reason)) {
															$extra_cost_reason .= '<br>';
														}
   													tep_add_equipment_item_history($result['equipment_item_id'], '5', '', $oID, $order_info_result['address_id']);
													$extra_cost_reason .= $result['equipment_name'] . ' was not found at ' . $address_name . '.';
													$equipment_status = '4';
													$something_missing = true;
													$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $equipment_status . "' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
												}
												//write to those tables that the item was removed - regardless of it being bugged or not
    											$database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '" . $oID . "', '" . addslashes($result['equipment_name']) . "', '" . $equipment_status . "', '', '', '', '', '', '0')");
    											$database->query("update " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " set equipment_status_id = '" . $equipment_status . "' where equipment_item_id = '" . $result['equipment_item_id'] . "' and address_id = '" . $order_data_result['address_id'] . "' limit 1");
											
										}
    									$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
    									$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $address_comments  . "', deposit_cost = '0', status = '4' where address_id = '" . $order_info_result['address_id'] . "' limit 1");
                                        $database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_completed_status = '1', date_completed = '" . mktime() . "', extra_cost = '" . $extra_cost . "', order_total = order_total + '". $extra_cost . "', extra_cost_description = '" . addslashes($extra_cost_reason) . "' where order_id = '" . $oID . "' limit 1");
										if ((strpos(tep_fetch_installed_post_type($order_data_result['address_id']), 'PVC') !== false) && (($metal_stakes != $order_data_result['number_of_posts']) && ($metal_stakes < $order_data_result['number_of_posts']))) {
                                            $missing_stakes = ($order_data_result['number_of_posts'] - $metal_stakes);
											$extra_cost += ($missing_stakes * 20);
											if (!empty($extra_cost_reason)) {
												$extra_cost_reason .= '<br>';
											}
											$extra_cost_reason .= $missing_stakes . ' Metal Stake'.(($missing_stakes > 1) ? 's were' : ' was').' missing.';
											$something_missing = true;
										}

									$deposit_cost = $order_data_result['deposit_cost'];
										
										if ($something_missing || ($deposit_cost > 0) || ($extra_cost > 0)) {
											if ($deposit_cost > 0) {
											
											} else {
												$deposit_cost = 0;
											}
											if ($extra_cost > 0) {
												$deposit_cost -= $extra_cost;
													if ($deposit_cost < 0) {
														$extra_cost = ($deposit_cost*-1);
													} else {
														$extra_cost = 0;
													}
											}
											if ($deposit_cost > 0) {
												//Returning deposit.
												$account = new account($order_data_result['user_id'], '', $order_data_result['billing_method_id']);
												$account->set_credit_amount($deposit_cost, 'Refund for Deposit', 'The items have been successfully removed from ' . $address_name . '.  Your deposit has now been credited to your account and can be used for credit on future orders or you can request a refund.'.((!empty($extra_cost_reason)) ? ('<br><br>Please note that your deposit has been reducred by the following:<br>'.$extra_cost_reason) : ''), $oID, 'refund');
											}
											if ($extra_cost > 0 || $something_missing) {
												//Charging extra, or at least note missing items on invoice
												$account = new account($order_data_result['user_id'], '', $order_data_result['billing_method_id']);
												$account->set_debit_credit_amount($extra_cost, 'Missing/Damaged items.', $extra_cost_reason, '3', $oID);
											}
										}
										
									if ($something_missing)
										$order_email = new order_email($oID, true, 'removal_success_missing_equipment');
									else
										$order_email = new order_email($oID, true, 'removal_success_complete');
									//Yay all done.
								} else {
									//Removal failure.  The only reason is a rescheduale.  So we work out if there was a date and re-scheduale (+ make as service call) or we flag.
									$removal_fail_new_date = tep_fill_variable('removal_fail_new_date', 'session', '');
									$address_comments = tep_fill_variable('address_comments', 'session', '');
									$agent_comments = tep_fill_variable('agent_comments', 'session', '');
									
												$query = $database->query("select user_id, address_id, order_type_id, base_cost, extended_cost, equipment_cost, discount_cost, deposit_cost, order_total, date_added, date_schedualed, last_modified, date_accepted, date_completed, order_status_id, billing_method_id, service_level_id, order_issue, extra_cost, extra_cost_description, special_conditions, inserted_order_type_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
												$result = $database->fetch_array($query);
												
												$database->query("insert into " . TABLE_ORDERS . " (user_id, address_id, order_type_id, base_cost, extended_cost, equipment_cost, discount_cost, deposit_cost, order_total, date_added, date_schedualed, last_modified, date_accepted, date_completed, order_status_id, billing_method_id, service_level_id, order_issue, extra_cost, extra_cost_description, special_conditions, inserted_order_type_id) values ('" . $result['user_id'] . "', '" . $result['address_id'] . "', '2', '20', '', '', '', '', '20', '" . $result['date_added'] . "', '" . $result['date_schedualed'] . "', '" . mktime() . "', '" . $result['date_accepted'] . "', '" . mktime() . "', '3', '" . $result['billing_method_id'] . "', '" . $result['service_level_id'] . "', '', '0', '', '', '" . $result['inserted_order_type_id'] . "')");
												$new_order_id = $database->insert_id();
												
												$query = $database->query("select installer_id from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $oID . "' limit 1");
												$result = $database->fetch_array($query);
												
												$database->query("insert into " . TABLE_INSTALLERS_TO_ORDERS . " (installer_id, order_id) values ('" . $result['installer_id'] . "', '" . $new_order_id . "')"); 
												
												$query = $database->query("select number_of_posts, special_instructions, admin_comments, installer_comments from " . TABLE_ORDERS_DESCRIPTION . " where order_id = '" . $oID . "' limit 1");
												$result = $database->fetch_array($query);
												
												$database->query("insert into " . TABLE_ORDERS_DESCRIPTION . " (order_id, number_of_posts, special_instructions, admin_comments, installer_comments) values ('" . $new_order_id . "', '" . $result['number_of_posts'] . "', '" . $result['special_instructions'] . "', '" . $result['admin_comments'] . "', '" . $agent_comments . "')");
											
												$query = $database->query("select order_status_id, date_added, user_notified, comments from " . TABLE_ORDERS_HISTORY . " where order_id = '" . $oID . "'");
													foreach($database->fetch_array($query) as $result){
														$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $new_order_id . "', '" . $result['order_status_id'] . "','" . $result['date_added'] . "', '" . $result['user_notified'] . "', '" . $result['comments'] . "')");
													}
													
												//Bill them for the nfw order.
												$account = new account($order_data_result['user_id'], '', $order_data_result['billing_method_id']);
												$account->set_debit_amount('20', tep_get_order_type_name('2'), $order_data_result['house_number']. ' ' .$order_data_result['street_name'], '2', $new_order_id);

												tep_create_order_history($new_order_id, '3', 'The removal was prevented by the homeowner.' . ((!empty($removal_fail_new_date)) ? ' It has been rescheduled for ' . $removal_fail_new_date.'.': ' No new date was available.  Please contact Realty Sign Post for details.'), true);
												
												//Update the old order with the new date or leave and mark on hold.
													if (!empty($removal_fail_new_date)) {
														$date_stamp = strtotime(stripslashes($removal_fail_new_date));
														$database->query("update " . TABLE_ORDERS . " set order_status_id = '1', date_schedualed = '" . $date_stamp . "', order_completed_status = '0', completed_details = 'Removal stopped by homeowner.<br>Has been rescheduled.' where order_id = '" . $oID . "' limit 1");
														tep_create_order_history($oID, '1', 'Your order could not be completed.  It has been rescheduled for ' . stripslashes($removal_fail_new_date) . '.', true);
														$order_email = new order_email($oID, true, 'removal_failed_new_date');
													} else {
														//Mark as on hold.
                                                        tep_create_order_history($oID, '4', 'Your order could not be completed.  It has not yet been rescheduled.  Please contact Realty Sign Post.', true);
                                                        $hold_order = new orders('other', $oID);
                                                        $hold_order->flag_and_hold();

														$order_email = new order_email($oID, true, 'removal_failed_no_date');
													}
										
								}
						}
						if (!$error_status) {
							$session->php_session_unregister('step');
							$session->php_session_unregister('order_id');
							tep_redirect(FILENAME_INSTALLER_VIEW_TODAY);
						}
				} else {
						if ($order_type_id == '1') {
							//Install.
								if ($order_status == '1') {
									//Success.
									$equipment = tep_fill_variable('equipment', 'session', array());
									$post_type_id = tep_fill_variable('post_type_id', 'session', '');
									$other_items = tep_fill_variable('other_items', 'session', array());
									$address_comments = tep_fill_variable('address_comments', 'session', '');
									$agent_comments = tep_fill_variable('agent_comments', 'session', '');
								} else {
									//Failure.
									$address_comments = tep_fill_variable('address_comments', 'session', '');
									$agent_comments = tep_fill_variable('agent_comments', 'session', '');
									
									$install_fail_reason = tep_fill_variable('install_fail_reason', 'session', '');
									
									$install_fail_3_yes_no = tep_fill_variable('install_fail_3_yes_no', 'session', '');
									$install_fail_3_house_number = tep_fill_variable('install_fail_3_house_number', 'session', '');
									$install_fail_3_street_name = tep_fill_variable('install_fail_3_street_name', 'session', '');
									
									$install_fail_4_yes_no = tep_fill_variable('install_fail_4_yes_no', 'session', '');
									$install_fail_4_new_date = tep_fill_variable('install_fail_4_new_date', 'session', '');
								}
						} elseif ($order_type_id == '2') {
							$query = $database->query("select service_call_reason_id, service_call_detail_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $oID . "' limit 1");
							$result = $database->fetch_array($query);
							
								if ($order_status == '1') {
									$equipment = tep_fill_variable('equipment', 'session', array());	
									$remove_equipment = tep_fill_variable('remove_equipment', 'session', array());	
									$sc_success_3 = tep_fill_variable('sc_success_3', 'session', '1');	
									$other_items = tep_fill_variable('other_items', 'session', array());
								} else {
									$sc_fail_5 = tep_fill_variable('sc_fail_5', 'session', '1');	
								}
							$address_comments = tep_fill_variable('address_comments', 'session', '');
							$agent_comments = tep_fill_variable('agent_comments', 'session', '');
						} elseif($order_type_id == '3') {
								if ($order_status == '1') {
									$equipment = tep_fill_variable('equipment', 'session', array());
									$address_comments = tep_fill_variable('address_comments', 'session', '');
									$agent_comments = tep_fill_variable('agent_comments', 'session', '');
										if (strpos(tep_fetch_installed_post_type($order_data_result['address_id']), 'PVC') !== false) {
											$metal_stakes = tep_fill_variable('metal_stakes', 'session', $order_data_result['number_of_posts']);
										} else {
											$metal_stakes = tep_fill_variable('metal_stakes', 'session', '0');
										}
								} else {
                                    //echo "failed 2";
									$removal_fail_new_date = tep_fill_variable('removal_fail_new_date', 'session', '');
									$address_comments = tep_fill_variable('address_comments', 'session', '');
									$agent_comments = tep_fill_variable('agent_comments', 'session', '');
								}
						}
				}
			break;
		}
		
	$button_text = '';
	
?>
<script language="javascript" data-cfasync="false">
    $(document).ready(function () {
        $(".new_date").datepicker({startDate: "+0d"});
    });
</script>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('installer_mark_complete', 'all')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('installer_mark_complete', 'all'); ?></td>
	</tr>
	<tr>
		<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<table width="100%" class="pageBox" cellspacing="0" cellpadding="0">
								<form action="<?php echo PAGE_URL; ?>?page_action=submit" method="post">
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td class="pageBoxHeading" align="left">&nbsp;&nbsp;Job Id: <?php echo $order_data_result['order_id']; ?></td>
									<td class="pageBoxHeading" align="right">Date Scheduled: <?php echo date("n/d/Y", $order_data_result['date_schedualed']); ?>&nbsp;&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top" width="50%">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td class="pageBoxContent" valign="top" width="140"><b>Agent:</b> </td>
												<td class="pageBoxContent" valign="top" width="300" align="left"><?php echo $order_data_result['firstname'].' '.$order_data_result['lastname'] . ' - ' . $order_data_result['agency_name']; ?></td>
											</tr>
											<tr>
												<td class="pageBoxContent" valign="top" width="140"><b>Job Type:</b> </td>
												<td class="pageBoxContent" valign="top" width="300" align="left"><?php echo $order_data_result['order_type_name']; ?></td>
											</tr>
											<tr>
												<td class="pageBoxContent" valign="top" width="140"><b>Service Level:</b> </td>
												<td class="pageBoxContent" valign="top" width="300" align="left"><?php echo $order_data_result['service_level_name']; ?></td>
											</tr>
										</table>
									</td>
									<td valign="top" width="50%">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td class="pageBoxContent" valign="top" width="140"><b>Address:</b> </td>
												<td class="pageBoxContent" valign="top" width="300" align="left"><?php echo $order_data_result['house_number'] .' ' . $order_data_result['street_name'].'<br>'.$order_data_result['city'].'<br>'.$order_data_result['state_name'] . '<br>'.$order_data_result['zip4']; ?></td>
											</tr>
											<tr>
											    <td class="pageBoxContent" valign="top" width="140"><b>Admin comment:</b> </td>
											    <td><?php echo $order_data_result['admin_comments']; ?></td>
											</tr>
											<tr>
											    <td class="pageBoxContent" valign="top" width="140"><b>Agent special instruction:</b> </td>
											    <td><?php echo $order_data_result['special_instructions']; ?></td>
											</tr>
											<tr>
											    <td class="pageBoxContent" valign="top" width="140"><b>Installer comment:</b> </td>
											    <td><?php echo $order_data_result['installer_comments']; ?></td>
											</tr>
											<tr>
											    <td class="pageBoxContent" valign="top" width="140"><b>Installer Comments for Agent:</b> </td>
											    <td><?php echo $order_data_result['installertoagent']; ?></td>
											</tr>
											 <tr>
                                                <td class="pageBoxContent" valign="top" width="140"><b> Cross street / Directions:</b> </td>
                                                <td><?php echo $order_data_result['cross_street_directions']; ?></td>
                                            </tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
					</tr>
					<?php
						switch($order_type_id) {
							case '1': 
								//Install.
								switch($step) {
									
									case '1':
										$button_text = 'next_step_update_job';
									?>
									<tr>
										<td class="pageBoxContent">This Signpost <input name="order_status" value="1" type="radio"<?php echo (($order_status == '1') ? ' CHECKED' : ''); ?> />&nbsp;<b>Was</b>&nbsp;/<input name="order_status" value="0" type="radio"<?php echo (($order_status == '0') ? ' CHECKED' : ''); ?> />&nbsp;<b>Was Not</b>&nbsp; Installed successfully.</td>
									</tr>
									<tr>
										<td class="pageBoxContent"><i>If post was installed, mark job as "Was" completed successfully, even if not all extra items were NOT installed. If NO signpost was installed, mark job as "Was Not" completed successfully.</i></td>
									</tr>
									<?php
									break;
									case '2':
										if ($order_status == '1') {
											$button_text = 'next_step_update_job';
											//Installed Successfully.
											?>
											<tr>
												<td class="pageBoxContent"><b>Posts</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">Requested: <?php echo $order_data_result['number_of_posts']; ?>.</td>
											</tr>
											<tr>
												<td class="pageBoxContent">You installed
                                                <?php
                                                    //display if order type install
                                                        echo '<select name="post_type_id">';
                                                        echo '<option>Please Select</option>';

                                                        $query = $database->query("select equipment_id, name as equipment_name from equipment  where equipment_type_id = '1'");
                                                        foreach($database->fetch_array($query) as $result){
                                                            $selected = '';
                                                            if(((isset($post_type_id) && !empty($post_type_id)) ? $post_type_id : DEFAULT_INSTALL_POST_TYPE) == $result['equipment_id']){
                                                                $selected = 'selected';
                                                            }
                                                            echo '<option value="' . $result['equipment_id'] . '" ' . $selected.'>'. $result['equipment_name'] . '</option>';
                                                        }
                                                        echo '</select>';

                                                    ?> Posts.

											</tr>
											<?php
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
											$answers_array = array();
											$first = true;
											$string = '';
											    foreach($database->fetch_array($query) as $result){
													$answers_array[] = $result['equipment_group_answer_id'];
														if ($first) {
															$first = false;
														} else {
															$string .= '<br>';
														}
														if (in_array($result['equipment_id'], $equipment)) {
															$checked = ' CHECKED';
														} else {
															$checked = '';
														}
													$string .= '<input type="checkbox" name="equipment[]"'.$checked.' value="' . $result['equipment_id'] . '">&nbsp;&nbsp;Installed "' . $result['equipment_name'].'"';
												}
												?>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Requested Equipment</b></td>
												</tr>
                                                    <?php
                                                    if ($order_data_result['service_level_name'] == "Silver") {
                                                    ?>
                                                <tr>
                                                    <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                                                </tr>
                                                <tr>
                                                <td class="pageBoxContent">Silver Level Agent:  No panels from the warehouse were to be installed</td>
                                                </tr>
                                                    <?php   
                                                    }
                                                    ?>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<?php
												if (!empty($string)) {
													?>
													
													<tr>
														<td class="pageBoxContent">Check off the below items to indicate you installed them.</td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><?php echo $string; ?></td>
													</tr>
													<?php
												} else {
													?>
													<tr>
														<td class="pageBoxContent">There was no 'add on' items ordered by the agent.  If you did install EXTRA items, please mark the appropriate boxes under "Extra Equipment".</td>
													</tr>
													<?php
												}
											//Add in the selection here for other installed items.
											$other_items = tep_generate_available_equipment_string('1', $order_data_result['service_level_id'], $order_data_result['user_id'], $other_items, $order_data_result['zip4'], '', false, false, $answers_array, false, false);
												?>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Comments</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">ADDRESS COMMENTS are for RSP viewing ONLY. Include install issues, updated directions, homeowner issues, etc. <br />
COMMENTS FOR AGENTS will be emailed to the agent, who pay our bills.  Use this field sparingly and with discretion.   Include comments such as "Installed where Homeowner requested" or "Had to install PVC due to space limitations" when wood was requested or "No Sign Panel at removal", etc. </td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">
														<table cellspacing="0" cellpadding="0">
															<tr>
																<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																<td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
															</tr>
															<tr>
																<td class="pageBoxContent" valign="top">ADDRESS COMMENTS: </td>
																<td><textarea name="address_comments"><?php echo stripslashes($address_comments); ?></textarea></td>
																<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
																<td class="pageBoxContent" valign="top"><i>Comments will be stored with the address.</i></td>
															</tr>
															<tr>
																<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
															</tr>
															<tr>
																<td class="pageBoxContent" valign="top">COMMENTS FOR AGENTS: </td>
																<td><textarea name="agent_comments"><?php echo stripslashes($agent_comments); ?></textarea></td>
																<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
																<td class="pageBoxContent" valign="top"><i>USE THIS FIELD SPARINGLY AND WITH DISCRETION.  THESE COMMENTS WILL BE E-MAILED TO THE AGENT.</i></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContentLarge">----------------------------------</td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContentLarge"><b>Extra Equipment</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<?php
												if (!empty($other_items)) {
													?>
													<tr>
														<td class="pageBoxContent">Mark any items below that you installed extra.</td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><?php echo $other_items; ?></td>
													</tr>
													<?php
												} else {
													?>
													<tr>
														<td class="pageBoxContent">There are no other equipment items that are available to be assigned to this order.  If you did install extra equipment then please contact the Admin for assistance.</td>
													</tr>
													<?php
												}
												?>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContentLarge">----------------------------------</td>
												</tr>
												<?php
										} else {
											//Not installed Successfully.
											$button_text = '';
											//We will do all the options on this page to make it nice. First ask them why it was not installed and then give them the relevent options.
											?>
											<tr>
												<td class="pageBoxContent"><b>Post was not installed.</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">Please specify why this post was not installed, wait for the page to reload and then insert the relevent details.  Remember this page is only to be used if neither the post nor any equipment was installed at this address.</td>
											</tr>
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">
													<table width="100%" cellspacing="0" cellpadding="0">
														<tr>
															<td width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
															<td></td>
														</tr>
														<tr>
															<td width="250" class="pageBoxContent">Post was not installed because: </td>
															<td><?php echo tep_generate_install_fail_pulldown_menu('install_fail_reason', $install_fail_reason, ' onchange="this.form.submit();"'); ?></td>
														</tr>
													</table>
												</td>
											</tr>
											<?php
												if (!empty($install_fail_reason)) {
													//Show the options.
													?>
													<tr>
														<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
													</tr>
													<?php
														if ($install_fail_reason == '1') {
															$button_text = 'next_step_update_job';
															$address_comments = 'Posts are not allowed at this address by the Home Owner\'s Association';
															$agent_comments = 'Posts are not allowed at this address by the Home Owner\'s Association';
														} elseif ($install_fail_reason == '2') {
															$button_text = 'next_step_update_job';
														} elseif ($install_fail_reason == '3') {
															?>
															<tr>
																<td class="pageBoxContent">
																	<table width="100%" cellspacing="0" cellpadding="0">
																		<tr>
																			<td width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
																			<td></td>
																		</tr>
																		<tr>
																			<td width="250" class="pageBoxContent">Could you work out the correct #?: </td>
																			<td><?php echo tep_generate_yes_no_pulldown_menu('install_fail_3_yes_no', $install_fail_3_yes_no, ' onchange="this.form.submit();"'); ?></td>
																		</tr>
																	</table>
																</td>
															</tr>
															<?php
																if ($install_fail_3_yes_no == '1') {
																	$button_text = 'next_step_update_job';
																	?>
																	<tr>
																		<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
																	</tr>
																	<tr>
																		<td class="pageBoxContent">
																			<table width="100%" cellspacing="0" cellpadding="0">
																				<tr>
																					<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																					<td></td>
																				</tr>
																				<tr>
																					<td width="150" class="pageBoxContent">House Number: </td>
																					<td><input type="text" name="install_fail_3_house_number" value="<?php echo $install_fail_3_house_number; ?>" /></td>
																				</tr>
																				<tr>
																					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																				</tr>
																				<tr>
																					<td width="150" class="pageBoxContent">Street Name: </td>
																					<td><input type="text" name="install_fail_3_street_name" value="<?php echo $install_fail_3_street_name; ?>" /></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<?php
																} elseif ($install_fail_3_yes_no == '2') {
																	$button_text = 'next_step_update_job';
																}
														} elseif ($install_fail_reason == '4') {
															?>
															<tr>
																<td class="pageBoxContent">
																	<table width="100%" cellspacing="0" cellpadding="0">
																		<tr>
																			<td width="300"><img src="images/pixel_trans.gif" height="1" width="300" /></td>
																			<td></td>
																		</tr>
																		<tr>
																			<td width="300" class="pageBoxContent">Does the homeowner want the post installed on a different date?: </td>
																			<td><?php echo tep_generate_yes_no_unsure_pulldown_menu('install_fail_4_yes_no', $install_fail_4_yes_no, ' onchange="this.form.submit();"'); ?></td>
																		</tr>
																	</table>
																</td>
															</tr>
															<?php
															if (!empty($install_fail_4_yes_no)) {
																if ($install_fail_4_yes_no == '1') {
																	//Yes, option of date or on hold.
																	$button_text = 'next_step_update_job';
																	?>
																	<tr>
																		<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
																	</tr>
																	<tr>
																		<td class="pageBoxContent"><em>Enter a new date in the box below or leave blank to mark as on hold.</em></td>
																	</tr>
																	<tr>
																		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																	</tr>
																	<tr>
																		<td class="pageBoxContent">
																			<table width="100%" cellspacing="0" cellpadding="0">
																				<tr>
																					<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																					<td></td>
																				</tr>
																				<tr>
																					<td width="150" class="pageBoxContent">New Date: </td>
                                                                                    <td width="450" class="pageBoxContent"><input type="text" class="new_date" name="install_fail_4_new_date" value="<?php echo $install_fail_4_new_date; ?>"></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<?php
																} elseif ($install_fail_4_yes_no == '2') {
																	//No.  Thats it.
																	$button_text = 'next_step_update_job';
																} elseif ($install_fail_4_yes_no == '3') {
																	//Unsure.  Thats it.
																	$button_text = 'next_step_update_job';
																}															}
														} elseif ($install_fail_reason == '5') {
															//This would be our Unable to find address.
															$button_text = 'next_step_update_job';
														} elseif ($install_fail_reason == '6') {
															//This would be our post already installed.
															$button_text = 'next_step_update_job';
														}
														if (!empty($button_text)) {
														?>
														<tr>
															<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
														</tr>
														<tr>
															<td class="pageBoxContent"><b>Comments</b></td>
														</tr>
														<tr>
															<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
														</tr>
														<tr>
															<td class="pageBoxContent">ADDRESS COMMENTS are for RSP viewing ONLY. Include install issues, updated directions, homeowner issues, etc. <br />
COMMENTS FOR AGENTS will be emailed to the agent, who pay our bills.  Use this field sparingly and with discretion.   Include comments such as "Installed where Homeowner requested" or "Had to install PVC due to space limitations" when wood was requested or "No Sign Panel at removal", etc. </td>
														</tr>
														<tr>
															<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
														</tr>
														<tr>
															<td class="pageBoxContent">
																<table cellspacing="0" cellpadding="0">
																	<tr>
																		<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																		<td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
																	</tr>
																	<tr>
																		<td class="pageBoxContent" valign="top">ADDRESS COMMENTS: </td>
																		<td><textarea name="address_comments"><?php echo stripslashes($address_comments); ?></textarea></td>
																<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
																<td class="pageBoxContent" valign="top"><i>Comments will be stored with the address.</i></td>
																	</tr>
																	<tr>
																		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																	</tr>
																	<tr>
																		<td class="pageBoxContent" valign="top">COMMENTS FOR AGENTS: </td>
																		<td><textarea name="agent_comments"><?php echo stripslashes($agent_comments); ?></textarea></td>
																		<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
																<td class="pageBoxContent" valign="top"><i>USE THIS FIELD SPARINGLY AND WITH DISCRETION.  THESE COMMENTS WILL BE E-MAILED TO THE AGENT.</i></td>
																	</tr>
																</table>
															</td>
														</tr>
														<?php
														}
												}
											?>
											<?php
										}
										?>
										
										<?php
									break;
									case '3':
										if ($order_status == '1') {
											//Success.  Create the confirmation page.
											$button_text = 'next_step_mark_complete';
											?>
											<tr>
												<td class="pageBoxContent">Please review the details below and confirm they are correct. To make any changes press the back button.</td>
											</tr>
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Job Status</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">This Signpost was <strong>Installed Successfully</strong>.</td>
											</tr>
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Posts</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;<strong><?php echo $order_data_result['number_of_posts']; ?></strong> "<strong><?php echo tep_get_equipment_name($post_type_id); ?></strong>" post<?php echo (($order_data_result['number_of_posts'] > 1) ? 's where' : ' was'); ?> installed.</td>
											</tr>
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Requested Equipment</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<?php
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												$answers_array = array();
												$first = true;
												$string = '';
												    foreach($database->fetch_array($query) as $result){
														$answers_array[] = $result['equipment_group_answer_id'];
															if ($first) {
																$first = false;
															} else {
																$string .= '<br>';
															}
															if (in_array($result['equipment_id'], $equipment)) {
																$string .= '&nbsp;&nbsp;"' . $result['equipment_name'] . '" was Installed.';
															} else {
																$string .= '&nbsp;&nbsp;<span style="color:#FF0000;">"' . $result['equipment_name'] . '" was not Installed.</span>';
															}
														
													}
													if (!empty($string)) {
														?>
														<tr>
															<td class="pageBoxContent"><?php echo $string; ?></td>
														</tr>
														<?php
													} else {
														?>
														<tr>
															<td class="pageBoxContent">There was no equipment requested for this order.</td>
														</tr>
														<?php
													}
												//Do the other the manual way.
												?>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Extra Equipment</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<?php
													$extra_equipment_string = tep_create_installer_confirmation_equipment_string($other_items);
														if (!empty($extra_equipment_string)) {
															?>
															<tr>
																<td class="pageBoxContent"><?php echo $extra_equipment_string; ?></td>
															</tr>

															<?php
														} else {
															?>
															<tr>
																<td class="pageBoxContent">No extra equipment was used in this order.</td>
															</tr>
															<?php
														}
											?>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Comments</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">Please review the comments you entered below.  Address comments will be stored with the address for next time whereas Agent comments will be sent to the agent along with confirmation of this order.  If any requested equipment could not be installed or anything happened with this install make sure it is recorded here.</td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">
														<table cellspacing="0" cellpadding="0">
															<tr>
																<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																<td width="100"><img src="images/pixel_trans.gif" height="1" width="200" /></td>
															</tr>
															<tr>
																<td class="pageBoxContent" valign="top">Address Comments: </td>
																<td width="200"><?php echo nl2br(stripslashes($address_comments)); ?></td>
															</tr>
															<tr>
																<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
															</tr>
															<tr>
																<td class="pageBoxContent" valign="top">Agent Comments: </td>
																<td width="200"><?php echo nl2br(stripslashes($agent_comments)); ?></td>
															</tr>
														</table>
													</td>
												</tr>
											<?php
										} else {
											//Failure.  Create the confirmation page.
											$button_text = 'next_step_mark_complete';
											//This is where it gets fun as we have a few different options.  Luckily nothing is as messy  its more just a case of runnign through it.
											?>
											<tr>
												<td class="pageBoxContent">Please review the details below and confirm they are correct. To make any changes press the back button.</td>
											</tr>
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Job Status</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><span style="color:#FF0000;">This Signpost was <strong>Not Installed Successfully</strong>.</span></td>
											</tr>
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<?php
												if ($install_fail_reason == '1') {
													//Post Not Allowed
													?>
													<tr>
														<td class="pageBoxContent">Posts were not allowed at that address.</td>
													</tr>
													<?php
												} elseif ($install_fail_reason == '2') {
													//No Room to Install
													?>
													<tr>
														<td class="pageBoxContent">There was no room to install the post.</td>
													</tr>
													<?php
												} elseif ($install_fail_reason == '3') {
													//Wrong House #
													?>
													<tr>
														<td class="pageBoxContent">The house number was wrong.</td>
													</tr>
													<tr>
														<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
													</tr>
													<?php
														if ($install_fail_3_yes_no == '1') {
														?>
														<tr>
															<td class="pageBoxContent">I was able to work out the correct address and it is entered below:</td>
														</tr>
														<tr>
															<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
														</tr>
														<tr>
															<td class="pageBoxContent">
																<table width="100%" cellspacing="0" cellpadding="0">
																	<tr>
																		<td width="20"><img src="images/pixel_trans.gif" height="1" width="20" /></td>
																		<td class="pageBoxContent"><?php echo $install_fail_3_house_number . '<br>' . nl2br($install_fail_3_street_name); ?></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td height="20"><img src="images/pixel_trans.gif" height="20" width="1"></td>
														</tr>
														<tr>
															<td height="5" class="pageBoxContent"><b>Important Note: Once you confirm this address change the address will be updated and you will be asked to complete the equipment information for this install based on the new address. You will need to do that and confirm it again to set it as complete.</b></td>
														</tr>
														<?php
														} else {
														?>
														<tr>
															<td class="pageBoxContent">I was not able to work out the correct address.</td>
														</tr>
														<?php
														}
													?>
													<?php
												} elseif ($install_fail_reason == '4') {
													//Stopped by Homeowner/Tenant
													?>
													<tr>
														<td class="pageBoxContent">The Homeowner/Tenant prevented the install taking place.</td>
													</tr>
													<tr>
														<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
													</tr>
													
													<?php
														if ($install_fail_4_yes_no == '1') {
															?>
															<tr>
																<td class="pageBoxContent">The Homeowner/Tenant <strong>does require</strong> the install at a later date.</td>
															</tr>
															<tr>
																<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
															</tr>
															<?php
																if (empty($install_fail_4_new_date)) {
																?>
																<tr>
																	<td class="pageBoxContent">I was not able to get a new installation date.</td>
																</tr>
																<?php
																} else {
																?>
																<tr>
																	<td class="pageBoxContent">It has been requested that the install now take place on <strong><?php echo stripslashes($install_fail_4_new_date); ?>.</strong></td>
																</tr>
																<?php
																}
															?>
															<?php
														} elseif ($install_fail_4_yes_no == '2') {
															?>
															<tr>
																<td class="pageBoxContent">The Homeowner/Tenant <strong>does not require</strong> the install at a later date.</td>
															</tr>
															<?php
														} elseif ($install_fail_4_yes_no == '3') {
															?>
															<tr>
																<td class="pageBoxContent">I am <strong>not sure </strong>if this install is requried at a later date.</td>
															</tr>
															<?php
														}
												} elseif ($install_fail_reason == '5') {
													//Unable to Find Address
													?>
													<tr>
														<td class="pageBoxContent">The address was unable to be found.</td>
													</tr>
													<?php
												} elseif ($install_fail_reason == '6') {
													//Post Already Installed
													?>
													<tr>
														<td class="pageBoxContent">A post was already installed at the address.</td>
													</tr>
													<?php
												}
											?>
											<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Comments</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">Please review the comments you entered below.  Address comments will be stored with the address for next time whereas Agent comments will be sent to the agent along with confirmation of this order.  If any requested equipment could not be installed or anything happened with this install make sure it is recorded here.</td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">
														<table cellspacing="0" cellpadding="0">
															<tr>
																<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																<td width="100"><img src="images/pixel_trans.gif" height="1" width="200" /></td>
															</tr>
															<tr>
																<td class="pageBoxContent" valign="top">Address Comments: </td>
																<td width="200"><?php echo nl2br(stripslashes($address_comments)); ?></td>
															</tr>
															<tr>
																<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
															</tr>
															<tr>
																<td class="pageBoxContent" valign="top">Agent Comments: </td>
																<td width="200"><?php echo nl2br(stripslashes($agent_comments)); ?></td>
															</tr>
														</table>
													</td>
												</tr>
											<?php
											
										}
									break;
								}
							break;
							case '2': 
								//Service Call.
								switch($step) {
									case '1':
										$button_text = 'next_step_update_job';
									?>
									<tr>
										<td class="pageBoxContent">This job <input name="order_status" value="1" type="radio"<?php echo (($order_status == '1') ? ' CHECKED' : ''); ?> />&nbsp;<b>Was</b>&nbsp;/<input name="order_status" value="0" type="radio"<?php echo (($order_status == '0') ? ' CHECKED' : ''); ?> />&nbsp;<b>Was Not</b>&nbsp; completed successfully.</td>
									</tr>
									<?php
									break;
									case '2':
										$query = $database->query("select service_call_reason_id, service_call_detail_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $oID . "' limit 1");
										$result = $database->fetch_array($query);
										
										if ($order_status == '1') {
											//Success.
											?>
											<tr>
												<td class="pageBoxContent"><b>Service call was completed successfully.</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">Please add any required comments and mark all the associated details for this service call.</td>
											</tr>
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<?php
											if ($result['service_call_reason_id'] == '1') {
												//Exchange Rider:
												$button_text = 'next_step_update_job';
												?>
											<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>		
											<tr>
												<td class="pageBoxContent">Exchange Rider.</td>
											</tr>										
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Requested Equipment</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<?php
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "' order by method_id");
												$answers_array = array();
												$first = true;
												$string = '';
												    foreach($database->fetch_array($query) as $result){
													
															if ($first) {
																$first = false;
															} else {
																$string .= '<br>';
															}
															if (in_array($result['equipment_id'], $equipment)) {
																$checked = ' CHECKED';
															} else {
																$checked = '';
															}
														$string .= '<input type="checkbox" name="equipment[]"'.$checked.' value="' . $result['equipment_id'] . '">&nbsp;&nbsp;'.(($result['method_id'] == '1') ? 'Installed' : 'Removed').' "' . $result['equipment_name'].'"';
													}
														?>
														<tr>
															<td class="pageBoxContent"><?php echo $string; ?></td>
														</tr>
														<?php
											} elseif ($result['service_call_reason_id'] == '2') {
												//Install New Rider or BBox:
												$button_text = 'next_step_update_job';
												?>
												
												<tr>
													<td class="pageBoxContent"><b>Requested Equipment</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">It was requested that the following equipment be installed.</td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
											<?php
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
											$answers_array = array();
											$first = true;
											$string = '';
											    foreach($database->fetch_array($query) as $result){
													$answers_array[] = $result['equipment_group_answer_id'];
														if ($first) {
															$first = false;
														} else {
															$string .= '<br>';
														}
														if (in_array($result['equipment_id'], $equipment)) {
															$checked = ' CHECKED';
														} else {
															$checked = '';
														}
													$string .= '<input type="checkbox" name="equipment[]"'.$checked.' value="' . $result['equipment_id'] . '">&nbsp;&nbsp;Installed "' . $result['equipment_name'].'"';
												}
												?>
												<tr>
													<td class="pageBoxContent"><?php echo $string; ?></td>
												</tr>
												<?php
											} elseif ($result['service_call_reason_id'] == '3') {
												//Replace/Exchange Agent SignPanel:
												$button_text = 'next_step_update_job';
												?>
												
												<tr>
													<td class="pageBoxContent"><b>Requested Equipment</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">It was requested that the following equipment be installed.</td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
											<?php
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "' order by equipment_name");
											$answers_array = array();
											$first = true;
											$string = '';
											    foreach($database->fetch_array($query) as $result){
													$answers_array[] = $result['equipment_group_answer_id'];
														if ($first) {
															$first = false;
														} else {
															$string .= '<br>';
														}
														if (in_array($result['equipment_id'], $equipment)) {
															$checked = ' CHECKED';
														} else {
															$checked = '';
														}
													$string .= '<input type="checkbox" name="equipment[]"'.$checked.' value="' . $result['equipment_id'] . '">&nbsp;&nbsp;Installed "' . $result['equipment_name'].'"';
												}
												?>
												<tr>
													<td class="pageBoxContent"><?php echo $string; ?></td>
												</tr>
												
											<?php
											//Work out if any equipment needed to be removed and show that.
											
											$query = $database->query("select e.equipment_id, eita.equipment_item_id, e.name as equipment_name from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.equipment_id = e.equipment_id and eita.address_id = '" . $order_data_result['address_id'] . "' and eita.equipment_status_id = '2' and e.equipment_type_id = '4' order by equipment_name");
											$answers_array = array();
											$first = true;
											$string = '';
											    foreach($database->fetch_array($query) as $result){
														if ($first) {
															$first = false;
														} else {
															$string .= '<br>';
														}
														if (in_array($result['equipment_id'], $remove_equipment)) {
															$checked = ' CHECKED';
														} else {
															$checked = '';
														}
													$string .= '<input type="checkbox" name="remove_equipment[]"'.$checked.' value="' . $result['equipment_id'] . '">&nbsp;&nbsp;Installed "' . $result['equipment_name'].'"';
												}
												if (!empty($string)) {
												?>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Removal Equipment</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">Please select the signpanel(s) you removed.</td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><?php echo $string; ?></td>
												</tr>
												<?php
												}
											} elseif ($result['service_call_reason_id'] == '4') {
												//Post Leaning/Straighten Post:
												$button_text = 'next_step_update_job';
												$reason = '';
													if ($result['service_call_detail_id'] == '1') {
														$reason = 'Weather';
													} elseif ($result['service_call_detail_id'] == '2') {
														$reason = 'Improper Installation';
													} elseif ($result['service_call_detail_id'] == '3') {
														$reason = 'Someone moved Post';
													} elseif ($result['service_call_detail_id'] == '4') {
														$reason = 'Other';
													}
												?>
												
												<tr>
													<td class="pageBoxContent"><b>Reason and Details</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;It was stated that the post was leaning due to "<?php echo $reason; ?>".</td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Result</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><input type="radio" name="sc_success_3" value="1"<?php echo (($sc_success_3 == '1') ? ' CHECKED' : ''); ?> /> The post was found leaning and was fixed.</td>
												</tr>
												<tr>
													<td class="pageBoxContent"><input type="radio" name="sc_success_3" value="2"<?php echo (($sc_success_3 == '2') ? ' CHECKED' : ''); ?> /> The post was not found leaning.</td>
												</tr>
											<?php
											} elseif ($result['service_call_reason_id'] == '5') {
												//Move Post:
												$button_text = 'next_step_update_job';
												?>
												
												<tr>
													<td class="pageBoxContent"><b>Reason and Details</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Move post.</td>
												</tr>
												
												
											<?php

											} elseif ($result['service_call_reason_id'] == '6') {
												//Install equipment forgotten at install:
												$button_text = 'next_step_update_job';
												?>
												<tr>
													<td class="pageBoxContent"><b>Requested Equipment</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">The following equipment was forgotten at time of install.</td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
											<?php
											$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
											$answers_array = array();
											$first = true;
											$string = '';
											    foreach($database->fetch_array($query) as $result){
													$answers_array[] = $result['equipment_group_answer_id'];
														if ($first) {
															$first = false;
														} else {
															$string .= '<br>';
														}
														if (in_array($result['equipment_id'], $equipment)) {
															$checked = ' CHECKED';
														} else {
															$checked = '';
														}
													$string .= '<input type="checkbox" name="equipment[]"'.$checked.' value="' . $result['equipment_id'] . '">&nbsp;&nbsp;Installed "' . $result['equipment_name'].'"';
												}
												?>
												<tr>
													<td class="pageBoxContent"><?php echo $string; ?></td>
												</tr>
												<?php
											} elseif ($result['service_call_reason_id'] == '7') {
												//Other:
												//Move Post:
												$button_text = 'next_step_update_job';
												?>
												
												<tr>
													<td class="pageBoxContent"><b>Reason and Details</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Other:</td>
												</tr>
												<tr>
													<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order_data_result['special_instructions']; ?></td>
												</tr>
												
											<?php
											}
											
											$extra_equipment_string = tep_generate_available_equipment_string('1', $order_data_result['service_level_id'], $order_data_result['user_id'], $other_items, $order_data_result['zip4'], '', false, false, array(), false, false);
														?>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContentLarge">----------------------------------</td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContentLarge"><b>Extra Equipment</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<?php
												if (!empty($extra_equipment_string)) {
													?>
													<tr>
														<td class="pageBoxContent">Mark any items below that you installed extra.</td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><?php echo $extra_equipment_string; ?></td>
													</tr>
													<?php
												} else {
													?>
													<tr>
														<td class="pageBoxContent">There are no other equipment items that are available to be assigned to this order.  If you did install extra equipment then please contact the Admin for assistance.</td>
													</tr>
													<?php
												}
												?>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContentLarge">----------------------------------</td>
												</tr>
											<?php
										} else {
											?>
											<tr>
												<td class="pageBoxContent"><b>Service call was not completed successfully.</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">Please specify why this service call was not completed and add any relevent comments.</td>
											</tr>
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<?php
											if ($result['service_call_reason_id'] == '5') {
												//Move Post:
												$button_text = 'next_step_update_job';
												?>
												<tr>
													<td class="pageBoxContent"><b>Reason and Details</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><input type="radio" name="sc_fail_5" value="1"<?php echo (($sc_fail_5 == '1') ? ' CHECKED' : ''); ?> /> No Marker/Bad location directions.</td>
												</tr>
												<tr>
													<td class="pageBoxContent"><input type="radio" name="sc_fail_5" value="2"<?php echo (($sc_fail_5 == '2') ? ' CHECKED' : ''); ?> /> Unable to Install where agent requests.</td>
												</tr>
												<tr>
													<td class="pageBoxContent"><input type="radio" name="sc_fail_5" value="3"<?php echo (($sc_fail_5 == '3') ? ' CHECKED' : ''); ?> /> Original Post Missing.</td>
												</tr>
												<?php
											} else {
												//All the others are the same.
												
												$button_text = 'next_step_update_job';
												?>
												
												<tr>
													<td class="pageBoxContent"><b>Reason</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><input type="radio" name="sc_fail_5" value="1"<?php echo (($sc_fail_5 == '1') ? ' CHECKED' : ''); ?> />&nbsp;&nbsp;&nbsp;Post was missing.</td>
												</tr>
												<tr>
													<td class="pageBoxContent"><input type="radio" name="sc_fail_5" value="2"<?php echo (($sc_fail_5 == '2') ? ' CHECKED' : ''); ?> />&nbsp;&nbsp;&nbsp;Other (please specify below).</td>
												</tr>
											<?php
											}
										}
										?>
										<tr>
											<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
										</tr>
										<tr>
											<td class="pageBoxContent"><b>Comments</b></td>
										</tr>
										<tr>
											<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
										</tr>
										<tr>
											<td class="pageBoxContent">ADDRESS COMMENTS are for RSP viewing ONLY. Include install issues, updated directions, homeowner issues, etc. <br />
COMMENTS FOR AGENTS will be emailed to the agent, who pay our bills.  Use this field sparingly and with discretion.   Include comments such as "Installed where Homeowner requested" or "Had to install PVC due to space limitations" when wood was requested or "No Sign Panel at removal", etc. </td>
										</tr>
										<tr>
											<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
										</tr>
										<tr>
											<td class="pageBoxContent">
												<table cellspacing="0" cellpadding="0">
													<tr>
														<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
														<td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent" valign="top">ADDRESS COMMENTS: </td>
														<td><textarea name="address_comments"><?php echo stripslashes($address_comments); ?></textarea></td>
														<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
														<td class="pageBoxContent" valign="top"><i>Comments will be stored with the address.</i></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent" valign="top">COMMENTS FOR AGENTS: </td>
														<td><textarea name="agent_comments"><?php echo stripslashes($agent_comments); ?></textarea></td>
														<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
														<td class="pageBoxContent" valign="top"><i>USE THIS FIELD SPARINGLY AND WITH DISCRETION.  THESE COMMENTS WILL BE E-MAILED TO THE AGENT.</i></td>
													</tr>
												</table>
											</td>
										</tr>
										<?php
									break;
									case '3':
										$button_text = 'next_step_mark_complete';
										
										$query = $database->query("select service_call_reason_id, service_call_detail_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $oID . "' limit 1");
										$result = $database->fetch_array($query);
											if ($order_status == '1') {
												?>
												
												<tr>
													<td class="pageBoxContent">Please review the details below and confirm they are correct. To make any changes press the back button.</td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Job Status</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">This job was <strong>Completed Sucessfully</strong>.</td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<?php
												if ($result['service_call_reason_id'] == '1') {
													//Exchange Rider:
													?>
											<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>		
											<tr>
												<td class="pageBoxContent">Exchange Rider.</td>
											</tr>										
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Requested Equipment</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<?php
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "' order by method_id");
												$answers_array = array();
												$first = true;
												$string = '';
												    foreach($database->fetch_array($query) as $result){
															if (!$first) {
																$string .= '<br>';
															}
														$first = false;
															if (in_array($result['equipment_id'], $equipment)) {
																$string .= '&nbsp;&nbsp;"' . $result['equipment_name'] . '" was '.(($result['method_id'] == '1') ? 'Installed' : 'Removed') . '.';
															} else {
																$string .= '&nbsp;&nbsp;<span style="color:#FF0000;">"' . $result['equipment_name'] . '" was not '.(($result['method_id'] == '1') ? 'Installed' : 'Removed') . '.</span>';
															}
													}
														?>
														<tr>
															<td class="pageBoxContent"><?php echo $string; ?></td>
														</tr>
														<?php
													
												} elseif ($result['service_call_reason_id'] == '2') {
													//Install New Rider or BBox:
													?>
											<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>		
											<tr>
												<td class="pageBoxContent">Install New Rider or BBox.</td>
											</tr>										
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Requested Equipment</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<?php
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												$answers_array = array();
												$first = true;
												$string = '';
												    foreach($database->fetch_array($query) as $result){
														
															if ($first) {
																$first = false;
															} else {
																$string .= '<br>';
															}
															if (in_array($result['equipment_id'], $equipment)) {
																$string .= '&nbsp;&nbsp;"' . $result['equipment_name'] . '" was Installed.';
															} else {
																$string .= '&nbsp;&nbsp;<span style="color:#FF0000;">"' . $result['equipment_name'] . '" was not Installed.</span>';
															}
														
													}
													if (!empty($string)) {
														?>
														<tr>
															<td class="pageBoxContent"><?php echo $string; ?></td>
														</tr>
														<?php
													} else {
														?>
														<tr>
															<td class="pageBoxContent">There was no equipment requested for this order.</td>
														</tr>
														<?php
													}
											
												} elseif ($result['service_call_reason_id'] == '3') {
													//Replace/Exchange Agent SignPanel:
												?>
													<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>		
											<tr>
												<td class="pageBoxContent">Replace/Exchange Agent SignPanel.</td>
											</tr>										
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Requested Equipment</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<?php
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												$answers_array = array();
												$first = true;
												$string = '';
												    foreach($database->fetch_array($query) as $result){
														
															if ($first) {
																$first = false;
															} else {
																$string .= '<br>';
															}
															if (in_array($result['equipment_id'], $equipment)) {
																$string .= '&nbsp;&nbsp;"' . $result['equipment_name'] . '" was Installed.';
															} else {
																$string .= '&nbsp;&nbsp;<span style="color:#FF0000;">"' . $result['equipment_name'] . '" was not Installed.</span>';
															}
														
													}
													if (!empty($string)) {
														?>
														<tr>
															<td class="pageBoxContent"><?php echo $string; ?></td>
														</tr>
														<?php
													} else {
														?>
														<tr>
															<td class="pageBoxContent">There was no equipment requested for this order.</td>
														</tr>
														<?php
													}
												
											//Work out if any equipment needed to be removed and show that.
											
												$query = $database->query("select eita.equipment_item_id, e.name as equipment_name from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.equipment_id = e.equipment_id and eita.address_id = '" . $order_data_result['address_id'] . "' and eita.equipment_status_id = '2' and e.equipment_type_id = '4'");
												$answers_array = array();
												$first = true;
												$string = '';
												    foreach($database->fetch_array($query) as $result){
															if ($first) {
																$first = false;
															} else {
																$string .= '<br>';
															}
															if (in_array($result['equipment_id'], $remove_equipment)) {
																$string .= '&nbsp;&nbsp;"' . $result['equipment_name'] . '" was Removed.';
															}
													}
													if (!empty($string)) {
													?>
													<tr>
														<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><b>Removal Equipment</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">Please select the signpanel(s) you removed.</td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><?php echo $string; ?></td>
													</tr>
													<?php
													}
												} elseif ($result['service_call_reason_id'] == '4') {
													//Post Leaning/Straighten Post:
													$reason = '';
														if ($result['service_call_detail_id'] == '1') {
															$reason = 'Weather';
														} elseif ($result['service_call_detail_id'] == '2') {
															$reason = 'Improper Installation';
														} elseif ($result['service_call_detail_id'] == '3') {
															$reason = 'Someone moved Post';
														} elseif ($result['service_call_detail_id'] == '4') {
															$reason = 'Other';
														}
													?>
													<tr>
														<td class="pageBoxContent"><b>Reason and Details</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>		
													<tr>
														<td class="pageBoxContent">Post Leaning/Straighten Post.</td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>	
													<tr>
														<td class="pageBoxContent">Leaning due to "<?php echo $reason; ?>".</td>
													</tr>										
													<tr>
														<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><b>Result</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><?php echo (($sc_success_3 == '1') ? 'The post was found leaning and was fixed.' : 'The post was not found leaning.'); ?></td>
													</tr>		
													<?php
												} elseif ($result['service_call_reason_id'] == '5') {
													//Move Post:
													?>
													<tr>
														<td class="pageBoxContent"><b>Reason and Details</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>		
													<tr>
														<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Move Post.</td>
													</tr>
													
													<?php
												} elseif ($result['service_call_reason_id'] == '6') {
													//Install equipment forgotten at install:
													?>
													<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>		
											<tr>
												<td class="pageBoxContent">Install Equipment Forgotten at Install.</td>
											</tr>										
											<tr>
												<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Requested Equipment</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<?php
												$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
												$answers_array = array();
												$first = true;
												$string = '';
												    foreach($database->fetch_array($query) as $result){
														
															if ($first) {
																$first = false;
															} else {
																$string .= '<br>';
															}
															if (in_array($result['equipment_id'], $equipment)) {
																$string .= '&nbsp;&nbsp;"' . $result['equipment_name'] . '" was Installed.';
															} else {
																$string .= '&nbsp;&nbsp;<span style="color:#FF0000;">"' . $result['equipment_name'] . '" was not Installed.</span>';
															}
														
													}
													if (!empty($string)) {
														?>
														<tr>
															<td class="pageBoxContent"><?php echo $string; ?></td>
														</tr>
														<?php
													} else {
														?>
														<tr>
															<td class="pageBoxContent">There was no equipment requested for this order.</td>
														</tr>
														<?php
													}
												} elseif ($result['service_call_reason_id'] == '7') {
													//Other:
													?>
													<tr>
														<td class="pageBoxContent"><b>Reason and Details</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>		
													<tr>
														<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Other:</td>
													</tr>
													<tr>
														<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order_data_result['special_instructions']; ?></td>
													</tr>
													<?php
												}
												?>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Extra Equipment</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<?php
													$extra_equipment_string = tep_create_installer_confirmation_equipment_string($other_items);
														if (!empty($extra_equipment_string)) {
															?>
															<tr>
																<td class="pageBoxContent"><?php echo $extra_equipment_string; ?></td>
															</tr>

															<?php
														} else {
															?>
															<tr>
																<td class="pageBoxContent">No extra equipment was used in this order.</td>
															</tr>
															<?php
														}
											?>
												<?php
											} else {
												//Failure.
												
												?>
												<tr>
													<td class="pageBoxContent">Please review the details below and confirm they are correct. To make any changes press the back button.</td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Job Status</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">This job was <strong>Not Completed Sucessfully</strong>.</td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<?php
												
												if ($result['service_call_reason_id'] == '1') {
													//Exchange Rider:
													$reason = '';
														if ($sc_fail_5 == '1') {
															$reason = 'Original Post Missing';
														} elseif ($sc_fail_5 == '2') {
															$reason = 'Other';
														}
														?>
											<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>		
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Exchange Rider.</td>
											</tr>										
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;<strong><?php echo $reason; ?>.</strong></td>
											</tr>
											
											
													<?php
												} elseif ($result['service_call_reason_id'] == '2') {
													//Install New Rider or BBox:
													$reason = '';
														if ($sc_fail_5 == '1') {
															$reason = 'Original Post Missing';
														} elseif ($sc_fail_5 == '2') {
															$reason = 'Other';
														}
													?>
											<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>		
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Install New Rider or BBox.</td>
											</tr>										
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;<strong><?php echo $reason; ?>.</strong></td>
											</tr>
											
											
													<?php
													
												} elseif ($result['service_call_reason_id'] == '3') {
													//Replace/Exchange Agent SignPanel:
													$reason = '';
														if ($sc_fail_5 == '1') {
															$reason = 'Original Post Missing';
														} elseif ($sc_fail_5 == '2') {
															$reason = 'Other';
														}
													?>
													<tr>
														<td class="pageBoxContent"><b>Reason and Details</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>		
													<tr>
														<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Replace/Exchange Agent SignPanel.</td>
													</tr>										
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;<strong><?php echo $reason; ?>.</strong></td>
													</tr>
													<?php
													
												} elseif ($result['service_call_reason_id'] == '4') {
													//Post Leaning/Straighten Post:
													$reason = '';
														if ($sc_fail_5 == '1') {
															$reason = 'Original Post Missing';
														} elseif ($sc_fail_5 == '2') {
															$reason = 'Other';
														}
													?>
													<tr>
														<td class="pageBoxContent"><b>Reason and Details</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>		
													<tr>
														<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Post Leaning/Straighten Post.</td>
													</tr>										
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;<strong><?php echo $reason; ?>.</strong></td>
													</tr>
													<?php
												} elseif ($result['service_call_reason_id'] == '5') {
													//Move Post:
													$reason = '';
														if ($sc_fail_5 == '1') {
															$reason ='No Marker/Bad location directions';
														} elseif ($sc_fail_5 == '2') {
															$reason = 'Unable to Install where agent requests';
														} elseif ($sc_fail_5 == '3') {
															$reason = 'Original Post Missing';
														}
													?>
													<tr>
														<td class="pageBoxContent"><b>Reason and Details</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>		
													<tr>
														<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Move Post.</td>
													</tr>										
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;<strong><?php echo $reason; ?>.</strong></td>
													</tr>
													<?php
												} elseif ($result['service_call_reason_id'] == '6') {
													//Install equipment forgotten at install:
													$reason = '';
														if ($sc_fail_5 == '1') {
															$reason = 'Original Post Missing';
														} elseif ($sc_fail_5 == '2') {
															$reason = 'Other';
														}
													?>
											<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>		
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Install Equipment Forgotten at Install </td>
											</tr>	
							
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;<strong><?php echo $reason; ?>.</strong></td>
											</tr>
											
											
													<?php
												} elseif ($result['service_call_reason_id'] == '7') {
													//Other:
													$reason = '';
														if ($sc_fail_5 == '1') {
															$reason = 'Original Post Missing';
														} elseif ($sc_fail_5 == '2') {
															$reason = 'Other';
														}
													?>
											<tr>
												<td class="pageBoxContent"><b>Reason and Details</b></td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>		
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;Other: </td>
											</tr>	
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order_data_result['special_instructions']; ?></td>
											</tr>									
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;<strong><?php echo $reason; ?>.</strong></td>
											</tr>
											
											
													<?php
												}
											}
										?>
										<tr>
														<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><b>Comments</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">Please review the comments you entered below.  Address comments will be stored with the address for next time whereas Agent comments will be sent to the agent along with confirmation of this order.  If any requested equipment could not be installed or anything happened with this install make sure it is recorded here.</td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">
															<table cellspacing="0" cellpadding="0">
																<tr>
																	<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																	<td width="100"><img src="images/pixel_trans.gif" height="1" width="200" /></td>
																</tr>
																<tr>
																	<td class="pageBoxContent" valign="top">Address Comments: </td>
																	<td width="200"><?php echo nl2br(stripslashes($address_comments)); ?></td>
																</tr>
																<tr>
																	<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																</tr>
																<tr>
																	<td class="pageBoxContent" valign="top">Agent Comments: </td>
																	<td width="200"><?php echo nl2br(stripslashes($agent_comments)); ?></td>
																</tr>
															</table>
														</td>
													</tr>
										<?php	
									break;
								}
							break;
							case '3': 
								//Removal.
								
								switch($step) {
									case '1':
										$button_text = 'next_step_update_job';
										?>
										<tr>
											<td class="pageBoxContent">This Signpost <input name="order_status" value="1" type="radio"<?php echo (($order_status == '1') ? ' CHECKED' : ''); ?> />&nbsp;<b>Was</b>&nbsp;/<input name="order_status" value="0" type="radio"<?php echo (($order_status == '0') ? ' CHECKED' : ''); ?> />&nbsp;<b>Was Not</b>&nbsp; Recovered Successfully.</td>
										</tr>
										<tr>
											<td class="pageBoxContent"><i>For Missing Items:  Mark job as "Was" completed successfully and note missing items on next page. IF homeowner stopped removal of post, mark job as "Was Not" completed successfully.  You will be asked for a new removal date.</i></td>
										</tr>
										<?php
									break;
									case '2':
											if ($order_status == '1') {
												//Success, check the equipment.
												$button_text = 'next_step_update_job';
												?>
												<tr>
													<td class="pageBoxContent">Please look over the following equipment and mark what was removed and what was not.</td>
												</tr>
												<?php
													$query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_id ASC");
														foreach($database->fetch_array($query) as $result){
															//Work out what equipment of this type should be at the address and only show if there were some.
															$count_query = $database->query("select count(eita.equipment_item_to_address_id) as count from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita where e.equipment_type_id = '" . $result['equipment_type_id'] . "' and e.equipment_id = eita.equipment_id and eita.address_id = '" . $order_data_result['address_id'] . "'");
															$count_result = $database->fetch_array($count_query);
															
																if ($count_result['count'] > 0) {
																	?>
																	<tr>
																		<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
																	</tr>
																	<tr>
																		<td class="pageBoxContent"><b><?php echo $result['equipment_type_name']; ?></b></td>
																	</tr>
																	<tr>
																		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																	</tr>
																	<?php
																	//Now list the equipment.
																		$equipment_query = $database->query("select ei.equipment_item_id, e.name, e.tracking_method_id, ei.code from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei where eita.address_id = '" . $order_data_result['address_id'] . "' and eita.equipment_item_id = ei.equipment_item_id and ei.equipment_id = e.equipment_id and e.equipment_type_id = '" . $result['equipment_type_id'] . "' and eita.equipment_status_id = '2' order by e.name");
																		$loop = 0;
																		    foreach($database->fetch_array($equipment_query) as $equipment_result){
																					if ($loop > 0) {
																						?>
																						<tr>
																							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																						</tr>
																						<?php
																					}
																				$success_checked = '';
																				$damage_checked = '';
																				$missing_checked = '';
																					if (!isset($equipment[$equipment_result['equipment_item_id']])) {
																						$success_checked = ' CHECKED';
																					} else {
																						if ($equipment[$equipment_result['equipment_item_id']] == '1') {
																							$success_checked = ' CHECKED';
																						} elseif ($equipment[$equipment_result['equipment_item_id']] == '2') {
																							$damage_checked = ' CHECKED';
																						} elseif ($equipment[$equipment_result['equipment_item_id']] == '3') {
																							$missing_checked = ' CHECKED';
																						}
																					}
																				?>
																				<tr>
																					<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $equipment_result['name'] . (($equipment_result['tracking_method_id'] == '1') ? '&nbsp;('.$equipment_result['code'].')' : ''); ?></td>
																				</tr>
																				<tr>
																					<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="equipment[<?php echo $equipment_result['equipment_item_id']; ?>]" value="1"<?php echo $success_checked; ?> /> was <strong>removed successfully</strong>.</td>
																				</tr>
																				<tr>
																					<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="equipment[<?php echo $equipment_result['equipment_item_id']; ?>]" value="2"<?php echo $damage_checked; ?> /> was <strong>damaged</strong>.</td>
																				</tr>
																				<tr>
																					<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="equipment[<?php echo $equipment_result['equipment_item_id']; ?>]" value="3"<?php echo $missing_checked; ?> /> was <strong>missing</strong>.</td>
																				</tr>
																				
																				<?php
																				$loop++;
																			}
																		
																
																}
														}
														
														if (strpos(tep_fetch_installed_post_type($order_data_result['address_id']), 'PVC') !== false) {
															?>
															<tr>
																<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
															</tr>
															<tr>
																<td class="pageBoxContent">
																	<table cellspacing="0" cellpadding="0">
																		<tr>
																			<td width="200"><img src="images/pixel_trans.gif" height="1" width="200" /></td>
																			<td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
																		</tr>
																		<tr>
																			<td class="pageBoxContent" valign="top">Number of Metal Stakes returned: </td>
																			<td><input type="text" name="metal_stakes" size="1" value="<?php echo $order_data_result['number_of_posts']; ?>" />&nbsp;/&nbsp;<?php echo $order_data_result['number_of_posts']; ?></td>
																		</tr>
																	</table>
																</td>
															</tr>
															<?php
														}
													?>
														<tr>
															<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
														</tr>
														<tr>
															<td class="pageBoxContent"><b>Comments</b></td>
														</tr>
														<tr>
															<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
														</tr>
														<tr>
															<td class="pageBoxContent">ADDRESS COMMENTS are for RSP viewing ONLY. Include install issues, updated directions, homeowner issues, etc. <br />
COMMENTS FOR AGENTS will be emailed to the agent, who pay our bills.  Use this field sparingly and with discretion.   Include comments such as "Installed where Homeowner requested" or "Had to install PVC due to space limitations" when wood was requested or "No Sign Panel at removal", etc. </td>
														</tr>
														<tr>
															<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
														</tr>
														<tr>
															<td class="pageBoxContent">
																<table cellspacing="0" cellpadding="0">
																	<tr>
																		<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																		<td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
																	</tr>
																	<tr>
																		<td class="pageBoxContent" valign="top">ADDRESS COMMENTS: </td>
																		<td><textarea name="address_comments"><?php echo stripslashes($address_comments); ?></textarea></td>
																		<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
																		<td class="pageBoxContent" valign="top"><i>Comments will be stored with the address.</i></td>
																	</tr>
																	<tr>
																		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																	</tr>
																	<tr>
																		<td class="pageBoxContent" valign="top">COMMENTS FOR AGENTS: </td>
																		<td><textarea name="agent_comments"><?php echo stripslashes($agent_comments); ?></textarea></td>
																		<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
														<td class="pageBoxContent" valign="top"><i>USE THIS FIELD SPARINGLY AND WITH DISCRETION.  THESE COMMENTS WILL BE E-MAILED TO THE AGENT.</i></td>
																	</tr>
																</table>
															</td>
														</tr>
												<?php
											} else {
												$button_text = 'next_step_update_job';
												?>
												<tr>
													<td class="pageBoxContent"><b>Post was not removed.</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">Please either specify when the post should be removed of if you do not know then leave it blank.  Remember this page is only used if you did not remove the post and it is still there.  If anything is missing go back and mark it as completed.</td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>

												<tr>
													<td class="pageBoxContent">
														<table width="100%" cellspacing="0" cellpadding="0">
															<tr>
																<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																<td></td>
															</tr>
															<?php
																if (empty($removal_fail_new_date)) {
																	$removal_fail_new_date= date("n/d/Y"); 
																}
															?>
															<tr>
																<td width="150" class="pageBoxContent">New Date: </td>
                                                                <td width="450" class="pageBoxContent"><input type="text" class="new_date" name="removal_fail_new_date" value="<?php echo $removal_fail_new_date; ?>"></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Comments</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">ADDRESS COMMENTS are for RSP viewing ONLY. Include install issues, updated directions, homeowner issues, etc. <br />
COMMENTS FOR AGENTS will be emailed to the agent, who pay our bills.  Use this field sparingly and with discretion.   Include comments such as "Installed where Homeowner requested" or "Had to install PVC due to space limitations" when wood was requested or "No Sign Panel at removal", etc. </td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">
														<table cellspacing="0" cellpadding="0">
															<tr>
																<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																<td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
															</tr>
															<tr>
																<td class="pageBoxContent" valign="top">Address Comments: </td>
																<td><textarea name="address_comments"><?php echo stripslashes($address_comments); ?></textarea></td>
																<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
																<td class="pageBoxContent" valign="top"><i>Comments will be stored with the
address.  Please note any changes to directions/cross
streets.</i></td>
															</tr>
															<tr>
																<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
															</tr>
															<tr>
																<td class="pageBoxContent" valign="top">Agent Comments: </td>
																<td><textarea name="agent_comments"><?php echo stripslashes($agent_comments); ?></textarea></td>
															</tr>
														</table>
													</td>
												</tr>
												<?php
												}
										break;
										case '3':
											$button_text = 'next_step_mark_complete';
											
												if ($order_status == '1') {
													//Removal completed successfully.
													?>
													<tr>
														<td class="pageBoxContent">Please review the details below and confirm they are correct. To make any changes press the back button.</td>
													</tr>
													<tr>
														<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><b>Job Status</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">This Signpost was <strong>Recovered Successfully</strong>.</td>
													</tr>
													<tr>
														<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">Please read the following equipment list and confirm that these items were removed, missing or damaged as marked.  To change anythign press the Back button or press Confirm to submit the details.</td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">Please look over the following equipment and mark what was removed and what was not.</td>
													</tr>
													<?php
														$query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_id ASC");
															foreach($database->fetch_array($query) as $result){
																//Work out what equipment of this type should be at the address and only show if there were some.
																$count_query = $database->query("select count(eita.equipment_item_to_address_id) as count from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita where e.equipment_type_id = '" . $result['equipment_type_id'] . "' and e.equipment_id = eita.equipment_id and eita.address_id = '" . $order_data_result['address_id'] . "'");
																$count_result = $database->fetch_array($count_query);
																
																	if ($count_result['count'] > 0) {
																		?>
																		<tr>
																			<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
																		</tr>
																		<tr>
																			<td class="pageBoxContent"><b><?php echo $result['equipment_type_name']; ?></b></td>
																		</tr>
																		<tr>
																			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																		</tr>
																		<?php
																		//Now list the equipment.
																			$equipment_query = $database->query("select ei.equipment_item_id, e.name, e.tracking_method_id, ei.code from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei where eita.address_id = '" . $order_data_result['address_id'] . "' and eita.equipment_item_id = ei.equipment_item_id and ei.equipment_id = e.equipment_id and e.equipment_type_id = '" . $result['equipment_type_id'] . "' and eita.equipment_status_id = '2' order by e.name");
																			$loop = 0;
																			    foreach($database->fetch_array($equipment_query) as $equipment_result){
																						if ($loop > 0) {
																							?>
																							<tr>
																								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																							</tr>
																							<?php
																						}
																					?>
																					<tr>
																						<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $equipment_result['name'] . (($equipment_result['tracking_method_id'] == '1') ? '&nbsp;('.$equipment_result['code'].')' : ''); ?></td>
																					</tr>
																					<?php
																						if (!isset($equipment[$equipment_result['equipment_item_id']]) || ($equipment[$equipment_result['equipment_item_id']] == '1')) {
																						?>
																						<tr>
																							<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Was <strong>removed successfully</strong>.</td>
																						</tr>
																						<?php
																						} elseif ($equipment[$equipment_result['equipment_item_id']] == '2') {
																						?>
																						<tr>
																							<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#FF0000;">Was found <strong>damaged</strong>.</span></td>
																						</tr>
																						<?php	
																						} elseif ($equipment[$equipment_result['equipment_item_id']] == '3') {
																						?>
																						<tr>
																							<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#FF0000;">Was <strong>missing</strong>.</span></td>
																						</tr>
																						<?php
																						}
	
																					$loop++;
																				}
																			
																	
																	}
															}
															if ((strpos(tep_fetch_installed_post_type($order_data_result['address_id']), 'PVC') !== false) && (($metal_stakes != $order_data_result['number_of_posts']) && ($metal_stakes < $order_data_result['number_of_posts'])))  {
																?>
																<tr>
																	<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
																</tr>
																<tr>
																	<td class="pageBoxContent">
																		<table cellspacing="0" cellpadding="0">
																			<tr>
																				<td width="130"><img src="images/pixel_trans.gif" height="1" width="130" /></td>
																				<td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
																			</tr>
																			<tr>
																				<td class="pageBoxContent" valign="top"><strong>Missing Metal Stakes: </strong></td>
																				<td class="pageBoxContent"><strong><?php echo ($order_data_result['number_of_posts']-$metal_stakes); ?></strong></td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<?php
															}
														?>
													<tr>
														<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><b>Comments</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">Please review the comments you entered below.  Address comments will be stored with the address for next time whereas Agent comments will be sent to the agent along with confirmation of this order.  If any requested equipment could not be installed or anything happened with this install make sure it is recorded here.</td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">
															<table cellspacing="0" cellpadding="0">
																<tr>
																	<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																	<td width="100"><img src="images/pixel_trans.gif" height="1" width="200" /></td>
																</tr>
																<tr>
																	<td class="pageBoxContent" valign="top">Address Comments: </td>
																	<td width="200"><?php echo nl2br(stripslashes($address_comments)); ?></td>
																</tr>
																<tr>
																	<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																</tr>
																<tr>
																	<td class="pageBoxContent" valign="top">Agent Comments: </td>
																	<td width="200"><?php echo nl2br(stripslashes($agent_comments)); ?></td>
																</tr>
															</table>
														</td>
													</tr>
												<?php
											} else {
												//Removal not completed successfully.
												?>
												<tr>
													<td class="pageBoxContent">Please review the details below and confirm they are correct. To make any changes press the back button.</td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Job Status</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><span style="color:#FF0000;">This Signpost was <strong>Not Recovered Successfully</strong>.</span></td>
												</tr>
												<tr>
													<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent"><b>Reason and Details</b></td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
												<tr>
													<td class="pageBoxContent">The Homeowner/Tenant requested the post stay.</td>
												</tr>
												<tr>
													<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
												</tr>
													<?php
														if (empty($removal_fail_new_date)) {
													?>
														<tr>
															<td class="pageBoxContent">I was not able to get a new removal date.</td>
														</tr>
													<?php
														} else {
													?>
														<tr>
															<td class="pageBoxContent">It has been requested that the removal now take place on <strong><?php echo stripslashes($removal_fail_new_date); ?>.</strong></td>
														</tr>
													<?php
														}
													?>
													<tr>
														<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent"><b>Comments</b></td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">Please review the comments you entered below.  Address comments will be stored with the address for next time whereas Agent comments will be sent to the agent along with confirmation of this order.  If any requested equipment could not be installed or anything happened with this install make sure it is recorded here.</td>
													</tr>
													<tr>
														<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
													</tr>
													<tr>
														<td class="pageBoxContent">
															<table cellspacing="0" cellpadding="0">
																<tr>
																	<td width="150"><img src="images/pixel_trans.gif" height="1" width="150" /></td>
																	<td width="100"><img src="images/pixel_trans.gif" height="1" width="200" /></td>
																</tr>
																<tr>
																	<td class="pageBoxContent" valign="top">Address Comments: </td>
																	<td width="200"><?php echo nl2br(stripslashes($address_comments)); ?></td>
																</tr>
																<tr>
																	<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																</tr>
																<tr>
																	<td class="pageBoxContent" valign="top">Agent Comments: </td>
																	<td width="200"><?php echo nl2br(stripslashes($agent_comments)); ?></td>
																</tr>
															</table>
														</td>
													</tr>
												<?php
											}
											
									break;
								}
							break;
						}
					?>	
				<tr>
					<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
				</tr>
				<tr>
					<td width="100%">
						<table cellspacing="3" cellpadding="3" width="100%">
							<tr>
								<td align="left"><?php echo tep_create_button_submit('back', 'Go Back', ' name="back_button"'); ?></td>
								<td align="right"><?php echo ((!empty($button_text)) ? tep_create_button_submit($button_text, ucwords(str_replace('_', ' ', $button_text)), ' name="submit_button"') : ''); ?></form></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
