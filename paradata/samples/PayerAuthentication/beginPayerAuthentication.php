<?
	session_start();

	$ACCOUNT_TOKEN = "195325FCC230184964CAB3A8D93EEB31888C42C714E39CBBB2E541884485D04B";
	$TRANSACTION_URL = "http://127.0.0.1/PayerAuthentication/doTransaction.php";

?>

<html>
<head>
<title>Transaction in progress...</title>
<link rel="STYLESHEET" type="text/css" href="main.css">
</head>
<body bgcolor="#FFFFFF" link="#CC0000" text="#000000" topmargin="5" leftmargin="0" onload="load();">

<?
	include("Paygateway.php");
	include("CountryCodes.php");

	$errorMessages = array();

	$userAgent = $_SERVER['HTTP_USER_AGENT'];

	// Acquiring posted variables
	$buyerCode = $_POST["buyer_code"];
	$invoiceNumber = $_POST["invoice_number"];
	$chargeTotal = $_POST["charge_total"];
	$chargeType = $_POST["charge_type"];
	$creditCardNumber = $_POST["credit_card_number"];
	$cvv = $_POST["cvv"];
	$tcc = $_POST["transaction_condition_code"];
	$expireMonth = $_POST["expire_month"];
	$expireYear = $_POST["expire_year"];
	$orderDescription = $_POST["order_description"];
	$orderID = $_POST["order_id"];
	$orderUserID = $_POST["order_user_id"];
	$shippingCharge = $_POST["shipping_charge"];
	$taxAmount = $_POST["tax_amount"];

	$billAddressOne = $_POST["bill_address_one"];
	$billAddressTwo = $_POST["bill_address_two"];
	$billCity = $_POST["bill_city"];
	$billCompany = $_POST["bill_company"];
	$billCountryCode = $_POST["bill_country_code"];
	$billCustomerTitle = $_POST["bill_customer_title"];
	$billEmail = $_POST["bill_email"];
	$billFirstName = $_POST["bill_first_name"];
	$billLastName = $_POST["bill_last_name"];
	$billMiddleName = $_POST["bill_middle_name"];
	$billNote = $_POST["bill_note"];
	$billPhone = $_POST["bill_phone"];
	$billStateOrProvince = $_POST["bill_state_or_province"];
	$billZipOrPostalCode = $_POST["bill_zip_or_postal_code"];

	$shipAddressOne = $_POST["ship_address_one"];
	$shipAddressTwo = $_POST["ship_address_two"];
	$shipCity = $_POST["ship_city"];
	$shipCompany = $_POST["ship_company"];
	$shipCountryCode = $_POST["ship_country_code"];
	$shipCustomerTitle = $_POST["ship_customer_title"];
	$shipEmail = $_POST["ship_email"];
	$shipFirstName = $_POST["ship_first_name"];
	$shipLastName = $_POST["ship_last_name"];
	$shipMiddleName = $_POST["ship_middle_name"];
	$shipNote = $_POST["ship_note"];
	$shipPhone = $_POST["ship_phone"];
	$shipStateOrProvince = $_POST["ship_state_or_province"];
	$shipZipOrPostalCode = $_POST["ship_zip_or_postal_code"];

	if($_POST["do_transaction_on_authentication_inconclusive"]){
		// A value was passed in for the checkbox
		$doTransactionOnAuthenticationInconclusive = true;
	}else{
		//CheckBox Was Empty
		$doTransactionOnAuthenticationInconclusive = false;
	}

	//Setting the session variables
	$_SESSION["account_token"] = $ACCOUNT_TOKEN;
	$_SESSION["charge_total"] = $chargeTotal;
	$_SESSION["charge_type"] = $chargeType;
	$_SESSION["invoice_number"] = $invoiceNumber;
	$_SESSION["buyer_code"] = $buyerCode;
	$_SESSION["credit_card_number"] = $creditCardNumber;
	$_SESSION["cvv"] = $cvv;
	$_SESSION["transaction_condition_code"] = $tcc;
	$_SESSION["expire_month"] = $expireMonth;
	$_SESSION["expire_year"] = $expireYear;
	$_SESSION["order_description"] = $orderDescription;
	$_SESSION["order_id"] = $orderID;
	$_SESSION["order_user_id"] = $orderUserID;
	$_SESSION["shipping_charge"] = $shippingCharge;
	$_SESSION["tax_amount"] = $taxAmount;

	$_SESSION["bill_address_one"] = $billAddressOne;
	$_SESSION["bill_address_two"] = $billAddressTwo;
	$_SESSION["bill_city"] = $billCity;
	$_SESSION["bill_company"] = $billCompany;
	$_SESSION["bill_country_code"] = $billCountryCode;
	$_SESSION["bill_customer_title"] = $billCustomerTitle;
	$_SESSION["bill_email"] = $billEmail;
	$_SESSION["bill_first_name"] = $billFirstName;
	$_SESSION["bill_last_name"] = $billLastName;
	$_SESSION["bill_middle_name"] = $billMiddleName;
	$_SESSION["bill_note"] = $billNote;
	$_SESSION["bill_phone"] = $billPhone;
	$_SESSION["bill_state_or_province"] = $billStateOrProvince;
	$_SESSION["bill_zip_or_postal_code"] = $billZipOrPostalCode;

	$_SESSION["ship_address_one"] = $shipAddressOne;
	$_SESSION["ship_address_two"] = $shipAddressTwo;
	$_SESSION["ship_city"] = $shipCity;
	$_SESSION["ship_company"] = $shipCompany;
	$_SESSION["ship_country_code"] = $shipCountryCode;
	$_SESSION["ship_customer_title"] = $shipCustomerTitle;
	$_SESSION["ship_email"] = $shipEmail;
	$_SESSION["ship_first_name"] = $shipFirstName;
	$_SESSION["ship_last_name"] = $shipLastName;
	$_SESSION["ship_middle_name"] = $shipMiddleName;
	$_SESSION["ship_note"] = $shipNote;
	$_SESSION["ship_phone"] = $shipPhone;
	$_SESSION["ship_state_or_province"] = $shipStateOrProvince;
	$_SESSION["ship_zip_or_postal_code"] = $shipZipOrPostalCode;

	$_SESSION["do_transaction_on_authentication_inconclusive"] = $doTransactionOnAuthenticationInconclusive;


	$usePayerAuthentication = false;
	$chargeTypeAllowsAuthentication = false;

	if( $chargeType == SALE ||
		$chargeType == AUTH)
	{
		// The cardholder is usually only present during
		// auth and sales.  Other transaction (void, credit,
		// capture) do not require payer authentication.

		$chargeTypeAllowsAuthentication = true;
	}


	if( $chargeTypeAllowsAuthentication )
	{

		$lookupRequest = new AuthenticationRequest();

		$lookupRequest->setAction( LOOKUP );

		if( $ACCOUNT_TOKEN != "" ) {
			if(!$lookupRequest->setAccountToken( $ACCOUNT_TOKEN )){
				$errorMessages[] = $lookupRequest->getError();
			}
		}

		if( $creditCardNumber != "" ) {
			if(!$lookupRequest->setCreditCardNumber( $creditCardNumber )){
				$errorMessages[] = $lookupRequest->getError();
			}
		}
		if( $expireMonth != "" ) {
			if(!$lookupRequest->setExpireMonth( $expireMonth )){
				$errorMessages[] = $lookupRequest->getError();
			}
		}
		if( $expireYear != "" ){
			if(!$lookupRequest->setExpireYear( $expireYear )){
				$errorMessages[] = $lookupRequest->getError();
			}
		}
		if( $chargeTotal != "" ) {
			if(!$lookupRequest->setChargeTotal( $chargeTotal )){
				$errorMessages[] = $lookupRequest->getError();
			}
		}
		if( $orderID != "" ) {
			$lookupRequest->setOrderId( $orderID );
		}
		if( $userAgent != "" ) {
			$lookupRequest->setUserAgent( $userAgent );
		}

		if( sizeof($errorMessages) == 0 ) {

			// The setter methods succeeded on the PayerAuthenticationRequest

			$lookupResponse = $lookupRequest->doTransaction();

			if( $lookupResponse )
			{
				$lookupRespResponseCode = $lookupResponse->getResponseCode();
				$lookupRespResponseCodeText = $lookupResponse->getResponseCodeText();
				$lookupRespOrderID = $lookupResponse->getOrderId();
				$lookupRespTimestamp = $lookupResponse->getTimeStamp();
				$lookupRespAuthenticationTransactionID = $lookupResponse->getAuthenticationTransactionId();
				$lookupRespStatus = $lookupResponse->getStatus();
				$lookupRespLookupPayload = $lookupResponse->getLookupPayload();
				$lookupRespHiddenFields = $lookupResponse->getHiddenFields();
				$lookupRespAuthenticationUrl = $lookupResponse->getAuthenticationUrl();


				// The response contains an order id.
				// If one was not passed in, then we were assigned
				// one and it must be stored for the credit card
				// transaction.
				if( $orderID == "" )
				{
					$orderID = $lookupRespOrderID;
					$_SESSION["order_id"] = $orderID;
				}

				if( RC_SUCCESSFUL_TRANSACTION == $lookupRespResponseCode &&
					STATUS_ENROLLED == $lookupRespStatus )
				{
					// Cardholder is enrolled in Payer Authentication
					$usePayerAuthentication = true;
				}
				else if( $lookupRespResponseCode == RC_ILLEGAL_TRANSACTION_REQUEST)
				{
					// Don't do Payer Authentication
					$usePayerAuthentication = false;
				}
				else
				{
					// Don't do Payer Authentication
					$usePayerAuthentication = false;
				}

				$_SESSION["use_payer_authentication"] = $usePayerAuthentication;
				$_SESSION["authentication_transaction_id"] = $lookupRespAuthenticationTransactionID;

			}
			else
			{
				// You can continue the transaction at this point, although
				// you will not be covered by liability protection
			}

			if( $usePayerAuthentication ) {

				// use inline authentication
				$termURL = $TRANSACTION_URL;
?>
				<script language="Javascript">
					function load() {
						document.frmLaunchAuthentication.submit();
					}
				</script>

				<!--
					This form causes the authentication window to load
					the ACS page using the URL supplied by lookup response
				-->
				<form name="frmLaunchAuthentication" method="POST" action=<?echo $lookupRespAuthenticationUrl;?>>
					<input type="hidden" name="PaReq" value="<?echo $lookupRespLookupPayload;?>">
					<input type="hidden" name="TermUrl" value="<?echo $termURL;?>">
					<input type="hidden" name="MD" value="test">
				</form>

<?
			} else {
				// Not enrolled in payer authentication
				// Continue with normal transaction
?>
				<script language="Javascript">
					function load() {
						document.frmRedirect.submit();
					}
				</script>

				<!-- Not using PayerAuthentication, this will just redirect to next page. -->
				<form name="frmRedirect" method="POST" action="doTransaction.php">
				</form>
<?
			}

		} else {
			// There was an error message from setting the values on the
			// PayerAuthenticationRequest.
?>
			<BR><BR><P><center>
			<H2>Error Creating PayerAuthenticationRequest.</H2>
			<A href="PayPage.html" class = "header"><strong>Enter New Payment</strong></a>
			</center></p>
			<table cellpadding="2" cellspacing="2" border="0" width="500" align = "center">
			<tr class = "header">
			<td>&nbsp;Error</td>
			</tr>
			<tr align="left">
			<td width="300">
			<?
			foreach ($errorMessages as $error) {
				echo $error;
			}
			?>
			</td>
			</tr>
			</table>
<?
		}
	} else {
		// Not using PayerAuthentication because of transaction type
?>
		<script language="Javascript">
			function load() {
				document.frmRedirect.submit();
			}
		</script>

		<!-- Not using PayerAuthentication, this will just redirect to next page. -->
		<form name="frmRedirect" method="POST" action="doTransaction.php">
		</form>
<?
	}
?>
</body>
</html>

<?
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
