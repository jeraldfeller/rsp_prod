<?php
	// Cron job that should run every day, get number of active orders/customers, divide and send appropriate number of emails per day

	//start customisable values 
	$start_date = 8; //eg 5th
	$end_date = 23; //eg 25th
	$live = true; //if we're going live
	$limit = 0; //if we're limiting no of records (set to 0 if no limit)
	$test_email = 'library@cybril.com'; //test email (mine)
	$live_email = 'realtysp@yahoo.com'; //live email (admin)
	//end customisable values
	
	include('includes/application_top.php');
	include("includes/classes/mailbox.php");

	set_time_limit(120);

	$division = $end_date - $start_date; 
	
	$date_today = date('j');

	if ($date_today == 1) { //get all active orders/customers

		$truncate = $database->query('TRUNCATE TABLE ' . TABLE_EMAILS_DUE); //empty table first
		
		$sql = "SELECT DISTINCT o.user_id FROM " . TABLE_ORDERS . " as o, " . TABLE_USERS . " as u WHERE o.user_id = u.user_id AND o.address_id NOT IN (SELECT address_id FROM " . TABLE_ORDERS . " WHERE (order_type_id = 3 AND order_status_id = 3) OR order_status_id = 4)";
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
			
			while ($result = $database->fetch_array($query)) {
			
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

		$message = new email();
		$message->add_text($msg);
		$subject = 'Inventory Email Stats';
		$message->build_message();
		if ($live == false) {
			//$message->send('', $test_email, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
			mail($test_email, $subject, $msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
		} else {
			//$message->send('', $live_email, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
			//$message->send('', $test_email, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
			mail($live_email, $subject, $msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
			mail($test_email, $subject, $msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
		}

	} else if (($date_today >= $start_date) && ($date_today <= $end_date)) {
	
		//get the records (user id's) for the specific day and get their order/s details
		$sql = "SELECT * FROM " . TABLE_EMAILS_DUE . " WHERE date = '$date_today' ORDER BY user_id ";
		if ($limit != '') $sql .= " LIMIT $limit";
		$query = $database->query($sql);
		$emails_sent = 0;
		$message = array();
		$firstname = array();
		$lastname = array();
		$name = array();
		$user_email = array();
		$x=0;
		
		if ($database->num_rows($query) > 0) {

			if ($live == false) echo "<table border=1><tr><td>No Users:</td><td>" . $database->num_rows($query) . "</td></tr>";

			while ($result = $database->fetch_array($query)) {
			
				$order_sql = "SELECT * FROM " . TABLE_USERS . " as e, " . TABLE_USERS_DESCRIPTION . " as u, " . TABLE_ORDERS . " as o, " . TABLE_ADDRESSES . " as a WHERE e.user_id = u.user_id AND u.user_id = o.user_id AND o.user_id = " . $result['user_id'] . " AND o.address_id = a.address_id AND o.order_type_id = 1 AND o.address_id NOT IN (SELECT address_id FROM " . TABLE_ORDERS . " WHERE (order_type_id = 3 AND order_status_id = 3) OR order_status_id = 4) ORDER BY o.order_id ASC";
				$order_query = $database->query($order_sql);

				if ($database->num_rows($order_query) > 0) {
				
					$x++;
					$message[$x] = '<table width="100%">';
					
					if ($live == false) echo "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>";
					if ($live == false) echo "<tr><td width='30%'>User ID: </td><td width='70%'>".$result['user_id']."</td></tr>";
					if ($live == false) echo "<tr><td width='30%'>No Orders: </td><td width='70%'>".$database->num_rows($order_query)."</td></tr>";

					while ($order_result = $database->fetch_array($order_query)) {
	
						if ($live == true) {
							$user_email[$x] = $order_result['email_address'];
						} else {
							//$user_email[$x] = $live_email; 
							$user_email[$x] = $test_email; 
						}
						
						$firstname[$x] = $order_result['firstname'];
						$lastname[$x] = $order_result['lastname'];
						$name[$x] = $firstname[$x] . " " . $lastname[$x];
						
						$message[$x] .= "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>";
						$message[$x] .= "<tr><td width='30%'>Order ID: </td><td width='70%'>" . $order_result['order_id'] . "</td></tr>";
						$message[$x] .= "<tr><td width='30%'>Address: </td><td width='70%'>" . $order_result['house_number'] . " " . $order_result['street_name'] . " " . $order_result['city'] . " " . $order_result['zip'] . "</td></tr>";

						if ($live == false) echo "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>";
						if ($live == false) echo "<tr><td width='30%'>Order ID: </td><td width='70%'>" . $order_result['order_id'] . "</td></tr>";
						if ($live == false) echo "<tr><td width='30%'>Order Status: </td><td width='70%'>" . $order_result['order_status_id'] . "</td></tr>";
						if ($live == false) echo "<tr><td width='30%'>Address: </td><td width='70%'>" . $order_result['house_number'] . " " . $order_result['street_name'] . " " . $order_result['city'] . " " . $order_result['zip'] . "</td></tr>";
	
						if ($order_result['date_completed'] != '0') {
							$message[$x] .= "<tr><td width='30%'>Date Installed: </td><td width='70%'>" . date("Y-M-d",$order_result['date_completed']); 
							if ($live == false) echo "<tr><td width='30%'>Date Installed: </td><td width='70%'>" . date("Y-M-d",$order_result['date_completed']); 
						} else {
							$message[$x] .= "<tr><td width='30%'>Date Added: </td><td width='70%'>" . date("Y-M-d",$order_result['date_added']); 
							if ($live == false) echo "<tr><td width='30%'>Date Added: </td><td width='70%'>" . date("Y-M-d",$order_result['date_added']); 
						}
						
						$message[$x] .= "</td></tr><tr><td width='30%'>Date for Removal: </td><td width='70%'>";
						if ($live == false) echo "</td></tr><tr><td width='30%'>Date for Removal: </td><td width='70%'>";
						
						$remove_sql = "SELECT * FROM " . TABLE_ORDERS . " WHERE address_id = ". $order_result['address_id'] . " AND order_type_id = 3";
						$remove_query = $database->query($remove_sql);
						$remove_num = $database->num_rows($remove_query);
						
						if ($remove_num > 0) {
							$remove_result = $database->fetch_array($remove_query);
							if ($remove_result['date_schedualed'] != '0') {
								$message[$x] .= date("Y-M-d",$remove_result['date_schedualed']); 
								if ($live == false) echo date("Y-M-d",$remove_result['date_schedualed']); 
							} else {
								$message[$x] .= "None Scheduled";
								if ($live == false) echo "None Scheduled";
							}
						} else {
							$message[$x] .= "None Scheduled";
							if ($live == false) echo "None Scheduled";
						}
	
						$message[$x] .=  "</td></tr>";
						if ($live == false) echo  "</td></tr>";
						if ($live == false) echo "<tr><td width='30%'>User Name: </td><td width='70%'>$name[$x]</td></tr>";

					} //end while orders
				
					$message[$x] .= '</table>';
					$emails_sent++;
					
				} else { //no orders (active) for this user
				
					if ($live == false) echo "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>";
					if ($live == false) echo "<tr><td width='30%'>User ID: </td><td width='70%'>".$result['user_id']."</td></tr>";
					if ($live == false) echo "<tr><td width='30%'>No Orders: </td><td width='70%'>0</td></tr>";
					
				} //end if

			} //end while users/emails 

			if ($live == false) echo "<tr><td width='30%'>&nbsp;</td><td width='70%'>&nbsp;</td></tr>";
			if ($live == false) echo "<tr><td width='30%'>No Emails Sent:</td><td width='70%'>$emails_sent</td></tr>";
			if ($live == false) echo "</table>";
			
		} //end if users/emails

		//send emails
		for ($y=1;$y<=$x;$y++) {

			if ($message[$y] != '') {
			
				//$email_template = new email_template('inventory_status');
				//$email_template->load_email_template();
				//$email_template->set_email_template_variable('INVENTORY', $message[$y]);
				//$email_template->set_email_template_variable('NAME', $name[$y]);
				//$email_template->parse_template();
				//$email_template->send_email($user_email[$y], $firstname[$y].','.$lastname[$y]);

$subject = "RSP Active SignPost Summary for " . $name[$y];

$new_message = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
    <tbody>
        <tr>
            <td>Greetings from Realty SignPost. Below is a list of addresses where you currently have a signpost installed. This list includes the date the signpost was installed, and the scheduled removal date, if a removal date has been scheduled for that signpost. 
			We are providing this list to help you manage your active signpost installations. If you no longer need the signpost at one of the addresses listed, please place a removal order for that address. 
			<br /><br />
            If you feel there are mistakes on this list, please contact us at <a href='info@realtysignpost.com'>info@realtysignpost.com</a>, and we will make the appropriate corrections.
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
			<td>".$message[$y]."</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Thank you for your business.<br />
				Realty SignPost LLC<br />
				H. Douglas Myers and Ryan W. Myers, Brothers and Co-Owners<br />
				Complete Information: <a href='http://www.realtysignpost.com'>realtysignpost.com</a><br />
				Fax and Voicemail: 703-995-4567 or 202-478-2131<br />
				Emergency Issue Resolution: 202-256-0107.<br />
				'Wired for Your Future in Real Estate'
            </td>
        </tr>
    </tbody>
</table>";

mail($user_email[$y], $subject, 
    "<html><body>".$new_message . "</body></html>", 
    "From: " . EMAIL_FROM_NAME . " <".EMAIL_FROM_ADDRESS.">\n" . 
    "cc: " . EMAIL_FROM_NAME . " <" . $live_email . ">\n" . 
    "MIME-Version: 1.0\n" . 
    "Content-type: text/html; charset=iso-8859-1"); 

			}
									
		}

		//send email to Ryan
		$msg = "Total number of emails sent today: " . $emails_sent;

		$stat_message = new email();
		$stat_message->add_text($msg);
		$subject = 'Inventory Email Stats';
		$stat_message->build_message();
		if ($live == false) {
			//$stat_message->send('', $test_email, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
			mail($test_email, $subject, $msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
		} else {
			//$stat_message->send('', $live_email, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
			//$stat_message->send('', $test_email, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
			mail($live_email, $subject, $msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
			mail($test_email, $subject, $msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
		}
	
	} //end if date_today

	//check for bounced emails 
	$mailbox = new Pop3Mailbox('realtysi','mail.realtysignpost.com','h1k%^jk54j&');

	$headers = $mailbox->getHeaders();
	$email_msg = '';
	
	for($i=1;$i<count($headers)+1;$i++) {
	
		$bounce_msg = $mailbox->getMessage($i);
		if ((strpos($bounce_msg['subject'],'fail') == true) || (strpos($bounce_msg['body'],'fail') == true)) {
			$start = strpos($bounce_msg['body'],'To:');
			$end = strpos($bounce_msg['body'],'Subject:');
			$length = $end-$start;
			$email = substr($bounce_msg['body'],$start,$length);
			$email_msg .= $email . "\n";
			$mailbox->deleteMessage($i);
		}
	}
	
	$mailbox->close();

	if ($email_msg != '') {
	
		$bounce_message = new email();
		$bounce_message->add_text($email_msg);
		$subject = 'Bounced Emails';
		$bounce_message->build_message();
		if ($live == false) {
			//$bounce_message->send('', $test_email, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
			mail($test_email, $subject, $email_msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
		} else {
			//$bounce_message->send('', $live_email, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
			//$bounce_message->send('', $test_email, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
			mail($live_email, $subject, $email_msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
			mail($test_email, $subject, $email_msg, "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nX-Mailer: PHP/" . phpversion());
		}
	}
?>
