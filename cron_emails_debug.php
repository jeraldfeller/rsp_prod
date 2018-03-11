<?php
	// Cron job that should run every day, get number of active orders/customers, divide and send appropriate number of emails per day

	//start customisable values 
	$start_date = 2; //eg 5th
	$end_date = 12; //eg 25th
	$live = true; //if we're going live
	$limit = 0; //if we're limiting no of records (set to 0 if no limit)
	$live_email = 'realtysp@yahoo.com'; //live email (admin)
	//$live_email = 'library@cybril.com'; //live email (admin)
	$mailbox_user = 'realtysi'; //username for mailbox (on server)
	$mailbox_pass = 'EF78&*gh'; //password for mailbox
	$mailbox_pop3 = 'mail.realtysignpost.com'; //pop3 for mailbox
	//end customisable values

    // Conditionally setup test environment
    if ($live) {
        $inventory_server = "realtysignpost.com";
    } else {
        putenv("SERVER_MODE=TEST");
        $inventory_server = "testdnx.net";
	    $live_email = 'john.pelster@gmail.com'; //dev email
    }

    // Inventory Variables 
    $warehouses = array("Fairfax Warehouse", "MD Warehouse");
    $available  = "Available";
    $pending    = "Pending Install";
    $installed  = "Installed";

	include('includes/application_top.php');
	include("includes/classes/mailbox.php");

	set_time_limit(120);

	$division = $end_date - $start_date; 
	
    $date_today = date('j');
    $date_pretty = date('F j, Y');

    $user_id = array();

	if ($date_today == 1) { //get all active orders/customers

		$truncate = $database->query('TRUNCATE TABLE ' . TABLE_EMAILS_DUE); //empty table first
		
		$sql = "SELECT DISTINCT o.user_id FROM " . TABLE_ORDERS . " as o, " . TABLE_USERS . " as u 
		WHERE o.user_id = u.user_id 
		AND o.address_id NOT IN (SELECT address_id FROM " . TABLE_ORDERS . " 
		WHERE (order_type_id = 3 AND order_status_id = 3) 
		OR (order_type_id = 3 AND order_status_id = 4)
		OR (order_type_id = 1 AND order_status_id = 4) 
		OR (order_type_id = 1 AND order_status_id = 5))";
		
		$query = $database->query($sql);
		$rows = $database->num_rows($query);

		if ($rows > 0) {
		
			$mod = $rows%$division;
			
			if ($mod != '') {
				$per_day = ($rows-$mod)/$division;
			} else {
				$per_day = $rows/$division;
			}
			
			$x=1;
			foreach($database->fetch_array($query) as $result){
			
				$user_id[$x] = $result['user_id'];
				$x++;
				
			} //end while
			
			$z=$start_date;
			$p=1;
			
			//store this data (and per_day variable) somewhere so we can just grab appropriate customer numbers (and then their orders) on the specific day
			for ($y=1;$y<=$rows;$y++) {
		
				$sql = "INSERT INTO " . TABLE_EMAILS_DUE . " (date, user_id) VALUES ($z, $user_id[$y])";
				$query = $database->query($sql);
				
				if (($z != $end_date) && ($p == $per_day)) {
				
					$z++;
					$p=1;
					
				} else {
				
					$p++;
					
				}
											
			}
			
		} //end if rows

		//send email to Ryan
		$msg = "Total number of customers with active install orders: " . $rows;
		$msg .= "\nEmails will be spread over $division days from $start_date to $end_date: " . $per_day;
		if ($mod != '') $msg .= "\nWith a balance of $mod which will be sent on $end_date.";	

		$subject = 'Inventory Email Stats';
		#mail($live_email, $subject, $msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());

	} else if (($date_today >= $start_date) && ($date_today <= $end_date)) {
	
		//get the records (user id's) for the specific day and get their order/s details
		$sql = "SELECT * FROM " . TABLE_EMAILS_DUE . " WHERE date = '3' ORDER BY user_id ";
		if ($limit != '') $sql .= " LIMIT $limit";
		$query = $database->query($sql);
		$emails_sent = 0;
		$message = array();
		$firstname = array();
		$lastname = array();
        $name = array();
        $user_email = array();
        $inv_message = array();
        $opt_out = array();
		$x=0;
		
		if ($database->num_rows($query) > 0) {

			if ($live == false) echo "<table border=1><tr><td>No Users:</td><td>" . $database->num_rows($query) . "</td></tr>";
			foreach($database->fetch_array($query) as $result){
                $x = $result['user_id'];
				$user_id[$x] = $x;

                $order_sql = "SELECT * FROM " . TABLE_USERS . " AS e 
                JOIN " . TABLE_USERS_DESCRIPTION . " AS u ON (e.user_id = u.user_id) 
                JOIN " . TABLE_ORDERS . " AS o ON (e.user_id = o.user_id) 
                JOIN " . TABLE_ADDRESSES . " AS a ON (o.address_id = a.address_id)
                LEFT JOIN " . TABLE_INVENTORY_OPT_OUT . " AS ioo ON (e.user_id = ioo.user_id)
				WHERE e.user_id = " . $result['user_id'] . " 
				AND o.order_type_id = 1 
				AND o.address_id NOT IN (SELECT address_id FROM " . TABLE_ORDERS . " 
				WHERE (order_type_id = 3 AND order_status_id = 3) 
				OR (order_type_id = 3 AND order_status_id = 4)
				OR (order_type_id = 1 AND order_status_id = 4) 
				OR (order_type_id = 1 AND order_status_id = 5))
				ORDER BY o.order_id ASC";
				
				$order_query = $database->query($order_sql);

				if ($database->num_rows($order_query) > 0) {
					$message[$x] = "<table width=\"100%\">\n";
					
					if ($live == false) echo "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>\n";
					if ($live == false) echo "<tr><td width='30%'>User ID: </td><td width='70%'>".$result['user_id']."</td></tr>\n";
					if ($live == false) echo "<tr><td width='30%'>No Orders: </td><td width='70%'>".$database->num_rows($order_query)."</td></tr>\n";
					foreach($database->fetch_array($order_query) as $order_result){
						if ($live == true) {
							$user_email[$x] = $order_result['email_address'];
						} else {
							$user_email[$x] = $live_email; 
						}
						
						$firstname[$x] = $order_result['firstname'];
						$lastname[$x] = $order_result['lastname'];
                        $name[$x] = $firstname[$x] . " " . $lastname[$x];
                        $opt_out[$x] = $order_result['email_opt_out'];
						
						$message[$x] .= "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>\n";
						$message[$x] .= "<tr><td width='30%'>Order ID: </td><td width='70%'>" . $order_result['order_id'] . "</td></tr>\n";
						$message[$x] .= "<tr><td width='30%'>Address: </td><td width='70%'>" . $order_result['house_number'] . " " . $order_result['street_name'] . " " . $order_result['city'] . " " . $order_result['zip'] . "</td></tr>\n";

						if ($live == false) echo "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>\n";
						if ($live == false) echo "<tr><td width='30%'>Order ID: </td><td width='70%'>" . $order_result['order_id'] . "</td></tr>\n";
						if ($live == false) echo "<tr><td width='30%'>Order Status: </td><td width='70%'>" . $order_result['order_status_id'] . "</td></tr>\n";
						if ($live == false) echo "<tr><td width='30%'>Address: </td><td width='70%'>" . $order_result['house_number'] . " " . $order_result['street_name'] . " " . $order_result['city'] . " " . $order_result['zip'] . "</td></tr>\n";
	
						if ($order_result['date_completed'] != '0') {
							$message[$x] .= "<tr><td width='30%'>Date Installed: </td><td width='70%'>" . date("F j, Y",$order_result['date_completed']) . "</td></tr>\n"; 
							if ($live == false) echo "<tr><td width='30%'>Date Installed: </td><td width='70%'>" . date("F j, Y",$order_result['date_completed']) . "</td></tr>\n"; 
						} else {
							$message[$x] .= "<tr><td width='30%'>Date Added: </td><td width='70%'>" . date("F j, Y",$order_result['date_added']) . "</td></tr>\n"; 
							if ($live == false) echo "<tr><td width='30%'>Date Added: </td><td width='70%'>" . date("F j, Y",$order_result['date_added']) . "</td></tr>\n"; 
						}
						
						$message[$x] .= "<tr><td width='30%'>Date for Removal: </td><td width='70%'>\n";
						if ($live == false) echo "</td></tr><tr><td width='30%'>Date for Removal: </td><td width='70%'>\n";
						
						$remove_sql = "SELECT * FROM " . TABLE_ORDERS . " WHERE address_id = ". $order_result['address_id'] . " AND order_type_id = 3";
						$remove_query = $database->query($remove_sql);
						$remove_num = $database->num_rows($remove_query);
						
						if ($remove_num > 0) {
							$remove_result = $database->fetch_array($remove_query);
							if ($remove_result['date_schedualed'] != '0') {
								$message[$x] .= date("F j, Y",$remove_result['date_schedualed']); 
								if ($live == false) echo date("F j, Y", $remove_result['date_schedualed']); 
							} else {
								$message[$x] .= "None Scheduled";
								if ($live == false) echo "None Scheduled";
							}
						} else {
							$message[$x] .= "None Scheduled";
							if ($live == false) echo "None Scheduled";
						}
	
                        $message[$x] .=  "</td></tr>\n";

						if ($live == false) echo  "</td></tr>\n";
						if ($live == false) echo "<tr><td width='30%'>User Name: </td><td width='70%'>{$name[$x]}</td></tr>\n";

					} //end while orders
				
					$message[$x] .= '</table>';
					$emails_sent++;
					
				} else { //no orders (active) for this user
				
					if ($live == false) echo "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>\n";
					if ($live == false) echo "<tr><td width='30%'>User ID: </td><td width='70%'>".$result['user_id']."</td></tr>\n";
                    if ($live == false) echo "<tr><td width='30%'>No Orders: </td><td width='70%'>0</td></tr>\n";

					$message[$x] = '';
					
                } //end if

                //
                // Inventory Section
                //
                
                $equip_sql = "SELECT DISTINCT ei.equipment_id FROM " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_USERS . " u " . 
                             "WHERE u.user_id = '{$result['user_id']}' AND (ei.user_id = '{$result['user_id']}' OR (ei.user_id = '0' AND ei.agency_id = u.agency_id))";
                $equip_query = $database->query($equip_sql);
                $eqs = "";
                $ecount = 0;
                foreach($database->fetch_array($equip_query) as $eres){
                    if ($ecount > 0) {
                        $eqs .= "&";
                    } else {
                        $eqs .= "?";
                    }
                    $ecount++;
                    $eqs .= "equipment_id[]=" . $eres['equipment_id'];
                }
                $inventory_url = "http://{$inventory_server}/lib/inventory/inventory_json.php5{$eqs}";
                $inv_message[$x] = "<table width=\"100%\">\n";
                if ($ecount) {
                    // Pull the inventory JSON from the API
                    $contents = file_get_contents($inventory_url);
                    $inventory = json_decode($contents);
                    if (is_object($inventory) && property_exists($inventory, "equipment")) {
                        $equipment = $inventory->equipment;
                        foreach ($equipment as $equip) {
                            $whs = $equip->warehouses;
                            $equip_name = $equip->name;
                            $avail = 0;
                            $active = 0;
                            foreach ($warehouses as $warehouse) {
                                if (is_object($whs) && property_exists($whs, $warehouse)) {
                                    $wh = $whs->$warehouse;
                                    if (property_exists($wh, $available)) {
                                        $avail += $wh->$available;
                                        $active += $wh->$available;
                                    } 
                                    if (property_exists($wh, $installed)) {
                                        $active += $wh->$installed;
                                    } 
                                    if (property_exists($wh, $pending)) {
                                        $active += $wh->$pending;
                                    }
                                }
                            }
                            $inv_message[$x] .= "<tr><td width=\"50%\">{$equip_name}:</td><td width=\"50%\">{$avail} of {$active} Available</td></tr>\n";
                        }
                    }
                } else {
                    $inv_message[$x] .= "<tr><td>You have no equipment stored in our warehouse.</td></tr>\n";
                }
                $inv_message[$x] .= "</table>\n";

                //
                // End Inventory Section
                //

			} //end while users/emails 

			if ($live == false) echo "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>\n";
			if ($live == false) echo "<tr><td width='30%'>No Emails Sent:</td><td width='70%'>$emails_sent</td></tr>\n";
			if ($live == false) echo "</table>\n";
			
		} //end if users/emails

        //send emails
        foreach($user_id as $y => $uid) {
			if ($message[$y] != '') {
			
				$subject = date('F') . " Signpanel Inventory & Active Signpost Summary from Realty SignPost";

                $new_message = "<html>
                    <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
					<tbody>
						<tr>
                            <td>Greetings from Realty SignPost. Below is a list of addresses where you currently have a signpost installed. 
                            This list includes the date the signpost was installed, and the scheduled removal date. We are providing this 
                            list to help you manage your active signpost installations. If you no longer need the signpost at one of the 
                            addresses listed, please reschedule the removal order for that address at your earliest convenience.
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>{$message[$y]}</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
                        </tr>";
                if (!$opt_out[$y]) {
                    $new_message .= "
						<tr>
                            <td>Additionally, we are testing a new inventory management feature to help you manage the supply of your panels 
                            in our warehouse. This feature is still in active development, so please bear with us as we work out any issues 
                            we encounter. As of {$date_pretty}, your inventory is as follows:
                            </td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>{$inv_message[$y]}</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
                            <td>If you prefer to not receive the monthly signpanel/rider inventory update, you can stop receiving them by 
                            logging into your account, then selecting \"My Stored Equipment\" and then unchecking the option for this e-mail.
                            </td>
						</tr>
						<tr>
							<td>&nbsp;</td>
                        </tr>";
                }
                $new_message .= "
						<tr>
							<td>Thank you for your business.<br />
								Realty SignPost LLC<br />
								H. Douglas Myers and Ryan W. Myers, Brothers and Co-Owners<br />
								Complete Information: <a href='http://www.realtysignpost.com'>www.realtysignpost.com</a><br />
								Fax and Voicemail: 703-995-4567 or 202-478-2131<br />
								Emergency Issue Resolution: 202-256-0107<br />
								www.linkedin.com/in/realtysignpost<br />
								'Wired for Your Future in Real Estate'
							</td>
						</tr>
					</tbody>
                    </table>
                </html>";

                if ($y == 997)
				mail('john.pelster@gmail.com', 'DEBUG TEST3 ' . $subject, 
					"<html><body>".$new_message . "</body></html>", 
					"From: " . EMAIL_FROM_NAME . " <".EMAIL_FROM_ADDRESS.">\n" . 
					"MIME-Version: 1.0\n" . 
					"Content-type: text/html; charset=iso-8859-1"); 

                //send emails to extra agent email addresses
                /*
				$extra_query = $database->query("select DISTINCT email_address from emails_to_users where user_id = '" . $user_id[$y] . "' and email_status = '1'");
				while($extra_result = $database->fetch_array($extra_query)) {
					mail($extra_result['email_address'], $subject, 
						"<html><body>".$new_message . "</body></html>", 
						"From: " . EMAIL_FROM_NAME . " <".EMAIL_FROM_ADDRESS.">\n" . 
						"CC: " . EMAIL_FROM_NAME . " <" . $live_email . ">\n" . 
						"MIME-Version: 1.0\n" . 
						"Content-type: text/html; charset=iso-8859-1"); 
                }
                */
			}
		}

		//send email to Ryan
		$msg = "Total number of emails sent today (not including extra agent email addresses): " . $emails_sent;

		$subject = 'Inventory Email Stats';
        #mail($live_email, $subject, $msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
	
	} //end if date_today
?>
