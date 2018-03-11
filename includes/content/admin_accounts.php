<?php
$create = tep_fill_variable('create');
$transactions_since = tep_fill_variable('transactions_since', 'get', date('m/d/Y', strtotime("7 days ago")));
$search_text = tep_fill_variable('search', 'get');
$accounts_with = tep_fill_variable('accounts_with', 'get', 'credit');
$billing_method_id = tep_fill_variable('billing_method_id', 'get', '');
$agent_id = tep_fill_variable('agent_id', 'get', '');
$total = tep_fill_variable('total', 'get', '');
if (!empty($create)) {
    $billing_method_id = tep_fill_variable('billing_method_id');
    $agent_id = tep_fill_variable('agent_id');
    if ($billing_method_id == '3') {
        $agency_id = tep_fetch_user_agency_id($agent_id);
    } else {
        $agent_id = '';
        $agency_id = tep_fill_variable('agency_id', 'post', tep_fetch_user_agency_id($agent_id));
    }
    $amount = tep_fill_variable('amount');
    $details = tep_fill_variable('details');

    $error = '';

    if ($billing_method_id == 3 && empty($agent_id)) {
        $error .= "Agent is required. ";
    }
    if ($billing_method_id == 2 && empty($agency_id)) {
        $error .= "Agency is required. ";
    }
    if ($billing_method_id != 2 && $billing_method_id != 3) {
        $error .= "Valid billing method is required. ";
    }

    if (!$error) {
        $account_id = 0;
        $running_total = 0;
        $amount = -$amount;
        if ($billing_method_id == 2) {
            $s = "SELECT account_id, running_total FROM accounts WHERE agency_id = {$agency_id} AND user_id = 0 LIMIT 1";
            $q = $database->query($s);
            foreach($q as $r){
                $account_id = $r['account_id'];
                $running_total = $r['running_total'];
            }
            if (!$account_id) {
                $insert = "INSERT INTO accounts (user_id, agency_id) VALUES ('0', '{$agency_id}')";
                $database->query($insert);
                $account_id = $database->insert_id();
            }
        } else {
            $s = "SELECT account_id, running_total FROM accounts WHERE agency_id = {$agency_id} AND user_id = {$agent_id} LIMIT 1";
            $q = $database->query($s);
            foreach($q as $r){
                $account_id = $r['account_id'];
                $running_total = $r['running_total'];
            }
            if (!$account_id) {
                $insert = "INSERT INTO accounts (user_id, agency_id) VALUES ('{$agent_id}', '{$agency_id}')";
                $database->query($insert);
                $account_id = $database->insert_id();
            }
        }

        if ($amount != 0 && $account_id) {
            $running_total = $running_total - $amount;
            $details = $database->input($details);
    
            $update = "UPDATE " . TABLE_ACCOUNTS . " SET running_total = '{$running_total}' WHERE account_id = '{$account_id}'";
            $database->query($update);

            // Don't actually send billing_method_id, since this adjustment does not hit an invoice.
            // To make an adjustment that does affect an invoice, adjust either an order or the invoice itself.

            $transaction_data = array(
                "amount" => $amount,
                "date_added" => mktime(),
                "user_id" => $user->fetch_user_id(),
                "account_id" => $account_id,
                "reason" => 'Account Adjustment',
                "details" => "{$details}"
                );
            $error .= Transaction::log($transaction_data);
        } else {
            $error .= "Unable to make adjustment. ";
        }
    } 
    if (!empty($error)) {
?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="muted"><strong>ERROR:</strong> <?php echo $error; ?></span>
</div>
<?php
    }
}

// Account Balances
$balances = array();
if ($accounts_with == "negative") {
    $balance_sql = "SELECT account_id, running_total FROM " . TABLE_ACCOUNTS . " WHERE running_total < 0 ORDER BY running_total ASC";
} elseif ($accounts_with == "deferred") {
    $balance_sql = "SELECT a.account_id, a.running_total FROM " . TABLE_ACCOUNTS . " a INNER JOIN " . TABLE_USERS . " u ON (u.user_id = a.user_id) WHERE a.running_total < 0 AND u.billing_method_id = 1 ORDER BY a.running_total ASC";
} else {
    $balance_sql = "SELECT account_id, running_total FROM " . TABLE_ACCOUNTS . " WHERE running_total > 0 ORDER BY running_total DESC";
}
$query = $database->query($balance_sql);
foreach($query as $result){
    $balances[] = array("account_name" => account::getAccountName($result['account_id']), "balance" => $result['running_total']);
}

// Transaction Log
if (!empty($transactions_since)) {
    $date_since = strtotime($transactions_since);
    $transactions_where = " WHERE date_added >= '{$date_since}'";
} else {
    $transactions_where = "";
}

$transactions = array();
$transactions_sql = "SELECT transaction_id, user_id, account_id, billing_method_id, reason, details, amount, running_total, order_id, address_id, date_added FROM " . TABLE_TRANSACTIONS . $transactions_where . " ORDER BY transaction_id DESC";
$query = $database->query($transactions_sql);
foreach($query as $result){
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
?>
<script language="javascript" data-cfasync="false">
$(document).ready(function () {
      $.extend( $.fn.dataTableExt.oSort, {
        "dollars-pre": function ( a ) {
          var x = a.replace( /\$/, "" );
          return parseFloat( x );
        },
        "dollars-asc": function ( a, b ) {
          return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },
        "dollars-desc": function ( a, b ) {
          return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
      });

    $("#agency_select").hide();
    $("#billing_method_id").change(function () {
        if ($(this).val() == "3") {
            $("#agency_select").hide();
            $("#agent_select").show();
        } else if ($(this).val() == "2") {
            $("#agent_select").hide();
            $("#agency_select").show();
        }
    });

    $("#accounts_with").change(function () {
        $("#accounts_with_form").submit();
    });

    $("#transactions_since").datepicker();

    $("#account-balances").dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span4'i><'span8'p>>",
        "sScrollY": "200px",
        "bPaginate": false,
        "bFilter": false,
        "bStateSave": false,
        "bSortClasses": false,
        "bInfo": false,
        "oLanguage": {
            "sEmptyTable": "No matching accounts found."
        },
        "aoColumns": [
            null,
            {"sType": "dollars"}
        ],
        "aaSorting": [[1, "desc"]]
    });
    $("#transactions").dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span4'i><'span8'p>>",
        "bFilter": true,
        "sPaginationType": "bootstrap",
        "bStateSave": false,
        "bSortClasses": false,
        "bLengthChange": false,
        "oLanguage": {
            "sEmptyTable": "No transactions found."
        },
        "iDisplayLength": 50,
        "aoColumns": [
            null,
            null,
            null,
            null,
            null,
            null,
            {"sType": "dollars"},
            {"sType": "dollars"}
        ],
        "aaSorting": [[0, "desc"]]
    });

});
</script>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6">
            <form method="get" id="accounts_with_form">
                <div>
                    <strong class="lead">Account Balances</strong>
                    <div style="display: inline" class="pull-right form-horizontal">
                        <input type="hidden" name="transactions_since" value="<?php echo $transactions_since; ?>">
                        <select name="accounts_with" id="accounts_with">
                            <option value="credit"<?php echo $accounts_with == "credit" ? " selected" : ""; ?>>with Credit</option>
                            <option value="negative"<?php echo $accounts_with == "negative" ? " selected" : ""; ?>>Debit Balance - Invoice</option>
                            <option value="deferred"<?php echo $accounts_with == "deferred" ? " selected" : ""; ?>>Debit Balance - CC</option>
                        </select>
                    </div>
                </div>
            </form>
            <table id="account-balances" class="table table-condensed table-striped">
                <thead>
                    <tr><th>Account Name</th><th>Balance</th></tr>
                </thead>
                <tbody>
                <?php
                foreach ($balances as $account) {
                    echo "<tr><td>{$account['account_name']}</td><td>\${$account['balance']}</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="span6">
            <div>
                <strong class="lead">Adjustments</strong>
            </div>
            <p>Positive amounts credit accounts, negative amounts debit accounts.</p>
            <form method="post">
                <div class="row-fluid">
                    <label for="billing_method_id" class="span4">Account Type:</label>
                    <select name="billing_method_id" id="billing_method_id" class="span8">
                        <option value="2">Agency</option>
                        <option value="3" selected>Agent</option>
                    </select>
                </div>
                <div class="row-fluid" id="agency_select" style="display: none">
                    <label for="agency_id" class="span4">Account Name:</label>
                    <?php echo tep_draw_agency_pulldown('agency_id', '', $params = 'id="agency_id" class="span8"') ?>
                </div>
                <div class="row-fluid" id="agent_select">
				
                    <label for="agent_id" class="span4">Account Name:</label>
                    <?php echo tep_draw_agent_pulldown('agent_id', $agent_id, $params = 'id="agent_id" class="span8"') ?>
                </div>
                <div class="row-fluid">
		
                    <label for="amount" class="span4">Amount:</label>
                    <input name="amount" id="amount" class="span8" required="required" style="text-align: right;" placeholder="0.00" value="<?php echo $total; ?>"/>
                </div>
                <div class="row-fluid">
                    <label for="details" class="span4">Details:</label>
                    <textarea name="details" class="span8" id="details" required="required" rows="4" placeholder="Reason for adjustment"></textarea>
                </div>
                <div class="row-fluid">
                    <input type="submit" name="create" id="create" class="pull-right btn btn-primary" value="Create Adjustment" />
                </div>
            </form>
        </div>
    </div>
    <hr>
    <div class="row-fluid">
        <div style="margin-bottom: 5px">
            <form method="get" class="form-inline">
                <strong class="lead">Transactions</strong>
                <div style="display: inline" class="pull-right form-horizontal">
                    <label for="transactions_since">Since:</label>
                    <input type="text" id="transactions_since" name="transactions_since" value="<?php echo $transactions_since; ?>">
                    <input type="hidden" id="search_text" name="search" value="<?php echo $search_text; ?>">
                    <input type="submit" class="btn" name="transactions_filter" value="Filter">
                </div>
            </form>
        </div>
        <table id="transactions" class="table table-condensed table-striped">
            <thead>
                <tr><th style='display: none'>ID</th><th>Date/Time</th><th>User Name</th><th>Account Name</th><th>Reason</th><th>Details</th><th>Amount</th><th>Balance</th></tr>
            </thead>
            <tbody>
                <?php
                foreach ($transactions as $t) {
                    echo "<tr><td style='display: none'>{$t['transaction_id']}</td><td>{$t['date_added']}</td><td>{$t['user_name']}</td><td>{$t['account_name']}</td><td>{$t['reason']}</td><td>{$t['details']}</td><td>\${$t['amount']}</td><td>\${$t['running_total']}</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).on('ready',function(){

        $('#transactions_filter input:text').val($('#search_text').val());

        $('#transactions').dataTable().fnFilter($('#search_text').val());

        $('#transactions_filter input:text').on('keyup', function(){
           $('#search_text').val($(this).val());
        });


    });
</script>