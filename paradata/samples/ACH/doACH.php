<html>
<head>
   <title>Demo Store Sale Results</title>
   <link rel="STYLESHEET" type="text/css" href="main.css">
</head>
<body bgcolor="#FFFFFF" text="#000000">
 
<?php
	include("Paygateway.php");

	define("TEST_TOKEN", "195325FCC230184964CAB3A8D93EEB31888C42C714E39CBBB2E541884485D04B");

	$errorMessages = array();

	$ACHRequest = new ACHRequest();
	$ACHRequest->setAccountToken(TEST_TOKEN);

	// Populate request with data from web form

	$invoiceNumber = $HTTP_POST_VARS["invoice_number"];
	if($invoiceNumber != "") {
		if(!$ACHRequest->setInvoiceNumber($invoiceNumber)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$accountNumber = $HTTP_POST_VARS["account_number"];
	if($accountNumber != "") {
		if(!$ACHRequest->setAccountNumber($accountNumber)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$accountType = $HTTP_POST_VARS["account_type"];
	if(!$ACHRequest->setAccountType($accountType)) {
			$errorMessages[] = $ACHRequest->getError();
	}

	$accountClass = $HTTP_POST_VARS["account_class"];
	if(!$ACHRequest->setAccountClass($accountClass)) {
		$errorMessages[] = $ACHRequest->getError();
	}

	$customerIpAddress = $HTTP_POST_VARS["customer_ip_address"];
	if(!$ACHRequest->setCustomerIPAddress($customerIpAddress)) {
		$errorMessages[] = $ACHRequest->getError();
	}

	$routingNumber = $HTTP_POST_VARS["routing_number"];
	if($routingNumber != "") {
		if(!$ACHRequest->setRoutingNumber($routingNumber)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$currency = $HTTP_POST_VARS["currency"];
	if($currency != "") {
		if(!$ACHRequest->setCurrency($currency)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$checkNumber = $HTTP_POST_VARS["check_number"];
	if($checkNumber != "") {
		if(!$ACHRequest->setCheckNumber($checkNumber)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$clerkID = $HTTP_POST_VARS["clerk_id"];
	if($clerkID != "") {
		if(!$ACHRequest->setClerkID($clerkID)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$originalReferenceID = $HTTP_POST_VARS["original_reference_id"];
	if($originalReferenceID != "") {
		if(!$ACHRequest->setOriginalReferenceID($originalReferenceID)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}


	$billCustomerTitle = $HTTP_POST_VARS["bill_customer_title"];
	if($billCustomerTitle != "") {
		if(!$ACHRequest->setBillCustomerTitle($billCustomerTitle)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billMiddleName = $HTTP_POST_VARS["bill_middle_name"];
	if($billMiddleName != "") {
		if(!$ACHRequest->setBillMiddleName($billMiddleName)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billCountryCode = $HTTP_POST_VARS["bill_country_code"];
	if($billCountryCode != "") {
		if(!$ACHRequest->setBillCountryCode($billCountryCode)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billFirstName = $HTTP_POST_VARS["bill_first_name"];
	if($billFirstName != "") {
		if(!$ACHRequest->setBillFirstName($billFirstName)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billLastName = $HTTP_POST_VARS["bill_last_name"];
	if($billLastName != "") {
		if(!$ACHRequest->setBillLastName($billLastName)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billAddressOne = $HTTP_POST_VARS["bill_address_one"];
	if($billAddressOne != "") {
		if(!$ACHRequest->setBillAddressOne($billAddressOne)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billAddressTwo = $HTTP_POST_VARS["bill_address_two"];
	if($billAddressTwo != "") {
		if(!$ACHRequest->setBillAddressTwo($billAddressTwo)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billCity = $HTTP_POST_VARS["bill_city"];
	if($billCity != "") {
		if(!$ACHRequest->setBillCity($billCity)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billZipOrPostalCode = $HTTP_POST_VARS["bill_postal_code"];
	if($billZipOrPostalCode != "") {
		if(!$ACHRequest->setBillZipOrPostalCode($billZipOrPostalCode)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billStateOrProvince = $HTTP_POST_VARS["bill_state_or_province"];
	if($billStateOrProvince != "") {
		if(!$ACHRequest->setBillStateOrProvince($billStateOrProvince)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}
     
  $billCountryCode = $HTTP_POST_VARS["bill_country_code"];
	if($billCountryCode != "") {
		if(!$ACHRequest->setBillCountryCode($billCountryCode)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}


	$billEmail = $HTTP_POST_VARS["bill_email"];
	if($billEmail != "") {
		if(!$ACHRequest->setBillEmail($billEmail)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}
                                          
	$billCompany = $HTTP_POST_VARS["bill_company"];
	if($billCompany != "") {
		if(!$ACHRequest->setBillCompany($billCompany)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billPhone = $HTTP_POST_VARS["bill_phone"];
	if($billPhone != "") {
		if(!$ACHRequest->setBillPhone($billPhone)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billFax = $HTTP_POST_VARS["bill_fax"];
	if($billFax != "") {
		if(!$ACHRequest->setBillFax($billFax)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billSocialSecurityNumber = $HTTP_POST_VARS["bill_social_security_number"];
	if($billSocialSecurityNumber != "") {
		if(!$ACHRequest->setBillSocialSecurityNumber($billSocialSecurityNumber)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billBirthDay = $HTTP_POST_VARS["bill_birth_day"];
	if($billBirthDay != "") {
		if(!$ACHRequest->setBillBirthDay($billBirthDay)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billBirthMonth = $HTTP_POST_VARS["bill_birth_month"];
	if($billBirthMonth != "") {
		if(!$ACHRequest->setBillBirthMonth($billBirthMonth)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billBirthYear = $HTTP_POST_VARS["bill_birth_year"];
	if($billBirthYear != "") {
		if(!$ACHRequest->setBillBirthYear($billBirthYear)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billDriverLicenseNumber = $HTTP_POST_VARS["bill_driver_license_number"];
	if($billDriverLicenseNumber != "") {
		if(!$ACHRequest->setBillDriverLicenseNumber($billDriverLicenseNumber)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billDriverLicenseStateCode = $HTTP_POST_VARS["bill_driver_license_state_code"];
	if($billDriverLicenseStateCode != "") {
		if(!$ACHRequest->setBillDriverLicenseStateCode($billDriverLicenseStateCode)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$billDriverLicenseSwipe = $HTTP_POST_VARS["bill_driver_license_swipe"];
	if($billDriverLicenseSwipe != "") {
		if(!$ACHRequest->setBillDriverLicenseSwipe($billDriverLicenseSwipe)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipCustomerTitle = $HTTP_POST_VARS["ship_customer_title"];
	if($shipCustomerTitle != "") {
		if(!$ACHRequest->setShipCustomerTitle($shipCustomerTitle)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipFirstName = $HTTP_POST_VARS["ship_first_name"];
	if($shipFirstName != "") {
		if(!$ACHRequest->setShipFirstName($shipFirstName)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipLastName = $HTTP_POST_VARS["ship_last_name"];
	if($shipLastName != "") {
		if(!$ACHRequest->setShipLastName($shipLastName)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipMiddleName = $HTTP_POST_VARS["ship_middle_name"];
	if($shipMiddleName != "") {
		if(!$ACHRequest->setShipMiddleName($shipMiddleName)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipEmail = $HTTP_POST_VARS["ship_email"];
	if($shipEmail != "") {
		if(!$ACHRequest->setShipEmail($shipEmail)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipAddressOne = $HTTP_POST_VARS["ship_address_one"];
	if($shipAddressOne != "") {
		if(!$ACHRequest->setShipAddressOne($shipAddressOne)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipAddressTwo = $HTTP_POST_VARS["ship_address_two"];
	if($shipAddressTwo != "") {
		if(!$ACHRequest->setShipAddressTwo($shipAddressTwo)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipCity = $HTTP_POST_VARS["ship_city"];
	if($shipCity != "") {
		if(!$ACHRequest->setShipCity($shipCity)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipStateOrProvince = $HTTP_POST_VARS["ship_state_or_province"];
	if($shipStateOrProvince != "") {
		if(!$ACHRequest->setShipStateOrProvince($shipStateOrProvince)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipZipOrPostalCode = $HTTP_POST_VARS["ship_postal_code"];
	if($shipZipOrPostalCode != "") {
		if(!$ACHRequest->setShipZipOrPostalCode($shipZipOrPostalCode)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipCountryCode = $HTTP_POST_VARS["ship_country_code"];
	if($shipCountryCode != "") {
		if(!$ACHRequest->setShipCountryCode($shipCountryCode)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipCompany = $HTTP_POST_VARS["ship_company"];
	if($shipCompany != "") {
		if(!$ACHRequest->setShipCompany($shipCompany)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipPhone = $HTTP_POST_VARS["ship_phone"];
	if($shipPhone != "") {
		if(!$ACHRequest->setShipPhone($shipPhone)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$shipFax = $HTTP_POST_VARS["ship_fax"];
	if($shipFax != "") {
		if(!$ACHRequest->setShipFax($shipFax)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$chargeTotal = $HTTP_POST_VARS["charge_total"];
	if($chargeTotal != "") {
		if(!$ACHRequest->setChargeTotal($chargeTotal)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$purchaseOrderNumber = $HTTP_POST_VARS["purchase_order_number"];
	if($purchaseOrderNumber != "") {
		if(!$ACHRequest->setPurchaseOrderNumber($purchaseOrderNumber)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$orderDescription = $HTTP_POST_VARS["order_description"];
	if($orderDescription != "") {
		if(!$ACHRequest->setOrderDescription($orderDescription)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$orderID = $HTTP_POST_VARS["order_id"];
	if($orderID != "") {
		if(!$ACHRequest->setOrderID($orderID)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$transactionConditionCode = $HTTP_POST_VARS["transaction_condition_code"];
	if($transactionConditionCode != "") {
		if(!$ACHRequest->setTransactionConditionCode($transactionConditionCode)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$reference_id = $HTTP_POST_VARS["reference_id"];
	if ($reference_id != "") {
		if(!$ACHRequest->setReferenceId($reference_id)) {
			$errorMessages[] = $ACHRequest->getError();
		}
	}

	$chargeType = $HTTP_POST_VARS["charge_type"];
	if ($chargeType != "") {
		switch ($chargeType) {
			case "SALE":
				$chargeType = SALE;
				break;
			case "DEBIT":
				$chargeType = DEBIT;
				break;
			case "CREDIT":
				$chargeType = CREDIT;
				break;
			case "VOID":
				$chargeType = VOID;
				break;
			case "QUERY":
				$chargeType = QUERY;
				break;
		}
		if (!$ACHRequest->setChargeType($chargeType)) {
			$errorMessages[] = $ACHRequest->getError();
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
		$ACHResponse = $ACHRequest->doTransaction();

		// If there was a communication failure, then the response
		// object will be false.
		if($ACHResponse) {
			print("<br>");
			print("<table align='center' border = '0' cellspacing = '0' cellpadding = '0'>");
			print("  <tr>");
			print("    <td width='200' align='left' valign='top'>&nbsp;</td>");
			print("    <td align='center' valign='top'><h3><u>Transaction Results</u></h3></td>");
			print("    <td width='200' align='right' valign='top'><a href='demoachpage.html' class = 'header'><strong>Enter New Payment</strong></a></td>");
			print("  </tr>");
			print("</table>");

			print("<hr>");
			print("<center>");
			print("  <TABLE BORDER='0'>");
			print("    <TR ALIGN='CENTER'>");
			print("      <TD><b>Order ID:</b> " . $ACHRequest->GetOrderID() . "</TD>");
			print("      <TD><b>Purchase Order Number:</b> " . $ACHRequest->GetPurchaseOrderNumber() . "</TD>");
			print("      <TD><b>Invoice Number:</b> " . $ACHRequest->GetInvoiceNumber() . "</TD>");
			print("      <TD><b>Reference ID:</b> " . $ACHResponse->GetReferenceId() . "</TD>");
			print("    </TR>");
			print("  </TABLE>");

			print("  <HR>");
			print("  <br>");
			print("  <TABLE CELLPADDING='2' CELLSPACING='2' BORDER='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Response Fields</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Response Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHResponse->GetResponseCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Response Text:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHResponse->GetResponseCodeText() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Timestamp:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHResponse->GetTimeStamp() . "</TD>");
			print("    </TR>");
			print("    <tr align='LEFT' bgcolor='#EEEEEE'>");
			print("      <th align='right' width='200' valign='top'>Charge Type:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetChargeType() . "</td>");
			print("    </tr>");			
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Transaction Status:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHResponse->getTransactionStatus() . "</TD>");
			print("    </TR>");
			print("    <tr align='LEFT' bgcolor='#EEEEEE'>");
			print("      <th valign='top' align='right' width='200'>Transaction Condition Code:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetTransactionConditionCode() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Customer IP:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getCustomerIPAddress() . "</TD>");
			print("    </TR>");			
			print("  </TABLE>");

			print("  <p>&nbsp;</p>");
			print("  <table cellpadding='2' cellspacing='2' border='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Financial Details</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th valign='top' align='right' width='200'>Account Class:</th>");
			switch ($ACHRequest->GetAccountClass()) {
				case "":
					$accountClass = "";
					break;
				case PERSONAL:
					$accountClass = "Personal";
					break;
				case CORPORATE:
					$accountClass = "Corporate";
					break;
			}
			print("      <TD width='300'>&nbsp;" . $accountClass . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT' bgcolor='#EEEEEE'>");
			print("      <th valign='top' align='right' width='200'>Account Type:</th>");
			switch ($ACHRequest->GetAccountType()) {
				case "":
					$accountType = "";
					break;
				case SAVINGS:
					$accountType = "Saving";
					break;
				case CHECKING:
					$accountType = "Checking";
					break;
			}
			print("      <TD width='300'>&nbsp;" . $accountType . "</td>");
			print("    </tr>");			
			print("    <tr align='LEFT'>");
			print("      <th valign='top' align='right' width='200'>Routing Number:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetRoutingNumber() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT' bgcolor='#EEEEEE'>");
			print("      <th valign='top' align='right' width='200'>Account Number:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetAccountNumber() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th valign='top' align='right' width='200'>Check Number:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetCheckNumber() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT' bgcolor='#EEEEEE'>");
			print("      <th valign='top' align='right' width='200'>Charge Total:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetChargeTotal() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th valign='top' align='right' width='200'>Currency:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetCurrency() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Batch ID:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHResponse->getBatchID() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Clerk ID:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getClerkID() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Is Resubmittable:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHResponse->getIsResubmittable() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Original Reference ID:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHResponse->getOriginalReferenceId() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Amount:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHResponse->getAmount() . "</TD>");
			print("    </TR>");			
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Details:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetOrderDescription() . "</td>");
			print("    </tr>");
			print("  </table>");

			print("  <p>&nbsp;</p>");			

			print("  <TABLE CELLPADDING='2' CELLSPACING='2' BORDER='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Identity Information</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Social Security Number:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillSocialSecurityNumber() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <th align='right' width='200' valign='top'>Birth Day:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillBirthDay() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Birth Month:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillBirthMonth() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <th align='right' width='200' valign='top'>Birth Year:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillBirthYear() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Driver License Number:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillDriverLicenseNumber() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <th align='right' width='200' valign='top'>Driver License State Code:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillDriverLicenseStateCode() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Driver License Swipe:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillDriverLicenseSwipe() . "</td>");
			print("    </tr>");
			print("  </table>");

			print("  <p>&nbsp;</p>");			


			print("  <TABLE CELLPADDING='2' CELLSPACING='2' BORDER='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Billing Information</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Customer Title:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillCustomerTitle() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <th align='right' width='200' valign='top'>First Name:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillFirstName() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Middle Name:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillMiddleName() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Last Name:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillLastName() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Company:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillCompany() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Address One:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillAddressOne() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Address Two:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillAddressTwo() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>City:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillCity() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>State or Province:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillStateOrProvince() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Country Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillCountryCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Zip or Postal Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillZipOrPostalCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Phone:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillPhone() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Fax:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getBillFax() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Email:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->GetBillEmail() . "</TD>");
			print("    </TR>");
			print("  </TABLE>");
			print("  <p>&nbsp;</p>");

			print("  <TABLE CELLPADDING='2' CELLSPACING='2' BORDER='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Shipping Information</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Customer Title:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipCustomerTitle() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <th align='right' width='200' valign='top'>First Name:</th>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipFirstName() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Middle Name:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipMiddleName() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Last Name:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipLastName() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Company:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipCompany() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Address One:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipAddressOne() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Address Two:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipAddressTwo() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>City:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipCity() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>State or Province:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipStateOrProvince() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Country Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipCountryCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Zip or Postal Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipZipOrPostalCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Phone:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipPhone() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Fax:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipFax() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Email:</TH>");
			print("      <TD width='300'>&nbsp;" . $ACHRequest->getShipEmail() . "</TD>");
			print("    </TR>");
			print("  </TABLE>");
			print("  <p>&nbsp;</p>");

			//print("  </TABLE>");
			print("  <BR>");
			print("  <HR>");
			print("</center>");
		} else {
			print("<h1><font color=red>Test Transaction Failed</font></h1>A communication error occurred.");
			print("<p>Error: ". $ACHRequest->getError());
		}
	}
?>

</body>
</html>

