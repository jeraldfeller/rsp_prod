<html>
<head>
   <title>Demo Store Sale Results</title>
   <link rel="STYLESHEET" type="text/css" href="main.css">
</head>
<body bgcolor="#FFFFFF" text="#000000">

<?php
	include("../../Paygateway.php");

	define("TEST_TOKEN", "TESTA3D2B018CE6D5305338CE5F08256AD64C3CD2B9640B0C0F7ECA14D2B8DDC5DE59D5676E1C818BF");
	
	$errorMessages = array();

	$creditCardRequest = new TransactionRequest();

	$creditCardRequest->setAccountToken(TEST_TOKEN);


	// Populate request with data from web form
	$buyerCode = $HTTP_POST_VARS["buyer_code"];
	if($buyerCode != "") {
		if(!$creditCardRequest->setBuyerCode($buyerCode)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$invoiceNumber = $HTTP_POST_VARS["invoice_number"];
	if($invoiceNumber != "") {
		if(!$creditCardRequest->setInvoiceNumber($invoiceNumber)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}
	$billAddressOne = $HTTP_POST_VARS["bill_address_one"];
	if($billAddressOne != "") {
		if(!$creditCardRequest->setBillAddressOne($billAddressOne)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billAddressTwo = $HTTP_POST_VARS["bill_address_two"];
	if($billAddressTwo != "") {
		if(!$creditCardRequest->setBillAddressTwo($billAddressTwo))	{
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billCity = $HTTP_POST_VARS["bill_city"];
	if($billCity != "") {
		if(!$creditCardRequest->setBillCity($billCity)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billCompany = $HTTP_POST_VARS["bill_company"];
	if($billCompany != "") {
		if(!$creditCardRequest->setBillCompany($billCompany)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billCountryCode = $HTTP_POST_VARS["bill_country_code"];
	if($billCountryCode != "") {
		if(!$creditCardRequest->setBillCountryCode($billCountryCode)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billCustomerTitle = $HTTP_POST_VARS["bill_customer_title"];
	if($billCustomerTitle != "") {
		if(!$creditCardRequest->setBillCustomerTitle($billCustomerTitle)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billEmail = $HTTP_POST_VARS["bill_email"];
	if($billEmail != "") {
		if(!$creditCardRequest->setBillEmail($billEmail)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billFax = $HTTP_POST_VARS["bill_fax"];
	if($billFax != "") {
		if(!$creditCardRequest->setBillFax($billFax)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billFirstName = $HTTP_POST_VARS["bill_first_name"];
	if($billFirstName != "") {
		if(!$creditCardRequest->setBillFirstName($billFirstName)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billLastName = $HTTP_POST_VARS["bill_last_name"];
	if($billLastName != "") {
		if(!$creditCardRequest->setBillLastName($billLastName)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billMiddleName = $HTTP_POST_VARS["bill_middle_name"];
	if($billMiddleName != "") {
		if(!$creditCardRequest->setBillMiddleName($billMiddleName)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billNote = $HTTP_POST_VARS["bill_note"];
	if($billNote != "") {
		if(!$creditCardRequest->setBillNote($billNote)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billPhone = $HTTP_POST_VARS["bill_phone"];
	if($billPhone != "") {
		if(!$creditCardRequest->setBillPhone($billPhone)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billZipOrPostalCode = $HTTP_POST_VARS["bill_zip_or_postal_code"];
	if($billZipOrPostalCode != "") {
		if(!$creditCardRequest->setBillZipOrPostalCode($billZipOrPostalCode)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$billStateOrProvince = $HTTP_POST_VARS["bill_state_or_province"];
	if($billStateOrProvince != "") {
		if(!$creditCardRequest->setBillStateOrProvince($billStateOrProvince)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$cardBrand = $HTTP_POST_VARS["card_brand"];
	if($cardBrand != "") {
		if(!$creditCardRequest->setCardBrand($cardBrand)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$chargeTotal = $HTTP_POST_VARS["charge_total"];
	if($chargeTotal != "") {
		if(!$creditCardRequest->setChargeTotal($chargeTotal)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$chargeType = $HTTP_POST_VARS["charge_type"];
	if ($chargeType != "") {
		switch ($chargeType) {
			case "AUTH":
				$chargeType = AUTH;
				break;
			case "CAPTURE":
				$chargeType = CAPTURE;
				break;
			case "SALE":
				$chargeType = SALE;
				break;
			case "CREDIT":
				$chargeType = CREDIT;
				break;
			case "VOID":
				$chargeType = VOID;
				break;
			case "FORCE_AUTH":
				$chargeType = FORCE_AUTH;
				break;
			case "FORCE_SALE":
				$chargeType = FORCE_SALE;
				break;
			case "QUERY_PAYMENT":
				$chargeType = QUERY_PAYMENT;
				break;
			case "QUERY_CREDIT":
				$chargeType = QUERY_CREDIT;
				break;
			case "ADJUSTMENT":
				$chargeType = ADJUSTMENT;
				break;
		}
		if (!$creditCardRequest->setChargeType($chargeType)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$creditCardNumber = $HTTP_POST_VARS["credit_card_number"];
	if($creditCardNumber != "") {
		if(!$creditCardRequest->setCreditCardNumber($creditCardNumber)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$creditCardVerificationNumber = $HTTP_POST_VARS["credit_card_verification_number"];
	if($creditCardVerificationNumber != "") {
		if(!$creditCardRequest->setCreditCardVerificationNumber($creditCardVerificationNumber)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$duplicateCheck = $HTTP_POST_VARS["duplicate_check"];
	if($duplicateCheck != "") {
		if(!$creditCardRequest->setDuplicateCheck($duplicateCheck)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$purchaseOrderNumber = $HTTP_POST_VARS["purchase_order_number"];
	if($purchaseOrderNumber != "") {
		if(!$creditCardRequest->setPurchaseOrderNumber($purchaseOrderNumber)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$expireMonth = $HTTP_POST_VARS["expire_month"];
	if($expireMonth != "") {
		if(!$creditCardRequest->setExpireMonth($expireMonth)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$expireYear = $HTTP_POST_VARS["expire_year"];
	if($expireYear != "") {
		if(!$creditCardRequest->setExpireYear($expireYear)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$orderDescription = $HTTP_POST_VARS["order_description"];
	if($orderDescription != "") {
		if(!$creditCardRequest->setOrderDescription($orderDescription)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$orderID = $HTTP_POST_VARS["order_id"];
	if($orderID != "") {
		if(!$creditCardRequest->setOrderID($orderID)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$orderUserID = $HTTP_POST_VARS["order_user_id"];
	if($orderUserID != "") {
		if(!$creditCardRequest->setOrderUserID($orderUserID)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipAddressOne = $HTTP_POST_VARS["ship_address_one"];
	if($shipAddressOne != "") {
		if(!$creditCardRequest->setShipAddressOne($shipAddressOne)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipAddressTwo = $HTTP_POST_VARS["ship_address_two"];
	if($shipAddressTwo != "") {
		if(!$creditCardRequest->setShipAddressTwo($shipAddressTwo))	{
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipCity = $HTTP_POST_VARS["ship_city"];
	if($shipCity != "") {
		if(!$creditCardRequest->setShipCity($shipCity)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipCompany = $HTTP_POST_VARS["ship_company"];
	if($shipCompany != "") {
		if(!$creditCardRequest->setShipCompany($shipCompany)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipCountryCode = $HTTP_POST_VARS["ship_country_code"];
	if($shipCountryCode != "") {
		if(!$creditCardRequest->setShipCountryCode($shipCountryCode)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipCustomerTitle = $HTTP_POST_VARS["ship_customer_title"];
	if($shipCustomerTitle != "") {
		if(!$creditCardRequest->setShipCustomerTitle($shipCustomerTitle)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipEmail = $HTTP_POST_VARS["ship_email"];
	if($shipEmail != "") {
		if(!$creditCardRequest->setShipEmail($shipEmail)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipFax = $HTTP_POST_VARS["ship_fax"];
	if($shipFax != "") {
		if(!$creditCardRequest->setShipFax($shipFax)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipFirstName = $HTTP_POST_VARS["ship_first_name"];
	if($shipFirstName != "") {
		if(!$creditCardRequest->setShipFirstName($shipFirstName)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipLastName = $HTTP_POST_VARS["ship_last_name"];
	if($shipLastName != "") {
		if(!$creditCardRequest->setShipLastName($shipLastName)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipMiddleName = $HTTP_POST_VARS["ship_middle_name"];
	if($shipMiddleName != "") {
		if(!$creditCardRequest->setShipMiddleName($shipMiddleName)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipNote = $HTTP_POST_VARS["ship_note"];
	if($shipNote != "") {
		if(!$creditCardRequest->setShipNote($shipNote)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipPhone = $HTTP_POST_VARS["ship_phone"];
	if($shipPhone != "") {
		if(!$creditCardRequest->setShipPhone($shipPhone)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipZipOrPostalCode = $HTTP_POST_VARS["ship_zip_or_postal_code"];
	if($shipZipOrPostalCode != "") {
		if(!$creditCardRequest->setShipZipOrPostalCode($shipZipOrPostalCode)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shipStateOrProvince = $HTTP_POST_VARS["ship_state_or_province"];
	if($shipStateOrProvince != "") {
		if(!$creditCardRequest->setShipStateOrProvince($shipStateOrProvince)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$shippingCharge = $HTTP_POST_VARS["shipping_charge"];
	if($shippingCharge != "") {
		if(!$creditCardRequest->setShippingCharge($shippingCharge)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$taxAmount = $HTTP_POST_VARS["tax_amount"];
	if($taxAmount != "") {
		if(!$creditCardRequest->setTaxAmount($taxAmount)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$transactionConditionCode = $HTTP_POST_VARS["transaction_condition_code"];
	if($transactionConditionCode != "") {
		if(!$creditCardRequest->setTransactionConditionCode($transactionConditionCode)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$taxExempt = $HTTP_POST_VARS["tax_exempt"];
	if ($taxExempt != "") {
		if (!$creditCardRequest->setTaxExempt(($taxExempt == "true"))) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$stateTax = $HTTP_POST_VARS["state_tax"];
	if ($stateTax != "") {
		if (!$creditCardRequest->setStateTax($stateTax)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$bankApprovalCode = $HTTP_POST_VARS["bank_approval_code"];
	if ($bankApprovalCode != "") {
		if (!$creditCardRequest->setBankApprovalCode($bankApprovalCode)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$referenceID = $HTTP_POST_VARS["reference_id"];
	if ($referenceID != "") {
		if (!$creditCardRequest->setReferenceID($referenceID)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}


	// optional service details
	$folioNumber = $HTTP_POST_VARS["folio_number"];
	if ($folioNumber != "") {
		if (!$creditCardRequest->setFolioNumber($folioNumber)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}


	$industry = $HTTP_POST_VARS["industry"];
	if ($industry != "") {
		if (!$creditCardRequest->setIndustry($industry)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}


	$chargeTotalIncludesRestaurant = $HTTP_POST_VARS["charge_total_incl_restaurant"];
	if ($chargeTotalIncludesRestaurant != "") {
		if (!$creditCardRequest->setChargeTotalIncludesRestaurant($chargeTotalIncludesRestaurant)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$chargeTotalIncludesGiftshop = $HTTP_POST_VARS["charge_total_incl_giftshop"];
	if ($chargeTotalIncludesGiftshop != "") {
		if (!$creditCardRequest->setChargeTotalIncludesGiftshop($chargeTotalIncludesGiftshop)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$chargeTotalIncludesMinibar = $HTTP_POST_VARS["charge_total_incl_minibar"];
	if ($chargeTotalIncludesMinibar != "") {
		if (!$creditCardRequest->setChargeTotalIncludesMinibar($chargeTotalIncludesMinibar)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

		$chargeTotalIncludesPhone = $HTTP_POST_VARS["charge_total_incl_phone"];
	if ($chargeTotalIncludesPhone != "") {
		if (!$creditCardRequest->setChargeTotalIncludesPhone($chargeTotalIncludesPhone)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$chargeTotalIncludesLaundry = $HTTP_POST_VARS["charge_total_incl_laundry"];
	if ($chargeTotalIncludesLaundry != "") {
		if (!$creditCardRequest->setChargeTotalIncludesLaundry($chargeTotalIncludesLaundry)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$chargeTotalIncludesOther = $HTTP_POST_VARS["charge_total_incl_other"];
	if ($chargeTotalIncludesOther != "") {
		if (!$creditCardRequest->setChargeTotalIncludesOther($chargeTotalIncludesOther)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$serviceRate = $HTTP_POST_VARS["service_rate"];
	if ($serviceRate != "") {
		if (!$creditCardRequest->setServiceRate($serviceRate)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$serviceStartDay = $HTTP_POST_VARS["service_start_day"];
	if ($serviceStartDay != "") {
		if (!$creditCardRequest->setServiceStartDay($serviceStartDay)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$serviceStartMonth = $HTTP_POST_VARS["service_start_month"];
	if ($serviceStartMonth != "") {
		if (!$creditCardRequest->setServiceStartMonth($serviceStartMonth)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$serviceStartYear = $HTTP_POST_VARS["service_start_year"];
	if ($serviceStartYear != "") {
		if (!$creditCardRequest->setServiceStartYear($serviceStartYear)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$serviceEndDay = $HTTP_POST_VARS["service_end_day"];
	if ($serviceEndDay != "") {
		if (!$creditCardRequest->setServiceEndDay($serviceEndDay)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$serviceEndMonth = $HTTP_POST_VARS["service_end_month"];
	if ($serviceEndMonth != "") {
		if (!$creditCardRequest->setServiceEndMonth($serviceEndMonth)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$serviceEndYear = $HTTP_POST_VARS["service_end_year"];
	if ($serviceEndYear != "") {
		if (!$creditCardRequest->setServiceEndYear($serviceEndYear)) {
			$errorMessages[] = $creditCardRequest->getError();
		}
	}

	$serviceNoShow = $HTTP_POST_VARS["service_no_show"];
	if ($serviceNoShow != "") {
		if (!$creditCardRequest->setServiceNoShow($serviceNoShow)) {
			$errorMessages[] = $creditCardRequest->getError();
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
		$creditCardResponse = $creditCardRequest->doTransaction();

		// If there was a communication failure, then the response
		// object will be false.
		if($creditCardResponse) {
			print("<br>");
			print("<table align='center' border = '0' cellspacing = '0' cellpadding = '0'>");
			print("  <tr>");
			print("    <td width='200' align='left' valign='top'>&nbsp;</td>");
			print("    <td align='center' valign='top'><h3><u>Transaction Results</u></h3></td>");
			print("    <td width='200' align='right' valign='top'><a href='demopaypage.html' class = 'header'><strong>Enter New Payment</strong></a></td>");
			print("  </tr>");
			print("</table>");
			
			print("<hr>");
			print("<center>");
			print("  <TABLE BORDER='0'>");
			print("    <TR ALIGN='CENTER'>"	);
			print("      <TD><b>Order User ID:</b>" . $creditCardRequest->GetOrderUserID() . "</TD>");
			print("      <TD><b>Order ID:</b>" . $creditCardResponse->GetOrderID() . "</TD>");
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
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetResponseCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Response Text:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetResponseCodeText() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Timestamp:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetTimeStamp() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>AVS Response Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->getAVSCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Bank Approval Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetBankApprovalCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Bank Transaction ID:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetBankTransactionID() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Batch ID:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetBatchID() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Reference ID:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetReferenceID() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Credit Card Verification Response:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetCreditCardVerificationResponse() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>ISO Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetISOCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>State:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetState() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Authorized Amount:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetAuthorizedAmount() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Original Authorized Amount:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetOriginalAuthorizedAmount() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Captured Amount:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetCapturedAmount() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Credited Amount:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetCreditedAmount() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Time Stamp Created:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardResponse->GetTimeStampCreated() . "</TD>");
			print("    </TR>");
			print("  </TABLE>");

			print("  <p>&nbsp;</p>");
			print("  <table cellpadding='2' cellspacing='2' border='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Service Details</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th valign='top' align='right' width='200'>Folio Number:</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->getFolioNumber() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Industry:</th>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . ucwords(strtolower(str_replace("_", " ", $creditCardRequest->getIndustry()))) . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Charge Total Includes Restaurant</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->getChargeTotalIncludesRestaurant() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Charge Total Includes Gift Shop</th>");
			print("      <TD width='300' bgcolor='#EEEEEE'>&nbsp;" . $creditCardRequest->getChargeTotalIncludesGiftshop() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Charge Total Includes Minibar</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->getChargeTotalIncludesMinibar() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Charge Total Includes Phone</th>");
			print("      <TD width='300' bgcolor='#EEEEEE'>&nbsp;" . $creditCardRequest->getChargeTotalIncludesPhone() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Charge Total Includes Laundry</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->getChargeTotalIncludesLaundry() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Charge Total Includes Other</th>");
			print("      <TD width='300' bgcolor='#EEEEEE'>&nbsp;" . $creditCardRequest->getChargeTotalIncludesOther() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Service Rate:</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->getServiceRate() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Service Start Day:</th>");
			print("      <TD width='300' bgcolor='#EEEEEE'>&nbsp;" . $creditCardRequest->getServiceStartDay() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Service Start Month:</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->getServiceStartMonth() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Service Start Year:</th>");
			print("      <TD width='300' bgcolor='#EEEEEE'>&nbsp;" . $creditCardRequest->getServiceStartYear() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Service End Day:</th>");
			print("      <TD width='300' >&nbsp;" . $creditCardRequest->getServiceEndDay() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Service End Month:</th>");
			print("      <TD width='300' bgcolor='#EEEEEE'>&nbsp;" . $creditCardRequest->getServiceStartMonth() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Service End Year:</th>");
			print("      <TD width='300' >&nbsp;" . $creditCardRequest->getServiceStartYear() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Service No Show:</th>");
			print("      <TD width='300' bgcolor='#EEEEEE'>&nbsp;" . $creditCardRequest->getServiceNoShow() . "</td>");
			print("    </tr>");
			print("  </table>");

			print("  <p>&nbsp;</p>");
			print("  <table cellpadding='2' cellspacing='2' border='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Purchase Details</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th valign='top' align='right' width='200'>Charge Total:</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetChargeTotal() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Tax Amount:</th>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetTaxAmount() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th valign='top' align='right' width='200'>State Tax:</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetStateTax() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Tax Exempt:</th>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetTaxExempt() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Shipping Charge:</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetShippingCharge() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>Charge Type:</th>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetChargeType() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Credit Card Number:</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetCreditCardNumber() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th bgcolor='#EEEEEE' align='right' width='200' valign='top'>Expiry Date (MM/YYYY):</th>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetExpireMonth() . "/" . $creditCardRequest->GetExpireYear() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top'>Duplicate Check:</th>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetDuplicateCheck() . "</td>");
			print("    </tr>");
			print("    <tr align='LEFT'>");
			print("      <th bgcolor='#EEEEEE' align='right' width='200' valign='top'>Details:</th>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetOrderDescription() . "</td>");
			print("    </tr>");
			print("  </table>");
			print("  <p>&nbsp;</p>");
			print("  <TABLE CELLPADDING='2' CELLSPACING='2' BORDER='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Billing Information</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH valign='top' align='right' width='200'>Customer Title:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetBillCustomerTitle() . "</TD>");
			print("    </TR>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>First Name:</th>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetBillFirstName() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Last Name:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetBillLastName() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Middle Name:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetBillMiddleName() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Company:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetBillCompany() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Address One:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetBillAddressOne() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Address Two:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetBillAddressTwo() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>City:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetBillCity() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>State or Province:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetBillStateOrProvince() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Country Code:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetBillCountryCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Zip or Postal Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->getBillZipOrPostalCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Phone:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetBillPhone() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' valign='top'>Fax:</TH>");
			print("      <TD>&nbsp;" . $creditCardRequest->GetBillFax() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Email:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetBillEmail() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Note:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetBillNote() . "</TD>");
			print("    </TR>");
			print("  </TABLE>");
			print("  <p>&nbsp;</p>");
			print("  <TABLE CELLPADDING='2' CELLSPACING='2' BORDER='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Shipping Information</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH valign='top' align='right' width='200'>Customer Title:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetShipCustomerTitle() . "</TD>");
			print("    </TR>");
			print("    <tr align='LEFT'>");
			print("      <th align='right' width='200' valign='top' bgcolor='#EEEEEE'>First Name:</th>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetShipFirstName() . "</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Last Name:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetShipLastName() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Middle Name:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetShipMiddleName() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Company:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetShipCompany() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Address One:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetShipAddressOne() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Address Two:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetShipAddressTwo() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>City:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetShipCity() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>State or Province:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetShipStateOrProvince() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Country Code:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetShipCountryCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Zip or Postal Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetShipZipOrPostalCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Phone:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetShipPhone() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' valign='top'>Fax:</TH>");
			print("      <TD>&nbsp;" . $creditCardRequest->GetShipFax() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top' bgcolor='#EEEEEE'>Email:</TH>");
			print("      <TD bgcolor='#EEEEEE' width='300'>&nbsp;" . $creditCardRequest->GetShipEmail() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Note:</TH>");
			print("      <TD width='300'>&nbsp;" . $creditCardRequest->GetShipNote() . "</TD>");
			print("    </TR>");
			print("  </TABLE>");
			print("  <BR>");
			print("  <HR>");
			print("</center>");
		} else {
			print("<h1><font color=red>Test Transaction Failed</font></h1>A communication error occurred.");
			print("<p>Error: ". $creditCardRequest->getError());
		}
	}
?>

</body>
</html>

