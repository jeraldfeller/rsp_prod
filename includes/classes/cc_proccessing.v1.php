<?php

	
	class cc_proccessing {
		var $query_variables;
		var $query_string;
		var $response_code;
		var $errorMessages;
		var $url;
		
			function cc_proccessing() {
				$this->query_variables = array();
				$this->query_string = '';
				$this->response = false;
				$this->url = 'https://etrans.paygateway.com/TransactionManager';
				$this->errorMessages = array();
				$this->set_default_variables();
			}
			
			function set_default_variables() {
				
			}
			
			function pre_transaction($cardnumber, $cardname, &$errornumber, &$errortext) {
			  $cards = array ( array ('name' => 'Visa', 
									  'length' => '13,16', 
									  'prefixes' => '4',
									  'checkdigit' => true
									 ),
							   array ('name' => 'MasterCard', 
									  'length' => '16', 
									  'prefixes' => '51,52,53,54,55',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Diners Club', 
									  'length' => '14',
									  'prefixes' => '300,301,302,303,304,305,36,38',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Amex', 
									  'length' => '15', 
									  'prefixes' => '34,37',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Discover', 
									  'length' => '16', 
									  'prefixes' => '6011',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Discover', 
									  'length' => '16', 
									  'prefixes' => '6011',
									  'checkdigit' => true
									 )

							 );        
			
			  $ccErrorNo = 0;
			
			  $ccErrors [0] = "Unknown card type";
			  $ccErrors [1] = "No card number provided";
			  $ccErrors [2] = "Credit card number has invalid format";
			  $ccErrors [3] = "Credit card number is invalid";
			  $ccErrors [4] = "Credit card number is wrong length";
						   
			  // Establish card type
			  $cardType = -1;
			  for ($i=0; $i<sizeof($cards); $i++) {
			
				// See if it is this card (ignoring the case of the string)
				if (strtolower($cardname) == strtolower($cards[$i]['name'])) {
				  $cardType = $i;
				  break;
				}
			  }
			  
			  // If card type not found, report an error
			  if ($cardType == -1) {
				 $errornumber = 0;     
				 $errortext = $ccErrors [$errornumber];
				 return false; 
			  }
			   
			  // Ensure that the user has provided a credit card number
			  if (strlen($cardnumber) == 0)  {
				 $errornumber = 1;     
				 $errortext = $ccErrors [$errornumber];
				 return false; 
			  }
			   
			  // Check that the number is numeric, although we do permit a space to occur  
			  // every four digits. 
			  $cardexp = '^([0-9]{4})[[:space:]]?([0-9]{4})[[:space:]]?([0-9]{4})[[:space:]]?([0-9]{1,4})$';
			  if (!ereg($cardexp,$cardnumber, $matches))  {
				 $errornumber = 2;     
				 $errortext = $ccErrors [$errornumber];
				 return false; 
			  }
			  
			  // Now remove any spaces from the credit card number
			  $cardNo = $matches[1] . $matches[2] . $matches[3] . $matches[4];
				   
			  // Now check the modulus 10 check digit - if required
			  if ($cards[$cardType]['checkdigit']) {
				$checksum = 0;                                  // running checksum total
				$mychar = "";                                   // next char to process
				$j = 1;                                         // takes value of 1 or 2
			  
				// Process each digit one by one starting at the right
				for ($i = strlen($cardNo) - 1; $i >= 0; $i--) {
				
				  // Extract the next digit and multiply by 1 or 2 on alternative digits.      
				  $calc = $cardNo{$i} * $j;
				
				  // If the result is in two digits add 1 to the checksum total
				  if ($calc > 9) {
					$checksum = $checksum + 1;
					$calc = $calc - 10;
				  }
				
				  // Add the units element to the checksum total
				  $checksum = $checksum + $calc;
				
				  // Switch the value of j
				  if ($j ==1) {$j = 2;} else {$j = 1;};
				} 
			  
				// All done - if checksum is divisible by 10, it is a valid modulus 10.
				// If not, report an error.
				if ($checksum % 10 != 0) {
				 $errornumber = 3;     
				 $errortext = $ccErrors [$errornumber];
				 return false; 
				}
			  }  
			
			  // The following are the card-specific checks we undertake.
			
			  // Load an array with the valid prefixes for this card
			  $prefix = split(',',$cards[$cardType]['prefixes']);
				  
			  // Now see if any of them match what we have in the card number  
			  $PrefixValid = false; 
			  for ($i=0; $i<sizeof($prefix); $i++) {
				$exp = '^' . $prefix[$i];
				if (ereg($exp,$cardNo)) {
				  $PrefixValid = true;
				  break;
				}
			  }
				  
			  // If it isn't a valid prefix there's no point at looking at the length
			  if (!$PrefixValid) {
				 $errornumber = 3;     
				 $errortext = $ccErrors [$errornumber];
				 return false; 
			  }
				
			  // See if the length is valid for this card
			  $LengthValid = false;
			  $lengths = split(',',$cards[$cardType]['length']);
			  for ($j=0; $j<sizeof($lengths); $j++) {
				if (strlen($cardNo) == $lengths[$j]) {
				  $LengthValid = true;
				  break;
				}
			  }
			  
			  // See if all is OK by seeing if the length was valid. 
			  if (!$LengthValid) {
				 $errornumber = 4;     
				 $errortext = $ccErrors [$errornumber];
				 return false; 
			  };   
			  
			  // The credit card is in the required format.
			  return true;
			}

			function set_proccessing_variable($key, $value) {
				$this->query_variables[$key] = $value;
			}
			
			function generate_query_string() {
				$query_string = '';
				reset($this->query_variables);
					while(list($key, $val) = each($this->query_variables)) {
							if (!empty($query_string)) {
								$query_string .= '&';
							}
						$query_string .= $key.'='.$val;
					}
				$this->query_string = $query_string;
			}
			
			function fetch_query_variable($key) {
				$return = '';
					if (isset($this->query_variables[$key])) {
						$return = $this->query_variables[$key];
					}
				return $return;
			}
			
			function preform_transaction() {
				$this->errorMessages = array();

				$creditCardRequest = new TransactionRequest();
			
				$creditCardRequest->setAccountToken(CC_TOKEN);
				
				$buyerCode = $this->fetch_query_variable('buyer_code');
					
					if($buyerCode != "") {
						if(!$creditCardRequest->setBuyerCode($buyerCode)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$invoiceNumber = $this->fetch_query_variable('invoice_number');
					if($invoiceNumber != "") {
						if(!$creditCardRequest->setInvoiceNumber($invoiceNumber)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
					$billAddressOne = $this->fetch_query_variable('bill_address_one');
					if($billAddressOne != "") {
						if(!$creditCardRequest->setBillAddressOne($billAddressOne)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billAddressTwo = $this->fetch_query_variable('bill_address_two');
					if($billAddressTwo != "") {
						if(!$creditCardRequest->setBillAddressTwo($billAddressTwo))	{
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billCity = $this->fetch_query_variable('bill_city');
					if($billCity != "") {
						if(!$creditCardRequest->setBillCity($billCity)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billCompany = $this->fetch_query_variable('bill_company');
					if($billCompany != "") {
						if(!$creditCardRequest->setBillCompany($billCompany)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billCountryCode = $this->fetch_query_variable('bill_country_code');
					if($billCountryCode != "") {
						if(!$creditCardRequest->setBillCountryCode($billCountryCode)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billCustomerTitle = $this->fetch_query_variable('bill_customer_title');
					if($billCustomerTitle != "") {
						if(!$creditCardRequest->setBillCustomerTitle($billCustomerTitle)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billEmail = $this->fetch_query_variable('bill_email');
					if($billEmail != "") {
						if(!$creditCardRequest->setBillEmail($billEmail)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billFax = $this->fetch_query_variable('bill_fax');
					if($billFax != "") {
						if(!$creditCardRequest->setBillFax($billFax)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billFirstName = $this->fetch_query_variable('bill_first_name');
					if($billFirstName != "") {
						if(!$creditCardRequest->setBillFirstName($billFirstName)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billLastName = $this->fetch_query_variable('bill_last_name');
					if($billLastName != "") {
						if(!$creditCardRequest->setBillLastName($billLastName)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billMiddleName = $this->fetch_query_variable('bill_middle_name');
					if($billMiddleName != "") {
						if(!$creditCardRequest->setBillMiddleName($billMiddleName)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billNote = $this->fetch_query_variable('bill_note');
					if($billNote != "") {
						if(!$creditCardRequest->setBillNote($billNote)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billPhone = $this->fetch_query_variable('bill_phone');
					if($billPhone != "") {
						if(!$creditCardRequest->setBillPhone($billPhone)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
					$billZipOrPostalCode = $this->fetch_query_variable('bill_zip_or_postal_code');
					if($billZipOrPostalCode != "") {
						if(!$creditCardRequest->setBillZipOrPostalCode($billZipOrPostalCode)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$billStateOrProvince = $this->fetch_query_variable('bill_state_or_province');
					if($billStateOrProvince != "") {
						if(!$creditCardRequest->setBillStateOrProvince($billStateOrProvince)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$cardBrand = $this->fetch_query_variable('card_brand');
					if($cardBrand != "") {
						if(!$creditCardRequest->setCardBrand($cardBrand)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$chargeTotal = $this->fetch_query_variable('charge_total');
					if($chargeTotal != "") {
						if(!$creditCardRequest->setChargeTotal($chargeTotal)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$chargeType = $this->fetch_query_variable('charge_type');
					$chargeType = 'SALE';
					if ($chargeType != "") {
						switch ($chargeType) {
							case "AUTH":
								$chargeType = AUTH;
								break;
							case "CAPTURE":
								$chargeType = CAPTURE;
								break;
							case "SALE":
								$chargeType = SALE;
								break;
							case "CREDIT":
								$chargeType = CREDIT;
								break;
							case "VOID":
								$chargeType = VOID;
								break;
							case "FORCE_AUTH":
								$chargeType = FORCE_AUTH;
								break;
							case "FORCE_SALE":
								$chargeType = FORCE_SALE;
								break;
							case "QUERY_PAYMENT":
								$chargeType = QUERY_PAYMENT;
								break;
							case "QUERY_CREDIT":
								$chargeType = QUERY_CREDIT;
								break;
							case "ADJUSTMENT":
								$chargeType = ADJUSTMENT;
								break;
						}
						if (!$creditCardRequest->setChargeType($chargeType)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$creditCardNumber = $this->fetch_query_variable('credit_card_number');
					if($creditCardNumber != "") {
						if(!$creditCardRequest->setCreditCardNumber($creditCardNumber)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$creditCardVerificationNumber = $this->fetch_query_variable('credit_card_verification_number');
					if($creditCardVerificationNumber != "") {
						if(!$creditCardRequest->setCreditCardVerificationNumber($creditCardVerificationNumber)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$duplicateCheck = $this->fetch_query_variable('duplicate_check');
					if($duplicateCheck != "") {
						if(!$creditCardRequest->setDuplicateCheck($duplicateCheck)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$purchaseOrderNumber = $this->fetch_query_variable('purchase_order_number');
					if($purchaseOrderNumber != "") {
						if(!$creditCardRequest->setPurchaseOrderNumber($purchaseOrderNumber)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$expireMonth = $this->fetch_query_variable('expire_month');
					if($expireMonth != "") {
						if(!$creditCardRequest->setExpireMonth($expireMonth)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$expireYear = $this->fetch_query_variable('expire_year');
					if($expireYear != "") {
						if(!$creditCardRequest->setExpireYear($expireYear)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$orderDescription = $this->fetch_query_variable('order_description');
					if($orderDescription != "") {
						if(!$creditCardRequest->setOrderDescription($orderDescription)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$orderID = $this->fetch_query_variable('order_id');
					if($orderID != "") {
						if(!$creditCardRequest->setOrderID($orderID)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$orderUserID = $this->fetch_query_variable('order_user_id');
					if($orderUserID != "") {
						if(!$creditCardRequest->setOrderUserID($orderUserID)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipAddressOne = $this->fetch_query_variable('ship_address_one');
					if($shipAddressOne != "") {
						if(!$creditCardRequest->setShipAddressOne($shipAddressOne)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipAddressTwo = $this->fetch_query_variable('ship_address_two');
					if($shipAddressTwo != "") {
						if(!$creditCardRequest->setShipAddressTwo($shipAddressTwo))	{
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipCity = $this->fetch_query_variable('ship_city');
					if($shipCity != "") {
						if(!$creditCardRequest->setShipCity($shipCity)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipCompany = $this->fetch_query_variable('ship_company');
					if($shipCompany != "") {
						if(!$creditCardRequest->setShipCompany($shipCompany)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipCountryCode = $this->fetch_query_variable('ship_country_code');
					if($shipCountryCode != "") {
						if(!$creditCardRequest->setShipCountryCode($shipCountryCode)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipCustomerTitle = $this->fetch_query_variable('ship_customer_title');
					if($shipCustomerTitle != "") {
						if(!$creditCardRequest->setShipCustomerTitle($shipCustomerTitle)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipEmail = $this->fetch_query_variable('ship_email');
					if($shipEmail != "") {
						if(!$creditCardRequest->setShipEmail($shipEmail)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipFax = $this->fetch_query_variable('ship_fax');
					if($shipFax != "") {
						if(!$creditCardRequest->setShipFax($shipFax)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipFirstName = $this->fetch_query_variable('ship_first_name');
					if($shipFirstName != "") {
						if(!$creditCardRequest->setShipFirstName($shipFirstName)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipLastName = $this->fetch_query_variable('ship_last_name');
					if($shipLastName != "") {
						if(!$creditCardRequest->setShipLastName($shipLastName)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipMiddleName = $this->fetch_query_variable('ship_middle_name');
					if($shipMiddleName != "") {
						if(!$creditCardRequest->setShipMiddleName($shipMiddleName)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipNote = $this->fetch_query_variable('ship_note');
					if($shipNote != "") {
						if(!$creditCardRequest->setShipNote($shipNote)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipPhone = $this->fetch_query_variable('ship_phone');
					if($shipPhone != "") {
						if(!$creditCardRequest->setShipPhone($shipPhone)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipZipOrPostalCode = $this->fetch_query_variable('ship_zip_or_postal_code');
					if($shipZipOrPostalCode != "") {
						if(!$creditCardRequest->setShipZipOrPostalCode($shipZipOrPostalCode)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shipStateOrProvince = $this->fetch_query_variable('ship_state_or_province');
					if($shipStateOrProvince != "") {
						if(!$creditCardRequest->setShipStateOrProvince($shipStateOrProvince)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$shippingCharge = $this->fetch_query_variable('shipping_charge');
					if($shippingCharge != "") {
						if(!$creditCardRequest->setShippingCharge($shippingCharge)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$taxAmount = $this->fetch_query_variable('tax_amount');
					if($taxAmount != "") {
						if(!$creditCardRequest->setTaxAmount($taxAmount)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$transactionConditionCode = $this->fetch_query_variable('transaction_condition_code');
					if($transactionConditionCode != "") {
						if(!$creditCardRequest->setTransactionConditionCode($transactionConditionCode)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$taxExempt = $this->fetch_query_variable('tax_exempt');
					if ($taxExempt != "") {
						if (!$creditCardRequest->setTaxExempt(($taxExempt == "true"))) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$stateTax = $this->fetch_query_variable('state_tax');
					if ($stateTax != "") {
						if (!$creditCardRequest->setStateTax($stateTax)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$bankApprovalCode = $this->fetch_query_variable('bank_approval_code');
					if ($bankApprovalCode != "") {
						if (!$creditCardRequest->setBankApprovalCode($bankApprovalCode)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$referenceID = $this->fetch_query_variable('reference_id');
					if ($referenceID != "") {
						if (!$creditCardRequest->setReferenceID($referenceID)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
				
					// optional service details
					$folioNumber = $this->fetch_query_variable('folio_number');
					if ($folioNumber != "") {
						if (!$creditCardRequest->setFolioNumber($folioNumber)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
				
					$industry = $this->fetch_query_variable('industry');
					if ($industry != "") {
						if (!$creditCardRequest->setIndustry($industry)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
				
					$chargeTotalIncludesRestaurant = $this->fetch_query_variable('charge_total_incl_restaurant');
					if ($chargeTotalIncludesRestaurant != "") {
						if (!$creditCardRequest->setChargeTotalIncludesRestaurant($chargeTotalIncludesRestaurant)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$chargeTotalIncludesGiftshop = $this->fetch_query_variable('charge_total_incl_giftshop');
					if ($chargeTotalIncludesGiftshop != "") {
						if (!$creditCardRequest->setChargeTotalIncludesGiftshop($chargeTotalIncludesGiftshop)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$chargeTotalIncludesMinibar = $this->fetch_query_variable('charge_total_incl_minibar');
					if ($chargeTotalIncludesMinibar != "") {
						if (!$creditCardRequest->setChargeTotalIncludesMinibar($chargeTotalIncludesMinibar)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
						$chargeTotalIncludesPhone = $this->fetch_query_variable('charge_total_incl_phone');
					if ($chargeTotalIncludesPhone != "") {
						if (!$creditCardRequest->setChargeTotalIncludesPhone($chargeTotalIncludesPhone)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$chargeTotalIncludesLaundry = $this->fetch_query_variable('charge_total_incl_laundry');
					if ($chargeTotalIncludesLaundry != "") {
						if (!$creditCardRequest->setChargeTotalIncludesLaundry($chargeTotalIncludesLaundry)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$chargeTotalIncludesOther = $this->fetch_query_variable('charge_total_incl_other');
					if ($chargeTotalIncludesOther != "") {
						if (!$creditCardRequest->setChargeTotalIncludesOther($chargeTotalIncludesOther)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$serviceRate = $this->fetch_query_variable('service_rate');
					if ($serviceRate != "") {
						if (!$creditCardRequest->setServiceRate($serviceRate)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$serviceStartDay = $this->fetch_query_variable('service_start_day');
					if ($serviceStartDay != "") {
						if (!$creditCardRequest->setServiceStartDay($serviceStartDay)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$serviceStartMonth = $this->fetch_query_variable('service_start_month');
					if ($serviceStartMonth != "") {
						if (!$creditCardRequest->setServiceStartMonth($serviceStartMonth)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$serviceStartYear = $this->fetch_query_variable('service_start_year');
					if ($serviceStartYear != "") {
						if (!$creditCardRequest->setServiceStartYear($serviceStartYear)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$serviceEndDay = $this->fetch_query_variable('service_end_day');
					if ($serviceEndDay != "") {
						if (!$creditCardRequest->setServiceEndDay($serviceEndDay)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$serviceEndMonth = $this->fetch_query_variable('service_end_month');
					if ($serviceEndMonth != "") {
						if (!$creditCardRequest->setServiceEndMonth($serviceEndMonth)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
				
					$serviceEndYear = $this->fetch_query_variable('service_end_year');
					if ($serviceEndYear != "") {
						if (!$creditCardRequest->setServiceEndYear($serviceEndYear)) {
							$this->errorMessages[] = $creditCardRequest->getError();
						}
					}
	
					$creditCardResponse = $creditCardRequest->doTransaction();
          if ($creditCardResponse) {
//error::cc_error(print_r($creditCardRequest, true));   // DRC testing only
//error::cc_error(print_r($creditCardResponse, true));  // DRC testing only
					  $this->response_code = $creditCardResponse->GetResponseCode();
            if ($this->response_code != RC_SUCCESSFUL_TRANSACTION)
              $this->errorMessages[] = "ResponseCodeText: ".$creditCardResponse->GetResponseCodeText();
          } else {
					  $this->response_code = -1;
          }
			}
			
			function return_response() {
				return $this->response_code;
			}
	
			function error_messages() {
				return $this->errorMessages;
			}
	
	}
?>
