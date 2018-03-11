<?php
	/**
	 *	@package Paygateway
	 */

	// Constants for recurring requests
	define("ADD_CUSTOMER", "ADD_CUSTOMER");
	define("ADD_RECURRENCE", "ADD_RECURRENCE");
	define("ADD_CUSTOMER_AND_RECURRENCE", "ADD_CUSTOMER_AND_RECURRENCE");

	define("RECUR_COMMAND", "command");
	define("RECUR_CUSTOMER_ID", "customer_id");
	define("RECUR_ACCOUNT_TYPE", "account_type");
	define("RECUR_CUSTOMER_NAME", "customer_name");
	define("RECUR_EMAIL_ADDRESS", "email_address");
	define("RECUR_CREDIT_CARD_NUMBER", "credit_card_number");
	define("RECUR_EXPIRE_YEAR", "expire_year");
	define("RECUR_EXPIRE_MONTH", "expire_month");
	define("RECUR_BILLING_ADDRESS", "billing_address");
	define("RECUR_POSTAL_CODE", "postal_code");
	define("RECUR_COUNTRY_CODE", "country_code");

	define("RECUR_RECURRENCE_ID", "recurrence_id");
	define("RECUR_DESCRIPTION", "description");
	define("RECUR_CHARGE_TOTAL", "charge_total");
	define("RECUR_NOTIFY_CUSTOMER", "notify_customer");
	define("RECUR_PERIOD", "period");
	define("RECUR_NUMBER_OF_RETRIES", "number_of_retries");
	define("RECUR_START_DAY", "start_day");
	define("RECUR_START_MONTH", "start_month");
	define("RECUR_START_YEAR", "start_year");
	define("RECUR_END_DAY", "end_day");
	define("RECUR_END_MONTH", "end_month");
	define("RECUR_END_YEAR", "end_year");

	define("PERIOD_WEEKLY", 1);
	define("PERIOD_BIWEEKLY", 2);
	define("PERIOD_SEMIMONTHLY", 3);
	define("PERIOD_MONTHLY", 4);
	define("PERIOD_QUARTERLY", 5);
	define("PERIOD_ANNUAL", 6);

	/**
	 *	@package Paygateway
	 */
	class RecurringRequest extends TransactionRequestBase {
		function doTransaction() {
			$this->setTransactionType(RECURRING);
			return $this->executeTransaction();
		}

		function getAccountType() {
			return $this->getProperty(RECUR_ACCOUNT_TYPE);
		}

		function getBillingAddress() {
			return $this->getProperty(RECUR_BILLING_ADDRESS);
		}

		function getChargeTotal() {
			return $this->getProperty(RECUR_CHARGE_TOTAL);
		}

		function getCommand() {
			return $this->getProperty(RECUR_COMMAND);
		}

		function getCountryCode() {
			return $this->getProperty(RECUR_COUNTRY_CODE);
		}

		function getCreditCardNumber() {
			return $this->getProperty(RECUR_CREDIT_CARD_NUMBER);
		}

		function getCustomerID() {
			return $this->getProperty(RECUR_CUSTOMER_ID);
		}

		function getCustomerName() {
			return $this->getProperty(RECUR_CUSTOMER_NAME);
		}

		function getDescription() {
			return $this->getProperty(RECUR_DESCRIPTION);
		}

		function getEmailAddress() {
			return $this->getProperty(RECUR_EMAIL_ADDRESS);
		}

		function getEndDay() {
			return $this->getProperty(RECUR_END_DAY);
		}

		function getEndMonth() {
			return $this->getProperty(RECUR_END_MONTH);
		}

		function getEndYear() {
			return $this->getProperty(RECUR_END_YEAR);
		}

		function getExpireMonth() {
			return $this->getProperty(RECUR_EXPIRE_MONTH);
		}

		function getExpireYear() {
			return $this->getProperty(RECUR_EXPIRE_YEAR);
		}

		function getNumberOfRetries() {
			return $this->getProperty(RECUR_NUMBER_OF_RETRIES);
		}

		function getPeriod() {
			return $this->getProperty(RECUR_PERIOD);
		}

		function getZipOrPostalCode() {
			return $this->getProperty(RECUR_POSTAL_CODE);
		}

		function getRecurrenceID() {
			return $this->getProperty(RECUR_RECURRENCE_ID);
		}

		function getStartDay() {
			return $this->getProperty(RECUR_START_DAY);
		}

		function getStartMonth() {
			return $this->getProperty(RECUR_START_MONTH);
		}

		function getStartYear() {
			return $this->getProperty(RECUR_START_YEAR);
		}

		function getNotifyCustomer() {
			return $this->getProperty(RECUR_NOTIFY_CUSTOMER);
		}

		function setAccountType($argAccountType) {
			$this->setProperty(RECUR_ACCOUNT_TYPE, $argAccountType);
			$this->clearError();
			return true;
		}

		function setBillingAddress($argBillingAddress) {
			$this->setProperty(RECUR_BILLING_ADDRESS, $argBillingAddress);
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
				$this->setProperty(RECUR_CHARGE_TOTAL, $argChargeTotal);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid charge total");
			}
			return $result;
		}

		/**
		*	Possible values
		*     	- ADD_CUSTOMER
		*     	- ADD_RECURRENCE
		*     	- ADD_CUSTOMER_AND_RECURRENCE
		*
		*	@param string $argCommand
		*/
		function setCommand($argCommand) {
			$result = false;

			if(	$argCommand == ADD_CUSTOMER ||
				$argCommand == ADD_RECURRENCE ||
				$argCommand == ADD_CUSTOMER_AND_RECURRENCE) {
				// Valid
				$this->setProperty(RECUR_COMMAND, $argCommand);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid command");
			}
			return $result;
		}

		function setCountryCode($argCountryCode) {
			$this->setProperty(RECUR_COUNTRY_CODE, $argCountryCode);
			$this->clearError();
			return true;
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
				$this->setProperty(RECUR_CREDIT_CARD_NUMBER, $argCreditCardNumber);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid credit card number");
			}

			return $result;
		}

		function setCustomerID($argCustomerID) {
			$this->setProperty(RECUR_CUSTOMER_ID, $argCustomerID);
			$this->clearError();
			return true;
		}

		function setCustomerName($argCustomerName) {
			$this->setProperty(RECUR_CUSTOMER_NAME, $argCustomerName);
			$this->clearError();
			return true;
		}

		function setDescription($argDescription) {
			$this->setProperty(RECUR_DESCRIPTION, $argDescription);
			$this->clearError();
			return true;
		}

		function setEmailAddress($argEmailAddress) {
			$this->setProperty(RECUR_EMAIL_ADDRESS, $argEmailAddress);
			$this->clearError();
			return true;
		}

		function setEndDay($argEndDay) {
			$result = false;

			if ((strlen($argEndDay) == 1 ||
				strlen($argEndDay) == 2) &&
				is_numeric($argEndDay) &&
				settype($argEndDay, "integer") &&
				$argEndDay > 0 &&
				$argEndDay < 32) {
				// Valid
				$this->setProperty(RECUR_END_DAY, $argEndDay);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid end day");
			}

			return $result;
		}

		function setEndMonth($argEndMonth) {
			$result = false;

			if ((strlen($argEndMonth) == 1 ||
				strlen($argEndMonth) == 2) &&
				is_numeric($argEndMonth) &&
				settype($argEndMonth, "integer") &&
				$argEndMonth > 0 &&
				$argEndMonth < 13) {
				// Valid
				$this->setProperty(RECUR_END_MONTH, $argEndMonth);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid end month");
			}

			return $result;
		}

		function setEndYear($argEndYear) {
			$result = false;

			if (strlen($argEndYear) == 4 &&
				is_numeric($argEndYear) &&
				settype($argEndYear, "integer") &&
				$argEndYear > 2000 &&
				$argEndYear < 9999) {
				// Valid
				$this->setProperty(RECUR_END_YEAR, $argEndYear);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid end year");
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
				$this->setProperty(RECUR_EXPIRE_MONTH, $argExpireMonth);
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
				is_numeric($argExpireYear) &&
				settype($argExpireYear, "integer") &&
				$argExpireYear > 2000 &&
				$argExpireYear < 9999) {
				// Valid
				$this->setProperty(RECUR_EXPIRE_YEAR, $argExpireYear);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid expire year");
			}

			return $result;
		}

		function setNotifyCustomer($argNotifyCustomer) {
			$this->setProperty(RECUR_NOTIFY_CUSTOMER, $argNotifyCustomer);
			$this->clearError();
			return true;
		}

		/**
		*
		*	@param numeric $argNumberOfRetries
		*/
		function setNumberOfRetries($argNumberOfRetries) {
			$result = false;

			if (is_numeric($argNumberOfRetries)) {
				// Valid
				$this->setProperty(RECUR_NUMBER_OF_RETRIES, $argNumberOfRetries);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid number of retries");
			}

			return $result;
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
		function setPeriod($argPeriod) {
			$result = false;

			if ($argPeriod == PERIOD_WEEKLY ||
			    $argPeriod == PERIOD_BIWEEKLY ||
			    $argPeriod == PERIOD_SEMIMONTHLY ||
			    $argPeriod == PERIOD_MONTHLY ||
			    $argPeriod == PERIOD_QUARTERLY ||
			    $argPeriod == PERIOD_ANNUAL) {
				// Valid
				$this->setProperty(RECUR_PERIOD, $argPeriod);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid period ");
			}

			return $result;
		}

		function setZipOrPostalCode($argZipOrPostalCode) {
			$this->setProperty(RECUR_POSTAL_CODE, $argZipOrPostalCode);
			$this->clearError();
			return true;
		}

		function setRecurrenceID($argRecurrenceID) {
			$this->setProperty(RECUR_RECURRENCE_ID, $argRecurrenceID);
			$this->clearError();
			return true;
		}

		function setStartDay($argStartDay) {
			$result = false;

			if ((strlen($argStartDay) == 1 ||
				strlen($argStartDay) == 2) &&
				is_numeric($argStartDay) &&
				settype($argStartDay, "integer") &&
				$argStartDay > 0 &&
				$argStartDay < 32) {
				// Valid
				$this->setProperty(RECUR_START_DAY, $argStartDay);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid start day");
			}

			return $result;
		}

		function setStartMonth($argStartMonth) {
			$result = false;

			if ((strlen($argStartMonth) == 1 ||
				strlen($argStartMonth) == 2) &&
				is_numeric($argStartMonth) &&
				settype($argStartMonth, "integer") &&
				$argStartMonth > 0 &&
				$argStartMonth < 13) {
				// Valid
				$this->setProperty(RECUR_START_MONTH, $argStartMonth);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid start month");
			}

			return $result;
		}

		function setStartYear($argStartYear) {
			$result = false;

			if (strlen($argStartYear) == 4 &&
				is_numeric($argStartYear) &&
				settype($argStartYear, "integer") &&
				$argStartYear > 2000 &&
				$argStartYear < 9999) {
				// Valid
				$this->setProperty(RECUR_START_YEAR, $argStartYear);
				$this->clearError();
				$result = true;
			} else {
				// Invalid
				$this->setError("Invalid start year");
			}

			return $result;
		}
	}
?>
