<?php
	$order_type = tep_fill_variable('order_type', 'get', '1');
	$address_id = tep_fill_variable('address_id', 'get');
	
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
	$session->php_session_unregister('install_equipment');
	$session->php_session_unregister('remove_equipment');
	
	$session->php_session_unregister('adc_page');
	$session->php_session_unregister('adc_letter');
	$session->php_session_unregister('adc_number');
	
	$session->php_session_unregister('optional_with_nones');
	
	$session->php_session_unregister('miss_utility_yes_no');
	$session->php_session_unregister('lamp_yes_no');
	$session->php_session_unregister('lamp_use_gas');
	
	$session->php_session_unregister('special_instructions');
		
	$session->php_session_unregister('order_type_id');
	$session->php_session_register('order_type_id', $order_type);

	if (tep_address_is_assigned_to_user($address_id, $user->fetch_user_id())) {
		$session->php_session_unregister('address_id');
		$session->php_session_register('address_id', $address_id);
		$query = $database->query("select zip4 from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
		$result = $database->fetch_array($query);
		$session->php_session_register('zip4', $result['zip4']);

		$redirect = FILENAME_ORDER_CREATE_SPECIAL;
	} else {
		$redirect = FILENAME_ORDER_CREATE_ADDRESS;
	}
	
	tep_redirect($redirect);
		
	$order_type_name = tep_get_order_type_name($order_type);
?>