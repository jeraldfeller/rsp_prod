<?php
	/**
	 *	@package Paygateway
	 */

	// ACH BATCH RESPONSE transaction specific defines
	//define("BATCH_ID", "batch_id");
	define("NUMBER_OF_DEBITS", "number_of_debits");
	define("DEBIT_TOTAL", "debit_total");
	define("NUMBER_OF_CREDITS", "number_of_credits");
	define("CREDIT_TOTAL", "credit_total");
	define("NUMBER_OF_VOIDS", "number_of_voids");
	define("VOID_TOTAL", "void_total");
	define("NUMBER_OF_DECLINES", "number_of_declines");
	define("DECLINE_TOTAL", "decline_total");
	define("BATCH_STATE", "batch_state");
	define("BATCH_BALANCE_STATE", "batch_balance_state");

	class ACHBatchResponse extends TransactionResponseBase {

        function __construct($argResponseString){
			parent::TransactionResponse($argResponseString);
		}
		
		/**************************************************
		 * Getter functions for ACH BATCH RESPONSE fields
		 **************************************************/

		function getBatchID() {
			return $this->getProperty(BATCH_ID);
		}

		function getNumberOfDebits() {
			return $this->getProperty(NUMBER_OF_DEBITS);
		}

		function getDebitTotal() {
			return $this->getProperty(DEBIT_TOTAL);
		}

		function getNumberOfCredits() {
			return $this->getProperty(NUMBER_OF_CREDITS);
		}

		function getCreditTotal() {
			return $this->getProperty(CREDIT_TOTAL);
		}

		function getNumberOfVoids() {
			return $this->getProperty(NUMBER_OF_VOIDS);
		}

		function getVoidTotal() {
			return $this->getProperty(VOID_TOTAL);
		}

		function getNumberOfDeclines() {
			return $this->getProperty(NUMBER_OF_DECLINES);
		}

		function getDeclineTotal() {
			return $this->getProperty(DECLINE_TOTAL);
		}

		function getBatchState() {
			return $this->getProperty(BATCH_STATE);
		}

		function getBatchBalanceState() {
			return $this->getProperty(BATCH_BALANCE_STATE);
		}

	} // end ACHBatchResponse
?>
