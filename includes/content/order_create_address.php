<?php
// Updated 1/28/2012 brad@brgr2.com Added shorter CURL timeout when checking address, and alert here when that's the issue.
// Updated 1/14/2013 brad@brgr2.com Changed crossstreet box size.
// Updated 1/10/13 brad@brgr2.com

@session_start();

if (!$user->can_place_orders) {
    tep_redirect(FILENAME_ORDER_CREATE_DISALLOWED);
}

$order_type = tep_fill_variable('order_type', 'get');

if (empty($order_type)) { //haven't come via get link (orders box)
    $page_action = tep_fill_variable('page_action', 'get');
    $order_type = tep_fill_variable('order_type_id', 'session'); //could be 1, 2 or 3 at this stage

    if (empty($order_type)) {
        tep_redirect(FILENAME_ORDER_CREATE);
    }

    $noPreviousAddresses = false;
    $post_not_allowed_error = false;
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

    $request_zip4 = tep_fill_variable('request_zip4', 'post', tep_fill_variable('request_zip4', 'session', false));

    if (!empty($page_action) && ($page_action == 'submit') && !empty($submit_string)) {

        if ($order_type == ORDER_TYPE_INSTALL) {
			//die();
            if (!trim($house_number))
                $error->add_error('account_create_address', 'Please enter a House Number.');
            if (!trim($street_name))
                $error->add_error('account_create_address', 'Please enter a Street Name.');
            if (!trim($city))
                $error->add_error('account_create_address', 'Please enter a City.');
            if (!trim($zip))
                $error->add_error('account_create_address', 'Please enter a Zip.');
            if (!$state)
                $error->add_error('account_create_address', 'Please select a State.');
            if (!$county)
                $error->add_error('account_create_address', 'Please select a County.');
            if (!$miss_utility_yes_no)
                $error->add_error('account_create_address', 'Please answer the question regarding Miss Utility.');
            if ($miss_utility_yes_no != "yes" && !$lamp_yes_no)
                $error->add_error('account_create_address', 'Please answer the question regarding the lamp.');
            if ($miss_utility_yes_no != "yes" && $lamp_yes_no == "yes" && !$lamp_use_gas)
                $error->add_error('account_create_address', 'Please answer whether the lamp is gas or not.');
            if (!trim($cross_street_directions))
                $error->add_error('account_create_address', 'Please enter Crossstreet/Directions.');
            if ($request_zip4 && !empty($zip4_code)) {
                if (!tep_zip4_is_valid($zip4_code))
                    $error->add_error('account_create_address', 'Your zip+4 code is not in the correct format.  Please enter it in the format of 12345-1234.');
            }
        } else { //order type 2 or 3
            if (empty($address_id)) {
                $error->add_error('account_create_address', 'Please select an Address.');
            } else {
                if ($order_type == ORDER_TYPE_REMOVAL) {
                    tep_redirect(FILENAME_AGENT_ACTIVE_ADDRESSES . '?aID=' . $address_id . '&page_action=reschedule_removal');
                }
            }
        } //end if order type

        if ((!$error->get_error_status('account_create_address')) && ($order_type == ORDER_TYPE_INSTALL)) {
            //No error, try and get the zip4 code and if so then return that, otherwise spark an error.
            if (!$request_zip4 || empty($zip4_code)) {
                if(explode('-', $zip)[1] != '9999'){
                    if(explode('-', $zip4_code)[1] != '9999'){
                        $zip4_class = new zip4($house_number . ' ' . $street_name, tep_get_state_name($state), $city, $zip, get_usps_user_id());
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

        if ((!$error->get_error_status('account_create_address')) && ($order_type == ORDER_TYPE_INSTALL) && (!empty($zip4_code))) {
            if (!tep_fetch_installation_availability($zip4_code)) {
                $error->add_error('account_create_address', 'Sorry we don\'t currently service that area but please email us at '.INFO_EMAIL.' to discuss possible solutions.');
                $request_zip4 = true;
                //Now we can log the address.

                $database->query("insert into " . TABLE_OUT_OF_SERVICE_REQUESTS . " (date_added, house_number, street_name, city, county_id, state_id, zip, zip4, user_id) values ('" . time() . "', '" . $house_number . "', '" . $street_name . "', '" . $city . "', '" . $county . "', '" . $state . "', '" . $zip . "', '" . $zip4_code . "', '" . $user->fetch_user_id() . "')");
            }
        }

        $post_not_allowed_error = false;
        if (($order_type == ORDER_TYPE_INSTALL) && (!$error->get_error_status('account_create_address'))) {
            if (!tep_address_post_is_allowed($house_number, $street_name, $city, $county, $state)) {
                if ($pna != '1') {
                    $post_not_allowed_error = true;
                }
            }
        }
        if (!$error->get_error_status('account_create_address') && !$post_not_allowed_error) {
			
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
				
            } else {
                $session->php_session_register('address_id', $address_id);
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
            tep_redirect(FILENAME_ORDER_CREATE_SPECIAL);
        }
    }
} else { //have come from orders link (could be type 2 or 3)
    $address_id = '';
    $session->php_session_register('order_type_id', $order_type);
}

if ($order_type == ORDER_TYPE_INSTALL) {
   $text = 'Please enter the address details below.  Please note that to prevent delays all details on this page must be filled in.';
} else {
   $text = 'Please select an address below.  These are the addresses that you currently have a Sign Post installed at.  If an address does not show up then please contact us for assistance.';
}
	   
				   
 if ($order_type == ORDER_TYPE_INSTALL) { 
 
	//init twig form
	$form = array(
			'noPreviousAddresses' => $noPreviousAddresses,
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
			'text' => $text
	);
	$pulldowns = array(
		'states' => tep_draw_state_pulldown_bgdn('state', $state),
		'country' => tep_draw_county_pulldown_bgdn('county', $state, $county)
	);
	$vars = array(
		'form'=>$form,
		'pulldowns'=>$pulldowns,
		'order_type'=>$order_type,
		'step'=>1
	);
 
	echo $twig->render('order/order_create_address.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
 }
 else {

                        $sort_by = 'street';
                        $query = $database->query("select a.address_id, a.house_number, a.street_name, a.city from " . TABLE_ADDRESSES . " a join " . TABLE_ORDERS . " o on (a. address_id = o.address_id and o.order_type_id = '3' and o.order_status_id != '3' and o.order_status_id != '4'), " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_USERS . " u, " .    TABLE_USERS_DESCRIPTION . " ud where atu.user_id = u.user_id and u.user_id = ud.user_id  and u.user_id = '" . $user->fetch_user_id() . "' and atu.address_id = a.address_id and (o.              order_status_id != '3' or (o.order_id is NULL and a.status < '3')) order by a.address_id DESC");
                        $found = false;
                        foreach($database->fetch_array($query) as $result){
                            $found = true;
                            $checked = '';
                            if ($result['address_id'] == $address_id) {
                                $checked = 'CHECKED ';
                                $name = $result['house_number'] . ' ' . $result['street_name'] . ', ' . $result['city'];
                            } else {
                                $name = $result['house_number'] . ' ' . $result['street_name'] . ', ' . $result['city'];
                            }
							$result['name'] = $name;
							$vars['form']['result'][] = $result;
							$vars['form']['text'] = $text;
							//$vars['form']['name'] = $result;
						}
						$vars['order_type'] = $order_type;
						$vars['step'] = 1;		
                           
                        if (!$found) {
                            $noPreviousAddresses = true;

							echo $twig->render('order/order_create_address_not found.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error));
                        }
						else {
							echo $twig->render('order/order_create_address_select_address.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
						}
                        
 }


?>

