<?php

require_once 'invoice_functions.php';

if (!is_admin()) {
  exit(json_encode(array('error' => true, 'msg' => 'Access denied.')));
}

$account_item_id = 0;
if (!empty($_GET['account_item_id'])) {
  $account_item_id = (int) $_GET['account_item_id'];
}
if (!$account_item_id) {
  exit(json_encode(array('error' => true, 'msg' => 'Nothing to delete.')));
}

$difference = 0;
$account_id = 0;
$billing_method_id = 0;

$s = "SELECT total, billing_method_id, account_id FROM account_items WHERE account_item_id = '{$account_item_id}' LIMIT 1";
$q = $database->query($s);
foreach($database->fetch_array($q) as $r){
    $difference = $r['total'];
    $account_id = $r['account_id'];
    $billing_method_id = $r['billing_method_id'];
}
error_log("Difference: {$difference}");
if ($difference) {
  $sql = "UPDATE accounts SET running_total = running_total - '{$difference}' WHERE account_id = '$account_id' LIMIT 1";
  $database->query($sql);

  $transaction_data = array(
    "amount" => $difference,
    "billing_method_id" => $billing_method_id,
    "date_added" => mktime(),
    "user_id" => $user->fetch_user_id(),
    "account_id" => $account_id,
    "account_item_id" => $account_item_id,
    "reason" => 'Invoice Adjustment',
    "details" => "Line Item Deleted"
  );
  Transaction::log($transaction_data);
}

$s = "DELETE FROM account_items WHERE account_item_id = {$account_item_id} LIMIT 1";
$q = $database->query($s);

exit(json_encode(array('error' => false, 'msg' => 'Item deleted.')));
?>
