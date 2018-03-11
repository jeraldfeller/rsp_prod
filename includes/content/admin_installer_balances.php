<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$uID = tep_fill_variable('uID', 'get', tep_fill_variable('uID', 'post'));
	
	$date = tep_fill_variable('date', 'post', date("n", mktime()).'-'.date("Y", mktime()));
	$search_status = tep_fill_variable('search_status', 'get', '1');
	$explode = explode('-', $date);
	
	$month = $explode[0];
	$year = $explode[1];
	
	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	$page = tep_fill_variable('page', 'get', 1);
	
		if ($page_action == 'make_payment_confirm') {
			$payment_amount = tep_fill_variable('payment_amount', 'post');
			$payment_reason = tep_fill_variable('payment_reason', 'post');
			
			$payment = new installer_payments($uID);
			$payment->insert_installer_payout($payment_amount, $payment_reason);
			
			$message = 'Payment Successfully Added.';
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
		<?php
				if ($page_action != 'view_balance') {
					$where = '';
					$listing_split = new split_page("select u.user_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '3' and utug.user_id = u.user_id " . (($search_status != '2') ? " and u.active_status = '" . $search_status . "'" : '') . " and u.user_id = ud.user_id order by ud.firstname, ud.lastname", '20', 'u.user_id');
						if ($listing_split->number_of_rows > 0) {
			?>			
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td class="pageBoxHeading">Installer Name</td>
							<td class="pageBoxHeading">Total Owed</td>
							<td class="pageBoxHeading" align="right">Action</td>
							<td width="10" class="pageBoxHeading"></td>
						</tr>
					<?php
						$uData = array();
						$query = $database->query($listing_split->sql_query);
							while($result = $database->fetch_array($query)) {
								if ($uID == $result['user_id']) {
									$uData = $result;
								}
							$payment = new installer_payments($result['user_id']);
							
					?>
						<tr>
							<td class="pageBoxContent"><?php echo $result['firstname']; ?> <?php echo $result['lastname']; ?></td>
							<td class="pageBoxContent">$<?php echo number_format($payment->fetch_end_month_balance(date("Y", mktime()), date("n", mktime())), 2); ?></td>
							<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_INSTALLER_BALANCES . '?uID='.$result['user_id']; ?>">View</a></td>
							<td width="10" class="pageBoxContent"></td>
						</tr>
				<?php
							}
							?>
						</table>
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td colspan="4">
									<table class="normaltable" cellspacing="0" cellpadding="2">
										<tr>
											<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> installers)'); ?></td>
											<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
										</tr>
									</table>
								</td>
							</tr>
							</table>
							<?php
						}
						
					} else {
					
						$query = $database->query("select firstname, lastname from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $uID . "' limit 1");
						$result = $database->fetch_array($query);
						
					?>
					<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="main"><b>Report for <?php echo $result['firstname'] . ' ' . $result['lastname']; ?></b></td>
							</tr>
							<tr>
								<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
							</tr>
							<?php
								$payment = new installer_payments($uID);
								
								$report = $payment->fetch_balance_list($year, $month);
									if (!empty($report)) {
									?>
									<tr>
										<td width="100%" align="left">
											<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
												<tr>
													<td class="pageBoxHeading">Date</td>
													<td class="pageBoxHeading">Type</td>
													<td class="pageBoxHeading">Description</td>
													<td class="pageBoxHeading">Total</td>
													<td class="pageBoxHeading" align="right">Running Total</td>
													<td width="10" class="pageBoxHeading"></td>
												</tr>
												<?php
													for($n = 0, $m = count($report); $n < $m; $n++) {
													?>
													<tr>
														<td class="pageBoxContent" valign="top"><?php echo date("n/d/Y", $report[$n]['date_added']); ?></td>
														<td class="pageBoxContent" valign="top"><?php echo $report[$n]['type']; ?></td>
														<td class="pageBoxContent" valign="top"><?php echo $report[$n]['description']; ?></td>
														<td class="pageBoxContent" valign="top">$<?php echo $report[$n]['total']; ?></td>
														<td class="pageBoxContent" align="right" valign="top">$<?php echo $report[$n]['running_total']; ?></td>
														<td width="10" class="pageBoxContent"></td>
													</tr>
													<?php
													}
												?>
											</table>
										</td>
									</tr>
								<?php
									} else {
									?>
									<tr>
										<td width="100%" align="left" class="main">There are no available records for this period.</td>
									</tr>
								
								<?php
								}
								?>
								</table>
					<?php
					
					}
			?>
				
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<table width="100%" cellspacing="0" cellpadding="0">
				<?php
					if(!empty($message)) {
				?>
				<tr>
					<td class="mainSuccess"><?php echo $message; ?></td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td width="100%">
					<?php
						if ($page_action == 'view_balance') {
							$dates = $payment->fetch_year_month_list();
							
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading" colspan="2">Viewing <?php echo $result['firstname'] . ' ' . $result['lastname']; ?> for <?php echo $month.'/'.$year; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_INSTALLER_BALANCES; ?>?page_action=view_balance&uID=<?php echo $uID; ?>&page=<?php echo $page; ?>" method="post">
							<tr>
								<td class="main">Month Start Total: <strong>$<?php echo $payment->fetch_end_month_balance($year, $month - 1); ?></strong></td>
							</tr>
							<tr>
								<td class="main">Month End Total: <strong>$<?php echo $payment->fetch_end_month_balance($year, $month); ?></strong></td>
							</tr>
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left" class="main">View Month: </td>
											<td><?php echo tep_generate_pulldown_menu('date', $dates, $date, ' onchange="this.form.submit();"'); ?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							</form>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="right"><a href="<?php echo FILENAME_ADMIN_INSTALLER_BALANCES; ?>?uID=<?php echo $uID; ?>&page=<?php echo $page; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
						<?php
					} elseif ($page_action == 'make_payment') {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<form action="<?php echo FILENAME_ADMIN_INSTALLER_BALANCES; ?>?page_action=make_payment_confirm&uID=<?php echo $uID; ?>&page=<?php echo $page; ?>" method="post">
						<tr>
							<td class="pageBoxHeading"><b>Making payment to <?php echo $uData['firstname'] . ' ' . $uData['lastname']; ?></b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="main">Make a payment of the following amount to this installer.  The amount showen by default is the total owed currently.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<?php
							$payment = new installer_payments($uID);
							
							
						?>
						<tr>
							<td class="main">Amount: <input name="payment_amount" value="<?php echo $payment->fetch_end_month_balance(date("Y", mktime()), date("n", mktime())); ?>" /></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="main">Reason:</td>
						</tr>
						<tr>
							<td class="main"><textarea name="payment_reason">Payment</textarea></td>
						</tr>
						<tr>
							<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent" width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="left"><?php echo tep_create_button_submit('payment', 'Make Payment'); ?></form></td>
										<td align="right"><a href="<?php echo FILENAME_ADMIN_INSTALLER_BALANCES; ?>?uID=<?php echo $uID; ?>&page=<?php echo $page; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						
					</table>
					<?php
					} elseif (!empty($uID)) {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Options for <?php echo $uData['firstname'] . ' ' . $uData['lastname']; ?></b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent" width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="left"><a href="<?php echo FILENAME_ADMIN_INSTALLER_BALANCES; ?>?page_action=make_payment&uID=<?php echo $uID; ?>&page=<?php echo $page; ?>"><?php echo tep_create_button_link('payment', 'Make Payment'); ?></a></td>
										<td align="right"><a href="<?php echo FILENAME_ADMIN_INSTALLER_BALANCES; ?>?page_action=view_balance&uID=<?php echo $uID; ?>&page=<?php echo $page; ?>"><?php echo tep_create_button_link('balance', 'View Balance'); ?></a></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						
					</table>
					<?php
					} else {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Installer Balances</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click View to view the relevent Installer Balance or add a payment.</td>
						</tr>
						<tr>
							<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
						</tr>
						<tr>
							<form action="<?php echo FILENAME_ADMIN_INSTALLER_BALANCES; ?>" method="get">
                                <td class="main">Show only with Status: <select name="search_status" onchange="this.form.submit();"><option value="3"<?php echo (($search_status == '') ? ' SELECTED' : ''); ?>>Any</option><option value="1"<?php echo (($search_status == '1') ? ' SELECTED' : ''); ?>>Active</option><option value="0"<?php echo (($search_status == '0') ? ' SELECTED' : ''); ?>>Inactive</option></select></td>
							</form>
						</tr>
					</table>
				<?php
					}
				?>
		</td>
	</tr>
</table>
		</td>
	</tr>
</table>