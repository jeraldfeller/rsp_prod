<?php
	$year = tep_fill_variable('year', 'get');
	$month = tep_fill_variable('month', 'get');
	$order_type = tep_fill_variable('order_type', 'get', '0');
	$page_action = tep_fill_variable('page_action', 'get', '');
		if ($page_action == 'print') {
			//Hide the template and just run the special data.
			$this->change_template_file('print.tpl');
		}


?>
<table width="100%" cellspacing="0" cellpadding="0">

	<tr>
		<td width="100%" valign="top">

					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td class="pageBoxHeading">Date Scheduled</td>
							<td class="pageBoxHeading">Date Completed</td>
							<td class="pageBoxHeading" align="center">Service</td>
							<td class="pageBoxHeading" align="center">Address</td>
							<td class="pageBoxHeading" align="center">Extras</td>
							<td class="pageBoxHeading" align="center">Total</td>
							<td class="pageBoxHeading" align="center">Status</td>
							<td width="10" class="pageBoxHeading"></td>
						</tr>
					<?php
						$start_year_stamp = mktime(0, 0, 0, 1, 1, date("Y", mktime()));
						$end_year_stamp = mktime(0, 0, -1, 1, 1, (date("Y", mktime())+1));
					
						$query = $database->query("select o.order_id, a.house_number, a.street_name, a.city, c.name as county_name, s.name as state_name, ot.name as order_type_name, os.order_status_name, o.date_completed, o.date_schedualed, o.base_cost, o.extended_cost, o.equipment_cost, o.discount_cost, o.deposit_cost, o.extra_cost, o.extra_cost_description, o.order_issue, o.order_total from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where o.user_id = '" . $user->fetch_user_id() . "' and o.date_added >= '" . $start_year_stamp . "' and o.date_added <= '" . $end_year_stamp . "' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and a.county_id = c.county_id and a.state_id = s.state_id order by o.date_added DESC");
						$loop = 0;
							while($result = $database->fetch_array($query)) {
								$order = new orders('fetch', $result['order_id']);
					
								$order_data = $order->return_result();
									
					?>
						<?php
							if ($loop > 0) {
						?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td colspan="8" width="100%"><hr></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
						<?php
							}
						?>
						<tr>
							<td class="pageBoxContent" valign="top">&nbsp;&nbsp;&nbsp;<?php echo date("n/d/Y", $result['date_schedualed']); ?></td>
							<td class="pageBoxContent" align="center" valign="top"><?php echo ((!empty($result['date_completed'])) ? date("n/d/Y", $result['date_completed']) : ''); ?></td>
							<td class="pageBoxContent" align="center" valign="top"><?php echo $result['order_type_name']; ?></td>
							<td class="pageBoxContent" align="center" valign="top"><?php echo $result['house_number'].' '.$result['street_name'].'<br>'.$result['city'].'<br>'.$result['state_name']; ?></td>
							<td class="pageBoxContent" align="center" valign="top"><?php echo tep_create_view_equipment_string($order_data['optional']); ?></td>
							<td class="pageBoxContent" align="center" valign="top">$<?php echo number_format($result['order_total']); ?></td>
							<td class="pageBoxContent" align="center" valign="top"><?php echo $result['order_status_name']; ?></td>
							<td width="10" class="pageBoxContent" valign="top"></td>
						</tr>
						
					<?php
									$loop++;
								}
					?>
				</table>


		</td>
		<?php
			if ($page_action != 'print') {
		?>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">

			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent"></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><a href="<?php echo FILENAME_AGENT_YEAR_TO_DATE . '?page_action=print'; ?>">View Printable</a></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

		</td>
		<?php
			}
		?>
	</tr>
</table>