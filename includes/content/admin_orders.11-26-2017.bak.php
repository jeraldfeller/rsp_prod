<?php
$page_action = tep_fill_variable('page_action', 'get');
$oID = tep_fill_variable('oID', 'get');
$order_view = tep_fill_variable('order_view', 'get', 'open');
$order_edit = tep_fill_variable('order_edit', 'get', 'open');
$order_status = tep_fill_variable('order_status', 'get', '1');
$order_type = tep_fill_variable('order_type', 'get', '');
$active = tep_fill_variable('active', 'get', '1');
$service_level_id = tep_fill_variable('service_level_id', 'get', '');
$show_order_id = tep_fill_variable('show_order_id', 'get', '');
$list_method = tep_fill_variable('list_method', 'get', '2');

$show_house_number = tep_fill_variable('show_house_number', 'get', '');
$show_street_name = tep_fill_variable('show_street_name', 'get', '');
$show_city = tep_fill_variable('show_city', 'get', '');
$show_zip = tep_fill_variable('show_zip', 'get', '');
$show_zip4 = tep_fill_variable('show_zip4', 'get', '');
$show_between_type = tep_fill_variable('show_between_type', 'get', 'scheduled');
$show_between_start = tep_fill_variable('show_between_start', 'get');
$show_between_end = tep_fill_variable('show_between_end', 'get', ''); //
$inserted_order_type_id = tep_fill_variable('inserted_order_type_id', 'get', '');
$red_flagged = tep_fill_variable('red_flagged', 'get', '');
$miss_utility_to_be_placed = tep_fill_variable('miss_utility_to_be_placed', 'get', '');
$miss_utility_placed = tep_fill_variable('miss_utility_placed', 'get', '');
$miss_utility_open = tep_fill_variable('miss_utility_open', 'get', 0);
$miss_utility_called = tep_fill_variable('miss_utility_called', 'get', 0);
$miss_utility_completed = tep_fill_variable('miss_utility_completed', 'get', 0);
$show_county = tep_fill_variable('show_county_id', 'get', '');
$show_address_id = tep_fill_variable('show_address_id', 'get', '');
$show_number_of_posts = tep_fill_variable('show_number_of_posts', 'get', '');
$show_equipment = tep_fill_variable('show_equipment', 'get', array());

$installer_id = tep_fill_variable('installer_id', 'get', '');
$agent_id = tep_fill_variable('agent_id', 'get', '');
$agency_id = tep_fill_variable('agency_id', 'get', '');
$button_action = tep_fill_variable('button_action_y');

$message = '';
$page_number = tep_fill_variable('page_number', 'post', 1);
$page = tep_fill_variable('page', 'post', 1);

$free_inventory = tep_fill_variable('free_inventory', 'get', tep_fill_variable('free_inventory.x', 'post', ''));
$address_id = tep_fill_variable('address_id', 'post', '');

if ($free_inventory && $address_id) {
    if (getenv("SERVER_MODE") == "TEST") {
        $url = "http://" . $_SERVER['SERVER_NAME'] . "/lib/inventory/free_inventory_json.php?address_id=" . urlencode($address_id);
    } else {
        $url = "http://realtysignpost.net/lib/inventory/free_inventory_json.php?address_id=" . urlencode($address_id); // CloudFlare workaround
    }
    $contents = file_get_contents($url);
    $result = json_decode($contents);
    if (is_object($result) && property_exists($result, 'count')) {
        $freed_items = "<div class='alert alert-info'>" . $result->count . " equipment items made Available.</div>\n";
    } else {
        $freed_items = '';
    }
}


if ($page_action == 'edit_confirm') {
    //Editing the order, load the variables.
    if (!empty($button_action)) {

        $house_number = tep_fill_variable('house_number');
        $street_name = tep_fill_variable('street_name');
        $city = tep_fill_variable('city');
        $state_id = tep_fill_variable('state_id');
        $county_id = tep_fill_variable('county_id');
        $zip = tep_fill_variable('zip');
        $adc_number = tep_fill_variable('adc_number');
        $cross_street_directions = tep_fill_variable('cross_street_directions');
        $order_total = tep_fill_variable('order_total');
        $order_total_old = tep_fill_variable('order_total_old');
        $number_of_posts = tep_fill_variable('number_of_posts');
        $miss_utility_yes_no = tep_fill_variable('miss_utility_yes_no');
        $lamp_yes_no = tep_fill_variable('lamp_yes_no');
        $lamp_use_gas = tep_fill_variable('lamp_use_gas');
        $contacted = tep_fill_variable('contacted');
        $special_instructions = tep_fill_variable('special_instructions');
        $optional = tep_fill_variable('optional', 'post', array());
        $optional = parse_equipment_array($optional);

        $admin_comments = tep_fill_variable('admin_comments');
        $installer_comments = tep_fill_variable('installer_comments');

        $new_order_status_id = tep_fill_variable('new_order_status_id');
        $new_user_notified = tep_fill_variable('new_user_notified');
        $new_comment = tep_fill_variable('new_comment');
        $date_schedualed = tep_fill_variable('date_schedualed');

        $assigned_installer = tep_fill_variable('assigned_installer');


        $extended_cost = tep_fill_variable('extended_cost', 'post', 0);
        $extra_cost = tep_fill_variable('extra_cost', 'post', 0);
        $extra_cost_description = tep_fill_variable('extra_cost_description');
        $red_flag_off = tep_fill_variable('red_flag_off', 'post');
        //Now work out new zip4 code.
        $zip4_code = tep_fill_variable('zip4', 'post', '');
        if (empty($zip4_code)) {
            $zip4_class=new zip4($house_number.' '.$street_name,tep_get_state_name($state_id), $city, $zip);
            if ($zip4_class->search()) {
                $zip4_code = $zip4_class->return_zip_code();
            } elseif ($new_order_status_id != '3') {
                $error->add_error('admin_orders', 'Either the address is invalid or there is a problem with the system.  The zip 4 address was not able to be fetched.');
            }
        }
		
		//Equipment Signpost Installed
        $equipment_id = tep_fill_variable('equipment_id', 'post');
        
        $query = $database->query("select date_schedualed from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
        $oResult = $database->fetch_array($query);
        if ($oResult['date_schedualed'] != strtotime($date_schedualed)) {
            if (strtotime($date_schedualed) < strtotime('today')) {
                $error->add_error('admin_orders', 'That Job Start Date is in the past, please try again.');
            }
        }
        
        if (!$error->get_error_status('admin_orders')) {
            //Update the order.
            $date_schedualed_stamp = strtotime($date_schedualed);
            $query = $database->query("select address_id, user_id, order_type_id from " . TABLE_ORDERS . " where order_id = '" . $oID . "' limit 1");
            $result = $database->fetch_array($query);
            $extended_cost = tep_fetch_service_area_cost(tep_fetch_zip4_service_area($zip4_code)); //mjp
            $data = array('house_number' => $house_number,
                'street_name' => $street_name,
                'city' => $city,
                'state_id' => $state_id,
                'county_id' => $county_id,
                'zip' => $zip,
                'zip4' => $zip4_code,
                'number_of_posts' => $number_of_posts,
                'cross_street_directions' => $cross_street_directions,
                'number_of_posts' => $number_of_posts,
                'miss_utility_yes_no' => $miss_utility_yes_no,
                'lamp_yes_no' => $lamp_yes_no,
                'lamp_use_gas' => $lamp_use_gas,
                'contacted' => $contacted,
                'special_instructions' => $special_instructions,
                'admin_comments' => $admin_comments,
                'assigned_installer' => $assigned_installer,
                'installer_comments' => $installer_comments,
                'address_id' => $result['address_id'],
                'user_id' => $result['user_id'],
                'optional' => $optional,
                'red_flag_off' => $red_flag_off,
                'extended_cost' => $extended_cost,
                'extra_cost' => $extra_cost,
                'extra_cost_description' => $extra_cost_description,
                'date_schedualed' => $date_schedualed_stamp);
            if ($order_total != $order_total_old) {
                $data['order_total_force'] = true;
                $data['order_total'] = $order_total;
            } else {
                $data['order_total_force'] = false;
            }

            $last_modified_by = tep_fill_variable('user_id', 'session', 0);
            $old_order_status_id = tep_fetch_orders_status_id($oID);

            $order = new orders('update', $oID, $data, $result['user_id']);
            $accepted_installer_id = $order->fetch_accepted_installer_id();

            if (!empty($new_comment) || ($new_order_status_id != $old_order_status_id)) {
                tep_create_orders_history($oID, $new_order_status_id, $new_comment, $new_user_notified);
                if ($new_order_status_id == '3') {
                    $database->query("update " . TABLE_ORDERS . " set date_completed = '" . mktime() . "', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where order_id = '" . (int)$oID . "'");
                    if (!$accepted_installer_id) {
                        $database->query("INSERT INTO " . TABLE_INSTALLERS_TO_ORDERS . " (order_id, installer_id) VALUES ('{$oID}', '{$user->fetch_user_id()}')");
                    }

                    if ($result['order_type_id'] == '1') {
                        $database->query("update " . TABLE_ADDRESSES . " set status = '2' where address_id = '" . $result['address_id']. "' limit 1");
                        $equery = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
                        while($eresult = $database->fetch_array($equery)) {
                            $database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $eresult['equipment_id'] . "', '" . $eresult['equipment_item_id'] . "', '2', '" . $result['address_id'] . "')");
                            $database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $eresult['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
                            $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $eresult['equipment_item_id'] . "' limit 1");
                            tep_add_equipment_item_history($eresult['equipment_item_id'], '2', '', $oID, $result['address_id']);
                        }
						// Update Signpost Inventory
                        $database->query("UPDATE ".TABLE_EQUIPMENT_ITEMS." SET equipment_status_id = 1 where equipment_status_id = 0 AND equipment_id = $equipment_id LIMIT 1");
                    } elseif ($result['order_type_id'] == '2') {     // mjp??
                        $database->query("update " . TABLE_ADDRESSES . " set status = '2' where address_id = '" . $result['address_id']. "' limit 1");
                        $equery = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $oID . "'");
                        while($eresult = $database->fetch_array($equery)) {
                            $database->query("insert into " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " (equipment_id, equipment_item_id, equipment_status_id, address_id) values ('" . $eresult['equipment_id'] . "', '" . $eresult['equipment_item_id'] . "', '2', '" . $result['address_id'] . "')");
                            $database->query("update " . TABLE_EQUIPMENT_TO_ORDERS . " set equipment_status_id = '2' where equipment_item_id = '" . $eresult['equipment_item_id'] . "' and order_id = '" . $oID . "' limit 1");
                            $database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '2' where equipment_item_id = '" . $eresult['equipment_item_id'] . "' limit 1");
                            tep_add_equipment_item_history($eresult['equipment_item_id'], '2', '', $oID, $result['address_id']);
                        }
                    } elseif ($result['order_type_id'] == '3') {
                        $equery = $database->query("select eita.equipment_id, eita.equipment_item_id, e.name as equipment_name, e.replace_cost, e.equipment_type_id from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.address_id = '" . $result['address_id'] . "' and eita.equipment_status_id = '2' and eita.equipment_id = e.equipment_id");
                        while($eresult = $database->fetch_array($equery)) {
                            $item_status = 0; //available
							$equipment_status = '0'; //default status (available)
							
							/*	
								run through all orders assigned to those addresses
								get count of all pending removals
								if count>=2 then this item is bugged - we keep it's status as Installed
							*/
							$orders_count =  $database->query('SELECT count(o.`order_id`) as `pending_count` FROM orders o JOIN equipment_items_to_addresses eia ON eia.address_id = o.address_id WHERE o.`order_type_id`=3 AND o.`order_status_id`=1 AND eia.`equipment_item_id`='.$eresult['equipment_item_id']);
							$orders_count_result = $database->fetch_array($orders_count);
							$bugged = intval($orders_count_result['pending_count']); //bugged orders count
							
							
							if ($bugged>=2){
								//this item is bugged
								$item_status = 2;						
							}
							//set a correct status. It might be already set to 2, but just in case there was some status corruption
							$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $item_status . "' where equipment_item_id = '" . $eresult['equipment_item_id'] . "' limit 1");
							
							//other queries are the same
							$database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name, equipment_group_answer_id, method_id) values ('" . $eresult['equipment_id'] . "', '" . $eresult['equipment_item_id'] . "', '" . $oID . "', '" . addslashes($eresult['equipment_name']) . "', '0', '', '', '', '', '', '0')");
							$database->query("update " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " set equipment_status_id = '0' where equipment_item_id = '" . $eresult['equipment_item_id'] . "' and address_id = '" . $result['address_id'] . "' limit 1");
							//added a history record
							tep_add_equipment_item_history($eresult['equipment_item_id'], '3', '', $oID, $result['address_id']); 
                        }
                    }
                } elseif ($new_order_status_id == '4') {
                    $cancelled_order = new orders('cancel', $oID);
                }
            }
            //Check if we need to run the reassign.
            $reassign = tep_fill_variable('reassign', 'post');
            if ($reassign == '1') {
                $user_id = tep_fill_variable('user_id', 'post');
                $last_modified_by = tep_fill_variable('user_id', 'session', 0);

                $database->query("update " . TABLE_ADDRESSES_TO_USERS . " set user_id = '" . $user_id . "' where address_id = '" . $result['address_id'] . "'");
                //echo "update " . TABLE_ADDRESSES_TO_USERS . " set user_id = '" . $user_id . "' where address_id = '" . $result['address_id'] . "'". '<br>';
                $database->query("update " . TABLE_ORDERS . " set user_id = '" . $user_id . "', last_modified = '" . mktime() . "', last_modified_by = '" . $last_modified_by . "' where address_id = '" . $result['address_id'] . "'");
                //echo"update " . TABLE_ORDERS . " set user_id = '" . $user_id . "' where address_id = '" . $result['address_id'] . "'". '<br>';
            }
            $page_action = '';
            $oID = '';
        } else {
            $page_action = 'edit';
        }
    } else {
        $page_action = 'edit';
    }
} elseif ($page_action == 'add_confirm') {

    if (!empty($button_action)) {
        //Get the content and then check it off.

        $order_type_id = tep_fill_variable('order_type_id');

        $user_id = tep_fill_variable('user_id');

        $address_id = tep_fill_variable('address_id');
        $house_number = tep_fill_variable('house_number');
        $street_name = tep_fill_variable('street_name');
        $city = tep_fill_variable('city');
        $zip = tep_fill_variable('zip');
        $zip4_code = tep_fill_variable('zip4_code');
        $adc_number = tep_fill_variable('adc_number');

        $miss_utility_yes_no = tep_fill_variable('miss_utility_yes_no');
        $lamp_yes_no = tep_fill_variable('lamp_yes_no');
        $lamp_use_gas = tep_fill_variable('lamp_use_gas');
        $contacted = tep_fill_variable('contacted');

        $state = tep_fill_variable('state');
        $county = tep_fill_variable('county');
        $number_of_posts = tep_fill_variable('number_of_posts');
        $promo_code = tep_fill_variable('promo_code');
        $cross_street_directions = tep_fill_variable('cross_street_directions');
        $jobdate = tep_fill_variable('jobdate');


        $newdate=explode("-","$jobdate");
        if($newdate[1]=='JAN')
        {
            $start_month='01';
        }
        elseif($newdate[1]=='FEB')
        {
            $start_month='02';
        }
        elseif($newdate[1]=='MAR')
        {
            $start_month='03';
        }
        elseif($newdate[1]=='APR')
        {
            $start_month='04';
        }
        elseif($newdate[1]=='MAY')
        {
            $start_month='05';
        }
        elseif($newdate[1]=='JUN')
        {
            $start_month='06';
        }
        elseif($newdate[1]=='JUL')
        {
            $start_month='07';
        }
        elseif($newdate[1]=='AUG')
        {
            $start_month='08';
        }
        elseif($newdate[1]=='SEP')
        {
            $start_month='09';
        }
        elseif($newdate[1]=='OCT')
        {
            $start_month='10';
        }
        elseif($newdate[1]=='NOV')
        {
            $start_month='11';
        }
        elseif($newdate[1]=='DEC')
        {
            $start_month='12';
        }
        $start_day=$newdate[0];
        $start_year=$newdate[2];

        $job_start_date = $start_month.'/'.$start_day.'/'.$start_year;

        $payment_method_id = tep_fill_variable('payment_method_id');

        $auto_removal = tep_fill_variable('auto_removal', 'post', 1);

        $special_instructions = tep_fill_variable('special_instructions');
        $optional = tep_fill_variable('optional', 'post', array());
        $optional = parse_equipment_array($optional);

        $extra_cost = tep_fill_variable('extra_cost', 'post', 0);
        $extra_cost_description = tep_fill_variable('extra_cost_description');

        $auto_remove_period = tep_fill_variable('auto_remove_period', 'post', AUTOMATIC_REMOVAL_TIME);

        if (empty($order_type_id)) {
            $error->add_error('admin_orders', 'Please select the type of order you wish to create.');
        }
        if (empty($user_id)) {
            $error->add_error('admin_orders', 'Please select the Agent you wish to create this order for.');
        }
        if (($order_type_id == 1) || (($order_type_id == 2) && !empty($house_number))) {
            $fail = false;
            if (empty($house_number)) {
                $error->add_error('admin_orders', 'Please enter a House Number.');
                $fail = true;
            }
            if (empty($street_name)) {
                $error->add_error('admin_orders', 'Please enter a Street Name.');
                $fail = true;
            }
            if (empty($city)) {
                $error->add_error('admin_orders', 'Please enter a City.');
                $fail = true;
            }
            if (empty($zip)) {
                $error->add_error('admin_orders', 'Please enter a Zip Code.');
                $fail = true;
            }
            if (empty($state)) {
                $error->add_error('admin_orders', 'Please select a State.');
                $fail = true;
            }
            if (empty($county)) {
                $error->add_error('admin_orders', 'Please select a County.');
                $fail = true;
            }
            if (empty($cross_street_directions)) {
                $error->add_error('admin_orders', 'Please enter Cross Street/Directions.');
                $fail = true;
            }
            if ((!$fail) && empty($zip4_code)) {
                $zip4_class=new zip4($house_number.' '.$street_name,tep_get_state_name($state), $city, $zip);
                if ($zip4_class->search()) {
                    $zip4_code = $zip4_class->return_zip_code();
                } else {
                    $error->add_error('admin_orders', 'Either the address is invalid or there is a problem with the system.  The zip 4 address was not able to be fetched.');
                }
            }
        } else {
            if (empty($address_id)) {
                $error->add_error('admin_orders', 'Please select and Address to use for this order.');
            }
        }
        if (empty($job_start_date)) {
            $error->add_error('admin_orders', 'Please enter a Job Start Date.');
        } else {
            $date_schedualed = strtotime($job_start_date);
            if ($date_schedualed < mktime()) {
                $error->add_error('admin_orders', 'That Job Start Date is in the past, please try again.');
            } else {
                $day = date("d", $date_schedualed);
                $month = date("n", $date_schedualed);
                $year = date("Y", $date_schedualed);
                if (tep_date_is_holiday($day, $month, $year)) {
                    $error->add_error('admin_orders', 'The day you have chosen is a holiday.  Please try again. ');
                }
            }
        }
        if (!$error->get_error_status('admin_orders')) {
            //Proccess.

            if (empty($address_id)) {
                if (tep_zip4_is_valid($zip4_code)) {
                    $explode = tep_break_zip4_code($zip4_code);
                    $zip4_start = $explode[0];
                    $zip4_end = $explode[1];
                } else {
                    $zip4_start = '';
                    $zip4_end = '';
                }
                $database->query("insert into " . TABLE_ADDRESSES . " (house_number, street_name, city, zip, state_id, county_id, zip4, zip4_start, zip4_end, adc_number, number_of_posts, cross_street_directions) values ('" . $house_number . "', '" . $street_name . "', '" . $city . "', '" . $zip . "', '" . $state . "', '" . $county . "', '" . $zip4_code . "', '" . $zip4_start . "', '" . $zip4_end . "', '" . $adc_number . "','" . $number_of_posts . "', '" . $cross_street_directions . "')");
                $address_id = $database->insert_id();
                $database->query("insert into " . TABLE_ADDRESSES_TO_USERS . " (address_id, user_id) values ('" . $address_id . "', '" . $user_id . "')");

            }
            $data = array('address_id' => $address_id,
                'user_id' => $user_id,
                'order_type_id' => $order_type_id,
                'billing_method_id' => $payment_method_id,
                'schedualed_start' => $date_schedualed,
                'special_instructions' => $special_instructions,
                'number_of_posts' => 1,
                'optional' => $optional,
                'zip4' => $zip4_code,
                'county' => $county,
                'miss_utility_yes_no' => $miss_utility_yes_no,
                'lamp_yes_no' => $lamp_yes_no,
                'lamp_use_gas' => $lamp_use_gas,
                'contacted' => $contacted,
                'extra_cost' => $extra_cost,
                'extra_cost_description' => $extra_cost_description,
                'promo_code' => $promo_code);
            if ($order_type_id == ORDER_TYPE_SERVICE) {
                $sc_reason = tep_fill_variable('sc_reason', 'post', '7');
                $sc_reason_4 = tep_fill_variable('sc_reason_4');
                $sc_reason_5 = tep_fill_variable('sc_reason_5');
                $sc_reason_7 = tep_fill_variable('sc_reason_7');
                $install_equipment = tep_fill_variable('install_equipment', 'post', array());
                $remove_equipment = tep_fill_variable('remove_equipment', 'post', array());

                $data['sc_reason'] = $sc_reason;
                $data['install_equipment'] = $install_equipment;
                $data['remove_equipment'] = $remove_equipment;

                switch ($sc_reason) {
                case 4:
                    $data['sc_detail'] = $sc_reason_4;
                    break;
                case 5:
                    $data['sc_detail'] = $sc_reason_5;
                    break;
                case 7:
                    $data['sc_detail'] = $sc_reason_7;
                    break;
                }
            }
            $order = new orders('insert', '', $data, $user_id, true, '2');
            $order_id = $order->id;
            $credit = $order->credit;

            tep_format_order_email($order_id, 'aom_order_confirm', $user->fetch_user_id(), $credit);

            //Now we will generate the entry for the automatic removal.
            if (($auto_removal == '1') && ($auto_remove_period > 0)) {
                //We want to set one so lets work it out.
                //There are 86400 seconds in a day so we will use that.
                $delay = 86400 * ($auto_remove_period - 1);
                $removal_time = ($date_schedualed + $delay);
                //Now add business days until we hit a Monday
                do {
                    $removal_time = add_business_days($removal_time, 1);
                    $removal_day = date('N', $removal_time); // get the day of the week
                } while ($removal_day != 1);  // and check to see if it's a Monday
                //Now we have a removal time.  Set the data and insert.
                $data = array('address_id' => $address_id,
                    'user_id' => $user_id,
                    'billing_method_id' => $payment_method_id,
                    'zip4' => $zip4_code,
                    'order_type_id' => 3,
                    'schedualed_start' => $removal_time,
                    'county' => $county);
                $order = new orders('insert', '', $data, $user_id, true, '2');
            }
        } else {
            $page_action = 'add';
        }
    } else {
        $page_action = 'add';
    }
}

if (isset($freed_items)) echo $freed_items;
?>
<table width="100%" cellspacing="0" cellpadding="0">
<?php
if ($error->get_error_status('admin_orders')) {
?>
    <tr>
        <td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_orders'); ?></td>
    </tr>
<?php
}
?>
<?php
if ($error->get_error_status('admin_orders', 'warning')) {
?>
    <tr>
        <td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_orders', 'warning'); ?></td>
        <td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_orders', 'warning'); ?></td>
    </tr>
<?php
}
?>
    <tr>
        <td width="100%" valign="top">
<?php
if (($page_action != 'add') && ($page_action != 'edit') && ($page_action != 'view')) {
	
	//die();
	
    $where = '';
    $extra_tables = '';
    if (empty($show_address_id)) {
        if (!empty($show_house_number)) {
            $where .= ' and ';
            //$where .= " (a.house_number = '" . $show_house_number . "' or a.house_number like '" . $show_house_number . "%'  or a.house_number like '%" . $show_house_number . "')";
            $where .= " (a.house_number = '" . $show_house_number . "' or a.house_number like '" . $show_house_number . "')";
        }
        if (!empty($show_city)) {
            $where .= ' and ';
            $where .= " (a.city = '" . $show_city . "' or a.city like '" . $show_city . "%'  or a.city like '%" . $show_city . "')";
        }
        if (!empty($show_zip)) {
            $where .= ' and ';
            $where .= " (a.zip = '" . $show_zip . "' or a.zip like '" . $show_zip . "%'  or a.zip like '%" . $show_zip . "')";
        }
        if (strlen($show_county) > 0) {
            $where .= " and a.county_id='$show_county'";
        }
        if (!empty($show_street_name)) {
            $where .= ' and ';
            $where .= " (a.street_name = '" . $show_street_name . "' or a.street_name like '" . $show_street_name . "%'  or a.street_name like '%" . $show_street_name . "'  or a.street_name like '%" . $show_street_name . "%')";
        }
        if (!empty($show_number_of_posts)) {
            $where .= " and a.number_of_posts = '" . (int)$show_number_of_posts . "'";
        }
    } else {
        $where .= ' and ';
        $where .= " (a.address_id = '" . $show_address_id . "')";
    }
    //if (!empty($show_house_number)) {
    //$where .= ' and ';
    //$where .= " (a.house_number = '" . $show_house_number . "' or a.house_number like '" . $show_house_number . "%'  or a.house_number like '%" . $show_house_number . "')";
    //}
    if (!empty($agent_id)) {
        $where .= ' and ';
        $where .= " o.user_id = '" . $agent_id . "'";
    }

    if (empty($active)) {
        $where .= " and a.status != '4' and a.status != '3'";
    }
    if (!empty($agency_id)) {
        $where .= ' and ';
        $where .= " u.agency_id = '" . $agency_id . "'";
    }

    if (!empty($inserted_order_type_id)) {
        $where .= ' and ';
        $where .= " o.inserted_order_type_id = '" . $inserted_order_type_id . "'";
    }
    if (!empty($service_level_id)) {
        $where .= ' and ';
        $where .= " o.service_level_id = '" . $service_level_id . "'";
    }
    if ($red_flagged == '1') {
        $where .= " and o.order_issue = '1'";
    }
    if ($miss_utility_to_be_placed == '1') {
        $where .= " and o.order_type_id = 1"; 
        $where .= " and omu.contacted = 0"; 
        $where .= " and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))"; 
    }
    if ($miss_utility_placed == '1') {
        $where .= " and o.order_type_id = 1"; 
        $where .= " and omu.contacted = 1";
        $where .= " and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))"; 
    }

    if ($miss_utility_open == '1') {
        $where .= " and o.order_type_id = 1"; 
        $where .= " and o.order_status_id < 3";
        $where .= " and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))"; 
        $where .= " and omu.contacted = 0";
    } else if ($miss_utility_called == '1') {
        $where .= " and o.order_type_id = 1"; 
        $where .= " and o.order_status_id < 3";
        $where .= " and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))"; 
        $where .= " and omu.contacted = 1";
    } else if ($miss_utility_completed == '1') {
        $where .= " and o.order_type_id = 1"; 
        $where .= " and o.order_status_id = 3";
        $where .= " and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))"; 
        $where .= " and omu.contacted = 1";
    }

    if (!empty($order_type)) {
        if ($order_type == '4') {
            $extra_tables .= " left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') ";
            $where .= " and o.order_type_id = '1' and o.order_status_id < '4' and o2.order_id is NULL ";
        } else {
            $where .= " and o.order_type_id = '" . $order_type . "'";
        }
    }
    if (is_array($show_equipment) && !empty($show_equipment)) {
        reset($show_equipment);
        while(list($equipment_type_id, $equipment_id) = each($show_equipment)) {
            if (!empty($equipment_id)) {
                $extra_tables .= ", " . TABLE_EQUIPMENT_TO_ORDERS . " eto" . (int)$equipment_type_id;
                $where .= " and o.order_id = eto" . (int)$equipment_type_id . ".order_id and eto" . (int)$equipment_type_id . ".equipment_id = '" . (int)$equipment_id . "'";
            }
        }
    }


    if (!empty($show_between_type)) {
        if (!empty($show_between_start)) {
            $start_timestamp = @strtotime($show_between_start);
        } else {
            $start_timestamp = 0;
        }
        if (!empty($show_between_end)) {
            $end_timestamp = @strtotime($show_between_end);
            if ($end_timestamp > 0) {
                $end_timestamp += ((60*60*24) - 1); //End as opposed to start of day.
            }
        } else {
            $end_timestamp = 0;
        }
        if ($show_between_type == 'accepted') {
            $where .= " and o.date_accepted > 0 ";
            if ($start_timestamp > 0) {
                $where .= " and o.date_accepted >= '" . $start_timestamp . "' ";
            }
            if ($end_timestamp > 0) {
                $where .= " and o.date_accepted <= '" . $end_timestamp . "' ";
            }
        }
        if ($show_between_type == 'ordered') {
            $where .= " and o.date_added > 0 ";
            if ($start_timestamp > 0) {
                $where .= " and o.date_added >= '" . $start_timestamp . "' ";
            }
            if ($end_timestamp > 0) {
                $where .= " and o.date_added <= '" . $end_timestamp . "' ";
            }
        }
        if ($show_between_type == 'scheduled') {
            $where .= " and o.date_schedualed > 0 ";
            if ($start_timestamp > 0) {
                $where .= " and o.date_schedualed >= '" . $start_timestamp . "' ";
            }
            if ($end_timestamp > 0) {
                $where .= " and o.date_schedualed <= '" . $end_timestamp . "' ";
            }
        }
        if ($show_between_type == 'completed') {
            $where .= " and o.date_completed > 0 ";
            $order_status = '3';
            if ($start_timestamp > 0) {
                $where .= " and o.date_completed >= '" . $start_timestamp . "' ";
            }
            if ($end_timestamp > 0) {
                $where .= " and o.date_completed <= '" . $end_timestamp . "' ";
            }
        }
    }
    if (!empty($show_order_id)) {
        $where = " and o.order_id = '" . (int)$show_order_id . "'";
        $order_status = '';
    }
	
	//die9
	
    if (!empty($installer_id)) {
        if ($installer_id == 'unassigned') {
            $listing_split = new split_page("select o.order_id, o.date_schedualed, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city, o.order_issue from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o. order_id = omu.order_id)" . $extra_tables . ", " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where  o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id" . ((!empty($order_status)) ? " and o.order_status_id = '" . $order_status . "'" : '') . " and o.address_id = a.address_id " . $where . " order by o.date_schedualed " . (($list_method == '1') ? 'ASC' : 'DESC'), '20', 'o.order_id');
        } else {
            $listing_split = new split_page("select o.order_id, o.date_schedualed, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city, o.order_issue, ito.installer_id as itoid, itia.installer_id, ica.installation_area_id, ia.installer_id, itia.installer_id as itiaid  from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id) left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id)" . $extra_tables . ", " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id" . ((!empty($order_status)) ? " and o.order_status_id = '" . $order_status . "'" : '') . $where . " and o.address_id = a.address_id and o.user_id = u.user_id and  ((ito.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $installer_id . "')) group by o.order_id order by o.date_schedualed " . (($list_method == '1') ? 'ASC' : 'DESC'), '20', 'o.order_id');
        }
    } else {
        $listing_split = new split_page("select o.order_id, o.date_schedualed, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city, o.order_issue from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o. order_id = omu.order_id)" . $extra_tables . ", " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where  o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id" . ((!empty($order_status)) ? " and o.order_status_id = '" . $order_status . "'" : '') . " and o.address_id = a.address_id " . $where . " order by o.date_schedualed " . (($list_method == '1') ? 'ASC' : 'DESC'), '20', 'o.order_id');
    }
	
    if ($listing_split->number_of_rows > 0) {
?>
                    <table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
                        <tr>
                            <td class="pageBoxHeading" align="center">Agent Scheduled Date</td>
                            <td class="pageBoxHeading" align="center">Address</td>
                            <td class="pageBoxHeading" align="center">Order Total</td>
                            <td class="pageBoxHeading" align="center">Order Type</td>
                            <td class="pageBoxHeading" align="center">Order Status</td>
                            <td class="pageBoxHeading" align="right">Action</td>
                            <td width="10" class="pageBoxHeading"></td>
                        </tr>
<?php

        if ($installer_id == 'unassigned') {
            $query = $database->query("select o.order_id, o.date_schedualed, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city, o.order_issue from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o. order_id = omu.order_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a, " . TABLE_USERS . " u where  o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id" . ((!empty($order_status)) ? " and o.order_status_id = '" . $order_status . "'" : " and o.order_status_id != '3' and o.order_status_id != '4' ") . ((!empty($order_type)) ? " and o.order_type_id = '" . $order_type . "'" : '') . ((!empty($service_level_id)) ? " and o.service_level_id = '" . $service_level_id . "'" : '') . " and o.order_status_id > 0 and o.address_id = a.address_id " . $where . " order by o.date_schedualed DESC");
        } else {
            $query = $database->query($listing_split->sql_query);
        }
        while($result = $database->fetch_array($query)) {
            $unassigned = false;
            if ($installer_id == 'unassigned') {
                if ((tep_fetch_true_assigned_installer($result['order_id']) == '') && (tep_fetch_assigned_order_installer($result['order_id']) == '')) {
                    $unassigned = true;
                } else {
                    continue;
                }
            } else {
                if ((tep_fetch_true_assigned_installer($result['order_id']) == '') && (tep_fetch_assigned_order_installer($result['order_id']) == '')) {
                    $unassigned = true;
                }
            }
?>
                        <tr>
                            <td class="pageBoxContent" align="center" valign="middle"><?php echo (($result['order_issue'] > 0) ? tep_draw_flag($result['order_issue']) . '&nbsp;&nbsp;' : '') . date("n/d/Y", $result['date_schedualed']); ?></td>
                            <td class="pageBoxContent" align="center"><?php echo $result['house_number']; ?> <?php echo $result['street_name']; ?>, <?php echo $result['city']; ?></td>
                            <td class="pageBoxContent" align="center">$<?php echo $result['order_total']; ?></td>
                            <td class="pageBoxContent" align="center"><?php echo $result['order_type_name']; ?></td>
                            <td class="pageBoxContent" align="center"><?php echo $result['order_status_name']; ?></td>
                            <td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_ORDERS . '?oID='.$result['order_id'].'&page_action=view&' . tep_get_all_get_params(array('oID', 'action', 'page_action')); ?>">View</a><?php if (($result['order_status_id'] != '3') || ($unassigned)) { ?> | <a href="<?php echo FILENAME_ADMIN_ORDERS . '?oID='.$result['order_id'].'&page_action=edit&' . tep_get_all_get_params(array('oID', 'action', 'page_action')); ?>">Edit</a><?php } ?></td>
                            <td width="10" class="pageBoxContent"></td>
                        </tr>
<?php
        }
        if ($installer_id != 'unassigned') {
?>
                        <tr>
                            <td colspan="3">
                                <table class="normaltable" cellspacing="0" cellpadding="2">
                                    <tr>
                                        <td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> orders)'); ?></td>
                                        <td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'x', 'y'. 'oID', 'page_action'))); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
<?php
        }
    }
?>
                </table>
<?php
} else {
    if (!empty($oID)) {
		//die();
        $order = new orders('fetch', $oID);
        $order_data = $order->return_result();
    }
    if ($page_action == 'view') {
		
		
		
		
        $user_query = $database->query("select u.agent_id, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $order_data['user_id'] . "' and u.user_id = ud.user_id limit 1");
        $user_result = $database->fetch_array($user_query);

?>

                <table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Order Id: <?php echo $oID; ?></b></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Address Id: <?php echo $order_data['address_id']; ?></b></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Order Status: <?php echo tep_get_order_status_name($order_data['order_status_id']); ?></b></td>
                    </tr>

                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Agent Information</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Agent Name: </td><td class="pageBoxContent"><a target="_blank" href="/<?php echo FILENAME_ADMIN_USERS ?>?uID=<?php echo $order_data['user_id']; ?>&page_action=edit&"><?php echo $user_result['firstname']; ?> <?php echo $user_result['lastname']; ?></a></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Agency Name: </td><td class="pageBoxContent"><?php echo ((!empty($user_result['name'])) ? $user_result['name'] : 'None'); ?></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>

                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Address Information</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Address: </td><td class="pageBoxContent"><?php echo $order_data['house_number']; ?>, <?php echo $order_data['street_name']; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">City: </td><td class="pageBoxContent"><?php echo $order_data['city']; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">County: </td><td class="pageBoxContent"><?php echo $order_data['county_name']; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">State: </td><td class="pageBoxContent"><?php echo $order_data['state_name']; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Zip: </td><td class="pageBoxContent"><?php echo ((!empty($order_data['zip'])) ? $order_data['zip'] : ''); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Zip4: </td><td class="pageBoxContent"><?php echo $order_data['zip4']; ?></td>
                    </tr>
                    <?php if ($order_data['order_type_id'] == ORDER_TYPE_INSTALL)  { ?>
                    <tr>
                        <td class="pageBoxContent">Agent Miss Utility Requested:</td>
                        <td class="pageBoxContent"><?php echo $order_data['miss_utility_yes_no'];?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Yard Lamp:</td>
                        <td class="pageBoxContent"><?php echo $order_data['miss_utility_yes_no'] == "no" ? $order_data['lamp_yes_no'] : "N/A";?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Gas Lamp:</td>
                        <td class="pageBoxContent"><?php echo $order_data['miss_utility_yes_no'] == "no" ? $order_data['lamp_use_gas'] : "N/A";?></td>
                        </td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Miss Utility Contacted:</td>
                        <td class="pageBoxContent"><?php echo ($order_data['miss_utility_yes_no'] == "yes" || !($order_data['lamp_yes_no'] == "no" || $order_data['lamp_use_gas'] == "no"))? $order_data['contacted'] : "N/A";?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Cross Street/Directions: </td>
                        <td class="pageBoxContent"><?php echo $order_data['cross_street_directions']; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Job Description</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Order Type: </td><td class="pageBoxContent"><?php echo $order_data['order_type_name']; ?></td>
                    </tr>
<?php
        if ($order_data['inserted_order_type_id'] > 0) {
            $type_name = '';
            if ($order_data['inserted_order_type_id'] == '1') {
                $type_name = 'Realtysignpost.net';
            } elseif ($order_data['inserted_order_type_id'] == '2') {
                $type_name = 'Admin';
            } elseif ($order_data['inserted_order_type_id'] == '3') {
                $type_name = 'Order Import';
            } elseif ($order_data['inserted_order_type_id'] == '4') {
                $type_name = 'Realtysignpost.com';
            }
?>
                        <tr>
                            <td class="pageBoxContent">Placed from: </td><td class="pageBoxContent"><?php echo $type_name; ?></td>
                        </tr>
<?php
        }
        if (!empty($order_data['placed_by'])) {
            $placed_query = $database->query("select firstname, lastname from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . (int)$order_data['placed_by'] . "' limit 1");
            $placed_result = $database->fetch_array($placed_query);
?>
                            <tr>
                                <td class="pageBoxContent">Placed By: </td><td class="pageBoxContent"><?php echo $placed_result['firstname'].' '.$placed_result['lastname']; ?></td>
                            </tr>
<?php
        }
?>
<?php
        $assigned_installer_id = tep_fetch_assigned_order_installer($oID);
        if ($order_data['last_modified_by']) {
            $last_modified_by_data = tep_fetch_email_data($order_data['last_modified_by']);
            $last_modified_by = $last_modified_by_data['lastname'] . ", " . $last_modified_by_data['firstname'];
        } else {
            $last_modified_by = "N/A";
        }

?>
                    <tr>
                        <td class="pageBoxContent">Assigned Installer: </td><td class="pageBoxContent"><?php echo (($assigned_installer_id !== false) ? tep_fetch_installer_name($assigned_installer_id) : 'None'); ?></td>
                    </tr>

                    <tr>
                        <td class="pageBoxContent">Date Added: </td><td class="pageBoxContent"><?php echo date("n/d/Y g:i:s a", $order_data['date_added']); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Last Modified: </td><td class="pageBoxContent"><?php echo (($order_data['last_modified'] > 0) ? date("n/d/Y g:i:s a", $order_data['last_modified']) : 'Never'); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Last Modified By: </td><td class="pageBoxContent"><?php echo $last_modified_by; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Agent Scheduled Date: </td><td class="pageBoxContent"><?php echo (($order_data['date_schedualed'] > 0) ? date("n/d/Y", $order_data['date_schedualed']) : 'Never'); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Date Completed: </td><td class="pageBoxContent"><?php echo (($order_data['date_completed'] > 0) ? date("n/d/Y", $order_data['date_completed']) : 'Never'); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Number of Posts: </td><td class="pageBoxContent"><?php echo $order_data['number_of_posts']; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Special Instructions: </td>
                        <td class="pageBoxContent"><?php echo $order_data['special_instructions']; ?></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
<?php
        if (($order_data['order_type_id'] == ORDER_TYPE_INSTALL) ||
            ($order_data['order_type_id'] == ORDER_TYPE_SERVICE))  {
?>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Equipment</b></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="2"><?php echo tep_create_view_equipment_string($order_data['optional'], false, $order_data['order_type_id'], $order_data['order_status_id']); ?></td>
                    </tr>
<?php
            }
        if (($order_data['order_type_id'] == ORDER_TYPE_SERVICE) && isset($order_data['service_call_reason_id'])) {
?>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Reason and Details</b></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="2">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="main"><b>Reason: </b></td>
                                </tr>
                                <tr>
                                <td class="main"><?php
            if ($order_data['service_call_reason_id'] == '1') {
                echo 'Exchange Rider';
                for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($order_data['equipment'][$n]['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$order_data['equipment'][$n]['name'];
                }
            } elseif ($order_data['service_call_reason_id'] == '2') {
                echo 'Install New Rider or BBox';

                for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
                }
            } elseif ($order_data['service_call_reason_id'] == '3') {
                echo 'Replace/Exchange Agent SignPanel';
                for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
                }
            } elseif ($order_data['service_call_reason_id'] == '4') {
                echo 'Post Leaning/Straighten Post';
                if ($order_data['service_call_detail_id'] == '1') {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weather';
                } elseif ($order_data['service_call_detail_id'] == '2') {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Improper Installation';
                } elseif ($order_data['service_call_detail_id'] == '3') {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Someone moved Post';
                } elseif ($order_data['service_call_detail_id'] == '4') {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other';
                }
            } elseif ($order_data['service_call_reason_id'] == '5') {
                echo 'Move Post';
                //Check if any posts were marked as lost and jot themdown.
                for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'] . ' was missing and was replaced.';
                }
            } elseif ($order_data['service_call_reason_id'] == '6') {
                echo 'Install equipment forgotten at install';
                for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
                }
            } elseif ($order_data['service_call_reason_id'] == '7') {
                echo 'Other';
            }
?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
<?php
        }
?>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Agent Preferences</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="2"><?php echo ((tep_agent_has_preferences($order_data['user_id'], $order_data['order_type_id'])) ? tep_create_agent_preferences_string($order_data['user_id'], $order_data['order_type_id']) : 'Agent has no personal preferences.'); ?></td>
                    </tr>
                    <tr>
                    <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                </tr>
                <tr>
                    <td class="mainLarge" colspan="2">Order Totals</td>
                </tr>
                <tr>
                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
                </tr>
<?php

?>
                <tr>
                    <td width="100%" colspan="2">
                        <table cellspacing="0" cellpadding="0">
<?php
        if($order_data['base_cost'] != 0) {
?>
                            <tr>
                                <td class="main" width="140">Base Cost: </td><td class="main">$<?php echo number_format($order_data['base_cost'], 2); ?></td>
                            </tr>
<?php
        }
?>
<?php
        $ext = $order_data['extended_cost'];
        if ($ext != 0) {
?>
                            <tr>
                                <td class="main" width="140">Extended Cost: </td><td class="main">$<?php echo number_format($ext, 2); ?></td>
                            </tr>
<?php
        }
        $eqt = $order_data['equipment_cost'];
        if ($eqt != 0) {
?>
                            <tr>
                                <td class="main" width="140">Equipment Cost: </td><td class="main">$<?php echo number_format($eqt, 2); ?></td>
                            </tr>
<?php
        }
        $dis = $order_data['discount_cost'];
        if (($dis != 0)) {
?>
                            <tr>
                                <td class="main" width="140">Adjustment: </td><td class="main">$<?php echo number_format(($dis), 2); ?></td>
                            </tr>
<?php
        }
        if (!empty($order_data['extra_cost']) && ($order_data['extra_cost'] != 0)) {
?>
                            <tr>
                                <td class="main" width="140">Extra Cost: </td><td class="main">$<?php echo number_format($order_data['extra_cost'], 2); ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140">Extra Cost Reason: </td><td class="main"><?php echo $order_data['extra_cost_description']; ?></td>
                            </tr>
<?php
        }

        $dep = $order_data['deposit_cost'];
        if (($dep != 0)) {
?>
                            <tr>
                                <td class="main" width="140">Deposit: </td><td class="main">$<?php echo number_format(($dep), 2); ?></td>
                            </tr>
<?php
        }
?>
                            <tr>
                                <td class="main" width="140" height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                            </tr>
<?php
        if ($order_data['order_total'] > 0) {
?>
                            <tr>
                                <td class="main" width="140"><b>Total Cost: </b></td><td class="main">$<?php echo number_format($order_data['order_total'], 2); ?></td>
                            </tr>
<?php
        } else {
?>
                            <tr>
                                <td height="1" width="250"><img src="images/pixel_trans.gif" height="1" width="250" /></td>
                            </tr>
                            <tr>
                                <td class="main" width="250"><b>There is no cost for this order.</b></td>
                            </tr>
<?php
        }
?>
                        </table>
                    </td>
                </tr>
                <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>

                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Comments</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" valign="top">Admin Comments: </td>
                        <td class="pageBoxContent"><?php echo $order_data['admin_comments']; ?></td>
                    </tr>
<?php
        if (!empty($order_data['completed_details'])) {
?>
                    <tr>
                        <td class="pageBoxContent" valign="top">Completion Comments: </td>
                        <td class="pageBoxContent"><?php echo $order_data['completed_details']; ?></td>
                    </tr>

<?php
        }
?>
                    <tr>
                        <td class="pageBoxContent" valign="top">Installer Comments: </td>
                        <td class="pageBoxContent"><?php echo $order_data['address_comments']; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" valign="top">Installer Comments for Agent: </td>
                        <td class="pageBoxContent"><?php echo $order_data['installer_comments']; ?></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Order History</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
<?php
        $status_history = tep_fetch_order_history($oID);
        $status_history_count = count($status_history);
        $n = 0;
        while($n < $status_history_count) {
?>
                    <tr>
                        <td class="pageBoxContent" colspan="2" NOWRAP>Date: <?php echo date("n/d/Y", $status_history[$n]['date_added']); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2" NOWRAP>Status: <?php echo $status_history[$n]['order_status_name']; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2" NOWRAP>User Notified: <?php echo (($status_history[$n]['user_notified'] == '1') ? 'Yes' : 'No'); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2">Comments: </td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2"><?php echo $status_history[$n]['comments']; ?></td>
                    </tr>
                    <tr>
                        <td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
                    </tr>
<?php
            $n++;
        }
?>					
                </table>
<?php
    } elseif($page_action == 'edit') {
?>
        <script language="javascript" data-cfasync="false">
        $(document).ready(function () {
            $("#date_schedualed").datepicker();
        });
        </script>
            <form action="<?php echo FILENAME_ADMIN_ORDERS; ?>?page_action=edit_confirm&oID=<?php echo $oID; ?>&<?php echo tep_get_all_get_params(array('oID', 'action', 'page_action')); ?>" method="post">
            <table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Agent Information</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Agent</td><td class="pageBoxContent"><?php echo tep_draw_agent_pulldown('user_id', $order_data['user_id']); ?> (only works if checkbox is checked)</td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Re-assign this order</td><td class="pageBoxContent"><input type="checkbox" name="reassign" value="1" /></td>
                    </tr>
<?php
        if ($order_data['order_issue'] == '1') {
?>
                        <tr>
                            <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                        </tr>
                        <tr>
                            <td class="pageBoxContent" colspan="2"><?php echo tep_draw_flag($order_data['order_issue']); ?>&nbsp;&nbsp;This order is currently Red Flagged.&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="red_flag_off" value="1" />&nbsp;&nbsp;Turn Red Flag Off</td>
                        </tr>

<?php	
        }
?>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Address Information</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">House Number</td><td class="pageBoxContent"><input type="text" name="house_number" value="<?php echo $order_data['house_number']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Street Name</td><td class="pageBoxContent"><input type="text" name="street_name" value="<?php echo $order_data['street_name']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">City</td><td class="pageBoxContent"><input type="text" name="city" value="<?php echo $order_data['city']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">County</td><td class="pageBoxContent"><?php echo tep_draw_county_pulldown('county_id', '', $order_data['county_id']); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">State</td><td class="pageBoxContent"><?php echo tep_draw_state_pulldown('state_id', $order_data['state_id']); ?></td>
                    </tr>

                    <tr>
                        <td class="pageBoxContent">Zip</td><td class="pageBoxContent"><input type="text" name="zip" value="<?php echo $order_data['zip']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Zip 4</td><td class="pageBoxContent"><input type="text" name="zip4" value="<?php echo $order_data['zip4']; ?>" /> (leave blank to have the system auto fetch)</td>
                    </tr>
                    <?php if ($order_data['order_type_id'] == ORDER_TYPE_INSTALL)  { ?>
                    <tr>
                        <td class="pageBoxContent">Agent Miss Utility Requested</td>
                        <td class="pageBoxContent">
                            <select name="miss_utility_yes_no">
                                <option value="yes"<?php echo ($order_data['miss_utility_yes_no'] == "yes") ? " selected" : ""?>>yes</option>
                                <option value="no"<?php echo ($order_data['miss_utility_yes_no'] == "no") ? " selected" : ""?>>no</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Yard Lamp</td>
                        <td class="pageBoxContent">
                            <select name="lamp_yes_no">
                                <option value="yes"<?php echo ($order_data['lamp_yes_no'] == "yes") ? " selected" : ""?>>yes</option>
                                <option value="no"<?php echo ($order_data['lamp_yes_no'] == "no") ? " selected" : ""?>>no</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Gas Lamp</td>
                        <td class="pageBoxContent">
                            <select name="lamp_use_gas">
                                <option value="yes"<?php echo ($order_data['lamp_use_gas'] == "yes") ? " selected" : ""?>>yes</option>
                                <option value="no"<?php echo ($order_data['lamp_use_gas'] == "no") ? " selected" : ""?>>no</option>
                                <option value="unsure"<?php echo ($order_data['lamp_use_gas'] == "unsure") ? " selected" : ""?>>unsure</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Miss Utility Contacted</td>
                        <td class="pageBoxContent">
                            <select name="contacted">
                                <option value="yes"<?php echo ($order_data['contacted'] == "yes") ? " selected" : ""?>>yes</option>
                                <option value="no"<?php echo ($order_data['contacted'] != "yes") ? " selected" : ""?>>no</option>
                            </select>
                        </td>
                    </tr>
                    <?php } ?>

                    <tr>
                        <td class="pageBoxContent">Cross Street/Directions</td>
                        <td class="pageBoxContent"><textarea name="cross_street_directions"><?php echo $order_data['cross_street_directions']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Job Description</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Order Type</td><td class="pageBoxContent"><?php echo $order_data['order_type_name']; ?></td>
                    </tr>
<?php
        $assigned_installer_id = tep_fetch_true_assigned_installer($oID);
        if ($assigned_installer_id === false) {
            $assigned_installer_id = '';
        }

        $installer_name = '';
        if (empty($assigned_installer_id)) {
            $default_installer = tep_fetch_assigned_order_installer($oID);
            if ($default_installer !== false) {
                $installer_name = tep_fetch_installer_name($default_installer);
            } else {
                $installer_name = 'None';
            }
        }
?>
                    <tr>
                        <td class="pageBoxContent">Assigned Installer</td><td class="pageBoxContent"><?php echo tep_draw_installer_pulldown('assigned_installer', $assigned_installer_id, array(array('id' => '', 'name' => 'Unassigned/Default'))); ?><?php echo ((!empty($installer_name)) ? ' - ' . $installer_name : ''); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Order Status</td><td class="pageBoxContent"><?php echo $order_data['order_status_name']; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Extra Cost</td><td class="pageBoxContent"><input type="text" name="extra_cost" value="<?php echo $order_data['extra_cost']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Extra Cost Reason</td><td class="pageBoxContent"><input type="text" name="extra_cost_description" value="<?php echo $order_data['extra_cost_description']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Job Total</td><td class="pageBoxContent"><input type="text" name="order_total" value="<?php echo $order_data['order_total']; ?>" /><input type="hidden" name="order_total_old" value="<?php echo $order_data['order_total']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Date Added</td><td class="pageBoxContent"><?php echo date("n/d/Y g:i:s a", $order_data['date_added']); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Last Modified</td><td class="pageBoxContent"><?php echo (($order_data['last_modified'] > 0) ? date("n/d/Y g:i:s a", $order_data['last_modified']) : 'Never'); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Agent Scheduled Date</td><td class="pageBoxContent"><input id="date_schedualed" type="text" name="date_schedualed" value="<?php echo (($order_data['date_schedualed'] > 0) ? date("n/d/Y", $order_data['date_schedualed']) : ''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Number of Posts</td><td class="pageBoxContent"><input type="text" name="number_of_posts" value="<?php echo $order_data['number_of_posts']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Special Instructions</td>
                        <td class="pageBoxContent"><textarea name="special_instructions"><?php echo $order_data['special_instructions']; ?></textarea></td>
                    </tr>

                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
					<tr>
                        <td colspan="2" class="pageBoxContent"><b>Signpost Installed</b></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td><?php
                            //display if order type install
                            if($order_data['order_type_id'] == 1){
                                echo '<select name="equipment_id">';
                                echo '<option>Please Select</option>';
                                $query = $database->query("select equipment_id, name as equipment_name from equipment  where equipment_type_id = '1'");
                                while($result = $database->fetch_array($query)) {
                                    echo '<option value="' . $result['equipment_id'] . '">'. $result['equipment_name'] . '</option>';
                                }
                                echo '</select>';
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Equipment</b></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>

<?php

        if ($order_data['order_type_id'] == '1') {
?>
<?php

            $optional = tep_convert_view_equipment_array_to_edit($order_data['optional']);

?>
                    <tr>
                        <td width="100%" colspan="2">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <?php echo tep_generate_available_equipment_string($order_data['order_type_id'], tep_get_service_level_id($order_data['user_id']), $order_data['user_id'], $optional, $order_data['zip4'], $order_data['address_id'], true, false); ?>
                            </table>
                        </td>
                    </tr>
<?php
        } elseif (($order_data['order_type_id'] == '2') && isset($order_data['service_call_reason_id'])) {

            //} else {
?>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Reason and Details</b></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="2">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="main"><b>Reason: </b></td>
                                </tr>
                                <tr>
                                <td class="main"><?php
            if ($order_data['service_call_reason_id'] == '1') {
                echo 'Exchange Rider';
            } elseif ($order_data['service_call_reason_id'] == '2') {
                echo 'Install New Rider or BBox';

                for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
                }
            } elseif ($order_data['service_call_reason_id'] == '3') {
                echo 'Replace/Exchange Agent SignPanel';
                for ($n = 0, $m = count($order_data['equipment']); $n < $m; $n++) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$order_data['equipment'][$n]['name'];
                }
            } elseif ($order_data['service_call_reason_id'] == '4') {
                echo 'Post Leaning/Straighten Post';
                if ($order_data['service_call_detail_id'] == '1') {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weather';
                } elseif ($order_data['service_call_detail_id'] == '2') {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Improper Installation';
                } elseif ($order_data['service_call_detail_id'] == '3') {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Someone moved Post';
                } elseif ($order_data['service_call_detail_id'] == '4') {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other';
                }
            } elseif ($order_data['service_call_reason_id'] == '5') {
                echo 'Move Post';
            } elseif ($order_data['service_call_reason_id'] == '6') {
                echo 'Install equipment forgotten at install';
            } elseif ($order_data['service_call_reason_id'] == '7') {
                echo 'Other';
            }
?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
<?php
        }
?>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Order Comments</b> <i>(These are only viewable for admins and installers)</i></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Admin Comments</td>
                        <td class="pageBoxContent"><textarea name="admin_comments"><?php echo $order_data['admin_comments']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pageBoxContent"><b>Order History</b></td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
<?php
        $status_history = tep_fetch_order_history($oID);
        $status_history_count = count($status_history);
        $n = 0;
        while($n < $status_history_count) {
?>
                    <tr>
                        <td class="pageBoxContent" colspan="2" NOWRAP>Date: <?php echo date("n/d/Y", $status_history[$n]['date_added']); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2" NOWRAP>Status: <?php echo $status_history[$n]['order_status_name']; ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2" NOWRAP>User Notified: <?php echo (($status_history[$n]['user_notified'] == '1') ? 'Yes' : 'No'); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2">Comments: </td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2"><?php echo $status_history[$n]['comments']; ?></td>
                    </tr>
                    <tr>
                        <td height="20"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
                    </tr>
<?php
            $n++;
        }
?>
                    <tr>
                        <td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="main" colspan="2"><b>Add a New Comment</b></td>
                    </tr>
                    <tr>
                        <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2" NOWRAP>Status: <?php echo tep_draw_orders_status_pulldown('new_order_status_id', $order_data['order_status_id']); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2" NOWRAP>Notify User: <?php echo tep_draw_notify_user_pulldown('new_user_notified', '0'); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2">Comments: </td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent" colspan="2"><textarea name="new_comment"></textarea></td>
                    </tr>				
                </table>
<?php
    } else {
        $order_type_id = tep_fill_variable('order_type_id');
        $user_id = tep_fill_variable('user_id');
        $address_id = tep_fill_variable('address_id');
        $house_number = tep_fill_variable('house_number');
        $street_name = tep_fill_variable('street_name');
        $city = tep_fill_variable('city');
        $adc_number = tep_fill_variable('adc_number');
        $zip = tep_fill_variable('zip');
        $zip4_code = tep_fill_variable('zip4_code');
        $state = tep_fill_variable('state');
        $county = tep_fill_variable('county');
        $cross_street_directions = tep_fill_variable('cross_street_directions');

        $miss_utility_yes_no = tep_fill_variable('miss_utility_yes_no', 'post', 'no');
        $lamp_yes_no = tep_fill_variable('lamp_yes_no', 'post', 'no');
        $lamp_use_gas = tep_fill_variable('lamp_use_gas', 'post', 'no');
        $contacted = tep_fill_variable('contacted', 'post', 'no');

        $sc_reason = tep_fill_variable('sc_reason', 'post', '7');
        $sc_reason_4 = tep_fill_variable('sc_reason_4');
        $sc_reason_5 = tep_fill_variable('sc_reason_5');
        $sc_reason_7 = tep_fill_variable('sc_reason_7');
        $install_equipment = tep_fill_variable('install_equipment', 'post', array());
        $remove_equipment = tep_fill_variable('remove_equipment', 'post', array());

        $auto_removal = tep_fill_variable('auto_removal', 'post', '1');

        $payment_method_id = tep_fill_variable('payment_method_id', 'post', tep_get_default_billing_method($user_id));

        $tomorrow=strtotime("+1 day");
        //$job_start_date = tep_fill_variable('job_start_date');
        $jobdate = tep_fill_variable('jobdate', 'post', date('d-M-Y',"$tomorrow"));

        $special_instructions = tep_fill_variable('special_instructions');
        $promo_code = tep_fill_variable('promo_code');
        $number_of_posts = tep_fill_variable('number_of_posts', 'post', '1');
        $optional = tep_fill_variable('optional', 'post', array());
        $optional = parse_equipment_array($optional);

        $extended_cost = tep_fill_variable('extended_cost', 'post', '0.00');
        $extra_cost = tep_fill_variable('extra_cost', 'post', '0.00');
        $extra_cost_description = tep_fill_variable('extra_cost_description');
        if (empty($zip4_code)) {
            if (!empty($house_number) || !empty($street_name)) {
                $zip4_class=new zip4($house_number.' '.$street_name,tep_get_state_name($state), $city, $zip);
                if ($zip4_class->search()) {
                    $zip4_code = $zip4_class->return_zip_code();
                }
            }
        }
?>
            <form action="<?php echo FILENAME_ADMIN_ORDERS; ?>?page_action=add_confirm&<?php echo tep_get_all_get_params(array('oID', 'action', 'page_action')); ?>" method="post">
            <table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
                    <tr>
                        <td class="main" colspan="2">Please fill in the details below to add a new order.  Please note that that the details must be completed in order.</td>
                    </tr>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Order Type</td><td class="pageBoxContent"><?php echo tep_draw_order_type_pulldown('order_type_id', $order_type_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Please Select'))); ?></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Job Start Date:</td><td class="pageBoxContent"><?php echo("<script>DateInput('jobdate', true, 'DD-MON-YYYY','$jobdate');</script>")?><noscript><?php echo tep_draw_month_pulldown('start_month', ((isset($start_month)) ? $start_month : '')); ?>/<?php echo tep_draw_day_pulldown('start_day', ((isset($start_day)) ? $start_day : '')); ?>/<?php echo tep_draw_year_pulldown('start_year', ((isset($start_year)) ? $start_year : '')); ?></noscript></td>
                    </tr>
<?php
        if (!empty($order_type_id)) {
?>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="pageBoxContent">Agent</td><td class="pageBoxContent"><?php echo tep_draw_agent_pulldown('user_id', $user_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Please Select'))); ?></td>
                    </tr>
<?php
            if (!empty($user_id)) {
                $query = $database->query("select coalesce(a.auto_remove_period, " . AUTOMATIC_REMOVAL_TIME . ") as auto_remove_period from users u left join agencys a on (u.agency_id = a.agency_id) where u.user_id = '" . $user_id . "'");
                $result = $database->fetch_array($query);
                if ($result) {
                    $auto_remove_period = $result['auto_remove_period'];
                } else {
                    $auto_remove_period = AUTOMATIC_REMOVAL_TIME;
                }
?>
                        <tr>
                            <td colspan="2" class="pageBoxContent"><b>Address Information</b></td>
                        </tr>
                        <tr>
                            <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                        </tr>
<?php
                if (($order_type_id == '2') || ($order_type_id == '3')) {
                    $county = '';
                    $query = $database->query("select a.address_id, a.house_number, TRIM(a.street_name) as street_name, a.city, a.county_id from " . TABLE_ADDRESSES . " a left join " . TABLE_ORDERS . " o on (a.address_id = o.address_id and o.order_type_id = '3'), " . TABLE_ORDERS . " o2, " . TABLE_ADDRESSES_TO_USERS . " atu where atu.user_id = '" . $user_id. "' and atu.address_id = a.address_id and (o.order_id IS NULL" . (($order_type_id == '2') ? " or o.order_status_id = '1'" : '') . ") and a.address_id = o2.address_id and o2.order_type_id = '1' and o2.order_status_id < '4' order by street_name, a.house_number");
                    $found = false;
                    while($result = $database->fetch_array($query)) {
                        $found = true;
                        $checked = '';
                        if ($result['address_id'] == $address_id) {
                            $checked = 'CHECKED ';
                            $county = $result['county_id'];
                            $name = '<b>'.$result['house_number'].' '.$result['street_name'].', ' .$result['city']. '</b>';
                        } else {
                            $name = $result['house_number'].' '.$result['street_name'].', '.$result['city'];
                        }
?>
                                <tr>
                                    <td class="main">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="address_id" value="<?php echo $result['address_id']; ?>"<?php echo $checked; ?> onchange="this.form.submit();"/></td><td class="main"><?php echo $name; ?></td>
                                </tr>
<?php
                    }
                    if (!$found) {
?>
                            <tr>
                                    <td class="main" colspan="2"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No previous addresses could be found.   Please request an Install to register a new address.</b></td>
                                </tr>
<?php
                    }
                }
                if (($order_type_id == '1') || ($order_type_id == '2')) {
?>
                        <tr>
                                <td class="main">House Number: </td><td><input type="text" name="house_number" value="<?php echo $house_number; ?>" /></td>
                            </tr>
                            <tr>
                                <td class="main">Street Name: </td><td><input type="text" name="street_name" value="<?php echo $street_name; ?>" /></td>
                            </tr>
                            <tr>
                                <td class="main">City: </td><td><input type="text" name="city" value="<?php echo $city; ?>" /></td>
                            </tr>

                            <tr>
                                <td class="main">State: </td><td><?php echo tep_draw_state_pulldown('state', $state, ' onchange="this.form.submit();"'); ?></td>
                            </tr>
                            <tr>
                                <td class="main">Zip Code: </td><td><input type="text" name="zip" value="<?php echo $zip; ?>" onchange="this.form.submit();"/></td>
                            </tr>
                            <tr>
                                <td class="main">County: </td><td><?php echo tep_draw_county_pulldown('county', $state, $county); ?></td>
                            </tr>
                            <tr>
                                <td class="pageBoxContent">ADC</td><td class="pageBoxContent"><input type="text" name="adc_number" value="<?php echo $adc_number; ?>" /></td>
                            </tr>
                            <tr>
                                <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                            </tr>
                            <tr>
                                <td class="main">Zip 4: </td><td><input type="text" name="zip4_code" value="<?php echo $zip4_code; ?>" /></td>
                            </tr>
                            <tr>
                                <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="main">Cross street/Directions:</td>
                                        </tr>
                                        <tr>
                                            <td><textarea name="cross_street_directions" cols="40" rows="8"><?php echo $cross_street_directions; ?></textarea></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
<?php
                }
                if ($order_type_id == '1') {
?>
                            <tr>
                                <td class="pageBoxContent">Agent Miss Utility Requested</td>
                                <td class="pageBoxContent">
                                    <select name="miss_utility_yes_no">
                                        <option value="yes"<?php echo ($miss_utility_yes_no == "yes") ? " selected" : ""?>>yes</option>
                                        <option value="no"<?php echo ($miss_utility_yes_no == "no") ? " selected" : ""?>>no</option>
                                    </select>
                               </td>
                            </tr>
                            <tr>
                                <td class="pageBoxContent">Yard Lamp</td>
                                <td class="pageBoxContent">
                                    <select name="lamp_yes_no">
                                        <option value="yes"<?php echo ($lamp_yes_no == "yes") ? " selected" : ""?>>yes</option>
                                        <option value="no"<?php echo ($lamp_yes_no == "no") ? " selected" : ""?>>no</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="pageBoxContent">Gas Lamp</td>
                                <td class="pageBoxContent">
                                    <select name="lamp_use_gas">
                                        <option value="yes"<?php echo ($lamp_use_gas == "yes") ? " selected" : ""?>>yes</option>
                                        <option value="no"<?php echo ($lamp_use_gas == "no") ? " selected" : ""?>>no</option>
                                        <option value="unsure"<?php echo ($lamp_use_gas == "unsure") ? " selected" : ""?>>unsure</option>
                                    </select>
                                </td>
                            </tr>
                            <input type="hidden" name="contacted" value="no" />
<?php
                }
?>
                            <tr>
                                <td class="main" width="140" height="5"></td><td><input type="submit" value="Update For Zip4 Code" /></td>
                            </tr>
                        <tr>
                            <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="pageBoxContent"><b>Payment Information</b></td>
                        </tr>
                        <tr>
                            <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                        </tr>
                        <tr>
                            <td class="main">Billing Method: </td><td><?php echo tep_draw_billing_method_pulldown_for_user($user_id, 'payment_method_id', $payment_method_id, ' onchange="this.form.submit();"', false); ?></td>
                        </tr>
<?php
                if ($payment_method_id == '1') {
?>
                            <tr>
                                <td width="100%" colspan="2">
                                    <table cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="main" colspan="2"><b>Credit Card Details</b> - <i>Currently Not Functional</i></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Credit Card Type: </td><td><input type="text" name="cc_type" value="<?php //echo $house_number; ?>" placeholder="Credit Card not available"/></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Card Number: </td><td><input type="text" name="cc_number" value="<?php //echo $street_name; ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Expiry: </td><td><input type="text" name="expiry" value="<?php //echo $city; ?>" /></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
<?php
                } 
                //Maybe other status here.
                //Now onto special options.

                $order_type = $order_type_id;
                $agent_id = $user_id;
                $zip4 = "{$zip4_code}";

                if (($order_type == '1')) {
                        //Install.
                ?>
                <tr>
                    <td colspan="2" width="100%">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="main"><b>Optional Extras</b></td>
                            </tr>
                            <tr>
                                <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                            </tr>
                            <?php echo tep_generate_available_equipment_string($order_type, tep_get_service_level_id($agent_id), $agent_id, $optional, $zip4, ''); ?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="5"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
                </tr>
                <?php
                    } elseif ($order_type == '2') {
                        //Service call.  Need to make some more complex questions here.
                        
                        ?>
                        <tr>
                            <td colspan="2" width="100%">
                                <table cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="main"><b>Reason and Details</b></td>
                                    </tr>
                                    <tr>
                                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                                    </tr>
                                    <tr>
                                        <td width="100%">
                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
                                                    <td width="100%"></td>
                                                </tr>
                                                <tr>
                                                    <td class="main">Reason: </td>
                                                    <td width="100%"><?php echo tep_generate_service_call_pulldown_menu('sc_reason', $sc_reason, $agent_id, tep_fill_variable('address_id'), ' onchange="this.form.submit();"'); ?></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <?php
                                        if (!empty($sc_reason)) {
                                            if ($sc_reason == '1') {
                                                //Rider Exchange.
                                                //First need to list all the installed.
                                                ?>
                                                <tr>
                                                    <td width="100%">
                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
                                                                <td width="100%"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="main" valign="top">Rider to Remove: </td>
                                                                <td width="100%"><?php
                                                                        $query = $database->query("select eita.equipment_id, e.name as equipment_name from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_EQUIPMENT . " e where eita.equipment_id = e.equipment_id and eita.equipment_status_id = '2' and e.equipment_type_id = '2' and eita.address_id = '" . tep_fill_variable('address_id') . "'");
                                                                        $loop = 0;
                                                                        $exclude_array = array();
                                                                            while($result = $database->fetch_array($query)) {
                                                                                    if ($loop > 0) {
                                                                                        echo '<br>';
                                                                                    }
                                                                                $exclude_array[] = $result['equipment_id'];
                                                                                    if (in_array($result['equipment_id'], $remove_equipment)) {
                                                                                        $checked = ' CHECKED';
                                                                                    } else {
                                                                                        $checked = '';
                                                                                    }
                                                                                echo '<input class="remove-equipment" type="checkbox" name="remove_equipment[]" value="'.$result['equipment_id'].'"'.$checked.'>&nbsp;&nbsp;&nbsp;'.$result['equipment_name'];
                                                                                $loop++;
                                                                            }
                                                                ?></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                                                </tr>
                                                <tr>
                                                    <td width="100%">
                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
                                                                <td width="100%"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="main" valign="top">Rider to Install: </td>
                                                                <td width="100%"><?php
                                                                        $warehouses = tep_get_sevicing_warehouse(fetch_address_zip4(tep_fill_variable('address_id')));
                                                                        $query = $database->query("select e.equipment_id, e.name as equipment_name from " . TABLE_EQUIPMENT . " e where e.equipment_type_id = '2'");
                                                                        $loop = 0;
                                                                            while($result = $database->fetch_array($query)) {
                                                                                    if (tep_fetch_available_equipment_count($result['equipment_id'], $warehouses, $agent_id) < 1) {
                                                                                        continue;
                                                                                    }
                                                                                    if (in_array($result['equipment_id'], $exclude_array)) {
                                                                                        continue;
                                                                                    }
                                                                                    if ($loop > 0) {
                                                                                        echo '<br>';
                                                                                    }
                                                                                    if (in_array($result['equipment_id'], $install_equipment)) {
                                                                                        $checked = ' CHECKED';
                                                                                    } else {
                                                                                        $checked = '';
                                                                                    }
                                                                                echo '<input type="checkbox" class="install-equipment" name="install_equipment[]" value="'.$result['equipment_id'].'"'.$checked.'>&nbsp;&nbsp;&nbsp;'.$result['equipment_name'];
                                                                                $loop++;
                                                                            }
                                                                ?></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                
                                                
                                                <?php
                                            } elseif ($sc_reason == '2') {
                                                //Install new bb or rider.
                                                $warehouses = tep_get_sevicing_warehouse(fetch_address_zip4(tep_fill_variable('address_id')));
                                                $query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " where equipment_type_id = '2' or equipment_type_id = '3' order by equipment_type_name");
                                                $loop = 0;
                                                    while($result = $database->fetch_array($query)) {
                                                        $string = '';
                                                        $exclude_array = tep_fetch_installed_equipment_array($result['equipment_type_id'], tep_fill_variable('address_id'));
                                                        $sub_query = $database->query("select equipment_id, name from " . TABLE_EQUIPMENT . " where equipment_type_id = '" . $result['equipment_type_id'] . "'");
                                                            while($sub_result = $database->fetch_array($sub_query)) {
                                                                    if (tep_fetch_available_equipment_count($sub_result['equipment_id'], $warehouses, $agent_id) < 1) {
                                                                        continue;
                                                                    }
                                                                    if (in_array($sub_result['equipment_id'], $exclude_array)) {
                                                                        continue;
                                                                    }
                                                                    if (in_array($sub_result['equipment_id'], $equipment)) {
                                                                        $checked = ' CHECKED';
                                                                    } else {
                                                                        $checked = '';
                                                                    }
                                                                    if (!empty($string)) {
                                                                        $string .= '<br>';
                                                                    }
                                                                $string .= '<input name="equipment[]" type="checkbox"'.$checked.' value="'.$sub_result['equipment_id'].'">'.$sub_result['name'];
                                                            }
                                                            if (!empty($string)) {
                                                                if ($loop > 0) {
                                                                    ?>
                                                                    <tr>
                                                                        <td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td class="main"><b><?php echo $result['equipment_type_name']; ?></b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="100%">
                                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                                            <tr>
                                                                                <td width="20"><img src="images/pixel_trans.gif" height="1" width="20" /></td>
                                                                                <td width="100%"><?php echo $string; ?></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                $loop++;
                                                            }
                                                    
                                                    }
        
                                            } elseif ($sc_reason == '3') {
                                                //Replace/Exchange Agent SignPanel
                                                $warehouses = tep_get_sevicing_warehouse(fetch_address_zip4(tep_fill_variable('address_id')));
                                                $query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " where equipment_type_id = '4' order by equipment_type_name");
                                                $loop = 0;
                                                    while($result = $database->fetch_array($query)) {
                                                        $string = '';
                                                        $exclude_array = tep_fetch_installed_equipment_array($result['equipment_type_id'], tep_fill_variable('address_id'));
                                                        $sub_query = $database->query("select equipment_id, name from " . TABLE_EQUIPMENT . " where equipment_type_id = '" . $result['equipment_type_id'] . "'");
                                                            while($sub_result = $database->fetch_array($sub_query)) {
                                                                    if (tep_fetch_available_equipment_count($sub_result['equipment_id'], $warehouses, $agent_id) < 1) {
                                                                        continue;
                                                                    }
                                                                    if (in_array($sub_result['equipment_id'], $exclude_array)) {
                                                                        continue;
                                                                    }
                                                                    if (in_array($sub_result['equipment_id'], $equipment)) {
                                                                        $checked = ' CHECKED';
                                                                    } else {
                                                                        $checked = '';
                                                                    }
                                                                    if (!empty($string)) {
                                                                        $string .= '<br>';
                                                                    }
                                                                $string .= '<input name="equipment[]" type="checkbox"'.$checked.' value="'.$sub_result['equipment_id'].'">'.$sub_result['name'];
                                                            }
                                                            if (!empty($string)) {
                                                                if ($loop > 0) {
                                                                    ?>
                                                                    <tr>
                                                                        <td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td class="main"><b><?php echo $result['equipment_type_name']; ?></b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="100%">
                                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                                            <tr>
                                                                                <td width="20"><img src="images/pixel_trans.gif" height="1" width="20" /></td>
                                                                                <td width="100%"><?php echo $string; ?></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                $loop++;
                                                            }
                                                        
                                                    }
                                            
                                            } elseif ($sc_reason == '4') {
                                                //Post Leaning/Straighten Post
                                                ?>
                                                <tr>
                                                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                                                </tr>
                                                <tr>
                                                    <td width="100%">
                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
                                                                <td width="100%"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="main">Leaning due to: </td>
                                                                <td width="100%"><?php echo tep_generate_post_leaning_reason_pulldown_menu('sc_reason_4', $sc_reason_4); ?></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <?php
                                            } elseif ($sc_reason == '5') {
                                                //Move Post
                                                ?>
                                                <input type="hidden" name="sc_reason_5" value="true" />
                                                <tr>
                                                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                                                </tr>
                                                <tr>
                                                    <td class="main"><i>Please make sure to properly mark where the new post is to go or provide exact details below.</i></td>
                                                </tr>
                                                <?php
                                            } elseif ($sc_reason == '6') {
                                            //Forgotten Equipment.
                                                ?>
                                                <tr>
                                                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                                                </tr>
                                                <tr>
                                                    <td class="main">Please select the forgotten equipment from the list below:</td>
                                                </tr>
                                                <tr>
                                                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                                                </tr>
                                                <?php echo tep_generate_available_equipment_string('1', tep_get_service_level_id($agent_id), $agent_id, $optional, fetch_address_zip4(tep_fill_variable('address_id')), tep_fill_variable('address_id'), false, true, array(), false); ?>

                                                <?php
                                            } elseif ($sc_reason == '7') {
                                            //Other
                                                ?>
                                                <input type="hidden" name="sc_reason_7" value="true" />
                                                <tr>
                                                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                                                </tr>
                                                <tr>
                                                    <td class="main"><i>Please describe the issue fully below.</i></td>
                                                </tr>
                                                <?php
                                            }
                                            
                                        }
                                    ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="5"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
                        </tr>
                        <?php
                    } elseif ($order_type == '3') {
                    //Removal.
                    }

                 if ($order_type_id == 1) {
?>
                <tr>
                    <td class="main" colspan="2">Number of posts requested: <input type="text" name="number_of_posts" value="<?php echo $number_of_posts; ?>" /></td>
                </tr>
<?php
                }
?>
                <tr>
                    <td width="100%" colspan="2">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="main">Please provide any special directions or requirements for this order in the box below.</td>
                            </tr>
                            <tr>
                                <td class="main"><textarea  cols="40" rows="8" name="special_instructions"><?php echo $special_instructions; ?></textarea></td>
                            </tr>
                        </table>
                    </td>
                </tr>
<?php
                if ($order_type_id == '1') {
?>
                <tr>
                    <td class="main" colspan="2">Schedule Removal: <input type="radio" name="auto_removal" value="1" <?php echo (($auto_removal == '1') ? 'CHECKED ': ''); ?> />&nbsp;Yes&nbsp;&nbsp;<input type="radio" name="auto_removal" value="0" <?php echo (($auto_removal == '0') ? 'CHECKED ': ''); ?> />&nbsp;No <i>(This will make a new removal for this agent for <?php echo $auto_remove_period; ?> days)</i></td>
                    <input type="hidden" name="auto_remove_period" value="<?php echo $auto_remove_period; ?>" />
                </tr>
<?php
                } else {

?>
                <input type="hidden" name="auto_removal" value="0" />
<?php
                }
?>
                <tr>
                    <td class="main" colspan="2">Promotional Code: <input type="text" name="promo_code" value="<?php echo $promo_code; ?>" /></td>
                </tr>
                <tr>
                    <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                </tr>
                <tr>
                    <td class="mainLarge" colspan="2">Order Totals</td>
                </tr>
                <tr>
                    <td class="main" width="140" height="5"></td><td><input type="submit" value="Update Cost" /></td>
                </tr>
                <tr>
                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
                </tr>
<?php

                $order = new orders('', '', array('address_id' => $address_id, 'user_id' => $user_id, 'order_type_id' => $order_type_id, 'promo_code' => $promo_code, 'optional' => $optional, 'county' => $county, 'zip4' => $zip4, 'payment_method' => $payment_method_id, 'billing_method_id' => $payment_method_id, 'extra_cost' => $extra_cost, 'sc_reason' => $sc_reason, 'install_equipment' => $install_equipment, 'number_of_posts' => $number_of_posts, 'equipment' => array()), $user_id);
                $extended_cost = $order->extended_cost;

                $total = $order->fetch_order_total($zip4_code);
?>
                <tr>
                    <td width="100%" colspan="2">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="main" width="140">Base Cost: </td><td class="main">$<?php $base = $order->base_cost; echo number_format($base, 2); ?></td>
                            </tr>
<?php
                $ext = $order->extended_cost;
                if ($ext > 0) {
?>
                            <tr>
                                <td class="main" width="140">Extended Cost: </td><td class="main">$<?php echo number_format($ext, 2); ?></td>
                            </tr>
<?php
                }
                $eqt = $order->equipment_cost;
                if ($eqt > 0) {
?>
                            <tr>
                                <td class="main" width="140">Equipment Cost: </td><td class="main">$<?php echo number_format($eqt, 2); ?></td>
                            </tr>
<?php
                }
                if ($order->deposit_cost > 0) {
?>
                            <tr>
                                <td class="main" width="140">Deposit Cost: </td><td class="main">$<?php echo number_format($order->deposit_cost, 2); ?></td>
                            </tr>
<?php
                }
                if ($order->discount_cost != 0) {
?>
                            <tr>
                                <td class="main" width="140">Adjustment: </td><td class="main">$<?php echo number_format($order->discount_cost, 2); ?></td>
                            </tr>
<?php
                }
?>
                            <tr>
                                <td class="main" width="140">Extra Cost: </td><td class="main">$<input type="text" size="5" name="extra_cost" value="<?php echo $extra_cost; ?>" /></td>
                            </tr>
                            <tr>
                                <td class="main" width="140">Extra Cost Reason: </td><td class="main"><input type="text" name="extra_cost_description" value="<?php echo $extra_cost_description; ?>" /></td>
                            </tr>
<?php
                if ($order->credit != 0) {
?>
                            <tr>
                                <td class="main" width="140">Credit: </td><td class="main">$<?php echo number_format($order->credit, 2); ?></td>
                            </tr>
<?php
                }
?>
                            <tr>
                                <td class="main" width="140" height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                            </tr>
                            <tr>
                                <td class="main" width="140"><b>Total Cost: </b></td><td class="main">$<?php echo number_format($total, 2); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
<?php
            } 
        }
    }
?>
                    <tr>
                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                    </tr>		
                </table>
<?php
}
?>
        </td>
        <td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
        <td width="250" valign="top">
<?php
if ($page_action == 'view') {
?>
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
                            <td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_ORDERS.'?'.tep_get_all_get_params(array('oID', 'action', 'page_action'), array("free_inventory" => 1)); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?><?php
                            if ($order_data['order_type_id'] == 3 && $order_data['order_status_id'] == 3) {
                                echo "&nbsp;&nbsp;&nbsp;";
                                echo "<input name='address_id' type='hidden' value='{$order_data['address_id']}'>\n";
                                echo tep_create_button_submit('free_inventory', 'Free Inventory', ' name="free_inventory"');
                            }
?></form></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
<?php
} elseif ($page_action == 'edit')  {
?>
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="100%">
                        <table width="250" cellspacing="0" celpadding="0" class="pageBox">
                            <tr>
                                <td class="pageBoxContent">Press cancel to go back to the previous page or press update to confirm changes.</td>
                            </tr>
                            <tr>
                                <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
                            </tr>
                            <tr>
                                <td width="100%" align="right">
                                    <table cellspacing="0" cellpadding="0" width="100%">
                                        <tr>
                                            <td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="button_action"'); ?><!--<input type="submit" value="Update" />--></form></td>
                                            <td align="right"><form action="<?php echo FILENAME_ADMIN_ORDERS.'?'.tep_get_all_get_params(array('oID', 'action', 'page_action')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
<?php
} elseif ($page_action == 'add')  {
?>
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="100%">
                        <table width="250" cellspacing="0" celpadding="0" class="pageBox">
                            <tr>
                                <td class="pageBoxContent">Press cancel to go back to the previous page or press update to Insert the Order.</td>
                            </tr>
                            <tr>
                                <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
                            </tr>
                            <tr>
                                <td width="100%" align="right">
                                    <table width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="left"><?php echo tep_create_button_submit('insert_order', 'Insert Order', ' name="button_action"'); ?></form></td>
                                            <td align="right"><form action="<?php echo FILENAME_ADMIN_ORDERS.'?'.tep_get_all_get_params(array('oID', 'action', 'page_action')); ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
<?php
} else {
?>
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="100%">
                        <table width="250" cellspacing="0" celpadding="0" class="pageBox">
                            <tr>
                                <td class="pageBoxContent">Click on an order to either edit or view.  To create a new order press the "Create" button below.</td>
                            </tr>
                            <tr>
                                <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
                            </tr>
                            <form action="<?php echo FILENAME_ADMIN_ORDERS; ?>" method="get">
                            <tr>
                                <td width="100%">
                                    <table width="100%" cellspacing="2" cellpadding="2">
                                        <tr>
                                            <td width="100"><img src="images/pixel_trans.gif" height="1" width="100" /></td>
                                            <td width="100%"></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Status:</td><td class="main"><?php echo tep_draw_orders_status_pulldown('order_status', $order_status, array(array('id' => '', 'name' => 'Any')), ' onchange="this.form.submit();"'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Order Type:</td><td class="main"><?php echo tep_draw_order_type_pulldown('order_type', $order_type, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any'))); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="main">House #: </td>
                                            <td class="main"><input type="text" name="show_house_number" value="<?php echo $show_house_number; ?>" /><br /><em>Use % as wildcard</em></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Street Name: </td>
                                            <td class="main"><input type="text" name="show_street_name" value="<?php echo $show_street_name; ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td class="main">City: </td>
                                            <td class="main"><input type="text" name="show_city" value="<?php echo $show_city; ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Zip: </td>
                                            <td class="main"><input type="text" name="show_zip" value="<?php echo $show_zip; ?>" /></td>
                                        </tr>

                                        <tr>
                                            <td class="main">Order ID: </td>
                                            <td class="main"><input type="text" name="show_order_id" value="" /></td>
                                        </tr>
                                        <tr>
                                            <td class="main">List Order: </td>
                                            <td class="main"><?php echo tep_generate_pulldown_menu('list_method', array(array('id' => '2', 'name' => 'Newest to Oldest'), array('id' => '1', 'name' => 'Oldest to Newest')), $list_method, ' onchange="this.form.submit();"'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Address ID: </td>
                                            <td class="main"><input type="text" name="show_address_id" value="<?php echo $show_address_id; ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td class="main">County: </td>
                                            <td class="main"><?php echo tep_draw_county_pulldown('show_county_id', null, $show_county, array(array('id' => '', 'name' => 'Any'),array('id'=>'0','name'=>'None')), ' onchange="this.form.submit();"', true); ?></td>
                                        </tr>
                                        <tr>
                                            <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2">Agent:</td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2"><?php echo tep_draw_agent_pulldown('agent_id', $agent_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any')), '', true); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2">Agency:</td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2"><?php echo tep_draw_agency_pulldown('agency_id', $agency_id, ' onchange="this.form.submit();"', array(array('id' => '', 'name' => 'Any'))); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2">Service Level:</td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2"><?php echo tep_draw_service_level_pulldown('service_level_id', $service_level_id, ' onchange="this.form.submit();"', false, array(array('id' => '', 'name' => 'Any')), false); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2">Placement Type:</td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2"><?php echo tep_generate_order_placement_type_pulldown_menu('inserted_order_type_id', $inserted_order_type_id, ' onchange="this.form.submit();"'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Installer:</td><td class="main"><?php echo tep_draw_installer_pulldown('installer_id', $installer_id, array(array('id' => '', 'name' => 'Any'), array('id' => 'unassigned', 'name' => 'Unassigned')), ' onchange="this.form.submit();"'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Red Flagged:</td><td class="main"><input type="checkbox" name="red_flagged" value="1"<?php echo (($red_flagged == '1') ? ' CHECKED' : ''); ?> /></td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2">Miss Utility Calls to be Placed: <input type="checkbox" name="miss_utility_to_be_placed" value="1"<?php echo (($miss_utility_to_be_placed == '1') ? ' CHECKED' : ''); ?> /></td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2">Miss Utility Calls Placed: <input type="checkbox" name="miss_utility_placed" value="1"<?php echo (($miss_utility_placed == '1') ? ' CHECKED' : ''); ?> /></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Include Removed:</td><td class="main"><input type="radio" name="active" value="1"<?php echo (($active == '1') ? ' CHECKED' : ''); ?> /> Yes <input type="radio" name="active" value="0"<?php echo (($active == '0') ? ' CHECKED' : ''); ?> /> No</td>
                                        </tr>



                                        <tr>
                                            <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Show When: </td>
                                            <td class="main"><select name="show_between_type"><option value=""<?php echo (($show_between_type == '') ? ' SELECTED' : ''); ?>>Any</option><option value="ordered"<?php echo (($show_between_type == 'ordered') ? ' SELECTED' : ''); ?>>Ordered</option><option value="scheduled"<?php echo (($show_between_type == 'scheduled') ? ' SELECTED' : ''); ?>>Scheduled</option><option value="accepted"<?php echo (($show_between_type == 'accepted') ? ' SELECTED' : ''); ?>>Accepted</option><option value="completed"<?php echo (($show_between_type == 'completed') ? ' SELECTED' : ''); ?>>Completed</option></select></td>
                                        </tr>
                                        <tr>
                                            <td class="main" colspan="2">Between: <input type="text" name="show_between_start" value="<?php echo $show_between_start; ?>" size="7" /> and <input type="text" name="show_between_end" value="<?php echo $show_between_end; ?>" size="7" /></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><i>(mm/dd/YY)</i></td>
                                        </tr>

                                        <tr>
                                            <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                                        </tr>
                                        <tr>
                                            <td class="main">Number of Posts: </td>
                                            <td class="main"><input type="text" name="show_number_of_posts" value="<?php echo $show_number_of_posts; ?>" /></td>
                                        </tr>
<?php
    $query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_name");
    while($result = $database->fetch_array($query)) {
?>
                                                        <tr>
                                                            <td class="main"><?php echo $result['equipment_type_name']; ?>: </td>
                                                            <td class="main"><?php echo tep_draw_equipment_pulldown('show_equipment['.$result['equipment_type_id'].']', ((isset($show_equipment[$result['equipment_type_id']])) ? $show_equipment[$result['equipment_type_id']] : ''), '', '', array(array('id' => '', 'name' => 'Any')), $result['equipment_type_id']); ?></td>
                                                        </tr>
<?php
    }
?>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
                            </tr>
                            <tr>
                                <td width="100%" align="right"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?></td>
                            </tr>
                            </form>
                            <tr>
                                <td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
                            </tr>
                            <tr>
                                <td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_ORDERS; ?>?page_action=add&order_status=<?php echo $order_status; ?>&order_type=<?php echo $order_type; ?>&installer_id=<?php echo $installer_id; ?>&page=<?php echo $page; ?>&agent_id=<?php echo $agent_id; ?>&agency_id=<?php echo $agency_id; ?>&show_house_number=<?php echo $show_house_number; ?>&show_street_name=<?php echo $show_street_name; ?>&show_city=<?php echo $show_city; ?>&service_level_id=<?php echo $service_level_id; ?>&show_zip=<?php echo $show_zip; ?>&show_between_type=<?php echo $show_between_type; ?>&show_between_start=<?php echo urlencode($show_between_start); ?>&show_between_end=<?php echo urlencode($show_between_end); ?>" method="post"><?php echo tep_create_button_submit('create', 'Create'); ?><!--<input type="submit" value="Create" />--></form></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
<?php
}
?>
                </td>
    </tr>
</table>
        </td>
    </tr>
</table>
