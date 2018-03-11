<?php
	/**
	 *	@package Paygateway
	 */

	// ACH transaction specific defines - alphabetically
	define("ACCOUNT_NUMBER", "account_number");
	define("ACCOUNT_CLASS", "account_class");
	define("ACCOUNT_TYPE", "account_type");
	define("BILL_BIRTH_DAY", "bill_birth_day");
	define("BILL_BIRTH_MONTH", "bill_birth_month");
	define("BILL_BIRTH_YEAR", "bill_birth_year");
	define("BILL_DRIVER_LICENSE_NUMBER", "bill_driver_license_number");
	define("BILL_DRIVER_LICENSE_STATE_CODE", "bill_driver_license_state_code");
	define("BILL_DRIVER_LICENSE_SWIPE", "bill_driver_license_swipe");
	define("BILL_SOCIAL_SECURITY_NUMBER", "bill_social_security_number");
	define("CHECK_NUMBER", "check_number");
	define("CLERK_ID", "clerk_id");
	define("ORIGINAL_REFERENCE_ID", "original_reference_id");
	define("ACH_REFERENCE_ID", "reference_id"); 
	define("ROUTING_NUMBER", "routing_number");

	// Possible values for ACCOUNT_TYPE
	define("SAVINGS", 0);
	define("CHECKING", 1);

	// Possible values for ACCOUNT_CLASS
	define("PERSONAL", 0);
	define("CORPORATE", 1);

	// TCCs for ACH
	define("ACH_PREARRANGED_PAYMENT_AND_DEPOSIT", 50);
	define("ACH_TELEPHONE", 51);
	define("ACH_WEB", 52);
	define("ACH_CASH_CONCENTRATION_OR_DISBURSEMENT_ORDER", 53);


	/**
	 *	@package Paygateway
	 */
	class ACHRequest extends TransactionRequestBase {


		function ACHRequest() {
		}

		function doTransaction() {
			$this->setTransactionType(ACH);
			return $this->executeTransaction();
		}

		/************************************************************
		 ************************************************************		
		 *  Setter functions for ACH request fields - in alpha order
		 ************************************************************		
		 ************************************************************/
		 
		function setAccountNumber($argAccountNumber) {
			$this->setProperty(ACCOUNT_NUMBER, $argAccountNumber);
			$this->clearError();
			return true;
		}
		
		/**
		 * The valid values are an empty string (for no account class) and:
		 * 
		 * - PERSONAL
		 * - CORPORATE
		 * 
		 * @param mixed $argAccountClass
		 */
		function setAccountClass($argAccountClass) {
			if ($argAccountClass == PERSONAL || $argAccountClass == CORPORATE || $argAccountClass == "") {
				$this->setProperty(ACCOUNT_CLASS, $argAccountClass);
				$this->clearError();
				return true;
			} else {
				$this->setError("Invalid account class.");
				return false;
			}
		}

		/**
		 * The valid values are an empty string (for no account type) and:
		 * 
		 * - CHECKING
		 * - SAVINGS
		 * 
		 * @param mixed $argAccountType
		 */
		function setAccountType($argAccountType) {
			$result = false;

			if($argAccountType == CHECKING || $argAccountType == SAVINGS || $argAccountType == "") {
				// Good account type
				$this->setProperty(ACCOUNT_TYPE, $argAccountType);
				$this->clearError();
				$result = true;
			} else {
				// Invalid account type
				$this->setError("Invalid account type.");
			}
			return $result;
		}

		function setReferenceID($argReferenceID) {
			$this->setProperty(ACH_REFERENCE_ID, $argReferenceID);
			$this->clearError();
			return true;
		}

		function setRoutingNumber($argRoutingNumber) {
			$this->setProperty(ROUTING_NUMBER, $argRoutingNumber);
			$this->clearError();
			return true;
		}

		function setCheckNumber($argCheckNumber) {                                                                   
			$this->setProperty(CHECK_NUMBER,$argCheckNumber);                                                               
			$this->clearError();                                                                              
			return true;                                    
		}                                                                                                     

		function setClerkID($argClerkID) {                                                                       
			$this->setProperty(CLERK_ID,$argClerkID);                                                              
			$this->clearError();                                                                                       
			return true;                                                                            
		}                                                                                         

		function setOriginalReferenceID($argOriginalReferenceID) {                                
			$this->setProperty(ORIGINAL_REFERENCE_ID,$argOriginalReferenceID);                      
			$this->clearError();                                                                    
			return true;                                                                            
		}                                                                                                                                                                                       

		function setBillCustomerTitle($argBillCustomerTitle) {                                    
			$this->setProperty(BILL_CUSTOMER_TITLE,$argBillCustomerTitle);                           
			$this->clearError();                                                                    
			return true;                                                                            
		}                                                                                         

		function setBillMiddleName($argBillMiddleName) {                                          
			$this->setProperty(BILL_MIDDLE_NAME,$argBillMiddleName);                                
			$this->clearError();                                                                    
			return true;                                                                            
		}
		
		function setBillEmail($argBillEmail) {
			$this->setProperty(BILL_EMAIL, $argBillEmail);
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
			$this->setProperty(BILL_COMPANY,$argBillCompany);
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

		function setBillPhone($argBillPhone) {
			$this->setProperty(BILL_PHONE,$argBillPhone);
			$this->clearError();
			return true;
		}

		function setBillFax($argBillFax) {
			$this->setProperty(BILL_FAX,$argBillFax);
			$this->clearError();
			return true;
		}

		function setBillSocialSecurityNumber($argBillSocialSecurityNumber) {
			$this->setProperty(BILL_SOCIAL_SECURITY_NUMBER,$argBillSocialSecurityNumber);                                                  
			$this->clearError();                                                                                                          
			return true;                                                                                               
		}                                                                                                                         

		function setBillBirthDay($argBillBirthDay) {                                                                            
			$this->setProperty(BILL_BIRTH_DAY,$argBillBirthDay);                                                                     
			$this->clearError();                                                                                        
			return true;                                                                                            
		}                                                                                                              

		function setBillBirthMonth($argBillBirthMonth) {                                                                                        
			$this->setProperty(BILL_BIRTH_MONTH,$argBillBirthMonth);                                                             
			$this->clearError();                                                                                                   
			return true;                                                                                                            
		}                                                                                                                       

		function setBillBirthYear($argBillBirthYear) {                                                                                
			$this->setProperty(BILL_BIRTH_YEAR,$argBillBirthYear);                                             
			$this->clearError();                                                                                          
			return true;                                                                                                  
		}                                                                                                               

		function setBillDriverLicenseNumber($argBillDriverLicenseNumber) {                                              
			$this->setProperty(BILL_DRIVER_LICENSE_NUMBER,$argBillDriverLicenseNumber);                                      
			$this->clearError();                                                                                          
			return true;                                                                                                  
		}                                                                                                               

		function setBillDriverLicenseStateCode($argBillDriverLicenseStateCode) {                                        
			$this->setProperty(BILL_DRIVER_LICENSE_STATE_CODE,$argBillDriverLicenseStateCode);                                
			$this->clearError();                                                                                          
			return true;                                                                                                  
		}                                                                                                               

		function setBillDriverLicenseSwipe($argBillDriverLicenseSwipe) {
			$this->setProperty(BILL_DRIVER_LICENSE_SWIPE,$argBillDriverLicenseSwipe);
			$this->clearError();
			return true;
		}
   
		function setShipCustomerTitle($argShipCustomerTitle) {
			$this->setProperty(SHIP_CUSTOMER_TITLE,$argShipCustomerTitle);
			$this->clearError();
			return true;
		}

		function setShipFirstName($argShipFirstName) {
			$this->setProperty(SHIP_FIRST_NAME,$argShipFirstName);
			$this->clearError();
			return true;
		}

		function setShipLastName($argShipLastName) {
			$this->setProperty(SHIP_LAST_NAME,$argShipLastName);
			$this->clearError();
			return true;
		}

		function setShipMiddleName($argShipMiddleName) {
			$this->setProperty(SHIP_MIDDLE_NAME,$argShipMiddleName);
			$this->clearError();
			return true;
		}

		function setShipEmail($argShipEmail) {
			$this->setProperty(SHIP_EMAIL,$argShipEmail);
			$this->clearError();
			return true;
		}

		function setShipAddressOne($argShipAddressOne) {
			$this->setProperty(SHIP_ADDRESS_ONE,$argShipAddressOne);
			$this->clearError();                                                       
			return true;                                                                      
		}                                                                      

		function setShipAddressTwo($argShipAddressTwo) {                             
			$this->setProperty(SHIP_ADDRESS_TWO,$argShipAddressTwo);                    
			$this->clearError();                                                           
			return true;                                                                     
		}                                                                                    

		function setShipCity($argShipCity) {                                                         
			$this->setProperty(SHIP_CITY,$argShipCity);                                                 
			$this->clearError();                                                                       
			return true;                                                                               
		}                                                                                            

		function setShipStateOrProvince($argShipStateOrProvince) {                                   
			$this->setProperty(SHIP_STATE_OR_PROVINCE,$argShipStateOrProvince);                           
			$this->clearError();
			return true;
		}

		function setShipZipOrPostalCode($argShipPostalCode) {
			$this->setProperty(SHIP_POSTAL_CODE,$argShipPostalCode);
			$this->clearError();
			return true;
		}
		
		/**
		*	- Accept only 2 characters country code
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

		function setShipCompany($argShipCompany) {
			$this->setProperty(SHIP_COMPANY,$argShipCompany);
			$this->clearError();
			return true;
		}

		function setShipPhone($argShipPhone) {
			$this->setProperty(SHIP_PHONE,$argShipPhone);
			$this->clearError();
			return true;
		}

		function setShipFax($argShipFax) {
			$this->setProperty(SHIP_FAX,$argShipFax);
			$this->clearError();
			return true;
		}		

		function setCartridgeType($argCartridgeType) {
			$this->setProperty(CARTRIDGE_TYPE, $argCartridgeType);
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

		function setCurrency($argCurrency) {
			$result = false;
			if (strlen($argCurrency) == 3) {
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

		function setPurchaseOrderNumber($argPurchaseOrderNumber) {
			$this->setProperty(PO_NUMBER, $argPurchaseOrderNumber);
			$this->clearError();
			return true;
		}

		/**
		*	Possible values
		*	- ACH_PREARRANGED_PAYMENT_AND_DEPOSIT = 50;
		*	- ACH_TELEPHONE = 51;
		*	- ACH_WEB = 52;
		*	- ACH_CASH_CONCENTRATION_OR_DISBURSEMENT_ORDER = 53
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

		function setInvoiceNumber($argInvoiceNumber) {
			$this->setProperty(INVOICE_NUMBER, $argInvoiceNumber);
			$this->clearError();
			return true;
		}

		/**
		*	Only these values are allowed:
		*		- SALE
		*		- DEBIT
		*		- VOID
		*		- QUERY
		*		- CREDIT
		*
		*	@param string $argChargeType
		*/
		function setChargeType($argChargeType) {
			$result = false;

			if($argChargeType == SALE         ||
			   $argChargeType == DEBIT			  ||
			   $argChargeType == VOID				  ||
			   $argChargeType == QUERY			  ||
			   $argChargeType == CREDIT			  
			   ) {
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

		function getAccountNumber() {
			return $this->getProperty(ACCOUNT_NUMBER);
		}

		function getRoutingNumber() {
			return $this->getProperty(ROUTING_NUMBER);
		}

		function getAccountType() {
			return $this->getProperty(ACCOUNT_TYPE);
		}
		
		function getAccountClass() {
			return $this->getProperty(ACCOUNT_CLASS);
		}

		function getCheckNumber() {                                                                   
			return $this->getProperty(CHECK_NUMBER);                                                               
		}                                                                                                     
		
		function getClerkID() {                                                                       
			return $this->getProperty(CLERK_ID);
		}                                                                                         
		
		function getOriginalReferenceID() {
			return $this->getProperty(ORIGINAL_REFERENCE_ID);
		}
		
		function getBillCustomerTitle() {                                    
			return $this->getProperty(BILL_CUSTOMER_TITLE);                                                                         
		}                                                                                         
		
		function getBillMiddleName() {                                          
			return $this->getProperty(BILL_MIDDLE_NAME);                                                             
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

		function getBillEmail() {
			return $this->getProperty(BILL_EMAIL);
		}

		function getBillFirstName() {
			return $this->getProperty(BILL_FIRST_NAME);
		}

		function getBillLastName() {
			return $this->getProperty(BILL_LAST_NAME);
		}

		function getBillZipOrPostalCode() {
			return $this->getProperty(BILL_ZIP_OR_POSTAL_CODE);
		}

		function getBillStateOrProvince() {
			return $this->getProperty(BILL_STATE_OR_PROVINCE);
		}

		function getBillCountryCode() {                                        
			return $this->getProperty(BILL_COUNTRY_CODE);                                             
		}                                                                                         
		
		function getBillCompany() {                                                
			return $this->getProperty(BILL_COMPANY);
		}
		
		function getBillPhone() {
			return $this->getProperty(BILL_PHONE);
		}
		
		function getBillFax() {
			return $this->getProperty(BILL_FAX);
		}
		
		function getBillSocialSecurityNumber() {
			return $this->getProperty(BILL_SOCIAL_SECURITY_NUMBER);                                                    
		}                                                                                                                         
		
		function getBillBirthDay() {                                                                            
			return $this->getProperty(BILL_BIRTH_DAY);                                                              
		}                                                                                                              
		
		function getBillBirthMonth() {                                                                                        
			return $this->getProperty(BILL_BIRTH_MONTH);                                                                              
		}                                                                                                                       
		
		function getBillBirthYear() {                                                                                
			return $this->getProperty(BILL_BIRTH_YEAR);                                                                    
		}                                                                                                               
		
		function getBillDriverLicenseNumber() {                                              
			return $this->getProperty(BILL_DRIVER_LICENSE_NUMBER);                                                        
		}                                                                                                               
		
		function getBillDriverLicenseStateCode() {                                        
			return $this->getProperty(BILL_DRIVER_LICENSE_STATE_CODE);                                                    
		}                                                                                                               
		
		function getBillDriverLicenseSwipe() {
			return $this->getProperty(BILL_DRIVER_LICENSE_SWIPE);
		}
		
		function getShipCustomerTitle() {
			return $this->getProperty(SHIP_CUSTOMER_TITLE);
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
		
		function getShipEmail() {
			return $this->getProperty(SHIP_EMAIL);
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
		
		function getShipStateOrProvince() {                                   
			return $this->getProperty(SHIP_STATE_OR_PROVINCE);
		}
		
		function getShipZipOrPostalCode() {
			return $this->getProperty(SHIP_POSTAL_CODE);
		}
		
		function getShipCountryCode() {
			return $this->getProperty(SHIP_COUNTRY_CODE);
		}
		
		function getShipCompany() {
			return $this->getProperty(SHIP_COMPANY);
		}
		
		function getShipPhone() {
			return $this->getProperty(SHIP_PHONE);
		}
		
		function getShipFax() {
			return $this->getProperty(SHIP_FAX);
		}

		function getCartridgeType() {
			return $this->getProperty(CARTRIDGE_TYPE);
		}

		function getChargeTotal() {
			return $this->getProperty(CHARGE_TOTAL);
		}

		function getChargeType() {
			return $this->getProperty(CHARGE_TYPE);
		}

		function getCurrency() {
			return $this->getProperty(CURRENCY);
		}

		function getCustomerIPAddress() {
			return $this->getProperty(CUSTOMER_IP_ADDRESS);
		}

		function getTransactionConditionCode() {
			return $this->getProperty(TRANSACTION_CONDITION_CODE);
		}

		function getOrderDescription() {
			return $this->getProperty(ORDER_DESCRIPTION);
		}

		function getOrderID() {
			return $this->getProperty(ORDER_ID);
		}

		function getPurchaseOrderNumber() {
			return $this->getProperty(PO_NUMBER);
		}

		function getInvoiceNumber() {
			return $this->getProperty(INVOICE_NUMBER);
		}

	} // end ACHRequest
?>
