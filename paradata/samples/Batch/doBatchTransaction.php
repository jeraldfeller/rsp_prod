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

	$batchRequest = new TransactionRequest();

	$batchRequest->setAccountToken(TEST_TOKEN);

	// Populate request with data from web form
	$action = $HTTP_POST_VARS["action"];
	if($action != "") {
		if(!$batchRequest->setAction($action)) {
			$errorMessages[] = $batchRequest->getError();
		}
	}

	$batchId = $HTTP_POST_VARS["batch_id"];
	if($batchId != "") {
		if(!$batchRequest->setBatchId($batchId)) {
			$errorMessages[] = $batchRequest->getError();
		}
	}


	if(sizeof($errorMessages) != 0) {
		// Print out all the errors that happened when setting.
		print("<h1><font color='red'>Test Transaction Not Attempted</font></h1>");
		print("<p>There was an error setting the fields of the TransactionRequest object.");
		print("The following errors were found:");
		print("<ul>");

		foreach ($errorMessages as $error) {
			print("   <li>$error</li>");
		}

		print("</ul>");

	} else {
		// No errors setting the values; perform the transaction
		$batchResponse = $batchRequest->doBatchTransaction();

		// If there was a communication failure, then the response
		// object will be false.
		if($batchResponse) {
			print("<br>");
			print("<table align='center' border = '0' cellspacing = '0' cellpadding = '0'>");
			print("  <tr>");
			print("    <td width='200' align='left' valign='top'>&nbsp;</td>");
			print("    <td align='center' valign='top'><h3><u>Transaction Results</u></h3></td>");
			print("    <td width='200' align='right' valign='top'><a href='demobatchpaypage.html' class = 'header'><strong>Enter New Payment</strong></a></td>");
			print("  </tr>");
			print("</table>");
			print("<hr>");
			print("<center>");
			print("  <TABLE CELLPADDING='2' CELLSPACING='2' BORDER='0' width='500'>");
			print("    <tr class = 'header'>");
			print("      <td colspan=2>&nbsp;Response Fields</td>");
			print("    </tr>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Response Code:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->GetResponseCode() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Response Text:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->GetResponseCodeText() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Timestamp:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->GetTimeStamp() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Payment Total:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->getPaymentTotal() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Credit Total:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->getCreditTotal() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Number Of Payments:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->getNumberOfPayments() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Number Of Credits:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->getNumberOfCredits() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Batch ID:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->getBatchID() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT'>");
			print("      <TH align='right' width='200' valign='top'>Batch State:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->getBatchState() . "</TD>");
			print("    </TR>");
			print("    <TR ALIGN='LEFT' bgcolor='#EEEEEE'>");
			print("      <TH align='right' width='200' valign='top'>Batch Balance State:</TH>");
			print("      <TD width='300'>&nbsp;" . $batchResponse->getBatchBalanceState() . "</TD>");
			print("    </TR>");
			print("  </TABLE>");
			print("  <BR>");
			print("  <HR>");
			print("</center>");
		} else {
			print("<h1><font color='red'>Test Transaction Failed</font></h1>A communication error occurred.");
			print("<p>Error: ". $batchRequest->getError());
		}
	}
?>

</body>
</html>

