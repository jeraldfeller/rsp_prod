<?php

@session_start(); 

	$page_action = tep_fill_variable('page_action', 'get');
	$order_type = tep_fill_variable('order_type_id', 'session');
	$submit_string = tep_fill_variable('submit_string_y', 'post');
	$payment_method = tep_fill_variable('payment_method_id', 'post', tep_fill_variable('payment_method_id', 'session', $user->fetch_billing_method_id()));

	//$start_day = tep_fill_variable('start_day', 'post', date("d", tep_fill_variable('schedualed_start', 'session', mktime(0, 0, 0, date("n"), (date("d") + 1)))));
	//$start_month = tep_fill_variable('start_month', 'post', date("n", tep_fill_variable('schedualed_start', 'session', mktime(0, 0, 0, date("n"), (date("d") + 1)))));
	//$start_year = tep_fill_variable('start_year', 'post', date("Y", tep_fill_variable('schedualed_start', 'session', mktime(0, 0, 0, date("n"), (date("d") + 1)))));
	
		$jobdate = tep_fill_variable('jobdate', 'post', date("n/d/Y", tep_fill_variable('schedualed_start', 'session', mktime(0, 0, 0, date("n"), (date("d") + 1)))));
	$sc_reason = tep_fill_variable('sc_reason', 'session');

	
	//print_r($jobdate);
	
	if (empty($order_type)) 
	{
		tep_redirect(FILENAME_ORDER_CREATE);
	}
	if($order_type == ORDER_TYPE_INSTALL) 
	{
		$shipping_address = tep_fill_variable('street_name', 'session');
	} 
	else 
	{
		$shipping_address = tep_fill_variable('address_id', 'session');
	}
	if (empty($shipping_address)) 
	{
		tep_redirect(FILENAME_ORDER_CREATE_ADDRESS);
    }

	// Create an order object to calculate and show the cost.
	$request_payment = true;
    //$schedualed_start = mktime(0, 0, 0, $start_month, $start_day, $start_year);
	$schedualed_start = strtotime($jobdate);
	
	$start_month = date("n", $schedualed_start);
				$start_day = date("d", $schedualed_start);
				$start_year = date("Y", $schedualed_start);

    // Miss Utility info
	$miss_utility_yes_no = tep_fill_variable('miss_utility_yes_no', 'session');
	$lamp_yes_no = tep_fill_variable('lamp_yes_no', 'session');
    $lamp_use_gas = tep_fill_variable('lamp_use_gas', 'session');

    $delay_ts = add_business_days(time(), MISS_UTILITY_DELAY + 1);
    $delay = array();
    $delay['day'] = date('j', $delay_ts);
    $delay['month'] = date('n', $delay_ts);
    $delay['year'] = date('Y', $delay_ts);
    $delay['str'] = date('l F j, Y', $delay_ts);

	//echo $miss_utility_yes_no;
	//echo $schedualed_start." vs ".$delay_ts;
	
    if ($miss_utility_yes_no == "yes" || $lamp_use_gas == "yes" || $lamp_use_gas == "unsure") {
        // Adjust start date, if neccesary
        if ( $schedualed_start < $delay_ts ) {
            if ($lamp_use_gas == "yes") {
                $error->add_error('account_create_payment', 'Since there is a gas line in the yard, we must delay your order for ' . MISS_UTILITY_DELAY . ' business days, so that Miss Utility can properly mark any utilities. The earliest we can install the signpost will be ' . $delay['str'] . '.');
            } else {
                $error->add_error('account_create_payment', 'Due to Miss Utility requirements, we must delay your order for ' . MISS_UTILITY_DELAY . ' business days, so that Miss Utility can properly mark any utilities. The earliest we can install the signpost will be ' . $delay['str'] . '.');
            }
            $start_day = $delay['day'];
            $start_month = $delay['month'];
            $start_year = $delay['year'];
            $schedualed_start = $delay_ts;
        }
    }

    $extra_cost = tep_fetch_extra_cost($schedualed_start);
    $extra_cost_string = tep_fetch_extra_cost_string($schedualed_start);

    $zip4 = tep_fill_variable('zip4', 'session');
    $extended_cost = tep_fetch_service_area_cost(tep_fetch_zip4_service_area($zip4));

    $data = array('address_id' => tep_fill_variable('address_id', 'session'),
        'order_type_id' => tep_fill_variable('order_type_id', 'session'),
        'schedualed_start' => $schedualed_start,
        'special_instructions' => tep_fill_variable('special_instructions', 'session'),
        'optional' => tep_fill_variable('optional', 'session'),
        'number_of_posts' => tep_fill_variable('number_of_posts', 'session'),
        'county' => tep_fill_variable('county', 'session'),
        'payment_method' => $payment_method,
        'billing_method_id' => $payment_method,
        'extra_cost' => $extra_cost,
        'extra_cost_description' => $extra_cost_string,
        'extended_cost' => $extended_cost,
        'promo_code' => tep_fill_variable('promo_code', 'session'),
        'sc_reason' => $sc_reason,
        'install_equipment' => tep_fill_variable('install_equipment', 'session', array()),
        'equipment' => tep_fill_variable('equipment', 'session', array()));				//print_r($data);		
	// This will look up any account credit that may be applied
	$order = new orders('', '', $data);
    $extended_cost = $order->create_extended_order_cost($zip4); // mjp
	$total = $order->fetch_order_total($zip4);
	if ($total <= 0)
	{
		$request_payment = false;
	}
	$tomorrow=strtotime("+1 day");

    // Check for Deferred Billing
    
    if ($payment_method == 1 && $user->fetch_billing_method_id() != 3 && $total > 0) {
        $account_id = account::getAccountId($user->fetch_user_id(), $user->agency_id, $payment_method, false);
        $deferred = new DeferredBilling($account_id);
    } else {
        $deferred = new DeferredBilling();
    }
    $deferred_total = $deferred->getTotal();

	// Handle form submission
	if (!empty($page_action) && ($page_action == 'submit') && !empty($submit_string)) 
	{
		//$dt=$_POST['jobdate'];		
		/*$newdate=explode("-","$dt");
		if($newdate[1]=='JAN') $start_month='01';
		elseif($newdate[1]=='FEB') $start_month='02';
		elseif($newdate[1]=='MAR') $start_month='03';
		elseif($newdate[1]=='APR') $start_month='04';
		elseif($newdate[1]=='MAY') $start_month='05';
		elseif($newdate[1]=='JUN') $start_month='06';
		elseif($newdate[1]=='JUL') $start_month='07';
		elseif($newdate[1]=='AUG') $start_month='08';
		elseif($newdate[1]=='SEP') $start_month='09';
		elseif($newdate[1]=='OCT') $start_month='10';
		elseif($newdate[1]=='NOV') $start_month='11';
		elseif($newdate[1]=='DEC') $start_month='12';
		$start_day=$newdate[0];
		$start_year=$newdate[2];*/		
        //$schedualed_start = mktime(0, 0, 0, $start_month, $start_day, $start_year);
		
		
		$schedualed_start = strtotime($jobdate);		
        if ($miss_utility_yes_no == "yes" || $lamp_use_gas == "yes" || $lamp_use_gas == "unsure") {
            // Adjust start date, if neccesary
            if ( $schedualed_start < $delay_ts ) {
                $error->add_error('account_create_payment', 'Due to the requirements of Miss Utility, we will be unable to install the signpost before the current Install Window Start Date. If you want to discuss the install date with us, feel free to call us at '.EMERGENCY_NUMBER.' or e-mail us at '.INFO_EMAIL.'.');
               /* $start_day = $delay['day'];
                $start_month = $delay['month'];
                $start_year = $delay['year'];*/
                $schedualed_start = $delay_ts;
            }
        }


		$address_id = tep_fill_variable('address_id', 'session', '');

		// Validate the job date. Bump Sunday to Monday, error on Holidays.

		/*if ((date("d", $schedualed_start) != $start_day) ||
			(date("n", $schedualed_start) != $start_month) ||
			(date("Y", $schedualed_start) != $start_year)) 
		{
			$error->add_error('account_create_payment', 'Please enter a valid Activity Window Start Date.');
		} */
		
		/*else 
		{*/

			if (tep_date_is_sunday($start_month, $start_day, $start_year)) 
			{
				$schedualed_start += (60*60*24);
				$start_month = date("n", $schedualed_start);
				$start_day = date("d", $schedualed_start);
				$start_year = date("Y", $schedualed_start);
			}
			$tomorrow = $schedualed_start;
			if ($schedualed_start < time())
			{
				$error->add_error('account_create_payment', 'The date you have selected is in the past, please select a future date.');
			} 
			elseif (tep_date_is_holiday($start_day, $start_month, $start_year) !== false) 
			{
				$error->add_error('account_create_payment', 'The day you have chosen is a holiday.  Please try again. ');
			} 
			else 
			{
			  	if (!empty($address_id) && ($schedualed_start < tep_fetch_install_date($address_id))) 
				{
					//echo $schedualed_start;
					$error->add_error('account_create_payment', 'Your job start date must be after the install date.');
				} 
				else 
				{
					$session->php_session_unregister('schedualed_start');
					$session->php_session_register('schedualed_start', $schedualed_start);
				}
			}
		/*}*/

		// For CC, pre-check the details if a payment is required

		if (($payment_method == '1') && $request_payment) 
		{
			$cc_type = tep_fill_variable('cc_type');
			$cc_name = tep_fill_variable('cc_name');
			$cc_number = str_replace(array('-', ' '), '', tep_fill_variable('cc_number'));
			$cc_month = tep_fill_variable('cc_month');
			$cc_year = tep_fill_variable('cc_year');
			$cc_verification_number = tep_fill_variable('cc_verification_number');
			$cc_billing_street = tep_fill_variable('cc_billing_street');
			$cc_billing_city = tep_fill_variable('cc_billing_city');
			$cc_billing_zip = tep_fill_variable('cc_billing_zip');

			$cc_proccessing = new cc_proccessing();
			$error_code = '';
			$error_text = '';
			$cc_proccessing->pre_transaction($cc_number, $cc_type, $error_code, $error_text);

			if (!empty($error_code)) 
			{
				$error->add_error('account_create_payment', 'The credit card you entered is invalid.  Please try again.');
				$error->cc_error(__FILE__.':'.__LINE__.' '.$user->fetch_user_name().'('.$user->fetch_user_id().") \"$error_text\"");
			} 
			else 
			{
				if (empty($cc_billing_street)) 
				{
					if (!empty($error_text)) {$error_text .= '<br>';}
					$error_text .= 'Please enter your billing street';
				}
				if (empty($cc_billing_city)) 
				{
					if (!empty($error_text)) {$error_text .= '<br>';}
					$error_text .= 'Please enter your billing city';
				}
				if (empty($cc_billing_zip)) 
				{
					if (!empty($error_text)) {$error_text .= '<br>';}
					$error_text .= 'Please enter your billing zip';
				}
                if (empty($cc_verification_number)) 
				{
                    if (!empty($error_text)) {$error_text .= '<br>';}
                    $error_text .= 'Please enter your security code';
                }
				if (!empty($error_text)) 
				{
					$error->add_error('account_create_payment', $error_text);
				}
			}
		}

		// Save CC details for next screen if no errors

		if (!$error->get_error_status('account_create_payment')) 
		{
			$session->php_session_register('payment_method_id', $payment_method);
			if ($payment_method == BILLING_METHOD_CREDIT && $request_payment) 
			{
				//Credit card.
				$session->php_session_register('cc_type', $cc_type);
				$session->php_session_register('cc_name', $cc_name);
				$session->php_session_register('cc_number', $cc_number);
				$session->php_session_register('cc_month', $cc_month);
				$session->php_session_register('cc_year', $cc_year);
				$session->php_session_register('cc_verification_number', $cc_verification_number);
				$session->php_session_register('cc_billing_street', $cc_billing_street);
				$session->php_session_register('cc_billing_city', $cc_billing_city);
				$session->php_session_register('cc_billing_zip', $cc_billing_zip);
			}

			tep_redirect(FILENAME_ORDER_CREATE_CONFIRMATION);
		}

	} // end submit

	if ($session->php_session_is_registered('cc_error')) 
	{
		$error->add_error('account_create_payment', tep_fill_variable('cc_error', 'session'));
		$session->php_session_unregister('cc_error');
	}

	//$tomorrow_date=date('d-M-Y',"$tomorrow");
	$show_date=date('m/d/Y',"$schedualed_start");

	if ($order_type != ORDER_TYPE_INSTALL) 
	{
		if (!isset($address_id))
		{
			$address_id = tep_fill_variable('address_id', 'session');
		}
		$address_information = tep_fetch_address_information($address_id);
		$house_number = $address_information['house_number'];
		$street_name = $address_information['street_name'];
		$city = $address_information['city'];
		$county_name = $address_information['county_name'];
		$zip = $address_information['zip'];
		$state_name = $address_information['state_name'];
	} 
	else 
	{
		$house_number = tep_fill_variable('house_number', 'session'); 
		$street_name = tep_fill_variable('street_name', 'session'); 
		$city = tep_fill_variable('city', 'session'); 
		$county = tep_fill_variable('county', 'session'); 
		$zip = tep_fill_variable('zip', 'session'); 
		$state_id = tep_fill_variable('state', 'session'); 
		$state_name = tep_get_state_name($state_id);
	}		
	if ($payment_method == BILLING_METHOD_CREDIT) {
		$payment['cc_type'] = tep_fill_variable('cc_type', 'post', tep_fill_variable('cc_type', 'session'));
		$payment['cc_name'] = tep_fill_variable('cc_name', 'post', tep_fill_variable('cc_name', 'session'));
		$payment['cc_number'] = tep_fill_variable('cc_number', 'post', tep_fill_variable('cc_number', 'session'));	
		$payment['cc_month'] = tep_fill_variable('cc_month', 'post', tep_fill_variable('cc_month', 'session', date("n")));
		$payment['cc_year'] = tep_fill_variable('cc_year', 'post', tep_fill_variable('cc_year', 'session', (date("Y")+1)));
		$payment['cc_verification_number'] = tep_fill_variable('cc_verification_number', 'post', tep_fill_variable('cc_verification_number', 'session'));
		$payment['cc_billing_street'] = tep_fill_variable('cc_billing_street', 'post', tep_fill_variable('cc_billing_street', 'session'));	
		$payment['cc_billing_city'] = tep_fill_variable('cc_billing_city', 'post', tep_fill_variable('cc_billing_city', 'session'));	
		$payment['cc_billing_zip'] = tep_fill_variable('cc_billing_zip', 'post', tep_fill_variable('cc_billing_zip', 'session'));
		
		$pulldowns = array(
			'payment_method_id' => tep_draw_billing_method_pulldown_bgdn('payment_method_id', $payment_method, 'change-submit'),
			'cc_type' => tep_draw_credit_card_type_pulldown_bgdn('cc_type', $payment['cc_type'])
		);
	}	
	else {
		$payment = false;
		$pulldowns = false;
		$pulldowns = array(
			'payment_method_id' => tep_draw_billing_method_pulldown_bgdn('payment_method_id', $payment_method, 'change-submit'),
		);
	}
	
	
		$vars = array(
			'payment'=>$payment,
			'order_type'=>$order_type,
			'order_total'=>$total,
			'request_payment'=>$request_payment,
			'payment_method'=>$payment_method,
			'step'=>3,
			'extra_cost_string'=>tep_fetch_extra_cost_string($schedualed_start),
			'jobdate'=>$show_date,
			'pulldowns'=>$pulldowns
		);
		
		//print_r($vars);
		//echo $deferred_total;
		
		if ($user->fetch_billing_method_id() == 1 && $deferred_total != 0) {
			$vars['deferred'] = $deferred->createSiteHTMLTwig($total);
		} else {
			$vars['deferred'] = null;
		}


		echo $twig->render('order/order_create_payment.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'order'=>$order, 'vars'=>$vars));
		
	/*if ($user->fetch_billing_method_id() == 1 && $deferred_total != 0) {
        echo $deferred->createSiteHTML($total);
    }*/

?>