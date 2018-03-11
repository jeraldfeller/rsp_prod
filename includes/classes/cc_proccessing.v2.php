<?php

define('EPNACCOUNT','0512949'); //Production value.
//define('EPNACCOUNT','05971'); // Testing value. Do not enable this on this server!!!

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
		$this->url = ' ';//'https://etrans.paygateway.com/TransactionManager';
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

		$post_data 	= null;

		// Process the variables
		$error = 0;

		// Set up the post data to send.
		if(!$error) {
			///////////////////////////////////////////
			//////////// PRODUCTION VALUES ////////////
			///////////////////////////////////////////
			$ePNAccount   		= EPNACCOUNT;
			$CVV2Type     		= 1;
			$chargeTotal 		= $this->fetch_query_variable('charge_total');
			$creditCardNumber 	= $this->fetch_query_variable('credit_card_number');
			$creditCardVerificationNumber = $this->fetch_query_variable('credit_card_verification_number');
			$expireMonth 		= $this->fetch_query_variable('expire_month');
			$expireYear 		= $this->fetch_query_variable('expire_year');
			$invoiceNumber 	= $this->fetch_query_variable('invoice_number');
			$orderDescription  = $this->fetch_query_variable('order_description');
			$billCompany 		= $this->fetch_query_variable('bill_company');
			$billFirstName 	= $this->fetch_query_variable('bill_first_name');
			$billLastName 		= $this->fetch_query_variable('bill_last_name');
			$billEmail 		= $this->fetch_query_variable('bill_email');
			$billPhone 		= $this->fetch_query_variable('bill_phone');
			$billAddressOne 	= $this->fetch_query_variable('bill_address_one');
			$billAddressTwo 	= $this->fetch_query_variable('bill_address_two');
			$billCity 		= $this->fetch_query_variable('bill_city');
			$billStateOrProvince = $this->fetch_query_variable('bill_state_or_province');
			$billZipOrPostalCode = $this->fetch_query_variable('bill_zip_or_postal_code');
			$billCountryCode 	= $this->fetch_query_variable('bill_country_code');
			empty($billAddressTwo)?
				$address = $billAddressOne:
				$address = $billAddressOne . ' ' . $billAddressTwo;
			///////////////////////////////////////////


			// Process the data into POST
			$post_data = array(
				"ePNAccount" 	=>$ePNAccount,
				"CardNo"		=>$creditCardNumber,
				"ExpMonth"	=>$expireMonth,
				"ExpYear"	=>$expireYear,
				"Total"		=>$chargeTotal,
				"Address"	=>$address,
				"Zip"		=>$billZipOrPostalCode,
				"EMail"		=>$billEmail,
				"CVV2Type"	=>$CVV2Type,
				"CVV2"		=>$creditCardVerificationNumber,
				"HTML"		=>"No",
				"Company"		=>$billComany,
				"FirstName"	=>$billFirstName,
				"LastName"	=>$billLastName,
				"City"		=>$billCity,
				"State"		=>$billStateorProvince,
				"Phone"		=>$billPhone,
				//"Inv"		=>$invoiceNumber,
				"Description"	=>$orderDescription,
			);

			$ch=curl_init("https://www.eProcessingNetwork.Com/cgi-bin/tdbe/transact.pl"); // initiate cURL w/ protocol & URL of remote host
			curl_setopt($ch,CURLOPT_POST, true); 			// normal POST request
			curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 		// set response to return as variable
			$response=curl_exec($ch);					// trap response into $response var
                        if(!$response) {
                            echo 'Error contacting the remote server';
                        }
			curl_close($ch); 							// close cURL transfer

			$auth=substr($response,1,1);
			echo '<pre>'; print_r($response);echo '</pre>'; // Uncomment this line for production.

			if($auth=="Y")
			// Authorized transaction. Just return success.
			{
				$this->response_code = 1;
			}
			else
			// Declined transaction
			{
			        echo 'TRANSACTION FAILED';
				$this->response_code = -1;
				$response_array      = explode(",",$response);
				$error_message       = "RepsoneCodeText: ";

				if($auth=="U") {
					$error_message .= "UNABLE to complete the transaction. ";
				} elseif($auth=="N") {
					$error_message .= "MERCHANT DECLINED the transaction. ";
				}
				
				foreach($response_array as $value) { $error_message .= ' '.$value; }
				
				if(substr_count(strtoupper($response_array[1]),"NOT"))
				// Address Verification Systems response.
				{
					// Check for failure here by searching for "NOT". If not valid, add the message to the error code.
					$error_message .= str_replace('"','',$response_array[1]);
				}
				if(substr_count(strtoupper($response_array[2]),"NOT"))
				// CVV2 validity response.
				{
					// Check for failure here by searching for "NOT". If not valid, add the message to the error code.
					$error_message .= str_replace('"','',$response_array[2]);
				}

				// Set the error message variable.
				$this->errorMessages[] = $error_message;
			}
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