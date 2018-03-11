<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get');
		
	$order_view = tep_fill_variable('order_view', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '1');
	$order_type = tep_fill_variable('order_type', 'get', '');
	$agent_id = tep_fill_variable('agent_id', 'get', '');
	$show_house_number = tep_fill_variable('show_house_number', 'get', '');
	$show_street_name = tep_fill_variable('show_street_name', 'get', '');
	$show_city = tep_fill_variable('show_city', 'get', '');
	
	if (!empty($oID)) {
		$query = $database->query("select o.order_id from " . TABLE_ORDERS . " o, " . TABLE_USERS . " u where o.order_id = '" . $oID . "' and o.user_id = u.user_id and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()). "' limit 1");
		$result = $database->fetch_array($query);
		if (empty($result['order_id'])) {
			$oID = '';
			$page_action = '';
		}
	}
	
	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	if ($page_action == 'edit_confirm') {
		//Do all the checking.
		$order = new orders('fetch', $oID);
		//$data = $order->return_result();
		$optional = tep_fill_variable('optional');
		$optional = parse_equipment_array($optional);
		//var_dump($optional);
		$order_data = $order->return_result();
		if ($order_data['order_type_id'] == '1') {	
		//die('11');
			$house_number = tep_fill_variable('house_number');
	    	$street_name = tep_fill_variable('street_name');
			$city = tep_fill_variable('city');
			$state = tep_fill_variable('state_id');
			$county = tep_fill_variable('county_id');
			$zip = tep_fill_variable('zip');
			$zip4 = tep_fill_variable('zip4');
			$cross_street_directions = tep_fill_variable('cross_street_directions');
			$number_of_posts = tep_fill_variable('number_of_posts');
			$special_instructions = tep_fill_variable('special_instructions');
					
			$card_submit = tep_fill_variable('card_submit');
						
			$fail = false;
			if (empty($number_of_posts) || !is_numeric($number_of_posts)) {
				$error->add_error('order_view', 'Please enter the Number of Posts.');
				$fail = true;
			}
	    	if (empty($house_number)) {
				$error->add_error('order_view', 'Please enter a House Number.');
				$fail = true;
			}
	    	if (empty($street_name)) {
				$error->add_error('order_view', 'Please enter a Street Name.');
				$fail = true;
			}
			if (empty($city)) {
				$error->add_error('order_view', 'Please enter a City.');
				$fail = true;
			}
			if (empty($zip)) {
				$error->add_error('order_view', 'Please enter a Zip Code.');
				$fail = true;
			}
			if (empty($state)) {
				$error->add_error('order_view', 'Please select a State.');
				$fail = true;
			}
			if (empty($county)) {
				$error->add_error('order_view', 'Please select a County.');
				$fail = true;
			}
			if (empty($cross_street_directions)) {
				$error->add_error('order_view', 'Please enter Cross Street/Directions.');
				$fail = true;
	    	}
			if (!$fail && empty($zip4)) {
				$zip4_class=new zip4($house_number.' '.$street_name,tep_get_state_name($state), $city, $zip);
				if ($zip4_class->search()) {
					$zip4_code = $zip4_class->return_zip_code();
				} else {
	    			$error->add_error('order_view', 'Either the address is invalid or there is a problem with the system.  The zip 4 address was not able to be fetched.');
				}
			}
		}
    	$job_start_date = tep_fill_variable('job_start_date');
		if (empty($job_start_date)) {
			$error->add_error('order_view', 'Please enter a Job Start Date.');
            $fail = true;
		} else {
    		$date_schedualed = strtotime($job_start_date);
			if ($date_schedualed < time()) {
                $error->add_error('order_view', 'That Job Start Date is in the past, please try again.');
                $fail = true;
			}
		}
		//Work out if the cost will be any more and if the agent is credit card billable. 
		//If so we need the number and detaiils.
		$card_request = false;
		if (false) {
            $total = $order_data['order_total'];
			$query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
			$result = $database->fetch_array($query);
			if ($order_data['order_type_id'] == '1') {	
				$order_data['special_instructions'] = $special_instructions;
			    $order_data['optional'] = $optional;
				$order_data['address_id'] = $result['address_id'];
				$order_data['county'] = $county;
				$order_data['date_schedualed'] = $date_schedualed;
			    $order_data['optional'] = $optional;
			} else {
				$order_data['address_id'] = $result['address_id'];
				$order_data['date_schedualed'] = $date_schedualed;
			    $order_data['optional'] = $optional;
			}
		
			$query = $database->query("select zip4 from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "' limit 1");
			$result = $database->fetch_array($query);
            $order_data['zip4'] = $result['zip4'];

			$pre_order = new orders('', $oID, $order_data, $order_data['user_id']);
			$new_total = $pre_order->fetch_order_total();
					
			if ($new_total > $total) {
				$difference = $new_total - $total;
				//Need to request their card number
				if (empty($card_submit)) {
					$card_request = true;
				} else {
					//Check the card.
	    			$cc_type = tep_fill_variable('cc_type');
				    $cc_name = tep_fill_variable('cc_name');
					$cc_number = str_replace(array('-', ' '), '', tep_fill_variable('cc_number'));
					$cc_month = tep_fill_variable('cc_month');
					$cc_year = tep_fill_variable('cc_year');
					$cc_verification_number = tep_fill_variable('cc_verification_number');
					$cc_billing_address = tep_fill_variable('cc_billing_address');
								
					if (empty($cc_name)) {
					    $error->add_error('order_view', 'Please enter your Credit Card Name.');
					}
					if (empty($cc_number)) {
					    $error->add_error('order_view', 'Please enter your Credit Card Number.');
					}
					if (empty($cc_verification_number)) {
						$error->add_error('order_view', 'Please enter your Verification Number.');
					}
					if (empty($cc_billing_address)) {
						$error->add_error('order_view', 'Please enter your Billing Address.');
					}
					if (!$error->get_error_status('order_view')) {
						$cc_proccessing = new cc_proccessing();
					    $error_code = '';
						$error_text = '';
						$cc_proccessing->pre_transaction($cc_number, $cc_type, $error_code, $error_text);
											
						if (!empty($error_code)) {
							$error->add_error('order_view', 'The credit card you entered is invalid.  Please try again.');
							$error->cc_error(__FILE__.':'.__LINE__.' '.$user->fetch_user_name().'('.$user->fetch_user_id().") \"$error_text\"");

							$card_request = true;
						} else {
							$cc_proccessing->set_proccessing_variable('credit_card_number', $cc_number);
					    	$cc_proccessing->set_proccessing_variable('charge_type', 'AUTH');
							$cc_proccessing->set_proccessing_variable('expire_month', $cc_month);
					    	$cc_proccessing->set_proccessing_variable('expire_year', $cc_year);
							$cc_proccessing->set_proccessing_variable('credit_card_verification_number', $cc_verification_number);
					    	$cc_proccessing->set_proccessing_variable('charge_total', number_format($difference, 2));
							$cc_proccessing->set_proccessing_variable('order_user_id', $user->fetch_user_id());
							$cc_proccessing->set_proccessing_variable('reference_id', $_SERVER['REMOTE_ADDR']);
							$cc_proccessing->set_proccessing_variable('card_brand', $cc_type);
							
							$cc_proccessing->preform_transaction();
							if ($cc_proccessing->return_response() == 1) {
								//accepted
									
							} else {
								$error->add_error('order_view', 'There was an error processing the credit card you entered.  Please try again.');
								$error->cc_error(__FILE__.':'.__LINE__.' '. $user->fetch_user_name().'('.$user->fetch_user_id().") rcode ".$cc_proccessing->return_response().' "'. implode("\n",$cc_proccessing->error_messages()) ."\"");
					    		$card_request = true;
							}
						}
					}
		    	}
			}
    	} // end if Credit Card order
		if (!$error->get_error_status('order_view') && !$card_request) {
			//die('aa');
    		$query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
			$result = $database->fetch_array($query);
			if ($order_data['order_type_id'] == '1') {	
    			if ($result['address_id'] != NULL) {
					$database->query("update " .TABLE_ADDRESSES . " set house_number = '" . $house_number . "', street_name = '" . $street_name . "', city = '" . $city . "', zip = '" . $zip . "', state_id = '" . $state . "', county_id = '" . $county . "', " . ((!empty($zip4_code)) ? "zip4 = '" . $zip4_code . "', " : '') . "adc_number = '', number_of_posts = '" . $number_of_posts . "', cross_street_directions = '" . $cross_street_directions . "' where address_id = '" . $result['address_id'] . "' limit 1");
				}
			    if ($order_data['order_type_id'] == '1') {	
				    $order_data['special_instructions'] = $special_instructions;
			        $order_data['optional'] = $optional;
				    $order_data['address_id'] = $result['address_id'];
				    $order_data['county'] = $county;
				    $order_data['date_schedualed'] = $date_schedualed;
				    $order_data['number_of_posts'] = $number_of_posts;
			        $order_data['optional'] = $optional;
			    } else {
			    	$order_data['address_id'] = $result['address_id'];
			    	$order_data['date_schedualed'] = $date_schedualed;
			        $order_data['optional'] = $optional;
		    	}
				
				$order_data['house_number'] = $house_number;
				$order_data['city'] = $city;
				$order_data['street_name'] = $street_name;
				$order_data['zip4'] = $zip;

				
				$query = $database->query("select zip4 from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "' limit 1");
				$result = $database->fetch_array($query);
                $order_data['zip4'] = $result['zip4'];

			    $order = new orders('update', $oID, $order_data, $order_data['user_id']);
				$page_action = 'view';
			} else {
				$page_action = 'edit';
            }
        }
	} elseif ($page_action == 'delete_confirm') {
        $cancelled_order = new orders('cancel', $oID, array('reason' => 'Order has been canceled by Order Manager.'));
        $order_data = $cancelled_order->return_result();
		$page_action = 'view';
	}

	
				if (empty($oID)) {
					
					$where = '';
						//$where .= " and o.order_status_id = '3'";
						if(empty($order_status)){
						$where .= " and o.order_status_id = '3'";}elseif (!empty($order_status)) {
							$where .= " and o.order_status_id = '" . $order_status . "'";
						}
						if (!empty($order_type)) {
							$where .= " and o.order_type_id = '" . $order_type . "'";
						}
//print "select o.order_id, o.order_total, ot.name as order_type_name, os.order_status_name, a.house_number, a.street_name, a.city from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a where o.user_id = '" . $user->fetch_user_id() . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id " . $where . " order by o.date_schedualed DESC, '20', 'o.order_id'";//exit;
			
$listing_split = new split_page("select o.order_id, o.order_total, ot.name as order_type_name, os.order_status_name, o.order_status_id, a.house_number, a.street_name, a.city, ud.firstname, ud.lastname from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where o.user_id = u.user_id and u.agency_id = '" . tep_fetch_order_manager_agency($user->fetch_user_id()) . "'" . ((!empty($agent_id)) ? " and u.user_id = '" . $agent_id . "'" : '') . " " . (!empty($show_house_number) ? " and a.house_number = '" . $show_house_number . "'" : '') . " " . (!empty($show_street_name) ? " and (a.street_name = '" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "%' or a.street_name like '" . $show_street_name . "%')" : '') . " " . (!empty($show_city) ? " and (a.city = '" . $show_city . "' or a.city like '%" . $show_city . "' or a.city like '%" . $show_city . "%' or a.city like '" . $show_city . "%')" : '') . " and u.user_id = ud.user_id and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id " . $where . " order by o.date_schedualed DESC", '500', 'o.order_id');
						if ($listing_split->number_of_rows > 0) {
							
						$query = $database->query($listing_split->sql_query);
						    foreach($database->fetch_array($query) as $result) {
								$vars['split_result'][] = $result;
	
							}

						}
						//$vars['show'] = $show;
						$vars['listing_split'] = $listing_split;
						$vars['pagination'] = tep_get_all_get_params(array('page', 'info', 'x', 'y'));
						$vars['pulldowns']['orderType'] = tep_draw_order_type_pulldown_bgdn('order_type', $order_type, 'change-submit', array(array('id' => '', 'name' => 'All Orders')));
						//print_r($vars['pulldowns']['order']);
						$vars['pulldowns']['orderStatus'] = tep_draw_orders_status_pulldown_bgdn('order_status', $order_status, array(array('id' => '', 'name' => 'Any')), 'change-submit');
						echo $twig->render('aom/orders_list.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

				} else {
					
					$order = new orders('fetch', $oID);
					
					$order_data = $order->return_result();
					
					
					if ($page_action == 'edit') {
						
						if (!empty($card_request)) 
			{
				$cc_type = tep_fill_variable('cc_type', 'post', tep_fill_variable('cc_type', 'session'));
				$cc_name = tep_fill_variable('cc_name', 'post', tep_fill_variable('cc_name', 'session'));
				$cc_number = tep_fill_variable('cc_number', 'post', tep_fill_variable('cc_number', 'session'));
				$cc_month = tep_fill_variable('cc_month', 'post', tep_fill_variable('cc_month', 'session'));
				$cc_year = tep_fill_variable('cc_year', 'post', tep_fill_variable('cc_year', 'session'));
				$cc_verification_number = tep_fill_variable('cc_verification_number', 'post', tep_fill_variable('cc_verification_number', 'session'));
				$cc_billing_street = tep_fill_variable('cc_billing_street', 'post', tep_fill_variable('cc_billing_street', 'session'));
				$cc_billing_city = tep_fill_variable('cc_billing_city', 'post', tep_fill_variable('cc_billing_city', 'session'));
				$cc_billing_zip = tep_fill_variable('cc_billing_zip', 'post', tep_fill_variable('cc_billing_zip', 'session'));
			}


			if (isset($_POST['optional'])) {
								$optional = parse_equipment_array($_POST['optional']);
							} else {
								$optional = tep_convert_view_equipment_array_to_edit($order_data['optional']);
							}
							$tep = tep_generate_available_equipment_string($order_data['order_type_id'], $user->fetch_service_level_id(), $user->fetch_user_id(), $optional, $order_data['zip4'], $order_data['address_id'], true, true);

			if (!empty($card_request)) 
			{
				$cc_type = tep_fill_variable('cc_type', 'post', tep_fill_variable('cc_type', 'session'));
				$cc_name = tep_fill_variable('cc_name', 'post', tep_fill_variable('cc_name', 'session'));
				$cc_number = tep_fill_variable('cc_number', 'post', tep_fill_variable('cc_number', 'session'));
				$cc_month = tep_fill_variable('cc_month', 'post', tep_fill_variable('cc_month', 'session'));
				$cc_year = tep_fill_variable('cc_year', 'post', tep_fill_variable('cc_year', 'session'));
				$cc_verification_number = tep_fill_variable('cc_verification_number', 'post', tep_fill_variable('cc_verification_number', 'session'));
				$cc_billing_street = tep_fill_variable('cc_billing_street', 'post', tep_fill_variable('cc_billing_street', 'session'));
				$cc_billing_city = tep_fill_variable('cc_billing_city', 'post', tep_fill_variable('cc_billing_city', 'session'));
				$cc_billing_zip = tep_fill_variable('cc_billing_zip', 'post', tep_fill_variable('cc_billing_zip', 'session'));

			}

							$vars['order_data'] = $order_data;
							if(isset($card_request)) {
								$vars['card_request'] = $card_request;
							}
							if(isset($cc_type)) {
								$vars['pulldowns']['cc_type'] = tep_draw_credit_card_type_pulldown_bgdn('cc_type', $cc_type);
							}
							$vars['page_action'] = $page_action;
							$vars['pulldowns']['state'] = tep_draw_state_pulldown_bgdn('state_id', $order_data['state_id']);
							$vars['pulldowns']['county'] = tep_draw_county_pulldown_bgdn('county_id', '', $order_data['county_id']);
							
							$vars['k'] = tep_generate_available_equipment_string($order_data['order_type_id'], tep_get_service_level_id($order_data['user_id']), $order_data['user_id'], $optional, $order_data['zip4'], $order_data['address_id'], true, true);
						$vars['order_data']['oID'] = $oID;
						echo $twig->render('aom/edit_order.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

			
					} else {
						
						
					//tep_create_orders_history($oID, '1', 'Thank you for your order.  It has now been received and is awaiting acceptance.');
						$user_query = $database->query("select u.agent_id, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $order_data['user_id'] . "' and u.user_id = ud.user_id limit 1");
						$user_result = $database->fetch_array($user_query);
						
						$vars = array();
						$vars['optional'] = $order_data['optional'];
						$vars['order_data'] = $order_data;
						$vars['user_result'] = $user_result;
						$vars['eq'] = tep_create_view_equipment_string($order_data['optional']);
						//print_r($vars['order_data']['optional']);
						$vars['status_history'] = tep_fetch_order_history($oID);
						$vars['return_page'] = isset($_GET['return_page']) ? $_GET['return_page'] : FILENAME_ORDER_VIEW;
						$vars['page_action'] = $page_action;
						$vars['order_data']['oID'] = $oID;
						echo $twig->render('aom/view_order.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
					}
				}
