<?php
@session_start(); 
	if (!$user->can_place_orders) {
		tep_redirect(FILENAME_ORDER_CREATE_DISALLOWED);
	}
	
	if(isset($_GET['clear'])) {
		//die();
		$session->php_session_unregister('order_type_id');
        $session->php_session_unregister('order_type');
        
		$session->php_session_unregister('order_id');
		$session->php_session_unregister('credit');
		$session->php_session_unregister('agent_id');
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
		$session->php_session_unregister('install_equipment');
		$session->php_session_unregister('remove_equipment');
		$session->php_session_unregister('special_instructions');
		$session->php_session_unregister('adc_page');
		$session->php_session_unregister('adc_letter');
		$session->php_session_unregister('adc_number');
		$session->php_session_unregister('miss_utility_yes_no');
		$session->php_session_unregister('lamp_yes_no');
		$session->php_session_unregister('lamp_use_gas');
		
		$session->php_session_unregister('optional_with_nones');
		
			$session->php_session_unregister('adc_page');
			$session->php_session_unregister('adc_letter');
			$session->php_session_unregister('adc_number');
			
			$session->php_session_unregister('miss_utility_yes_no');
			$session->php_session_unregister('lamp_yes_no');
			$session->php_session_unregister('lamp_use_gas');
			
			$session->php_session_unregister('special_instructions');
			
			$session->php_session_unregister('order_type_id');
			$session->php_session_unregister('address_id');	
			
			tep_redirect(FILENAME_AOM_ORDER_CREATE_ADDRESS);

	}
    if(isset($_GET['clearSC'])) {
		//die();
		$session->php_session_unregister('order_type_id');
        $session->php_session_unregister('order_type');
        
		$session->php_session_unregister('order_id');
		$session->php_session_unregister('credit');
		$session->php_session_unregister('agent_id');
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
		$session->php_session_unregister('install_equipment');
		$session->php_session_unregister('remove_equipment');
		$session->php_session_unregister('special_instructions');
		$session->php_session_unregister('adc_page');
		$session->php_session_unregister('adc_letter');
		$session->php_session_unregister('adc_number');
		$session->php_session_unregister('miss_utility_yes_no');
		$session->php_session_unregister('lamp_yes_no');
		$session->php_session_unregister('lamp_use_gas');
		
		$session->php_session_unregister('optional_with_nones');
		
			$session->php_session_unregister('adc_page');
			$session->php_session_unregister('adc_letter');
			$session->php_session_unregister('adc_number');
			
			$session->php_session_unregister('miss_utility_yes_no');
			$session->php_session_unregister('lamp_yes_no');
			$session->php_session_unregister('lamp_use_gas');
			
			$session->php_session_unregister('special_instructions');
			
			$session->php_session_unregister('order_type_id');
			$session->php_session_unregister('address_id');	
			
            if(isset($_GET['agent_id'])) {
                tep_redirect(FILENAME_AOM_ORDER_CREATE_ADDRESS.'?order_type=2&agent_id='.$_GET['agent_id']);
            } else {
                tep_redirect(FILENAME_AOM_ORDER_CREATE_ADDRESS.'?order_type=2');
            }
			


    }
	//echo $session->user_session_id;
	//print_r($_SESSION);
	
	$page_action = tep_fill_variable('page_action', 'get');

	$order_type = tep_fill_variable('order_type', 'get',
		tep_fill_variable('order_type', 'session', ORDER_TYPE_INSTALL));
	$session->php_session_register('order_type', $order_type);
	$session->php_session_register('order_type_id', $order_type);

	$post_not_allowed_error = false;
    if(isset($_GET['agent_id'])) {
        $agent_id = tep_fill_variable('agent_id', 'get');
    } else {
        $agent_id = tep_fill_variable('agent_id', 'post', tep_fill_variable('agent_id', 'session'));
    }
	
	$address_id = tep_fill_variable('address_id', 'post', tep_fill_variable('address_id', 'session')); //will only have address id if 2 or 3
	$house_number = tep_fill_variable('house_number', 'post', tep_fill_variable('house_number', 'session'));
	$street_name = tep_fill_variable('street_name', 'post', tep_fill_variable('street_name', 'session'));
	$city = tep_fill_variable('city', 'post', tep_fill_variable('city', 'session'));
	$zip = tep_fill_variable('zip', 'post', tep_fill_variable('zip', 'session'));
	$state = tep_fill_variable('state', 'post', tep_fill_variable('state', 'session'));
	$county = tep_fill_variable('county', 'post', tep_fill_variable('county', 'session'));
	$miss_utility_yes_no = tep_fill_variable('miss_utility_yes_no', 'post', tep_fill_variable('miss_utility_yes_no', 'session'));
    $lamp_yes_no = tep_fill_variable('lamp_yes_no', 'post', tep_fill_variable('lamp_yes_no', 'session'));
    $lamp_use_gas = tep_fill_variable('lamp_use_gas', 'post', tep_fill_variable('lamp_use_gas', 'session'));
    $adc_page = tep_fill_variable('adc_page', 'post', tep_fill_variable('adc_page', 'session'));
	$adc_letter = tep_fill_variable('adc_letter', 'post', tep_fill_variable('adc_letter', 'session'));
	$adc_number = tep_fill_variable('adc_number', 'post', tep_fill_variable('adc_number', 'session'));
	$cross_street_directions = tep_fill_variable('cross_street_directions', 'post', tep_fill_variable('cross_street_directions', 'session'));
	$submit_string = tep_fill_variable('submit_string_y', 'post', tep_fill_variable('submit_string'));
	$zip4_code = tep_fill_variable('zip4_code', 'post', tep_fill_variable('zip4_code', 'session'));
	$pna = tep_fill_variable('pna', 'post', tep_fill_variable('pna', 'session'));
	
	//print_r($_SESSION);
	
	if(isset($_GET['address_id'])) {
		$address_id = $_GET['address_id'];
	}
	
	$request_zip4 = tep_fill_variable('request_zip4', 'post', tep_fill_variable('request_zip4', 'session', false));
	
	if (!empty($page_action) && ($page_action == 'submit') && !empty($submit_string)) {
		
			if ($order_type == ORDER_TYPE_INSTALL) {
				if (empty($agent_id)) {
					$error->add_error('aom_order_create_address', 'Please select an Agent from the dropdown list.');
				}
				if (empty($house_number)) {
					$error->add_error('aom_order_create_address', 'Please enter a House Number.');
				}
				if (empty($street_name)) {
					$error->add_error('aom_order_create_address', 'Please enter a Street Name.');
				}
				if (empty($city)) {
					$error->add_error('aom_order_create_address', 'Please enter a City.');
				}
				if (empty($zip)) {
					$error->add_error('aom_order_create_address', 'Please enter a Zip.');
				}
				if (empty($state)) {
					$error->add_error('aom_order_create_address', 'Please select a State.');
				}
				if (empty($county)) {
					$error->add_error('aom_order_create_address', 'Please select a County.');
				}
	            if (empty($miss_utility_yes_no)) {
					$error->add_error('aom_order_create_address', 'Please answer the question regarding Miss Utility.');
				}
				if ($miss_utility_yes_no != "yes" && empty($lamp_yes_no))	{
					$error->add_error('aom_order_create_address', 'Please answer the question regarding the lamp.');
				}
				if ($miss_utility_yes_no != "yes" && $lamp_yes_no == "yes" && empty($lamp_use_gas)) {
					$error->add_error('aom_order_create_address', 'Please answer whether the lamp is gas or not.');
				}
				if (empty($cross_street_directions)) {
					$error->add_error('aom_order_create_address', 'Please enter Crossstreet/Directions.');
				}
				if ($request_zip4 && !empty($zip4_code)) {
					if (!tep_zip4_is_valid($zip4_code)) {
						$error->add_error('aom_order_create_address', 'Your zip4 code is not in the correct format.  Please enter it in the format of 12345-1234.');
					}
				}

			} else { //order type 2 or 3

				if (empty($address_id)) {
					$error->add_error('aom_order_create_address', 'Please select an Address.');
				}

			} //end if order type
				
			if ((!$error->get_error_status('aom_order_create_address')) && ($order_type == ORDER_TYPE_INSTALL)) {
			//No error, try and get the zip4 code and if so then return that, otherwise spark an error.
                if (!$request_zip4 || empty($zip4_code)) {
                    if(explode('-', $zip)[1] != '9999'){
                        if(explode('-', $zip4_code)[1] != '9999'){
                            $zip4_class = new zip4($house_number . ' ' . $street_name, tep_get_state_name($state), $city, $zip);
                            if ($zip4_class->search()) {
                                $zip4_code = $zip4_class->return_zip_code();
                                $request_zip4 = false;
                            } else {
                                // brad@brgr2.com
                                if ($zip4_class->return_fail_type() == 'network') {
                                    $error->add_error('account_create_address', '<div class="alert alert-error"><i class="icon-4x pull-left icon-warning-sign"></i> <button type="button" class="close" data-dismiss="alert">&times;</button> <p><strong>Error Verifying Address</strong> We tried to look up that address via the US Postal System, but the check failed because of a network issue. Please try again or enter the zip+4 code yourself or lookup at <a href="https://tools.usps.com/go/ZipLookupAction!input.action" target="_blank">USPS</a>.  Please enter all nine digits in zip+4 field (12345-6789).  Thank you.</div>');
                                } else if ($zip4_class->return_fail_type() == 'mismatch') {
                                    $error->add_error('account_create_address', 'We tried to look up that address via the US Postal System, but it didn\'t find a match. Please double check the address and try again or enter the zip+4 code yourself or lookup at <a href="https://tools.usps.com/go/ZipLookupAction!input.action" target="_blank">USPS</a>.  Please enter all nine digits in zip+4 field (12345-6789).  Thank you.');
                                } else if ($zip4_class->return_fail_type() == 'invalid'){
                                    $error->add_error('account_create_address', $zip4_class->return_fail_description());
                                }
                                $request_zip4 = true;
                            }


                        }
                    }
                }
		}
		if (!$error->get_error_status('aom_order_create_address') && !empty($zip4_code)) {
			if (!zip4_is_deliverable($zip4_code)) {
				$error->add_error('aom_order_create_address', 'Sorry we don\'t currently service that area but please email us at '.INFO_EMAIL.' to discuss possible solutions.');
				$request_zip4 = true;
				//Now we can log the address.
				
				$database->query("insert into " . TABLE_OUT_OF_SERVICE_REQUESTS . " (date_added, house_number, street_name, city, county_id, state_id, zip, zip4, user_id) values ('" . time() . "', '" . $house_number . "', '" . $street_name . "', '" . $city . "', '" . $county . "', '" . $state . "', '" . $zip . "', '" . $zip4_code . "', '" . $user->fetch_user_id() . "')");
			}
		}
		$post_not_allowed_error = false;
			if (($order_type == ORDER_TYPE_INSTALL) && (!$error->get_error_status('aom_order_create_address'))) {
				if (!tep_address_post_is_allowed($house_number, $street_name, $city, $county, $state)) {
					if ($pna != '1') {
						$post_not_allowed_error = true;
					}
				}
			}
				
			if (!$error->get_error_status('aom_order_create_address') && !$post_not_allowed_error) {
					if ($order_type == ORDER_TYPE_INSTALL) {
						$session->php_session_register('house_number', $house_number);
						$session->php_session_register('street_name', $street_name);
						$session->php_session_register('city', $city);
						$session->php_session_register('zip', $zip);
						$session->php_session_register('state', $state);
						$session->php_session_register('county', $county);
						$session->php_session_register('miss_utility_yes_no', $miss_utility_yes_no);
						$session->php_session_register('lamp_yes_no', $lamp_yes_no);
						$session->php_session_register('lamp_use_gas', $lamp_use_gas);
						$session->php_session_register('cross_street_directions', $cross_street_directions);
						$session->php_session_register('zip4', $zip4_code);
						$session->php_session_register('adc_page', $adc_page);
						$session->php_session_register('adc_letter', $adc_letter);
						$session->php_session_register('adc_number', $adc_number);
						$session->php_session_register('request_zip4', $request_zip4);
						$session->php_session_register('pna', $pna);
						$session->php_session_register('agent_id', $agent_id);	
					} else {
						$session->php_session_register('address_id', $address_id);
						$session->php_session_register('agent_id', $agent_id);	
						$query = $database->query("select house_number, street_name, city, zip, state_id, county_id, zip4, adc_number, cross_street_directions from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
						$result = $database->fetch_array($query);
						
						$session->php_session_register('house_number', $result['house_number']);
						$session->php_session_register('street_name', $result['street_name']);
						$session->php_session_register('city', $result['city']);
						$session->php_session_register('zip', $result['zip']);
						$session->php_session_register('state', $result['state_id']);
						$session->php_session_register('county', $result['county_id']);
						$session->php_session_register('miss_utility_yes_no', $miss_utility_yes_no);
						$session->php_session_register('lamp_yes_no', $lamp_yes_no);
						$session->php_session_register('lamp_use_gas', $lamp_use_gas);
						$session->php_session_register('cross_street_directions', $result['cross_street_directions']);
						$session->php_session_register('zip4', $result['zip4']);
						$session->php_session_register('request_zip4', $request_zip4);
						
						$explode = explode('_', $result['adc_number']);
							if (count($explode) != 3) {
								$adc_page = '';
								$adc_letter = '';
								$adc_number = '';
							} else {
								$adc_page = $explode[0];
								$adc_letter = $explode[1];
								$adc_number = $explode[2];
							}
					}

					if ($order_type == ORDER_TYPE_REMOVAL) {
						tep_redirect(FILENAME_AOM_ACTIVE_ADDRESSES . '?aID=' . $address_id . '&page_action=reschedule_removal');
					} else {
						tep_redirect(FILENAME_AOM_ORDER_CREATE_SPECIAL);
					}
				}
			}

								if (!empty($agent_id)) {
									$billing_method_id = tep_fetch_agent_billing_method_id($agent_id);
									$service_level_id = tep_get_service_level_id($agent_id);
									$agent_mrid = tep_get_agent_id($agent_id);

								}
								else {
									$billing_method_id = null;
									$service_level_id = null;
									$agent_mrid = null;
								}
	
					if ($order_type == ORDER_TYPE_INSTALL) {
						$form = array(
						//'noPreviousAddresses' => $noPreviousAddresses,
						'post_not_allowed_error' => $post_not_allowed_error,
						'address_id' => $address_id,
						'house_number' => $house_number,
						'street_name' => $street_name,
						'city' => $city,
						'zip' => $zip,
						'state' => $state,
						'county' => $county,
						'miss_utility_yes_no' => $miss_utility_yes_no,
						'lamp_yes_no' => $lamp_yes_no,
						'lamp_use_gas' => $lamp_use_gas,
						'adc_page' => $adc_page,
						'adc_letter' => $adc_letter,
						'adc_page' => $adc_page,
						'adc_number' => $adc_number,
						'cross_street_directions' => $cross_street_directions,
						'submit_string' => $submit_string,
						'zip4_code' => $zip4_code,
						'pna' => $pna,
						'request_zip4' => $request_zip4,
						'agent_id' => $agent_id,
						'billing_method_id' => $billing_method_id,
						'service_level_id' => $service_level_id,
						'agent_mrid' => $agent_mrid
						
						//'text' => $text
					);
					$pulldowns = array(
						'states' => tep_draw_state_pulldown_bgdn('state', $state),
						'country' => tep_draw_county_pulldown_bgdn('county', $state, $county),
						'agents' => tep_draw_aom_agent_pulldown_bgdn('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
					);
					$vars = array(
						'form'=>$form,
						'pulldowns'=>$pulldowns,
						'order_type'=>$order_type,
						'step'=>1
					);
					
					$vars['form']['billing_method_id'] = tep_get_billing_method_name($billing_method_id);//$billing_method_id;
					$vars['form']['service_level_id'] = tep_get_service_level_name($service_level_id);//$service_level_id;
				 
					echo $twig->render('aom/aom_order_create_address.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
	
					} else {
						$found = true;
						if (!empty($agent_id)) {
				?>
							
							<?php
							$query = $database->query("select a.address_id, a.house_number, a.street_name, a.city from " . TABLE_ADDRESSES . " a left join " . TABLE_ORDERS . " o on (a.address_id = o.address_id and o.order_type_id = '3' and o.order_status_id != '3' and o.order_status_id != '4'), " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where atu.user_id = u.user_id and u.user_id = ud.user_id  and u.user_id = '" . $agent_id . "' and atu.address_id = a.address_id and (o.order_status_id != '3' or (o.order_id is NULL and a.status < '3')) order by a.address_id DESC");
							$found = false;
							foreach($database->fetch_array($query) as $result){
								$found = true;
								$checked = '';
									if ($result['address_id'] == $address_id) {
										$checked = 'CHECKED ';
										$name = '<b>'.$result['house_number'].' '.$result['street_name'].', '.$result['city'].'</b>';
									} else {
										$name = $result['house_number'].' '.$result['street_name'].', '.$result['city'];
									}
									$result['name'] = $name;
                                    $result['checked'] = $checked;
									$vars['form']['result'][] = $result;
									//$vars['form']['text'] = $text;

							}
							/*if (!$found) {
								//nothing found
								?>
								<tr>
									<td class="main"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This Agent does not currently have any Active signposts.  When ready, we will be happy to meet their <?php echo BUSINESS_NAME; ?> needs.</b></td>
								</tr>
								<?php
							}*/
							
							
							
						}
						$pulldowns = array(
							'agents' => tep_draw_aom_agent_pulldown_bgdn('agent_id', $agent_id, $user->fetch_user_id(),'change-submit',array(array('id' => '', 'name' => 'Please Select'))),
						);
						
						if (!empty($agent_id)) { 
									$billing_method_id = tep_fetch_agent_billing_method_id($agent_id);
									$service_level_id = tep_get_service_level_id($agent_id);
									$agent_mrid = tep_get_agent_id($agent_id);

								}
								else {
									$billing_method_id = null;
									$service_level_id = null;
									$agent_mrid = null;
								}
						
						$vars['pulldowns']=$pulldowns;
						$vars['order_type'] = $order_type;
						$vars['step'] = 1;
						$vars['found'] = $found;
						$vars['form']['agent_id'] = $agent_id;
						$vars['form']['billing_method_id'] = tep_get_billing_method_name($billing_method_id);//$billing_method_id;
						$vars['form']['service_level_id'] = tep_get_service_level_name($service_level_id);//$service_level_id;
						$vars['form']['agent_mrid'] = $agent_mrid;
							
										/*'billing_method_id' => $billing_method_id,
										'service_level_id' => $service_level_id,
										'agent_mrid' => $agent_mrid);*/
							//$agent_id = tep_fill_variable('agent_id', 'post', tep_fill_variable('agent_id', 'session'));
						//echo $found;
							
						echo $twig->render('aom/aom_order_create_address_select_address.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
						
					}
