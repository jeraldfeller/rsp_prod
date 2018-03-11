<?php
	/**
	 *	@package Paygateway
	 */
	class RecurringResponse extends TransactionResponseBase {

        function __construct($argResponseString){
			parent::TransactionResponse($argResponseString);
		}

	}
?>
