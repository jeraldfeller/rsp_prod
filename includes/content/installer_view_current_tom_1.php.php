<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$view_type = tep_fill_variable('view_type', 'get');
	$day_view = tep_fill_variable('day_view', 'get', 'today');
	$display_view = tep_fill_variable('display_view', 'get', 'overview');
	$submit_value = tep_fill_variable('submit_value');
	
		if($page_action == 'update_order') {
			//Loop over orders and update the show_order.
			$order_id = tep_fill_variable('order_id', 'post', array());
			$count = count($order_id);
			$n = 0;
				if ($submit_value == "Update Order") {
					while($n < $count) {
						$show_order = tep_fill_variable('order_'.$order_id[$n], 'post', '1');
							$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " where order_id = '" . $order_id[$n] . "' limit 1");
							$result = $database->fetch_array($query);
								if ($result['count'] > 0) {
									$database->query("update " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " set show_order_id = '" . $show_order . "' where order_id = '" . $order_id[$n] . "' limit 1");
								} else {
									$database->query("insert into " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " (order_id, show_order_id) values ('" . $order_id[$n] . "', '" . $show_order . "')");
								}
						$n++;
					}
				} elseif ($submit_value == 'Confirm Accept') {
					while($n < $count) {
						$database->query("update " . TABLE_ORDERS . " set order_status_id = '2' where order_id = '" . $order_id[$n] . "' and order_status_id = '1' limit 1");
						$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $order_id[$n] . "', '2', '" . mktime() . "', '0', 'Your order has been scheduled.  You can no longer edit this order.')");
						$check_query = $database->query("select count(installer_id) as count from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $order_id[$n] . "' limit 1");
						$check_result = $database->fetch_array($check_query);
							if ($check_result['count'] > 0) {
								$database->query("update " . TABLE_INSTALLERS_TO_ORDERS . " set installer_id = '" . $user->fetch_user_id() . "' where order_id = '" . $order_id[$n] . "' limit 1");
							} else {
								$database->query("insert into " . TABLE_INSTALLERS_TO_ORDERS . " (installer_id, order_id) values ('" . $user->fetch_user_id() . "', '" . $order_id[$n] . "')");
							}
						$n++;
					}
				}
		}

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('installer_view_current')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('installer_view_current'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
		<?php
				$where = '';
				//Here we work out if it is today or tomorrow and change the where to match.
					if ($day_view == 'tomorrow') {
						$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
						$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
					} else {
						$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp())); 
						$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
					}
				//We only want the orders for the specifed day.
		?>			
						<?php
							if ($display_view == 'detailed') {
						?>
							<form action="<?php echo FILENAME_INSTALLER_VIEW_CURRENT; ?>?page_action=update_order&day_view=<?php echo $day_view; ?>&display_view=<?php echo $display_view; ?>" method="post">
						<?php
							}
						?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					
					<tr>
						<td class="pageBoxHeading">Order Id</td>
						<td class="pageBoxHeading">Date</td>
						<td class="pageBoxHeading">Job Status</td>
						<td class="pageBoxHeading">Type</td>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxHeading">Address</td>
						<td class="pageBoxHeading">Service Level</td>
						<?php
							}
						?>
						<td class="pageBoxHeading" align="right">Zip4</td>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxHeading" align="right">Order</td>
						<?php
							}
						?>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$extra = '';
						if ($display_view == 'detailed') {
							//Fetch extra information,
							$extra = ', otiso.show_order_id, a.house_number, a.street_name,  a.cross_street_directions, a.number_of_posts, a.address_post_allowed, a.city, a.zip, s.name as state_name, c.name as county_name,sld.name as service_level_name, od.special_instructions, od.admin_comments';
						}
					$query = $database->query("select o.order_id, o.date_schedualed, os.order_status_name, ot.name as order_type_name, a.zip4".$extra." from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed)), " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id), " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c, " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed >= '" . $midnight_tonight . "' and o.date_schedualed < '" . $midnight_future . "' and o.order_issue != '1' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and a.state_id = s.state_id and a.county_id = c.county_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end > a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");
						while($result = $database->fetch_array($query)) {
							if (($display_view == 'detailed') && ($result['show_order_id'] == NULL)) {
								$result['show_order_id'] = '1';
							}
				?>
					<tr>
						<td class="pageBoxContent" valign="top"><?php echo $result['order_id']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo date("n/d/Y", $result['date_schedualed']); ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['order_status_name']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['order_type_name']; ?></td>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxContent" valign="top"><?php echo $result['house_number'].' ' .$result['street_name'].'<br>'.$result['city'].'<br>'.$result['county_name'].' '.$result['state_name'].'<br>'.$result['zip'] . (($result['address_post_allowed'] == '0') ? '<br><b>Posts may not be allowed at this address.</b>' : ''); ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['service_level_name']; ?></td>
						<?php
							}
						?>
						<td class="pageBoxContent" align="right" valign="top"><?php echo $result['zip4']; ?></td>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxContent" align="right" valign="top"><input type="hidden" name="order_id[]" value="<?php echo $result['order_id']; ?>" /><input type="text" size="1" name="order_<?php echo $result['order_id']; ?>" value="<?php echo $result['show_order_id']; ?>" /></td>
						<?php
							}
						?>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
						}
			?>
			</table>
			<?php
							if ($display_view == 'detailed') {
						?>
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
							</tr>
							<tr>
								<td><input type="submit" name="submit_value" value="Update Order" /></td>
								<td align="right"><input type="submit" name="submit_value" value="Accept Jobs" /></td>
							</tr>
						</table>
							</form>
						<?php
							}
						?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<table width="250" cellspacing="0" celpadding="0" class="pageBox">
				<?php
					if ($submit_value != 'Accept Jobs') {
				?>
				<tr>
					<td class="pageBoxContent">&PAGE_TEXT</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
				</tr>
				<tr>
					<td width="100%"><HR></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
				</tr>
				<tr>
					<td width="100%">
						<table width="100%" cellspacing="0" cellpadding="0">
						<?php
							//Show items that are both.
						?>
						<form action="<?php echo FILENAME_INSTALLER_VIEW_CURRENT; ?>" method="get">
						<tr>
							<td class="pageBoxContent">Show Day: </td>
							<td class="pageBoxContent"><?php echo tep_draw_today_tomorrow_pulldown('day_view', $day_view, ' onchange="this.form.submit();"'); ?></td>
						</tr>
						<tr>
							<td height="2"><img src="images/pixel_trans.gif" height="2" width="1" /></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Show View: </td>
							<td class="pageBoxContent"><?php echo tep_draw_detailed_overview_pulldown('display_view', $display_view, ' onchange="this.form.submit();"'); ?></td>
						</tr>
						</form>
						<?php
							if ($display_view == 'detailed') {
								//Show options for detailed.
						?>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td colspan="2" align="right" class="main"><a href="<?php echo FILENAME_INSTALLER_VIEW_PRINTABLE; ?>?display_view=<?php echo $display_view; ?>&day_view=<?php echo $day_view; ?>" target="_blank">Show Printable Jobsheet</a> | <a href="<?php echo FILENAME_INSTALLER_VIEW_PRINTABLE_EQUIPMENT; ?>?display_view=<?php echo $display_view; ?>&day_view=<?php echo $day_view; ?>" target="_blank">Show Printable Equipment Sheet</a></td>
						</tr>
						<?php
							} else {
								//Show options for overview.
						?>
						
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
					<td class="pageBoxContent">Are you sure you want to accept these jobs?  This action can not be undone and will schedule all unscheduled jobs for <?php echo ucfirst($day_view); ?>.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
				</tr>
				<form action="<?php echo FILENAME_INSTALLER_VIEW_CURRENT; ?>?page_action=update_order&day_view=<?php echo $day_view; ?>&display_view=<?php echo $display_view; ?>" method="post">
				<?php
					$order_id = tep_fill_variable('order_id', 'post', array());
					$count = count($order_id);
					$n = 0;
						while($n < $count) {
					?>
						<input type="hidden" name="order_id[]" value="<?php echo $order_id[$n]; 	?>" />
					<?php
								$n++;
						}
				?>
				<tr>
					<td width="100%">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left"><input type="submit" name="submit_value" value="Confirm Accept" /></td>
								<td align="right"><input type="submit" name="submit_value" value="Cancel" /></td>
							</tr>
						</table>
				</tr>
				</form>
				<?php
					}
				?>
			</table>
		</td>
	</tr>
</table>