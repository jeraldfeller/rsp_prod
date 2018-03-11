<?php
	class orders {
		var $data;
		var $id;
		var $result;
		var $base_cost;
		var $extended_cost;
		var $equipment_cost;
		var $discount_cost;
		var $deposit_cost;
		var $extra_cost;
		var $total_cost;
		var $user_id;
		var $credit;
		var $action;
		var $order_insert_type_id;
	
			function orders($action = 'fetch', $id = '', $data = array(), $user_id = '', $change_address_status = true, $order_insert_type_id = '1') 
			{
				global $user;
				if ($user_id and is_numeric($user_id) and (int) $user_id > 0) {
					$this->user_id = $user_id;
				} else {
					$this->user_id = $user->fetch_user_id();
                }

                if (!is_array($data) || !array_key_exists("billing_method_id", $data)) {
                    $data["billing_method_id"] = tep_get_default_billing_method($this->user_id);
                }

				$this->order_insert_type_id = $order_insert_type_id;
				$this->action = $action;
				$this->id = $id;
                $this->data = $data;

                // Fetching the order total triggers the calculation of the discount as well
                if (tep_zip4_is_valid($this->fetch_data_item('zip4', ''))) {
                    $this->total_cost = $this->fetch_order_total($this->fetch_data_item('zip4', ''));
                } else {
                    $this->total_cost = $this->fetch_order_total();
                }

				if ($action == 'fetch') {
                    $this->result = $this->fetch_order();
                    $this->user_id = $this->fetch_data_item('user_id', $this->user_id);
				} elseif ($action == 'insert') {
				    $this->result = $this->insert_order($change_address_status);
				} elseif ($action == 'update') {
				    $this->result = $this->update_order($change_address_status);
                } elseif ($action == 'cancel') {
                    $this->result = $this->cancel_order();
                }
			}
			
            function fetch_data_item($item, $callback = '') {
                // Priorities:
                //
                //   (1) Passed in arguments
                //   (2) Database values
                //   (3) Callback
                
                if (is_array($this->data) && array_key_exists($item, $this->data)) {
					return $this->data[$item];
                } elseif (is_array($this->result) && array_key_exists($item, $this->result)) {
					return $this->result[$item];
				} else {
					return $callback;
				}
			}
			
			function update_order() {
				global $database;
                global $session;

				$array = array();
				if ($this->fetch_data_item('red_flag_off', false) === '1') {
					$array['order_issue'] = '0';
                }
                $this->loop_update(TABLE_ORDERS, $array, 'order_id', $this->id);

                if ($session->php_session_is_registered('user_id')) {
                    $last_modified_by = $session->php_return_session_variable('user_id');
                } else {
                    $last_modified_by = 0;
                }

				$array = array('order_type_id' => $this->fetch_data_item('order_type_id', false),
					'date_schedualed' => $this->fetch_data_item('date_schedualed', false),
					'date_completed' => $this->fetch_data_item('date_completed', false),
					'extra_cost_description' => $this->fetch_data_item('extra_cost_description', false),
					'last_modified' => mktime(),
					'last_modified_by' => $last_modified_by,
					'order_status_id' => $this->fetch_data_item('order_status_id', false));

				$query = $database->query("select order_type_id, billing_method_id from " . TABLE_ORDERS . " where order_id = '" . $this->id . "' limit 1");
				$result = $database->fetch_array($query);

                $old_date_schedualed = 0;
                $new_date_schedualed = $this->fetch_data_item('date_schedualed', 0);
                $check_reschedule_query = $database->query("SELECT date_schedualed FROM " . TABLE_ORDERS . " WHERE order_id = '" . $this->id . "' limit 1");
                while ($check_reschedule_row = $database->fetch_array($check_reschedule_query)) {
                    $old_date_schedualed = $check_reschedule_row['date_schedualed'];
                }

                if ($old_date_schedualed != $new_date_schedualed) {
                    // The order has been rescheduled.  Log it.
                    $reschedule_user_id = $_SESSION['user_id'];
                    $rescheduled_date = strtotime('now');
                    $reschedule_table = TABLE_RESCHEDULE_HISTORY;
                    $reschedule_query = "INSERT INTO {$reschedule_table} (order_id, user_id, old_scheduled_date, new_scheduled_date, rescheduled_date) ";
                    $reschedule_query.= "VALUES ({$this->id}, {$reschedule_user_id}, {$old_date_schedualed}, {$new_date_schedualed}, {$rescheduled_date})";
                    $database->query($reschedule_query);
                }

				$this->loop_update(TABLE_ORDERS, $array, 'order_id', $this->id);
				$array = array('date_schedualed' => $this->fetch_data_item('date_schedualed', false));
				$this->loop_update(TABLE_ORDERS, $array, 'order_id', $this->id);

				$array = array('special_instructions' => $this->fetch_data_item('special_instructions', false),
					'number_of_posts' => $this->fetch_data_item('number_of_posts', false),
					'admin_comments' => $this->fetch_data_item('admin_comments', false),
					'installer_comments' => $this->fetch_data_item('installer_comments', false));
				$this->loop_update(TABLE_ORDERS_DESCRIPTION, $array, 'order_id', $this->id);

				$address_id_query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $this->id . "' limit 1");
				$address_id_result = $database->fetch_array($address_id_query);

				$zip4_start = false;
				$zip4_end = false;
				if (tep_zip4_is_valid($this->fetch_data_item('zip4', ''))) {
					$explode = tep_break_zip4_code($this->fetch_data_item('zip4', array()));
					$zip4_start = $explode[0];
					$zip4_end = $explode[1];
				}
				$array = array('status' => $this->fetch_data_item('status', false),
					'house_number' => $this->fetch_data_item('house_number', false),
					'street_name' =>$this->fetch_data_item('street_name', false) ,
					'city' => $this->fetch_data_item('city', false),
					'zip' => $this->fetch_data_item('zip', false),
					'state_id' => $this->fetch_data_item('state_id', false),
					'county_id' => $this->fetch_data_item('county_id', false),
					'zip4' => $this->fetch_data_item('zip4', false),
					'zip4_start' => $zip4_start,
					'zip4_end' => $zip4_end,
					'adc_number' => $this->fetch_data_item('adc_number', false),
					'cross_street_directions' => $this->fetch_data_item('cross_street_directions', false));
				if (($result['order_type_id'] == '3') && $this->fetch_data_item('order_status_id', false) == '3') {
					$array['status'] = '3';
				}
				$this->loop_update(TABLE_ADDRESSES, $array, 'address_id', $address_id_result['address_id']);			

				$assigned_installer = $this->fetch_data_item('assigned_installer', false);

				if (($assigned_installer !== false) && ($assigned_installer != tep_fetch_assigned_order_installer($this->id))) {
					$this->update_installer_id($assigned_installer);
				}
				if ($result['order_type_id'] == '1') {
					$optional = $this->fetch_data_item('optional', array());
                    $this->update_optional($optional);  

					$old_cost_query = $database->query("select order_total, order_type_id from " . TABLE_ORDERS . " where order_id = '" . $this->id . "' limit 1");
                    $old_cost_result = $database->fetch_array($old_cost_query);
                    $old_total = $old_cost_result['order_total'];
                    $old_order_type_id = $old_cost_result['order_type_id'];

                    $new_total = $this->fetch_order_total($this->fetch_data_item('zip4', false));
                    $equipment_cost = $this->equipment_cost;
                    $discount_cost = $this->discount_cost;
                    $base_cost = $this->base_cost;
                    $extended_cost = $this->extended_cost;
                    $extra_cost = $this->extra_cost;
                    $credit = $this->credit;
                    $deposit_cost = $this->deposit_cost;

                    $new_total += $credit;  // Put the credit back on, so we are comparing the totals properly.

					if ($this->fetch_data_item('order_total_force', false)) {
						$new_total = $this->fetch_data_item('order_total', false);
					}
					if ($new_total != $old_total) {
						$account = new account($this->user_id, '', $result['billing_method_id']);
						if ($new_total > $old_total) {
							//Need to bill more.
							$difference = ($new_total - $old_total);
							if ($difference > 0) {
								$account->set_debit_credit_amount($difference, 'Extra charges for Order', $array['house_number'] . " " . $array['street_name'] . " " . $array['city'], $old_order_type_id, $this->id);
							}
						} else {
							$difference = ($old_total - $new_total);
							if ($difference > 0) {
								$account->set_credit_amount($difference, 'Total reduction for Order', $array['house_number'] . " " .     $array['street_name'] . " " . $array['city'], $this->id, 'cancel');
							}

						}
					}
                    $database->query("update " . TABLE_ORDERS . " set equipment_cost = '{$equipment_cost}', order_total = '{$new_total}', discount_cost = '{$discount_cost}', base_cost = '{$base_cost}', extended_cost = '{$extended_cost}', extra_cost = '{$extra_cost}', deposit_cost = '{$deposit_cost}' where order_id = '{$this->id}' limit 1");
                    error_log("update " . TABLE_ORDERS . " set equipment_cost = '{$equipment_cost}', order_total = '{$new_total}', discount_cost = '{$discount_cost}', base_cost = '{$base_cost}', extended_cost = '{$extended_cost}', extra_cost = '{$extra_cost}', deposit_cost = '{$deposit_cost}' where order_id = '{$this->id}' limit 1");

                    if ($this->fetch_data_item('miss_utility_yes_no', false) == "yes" || 
                        $this->fetch_data_item('lamp_yes_no', false) == "yes" || 
                        $this->fetch_data_item('lamp_use_gas', false) != "yes") {
                            // 0: no
                            // 1: yes
                            // 2: unsure
                            $agent_requested = $this->data['miss_utility_yes_no'] == "yes" ? 1 : 0;
                            $has_lamp = $this->data['lamp_yes_no'] == "yes" ? 1 : 0;
                            $has_gas_lamp = ($this->data['lamp_use_gas'] == "yes") ? 1 : ($this->data['lamp_use_gas'] == "no" ? 0 : 2);
                            $contacted = $this->data['contacted'] == "yes" ? 1 : 0;
                            $miss_utility_sql = "insert into " . TABLE_ORDERS_MISS_UTILITY . " (order_id, has_lamp, has_gas_lamp, contacted, ";
                            $miss_utility_sql.= "agent_requested) values ('" . $this->id ."', '" . $has_lamp . "', '" . $has_gas_lamp . "', '";
                            $miss_utility_sql.= $contacted . "', '" . $agent_requested . "') on duplicate key update has_lamp = '";
                            $miss_utility_sql.= $has_lamp . "', has_gas_lamp = '" . $has_gas_lamp . "', contacted = '" . $contacted . "', ";
                            $miss_utility_sql.= "agent_requested = '" . $agent_requested . "'";
                    } else {
                            // remove old Miss Utility reference
                            $miss_utility_sql = "delete from " . TABLE_ORDERS_MISS_UTILITY . " where order_id = '" . $this->id . "' limit 1";
                    }
                    $database->query($miss_utility_sql);
				}
			}
			
			function update_installer_id($installer_id) {
				global $database;
				
					$query = $database->query("select installer_id from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $this->id . "' limit 1");
					$result = $database->fetch_array($query);
						if ($result['installer_id'] != NULL) {
								if (empty($installer_id)) {
									$database->query("delete from " . TABLE_INSTALLERS_TO_ORDERS . " where  order_id = '" . $this->id . "' limit 1");
								} else {
									$database->query("update " . TABLE_INSTALLERS_TO_ORDERS . " set installer_id = '"  . $installer_id . "' where order_id = '" . $this->id . "' limit 1");
								}
						} else {
								if (!empty($installer_id)) {
									
									$database->query("insert into " . TABLE_INSTALLERS_TO_ORDERS . " (installer_id, order_id) values ('" . $installer_id . "', '" . $this->id . "')");
								}
						}
			}
			
			function update_optional($optional) {
				global $database;
				if (!is_array($optional)) {
				    $optional = array();
				}
                reset($optional);

                $number_of_posts = $this->fetch_data_item('number_of_posts', 1);
                foreach($optional as $group => $answers) {
                    foreach ($answers as $index => $answer) {
                        tep_reassign_equipment_to_order($this->id, $group, $answer, '1', fetch_address_zip4($this->fetch_data_item('address_id', '')), $this->user_id, $this->fetch_data_item('address_id', ''), $this->fetch_data_item('number_of_posts', 1));
                    }
                }

				
				$query = $database->query("select equipment_to_order_id, equipment_item_id, equipment_group_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $this->id . "'");
				while($result = $database->fetch_array($query)) {
			    	if (!isset($optional[$result['equipment_group_id']]) || !in_array($result['equipment_group_answer_id'], $optional[$result['equipment_group_id']])) {
				    	$database->query("delete from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_to_order_id = '" . $result['equipment_to_order_id'] . "' limit 1");
					    $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id'] . "' limit 1");
				    }
				}
				
			}
			
			function loop_update($table, $array, $column, $id) {
				global $database;
					while(list($key, $val) = each($array)) {
							if ($val === false) {
								continue;
							}
						$database->query("update " . $table . " set " . $key . " = '" . $val . "' where " . $column . " = '" . $id . "' limit 1");
					}
			}
			
			function view_order() {
				
			}

            function cancel_order() {
                // Update costs and order_status_id
                // Free equipment
                
                global $database;
                $reason = $this->fetch_data_item('reason', 'Order has been cancelled');
                $last_modified_by = tep_fill_variable('user_id', 'session', 0);
                $last_modified = mktime();

                // First fetch the order so we have all the data
                $this->result = $this->fetch_order();
                $this->user_id = $this->fetch_data_item('user_id', $this->user_id);
                $billing_method_id = $this->result['billing_method_id'];

                // Credit back to account
                $account = new account($this->user_id, '', $billing_method_id);
                $account->create_cancel_order_entry($this->id);
                tep_create_order_history($this->id, '4', $reason);

                $address_id = $this->fetch_data_item('address_id');
                $order_type_id = $this->fetch_data_item('order_type_id');

                // Update costs and last_modified by
                $database->query("update " . TABLE_ORDERS . " set equipment_cost = '0', order_total = '0', discount_cost = '0', base_cost = '0', extended_cost = '0', extra_cost = '0', deposit_cost = '0', order_status_id = '4', last_modified_by = '{$last_modified_by}', last_modified = '{$last_modified}' where order_id = '{$this->id}' limit 1");

                if (($order_type_id == ORDER_TYPE_INSTALL) || ($order_type_id == ORDER_TYPE_SERVICE)) {
                    $equery = $database->query("select eita.equipment_id, eita.equipment_item_id, e.name as equipment_name, e.replace_cost, e.equipment_type_id from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.address_id = '{$address_id}' and eita.equipment_status_id = '2' and eita.equipment_id = e.equipment_id");
                    while($eresult = $database->fetch_array($equery)) {
                        $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $eresult['equipment_item_id'] . "' limit 1");
                    }
                }

                // Cancel associated pending service calls and removals
                if ($order_type_id == ORDER_TYPE_INSTALL) {
                    $query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '{$address_id}' and order_type_id IN ('2', '3') and order_status_id = '1'");
                    while ($result = $database->fetch_array($query)) {
                        $order_id = $result['order_id'];
                        $cancelled_order = new orders('cancel', $order_id);
                    }
                }

                return $this->fetch_order();
            }

			function insert_order($update_status = true) {
                global $user, $database;

                $number_of_posts = $this->fetch_data_item('number_of_posts', '1');

				if ($this->fetch_data_item('zip4', '') == '') {
					$this->data['zip4'] = fetch_address_zip4($this->fetch_data_item('address_id', ''));
				}
                $total_cost = $this->fetch_order_total($this->fetch_data_item('zip4', ''));
                $cost_before_credit = $total_cost + $this->credit;

				$order_status_id = $this->fetch_data_item('order_status', '1');
				$database->query("insert into " . TABLE_ORDERS . " (user_id, address_id, order_type_id, base_cost, extended_cost, equipment_cost, discount_cost, deposit_cost, order_total, date_added, date_schedualed, order_status_id, billing_method_id, service_level_id, extra_cost, extra_cost_description, special_conditions, date_completed, inserted_order_type_id, placed_by) values ('" . $this->user_id . "', '" . $this->fetch_data_item('address_id', '') . "', '" . $this->fetch_data_item('order_type_id', '') . "', '" . $this->base_cost . "', '" . $this->extended_cost . "', '" . $this->equipment_cost . "', '" . $this->discount_cost . "', '" . $this->deposit_cost . "', '" . $cost_before_credit . "', '" . mktime() . "', '" . $this->fetch_data_item('schedualed_start', mktime()) . "', '" . $order_status_id . "', '".$this->fetch_data_item('billing_method_id', '2') ."', '" . $this->fetch_data_item('service_level_id', tep_get_service_level_id($this->user_id)) . "', '" . $this->extra_cost. "', '".$this->fetch_data_item('extra_cost_description', '') ."', '".$this->fetch_data_item('special_conditions', '') ."', '" . $this->fetch_data_item('date_completed', '0')  . "', '" . $this->order_insert_type_id . "', '" . (int)$user->fetch_user_id() . "')");
                $order_id = $database->insert_id();

                // insert the Miss Utility information, if applicable
                if ($this->fetch_data_item('miss_utility_yes_no', "no") == "yes" || 
                    $this->fetch_data_item('lamp_use_gas', "no") == "yes" || 
                    $this->fetch_data_item('lamp_use_gas', "no") == "unsure") { 
                    // 0: no
                    // 1: yes
                    // 2: unsure
                    $agent_requested = $this->data['miss_utility_yes_no'] == "yes" ? 1 : 0;
                    $has_lamp = $this->data['lamp_yes_no'] == "yes" ? 1 : 0;
                    $has_gas_lamp = ($this->data['lamp_use_gas'] == "yes") ? 1 : ($this->data['lamp_use_gas'] == "no" ? 0 : 2);
                    $miss_utility_sql = "insert into " . TABLE_ORDERS_MISS_UTILITY . " (order_id, agent_requested, has_lamp, has_gas_lamp) ";
                    $miss_utility_sql.= "values ($order_id, $agent_requested, $has_lamp, $has_gas_lamp)";

                    $database->query($miss_utility_sql);
                }

				if ($this->deposit_cost > 0) {
					$database->query("update " . TABLE_ADDRESSES . " set deposit_cost = '" . $this->deposit_cost . "' where address_id = '" . $this->fetch_data_item('address_id', '') . "' limit 1");
					tep_update_user_deposit($this->user_id);
				}
				$database->query("insert into " . TABLE_ORDERS_DESCRIPTION . " (order_id, special_instructions, number_of_posts) values ('" . $order_id . "', '" . $this->fetch_data_item('special_instructions', '') . "', '" . $this->fetch_data_item('number_of_posts', '') . "')");
				if ($update_status) {
					if ($this->fetch_data_item('order_type_id', '1') == '1') {
						$database->query("update " . TABLE_ADDRESSES . " set status = '1' where address_id = '" . $this->fetch_data_item('address_id', '') . "' limit 1"); 
					} elseif ($this->fetch_data_item('order_type_id', '1') == '3') {
						$database->query("update " . TABLE_ADDRESSES . " set status = '3' where address_id = '" . $this->fetch_data_item('address_id', '') . "' limit 1");
					}
				}
				if (($this->fetch_data_item('billing_method_id', '2') >= '2') && ($cost_before_credit > 0)) {
                    // Invoice order...  Deduct the entire amount from the account (this will take care of the credit)
                    $address_array = tep_fetch_address_information($this->fetch_data_item('address_id', ''));

					$account = new account($this->user_id, '', $this->fetch_data_item('billing_method_id'));
					$account->set_debit_amount($cost_before_credit, tep_get_order_type_name($this->fetch_data_item('order_type_id', '')), $address_array['house_number']. ' ' .$address_array['street_name']. ' ' .$address_array['city'], $this->fetch_data_item('order_type_id', ''), $order_id);
			    }
                if (($this->fetch_data_item('billing_method_id', '2') == '1') && ($this->credit > 0)) {
                    // Credit card order...  Deduct the credit from the account
                    $address_array = tep_fetch_address_information($this->fetch_data_item('address_id', ''));

					$account = new account($this->user_id, '', $this->fetch_data_item('billing_method_id'));
					$account->apply_credit($this->credit, tep_get_order_type_name($this->fetch_data_item('order_type_id', '')), $address_array['house_number']. ' ' .$address_array['street_name']. ' ' .$address_array['city'], $this->fetch_data_item('order_type_id', ''), $order_id);
			    }
					
				//Work out if a promotional code was used and of so then assign it to both the order and user.
				if ($this->discount_cost > 0) {
					$promo_code = $this->fetch_data_item('promo_code', '');
					$promo_id_query = $database->query("select promotional_code_id from " . TABLE_PROMOTIONAL_CODES . " where code = '" . $promo_code . "' limit 1");
					$promo_id_result = $database->fetch_array($promo_id_query);
					if (!empty($promo_id_result)) {
						$database->query("insert into " . TABLE_PROMOTIONAL_CODES_TO_ORDERS . " (promotional_code_id, order_id) values ('" . $promo_id_result['promotional_code_id'] . "', '" . $order_id . "')");
						$database->query("insert into " . TABLE_PROMOTIONAL_CODES_TO_USERS . " (promotional_code_id, user_id, date_added) values ('" . $promo_id_result['promotional_code_id'] . "', '" . $this->user_id . "', '" . mktime() . "')");
					}
                }

                //Insert default vaue into installer order.
				$database->query("insert into " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " (order_id) values ('" . $order_id . "')");
				//Now insert the equipment entrys, we will run this off a function.
				if ($this->fetch_data_item('order_type_id', '') == '1') {
					$optional = $this->fetch_data_item('optional', array());
					while(list($group_id, $values) = each($optional)) {
						if (empty($values) || !is_array($values) || (count($values) == 0)) {
							continue;
						} else {
							$count = count($values);
							$n = 0;
							while($n < $count) {
								if (!empty($values) && !empty($values[$n])) {
			    					//Set as status one as its actually only pending install and will not be installed till the installer marks as competed.
									tep_assign_equipment_to_order($order_id, $group_id, $values[$n], 1, fetch_address_zip4($this->fetch_data_item('address_id', '')), $this->user_id, $this->fetch_data_item('address_id', ''), 0, $number_of_posts);
								}
								$n++;
							}
						}
					}
				} elseif ($this->fetch_data_item('order_type_id', '') == '2') {
					$database->query("insert into " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " (order_id, service_call_reason_id, service_call_detail_id) values ('" . $order_id . "', '" . $this->fetch_data_item('sc_reason', '7') . "', '" . $this->fetch_data_item('sc_detail') . "')");
					if (($this->fetch_data_item('sc_reason') == '3')) {
						//Assign the equipment.
				    	$equipment = $this->fetch_data_item('equipment', '');
								
						for ($n = 0, $m = count($equipment); $n < $m; $n++) {
							tep_assign_post_to_order($order_id, $equipment[$n], '1',  $this->fetch_data_item('zip4'), 1, $number_of_posts);
						}
					} elseif ($this->fetch_data_item('sc_reason') == '1') {	
                        foreach ($this->fetch_data_item('install_equipment', array()) as $ieq) {
                            tep_assign_post_to_order($order_id, $ieq, '1', $this->fetch_data_item('zip4'), '1', '1', $number_of_posts);
                        }
                        foreach ($this->fetch_data_item('remove_equipment', array()) as $req) {
                            tep_assign_post_to_order($order_id, $req, '1', $this->fetch_data_item('zip4'), '1', '0', $number_of_posts);
                        }
					} elseif (($this->fetch_data_item('sc_reason') == '6') || ($this->fetch_data_item('sc_reason') == '2')) {
						$optional = $this->fetch_data_item('optional', array());
						while(list($group_id, $values) = each($optional)) {
						    if (empty($values) || !is_array($values) || (count($values) == 0)) {
							    continue;
					        } else {
						        $count = count($values);
						        $n = 0;
						        while($n < $count) {
							        if (!empty($values) && !empty($values[$n])) {
				   				        //Set as status one as its actually only pending install and will not be installed till the installer marks as competed.
								        tep_assign_equipment_to_order($order_id, $group_id, $values[$n], 1, fetch_address_zip4($this->fetch_data_item('address_id', '')), $this->user_id, $this->fetch_data_item('address_id', ''), 0, $number_of_posts);
								    }
							        $n++;
							    }
					        }
					    }   
			    	}
		    	}
			    $promo_code = $this->fetch_data_item('promo_code', '');
			    //If we have a code and can use it then assign to order.
			    if(!empty($promo_code) && tep_promotional_code_is_valid($promo_code)) {
				    $promotional_code_id = tep_fetch_promotional_id($promo_code);
				    tep_assign_promotional_code_to_order($order_id, $promotional_code_id);
			    }
			    //Set order history.
			    if ($order_status_id == '1') {
				    tep_create_orders_history($order_id, '1', 'Thank you for your order.  It has now been received and is awaiting acceptance.');
			    } else {
				    tep_create_orders_history($order_id, '1', 'Thank you for your order.  It has now been received and is awaiting acceptance.');
		    	    tep_create_orders_history($order_id, $order_status_id, 'Your order has been completed and your post is installed.');
			    }
			    $this->id = $order_id;
		    }
	
            function fetch_order_total($zip4 = "") {
                $cost = 0;
                $order_type_id = $this->fetch_data_item('order_type_id', '1');

                if ($order_type_id == ORDER_TYPE_REMOVAL) {
                    $this->result['base_cost'] = $this->base_cost = 0;
                    $this->result['discount_cost'] = $this->discount_cost = 0;
                    $this->result['extended_cost'] = $this->extended_cost = 0;
                    $this->result['equipment_cost'] = $this->equipment_cost = 0;
                    $this->result['extra_cost'] = $this->extra_cost = 0;
                    $this->result['credit'] = $this->credit = 0;
                    $this->result['deposit_cost'] = $this->deposit_cost = 0;
                    $this->result['order_total'] = $this->total_cost = 0;

					return $cost;
				} elseif ($order_type_id == ORDER_TYPE_SERVICE) {
                    return $this->fetch_order_total_sc($zip4);
                }

    			//First get the base cost.
				$this->base_cost = $this->create_base_order_cost() * $this->fetch_data_item('number_of_posts', '1');
                $cost += $this->base_cost;

                //Now get the discount
                //Get the per post amount discount
                $this->discount_cost = $this->create_discount_order_cost() * $this->fetch_data_item('number_of_posts', '1');
                $cost += $this->discount_cost;
				//Now add in promo codes and percentage discount
				$discounted_price = $this->create_order_discount_price($cost);
				$cost = ($cost - $discounted_price);
                $this->discount_cost = $this->discount_cost - $discounted_price;

				//Now get the extended service cost.
				$this->extended_cost = $this->create_extended_order_cost($zip4);
				$cost += $this->extended_cost;

				//Now get the cost for any extra equipment.
				$this->equipment_cost = $this->create_order_equipment_cost();
                $cost += $this->equipment_cost;

				$this->extra_cost = $this->create_order_extra_cost();
				$cost += $this->extra_cost;

				$this->deposit_cost = $this->create_order_deposit_cost();
				$cost += $this->deposit_cost;

				$credit = $this->create_order_credit_price($cost);
				if ($credit > $cost) {
                    $this->credit = $cost;
					$cost = 0;
                } else {
                    $this->credit = $credit;
					$cost -=$credit;
				}

                $this->total_cost = $cost;

                if ($cost == 0 && ($this->base_cost + $this->equipment_cost + $this->extended_cost + $this->extra_cost == 0)) {
                    // Don't show discount on free orders
                    $this->discount_cost = 0;
                    $this->credit = 0;
                }

                if (!is_array($this->result)) {
                    $this->result = array();
                }

                $this->result['base_cost'] = $this->base_cost;
                $this->result['discount_cost'] = $this->discount_cost;
                $this->result['extended_cost'] = $this->extended_cost;
                $this->result['equipment_cost'] = $this->equipment_cost;
                $this->result['extra_cost'] = $this->extra_cost;
                $this->result['credit'] = $this->credit;
                $this->result['deposit_cost'] = $this->deposit_cost;
                $this->result['order_total'] = $this->total_cost;

				return $cost;
			}
			
			function fetch_order_total_sc() {
                global $database;
                $cost = 0;

				$sc_reason = $this->fetch_data_item('sc_reason');
				$free = false;
				if ($sc_reason == '1') {
					if (tep_get_service_level_id($this->user_id) == '3') {
						$query = $database->query("select count(sco.service_call_option_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " sco where o.order_id = sco.order_id and o.address_id = '" . $this->fetch_data_item('address_id') . "' and sco.service_call_reason_id = '1'");
						$result = $database->fetch_array($query);
						if ($result['count'] < 1) {
							$free = true;
						}
					}
					if ($free) {
						$this->base_cost = 0;
					} else {
						$this->base_cost = 20;
					}
				} elseif ($sc_reason == '2') {
					$this->base_cost = 20;
				} elseif ($sc_reason == '3') {
					$this->base_cost = 20;
				} elseif ($sc_reason == '4') {
					$this->base_cost = 0;
				} elseif ($sc_reason == '5') {
					$this->base_cost = 20;
				} elseif ($sc_reason == '6') {
					$this->base_cost = 0;
				} elseif ($sc_reason == '7') {
					$this->base_cost = 20;
                }

				//First get the base cost.
				$cost += $this->base_cost;
				//Now get the extended service cost.
				//$this->extended_cost = $this->create_extended_order_cost();
				//$cost += $this->extended_cost;
				$this->extended_cost = 0;
				//Now get the cost for any extra equipment.
				if ($sc_reason == '2') {
					$this->equipment_cost = $this->create_order_equipment_cost();
					if ($this->equipment_cost > 20) {
						$this->base_cost = 0;
					} elseif ($this->equipment_cost > 0) {
						$difference = 20-$this->equipment_cost;
						$this->base_cost = $difference;
					}
					$cost = 0;
					$cost +=$this->base_cost;
					$cost +=$this->equipment_cost;
                } elseif ($sc_reason == '1') {
					if ($free) {
                        $this->equipment_cost = 0;
                    } else {
                        $install_equipment = $this->fetch_data_item('install_equipment', array());
                        $this->equipment_cost = max(0, 5 * (count($install_equipment) - 1));
					    if ($this->equipment_cost > 20) {
						    $this->base_cost = 0;
					    } elseif ($this->equipment_cost > 0) {
						    $difference = 20-$this->equipment_cost;
						    $this->base_cost = $difference;
					    }
					    $cost = 0;
					    $cost +=$this->base_cost;
					}
                    $cost +=$this->equipment_cost;
			   	}
                //the discount/adjustment on Service Calls is $0
                $this->discount_cost = 0;

				$this->extra_cost = $this->create_order_extra_cost();
                $cost +=$this->extra_cost;

                $this->deposit_cost = 0;

				$credit = $this->create_order_credit_price($cost);
				if ($credit > $cost) {
                    $this->credit = $cost;
					$cost = 0;
                } else {
                    $this->credit = $credit;
					$cost -=$credit;
                }

                $this->total_cost = $cost;

                if ($cost == 0 && ($this->base_cost + $this->equipment_cost + $this->extended_cost + $this->extra_cost == 0)) {
                    // Don't show discount on free orders
                    $this->discount_cost = 0;
                    $this->credit = 0;
                }

                if (!is_array($this->result)) {
                    $this->result = array();
                }

                $this->result['base_cost'] = $this->base_cost;
                $this->result['discount_cost'] = $this->discount_cost;
                $this->result['extended_cost'] = $this->extended_cost;
                $this->result['equipment_cost'] = $this->equipment_cost;
                $this->result['extra_cost'] = $this->extra_cost;
                $this->result['credit'] = $this->credit;
                $this->result['deposit_cost'] = $this->deposit_cost;
                $this->result['order_total'] = $this->total_cost;

				return $cost;
			}
			
			function create_order_sc_2_equipment_cost() {
				global $database;
				
				$equipment = $this->fetch_data_item('equipment');
				
				$cost = 0;
				$pp = 0;
					for ($n = 0, $m = count($equipment); $n < $m; $n++) {
						$query = $database->query("select equipment_type_id from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment[$n] . "' limit 1");
						$result = $database->fetch_array($query);
						
							if ($result['equipment_type_id'] == '3') {
								$cost += 10;
							} elseif ($result['equipment_type_id'] == '2') {
								if ($pp > 0) {
									$cost -= 10;
									$cost += 25;
								} else {
									$cost += 16;
								}
							}
					}
					
				return $cost;
			}
			
			//Get the base cost of an order,
			function create_base_order_cost() {
				global $user, $database;
				$service_level_id = tep_get_service_level_id($this->user_id);
				if ($this->fetch_data_item('order_type_id', '1') == '3') {
					return 0;
				}
				$query = $database->query("select installation_cost from " . TABLE_ORDER_TYPES . " where order_type_id = '" . $this->fetch_data_item('order_type_id', false) . "' limit 1");
				$result = $database->fetch_array($query);
			    if ((!empty($result['installation_cost']) && ($result['installation_cost'] > 0)) || ($this->fetch_data_item('order_type_id', '1') == '2')) {
				    return $result['installation_cost'];
				}
				$service_level_cost = tep_get_service_level_cost($service_level_id);
				return $service_level_cost;
			}
			
			//Get the discount cost of an order,
			function create_discount_order_cost() {
			    if ($this->fetch_data_item('order_type_id', '1') == '3') {
			        return 0;
				}
			    return tep_fetch_user_amount_reduction($this->user_id);
			}
			
			//Get the cost with any extended service areas.
			function create_extended_order_cost($zip4 = "") { // mjp

                if ($this->id != 0) {
                    $zip4 = tep_fetch_order_address_zip4($this->id);
                } else {
                    if (empty($zip4)) 
                        $zip4 = $this->fetch_data_item('zip4', '');
                }

                $service_area_id = tep_fetch_zip4_service_area($zip4);
                $this->extended_cost = tep_fetch_service_area_cost($service_area_id);
                return $this->extended_cost;
			}
			
			//Get the cost of any extra equipment.
			function create_order_equipment_cost() {
                $order_type_id = $this->fetch_data_item('order_type_id', '1');
                $optional = $this->fetch_data_item('optional', array());
                $optional = parse_equipment_array($optional);
				$service_level_id = tep_get_service_level_id($this->user_id);
                $address_id = $this->fetch_data_item('address_id', '');

                $number_of_posts = $this->fetch_data_item('number_of_posts', 1);
				return $number_of_posts * tep_fetch_equipment_cost($optional, $service_level_id, $address_id, $order_type_id);
			}
			
			//Get the cost of any extra equipment.
			function create_order_extra_cost() {
				$extra_cost = $this->fetch_data_item('extra_cost', 0);
				return $extra_cost;
			}
			
			function create_order_deposit_cost() {
				global $database;
					if (!empty($this->id)) {
						$query = $database->query("select deposit_cost from " . TABLE_ORDERS . " where order_id = '" . $this->id . "' limit 1");
						$result = $database->fetch_array($query);
						
						return $result['deposit_cost'];
					}
				$extra_cost = 0;
                $billing_method_id = $this->fetch_data_item('billing_method_id', 2);
					
				if ($billing_method_id == '1') {
					if (tep_user_requires_deposit($this->user_id)) {
						$extra_cost = REQUIRE_DEPOSIT_AMOUNT;
					}
				}
				return $extra_cost;
			}
			
			//Work out if the client has any discount available and if so then work out the cost.
			function create_order_discount_price($cost) {
					$promo_code = $this->fetch_data_item('promo_code', '');
					$run_discount = false;
						if(!empty($promo_code) && tep_promotional_code_is_valid($promo_code)) {
							$promotional_id = tep_fetch_promotional_id($promo_code);
							$details = tep_fetch_promotional_details($promotional_id);
							
								if ($details['discount_type'] == '1') {
									//Cash off, just subtract amount.
									$cost = ($cost - $details['discount_amount']);
								} elseif ($details['discount_type'] == '2') {
									//Percent off.  Reduce by percent.
									$percent_off = ($details['discount_amount']/100);
									$percent_modifier = (1-$percent_off);
									$cost = ($cost*$percent_modifier);
								} else {
									//Others to be added.
								}
							$run_discount = true;
						}
					$percentage_reduction = tep_fetch_user_percentage_reduction($this->user_id);
					$percentage_reduction /= 100;
					$percentage_reduction = (1 - $percentage_reduction);
					
						if ($percentage_reduction < 1) {
							$run_discount = true;
							$cost *= $percentage_reduction;
						}
						if (!$run_discount) {
							$cost = 0;
						}
				return $cost;
			}
			
			function create_order_credit_price($total) {
                global $database;

                // Its very important here to make sure we are using the DB value of billing_method_id, not
                // the passed in or default value, which may be incorrect.

                if (is_array($this->result) && array_key_exists('billing_method_id', $this->result)) {
                    $billing_method_id = $this->result['billing_method_id'];
                } else {
                    $billing_method_id = $this->fetch_data_item('billing_method_id');
                }

				$account = new account($this->user_id, '', $billing_method_id);
				$credit = 0;
				$credit_amount = $account->fetch_available_credit();
					
				if ($credit_amount > 0) {
					$credit = $credit_amount;
				}
				if ($credit > $total) {
					$credit = $total;
                }
                $this->credit = $credit;
				return $credit;
			}
			
			function fetch_order() {
				global $database;
				$order_query = $database->query("select o.user_id, o.base_cost, o.extended_cost, o.order_issue, o.equipment_cost, o.discount_cost, o.order_total, o.date_added, o.date_schedualed, o.deposit_cost, o.last_modified, o.last_modified_by, o.date_completed, o.address_id, o.order_total, o.order_type_id, o.order_status_id, o.extra_cost, o.extra_cost_description, o.inserted_order_type_id, o.billing_method_id, od.number_of_posts, od.special_instructions, od.admin_comments, od.installer_comments, o.completed_details, o.placed_by from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od where o.order_id = '" . $this->id . "' and o.order_id = od.order_id limit 1");
				$order_result = $database->fetch_array($order_query);
				//error_log("SQL: select o.user_id, o.base_cost, o.extended_cost, o.order_issue, o.equipment_cost, o.discount_cost, o.order_total, o.date_added, o.date_schedualed, o.deposit_cost, o.last_modified, o.date_completed, o.address_id, o.order_total, o.order_type_id, o.order_status_id, o.extra_cost, o.extra_cost_description, o.inserted_order_type_id, od.number_of_posts, od.special_instructions, od.admin_comments, od.installer_comments, o.completed_details, o.placed_by from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od where o.order_id = '" . $this->id . "' and o.order_id = od.order_id limit 1,\nRESULT: ".var_export($order_query,true)."\nDATA: ".var_export($order_result,true));
				//error_log("BACKTRACE: ".var_export(debug_backtrace(),true));
				if($order_query===false or !is_array($order_result))
				{
					$order_result=array();
				}
				
				$addressdata=tep_fetch_address_details($order_result['address_id']);
				if(is_array($addressdata))
				{  // mjp various errors in errorlog
                    $order_result = array_merge($order_result, $addressdata);
				}
				//error_log("ADDRESS_DATA: ".var_export( tep_fetch_address_details($order_result['address_id']),true));
				$order_result['order_type_name'] = tep_get_order_type_name($this->id);
				$order_result['promo_code'] = tep_db_get_order_promotional_code($this->id);
				
				//temp fix
				
				$order_result['optional'] = tep_get_equipment_assigned_to_order($this->id);
				$order_result['equipment'] = tep_get_other_equipment_assigned_to_order($this->id);
				
				$order_result['order_type_name'] = (isset($order_result['order_type_id']))?tep_get_order_type_name($order_result['order_type_id']):'';
				$order_result['order_status_name'] = (isset($order_result['order_status_id']))?tep_get_order_status_name($order_result['order_status_id']):'';
					
				if(isset($order_result['order_type_id']) and $order_result['order_type_id'] == '2') {
					$query = $database->query("select service_call_reason_id, service_call_detail_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $this->id . "' limit 1");
					$result = $database->fetch_array($query);
					
					$order_result['service_call_reason_id'] = $result['service_call_reason_id'];
					$order_result['service_call_detail_id'] = $result['service_call_detail_id'];
					
						if (empty($order_result['service_call_reason_id'])) {
							$order_result['service_call_reason_id'] = '7';
						}

                }	

                $miss_utility_sql = "select agent_requested, has_lamp, has_gas_lamp, contacted from " . TABLE_ORDERS_MISS_UTILITY;
                $miss_utility_sql.= ' where order_id = ' . $this->id . ' limit 1';

                $muq = $database->query($miss_utility_sql);

                $order_result['miss_utility_yes_no'] = "no";
                $order_result['lamp_yes_no'] = "no";
                $order_result['lamp_use_gas'] = "no";
                $order_result['contacted'] = "no";

                while ($row = $database->fetch_array($muq)) {
                    $order_result['miss_utility_yes_no'] = ($row['agent_requested'] == 1) ? "yes" : "no";
                    $order_result['lamp_yes_no'] = ($row['has_lamp'] == 1) ? "yes" : "no";
                    $order_result['lamp_use_gas'] = ($row['has_gas_lamp'] == 1) ? "yes" : (($row['has_gas_lamp'] == 0) ? "no" : "unsure");
                    $order_result['contacted'] = ($row['contacted'] == 1) ? "yes" : "no";
                }

				return $order_result;
			}
			
			function return_result() {
				return $this->result;
            }

            function fetch_accepted_installer_id() {
                global $database;

                $query = $database->query("SELECT installer_id FROM " . TABLE_INSTALLERS_TO_ORDERS . " WHERE order_id = '{$this->id}' LIMIT 1");
                $accepted_installer_id = 0;
                while ($result = $database->fetch_array($query)) {
                    $accepted_installer_id = $result['installer_id'];
                }

                return $accepted_installer_id;
            }

			function flag_and_hold() {
                global $database;

                $sql1 = "UPDATE " . TABLE_ORDERS . " SET order_issue = '1', order_status_id = '" . ORDER_STATUS_ONHOLD . "' WHERE order_id = '{$this->id}' LIMIT 1";
                $sql2 = "SELECT address_id, order_type_id FROM " . TABLE_ORDERS . " WHERE order_id = '{$this->id}' LIMIT 1";

                $query = $database->query($sql1);
                $count = $database->affected_rows();
                $query = $database->query($sql2);

                // Figure out what kind of order this is...  Don't assume we've fetched the full order yet.
                if ($result = $database->fetch_array($query)) {
                    $order_type_id = $result['order_type_id'];
                    $address_id = $result['address_id'];
                } else {
                    $order_type_id = 0;
                    $address_id = 0;
                }

                // Flag and hold associated pending service calls and removals
                if ($order_type_id == ORDER_TYPE_INSTALL) {
                    $query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '{$address_id}' and order_type_id IN ('2', '3') and order_status_id = '1'");
                    while ($result = $database->fetch_array($query)) {
                        $order_id = $result['order_id'];
                        $hold_order = new orders('other', $order_id);
                        $count += $hold_order->flag_and_hold();
                    }
                }

                return $count;
            }

            function has_agent_panel() {
                global $database;

                $query = $database->query("SELECT COUNT(*) AS count FROM " . TABLE_EQUIPMENT_TO_ORDERS . " WHERE equipment_group_id = '5' AND order_id = '" . $this->id . "'");
                $result = $database->fetch_array($query);
                $count = $result['count'];
                return (bool) $count;
				return 0;
            }
	}
?>
