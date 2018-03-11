<?php
class Transaction {
    public static function log($data) {
        global $database;

        $error = "";

        $required = array("amount", "date_added", "user_id");
        $optional = array("billing_method_id", "account_id", "order_id", "address_id", "account_item_id", "reason", "details");
        $restricted = array("transaction_id", "running_total");

        # Required parameters
        foreach ($required as $param) {
            if (array_key_exists($param, $data)) {
                $$param = $data[$param];
            }
            if (!isset($$param)) {
                $error .= "Transaction log error: {$param} is required.\n";
            }
        }

        $amount = to_float($amount);

        # Optional parameters
        foreach ($optional as $param) {
            if (array_key_exists($param, $data)) {
                $$param = $data[$param];
            }
        }

        # Do some sanity checks
        if (isset($billing_method_id) && $billing_method_id > 1) {
            //if (!isset($account_item_id)) {
            //    $error .= "Transaction log error: account_item_id is required for non-CC transactions.\n";
            //}
            if (!isset($account_id)) {
                $error .= "Transaction log error: account_id is required for non-CC transactions.\n";
            }
        }
        
        # Default to NULL
        foreach ($optional as $param) {
            if (!isset($$param)) {
                $$param = "NULL";
            }
        }
        # Update strings
        if ($reason != "NULL") {
            $reason = "'{$database->input($reason)}'";
        } 
        if ($details != "NULL") {
            $details = "'{$database->input($details)}'";
        }

        # Restricted parameters
        foreach ($restricted as $param) {
            if (array_key_exists($param, $data)) {
                $$param = $data[$param];
            }
            if (isset($$param)) {
                $error .= "Transaction log error: {$param} is restricted.\n";
            }
        }

        if(!empty($error)) {
            error_log($error);
            return $error;
        }

        # Conditionally set running_total
        if ($billing_method_id == 1) {
            $running_total = 0;
        } else {
            $select = "SELECT running_total FROM " . TABLE_ACCOUNTS . " WHERE account_id = '{$account_id}'";
            $query = $database->query($select);
            if ($result = $database->fetch_array($query)) {
                $running_total = $result['running_total'];
            }
            if (!isset($running_total)) {
                $running_total = 0;
            }
        }

        $sql = "INSERT INTO " . TABLE_TRANSACTIONS . " (amount, billing_method_id, date_added, user_id, account_id, order_id, address_id, account_item_id, reason, details, running_total) VALUES ({$amount}, {$billing_method_id}, {$date_added}, {$user_id}, {$account_id}, {$order_id}, {$address_id}, {$account_item_id}, {$reason}, {$details}, {$running_total})";
        $database->query($sql);
    }
}
?>
