<?php
//tst
		$user_id = $user->fetch_user_id();
		
		$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 

		$tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
		
		


			//Now the group specific items.

			$user_group_id = $user->fetch_user_group_id();

				
				//print_r($vars['newsitem']);
				

					$transactions = array();
					$transactions_sql = "SELECT transaction_id, user_id, account_id, billing_method_id, reason, details, amount, running_total, order_id, address_id, date_added FROM " . TABLE_TRANSACTIONS ." WHERE billing_method_id=1 and date_added >= 14500 and user_id=".$user->fetch_user_id()." ORDER BY transaction_id DESC";
					$query = $database->query($transactions_sql);
					while ($result = $database->fetch_array($query)) {
						$transaction = array();
						$transaction['date_added'] = date('n/d/Y H:i:s', $result['date_added']);
						$transaction['user_name'] = tep_get_user_name($result['user_id']);
						$transaction['account_name'] = account::getAccountName($result['account_id']);
						$transaction['details'] = $result['details'];
						$transaction['reason'] = $result['reason'];
						$transaction['amount'] = $result['amount'];
						$transaction['transaction_id'] = $result['transaction_id'];
						$transaction['running_total'] = $result['running_total'];

						$transactions[] = $transaction;
					}
					
					$vars['transactions'] = $transactions;
					//print_r($transactions);
					
					echo $twig->render('agent/transactions.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
	?>
