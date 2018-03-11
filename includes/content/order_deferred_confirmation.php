<?php
if (isset($_SESSION['user_id'])) {
    $user_id=$_SESSION['user_id'];
    $user_name=$_SESSION['user_name'];
} else {
    $error->add_error('account_create_confirmation', 'User information not available so payment confirmation aborted.');
    tep_redirect(FILENAME_ACCOUNT_OVERVIEW);
}
$user_id=$user->fetch_user_id();
$user_name=$_SESSION['user_name'];
$page_action = tep_fill_variable('page_action', 'get');
$email_data =  tep_fetch_email_data($user_id);
$agent_data =  tep_fetch_agent_data($user_id);
$tos = tep_fill_variable('tos', 'post');
$pna = tep_fill_variable('pna', 'post');

$payment_method = tep_fill_variable('payment_method_id', 'session');
if (empty($payment_method)) {
    tep_redirect(FILENAME_DEFERRED_PAYMENT);
}
//Get all variable from user table
$query_user=$database->query("select * from ". TABLE_USERS ." where user_id='$user_id'");
$result_user = $database->fetch_array($query_user);
$email_address=$result_user['email_address'];
$agency_id=$result_user['agency_id'];
$agent_id=$result_user['agent_id'];
$query_agency=$database->query("select * from ". TABLE_AGENCYS ." where agency_id='$agency_id'");
$result_agency = $database->fetch_array($query_agency);
$agency_name=$result_agency['name'];
$agency_address=$result_agency['address'];

// Deferred Billing
$deferred_total = 0;
$account_id = account::getAccountId($user->fetch_user_id(), $user->agency_id, $payment_method, false);
$deferred = new DeferredBilling($account_id);
$deferred_total = $deferred->getTotal();
$deferred_summary = $deferred->summarize();

if (!empty($page_action) && ($page_action == 'submit')) {
    if (empty($tos) || (!$tos)) {
        $error->add_error('account_create_confirmation', 'You must agree to the Terms of Service before placing an order.');
    }
    if (!$error->get_error_status('account_create_confirmation')) {
        //Proccess.

        if (($payment_method == BILLING_METHOD_CREDIT) && $deferred_total > 0){
            //Credit card so lets try the payment.

            $cc_proccessing = new cc_proccessing();
            $error_code = '';
            $error_text = '';
            $error_string = '';
            $cc_proccessing->pre_transaction(tep_fill_variable('cc_number', 'session'), tep_fill_variable('cc_type', 'session'), $error_code, $error_text);
            $errorFlag = true;
            if (empty($error_code)) {
                $agency_query = $database->query("select a.name, a.service_level_id, a.billing_method_id, a.address, a.contact_name, a.contact_phone from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.user_id = '" . $user->fetch_user_id() . "' and u.agency_id = a.agency_id limit 1");
                $agency_result = $database->fetch_array($agency_query);

                $cc_proccessing->set_proccessing_variable('bill_first_name', tep_fill_variable('user_first_name', 'session'));
                $cc_proccessing->set_proccessing_variable('bill_last_name', tep_fill_variable('user_last_name', 'session'));
                $cc_proccessing->set_proccessing_variable('bill_address_one', tep_fill_variable('cc_billing_street', 'session'));
                $cc_proccessing->set_proccessing_variable('bill_city', tep_fill_variable('cc_billing_city', 'session'));
                $cc_proccessing->set_proccessing_variable('bill_zip_or_postal_code', tep_fill_variable('cc_billing_zip', 'session'));
                $cc_proccessing->set_proccessing_variable('bill_company', stripslashes($agency_result['name']));
                $cc_proccessing->set_proccessing_variable('bill_country_code', 'US');
                $cc_proccessing->set_proccessing_variable('bill_phone', $agency_result['contact_phone']);
                $cc_proccessing->set_proccessing_variable('order_description', "Deferred Payment: {$deferred_summary}");
                $infoStr = date('ymd-His').str_replace(' ','',"Account" . account::getAccountName($account_id));
                $cc_proccessing->set_proccessing_variable('invoice_number', $infoStr);
                $cc_proccessing->set_proccessing_variable('order_id', $infoStr);
                $cc_proccessing->set_proccessing_variable('credit_card_number', tep_fill_variable('cc_number', 'session'));
                $cc_proccessing->set_proccessing_variable('charge_type', 'AUTH');
                $cc_proccessing->set_proccessing_variable('expire_month', tep_fill_variable('cc_month', 'session'));
                $cc_proccessing->set_proccessing_variable('expire_year', tep_fill_variable('cc_year', 'session'));
                $cc_proccessing->set_proccessing_variable('credit_card_verification_number', tep_fill_variable('cc_verification_number', 'session'));
                $cc_proccessing->set_proccessing_variable('charge_total', number_format($deferred_total, 2));
                $cc_proccessing->set_proccessing_variable('order_user_id', $user->fetch_user_id());
                $cc_proccessing->set_proccessing_variable('reference_id', $_SERVER['REMOTE_ADDR']);
                $cc_proccessing->set_proccessing_variable('card_brand', tep_fill_variable('cc_type', 'session'));
                $cc_proccessing->preform_transaction();

                if ($cc_proccessing->return_response() == 1) {
                    //accepted
                    $errorFlag = false;
                    $actuallyChargedTotal = $deferred_total;
                    $account = new account($user->fetch_user_id(), $account_id, $payment_method);
                    $account->apply_deferred($deferred_total, $deferred_summary);
                } else {
                    $errorFlag = true;
                    $error_string = 'There was an error processing the credit card you entered.  Please try again.';
                    $error->cc_error(__FILE__.':'.__LINE__." $user_name($user_id) rcode ".$cc_proccessing->return_response().' "'. implode("\n",$cc_proccessing->error_messages()) ."\"");
                    $error_string .= ' The gateway responded: ';
                    foreach($cc_proccessing->error_messages() as $value) { $error_string .= ' '.$value; }
                }
            } else {
                $errorFlag = true;
                $error_string = 'The credit card you entered is invalid.  Please try again.';
                $error->cc_error(__FILE__.':'.__LINE__." $user_name($user_id) \"$error_text\"");
            }
            if ($errorFlag) {
                $session->php_session_register('cc_error', $error_string);
                tep_redirect(FILENAME_DEFERRED_PAYMENT);
            }        
        }

        // Save the deferred billing information
        $session->php_session_register('deferred_total', $deferred->getTotal());
        $session->php_session_register('deferred_transactions', $deferred->getTransactions());
        $session->php_session_register('deferred_credit', $deferred->getCredit());

        //Send the email.
        tep_format_deferred_billing_email($user_id);
        tep_redirect(FILENAME_DEFERRED_SUCCESS);

    }
}
?>
<form class="order_form" action="<?php echo PAGE_URL; ?>?page_action=submit" method="post"  onsubmit="if(document.forms[0].tos[0].checked == false) { alert('You must agree to the Terms of Service before a order can be proccessed.'); return false; } else { return false; }">
<table width="100%" cellspacing="0" cellpadding="0">
<?php
if ($error->get_error_status('account_create_confirmation')) {
?>
  <tr>
    <td class="mainError"><?php echo $error->get_error_string('account_create_confirmation'); ?></td>
  </tr>
  <tr>
    <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
<?php
}
?>
  <tr>
    <td width="100%">
      <table width="100%" cellspacing="0" cellpadding="2" class="pageBox">
        <tr>
          <td class="main">
              <div class="alert alert-info alert-block">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <i class="icon-4x pull-left icon-warning-sign badge badge-info" style="padding: 6px;"></i>
                  <h4>IMPORTANT!</h4> You must receive the Payment Confirmation e-mail to be certain we have received your payment. 
                  Be sure to complete the payment process until you see a CONGRATULATIONS page.
              </div>

              &PAGE_TEXT

          </td>
        </tr>
        <tr>
          <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
        </tr>
        <tr>
          <td class="mainLarge">Agency Information</td>
        </tr>
        <tr>
          <td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
        </tr>
        <tr>
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
          <td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
        </tr>
        <tr>
          <td class="mainLarge">Payment Information</td>
        </tr>
        <tr>
          <td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
        </tr>
        <tr>
          <td width="100%">
            <table cellspacing="0" cellpadding="0">
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
                <td class="main" width="140">Security code: </td><td class="main"><?php echo (tep_fill_variable('cc_verification_number', 'session')); ?></td>
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
        <tr>
          <td height="8"><img src="images/pixel_trans.gif" height="8" width="1"></td>
        </tr>
<?php
    echo $deferred->createSiteHTML(0);
if (($payment_method == BILLING_METHOD_CREDIT) && ($deferred_total > 0)) {
?>
        <tr>
          <td class="main">When you press the "Process Order" button your credit card will be charged.</td>
        </tr>
        <tr>
          <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
        </tr>
<?php
}
?>
        <tr>
          <td class="main">I have read and accept the Realty Sign Post Terms of Service <input type="checkbox" id="tos" name="tos" value="1" /> <a target="_blank" href="<?php echo FILENAME_TERMS_OF_SERVICE; ?>">(Click here to View)</a></td>
        </tr>
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
          <td align="right"><?php echo tep_create_button_submit('proccess_order', 'Process Order'); ?></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
  </tr>
  <tr>
    <td align="center" class="mediumGrey"><?php echo tep_show_deferred_footer(); ?></td>
  </tr>
</table>
</form>
