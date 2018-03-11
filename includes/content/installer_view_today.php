<?php
	//This is now tomorrow.
	$user_default = $database->query("select default_order_by,display_view from " . TABLE_USERS . " where user_id = '" . $user->fetch_user_id() . "' limit 1");
	$val_default = $database->fetch_array($user_default);
	$default_order_by = !empty($val_default['default_order_by'])?$val_default['default_order_by']:'1';
	$default_display_view = !empty($val_default['display_view'])?$val_default['display_view']:'overview';
	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get');
	$view_type = tep_fill_variable('view_type', 'get');
	$day_view = tep_fill_variable('day_view', 'get', 'today');
	$order_by = tep_fill_variable('order_by', 'get', $default_order_by);
	$display_view = tep_fill_variable('display_view', 'get', $default_display_view);
	$submit_value = tep_fill_variable('submit_value');
	$accept_jobs = tep_fill_variable('accept_jobs_y');

	// Change Order Status
    $isStatusChange = tep_fill_variable('changeStatus', 'get');
    $orderStatusId = tep_fill_variable('orderStatusId', 'get');
    if($isStatusChange == 'true'){
        $database->query("UPDATE " . TABLE_ORDERS . " SET order_status_id = $orderStatusId WHERE order_id = $oID");
    }

		if (!empty($accept_jobs)) {
			$page_action = 'accept_jobs';
		}
		if ($page_action == 'csv_export') {
			$file = '';
			$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp()));
			$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
			$query = $database->query("select a.house_number, a.city, a.street_name, c.name as county_name, a.zip, otiso.show_order_id as order_column from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) order by order_column, date_schedualed ASC");
				foreach($database->fetch_array($query) as $result){
						if (!empty($file)) {
							$file .= "\n";
						}
					$file .= $result['house_number'].' '.$result['street_name'].','.$result['city'].','.$result['county_name'].','.$result['zip'];
				}
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="RSPC_orders_' . date("n_d_Y", ($midnight_tonight+1)).'.csv"');
			header('Content-Length: '.strlen($file));
			echo $file;
			die();

		} elseif($page_action == 'update_order') {
			//Loop over orders and update the show_order.
			$order_id = tep_fill_variable('order_id', 'post', array());
			$count = count($order_id);
			$n = 0;
				while($n < $count) {
					$show_order = tep_fill_variable('order_'.$order_id[$n], 'post', '1');
						$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " where order_id = '" . $order_id[$n] . "' limit 1");
						$result = $database->fetch_array($query);
							if ($result['count'] > 0) {
								$database->query("update " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " set show_order_id = '" . $show_order . "' where order_id = '" . $order_id[$n] . "' limit 1");
							} else {
								$database->query("insert into " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " (order_id, show_order_id) values ('" . $order_id[$n] . "', '" . $show_order . "')");
							}
					$n++;
				}
		} elseif ($page_action == 'accept_jobs_confirm') {
			$order_id = tep_fill_variable('accepted_jobs', 'post', array());
			$count = count($order_id);
			$n = 0;
				while($n < $count) {
					$query = $database->query("select order_status_id from " . TABLE_ORDERS . " where order_id = '" . $order_id[$n] . "' limit 1");
					$result = $database->fetch_array($query);
						if ($result['order_status_id'] == '1') {
							$database->query("update " . TABLE_ORDERS . " set order_status_id = '2', date_accepted = '" . mktime() . "' where order_id = '" . $order_id[$n] . "' and order_status_id = '1' limit 1");
							$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $order_id[$n] . "', '2', '" . mktime() . "', '0', 'Your order has been scheduled.  You can no longer edit this order.')");
							$check_query = $database->query("select count(installer_id) as count from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $order_id[$n] . "' limit 1");
							$check_result = $database->fetch_array($check_query);
								if ($check_result['count'] > 0) {
									$database->query("update " . TABLE_INSTALLERS_TO_ORDERS . " set installer_id = '" . $user->fetch_user_id() . "' where order_id = '" . $order_id[$n] . "' limit 1");
								} else {
									$database->query("insert into " . TABLE_INSTALLERS_TO_ORDERS . " (installer_id, order_id) values ('" . $user->fetch_user_id() . "', '" . $order_id[$n] . "')");
								}
						}
					$n++;
				}
		} elseif($page_action == 'edit_confirm') {
			//Loop over orders and update the show_order.
			$equipment = tep_fill_variable('equipment', 'post', array());
			$missing = tep_fill_variable('missing', 'post', array());
			$damaged = tep_fill_variable('damaged', 'post', array());
			$install_status = tep_fill_variable('install_status');
			$session->php_session_unregister('install_status');
			$post_type_id = tep_fill_variable('post_type_id');
			$installer_comments = tep_fill_variable('installer_comments');
			$session->php_session_unregister('installer_comments');
			$agent_comments = tep_fill_variable('agent_comments');
			$session->php_session_unregister('agent_comments');
			$query = $database->query("select user_id, address_id, order_type_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
			$result = $database->fetch_array($query);
			$user_id = $result['user_id'];
			$address_id = $result['address_id'];
			$order_type_id = $result['order_type_id'];

			//The best thing here wil be to insert all the equipment as part of the order, sounds stupid but its not, apparently.
				if ($order_type_id == '3') { //Obviously only an removal.
					$address_query = $database->query("select equipment_id, equipment_item_id from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . $address_id . "'");
						while($address_result = $database->fetch_array($address_query)) {
							tep_assign_removal_item_to_order($oID, $address_result['equipment_id'], $address_result['equipment_item_id'], '2');
						}
				}

				if ($install_status == '1') {

						if (($order_type_id == '1') || ($order_type_id == '2')) {
							$status = '3';
						} else {
							$status = '4';
						}
						if ($order_type_id == '1') {
							$post_string = ", post_type_id = '" . $post_type_id . "'";
						} else {
							$post_string = '';
						}
					//If its a 1 then assign the post with the order.  Let the system handle it from there.
						if (($order_type_id == '1') && ($install_status == '1')) {
							tep_assign_post_to_order($oID, $post_type_id, '2', fetch_address_zip4($address_id));
							$equipment[] = $post_type_id;
							//tep_assign_equipment_to_order($oID, $group_id, $values[$n], 1, fetch_address_zip4($this->fetch_data_item('address_id', '')), $this->fetch_user_id(), $this->fetch_data_item('address_id', ''));
						}

					$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $installer_comments  . "', status = '" . $status . "'" . $post_string . " where address_id = '" . $result['address_id'] . "' limit 1");

					$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
					$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
					$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $oID . "', '3', '" . mktime() . "', '1', 'Your order has now been completed successfully.')");
					$equipment_string = '';
					//Now update the equipment.
					//Used only for removal deposit.
					$removal_issue = false;

					$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
						foreach($database->fetch_array($query) as $result){
							$status = '';
							//Available = 0
							//Returned = 3 - not used
							//Installed = 2
							//Pending = 1
							//Missing = 4
							//Damaged = 5
							//Not installed = 0;
							//Now prepare the database for the email.
								if (!empty($equipment_string)) {
									$equipment_string .= '<br>';
								}
								if (in_array($result['equipment_id'], $equipment)) {
									//Its here so it happened.


									if ($result['method_id'] == '1') {

										//Installation so mark it as installed.
										$status = '2';
										$equipment_string .= $result['equipment_name'] . ' was successfully installed.';
										$database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $result['equipment_id'] . "', '" . $result['equipment_item_id'] . "', '2', '" . $address_id . "')");
									} else {

										//Removal so mark it as removed.
										$status = '0';
										$database->query("update " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " set equipment_status_id = '0' where address_id = '" . $address_id . "' and equipment_id = '" . $result['equipment_id'] . "' limit 1");

										$equipment_string .= $result['equipment_name'] . ' was successfully removed.';
									}
								} else {
									//Its here so it didn't.
									if ($result['method_id'] == '1') {
										//Instalation.  Means something strange.
										$status = '0';
										$equipment_string .= $result['equipment_name'] . ' was unable to be installed.  Please view the comments below.';
									} else {
										//Removal, means either missing or damaged.
											if (in_array($result['equipment_id'], $missing)) {
												//Its missing, ie. stolen.
												$status = '4';
												$removal_issue = true;
												$equipment_string .= $result['equipment_name'] . ' was not at the property and has been assumed stolen.';
											} elseif (in_array($result['equipment_id'], $damaged)) {
												//Its damages, ie. damaged?
												$status = '5';
												$removal_issue = true;
												$equipment_string .= $result['equipment_name'] . ' was found damaged at the property.';
											} else {
												//Shouldnt be here.  Maybe set an error later.
												$status = '2';
												$removal_issue = true;
												$equipment_string .= $result['equipment_name'] . ' was unsuccessfully removed.';
											}
									}
								}
							//We now have the details and status.  We need to set both the equipment to order and the equipment table.
							$database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $result['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
							$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
						}
				//Now prepare the email for the customer.
					$customer_info_query = $database->query("select u.user_id, u.email_address, ud.firstname, ud.lastname, a.house_number, a.street_name, c.name as county_name, a.city, a.zip, s.name as state_name, ot.name as order_type_name from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_COUNTYS . " c, " . TABLE_STATES . " s, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where o.order_id = '" .$oID . "' and o.address_id = a.address_id and a.county_id = c.county_id and o.user_id = ud.user_id and ud.user_id = u.user_id and o.order_type_id = ot.order_type_id and a.state_id = s.state_id limit 1");
					$customer_info_result = $database->fetch_array($customer_info_query);
					//Time to email.
					$equipment_string = tep_create_completed_order_equipment_string($oID, true, $equipment, array(), tep_fill_variable('damaged', 'post', array()), tep_fill_variable('missing', 'post', array()));

					$email_template = new email_template('installer_view_completed');
					$email_template->load_email_template();
					$email_template->set_email_template_variable('HOUSE_NUMBER', $customer_info_result['house_number']);
					$email_template->set_email_template_variable('STREET_NAME', $customer_info_result['street_name']);
					$email_template->set_email_template_variable('CITY', $customer_info_result['city']);
					$email_template->set_email_template_variable('ORDER_TYPE', $customer_info_result['order_type_name']);
					$email_template->set_email_template_variable('STATE_NAME', $customer_info_result['state_name']);
					$email_template->set_email_template_variable('COUNTY_NAME', $customer_info_result['county_name']);
					$email_template->set_email_template_variable('AGENT_COMMENTS', $agent_comments);
					$email_template->set_email_template_variable('DATE_COMPLETED', date("n/d/Y", mktime()));

					$email_template->set_email_template_variable('EQUIPMENT', $equipment_string);
					$email_template->parse_template();
					//$email_template->send_email($customer_info_result['email_address'], $customer_info_result['firstname'].','.$customer_info_result['lastname']);

					$extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $customer_info_result['user_id'] . "' and email_status = '1'");
						foreach($database->fetch_array($extra_query) as $extra_result){
							//$email_template->send_email($extra_result['email_address'],$result['firstname'].' '.$result['lastname']);
						}
					//$email_template->send_email('jon@onemall.co.nz',$result['firstname'].' '.$result['lastname']);

				} else {
					//Should only happen if install.
						if ($order_type_id == '1') {
							$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_type_id = '2', date_completed = '" . mktime() . "' where order_id = '" . $oID . "' limit 1");
							$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $installer_comments  . "', status = '0' where address_id = '" . $result['address_id'] . "' limit 1");
							$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
							$canceled_query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '3' limit 1");
							$canceled_result = $database->fetch_array($canceled_query);
							$database->query("update " . TABLE_ORDERS . " set order_status_id = '4' where order_id = '" . $canceled_result['order_id'] . "' limit 1");
							$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $canceled_result['order_id'] . "', '4', '" . mktime() . "', '1', 'This order has been canceled as the post was not able to be installed.  Please read the relevent installation for more information.')");
							$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $oID . "', '3', '" . mktime() . "', '1', 'Your order was not able to be completed.  " . $agent_comments . "')");
						} elseif ($order_type_id == '3') {

						}
					$customer_info_query = $database->query("select u.user_id, u.email_address, ud.firstname, ud.lastname, a.house_number, a.street_name, c.name as county_name, a.city, a.zip, s.name as state_name, ot.name as order_type_name from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_COUNTYS . " c, " . TABLE_STATES . " s, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where o.order_id = '" .$oID . "' and o.address_id = a.address_id and a.county_id = c.county_id and o.user_id = ud.user_id and ud.user_id = u.user_id and o.order_type_id = ot.order_type_id and a.state_id = s.state_id limit 1");
					$customer_info_result = $database->fetch_array($customer_info_query);

					//Now prepare the email for the customer.
					$equipment_string = tep_create_completed_removal_equipment_string($oID, true, $equipment, array(), tep_fill_variable('damaged', 'post', array()), tep_fill_variable('missing', 'post', array()));

					$email_template = new email_template('installer_view_completed');
					$email_template->load_email_template();
					$email_template->set_email_template_variable('HOUSE_NUMBER', $customer_info_result['house_number']);
					$email_template->set_email_template_variable('STREET_NAME', $customer_info_result['street_name']);
					$email_template->set_email_template_variable('CITY', $customer_info_result['city']);
					$email_template->set_email_template_variable('ORDER_TYPE', $customer_info_result['order_type_name']);
					$email_template->set_email_template_variable('STATE_NAME', $customer_info_result['state_name']);
					$email_template->set_email_template_variable('COUNTY_NAME', $customer_info_result['county_name']);
					$email_template->set_email_template_variable('AGENT_COMMENTS', $agent_comments);
					$email_template->set_email_template_variable('DATE_COMPLETED', date("n/d/Y", mktime()));

					$email_template->set_email_template_variable('EQUIPMENT', $equipment_string);
					$email_template->parse_template();
					//$email_template->send_email($result['email_address'], $customer_info_result['firstname'].','.$customer_info_result['lastname']);

					$extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $customer_info_result['user_id'] . "' and email_status = '1'");
						foreach($database->fetch_array($extra_query) as $extra_result){
							//$email_template->send_email($extra_result['email_address'],$result['firstname'].' '.$result['lastname']);
						}
					//$email_template->send_email('jon@onemall.co.nz',$result['firstname'].' '.$result['lastname']);
				}
			//Work out the installer payment and any extra user payments or re-embursements.

			//Check if it was a special condition item and if so was the condition met, if not then re-imburse.
			$today = mktime(0, 0, 0, date("n", mktime()), date("d", mktime()), date("Y", mktime()));
			$tonight = ($today + (60*60*24));

			$query = $database->query("select user_id, date_schedualed, special_conditions from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
			$result = $database->fetch_array($query);
				if (!empty($result['special_conditions'])) {
					$explode = explode('|', $result['special_conditions']);
					$new_array = array();
					$refund_array = array();
						if (in_array('rush_install')) {
							if (($result['date_schedualed'] >= $today) && ($result['date_schedualed'] < $tonight)) {
								$new_array[] = 'rush_install';
							} else {
								$refund_array[] = 'rush_install';
							}
						}
						if (in_array('saturday_install')) {
							if ((date("w", mktime()) == 6) && ($result['date_schedualed'] >=  $today) && ($result['date_schedualed'] < $tonight)) {
								$new_array[] = 'saturday_install';
							} else {
								$refund_array[] = 'saturday_install';
							}
						}
					$new_string = '';
						for ($n = 0, $m = count($new_array); $n < $m; $n++) {
								if (!empty($new_string)) {
									$new_string .= '|';
								}
							$new_string .= $new_array[$n];
						}
					$database->query("update " . TABLE_ORDERS . " set special_conditions = '" . $new_string . "' where order_id = '" . $oID . "' limit 1");

					//Now refund any oweing.
						if (!empty($refund_array)) {
							$account = new account($user_id);
							$refund_amount = tep_fetch_extra_cost($refund_array);
							$refund_reason ='Refund for ' . tep_fetch_extra_cost_string($refund_array);
							$account->set_credit_amount($refund_amount, 'Refund for Services', $refund_reason, $oID, 'cancel');
						}
				}
			//Now if this was a return and everything was fine we return the deposit amount.
				if ($order_type_id == '3') {
					if (!$removal_issue) {
						$query = $database->query("select deposit_cost from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
						$result = $database->fetch_array($query);
							if (!empty($result['deposit_cost'])) {
								$u_query = $database->query("select deposit_remaining_count from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
								$u_result = $database->fetch_array($u_query);
									if ($u_result['deposit_remaining_count'] > 0) {
										$database->query("update " . TABLE_USERS . " set deposit_remaining_count = '" . ($u_result['deposit_remaining_count'] - 1) . "' where user_id = '" . $user_id . "' limit 1");
											if (($u_result['deposit_remaining_count'] - 1) == 0) {
												$database->query("update " . TABLE_USERS . " set require_deposit = '0' where user_id = '" . $user_id . "' limit 1");
											}
									}
								$account = new account($user_id);

								$account->set_credit_amount($result['deposit_cost'], 'Refund for Deposit', 'The items have been successfulyl removed from the property.  Your deposit has now been credited to your account and can be used for credit on future orders or you can request a refund.', $oID, 'refund');
							}
					}
				}

			//For this we need the following details.
			//$order_id, $order_type_id, $service_area_id, $equipment_array, $special_array
			$query = $database->query("select date_schedualed from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
			$result = $database->fetch_array($query);

			$extra_payment = false;
			$today = mktime(0, 0, 0, date("n", mktime()), date("d", mktime()), date("Y", mktime()));
			$tonight = ($today + (60*60*24));
				if (($result['date_schedualed'] < $tonight) && ($result['date_schedualed'] >= $today)) {
					//Its definately the right day.
					$extra_payment = true;
				}

			$order_id = $oID;
			$installer_payments = new installer_payments($user->fetch_user_id(), true);
			$installer_payments->insert_installer_payment($order_id, $extra_payment);


			header('Location: ' . FILENAME_INSTALLER_VIEW_TODAY);
			die();
		}

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('installer_view_current')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('installer_view_current'); ?></td>
	</tr>
	<tr>
		<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
	</tr>
	<?php
		}

	$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp()));
	$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
	$midnight_future_plus_1 = ($midnight_tonight + ((60*60*24) * 2));
		//If today is a sunday then just don't show anything.
		//if (date("w", ($midnight_tonight+1)) == 0) {
			//$midnight_tonight += (60*60*24);
			//$midnight_future += (60*60*24);
		//}

		if (($page_action != 'edit') && ($page_action != 'preview')) {
	?>
	<tr>
		<td class="main"><b>Jobs for <?php echo date("l dS \of F Y", ($midnight_future-1)); ?></b></td>
	</tr>

	<tr>
		<td class="main">
			<table width="100%" cellspacing="2" cellpadding="2">
				<tr>
					<td class="main">Installations: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '1', '', '', false); ?></td>
					<td class="main">Service Calls: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '2', '', '', false); ?></td>
					<td class="main">Removals: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '3', '', '', false); ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
		<?php
			if (($page_action != 'edit') && ($page_action != 'preview')) {
				$where = '';

				//We only want the orders for the specifed day.
		?>
						<?php
							if ($display_view == 'detailed') {
								if ($page_action == 'accept_jobs') {
								?>
									<form action="<?php echo FILENAME_INSTALLER_VIEW_TODAY; ?>?page_action=accept_jobs_confirm&display_view=<?php echo $display_view; ?>&order_by=<?php echo $order_by; ?>" method="post">
								<?php
								} else {
								?>
									<form action="<?php echo FILENAME_INSTALLER_VIEW_TODAY; ?>?page_action=update_order&day_view=<?php echo $day_view; ?>&display_view=<?php echo $display_view; ?>&order_by=1" method="post">
								<?php
								}
							}
						?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">

					<tr>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxHeading" align="right">Accept</td>
						<?php
							}
						?>
						<td class="pageBoxHeading">Date</td>

						<td class="pageBoxHeading">Type</td>
						<td class="pageBoxHeading">Job Status</td>
						<?php
							if ($display_view == 'detailed') {
						?>

						<td class="pageBoxHeading">Address</td>
						<td class="pageBoxHeading">Service Level</td>

						<?php
							} else {
						?>
						<td class="pageBoxHeading">House #</td>
						<td class="pageBoxHeading">Street</td>
						<td class="pageBoxHeading">City</td>
						<?php
							}
						?>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxHeading" align="right">Order</td>
						<?php
							}
						?>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$extra = '';
					$accepted_jobs = tep_fill_variable('accepted_jobs', 'post', array());
						if ($display_view == 'detailed') {
							//Fetch extra information,
							$extra = ', otiso.show_order_id, a.house_number, a.street_name,  a.cross_street_directions, a.number_of_posts, a.address_post_allowed, a.city, a.zip, s.name as state_name, c.name as county_name,sld.name as service_level_name, od.special_instructions, od.admin_comments';
						} else {
							$extra = ', a.house_number, a.street_name, a.city';
						}
					//o.date_schedualed >= '" . $midnight_tonight . "' and
					$order_column = 'o.date_schedualed';
						if ($order_by == '1') {
							$order_column = 'order_column';
						} elseif ($order_by == '2') {
							$order_column = 'o.date_schedualed';
						} elseif ($order_by == '3') {
							$order_column = 'o.date_added';
						} elseif ($order_by == '4') {
							$order_column = 'o.date_accepted';
						} elseif ($order_by == '5') {
							$order_column = 'a.house_number';
						} elseif ($order_by == '6') {
							$order_column = 'LTRIM(a.street_name)';
						}
					$query = $database->query("select o.order_id, o.date_schedualed, o.order_status_id, o.order_type_id, os.order_status_name, ot.name as order_type_name, otiso.show_order_id as order_column, a.zip4".$extra." from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future_plus_1 . "' and o.order_status_id < '3' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.order_status_id = os.order_status_id and o.service_level_id = sld.service_level_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id, otiso.show_order_id, sld.name, od.special_instructions, od.admin_comments order by " .  $order_column . ' ASC');
					//					$query = $database->query("select o.order_id, o.date_schedualed, o.order_status_id, os.order_status_name, ot.name as order_type_name, otiso.show_order_id as order_column, a.zip4".$extra." from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on(((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ia.installation_area_id = ica.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id < '3' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and a.state_id = s.state_id and a.county_id = c.county_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) order by " . (($display_view == 'detailed') ? 'order_column' : 'date_schedualed'));
					$order_default_order = 1; 
						$orderData = array();
                    foreach($database->fetch_array($query) as $result){
						if($result['show_order_id'] == 0){
							$orderData[$result['date_schedualed']] = $result;
						}else{
							$orderData[$result['show_order_id']] = $result; 
						}
						
					}
					ksort($orderData);
				
				
					$loop = 0; 
					foreach($orderData as $result){
						//while($result = $database->fetch_array($query)) {
							$loop++;
								if (($display_view == 'detailed') && ($result['show_order_id'] == NULL)) {
									$result['show_order_id'] = $order_default_order;
									$order_default_order++;
								}
								/*
								if (!empty($accepted_jobs)) {
									if (in_array($result['order_id'], $accepted_jobs) || ($result['order_status_id'] == '2')) {
										$accepted = true;
									} else {
										$accepted = false;
									}
								} else {
									$accepted = true;
								}*/

								if (!empty($accepted_jobs)) {

									if (in_array($result['order_id'], $accepted_jobs) ) {
										$accepted = true;
									}else {
										$accepted = false;
									}
								}else if($result['order_status_id'] == '2'){
									$accepted = true;
								}
								else {
									$accepted = false;
								}

								if($accepted == true){
									$acceptedBoxIsChecked = 'CHECKED';
								}else{
									$acceptedBoxIsChecked = '';
								}
				?>
					<tr>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxContent" align="right" valign="top"><input type="checkbox" name="accepted_jobs[]" value="<?php echo $result['order_id']; ?>" <?php echo $acceptedBoxIsChecked; ?>/></td>
						<?php
							}
						?>
						<td class="pageBoxContent" valign="top"><?php echo date("n/d/Y", $result['date_schedualed']); ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['order_type_name']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['order_status_name']; ?></td>
						<?php
							if ($display_view == 'detailed') {
						?>

						<td class="pageBoxContent" valign="top"><?php echo $result['house_number'].' ' .$result['street_name'].'<br>'.$result['city'].' '.$result['state_name'].' '.$result['zip'] . (($result['address_post_allowed'] == '0') ? '<br><b>Posts may not be allowed at this address.</b>' : ''); ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['service_level_name']; ?></td>
						<?php
							} else {
						?>
						<td class="pageBoxContent" valign="top" align="right"><?php echo $result['house_number']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['street_name']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['city']; ?></td>
						<?php
							}
						?>

						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxContent" align="right" valign="top"><input type="hidden" name="order_id[]" value="<?php echo $result['order_id']; ?>" /><input type="text" size="1" name="order_<?php echo $result['order_id']; ?>" value="<?php echo $result['show_order_id']; ?>" /></td>
						<?php
							}
						?>
						<td class="pageBoxContent" align="right" valign="top">

						<?php
							$yes_mark = false;
						if($result['order_status_id'] != '2'){
							//echo 'here';
							$queryC = $database->query("select  c.value from " . TABLE_CONFIGURATION . " c where c.key_name	 = 'SAME_DAY_ACCEPT_ORDER'");
							$items_resultC = $database->fetch_array($queryC);
							$acceptance = trim($items_resultC['value']);
							if(strtolower($acceptance) == 'yes'){
							$yes_mark = true;
							}
						}
						/*echo '<a href="'.FILENAME_INSTALLER_VIEW_TODAY.'?changeStatus=true&orderStatusId=5&oID='. $result['order_id'].'&page='.FILENAME_INSTALLER_VIEW_TODAY.'">On Hold</a> | ';*/
    			echo (($result['order_status_id'] == '2' || $yes_mark==true) ? '<a href="'.FILENAME_INSTALLER_MARK_COMPLETE.'?page_action=edit&oID='. $result['order_id'].'">Mark Completed</a>' : '<a href="'.FILENAME_INSTALLER_VIEW_DETAILS.'?oID='. $result['order_id'].'&page='.FILENAME_INSTALLER_VIEW_TODAY . '">View Details</a>');
						/*
						if($result['order_type_id'] == 1){
                            if($result['date_schedualed'] <= $midnight_tonight && ($result['order_status_id'] == 2 || $result['order_status_id'] == 5)){
                                echo (($result['order_status_id'] == '2' || $yes_mark==true) ? '<a href="'.FILENAME_INSTALLER_MARK_COMPLETE.'?page_action=edit&oID='. $result['order_id'].'">Mark Completed</a>' : '<a href="'.FILENAME_INSTALLER_VIEW_DETAILS.'?oID='. $result['order_id'].'&page='.FILENAME_INSTALLER_VIEW_TODAY . '">View Details</a>');
                            }
                        }else{
                            echo (($result['order_status_id'] == '2' || $yes_mark==true) ? '<a href="'.FILENAME_INSTALLER_MARK_COMPLETE.'?page_action=edit&oID='. $result['order_id'].'">Mark Completed</a>' : '<a href="'.FILENAME_INSTALLER_VIEW_DETAILS.'?oID='. $result['order_id'].'&page='.FILENAME_INSTALLER_VIEW_TODAY . '">View Details</a>');
                        }
            */
						?>
						</td>

						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
						}
						if ($loop == 0) {
							?>
							<tr>
								<td colspan="<?php echo (($display_view == 'detailed') ? '9' : '6'); ?>" class="main">There are currently no orders assigned to you for Today.</td>
							</tr>
							<?php
						}
			?>
			</table>
			<?php
				} else {
					if ($page_action == 'edit') {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="0">
							<?php
								//Now we work out what type of order it was and show them the relevent options.
								//We also get the address from here to make sure we have the correct order.
								$order_data_query = $database->query("select o.order_id, o.date_schedualed, o.order_type_id, a.house_number, a.street_name, a.city, a.zip, a.zip4, a.number_of_posts, a.cross_street_directions, ot.name as order_type_name, s.name as state_name, c.name as county_name, ud.firstname, ud.lastname from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_ADDRESSES . " a, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_ORDER_TYPES . " ot, " . TABLE_COUNTYS . " c, " . TABLE_STATES . " s where o.order_id = '" . $oID . "' and o.order_id = od.order_id and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and a.state_id = s.state_id and a.county_id = c.county_id and o.user_id = ud.user_id limit 1");
								$order_data_result = $database->fetch_array($order_data_query);
								$install_status = tep_fill_variable('install_status', 'session', '1');
								$installer_comments = tep_fill_variable('installer_comments', 'session');
								$agent_comments = tep_fill_variable('agent_comments', 'session');
							?>
							<form action="<?php echo PAGE_URL; ?>?page_action=preview&oID=<?php echo $oID; ?>" method="post">
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
								<td class="pageBoxContent" colspan="2">This job <input name="install_status" value="1" type="radio"<?php echo (($install_status == '1') ? ' CHECKED' : ''); ?> />&nbsp;<b>Was</b>&nbsp;/<input name="install_status" value="0" type="radio"<?php echo (($install_status == '0') ? ' CHECKED' : ''); ?> />&nbsp;<b>Was Not</b>&nbsp; completed successfully.</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="pageBoxContent" valign="top">Agent: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['firstname'].' '.$order_data_result['lastname']; ?></td>
										</tr>
										<tr>
											<td class="pageBoxContent" valign="top">Job Type: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['order_type_name']; ?></td>
										</tr>
										<tr>
											<td class="pageBoxContent" valign="top">Address: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['house_number'] .' ' . $order_data_result['street_name'].'<br>'.$order_data_result['city'].'<br>'.$order_data_result['state_name'] . '<br>'.$order_data_result['zip4']; ?></td>
										</tr>

									</table>
								</td>
							</tr>

							<?php
								if ($order_data_result['order_type_id'] < 3) {
									$equipment_array = tep_fill_variable('equipment', 'session', array());
									$equipment_string = tep_create_completed_order_equipment_string($oID, false, $equipment_array);
										if (!empty($equipment_string)) {
											?>
											<tr>
												<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Equipment</b></td>
											</tr>
											<tr>
												<td class="pageBoxContent">Please check to make sure that all the following equipment was either installed or removed.</td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="main"><?php echo $equipment_string; ?></td>
											</tr>
											<?php
										}
								} else {
									$equipment_string = tep_create_completed_removal_equipment_string($oID, false, tep_fill_variable('equipment', 'session', array()), array(), tep_fill_variable('damaged', 'session', array()), tep_fill_variable('missing', 'session', array()));
										if (!empty($equipment_string)) {
											?>
											<tr>
												<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Equipment</b></td>
											</tr>
											<tr>
												<td class="pageBoxContent">Please check to make sure that all the following equipment was removed from the property.</td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="main"><?php echo $equipment_string; ?></td>
											</tr>
											<?php
										}
								}
							?>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<?php
								//Build the checkoff list.
							?>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td class="pageBoxContent"><b>Comments</b></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Please make any comments below that need to be stored with the address.  This will be kept with the address and will be shown to the installer on later jobs at this address.</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td width="100%"><textarea name="installer_comments" cols="30" rows="7"><?php echo $installer_comments; ?></textarea></td>
							</tr>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Please add any comments below that need to be sent to the agent regarding this job.</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td width="100%"><textarea name="agent_comments" cols="30" rows="7"><?php echo $agent_comments; ?></textarea></td>
							</tr>
						</table>
						<?php
					} elseif ($page_action == 'preview') {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="0">
							<?php
								//Now we work out what type of order it was and show them the relevent options.
								//We also get the address from here to make sure we have the correct order.
								$order_data_query = $database->query("select o.order_id, o.date_schedualed, o.order_type_id, a.house_number, a.street_name, a.city, a.zip, a.zip4, a.number_of_posts, a.cross_street_directions, ot.name as order_type_name, s.name as state_name, c.name as county_name, ud.firstname, ud.lastname from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_ADDRESSES . " a, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_ORDER_TYPES . " ot left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) where o.order_id = '" . $oID . "' and o.order_id = od.order_id and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.user_id = ud.user_id limit 1");
								$order_data_result = $database->fetch_array($order_data_query);

								$install_status = tep_fill_variable('install_status', 'post', 0);
								$session->php_session_register('install_status', $install_status);
							?>
							<form action="<?php echo PAGE_URL; ?>?oID=<?php echo $oID; ?>&page_action=edit_confirm" method="post">
							<input type="hidden" name="install_status" value="<?php echo $install_status; ?>" />
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
								<td class="pageBoxContent" colspan="2">This job <b><?php echo (($install_status == 1) ? 'Was' : 'Was Not'); ?></b> completed successfully.</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="pageBoxContent" valign="top">Agent: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['firstname'].' '.$order_data_result['lastname']; ?></td>
										</tr>
										<tr>
											<td class="pageBoxContent" valign="top">Job Type: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['order_type_name']; ?></td>
										</tr>
										<tr>
											<td class="pageBoxContent" valign="top">Address: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['house_number'] .' ' . $order_data_result['street_name'].'<br>'.$order_data_result['city'].'<br>'.$order_data_result['state_name'].'<br>'.$order_data_result['zip4']; ?></td>
										</tr>
									</table>
								</td>
							</tr>

							<?php
								if (($order_data_result['order_type_id'] == '1') && ($install_status == '1')) {
							?>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td class="pageBoxContent"><b>Posts</b></td>
							</tr>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td class="pageBoxContent">You installed <?php echo tep_get_equipment_name('post_type_id', DEFAULT_INSTALL_POST_TYPE, '', $order_data_result['zip4']); ?> Posts.</td>
							</tr>
							<?php
								}
							?>


							<?php
								if ($order_data_result['order_type_id'] < 3) {
									$equipment_string = tep_create_completed_order_equipment_string($oID, true, tep_fill_variable('equipment', 'post', array()), array(), tep_fill_variable('damaged', 'post', array()), tep_fill_variable('missing', 'post', array()));
									$equipment = tep_fill_variable('equipment');

									$session->php_session_register('equipment', $equipment);
										if (!empty($equipment_string)) {
											?>
											<tr>
												<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Equipment</b></td>
											</tr>
											<tr>
												<td class="pageBoxContent">Please check to make sure that all the following equipment was either installed or removed.</td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="main"><?php echo $equipment_string; ?></td>
											</tr>
											<?php
										}
								} else {
									$equipment_string = tep_create_completed_removal_equipment_string($oID, true, tep_fill_variable('equipment', 'post', array()), array(), tep_fill_variable('damaged', 'post', array()), tep_fill_variable('missing', 'post', array()));
									$equipment = tep_fill_variable('equipment');
									$missing = tep_fill_variable('missing');
									$damaged = tep_fill_variable('damaged');

									$session->php_session_register('equipment', $equipment);
									$session->php_session_register('missing', $missing);
									$session->php_session_register('damaged', $damaged);
										if (!empty($equipment_string)) {
											?>
											<tr>
												<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
											</tr>
											<tr>
												<td class="pageBoxContent"><b>Equipment</b></td>
											</tr>
											<tr>
												<td class="pageBoxContent">Please check to make sure that all the following equipment was removed from the property.</td>
											</tr>
											<tr>
												<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
											</tr>
											<tr>
												<td class="main"><?php echo $equipment_string; ?></td>
											</tr>
											<?php
										}
								}

							?>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<?php
								//Build the checkoff list.
							?>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td class="pageBoxContent"><b>Comments</b></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Address Comments:</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td width="100%" class="pageBoxContent"><?php $comments = tep_fill_variable('installer_comments'); echo ((!empty($comments)) ? $comments : 'None'); ?></td>
							</tr>
							<input type="hidden" name="installer_comments" value="<?php echo addslashes($comments); ?>" />
							<?php
								$session->php_session_register('installer_comments', $comments);
							?>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Agent Comments:</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td width="100%" class="pageBoxContent"><?php $comments = tep_fill_variable('agent_comments'); echo ((!empty($comments)) ? $comments : 'None'); ?></td>
							</tr>
							<input type="hidden" name="agent_comments" value="<?php echo addslashes($comments); ?>" />
							<?php
								$session->php_session_register('agent_comments', $comments);
							?>
							<tr>
								<td height="25"><img src="images/pixel_trans.gif" height="25" width="1" /></td>
							</tr>
							<tr>
								<td align="right"><a href="<?php echo PAGE_URL; ?>?page_action=edit&oID=<?php echo $oID; ?>"><?php echo tep_create_button_link('back', 'Back'); ?></a></td>
							</tr>
						</table>
						<?php
					}
				}
			?>
			<?php
							if (($display_view == 'detailed') && ($page_action != 'accept_jobs') && ($page_action != 'edit') && ($page_action != 'preview')) {
						?>
						<table width="100%" cellspacing="0" cellpadding="0">

							<tr>

								<td><?php
									//Its today so should always be able to update the status.
									//$current_hour = date("H.i", mktime());
									//$limit_time = str_replace(':', '.', INSTALLER_MARK_SCHEDUALED_TIME);
									//Extra little check here in case its saturday, obviously can't do it till tomorrow.
										//if ((date("w", ($midnight_tonight+1)) != 0) ) {
											echo tep_create_button_submit('accept_jobs', 'Accept Jobs', 'name="accept_jobs"');
										//}
								?></td>
								<td align="right"><?php echo tep_create_button_submit('update_job_order', 'Update Order', ' name="submit_value"'); ?></td>
							</tr>
						</table>
							</form>
						<?php
							}
						?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<table width="250" cellspacing="0" celpadding="0" class="pageBox">
				<?php
					if ($page_action == 'accept_jobs') {
				?>
				<tr>
					<td class="pageBoxContent">Are you sure you want to accept these jobs?  This action can not be undone and will schedule all unscheduled jobs for <?php echo ucfirst($day_view); ?>.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
				</tr>

				<tr>
					<td width="100%">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left"><?php echo tep_create_button_submit('confirm_accept', 'Confirm Accept'); ?></td>
								<td align="right"><a href="<?php echo FILENAME_INSTALLER_VIEW_TODAY; ?>?display_view=detailed&order_by=<?php echo $order_by; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></td>
							</tr>
						</table>
				</tr>
				</form>


				<?php
				} elseif ($page_action == 'edit') {
			?>
			<td width="250" valign="top">
				<table width="250" cellspacing="0" celpadding="0" class="pageBox">
					<tr>
						<td class="pageBoxContent">Please select the required options and make the appropriate comments on this job.  When you are done press the "Update Job" button below or pres the "Cancel" button at any time to cancel the changes.</td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td align="left"><?php echo tep_create_button_submit('next_step_update_job', 'Next Step - Update Job'); ?></form></td>
									<td align="right"><form action="<?php echo FILENAME_INSTALLER_VIEW_TODAY; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<?php
				} elseif ($page_action == 'preview') {
			?>
			<td width="250" valign="top">
				<table width="250" cellspacing="0" celpadding="0" class="pageBox">
					<tr>
						<td class="pageBoxContent">Please confirm these details are correct.  When you press confirm below the agent will be notified and this order will be marked as complete .</td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td align="left"><?php echo tep_repost_variables($_POST); ?><?php echo tep_create_button_submit('update_job', 'Update Job'); ?></form></td>
									<td align="right"><form action="<?php echo FILENAME_INSTALLER_VIEW_TODAY; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>

				<?php
					} else {
				?>
				<tr>
					<td class="pageBoxContent">&PAGE_TEXT</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
				</tr>
				<tr>
					<td width="100%"><HR></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
				</tr>
				<tr>
					<td width="100%">
						<table width="100%" cellspacing="0" cellpadding="0">
						<?php
							//Show items that are both.
						?>
						<form action="<?php echo FILENAME_INSTALLER_VIEW_TODAY; ?>" method="get">
						<tr>
							<td class="pageBoxContent">Show View: </td>
							<td class="pageBoxContent"><?php echo tep_draw_detailed_overview_pulldown('display_view', $display_view, ' onchange="this.form.submit();"'); ?></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Order By: </td>
							<td class="pageBoxContent"><?php echo tep_generate_pulldown_menu('order_by', array(array('id' => '1', 'name' => 'Order'), array('id' => '2', 'name' => 'Date Scheduled'), array('id' => '3', 'name' => 'Date Ordered'), array('id' => '4', 'name' => 'Date Accepted'), array('id' => '5', 'name' => 'House Number'), array('id' => '6', 'name' => 'Street Name')), $order_by, ' onchange="this.form.submit();"'); ?></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td colspan="2" width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="right"><?php echo tep_create_button_submit('update', 'Update'); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						</form>
						<?php
							if ($display_view == 'detailed') {
								//Show options for detailed.
						/*?>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td colspan="2" align="right" class="main"><a href="<?php echo FILENAME_INSTALLER_VIEW_PRINTABLE; ?>?display_view=<?php echo $display_view; ?>&order_by=<?php echo $order_by; ?>&day_view=today" target="_blank">Show Printable Jobsheet</a><br /><br /><a href="<?php echo FILENAME_INSTALLER_VIEW_PRINTABLE_EQUIPMENT; ?>?display_view=<?php echo $display_view; ?>&order_by=<?php echo $order_by; ?>&day_view=today" target="_blank">Show Printable Equipment Sheet</a><br /><br /><a href="<?php echo FILENAME_INSTALLER_VIEW_TODAY; ?>?page_action=csv_export">Export as CSV</a></td>
						</tr>
						<?php*/
							} else {
								//Show options for overview.
						?>

						<?php
							}
						?>
						</table>
					</td>
				</tr>
				<?php
					}
				?>
			</table>
		</td>
	</tr>
</table>
