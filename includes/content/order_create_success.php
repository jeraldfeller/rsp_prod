<?php
// Updated 1/10/13 brad@brgr2.com
@session_start(); 


if(isset($_GET['print'])) {
	
	
	  $rsid = null;
    if(!empty($_GET['rsid'])) {
        $_COOKIE['rsid'] = $_GET['rsid'];
    }

    if(!empty($_COOKIE['rsid'])) {
        $rsid = $_COOKIE['rsid'];
    }
	
    /*if (isset($_SESSION['user_id'])) {
	    $user_id=$_SESSION['user_id'];
        $user_name=$_SESSION['user_name'];
    } else {
    ?>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
        <td class="mainError" colspan="2">User information not available so order_success aborted.</td>
        </tr>
    </table>
<?php
    die();
    }*/
	
	if($user->fetch_user_id()=='' or $user->fetch_user_id()==false or $user->fetch_user_id()==0) {
		/*$error->add_error('order_create_confirmation', 'User information not available so order confirmation aborted.');
		tep_redirect(FILENAME_ORDER_CREATE);*/
		?>
		<table width="100%" cellspacing="0" cellpadding="0">
			<tr>
			<td class="mainError" colspan="2">User information not available. Please re-login to the website and place order again here - <a href="index.php?action=logout">Logoff</a></td>
			</tr>
		</table>
		<?
		die();
	}
    

	$page_action = tep_fill_variable('page_action', 'get');
	$order_type = tep_fill_variable('order_type_id', 'session');
	$tos = tep_fill_variable('tos', 'post');
	$pna = tep_fill_variable('pna', 'post');

	$sc_reason = tep_fill_variable('sc_reason', 'session');
	$sc_reason_4  = tep_fill_variable('sc_reason_4', 'session');
	$sc_reason_5  = tep_fill_variable('sc_reason_5', 'session');
	$sc_reason_7  = tep_fill_variable('sc_reason_7', 'session');
	$equipment  = tep_fill_variable('equipment', 'session', array());
	$install_equipment  = tep_fill_variable('install_equipment', 'session', array());
    $remove_equipment  = tep_fill_variable('remove_equipment', 'session', array());
    $date_added = tep_fill_variable('date_added', 'session');
    
    // mjp
    $cc_type = tep_fill_variable('cc_type', 'session', '');
    $cc_name = tep_fill_variable('cc_name', 'session', '');
    $cc_number = tep_fill_variable('cc_number', 'session', '');
    $cc_month = tep_fill_variable('cc_month', 'session', '');
    $cc_year = tep_fill_variable('cc_year', 'session', '');
    $cc_verification_number = tep_fill_variable('cc_verification_number', 'session', '');
    $cc_billing_street = tep_fill_variable('cc_billing_street', 'session', '');
    $cc_billing_city = tep_fill_variable('cc_billing_city', 'session', '');
    $cc_billing_zip = tep_fill_variable('cc_billing_zip', 'session', '');

    $deferred_total = tep_fill_variable('deferred_total', 'session', 0);
    $deferred_transactions = tep_fill_variable('deferred_transactions', 'session', array());
    $deferred_credit = tep_fill_variable('deferred_credit', 'session', 0);


    if (empty($order_type)) {
        tep_redirect(FILENAME_ORDER_CREATE);
    }
	

    if($order_type == '1') {
        $shipping_address = tep_fill_variable('street_name', 'session');
    } else {
        $shipping_address = tep_fill_variable('address_id', 'session');
    }
    if (empty($shipping_address)) {
        tep_redirect(FILENAME_ORDER_CREATE_ADDRESS);
    }
    $payment_method = tep_fill_variable('payment_method_id', 'session');
    if (empty($payment_method)) {
        tep_redirect(FILENAME_ORDER_CREATE_PAYMENT);
    }
    //Get all variable from user table
    $query_user=$database->query("select * from ". TABLE_USERS ." where user_id='".$user->fetch_user_id()."'");
    $result_user = $database->fetch_array($query_user);
    $email_address=$result_user['email_address'];
    $agency_id=$result_user['agency_id'];
    $agent_id=$result_user['agent_id'];
    $query_agency=$database->query("select * from ". TABLE_AGENCYS ." where agency_id='$agency_id'");
    $result_agency = $database->fetch_array($query_agency);
    $agency_name=$result_agency['name'];
    $agency_address=$result_agency['address'];
    //Get all the variables.
    $special_instructions = tep_fill_variable('special_instructions', 'session');
    $optional = tep_fill_variable('optional', 'session', array());
    if ($order_type == '1') {
        $optional = parse_equipment_array($optional);
        //$session->php_session_register('optional', $optional);
    }

    $address_id = tep_fill_variable('address_id', 'session');
    $house_number = trim(tep_fill_variable('house_number', 'session'));
    $street_name = tep_fill_variable('street_name', 'session');
    $adc_page = tep_fill_variable('adc_page', 'session');
    $adc_letter = tep_fill_variable('adc_letter', 'session');
    $adc_number = tep_fill_variable('adc_number', 'session');
    $city = tep_fill_variable('city', 'session');
    $zip = tep_fill_variable('zip', 'session');
    $zip4 = tep_fill_variable('zip4', 'session');
    $zip4_code = $zip4;
    $state = tep_fill_variable('state', 'session');
    $county = tep_fill_variable('county', 'session');
    $number_of_posts = tep_fill_variable('number_of_posts', 'session');
    $promo_code = tep_fill_variable('promo_code', 'session');
    $cross_street_directions = tep_fill_variable('cross_street_directions', 'session');
    $schedualed_start = tep_fill_variable('schedualed_start', 'session');
    $miss_utility_yes_no = tep_fill_variable('miss_utility_yes_no', 'session');
    $lamp_yes_no = tep_fill_variable('lamp_yes_no', 'session');
    $lamp_use_gas = tep_fill_variable('lamp_use_gas', 'session');

    $miss_utility_string = "";
    if ($miss_utility_yes_no == "yes") {
        $miss_utility_string = "Miss Utility call requested.";
    } else if ($lamp_yes_no == "no") {
        $miss_utility_string = "No lamp on property.";
    } else if ($lamp_use_gas == "yes") {
        $miss_utility_string = "Gas lamp on property.";
    } else if ($lamp_use_gas == "unsure") {
        $miss_utility_string = "Possible gas lamp on property.";
    } else if ($lamp_use_gas == "no") {
        $miss_utility_string = "No gas lamp on property.";
    }

    $email_data = tep_fetch_email_data($user->fetch_user_id());
    $agent_data = tep_fetch_agent_data($user->fetch_user_id());
    $service_area_window = tep_fetch_service_area_window(tep_fetch_zip4_service_area($zip4));
    if ($service_area_window == 0) {
        $service_area_window = 5;
    }
    if (tep_date_is_saturday($schedualed_start)) {
        $service_area_window++;
    }
    $schedualed_end = add_business_days($schedualed_start, $service_area_window-1);
    $miss_utility_start = subtract_business_days($schedualed_start, MISS_UTILITY_DELAY);
    $miss_utility_end = subtract_business_days($schedualed_start, 1);
    if ($order_type == 1 && ($miss_utility_yes_no == 'yes' || ($lamp_yes_no == 'yes' && $lamp_use_gas != 'no'))) {
        $show_miss_utility = true;
    } else {
        $show_miss_utility = false;
    }
		
	//Work out if this is a rush or sat install and create the extra cost.
	$extra_cost = tep_fetch_extra_cost($schedualed_start);
    $extended_cost = tep_fetch_service_area_cost(tep_fetch_zip4_service_area($zip4)); //mjp
	$address_information = tep_fetch_address_information($address_id);
	
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
	  <td valign="top" align="center"><span class="headerFirstWord">Order Confirmation</span> </td>
	</tr>
	<tr>
		<td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
		<td width="100%">
			<table width="100%" cellspacing="0" cellpadding="2" class="pageBox">
				<tr>
					<td class="mainLarge">Agent Information</td>
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
					<td class="mainLarge">Address Information</td>
				</tr>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td class="main" width="140"><b>Activity Requested:</b> </td><td class="main"><b><?php echo tep_get_order_type_name($order_type); ?></b></td>
							</tr>
							<?php
							if ($show_miss_utility) {
							?>
							<tr>
							    <td class="main" width="140">Miss Utility<br>Marking Window: </td><td class="main"><?php echo date("n/d/Y", $miss_utility_start); ?> - <?php echo date("n/d/Y", $miss_utility_end); ?></td>
							</tr>
							<?php
							}
							?>
							<tr>
							    <td class="main" width="140"><b>Date Range for<br>Order Completion:</b> </td><td class="main"><b><?php echo date("n/d/Y", $schedualed_start); ?> - <?php echo date("n/d/Y", $schedualed_end); ?></b></td>
							</tr>
                            <tr>
                                <td class="main">Street Address: </td><td class="main"><?php echo "{$house_number} {$street_name}"; ?></td>
                            </tr>
							<tr>
								<td class="main">City: </td><td class="main"><?php echo $city; ?></td>
							</tr>
							<tr>
								<td class="main">Zip+4: </td><td class="main"><?php echo $zip4; ?></td>
							</tr>
							<tr>
								<td class="main">County: </td><td class="main"><?php echo tep_get_county_name($county); ?></td>
							</tr>
							<tr>
								<td class="main">State: </td><td class="main"><?php echo tep_get_state_name($state); ?></td>
							</tr>
							<tr>
								<td class="main">Number of Posts: </td><td class="main"><?php echo $number_of_posts;; ?></td>
							</tr>
							<tr>
								<td class="main">Crossstreet/Directions: </td><td class="main"><?php echo $cross_street_directions; ?></td>
							</tr>
							<?php
							$service_area_id = tep_fetch_zip4_service_area($zip4_code);
								if (tep_fetch_service_area_window($service_area_id) > 0) {
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2"><b>This address has a <?php echo tep_fetch_service_area_window($service_area_id); ?> business day installation window (excludes weekends, Federal Holidays and severe weather days).</b></td>
							</tr>
							<?php
								}
							?>
						</table>
					</td>
				</tr>
				<?php
					$order = new orders('fetch', tep_fill_variable('order_id', 'session'));
					$data = $order->fetch_order();
					
					$base_cost = $data['base_cost'];
					$extended_cost = $data['extended_cost'];
					$equipment_cost = $data['equipment_cost'];
					$extra_cost = $data['extra_cost'];
					$deposit_cost = $data['deposit_cost'];
					$discount_cost = $data['discount_cost'];
					$total = $data['order_total'];
                    $credit = tep_fill_variable('credit', 'session', 0);
                    $total -= $credit;

          if ($total + $credit > 0) {
				?>
				<tr>
					<td height="8"><img src="images/pixel_trans.gif" height="8" width="1"></td>
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
								<td class="main" width="140">Name on Card: </td><td class="main"><?php echo tep_fill_variable('cc_name', 'session'); ?></td>
							</tr>
							<tr>
								<td class="main" width="140">Card Type: </td><td class="main"><?php echo ucfirst(strtolower(tep_fill_variable('cc_type', 'session'))); ?></td>
							</tr>
							<tr>
								<td class="main" width="140">Card Number: </td><td class="main"><?php echo tep_secure_credit_card_number(tep_fill_variable('cc_number', 'session')); ?></td>
							</tr>
							<tr>
								<td class="main" width="140" valign="top">Billing Address: </td><td class="main"><?php echo tep_fill_variable('cc_billing_street', 'session').'<br>'. tep_fill_variable('cc_billing_city', 'session').'<br>'. tep_fill_variable('cc_billing_zip', 'session'); ?></td>
							</tr>
							<tr>
								<td class="main" width="140"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
							</tr>
							<?php
								}
							?>
							<?php
								if (SHOW_PROMO_CODE_AREA == 'true') {
							?>
							<?php
								if ($promo_code == '') {
									$promo_string = 'None Entered';
								} elseif (tep_promotional_code_is_valid($promo_code)) {
									$promo_string = $promo_code;
								} else {
									$promo_string = 'Invalid Code';
								}
							?>
							<tr>
								<td class="main">Promotional Code: </td><td class="main"><?php echo $promo_string; ?></td>
							</tr>
							<?php
								}
							?>
						</table>
					</td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td height="8"><img src="images/pixel_trans.gif" height="8" width="1"></td>
				</tr>
				<tr>
					<td class="mainLarge" colspan="2">Extra Information and Optional Extras</td>
				</tr>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				
				<?php
					if ($order_type == ORDER_TYPE_SERVICE) {
				?>
				<tr>
					<td width="100%">
						<table width="100%" cellspacing="0" cellpadding="0">
							
							<?php
								if ($sc_reason == '1') {
									$string = 'Exchange Rider';
								} elseif ($sc_reason == '2') {
									$string = 'Install New Rider or BBox';
								} elseif ($sc_reason == '3') {
									$string = 'Replace/Exchange Agent SignPanel';
								} elseif ($sc_reason == '4') {
									$string = 'Post Leaning/Straighten Post';
								} elseif ($sc_reason == '5') {
									$string = 'Move Post';
								} elseif ($sc_reason == '6') {
									$string = 'Install equipment forgotten at install';
								} elseif ($sc_reason == '7') {
									$string = 'Other';
								}
								
							?>
							<tr>
								<td class="main"><strong>Reason:</strong> <?php echo $string; ?></td>
							</tr>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td class="main">&nbsp;&nbsp;Details:</td>
							</tr>
							<tr>
								<td height="4"><img src="images/pixel_trans.gif" height="4" width="1" /></td>
							</tr>
							<tr>
								<td>
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="10"><img src="images/pixel.gif" height="1" width="10" /></td>
											
											<td width="100%" class="main" align="left"><?php
												if ($sc_reason == '1') {
                                                    $string = 'Exchange Rider<br>';

                                                    $remove_equipment_name = equipment_array_to_string($remove_equipment);
                                                    $install_equipment_name = equipment_array_to_string($install_equipment);
													
													$string .= '&nbsp;&nbsp;Remove:&nbsp;&nbsp; '. $remove_equipment_name . '<br>';
													$string .= '&nbsp;&nbsp;Install:&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp; '. $install_equipment_name . '<br>';
												} elseif ($sc_reason == '2') {
													$string = 'Install New Rider or BBox';
														for ($n = 0, $m = count($equipment); $n < $m; $n++) {
															$query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment[$n] . "' limit 1");
															$result = $database->fetch_array($query);
															$string .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Install ' . $result['name'];
														}
												} elseif ($sc_reason == '3') {
													$string = 'Replace/Exchange Agent SignPanel';
														for ($n = 0, $m = count($equipment); $n < $m; $n++) {
															$query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment[$n] . "' limit 1");
															$result = $database->fetch_array($query);
															$string .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Install ' . $result['name'];
														}
												} elseif ($sc_reason == '4') {
														if ($sc_reason_4 == '1') {
															$string= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weather';
														} elseif ($sc_reason_4 == '2') {
															$string = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Improper Installation';
														} elseif ($sc_reason_4 == '3') {
															$string = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Someone moved Post';
														} elseif ($sc_reason_4 == '4') {
															$string = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other';
														}
												} elseif ($sc_reason == '5') {
													$string = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Details are marked in the comments section.';
												} elseif ($sc_reason == '6') {
													//$string = 'Install equipment forgotten at install';
													$string = tep_create_confirmation_equipment_string($optional);
												} elseif ($sc_reason == '7') {
													$string = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Details are marked in the comments section.';
												}
												echo $string;
												
											?></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
				</tr>
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td class="main" width="140">Special Instructions: </td><td class="main"><?php echo $special_instructions; ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
					} else {
				?>
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td class="main" width="140">Special Instructions: </td><td class="main"><?php echo $special_instructions; ?></td>
							</tr>
						</table>
					</td>
				</tr>
                <tr>
                    <td width="100%">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="main" width="140">Miss Utility: </td><td class="main"><?php echo $miss_utility_string; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
				<tr>
					<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
				</tr>
				<tr>
					<td width="100%">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="main">Equipment: </td>
							</tr>
							<tr>
								<td width="5"><img src="images/pixel.gif" height="5" width="1" /></td><td width="100%" class="main" align="left"><?php echo tep_create_confirmation_equipment_string($optional); ?></td>
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
				<tr>
					<td class="mainLarge" colspan="2">Order Totals</td>
				</tr>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0">
							<?php
									if ($base_cost > 0) {
							?>
							<tr>
								<td class="main" width="140">Base Cost: </td><td class="main">$<?php echo number_format($base_cost, 2); ?></td>
							</tr>
							<?php
									}
									if ($extended_cost > 0) {
							?>
							<tr>
								<td class="main" width="140">Extended Cost: </td><td class="main">$<?php echo number_format($extended_cost, 2); ?></td>
							</tr>
							<?php
									}
									if ($equipment_cost > 0) {
							?>
							<tr>
								<td class="main" width="140">Equipment Cost: </td><td class="main">$<?php echo number_format($equipment_cost, 2); ?></td>
							</tr>
							<?php
									}
									if ($extra_cost > 0) {
							?>
							<tr>
								<td class="main" width="140">Extra Cost: </td><td class="main">$<?php echo number_format($extra_cost, 2); ?> <em>(<?php echo tep_fetch_extra_cost_string($schedualed_start); ?>)</em></td>
							</tr>
							<?php
									}
									if ($deposit_cost > 0) {
							?>
							<tr>
								<td class="main" width="140">Deposit Cost: </td><td class="main">$<?php echo number_format($deposit_cost, 2); ?> <em>(This will be refunded when the signpost is successfully removed)</em></td>
							</tr>
							<?php
									}
									if ($discount_cost != 0) {
									
							?>
							<tr>
								<td class="main" width="140">Adjustment: </td><td class="main">$<?php echo number_format(($discount_cost), 2); ?></td>
							</tr>

							<?php
									}
									if ($credit != 0) {
							?>
							<tr>
								<td class="main" width="140">Credit: </td><td class="main">$-<?php echo number_format($credit, 2); ?></td>
							</tr>
							<?php
								}
								if ($total > 0) {
							?>
							<tr>
								<td class="main" width="140" height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
							</tr>
							<tr>
								<td class="main" width="140"><b>Total Cost: </b></td><td class="main"><b>$<?php echo number_format($total, 2); ?></b></td>
							</tr>
							<?php
								} else {
								?>
							<tr>
								<td class="main" width="300" height="1"><img src="images/pixel_trans.gif" height="1" width="300" /></td>
							</tr>
							<tr>
								<td class="main" width="300"><b>There is no charge for this order.</b></td>
                            </tr>
                            <?php
                                }
                            ?>
                            <tr>
                                <td class="main" width="140">Date/Time<br>Order Placed:</td><td class="main"><?php echo date("F j, Y, g:i a"); ?></td>
                            </tr>
						</table>
					</td>
                </tr>
				<?php
                if ($deferred_total > 0 && $payment_method == 1) {
                    echo DeferredBilling::applyTemplate($total, $deferred_total, $deferred_credit, $deferred_transactions, false, '', 'past');
                }
                ?>
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
	$session->php_session_unregister('order_id');
	$session->php_session_unregister('order_type_id');
	$session->php_session_unregister('address_id');
	$session->php_session_unregister('house_number');
	$session->php_session_unregister('street_name');
	$session->php_session_unregister('number_of_posts');
	$session->php_session_unregister('city');
	$session->php_session_unregister('zip');
	$session->php_session_unregister('zip4');
	$session->php_session_unregister('state');
	$session->php_session_unregister('county');
	$session->php_session_unregister('cross_street_directions');
	$session->php_session_unregister('schedualed_start');
	$session->php_session_unregister('request_zip4');
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
	$session->php_session_unregister('sc_reason');
	$session->php_session_unregister('sc_reason_4');
	$session->php_session_unregister('sc_reason_5');
	$session->php_session_unregister('sc_reason_7');
	$session->php_session_unregister('equipment');
	$session->php_session_unregister('optional');
	$session->php_session_unregister('install_equipment');
	$session->php_session_unregister('remove_equipment');
	$session->php_session_unregister('order_with_credit_total');
    $session->php_session_unregister('miss_utility_yes_no');
    $session->php_session_unregister('lamp_yes_no');
    $session->php_session_unregister('lamp_use_gas');
   // $session->php_session_unregister('date_added');
	$session->php_session_unregister('deferred_total');
	$session->php_session_unregister('deferred_transactions');
	$session->php_session_unregister('deferred_credit');
}





    if (isset($_SESSION['user_id'])) {
        $user_id=$_SESSION['user_id'];
        $user_name=$_SESSION['user_name'];
    } else {
      $error->add_error('account_create_success', 'User information not available so order success aborted.');
        tep_redirect(FILENAME_ORDER_CREATE);
    }
	$page_action = tep_fill_variable('page_action', 'get');
	$order_type = tep_fill_variable('order_type_id', 'session');
	$order_id = tep_fill_variable('order_id', 'session');
	$tos = tep_fill_variable('tos', 'post');
	$pna = tep_fill_variable('pna', 'post');

	$sc_reason = tep_fill_variable('sc_reason', 'session');
	$sc_reason_4  = tep_fill_variable('sc_reason_4', 'session');
	$sc_reason_5  = tep_fill_variable('sc_reason_5', 'session');
	$sc_reason_7  = tep_fill_variable('sc_reason_7', 'session');
	$equipment  = tep_fill_variable('equipment', 'session', array());
	$install_equipment  = tep_fill_variable('install_equipment', 'session', array());
	$remove_equipment  = tep_fill_variable('remove_equipment', 'session', array());
    
    $miss_utility_yes_no = tep_fill_variable('miss_utility_yes_no', 'session', '');
    $lamp_yes_no = tep_fill_variable('lamp_yes_no', 'session', '');
    $lamp_use_gas = tep_fill_variable('lamp_use_gas', 'session', '');

    $deferred_total = tep_fill_variable('deferred_total', 'session', 0);
    $deferred_transactions = tep_fill_variable('deferred_transactions', 'session', array());
    $deferred_credit = tep_fill_variable('deferred_credit', 'session', 0);

    $date_added = time();

    if ($miss_utility_yes_no == "yes") {
        $miss_utility_string = "Miss Utility call requested.";
    } else if ($lamp_yes_no == "no") {
        $miss_utility_string = "No lamp on property.";
    } else if ($lamp_use_gas == "yes") {
        $miss_utility_string = "Gas lamp on property.";
    } else if ($lamp_use_gas == "unsure") {
        $miss_utility_string = "Possible gas lamp on property.";
    } else if ($lamp_use_gas == "no") {
        $miss_utility_string = "No gas lamp on property.";
    }
	else {
	   $miss_utility_string = '';
	}

    // mjp
    $form['cc_type'] = tep_fill_variable('cc_type', 'session', '');
    $form['cc_name'] = tep_fill_variable('cc_name', 'session', '');
    $form['cc_number'] = tep_fill_variable('cc_number', 'session', '');
    $form['cc_month'] = tep_fill_variable('cc_month', 'session', '');
    $form['cc_year'] = tep_fill_variable('cc_year', 'session', '');
    $form['cc_verification_number'] = tep_fill_variable('cc_verification_number', 'session', '');
    $form['cc_billing_street'] = tep_fill_variable('cc_billing_street', 'session', '');
    $form['cc_billing_city'] = tep_fill_variable('cc_billing_city', 'session', '');
    $form['cc_billing_zip'] = tep_fill_variable('cc_billing_zip', 'session', '');
    
	if (empty($order_type))
		tep_redirect(FILENAME_ORDER_CREATE);

	if ($order_type == '1') {
		$shipping_address = tep_fill_variable('street_name', 'session');
	} else {
		$shipping_address = tep_fill_variable('address_id', 'session');

	}

	if (empty($shipping_address))
		tep_redirect(FILENAME_ORDER_CREATE_ADDRESS);

	$payment_method = tep_fill_variable('payment_method_id', 'session');
	if (empty($payment_method))
		tep_redirect(FILENAME_ORDER_CREATE_PAYMENT);

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

	$special_instructions = tep_fill_variable('special_instructions', 'session');
	$optional = tep_fill_variable('optional', 'session', array());
	if ($order_type == '1') {
		$optional = parse_equipment_array($optional);
		$session->php_session_register('optional', $optional);
	}
	$address_id = tep_fill_variable('address_id', 'session');

	if (!empty($address_id)) {
		//We have a saved address.  Now get the information and populate the session variables.
		$query = $database->query("select house_number, street_name, city, zip, state_id, county_id, zip4, adc_number, cross_street_directions from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
		$result = $database->fetch_array($query);

		$session->php_session_register('house_number', $result['house_number']);
		$session->php_session_register('street_name', $result['street_name']);
		$session->php_session_register('city', $result['city']);
		$session->php_session_register('zip', $result['zip']);
		$session->php_session_register('state', $result['state_id']);
		$session->php_session_register('county', $result['county_id']);
		$session->php_session_register('cross_street_directions', $result['cross_street_directions']);
		$session->php_session_register('zip4', $result['zip4']);
	}

	$address_id = tep_fill_variable('address_id', 'session');
	$house_number = trim(tep_fill_variable('house_number', 'session'));
	$street_name = tep_fill_variable('street_name', 'session');
	$adc_page = tep_fill_variable('adc_page', 'session');
	$adc_letter = tep_fill_variable('adc_letter', 'session');
	$adc_number = tep_fill_variable('adc_number', 'session');
	$city = tep_fill_variable('city', 'session');
	$zip = tep_fill_variable('zip', 'session');
	$zip4 = tep_fill_variable('zip4', 'session');
	$zip4_code = $zip4;
	$state = tep_fill_variable('state', 'session');
	$county = tep_fill_variable('county', 'session');
	$number_of_posts = tep_fill_variable('number_of_posts', 'session');
	$promo_code = tep_fill_variable('promo_code', 'session');
	$cross_street_directions = tep_fill_variable('cross_street_directions', 'session');

	//Work out if this is a rush or sat install and create the extra cost.
	$schedualed_start = tep_fill_variable('schedualed_start', 'session');
	$extra_cost = tep_fetch_extra_cost($schedualed_start);
    $extended_cost = tep_fetch_service_area_cost(tep_fetch_zip4_service_area($zip4)); //mjp
    $email_data = tep_fetch_email_data($user->fetch_user_id());
    $agent_data = tep_fetch_agent_data($user->fetch_user_id());
	$service_area_window = tep_fetch_service_area_window(tep_fetch_zip4_service_area($zip4_code));
	if ($service_area_window == 0) {
	    $service_area_window = 5;
	}
	if (tep_date_is_saturday($schedualed_start)) {
	    $service_area_window++;
	}
	$schedualed_end = add_business_days($schedualed_start, $service_area_window-1);
	$miss_utility_start = subtract_business_days($schedualed_start, MISS_UTILITY_DELAY);
	$miss_utility_end = subtract_business_days($schedualed_start, 1);
	if ($order_type == 1 && ($miss_utility_yes_no == 'yes' || ($lamp_yes_no == 'yes' && $lamp_use_gas != 'no'))) {
	    $show_miss_utility = true;
	} else {
	    $show_miss_utility = false;
	}
	$order = new orders('fetch', $order_id);
	$data = $order->fetch_order();
	$base_cost = $data['base_cost'];
	$extended_cost = $data['extended_cost'];
	$equipment_cost = $data['equipment_cost'];
	$extra_cost = $data['extra_cost'];
	$deposit_cost = $data['deposit_cost'];
	$discount_cost = $data['discount_cost'];
	$total = $data['order_total']; 
	$credit = tep_fill_variable('credit', 'session', 0);
    $total -= $credit;		
$error->cc_error("Variables $user_name($user_id) $house_number $street_name for base $base_cost, ext $extended_cost, eqp $equipment_cost, ext $extra_cost, dep $deposit_cost, disc $discount_cost, cred $credit");

$session->php_session_register('order_id',$order_id);
$session->php_session_register('order_type_id',$order_type);
$session->php_session_register('address_id',$address_id);
$session->php_session_register('house_number',$house_number);
$session->php_session_register('street_name',$street_name);
$session->php_session_register('number_of_posts',$number_of_posts);
$session->php_session_register('city',$city);
$session->php_session_register('zip',$zip);
$session->php_session_register('zip4',$zip4);
$session->php_session_register('state',$state);
$session->php_session_register('county',$county);
$session->php_session_register('cross_street_directions',$cross_street_directions);
$session->php_session_register('schedualed_start',$schedualed_start);
$session->php_session_register('miss_utility_yes_no',$miss_utility_yes_no);
$session->php_session_register('lamp_yes_no',$lamp_yes_no);
$session->php_session_register('lamp_use_gas',$lamp_use_gas);
$session->php_session_register('date_added',$date_added);
$session->php_session_register('credit',$credit);
$session->php_session_register('request_zip4',tep_fill_variable('request_zip4', 'session'));
$session->php_session_register('payment_method_id',tep_fill_variable('payment_method_id', 'session'));
$session->php_session_register('cc_type',tep_fill_variable('cc_type', 'session'));
$session->php_session_register('cc_name',tep_fill_variable('cc_name', 'session'));
$session->php_session_register('cc_number',tep_fill_variable('cc_number', 'session'));
$session->php_session_register('cc_month',tep_fill_variable('cc_month', 'session'));
$session->php_session_register('cc_year',tep_fill_variable('cc_year', 'session'));
$session->php_session_register('cc_verification_number',tep_fill_variable('cc_verification_number', 'session'));
$session->php_session_register('cc_billing_street',tep_fill_variable('cc_billing_street', 'session'));
$session->php_session_register('cc_billing_city',tep_fill_variable('cc_billing_city', 'session'));
$session->php_session_register('cc_billing_zip',tep_fill_variable('cc_billing_zip', 'session'));
$session->php_session_register('sc_reason',tep_fill_variable('sc_reason', 'session'));
$session->php_session_register('sc_reason_4',tep_fill_variable('sc_reason_4', 'session'));
$session->php_session_register('sc_reason_5',tep_fill_variable('sc_reason_5', 'session'));
$session->php_session_register('sc_reason_7',tep_fill_variable('sc_reason_7', 'session'));
$session->php_session_register('equipment',tep_fill_variable('equipment', 'session'));
$session->php_session_register('optional',tep_fill_variable('optional', 'session'));
$session->php_session_register('install_equipment',tep_fill_variable('install_equipment', 'session'));
$session->php_session_register('remove_equipment',tep_fill_variable('remove_equipment', 'session'));
$session->php_session_register('special_instructions',tep_fill_variable('special_instructions', 'session'));
$session->php_session_register('order_with_credit_total', tep_fill_variable('order_with_credit_total', 'session'));
$session->php_session_register('deferred_total',$deferred_total);
$session->php_session_register('deferred_transactions',$deferred_transactions);
$session->php_session_register('deferred_credit',$deferred_credit);

/*
$session->php_session_unregister('miss_utility_yes_no');
$session->php_session_unregister('lamp_yes_no');
$session->php_session_unregister('lamp_use_gas');
unset($_SESSION['lamp_use_gas']);
unset($_SESSION['lamp_yes_no']);
$session->php_session_unregister('order_id');
$session->php_session_unregister('order_type_id');
$session->php_session_unregister('address_id');
$session->php_session_unregister('house_number');
$session->php_session_unregister('street_name');
$session->php_session_unregister('number_of_posts');
$session->php_session_unregister('city');
$session->php_session_unregister('zip');
$session->php_session_unregister('zip4');
$session->php_session_unregister('state');
$session->php_session_unregister('county');
$session->php_session_unregister('cross_street_directions');
$session->php_session_unregister('schedualed_start');
$session->php_session_unregister('request_zip4');
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
$session->php_session_unregister('sc_reason');
$session->php_session_unregister('sc_reason_4');
$session->php_session_unregister('sc_reason_5');
$session->php_session_unregister('sc_reason_7');
$session->php_session_unregister('equipment');
$session->php_session_unregister('optional');
$session->php_session_unregister('optional_with_nones');
$session->php_session_unregister('install_equipment');
$session->php_session_unregister('remove_equipment');
$session->php_session_unregister('special_instructions');
$session->php_session_unregister('adc_page');
$session->php_session_unregister('adc_letter');
$session->php_session_unregister('adc_number');
$session->php_session_unregister('order_with_credit_total');

$session->php_session_unregister('deferred_total');
$session->php_session_unregister('deferred_transactions');
$session->php_session_unregister('deferred_credit');
*/

//print_r($_SESSION);

$form = array (	
	'show_miss_utility'=>$show_miss_utility,
	//'order_type'=>$order_type,
	'order_type_name'=>tep_get_order_type_name($order_type),
	'miss_utility_start'=>$miss_utility_start,
	'miss_utility_end'=> $miss_utility_end,
	'schedualed_start'=>$schedualed_start,	
	'schedualed_end'=>$schedualed_end,	
	'county_name'=>tep_get_county_name($county),
	'state_name'=>tep_get_state_name($state),	
	'zip4_code'=>$zip4_code,
	'city'=>$city,	
	'number_of_posts'=>$number_of_posts,
	'promo_code'=>$promo_code,
	'cross_street_directions'=>$cross_street_directions,
	'street_name'=>$street_name,
	'house_number'=>$house_number,	
	'special_instructions'=>$special_instructions,
	'miss_utility_string'=>$miss_utility_string,
	'service_area_window'=>$service_area_window,
	'equipment_array'=>tep_create_confirmation_equipment_string_bgdn($optional),
	'order_total'=>$total,	
	'extra_cost_string'=>tep_fetch_extra_cost_string($schedualed_start)
	);
	
	
	
	if ($order_type == ORDER_TYPE_SERVICE) {

  $string = '';
/*  
  if ($sc_reason == '1') {
    $string = 'Exchange Rider';
  } elseif ($sc_reason == '2') {
    $string = 'Install New Rider or BBox';
  } elseif ($sc_reason == '3') {
    $string = 'Replace/Exchange Agent SignPanel';
  } elseif ($sc_reason == '4') {
    $string = 'Post Leaning/Straighten Post';
  } elseif ($sc_reason == '5') {
    $string = 'Move Post';
  } elseif ($sc_reason == '6') {
    $string = 'Install equipment forgotten at install';
  } elseif ($sc_reason == '7') {
    $string = 'Other';
  }
*/

	
	
  if ($sc_reason == '1') {
    $string = 'Exchange Rider';

    $install_equipment_name = equipment_array_to_string($install_equipment);
    $remove_equipment_name = equipment_array_to_string($remove_equipment);

//	$form['sc']['name'] = $string;
	$form['sc']['install_equipment_name'] = $install_equipment_name;
	$form['sc']['remove_equipment_name'] = $remove_equipment_name;
	
   // $string .= '&nbsp;&nbsp;Remove:&nbsp;&nbsp; '. $remove_equipment_name . '<br>';
   // $string .= '&nbsp;&nbsp;Install:&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp; '. $install_equipment_name . '<br>';
  } elseif ($sc_reason == '2') {
	  
    $string = 'Install New Rider or BBox';
	$eq_string = tep_create_confirmation_equipment_string_bgdn($optional);
	$form['sc']['equipment_array'] = $eq_string;
  //  $string .= '<br>' . tep_create_confirmation_equipment_string($optional);
  
  } elseif ($sc_reason == '3') {
    //$string = 'Replace/Exchange Agent SignPanel';
    for ($n = 0, $m = count($equipment); $n < $m; $n++) {
      $query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment[$n] . "' limit 1");
      $result = $database->fetch_array($query);
	  $form['sc']['agent_equipment_name'] = $result['name'];
      //$string .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Install ' . $result['name'];
    }
  } elseif ($sc_reason == '4') {
    if ($sc_reason_4 == '1') {
      $string = 'Weather';
    } elseif ($sc_reason_4 == '2') {
      $string = 'Improper Installation';
    } elseif ($sc_reason_4 == '3') {
      $string = 'Someone moved Post';
    } elseif ($sc_reason_4 == '4') {
      $string = 'Other';
    }
  } elseif ($sc_reason == '5') {
	  $string = 'Details are marked in the comments section.';
  } elseif ($sc_reason == '6') {
    $string = 'Install equipment forgotten at install';
	$eq_string = tep_create_confirmation_equipment_string_bgdn($optional);
	$form['sc']['equipment_array'] = $eq_string;
   // $string = tep_create_confirmation_equipment_string($optional);
  } elseif ($sc_reason == '7') {
    $string = 'Details are marked in the comments section.';
  }
  //echo $string;
  
  $form['sc']['name'] = $string;

} 

if ($deferred_total > 0 && $payment_method == 1) {
                    $deferred = DeferredBilling::applyTemplateTwigHorizontal($total, $deferred_total, $deferred_credit, $deferred_transactions, false, '', 'past');
                } else {
					$deferred = null;
				}

	$vars['order_type'] = $order_type;
	$vars['deferred'] = $deferred;
	$vars['form'] = $form;	
	$vars['email_data'] = tep_fetch_email_data($user->fetch_user_id());
	$vars['agent_data'] = tep_fetch_agent_data($user->fetch_user_id());
	$vars['step'] = 5;	 
	echo $twig->render('order/order_create_success.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'order'=>$data, 'vars'=>$vars));
?>
