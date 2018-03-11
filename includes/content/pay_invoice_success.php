<?php
require_once('lib/invoices/invoice_functions.php');

if (isset($_SESSION['user_id'])) {
    $user_id=$_SESSION['user_id'];
    $user_name=$_SESSION['user_name'];
} else {
    $error->add_error('account_create_success', 'User information not available so order success aborted.');
    tep_redirect(FILENAME_ACCOUNT_OVERVIEW);
}

$page_action = tep_fill_variable('page_action', 'get');
$tos = tep_fill_variable('tos', 'post');
$pna = tep_fill_variable('pna', 'post');

$cc_type = tep_fill_variable('cc_type', 'session', '');
$cc_name = tep_fill_variable('cc_name', 'session', '');
$cc_number = tep_fill_variable('cc_number', 'session', '');
$cc_month = tep_fill_variable('cc_month', 'session', '');
$cc_year = tep_fill_variable('cc_year', 'session', '');
$cc_verification_number = tep_fill_variable('cc_verification_number', 'session', '');
$cc_billing_street = tep_fill_variable('cc_billing_street', 'session', '');
$cc_billing_city = tep_fill_variable('cc_billing_city', 'session', '');
$cc_billing_zip = tep_fill_variable('cc_billing_zip', 'session', '');

$payment_method = tep_fill_variable('payment_method_id', 'session');
if (empty($payment_method))
    tep_redirect(FILENAME_PAY_INVOICE_PAYMENT);

// Invoice Information

$invoice_id = tep_fill_variable('invoice_id', 'session', '');
$invoice_total = tep_fill_variable('invoice_total', 'session', 0);
$invoice_user_id = tep_fill_variable('invoice_user_id', 'session', 0);
$invoice_agency_id = tep_fill_variable('invoice_agency_id', 'session', 0);
$account_id = tep_fill_variable('invoice_account_id', 'session', 0);

if ($invoice_user_id == 0) {
    $billing_method_id = 2;
} else {
    $billing_method_id = 3;
}

$invoice_total = +$invoice_total;

if (empty($invoice_id) || $invoice_total <= 0 || ($invoice_user_id == 0 && $invoice_agency_id == 0)) {
    // We need an ID, a good total, etc.
    tep_redirect(FILENAME_ACCOUNT_OVERVIEW);
}

if ($invoice_user_id != 0 && ($user->fetch_user_id() != $invoice_user_id || $billing_method_id != 3)) {
    // Keep Agent Invoice private
    tep_redirect(FILENAME_403);
}

if ($invoice_agency_id != 0 && ($user->agency_id != $invoice_agency_id || !is_ap())) {
    // AP can only see their own invoices
    tep_redirect(FILENAME_403);
}

if (!(is_ap() || $billing_method_id == 3)) {
    // What business do they have here?
    tep_redirect(FILENAME_403);
}

$email_data = tep_fetch_email_data($user->fetch_user_id());
$agent_data = tep_fetch_agent_data($user->fetch_user_id());
?>
<script data-cfasync="false">
$(document).ready(function () {
    $.each($('img'),function(i,img){
        if($(img).attr('height') != undefined) {
            $(img).css('height',$(img).attr('height'));
        }
        if($(img).attr('width') != undefined) {
            $(img).css('width',$(img).attr('width'));
        }
    });
    $("body").css("padding", "2% 20%");
});
</script>

<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%">
            <table width="100%" cellspacing="0" cellpadding="2" class="pageBox">
                <tr>
                    <td class="main">
                        <!-- &PAGE_TEXT-->
                        <div class="alert alert-success alert-block" style="min-height: 60px;">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="icon-4x pull-left icon-ok-sign"></i>
                            <h4>CONGRATULATIONS!</h4>  <p>You have successfully made an invoice payment. Thank you for your Business!</p>
                        </div> 
                    </td>
                </tr>
                <tr>
                    <td width="100%" style="text-align: center;">
                        <a class="btn" href="pay_invoice_success_print.php5" target="_blank"><i class="icon-print"></i> Print</a>
                        <a class="btn" href="account_overview.php"><i class="icon-ok"></i> Finished - Return to Account Overview</a>
                    </td>
                </tr>                                
                <tr>
                    <td class="mainLarge"><h4 class="alert alert-info">Invoice Information</h4></td>
                </tr>
                    <td width="100%">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="main" width="140">Account Name: </td><td class="main"><?php echo account::getAccountName($account_id); ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140">Invoice: </td><td class="main"><?php echo $invoice_id; ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140"><b>Total Paid: </b></td><td class="main"><b>$<?php echo number_format($invoice_total, 2); ?></b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="8"><img src="images/pixel_trans.gif" height="8" width="1"></td>
                </tr>
                <tr>
                    <td class="mainLarge" colspan="2"><h4 class="alert alert-info">Payment Information</h4></td>
                </tr>
                <tr>
                    <td width="100%">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="main" width="140">Payment Method: </td><td class="main"><?php echo tep_get_payment_type_name($payment_method); ?></td>
                            </tr>
<?php
    if ($payment_method == BILLING_METHOD_CREDIT) {
?>
                            <tr>
                                <td class="main" width="140">Name on Card: </td><td class="main"><?php echo tep_fill_variable('cc_name', 'session'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140">Card Type: </td><td class="main"><?php echo ucfirst(strtolower(tep_fill_variable('cc_type', 'session'))); ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140">Card Number: </td><td class="main"><?php echo tep_secure_credit_card_number(tep_fill_variable('cc_number', 'session')); ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140">Security code: </td><td class="main"><?php echo tep_fill_variable('cc_verification_number', 'session'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140" valign="top">Billing Address: </td><td class="main"><?php echo tep_fill_variable('cc_billing_street', 'session').'<br>'. tep_fill_variable('cc_billing_city', 'session').'<br>'. tep_fill_variable('cc_billing_zip', 'session'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
<?php
}
?>
                <tr>
                    <td height="8"><img src="images/pixel_trans.gif" height="8" width="1"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td height="8"><img src="images/pixel_trans.gif" height="8" width="1"></td>
    </tr>
    <tr>
            <td width="100%" style="text-align: center;">
                    <a class="btn" href="pay_invoice_success_print.php5" target="_blank"><i class="icon-print"></i> Print</a>
                    <a class="btn" href="<?php echo FILENAME_ACCOUNT_OVERVIEW; ?>"><i class="icon-ok"></i> Finished - Return to Account Overview</a>
            </td>
    </tr>
</table>
<?php

$session->php_session_register('payment_method_id_print',tep_fill_variable('payment_method_id', 'session'));
$session->php_session_register('cc_type_print',tep_fill_variable('cc_type', 'session'));
$session->php_session_register('cc_name_print',tep_fill_variable('cc_name', 'session'));
$session->php_session_register('cc_number_print',tep_fill_variable('cc_number', 'session'));
$session->php_session_register('cc_month_print',tep_fill_variable('cc_month', 'session'));
$session->php_session_register('cc_year_print',tep_fill_variable('cc_year', 'session'));
$session->php_session_register('cc_verification_number_print',tep_fill_variable('cc_verification_number', 'session'));
$session->php_session_register('cc_billing_street_print',tep_fill_variable('cc_billing_street', 'session'));
$session->php_session_register('cc_billing_city_print',tep_fill_variable('cc_billing_city', 'session'));
$session->php_session_register('cc_billing_zip_print',tep_fill_variable('cc_billing_zip', 'session'));
$session->php_session_register('invoice_id_print',$invoice_id);
$session->php_session_register('invoice_total_print',$invoice_total);
$session->php_session_register('invoice_account_id_print',$account_id);
$session->php_session_register('invoice_user_id_print',$invoice_user_id);
$session->php_session_register('invoice_agency_id_print',$invoice_agency_id);

$session->php_session_unregister('payment_method_id');
$session->php_session_unregister('cc_type');
$session->php_session_unregister('cc_name');
$session->php_session_unregister('cc_number');
$session->php_session_unregister('cc_month');
$session->php_session_unregister('cc_year');
$session->php_session_unregister('cc_verification_number');
$session->php_session_unregister('cc_billing_street');
$session->php_session_unregister('cc_billing_city');
$session->php_session_unregister('cc_billing_zip');
$session->php_session_unregister('invoice_id');
$session->php_session_unregister('invoice_total');
$session->php_session_unregister('invoice_account_id');
$session->php_session_unregister('invoice_user_id');
$session->php_session_unregister('invoice_agency_id');
?>
