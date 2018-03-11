<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get');
	
		if($page_action == 'edit_confirm') {
			//Loop over orders and update the show_order.
			$equipment = tep_fill_variable('equipment', 'post', array());
			$missing = tep_fill_variable('missing', 'post', array());
			$damaged = tep_fill_variable('damaged', 'post', array());
			$install_status = tep_fill_variable('install_status');
			$post_type_id = tep_fill_variable('post_type_id');
			$installer_comments = tep_fill_variable('installer_comments');
			$agent_comments = tep_fill_variable('agent_comments');
			$query = $database->query("select user_id, placed_by, address_id, order_type_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
			$result = $database->fetch_array($query);
            $user_id = $result['user_id'];
			$placed_id = $result['placed_by'];
			$address_id = $result['address_id'];
			$order_type_id = $result['order_type_id'];
		
			//The best thing here wil be to insert all the equipment as part of the order, sounds stupid but its not, apparently.
			$address_query = $database->query("select equipment_id, equipment_item_id from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . $address_id . "'");
				while($address_result = $database->fetch_array($address_query)) {
					tep_assign_removal_item_to_order($oID, $address_result['equipment_id'], $address_result['equipment_item_id'], '2');
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
            $last_modified_by = tep_fill_variable('user_id', 'session', 0);
			$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $installer_comments  . "', status = '" . $status . "'" . $post_string . " where address_id = '" . $result['address_id'] . "' limit 1");
						
			$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', date_completed = '" . mktime() . "', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where order_id = '" . $oID . "' limit 1");
			$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
			$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $oID . "', '3', '" . mktime() . "', '1', 'Your order has now been completed successfully.')");
			$equipment_string = '';
			//Now update the equipment.
			//Used only for removal deposit.
			$removal_issue = false;
					
			$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
			while($result = $database->fetch_array($query)) {
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
			$equipment_string = tep_create_completed_order_equipment_string($oID, true, tep_fill_variable('equipment', 'post', array()), array(), tep_fill_variable('damaged', 'post', array()), tep_fill_variable('missing', 'post', array()));
										
            $email_template = new email_template('installer_view_completed');
			$email_template->load_email_template();
            
        $email_template->set_email_template_variable('ADMIN_LABEL','');                 
        $email_template->set_email_template_variable('ADMIN_INFO','');                 
        $admin_string = "";
      if ($placed_id != $user_id) {
        $aom_query = $database->query("select u.user_id, u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $placed_id . "' and u.user_id = ud.user_id limit 1");
        $aom_result = $database->fetch_array($aom_query);
        $aom_name = $aom_result['firstname'].' '.$aom_result['lastname'];
        $aom_email = $aom_result['email_address'];
        $email_template->set_email_template_variable('AOM_NAME',$aom_result['firstname'].' '.$aom_result['lastname']);                 
        $email_template->set_email_template_variable('AOM_LABEL','AOM Name: ');                 
      } else {
        $aom_name = "";
        $aom_email = "";
        $email_template->set_email_template_variable('AOM_NAME','');                 
        $email_template->set_email_template_variable('AOM_LABEL','');                 
      }

      $a_query = $database->query("select u.user_id, u.agent_id, u.email_address, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $customer_info_result['user_id'] . "' and u.user_id = ud.user_id limit 1");
      $a_result = $database->fetch_array($query);

      $email_template->set_email_template_variable('AGENT_NAME',$a_result['firstname'].' '.$a_result['lastname']);        
      $email_template->set_email_template_variable('AGENT_ID',$a_result['agent_id']);        
      $email_template->set_email_template_variable('AGENCY_NAME',$a_result['name']);                    

			$email_template->set_email_template_variable('HOUSE_NUMBER', $customer_info_result['house_number']);
			$email_template->set_email_template_variable('STREET_NAME', $customer_info_result['street_name']);
			$email_template->set_email_template_variable('CITY', $customer_info_result['city']);
			$email_template->set_email_template_variable('ORDER_TYPE', $customer_info_result['order_type_name']);
			$email_template->set_email_template_variable('STATE_NAME', $customer_info_result['state_name']);
			$email_template->set_email_template_variable('COUNTY_NAME', $customer_info_result['county_name']);
			$email_template->set_email_template_variable('AGENT_COMMENTS', $agent_comments);
			$email_template->set_email_template_variable('DATE_COMPLETED', date("n/d/Y", mktime()));

            if (strlen($equipment_string) > 0) {
                $email_template->set_email_template_variable('EQUIPMENT', $equipment_string);
                $email_template->set_email_template_variable('EQUIPMENT_LABEL', 'Equipment:');
            } else {
                $email_template->set_email_template_variable('EQUIPMENT', '');
                $email_template->set_email_template_variable('EQUIPMENT_LABEL', '');
            }
			$email_template->parse_template();
			$email_template->send_email($customer_info_result['email_address'], $customer_info_result['firstname'].','.$customer_info_result['lastname']);
            $admin_string .= $customer_info_result['email_address'];
			$extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $customer_info_result['user_id'] . "' and email_status = '1'");
			while($extra_result = $database->fetch_array($extra_query)) {
			    $email_template->send_email($extra_result['email_address'],$result['firstname'].' '.$result['lastname']);
                $admin_string .= ", ".$extra_result['email_address'];
			}
            if (strlen($aom_email) > 0) {
                $email_template->send_email($aom_email,$aom_name);
                $admin_string .= ", ".$aom_email;
            }
        $email_template->set_email_template_variable('ADMIN_LABEL','This confirmation e-mail was sent to: ');                 
        $email_template->set_email_template_variable('ADMIN_INFO',$admin_string);                 
                $email_template->send_email('realtysp@yahoo.com','Installer confirm');
//                $email_template->send_email('onlineme@onlinemediasupport.com','Installer confirm');
            
		} else {
            //Should only happen if install.
            $last_modified_by = tep_fill_variable('user_id', 'session', 0);
		    if ($order_type_id == '1') {
				$database->query("update " . TABLE_ORDERS . " set order_status_id = '3', order_type_id = '2', date_completed = '" . mktime() . "', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where order_id = '" . $oID . "' limit 1");
				$database->query("update " . TABLE_ADDRESSES . " set installer_comments = '" . $installer_comments  . "', status = '0' where address_id = '" . $result['address_id'] . "' limit 1");
				$database->query("update " . TABLE_ORDERS_DESCRIPTION . " set installer_comments = '" . $agent_comments . "' where order_id = '" . $oID . "' limit 1");
				$canceled_query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "' and order_type_id = '3' limit 1");
				$canceled_result = $database->fetch_array($canceled_query);
				$database->query("update " . TABLE_ORDERS . " set order_status_id = '4', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where order_id = '" . $canceled_result['order_id'] . "' limit 1");
				$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $canceled_result['order_id'] . "', '4', '" . mktime() . "', '1', 'This order has been canceled as the post was not able to be installed.  Please read the relevent installation for more information.')");
				$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $oID . "', '3', '" . mktime() . "', '1', 'Your order was not able to be completed.  " . $agent_comments . "')");
			} elseif ($order_type_id == '3') {
			}
			$customer_info_query = $database->query("select u.user_id, u.email_address, ud.firstname, ud.lastname, a.house_number, a.street_name, c.name as county_name, a.city, a.zip, s.name as state_name, ot.name as order_type_name from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_COUNTYS . " c, " . TABLE_STATES . " s, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where o.order_id = '" .$oID . "' and o.address_id = a.address_id and a.county_id = c.county_id and o.user_id = ud.user_id and ud.user_id = u.user_id and o.order_type_id = ot.order_type_id and a.state_id = s.state_id limit 1");
			$customer_info_result = $database->fetch_array($customer_info_query);
						
			//Now prepare the email for the customer.
					$equipment_string = tep_create_completed_removal_equipment_string($oID, true, tep_fill_variable('equipment', 'post', array()), array(), tep_fill_variable('damaged', 'post', array()), tep_fill_variable('missing', 'post', array()));
					
										
					$email_template = new email_template('installer_view_completed');
					$email_template->load_email_template();
        $email_template->set_email_template_variable('ADMIN_LABEL','');                 
        $email_template->set_email_template_variable('ADMIN_INFO','');                 
        $admin_string = "";
                                        
      if ($placed_id != $user_id) {
        $aom_query = $database->query("select u.user_id, u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $placed_id . "' and u.user_id = ud.user_id limit 1");
        $aom_result = $database->fetch_array($aom_query);
        $aom_name = $aom_result['firstname'].' '.$aom_result['lastname'];
        $aom_email = $aom_result['email_address'];
        $email_template->set_email_template_variable('AOM_NAME',$aom_result['firstname'].' '.$aom_result['lastname']);                 
        $email_template->set_email_template_variable('AOM_LABEL','AOM Name: ');                 
      } else {
        $aom_name = "";
        $aom_email = "";
        $email_template->set_email_template_variable('AOM_NAME','');                 
        $email_template->set_email_template_variable('AOM_LABEL','');                 
      }

      $a_query = $database->query("select u.user_id, u.agent_id, u.email_address, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $customer_info_result['user_id'] . "' and u.user_id = ud.user_id limit 1");
      $a_result = $database->fetch_array($query);

      $email_template->set_email_template_variable('AGENT_NAME',$a_result['firstname'].' '.$a_result['lastname']);        
      $email_template->set_email_template_variable('AGENT_ID',$a_result['agent_id']);        
      $email_template->set_email_template_variable('AGENCY_NAME',$a_result['name']);                    

					$email_template->set_email_template_variable('HOUSE_NUMBER', $customer_info_result['house_number']);
					$email_template->set_email_template_variable('STREET_NAME', $customer_info_result['street_name']);
					$email_template->set_email_template_variable('CITY', $customer_info_result['city']);
					$email_template->set_email_template_variable('ORDER_TYPE', $customer_info_result['order_type_name']);
					$email_template->set_email_template_variable('STATE_NAME', $customer_info_result['state_name']);
					$email_template->set_email_template_variable('COUNTY_NAME', $customer_info_result['county_name']);
					$email_template->set_email_template_variable('AGENT_COMMENTS', $agent_comments);
					$email_template->set_email_template_variable('DATE_COMPLETED', date("n/d/Y", mktime()));

            if (strlen($equipment_string) > 0) {
                $email_template->set_email_template_variable('EQUIPMENT', $equipment_string);
                $email_template->set_email_template_variable('EQUIPMENT_LABEL', 'Equipment:');
            } else {
                $email_template->set_email_template_variable('EQUIPMENT', '');
                $email_template->set_email_template_variable('EQUIPMENT_LABEL', '');
            }
			$email_template->parse_template();
            $email_template->send_email($result['email_address'], $customer_info_result['firstname'].','.$customer_info_result['lastname']);
            $admin_string .= $result['email_address'];
				
			$extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $customer_info_result['user_id'] . "' and email_status = '1'");
			while($extra_result = $database->fetch_array($extra_query)) {
			    $email_template->send_email($extra_result['email_address'],$result['firstname'].' '.$result['lastname']);
                $admin_string .= ", ".$extra_result['email_address'];
			}
            if (strlen($aom_email) > 0) {
                $email_template->send_email($aom_email,$aom_name);
                $admin_string .= ", ".$aom_email;
            }
        $email_template->set_email_template_variable('ADMIN_LABEL','This confirmation e-mail was sent to: ');                 
        $email_template->set_email_template_variable('ADMIN_INFO',$admin_string);                 
                $email_template->send_email('realtysp@yahoo.com','Installer confirm');
//               $email_template->send_email('onlineme@onlinemediasupport.com','Installer confirm');
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
                    $last_modified_by = tep_fill_variable('user_id', 'session', 0);
					$database->query("update " . TABLE_ORDERS . " set special_conditions = '" . $new_string . "', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where order_id = '" . $oID . "' limit 1");
				
					//Now refund any oweing.
						if (!empty($refund_array)) {
							$account = new account($user_id);
							$refund_amount = tep_fetch_extra_cost($refund_array);
							$refund_reason ='Refund for ' . tep_fetch_extra_cost_string($refund_array);
							$account->set_credit_amount($refund_amount, 'Refund for Services', $refund_reason, $oID, 'cancel');
						}
				}
			//Now if this was a return and everythign was fine we return the deposit amount.
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
			

			header('Location: ' . FILENAME_INSTALLER_AWAITING_COMPLETED);
			die();
		}

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('installer_awaiting_completed')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('installer_awaiting_completed'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
		<?php
			if (($page_action != 'edit') && ($page_action != 'preview')) {
				$where = '';
				//Here we work out if it is today or tomorrow and change the where to match.
						$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
						$midnight_two_days_ago = ($midnight_tonight - (86400 * 2));
				//We only want the orders for the specifed day.
		?>			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Job Id</td>
						<td class="pageBoxHeading">Date Scheduled</td>
						<td class="pageBoxHeading">Job Status</td>
						<td class="pageBoxHeading">Type</td>
						<td class="pageBoxHeading">Zip4</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$extra = '';
					//o.date_schedualed > '" . $midnight_two_days_ago . "' and o.date_schedualed < '" . $midnight_tonight . "' and
					$listing_split = new split_page("select o.order_id, o.date_schedualed, o.order_status_id, os.order_status_name, ot.name as order_type_name, a.zip4".$extra." from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLERS_TO_ORDERS . " ito, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_tonight . "' and o.order_issue != '1' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and a.state_id = s.state_id and a.county_id = c.county_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and o.order_id = ito.order_id and ito.installer_id =  '" . $user->fetch_user_id() . "' and o.order_status_id = '2' group by o.order_id order by o.date_completed DESC", '20', 'o.order_id');
						if ($listing_split->number_of_rows > 0) {
							$query = $database->query($listing_split->sql_query);
								while($result = $database->fetch_array($query)) {
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['order_id']; ?></td>
						<td class="pageBoxContent"><?php echo date("n/d/Y", $result['date_schedualed']).' -'.$result['date_schedualed']; ?></td>
						<td class="pageBoxContent"><?php echo $result['order_status_name']; ?></td>
						<td class="pageBoxContent"><?php echo $result['order_type_name']; ?></td>
						<td class="pageBoxContent"><?php echo $result['zip4']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_INSTALLER_AWAITING_COMPLETED; ?>?page_action=edit&oID=<?php echo $result['order_id']; ?>">Mark Completed</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
								}
								?>
						<tr>
							<td colspan="5">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> completed orders)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
									</tr>
								</table>
							</td>
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
								$order_data_query = $database->query("select o.order_id, o.date_schedualed, o.order_type_id, a.house_number, a.street_name, a.zip, a.zip4, a.number_of_posts, a.cross_street_directions, ot.name as order_type_name, s.name as state_name, c.name as county_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_ADDRESSES . " a, " . TABLE_ORDER_TYPES . " ot, " . TABLE_COUNTYS . " c, " . TABLE_STATES . " s where o.order_id = '" . $oID . "' and o.order_id = od.order_id and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and a.state_id = s.state_id and a.county_id = c.county_id limit 1");
								$order_data_result = $database->fetch_array($order_data_query);
								
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
								<td class="pageBoxContent" colspan="2">This job <input name="install_status" value="1" type="radio" CHECKED />&nbsp;<b>Was</b>&nbsp;/<input name="install_status" value="0" type="radio" />&nbsp;<b>Was Not</b>&nbsp; completed successfully.</td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="pageBoxContent" valign="top">Address: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['house_number'] .' ' . $order_data_result['street_name'].'<br>'.$order_data_result['zip'].'<br>'.$order_data_result['zip4'].'<br>'.$order_data_result['county_name'].'<br>'.$order_data_result['state_name']; ?></td>
										</tr>
										<tr>
											<td class="pageBoxContent" valign="top">Job Type: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['order_type_name']; ?></td>
										</tr>
									</table>
								</td>
							</tr>
							
							<?php	
								if ($order_data_result['order_type_id'] < 3) {
									$equipment_string = tep_create_completed_order_equipment_string($oID);
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
									$equipment_string = tep_create_completed_removal_equipment_string($oID);
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
								<td width="100%"><textarea name="installer_comments" cols="40" rows="10"></textarea></td>
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
								<td width="100%"><textarea name="agent_comments" cols="40" rows="10"></textarea></td>
							</tr>
						</table>
						<?php
					} elseif ($page_action == 'preview') {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="0">
							<?php
								//Now we work out what type of order it was and show them the relevent options.
								//We also get the address from here to make sure we have the correct order.
								$order_data_query = $database->query("select o.order_id, o.date_schedualed, o.order_type_id, a.house_number, a.street_name, a.zip, a.zip4, a.number_of_posts, a.cross_street_directions, ot.name as order_type_name, s.name as state_name, c.name as county_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_ADDRESSES . " a, " . TABLE_ORDER_TYPES . " ot, " . TABLE_COUNTYS . " c, " . TABLE_STATES . " s where o.order_id = '" . $oID . "' and o.order_id = od.order_id and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and a.state_id = s.state_id and a.county_id = c.county_id limit 1");
								$order_data_result = $database->fetch_array($order_data_query);
								$install_status = tep_fill_variable('install_status', 'post', 0);
							?>
							<form action="<?php echo PAGE_URL; ?>?oID=<?php echo $oID; ?>&page_action=edit_confirm" method="post">
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
											<td class="pageBoxContent" valign="top">Address: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['house_number'] .' ' . $order_data_result['street_name'].'<br>'.$order_data_result['zip'].'<br>'.$order_data_result['zip4'].'<br>'.$order_data_result['county_name'].'<br>'.$order_data_result['state_name']; ?></td>
										</tr>
										<tr>
											<td class="pageBoxContent" valign="top">Job Type: </td>
											<td class="pageBoxContent" valign="top"><?php echo $order_data_result['order_type_name']; ?></td>
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
								<td class="pageBoxContent">You installed <?php echo tep_generate_post_type_pulldown_menu('post_type_id', '', '', $order_data_result['zip4']); ?> Posts.</td>
							</tr>
							<?php
								}
							?>

							
							<?php	
								if ($order_data_result['order_type_id'] < 3) {
									$equipment_string = tep_create_completed_order_equipment_string($oID, true, tep_fill_variable('equipment', 'post', array()), array(), tep_fill_variable('damaged', 'post', array()), tep_fill_variable('missing', 'post', array()));
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
								<td width="100%" class="pageBoxContent"><?php echo tep_fill_variable('installer_comments'); ?></td>
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
								<td width="100%" class="pageBoxContent"><?php echo tep_fill_variable('agent_comments'); ?></td>
							</tr>
						</table>
						<?php
					}
				}
			?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		
			<?php
				if (($page_action != 'edit') && ($page_action != 'preview')) {
			?>
			<td width="250" valign="top">
				<table width="250" cellspacing="0" celpadding="0" class="pageBox">
					<tr>
						<td class="pageBoxContent">Click "Mark Completed" and fill out the required details to mark that job as completed.</td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
				</table>
			</td>
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
									<td align="left"><?php echo tep_create_button_submit('update_job', 'Update Job'); ?></form></td>
									<td align="right"><form action="<?php echo FILENAME_INSTALLER_AWAITING_COMPLETED; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
									<td align="right"><form action="<?php echo FILENAME_INSTALLER_AWAITING_COMPLETED; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<?php
				}
			?>
		
	</tr>
</table>
