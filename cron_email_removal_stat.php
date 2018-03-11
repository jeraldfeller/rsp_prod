<?php

	// Cron job that should run every day on 01:01 AM, sending stats to a specific email address.



	$live = true; //if we're going live

	$limit = 0; //if we're limiting no of records (set to 0 if no limit)

	$live_email = 'realtysp@yahoo.com'; //live email (admin)

	//$live_email = 'netz_pro@hotmail.com';

	//end customisable values

	include('includes/application_top.php');

	include("includes/classes/mailbox.php");



$start_time = mktime(0, 0, 0, date("n", mktime()), (date("d", mktime())+7), date("Y", mktime()));

$end_time = ($start_time + ((60*60*24) - 1));



$seven_day_count = 0;

$fifteen_day_count = 0;

$mail_message ="";



$query = $database->query("select order_id, address_id, date_schedualed, user_id from " . TABLE_ORDERS . " where date_schedualed >= '" . $start_time . "' and date_schedualed <= '". $end_time . "' and order_type_id = '3' and order_status_id <= '2'");
    foreach($database->fetch_array($query) as $result)
	{



		$user_query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $result['user_id'] . "' and u.user_id = ud.user_id limit 1");

		$user_result = $database->fetch_array($user_query);

		//	if (($user_result['email_address'] == 'hdmyers@excite.com') || ($user_result['email_address'] == 'ryan_myers@yahoo.com')) {

				$address_query = $database->query("select a.house_number, a.street_name, a.city, a.zip, c.name as county_name, s.name as state_name from " . TABLE_ADDRESSES . " a, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where a.address_id = '" . $result['address_id'] . "' and a.state_id = s.state_id and a.county_id = c.county_id limit 1");

				$address_result = $database->fetch_array($address_query);



							$seven_day_count += 1;

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" width=\"200\">Removal Scheduled On:</td>";

								$mail_message .= "<td align=\"left\"  >&nbsp;" . date("l jS \of F Y", $result['date_schedualed']) . "</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >Street Address:</td>";

								$mail_message .= "<td align=\"left\"  >&nbsp;" . $address_result["house_number"] . "&nbsp;" . $address_result['street_name'] . "</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >City Name:</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;$address_result[city]</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >State:</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;$address_result[state_name]</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >County:</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;$address_result[county_name]</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >Email Address:</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;$user_result[email_address]</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >&nbsp;</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;</td>";

							$mail_message .= "</tr>";

							

	}



					$mail_message_top = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";

					$mail_message_top .= "<tr>";

						$mail_message_top .= "<td align=\"left\" colspan=\"2\" ><b>Total # of e-mails sent for 7 day removal notification :&nbsp;&nbsp;$seven_day_count</b></td>";

					$mail_message_top .= "</tr>";

					$mail_message_top .= "<tr>";

						$mail_message_top .= "<td align=\"left\" colspan=\"2\" >&nbsp;</td>";

					$mail_message_top .= "</tr>";

					

					$mail_message_full = $mail_message_top . $mail_message;

					

					$mail_message ="";



$start_time = mktime(0, 0, 0, date("n", mktime()), (date("d", mktime())+15), date("Y", mktime()));

$end_time = ($start_time + ((60*60*24) - 1));

$query = $database->query("select order_id, address_id, date_schedualed, user_id from " . TABLE_ORDERS . " where date_schedualed >= '" . $start_time . "' and date_schedualed <= '". $end_time . "' and order_type_id = '3' and order_status_id <= '2'");

    foreach($database->fetch_array($query) as $result)
	{

		$user_query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $result['user_id'] . "' and u.user_id = ud.user_id limit 1");

		$user_result = $database->fetch_array($user_query);

			//if (($user_result['email_address'] == 'hdmyers@excite.com') || ($user_result['email_address'] == 'ryan_myers@yahoo.com')) {

				$address_query = $database->query("select a.house_number, a.street_name, a.city, a.zip, c.name as county_name, s.name as state_name from " . TABLE_ADDRESSES . " a, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where a.address_id = '" . $result['address_id'] . "' and a.state_id = s.state_id and a.county_id = c.county_id limit 1");

				$address_result = $database->fetch_array($address_query);





							$fifteen_day_count += 1;

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >Removal Scheduled On:</td>";

								$mail_message .= "<td align=\"left\"  >&nbsp;" . date("l jS \of F Y", $result['date_schedualed']) . "</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >Street Address:</td>";

								$mail_message .= "<td align=\"left\"  >&nbsp;" . $address_result["house_number"] . "&nbsp;" . $address_result['street_name'] . "</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >City Name:</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;$address_result[city]</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >State:</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;$address_result[state_name]</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >County:</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;$address_result[county_name]</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >Email Address:</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;$user_result[email_address]</td>";

							$mail_message .= "</tr>";

							$mail_message .= "<tr>";

								$mail_message .= "<td align=\"left\" >&nbsp;</td>";

								$mail_message .= "<td align=\"left\" >&nbsp;</td>";

							$mail_message .= "</tr>";



	}



					$mail_message .= "</table>";

					

					$mail_message_bottom = "<tr>";

						$mail_message_bottom .= "<td align=\"left\" colspan=\"2\" >&nbsp;</td>";

					$mail_message_bottom .= "</tr>";

					$mail_message_bottom .= "<tr>";

						$mail_message_bottom .= "<td align=\"left\" colspan=\"2\" ><b>Total # of e-mails sent for 15 day removal notification :&nbsp;&nbsp;$fifteen_day_count</b></td>";

					$mail_message_bottom .= "</tr>";

					$mail_message_bottom .= "<tr>";

						$mail_message_bottom .= "<td align=\"left\" colspan=\"2\" >&nbsp;</td>";

					$mail_message_bottom .= "</tr>";

					

					$mail_message_full .= $mail_message_bottom . $mail_message;





		$message = new email();

		$message->add_text($mail_message_full);

		$subject = date("F j, Y") . ' Removal Notification Summary E-mail';

		$message->build_message();

		mail($live_email, $subject, "<html><body>".$mail_message_full . "</body></html>", "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1" );



?>