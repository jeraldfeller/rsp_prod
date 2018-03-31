<?php
require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';
require_once 'invoice_functions.php';

if (!is_admin()) {
    echo "Access Denied.";
    die;
}

// The month and year of the **invoice** to balance to
$month = date('n', strtotime('1 month ago'));
$year = date('Y', strtotime('1 month ago'));
$now = mktime();

if ($month == 12) {
    $ts = strtotime(($year+1) . "-01-01");
} else {
    $ts = strtotime($year . "-" . ($month+1) . "-01");
}

$agency_sql = "
    select a.agency_id, sum(order_total) as uninvoiced_total
    from orders o, users u, agencys a 
    where u.agency_id = a.agency_id and o.billing_method_id = '2' 
    and (
          order_status_id in ('1', '2', '5') or
          (order_status_id = '3' AND date_completed >= '{$ts}')
        )
    and o.user_id = u.user_id
    group by a.agency_id";

$agent_sql = "
    select user_id, sum(order_total) as uninvoiced_total
    from orders o
    where o.billing_method_id = '3' 
    and (
          order_status_id in ('1', '2', '5') or
          (order_status_id = '3' AND date_completed >= '{$ts}')
        )
    group by user_id";

$account_sql = "select account_id, running_total from accounts";

$agencies = array();
$agents = array();
$accounts = array();

$query = $database->query($agency_sql);
foreach($database->fetch_array($query) as $result){
    $agencies[] = $result;
}

$query = $database->query($agent_sql);
foreach($database->fetch_array($query) as $result){
    $agents[] = $result;
}

$query = $database->query($account_sql);
foreach($database->fetch_array($query) as $result){
    $accounts[$result['account_id']] = -1 * $result['running_total'];
}

$sql = array();

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<body>\n";
echo "<h1>Report</h1>\n";
echo "<table border='1'>\n";
echo "<thead><tr><th>Account</th><th>Invoice Balance</th><th>Uninvoiced Total</th><th>Account Balance</th><th>Delta</th></tr></thead>\n";
echo "<tbody>\n";

foreach ($agencies as $index => $record) {
    $agency_id = $record["agency_id"];
    $uninvoiced_total = $record["uninvoiced_total"];
    $account_id = account::getAccountId(0, $agency_id, 2, false);
    $invoice_total = str_replace(",", "", get_account_total(0, $agency_id, $month, $year));
    $agencies[$index]["invoice_total"] = $invoice_total;
    $agencies[$index]["account_id"] = $account_id;
    $name = tep_get_agency_name($agency_id);
    if ($account_id) {
        $account_balance = $accounts[$account_id];
    } else {
        $account_balance = 0;
    }
    $agencies[$index]["account_balance"] = $account_balance;
    $delta = ($uninvoiced_total + $invoice_total) - $account_balance;
    if ($delta) {
        echo "<tr><td>{$name}</td><td>{$invoice_total}</td><td>{$uninvoiced_total}</td><td>{$account_balance}</td><td>{$delta}</td></tr>\n";
        $new_running_total = -1 * ($invoice_total + $uninvoiced_total);
        if ($account_id) {
            $sql[] = "UPDATE accounts SET running_total = '{$new_running_total}' WHERE account_id = '{$account_id}'; --{$name}";
            $sql[] = "INSERT INTO transactions (user_id, account_id, date_added, amount, running_total, reason, details) \n".
                     "  VALUES ('{$user->fetch_user_id()}', '{$account_id}', '{$now}', '{$delta}', '{$new_running_total}', 'Account Adjustment', 'Balance Account to Invoice');";
        } else {
            $sql[] = "INSERT INTO accounts (user_id, agency_id, install_running_total, service_call_running_total, removal_running_total, running_total)\n".
                     "  VALUES ('0', '{$agency_id}', '0', '0', '0', '{$new_running_total}'); --{$name}";
            $sql[] = "INSERT INTO transactions (user_id, account_id, date_added, amount, running_total, reason, details) \n".
                     "  VALUES ('{$user->fetch_user_id()}', '{$account_id}', '{$now}', '{$delta}', '{$new_running_total}', 'Account Adjustment', 'Balance Account to Invoice');";
        }
    }
}

foreach ($agents as $index => $record) {
    $agent_id = $record["user_id"];
    $uninvoiced_total = $record["uninvoiced_total"];
    $account_id = str_replace(",", "", account::getAccountId($agent_id, 0, 3, false));
    $invoice_total = get_account_total(0, $agent_id, $month, $year);
    $agents[$index]["invoice_total"] = $invoice_total;
    $agents[$index]["account_id"] = $account_id;
    $name = tep_get_user_name($agent_id);
    if ($account_id) {
        $account_balance = $accounts[$account_id];
    } else {
        $account_balance = 0;
    }
    $agents[$index]["account_balance"] = $account_balance;
    $agent_data = tep_fetch_agent_data($agent_id);
    $agency_id = $agent_data["agency_id"];

    $delta = ($uninvoiced_total + $invoice_total) - $account_balance;
    if ($delta) {
        echo "<tr><td>{$name}</td><td>{$invoice_total}</td><td>{$uninvoiced_total}</td><td>{$account_balance}</td><td>{$delta}</td></tr>\n";
        $new_running_total = -1 * ($invoice_total + $uninvoiced_total);
        if ($account_id) {
            $sql[] = "UPDATE accounts SET running_total = '{$new_running_total}' WHERE account_id = '{$account_id}'; --{$name}";
            $sql[] = "INSERT INTO transactions (user_id, account_id, date_added, amount, running_total, reason, details) \n".
                     "  VALUES ('{$user->fetch_user_id()}', '{$account_id}', '{$now}', '{$delta}', '{$new_running_total}', 'Account Adjustment', 'Balance Account to Invoice');";
        } else {
            $sql[] = "INSERT INTO accounts (user_id, agency_id, install_running_total, service_call_running_total, removal_running_total, running_total)\n".
                     "  VALUES ('{$agent_id}', '{$agency_id}', '0', '0', '0', '{$new_running_total}'); --{$name}";
            $sql[] = "INSERT INTO transactions (user_id, account_id, date_added, amount, running_total, reason, details) \n".
                     "  VALUES ('{$user->fetch_user_id()}', '{$account_id}', '{$now}', '{$delta}', '{$new_running_total}', 'Account Adjustment', 'Balance Account to Invoice');";
        }
    }
}
echo "</tbody>\n</table>\n";
echo "<h1>SQL UPDATES TO RUN</h1>\n";
echo "<pre>\n";
foreach ($sql as $s) {
    echo "{$s}\n";
}
echo "</pre>\n";
echo "</body>\n</html>";
?>
