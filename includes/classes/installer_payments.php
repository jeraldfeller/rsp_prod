<?php
	class installer_payments {
		var $installer_id;

        function __construct($installer_id) {
			$this->installer_id = $installer_id;
		}
		
		function fetch_end_month_balance($year, $month) {
			global $database;

			$month_end = mktime(0, 0,-1, ($month + 1), 1, $year);
			
			$query = $database->query("select installer_payment_running_total from " . TABLE_INSTALLER_PAYMENTS . " where user_id = '" . $this->installer_id . "' and date_added <= '" . $month_end . "' order by date_added DESC limit 1");
			$result = $database->fetch_array($query);
			
				if (empty($result['installer_payment_running_total'])) {
					return 0;
				} else {
					return $result['installer_payment_running_total'];
				}
		}
		
		function fetch_year_month_list() {
			global $database;
			
			$return_array = array();
			
			$query = $database->query("select year, month from " . TABLE_INSTALLER_PAYMENTS . " where user_id = '" . $this->installer_id . "' group by year, month order by year, month DESC");
				foreach($database->fetch_array($query) as $result){
					$return_array[] = array('id' => $result['month'].'-'.$result['year'], 'name' => $result['month'].'/'.$result['year']);
				}
			
			return $return_array;
		}
		
		function fetch_balance_list($year, $month) {
			global $database;
			$month_start = mktime(0, 0,0, $month, 1, $year);
			$month_end = mktime(0, 0,-1, $month + 1, 1, $year);
			
			$return_array = array();
			$query = $database->query("select ip.date_added, ip.direction, ip.installer_payment_total, ip.installer_payment_running_total, ipd.installer_payment_description from " . TABLE_INSTALLER_PAYMENTS . " ip, " . TABLE_INSTALLER_PAYMENTS_DESCRIPTION . " ipd where ip.user_id = '" . $this->installer_id . "' and ip.date_added >= '" . $month_start. "' and ip.date_added <= '" . $month_end . "' and ip.installer_payment_id = ipd.installer_payment_id order by ip.installer_payment_id ASC");
				foreach($database->fetch_array($query) as $result){
					$return_array[] = array('date_added' => $result['date_added'], 'type' => (($result['direction'] == '1') ? 'Credit' : 'Debit'), 'total' => $result['installer_payment_total'], 'running_total' => $result['installer_payment_running_total'], 'description' => $result['installer_payment_description']);
				}
			
			return $return_array;
		}
		
		function insert_installer_payment($order_id, $extra_payment = false) {
			global $database;
			
			$query = $database->query("select order_type_id, address_id, special_conditions from " . TABLE_ORDERS . " where order_id - '" . $order_id . "' limit 1");
			$result = $database->fetch_array($query);
			
			$order_type_id = $result['order_type_id'];
			
			$special_array = array();
				if (!empty($result['special_conditions'])) {
					$special_array = explode('|', $result['special_conditions']);
				}
			
			$address_query = $database->query("select zip4 from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "' limit 1");
			$address_result = $database->fetch_array($address_query);
			
			$service_area_id = tep_fetch_zip4_service_area($address_result['zip4']);
			
			//Now the equipment.  Note here that we only deal with equipment that actually occured.
			$equipment_array = array();
			$query = $database->query("select equipment_id, method_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $order_id . "'");
				foreach($database->fetch_array($query) as $result){
					$equipment_array[$result['equipment_id']] = $result['method_id'];
				}
			
			$direction = '1';
			$previous_total = $this->fetch_end_month_balance(date("Y", mktime()), date("n", mktime()));
			
			$order_total = 0;
			$order_string = '';

			$type_query = $database->query("select installation_cost, name from " . TABLE_ORDER_TYPES . " where order_type_id = '" . $order_type_id . "' limit 1");
			$type_result = $database->fetch_array($type_query);
				if ($type_result['installation_cost'] > 0) {
					$order_string = $type_result['name'] . ' : $' . number_format($type_result['installation_cost'], 2);
					$order_total += $type_result['installation_cost'];
				}
			
			$service_area_query = $database->query("select installation_cost, installer_modifier, name from " . TABLE_SERVICE_AREAS . " where service_area_id = '" . $service_area_id . "' limit 1");
			$sevice_area_result = $database->fetch_array($service_area_query);
				if (!empty($sevice_area_result['installation_cost']) && ($sevice_area_result['installation_cost'] > 0)) {
					$order_string .= '<br>' . $sevice_area_result['name'] . ' : $' . number_format($sevice_area_result['installation_cost']);
					$order_total += $sevice_area_result['installation_cost'];
				} elseif (!empty($sevice_area_result['installer_modifier']) && ($sevice_area_result['installer_modifier'] > 0)) {
					$new_total = $order_total * $sevice_area_result['installer_modifier'];
					//$difference = $new_total - $total;
                    $difference = $new_total - $order_total;
					$order_string .= '<br>' . $sevice_area_result['name'] . ' : $' . number_format($difference, 2);
					$order_total = $new_total;
				}
			
			//Equipment.  Built on type and order type.
				if (is_array($equipment_array)) {
					while(list($equipment_id, $method) = each($equipment_array)) {
							if ($method == '1') {
								//Install.
								$string = 'installer_install_payment';
							} else {
								//Remove.
								$string = 'installer_remove_payment';
							}
						$query = $database->query("select name, " . $string . " as total from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment_id . "' limit 1");
						$result = $database->fetch_array($query);
							if (!empty($result['total']) && ($result['total'] > 0)) {
								$order_string .= '<br>' . $result['name'] . ' : $' . number_format($result['total'], 2);
								$order_total += $result['total'];
							}
					}
				}
				if (is_array($special_array)) {
					reset($special_array);
						for ($n = 0, $m = count($special_array); $n < $m; $n++) {
							$special_query = $database->query("select name, installation_cost from " . TABLE_SPECIAL_INSTALLATION_COSTS . " where code = '" . $special_array[$n] . "' limit 1");
							$special_result = $database->fetch_array($special_query);
								if (!empty($special_result['installation_cost']) && ($special_result['installation_cost'] > 0)) {
									$order_string .= '<br>' . $special_result['name'] . ' : $' . number_format($special_result['installation_cost'], 2);
									$order_total += $special_result['installation_cost'];
								}
						}
				}
				
				if ($extra_payment) {
						if (defined('INSTALLER_PAYMENT_SCHEDUALE')) {
							$explode = explode('|', INSTALLER_PAYMENT_SCHEDUALE);
							$current_time = date("G.i", mktime());
								for ($n = 0, $m = count($explode); $n < $m; $n++) {
									$temp_explode = explode('-', $explode[$n]);
									$this_time = str_replace(':', '.', $temp_explode[0]);
										if ($current_time <= $this_time) {
											$order_string .= '<br>Bonus for before '. $temp_explode[0] . ' : $' . number_format($temp_explode[1], 2);
											$order_total += $temp_explode[1];
											break;
										}
								}
						}
				}
				
				if ($order_total > 0) {
					$running_total = $previous_total + $order_total;
					
					$database->query("insert into " . TABLE_INSTALLER_PAYMENTS . " (user_id, date_added, order_id, direction, installer_payment_total, installer_payment_running_total, month, year) values ('" . $this->installer_id . "', '" . mktime() . "', '" . $order_id . "', '" . $direction . "', '" . $order_total . "', '" . $running_total . "', '" . date("n", mktime()) . "', '" . date("Y", mktime()) . "')");
					$database->query("insert into " . TABLE_INSTALLER_PAYMENTS_DESCRIPTION . " (installer_payment_id, installer_payment_description) values ('" . $database->insert_id() . "', '" . $order_string . "')");
				}
		}
		
		function insert_installer_payout($amount, $reason = '') {
			global $database;
			
			$direction = '0';
			
			$previous_total = $this->fetch_end_month_balance(date("Y", mktime()), date("n", mktime()));
			
			$running_total = $previous_total -= $amount;
			
			$database->query("insert into " . TABLE_INSTALLER_PAYMENTS . " (user_id, date_added, direction, installer_payment_total, installer_payment_running_total, month, year) values ('" . $this->installer_id . "', '" . mktime() . "', '" . $direction . "', '" . $amount . "', '" . $running_total . "', '" . date("n", mktime()) . "', '" . date("Y", mktime()) . "')");
			$database->query("insert into " . TABLE_INSTALLER_PAYMENTS_DESCRIPTION . " (installer_payment_id, installer_payment_description) values ('" . $database->insert_id() . "', '" . $reason . "')");
		}
	}
?>