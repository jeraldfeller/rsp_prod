<?php
require_once 'invoice_functions.php';

/*
 * Part of Realty Sign Post (c) 2013 Realty Sign Post.
 * Description: Saves a new item to account_items. Accessed via AJAX. Requires get variables:
 *    date_added  timestamp
 *    reason      text
 *    details     text
 *    user_id     0 | user_id
 *    agency_id   0 | agency_id
 *    month       int
 *    year        int
 *
 * Original Author: Brad Berger <brad@brgr2.com>
 * See Version Control for full change history.
 *
 */

// Make sure it's only admin accessible. 
if (!is_admin()) {
  exit(json_encode(array('error' => true, 'msg' => 'Access denied.')));
}

// Include and get the variables needed.
$l = explode(',', 'account_item_id,date_added,reason,details,total,user_id,agency_id,month,year');
foreach ($l as $v) {
  if (!isset($_GET[$v])) {
    exit(json_encode(array('error' => true, 'msg' => $v . ' can not be empty')));
  }
  $$v = addslashes($_GET[$v]);
}

if (!$user_id && !$agency_id) {
    exit(json_encode(array('error' => true, 'msg' => 'user_id and agency_id can not both be empty')));
} else if (!$agency_id) {
    $s = "SELECT agency_id FROM users WHERE user_id = '{$user_id}'";
    $q = $database->query($s);
    while ($r = $database->fetch_array($q)) {
        $agency_id = $r['agency_id'];
    }
}

// Date added. If not set, make it now.
if (!$date_added) {
  $date_added = time();
}

$s = "SELECT account_item_id, total FROM account_items WHERE account_item_id = '{$account_item_id}' LIMIT 1";
$q = $database->query($s);
$found = false;
if ($r = $database->fetch_array($q)) {
    $found = true;
    $old_total = $r['total'];
    $difference = $total - $old_total;
    //error_log("Found Diff: {$difference}");
}
if (!isset($difference)) {
    $old_total = 0;
    $difference = $total;
    //error_log("Calc Diff: {$difference}");
}

$account_id = 0;
if (!$user_id) {
    $s = "SELECT account_id, running_total FROM accounts WHERE agency_id = {$agency_id} AND user_id = 0 LIMIT 1";
    $q = $database->query($s);
    while ($r = $database->fetch_array($q)) {
        $account_id = $r['account_id'];
        $running_total = $r['running_total'];
    }
    if (!$account_id) {
        $insert = "INSERT INTO accounts (user_id, agency_id) VALUES ('0', '{$agency_id}')";
        $database->query($insert);
        $account_id = $database->insert_id();
    }
} else {
    $s = "SELECT account_id, running_total FROM accounts WHERE agency_id = {$agency_id} AND user_id = {$user_id} LIMIT 1";
    $q = $database->query($s);
    while ($r = $database->fetch_array($q)) {
        $account_id = $r['account_id'];
        $running_total = $r['running_total'];
    }
    if (!$account_id) {
        $insert = "INSERT INTO accounts (user_id, agency_id) VALUES ('{$user_id}', '{$agency_id}')";
        $database->query($insert);
        $account_id = $database->insert_id();
    }
}
if (!isset($running_total)) {
    $running_total = 0;
}

// Refunds/credits have a direction of 1. So all minus values should refelct that here.
$total = (float) $total;
if ($total < 0) {
  $direction = 1;
} else {
  $direction = 0;
}
//exit(json_encode(array('error' => true, 'msg' => "{$reason} {$total}")));

if ($user_id) {
  $billing_method_id = 3;
} else {
  $billing_method_id = 2;
}

if ($found) {
    $sql = "UPDATE account_items SET reason='{$reason}',details='{$details}',date_added='{$date_added}',total={$total} WHERE account_item_id = {$account_item_id}";
    $query = $database->query($sql);
} else {
    $sql = "INSERT INTO account_items(account_id,date_added,agency_id,user_id,reason,details,total,month_added,year_added,direction,billing_method_id) VALUES ({$account_id},{$date_added},{$agency_id},{$user_id},'{$reason}','{$details}','{$total}',{$month},{$year},{$direction},{$billing_method_id})";
    $query = $database->query($sql);
    $account_item_id = $database->insert_id();
}

if ($query) {
  if ($difference) {
    $sql2 = "UPDATE accounts SET running_total = '{$running_total}' - '{$difference}' WHERE account_id = '$account_id' LIMIT 1";
    $database->query($sql2);

    $transaction_data = array(
      "amount" => $difference,
      "billing_method_id" => $billing_method_id,
      "date_added" => mktime(),
      "user_id" => $user->fetch_user_id(),
      "account_id" => $account_id,
      "account_item_id" => $account_item_id,
      "reason" => 'Invoice Adjustment',
      "details" => "{$reason} {$details}" 
    );
    Transaction::log($transaction_data);
  }

  exit(json_encode(array('error' => false, 'msg' => 'Saved.')));
} else {
  exit(json_encode(array('error' => true, 'msg' => 'Something went wrong.')));
}
?>
