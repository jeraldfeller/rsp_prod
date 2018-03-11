<?php
// Updated 1/10/13 brad@brgr2.com

@session_start();

    /*if (isset($_SESSION['user_id'])) {
        $user_id=$_SESSION['user_id'];
        $user_name=$_SESSION['user_name'];
    } else {
      $error->add_error('order_create_confirmation', 'User information not available so order confirmation aborted.');
      tep_redirect(FILENAME_ORDER_CREATE);
    }*/
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
// mjp make extended cost exd rather than ext which is extra cost
// mjp improve timeout error handling.
  $user_id=$user->fetch_user_id();
  $user_name=$_SESSION['user_name'];
  $page_action = tep_fill_variable('page_action', 'get');
  $order_type = tep_fill_variable('order_type_id', 'session');
  $tos = tep_fill_variable('tos', 'post');
  $pna = tep_fill_variable('pna', 'post');

  $sc_reason = tep_fill_variable('sc_reason', 'session');
  $sc_reason_4  = tep_fill_variable('sc_reason_4', 'session');
  $sc_reason_5  = tep_fill_variable('sc_reason_5', 'session');
  $sc_reason_7  = tep_fill_variable('sc_reason_7', 'session');
  $equipment  = tep_fill_variable('equipment', 'session', array());
  $install_equipment  = tep_fill_variable('install_equipment', 'session', array());
  $remove_equipment  = tep_fill_variable('remove_equipment', 'session', array());

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
  $payment_method = tep_fill_variable('payment_method_id', 'session');
  if (empty($payment_method)) {
    tep_redirect(FILENAME_ORDER_CREATE_PAYMENT);
  }
  //Get all variable from user table
  $query_user=$database->query("select * from ". TABLE_USERS ." where user_id='$user_id'");
  $result_user = $database->fetch_array($query_user);
  $email_address=$result_user['email_address'];
  $agency_id=$result_user['agency_id'];
  $agent_id=$result_user['agent_id'];
  $query_agency=$database->query("select * from ". TABLE_AGENCYS ." where agency_id='$agency_id'");
  $result_agency = $database->fetch_array($query_agency);
  $agency_name=$result_agency['name'];
  $agency_address=$result_agency['address'];
  //Get all the variables.
  $special_instructions = tep_fill_variable('special_instructions', 'session');
  $optional = tep_fill_variable('optional', 'session', array());
  $optional = parse_equipment_array($optional);
  $session->php_session_register('optional', $optional);

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
  $schedualed_start = tep_fill_variable('schedualed_start', 'session');
  $miss_utility_yes_no = tep_fill_variable('miss_utility_yes_no', 'session');
  $lamp_yes_no = tep_fill_variable('lamp_yes_no', 'session');
  $lamp_use_gas = tep_fill_variable('lamp_use_gas', 'session');

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

  // Check for Deferred Billing

  if ($payment_method == 1 && $user->fetch_billing_method_id() != 3) {
    $account_id = account::getAccountId($user->fetch_user_id(), $user->agency_id, $payment_method, false);
    $deferred = new DeferredBilling($account_id);
  } else {
    $deferred = new DeferredBilling();
  }
  $deferred_total = $deferred->getTotal();

  if (!empty($page_action) && ($page_action == 'submit')) {
    if (empty($tos) || (!$tos)) {
      $error->add_error('order_create_confirmation', 'You must agree to the Terms of Service before placing an order.');
    }
    if (empty($pna) && !tep_address_post_is_allowed($house_number, $street_name, $city, $county, $state)) {
      //$error->add_error('order_create_confirmation', 'You must acknowledge the signpost not allowed warning before placing an order .');
    }

    if (!$error->get_error_status('order_create_confirmation')) {
      //Proccess.

      $schedualed_start = tep_fill_variable('schedualed_start', 'session');

      $extra_cost = tep_fetch_extra_cost($schedualed_start);
      $extra_cost_string = tep_fetch_extra_cost_string($schedualed_start);

      $extended_cost = tep_fetch_service_area_cost(tep_fetch_zip4_service_area($zip4)); //mjp

      $data = array('address_id' => tep_fill_variable('address_id', 'session'),
        'order_type_id' => $order_type,
        'schedualed_start' => tep_fill_variable('schedualed_start', 'session'),
        'special_instructions' => tep_fill_variable('special_instructions', 'session'),
        'optional' => $optional,
        'number_of_posts' => $number_of_posts,
        'county' => tep_fill_variable('county', 'session'),
        'payment_method' => $payment_method,
        'billing_method_id' => $payment_method,
        'extended_cost' => $extended_cost,
        'extra_cost' => $extra_cost,
        'extra_cost_description' => $extra_cost_string,
        'promo_code' => tep_fill_variable('promo_code', 'session'),
        'miss_utility_yes_no' => $miss_utility_yes_no,
        'lamp_yes_no' => $lamp_yes_no,
        'lamp_use_gas' => $lamp_use_gas,
        'sc_reason' => $sc_reason,
        'install_equipment' => $install_equipment,
        'equipment' => $equipment);

      $order = new orders('', '', $data);
      $total = $order->fetch_order_total($zip4);
      $credit = $order->credit;

      if ($total == 0) {
          // Don't apply Deferred Billing to free orders
          $deferred = new DeferredBilling();
          $deferred_total = $deferred->getTotal();
      }

      $session->php_session_register('order_with_credit_total', $total);
      $session->php_session_register('credit', $credit);

      if (($payment_method == BILLING_METHOD_CREDIT) && ($total + $deferred_total > 0)){
        //Credit card so lets try the payment.

        $cc_proccessing = new cc_proccessing();
        $error_code = '';
        $error_text = '';
        $error_string = '';
        $cc_proccessing->pre_transaction(tep_fill_variable('cc_number', 'session'), tep_fill_variable('cc_type', 'session'), $error_code, $error_text);
        $errorFlag = true;
        if (empty($error_code)) {
          $agency_query = $database->query("select a.name, a.service_level_id, a.billing_method_id, a.address, a.contact_name, a.contact_phone from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.user_id = '" . $user->fetch_user_id() . "' and u.agency_id = a.agency_id limit 1");
          $agency_result = $database->fetch_array($agency_query);

          $cc_proccessing->set_proccessing_variable('bill_first_name', tep_fill_variable('user_first_name', 'session'));
          $cc_proccessing->set_proccessing_variable('bill_last_name', tep_fill_variable('user_last_name', 'session'));
          $cc_proccessing->set_proccessing_variable('bill_address_one', tep_fill_variable('cc_billing_street', 'session'));
          $cc_proccessing->set_proccessing_variable('bill_city', tep_fill_variable('cc_billing_city', 'session'));
          $cc_proccessing->set_proccessing_variable('bill_zip_or_postal_code', tep_fill_variable('cc_billing_zip', 'session'));
          $cc_proccessing->set_proccessing_variable('bill_company', stripslashes($agency_result['name']));
          $cc_proccessing->set_proccessing_variable('bill_country_code', 'US');
          $cc_proccessing->set_proccessing_variable('bill_phone', $agency_result['contact_phone']);
          $cc_proccessing->set_proccessing_variable('order_description', tep_get_order_type_name($order_type).": $house_number $street_name");
          $infoStr = date('ymd-His').str_replace(' ','-'," $house_number $street_name");
          $cc_proccessing->set_proccessing_variable('invoice_number', $infoStr);
          $cc_proccessing->set_proccessing_variable('order_id', $infoStr);
          $cc_proccessing->set_proccessing_variable('credit_card_number', tep_fill_variable('cc_number', 'session'));
          $cc_proccessing->set_proccessing_variable('charge_type', 'AUTH');
          $cc_proccessing->set_proccessing_variable('expire_month', tep_fill_variable('cc_month', 'session'));
          $cc_proccessing->set_proccessing_variable('expire_year', tep_fill_variable('cc_year', 'session'));
          $cc_proccessing->set_proccessing_variable('credit_card_verification_number', tep_fill_variable('cc_verification_number', 'session'));
          $cc_proccessing->set_proccessing_variable('charge_total', number_format($total + $deferred_total, 2));
          $cc_proccessing->set_proccessing_variable('order_user_id', $user->fetch_user_id());
          $cc_proccessing->set_proccessing_variable('reference_id', $_SERVER['REMOTE_ADDR']);
          $cc_proccessing->set_proccessing_variable('card_brand', tep_fill_variable('cc_type', 'session'));
          unset($order);
          $error->cc_error("Sending $user_name($user_id) $house_number $street_name for $total");



		//test



		 $cc_proccessing->preform_transaction();

          if ($cc_proccessing->return_response() == 1) {


			//  if(true) {

            //accepted
            $errorFlag = false;
            $actuallyChargedTotal = $total + $deferred_total;
            $error->cc_error("Accepted $user_name($user_id) $house_number $street_name for $actuallyChargedTotal");
            if ($deferred_total > 0) {
                // Apply the deferred total to the Agent Account
                $account = new account($user->fetch_user_id(), $account_id, $payment_method);
                $account->apply_deferred($deferred_total, "Paid with " . tep_get_order_type_name($order_type).": $house_number $street_name");
            }
          } else {
            $errorFlag = true;
            $error_string = 'There was an error processing the credit card you entered.  Please try again.';
            $error->cc_error(__FILE__.':'.__LINE__." $user_name($user_id) rcode ".$cc_proccessing->return_response().' "'. implode("\n",$cc_proccessing->error_messages()) ."\"");
            $error_string .= ' The gateway responded: ';
            foreach($cc_proccessing->error_messages() as $value) { $error_string .= ' '.$value; }
          }
        } else {
          $errorFlag = true;
          $error_string = 'The credit card you entered is invalid.  Please try again.';
          $error->cc_error(__FILE__.':'.__LINE__." $user_name($user_id) \"$error_text\"");
        }
        if ($errorFlag) {
          $session->php_session_register('cc_error', $error_string);
          tep_redirect(FILENAME_ORDER_CREATE_PAYMENT);
        }
      } else {
            $error->cc_error("Not credit $user_name($user_id) $house_number $street_name for $total");
      }
      if (empty($address_id))
	  {
        if (tep_zip4_is_valid($zip4_code))
		{
          $explode = tep_break_zip4_code($zip4_code);
          $zip4_start = $explode[0];
          $zip4_end = $explode[1];
        }
        else
        {
			$zip4_start = '';
          	$zip4_end = '';
		}
        if (!empty($adc_page) && $adc_page != 'Page') {
          $adc_number = $adc_page.'_'.$adc_letter.'_'.$adc_number;
        } else {
          $adc_number = '';
        }
        if (tep_address_post_is_allowed($house_number, $street_name, $city, $county, $state)) {
          $post_allowed = '1';
        } else {
          $post_allowed = '0';
        }
        $database->query("insert into " . TABLE_ADDRESSES . " (house_number, street_name, city, zip, state_id, county_id, zip4, zip4_start, zip4_end, adc_number, number_of_posts, cross_street_directions, address_post_allowed) values ('" . $house_number . "', '" . $street_name . "', '" . $city . "', '" . $zip . "', '" . $state . "', '" . $county . "', '" . $zip4_code . "', '" . $zip4_start . "', '" . $zip4_end . "', '" . $adc_number . "','" . $number_of_posts . "', '" . $cross_street_directions . "', '" . $post_allowed . "')");
        $address_id = $database->insert_id();
        $database->query("insert into " . TABLE_ADDRESSES_TO_USERS . " (address_id, user_id) values ('" . $address_id . "', '" . $user->fetch_user_id() . "')");

        $session->php_session_unregister('address_id');
        $session->php_session_register('address_id', $address_id);
      }

      $schedualed_start = tep_fill_variable('schedualed_start', 'session');

      $extra_cost_array = fetch_extra_cost_array($schedualed_start);

      $extra_cost = tep_fetch_extra_cost($extra_cost_array);
      $extra_cost_string = tep_fetch_extra_cost_string($extra_cost_array);
      $extra_cost_code_string = tep_fetch_extra_cost_code_string($extra_cost_array);

      $data = array('address_id' => tep_fill_variable('address_id', 'session'),
        'order_type_id' => tep_fill_variable('order_type_id', 'session'),
        'schedualed_start' => tep_fill_variable('schedualed_start', 'session'),
        'special_instructions' => tep_fill_variable('special_instructions', 'session'),
        'optional' => $optional,
        'number_of_posts' => $number_of_posts,
        'county' => tep_fill_variable('county', 'session'),
        'payment_method' => $payment_method,
        'billing_method_id' => $payment_method,
        'extended_cost' => $extended_cost,
        'extra_cost' => $extra_cost,
        'extra_cost_description' => $extra_cost_string,
        'miss_utility_yes_no' => tep_fill_variable('miss_utility_yes_no', 'session'),
        'lamp_yes_no' => tep_fill_variable('lamp_yes_no', 'session'),
        'lamp_use_gas' => tep_fill_variable('lamp_use_gas', 'session'),
        'promo_code' => tep_fill_variable('promo_code', 'session'),
        'sc_reason' => $sc_reason,
        'equipment' => $equipment,
        'special_conditions' => $extra_cost_code_string,
        'install_equipment' => $install_equipment,
        'remove_equipment' => $remove_equipment);

      if ($sc_reason == '4') {
        $data['sc_detail'] = $sc_reason_4;
      } elseif ($sc_reason == '5') {
        $data['sc_detail'] = $sc_reason_5;
      } elseif ($sc_reason == '7') {
        $data['sc_detail'] = $sc_reason_7;
      }

      $order = new orders('insert', '', $data);

      // Catch any mismatches and save to log
      if (isset($actuallyChargedTotal)) {
        $insertedTotal = (tep_fill_variable('order_type_id', 'session') == ORDER_TYPE_SERVICE)
          ? $order->fetch_order_total_sc() : $order->fetch_order_total();
        if ($insertedTotal != $actuallyChargedTotal) {
          $error->cc_error(__FILE__.':'.__LINE__." total mismatch: db $insertedTotal != charged $actuallyChargedTotal\n". print_r($order, true));
        }
      }

      // Save the order_id for reference on _success
      $order_id = $order->id;
      $session->php_session_register('order_id', $order_id);

      // Save the deferred billing information as well
      $session->php_session_register('deferred_total', $deferred->getTotal());
      $session->php_session_register('deferred_transactions', $deferred->getTransactions());
      $session->php_session_register('deferred_credit', $deferred->getCredit());

      $auto_remove_period = $result_agency['auto_remove_period'];
      if (empty($auto_remove_period) || $auto_remove_period <= 0) {
          $auto_remove_period = AUTOMATIC_REMOVAL_TIME;
      }
      //Now we will generate the entry for the automatic removal.
      if (($auto_remove_period > 0) && (tep_fill_variable('order_type_id', 'session') == ORDER_TYPE_INSTALL)) {

        //We want to set one so lets work it out.
        //There are 86400 seconds in a day so we will use that.
        $delay = 86400 * ($auto_remove_period - 1);
        $removal_time = ($schedualed_start + $delay);
        //Now add business days until we hit a Monday
        do {
            $removal_time = add_business_days($removal_time, 1);
            $removal_day = date('N', $removal_time); // get the day of the week
        } while ($removal_day != 1);  // and check to see if it's a Monday

        //Now we have a removal time.  Set the data and insert.

        $data = array('address_id' => tep_fill_variable('address_id', 'session'),
          'order_type_id' => ORDER_TYPE_REMOVAL,
          'schedualed_start' => $removal_time,
          'county' => tep_fill_variable('county', 'session'),
          'promo_code' => tep_fill_variable('promo_code', 'session'),
          'number_of_posts' => $number_of_posts,
          'billing_method_id' => $payment_method);
        $order = new orders('insert', '', $data, '', false, '1');
      }

      //Send the email.
      // echo "email method called<br/>";
      tep_format_order_email($order_id, 'order_confirm', '', $credit);
      $default_installerID = tep_fetch_assigned_order_installer($order_id);
      //check_subscribe_installer($default_installerID,$order_id,$credit);
      tep_redirect(FILENAME_ORDER_CREATE_SUCCESS);

    }
  }

  //Work out if this is a rush or sat install and create the extra cost.
  $schedualed_start = tep_fill_variable('schedualed_start', 'session');
  $extra_cost = tep_fetch_extra_cost($schedualed_start);

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

  $service_area_id = tep_fetch_zip4_service_area($zip4_code);

  $fetch_service_area_window = tep_fetch_service_area_window($service_area_id);




$show_payment = true;

$order = new orders('other', '', array('address_id' => $address_id, 'promo_code' => $promo_code, 'optional' => $optional, 'county' => $county, 'zip4' => $zip4, 'payment_method' => $payment_method, 'billing_method_id' => $payment_method, 'extra_cost' => $extra_cost, 'sc_reason' => $sc_reason, 'number_of_posts' => $number_of_posts, 'equipment' => $equipment, 'install_equipment' => $install_equipment), $user->fetch_user_id());

if (($order_type == ORDER_TYPE_SERVICE) ) {
  $total = $order->fetch_order_total_sc();

  if ($sc_reason == '1') {
    //Work out the cost and if a free excahnge has taken place.
  } elseif ($sc_reason == '2') {
    //Cost
  } elseif ($sc_reason == '3') {
    //Cost
  } elseif ($sc_reason == '4') {
    $show_payment = false;
  } elseif ($sc_reason == '5') {

  } elseif ($sc_reason == '6') {
    $show_payment = false;
  } elseif ($sc_reason == '7') {

  }
} elseif ($order_type == ORDER_TYPE_INSTALL) {
  $total = $order->fetch_order_total($zip4);
}

if ($total <= 0) {
    $show_payment = false;
    // Don't apply Deferred Billing to free orders
    $deferred = new DeferredBilling();
    $deferred_total = $deferred->getTotal();
}

$form = array (
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
	'special_instructions'=>$special_instructions,
	'miss_utility_string'=>$miss_utility_string,
	'equipment_array'=>tep_create_confirmation_equipment_string_bgdn($optional),
	'order_total'=>$total,
	'extra_cost_string'=>tep_fetch_extra_cost_string($schedualed_start),
	'deferred_total'=>$deferred_total,
	'fetch_service_area_window'=>$fetch_service_area_window,
	'payment_method'=>$payment_method

);

 $vars['email_data'] = tep_fetch_email_data($user->fetch_user_id());
 $vars['agent_data'] = tep_fetch_agent_data($user->fetch_user_id());

if ($show_payment) {

	$payment_type = tep_get_payment_type_name($payment_method);

	$form['payment_type'] = $payment_type;
	$form['cc_name'] = tep_fill_variable('cc_name', 'session');
	$form['cc_type'] = ucfirst(strtolower(tep_fill_variable('cc_type', 'session')));
	$form['cc_number'] = tep_secure_credit_card_number_bgdn(tep_fill_variable('cc_number', 'session'));
	$form['cc_verification_number'] = tep_fill_variable('cc_verification_number', 'session');
	$form['cc_billing_street'] = tep_fill_variable('cc_billing_street', 'session');
	$form['cc_billing_city'] = tep_fill_variable('cc_billing_city', 'session');
	$form['cc_billing_zip'] = tep_fill_variable('cc_billing_zip', 'session');

  if (SHOW_PROMO_CODE_AREA == 'true') {

    if ($promo_code == '') {
      $promo_string = 'None Entered';
    } elseif (tep_promotional_code_is_valid($promo_code)) {
      $promo_string = $promo_code;
    } else {
      $promo_string = 'Invalid Code';
    }

  }

}

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
  } elseif ($sc_reason == '5') {
	  $string = 'Details are marked in the comments section.';
  } elseif ($sc_reason == '6') {
    $string = 'Install equipment forgotten at install';
	$eq_string = tep_create_confirmation_equipment_string_bgdn($optional);
	$form['sc']['equipment_array'] = $eq_string;
   // $string = tep_create_confirmation_equipment_string($optional);
  } elseif ($sc_reason == '7') {
    $string = 'Details are marked in the comments section.';
  }
  //echo $string;

  $form['sc']['name'] = $string;

?>
<?php
}




if ($user->fetch_billing_method_id() == 1 && $deferred_total != 0) {
    $deferred = $deferred->createSiteHTMLTwigHorizontal($total);
} else {
	$deferred = null;
}

$vars['form'] = $form;
$vars['order_type'] = $order_type;
$vars['step'] = 4;
$vars['show_payment'] = $show_payment;

$vars['deferred'] = $deferred;

	echo $twig->render('order/order_create_confirmation.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'order'=>$order, 'vars'=>$vars));

?>
