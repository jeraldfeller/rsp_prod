<?php
    $rsid = null;
    if(!empty($_GET['rsid'])) {
        $_COOKIE['rsid'] = $_GET['rsid'];
    }

    if(!empty($_COOKIE['rsid'])) {
        $rsid = $_COOKIE['rsid'];
    }

	include('includes/application_top.php');
	
    if (isset($_SESSION['user_id'])) {
	    $user_id=$_SESSION['user_id'];
        $user_name=$_SESSION['user_name'];
    } else {
    ?>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
        <td class="mainError" colspan="2">User information not available so order_success_print aborted.</td>
        </tr>
    </table>
<?php
    die();
    }
    

	$page_action = tep_fill_variable('page_action', 'get');
	$tos = tep_fill_variable('tos', 'post');

    $invoice_total = tep_fill_variable('invoice_total_print', 'session', 0);
    $invoice_id = tep_fill_variable('invoice_id_print', 'session', 0);
    $invoice_user_id = tep_fill_variable('invoice_user_id_print', 'session', 0);
    $invoice_agency_id = tep_fill_variable('invoice_agency_id_print', 'session', 0);
    $account_id = tep_fill_variable('invoice_account_id_print', 'session', 0);

    $payment_method = tep_fill_variable('payment_method_id_print', 'session');
    if (empty($payment_method)) {
        tep_redirect(FILENAME_ACCOUNT_OVERVIEW);
    }

    $email_data = tep_fetch_email_data($user->fetch_user_id());
    $agent_data = tep_fetch_agent_data($user->fetch_user_id());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Success</title>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<meta name="keywords" content="" />
<meta name="description" content="" />
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style1 {
	color: #FFFFFF;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
}
.style2 {
	color: #000000;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style4 {
	font-size: 17px;
	color: #000000;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {color: #0099FF}
.style6 {
	color: #000000;
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
}
-->
</style></head>

<body onLoad="window.print();">
<table width="80%" cellspacing="0" cellpadding="0" align="center">
   <tr>
		<td align="center"><img name="head_r2_c2" src="images/head_r2_c2.jpg" width="310" height="98" border="0" id="head_r2_c2" alt="" /></td>
	</tr>
	<tr>
		<td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
	  <td valign="top" align="center"><span class="headerFirstWord">Payment Confirmation</span> </td>
	</tr>
	<tr>
		<td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
		<td width="100%">
			<table width="100%" cellspacing="0" cellpadding="2" class="pageBox">
				<tr>
					<td class="mainLarge">Invoice Information</td>
				</tr>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				<tr>
                    <td width="100%">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="main" width="140"><b>Account Name: </b></td><td class="main"><b><?php echo account::getAccountName($account_id); ?></b></td>
                            </tr>
                            <tr>
                                <td class="main" width="140"><b>Invoice: </b></td><td class="main"><b><?php echo $invoice_id; ?></b></td>
                            </tr>
                            <tr>
                                <td class="main" width="140"><b>Total Paid: </b></td><td class="main"><b>$<?php echo number_format($invoice_total, 2); ?></b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				<tr>
					<td class="mainLarge" colspan="2">Payment Information</td>
				</tr>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
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
								<td class="main" width="140">Name on Card: </td><td class="main"><?php echo tep_fill_variable('cc_name_print', 'session'); ?></td>
							</tr>
							<tr>
								<td class="main" width="140">Card Type: </td><td class="main"><?php echo ucfirst(strtolower(tep_fill_variable('cc_type_print', 'session'))); ?></td>
							</tr>
							<tr>
								<td class="main" width="140">Card Number: </td><td class="main"><?php echo tep_secure_credit_card_number(tep_fill_variable('cc_number_print', 'session')); ?></td>
							</tr>
							<tr>
								<td class="main" width="140" valign="top">Billing Address: </td><td class="main"><?php echo tep_fill_variable('cc_billing_street_print', 'session').'<br>'. tep_fill_variable('cc_billing_city_print', 'session').'<br>'. tep_fill_variable('cc_billing_zip_print', 'session'); ?></td>
							</tr>
							<tr>
								<td class="main" width="140"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
							</tr>
							<?php
								}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<td height="8"><img src="images/pixel_trans.gif" height="8" width="1"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
	</tr>
	<tr>
		<td height="20"><hr /></td>
	</tr>
    <tr>
     <td class="style6" align="center"><small>P.O. Box 641, McLean, VA 22101-0641 | Email: info@realtysignpost.com | Fax to: 703-995-4567 or 202-478-2131</small></td>
	</tr>
	<tr>
		<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
	</tr>
</table>
</body>
</html>
<?php
	$session->php_session_unregister('payment_method_id_print');
	$session->php_session_unregister('cc_type_print');
	$session->php_session_unregister('cc_name_print');
	$session->php_session_unregister('cc_number_print');
	$session->php_session_unregister('cc_month_print');
	$session->php_session_unregister('cc_year_print');
	$session->php_session_unregister('cc_verification_number_print');
	$session->php_session_unregister('cc_billing_street_print');
	$session->php_session_unregister('cc_billing_city_print');
	$session->php_session_unregister('cc_billing_zip_print');
	$session->php_session_unregister('invoice_id_print');
	$session->php_session_unregister('invoice_user_id_print');
	$session->php_session_unregister('invoice_agency_id_print');
	$session->php_session_unregister('invoice_account_id_print');
	$session->php_session_unregister('invoice_total_print');
?>
