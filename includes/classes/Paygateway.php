<?php
	// Copyright 2006, Payment Processing Inc

	/**
	 *	@version Payment API for PHP v3.0.0
	 *
	 */

	/**
	 *	@package Paygateway
	 */

	require_once(DIR_CLASSES."CountryCodes.php");
	require_once(DIR_CLASSES."AchRequest.php");
	require_once(DIR_CLASSES."AchResponse.php");
	require_once(DIR_CLASSES."AchBatchRequest.php");
	require_once(DIR_CLASSES."AchBatchResponse.php");
	require_once(DIR_CLASSES."AuthenticationRequest.php");
	require_once(DIR_CLASSES."AuthenticationResponse.php");
	require_once(DIR_CLASSES."RecurringRequest.php");
	require_once(DIR_CLASSES."RecurringResponse.php");

	// Constants
	define("VERSION", "PHP Plug v3.0.0");
	define("PROT_VERSION", "3");
	define("PROTOCOL_VERSION", "protocol_version");
	define("PARAMETER_SEPARATOR", "&");
	define("POST_URL", "https://etrans.paygateway.com/TransactionManager");

	// Credit card charge type constants
	define("AUTH", "AUTH");
	define("CAPTURE", "CAPTURE");
	define("SALE", "SALE");
	define("VOID", "VOID");
	define("CREDIT", "CREDIT");
	define("DEBIT", "DEBIT");
	define("QUERY", "QUERY");
	define("VOID_AUTH", "VOID_AUTH");
	define("VOID_CAPTURE", "VOID_CAPTURE");
	define("VOID_CREDIT", "VOID_CREDIT");
	define("CREATE_ORDER", "CREATE_ORDER");
	define("CANCEL_ORDER", "CANCEL_ORDER");
	define("CLOSE_ORDER", "CLOSE_ORDER");
	define("ADJUSTMENT", "ADJUSTMENT");
	define("FORCE_AUTH", "FORCE_AUTH");
	define("FORCE_SALE", "FORCE_SALE");
	define("QUERY_PAYMENT", "QUERY_PAYMENT");
	define("QUERY_CREDIT", "QUERY_CREDIT");

	// Batch request action constants
	define("SETTLE", "SETTLE");
	define("PURGE", "PURGE");
	define("TOTALS", "TOTALS");

	// Credit card brands
	define("VISA", "VISA");
	define("MASTERCARD", "MASTERCARD");
	define("DISCOVER", "DISCOVER");
	define("NOVA", "NOVA");
	define("AMEX", "AMEX");
	define("DINERS", "DINERS");
	define("EUROCARD", "EUROCARD");

	// Response Codes
	define("RC_SUCCESSFUL_TRANSACTION", 1);
	define("RC_CREDIT_CARD_DECLINED", 100);
	define("RC_TRANSACTION_NOT_POSSIBLE", 6);
	define("RC_ILLEGAL_TRANSACTION_REQUEST", 4);
	define("RC_MISSING_REQUIRED_REQUEST_FIELD", 2);
	define("RC_MISSING_REQUIRED_RESPONSE_FIELD", 8);
	define("RC_INVALID_REQUEST_FIELD", 3);
	define("RC_INVALID_RESPONSE_FIELD", 9);
	define("RC_TRANSACTION_CLIENT_ERROR", 10);
	define("RC_PAYMENT_ENGINE_ERROR", 102);
	define("RC_ACQUIRER_GATEWAY_ERROR", 101);
	define("RC_TRANSACTION_SERVER_ERROR", 5);
	define("RC_INVALID_VERSION", 7);

	// Constants for credit card post string keys
	define("ACCOUNT_TOKEN", "account_token");
	define("VERSION_ID", "version_id");
	define("TRANSACTION_TYPE", "transaction_type");
	define("CREDIT_CARD_NUMBER", "credit_card_number");
	define("EXPIRE_MONTH", "expire_month");
	define("EXPIRE_YEAR", "expire_year");
	define("CREDIT_CARD_VERIFICATION_NUMBER", "credit_card_verification_number");
	define("ECOMMERCE_INDICATOR", "ecommerce_indicator");
	define("CHARGE_TYPE", "charge_type");
	define("CURRENCY", "currency");
	define("CHARGE_TOTAL", "charge_total");
	define("CARD_BRAND", "card_brand");
	define("ORDER_ID", "order_id");
	define("REFERENCE_ID", "capture_reference_id");
	define("CC_REFERENCE_ID", "reference_id");
	define("ORDER_DESCRIPTION", "order_description");
	define("ORDER_USER_ID", "order_user_id");
	define("TAX_AMOUNT", "tax_amount");
	define("SHIPPING_CHARGE", "shipping_charge");
	define("CARTRIDGE_TYPE", "cartridge_type");
	define("PO_NUMBER", "purchase_order_number");
	define("TRANSACTION_CONDITION_CODE", "transaction_condition_code");
	define("CAVV", "cavv");
	define("XID", "x_id");
	define("CUSTOMER_IP_ADDRESS", "customer_ip_address");
	define("ORDER_CUSTOMER_ID", "order_customer_id");
	define("STATE_TAX", "state_tax");
	define("TRACK1", "track1");
	define("TRACK2", "track2");
	define("TAX_EXEMPT", "tax_exempt");
	define("BILL_FIRST_NAME", "bill_first_name");
	define("BILL_MIDDLE_NAME", "bill_middle_name");
	define("BILL_LAST_NAME", "bill_last_name");
	define("BILL_CUSTOMER_TITLE", "bill_customer_title");
	define("BILL_COMPANY", "bill_company");
	define("BILL_ADDRESS_ONE", "bill_address_one");
	define("BILL_ADDRESS_TWO", "bill_address_two");
	define("BILL_CITY", "bill_city");
	define("BILL_STATE_OR_PROVINCE", "bill_state_or_province");
	define("BILL_ZIP_OR_POSTAL_CODE", "bill_postal_code");
	define("BILL_COUNTRY_CODE", "bill_country_code");
	define("BILL_EMAIL", "bill_email");
	define("BILL_PHONE", "bill_phone");
	define("BILL_FAX", "bill_fax");
	define("BILL_NOTE", "bill_note");
	define("SHIP_FIRST_NAME", "ship_first_name");
	define("SHIP_MIDDLE_NAME", "ship_middle_name");
	define("SHIP_LAST_NAME", "ship_last_name");
	define("SHIP_CUSTOMER_TITLE", "ship_customer_title");
	define("SHIP_COMPANY", "ship_company");
	define("SHIP_ADDRESS_ONE", "ship_address_one");
	define("SHIP_ADDRESS_TWO", "ship_address_two");
	define("SHIP_CITY", "ship_city");
	define("SHIP_STATE_OR_PROVINCE", "ship_state_or_province");
	define("SHIP_ZIP_OR_POSTAL_CODE", "ship_postal_code");
	define("SHIP_COUNTRY_CODE", "ship_country_code");
	define("SHIP_EMAIL", "ship_email");
	define("SHIP_PHONE", "ship_phone");
	define("SHIP_FAX", "ship_fax");
	define("SHIP_NOTE", "ship_note");
	define("BUYER_CODE", "buyer_code");
	define("INVOICE_NUMBER", "invoice_number");
	define("DUPLICATE_CHECK", "duplicate_check");
	define("BANK_APPROVAL_CODE", "bank_approval_code");
	

	// Constants for credit card post string values
	define("CREDIT_CARD", "CREDIT_CARD");
	define("BATCH", "BATCH");
	define("RECURRING", "RECURRING");
	define("AUTHENTICATION", "AUTHENTICATION");
	define("ACH", "ACH");
	define("ACH_BATCH", "ACH_BATCH");
	
	// Duplicate check values
	define("CHECK", "CHECK");
	define("OVERRIDE", "OVERRIDE");
	define("NO_CHECK", "NO_CHECK");

	// Constants for batch post string keys
	//define("ACTION", "action");
	//define("BATCH_ID", "batch_id");  // same name as response field. share constant.
	//define("VERSION_ID", "version_id");

	// Constants for Authentication Requests
	define("AUTHENTICATION_ACTION", "action");
	define("MAF_PASSWORD",  "maf_password");
	//define("ORDER_ID",  "order_id");
	//define("ORDER_DESCRIPTION",  "order_description");
	//define("CHARGE_TOTAL",  "charge_total");
	//define("CREDIT_CARD_NUMBER",  "credit_card_number");
	//define("EXPIRE_MONTH",  "expire_month");
	//define("EXPIRE_YEAR",  "expire_year");
	define("BROWSER_HEADER",  "browser_header");
	define("USER_AGENT",  "user_agent");
	define("IS_RECURRING",  "is_recurring");
	define("RECURRING_PERIOD",  "recurrence_period");
	define("RECURRING_END_DAY", "recurrence_end_day");
	define("RECURRING_END_MONTH",  "recurrence_end_month");
	define("RECURRING_END_YEAR",  "recurrence_end_year");
	define("INSTALLMENT",  "installment");
	//define("AUTHENTICATION_TRANSACTION_ID",  "authentication_transaction_id");
	//define("AUTHENTICATION_PAYLOAD",  "authentication_payload");
	define("LOOKUP", "LOOKUP");
	define("AUTHENTICATE","AUTHENTICATE");
	//define("PERIOD_WEEKLY", 1);
	//define("PERIOD_BIWEEKLY", 2);
	//define("PERIOD_SEMIMONTHLY", 3);
	//define("PERIOD_MONTHLY", 4);
	//define("PERIOD_QUARTERLY", 5);
	//define("PERIOD_ANNUAL", 6);

	// Constants common to all responses
	define("RESPONSE_CODE", "response_code");
	define("SECONDARY_RESPONSE_CODE", "secondary_response_code");
	define("RESPONSE_CODE_TEXT", "response_code_text");
	define("TIME_STAMP", "time_stamp");
	define("RETRY_RECOMMENDED", "retry_recommended");

	// Constants for credit card response fields
	//define("REFERENCE_ID", "capture_reference_id");  	// duplicated from input field
	//define("ORDER_ID", "order_id");			// duplicated from input field
	define("ISO_CODE", "iso_code");
	//define("BANK_APPROVAL_CODE", "bank_approval_code");
	define("BANK_TRANSACTION_ID", "bank_transaction_id");
	//define("BATCH_ID", "batch_id");
	define("AVS_CODE", "avs_code");
	define("CREDIT_CARD_VERIFICATION_RESPONSE", "credit_card_verification_response");
	define("STATE", "state");
	define("AUTHORIZED_AMOUNT", "authorized_amount");
	define("ORIGINAL_AUTHORIZED_AMOUNT", "original_authorized_amount");
	define("CAPTURED_AMOUNT", "captured_amount");
	define("CREDITED_AMOUNT", "credited_amount");
	define("TIME_STAMP_CREATED", "time_stamp_created");

	// Constants for batch response fields
	//define("BATCH_ID", "batch_id");			// duplicated from ccresp.
	define("PAYMENT_TOTAL", "payment_total");
	//define("CREDIT_TOTAL", "credit_total");
	define("NUMBER_OF_PAYMENTS", "number_of_payments");
	//define("NUMBER_OF_CREDITS", "number_of_credits");
	//define("BATCH_STATE", "batch_state");
	//define("BATCH_BALANCE_STATE", "batch_balance_state");

	// Constant for TransactionconditionCode
	define("TCC_DEFAULT" , 0);
	define("TCC_CARDHOLDER_NOT_PRESENT_MAIL_FAX_ORDER" , 1);
	define("TCC_CARDHOLDER_NOT_PRESENT_TELEPHONE_ORDER", 2);
	define("TCC_CARDHOLDER_NOT_PRESENT_INSTALLMENT", 3);
	define("TCC_CARDHOLDER_NOT_PRESENT_PAYER_AUTHENTICATION", 4);
	define("TCC_CARDHOLDER_NOT_PRESENT_SECURE_ECOMMERCE", 5);
	define("TCC_CARDHOLDER_NOT_PRESENT_RECURRING_BILLING", 6);
	define("TCC_CARDHOLDER_PRESENT_RETAIL_ORDER", 7);
	define("TCC_CARDHOLDER_PRESENT_RETAIL_ORDER_WITHOUT_SIGNATURE", 8);
	define("TCC_CARDHOLDER_PRESENT_RETAIL_ORDER_KEYED", 9);
	define("TCC_CARDHOLDER_NOT_PRESENT_PAYER_AUTHENTICATION_ATTEMPTED", 10);

	// service fields
	define("FOLIO_NUMBER", "folio_number");
	define("INDUSTRY", "industry");
	define("CHARGE_TOTAL_INCLUDES_RESTAURANT", "charge_total_incl_restaurant");
	define("CHARGE_TOTAL_INCLUDES_GIFTSHOP", "charge_total_incl_giftshop");
	define("CHARGE_TOTAL_INCLUDES_MINIBAR", "charge_total_incl_minibar");
	define("CHARGE_TOTAL_INCLUDES_PHONE", "charge_total_incl_phone");
	define("CHARGE_TOTAL_INCLUDES_LAUNDRY", "charge_total_incl_laundry");
	define("CHARGE_TOTAL_INCLUDES_OTHER", "charge_total_incl_other");
	define("SERVICE_RATE", "service_rate");
	define("SERVICE_START_YEAR", "service_start_year");
	define("SERVICE_START_MONTH", "service_start_month");
	define("SERVICE_START_DAY", "service_start_day");
	define("SERVICE_END_YEAR", "service_end_year");
	define("SERVICE_END_MONTH", "service_end_month");
	define("SERVICE_END_DAY", "service_end_day");
	define("SERVICE_NO_SHOW", "service_no_show");

	// service industry types
	define("DIRECT_MARKETING", "DIRECT_MARKETING");
	define("RETAIL", "RETAIL");
	define("LODGING", "LODGING");
	define("RESTAURANT", "RESTAURANT");

	/**
	 *	@package Paygateway
	 */
	class TransactionRequestBase {
		// Object variables
		var $objPostData = array();
		var $objError    = "";
		var $objCABundle = "";

		function setProperty($argKey, $argValue) {
			$this->objPostData[$argKey] = $argValue;
		}

		function getProperty($argKey) {
			return ($this->objPostData[$argKey]);
		}

		function getPostString() {
			$varPostString = "";

			// Reset array pointer
			reset($this->objPostData);

			// Iterate through all keys and values
			foreach($this->objPostData as $varKey => $varValue) {
					//if ($varKey == 'charge_total') $varValue = '1.00';
				$varPostString .= $varKey . "=" . urlencode($varValue) . PARAMETER_SEPARATOR;
			}

			// Remove trailing ampersand
			$varLastIndex = strlen($varPostString) - 1;
			if($varPostString[$varLastIndex] == PARAMETER_SEPARATOR) {
				$varPostString = substr($varPostString, 0, $varLastIndex);
			}
			return $varPostString;
		}

		function executeTransaction() {
			$this->setVersionID(VERSION);
			$this->setProtocolVersion(PROT_VERSION);
			$postFields = $this->getPostString();

			//echo $postFields . '<br>';
			
			if($curled = curl_init(POST_URL)) {
				curl_setopt($curled, CURLOPT_POST, 1);
				curl_setopt($curled, CURLOPT_POSTFIELDS, $postFields);
				curl_setopt($curled, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curled, CURLOPT_TIMEOUT, 270);  // 4.5 minute timeout
				curl_setopt($curled, CURLOPT_USERAGENT, $this->getVersionID());

				// Check for CA certs settings.
				// Look to see if method set the location
				// or if environment variable set the location
				if( $this->getCABundle() == "" ) {
					if( getenv('CURL_CA_BUNDLE') != "" ) {
						curl_setopt($curled, CURLOPT_CAINFO, getenv('CURL_CA_BUNDLE') );
					}
				} else {
					curl_setopt($curled, CURLOPT_CAINFO, $this->getCABundle() );
				}

				$varResponse = curl_exec($curled);
//die($varResponse);
				if($varResponse == "") {
					$this->setError(curl_error($curled));
					curl_close($curled);
					return false;
				} else {
					curl_close($curled);

					$transType = $this->getTransactionType();

					if( $transType == AUTHENTICATION){

						$txnResponse = new AuthenticationResponse($varResponse);

					}else if( $transType == RECURRING){

						$txnResponse = new RecurringResponse($varResponse);

					}else if ( $transType == ACH) {

						$txnResponse = new ACHResponse($varResponse);
					}else if ( $transType == ACH_BATCH) {

						$txnResponse = new ACHBatchResponse($varResponse);
					}else{

						$txnResponse = new TransactionResponse($varResponse);
					}

					return $txnResponse;
				}
			} else {
				print("ERROR: cURL initialization failed.  Check your cURL/PHP configuration.<br>");
			}
		}

		function setTransactionType($argTransactionType) {
			$result = false;

			if($argTransactionType == BATCH ||
			   $argTransactionType == CREDIT_CARD ||
			   $argTransactionType == AUTHENTICATION ||
			   $argTransactionType == RECURRING ||
			   $argTransactionType == ACH ||
			   $argTransactionType == ACH_BATCH) {
				// Good transaction type
				$this->setProperty(TRANSACTION_TYPE, $argTransactionType);
				$this->clearError();
				$result = true;
			} else {
				// Invalid transaction type
				$this->setError("Invalid transaction type.");
			}
			return $result;


		}

		function getTransactionType() {
			return $this->getProperty(TRANSACTION_TYPE);
		}

		function setVersionID($argVersionID) {
			$this->setProperty(VERSION_ID, $argVersionID);
			$this->clearError();
			return true;
		}
		
		function setProtocolVersion($argProtocolVersion) {
			$this->setProperty(PROTOCOL_VERSION, $argProtocolVersion);
			$this->clearError();
			return true;
		}			

		function getVersionID() {
			return $this->getProperty(VERSION_ID);
		}

		function setError($argError) {
			$this->objError = $argError;
		}

		function getError() {
			return $this->objError;
		}

		function clearError() {
			$this->setError("");
		}

		function setCABundle( $argCABundle ) {
			$this->objCABundle = $argCABundle;
		}

		function getCABundle( ) {
			return $this->objCABundle;
		}

		function setAccountToken($argAccountToken) {
			$this->setProperty(ACCOUNT_TOKEN, $argAccountToken);
			$this->clearError();
			return true;
		}

		function getAccountToken() {
			return $this->getProperty(ACCOUNT_TOKEN);
		}
	}

	/**
	 *	@package Paygateway
	 */
	class TransactionRequest extends TransactionRequestBase {


		function TransactionRequest() {
		}

		function doTransaction() {
			$this->setTransactionType(CREDIT_CARD);
			return $this->executeTransaction();
		}

		function doBatchTransaction() {
			$this->setTransactionType(BATCH);
			return $this->executeTransaction();
		}

		/**
		*	- Payer Authentication transaction only.
		*
		*	@param string $argAuthenticationTransactionID
		*/
		function setAuthenticationTransactionID($argAuthenticationTransactionID)  {
			$this->setProperty(AUTHENTICATION_TRANSACTION_ID, $argAuthenticationTransactionID);
			$this->clearError();
			return true;
		}

		/**
		*	- Payer Authentication transaction only.
		*
		*	@param string $argAuthenticationPayload
		*/
		function setAuthenticationPayload($argAuthenticationPayload)  {
			$this->setProperty(AUTHENTICATION_PAYLOAD, $argAuthenticationPayload);
			$this->clearError();
			return true;
		}

		/**
		*	- Payer Authentication transaction only.
		*	- if true, will process the transaction even if the authentication status is inconclusive.
		*
		*	@param bool $argSuccessOnAuthenticationInconclusive
		*/
		function setDoTransactionOnAuthenticationInconclusive($argSuccessOnAuthenticationInconclusive)  {
			if(true == $argSuccessOnAuthenticationInconclusive){
				$this->setProperty(AUTHENTICATION_INCONCLUSIVE, "true");
			}else{
				$this->setProperty(AUTHENTICATION_INCONCLUSIVE, "false");
			}
			$this->clearError();
			return true;
		}

		function setBillAddressOne($argBillAddressOne)  {
			$this->setProperty(BILL_ADDRESS_ONE, $argBillAddressOne);
			$this->clearError();
			return true;
		}

		function setBillAddressTwo($argBillAddressTwo) {
			$this->setProperty(BILL_ADDRESS_TWO, $argBillAddressTwo);
			$this->clearError();
			return true;
		}

		function setBillCity($argBillCity) {
			$this->setProperty(BILL_CITY, $argBillCity);
			$this->clearError();
			return true;
		}

		function setBillCompany($argBillCompany) {
			$this->setProperty(BILL_COMPANY, $argBillCompany);
			$this->clearError();
			return true;
		}

		/**
		*	- Accept only 2 characters country code
		*
		*	@param string $argBillCountryCode
		*/
		function setBillCountryCode($argBillCountryCode) {
			$result = false;

			if (strlen($argBillCountryCode) == 2) {
				// Valid code
				$this->setProperty(BILL_COUNTRY_CODE, $argBillCountryCode);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid bill country code.");
			}

			return $result;
		}

		function setBillCustomerTitle($argBillCustomerTitle) {
			$this->setProperty(BILL_CUSTOMER_TITLE, $argBillCustomerTitle);
			$this->clearError();
			return true;
		}

		function setBillEmail($argBillEmail) {
			$this->setProperty(BILL_EMAIL, $argBillEmail);
			$this->clearError();
			return true;
		}

		function setBillFax($argBillFax) {
			$this->setProperty(BILL_FAX, $argBillFax);
			$this->clearError();
			return true;
		}

		function setBillFirstName($argBillFirstName) {
			$this->setProperty(BILL_FIRST_NAME, $argBillFirstName);
			$this->clearError();
			return true;
		}

		function setBillLastName($argBillLastName) {
			$this->setProperty(BILL_LAST_NAME, $argBillLastName);
			$this->clearError();
			return true;
		}

		function setBillMiddleName($argBillMiddleName) {
			$this->setProperty(BILL_MIDDLE_NAME, $argBillMiddleName);
			$this->clearError();
			return true;
		}

		function setBillNote($argBillNote) {
			$this->setProperty(BILL_NOTE, $argBillNote);
			$this->clearError();
			return true;
		}

		function setBillPhone($argBillPhone) {
			$this->setProperty(BILL_PHONE, $argBillPhone);
			$this->clearError();
			return true;
		}

		function setBillZipOrPostalCode($argBillPostalCode) {
			$this->setProperty(BILL_ZIP_OR_POSTAL_CODE, $argBillPostalCode);
			$this->clearError();
			return true;
		}

		function setBillStateOrProvince($argBillStateOrProvince) {
			$this->setProperty(BILL_STATE_OR_PROVINCE, $argBillStateOrProvince);
			$this->clearError();
			return true;
		}

		function setReferenceID($argReferenceID) {
			$this->setProperty(CC_REFERENCE_ID, $argReferenceID);
			$this->clearError();
			return true;
		}

		/**
		 * @deprecated Card Brand is now determined by the credit card number
		 */
		function setCardBrand($argCardBrand) {
			$result = false;

			if ($argCardBrand == VISA       ||
				$argCardBrand == MASTERCARD ||
				$argCardBrand == DISCOVER   ||
				$argCardBrand == NOVA       ||
				$argCardBrand == AMEX       ||
				$argCardBrand == DINERS     ||
				$argCardBrand == EUROCARD) {
				// Valid
				$this->setProperty(CARD_BRAND, $argCardBrand);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid card brand.");
			}
			return $result;
		}

		function setCartridgeType($argCartridgeType) {
			$this->setProperty(CARTRIDGE_TYPE, $argCartridgeType);
			$this->clearError();
			return true;
		}

		function setCAVV($CAVV) {
			$this->setProperty(CAVV, $CAVV);
			$this->clearError();
			return true;
		}

		/**
		*	- Numeric format :  "1000.00"
		*
		*	@param numeric $argChargeTotal
		*/
		function setChargeTotal($argChargeTotal) {
			$result = false;

			if(is_numeric($argChargeTotal)) {
				// Valid
				$this->setProperty(CHARGE_TOTAL, $argChargeTotal);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid charge total");
			}
			return $result;
		}

		/**
		*	Only these values are allowed:
		*		- AUTH
		*		- CAPTURE
		*		- SALE
		*		- VOID
		*		- CREDIT
		*		- DEBIT
		*		- QUERY
		*		- VOID_AUTH
		*		- VOID_CAPTURE
		*		- VOID_CREDIT
		*		- CREATE_ORDER
		*		- CANCEL_ORDER
		*		- CLOSE_ORDER
		*		- ADJUSTMENT
		*		- FORCE_AUTH
		*		- FORCE_SALE
		*		- QUERY_PAYMENT
		*		- QUERY_CREDIT
		*		- SETTLE
		*		- PURGE
		*		- TOTALS
		*
		*	@param string $argChargeType
		*/
		function setChargeType($argChargeType) {
			$result = false;

			if($argChargeType == AUTH          ||
			   $argChargeType == CAPTURE       ||
			   $argChargeType == SALE          ||
			   $argChargeType == VOID          ||
			   $argChargeType == DEBIT         ||
			   $argChargeType == CREDIT        ||
			   $argChargeType == QUERY         ||
			   $argChargeType == VOID_AUTH     ||
			   $argChargeType == VOID_CAPTURE  ||
			   $argChargeType == VOID_CREDIT   ||
			   $argChargeType == CREATE_ORDER  ||
			   $argChargeType == CANCEL_ORDER  ||
			   $argChargeType == CLOSE_ORDER   ||
			   $argChargeType == ADJUSTMENT    ||
			   $argChargeType == FORCE_AUTH    ||
			   $argChargeType == FORCE_SALE    ||
			   $argChargeType == QUERY_PAYMENT ||
			   $argChargeType == QUERY_CREDIT  ||
			   $argChargeType == SETTLE        ||
			   $argChargeType == PURGE         ||
			   $argChargeType == TOTALS) {
				// Good charge type
				$this->setProperty(CHARGE_TYPE, $argChargeType);
				$this->clearError();
				$result = true;
			} else {
				// Invalid charge type
				$this->setError("Invalid charge type.");
			}
			return $result;
		}

		/**
		*	- Format :  No spaces & no letters  (ex.: 4242424242424242)
		*
		*	@param numeric $argCreditCardNumber
		*/
		function setCreditCardNumber($argCreditCardNumber) {
			$result = false;

			// one or more digit,
			// no spaces,
			// no letters
			if(is_numeric($argCreditCardNumber)) {
				// Valid
				$this->setProperty(CREDIT_CARD_NUMBER, $argCreditCardNumber);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid credit card number");
			}

			return $result;
		}

		function setCreditCardVerificationNumber($argCreditCardVerificationNumber) {
			$this->setProperty(CREDIT_CARD_VERIFICATION_NUMBER, $argCreditCardVerificationNumber);
			$this->clearError();
			return true;
		}

		/**
		 * @deprecated
		 */
		function setCurrency($argCurrency) {
			$result = false;

			if (strlen($argCurrency) == 3 && is_numeric($argCurrency)) {
				// Valid
				$this->setProperty(CURRENCY, $argCurrency);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid currency code");
			}

			return $result;
		}

		function setCustomerIPAddress($argCustomerIPAddress) {
			$this->setProperty(CUSTOMER_IP_ADDRESS, $argCustomerIPAddress);
			$this->clearError();
			return true;
		}

		/**
		 * @deprecated Use setTransactionConditionCode instead
		 */
		function setEcommerceIndicator($argEcommerceIndicator) {
			$this->setProperty(ECOMMERCE_INDICATOR, $argEcommerceIndicator);
			$this->clearError();
			return true;
		}

		/**
		*	- Format :  2 digits (ex.: February = "02")
		*
		*	@param numeric $argExpireMonth
		*/
		function setExpireMonth($argExpireMonth) {
			$result = false;

			if ((strlen($argExpireMonth) == 1 ||
				strlen($argExpireMonth) == 2) &&
				is_numeric($argExpireMonth) &&
				settype($argExpireMonth, "integer") &&
				$argExpireMonth > 0 &&
				$argExpireMonth < 13) {
				// Valid
				$this->setProperty(EXPIRE_MONTH, $argExpireMonth);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid expire month");
			}

			return $result;
		}

		/**
		*	- Format :  4 digits (ex.: "2006")
		*
		*	@param numeric $argExpireYear
		*/
		function setExpireYear($argExpireYear) {
			$result = false;

			if (strlen($argExpireYear) == 4 &&
				is_numeric($argExpireYear)) {
				// Valid
				$this->setProperty(EXPIRE_YEAR, $argExpireYear);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid expire year");
			}

			return $result;
		}

		function setOrderCustomerID($argOrderCustomerID) {
			$this->setProperty(ORDER_CUSTOMER_ID, $argOrderCustomerID);
			$this->clearError();
			return true;
		}

		function setOrderDescription($argOrderDescription) {
			$this->setProperty(ORDER_DESCRIPTION, $argOrderDescription);
			$this->clearError();
			return true;
		}

		function setOrderID($argOrderID) {
			$this->setProperty(ORDER_ID, $argOrderID);
			$this->clearError();
			return true;
		}

		function setOrderUserID($argOrderUserID) {
			$this->setProperty(ORDER_USER_ID, $argOrderUserID);
			$this->clearError();
			return true;
		}

		function setPurchaseOrderNumber($argPurchaseOrderNumber) {
			$this->setProperty(PO_NUMBER, $argPurchaseOrderNumber);
			$this->clearError();
			return true;
		}

		function setShipAddressOne($argShipAddressOne) {
			$this->setProperty(SHIP_ADDRESS_ONE, $argShipAddressOne);
			$this->clearError();
			return true;
		}

		function setShipAddressTwo($argShipAddressTwo) {
			$this->setProperty(SHIP_ADDRESS_TWO, $argShipAddressTwo);
			$this->clearError();
			return true;
		}

		function setShipCity($argShipCity) {
			$this->setProperty(SHIP_CITY, $argShipCity);
			$this->clearError();
			return true;
		}

		function setShipCompany($argShipCompany) {
			$this->setProperty(SHIP_COMPANY, $argShipCompany);
			$this->clearError();
			return true;
		}

		/**
		*	- Accept only 2 characters country code (ex.: Canada = "CA")
		*
		*	@param string $argShipCountryCode
		*/
		function setShipCountryCode($argShipCountryCode) {
			$result = false;

			if (strlen($argShipCountryCode) == 2) {
				// Valid code
				$this->setProperty(SHIP_COUNTRY_CODE, $argShipCountryCode);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid ship country code.");
			}

			return $result;
		}

		function setShipCustomerTitle($argShipCustomerTitle) {
			$this->setProperty(SHIP_CUSTOMER_TITLE, $argShipCustomerTitle);
			$this->clearError();
			return true;
		}

		function setShipEmail($argShipEmail) {
			$this->setProperty(SHIP_EMAIL, $argShipEmail);
			$this->clearError();
			return true;
		}

		function setShipFax($argShipFax) {
			$this->setProperty(SHIP_FAX, $argShipFax);
			$this->clearError();
			return true;
		}

		function setShipFirstName($argShipFirstName) {
			$this->setProperty(SHIP_FIRST_NAME, $argShipFirstName);
			$this->clearError();
			return true;
		}

		function setShipLastName($argShipLastName) {
			$this->setProperty(SHIP_LAST_NAME, $argShipLastName);
			$this->clearError();
			return true;
		}

		function setShipMiddleName($argShipMiddleName) {
			$this->setProperty(SHIP_MIDDLE_NAME, $argShipMiddleName);
			$this->clearError();
			return true;
		}

		function setShipNote($argShipNote) {
			$this->setProperty(SHIP_NOTE, $argShipNote);
			$this->clearError();
			return true;
		}

		function setShipPhone($argShipPhone) {
			$this->setProperty(SHIP_PHONE, $argShipPhone);
			$this->clearError();
			return true;
		}


		function setShippingCharge($argShippingCharge) {
			$result = false;

			if(is_numeric($argShippingCharge)) {
				$this->setProperty(SHIPPING_CHARGE, $argShippingCharge);
				$this->clearError();
				$result = true;
			} else {
				$this->setError("Invalid shipping charge");
			}

			return $result;
		}

		function setShipStateOrProvince($argShipStateOrProvince) {
			$this->setProperty(SHIP_STATE_OR_PROVINCE, $argShipStateOrProvince);
			$this->clearError();
			return true;
		}

		function setShipZipOrPostalCode($argShipZipOrPostalCode) {
			$this->setProperty(SHIP_ZIP_OR_POSTAL_CODE, $argShipZipOrPostalCode);
			$this->clearError();
			return true;
		}

		/**
		*	- Numeric format :  "1000.00"
		*
		*	@param numeric $argStateTax
		*/
		function setStateTax($argStateTax) {
			$result = false;

			if(is_numeric($argStateTax)) {
				$this->setProperty(STATE_TAX, $argStateTax);
				$this->clearError();
				$result = true;
			} else {
				$this->setError("Invalid state tax amount");
			}

			return $result;
		}

		/**
		*	- Numeric format :  "1000.00"
		*
		*	@param numeric $argTaxAmount
		*/
		function setTaxAmount($argTaxAmount) {
			$result = false;

			if(is_numeric($argTaxAmount)) {
				$this->setProperty(TAX_AMOUNT, $argTaxAmount);
				$this->clearError();
				$result = true;
			} else {
				$this->setError("Invalid tax amount");
			}

			return $result;
		}

		/**
		*
		*	@param bool $argTaxExempt
		*/
		function setTaxExempt($argTaxExempt) {
			if(true == $argTaxExempt){
				$this->setProperty(TAX_EXEMPT, "true");
			}else{
				$this->setProperty(TAX_EXEMPT, "false");
			}
			$this->clearError();
			return true;
		}

		function setTrack1($argTrack1) {
			$this->setProperty(TRACK1, $argTrack1);
			$this->clearError();
			return true;
		}

		function setTrack2($argTrack2) {
			$this->setProperty(TRACK2, $argTrack2);
			$this->clearError();
			return true;
		}

		/**
		*	Possible values
		*	- TCC_CARDHOLDER_NOT_PRESENT_MAIL_FAX_ORDER = 1
		*	- TCC_CARDHOLDER_NOT_PRESENT_TELEPHONE_ORDER = 2
		*	- TCC_CARDHOLDER_NOT_PRESENT_INSTALLMENT = 3
		*	- TCC_CARDHOLDER_NOT_PRESENT_PAYER_AUTHENTICATION = 4
		*	- TCC_CARDHOLDER_NOT_PRESENT_SECURE_ECOMMERCE = 5
		*	- TCC_CARDHOLDER_NOT_PRESENT_RECURRING_BILLING = 6
		*	- TCC_CARDHOLDER_PRESENT_RETAIL_ORDER = 7
		*	- TCC_CARDHOLDER_PRESENT_RETAIL_ORDER_WITHOUT_SIGNATURE = 8
		*	- TCC_CARDHOLDER_PRESENT_RETAIL_ORDER_KEYED = 9
		*	- TCC_CARDHOLDER_NOT_PRESENT_PAYER_AUTHENTICATION_ATTEMPTED = 10
		*
		*	@param numeric $argTCC
		*/
		function setTransactionConditionCode($argTCC) {
			$result = false;

			if(is_numeric($argTCC)) {
				$this->setProperty(TRANSACTION_CONDITION_CODE, $argTCC);
				$this->clearError();
				$result = true;
			} else {
				$this->setError("Invalid transaction condition code");
			}

			return $result;
		}

		function setXID($XID) {
			$this->setProperty(XID, $XID);
			$this->clearError();
			return true;
		}

		function setInvoiceNumber($argInvoiceNumber) {
			$this->setProperty(INVOICE_NUMBER, $argInvoiceNumber);
			$this->clearError();
			return true;
		}

		function setBuyerCode($argBuyerCode) {
			$this->setProperty(BUYER_CODE, $argBuyerCode);
			$this->clearError();
			return true;
		}

		/**
		*	Possible values
		*     	- SETTLE
		*     	- TOTALS
		*     	- PURGE
		*
		*	@param string $argAction
		*/
		// Batch request setters
		function setAction($argAction) {
			$result = false;

			if ($argAction == SETTLE ||
				$argAction == TOTALS ||
				$argAction == PURGE) {

				$this->setProperty(ACTION, $argAction);
				$this->clearError();
				$result = true;

			} else {
				$this->setError("Invalid batch action");
			}

			return $result;
		}

		function setBatchID($argBatchID) {
			$this->setProperty(BATCH_ID, $argBatchID);
			$this->clearError();
			return true;
		}

		// Recurring request setters
		function setCustomerID($argCustomerID) {
			$this->setProperty(CUSTOMER_ID, $argCustomerID);
			$this->clearError();
			return true;
		}

		// service request setters
		function setFolioNumber($argFolioNumber) {
			$this->setProperty(FOLIO_NUMBER, $argFolioNumber);
			return true;

		}

		/**
		*	Possible values
		*	- DIRECT_MARKETING
		*	- RETAIL
		*	- LODGING
		*	- RESTAURANT
		*
		*	@param string $argIndustry
		*/
		function setIndustry($argIndustry) {
			$result = false;

			if($argIndustry == DIRECT_MARKETING ||
			   $argIndustry == RETAIL ||
			   $argIndustry == LODGING ||
			   $argIndustry == RESTAURANT) {
				// Good industry
				$this->setProperty(INDUSTRY, $argIndustry);
				$this->clearError();
				$result = true;
			} else {
				// Invalid industry
				$this->setError("Invalid industry.");
			}
			return $result;

		}

		function setChargeTotalIncludesRestaurant($arg) {
			$arg = strtolower($arg);
			if ($arg != "true") {
				$arg = "false";
			}
			$this->setProperty(CHARGE_TOTAL_INCLUDES_RESTAURANT, $arg);
			return true;
		}

		function setChargeTotalIncludesGiftshop($arg) {
			$arg = strtolower($arg);
			if ($arg != "true") {
				$arg = "false";
			}
			$this->setProperty(CHARGE_TOTAL_INCLUDES_GIFTSHOP, $arg);
			return true;
		}

		function setChargeTotalIncludesMinibar($arg) {
			$arg = strtolower($arg);
			if ($arg != "true") {
				$arg = "false";
			}
			$this->setProperty(CHARGE_TOTAL_INCLUDES_MINIBAR, $arg);
			return true;
		}

		function setChargeTotalIncludesPhone($arg) {
			$arg = strtolower($arg);
			if ($arg != "true") {
				$arg = "false";
			}
			$this->setProperty(CHARGE_TOTAL_INCLUDES_PHONE, $arg);
			return true;
		}

		function setChargeTotalIncludesLaundry($arg) {
			$arg = strtolower($arg);
			if ($arg != "true") {
				$arg = "false";
			}
			$this->setProperty(CHARGE_TOTAL_INCLUDES_LAUNDRY, $arg);
			return true;
		}

		function setChargeTotalIncludesOther($arg) {
			$arg = strtolower($arg);
			if ($arg != "true") {
				$arg = "false";
			}
			$this->setProperty(CHARGE_TOTAL_INCLUDES_OTHER, $arg);
			return true;
		}

		function setServiceRate($argServiceRate) {

			$result = false;

			if(is_numeric($argServiceRate)) {
				// Valid
				$this->setProperty(SERVICE_RATE, $argServiceRate);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid service rate");
			}
			return $result;
		}

		function setServiceStartYear($argYear) {
			$result = false;

			if (strlen($argYear) == 4 &&
				is_numeric($argYear) &&
				settype($argYear, "integer") &&
				$argYear > 2000 &&
				$argYear < 9999) {
				// Valid
				$this->setProperty(SERVICE_START_YEAR, $argYear);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid service start year");
			}

			return $result;

		}

		function setServiceStartMonth($argMonth) {
			$result = false;

			if ((strlen($argMonth) == 1 ||
				strlen($argMonth) == 2) &&
				is_numeric($argMonth) &&
				settype($argMonth, "integer") &&
				$argMonth > 0 &&
				$argMonth < 13) {
				// Valid
				$this->setProperty(SERVICE_START_MONTH, $argMonth);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid service start month");
			}

			return $result;

		}

		function setServiceStartDay($argDay) {
			$result = false;

			if ((strlen($argDay) == 1 ||
				strlen($argDay) == 2) &&
				is_numeric($argDay) &&
				settype($argDay, "integer") &&
				$argDay > 0 &&
				$argDay < 32) {
				// Valid
				$this->setProperty(SERVICE_START_DAY, $argDay);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid service start day");
			}

			return $result;

		}

		function setServiceEndYear($argYear) {
			$result = false;

			if (strlen($argYear) == 4 &&
				is_numeric($argYear) &&
				settype($argYear, "integer") &&
				$argYear > 2000 &&
				$argYear < 9999) {
				// Valid
				$this->setProperty(SERVICE_END_YEAR, $argYear);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid service end year");
			}

			return $result;

		}

		function setServiceEndMonth($argMonth) {
			$result = false;

			if ((strlen($argMonth) == 1 ||
				strlen($argMonth) == 2) &&
				is_numeric($argMonth) &&
				settype($argMonth, "integer") &&
				$argMonth > 0 &&
				$argMonth < 13) {
				// Valid
				$this->setProperty(SERVICE_END_MONTH, $argMonth);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid service end month");
			}

			return $result;

		}

		function setServiceEndDay($argDay) {
			$result = false;

			if ((strlen($argDay) == 1 ||
				strlen($argDay) == 2) &&
				is_numeric($argDay) &&
				settype($argDay, "integer") &&
				$argDay > 0 &&
				$argDay < 32) {
				// Valid
				$this->setProperty(SERVICE_END_DAY, $argDay);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid service end day");
			}

			return $result;

		}


		function setServiceNoShow($arg) {
			$arg = strtolower($arg);
			if ($arg != "true") {
				$arg = "false";
			}
			$this->setProperty(SERVICE_NO_SHOW, $arg);
			return true;
		}
		
		/**
		 * The valid values are:
		 * 
		 * - CHECK
		 * - OVERRIDE
		 * - NO_CHECK
		 * 
		 * @param string $argDuplicateCheck
		 */
		function setDuplicateCheck($argDuplicateCheck) {
			if ($argDuplicateCheck == CHECK ||
				$argDuplicateCheck == OVERRIDE ||
				$argDuplicateCheck == NO_CHECK) {
				$this->setProperty(DUPLICATE_CHECK, $argDuplicateCheck);
				$this->clearError();
				return true;
			} else {
				$this->setError("Invalid duplicate check.");
				return false;
			}
		}

		function setBankApprovalCode($argBankApprovalCode) {
			$this->setProperty(BANK_APPROVAL_CODE, $argBankApprovalCode);
			$this->clearError();
			return true;
		}


		// Credit card request getters

		//Authentication getters
		function getAuthenticationTransactionID()  {
			return $this->getProperty(AUTHENTICATION_TRANSACTION_ID);
		}
		function getAuthenticationPayload()  {
			return $this->getProperty(AUTHENTICATION_PAYLOAD);
		}
		function getDoTransactionOnAuthenticationInconclusive()  {
			return $this->getProperty(AUTHENTICATION_INCONCLUSIVE);
		}

		function getBillAddressOne()  {
			return $this->getProperty(BILL_ADDRESS_ONE);
		}

		function getBillAddressTwo() {
			return $this->getProperty(BILL_ADDRESS_TWO);
		}

		function getBillCity() {
			return $this->getProperty(BILL_CITY);
		}

		function getBillCompany() {
			return $this->getProperty(BILL_COMPANY);
		}

		function getBillCountryCode() {
			return $this->getProperty(BILL_COUNTRY_CODE);
		}

		function getBillCustomerTitle() {
			return $this->getProperty(BILL_CUSTOMER_TITLE);
		}

		function getBillEmail() {
			return $this->getProperty(BILL_EMAIL);
		}

		function getBillFax() {
			return $this->getProperty(BILL_FAX);
		}

		function getBillFirstName() {
			return $this->getProperty(BILL_FIRST_NAME);
		}

		function getBillLastName() {
			return $this->getProperty(BILL_LAST_NAME);
		}

		function getBillMiddleName() {
			return $this->getProperty(BILL_MIDDLE_NAME);
		}

		function getBillNote() {
			return $this->getProperty(BILL_NOTE);
		}

		function getBillPhone() {
			return $this->getProperty(BILL_PHONE);
		}

		function getBillZipOrPostalCode() {
			return $this->getProperty(BILL_ZIP_OR_POSTAL_CODE);
		}

		function getBillStateOrProvince() {
			return $this->getProperty(BILL_STATE_OR_PROVINCE);
		}

		/**
		 * @deprecated
		 */
		function getCardBrand() {
			return $this->getProperty(CARD_BRAND);
		}

		function getCartridgeType() {
			return $this->getProperty(CARTRIDGE_TYPE);
		}

		function getCAVV() {
			return $this->getProperty(CAVV);
		}

		function getChargeTotal() {
			return $this->getProperty(CHARGE_TOTAL);
		}

		function getChargeType() {
			return $this->getProperty(CHARGE_TYPE);
		}

		function getCreditCardNumber() {
			// Mask part of the credit card number
			$ccNumber = $this->getProperty(CREDIT_CARD_NUMBER);
			return substr_replace($ccNumber, str_repeat('*', strlen($ccNumber) - 8), 4, -4);
		}

		function getCreditCardVerificationNumber() {
			return $this->getProperty(CREDIT_CARD_VERIFICATION_NUMBER);
		}

		/**
		 * @deprecated
		 */
		function getCurrency() {
			return $this->getProperty(CURRENCY);
		}

		function getCustomerIPAddress() {
			return $this->getProperty(CUSTOMER_IP_ADDRESS);
		}

		/**
		 * @deprecated Use getTransactionConditionCode instead
		 */
		function getEcommerceIndicator() {
			return $this->getProperty(ECOMMERCE_INDICATOR);
		}

		function getTransactionConditionCode() {
			return $this->getProperty(TRANSACTION_CONDITION_CODE);
		}

		function getExpireMonth() {
			return $this->getProperty(EXPIRE_MONTH);
		}

		function getExpireYear() {
			return $this->getProperty(EXPIRE_YEAR);
		}

		function getOrderCustomerID() {
			return $this->getProperty(ORDER_CUSTOMER_ID);
		}

		function getOrderDescription() {
			return $this->getProperty(ORDER_DESCRIPTION);
		}

		function getOrderID() {
			return $this->getProperty(ORDER_ID);
		}

		function getOrderUserID() {
			return $this->getProperty(ORDER_USER_ID);
		}

		function getPurchaseOrderNumber() {
			return $this->getProperty(PO_NUMBER);
		}

		function getShipAddressOne() {
			return $this->getProperty(SHIP_ADDRESS_ONE);
		}

		function getShipAddressTwo() {
			return $this->getProperty(SHIP_ADDRESS_TWO);
		}

		function getShipCity() {
			return $this->getProperty(SHIP_CITY);
		}

		function getShipCompany() {
			return $this->getProperty(SHIP_COMPANY);
		}

		function getShipCountryCode() {
			return $this->getProperty(SHIP_COUNTRY_CODE);
		}

		function getShipCustomerTitle() {
			return $this->getProperty(SHIP_CUSTOMER_TITLE);
		}

		function getShipEmail() {
			return $this->getProperty(SHIP_EMAIL);
		}

		function getShipFax() {
			return $this->getProperty(SHIP_FAX);
		}

		function getShipFirstName() {
			return $this->getProperty(SHIP_FIRST_NAME);
		}

		function getShipLastName() {
			return $this->getProperty(SHIP_LAST_NAME);
		}

		function getShipMiddleName() {
			return $this->getProperty(SHIP_MIDDLE_NAME);
		}

		function getShipNote() {
			return $this->getProperty(SHIP_NOTE);
		}

		function getShipPhone() {
			return $this->getProperty(SHIP_PHONE);
		}

		function getShippingCharge() {
			return $this->getProperty(SHIPPING_CHARGE);
		}

		function getShipStateOrProvince() {
			return $this->getProperty(SHIP_STATE_OR_PROVINCE);
		}

		function getShipZipOrPostalCode() {
			return $this->getProperty(SHIP_ZIP_OR_POSTAL_CODE);
		}

		function getStateTax() {
			return $this->getProperty(STATE_TAX);
		}

		function getTaxAmount() {
			return $this->getProperty(TAX_AMOUNT);
		}

		function getTaxExempt() {
			return $this->getProperty(TAX_EXEMPT);
		}

		function getTrack1() {
			return $this->getProperty(TRACK1);
		}

		function getTrack2() {
			return $this->getProperty(TRACK2);
		}

		function getXID() {
			return $this->getProperty(XID);
		}

		function getInvoiceNumber() {
			return $this->getProperty(INVOICE_NUMBER);
		}

		function getBuyerCode() {
			return $this->getProperty(BUYER_CODE);
		}

		function getDuplicateCheck() {
			return $this->getProperty(DUPLICATE_CHECK);
		}

		function getBankApprovalCode() {
			return $this->getProperty(BANK_APPROVAL_CODE);
		}

		// Batch request getters
		function getAction() {
			return $this->getProperty(ACTION);
		}

		function getBatchID() {
			return $this->getProperty(BATCH_ID);
		}

		// service request getters

		function getFolioNumber() {
			return $this->getProperty(FOLIO_NUMBER);
		}

		function getIndustry() {
			return $this->getProperty(INDUSTRY);
		}

		function getChargeTotalIncludesRestaurant() {
			return $this->getProperty(CHARGE_TOTAL_INCLUDES_RESTAURANT);
		}

		function getChargeTotalIncludesGiftshop() {
			return $this->getProperty(CHARGE_TOTAL_INCLUDES_GIFTSHOP);
		}

		function getChargeTotalIncludesMinibar() {
			return $this->getProperty(CHARGE_TOTAL_INCLUDES_MINIBAR);
		}

		function getChargeTotalIncludesPhone() {
			return $this->getProperty(CHARGE_TOTAL_INCLUDES_PHONE);
		}

		function getChargeTotalIncludesLaundry() {
			return $this->getProperty(CHARGE_TOTAL_INCLUDES_LAUNDRY);
		}

		function getChargeTotalIncludesOther() {
			return $this->getProperty(CHARGE_TOTAL_INCLUDES_OTHER);
		}

		function getServiceRate() {
			return $this->getProperty(SERVICE_RATE);
		}

		function getServiceStartDay() {
			return $this->getProperty(SERVICE_START_DAY);
		}

		function getServiceStartMonth() {
			return $this->getProperty(SERVICE_START_MONTH);
		}

		function getServiceStartYear() {
			return $this->getProperty(SERVICE_START_YEAR);
		}

		function getServiceEndDay() {
			return $this->getProperty(SERVICE_END_DAY);
		}

		function getServiceEndMonth() {
			return $this->getProperty(SERVICE_END_MONTH);
		}

		function getServiceEndYear() {
			return $this->getProperty(SERVICE_END_YEAR);
		}

		function getServiceNoShow() {
			return $this->getProperty(SERVICE_NO_SHOW);
		}
		
	} // end TransactionRequest

	/**
	 *	@package Paygateway
	 */
	class TransactionResponseBase {
		// Object variables
		var $objResponseFields = array();

		function TransactionResponse($argResponseString) {
			// Parse response string and set hashtable values
			$varResponseLinesArray = explode(chr(10), $argResponseString);

			foreach($varResponseLinesArray as $varElement) {
				$varKeyValueArray = explode("=", $varElement);
				// There may be equal signs in the value, so we
				// must add all the elements after the first one
				// as the value
				$varFirstElement = true;
				$varValue = "";
				$varValueArray = array();
				foreach($varKeyValueArray as $varKeyValueElement) {
					if(!$varFirstElement) {
						$varValueArray[] = $varKeyValueElement;
					}
					$varFirstElement = false;
				}
				$varValue = implode("=", $varValueArray);
				$this->objResponseFields[$varKeyValueArray[0]] = $varValue;
			}
		}

		function setProperty($argKey, $argValue) {
			$this->objResponseFields[$argKey] = $argValue;
		}

		function getProperty($argKey) {
			return $this->objResponseFields[$argKey];
		}

		// Common Response Fields getters
		function getResponseCode() {
			return $this->getProperty(RESPONSE_CODE);
		}

		function getSecondaryResponseCode() {
			return $this->getProperty(SECONDARY_RESPONSE_CODE);
		}

		function getResponseCodeText() {
			return $this->getProperty(RESPONSE_CODE_TEXT);
		}

		function getTimeStamp() {
			return $this->getProperty(TIME_STAMP);
		}

		function getTimeString() {
			$utcTime = $this->getProperty(TIME_STAMP);
			$utcTime = substr($utcTime, 0, strlen($utcTime) - 3);

			return date("l F j, Y H:i:s", $utcTime);
		}

		function getRetryRecommended() {
			return $this->getProperty(RETRY_RECOMMENDED);
		}
	}

	/**
	 *	@package Paygateway
	 */
	class TransactionResponse extends TransactionResponseBase {

		function TransactionResponse($argResponseString){
			parent::TransactionResponse($argResponseString);
		}

		function getBatchID() {
			return $this->getProperty(BATCH_ID);
		}

		// Credit Card Response field getters

		function getReferenceID() {
			return $this->getProperty(REFERENCE_ID);
		}

		function getOrderID() {
			return $this->getProperty(ORDER_ID);
		}

		function getISOCode() {
			return $this->getProperty(ISO_CODE);
		}

		function getBankApprovalCode() {
			return $this->getProperty(BANK_APPROVAL_CODE);
		}

		function getBankTransactionID() {
			return $this->getProperty(BANK_TRANSACTION_ID);
		}

		function getAVSCode() {
			return $this->getProperty(AVS_CODE);
		}

		function getCreditCardVerificationResponse() {
			return $this->getProperty(CREDIT_CARD_VERIFICATION_RESPONSE);
		}
		
		function getState() {
			return $this->getProperty(STATE);
		}
		
		function getAuthorizedAmount() {
			return $this->getProperty(AUTHORIZED_AMOUNT);
		}
		
		function getOriginalAuthorizedAmount() {
			return $this->getProperty(ORIGINAL_AUTHORIZED_AMOUNT);
		}
		
		function getCapturedAmount() {
			return $this->getProperty(CAPTURED_AMOUNT);
		}
		
		function getCreditedAmount() {
			return $this->getProperty(CREDITED_AMOUNT);
		}
		
		function getTimeStampCreated() {
			return $this->getProperty(TIME_STAMP_CREATED);
		}

		// Batch response field getters
		function getPaymentTotal() {
			return $this->getProperty(PAYMENT_TOTAL);
		}

		function getCreditTotal() {
			return $this->getProperty(CREDIT_TOTAL);
		}

		function getNumberOfPayments() {
			return $this->getProperty(NUMBER_OF_PAYMENTS);
		}

		function getNumberOfCredits() {
			return $this->getProperty(NUMBER_OF_CREDITS);
		}

		function getBatchState() {
			return $this->getProperty(BATCH_STATE);
		}

		function getBatchBalanceState() {
			return $this->getProperty(BATCH_BALANCE_STATE);
		}

		function getTransactionConditionCode() {
			return $this->getProperty(TRANSACTION_CONDITION_CODE);
		}

		// PayerAuth Response Field getters
		function getAuthenticationResponseCode() {
			return $this->getProperty(AUTHENTICATION_RESPONSE_CODE);
		}

		function getAuthenticationResponseCodeText() {
			return $this->getProperty(AUTHENTICATION_RESPONSE_CODE_TEXT);
		}

		function getAuthenticationTimeStamp() {
			return $this->getProperty(AUTHENTICATION_TIME_STAMP);
		}

		function getAuthenticationTimeString() {
			$utcTime = $this->getProperty(AUTHENTICATION_TIME_STAMP);
			$utcTime = substr($utcTime, 0, strlen($utcTime) - 3);

			return date("l F j, Y H:i:s", $utcTime);
		}

		function getAuthenticationRetryRecommended() {
			return $this->getProperty(AUTHENTICATION_RETRY_RECOMMENDED);
		}

		function getAuthenticationCAVV() {
			return $this->getProperty(AUTHENTICATION_CAVV);
		}

		function getAuthenticationXID() {
			return $this->getProperty(AUTHENTICATION_X_ID);
		}

		function getAuthenticationStatus() {
			return $this->getProperty(AUTHENTICATION_STATUS);
		}

		function getAuthenticationTransactionConditionCode() {
			return $this->getProperty(AUTHENTICATION_TRANSACTION_CONDITION_CODE);
		}

	} // end TransactionResponse
?>
