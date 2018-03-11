<?php
@session_start(); 
	$selected_agent_id = tep_fill_variable('agent_id', 'session');
	$show_payment = 1;
	//$user_id=$selected_agent_id;
    
    if($user->fetch_user_id()=='' or $user->fetch_user_id()==false or $user->fetch_user_id()==0) {
		/*$error->add_error('order_create_confirmation', 'User information not available so order confirmation aborted.');
		tep_redirect(FILENAME_ORDER_CREATE);*/
		?>
		<table width="100%" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mainError" colspan="2">User information not available. Please re-login to the website and place order again here - <a href="index.php?action=logout">Logoff</a></td>
			</tr>
		</table>
		<?
		die();
	}

	$page_action = tep_fill_variable('page_action', 'get');
	$order_type = tep_fill_variable('order_type_id', 'session');
	$order_id = tep_fill_variable('order_id', 'session');
	$tos = tep_fill_variable('tos', 'post');
	$pna = tep_fill_variable('pna', 'post');

	$sc_reason = tep_fill_variable('sc_reason', 'session');
	$sc_reason_4  = tep_fill_variable('sc_reason_4', 'session');
	$sc_reason_5  = tep_fill_variable('sc_reason_5', 'session');
	$sc_reason_7  = tep_fill_variable('sc_reason_7', 'session');
	$equipment  = tep_fill_variable('equipment', 'session', array());
	$install_equipment  = tep_fill_variable('install_equipment', 'session', array());
	$remove_equipment  = tep_fill_variable('remove_equipment', 'session', array());
    
    $miss_utility_yes_no = tep_fill_variable('miss_utility_yes_no', 'session', '');
    $lamp_yes_no = tep_fill_variable('lamp_yes_no', 'session', '');
    $lamp_use_gas = tep_fill_variable('lamp_use_gas', 'session', '');

    $deferred_total = tep_fill_variable('deferred_total', 'session', 0);
    $deferred_transactions = tep_fill_variable('deferred_transactions', 'session', array());
    $deferred_credit = tep_fill_variable('deferred_credit', 'session', 0);

    $date_added = time();

    if ($miss_utility_yes_no == "yes") {
        $miss_utility_string = "Miss Utility call requested.";
    } else if ($lamp_yes_no == "no") {
        $miss_utility_string = "No lamp on property.";
    } else if ($lamp_use_gas == "yes") {
        $miss_utility_string = "Gas lamp on property.";
    } else if ($lamp_use_gas == "unsure") {
        $miss_utility_string = "Possible gas lamp on property.";
    } else if ($lamp_use_gas == "no") {
        $miss_utility_string = "No gas lamp on property.";
    }
	else {
	   $miss_utility_string = '';
	}

    // mjp
    $form['cc_type'] = tep_fill_variable('cc_type', 'session', '');
    $form['cc_name'] = tep_fill_variable('cc_name', 'session', '');
    $form['cc_number'] = tep_fill_variable('cc_number', 'session', '');
    $form['cc_month'] = tep_fill_variable('cc_month', 'session', '');
    $form['cc_year'] = tep_fill_variable('cc_year', 'session', '');
    $form['cc_verification_number'] = tep_fill_variable('cc_verification_number', 'session', '');
    $form['cc_billing_street'] = tep_fill_variable('cc_billing_street', 'session', '');
    $form['cc_billing_city'] = tep_fill_variable('cc_billing_city', 'session', '');
    $form['cc_billing_zip'] = tep_fill_variable('cc_billing_zip', 'session', '');
    
	if (empty($order_type))
		tep_redirect(FILENAME_AOM_ORDER_CREATE_ADDRESS);

	if ($order_type == '1') {
		$shipping_address = tep_fill_variable('street_name', 'session');
	} else {
		$shipping_address = tep_fill_variable('address_id', 'session');

	}

	if (empty($shipping_address))
		tep_redirect(FILENAME_AOM_ORDER_CREATE_ADDRESS);

	$payment_method = tep_fill_variable('payment_method_id', 'session');
	if (empty($payment_method))
		tep_redirect(FILENAME_AOM_ORDER_CREATE_PAYMENT);

	//Get all variable from user table
	$query_user=$database->query("select * from ". TABLE_USERS ." where user_id='$selected_agent_id'");
	$result_user = $database->fetch_array($query_user);
	$email_address=$result_user['email_address'];
	$agency_id=$result_user['agency_id'];
	$agent_id=$result_user['agent_id'];
	$query_agency=$database->query("select * from ". TABLE_AGENCYS ." where agency_id='$agency_id'");
	$result_agency = $database->fetch_array($query_agency);
	$agency_name=$result_agency['name'];
	$agency_address=$result_agency['address'];

	$special_instructions = tep_fill_variable('special_instructions', 'session');
	$optional = tep_fill_variable('optional', 'session', array());
	if ($order_type == '1') {
		$optional = parse_equipment_array($optional);
		$session->php_session_register('optional', $optional);
	}
	$address_id = tep_fill_variable('address_id', 'session');

	if (!empty($address_id)) {
		//We have a saved address.  Now get the information and populate the session variables.
		$query = $database->query("select house_number, street_name, city, zip, state_id, county_id, zip4, adc_number, cross_street_directions from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
		$result = $database->fetch_array($query);

		$session->php_session_register('house_number', $result['house_number']);
		$session->php_session_register('street_name', $result['street_name']);
		$session->php_session_register('city', $result['city']);
		$session->php_session_register('zip', $result['zip']);
		$session->php_session_register('state', $result['state_id']);
		$session->php_session_register('county', $result['county_id']);
		$session->php_session_register('cross_street_directions', $result['cross_street_directions']);
		$session->php_session_register('zip4', $result['zip4']);
	}

	$address_id = tep_fill_variable('address_id', 'session');
	$house_number = trim(tep_fill_variable('house_number', 'session'));
	$street_name = tep_fill_variable('street_name', 'session');
	$adc_page = tep_fill_variable('adc_page', 'session');
	$adc_letter = tep_fill_variable('adc_letter', 'session');
	$adc_number = tep_fill_variable('adc_number', 'session');
	$city = tep_fill_variable('city', 'session');
	$zip = tep_fill_variable('zip', 'session');
	$zip4 = tep_fill_variable('zip4', 'session');
	$zip4_code = $zip4;
	$state = tep_fill_variable('state', 'session');
	$county = tep_fill_variable('county', 'session');
	$number_of_posts = tep_fill_variable('number_of_posts', 'session');
	$promo_code = tep_fill_variable('promo_code', 'session');
	$cross_street_directions = tep_fill_variable('cross_street_directions', 'session');

	//Work out if this is a rush or sat install and create the extra cost.
	$schedualed_start = tep_fill_variable('schedualed_start', 'session');
	$extra_cost = tep_fetch_extra_cost($schedualed_start);
    $extended_cost = tep_fetch_service_area_cost(tep_fetch_zip4_service_area($zip4)); //mjp
    $email_data = tep_fetch_email_data($user->fetch_user_id());
    $agent_data = tep_fetch_agent_data($user->fetch_user_id());
	$service_area_window = tep_fetch_service_area_window(tep_fetch_zip4_service_area($zip4_code));
	if ($service_area_window == 0) {
	    $service_area_window = 5;
	}
	if (tep_date_is_saturday($schedualed_start)) {
	    $service_area_window++;
	}
	$schedualed_end = add_business_days($schedualed_start, $service_area_window-1);
	$miss_utility_start = subtract_business_days($schedualed_start, MISS_UTILITY_DELAY);
	$miss_utility_end = subtract_business_days($schedualed_start, 1);
	if ($order_type == 1 && ($miss_utility_yes_no == 'yes' || ($lamp_yes_no == 'yes' && $lamp_use_gas != 'no'))) {
	    $show_miss_utility = true;
	} else {
	    $show_miss_utility = false;
	}
	//echo $order_id;
	$order = new orders('fetch', $order_id, array(), $selected_agent_id);
	$data = $order->fetch_order();
	$base_cost = $data['base_cost'];
	$extended_cost = $data['extended_cost'];
	$equipment_cost = $data['equipment_cost'];
	$extra_cost = $data['extra_cost'];
	$deposit_cost = $data['deposit_cost'];
	$discount_cost = $data['discount_cost'];
	$total = $data['order_total'];  
	$credit = tep_fill_variable('credit', 'session', 0);
    $total -= $credit;		
$error->cc_error("Variables ($selected_agent_id) $house_number $street_name for base $base_cost, ext $extended_cost, eqp $equipment_cost, ext $extra_cost, dep $deposit_cost, disc $discount_cost, cred $credit");

$session->php_session_register('order_type_id_print',$order_type);
$session->php_session_register('order_id_print',$order_id);
$session->php_session_register('credit_print',$credit);
$session->php_session_register('agent_id_print',$selected_agent_id);
$session->php_session_register('address_id_print',$address_id);
$session->php_session_register('house_number_print',$house_number);
$session->php_session_register('street_name_print',$street_name);
$session->php_session_register('number_of_posts_print',$number_of_posts);
$session->php_session_register('city_print',$city);
$session->php_session_register('zip_print',$zip);
$session->php_session_register('zip4_print',$zip4);
$session->php_session_register('state_print',$state);
$session->php_session_register('county_print',$county);
$session->php_session_register('cross_street_directions_print',$cross_street_directions);
$session->php_session_register('schedualed_start_print',$schedualed_start);
$session->php_session_register('miss_utility_yes_no_print',$miss_utility_yes_no);
$session->php_session_register('lamp_yes_no_print',$lamp_yes_no);
$session->php_session_register('lamp_use_gas_print',$lamp_use_gas);
$session->php_session_register('date_added_print',$date_added);
$session->php_session_register('request_zip4_print',tep_fill_variable('request_zip4', 'session'));
$session->php_session_register('payment_method_id_print',tep_fill_variable('payment_method_id', 'session'));
$session->php_session_register('cc_type_print',tep_fill_variable('cc_type', 'session'));
$session->php_session_register('cc_name_print',tep_fill_variable('cc_name', 'session'));
$session->php_session_register('cc_number_print',tep_fill_variable('cc_number', 'session'));
$session->php_session_register('cc_month_print',tep_fill_variable('cc_month', 'session'));
$session->php_session_register('cc_year_print',tep_fill_variable('cc_year', 'session'));
$session->php_session_register('cc_verification_number_print',tep_fill_variable('cc_verification_number', 'session'));
$session->php_session_register('cc_billing_street_print',tep_fill_variable('cc_billing_street', 'session'));
$session->php_session_register('cc_billing_city_print',tep_fill_variable('cc_billing_city', 'session'));
$session->php_session_register('cc_billing_zip_print',tep_fill_variable('cc_billing_zip', 'session'));
$session->php_session_register('sc_reason_print',tep_fill_variable('sc_reason', 'session'));
$session->php_session_register('sc_reason_4_print',tep_fill_variable('sc_reason_4', 'session'));
$session->php_session_register('sc_reason_5_print',tep_fill_variable('sc_reason_5', 'session'));
$session->php_session_register('sc_reason_7_print',tep_fill_variable('sc_reason_7', 'session'));
$session->php_session_register('equipment_print',tep_fill_variable('equipment', 'session'));
$session->php_session_register('optional_print',tep_fill_variable('optional', 'session'));
$session->php_session_register('install_equipment_print',tep_fill_variable('install_equipment', 'session'));
$session->php_session_register('remove_equipment_print',tep_fill_variable('remove_equipment', 'session'));
$session->php_session_register('special_instructions_print',tep_fill_variable('special_instructions', 'session'));

$session->php_session_unregister('order_type_id');
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

$session->php_session_register('order_id',$order_id);

$form = array (	
	'show_miss_utility'=>$show_miss_utility,
    'payment_type'=>tep_get_payment_type_name($payment_method),
	//"service_area_window" => $service_area_window,
	//'order_type'=>$order_type,
	'order_type_name'=>tep_get_order_type_name($order_type),
	'miss_utility_start'=>$miss_utility_start,
	'miss_utility_end'=> $miss_utility_end,
	'schedualed_start'=>$schedualed_start,	
	'schedualed_end'=>$schedualed_end,	
	'county_name'=>tep_get_county_name($county),
	'state_name'=>tep_get_state_name($state),	
	'zip4_code'=>$zip4_code,
	'city'=>$city,	
	'number_of_posts'=>$number_of_posts,
	'promo_code'=>$promo_code,
	'cross_street_directions'=>$cross_street_directions,
	'street_name'=>$street_name,
	'house_number'=>$house_number,
	//'order'=>$order,
	'special_instructions'=>$special_instructions,
	'miss_utility_string'=>$miss_utility_string,
	'service_area_window'=>$service_area_window,
	'equipment_array'=>tep_create_confirmation_equipment_string_bgdn($optional),
	'order_total'=>$total,
	'extra_cost_string'=>tep_fetch_extra_cost_string($schedualed_start)
	);
	
	
	
	if ($order_type == ORDER_TYPE_SERVICE) {

  $string = '';
/*  
  if ($sc_reason == '1') {
    $string = 'Exchange Rider';
  } elseif ($sc_reason == '2') {
    $string = 'Install New Rider or BBox';
  } elseif ($sc_reason == '3') {
    $string = 'Replace/Exchange Agent SignPanel';
  } elseif ($sc_reason == '4') {
    $string = 'Post Leaning/Straighten Post';
  } elseif ($sc_reason == '5') {
    $string = 'Move Post';
  } elseif ($sc_reason == '6') {
    $string = 'Install equipment forgotten at install';
  } elseif ($sc_reason == '7') {
    $string = 'Other';
  }
*/

	
	
  if ($sc_reason == '1') {
    $string = 'Exchange Rider';

    $install_equipment_name = equipment_array_to_string($install_equipment);
    $remove_equipment_name = equipment_array_to_string($remove_equipment);

//	$form['sc']['name'] = $string;
	$form['sc']['install_equipment_name'] = $install_equipment_name;
	$form['sc']['remove_equipment_name'] = $remove_equipment_name;
	
   // $string .= '&nbsp;&nbsp;Remove:&nbsp;&nbsp; '. $remove_equipment_name . '<br>';
   // $string .= '&nbsp;&nbsp;Install:&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp; '. $install_equipment_name . '<br>';
  } elseif ($sc_reason == '2') {
	  
    $string = 'Install New Rider or BBox';
	$eq_string = tep_create_confirmation_equipment_string_bgdn($optional);
	$form['sc']['equipment_array'] = $eq_string;
  //  $string .= '<br>' . tep_create_confirmation_equipment_string($optional);
  
  } elseif ($sc_reason == '3') {
    //$string = 'Replace/Exchange Agent SignPanel';
    for ($n = 0, $m = count($equipment); $n < $m; $n++) {
      $query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment[$n] . "' limit 1");
      $result = $database->fetch_array($query);
	  $form['sc']['agent_equipment_name'] = $result['name'];
      //$string .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Install ' . $result['name'];
    }
  } elseif ($sc_reason == '4') {
    if ($sc_reason_4 == '1') {
      $string = 'Weather';
    } elseif ($sc_reason_4 == '2') {
      $string = 'Improper Installation';
    } elseif ($sc_reason_4 == '3') {
      $string = 'Someone moved Post';
    } elseif ($sc_reason_4 == '4') {
      $string = 'Other';
    }
    $show_payment = 0;
  } elseif ($sc_reason == '5') {
	  $string = 'Details are marked in the comments section.';
  } elseif ($sc_reason == '6') {
      $show_payment = 0;
    $string = 'Install equipment forgotten at install';
	$eq_string = tep_create_confirmation_equipment_string_bgdn($optional);
	$form['sc']['equipment_array'] = $eq_string;
   // $string = tep_create_confirmation_equipment_string($optional);
  } elseif ($sc_reason == '7') {
    $string = 'Details are marked in the comments section.';
  }
  //echo $string;
  
  $form['sc']['name'] = $string;

} 

	$vars['order_type'] = $order_type;
	$vars['show_payment'] = $show_payment;
	//$vars['deferred'] = $deferred;
	$vars['form'] = $form;	
	$vars['email_data'] = tep_fetch_email_data($selected_agent_id);
	$vars['agent_data'] = tep_fetch_agent_data($selected_agent_id);
	$vars['step'] = 5;	 
	//print_r($vars['agent_data']);
	echo $twig->render('aom/aom_order_create_success.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'order'=>$data, 'vars'=>$vars));

?>
