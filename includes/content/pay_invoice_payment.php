<?php
require_once('lib/invoices/invoice_functions.php');

$page_action = tep_fill_variable('page_action', 'get');
$submit_string = tep_fill_variable('submit_string_y', 'post');

$start_day = tep_fill_variable('start_day', 'post', date("d", tep_fill_variable('schedualed_start', 'session', mktime(0, 0, 0, date("n", mktime()), (date("d", mktime()) + 1)))));
$start_month = tep_fill_variable('start_month', 'post', date("n", tep_fill_variable('schedualed_start', 'session', mktime(0, 0, 0, date("n", mktime()), (date("d", mktime()) + 1)))));
$start_year = tep_fill_variable('start_year', 'post', date("Y", tep_fill_variable('schedualed_start', 'session', mktime(0, 0, 0, date("n", mktime()), (date("d", mktime()) + 1)))));

$request_payment = true;

$payment_method = 1;

// Invoice Information
$invoice_id = tep_fill_variable('invoice_id', 'post', tep_fill_variable('invoice_id', 'session', ''));
$invoice_total = tep_fill_variable('invoice_total', 'post', tep_fill_variable('invoice_total', 'session', ''));
$invoice_user_id = tep_fill_variable('invoice_user_id', 'post', tep_fill_variable('invoice_user_id', 'session', ''));
$invoice_agency_id = tep_fill_variable('invoice_agency_id', 'post', tep_fill_variable('invoice_agency_id', 'session', ''));

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

if ($invoice_agency_id != 0) {
    $account_id = account::getAccountId(0, $invoice_agency_id, 2, true);
} else {
    $account_id = account::getAccountId($invoice_user_id, 0, 3, true);
}

$session->php_session_register('invoice_id', $invoice_id);
$session->php_session_register('invoice_total', $invoice_total);
$session->php_session_register('invoice_user_id', $invoice_user_id);
$session->php_session_register('invoice_agency_id', $invoice_agency_id);
$session->php_session_register('invoice_account_id', $account_id);

// Handle form submission
if (!empty($page_action) && ($page_action == 'submit') && !empty($submit_string)) {
    if (($payment_method == '1') && $request_payment) {
        $cc_type = tep_fill_variable('cc_type');
        $cc_name = tep_fill_variable('cc_name');
        $cc_number = str_replace(array('-', ' '), '', tep_fill_variable('cc_number'));
        $cc_month = tep_fill_variable('cc_month');
        $cc_year = tep_fill_variable('cc_year');
        $cc_verification_number = tep_fill_variable('cc_verification_number');
        $cc_billing_street = tep_fill_variable('cc_billing_street');
        $cc_billing_city = tep_fill_variable('cc_billing_city');
        $cc_billing_zip = tep_fill_variable('cc_billing_zip');

        $cc_proccessing = new cc_proccessing();
        $error_code = '';
        $error_text = '';
        $cc_proccessing->pre_transaction($cc_number, $cc_type, $error_code, $error_text);

        if (!empty($error_code)) {
            $error->add_error('account_create_payment', 'The credit card you entered is invalid.  Please try again.');
            $error->cc_error(__FILE__.':'.__LINE__.' '.$user->fetch_user_name().'('.$user->fetch_user_id().") \"$error_text\"");
        } else {
            if (empty($cc_billing_street)) {
                if (!empty($error_text)) $error_text .= '<br>';
                $error_text .= 'Please enter your billing street';
            }
            if (empty($cc_billing_city)) {
                if (!empty($error_text)) $error_text .= '<br>';
                $error_text .= 'Please enter your billing city';
            }
            if (empty($cc_billing_zip)) {
                if (!empty($error_text)) $error_text .= '<br>';
                $error_text .= 'Please enter your billing zip';
            }
            if (empty($cc_verification_number)) {
                if (!empty($error_text)) $error_text .= '<br>';
                $error_text .= 'Please enter your security code';
            }
            if (!empty($error_text)) {
                $error->add_error('account_create_payment', $error_text);
            }
        }
    }

    // Save CC details for next screen if no errors

    if (!$error->get_error_status('account_create_payment')) {
        $session->php_session_register('payment_method_id', $payment_method);
        if ($payment_method == BILLING_METHOD_CREDIT && $request_payment) {
            //Credit card.
            $session->php_session_register('cc_type', $cc_type);
            $session->php_session_register('cc_name', $cc_name);
            $session->php_session_register('cc_number', $cc_number);
            $session->php_session_register('cc_month', $cc_month);
            $session->php_session_register('cc_year', $cc_year);
            $session->php_session_register('cc_verification_number', $cc_verification_number);
            $session->php_session_register('cc_billing_street', $cc_billing_street);
            $session->php_session_register('cc_billing_city', $cc_billing_city);
            $session->php_session_register('cc_billing_zip', $cc_billing_zip);
        }
        tep_redirect(FILENAME_PAY_INVOICE_CONFIRMATION);
    }
} // end submit

if ($session->php_session_is_registered('cc_error')) 
{
    $error->add_error('account_create_payment', tep_fill_variable('cc_error', 'session'));
    $session->php_session_unregister('cc_error');
}

?>

<div class="alert alert-info alert-block">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="icon-4x pull-left icon-warning-sign badge badge-info" style="padding: 6px;"></i>
    <h4>IMPORTANT!</h4> You must receive the Payment Confirmation e-mail to be certain we have received your payment. 
    Be sure to complete the payment process until you see a CONGRATULATIONS page.
</div> 

<form class="order_form" action="<?php echo PAGE_URL; ?>?page_action=submit" method="post" name="frm1">
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%">
            <table width="100%" cellspacing="0" cellpadding="2" class="pageBox" border="0">
                <tr>
                    <td class="mainLarge" colspan="2">Invoice Information</td>
                </tr>
                <tr>
                    <td width="100%">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="main">Account: </td><td><?php echo account::getAccountName($account_id); ?></td>
                            </tr>
                            <tr>
                                <td class="main" width="140">Invoice: </td><td><?php echo $invoice_id; ?></td>
                            </tr>
                            <tr>
                                <td class="main"><b>Total Due:</b> </td><td><b>$<?php echo number_format($invoice_total, 2); ?></b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
<?php
$text = 'Please enter your Credit Card Details below.';
if ($request_payment) {
?>
                <tr>
                    <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
                </tr>
                <tr>
                    <td class="mainLarge" colspan="2">Payment Information</td>
                </tr>
                <tr>
                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
                </tr>

                <tr>
                    <td class="main"><?php echo $text; ?></td>
                </tr>
                <tr>
                    <td height="3"><img scr="images/pixel_trans.gif" height="3" width="1"/></td>
                </tr>
                <tr>
                    <td height="5"><img scr="images/pixel_trans.gif" height="5" width="1"/></td>
                </tr>

<?php
    if ($payment_method == BILLING_METHOD_CREDIT) {
        $cc_type = tep_fill_variable('cc_type', 'post', tep_fill_variable('cc_type', 'session'));
        $cc_name = tep_fill_variable('cc_name', 'post', tep_fill_variable('cc_name', 'session'));
        $cc_number = tep_fill_variable('cc_number', 'post', tep_fill_variable('cc_number', 'session'));
        $cc_month = tep_fill_variable('cc_month', 'post', tep_fill_variable('cc_month', 'session', date("n", mktime())));
        $cc_year = tep_fill_variable('cc_year', 'post', tep_fill_variable('cc_year', 'session', (date("Y", mktime())+1)));
        $cc_verification_number = tep_fill_variable('cc_verification_number', 'post', tep_fill_variable('cc_verification_number', 'session'));
        $cc_billing_street = tep_fill_variable('cc_billing_street', 'post', tep_fill_variable('cc_billing_street', 'session'));
        $cc_billing_city = tep_fill_variable('cc_billing_city', 'post', tep_fill_variable('cc_billing_city', 'session'));
        $cc_billing_zip = tep_fill_variable('cc_billing_zip', 'post', tep_fill_variable('cc_billing_zip', 'session'));
?>
                    <tr>
                        <td width="100%">
                            <table cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="main" colspan="2"><b>Credit Card Details</b></td>
                                </tr>
                                <tr>
                                    <td class="main">Name on Card: </td><td><input type="text" name="cc_name" value="<?php echo $cc_name; ?>" /></td>
                                </tr>
                                <tr>
                                    <td class="main">Card Type</td><td><?php echo tep_draw_credit_card_type_pulldown('cc_type', $cc_type); ?></td>
                                </tr>
                                <tr>
                                    <td class="main">Card Number: </td><td><input type="text" name="cc_number" value="<?php echo $cc_number; ?>" /></td>
                                </tr>
                                <tr>
                                    <td class="main">Expiry Date: </td><td><?php echo tep_draw_month_pulldown('cc_month', $cc_month); ?>/<?php echo tep_draw_year_pulldown('cc_year', $cc_year); ?></td>
                                </tr>
                                <tr>
                                    <td class="main">Security Code: </td><td><input type="text" name="cc_verification_number" value="<?php echo $cc_verification_number; ?>" /></td>
                                </tr>
                                    <td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td><tr>
                                </tr>
                                <tr>
                                    <td class="main"><b>Billing Address</b></td>
                                </tr>
                                <tr>
                                    <td class="main" valign="top">Street: </td>
                                    <td><input name="cc_billing_street" value="<?php echo $cc_billing_street; ?>" /></td>
                                </tr>
                                <tr>
                                    <td class="main" valign="top">City: </td>
                                    <td><input name="cc_billing_city" value="<?php echo $cc_billing_city; ?>" /></td>
                                </tr>
                                <tr>
                                    <td class="main" valign="top">Zip: </td>
                                    <td><input name="cc_billing_zip" value="<?php echo $cc_billing_zip; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
<?php
        echo CC_WARN_1;
    } else {
?>

<?php
    }
} else {
    echo '<input type="hidden" name="payment_method" value="1">';
}
?>
            </table>
        </td>
    </tr>
    <tr>
        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
    </tr>
    <tr>
        <td width="100%">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="right"><?php echo tep_create_button_submit('proceed_to_final_review', 'Proceed to Final Review', ' name="submit_string"'); ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
    </tr>
    <tr>
        <td align="center" class="mediumGrey"><?php echo tep_show_pay_invoice_footer(); ?></td>
    </tr>
</table>
</form>
