<?php
	/**
	 *	@package Paygateway
	 */

	// CreditCard PayerAuth transaction fields
	define("AUTHENTICATION_TRANSACTION_ID", "authentication_transaction_id");
	define("AUTHENTICATION_PAYLOAD", "authentication_payload");
	define("AUTHENTICATION_INCONCLUSIVE", "success_on_authentication_inconclusive");

	// Constant for PayerAuth response fields
	define("AUTHENTICATION_RESPONSE_CODE","authentication_response_code");
	define("AUTHENTICATION_RESPONSE_CODE_TEXT","authentication_response_code_text");
	define("AUTHENTICATION_TIME_STAMP","authentication_time_stamp");
	define("AUTHENTICATION_RETRY_RECOMMENDED","authentication_retry_recommended");
	define("AUTHENTICATION_CAVV","authentication_cavv");
	define("AUTHENTICATION_X_ID","authentication_x_id");
	define("AUTHENTICATION_STATUS","authentication_status");
	define("AUTHENTICATION_TRANSACTION_CONDITION_CODE","authentication_transaction_condition_code");

	//Constant for PayerAuth LOOKUP response fields
	//define("AUTHENTICATION_TRANSACTION_ID",  "authentication_transaction_id");
	define("LOOKUP_PAYLOAD",  "lookup_payload");
	define("HIDDEN_FIELDS",  "hidden_fields");
	//define("ORDER_ID",  "order_id");
	define("STATUS",  "status");
	define("AUTHENTICATION_URL",  "authentication_url");
	define("STATUS_ENROLLED" , "Y");
	define("STATUS_NOT_ENROLLED", "N");
	define("STATUS_ENROLLED_BUT_AUTHENTICATION_UNAVAILABLE", "U");

	/**
	 *	@package Paygateway
	 */
	class AuthenticationRequest extends TransactionRequestBase {

		function doTransaction() {
			$this->setTransactionType(AUTHENTICATION);
			return $this->executeTransaction();
		}

		/**
		*	Possible values
		*     	- AUTHENTICATE
		*     	- LOOKUP
		*
		*	@param string $argAuthenticationAction
		*/
		function setAction($argAuthenticationAction)  {
			$result = false;

			if ($argAuthenticationAction == AUTHENTICATE ||
				$argAuthenticationAction == LOOKUP) {
				// Valid
				$this->setProperty(AUTHENTICATION_ACTION, $argAuthenticationAction);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid Authentication Action");
			}
			return $result;
		}

		function setMafPassword($argMafPassword)  {
			$this->setProperty(MAF_PASSWORD, $argMafPassword);
			$this->clearError();
			return true;
		}

		function setBrowserHeader($argBrowserHeader)  {
			$this->setProperty(BROWSER_HEADER, $argBrowserHeader);
			$this->clearError();
			return true;
		}

		function setUserAgent($argUserAgent)  {
			$this->setProperty(USER_AGENT, $argUserAgent);
			$this->clearError();
			return true;
		}

		/**
		*	Possible values
		*     	- PERIOD_WEEKLY = 1 : every week
		*     	- PERIOD_BIWEEKLY = 2 : every 2 week
		*     	- PERIOD_SEMIMONTHLY = 3 : twice a month
		*     	- PERIOD_MONTHLY = 4 : every month
		*     	- PERIOD_QUARTERLY = 5 : every three month
		*     	- PERIOD_ANNUAL = 6 : every year
		*
		*	@param string $argPeriod
		*/
		function setRecurrencePeriod($argRecurrencePeriod)  {
			$result = false;

			if ($argRecurrencePeriod == PERIOD_WEEKLY ||
				$argRecurrencePeriod == PERIOD_BIWEEKLY ||
				$argRecurrencePeriod == PERIOD_SEMIMONTHLY ||
				$argRecurrencePeriod == PERIOD_MONTHLY ||
				$argRecurrencePeriod == PERIOD_QUARTERLY ||
				$argRecurrencePeriod == PERIOD_ANNUAL) {
				// Valid
				$this->setProperty(RECURRING_PERIOD, $argRecurrencePeriod);
				$this->clearError();
				$result = true;
			} else {
					// Invalid
					$this->setError("Invalid period ");
			}
		}

		function setRecurrenceEndDay($argRecurrenceEndDay)  {
			$this->setProperty(RECURRING_END_DAY, $argRecurrenceEndDay);
			$this->clearError();
			return true;
		}

		function setRecurrenceEndMonth($argRecurrenceEndMonth)  {
			$this->setProperty(RECURRING_END_MONTH, $argRecurrenceEndMonth);
			$this->clearError();
			return true;
		}

		function setRecurrenceEndYear($argRecurrenceEndYear)  {
			$this->setProperty(RECURRING_END_YEAR, $argRecurrenceEndYear);
			$this->clearError();
			return true;
		}

		function setInstallment($argInstallment)  {
			$this->setProperty(INSTALLMENT, $argInstallment);
			$this->clearError();
			return true;
		}

		function setAuthenticationTransactionID($argAuthenticationTransactionID)  {
			$this->setProperty(AUTHENTICATION_TRANSACTION_ID, $argAuthenticationTransactionID);
			$this->clearError();
			return true;
		}

		function setAuthenticationPayload($argAuthenticationPayload)  {
			$this->setProperty(AUTHENTICATION_PAYLOAD, $argAuthenticationPayload);
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
		*	- Format :  No spaces & no letters (ex.: 4242424242424242)
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


		function setRecurring($argRecurring){
			if(true == $argRecurring){
				$this->setProperty(IS_RECURRING, "true");
			}else{
				$this->setProperty(IS_RECURRING, "false");
			}
			$this->clearError();
			return true;
		}


		// Payer Auth request getters
		function getAction() {
			return $this->getProperty(ACTION);
		}
		function isRecurring(){
			return $this->getProperty(IS_RECURRING);
		}
		function getMafPassword()  {
			return $this->getProperty(MAF_PASSWORD);
		}
		function getBrowserHeader()  {
			return $this->getProperty(BROWSER_HEADER);
		}
		function getUserAgent()  {
			return $this->getProperty(USER_AGENT);
		}
		function getRecurrencePeriod()  {
			return $this->getProperty(RECURRING_PERIOD);
		}
		function getRecurrenceEndDay()  {
			return $this->getProperty(RECURRING_END_DAY);
		}
		function getRecurrenceEndMonth()  {
			return $this->getProperty(RECURRING_END_MONTH);
		}
		function getRecurrenceEndYear()  {
			return $this->getProperty(RECURRING_END_YEAR);
		}
		function getInstallment()  {
			return $this->getProperty(INSTALLMENT);
		}
		function getAuthenticationTransactionID()  {
			return $this->getProperty(AUTHENTICATION_TRANSACTION_ID);
		}

		function getAuthenticationPayload()  {
			return $this->getProperty(AUTHENTICATION_PAYLOAD);
		}

		function getChargeTotal() {
			return $this->getProperty(CHARGE_TOTAL);
		}

		function getCreditCardNumber() {
			return $this->getProperty(CREDIT_CARD_NUMBER);
		}

		function getExpireMonth() {
			return $this->getProperty(EXPIRE_MONTH);
		}

		function getExpireYear() {
			return $this->getProperty(EXPIRE_YEAR);
		}


		function getOrderDescription() {
			return $this->getProperty(ORDER_DESCRIPTION);
		}

		function getOrderID() {
			return $this->getProperty(ORDER_ID);
		}

	}
?>
