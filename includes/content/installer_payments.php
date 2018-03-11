<?php
	$date = tep_fill_variable('date', 'post', date("n", mktime()).'-'.date("Y", mktime()));
	
	$explode = explode('-', $date);
	
	$month = $explode[0];
	$year = $explode[1];
	

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
					<table width="100%" cellspacing="0" cellpadding="0">
							<?php
								$payment = new installer_payments($user->fetch_user_id());
								
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
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<table width="100%" cellspacing="0" cellpadding="0">

				<tr>
					<td width="100%">
					<?php

							$dates = $payment->fetch_year_month_list();
							
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading" colspan="2">Viewing <?php echo $month.'/'.$year; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_INSTALLER_PAYMENTS; ?>" method="post">
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
							
						</table>

		</td>
	</tr>
</table>
		</td>
	</tr>
</table>