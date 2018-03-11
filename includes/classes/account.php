<?php
/*
	Herein lies the account features for the agents.  Like all classes it is self contained.
	It will have the ability to insert all types of payments and work out running totals.
	It will also have the ability to work out if any credit is available as an extra form of payment.

	It will also have functions for refunds and may have listing functions although maybe not.

	types:
	1 = order
	2 = refund
	3 = payment
	4 = cancelation
	5 = refund returned
	6 = credit used for order

	statuses
	0 = Canceled
	1 = Pending
	2 = Completed (orders are made this)
*/
class account 
{
	var $user_id;
	var $account_id;
	var $account_values;
	var $account_cost;
	var $account_direction;
	var $running_total;
	var $install_running_total;
	var $service_call_running_total;
	var $removal_running_total;
	
	var $agency_id;
	var $billing_method_id;
    function __construct($user_id, $account_id = '', $billing_method_id = 0) {
        global $database, $user;

        if ($billing_method_id == 1) {
            // Partial credits and refunds go on Agent Invoice for CC users
            $billing_method_id = 3;
        }
		
		if ($user_id and is_numeric($user_id) and (int) $user_id > 0) {
            // Is the user_id specified?  If so, use that use for the agency_id and billing_method_id
            $user_id = (int)$user_id;
            $this->user_id = $user_id;

            $user_sql = "select agency_id, billing_method_id from users where user_id = '" . $user_id . "'";
            $user_query = $database->query($user_sql);
            $user_res = $database->fetch_array($user_query);

            $this->agency_id = $user_res['agency_id'];
            if (!$billing_method_id) {
                $this->billing_method_id = $user_res['billing_method_id'];
            } else {
                $this->billing_method_id = $billing_method_id;
            }
		} else {
            // Otherwise, pull from the session user
			$this->user_id = $user->fetch_user_id();
            $this->agency_id = $user->agency_id;
            if (!$billing_method_id) {
                $this->billing_method_id=$user->fetch_billing_method_id();
                if (!isset($this->billing_method_id)) {
                    $this->billing_method_id = 2;
                }
            } else {
                $this->billing_method_id = $billing_method_id;
            }
		}
		
		
        $result=array();
		if (empty($account_id)) {
            if ($this->billing_method_id == 2) {
			    $query = $database->query("select account_id from " . TABLE_ACCOUNTS . " where agency_id = '{$this->agency_id}' and user_id = '0' limit 1");
                $result = $database->fetch_array($query);
            } else { // Default to Agent account, since CC user's refunds shouldn't go to their agencies.
			    $query = $database->query("select account_id from " . TABLE_ACCOUNTS . " where agency_id = '{$this->agency_id}' and user_id = '{$this->user_id}' limit 1");
                $result = $database->fetch_array($query);
            }
		} else {
			$result['account_id'] = $account_id;
        }

		if (!isset($result['account_id']) or empty($result['account_id'])) {
            if ($this->billing_method_id == 2) {
			    $database->query("insert into ". TABLE_ACCOUNTS . " (user_id, agency_id, running_total) values ('0', '{$this->agency_id}', 0)");
            } else { // Default to Agent Invoice, since CC user's refunds shouldn't go to their agencies.
                $database->query("insert into ". TABLE_ACCOUNTS . " (user_id, agency_id, running_total) values ('{$this->user_id}', '{$this->agency_id}', 0)");
            }
			$account_id = $database->insert_id();
		} else {
			$account_id = $result['account_id'];
		}
		

		$this->account_id = $account_id;
		$this->user_id = $user_id;
        $this->fetch_current_running_totals();

        //error_log("DEBUG: Account billing_method = {$this->billing_method_id}, id = {$result['account_id']}");
	}

	function return_account_id() {
		return $this->account_id;
	}

	function fetch_available_credit() {
		global $database;

		$query = $database->query("select running_total from " . TABLE_ACCOUNTS . " where account_id = '" . $this->account_id . "' limit 1");
        $result = $database->fetch_array($query);

        if (empty($result['running_total'])) {
            $this->running_total = 0;
			return 0;
        } else {
            $this->running_total = $result['running_total'];
			return $result['running_total'];
        }
	}

	function set_debit_amount($total, $reason, $description, $order_type_id, $order_id) {
		global $database, $user;

		$now = mktime();
		$month = date("n", $now);
		$year = date("Y", $now);

		$direction = '0';
		$type = '1';
		$status_id = '2';

		$running_total = $this->running_total;
		$running_total -= $total;

        $database->query("insert into " . TABLE_ACCOUNT_ITEMS . " (account_id, date_added, month_added, year_added, direction, total, type, status_id, running_total, order_type_id, reference_id, user_id, agency_id, billing_method_id, reason, details) values ('" . $this->account_id . "', '" . $now . "', '" . $month . "', '" . $year . "', '" . $direction . "', '" . $total . "', '" . $type . "', '" . $status_id . "', '" . $running_total . "', '" . $order_type_id . "', '" . $order_id . "'," .$this->user_id . "," . (empty($this->agency_id)?"NULL":$this->agency_id) ."," . (empty($this->billing_method_id)?"NULL":$this->billing_method_id) .", '" . addslashes($reason) . "', '" . addslashes($description) . "')");
        $account_item_id = $database->insert_id();

		$this->running_total = $running_total;
		$this->update_account_running_totals();

        $transaction_data = array(
            "amount" => $total,
            "billing_method_id" => $this->billing_method_id,
            "date_added" => mktime(),
            "user_id" => $user->fetch_user_id(),
            "account_id" => $this->account_id,
            "order_id" => $order_id,
            "account_item_id" => $account_item_id,
            "reason" => $reason,
            "details" => $description
        );

        Transaction::log($transaction_data);
	}

	function set_debit_credit_amount($total, $reason, $description, $order_type_id, $order_id) {
		global $database, $user;

		$now = mktime();
		$month = date("n", $now);
		$year = date("Y", $now);

		$direction = '0';
		$type = '6';
		$status_id = '2';

		$running_total = $this->running_total;
		$running_total -= $total;

		$database->query("insert into " . TABLE_ACCOUNT_ITEMS . " (account_id, date_added, month_added, year_added, direction, total, type, status_id, running_total, order_type_id, reference_id, user_id, agency_id, billing_method_id, reason, details) values ('" . $this->account_id . "', '" . $now . "', '" . $month . "', '" . $year . "', '" . $direction . "', '" . $total . "', '" . $type . "', '" . $status_id . "', '" . $running_total . "', '" . $order_type_id . "', '" . $order_id . "',".$this->user_id."," . (empty($this->agency_id)?"NULL":$this->agency_id) . "," . (empty($this->billing_method_id)?"NULL":$this->billing_method_id) .", '" . addslashes($reason) . "', '" .  addslashes($description) . "')");
        $account_item_id = $database->insert_id();

		$this->running_total = $running_total;
		$this->update_account_running_totals();

        $transaction_data = array(
            "amount" => $total,
            "billing_method_id" => $this->billing_method_id,
            "date_added" => mktime(),
            "user_id" => $user->fetch_user_id(),
            "account_id" => $this->account_id,
            "order_id" => $order_id,
            "account_item_id" => $account_item_id,
            "reason" => $reason,
            "details" => $description
        );

        Transaction::log($transaction_data);
	}

	function set_credit_amount($total, $reason, $description, $order_id, $type = 'cancel', $order_type_id = '') {
        global $database, $user;

		$now = mktime();
		$month = date("n", $now);
		$year = date("Y", $now);

		$direction = '1';
		if ($type == 'cancel') {
			$type = '4';
		} else {
			$type = '5';
		}
		$status_id = '2';

		$running_total = $this->running_total;
        $running_total += $total;

		$database->query("insert into " . TABLE_ACCOUNT_ITEMS . " (account_id, date_added, month_added, year_added, direction, total, type, status_id, running_total, reference_id,user_id, agency_id, billing_method_id, reason, details) values ('" . $this->account_id . "', '" . $now . "', '" . $month . "', '" . $year . "', '" . $direction . "', '" . $total . "', '" . $type . "', '" . $status_id . "', '" . $running_total . "', '" . $order_id . "',". (empty($this->user_id)?"NULL":$this->user_id) ."," . (empty($this->agency_id)?"NULL":$this->agency_id) . "," . (empty($this->billing_method_id)?"NULL":$this->billing_method_id) .", '" . addslashes($reason) . "', '" . addslashes($description) . "')");
        $account_item_id = $database->insert_id();

		$this->running_total = $running_total;
		$this->update_account_running_totals();

        $transaction_data = array(
            "amount" => -$total,
            "billing_method_id" => $this->billing_method_id,
            "date_added" => mktime(),
            "user_id" => $user->fetch_user_id(),
            "account_id" => $this->account_id,
            "order_id" => $order_id,
            "account_item_id" => $account_item_id,
            "reason" => $reason,
            "details" => $description
        );

        Transaction::log($transaction_data);
    }

	function apply_invoice_payment($total, $invoice_id) {
        global $database, $user;

        $parts = explode('-', $invoice_id);
        if (count($parts) != 4) {
            error_log('Malformed invoice id in account->apply_invoice_payment.');
            error_log("Tried to apply total of {$total} to Invoice # {$invoice_id}. (Agency_id {$invoice_agency_id}, User_id {$invoice_user_id})");
            return;
        }

        $now = mktime();
        $year = $parts[0];
        $month = $parts[1];

		$direction = '1';
        $type = '0';
        $status_id = '0';
        $order_id = '0';

        $reason = 'Invoice Payment';
        $description = 'Credit Card Payment on Invoice #'.$invoice_id;

		$running_total = $this->running_total;
        $running_total += $total;

		$database->query("insert into " . TABLE_ACCOUNT_ITEMS . " (account_id, date_added, month_added, year_added, direction, total, type, status_id, running_total, reference_id,user_id, agency_id, billing_method_id, reason, details) values ('" . $this->account_id . "', '" . $now . "', '" . $month . "', '" . $year . "', '" . $direction . "', '" . -$total . "', '" . $type . "', '" . $status_id . "', '" . $running_total . "', '" . $order_id . "',". (empty($this->user_id)?"NULL":$this->user_id) ."," . (empty($this->agency_id)?"NULL":$this->agency_id) . "," . (empty($this->billing_method_id)?"NULL":$this->billing_method_id) .", '" . addslashes($reason) . "', '" . addslashes($description) . "')");
        $account_item_id = $database->insert_id();

		$this->running_total = $running_total;
		$this->update_account_running_totals();

        $transaction_data = array(
            "amount" => -$total,
            "billing_method_id" => $this->billing_method_id,
            "date_added" => mktime(),
            "user_id" => $user->fetch_user_id(),
            "account_id" => $this->account_id,
            "account_item_id" => $account_item_id,
            "reason" => $reason,
            "details" => $description
        );

        Transaction::log($transaction_data);
    }

	function apply_credit($total, $reason, $description, $order_type_id, $order_id) {
		global $database, $user;

		$now = mktime();
		$month = date("n", $now);
		$year = date("Y", $now);

		$direction = '0';
		$type = '1';
		$status_id = '2';

		$running_total = $this->running_total;
		$running_total -= $total;

		$this->running_total = $running_total;
		$this->update_account_running_totals();

        $transaction_data = array(
            "amount" => $total,
            "billing_method_id" => $this->billing_method_id,
            "date_added" => mktime(),
            "user_id" => $user->fetch_user_id(),
            "account_id" => $this->account_id,
            "order_id" => $order_id,
            "reason" => $reason,
            "details" => $description
        );

        Transaction::log($transaction_data);
    }

	function apply_deferred($total, $description) {
	    return $this->set_credit_amount($total, "Deferred Billing", $description, 0, $type = 'deferred', $order_type_id = '');
    }

	function apply_extended_rental($total, $address_id, $description) {
        global $database, $user;

		$now = mktime();
		$month = date("n", $now);
		$year = date("Y", $now);

		$direction = '0';
		$type = '1';
		$status_id = '2';
        $order_id = '0';

        $reason = 'Extended Rental';

		$running_total = $this->running_total;
        $running_total -= $total;

		$database->query("insert into " . TABLE_ACCOUNT_ITEMS . " (account_id, date_added, month_added, year_added, direction, total, type, status_id, running_total, reference_id,user_id, agency_id, billing_method_id, reason, details) values ('" . $this->account_id . "', '" . $now . "', '" . $month . "', '" . $year . "', '" . $direction . "', '" . $total . "', '" . $type . "', '" . $status_id . "', '" . $running_total . "', '" . $order_id . "',". (empty($this->user_id)?"NULL":$this->user_id) ."," . (empty($this->agency_id)?"NULL":$this->agency_id) . "," . (empty($this->billing_method_id)?"NULL":$this->billing_method_id) .", '" . addslashes($reason) . "', '" . addslashes($description) . "')");
        $account_item_id = $database->insert_id();

		$this->running_total = $running_total;
		$this->update_account_running_totals();

        if ($total > 0) {
            $transaction_data = array(
                "amount" => $total,
                "billing_method_id" => $this->billing_method_id,
                "date_added" => mktime(),
                "address_id" => $address_id,
                "user_id" => $user->fetch_user_id(),
                "account_id" => $this->account_id,
                "account_item_id" => $account_item_id,
                "reason" => $reason,
                "details" => $description
            );

            Transaction::log($transaction_data);
        }
    }

	function update_account_running_totals() {
		global $database;
        $database->query("update " . TABLE_ACCOUNTS . " set running_total = '{$this->running_total}' where account_id = '{$this->account_id}' limit 1");
	}

	function fetch_current_running_totals() {
        return $this->fetch_available_credit();
	}

	function create_cancel_order_entry($order_id) {
		global $database;
		$query = $database->query("select o.order_total, o.order_type_id, ot.name, a.house_number, a.street_name from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a, " . TABLE_ORDER_TYPES . " ot where o.order_id = '" . $order_id . "' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id limit 1");
		$result = $database->fetch_array($query);
		if ($result['order_total'] > 0) {
			$this->set_credit_amount($result['order_total'], 'Cancellation of ' . $result['name'], 'Cancellation of ' . $result['name'] . ' at ' . $result['house_number'].' '.$result['street_name'], $order_id, 'cancel', $result['order_type_id']);
		}
    }

    public static function getAccountName($account_id) {
        global $database;
        if (empty($account_id)) {
            return "Credit Card";
        }
        $account_name = "";
        $query = $database->query("SELECT agency_id, user_id FROM " . TABLE_ACCOUNTS . " WHERE account_id = '{$account_id}' LIMIT 1");
        if ($result = $database->fetch_array($query)) {
            if ($result['user_id'] == 0 && $result['agency_id'] != 0) {
                $account_name = tep_get_agency_name($result['agency_id']);
            } elseif ($result['user_id'] != 0) {
                $account_name = tep_get_user_name($result['user_id']);
            }
        }
        $account_name = trim($account_name);
        if (empty($account_name)) {
            $account_name = "Defunct Account #{$account_id}";
        }
        return $account_name;
    }

    public static function getAccountId($user_id = 0, $agency_id = 0, $billing_method_id = 3, $force = true) {
        global $database;

        $account_id = 0;

        if (!$user_id && !$agency_id) {
            return $account_id;
        } else if (!$agency_id) {
            $s = "SELECT agency_id FROM users WHERE user_id = '{$user_id}'";
            $q = $database->query($s);
            foreach($database->fetch_array($q) as $r){
                $agency_id = $r['agency_id'];
            }
        }

        if ($billing_method_id == 2) {
            $s = "SELECT account_id, running_total FROM accounts WHERE agency_id = '{$agency_id}' AND user_id = '0' LIMIT 1";
            $q = $database->query($s);
            foreach($database->fetch_array($q) as $r){
                $account_id = $r['account_id'];
                $running_total = $r['running_total'];
            }
            if ($force && !$account_id) {
                $insert = "INSERT INTO accounts (user_id, agency_id) VALUES ('0', '{$agency_id}')";
                $database->query($insert);
                $account_id = $database->insert_id();
            }
        } else {
            $s = "SELECT account_id, running_total FROM accounts WHERE agency_id = '{$agency_id}' AND user_id = '{$user_id}' LIMIT 1";
            $q = $database->query($s);
            foreach($database->fetch_array($q) as $r){
                $account_id = $r['account_id'];
                $running_total = $r['running_total'];
            }
            if ($force && !$account_id) {
                $insert = "INSERT INTO accounts (user_id, agency_id) VALUES ('{$user_id}', '{$agency_id}')";
                $database->query($insert);
                $account_id = $database->insert_id();
            }
        }
        return $account_id;
    }
}
?>
