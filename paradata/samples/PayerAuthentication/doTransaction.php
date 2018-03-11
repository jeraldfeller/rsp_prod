<?php
session_id();
session_start();
?>

<html>
<head>
<title>Payer Authentication Transaction Results</title>
<link rel="STYLESHEET" type="text/css" href="main.css">
</head>
<body bgcolor="#FFFFFF" link="#CC0000" text="#000000" topmargin="5" leftmargin="0">
<?php

	include("Paygateway.php");

	$errorMessages = array();

	$paramPARes			= $_POST["PaRes"];
	$paramMD			= $_POST["MD"];

	$passedPayerAuthentication = false;

	// Read in variables from the session
	if($_SESSION["use_payer_authentication"]){
		$isEnrolled = $_SESSION["use_payer_authentication"];
	} else {
		$isEnrolled = false;
	}

	$ACCOUNT_TOKEN = $_SESSION["account_token"];
	$authenticationTransactionID = $_SESSION["authentication_transaction_id"];
	$chargeTotal = $_SESSION["charge_total"];
	$buyerCode = $_SESSION["buyer_code"];
	$invoiceNumber = $_SESSION["invoice_number"];
	$chargeType = $_SESSION["charge_type"];
	$creditCardNumber = $_SESSION["credit_card_number"];
	$cvv = $_SESSION["cvv"];
	$tcc = $_SESSION["transaction_condition_code"];
	$expireMonth = $_SESSION["expire_month"];
	$expireYear = $_SESSION["expire_year"];
	$orderDescription = $_SESSION["order_description"];
	$orderID = $_SESSION["order_id"];
	$orderUserID = $_SESSION["order_user_id"];
	$shippingCharge = $_SESSION["shipping_charge"];
	$taxAmount = $_SESSION["tax_amount"];

	$billAddressOne = $_SESSION["bill_address_one"];
	$billAddressTwo = $_SESSION["bill_address_two"];
	$billCity = $_SESSION["bill_city"];
	$billCompany = $_SESSION["bill_company"];
	$billCountryCode = $_SESSION["bill_country_code"];
	$billCustomerTitle = $_SESSION["bill_customer_title"];
	$billEmail = $_SESSION["bill_email"];
	$billFirstName = $_SESSION["bill_first_name"];
	$billLastName = $_SESSION["bill_last_name"];
	$billMiddleName = $_SESSION["bill_middle_name"];
	$billNote = $_SESSION["bill_note"];
	$billPhone = $_SESSION["bill_phone"];
	$billStateOrProvince = $_SESSION["bill_state_or_province"];
	$billZipOrPostalCode = $_SESSION["bill_zip_or_postal_code"];

	$shipAddressOne = $_SESSION["ship_address_one"];
	$shipAddressTwo = $_SESSION["ship_address_two"];
	$shipCity = $_SESSION["ship_city"];
	$shipCompany = $_SESSION["ship_company"];
	$shipCountryCode = $_SESSION["ship_country_code"];
	$shipCustomerTitle = $_SESSION["ship_customer_title"];
	$shipEmail = $_SESSION["ship_email"];
	$shipFirstName = $_SESSION["ship_first_name"];
	$shipLastName = $_SESSION["ship_last_name"];
	$shipMiddleName = $_SESSION["ship_middle_name"];
	$shipNote = $_SESSION["ship_note"];
	$shipPhone = $_SESSION["ship_phone"];
	$shipStateOrProvince = $_SESSION["ship_state_or_province"];
	$shipZipOrPostalCode = $_SESSION["ship_zip_or_postal_code"];

	$doTransactionOnAuthenticationInconclusive = $_SESSION["do_transaction_on_authentication_inconclusive"];


	$ccRequest = new TransactionRequest();

	if( $ACCOUNT_TOKEN != "" ) {
		if(!$ccRequest->setAccountToken( $ACCOUNT_TOKEN )){
			$errorMessages[] = $lookupRequest->getError();
		}
	}

	if( $chargeTotal != "" ) {
		if(!$ccRequest->setChargeTotal( $chargeTotal )){
			$errorMessages[] = $ccRequest->getError();
		}
	}
	if( $chargeType != "" ) {
		if(!$ccRequest->setChargeType( $chargeType )){
			$errorMessages[] = $ccRequest->getError();
		}
	}
	if( $invoiceNumber != "" ) {
			if(!$ccRequest->setInvoiceNumber( $invoiceNumber )){
				$errorMessages[] = $ccRequest->getError();
			}
		}
	if( $buyerCode != "" ) {
		if(!$ccRequest->setBuyerCode( $buyerCode )){
			$errorMessages[] = $ccRequest->getError();
		}
	}
	if( $creditCardNumber != "" ) {
		if(!$ccRequest->setCreditCardNumber( $creditCardNumber )){
			$errorMessages[] = $ccRequest->getError();
		}
	}
	if( $cvv != "" ) {
		if(!$ccRequest->setCreditCardVerificationNumber( $cvv )){
			$errorMessages[] = $ccRequest->getError();
		}
	}
	if( $tcc != "" ) {
		if(!$ccRequest->setTransactionConditionCode( $tcc )){
			$errorMessages[] = $ccRequest->getError();
		}
	}
	if( $expireMonth != "" ) {
		if(!$ccRequest->setExpireMonth( $expireMonth )){
			$errorMessages[] = $ccRequest->getError();
		}
	}
	if( $expireYear != "" ) {
		if(!$ccRequest->setExpireYear( $expireYear )){
			$errorMessages[] = $ccRequest->getError();
		}
	}
	if( $orderDescription != "" ) {
		$ccRequest->setOrderDescription( $orderDescription );
	}
	if( $orderID != "" ) {
		$ccRequest->setOrderId( $orderID );
	}
	if( $shippingCharge != "" ) {
		if(!$ccRequest->setShippingCharge( $shippingCharge )){
			$errorMessages[] = $ccRequest->getError();
		}
	}

	if( $taxAmount != "" ) {
		if(!$ccRequest->setTaxAmount( $taxAmount )){
			$errorMessages[] = $ccRequest->getError();
		}
	}
	if( $billAddressOne != "" ) {
		$ccRequest->setBillAddressOne( $billAddressOne );
	}
	if( $billAddressTwo != "" ) {
		$ccRequest->setBillAddressTwo( $billAddressTwo );
	}
	if( $billCity != "" ) {
		$ccRequest->setBillCity( $billCity );
	}
	if( $billCompany != "" ) {
		$ccRequest->setBillCompany( $billCompany );
	}
	if( $billCountryCode != "" ) {
		$ccRequest->setBillCountryCode( $billCountryCode );
	}
	if( $billCustomerTitle != "" ) {
		$ccRequest->setBillCustomerTitle( $billCustomerTitle );
	}
	if( $billEmail != "" ) {
		$ccRequest->setBillEmail( $billEmail );
	}
	if( $billFirstName != "" ) {
		$ccRequest->setBillFirstName( $billFirstName );
	}
	if( $billLastName != "" ) {
		$ccRequest->setBillLastName( $billLastName );
	}
	if( $billMiddleName != "" ) {
		$ccRequest->setBillMiddleName( $billMiddleName );
	}
	if( $billNote != "" ) {
		$ccRequest->setBillNote( $billNote );
	}
	if( $billPhone != "" ) {
		$ccRequest->setBillPhone( $billPhone );
	}
	if( $billZipOrPostalCode != "" ) {
		$ccRequest->setBillZipOrPostalCode( $billZipOrPostalCode );
	}
	if( $billStateOrProvince != "" ) {
		$ccRequest->setBillStateOrProvince( $billStateOrProvince );
	}

	if( $shipAddressOne != "" ) {
		$ccRequest->setShipAddressOne( $shipAddressOne );
	}
	if( $shipAddressTwo != "" ) {
		$ccRequest->setShipAddressTwo( $shipAddressTwo );
	}
	if( $shipCity != "" ) {
		$ccRequest->setShipCity( $shipCity );
	}
	if( $shipCompany != "" ) {
		$ccRequest->setShipCompany( $shipCompany );
	}
	if( $shipCountryCode != "" ) {
		$ccRequest->setShipCountryCode( $shipCountryCode );
	}
	if( $shipCustomerTitle != "" ) {
		$ccRequest->setShipCustomerTitle( $shipCustomerTitle );
	}
	if( $shipEmail != "" ) {
		$ccRequest->setShipEmail( $shipEmail );
	}
	if( $shipFirstName != "" ) {
		$ccRequest->setShipFirstName( $shipFirstName );
	}
	if( $shipLastName != "" ) {
		$ccRequest->setShipLastName( $shipLastName );
	}
	if( $shipMiddleName != "" ) {
		$ccRequest->setShipMiddleName( $shipMiddleName );
	}
	if( $shipNote != "" ) {
		$ccRequest->setShipNote( $shipNote );
	}
	if( $shipPhone != "" ) {
		$ccRequest->setShipPhone( $shipPhone );
	}
	if( $shipZipOrPostalCode != "" ) {
		$ccRequest->setShipZipOrPostalCode( $shipZipOrPostalCode );
	}
	if( $shipStateOrProvince != "" ) {
		$ccRequest->setShipStateOrProvince( $shipStateOrProvince );
	}

	if( $paramPARes != "" ) {

		$ccRequest->setAuthenticationPayload( $paramPARes );

		if( $authenticationTransactionID != "" ) {
			$ccRequest->setAuthenticationTransactionId( $authenticationTransactionID );
		}

		$ccRequest->setDoTransactionOnAuthenticationInconclusive( $doTransactionOnAuthenticationInconclusive );

		// You must set the transaction condition code to indicate that you
		// want to perform payer authentication.
		$ccRequest->setTransactionConditionCode(TCC_CARDHOLDER_NOT_PRESENT_PAYER_AUTHENTICATION );

	} else {
		// Bypassing payer authentication.
	}

	if(sizeof($errorMessages) == 0  ) {

		$ccResponse = $ccRequest->doTransaction();

		if( $ccResponse ) {

			// Getting the Credit Card Response Code
			$respResponseCode = $ccResponse->getResponseCode();
			$respResponseCodeText = $ccResponse->getResponseCodeText();
			$respTimeStamp = $ccResponse->getTimeStamp();

			$respOrderID = $ccResponse->getOrderId();
			$respReferenceID = $ccResponse->getReferenceId();
			$respISOCode = $ccResponse->getIsoCode();
			$respBankApprovalCode = $ccResponse->getBankApprovalCode();
			$respBankTransactionID = $ccResponse->getBankTransactionId();
			$respBatchID = $ccResponse->getBatchId();
			$respCreditCardVerificationValueResponse = $ccResponse->getCreditCardVerificationResponse();
			$respAVSCode = $ccResponse->getAvsCode();

			// Getting the Authentication Response Code
			$authRespResponseCode = $ccResponse->getAuthenticationResponseCode();
			$authRespResponseCodeText = $ccResponse->getAuthenticationResponseCodeText();
			$authRespTimeStamp = $ccResponse->getAuthenticationTimeStamp();

			$authRespCAVV = $ccResponse->getAuthenticationCAVV();
			$authRespXID = $ccResponse->getAuthenticationXID();
			$authRespTCC = $ccResponse->getAuthenticationTransactionConditionCode();

			if( RC_SUCCESSFUL_TRANSACTION == $authRespResponseCode )
				$passedPayerAuthentication = true;
?>

			<br>
			<table align="center" border = "0" cellspacing = "0" cellpadding = "0">
			  <tr>
				<td width="200" align="left" valign="top">&nbsp;</td>
			    <td align="center" valign="top"><h3><u>Transaction Results</u></h3></td>
				<td width="200" align="right" valign="top"><a href="PayPage.html" class = "header"><strong>Enter
			      New Payment</strong></a></td>
			  </tr>
			</table>
			<hr>
			<center>
			<table border="0"><tr align="center">
			  <table cellpadding="2" cellspacing="2" border="0" width="500">
				<tr class = "header">
				  <td colspan=2>&nbsp;Transaction Response Fields</td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Response Code:</th>
				  <td width="300">&nbsp;<?php echo $respResponseCode;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Response Text:</th>
				  <td width="300">&nbsp;<?php echo $respResponseCodeText;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Order ID:</th>
				  <td width="300">&nbsp;<?php echo $respOrderID;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Timestamp:</th>
				  <td width="300">&nbsp;<?php echo $respTimeStamp;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">AVS Response Code:</th>
				  <td width="300">&nbsp;<?php echo $respAVSCode;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Bank Approval Code:</th>
				  <td width="300">&nbsp;<?php echo $respBankApprovalCode;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Bank Transaction ID:</th>
				  <td width="300">&nbsp;<?php echo $respBankTransactionID;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Batch ID:</th>
				  <td width="300">&nbsp;<?php echo $respBatchID;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Reference ID:</th>
				  <td width="300">&nbsp;<?php echo $respReferenceID;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Credit Card Verification Value Response:</th>
				  <td width="300">&nbsp;<?php echo $respCreditCardVerificationValueResponse;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">ISO Code:</th>
				  <td width="300">&nbsp;<?php echo $respISOCode;?></td>
				</tr>
			  </table>
			  <p>&nbsp;</p>
			  <table cellpadding="2" cellspacing="2" border="0" width="500">
				<tr class = "header">
				  <td colspan=2>&nbsp;Authentication Response Fields</td>
				</tr>
				<tr align="left">
				  <th valign="top" align="right" width="200">Enrolled in Payer Authentication:</th>
				  <td width="300">&nbsp;<?php echo $isEnrolled;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Passed Payer Authentication:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $passedPayerAuthentication;?></td>
				</tr>
<?php
			if( $isEnrolled ) {
?>
				<tr align="left">
				  <th align="right" width="200" valign="top">Response Code:</th>
				  <td width="300">&nbsp;<?php echo $authRespResponseCode;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Response Code Text:</th>
				  <td width="300">&nbsp;<?php echo $authRespResponseCodeText;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Time Stamp:</th>
				  <td width="300">&nbsp;<?php echo $authRespTimeStamp;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">CAVV:</th>
				  <td width="300">&nbsp;<?php echo $authRespCAVV;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">XID:</th>
				  <td width="300">&nbsp;<?php echo $authRespXID;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Transaction Condition Code:</th>
				  <td width="300">&nbsp;<?php echo $authRespTCC;?></td>
				</tr>
<?php
			}		// End enrolled section
?>
			  </table>
			  <p>&nbsp;</p>
			  <table cellpadding="2" cellspacing="2" border="0" width="500">
				<tr class = "header">
				  <td colspan=2>&nbsp;Financial Details</td>
				</tr>
				<tr align="left">
				  <th valign="top" align="right" width="200">Charge Total:</th>
				  <td width="300">&nbsp;<?php echo $chargeTotal;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Tax Amount:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $taxAmount;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Shipping Charge:</th>
				  <td width="300">&nbsp;<?php echo $shippingCharge;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Charge Type:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $chargeType;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Credit Card
					Number:</th>
				  <td width="300">&nbsp;<?php echo obscureNumber($creditCardNumber);?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th  bgcolor="#EEEEEE"align="right" width="200" valign="top">Expiry Date (MM/YYYY):</th>
				  <td width="300">&nbsp;<?php echo $expireMonth;?>/<?php echo $expireYear;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Transaction Condition Code:</th>
				  <td width="300">&nbsp;<?php echo $tcc;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Details:</th>
				  <td width="300">&nbsp;<?php echo $orderDescription;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Invoice Number:</th>
				  <td width="300">&nbsp;<?php echo $invoiceNumber;?></td>
				</tr>
				<tr align="left" bgcolor="#EEEEEE">
				  <th align="right" width="200" valign="top">Buyer Code:</th>
				  <td width="300">&nbsp;<?php echo $buyerCode;?></td>
				</tr>
			  </table>
			  <p>&nbsp;</p>
			  <table cellpadding="2" cellspacing="2" border="0" width="500">
				<tr class = "header">
				  <td colspan=2>&nbsp;Billing Information</td>
				</tr>
				<tr align="left">
				  <th valign="top" align="right" width="200">Customer Title:</th>
				  <td width="300">&nbsp;<?php echo $billCustomerTitle;?></td>

				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">First Name:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $billFirstName;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Last Name:</th>
				  <td width="300">&nbsp;<?php echo $billLastName;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Middle Name:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $billMiddleName;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Company:</th>
				  <td width="300">&nbsp;<?php echo $billCompany;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Address One:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $billAddressOne;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Address Two:</th>
				  <td width="300">&nbsp;<?php echo $billAddressTwo;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">City:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $billCity;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">State or Province:</th>
				  <td width="300">&nbsp;<?php echo $billStateOrProvince;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Country Code:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $billCountryCode;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Zip or Postal Code:</th>
				  <td width="300">&nbsp;<?php echo $billZipOrPostalCode;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Phone:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $billPhone;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Email:</th>
				  <td width="300">&nbsp;<?php echo $billEmail;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Note:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $billNote;?></td>
				</tr>
			  </table>
			<p>&nbsp;</p>
			  <table cellpadding="2" cellspacing="2" border="0" width="500">
				<tr class = "header">
				  <td colspan=2>&nbsp;Shipping Information</td>
				</tr>
				<tr align="left">
				  <th valign="top" align="right" width="200">Customer Title:</th>
				  <td width="300">&nbsp;<?php echo $shipCustomerTitle;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">First Name:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $shipFirstName;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Last Name:</th>
				  <td width="300">&nbsp;<?php echo $shipLastName;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Middle Name:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $shipMiddleName;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Company:</th>
				  <td width="300">&nbsp;<?php echo $shipCompany;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Address One:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $shipAddressOne;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Address Two:</th>
				  <td width="300">&nbsp;<?php echo $shipAddressTwo;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">City:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $shipCity;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">State or Province:</th>
				  <td width="300">&nbsp;<?php echo $shipStateOrProvince;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Country Code:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $shipCountryCode;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Zip or Postal Code:</th>
				  <td width="300">&nbsp;<?php echo $shipZipOrPostalCode;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Phone:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $shipPhone;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top">Email:</th>
				  <td width="300">&nbsp;<?php echo $shipEmail;?></td>
				</tr>
				<tr align="left">
				  <th align="right" width="200" valign="top" bgcolor="#EEEEEE">Note:</th>
				  <td bgcolor="#EEEEEE" width="300">&nbsp;<?php echo $shipNote;?></td>
				</tr>
			  </table>
			<BR>
			<HR>
			</center>
<?php
		} else {
			echo( "Got null response from transaction." );

		}
	}

	if( sizeof($errorMessages) != 0 ) {
		// Error in at least one setter.  Display error page.
		// Do not perform transaction.
?>
		<BR><BR><P><center>
		<H2>Error Processing Transaction.</H2>
		<A href="PayPage.html" class = "header"><strong>Enter New Payment</strong></a>
		</center></p>
		<table cellpadding="2" cellspacing="2" border="0" width="500" align = "center">
		<tr class = "header">
		<td>&nbsp;Error</td>
		</tr>
		<tr align="LEFT">
		<td width="300">
		<?php
			foreach ($errorMessages as $error) {
				print($error);
			}
		?>
		</td>
		</tr>
		</table>
<?php
	}

// Finally, destroy the session.
session_destroy();
?>

</body>
</html>

<?php
	function obscureNumber( $ccnum )
	{
		if ($ccnum == "")
		{
			return "";
		}
		else
		{

			$begIndex = 4;
			$endIndex = strlen($ccnum) - 5;
			for ($varIndex = $begIndex; $varIndex <= $endIndex; $varIndex++)
			{
				$ccnum[$varIndex] = '*';
			}
			return $ccnum;
		}
	}

?>

