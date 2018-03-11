<?php
/*
	agent_active_addresses.php
	Allows agents to view thier current active addresses.
	This means that they are able to see what addresses currently have signposts and gives and inventory
	of what equipment is at each location.  This page also gives them the ability to scheduale a service call,
	a removal or extend the removal date.
	In this way it closely ties in with the order system but allow for the content to be displayed in a much
	more user friendly way.
	This will give a detailed breakfdown of each address including the install date, the number of service calls,
	the number of schedualed service calls and the scedualed removal date (as retrieved by the removal date
	for the removal assigned to the address).
*/
	$page_action = tep_fill_variable('page_action', 'get');
	$agent_id = tep_fill_variable('agent_id', 'post');
	$aID = tep_fill_variable('aID', 'get');
	$order_view = tep_fill_variable('order_view', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '');
	$order_type = tep_fill_variable('order_type', 'get', '');
	$job_start_date = tep_fill_variable('job_start_date', 'post', '');
	$show_house_number = tep_fill_variable('show_house_number', 'get', '');
	$show_street_name = tep_fill_variable('show_street_name', 'get', '');
	$show_city = tep_fill_variable('show_city', 'get', '');
//$error->add_error('agent_active_addresses', 'Please enter the Number of Posts.');
//if (!$error->get_error_status('agent_active_addrsses')) {

//}
/*
		if (!empty($aID)) {
			$query = $database->query("select atu.address_id from " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_USERS . " u where atu.address_id = '" . $aID . "' and atu.user_id = u.user_id and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()). "' limit 1");
			$result = $database->fetch_array($query);
				if (empty($result['address_id'])) {
					$aID = '';
					$page_action = '';
				}
		}
*/
		if ($page_action == 'reschedule_removal_success') {

				$special_instructions = tep_fill_variable('special_instructions', 'post', tep_fill_variable('special_instructions', 'session'));
				if (empty($job_start_date)) {
					$error->add_error('aom_active_addresses', 'Please enter a date in the proper format.');
				} else {
					$job_start_date=$_POST['job_start_date'];

					$schedualed_start = strtotime($job_start_date);
						if ($schedualed_start < time()) {
							$error->add_error('aom_active_addresses', 'The new date must be in the future.');
						} elseif ($schedualed_start < tep_fetch_install_date($aID)) {
							$error->add_error('aom_active_addresses', 'The new date must be greater than the install date.');
						}
				}
				if ($error->get_error_status('aom_active_addresses')) {
					$page_action = 'reschedule_removal';
				} else {
					//Check if this day is a weekend and if so change it to the next monday.
					$dow = date("w", $schedualed_start);
					$move = 0;
						if ($dow == 0) {
							$move = 1;
						} elseif ($dow > 5) {
							$move = (8 - $dow);
						}
					$schedualed_start += ($move * 86400);

					//We also need to check if this is beyond the 240 day window.
					$query = $database->query("select date_completed, user_id, service_level_id from " . TABLE_ORDERS . "  where address_id = '" . $aID . "' and order_type_id = '1' limit 1");
					$result = $database->fetch_array($query);
					$to_assign_agent_id = $result['user_id'];
					$to_assign_service_level_id = $result['service_level_id'];

						if (($schedualed_start - $result['date_completed']) > (MAX_FREE_REMOVAL_TIME * 60 * 60 * 24)) {
							//Extended!!!
							//Add an entry to the extended retals and flag the address, no bill is needed as the monthly cron does this.
							$database->query("update " . TABLE_ADDRESSES . " set extended_removal = '1' where address_id = '" . $aID . "' limit 1");

							$check_entry_query = $database->query("select extended_removal_address_id from " . TABLE_EXTENDED_REMOVAL_ADDRESSES . " where address_id = '" . $aID . "' limit 1");
							$check_entry_result = $database->fetch_array($check_entry_query);

								if (empty($check_entry_result['extended_removal_address_id'])) {
									$extended_date = ($result['date_completed'] + (MAX_FREE_REMOVAL_TIME * 60 * 60 * 24));
									$database->query("insert into " . TABLE_EXTENDED_REMOVAL_ADDRESSES . " (address_id, user_id, date_added, date_extended, day_extended, month_extended, year_extended) values ('" . $aID . "', '" . $user->fetch_user_id() . "', '" . time() . "', '" . $extended_date . "', '" . date("d", $extended_date) . "', '" . date("n", $extended_date) . "', '" . date("Y", $extended_date) . "')");
								}
						}

					//If the order doesn't exist then create it.
					$check_removal_query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '" . $aID . "' and order_type_id = '3'");
					$check_removal_result = $database->fetch_array($check_removal_query);
						if (!empty($check_removal_result['order_id'])) {
                            $order_id = $check_removal_result['order_id'];
                            $last_modified_by = tep_fill_variable('user_id', 'session', 0);

                            $old_schedualed_start_query = $database->query("SELECT date_schedualed FROM " . TABLE_ORDERS . " WHERE order_id = '" . $order_id . "' limit 1");
                            while ($old_schedualed_start_row = $database->fetch_array($old_schedualed_start_query)) {
                                $old_schedualed_start = $old_schedualed_start_row['date_schedualed'];
                            }

							$database->query("update " . TABLE_ORDERS . " set date_schedualed = '" . $schedualed_start . "', order_status_id = '1', last_modified = '" . time() . "', last_modified_by = '" . $last_modified_by . "' where address_id = '" . $aID . "' and order_type_id = '3' limit 1");
							$query = $database->query("select order_id from " . TABLE_ORDERS . "  where address_id = '" . $aID . "' and order_type_id = '1'");
							$result = $database->fetch_array($query);
							$post_query = $database->query("select number_of_posts from " . TABLE_ORDERS_DESCRIPTION . "  where order_id = ".$result['order_id']);
							$post_result = $database->fetch_array($post_query);
							$number_of_posts = $post_result['number_of_posts'];
							$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set special_instructions = '" . $special_instructions . "', number_of_posts = $number_of_posts where order_id = $order_id limit 1");

                            if ($old_schedualed_start != $schedualed_start) {
                                $rescheduled_date = time();
                                $reschedule_table = TABLE_RESCHEDULE_HISTORY;
                                $reschedule_query = "INSERT INTO {$reschedule_table} (order_id, user_id, old_scheduled_date, new_scheduled_date, rescheduled_date) ";
                                $reschedule_query.= "VALUES ({$order_id}, {$last_modified_by}, {$old_schedualed_start}, {$schedualed_start}, {$rescheduled_date})";
                                $database->query($reschedule_query);
                            }
						} else {
							$query = $database->query("select order_id, billing_method_id from " . TABLE_ORDERS . "  where address_id = '" . $aID . "' and order_type_id = '1' limit 1");
							$result = $database->fetch_array($query);
							$post_query = $database->query("select number_of_posts from " . TABLE_ORDERS_DESCRIPTION . "  where order_id = ".$result['order_id']." limit 1");
							$post_result = $database->fetch_array($post_query);
							$data = array('address_id' => $aID,
												  'order_type_id' => '3',
												  'special_instructions' => $special_instructions,
												  'schedualed_start' => $schedualed_start,
												  'service_level_id' => $to_assign_service_level_id,
												  'county' => tep_fetch_address_county_id($aID),
												  'promo_code' => '',
												  'number_of_posts' => $post_result['number_of_posts'],
												  'billing_method_id' => $result['billing_method_id']);
							$order = new orders('insert', '', $data, $to_assign_agent_id, false, '1');
							$order_id = $order->id;
						}
					$session->php_session_register('order_id', $order_id);
                    $session->php_session_register('agent_id', $to_assign_agent_id); // mjp 201110
					$page_action = '';
					tep_redirect(FILENAME_AOM_SCHEDULE_REMOVAL_SUCCESS.'?aID='.$aID.'&order_id='.$order_id);
				}
		}
		if (($page_action != 'view_equipment') && ($page_action != 'view_history') && ($page_action != 'reschedule_removal')) {

        }


		if (($page_action == 'view_equipment') || ($page_action == 'view_history') || ($page_action == 'reschedule_removal')) {
			$vars['address_information'] = tep_fetch_address_information($aID);

		}

		if (($page_action == 'view_agent_all')) {
			$vars['address_information'] = tep_fetch_address_information($aID);
			//echo $agent_id;

			$listing_split = new split_page("select a.address_id, o.date_added, a.house_number, a.street_name, a.city, a.zip, a.status, s.name as state_name, c.name as county_name, a.zip4, a.status, u.user_id, ud.firstname, ud.lastname from " . TABLE_ADDRESSES . " a left join " . TABLE_ORDERS . " o on (a.address_id = o.address_id and o.order_type_id = '3' and o.order_status_id != '3' and o.order_status_id != '4'), " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c, " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where atu.user_id = u.user_id and u.user_id = ud.user_id  and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()) . "'" . ((!empty($agent_id)) ? " and u.user_id = '" . $agent_id . "'" : '') . " " . (!empty($show_house_number) ? " and a.house_number = '" . $show_house_number . "'" : '') . " " . (!empty($show_street_name) ? " and (a.street_name = '" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "%' or a.street_name like '" . $show_street_name . "%')" : '') . " " . (!empty($show_city) ? " and (a.city = '" . $show_city . "' or a.city like '%" . $show_city . "' or a.city like '%" . $show_city . "%' or a.city like '" . $show_city . "%')" : '') . " and atu.address_id = a.address_id and a.state_id = s.state_id and a.county_id = c.county_id and (o.order_status_id != '3' or (o.order_id is NULL and a.status < '3')) order by a.address_id DESC", '500', 'a.address_id');
			if ($listing_split->number_of_rows > 0) {

				$query = $database->query($listing_split->sql_query);
					while($result = $database->fetch_array($query)) {

						$install_date_query = $database->query("select date_schedualed, order_id, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '1' limit 1");
						//echo "select date_schedualed, order_id, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '1' limit 1<br/>";
						$result['install_date'] = $database->fetch_array($install_date_query);
						//var_dump($result['install_date']);
						//echo "<br/>".date('Y/m/d H:i:s',1503547200)."<br/>";
						$result['oid'] = $result['install_date']['order_id'];
					//	echo "<br/>";
					//	var_dump($result['oid']);
						//echo "<br/>";
						$removal_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '3' limit 1");
					///	echo "select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '3' limit 1<br/>";
						$result['removal_date'] = $database->fetch_array($removal_date_query);
					//	var_dump($result['removal_date']);
						//echo "<br/>";
						$service_count_query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '2'");
						//echo "select count(order_id) as count from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '2'<br/>";
						$result['service_count'] = $database->fetch_array($service_count_query);
						//var_dump($result['service_count']);
					//	echo "<br/>";
						$status = 'Unknown';
							if ($result['install_date']['order_status_id'] == '3') {
								$status = 'Installed';
							} elseif ($result['install_date']['order_status_id'] == '1') {
								$status = 'Pending Installation';
							} elseif ($result['install_date']['order_status_id'] == '2') {
								$status = 'Installation Scheduled';
							} elseif ($result['install_date']['order_status_id'] == '5') {
								$status = 'Installation On Hold';
							}
							if ($result['removal_date']['order_status_id'] == '2') {
								$status = 'Installed - Removal Scheduled';
							} elseif ($result['removal_date']['order_status_id'] == '3') {
								$status = 'Removed';
							} elseif ($result['removal_date']['order_status_id'] == '5') {
								$status = 'Installed - Removal On Hold';
							}
						$result['status'] = $status;
						//var_dump($result['status']);
						//echo "<br/>";
						if ($result['install_date']['order_status_id'] == '1' || $result['install_date']['order_status_id'] == '2') {
							$vars['current_table'][] = $result;
						} else {
							$vars['table'][] = $result;
						}
					}
					//var_dump($result);
				}

						$vars['listing_split'] = $listing_split;
/*						$query = $database->query($listing_split->sql_query);
							while($result = $database->fetch_array($query)) {

								$install_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '1' limit 1");
								//echo "select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '1' limit 1<br/>";
								$result['install_date'] = $database->fetch_array($install_date_query);
								$removal_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '3' limit 1");
								$result['removal_date'] = $database->fetch_array($removal_date_query);
								$service_count_query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '2'");
								$result['service_count'] = $database->fetch_array($service_count_query);
								$status = 'Unknown';
									if ($result['install_date']['order_status_id'] == '3') {
										$status = 'Installed';
									} elseif ($result['install_date']['order_status_id'] == '1') {
										$status = 'Pending Installation';
									} elseif ($result['install_date']['order_status_id'] == '2') {
										$status = 'Installation Scheduled';
									} elseif ($result['install_date']['order_status_id'] == '5') {
										$status = 'Installation On Hold';
									}
									else {
										//$status = 'Cancelled';
										continue;
									}

									if ($result['removal_date']['order_status_id'] == '3') {
										continue;
										$status = 'Removed';
									} elseif ($result['removal_date']['order_status_id'] == '5') {
										$status = 'Installed - Removal On Hold';
									}

								$result['status'] = $status;
								$vars['table'][] = $result;

								var_dump($result);
							}
*/
								$pulldowns = array(
						'agents' => tep_draw_aom_agent_pulldown_bgdn('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
						'agents_pending_installs' => tep_draw_aom_agent_pulldown_bgdn_pending_installs('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
						'agents_pending_service_calls' => tep_draw_aom_agent_pulldown_bgdn_pending_service_calls('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
						'agents_pending_removals' => tep_draw_aom_agent_pulldown_bgdn_pending_removals('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
					);

					$vars['pulldowns'] = $pulldowns;
					echo $twig->render('aom/active_addresses_list_all.html.twig', array('user' => $user, 'agentID'=>$agent_id, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

		} else if (($page_action == 'view_agent_pending_installs')) {
					$vars['address_information'] = tep_fetch_address_information($aID);
					//echo $agent_id;

					$listing_split = new split_page("select a.address_id, o.date_added, a.house_number, a.street_name, a.city, a.zip, a.status, s.name as state_name, c.name as county_name, a.zip4, a.status, u.user_id, ud.firstname, ud.lastname from " . TABLE_ADDRESSES . " a left join " . TABLE_ORDERS . " o on (a.address_id = o.address_id and o.order_type_id = '3' and o.order_status_id != '3' and o.order_status_id != '4'), " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c, " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where atu.user_id = u.user_id and u.user_id = ud.user_id  and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()) . "'" . ((!empty($agent_id)) ? " and u.user_id = '" . $agent_id . "'" : '') . " " . (!empty($show_house_number) ? " and a.house_number = '" . $show_house_number . "'" : '') . " " . (!empty($show_street_name) ? " and (a.street_name = '" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "%' or a.street_name like '" . $show_street_name . "%')" : '') . " " . (!empty($show_city) ? " and (a.city = '" . $show_city . "' or a.city like '%" . $show_city . "' or a.city like '%" . $show_city . "%' or a.city like '" . $show_city . "%')" : '') . " and atu.address_id = a.address_id and a.state_id = s.state_id and a.county_id = c.county_id and (o.order_status_id != '3' or (o.order_id is NULL and a.status < '3')) order by a.address_id DESC", '500', 'a.address_id');
		            if ($listing_split->number_of_rows > 0) {

		            }
					$vars['listing_split'] = $listing_split;


								$query = $database->query($listing_split->sql_query);
									while($result = $database->fetch_array($query)) {

										$install_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '1' limit 1");
										$result['install_date'] = $database->fetch_array($install_date_query);
										$removal_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '3' limit 1");
										$result['removal_date'] = $database->fetch_array($removal_date_query);
										$service_count_query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '2'");
										$result['service_count'] = $database->fetch_array($service_count_query);
										$status = 'Unknown';
											if ($result['install_date']['order_status_id'] == '3') {
												$status = 'Installed';
											} elseif ($result['install_date']['order_status_id'] == '1') {
												$status = 'Pending Installation';
											} elseif ($result['install_date']['order_status_id'] == '2') {
												$status = 'Installation Scheduled';
											} elseif ($result['install_date']['order_status_id'] == '5') {
												$status = 'Installation On Hold';
											}
											else {
												//$status = 'Cancelled';
												continue;
											}

											if ($result['removal_date']['order_status_id'] == '3') {
												continue;
												$status = 'Removed';
											} elseif ($result['removal_date']['order_status_id'] == '5') {
												$status = 'Installed - Removal On Hold';
											}

										$result['status'] = $status;
										$vars['table'][] = $result;


									}
							$pulldowns = array(
								'agents' => tep_draw_aom_agent_pulldown_bgdn('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
								'agents_pending_installs' => tep_draw_aom_agent_pulldown_bgdn_pending_installs('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
								'agents_pending_service_calls' => tep_draw_aom_agent_pulldown_bgdn_pending_service_calls('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
								'agents_pending_removals' => tep_draw_aom_agent_pulldown_bgdn_pending_removals('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
							);

							$vars['pulldowns'] = $pulldowns;
							echo $twig->render('aom/active_addresses_list_pending_installs.html.twig', array('user' => $user, 'agentID'=>$agent_id, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

				} else if (($page_action == 'view_agent_pending_service_calls')) {
							$vars['address_information'] = tep_fetch_address_information($aID);
							//echo $agent_id;

							$listing_split = new split_page("select a.address_id, o.date_added, a.house_number, a.street_name, a.city, a.zip, a.status, s.name as state_name, c.name as county_name, a.zip4, a.status, u.user_id, ud.firstname, ud.lastname from " . TABLE_ADDRESSES . " a left join " . TABLE_ORDERS . " o on (a.address_id = o.address_id and o.order_type_id = '3' and o.order_status_id != '3' and o.order_status_id != '4'), " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c, " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where atu.user_id = u.user_id and u.user_id = ud.user_id  and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()) . "'" . ((!empty($agent_id)) ? " and u.user_id = '" . $agent_id . "'" : '') . " " . (!empty($show_house_number) ? " and a.house_number = '" . $show_house_number . "'" : '') . " " . (!empty($show_street_name) ? " and (a.street_name = '" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "%' or a.street_name like '" . $show_street_name . "%')" : '') . " " . (!empty($show_city) ? " and (a.city = '" . $show_city . "' or a.city like '%" . $show_city . "' or a.city like '%" . $show_city . "%' or a.city like '" . $show_city . "%')" : '') . " and atu.address_id = a.address_id and a.state_id = s.state_id and a.county_id = c.county_id and (o.order_status_id != '3' or (o.order_id is NULL and a.status < '3')) order by a.address_id DESC", '500', 'a.address_id');
				            if ($listing_split->number_of_rows > 0) {

				            }
							$vars['listing_split'] = $listing_split;


										$query = $database->query($listing_split->sql_query);
											while($result = $database->fetch_array($query)) {

												$install_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '1' limit 1");
												$result['install_date'] = $database->fetch_array($install_date_query);
												$removal_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '3' limit 1");
												$result['removal_date'] = $database->fetch_array($removal_date_query);
												$service_count_query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '2'");
												$result['service_count'] = $database->fetch_array($service_count_query);
												$status = 'Unknown';
													if ($result['install_date']['order_status_id'] == '3') {
														$status = 'Installed';
													} elseif ($result['install_date']['order_status_id'] == '1') {
														$status = 'Pending Installation';
													} elseif ($result['install_date']['order_status_id'] == '2') {
														$status = 'Installation Scheduled';
													} elseif ($result['install_date']['order_status_id'] == '5') {
														$status = 'Installation On Hold';
													}
													else {
														//$status = 'Cancelled';
														continue;
													}

													if ($result['removal_date']['order_status_id'] == '3') {
														continue;
														$status = 'Removed';
													} elseif ($result['removal_date']['order_status_id'] == '5') {
														$status = 'Installed - Removal On Hold';
													}

												$result['status'] = $status;
												$vars['table'][] = $result;


											}
									$pulldowns = array(
										'agents' => tep_draw_aom_agent_pulldown_bgdn('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
										'agents_pending_installs' => tep_draw_aom_agent_pulldown_bgdn_pending_installs('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
										'agents_pending_service_calls' => tep_draw_aom_agent_pulldown_bgdn_pending_service_calls('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
										'agents_pending_removals' => tep_draw_aom_agent_pulldown_bgdn_pending_removals('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
									);

									$vars['pulldowns'] = $pulldowns;
									echo $twig->render('aom/active_addresses_list_pending_service_calls.html.twig', array('user' => $user, 'agentID'=>$agent_id, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

						} else if (($page_action == 'view_agent_pending_removals')) {
									$vars['address_information'] = tep_fetch_address_information($aID);
									//echo $agent_id;

									$listing_split = new split_page("select a.address_id, o.date_added, a.house_number, a.street_name, a.city, a.zip, a.status, s.name as state_name, c.name as county_name, a.zip4, a.status, u.user_id, ud.firstname, ud.lastname from " . TABLE_ADDRESSES . " a left join " . TABLE_ORDERS . " o on (a.address_id = o.address_id and o.order_type_id = '3' and o.order_status_id != '3' and o.order_status_id != '4'), " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c, " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where atu.user_id = u.user_id and u.user_id = ud.user_id  and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()) . "'" . ((!empty($agent_id)) ? " and u.user_id = '" . $agent_id . "'" : '') . " " . (!empty($show_house_number) ? " and a.house_number = '" . $show_house_number . "'" : '') . " " . (!empty($show_street_name) ? " and (a.street_name = '" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "%' or a.street_name like '" . $show_street_name . "%')" : '') . " " . (!empty($show_city) ? " and (a.city = '" . $show_city . "' or a.city like '%" . $show_city . "' or a.city like '%" . $show_city . "%' or a.city like '" . $show_city . "%')" : '') . " and atu.address_id = a.address_id and a.state_id = s.state_id and a.county_id = c.county_id and (o.order_status_id != '3' or (o.order_id is NULL and a.status < '3')) order by a.address_id DESC", '500', 'a.address_id');
						            if ($listing_split->number_of_rows > 0) {

						            }
									$vars['listing_split'] = $listing_split;


												$query = $database->query($listing_split->sql_query);
													while($result = $database->fetch_array($query)) {

														$install_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '1' limit 1");
														$result['install_date'] = $database->fetch_array($install_date_query);
														$removal_date_query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '3' limit 1");
														$result['removal_date'] = $database->fetch_array($removal_date_query);
														$service_count_query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '2'");
														$result['service_count'] = $database->fetch_array($service_count_query);
														$status = 'Unknown';
															if ($result['install_date']['order_status_id'] == '3') {
																$status = 'Installed';
															} elseif ($result['install_date']['order_status_id'] == '1') {
																$status = 'Pending Installation';
															} elseif ($result['install_date']['order_status_id'] == '2') {
																$status = 'Installation Scheduled';
															} elseif ($result['install_date']['order_status_id'] == '5') {
																$status = 'Installation On Hold';
															}
															else {
																//$status = 'Cancelled';
																continue;
															}

															if ($result['removal_date']['order_status_id'] == '3') {
																continue;
																$status = 'Removed';
															} elseif ($result['removal_date']['order_status_id'] == '5') {
																$status = 'Installed - Removal On Hold';
															}

														$result['status'] = $status;
														$vars['table'][] = $result;


													}
											$pulldowns = array(
												'agents' => tep_draw_aom_agent_pulldown_bgdn('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
												'agents_pending_installs' => tep_draw_aom_agent_pulldown_bgdn_pending_installs('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
												'agents_pending_service_calls' => tep_draw_aom_agent_pulldown_bgdn_pending_service_calls('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
												'agents_pending_removals' => tep_draw_aom_agent_pulldown_bgdn_pending_removals('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
											);

											$vars['pulldowns'] = $pulldowns;
											echo $twig->render('aom/active_addresses_list_pending_removals.html.twig', array('user' => $user, 'agentID'=>$agent_id, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

								}
				elseif (($page_action != 'view_equipment') && ($page_action != 'view_history') && ($page_action != 'reschedule_removal')) {

					$pulldowns = array(
						'agents' => tep_draw_aom_agent_pulldown_bgdn('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
						'agents_pending_installs' => tep_draw_aom_agent_pulldown_bgdn_pending_installs('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
						'agents_pending_service_calls' => tep_draw_aom_agent_pulldown_bgdn_pending_service_calls('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
						'agents_pending_removals' => tep_draw_aom_agent_pulldown_bgdn_pending_removals('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
					);
					$vars['pulldowns'] = $pulldowns;
					echo $twig->render('aom/active_addresses_list_select_agent.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));


				} elseif ($page_action == 'view_equipment') {
					//View equipment.
				} elseif ($page_action == 'view_history') {

						//Now list the orders.
						$query = $database->query("select o.order_id, o.date_added, o.date_schedualed, o.date_completed, o.order_issue, o.order_status_id, os.order_status_name, ot.name as order_type_name from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.address_id = '" . $aID . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id order by date_schedualed");
							while($result = $database->fetch_array($query)) {
								$vars['history_result'][] = $result;
							}
					echo $twig->render('aom/active_addresses_history.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

				} elseif ($page_action == 'reschedule_removal') {
					$query = $database->query("select date_schedualed, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $aID . "' and order_type_id = '3' limit 1");
					$result = $database->fetch_array($query);
					if (empty($result['date_schedualed'])) {
						$result['date_schedualed'] = time();
					}
					$dt=$result['date_schedualed'];
					$order_status_id = $result['order_status_id'];
					$session->php_session_register('current_date_scheduled', $dt);
					$myDate =date('d-M-Y',$dt);
					$tomorrow = mktime(1, 0, 0, date("m"), date("j")+1, date("Y"));
					$tomorrow = date('d-M-Y',$tomorrow);

					$vars['dt'] = $dt;

					if ($order_status_id >= ORDER_STATUS_SCHEDULED) {
						echo $twig->render('aom/active_addresses_busy.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
					} else {
						$vars['aID'] = $aID;
						echo $twig->render('aom/active_addresses_reschedule.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
					}
			}
