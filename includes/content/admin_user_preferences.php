<?php
/*$orders = array();
$query = $database->query("select address_id from " . TABLE_ORDERS . " where order_type_id = '3'");
	while($result = $database->fetch_array($query)) {
			if (!isset($orders[$result['address_id']])) $orders[$result['address_id']] = 0;
		$orders[$result['address_id']] ++;
	}
reset($orders);
	while(list($id, $count) = each($orders)) {
		if ($count > 1) echo $id . '<br>';
	}	*/

/*echo '"address id","order id"' . "\n";
$query = $database->query("select order_id, address_id, user_id from " . TABLE_ORDERS . " where order_type_id = '3'  and date_added >= '1199149261'");
	while($result = $database->fetch_array($query)) {
		$check_query = $database->query("select order_id from " . TABLE_ORDERS . " where order_type_id = '1' and address_id = '" . $result['address_id'] . "' limit 1");
			if ($database->num_rows($check_query) > 0) {
				//echo $result['order_id'] . ' - ' . $result['address_id'] . ' is matched<br>';
				//echo '<br><br>';
				continue;
			}
		//echo '"'.$result['address_id'].'","'.$result['order_id'].'"' . "\n";
		$address_query = $database->query("select user_id from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . $result['address_id'] . "'");
		$address_result = $database->fetch_array($address_query);
			if (empty($address_result['user_id'])) continue;
		$address_query = $database->query("select address_id from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "'");
		$address_result = $database->fetch_array($address_query);
			if (empty($address_result['address_id'])) continue;
		echo '"'.$result['address_id'].'","'.$result['order_id'].'"' . "\n";
		/*
		$address_query = $database->query("select house_number, street_name, city, zip from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "' limit 1");
		$address_result = $database->fetch_array($address_query);
		
		$find_query = $database->query("select a.address_id from addresses a, addresses_to_users atu, orders o where a.house_number = '" . $address_result['house_number'] . "' and a.street_name = '" . $address_result['street_name'] . "' and a.city = '" . $address_result['city'] . "' and a.address_id = atu.address_id and atu.user_id = '" . $result['user_id'] . "' and a.address_id = o.address_id and o.order_type_id = '1' limit 1");
			if ($database->num_rows($find_query) > 0) {
				$find_result = $database->fetch_array($find_query);
				
				echo $result['order_id'] . ' - ' . $result['address_id'] . ' is unmatched<br>';
				echo 'matches to ' . $find_result['address_id'] . '<br>';
				$database->query("update " . TABLE_ORDERS . " set address_id = '" . $find_result['address_id'] . "' where order_id = '" . $result['order_id'] . "' limit 1");
				echo "update " . TABLE_ORDERS . " set address_id = '" . $find_result['address_id'] . "' where order_id = '" . $result['order_id'] . "' limit 1". '<br>';
				$database->query("update " . TABLE_ADDRESSES_TO_USERS . " set user_id = '0' where address_id = '" . $result['address_id'] . "' limit 1");
				echo "update " . TABLE_ADDRESSES_TO_USERS . " set user_id = '0' where address_id = '" . $result['address_id'] . "' limit 1". '<br>';
				echo '<br><br>';
			} else {
				//echo 'No match found<br>';
			}
			
		//echo '<br><br>';
	}
die();*/
/*$count = 0;
$query = $database->query("select equipment_item_id, equipment_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_status_id = '2'");
	while($result = $database->fetch_array($query)) {
		$equipment_query = $database->query("select name as equipment_name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $result['equipment_id'] . "'");
		$equipment_result = $database->fetch_array($equipment_query);
		
		//if ((strpos($equipment_result['equipment_name'], 'SignPost') === false) && (strpos($equipment_result['equipment_name'], 'Rider') === false) && ($equipment_result['equipment_name'] != 'Generic Brochure Box') && ($equipment_result['equipment_name'] != 'Brochure Box') && ($equipment_result['equipment_name'] != 'Coming Soon') && ($equipment_result['equipment_name'] != 'I\'m Gorgeous Inside') && ($equipment_result['equipment_name'] != 'Open Sunday') && ($equipment_result['equipment_name'] != 'Impressive') && ($equipment_result['equipment_name'] != 'Open Sunday 1-4') && ($equipment_result['equipment_name'] != 'Warranty')) {
			//

				//if ($equipment_result['equipment_status_id'] == '1') {
				
					$last_query = $database->query("select order_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_item_id = '" . $result['equipment_item_id'] . "' order by equipment_to_order_id DESC limit 1");
					$last_result = $database->fetch_array($last_query);
						
						//if ($last_result['order_id'] == $result['order_id']) {
						$order_query = $database->query("select order_type_id, order_status_id, date_schedualed, service_level_id from " . TABLE_ORDERS . " where order_id = '" . $last_result['order_id'] . "'");
						$order_result = $database->fetch_array($order_query);
							if ((($order_result['order_type_id'] == '1') || ($order_result['order_type_id'] == '2'))&& ($order_result['order_status_id'] != '4')) {
								continue;
							}
							if (($order_result['order_type_id'] == '3') && ($order_result['order_status_id'] < '3')) {
								continue;
							}
						//$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id']. "' limit 1");
						echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $result['equipment_item_id']. "' limit 1". '<br>';
						//echo $result['equipment_item_id'] . ' - ' . $equipment_result['equipment_name'] . ' - ' . $last_result['order_id']  . ' - ' . $order_result['order_type_id']  . ' - ' . $order_result['order_status_id']  . ' - ' . $order_result['service_level_id'] . '<br>';
						$count++;	
						//}
					
					
			//	}
		//}
		
	}
die('done - ' . $count);*/
//die(strtotime('14/3/2008 00:00:00'));
/*
$file = file(DIW_FS . 'orders2.csv');
	for ($n = 1, $m = count($file); $n < $m; $n++) {
		$file[$n] = trim($file[$n]);
			if (empty($file[$n])) {
				continue;
			}
		$explode = explode(',', str_replace('"', '', $file[$n]));
		$address_id = $explode[0];
		$order_id = $explode[1];
		$new_address_id = $explode[4];
		$new_order_id = $explode[3];
		
			if ($new_order_id == 'delete') {
					echo 'Deleteing ' . $explode[3] . ' - ' . substr($explode[5], 1, 4). '<br>';

					echo "delete from " . TABLE_ORDERS . " where order_id = '" . (int)$order_id . "' limit 1". '<br>';
					$database->query("delete from " . TABLE_ORDERS . " where order_id = '" . (int)$explode[0] . "' limit 1");
					//echo "delete from " . TABLE_ADDRESSES . " where address_id = '" . (int)$address_result['address_id'] . "' limit 1". '<br>';
					//$database->query("delete from " . TABLE_ADDRESSES . " where address_id = '" . (int)$address_result['address_id'] . "' limit 1");
					//echo "delete from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . (int)$address_result['address_id'] . "' limit 1". '<br>';
					//$database->query("delete from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . (int)$address_result['address_id'] . "' limit 1");
					//echo"delete from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . (int)$address_result['address_id'] . "' limit 1". '<br>';
					//$database->query("delete from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . (int)$address_result['address_id'] . "' limit 1");
					echo "delete from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . (int)$order_id . "' limit 1". '<br>';
					$database->query("delete from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . (int)$explode[0] . "' limit 1");

			//} elseif (isset($explode[5]) && ((substr($explode[5], 1, 4) == 'Inst') || (substr($explode[5], 1, 4) == 'inst'))) {
			//		echo 'Updating ' . $explode[3] . '<br>';
			//		$date_explode = explode(',', $explode[5]);
			//		$date = explode('/', substr($date_explode[0], 8));
			//		echo 'New date:' . substr($date_explode[0], 8). ' - ' . date("n/d/Y", mktime(1, 1, 1, $date[0], $date[1], $date[2])) . 'end<br>';
			//		$database->query("update " . TABLE_ORDERS . " set date_completed = '" . mktime(1, 1, 1, $date[0], $date[1], $date[2]) . "' where order_id = '" . (int)$explode[0] . "' limit 1");
			//		echo "update " . TABLE_ORDERS . " set date_completed = '" . mktime(1, 1, 1, $date[0], $date[1], $date[2]) . "' where order_id = '" . (int)$explode[0] . "' limit 1". '<br>';
			} elseif (is_numeric($new_order_id)) {
				$database->query("update " . TABLE_ORDERS . " set address_id = '" . $new_address_id . "' where order_id = '" . (int)$order_id . "' limit 1");
				echo "update " . TABLE_ORDERS . " set address_id = '" . $new_address_id . "' where order_id = '" . (int)$order_id . "' limit 1" . '<br>';
			}

	}
die();*/

/*
$file = file(DIW_FS . 'updates.csv');
	for ($n = 4, $m = count($file); $n < $m; $n++) {
		$file[$n] = trim($file[$n]);
			if (empty($file[$n])) {
				continue;
			}
			$explode = explode(',', $file[$n]);
var_dump($explode);
echo '<br>';
	
					$address_query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . (int)$explode[0] . "'");
					echo "select address_id from " . TABLE_ORDERS . " where order_id = '" . (int)$explode[0] . "'". '<br>';
						if ($database->num_rows($address_query) == 0) {
							continue;
						}
					$address_result = $database->fetch_array($address_query);

					$delete_address_query = $database->query("select address_id from " . TABLE_ORDERS . " where order_id = '" . (int)$explode[1] . "'");
					echo "select address_id from " . TABLE_ORDERS . " where order_id = '" . (int)$explode[1] . "'". '<br>';
					$delete_address_result = $database->fetch_array($delete_address_query);

					//echo "delete from " . TABLE_ADDRESSES . " where address_id = '" . (int)$delete_address_result['address_id'] . "' limit 1". '<br>';
					//$database->query("delete from " . TABLE_ADDRESSES . " where address_id = '" . (int)$delete_address_result['address_id'] . "' limit 1");
					//echo "delete from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . (int)$delete_address_result['address_id'] . "' limit 1". '<br>';
					//$database->query("delete from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . (int)$delete_address_result['address_id'] . "' limit 1");
					//echo"delete from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . (int)$delete_address_result['address_id'] . "' limit 1". '<br>';
					//$database->query("delete from " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " where address_id = '" . (int)$delete_address_result['address_id'] . "' limit 1");

					$database->query("update " . TABLE_ORDERS . " set address_id = '" . $address_result['address_id'] . "' where order_id = '" . (int)$explode[1] . "' limit 1");
					echo "update " . TABLE_ORDERS . " set address_id = '" . $address_result['address_id'] . "' where order_id = '" . (int)$explode[1] . "' limit 1". '<br>';
			//} elseif (isset($explode[5]) && ((substr($explode[5], 1, 4) == 'Inst') || (substr($explode[5], 1, 4) == 'inst'))) {
			//		echo 'Updating ' . $explode[3] . '<br>';
			//		$date_explode = explode(',', $explode[5]);
			//		$date = explode('/', substr($date_explode[0], 8));
			//		echo 'New date:' . substr($date_explode[0], 8). ' - ' . date("n/d/Y", mktime(1, 1, 1, $date[0], $date[1], $date[2])) . 'end<br>';
			//		$database->query("update " . TABLE_ORDERS . " set date_completed = '" . mktime(1, 1, 1, $date[0], $date[1], $date[2]) . "' where order_id = '" . (int)$explode[0] . "' limit 1");
			//		echo "update " . TABLE_ORDERS . " set date_completed = '" . mktime(1, 1, 1, $date[0], $date[1], $date[2]) . "' where order_id = '" . (int)$explode[0] . "' limit 1". '<br>';

echo '<br><br>';
	}
die();*/
/*
$allowed_array = array();
$allowed_array[] = '3163 Eakin Park Court';
$allowed_array[] = '5971 Wayne Rd';
$allowed_array[] = '5975 Wayne Rd';
$allowed_array[] = '5971 Wayne';
$allowed_array[] = '5975 Wayne';
$allowed_array[] = '3472 Turnberry';
$allowed_array[] = '3472 Turnberry Drive';
$allowed_array[] = '173 4th Street N';
$allowed_array[] = '25 S Franklin';
$allowed_array[] = '468 Maine street';
$allowed_array[] = '1024 Maine street';
$allowed_array[] = '272 Maine street';
	$query = $database->query("select address_id from " . TABLE_ADDRESSES_TO_USERS . " where user_id = '2095'");
		while($result = $database->fetch_array($query)) {
			$address_query = $database->query("select house_number, street_name from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "' limit 1");
			$address_result = $database->fetch_array($address_query);
		echo $address_result['house_number'] . ' - ' . $address_result['street_name'] . '<br>';
				if (!in_array($address_result['house_number'] . ' ' . $address_result['street_name'], $allowed_array)) {
					//echo 'not deleting<br><br>';
					//continue;
				}
			$order_query = $database->query("select order_id from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "'");
				while($order_result = $database->fetch_array($order_query)) {
					$equipment_query = $database->query("select equipment_item_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $order_result['order_id'] . "'");
						while($equipment_result = $database->fetch_array($equipment_query)) {
							echo "update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $equipment_result['equipment_item_id'] . "' limit 1". '<br>';
							$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '0' where equipment_item_id = '" . $equipment_result['equipment_item_id'] . "' limit 1");
						}
					$database->query("delete from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $order_result['order_id'] . "'");
					echo "delete from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $order_result['order_id'] . "'". '<br>';
				}
			$database->query("delete from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "'");
			echo "delete from " . TABLE_ORDERS . " where address_id = '" . $result['address_id'] . "'". '<br>';
			$database->query("delete from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "'");
			echo "delete from " . TABLE_ADDRESSES . " where address_id = '" . $result['address_id'] . "'". '<br>';
			$database->query("delete from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . $result['address_id'] . "'");
			echo "delete from " . TABLE_ADDRESSES_TO_USERS . " where address_id = '" . $result['address_id'] . "'". '<br>';
		}
	//$database->query("delete from " . TABLE_ADDRESSES_TO_USERS . " where user_id = '2095'");
	//echo "delete from " . TABLE_ADDRESSES_TO_USERS . " where user_id = '2095'". '<br>';
die();*/
	$page_action = tep_fill_variable('page_action', 'get');
	$uID = tep_fill_variable('uID', 'get');
	$submit_value = tep_fill_variable('submit_value_y', 'post');
	$page = tep_fill_variable('page', 'post', '1');
	$page_get = tep_fill_variable('page', 'get', '');
	
	$show_user_group_id = tep_fill_variable('show_user_group_id', 'get');
	$search_name = tep_fill_variable('search_name', 'get');
	$show_agency_id = tep_fill_variable('show_agency_id', 'get', '');
	$start_letter = tep_fill_variable('start_letter', 'get', '');
	$show_user = tep_fill_variable('show_user', 'get', '');
	
	$message = '';
	$pages = tep_fill_variable('pages', 'post', array());
		if ($page_action == 'update') {

			
			$install_preference = tep_fill_variable('install_preference');
			$service_call_preference = tep_fill_variable('service_call_preference');
			$removal_preference = tep_fill_variable('removal_preference');
			
			$query = $database->query("select agent_preference_id from " . TABLE_AGENT_PREFERENCES . " where user_id = '" . $uID . "' limit 1");
			$result = $database->fetch_array($query);
				if (!empty($result['agent_preference_id'])) {
					$database->query("update " . TABLE_AGENT_PREFERENCES . " set install_preference = '" . $install_preference . "', service_call_preference = '" . $service_call_preference . "', removal_preference = '" . $removal_preference . "' where agent_preference_id = '" . $result['agent_preference_id'] . "' limit 1");
				} else {
					$database->query("insert into ". TABLE_AGENT_PREFERENCES . " (user_id, install_preference, service_call_preference, removal_preference) values ('" . $uID . "', '" . $install_preference . "', '" . $service_call_preference . "', '" . $removal_preference . "')");
				}
			$uID = '';
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_users')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_users'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if (empty($uID)) {
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td width="25%" class="pageBoxHeading">Agent Name</td>
						<td width="25%" class="pageBoxHeading" align="center">Service Level</td>
						<td width="25%" class="pageBoxHeading" align="center" NOWRAP>Assigned Preferences</td>
						<td width="25%" class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$uData = array();
					$show_user_string = '';
						if (!empty($show_user) && ($show_user != 'any')) {
							if ($show_user == 'with') {
								$show_user_string = " and (ap.install_preference != '' or ap.service_call_preference != '' or ap.removal_preference != '')";
							} else {
								$show_user_string = " and ((ap.install_preference IS NULL or ap.install_preference = '') and (ap.service_call_preference IS NULL or ap.service_call_preference = '') and (ap.removal_preference IS NULL or ap.removal_preference = ''))";
							}
						}
                // Issue on Group By
                //$listing_split = new split_page("select u.user_id, u.email_address, ud.firstname, ud.lastname, ap.install_preference, ap.service_call_preference, ap.removal_preference, sld.name from " . TABLE_USERS . " u left join " . TABLE_AGENT_PREFERENCES . " ap on (u.user_id = ap.user_id), " . TABLE_AGENCYS . " a, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug, " . TABLE_SERVICE_LEVELS . " sld where u.user_id = ud.user_id and u.agency_id = a.agency_id and u.user_id = utug.user_id and utug.user_group_id = '1'  and u.users_status=1 and ".((!empty($search_name)) ? ("((ud.firstname like '" . $search_name . "%' or ud.firstname = '" . $search_name . "') or (ud.lastname = '" . $search_name . "' or ud.lastname like '" . $search_name . "%')) and ") : '')."utug.user_group_id = utug.user_group_id" . ((!empty($show_agency_id)) ? " and (a.agency_id = '" . $show_agency_id . "' or a.parent_agency_id = '" . $show_agency_id . "')" : '') . ((!empty($start_letter)) ? " and ud.firstname like '".$start_letter."%'" : '') . $show_user_string . " and u.service_level_id = sld.service_level_id group by u.user_id. ud.firstname order by ud.lastname", '20', 'u.user_id');
                $listing_split = new split_page("select u.user_id, u.email_address, ud.firstname, ud.lastname, ap.install_preference, ap.service_call_preference, ap.removal_preference, sld.name from " . TABLE_USERS . " u left join " . TABLE_AGENT_PREFERENCES . " ap on (u.user_id = ap.user_id), " . TABLE_AGENCYS . " a, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug, " . TABLE_SERVICE_LEVELS . " sld where u.user_id = ud.user_id and u.agency_id = a.agency_id and u.user_id = utug.user_id and utug.user_group_id = '1'  and u.users_status=1 and ".((!empty($search_name)) ? ("((ud.firstname like '" . $search_name . "%' or ud.firstname = '" . $search_name . "') or (ud.lastname = '" . $search_name . "' or ud.lastname like '" . $search_name . "%')) and ") : '')."utug.user_group_id = utug.user_group_id" . ((!empty($show_agency_id)) ? " and (a.agency_id = '" . $show_agency_id . "' or a.parent_agency_id = '" . $show_agency_id . "')" : '') . ((!empty($start_letter)) ? " and ud.firstname like '".$start_letter."%'" : '') . $show_user_string . " and u.service_level_id = sld.service_level_id group by u.user_id, ud.firstname, ud.lastname, ap.install_preference, ap.service_call_preference, ap.removal_preference order by ud.lastname", '20', 'u.user_id');

                if ($listing_split->number_of_rows > 0) {
                            $query = $database->query($listing_split->sql_query);
                            foreach($query as $result){
									$result['count'] = 0;
										if (!empty($result['install_preference'])) {
											$result['count'] += 1;
										}
										if (!empty($result['service_call_preference'])) {
											$result['count'] += 1;
										}
										if (!empty($result['removal_preference'])) {
											$result['count'] += 1;
										}
										if ($result['user_id'] == $uID) {
											$uData = $result;
										}
									
						?>
							<tr>
								<td width="40%" class="pageBoxContent"><?php echo $result['lastname'].', ',$result['firstname']; ?></td>
								<td width="40%" class="pageBoxContent" align="center"><?php echo $result['name']; ?></td>
								<td width="40%" class="pageBoxContent" align="center"><?php echo $result['count']; ?></td>
								<td width="40%" class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_USER_PREFERENCES . '?uID='.$result['user_id'].'&page_action=view&page='.$page.'&search_name='.$search_name.'&show_user_group_id='.$show_user_group_id.'&show_agency_id='.$show_agency_id.'&start_letter='.$start_letter.'&show_user='.$show_user; ?>">View/Edit</a></td>
								<td width="10" class="pageBoxContent"></td>
							</tr>
						<?php
								}
							?>
							<tr>
								<td colspan="8">
									<table class="normaltable" cellspacing="0" cellpadding="2">
										<tr>
											<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> users)'); ?></td>
											<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(10, tep_get_all_get_params(array('page', 'info', 'x', 'y', 'page_action', 'uID'))); ?></td>
										</tr>
									</table>
								</td>
							</tr>
							<?php
						}
					?>
				</table>
			<?php
				} else {
				?>
				<table width="100%" cellspacing=-"0" cellpadding="0">
					<tr>
					<form action="<?php echo FILENAME_ADMIN_USER_PREFERENCES; ?>?uID=<?php echo $uID; ?>&page_action=update&page=<?php echo $page.'&search_name='.$search_name.'&show_user_group_id='.$show_user_group_id.'&show_agency_id='.$show_agency_id.'&start_letter='.$start_letter.'&show_user='.$show_user; ?>" method="post">
						<td width="100%" align="left">
							<table width="100%" cellspacing="0" cellpadding="0">
								<?php
									$query = $database->query("select agent_preference_id, install_preference, service_call_preference, removal_preference from " . TABLE_AGENT_PREFERENCES . " where user_id = '" . $uID . "'");
									$result = $database->fetch_array($query);
										if (empty($result['agent_preference_id'])) {
											$result['install_preference'] = '';
											$result['service_call_preference'] = '';
											$result['removal_preference'] = '';
										}
								?>
											<tr>
												<td width"100%" align="left">
													<table cellspacing="0" cellpadding="0">
														<tr>
															<td class="main" width="200">Installation Preferences: </td>
															<td class="main" width="300"><textarea style="width: 350px; height:200px;" name="install_preference"><?php echo $result['install_preference']; ?></textarea></td>
														</tr>
														<tr>
															<td class="main" width="200">Service Call Preferences: </td>
															<td class="main" width="300"><textarea style="width: 350px; height:200px;" name="service_call_preference"><?php echo $result['service_call_preference']; ?></textarea></td>
														</tr>
														<tr>
															<td class="main" width="200">Removal Preferences: </td>
															<td class="main" width="300"><textarea style="width: 350px; height:200px;" name="removal_preference"><?php echo $result['removal_preference']; ?></textarea></td>
														</tr>
													</table>
												</td>
											</tr>

								
							</table>
						</td>
					</tr>
					
				</table>
				<?php
//Show page
				}
			?>
		</td>
		
		<?php
			if (!empty($uID)) {
		?>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press "Update" to save the changes or press "Cancel" to return to the previous page.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
										<?php if ($page_get == '') {		
											$cancel_link = 'admin_users.php?uID='.$uID.'&page_action=edit';		
										} else {		
											$cancel_link = FILENAME_ADMIN_USER_PREFERENCES.'?page='.$page.'&search_name='.$search_name.'&show_user_group_id='.$show_user_group_id.'&show_agency_id='.$show_agency_id.'&start_letter='.$start_letter.'&show_user='.$show_user;		
										} ?>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo $cancel_link; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<?php
			} else {
		?>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<form action="<?php echo FILENAME_ADMIN_USER_PREFERENCES; ?>" method="get">
								<tr>
									<td class="main">Show only Agency: <?php echo tep_draw_agency_pulldown('show_agency_id', $show_agency_id, '', array(array('id' => '', 'name' => 'Any'))); ?></td>
								</tr>
								<tr>
									<td class="main">Show users with name like: <input type="text" name="search_name" value="<?php echo $search_name; ?>" /></td>
								</tr>
								<tr>
									<td class="main">Show Users starting with <select name="start_letter"><?php
										$query = $database->query("select LEFT(ud.firstname, 1) as letter from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud , " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id " . ((!empty($show_user_group_id)) ? " and utug.user_group_id = '" . $show_user_group_id . "'" : '') . ((!empty($show_agency_id)) ? " and u.agency_id = '" . $show_agency_id . "'" : '') . " group by letter order by letter");
										echo '<option value="">Any</option>';
											while($result = $database->fetch_array($query)) {
													if (empty($result['letter'])) {
														continue;
													}
													if ($start_letter == strtolower($result['letter'])) {
														$selected = ' SELECTED';
													} else {
														$selected = '';
													}
												echo '<option value="'.strtolower($result['letter']).'"' . $selected . '>'.strtoupper($result['letter']).'</option>';
											}
									?></select></td>
								</tr>
								<tr>
									<td class="main">Show Users <select name="show_user">
										<option value="any"<?php echo (($show_user == 'any' ) ? ' SELECTED' : ''); ?>>With or Without Preferences</option>
										<option value="with"<?php echo (($show_user == 'with' ) ? ' SELECTED' : ''); ?>>With Preferences</option>
										<option value="without"<?php echo (($show_user == 'without' ) ? ' SELECTED' : ''); ?>>Without Preferences</option>
									</select></td>
								</tr>
								<tr>
									<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
								</tr>
								<tr>
									<td width="100%" align="right"><input type="submit" value="Search" /></td>
								</tr>
								</form>
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