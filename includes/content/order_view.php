<?php // Updated 1/17/2013 brad@brgr2.com Removed option for users to edit/remove scheduled or complete orders.
	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get');
		
	$order_view = tep_fill_variable('order_view', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '');
	$order_type = tep_fill_variable('order_type', 'get', '');
	$show['house_number'] = tep_fill_variable('show_house_number', 'get', '');
	$show['street_name'] = tep_fill_variable('show_street_name', 'get', '');
	$show['city'] = tep_fill_variable('show_city', 'get', '');
	//$show_house_number = tep_fill_variable('show_house_number', 'get', '');
	//$show_street_name = tep_fill_variable('show_street_name', 'get', '');
	//$show_city = tep_fill_variable('show_city', 'get', '');
	if (!empty($oID)) 
	{
		$query = $database->query("select user_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
		$result = $database->fetch_array($query);
		if ($result['user_id'] != $user->fetch_user_id()) 
		{
			$oID = '';
			$page_action = '';
		}
	}
	
	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
    if (!empty($oID)) 
	{		
        $order = new orders('fetch', $oID);

        $order_data = $order->return_result();
        $aID = $order_data['address_id'];

        if (tep_get_order_status_name($order_data['order_status_id']) != $order_status && !empty($order_status)) 
		{
            $oID = '';
            $page_action = '';
        }
    }
	if ($page_action == 'edit_confirm') 
	{
		//Do all the checking.
        
		if ($order_data['order_type_id'] == '1') 
		{	
			$house_number = tep_fill_variable('house_number', 'post');
			$street_name = tep_fill_variable('street_name', 'post');
			$city = tep_fill_variable('city', 'post');
			$state = tep_fill_variable('state_id', 'post');
			$county = tep_fill_variable('county_id', 'post');
			$zip = tep_fill_variable('zip', 'post');
			$zip4 = tep_fill_variable('zip4', 'post');
			$cross_street_directions = tep_fill_variable('cross_street_directions', 'post');
			$number_of_posts = tep_fill_variable('number_of_posts', 'post');
			$special_instructions = tep_fill_variable('special_instructions', 'post');
			$optional = tep_fill_variable('optional', 'post');
			$optional = parse_equipment_array($optional);
			
			$card_submit = tep_fill_variable('card_submit', 'post');
				
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
			if (!$fail && empty($zip4)) 
			{
				$zip4_class=new zip4($house_number.' '.$street_name,tep_get_state_name($state), $city, $zip);
				if ($zip4_class->search()) 
				{
					$zip4_code = $zip4_class->return_zip_code();
				} 
				else 
				{
					$error->add_error('order_view', 'Either the address is invalid or there is a problem with the system.  The zip 4 address was not able to be fetched.');
				}
			}
		}
		$job_start_date = tep_fill_variable('job_start_date');
		$special_instructions = tep_fill_variable('special_instructions');		
		$county = tep_fill_variable('county_id');
		if (empty($job_start_date)) 
		{
			$error->add_error('order_view', 'Please enter an Activity Window Start Date.');
		} 
		else 
		{
			$date_schedualed = strtotime($job_start_date);
			if ($date_schedualed < time()) {
				$error->add_error('order_view', 'That Activity Window Start Date is in the past, please try again.');
			}
		}
		//Work out if the cost will be any more and if the agent is credit card billable. 
		//If so we need the number and detaiils.
		$card_request = false;
		$optional = tep_fill_variable('optional');
		$optional = parse_equipment_array($optional);
		if ( tep_fetch_agent_billing_method_id($user->fetch_user_id()) == BILLING_METHOD_CREDIT) 
		{
			$query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
            $result = $database->fetch_array($query);

			$zip_query = $database->query("select zip4 from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "' limit 1");
            $zip_result = $database->fetch_array($zip_query);

            $pre_order = new orders('fetch', $oID);
            $data = $pre_order->return_result();
            $total = $pre_order->fetch_order_total($data['zip4']);

			if ($order_data['order_type_id'] == '1') 
			{	
				
				if ($result['address_id'] != NULL) 
				{
					$database->query("update " .TABLE_ADDRESSES . " set house_number = '" . $house_number . "', street_name = '" . $street_name . "', city = '" . $city . "', zip = '" . $zip . "', state_id = '" . $state . "', county_id = '" . $county . "', " . ((!empty($zip4_code)) ? "zip4 = '" . $zip4_code . "', " : '') . "adc_number = '', number_of_posts = '" . $number_of_posts . "', cross_street_directions = '" . $cross_street_directions . "' where address_id = '" . $result['address_id'] . "' limit 1");
					echo "update " .TABLE_ADDRESSES . " set house_number = '" . $house_number . "', street_name = '" . $street_name . "', city = '" . $city . "', zip = '" . $zip . "', state_id = '" . $state . "', county_id = '" . $county . "', " . ((!empty($zip4_code)) ? "zip4 = '" . $zip4_code . "', " : '') . "adc_number = '', number_of_posts = '" . $number_of_posts . "', cross_street_directions = '" . $cross_street_directions . "' where address_id = '" . $result['address_id'] . "' limit 1";
                }
				$data['house_number'] = $house_number;
				$data['city'] = $city;
				$data['street_name'] = $street_name;
				$data['zip4'] = $zip;
				
				$data['special_instructions'] = $special_instructions;
			    $data['optional'] = $optional;
			    $data['address_id'] = $result['address_id'];
			    $data['county'] = $county;
			    $data['date_schedualed'] = $date_schedualed;
			    $data['number_of_posts'] = $number_of_posts;
				$data['optional'] = $optional;
			} 
			else 
			{
                $data['special_instructions'] = $special_instructions;
			    $data['optional'] = $optional;
			    $data['county'] = $county;
			    $data['date_schedualed'] = $date_schedualed;
				$data['optional'] = $optional;
            }
            $data['zip4'] = $zip_result['zip4'];

			$order = new orders('', $oID, $data);
			$new_total = $order->fetch_order_total($data['zip4']);
			
			if ($new_total > $total) 
			{
				$difference = $new_total - $total;
				//Need to request their card number
				if (empty($card_submit)) 
				{
					$card_request = true;
					$error->add_error('order_view', 'Please enter your credit card details to pay the difference of $'.number_format($difference, 2).' for the updated order.', 'warning');
				} 
				else 
				{
					//Check the card.
					
					$cc_type = tep_fill_variable('cc_type');
					$cc_name = tep_fill_variable('cc_name');
					$cc_number = str_replace(array('-', ' '), '', tep_fill_variable('cc_number'));
					$cc_month = tep_fill_variable('cc_month');
					$cc_year = tep_fill_variable('cc_year');
					$cc_verification_number = tep_fill_variable('cc_verification_number');
					$cc_billing_street = tep_fill_variable('cc_billing_street');
					$cc_billing_city = tep_fill_variable('cc_billing_city');
					$cc_billing_zip = tep_fill_variable('cc_billing_zip');
				
					if (empty($cc_name)) {
						$error->add_error('order_view', 'Please enter your Credit Card Name.');
					}
					if (empty($cc_number)) {
						$error->add_error('order_view', 'Please enter your Credit Card Number.');
					}
					if (empty($cc_verification_number)) {
						$error->add_error('order_view', 'Please enter your Verification Number.');
					}
					if (empty($cc_billing_street) || empty($cc_billing_city) || empty($cc_billing_zip)) {
						$error->add_error('order_view', 'Please enter your complete Billing Address.');
					}
					if (!$error->get_error_status('order_view')) 
					{
						$cc_proccessing = new cc_proccessing();
						$error_code = '';
						$error_text = '';
						$cc_proccessing->pre_transaction($cc_number, $cc_type, $error_code, $error_text);
						
						if (!empty($error_code)) 
						{
							$error->add_error('order_view', 'The credit card you entered is invalid.  Please try again.');
							$error->cc_error(__FILE__.':'.__LINE__.' '.$user->fetch_user_name().'('.$user->fetch_user_id().") \"$error_text\"");
							$card_request = true;
						} 
						else 
						{
							$agency_query = $database->query("select a.name, a.service_level_id, a.billing_method_id, a.address, a.contact_name, a.contact_phone from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.user_id = '" . $user->fetch_user_id() . "' and u.agency_id = a.agency_id limit 1");
							$agency_result = $database->fetch_array($agency_query);

							$cc_proccessing->set_proccessing_variable('bill_first_name', tep_fill_variable('user_first_name', 'session'));
							$cc_proccessing->set_proccessing_variable('bill_last_name', tep_fill_variable('user_last_name', 'session'));
							$cc_proccessing->set_proccessing_variable('bill_address_one', $cc_billing_street);
							$cc_proccessing->set_proccessing_variable('bill_city', $cc_billing_city);
							$cc_proccessing->set_proccessing_variable('bill_zip_or_postal_code', $cc_billing_zip);
							$cc_proccessing->set_proccessing_variable('bill_country_code', 'US');
							$cc_proccessing->set_proccessing_variable('bill_company', stripslashes($agency_result['name']));
							$cc_proccessing->set_proccessing_variable('bill_phone', $agency_result['contact_phone']);

							$cc_proccessing->set_proccessing_variable('order_description', tep_get_order_type_name($order_type).": $house_number $street_name");
							$infoStr = date('ymd-His').str_replace(' ','-'," $house_number $street_name");
							$cc_proccessing->set_proccessing_variable('invoice_number', $infoStr);
							$cc_proccessing->set_proccessing_variable('order_id', $infoStr);
							$cc_proccessing->set_proccessing_variable('card_brand', $cc_type);
							$cc_proccessing->set_proccessing_variable('credit_card_number', $cc_number);
							$cc_proccessing->set_proccessing_variable('charge_type', 'AUTH');
							$cc_proccessing->set_proccessing_variable('expire_month', $cc_month);
							$cc_proccessing->set_proccessing_variable('expire_year', $cc_year);
							$cc_proccessing->set_proccessing_variable('credit_card_verification_number', $cc_verification_number);
							$cc_proccessing->set_proccessing_variable('charge_total', number_format($difference, 2));
							$cc_proccessing->set_proccessing_variable('order_user_id', $user->fetch_user_id());
							$cc_proccessing->set_proccessing_variable('reference_id', $_SERVER['REMOTE_ADDR']);
							
							$cc_proccessing->preform_transaction();
							if ($cc_proccessing->return_response() == 1) 
							{
								//accepted
  $error->add_error('order_view', 'Your card was successfully charged for the difference of $'.number_format($difference, 2).'.', 'success');
  $error->cc_error(__FILE__.':'.__LINE__.' success');
								
							} 
							else 
							{
								$error->add_error('order_view', 'There was an error processing the credit card you entered.  Please try again.');
								$error->cc_error(__FILE__.':'.__LINE__.' '.$user->fetch_user_name().'('.$user->fetch_user_id().') rcode "'. $cc_proccessing->return_response() .' "'. implode("\n",$cc_proccessing->$error_messages()) .'"');
								$card_request = true;
							}
						}
					}
					else 
					{
						$card_request = true;
						$error->add_error('order_view', 'Please enter your credit card details to pay the difference of $'.number_format($difference, 2).' for the updated order.', 'warning');
					}
				}
					
			}
		}
		
		if (!$error->get_error_status('order_view') && !$card_request) 
		{
			$query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
            $result = $database->fetch_array($query);

			$zip_query = $database->query("select zip4 from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "' limit 1");
            $zip_result = $database->fetch_array($zip_query);

            $pre_order = new orders('fetch', $oID, array("zip4" => $zip_result['zip4']));
            $data = $pre_order->return_result();

			if ($order_data['order_type_id'] == '1') 
			{	
				
				if ($result['address_id'] != NULL) 
				{
					$database->query("update addresses set house_number = '" . $house_number . "', street_name = '" . $street_name . "', city = '" . $city . "', zip = '" . $zip . "', state_id = '" . $state . "', county_id = '" . $county . "', " . ((!empty($zip4_code)) ? "zip4 = '" . $zip4_code . "', " : '') . "adc_number = '', number_of_posts = '" . $number_of_posts . "', cross_street_directions = '" . $cross_street_directions . "' where address_id = '" . $result['address_id'] . "' limit 1");
					//echo "update addresses set house_number = '" . $house_number . "', street_name = '" . $street_name . "', city = '" . $city . "', zip = '" . $zip . "', state_id = '" . $state . "', county_id = '" . $county . "', " . ((!empty($zip4_code)) ? "zip4 = '" . $zip4_code . "', " : '') . "adc_number = '', number_of_posts = '" . $number_of_posts . "', cross_street_directions = '" . $cross_street_directions . "' where address_id = '" . $result['address_id'] . "' limit 1";
                }
				
				$data['house_number'] = $house_number;
				$data['city'] = $city;
				$data['street_name'] = $street_name;
				$data['zip4'] = $zip;

				$data['special_instructions'] = $special_instructions;
			    $data['optional'] = $optional;
			    $data['address_id'] = $result['address_id'];
			    $data['county'] = $county;
			    $data['date_schedualed'] = $date_schedualed;
			    $data['number_of_posts'] = $number_of_posts;
				$data['optional'] = $optional;
			} 
			else 
			{
                $data['special_instructions'] = $special_instructions;
			    $data['optional'] = $optional;
			    $data['county'] = $county;
			    $data['date_schedualed'] = $date_schedualed;
				$data['optional'] = $optional;
            }

            $data['zip4'] = $zip_result['zip4'];
			$order = new orders('update', $oID, $data);
			$page_action = 'view';
		} 
		else 
		{
			
			$page_action = 'edit';
		}
	} 
	elseif ($page_action == 'delete_confirm') 
	{
        $cancelled_order = new orders('cancel', $oID, array('reason' => 'Order has been canceled Agent.'));
        $order_data = $cancelled_order->return_result();
        $page_action = 'view';
	}
		if (empty($oID)) 
		{
			//LIST STARTS
			$where = '';

			if(empty($order_status)){
				$where .= " ";
			}elseif (!empty($order_status)) {
				$where .= " and o.order_status_id = '" . $order_status . "'";
			}
			if (!empty($order_type)) {
				$where .= " and o.order_type_id = '" . $order_type . "'";
			}
			
			//echo $show_house_number;
			
			$listing_split = new split_page("select o.order_id, o.order_total, ot.name as order_type_name, os.order_status_name, a.house_number, a.street_name, a.city from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a where o.user_id = '" . $user->fetch_user_id() . "' " . (!empty($show['house_number']) ? " and a.house_number = '" . $show['house_number'] . "'" : '') . " " . (!empty($show['street_name']) ? " and (a.street_name = '" . $show['street_name'] . "' or a.street_name like '%" . $show['street_name'] . "' or a.street_name like '%" . $show['street_name'] . "%' or a.street_name like '" . $show['street_name'] . "%')" : '') . " " . (!empty($show['city']) ? " and (a.city = '" . $show['city'] . "' or a.city like '%" . $show['city'] . "' or a.city like '%" . $show['city'] . "%' or a.city like '" . $show['city'] . "%')" : '') . " and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id " . $where . " order by o.date_schedualed DESC", '500', 'o.order_id');
			
			$vars = array();
			
			if ($listing_split->number_of_rows > 0) 
			{
				$query = $database->query($listing_split->sql_query);
                foreach($database->fetch_array($query) as $result){
					$vars['split_result'][] = $result;
				}
			}
			
			$vars['show'] = $show;
			$vars['listing_split'] = $listing_split;
			$vars['pagination'] = tep_get_all_get_params(array('page', 'info', 'x', 'y'));
			$vars['pulldowns']['orderType'] = tep_draw_order_type_pulldown_bgdn('order_type', $order_type, 'change-submit', array(array('id' => '', 'name' => 'All Orders')));
			//print_r($vars['pulldowns']['order']);
			$vars['pulldowns']['orderStatus'] = tep_draw_orders_status_pulldown_bgdn('order_status', $order_status, array(array('id' => '', 'name' => 'Any')), 'change-submit');
			echo $twig->render('agent/orders_list.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
			// LIST ENDS
	} 
	else 
	{
		//INDIVIDUAL EDIT STARTS			
		$order = new orders('fetch', $oID);
					
		$order_data = $order->return_result();
		if ($page_action == 'edit') 
		{
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
							
							$vars['k'] = tep_generate_available_equipment_string($order_data['order_type_id'], $user->fetch_service_level_id(), $user->fetch_user_id(), $optional, $order_data['zip4'], $order_data['address_id'], true, true);
						$vars['order_data']['oID'] = $oID;
						echo $twig->render('agent/edit_order.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

			
					} else {												
						//VIEW INDIVIDUAL									
						$vars = array();

						$vars['order_data'] = $order_data;

						$vars['status_history'] = tep_fetch_order_history($oID);
						$vars['return_page'] = isset($_GET['return_page']) ? $_GET['return_page'] : FILENAME_ORDER_VIEW;
						$vars['page_action'] = $page_action;
						$vars['order_data']['oID'] = $oID;
						echo $twig->render('agent/view_order.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
						//VIEW INDIVIDUAL ENDS
					}												
				}
			?>
		<?php
			if (!empty($oID)) {
				if ($page_action == 'view') {
		?>
			
			<?php
				} elseif ($page_action == 'edit') {
			?> <!-- --
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press cancel to go back to the previous page.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ORDER_VIEW; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table> -->
			<?php
				}elseif ($page_action == 'delete') {
			?>

			<?php
				}
			} else {
			?>
			
			<?php
			}
		?>