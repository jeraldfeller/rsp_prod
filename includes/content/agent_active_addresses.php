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
@session_start();


	$page_action = tep_fill_variable('page_action', 'get');
	$aID = tep_fill_variable('aID', 'get');
	$order_view = tep_fill_variable('order_view', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '');
	$order_type = tep_fill_variable('order_type', 'get', '');
	$job_start_date = tep_fill_variable('job_start_date', 'post', '');
	$show_house_number = tep_fill_variable('show_house_number', 'get', '');
	$show_street_name = tep_fill_variable('show_street_name', 'get', '');
	$show_city = tep_fill_variable('show_city', 'get', '');
	$sort_by = tep_fill_variable('sort_by', 'get', 'number');
	
	$form = array();
	$form['house_number'] = $show_house_number;
	$form['street_name'] = $show_street_name;
	$form['city'] = $show_city;
	$form['sort_by'] = $sort_by;


		if ($page_action == 'reschedule_removal_success') {

				$special_instructions = tep_fill_variable('special_instructions', 'post', tep_fill_variable('special_instructions', 'session'));
				if (empty($job_start_date)) {
					$error->add_error('agent_active_addresses', 'Please enter a date in the proper format.');
				} else {
					$schedualed_start = strtotime($job_start_date);
					if ($schedualed_start < time()) {
					    $error->add_error('agent_active_addresses', 'The new date must be in the future.');
					} elseif ($schedualed_start < tep_fetch_install_date($aID)) {
						$error->add_error('agent_active_addresses', 'The new date must be greater than the install date.');
					} elseif ($schedualed_start > tep_max_removal_window(tep_fetch_install_date($aID))) {
						$error->add_error('agent_active_addresses', 'The new date is too far in the future, it can not be greater than ' . date("n/d/Y", tep_max_removal_window(tep_fetch_install_date($aID))) . '.');
					}
				}
				//print_r($error);
				if ($error->get_error_status('agent_active_addresses')) {
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
					$query = $database->query("select date_completed from " . TABLE_ORDERS . "  where address_id = '" . $aID . "' and order_type_id = '1' limit 1");
					$result = $database->fetch_array($query);

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

						//If the order dosnt exist then create it.
					$check_removal_query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '" . $aID . "' and order_type_id = '3'");
					$check_removal_result = $database->fetch_array($check_removal_query);
						if (!empty($check_removal_result['order_id'])) {
                            $order_id = $check_removal_result['order_id'];
                            $last_modified_by = tep_fill_variable('user_id', 'session', 0);

                            $old_schedualed_start_query = $database->query("SELECT date_schedualed FROM " . TABLE_ORDERS . " WHERE order_id = '" . $order_id . "' limit 1");
                            foreach($database->fetch_array($old_schedualed_start_query) as $old_schedualed_start_row){
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
										  'county' => tep_fetch_address_county_id($aID),
										  'promo_code' => '',
										  'number_of_posts' => $post_result['number_of_posts'],
										  'billing_method_id' => $result['billing_method_id']);

							$order = new orders('insert', '', $data, '', false, '1');
							$order_id = $order->id;
						}
					$session->php_session_register('order_id', $order_id);
					$page_action = '';
					tep_redirect(FILENAME_SCHEDULE_REMOVAL_SUCCESS.'?aID='.$aID);
				}
		}
		if (($page_action != 'view_equipment') && ($page_action != 'view_history') && ($page_action != 'reschedule_removal')) {
            $listing_split = new split_page("select a.address_id,  a.house_number, a.street_name, a.city, a.zip, a.status, s.name as state_name, c.name as county_name, a.zip4, a.status from " . TABLE_ADDRESSES . " a left join " . TABLE_ORDERS . " o on (a.address_id = o.address_id and o.order_type_id = '3' and o.order_status_id != '4') left join " . TABLE_ORDERS . " ow on (a.address_id = ow.address_id and ow.order_type_id = '1'), " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where atu.user_id = '" . $user->fetch_user_id() . "' and atu.address_id = a.address_id and a.state_id = s.state_id " . (!empty($form['house_number']) ? " and a.house_number = '" . $form['house_number'] . "'" : '') . " " . (!empty($form['street_name']) ? " and (a.street_name = '" . $form['street_name'] . "' or a.street_name like '%" . $form['street_name'] . "' or a.street_name like '%" . $form['street_name'] . "%' or a.street_name like '" . $form['street_name'] . "%')" : '') . " " . (!empty($form['city']) ? " and (a.city = '" . $form['city'] . "' or a.city like '%" . $form['city'] . "' or a.city like '%" . $form['city'] . "%' or a.city like '" . $form['city'] . "%')" : '') . " and a.county_id = c.county_id and (o.order_status_id != '3' or (o.order_id is NULL and a.status < '3')) and ow.order_status_id != '4' group by a.address_id order by " . (($form['sort_by'] == 'number') ? 'a.house_number + 0' : (($form['sort_by'] == 'street') ? 'a.street_name' : 'a.city')) . " ASC", '200', 'a.address_id');

        }

				if (($page_action != 'view_equipment') && ($page_action != 'view_history') && ($page_action != 'reschedule_removal')) {

/*$session->php_session_unregister('miss_utility_yes_no');
$session->php_session_unregister('lamp_yes_no');
$session->php_session_unregister('lamp_use_gas');
unset($_SESSION['lamp_use_gas']);
unset($_SESSION['lamp_yes_no']);
$session->php_session_unregister('order_id');
$session->php_session_unregister('order_type_id');
$session->php_session_unregister('address_id');
$session->php_session_unregister('house_number');
$session->php_session_unregister('street_name');
$session->php_session_unregister('number_of_posts');
$session->php_session_unregister('city');
$session->php_session_unregister('zip');
$session->php_session_unregister('zip4');
$session->php_session_unregister('state');
$session->php_session_unregister('county');
$session->php_session_unregister('cross_street_directions');
$session->php_session_unregister('schedualed_start');
$session->php_session_unregister('request_zip4');
$session->php_session_unregister('payment_method_id');
$session->php_session_unregister('cc_type');
$session->php_session_unregister('cc_name');
$session->php_session_unregister('cc_number');
$session->php_session_unregister('cc_month');
$session->php_session_unregister('cc_year');
$session->php_session_unregister('cc_verification_number');
$session->php_session_unregister('cc_billing_street');
$session->php_session_unregister('cc_billing_city');
$session->php_session_unregister('cc_billing_zip');
$session->php_session_unregister('sc_reason');
$session->php_session_unregister('sc_reason_4');
$session->php_session_unregister('sc_reason_5');
$session->php_session_unregister('sc_reason_7');
$session->php_session_unregister('equipment');
$session->php_session_unregister('optional');
$session->php_session_unregister('optional_with_nones');
$session->php_session_unregister('install_equipment');
$session->php_session_unregister('remove_equipment');
$session->php_session_unregister('special_instructions');
$session->php_session_unregister('adc_page');
$session->php_session_unregister('adc_letter');
$session->php_session_unregister('adc_number');
$session->php_session_unregister('order_with_credit_total');

$session->php_session_unregister('optional_with_nones');

$session->php_session_unregister('deferred_total');
$session->php_session_unregister('deferred_transactions');
$session->php_session_unregister('deferred_credit');*/

				
				//LIST STARTS

$vars['deferred'] = null;
				
				$billing_method_id = tep_fill_variable('billing_method_id', 'session', 1);

                    if ($billing_method_id == 1) {

                        $account_id = account::getAccountId($user->fetch_user_id(), $user->agency_id, $billing_method_id, false);

                        if ($account_id > 0) {

                            $deferred = new DeferredBilling($account_id);

                            $deferred_total = $deferred->getTotal();

                            if ($deferred_total > 0) {
								$vars['deferred'] = number_format($deferred_total, 2);;
                            }
                        }
                    }


				
				$vars['address_information'] = tep_fetch_address_information($aID);										
					if ($listing_split->number_of_rows > 0) {
				
						$query = $database->query($listing_split->sql_query);
						    foreach($database->fetch_array($query) as $result){

								$install_date_query = $database->query("select date_schedualed, order_id, order_status_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '1' limit 1");
								$result['install_date'] = $database->fetch_array($install_date_query);
								$result['oid'] = $result['install_date']['order_id'];							
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
									if ($result['removal_date']['order_status_id'] == '2') {
										$status = 'Installed - Removal Scheduled';
									} elseif ($result['removal_date']['order_status_id'] == '3') {
										$status = 'Removed';
									} elseif ($result['removal_date']['order_status_id'] == '5') {
										$status = 'Installed - Removal On Hold';
									}
								$result['status'] = $status;
								if ($result['install_date']['order_status_id'] == '1' || $result['install_date']['order_status_id'] == '2') {
									$vars['current_table'][] = $result;
								} else {
									$vars['table'][] = $result;
								}
							}
							
						}
						//print_r($vars['table']);
						$vars['listing_split'] = $listing_split;
						$vars['pagination'] = tep_get_all_get_params(array('page', 'info', 'x', 'y'));
						$vars['form'] = $form;
						echo $twig->render('agent/active_addresses_list.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));	
						//LIST ENDS						
				} elseif ($page_action == 'view_equipment') {
					//View equipment.
				} elseif ($page_action == 'view_history') {
					$vars['address_information'] = tep_fetch_address_information($aID);	
					//Now list the orders.
						$query = $database->query("select o.order_id, o.date_added, o.date_schedualed, o.date_completed, o.order_issue, o.order_status_id, os.order_status_name, ot.name as order_type_name from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.address_id = '" . $aID . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id order by date_schedualed");
							foreach($database->fetch_array($query) as $result){
								$vars['history_result'][] = $result;
							}
						echo $twig->render('agent/active_addresses_history.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));	
						//HISTORY ENDS	
							
				} elseif ($page_action == 'reschedule_removal') {
					
					//REMOVAL STARTS
					
					$query = $database->query("select o.date_schedualed, o.order_status_id, a.house_number, a.street_name, a.city, s.name as state_name from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a, " . TABLE_STATES . " s where o.address_id = '" . $aID . "' and a.address_id = '" . $aID . "' and a.state_id = s.state_id and order_type_id = '3' limit 1");
					$result = $database->fetch_array($query);
					if (empty($result['date_schedualed'])) {
						$result['date_schedualed'] = time();
					}
                    $dt=$result['date_schedualed'];
                    $order_status_id = $result['order_status_id'];
					$session->php_session_register('current_date_scheduled', $dt);
					$myDate = date('m/d/Y',$dt);
					$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
                    $tomorrow = date('m/d/Y',$tomorrow); 
					
					$vars['dt'] = $dt;
					$vars['house_number'] = $result['house_number'];
					$vars['street_name'] = $result['street_name'];
					$vars['city'] = $result['city'];
					$vars['state_name'] = $result['state_name'];
                    if ($order_status_id >= ORDER_STATUS_SCHEDULED) {
						echo $twig->render('agent/active_addresses_busy.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
					} 
					else { 
						$vars['aID'] = $aID;
						echo $twig->render('agent/active_addresses_reschedule.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
            }
		}
?>