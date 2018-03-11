<html>
<head>
   <title>Recurring Transaction Results</title>
   <link rel="STYLESHEET" type="text/css" href="main.css">
</head>
<body bgcolor="#FFFFFF" text="#000000">

<?php
	include("Paygateway.php");

	define("TEST_TOKEN", "195325FCC230184964CAB3A8D93EEB31888C42C714E39CBBB2E541884485D04B");

	$errorMessages = array();

	$recurringRequest = new RecurringRequest();

	$recurringRequest->setAccountToken(TEST_TOKEN);

	// Populate request with data from web form
	$command = $HTTP_POST_VARS["command"];
	if($command != "") {
		if(!$recurringRequest->setCommand($command)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$accountType = $HTTP_POST_VARS["account_type"];
	if($accountType != "") {
		if(!$recurringRequest->setAccountType($accountType)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$recurrenceID = $HTTP_POST_VARS["recurrence_id"];
	if($recurrenceID != "") {
		if(!$recurringRequest->setRecurrenceID($recurrenceID)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$description = $HTTP_POST_VARS["description"];
	if($description != "") {
		if(!$recurringRequest->setDescription($description)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$chargeTotal = $HTTP_POST_VARS["charge_total"];
	if($chargeTotal != "") {
		if(!$recurringRequest->setChargeTotal($chargeTotal)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$notifyCustomer = $HTTP_POST_VARS["notify_customer"];
	if($notifyCustomer != "") {
		if(!$recurringRequest->setNotifyCustomer($notifyCustomer)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	} else {
		$recurringRequest->setNotifyCustomer( 0 );
	}

	$period = $HTTP_POST_VARS["period"];
	if($period != "") {
		if(!$recurringRequest->setPeriod($period)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$numberOfRetries = $HTTP_POST_VARS["number_of_retries"];
	if($numberOfRetries != "") {
		if(!$recurringRequest->setNumberOfRetries($numberOfRetries)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$startDay = $HTTP_POST_VARS["start_day"];
	if($startDay != "") {
		if(!$recurringRequest->setStartDay($startDay)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$startMonth = $HTTP_POST_VARS["start_month"];
	if($startMonth != "") {
		if(!$recurringRequest->setStartMonth($startMonth)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$startYear = $HTTP_POST_VARS["start_year"];
	if($startYear != "") {
		if(!$recurringRequest->setStartYear($startYear)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$endDay = $HTTP_POST_VARS["end_day"];
	if($endDay != "") {
		if(!$recurringRequest->setEndDay($endDay)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$endMonth = $HTTP_POST_VARS["end_month"];
	if($endMonth != "") {
		if(!$recurringRequest->setEndMonth($endMonth)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$endYear = $HTTP_POST_VARS["end_year"];
	if($endYear != "") {
		if(!$recurringRequest->setEndYear($endYear)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$customerID = $HTTP_POST_VARS["customer_id"];
	if($customerID != "") {
		if(!$recurringRequest->setCustomerID($customerID)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$customerName = $HTTP_POST_VARS["customer_name"];
	if($customerName != "") {
		if(!$recurringRequest->setCustomerName($customerName)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$emailAddress = $HTTP_POST_VARS["email_address"];
	if($emailAddress != "") {
		if(!$recurringRequest->setEmailAddress($emailAddress)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$creditCardNumber = $HTTP_POST_VARS["credit_card_number"];
	if($creditCardNumber != "") {
		if(!$recurringRequest->setCreditCardNumber($creditCardNumber)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$expireMonth = $HTTP_POST_VARS["expire_month"];
	if($expireMonth != "") {
		if(!$recurringRequest->setExpireMonth($expireMonth)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$expireYear = $HTTP_POST_VARS["expire_year"];
	if($expireYear != "") {
		if(!$recurringRequest->setExpireYear($expireYear)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$billingAddress = $HTTP_POST_VARS["billing_address"];
	if($billingAddress != "") {
		if(!$recurringRequest->setBillingAddress($billingAddress)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$zipOrPostalCode = $HTTP_POST_VARS["zip_or_postal_code"];
	if($zipOrPostalCode != "") {
		if(!$recurringRequest->setZipOrPostalCode($zipOrPostalCode)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}

	$countryCode = $HTTP_POST_VARS["country_code"];
	if($countryCode != "") {
		if(!$recurringRequest->setCountryCode($countryCode)) {
			$errorMessages[] = $recurringRequest->getError();
		}
	}


	if(sizeof($errorMessages) != 0) {
		// Print out all the errors that happened when setting.
		print("<h1><font color=red>Test Transaction Not Attempted</font></h1>");
		print("<p>There was an error setting the fields of the TransactionRequest object.");
		print("The following errors were found:");
		print("<ul>");

		foreach ($errorMessages as $error) {
			print("   <li>$error</li>");
		}

		print("</ul>");

	} else {
		// No errors setting the values; perform the transaction
		$recurringResponse = $recurringRequest->doTransaction();

		// If there was a communication failure, then the response
		// object will be false.
		if($recurringResponse) {
			print("<br>");
			print("<table align='center' border = '0' cellspacing = '0' cellpadding = '0'>");
			print("  <tr>");
			print("    <td width='200' align='left' valign='top'>&nbsp;</td>");
			print("    <td align='center' valign='top'><h3><u>Recurring Transaction Results</u></h3></td>");
			print("    <td width='200' align='right' valign='top'><a href='demorecurringpaypage.html' class = 'header'><strong>Enter New Payment</strong></a></td>");
			print("  </tr>");
			print("</table>");
			print("<hr>");
			print("<center>");
			print("  <br>");
			print("  <TABLE CELLPADDING='2' CELLSPACING='2' BORDER='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Response Fields</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Response Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $recurringResponse->GetResponseCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Response Text:</TH>");
			print("      <TD width='300'>&nbsp;" . $recurringResponse->GetResponseCodeText() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Timestamp:</TH>");
			print("      <TD width='300'>&nbsp;" . $recurringResponse->getTimeString() . "</TD>");
			print("    </TR>");
			print("  </TABLE>");
			print("  <BR>");
			print("  <HR>");
			print("</center>");
		} else {
			print("<h1><font color=red>Recurring Transaction Failed</font></h1>A communication error occurred.");
			print("<p>Error: ". $recurringRequest->getError());
		}
	}
?>

</body>
</html>

