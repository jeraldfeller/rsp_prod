<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$view_type = tep_fill_variable('view_type', 'get', tep_fill_variable('view_type', 'post', 'day'));
	$day = tep_fill_variable('day', 'get', tep_fill_variable('day', 'post', date("d", mktime())));
	$month = tep_fill_variable('month', 'get', tep_fill_variable('month', 'post', date("n", mktime())));
	$year = tep_fill_variable('year', 'get', tep_fill_variable('year', 'post', date("Y", mktime())));
	$show_installer_id = tep_fill_variable('show_installer_id', 'get', tep_fill_variable('show_installer_id', 'post', ''));
	$show_state_id = tep_fill_variable('show_state_id', 'get', tep_fill_variable('show_state_id', 'post', ''));
	$show_count_breakdown = tep_fill_variable('show_count_breakdown', 'post', tep_fill_variable('show_count_breakdown', 'session', '0'));
	$reassign_installer_id = tep_fill_variable('reassign_installer_id', 'get', tep_fill_variable('reassign_installer_id', 'post', ''));

	$session->php_session_register('show_count_breakdown', $show_count_breakdown);	
	$message = '';
		if ($page_action == 'update') {
			if ($view_type == 'default') {
				//Query data from database and loop over and update.
				$new_installer_ids = tep_fill_variable('installation_area_id');
				//$query = $database->query("select installation_area_id, installer_id from " . TABLE_INSTALLATION_AREAS . ((!empty($show_state_id) || !empty($show_installer_id)) ? " where " : '') . ((!empty($show_installer_id)) ? " installer_id = '" . $show_installer_id . "'" : '') .  ((!empty($show_state_id)) ? (((!empty($show_installer_id)) ? " and " : '') . " state_id = '" . $show_state_id . "'") : ''));
					//while($result = $database->fetch_array($query)) {
					if (is_array($new_installer_ids)) {
						reset($new_installer_ids);
							while(list($id, $new_id) = each($new_installer_ids)) {
									//if (isset($new_installer_ids[$result['installation_area_id']])) {
										//$new_id = $new_installer_ids[$result['installation_area_id']];
									//} else {
										//$new_id = '';
								//}
								$database->query("update " . TABLE_INSTALLATION_AREAS . " set installer_id = '" . $new_id . "' where installation_area_id = '" . $id . "' limit 1");
							}
					}
			} elseif ($view_type == 'day') {
				$timestamp = time(0, 0, 0, $month, $day, $year);
				$start_of_day = time(0, 0, 0, date("n", $timestamp), date("d", $timestamp), date("Y", $timestamp));
				$end_of_day = time(0, 0, -1, date("n", $timestamp), (date("d", $timestamp)+1), date("Y", $timestamp));
				
				$new_installer_ids = tep_fill_variable('installation_area_id');
				//$query = $database->query("select installation_area_id, installer_id from " . TABLE_INSTALLATION_AREAS .  ((!empty($show_state_id) || !empty($show_installer_id)) ? " where " : '') . ((!empty($show_installer_id)) ? " installer_id = '" . $show_installer_id . "'" : '') .  ((!empty($show_state_id)) ? (((!empty($show_installer_id)) ? " and " : '') . " state_id = '" . $show_state_id . "'") : ''));
					if (is_array($new_installer_ids)) {
						reset($new_installer_ids);
							while(list($id, $new_id) = each($new_installer_ids)) {
								//We only use the join table here.  First we need to check if an entry exists and if so then update otherwise create.
								$check_query = $database->query("select installation_area_id, installer_id from " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " where installation_area_id = '" . $id . "' and date_covering = '" . $start_of_day . "' limit 1");
								$check_result = $database->fetch_array($check_query);
									if (($check_result != NULL) && ($check_result['installation_area_id'] != NULL)) {
										if ($check_result['installer_id'] != $new_id) {
											//Found - update.
											$database->query("update " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " set installer_id = '" . $new_id . "' where installation_area_id = '" . $id . "' and date_covering = '" . $start_of_day . "' limit 1");
										}
									} else {
										if ($check_result['installer_id'] != $new_id) {
											//Not found - create.
											$database->query("insert into " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " (installation_area_id, installer_id, date_covering, date_end_covering) values ('" . $id . "', '" . $new_id . "', '" . $start_of_day . "', '" . $end_of_day . "')");
										}
									}
							}
					}
			} elseif ($view_type == 'week') {
				$timestamp = strtotime("+2 day");
				$start_of_day = time(0, 0, 1, date("n", $timestamp), date("d", $timestamp), date("Y", $timestamp));
					
				$new_installer_ids = tep_fill_variable('installation_area_id');
				$query = $database->query("select installation_area_id, installer_id from " . TABLE_INSTALLATION_AREAS  . ((!empty($show_state_id) || !empty($show_installer_id)) ? " where " : '') . ((!empty($show_installer_id)) ? " installer_id = '" . $show_installer_id . "'" : '') .  ((!empty($show_state_id)) ? (((!empty($show_installer_id)) ? " and " : '') . " state_id = '" . $show_state_id . "'") : ''));
					foreach($query as $result){
							if (isset($new_installer_ids[$result['installation_area_id']])) {
								$new_id = $new_installer_ids[$result['installation_area_id']];
							} else {
								$new_id = '';
							}
						//We only use the join table here.  First we need to check if an entry exists and if so then update otherwise create.
						$check_query = $database->query("select installation_area_id from " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " where installation_area_id = '" . $result['installation_area_id'] . "' and date_covering = '" . $start_of_day . "' limit 1");
						$check_result = $database->fetch_array($check_query);
							if ($check_result['installer_id'] != NULL) {
								//Found - update.
								$database->query("update " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " set installer_id = '" . $new_id . "' where installation_area_id = '" . $result['installation_area_id'] . "' and date_covering = '" . $start_of_day . "' limit 1");
							} else {
								//Not found - create.
								$database->query("insert into " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " (installation_area_id, installer_id, date_covering) values ('" . $result['installation_area_id'] . "', '" . $new_id . "', '" . $start_of_day . "')");
							}
					}
			}
		} elseif ($page_action == 'reassign_confirm') {
			
			//First we will reassign all the installation areas in this selection for today.  This is the easier part.
				$timestamp = time(0, 0, 0, $month, $day, $year);
				
				$start_of_day = time(0, 0, 0, date("n", $timestamp), date("d", $timestamp), date("Y", $timestamp));
				$end_of_day = time(0, 0, -1, date("n", $timestamp), (date("d", $timestamp)+1), date("Y", $timestamp));

				$new_installer_ids = tep_fill_variable('installation_area_id');
				//$query = $database->query("select installation_area_id, installer_id from " . TABLE_INSTALLATION_AREAS .  ((!empty($show_state_id) || !empty($show_installer_id)) ? " where " : '') . ((!empty($show_installer_id)) ? " installer_id = '" . $show_installer_id . "'" : '') .  ((!empty($show_state_id)) ? (((!empty($show_installer_id)) ? " and " : '') . " state_id = '" . $show_state_id . "'") : ''));
					if (is_array($new_installer_ids)) {
						reset($new_installer_ids);
							while(list($id, $new_id) = each($new_installer_ids)) {
								$new_id = $reassign_installer_id;
								//We only use the join table here.  First we need to check if an entry exists and if so then update otherwise create.
								$check_query = $database->query("select installation_area_id, installer_id from " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " where installation_area_id = '" . $id . "' and date_covering = '" . $start_of_day . "' limit 1");
								$check_result = $database->fetch_array($check_query);
									if (($check_result != NULL) && ($check_result['installation_area_id'] != NULL)) {
										if ($check_result['installer_id'] != $new_id) {
											//Found - update.
											$database->query("update " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " set installer_id = '" . $new_id . "' where installation_area_id = '" . $id . "' and date_covering = '" . $start_of_day . "' limit 1");
											//echo "update " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " set installer_id = '" . $new_id . "' where installation_area_id = '" . $id . "' and date_covering = '" . $start_of_day . "' limit 1" . '<br>';
										}
									} else {
										if ($check_result['installer_id'] != $new_id) {
											//Not found - create.
											$database->query("insert into " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " (installation_area_id, installer_id, date_covering, date_end_covering) values ('" . $id . "', '" . $new_id . "', '" . $start_of_day . "', '" . $end_of_day . "')");
											//echo"insert into " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " (installation_area_id, installer_id, date_covering, date_end_covering) values ('" . $id . "', '" . $new_id . "', '" . $start_of_day . "', '" . $end_of_day . "')". '<br>';
										}
									}
							}
					}
			
			//Now lets loop over all the orders that should have been done today and manually assign them.  We don't do this for ones today as this would make it hard to reassign themagain.
			//$query = $database->query("select o.order_id from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDER_TYPES . " ot, " . TABLE_USERS . " u, " . TABLE_ADDRESSES . " a left join " . TABLE_INSTALLATION_AREAS . " ia on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) AND ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed), " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) where o.order_type_id = ot.order_type_id and o.order_status_id > 0 and o.order_status_id = os.order_status_id and (o.order_status_id = '1' or o.order_status_id = '2') and o.date_schedualed < '" . ($start_of_day-1) . "' and o.address_id = a.address_id and o.user_id = u.user_id and  ((ito.installer_id = '" . $show_installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $show_installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $show_installer_id . "')) group by o.order_id order by o.date_schedualed DESC");
			$query = $database->query("select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.order_type_id = ot.order_type_id and o.order_status_id > 0 and o.order_status_id = os.order_status_id and (o.order_status_id = '1' or o.order_status_id = '2') and o.date_schedualed < '" . ($start_of_day-1) . "' and o.address_id = a.address_id and o.user_id = u.user_id and  ((ito.installer_id = '" . $show_installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $show_installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $show_installer_id . "')) group by o.order_id order by o.date_schedualed DESC");

				//echo "select o.order_id from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDER_TYPES . " ot, " . TABLE_USERS . " u, " . TABLE_ADDRESSES . " a left join " . TABLE_INSTALLATION_AREAS . " ia on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) AND ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed), " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) where o.order_type_id = ot.order_type_id and o.order_status_id > 0 and o.order_status_id = os.order_status_id and (o.order_status_id = '1' or o.order_status_id = '2') and o.date_schedualed < '" . ($start_of_day-1) . "' and o.address_id = a.address_id and o.user_id = u.user_id and  ((ito.installer_id = '" . $show_installer_id . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $show_installer_id . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $show_installer_id . "')) group by o.order_id order by o.date_schedualed DESC". '<br>';
				foreach($database->fetch_array($query) as $result){
					$assigned_query = $database->query("select count(installer_id) as count from " . TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $result['order_id'] . "' limit 1");
					$assigned_result = $database->fetch_array($assigned_query);
						if (!empty($assigned_result['count'])) {
							$database->query("update " . TABLE_INSTALLERS_TO_ORDERS . " set installer_id = '" . $reassign_installer_id . "' where order_id = '" . $result['order_id'] . "' limit 1");
							//echo "update " . TABLE_INSTALLERS_TO_ORDERS . " set installer_id = '" . $reassign_installer_id . "' where order_id = '" . $result['order_id'] . "' limit 1" . '<br>';
						} else {
							$database->query("insert into " . TABLE_INSTALLERS_TO_ORDERS . " (installer_id, order_id) values ('" . $reassign_installer_id . "', '" . $result['order_id'] . "')");
							//echo "insert into " . TABLE_INSTALLERS_TO_ORDERS . " (installer_id, order_id) values ('" . $reassign_installer_id . "', '" . $result['order_id'] . "')" . '<br>';
						}
				}
			
			//die();
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_installer_assignment')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_installer_assignment'); ?></td>
	</tr>
	<?php
		}
		if ($view_type == 'default') {
			$title = 'Viewing default installer assignments';
		} elseif ($view_type == 'day') {
			$title = 'Viewing installer assignments for '.date("l, F d", mktime(0, 0, 1, $month, $day, $year));
		} elseif ($view_type == 'week') {
		
		}
	?>
	<?php
		if ($page_action == 'reassign') {
	?>
	<form action="<?php echo PAGE_URL; ?>?page_action=reassign_confirm " method="post">
	<?php
		} else {
	?>
	<form action="<?php echo PAGE_URL; ?>?page_action=update" method="post">
	<?php
		}
	?>
	<tr>
		<td class="main"><b><?php echo $title; ?></b></td>
	</tr>
	<tr>
		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
	</tr>
	<tr>
		<td width="100%" valign="top">
			<?php
			//If this is either a tomorrow or the next day then show the orders to installer.
				if (($view_type == 'day')) {
			?>
			
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td class="main"><b>The table below shows the number of order each installer has assigned based on the default and above assignments.</b><br /><i>If an order has been specifically assigned to a installer or the installer accepted the order before the assignment was changed then you can only change this via the order administration.</i><br /><?php if ($show_count_breakdown == '1') { ?><br /><i>Numbers in brackets represent the total for today broken down (in order) of area default assigned, daily area assigned and manually assigned.</i><?php } ?></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
				</tr>
			</table>
			<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
				<tr>
					<td class="pageBoxHeading">Installer Name</td>
					<td class="pageBoxHeading" align="center">Installs<?php  if ($show_count_breakdown == '1') { ?> (total/today only)<?php } ?></td>
					<td class="pageBoxHeading" align="center">Service Calls<?php  if ($show_count_breakdown == '1') { ?> (total/today only)<?php } ?></td>
					<td class="pageBoxHeading" align="center">Removals<?php  if ($show_count_breakdown == '1') { ?> (total/today only)<?php } ?></td>
					<td class="pageBoxHeading" align="center">Total<?php  if ($show_count_breakdown == '1') { ?> (total/today only)<?php } ?></td>
					<td width="10" class="pageBoxHeading"></td>	
				</tr>
				<?php
					//Add the assignments here.
					$query = $database->query("select utug.user_id, ud.firstname, ud.lastname from " . TABLE_USERS_TO_USER_GROUPS . " utug, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS . " u where utug.user_group_id = '3' and utug.user_id = ud.user_id and ud.user_id = u.user_id and u.active_status = '1' " . ((!empty($show_installer_id)) ? " and utug.user_id = '" . $show_installer_id . "' " : '') . " order by ud.lastname, ud.firstname");
                    foreach($database->fetch_array($query) as $result){
				?>
				<tr>
					<td class="pageBoxContent"><?php echo $result['lastname'].', '.$result['firstname']; ?></td>
					<td class="pageBoxContent" align="center"><?php echo tep_count_installer_orders($result['user_id'], $day, $month, $year, '1', $show_state_id, '', false); if ($show_count_breakdown == '1') { $today_count = tep_count_installer_orders($result['user_id'], $day, $month, $year, '1', $show_state_id, '', true); echo (($today_count > 0) ? ' ('.$today_count.' - ' . tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '1', $show_state_id, '', true, 'default').', '.tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '1', $show_state_id, '', true, 'day').', '.tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '1', $show_state_id, '', true, 'assigned').')' : ' (0)'); } ?></td>
					<td class="pageBoxContent" align="center"><?php echo tep_count_installer_orders($result['user_id'], $day, $month, $year, '2', $show_state_id, '', false); if ($show_count_breakdown == '1') { $today_count = tep_count_installer_orders($result['user_id'], $day, $month, $year, '2', $show_state_id, '', true); echo (($today_count > 0) ? ' ('.$today_count.' - ' . tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '2', $show_state_id, '', true, 'default').', '.tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '2', $show_state_id, '', true, 'day').', '.tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '2', $show_state_id, '', true, 'assigned').')' : ' (0)'); } ?></td>
					<td class="pageBoxContent" align="center"><?php echo tep_count_installer_orders($result['user_id'], $day, $month, $year, '3', $show_state_id, '', false); if ($show_count_breakdown == '1') { $today_count = tep_count_installer_orders($result['user_id'], $day, $month, $year, '3', $show_state_id, '', true); echo (($today_count > 0) ? ' ('.$today_count.' - ' . tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '3', $show_state_id, '', true, 'default').', '.tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '3', $show_state_id, '', true, 'day').', '.tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '3', $show_state_id, '', true, 'assigned').')' : ' (0)'); } ?></td>
					<td class="pageBoxContent" align="center"><?php echo tep_count_installer_orders($result['user_id'], $day, $month, $year, '', $show_state_id, '', false); if ($show_count_breakdown == '1') { $today_count = tep_count_installer_orders($result['user_id'], $day, $month, $year, '', $show_state_id, '', true); echo (($today_count > 0) ? ' ('.$today_count.' - ' . tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '', $show_state_id, '', true, 'default').', '.tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '', $show_state_id, '', true, 'day').', '.tep_count_installer_orders_sort($result['user_id'], $day, $month, $year, '', $show_state_id, '', true, 'assigned').')' : ' (0)'); } ?></td>
					<td width="10" class="pageBoxContent"></td>	
				</tr>
			<?php
						}
				?>
			</table>
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
				</tr>
			</table>
				<?php
				}
			?>
			
			<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
				<tr>
					<td class="pageBoxHeading">Service Area</td>
					<td class="pageBoxHeading" align="center">Assigned Installer</td>
					<td class="pageBoxHeading" align="center">Installs</td>
					<td class="pageBoxHeading" align="center">Service</td>
					<td class="pageBoxHeading" align="center">Removals</td>
					<td class="pageBoxHeading" align="center">Total</td>
					<td width="10" class="pageBoxHeading"></td>
				</tr>
				<?php
					if ($view_type == 'default') {
						$form_type = 'run';
						echo '<input type="hidden" name="view_type" value="default">';
						$query = $database->query("select installation_area_id, name, installer_id from " . TABLE_INSTALLATION_AREAS . " " . ((!empty($show_state_id) || !empty($show_installer_id)) ? " where " : '') . ((!empty($show_installer_id)) ? " installer_id = '" . $show_installer_id . "'" : '') .  ((!empty($show_state_id)) ? (((!empty($show_installer_id)) ? " and " : '') . " state_id = '" . $show_state_id . "'") : '') . " order by installer_id, name");
							foreach($database->fetch_array($query) as $result){
						?>
						<tr>
							<td class="pageBoxContent"><?php echo $result['name']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo tep_draw_installer_pulldown('installation_area_id['.$result['installation_area_id'].']', $result['installer_id'], array(array('id' => '', 'name' => 'Unassigned'))); ?></td>
							<td class="pageBoxContent" align="center"></td>
							<td class="pageBoxContent" align="center"></td>
							<td class="pageBoxContent" align="center"></td>
							<td class="pageBoxContent" align="center"></td>
							<td width="10" class="pageBoxContent"></td>
						</tr>
						<?php
							}
					} elseif ($view_type == 'day') {
						//Start of day;
						$timestamp = time(0, 0, 0, $month, $day, $year);
						$start_of_day = time(0, 0, 0, date("n", $timestamp), date("d", $timestamp), date("Y", $timestamp));
						//Check if the date is in the past, changed to allow for assignments based on today.
						$current_day = date("d", time());
						$current_month = date("n", time());
						$current_year = date("Y", time());
						
							if (($year < $current_year) || (($year == $current_year) && ($month < $current_month)) || (($year == $current_year) && ($month == $current_month) && ($day < $current_day))) {
								$form_type = 'lock';
							} else {
								$form_type = 'run';
							}
						echo '<input type="hidden" name="view_type" value="day"><input type="hidden" name="day" value="'.$day.'"><input type="hidden" name="month" value="'.$month.'"><input type="hidden" name="year" value="'.$year.'">';
						$query = $database->query("select ia.installation_area_id, ia.name, ia.installer_id as default_installer_id, itia.installer_id as new_installer_id from " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and itia.date_covering = '" . $start_of_day . "') " . ((!empty($show_installer_id) || !empty($show_state_id)) ? " where " : '') . ((!empty($show_installer_id)) ? " (ia.installer_id = '" . $show_installer_id . "' or itia.installer_id = '" . $show_installer_id . "') " : '') . ((!empty($show_state_id)) ? (((!empty($show_installer_id)) ? " and " : '') . " ia.state_id = '" . $show_state_id . "' ") : '') . " order by default_installer_id, new_installer_id, name");
							foreach($database->fetch_array($query) as $result){
								
								if ($result['new_installer_id'] != NULL) {
									$installer_id = $result['new_installer_id'];
								} else {
									$installer_id = $result['default_installer_id'];
								}
								if ($form_type == 'run') {
									$string = tep_draw_installer_pulldown('installation_area_id['.$result['installation_area_id'].']', $installer_id, array(array('id' => '', 'name' => 'Unassigned')));
								} else {
									$string = tep_fetch_installer_name($installer_id);
								}
						?>
						<tr>
							<td class="pageBoxContent"><?php echo $result['name']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $string; ?></td>
							<td class="pageBoxContent" align="center"><?php echo tep_count_area_orders($result['installation_area_id'], $day, $month, $year, 1); ?></td>
							<td class="pageBoxContent" align="center"><?php echo tep_count_area_orders($result['installation_area_id'], $day, $month, $year, 2); ?></td>
							<td class="pageBoxContent" align="center"><?php echo tep_count_area_orders($result['installation_area_id'], $day, $month, $year, 3); ?></td>
							<td class="pageBoxContent" align="center"><?php echo tep_count_area_orders($result['installation_area_id'], $day, $month, $year); ?></td>
							<td width="10" class="pageBoxContent"></td>
						</tr>
						<?php
							}
					} elseif ($view_type == 'week') {
						//Start of day;
						$timestamp = strtotime("+2 day");
						$start_of_day = time(0, 0, 1, date("n", $timestamp), date("d", $timestamp), date("Y", $timestamp));
						echo '<input type="hidden" name="view_type" value="week">';
						$query = $database->query("select ia.installation_area_id, ia.name, ia.installer_id as default_installer_id, itia.installer_id as new_installer_id from " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and itia.date_covering = '" . $start_of_day . "') order by name");
							foreach($database->fetch_array($query) as $result){
								if ($result['new_installer_id'] != NULL) {
									$installer_id = $result['new_installer_id'];
								} else {
									$installer_id = $result['default_installer_id'];
								}
						?>
						<tr>
							<td class="pageBoxContent"><?php echo $result['name']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo tep_draw_installer_pulldown('installation_area_id['.$result['installation_area_id'].']', $installer_id, array(array('id' => '', 'name' => 'Unassigned'))); ?></td>
							<td width="10" class="pageBoxContent"></td>
						</tr>
						<?php
							}
					}
				?>
			</table>
			
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
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><b>Installer Assignment Options</b></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Use the dropdown menus to change the installer and press update below to confirm the changes or use the links below to change the view.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								$callender = new callender('210', '245', $day, $month, $year);
								$callender->set_link_template('<a class=\"callenderCurrentDay\" href=\"'.FILENAME_ADMIN_INSTALLER_ASSIGNMENT.'?view_type=$view_type&day=$day&month=$month&year=$year\">$day</a>');
								$callender->generate_calender();
								$string = $callender->return_callender();
							?>
							<tr>
								<td width="100%" align="center"><?php echo $string; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								if ($page_action == 'reassign') {
									echo '<input type="hidden" name="view_type" value="day"><input type="hidden" name="show_installer_id" value="'.$show_installer_id.'"><input type="hidden" name="show_state_id" value="'.$show_state_id.'">';
								?>
								<tr>
									<td class="main">Select an installer from the list below to reassign
his orders. All areas for today, plus any previous
orders that are not complete, that were assigned to
this installer will be reassigned to the new
installer. This process can not be easily reversed.</td>
								</tr>
								<tr>
									<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
								</tr>
								<tr>
									<td class="main">Orders to be Reassigned: <br /><strong><?php $total = tep_count_installer_orders($show_installer_id, $day, $month, $year, '', $show_state_id, '', false); echo $total; ?></strong> consisting of <strong><?php $today = tep_count_installer_orders($show_installer_id, $day, $month, $year, '', $show_state_id, '', true); echo $today; ?></strong> assigned for today and <strong><?php echo ($total - $today); ?></strong> assigned previously.</td>
								</tr>
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
								</tr>
								<tr>
									<td class="main">Reassign to <?php echo tep_draw_installer_pulldown('reassign_installer_id', $show_installer_id, array()); ?></td>
								</tr>
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
								</tr>
								<tr>
									<td width="100%" align="right"><?php echo tep_create_button_submit('update', 'Update Changes'); ?>&nbsp;</td>
								</tr>
								</form>
								<?php
							
								} else {
								
							?>
							<tr>
								<td class="main">Show only Installer: <?php echo tep_draw_installer_pulldown('show_installer_id', $show_installer_id, array(array('id' => '', 'name' => 'All Installers'))); ?></td>
							</tr>
							<tr>
								<td class="main">Show only State: <?php echo tep_draw_state_pulldown('show_state_id', $show_state_id, '', array(array('id' => '', 'name' => 'All States'))); ?></td>
							</tr>
							<tr>
								<td class="main">Show Count Breakdowns: <input type="radio" name="show_count_breakdown" value="1"<?php echo (($show_count_breakdown == '1') ? ' CHECKED' : ''); ?> />&nbsp;Yes&nbsp;&nbsp;&nbsp;<input type="radio" name="show_count_breakdown" value="0"<?php echo (($show_count_breakdown == '0') ? ' CHECKED' : ''); ?> />&nbsp;No</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main" align="center"><a href="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT; ?>?view_type=default">View Default Assignments</a></td>
							</tr>
							<?php
								if (!empty($show_installer_id) && ($view_type == 'day') && ($form_type == 'run')) {
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><a href="<?php echo FILENAME_ADMIN_INSTALLER_ASSIGNMENT; ?>?view_type=day&day=<?php echo $day; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>&show_installer_id=<?php echo $show_installer_id; ?>&page_action=reassign">Re-assign all orders.</a></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><?php echo tep_create_button_submit('update', 'Update Changes'); ?>&nbsp;</td>
							</tr>
							
							</form>
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