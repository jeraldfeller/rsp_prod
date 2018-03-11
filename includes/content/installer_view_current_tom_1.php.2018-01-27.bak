<?php

	//This is now tomorrow.
	
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
	   
  
		} 

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('installer_view_current')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('installer_view_current'); ?></td>
	</tr>
	<tr>
		<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
	</tr>
	<?php
		}
	$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp()) + 1), date("Y", tep_fetch_current_timestamp())); 
	
	//Check if tomorrow was a sunday, if so then extend that date.
		if (date("w", ($midnight_tonight+1)) == 0) {
			$midnight_tonight += (60*60*24);
		}
	//Now get the next day and work out if it is a sunday, if so then extend the date.
	$midnight_tonight += (60*60*24);
		if (date("w", ($midnight_tonight+1)) == 0) {
			$midnight_tonight += (60*60*24);
		}
	
	$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
	?>
	<tr>
		<td class="main"><b>Jobs for <?php echo date("l dS \of F Y", ($midnight_tonight+1)); ?></b></td>
	</tr>
	<tr>
		<td class="main">
			<table width="100%" cellspacing="2" cellpadding="2">
				<?php /*<tr>
					<td class="main">Installations: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '1', '', '', false); ?></td>
					<td class="main">Service Calls: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '2', '', '', false); ?></td>
					<td class="main">Removals: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '3', '', '', false); ?></td>
				</tr> */ ?>
				<tr>
					<td class="main">Installations: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '1', '1', '', true); ?> Pending and <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '1', '2', '', true); ?> Scheduled</td>
				</tr>
				<tr>
					<td class="main">Service Calls: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '2', '1', '', true); ?> Pending and <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '2', '2', '', true); ?> Scheduled</td>
				</tr>
				<tr>
					<td class="main">Removals: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '3', '1', '', true); ?> Pending and <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_tonight+1)), date("n", ($midnight_tonight+1)), date("Y", ($midnight_tonight+1)), '3', '2', '', true); ?> Scheduled</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="100%" valign="top">
		<?php
				$where = '';
				//Here we work out if it is today or tomorrow and change the where to match.
					//if ($day_view == 'tomorrow') {
						//$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
						//$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
					//} else {
					//Need to work out if tomorrow is a sunday, if so then make it to the next monday.
						/*$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp())); 
						$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
							if (date("w", $midnight_tonight) == 0) {
								$midnight_tonight += (60*60*24);
								$midnight_future += (60*60*24);
							}*/
					//}
				//We only want the orders for the specifed day.
		?>			
						<?php
							if ($display_view == 'detailed') {
						?>
							<form action="<?php echo FILENAME_INSTALLER_VIEW_CURRENT_TOM_1; ?>?page_action=update_order&day_view=<?php echo $day_view; ?>&display_view=<?php echo $display_view; ?>" method="post">
						<?php
							}
						?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					
					<tr>
						<td class="pageBoxHeading">Date</td>
						<td class="pageBoxHeading">Type</td>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxHeading">Job Status</td>
						<td class="pageBoxHeading">Address</td>
						<td class="pageBoxHeading">Service Level</td>
						
						<?php
							} else {
						?>
						<td class="pageBoxHeading">House #</td>
						<td class="pageBoxHeading">Street</td>
						<td class="pageBoxHeading">City</td>
						<?php
							}
						?>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxHeading" align="right">Order</td>
						<?php
							}
						?>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$extra = '';
						if ($display_view == 'detailed') {
							//Fetch extra information,
							$extra = ', otiso.show_order_id, a.house_number, a.street_name,  a.cross_street_directions, a.number_of_posts, a.address_post_allowed, a.city, a.zip, s.name as state_name, c.name as county_name,sld.name as service_level_name, od.special_instructions, od.admin_comments';
						} else {
							$extra = ', a.house_number, a.street_name, a.city';
						}
						
 
   
					$query = $database->query("select o.order_id, o.date_schedualed, o.order_status_id, os.order_status_name, ot.name as order_type_name, otiso.show_order_id as order_column, a.zip4".$extra." from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and o.date_schedualed >= '" . $midnight_tonight . "' and o.order_issue != '1' and o.order_status_id < '3' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) order by " . (($display_view == 'detailed') ? 'order_column' : 'o.date_schedualed ASC'));
					$loop = 0;
 
						while($result = $database->fetch_array($query)) {
							$loop++;
								if (($display_view == 'detailed') && ($result['show_order_id'] == NULL)) {
									$result['show_order_id'] = '1';
								}
				?>
					<tr>
						<td class="pageBoxContent" valign="top"><?php echo date("n/d/Y", $result['date_schedualed']); ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['order_type_name']; ?></td>
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxContent" valign="top"><?php echo $result['order_status_name']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['house_number'].' ' .$result['street_name'].'<br>'.$result['city'].' '.$result['state_name'].' '.$result['zip'] . (($result['address_post_allowed'] == '0') ? '<br><b>Posts may not be allowed at this address.</b>' : ''); ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['service_level_name']; ?></td>
						<?php
							} else {
						?>
						<td class="pageBoxContent" valign="top" align="right"><?php echo $result['house_number']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['street_name']; ?></td>
						<td class="pageBoxContent" valign="top"><?php echo $result['city']; ?></td>
						<?php
							}
						?>
						
						<?php
							if ($display_view == 'detailed') {
						?>
						<td class="pageBoxContent" align="right" valign="top"><input type="hidden" name="order_id[]" value="<?php echo $result['order_id']; ?>" /><input type="text" size="1" name="order_<?php echo $result['order_id']; ?>" value="<?php echo $result['show_order_id']; ?>" /></td>
						<?php
							}
						?>
						<td class="pageBoxContent" align="right" valign="top"><?php echo '<a href="'.FILENAME_INSTALLER_VIEW_DETAILS.'?oID='. $result['order_id'].'&page='.FILENAME_INSTALLER_VIEW_CURRENT_TOM_1.'">View Details</a>'; ?></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
						}
						if ($loop == 0) {
							?>
							<tr>
								<td colspan="<?php echo (($display_view == 'detailed') ? '9' : '6'); ?>" class="main">There are currently no orders assigned to you for Tomorrow + 1.</td>
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
								<td><?php echo tep_create_button_submit('update_job_order', 'Update Order', ' name="submit_value"'); ?></form></td>
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
						<form action="<?php echo FILENAME_INSTALLER_VIEW_CURRENT_TOM_1; ?>" method="get">
						<tr>
							<td class="pageBoxContent">Show View: </td>
							<td class="pageBoxContent"><?php echo tep_draw_detailed_overview_pulldown('display_view', $display_view, ' onchange="this.form.submit();"'); ?></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td colspan="2" width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="right"><?php echo tep_create_button_submit('update', 'Update'); ?></td>
									</tr>
								</table>
							</td>
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
							<td colspan="2" align="right" class="main"><a href="<?php echo FILENAME_INSTALLER_VIEW_PRINTABLE; ?>?display_view=<?php echo $display_view; ?>&day_view=tomorrow1" target="_blank">Show Printable Jobsheet</a><br /><br /><a href="<?php echo FILENAME_INSTALLER_VIEW_PRINTABLE_EQUIPMENT; ?>?display_view=<?php echo $display_view; ?>&day_view=tomorrow1" target="_blank">Show Printable Equipment Sheet</a></td>
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
				
			</table>
		</td>
	</tr>
</table>