<?php

	define("TRANSACTION_STATUS", "transaction_status");
	define("BATCH_ID", "batch_id");
	define("IS_RESUBMITTABLE", "is_resubmittable");
//	define("ORIGINAL_REFERENCE_ID", "original_reference_id");
	define("AMOUNT", "amount");

	/**
	 *	@package Paygateway
	 */
	class ACHResponse extends TransactionResponseBase {

		function ACHResponse($argResponseString){
			parent::TransactionResponse($argResponseString);
		}

		function getReferenceId() {
			return $this->getProperty(ACH_REFERENCE_ID);
		}

		function getOrderId() {
			return $this->getProperty(ORDER_ID);
		}

		function getTransactionStatus() {
			return $this->getProperty(TRANSACTION_STATUS);
		}

		function getBatchID() {
			return $this->getProperty(BATCH_ID);
		}

		function getIsResubmittable() {
			return $this->getProperty(IS_RESUBMITTABLE);
		}

		function getOriginalReferenceId() {
			return $this->getProperty(ORIGINAL_REFERENCE_ID);
		}

		function getAmount() {
			return $this->getProperty(AMOUNT);
		}

	} // end ACHResponse
?>
