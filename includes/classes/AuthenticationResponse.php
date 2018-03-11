<?php
	/**
	 *	@package Paygateway
	 */
	class AuthenticationResponse extends TransactionResponseBase {

        function __construct($argResponseString){
			parent::TransactionResponse($argResponseString);
		}

		// PayerAuth LOOKUP Response Field getters
		function getAuthenticationTransactionID() {
			return $this->getProperty(AUTHENTICATION_TRANSACTION_ID);
		}
		function getLookupPayload() {
			return $this->getProperty(LOOKUP_PAYLOAD);
		}
		function getHiddenFields() {
			return $this->getProperty(HIDDEN_FIELDS);
		}

		function getOrderID() {
			return $this->getProperty(ORDER_ID);
		}

		function getAuthenticationURL() {
			return $this->getProperty(AUTHENTICATION_URL);
		}

		// PayerAuth AUTHENTICATE Response Field getters
		function getCAVV() {
			return $this->getProperty(CAVV);
		}

		function getXID() {
			return $this->getProperty(XID);
		}

		function getStatus() {
			return $this->getProperty(STATUS);
		}

		function getTransactionConditionCode() {
			return $this->getProperty(TRANSACTION_CONDITION_CODE);
		}

	} // end AuthenticationResponse
?>
