<?php
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

$deferred_total = tep_fill_variable('deferred_total', 'session', 0);
$deferred_transactions = tep_fill_variable('deferred_transactions', 'session', array());
$deferred_credit = tep_fill_variable('deferred_credit', 'session', 0);

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
    tep_redirect(FILENAME_DEFERRED_PAYMENT);

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
                            <h4>CONGRATULATIONS!</h4>  <p>You have successfully made a payment on your account. Thank you for your Business!</p>
                        </div> 
                    </td>
                </tr>
                <tr>
                    <td width="100%" style="text-align: center;">
                        <a class="btn" href="order_deferred_success_print.php5" target="_blank"><i class="icon-print"></i> Print</a>
                        <a class="btn" href="agent_active_addresses.php"><i class="icon-ok"></i> Finished - Return to Active Address List</a>
                    </td>
                </tr>                                
                <tr>
                    <td class="mainLarge"><h4 class="alert alert-info">Agency Information</h4></td>
                </tr>
                    <td width="100%">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="main" width="140"><b>Agent Name: </b></td><td class="main"><b><?php echo $user->fetch_user_name(); ?></b></td>
                            </tr>
                            <tr>
                                <td class="main" width="140"><b>Agent ID: </b></td><td class="main"><b><?php echo $agent_data['agent_id']; ?></b></td>
                            </tr>
                            <tr>
                                <td class="main" width="140">Agent Email: </td><td class="main"><?php echo $email_data['email_address']; ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140">Agency Name: </td><td class="main"><?php echo $agent_data['name']; ?></td>
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
        <td class="mainLarge" colspan="2"><h4 class="alert alert-info">Deferred Billing</h4></td>
    </tr>
    <tr>
        <td width="100%">
<?php 
echo DeferredBilling::applyTemplate(0, $deferred_total, $deferred_credit, $deferred_transactions, true, '', 'past');
?>
        </td>
    </tr>
    <tr>
        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
    </tr>
    <tr>
            <td width="100%" style="text-align: center;">
                    <a class="btn" href="order_deferred_success_print.php5" target="_blank"><i class="icon-print"></i> Print</a>
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
$session->php_session_register('deferred_total_print',$deferred_total);
$session->php_session_register('deferred_transactions_print',$deferred_transactions);
$session->php_session_register('deferred_credit_print',$deferred_credit);

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
$session->php_session_unregister('deferred_total');
$session->php_session_unregister('deferred_transactions');
$session->php_session_unregister('deferred_credit');
?>
