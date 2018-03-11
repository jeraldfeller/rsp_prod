<?php
// mjp 20120404 improve service call reason equipment validation
// mjp 20120423 allow none to be shown if no equipment in current warehouse.
// Updated 1/10/13 brad@brgr2.com
@session_start();

	$page_action = tep_fill_variable('page_action', 'get');
	$order_type = tep_fill_variable('order_type_id', 'session');
	$sc_reason = tep_fill_variable('sc_reason', 'post', tep_fill_variable('sc_reason', 'session'));
	$sc_reason_4  = tep_fill_variable('sc_reason_4', 'post', tep_fill_variable('sc_reason_4', 'session'));
	$sc_reason_5  = tep_fill_variable('sc_reason_5', 'post', tep_fill_variable('sc_reason_5', 'session'));
	$sc_reason_7  = tep_fill_variable('sc_reason_7', 'post', tep_fill_variable('sc_reason_7', 'session'));
	$equipment  = tep_fill_variable('equipment', 'post', tep_fill_variable('equipment', 'session', array()));
	$install_equipment = tep_fill_variable('install_equipment', 'post', tep_fill_variable('install_equipment', 'session', array()));
	$remove_equipment = tep_fill_variable('remove_equipment', 'post', tep_fill_variable('remove_equipment', 'session', array()));
	$submit_button = tep_fill_variable('submit_string_y', 'post', tep_fill_variable('submit_string_y'));

	if (empty($order_type)) {
		tep_redirect(FILENAME_ORDER_CREATE);
	}
	if($order_type == ORDER_TYPE_INSTALL) {
		$shipping_address = tep_fill_variable('street_name', 'session');
	} else {
		$shipping_address = tep_fill_variable('address_id', 'session');

	}
	if (empty($shipping_address)) {
		tep_redirect(FILENAME_ORDER_CREATE_ADDRESS);
    }
    if (!is_array($remove_equipment)) {
        $remove_equipment = array();
    }
    if (!is_array($install_equipment)) {
        $install_equipment = array();
    }

	$payment_method = tep_fill_variable('payment_method_id', 'session');
	$special_instructions = tep_fill_variable('special_instructions', 'post', tep_fill_variable('special_instructions', 'session'));
	$promo_code = tep_fill_variable('promo_code');
	$number_of_posts = tep_fill_variable('number_of_posts', 'post', tep_fill_variable('number_of_posts', 'session', '1'));
	$optional = tep_fill_variable('optional', 'post', tep_fill_variable('optional_with_nones', 'session', tep_fill_variable('optional', 'session', array())));
    $optional = parse_equipment_array2($optional);
    if ($order_type != ORDER_TYPE_INSTALL) {
        $address_id = tep_fill_variable('address_id', 'session');
        $address_information = tep_fetch_address_information($address_id);
        $form['house_number'] = $address_information['house_number'];
        $form['street_name'] = $address_information['street_name'];
        $form['city'] = $address_information['city'];
        $county_name = $address_information['county_name'];
        $form['zip'] = $address_information['zip'];
        $form['$zip4_code'] = $address_information['zip4'];
        $form['state_name'] = $address_information['state_name'];
    } else {
        $form['house_number'] = tep_fill_variable('house_number', 'session');
        $form['street_name'] = tep_fill_variable('street_name', 'session');
        $form['city'] = tep_fill_variable('city', 'session');
        $county = tep_fill_variable('county', 'session');
        $form['zip'] = tep_fill_variable('zip', 'session');
        $form['zip4_code'] = tep_fill_variable('zip4', 'session');
        $state_id = tep_fill_variable('state', 'session');
        $form['state_name'] = tep_get_state_name($state_id);
    }
    $personal = 0;//tep_is_personal_equipment($user->fetch_user_id(), $user->agency_id);



	if (empty($submit_button)) {
		$page_action = '';
	}


	if (($order_type == ORDER_TYPE_SERVICE) && ($page_action == 'submit')) {
		//Check if one of the options are done and some result is done.
		$error_status = false;
		if (!empty($sc_reason)) {
			if ($sc_reason == '1') {
				if (empty($install_equipment) || empty($remove_equipment)) {
					$error_status = true;
					$error->add_error('account_create_special', 'You must select both a rider to be installed and a rider to be removed.');
				}
			} elseif ($sc_reason == '2') {
				if (empty($optional)) {
					$error_status = true;
					$equipment = array();
					$error->add_error('account_create_special', 'You must select at least one item to be installed.');
				}
			} elseif ($sc_reason == '3') {
				if (empty($equipment)) {
					$error_status = true;
					$equipment = array();
					$error->add_error('account_create_special', 'You must select at least one item to be installed.');
				}
			} elseif ($sc_reason == '4') {
				if (empty($sc_reason_4)) {
					$error_status = true;
					$optional = array();
					$equipment = array();
					$error->add_error('account_create_special', 'Please select the reason from the pulldown list.');
				}
			} elseif ($sc_reason == '5') {
				if (empty($sc_reason_5)) {
					$error_status = true;
					$optional = array();
					$equipment = array();
				}
			} elseif ($sc_reason == '6') {
				if (empty($optional)) {
					$error_status = true;
					$equipment = array();
					$error->add_error('account_create_special', 'You must select at least one item that was missing from the install.');
				}
			} elseif ($sc_reason == '7') {
				if (empty($sc_reason_7)) {
					$optional = array();
					$equipment = array();
					$error_status = true;
				}
			}
		} else {
			$error_status = true;
			$error->add_error('account_create_special', 'You must select a reason for this service call.');
		}
		if ($error_status) {
			$page_action = '';
		}


	}


	if (!empty($page_action) && ($page_action == 'submit')) {

		if (empty($number_of_posts) && ($order_type == ORDER_TYPE_INSTALL)) {
			$error->add_error('account_create_special', 'Please enter the number of posts that you require.');
		}

    if ($order_type == ORDER_TYPE_INSTALL) { // mjp 20120404 improve service call reason equipment validation
            if ($personal > 0) {
                if (!isset($optional["5"])) {
                $error->add_error('account_create_special', 'Please select which personal equipment to install, or NONE if all of your panels are out of stock or you don\'t want a panel installed.');
                }
            }
        }



		if (!$error->get_error_status('account_create_special')) {

			//die('hrtr');

			$session->php_session_register('special_instructions', $special_instructions);
            $session->php_session_register('optional', $optional);
            $session->php_session_register('optional_with_nones', $optional);
			$session->php_session_register('promo_code', $promo_code);
			$session->php_session_register('number_of_posts', $number_of_posts);
			$session->php_session_register('sc_reason', $sc_reason);
			$session->php_session_register('sc_reason_4', $sc_reason_4);
			$session->php_session_register('sc_reason_5', $sc_reason_5);
			$session->php_session_register('sc_reason_7', $sc_reason_7);
			$session->php_session_register('equipment', $equipment);
			$session->php_session_register('install_equipment', $install_equipment);
			$session->php_session_register('remove_equipment', $remove_equipment);

			tep_redirect(FILENAME_ORDER_CREATE_PAYMENT);
		}
	}

	$form['promo_code'] = $promo_code;
	$form['number_of_posts'] = $number_of_posts;
	$form['special_instructions'] = $special_instructions;
	$vars['form'] = $form;

		if (($order_type == ORDER_TYPE_INSTALL)) {
						//Install.

					$vars['equipment_array'] = tep_generate_available_equipment_string_bgdn($order_type, $user->fetch_service_level_id(), $user->fetch_user_id(), $optional, tep_fill_variable('zip4', 'session'), tep_fill_variable('address_id', 'session'));

					$vars['step'] = 2;
					$vars['order_type'] = $order_type;

					//print_r($vars['equipment_array']);

					echo $twig->render('order/order_create_special.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));


					}


					elseif ($order_type == ORDER_TYPE_SERVICE) {
						//Service call.  Need to make some more complex questions here.


						if (!empty($sc_reason)) {
											if ($sc_reason == '1') {

													$query = $database->query("select eita.equipment_id, e.name as equipment_name from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.equipment_id = e.equipment_id and eita.equipment_status_id = '2' and e.equipment_type_id = '2' and eita.address_id = '" . tep_fill_variable('address_id', 'session') . "'");

																		$exclude_array = array();
																			foreach($database->fetch_array($query) as $result){

																				$exclude_array[] = $result['equipment_id'];
																					if (in_array($result['equipment_id'], $remove_equipment)) {
																						$checked = true;
																					} else {
																						$checked = false;
																					}
																				$remove_riders[] = array('equipment_id'=>$result['equipment_id'], 'checked'=>$checked, 'equipment_name'=>$result['equipment_name'])	;
																				//echo '<input type="checkbox" class="remove-equipment" name="remove_equipment[]" value="'.$result['equipment_id'].'"'.$checked.'>&nbsp;&nbsp;&nbsp;'.$result['equipment_name'];
																				//$loop++;
																			}



																		$warehouses = tep_get_sevicing_warehouse(fetch_address_zip4(tep_fill_variable('address_id', 'session')));
																		$query = $database->query("select e.equipment_id, e.name as equipment_name from " . TABLE_EQUIPMENT . " e where e.equipment_type_id = '2'");
																		$loop = 0;
																			foreach($database->fetch_array($query) as $result){
																					if (tep_fetch_available_equipment_count($result['equipment_id'], $warehouses, $user->fetch_user_id()) < 1) {
																						continue;
																					}
																					if (in_array($result['equipment_id'], $exclude_array)) {
																						continue;
																					}
																					if (in_array($result['equipment_id'], $install_equipment)) {
																						$checked = true; //' CHECKED';
																					} else {
																						$checked = false;
																					}

																				$install_riders[] = array('equipment_id'=>$result['equipment_id'], 'checked'=>$checked, 'equipment_name'=>$result['equipment_name'])	;
																				//echo '<input type="checkbox" class="install-equipment" name="install_equipment[]" value="'.$result['equipment_id'].'"'.$checked.'>&nbsp;&nbsp;&nbsp;'.$result['equipment_name'];
																				//$loop++;
																			}

																		$form['remove_riders'] = $remove_riders;
																		$form['install_riders'] = $install_riders;

											}
											elseif ($sc_reason == '2') {
												//Install new bb or rider.
												$vars['equipment_array'] = tep_generate_available_equipment_string_bgdn('1', $user->fetch_service_level_id(), $user->fetch_user_id(), $optional, fetch_address_zip4(tep_fill_variable('address_id', 'session')), tep_fill_variable('address_id', 'session'), false, true, array(), false);

											}
											elseif ($sc_reason == '3') {
												//Replace/Exchange Agent SignPanel
												$warehouses = tep_get_sevicing_warehouse(fetch_address_zip4(tep_fill_variable('address_id', 'session')));
												$query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " where equipment_type_id = '4' order by equipment_type_name");
												$loop = 0;
												$equipment_array = array();
													foreach($database->fetch_array($query) as $result){
														$string = '';
														$exclude_array = tep_fetch_installed_equipment_array($result['equipment_type_id'], tep_fill_variable('address_id', 'session'));
														$sub_query = $database->query("select equipment_id, name from " . TABLE_EQUIPMENT . " where equipment_type_id = '" . $result['equipment_type_id'] . "'");
														$equipment_items_array = array();
															foreach($database->fetch_array($sub_query) as $sub_result){
																	if (tep_fetch_available_equipment_count($sub_result['equipment_id'], $warehouses, $user->fetch_user_id()) < 1) {
																		continue;
																	}
																	if (in_array($sub_result['equipment_id'], $exclude_array)) {
																		continue;
																	}
																	if (in_array($sub_result['equipment_id'], $equipment)) {
																		$sub_result['checked'] = true;
																	} else {
																		$sub_result['checked'] = false;
																	}
																	$equipment_items_array = $sub_result;
															}
														$result['items'][] = $equipment_items_array;
														$equipment_array[] = $result;
													}
												$vars['equipment_array'] = $equipment_array;
												//print_r($vars['equipment_array']);
											}
											elseif ($sc_reason == '4') {
												//Post Leaning/Straighten Post
												$pulldowns['leaning_reason'] = tep_generate_post_leaning_reason_pulldown_menu_bgdn('sc_reason_4', $sc_reason_4);
											}
											elseif ($sc_reason == '5') {
												//Move Post

											}

										elseif ($sc_reason == '6') {
											//Forgotten Equipment.
												 $vars['equipment_array'] = tep_generate_available_equipment_string_bgdn('1', $user->fetch_service_level_id(), $user->fetch_user_id(), $optional, fetch_address_zip4(tep_fill_variable('address_id', 'session')), tep_fill_variable('address_id', 'session'), false, true, array(), false);
											}
										elseif ($sc_reason == '7') {
											//Other

											}
						}
						$pulldowns['sc_reason'] = tep_generate_service_call_pulldown_menu_bgdn('sc_reason', $sc_reason, $user->fetch_user_id(), tep_fill_variable('address_id', 'session'), 'change-submit');

					$vars['form'] = $form;
					$vars['sc_reason'] = $sc_reason;
					$vars['pulldowns'] = $pulldowns;
					$vars['order_type'] = $order_type;
					$vars['step'] = 2;
					echo $twig->render('order/order_create_special_service.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

					}
?>
