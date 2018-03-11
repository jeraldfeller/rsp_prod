<?php
	/**
	 *	@package Paygateway
	 */

	// ACH BATCH transaction specific defines - alphabetically
	define("ACTION", "action");
	//define("BATCH_ID", "batch_id");
	
	// Possible values for ACTION
	define("TOTALS_ACTION", "TOTALS");
	define("SETTLE_ACTION", "SETTLE");

	class ACHBatchRequest extends TransactionRequestBase {

        function __construct() {
		}

		function doTransaction() {
			$this->setTransactionType(ACH_BATCH);
			return $this->executeTransaction();
		}

		/***************************************************************
		 *  Setter functions for ACH BATCH request fields in alpha order
		 ***************************************************************/		 
		/**
		 * setAction function.  Valid values are:
		 * 
		 * - TOTALS
		 * - SETTLE
		 * 
		 * @param mixed $argAction
		 */
		function setAction($argAction) {
			if ($argAction == TOTALS_ACTION || 
			    $argAction == SETTLE_ACTION) {
				$this->setProperty(ACTION, $argAction);
				$this->clearError();
				return true;
			} else {
				$this->setError("Invalid action.");
				return false;
			}
		}

		/**
		 * setBatchID function.  
		 * 
		 * Specifies the batch id to settle or query.
		 *
		 * @param mixed $argBatchID
		 */
		function setBatchID($argBatchID) {
			$this->setProperty(BATCH_ID, $argBatchID);
			$this->clearError();
			return true;
		}

		/***************************************************************
		 *  Getter functions for ACH BATCH request fields in alpha order
		 ***************************************************************/

		function getAction() {
			return $this->getProperty(ACTION);
		}

		function getBatchID() {
			return $this->getProperty(BATCH_ID);
		}

	} // end ACHBatchRequest
?>
