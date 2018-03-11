<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$run_file = '';
	
	
		if ($page_action == 'export') {
			$export_format = stripslashes(tep_fill_variable('export_format', 'post'));
			$order_type = tep_fill_variable('order_type', 'post');
			$inserted_order_type_id = tep_fill_variable('inserted_order_type_id', 'post');
			$order_status = tep_fill_variable('order_status', 'post');
			$payment_method_id = tep_fill_variable('payment_method_id', 'post');
			$show_between_type = tep_fill_variable('show_between_type', 'post');
			$show_between_start = tep_fill_variable('show_between_start', 'post');
			$show_between_end = tep_fill_variable('show_between_end', 'post');
			$agency_id = tep_fill_variable('agency_id', 'post');
			$agent_id = tep_fill_variable('agent_id', 'post');
			$order_min = ((isset($_POST['order_min'])) ? $_POST['order_min'] : '');
			$filename = tep_fill_variable('filename', 'post', 'orders_' . date("n_d_Y") . '.csv');
				if (substr($filename, -4) != '.csv') {
					$filename .= '.csv';
				}
				
			$file = '';
			
			$where = '';
				
				if (!empty($agent_id)) {
					$where .= ' and ';
					$where .= " o.user_id = '" . $agent_id . "'";
				}
				if (!empty($agency_id)) {
					$where .= ' and ';
					$where .= " u.agency_id = '" . $agency_id . "'";
				}
			
				if (!empty($inserted_order_type_id)) {
					$where .= ' and ';
					$where .= " o.inserted_order_type_id = '" . $inserted_order_type_id . "'";
				}
				if (!empty($show_between_type)) {
					if (!empty($show_between_start)) {
						$start_timestamp = @strtotime($show_between_start);
					} else {
						$start_timestamp = 0;
					}
					if (!empty($show_between_end)) {
						$end_timestamp = @strtotime($show_between_end);
							if ($end_timestamp > 0) {
								$end_timestamp += ((60*60*24) - 1); //End as opposed to start of day.
							}
					} else {
						$end_timestamp = 0;
					}
					if ($show_between_type == 'accepted') {
						$where .= " and o.date_accepted > 0 ";
							if ($start_timestamp > 0) {
								$where .= " and o.date_accepted >= '" . $start_timestamp . "' ";
							}
							if ($end_timestamp > 0) {
										$where .= " and o.date_accepted <= '" . $end_timestamp . "' ";
							}
					}
					if ($show_between_type == 'ordered') {
						$where .= " and o.date_added > 0 ";
							if ($start_timestamp > 0) {
								$where .= " and o.date_added >= '" . $start_timestamp . "' ";
							}
							if ($end_timestamp > 0) {
								$where .= " and o.date_added <= '" . $end_timestamp . "' ";
							}
					}
					if ($show_between_type == 'scheduled') {
						$where .= " and o.date_schedualed > 0 ";
							if ($start_timestamp > 0) {
								$where .= " and o.date_schedualed >= '" . $start_timestamp . "' ";
							}
							if ($end_timestamp > 0) {
								$where .= " and o.date_schedualed <= '" . $end_timestamp . "' ";
							}
					}
					if ($show_between_type == 'completed') {
						$where .= " and o.date_completed > 0 ";
						$order_status = '3';
							if ($start_timestamp > 0) {
								$where .= " and o.date_completed >= '" . $start_timestamp . "' ";
							}
							if ($end_timestamp > 0) {
								$where .= " and o.date_completed <= '" . $end_timestamp . "' ";
							}
					}
				}
			$find_array = array('AgentID',
											'Order#',
											'AgentFirstName',
											'AgentLastName',
											'AgentServiceLevel',
											'DateOrdered',
											'DateCompleted',
											'OrderType',
											'HouseNumber',
											'StreetName',
											'City',
											'County',
											'State',
											'AgencyName',
											'StatusName',
											'PaymentMethod',
										   'OrderTotal',
										   'DateScheduled');
			$query = $database->query("select o.order_id, o.date_schedualed, o.date_added, o.date_completed, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city, c.name as county_name, s.name as state_name, o.order_issue, u.email_address, u.agent_id, ud.firstname, ud.lastname, ag.name as agency_name, bm.name as billing_method_name, sl.name as service_level_name from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_SERVICE_LEVELS . " sl, " . TABLE_ADDRESSES . " a left join (" . TABLE_STATES . " s, " . TABLE_COUNTYS . " c) on (a.state_id = s.state_id and a.county_id = c.county_id), " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_AGENCYS . " ag, " . TABLE_BILLING_METHODS . " bm where  o.order_type_id = ot.order_type_id and o.user_id = u.user_id and u.user_id = ud.user_id and o.billing_method_id = bm.billing_method_id and u.service_level_id = sl.service_level_id and u.agency_id = ag.agency_id and o.order_status_id = os.order_status_id" . ((!empty($order_status)) ? " and o.order_status_id = '" . $order_status . "'" : '') . ((!empty($order_type)) ? " and o.order_type_id = '" . $order_type . "'" : '') . (($order_min !== '') ? " and o.order_total > '" . $order_min . "' " : '') . " and o.order_status_id > 0 and o.address_id = a.address_id " . $where . " order by o.date_schedualed DESC");
				while($result = $database->fetch_array($query)) {
						if (!empty($file)) {
							$file .= "\n";
						}
					
					$replace_array = array($result['agent_id'],
														$result['order_id'],
														$result['firstname'],
														$result['lastname'],
														$result['service_level_name'],
														date("n/d/Y", $result['date_added']),
														date("n/d/Y", $result['date_completed']),
														$result['order_type_name'],
														$result['house_number'],
														$result['street_name'],
														$result['city'],
														$result['county_name'],
														$result['state_name'],
														$result['agency_name'],
														$result['order_status_name'],
														$result['billing_method_name'],
														 $result['order_total'],
														 date("n/d/Y", $result['date_schedualed']));
					
					$file .= str_replace($find_array, $replace_array, $export_format);
				}
			
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Content-Length: '.strlen($file));
			echo $file;
			die();
		}

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('order_export', 'all')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('order_export', 'all'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
				<?php
					if ($page_action == 'export') {
						
					} else {
						?>
						<form action="order_export.php?page_action=export" method="post">
						<tr>
							<td class="main"><b>Select the items from the list below to fine tune your export and press the export button.  To specify the order of the columns and what to show in your export, look at the instructions above the export text field at the bottom of the page.</b></td>
						</tr>
						<tr>
							<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
						</tr>
						<tr>
							<td width="500">
								<table width="500" cellspacing="2" cellpadding="2">
									<tr>
										<td width="100"><img src="images/pixel_trans.gif" height="1" width="100"></td>
										<td width="100%"></td>
									</tr>
									<tr>
										<td class="main">Orders of Type:</td>
										<td><?php echo tep_draw_order_type_pulldown('order_type', '', '', array(array('id' => '', 'name' => 'Any'))); ?></td>
									</tr>
									<tr>
										<td class="main">Orders of Status:</td>
										<td><?php echo tep_draw_orders_status_pulldown('order_status', '3', array(array('id' => '', 'name' => 'Any'))); ?></td>
									</tr>
									<tr>
										<td class="main">Payment Method:</td>
										<td><?php echo tep_draw_billing_method_pulldown('payment_method_id', '', '', false); ?></td>
									</tr>
									<tr>
										<td class="main">Placement Type:</td>
										<td><?php echo tep_generate_order_placement_type_pulldown_menu('inserted_order_type_id', ''); ?></td>
									</tr>
									<tr>
										<td class="main">Order amount greater than:</td>
										<td class="main"><input type="text" name="order_min" value="0" /> (leave blank for none)</td>
									</tr>
									<tr>
										<td class="main">Export Filename:</td>
										<td class="main"><input type="text" name="filename" value="<?php echo 'orders_' . date("n_d_Y") . '.csv'; ?>" /></td>
									</tr>
									<tr>
										<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
									</tr>
									<tr>
										<td class="main">When order is/was:</td>
										<td><select name="show_between_type"><option value="ordered">Ordered</option><option value="scheduled">Scheduled</option><option value="accepted">Accepted</option><option value="completed" SELECTED>Completed</option></select></td>
									</tr>
									<tr>
										<td class="main">From :</td>
										<td class="main"><input type="text" name="show_between_start" value="" size="7" /><em> (mm/dd/yyyy)</em></td>
									</tr>
									<tr>
										<td class="main">To :</td>
										<td class="main"><input type="text" name="show_between_end" value="" size="7" /><em> (mm/dd/yyyy)</em></td>
									</tr>
									<tr>
										<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
									</tr>
									<tr>
										<td class="main">Only Agency:</td>
										<td><?php echo tep_draw_agency_pulldown('agency_id', '', '', array(array('id' => '', 'name' => 'Any'))); ?></td>
									</tr>
									<tr>
										<td class="main">Only Agent:</td>
										<td><?php echo tep_draw_agent_pulldown('agent_id', '', '', array(array('id' => '', 'name' => 'Any'))); ?></td>
									</tr>
									<tr>
										<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
									</tr>
									<tr>
										<td class="main" colspan="2"><strong>Order Export Format</strong><br />
										Please enter the required export format in the box below.  This can be made up of any combination of the following:<br>
										Order#<br />
										AgentID<br />
										AgentFirstName<br />
										AgentLastName<br />
										AgentServiceLevel<br />
										DateOrdered<br />
										DateCompleted<br />
										OrderType<br />
										HouseNumber<br />
										StreetName<br />
										City<br />
										County<br />
										State<br />
										AgencyName<br />
										StatusName<br />
										PaymentMethod<br />
										OrderTotal<br />
										DateScheduled<br />
										<i>Example: AgentID,"AgentFirstName, AgentLastName",AgentServiceLevel, OrderTotal</i>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<textarea name="export_format" cols="64">Order#,"AgentFirstName, AgentLastName",OrderTotal,AgentServiceLevel</textarea>
										</td>
									</tr>
									<tr>
										<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
									</tr>
									<tr>
										<td colspan="2" align="right"><input type="submit" value=" Export " /></td>
									</tr>
								</table>
							</td>
						</tr>
						</form>
						<?php
					}
				?>
			</table>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
	</tr>
</table>