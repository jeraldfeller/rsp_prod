<?php
	/*
	echo 'time: ' . strtotime("January 26, 2009, 4:14 pm") . '<br>';
	echo 'new: ' . date("F j, Y, g:i a" , 1233007449) . '<br>';
	echo 'old: ' . date("F j, Y, g:i a" , 1232995796) . '<br>';
	
	echo 'working<br>';
	echo 'same: ' . date("F j, Y, g:i a" , 1232725312) . '<br>';
	echo 'new next: ' .date("F j, Y, g:i a" , 1233006827) . '<br>';
	echo 'old next: ' .date("F j, Y, g:i a" , 1232995796) . '<br>';
	echo 'old next: ' .date("F j, Y, g:i a" , 1233007449) . '<br>';
	die();*/
	$page_action = tep_fill_variable('page_action', 'get');
	
	$ajaxAction = tep_fill_variable('ajaxAction', 'post'); #added by Mukesh
	
	$aID = tep_fill_variable('aID', 'get');
	$user_group_id = tep_fill_variable('user_group_id', 'get');
	$search_name = tep_fill_variable('search_name', 'get');
	$search_billing_method_id = tep_fill_variable('search_billing_method_id', 'get');
	$submit_value = tep_fill_variable('submit_value_y', 'post');
	$start_letter = tep_fill_variable('start_letter', 'get', '');
	$page = tep_fill_variable('page', 'get', '');
	$search_status = tep_fill_variable('search_status', 'get', '0');
	
	$view = tep_fill_variable('view', 'get', '');

	$message = '';
	$pages = tep_fill_variable('pages', 'post', array());
	
	#Start Added By Mukesh 
	if(isset($ajaxAction) && !empty($ajaxAction) ){
		#echo 'I am here :: '.$ajaxAction.'<br>';
		#echo '<pre>'; #print_r($_POST); 
		
		if($ajaxAction == 'get-state-county'){
			
			$state_id = $_POST['state_id'];
			
			$query = $database->query("select county_id, name as county_name from " . TABLE_COUNTYS . " where state_id = '" . $state_id . "' order by name");
			$data[] = 'Please Select';
			foreach($query as $result){
				
				$data[$result['county_id']] = $result['county_name'];
				
			}		
			echo json_encode(  array('status'=>'success','data'=>$data)); die;
	
			#print_r($array);die;
		} 
	}
	#End Added By Mukesh
	
	
	
	if (!empty($submit_value)) {
		$name = tep_fill_variable('name', 'post');
		$office = tep_fill_variable('office', 'post');
		$contact_name = tep_fill_variable('contact_name', 'post');
		$contact_phone = tep_fill_variable('contact_phone', 'post');
		$service_level_id = tep_fill_variable('service_level_id', 'post');
		$billing_method_id = tep_fill_variable('billing_method_id', 'post');
		$parent_agency_id = tep_fill_variable('agency_parent_id', 'post');
		$discount_type = tep_fill_variable('discount_type', 'post');
		$discount_amount = tep_fill_variable('discount_amount', 'post');
		$address = tep_fill_variable('address', 'post');
		$auto_remove_period = tep_fill_variable('auto_remove_period', 'post');
		
		#Start Added By Mukesh
		
		$addr_street = tep_fill_variable('addr_street', 'post');
		$addr_city = tep_fill_variable('addr_city', 'post');
		$addr_state = tep_fill_variable('addr_state', 'post');
		$addr_county = tep_fill_variable('addr_county', 'post');
		$addr_zip = tep_fill_variable('addr_zip', 'post');
		
		#End Added By Mukesh
		
		
		for ($i = 1; $i <= 4; $i++) {
			foreach(array("email" . $i, "use_email" . $i) as $var) {
				$$var = tep_fill_variable($var, 'post');
			}
		}
		
			if ($discount_type > 0) {
				if (substr($discount_type, 0, 1) == '+') {
					$discount_type = substr($discount_type, 1);
				}
			}

			$auto_remove_period = (int) $auto_remove_period;
			if (empty($auto_remove_period) || $auto_remove_period <= 0 || $auto_remove_period == AUTOMATIC_REMOVAL_TIME) {
				$auto_remove_period = "NULL";
			} 
		
			
			if (!$error->get_error_status('admin_agencys')) {
					#echo 'I am here <pre>'; print_r($_POST); die;
					if ($page_action == 'edit') {
						
						$database->query("update " . TABLE_AGENCYS . " set name = '" . $name . "', office = '" . $office . "', service_level_id = '" . $service_level_id . "', billing_method_id = '" . $billing_method_id . "', address = '" . addslashes($address) . "', contact_name = '" . $contact_name . "', contact_phone = '" . $contact_phone . "', parent_agency_id = '" . $parent_agency_id . "', discount_type = '" . $discount_type . "', discount_amount = '" . $discount_amount . "', auto_remove_period = " . $auto_remove_period . ", addr_street='".$addr_street."',addr_city='".$addr_city."', addr_state='".$addr_state."', addr_county='".$addr_county."',addr_zip='".$addr_zip."' where agency_id = '" . $aID . "' limit 1");
						
						$database->query("delete from " . TABLE_EMAILS_TO_AGENCYS . " where agency_id = '" . $aID . "' limit 4");
						
						for ($i = 1; $i <= 4; $i++) {
							$email = ${"email$i"};
							$use_email = ${"use_email$i"};
							if (!empty($email)) {
								$database->query("INSERT INTO " . TABLE_EMAILS_TO_AGENCYS . " (agency_id, email_address, email_status) VALUES ('" . $aID . "', '" . $email . "', '" . $use_email . "')");
							}
						}
						
						$message = 'Successfully Updated';
					} else {
						
						$database->query("insert into " . TABLE_AGENCYS . " (name, office, service_level_id, billing_method_id, address, contact_name, contact_phone, parent_agency_id, discount_type, discount_amount, agency_status_id, auto_remove_period, addr_street, addr_city, addr_state, addr_county, addr_zip ) values ('" . $name . "', '" . $office . "', '" . $service_level_id . "', '" . $billing_method_id . "', '" . addslashes($address) . "', '" . $contact_name . "', '" . $contact_phone . "', '" . $parent_agency_id . "', '" . $discount_type . "', '" . $discount_amount . "', '1', " . $auto_remove_period .",'".$addr_street."' ,'".$addr_city."','".$addr_state."','".$addr_county."','".$addr_zip."' )");
						
						
						
						$aID = $database->insert_id();
						
						for ($i = 1; $i <= 4; $i++) {
							$email = ${"email$i"};
							$use_email = ${"use_email$i"};
							if (!empty($email)) {
								$database->query("INSERT INTO " . TABLE_EMAILS_TO_AGENCYS . " (agency_id, email_address, email_status) VALUES ('" . $aID . "', '" . $email . "', '" . $use_email . "')");
							}
						}
					
						
						$message = 'Successfully Inserted new Agency';
					}
				$page_action = '';
				$aID = '';
			}
		
	}
	
	if ($page_action == 'delete_confirm') {
			$merge_agency_id = tep_fill_variable('merge_agency_id');
			
			$database->query("delete from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "' limit 1");
			$database->query("delete from " . TABLE_EMAILS_TO_AGENCYS . " where agency_id = '" . $aID . "' limit 4");
			//echo"delete from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "' limit 1" . '<br>';
				if (!empty($merge_agency_id)) {
					$database->query("update " . TABLE_USERS . " set agency_id = '" . $merge_agency_id . "' where agency_id = '" . $aID . "'");
					//echo "update " . TABLE_USERS . " set agency_id = '" . $merge_agency_id . "' where agency_id = '" . $aID . "'" . '<br>';
				}
			$page_action = '';
			$aID = '';
		} elseif ($page_action == 'order_hold') {
			$database->query("update " . TABLE_AGENCYS . " set order_hold = '" . tep_fill_variable('status', 'get') . "' where agency_id = '" . $aID . "' limit 1");
			$page_action = '';
			$uID = '';
			$aID = '';
			$message = "Agency status changed successfully";
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_agencys')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_agencys'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if (($page_action != 'edit')&&($page_action != 'add') && ($page_action != 'inactive') && ($page_action != 'order_hold_inactive') && ($page_action != 'delete_inactive') && ($page_action != 'delete_inactive_confirm')) {
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td width="20%" class="pageBoxHeading">Agency Name</td>
						<td width="20%" class="pageBoxHeading" align="center">Agents</td>
						<td width="20%" class="pageBoxHeading" align="center">True Agency</td>
						<td width="20%" class="pageBoxHeading" align="center">Agency Status</td>
						<td width="20%" class="pageBoxHeading" align="center">AOM's</td>
						<td width="20%" class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$uData = array();
					$listing_split = new split_page("select agency_id, name, office, parent_agency_id, order_hold from " . TABLE_AGENCYS . " where agency_status_id = '1' " . (($view == 'all') ? '': " and parent_agency_id = ''") . ((!empty($start_letter)) ? " and name like '".$start_letter."%'" : '') . ((!empty($search_name)) ? " and (name like '%" .$search_name."%' or name like '".$search_name."%' or name = '" . $search_name . "') " : '') . (($search_status != '') ? " and order_hold = '" . (int) $search_status . "'" : '') . ((!empty($search_billing_method_id)) ? " and billing_method_id = '" . (int) $search_billing_method_id . "'" : '') . " order by name, office", '20', 'agency_id');
					$query = $database->query($listing_split->sql_query);
					    foreach($query as $result){
							$count_query = $database->query("select count(u.user_id) as count from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.active_status = '1'");
							$count_result = $database->fetch_array($count_query);
							$total_agents = $count_result['count'];
							$active_agents = $count_result['count'];
							
							$count_query = $database->query("select count(u.user_id) as count from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.active_status = '0'");
							$count_result = $database->fetch_array($count_query);
							$total_agents += $count_result['count'];
							$inactive_agents = $count_result['count'];
								if ($result['agency_id'] == $aID) {
									$uData = $result;
									$uData['count'] = $total_agents;
								}
						
						
				?>
					<tr>
						<td width="20%" class="pageBoxContent"><?php echo $result['name'] . ((!empty($result['office'])) ? (' (' . $result['office'] . ')') : ''); ?></td>
						<td width="20%" class="pageBoxContent" align="center"><?php echo $active_agents.' Active ('.$total_agents.' Total)'; ?></td>
						<td width="20%" class="pageBoxContent" align="center"><?php echo (($result['parent_agency_id'] == '0') ? 'Yes' : 'No'); ?></td>
						<td width="20%" class="pageBoxContent" align="center"><?php if ($result['order_hold'] == '0') { ?><img src="images/icon_status_green.gif" height="10" width="10" border="0" />&nbsp;&nbsp;<a href="<?php echo FILENAME_ADMIN_AGENCYS . '?aID='.$result['agency_id'].'&page_action=order_hold&status=1&'. tep_get_all_get_params(array('page_action', 'action', 'aID')); ?>"><img src="images/icon_status_red_light.gif" height="10" width="10" border="0" /></a><?php } else { ?><a href="<?php echo FILENAME_ADMIN_AGENCYS . '?aID='.$result['agency_id'].'&page_action=order_hold&status=0&'. tep_get_all_get_params(array('page_action', 'action', 'aID', 'status')); ?>"><img src="images/icon_status_green_light.gif" height="10" width="10" border="0" /></a>&nbsp;&nbsp;<img src="images/icon_status_red.gif" height="10" width="10" border="0" /><?php } ?></td>
						<td width="20%" class="pageBoxContent" align="center"><?php echo tep_count_agency_order_managers($result['agency_id']); ?></td>
						<td width="20%" class="pageBoxContent" align="right" NOWRAP>&nbsp;&nbsp;<a href="<?php echo FILENAME_ADMIN_USERS . '?user_group_id=1&show_agency_id='.$result['agency_id']; ?>">View Agents</a>&nbsp;|&nbsp;<a href="<?php echo FILENAME_ADMIN_AGENCYS . '?aID='.$result['agency_id'].'&page_action=edit&search_status='.$search_status.'&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_ORDERS . '?order_status=&agency_id='.$result['agency_id']; ?>">Orders</a>&nbsp;|&nbsp;<a href="<?php echo FILENAME_ADMIN_AGENCYS . '?aID='.$result['agency_id'].'&page_action=delete&search_status='.$search_status.'&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">Delete</a><br /><a href="<?php echo FILENAME_ADMIN_NEW_USERS . '?user_group_id=1&show_agency_id='.$result['agency_id']; ?>">View New Agents</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
					?>
					<tr>
						<td colspan="5">
							<table class="normaltable" cellspacing="0" cellpadding="2">
								<tr>
									<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> agencies)'); ?></td>
									<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			<?php
				} else if ($page_action == 'inactive' || $page_action == 'delete_inactive' || $page_action == 'order_hold_inactive' || $page_action == 'delete_inactive_confirm') {
					
					if($page_action == 'delete_inactive_confirm') {
						$merge_agency_id = tep_fill_variable('merge_agency_id');
			
						$database->query("delete from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "' limit 1");
						$database->query("delete from " . TABLE_EMAILS_TO_AGENCYS . " where agency_id = '" . $aID . "' limit 4");
						//echo"delete from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "' limit 1" . '<br>';
							if (!empty($merge_agency_id)) {
								$database->query("update " . TABLE_USERS . " set agency_id = '" . $merge_agency_id . "' where agency_id = '" . $aID . "'");
								//echo "update " . TABLE_USERS . " set agency_id = '" . $merge_agency_id . "' where agency_id = '" . $aID . "'" . '<br>';
							}
						$page_action = '';
						$aID = '';
					}
					
					if ($page_action == 'order_hold_inactive') {
						$database->query("update " . TABLE_AGENCYS . " set order_hold = '" . tep_fill_variable('status', 'get') . "' where agency_id = '" . $aID . "' limit 1");
						$page_action = '';
						$uID = '';
						$aID = '';
						$message = "Agency status changed successfully";
					}
					
					$last30 = strtotime("-".INACTIVE_MONTHS." months");
					
					$inactive_agents_query = $database->query("select DISTINCT u.user_id, max(o.date_added) as mxorder, u.last_login from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join orders o on (o.user_id=u.user_id) left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and u.active_status=1 and ug.user_group_id=1 and u.last_login<=".$last30." GROUP BY u.user_id HAVING max(o.date_added)<=".$last30." order by u.last_login");
					$fully_inactive_agents = array();
					while($result = $database->fetch_array($inactive_agents_query)) {
						$fully_inactive_agents[] = $result['user_id'];
					}
					
					
					 ?>
					<h4>Agencies to be made Inactive</h4>
					<table id="myTable" width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<thead>
						<th width="20%" class="pageBoxHeading">Agency Name</th>
						<th width="20%" class="pageBoxHeading" align="center">Agents</th>
						<th width="20%" class="pageBoxHeading" align="center">Agency Status</th>
						<th width="20%" class="pageBoxHeading" align="center">AOM's</th>
						<th width="20%" class="pageBoxHeading" align="center">Last Agent Login Date</th>

						<th width="90" class="pageBoxHeading" align="right">Action</th>
						<th width="10" class="pageBoxHeading"></th>
					</thead>
				<tbody>
				<?php
					$uData = array();
					$listing_split = new split_page("select agency_id, name, office, parent_agency_id, order_hold from " . TABLE_AGENCYS . " where order_hold = '0' order by name, office", '1000', 'agency_id');
					$z=0;
						if ($listing_split->number_of_rows > 0) {
							$query = $database->query($listing_split->sql_query);
							    foreach($query as $result){
									$z++;
									//check for active agents
									$count_query = $database->query("select count(u.user_id) as count from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.active_status = '1'");
									$count_result = $database->fetch_array($count_query);
									$total_agents = $count_result['count'];
									$active_agents = $count_result['count'];
									
									//check for inactive agents
									$count_query = $database->query("select count(u.user_id) as count from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.active_status = '0'");
									$count_result = $database->fetch_array($count_query);
									$total_agents += $count_result['count'];
									$inactive_agents = $count_result['count'];
									
									
									$count_current_agents_query = $database->query("select u.user_id from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.active_status = '1'");
									
									$all_inactive = true;
									foreach($count_current_agents_query as $result_current_agents){
										if(!in_array($result_current_agents['user_id'], $fully_inactive_agents)) {
											$all_inactive = false;
											continue;
										}
									}
									
									
									/*if($all_inactive) {
										$count_current_agents_query = $database->query("select u.user_id from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.active_status = '1'");
									}*/
									
									//$a_users = 
									
									if ( ($active_agents == 0) || ($total_agents == $inactive_agents) || ($all_inactive) ) {
										
										$misc_query = $database->query("select max(u.last_login) as maxlogin from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "')");

										foreach($misc_query as $result_misc){
											$result['maxlogin'] = $result_misc['maxlogin'];
										}
										
										if ($result['maxlogin'] == '' || $result['maxlogin'] == 0) {
											$result['maxlogin'] = 'Never';
										} else {
											$result['maxlogin'] = '<span style="display:none">'.$result['maxlogin'].'</span>'.date('m/d/Y', $result['maxlogin']);
										}
										
										if ($result['agency_id'] == $aID) {
											$uData = $result;
											$uData['count'] = $total_agents;
										}
										
										//if everyone is inactive
						?>
						
							<tr>
								<td width="20%" class="pageBoxContent"><?php echo $result['name']; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php echo $active_agents.' Active ('.$total_agents.' Total)'; ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php if ($result['order_hold'] == '0') { ?><img src="images/icon_status_green.gif" height="10" width="10" border="0" />&nbsp;&nbsp;<a href="<?php echo FILENAME_ADMIN_AGENCYS . '?aID='.$result['agency_id'].'&page_action=order_hold_inactive&status=1&'. tep_get_all_get_params(array('page_action', 'action', 'aID')); ?>"><img src="images/icon_status_red_light.gif" height="10" width="10" border="0" /></a><?php } else { ?><a href="<?php echo FILENAME_ADMIN_AGENCYS . '?aID='.$result['agency_id'].'&page_action=order_hold_inactive&status=1&'. tep_get_all_get_params(array('page_action', 'action', 'aID', 'status')); ?>"><img src="images/icon_status_green_light.gif" height="10" width="10" border="0" /></a>&nbsp;&nbsp;<img src="images/icon_status_red.gif" height="10" width="10" border="0" /><?php } ?></td>
								<td width="20%" class="pageBoxContent" align="center"><?php echo tep_count_agency_order_managers($result['agency_id']); ?></td>
								<td width="20%" class="pageBoxContent"><?php echo $result['maxlogin']; ?></td>
								<td width="20%" class="pageBoxContent" align="right" NOWRAP>&nbsp;&nbsp;<a href="<?php echo FILENAME_ADMIN_USERS . '?user_group_id=1&show_agency_id='.$result['agency_id']; ?>">View Agents</a>&nbsp;|&nbsp;<a href="<?php echo FILENAME_ADMIN_AGENCYS . '?aID='.$result['agency_id'].'&page_action=edit&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_ORDERS . '?order_status=&agency_id='.$result['agency_id']; ?>">Orders</a>&nbsp;|&nbsp;<a href="<?php echo FILENAME_ADMIN_AGENCYS . '?aID='.$result['agency_id'].'&page_action=delete_inactive&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">Delete</a></td>
								<td width="10" class="pageBoxContent"></td>
							</tr>
							
						<?php
									}
								}
								
							?>
							</tbody>

						<?php
							}
						?>
				</table>
						    <script>
							$(document).ready(function(){
								$('#myTable').DataTable( {
									"aaSorting": [[ 4, "asc" ]],
									"iDisplayLength": 25,
									"sPaginationType": "full_numbers"
								});
							});
							</script>
				<?
				} else {
					if (!empty($aID)) {
						//Edit
						$agency_query = $database->query("select name, office, service_level_id, billing_method_id, address, contact_name, contact_phone, parent_agency_id, discount_type, discount_amount, auto_remove_period, addr_street,addr_city,addr_state,addr_county,addr_zip  from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "' limit 1");
						
                        $agency_result = $database->fetch_array($agency_query);

                        $agency_email_query = $database->query("SELECT email_address, email_status FROM " . TABLE_EMAILS_TO_AGENCYS . " WHERE agency_id = '" . $aID . "' limit 4");
                        for ($i = 1; $i <= 4; $i++) {
                            foreach(array("email" . $i, "use_email" . $i, "email" . $i . "checked") as $var) {
                                $$var = "";
                            }
                        }
                        $i = 1;
                        foreach($agency_email_query as $agency_email_result){
                            $var = "email" . $i;
                            $$var = $agency_email_result["email_address"];
                            $var = "use_email" . $i;
                            $$var = $agency_email_result["email_status"];
                            if($$var) {
                                $var = "email" . $i . "checked";
                                $$var = " checked";
                            }
                            $i++;
                        }
					} else {
						//Add
						
						
						
						
						$agency_result = array('name' => '', 'office' => '', 'service_level_id' => '', 'billing_method_id' => '', 'address' => '', 'contact_name' => '', 'contact_phone' => '', 'parent_agency_id' => '', 'discount_type' => '', 'discount_amount' => '', 'auto_remove_period' => '', 'addr_street'=>'', 'addr_city'=>'', 'addr_state'=>'', 'addr_county'=>'', 'addr_zip'=>'');
						
                        for ($i = 1; $i <= 4; $i++) {
                            foreach(array("email" . $i, "use_email" . $i, "email" . $i . "checked") as $var) {
                                $$var = "";
                            }
                        }
					}
				
			?>
			<?php
				if($page_action=='edit') {
                // Need to use raw SQL instead of account class, since account class assumes a user
                $query = $database->query("SELECT running_total FROM " . TABLE_ACCOUNTS . " WHERE user_id = '0' AND agency_id = '{$aID}' LIMIT 1");
                if ($result = $database->fetch_array($query)) {
                    $agency_available_credit = $result['running_total'];
                } else {
                    $agency_available_credit = 0;
                }
                if ($agency_available_credit < 0) {
                    $agency_available_credit = 0;
                }
                $agency_available_credit = number_format($agency_available_credit, 2);
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_AGENCYS . '?page_action=edit&aID='.$aID.'&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">
			<?php
				} else {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_AGENCYS . '?page_action=add'.'&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>">
			<?php
				}
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxContent" width="120">Agency Name</td><td class="pageBoxContent"><textarea name="name"><?php echo $agency_result['name']; ?></textarea></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Office Name</td><td class="pageBoxContent"><input type="text" name="office" value="<?php echo $agency_result['office']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Agency Contact Name</td><td class="pageBoxContent"><input type="text" name="contact_name" value="<?php echo $agency_result['contact_name']; ?>" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Agency Contact Phone</td><td class="pageBoxContent"><input type="text" name="contact_phone" value="<?php echo $agency_result['contact_phone']; ?>" /></td>
					</tr>
                    
					<tr>
						<td class="pageBoxContent" valign="top" width="120">Agency Address</td><td class="pageBoxContent"><textarea name="address"><?php echo $agency_result['address']; ?></textarea></td>
					</tr>
                    
                    <tr>
						<td class="pageBoxContent" valign="top" width="120">Street Address</td>
                        <td class="pageBoxContent"><input type="text" name="addr_street" value="<?php echo $agency_result['addr_street']; ?>" /></td>
					</tr>
                    <!-- Start Changes by Mukesh-->
                    <tr>
						<td class="pageBoxContent" valign="top" width="120">City</td>
                        <td class="pageBoxContent"><input type="text" name="addr_city" value="<?php echo $agency_result['addr_city']; ?>" /></td>
					</tr>
                    <tr>
						<td class="pageBoxContent" valign="top" width="120">State</td>
                        <td class="pageBoxContent">
                        	<?php echo tep_draw_state_pulldown('addr_state', tep_fill_variable('state_id', 'post', $agency_result['addr_state']), ''); ?>
                        </td>
					</tr>
                    <tr>
						<td class="pageBoxContent" valign="top" width="120">County</td>
                        <td class="pageBoxContent">
                        	<?php echo tep_draw_county_pulldown('addr_county', tep_fill_variable('state_id', 'post', $agency_result['addr_state']), $agency_result['addr_county']); ?>
                        </td>
					</tr>
                    <tr>
						<td class="pageBoxContent" valign="top" width="120">Post Code</td>
                        <td class="pageBoxContent"><input type="text" name="addr_zip" value="<?php echo $agency_result['addr_zip']; ?>" /></td>
					</tr>
                    <!-- End Changes by Mukesh-->
					<tr>
                        <td class="pageBoxContent" valign="top" width="120">Invoice Emails</td>
                        <td class="pageBoxContent">
                            <input type="text" name="email1" value="<?php echo $email1; ?>" />
                            <input type="checkbox" value="1" name="use_email1"<?php echo $email1checked; ?> /> Receive invoices on this email
                        </td>
					</tr>
					<tr>
                        <td class="pageBoxContent" valign="top" width="120">&nbsp;</td>
                        <td class="pageBoxContent">
                            <input type="text" name="email2" value="<?php echo $email2; ?>" />
                            <input type="checkbox" value="1" name="use_email2"<?php echo $email2checked; ?> /> Receive invoices on this email
                        </td>
					</tr>
					<tr>
                        <td class="pageBoxContent" valign="top" width="120">&nbsp;</td>
                        <td class="pageBoxContent">
                            <input type="text" name="email3" value="<?php echo $email3; ?>" />
                            <input type="checkbox" value="1" name="use_email3"<?php echo $email3checked; ?> /> Receive invoices on this email
                        </td>
					</tr>
					<tr>
                        <td class="pageBoxContent" valign="top" width="120">&nbsp;</td>
                        <td class="pageBoxContent">
                            <input type="text" name="email4" value="<?php echo $email4; ?>" />
                            <input type="checkbox" value="1" name="use_email4"<?php echo $email4checked; ?> /> Receive invoices on this email
                        </td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Default Billing Method</td><td class="pageBoxContent"><?php echo tep_draw_billing_method_pulldown('billing_method_id', $agency_result['billing_method_id'], '', false); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Default Service Level</td><td class="pageBoxContent"><?php echo tep_draw_service_level_pulldown('service_level_id', $agency_result['service_level_id'], '', false); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Adjustment Type</td><td class="pageBoxContent"><?php echo tep_generate_discount_pulldown_menu('discount_type', $agency_result['discount_type']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">Adjustment Amount <br />(+ or - to add for amount or percent (under 100) for percent)</td><td valign="top" class="pageBoxContent"><input type="text" name="discount_amount" value="<?php echo $agency_result['discount_amount']; ?>" /></td>
					</tr>
					<?php 
					if($page_action=="edit")
					{
					?>
                    <tr>
                        <td class="pageBoxContent">Agency Credit: </td><td class="pageBoxContent">$<?php echo $agency_available_credit; ?> (Agency Invoice Orders)</td>
                    </tr>
                    <?php 
					}
                    ?>
					<tr>
                    <td class="pageBoxContent" width="120">Auto Remove Period</td><td valign="top" class="pageBoxContent"><input type="text" name="auto_remove_period" value="<?php echo !empty($agency_result['auto_remove_period']) ? $agency_result['auto_remove_period'] : AUTOMATIC_REMOVAL_TIME;?>" /></td>
					</tr>
					<tr>
					<tr>
						<td height="10" width="1"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2"><em>If this Agency is merely an incorrect spelling then select the real one below.</em></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="120">True Agency</td><td class="pageBoxContent"><?php echo tep_draw_agency_pulldown('agency_parent_id', $agency_result['parent_agency_id'], '', array(array('id' => '0', 'name' => 'None'))); ?></td>
					</tr>

				</table>
			<?php
				}
			?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
		<?php
			if (!empty($aID)) {
		?>
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
						if ($page_action == 'edit') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Make your required changes and press "Update" below or press "Cancel" to cancel your changes.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_AGENCYS.'?page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					<?php
						} elseif ($page_action == 'delete' || $page_action == 'delete_inactive') {
							$link = 'delete_inactive_confirm';
							if($page_action == 'delete_inactive') {
								$link = 'delete_inactive_confirm';
							}
					?>
						<form action="<?php echo FILENAME_ADMIN_AGENCYS.'?page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name.'&aID='.$aID.'&page_action='.$link; ?>" method="post">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete "<?php echo $uData['name']; ?>"?  This action can not be undone.  <?php if ($uData['count'] > 0) { ?>As there are currently agents assigned to this agency you must specify another agency to move them to.<?php } ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								if ($uData['count'] > 0) {
							?>
							<tr>
								<td class="main">Merge with: <?php echo tep_draw_agency_pulldown('merge_agency_id', '', '', array(), $aID, true); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('delete', 'Delete'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_AGENCYS.'?page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					<?php
						} elseif (!empty($page_action)) {
							$extra_data_query = $database->query("select name from " . TABLE_AGENCYS . " where agency_id = '" . $aID . "'");
							$extra_data_result = $database->fetch_array($extra_data_query);
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><b>Viewing <?php echo $uData['name']; ?></b></td>
							</tr>
							
							<?php
								if (!empty($uData['parent_agency_id'])) {
									$query = $database->query("select name from " . TABLE_AGENCYS . " where agency_id = '" . $uData['parent_agency_id'] . "' limit 1");
									$result = $database->fetch_array($query);
									
							?>
							<tr>
								<td class="pageBoxContent">Parent Agency: <?php echo $result['name']; ?></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Click Edit below to edit this Agency.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_AGENCYS . '?page_action=edit&aID='.$aID.'&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>"><?php echo tep_create_button_submit('edit', 'Edit'); ?><!--<input type="submit" value="Edit">--></form></td>
											<td align="right"><form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_AGENCYS.'?page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name ; ?>"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?><!--<input type="submit" value="Cancel">--></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					<?php
						}
					?>
					</td>
				</tr>
			</table>
		<?php
			} else {
				if ($page_action == 'inactive' || $page_action == 'delete_inactive_confirm') {
					
				} else if (!empty($page_action)) {
		?>
				<table width="250" cellspacing="0" celpadding="0" class="pageBox">
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
								<td class="pageBoxContent">Insert the details and when you are done press the Create button below or press Cancel to go back to the previous page.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('create', 'Create', ' name="submit_value"'); ?><!--<input type="submit" value="Create" name="submit_value">--></form></td>
											<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_AGENCYS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
								
							</tr>
						</table>
			<?php
				} else {
			?>
			<table width="250" cellspacing="0" celpadding="0" class="pageBox">
			
				<tr>
					<td class="pageBoxHeading"><b>Agency Options</b></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
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
					<td class="pageBoxContent">Click edit to edit an agency.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<form action="<?php echo FILENAME_ADMIN_AGENCYS; ?>" method="get">
						<tr>
							<td class="main"><input type="checkbox" name="view" value="all"<?php echo (($view == 'all') ? ' CHECKED' : ''); ?> />&nbsp;&nbsp;Show All Agencys </td>
						</tr>
						<tr>
							<td class="main">Show Agencies with name like: <input type="text" name="search_name" value="<?php echo $search_name; ?>" /></td>
                        </tr>
						<tr>
                            <td class="main">Show Agencies with Status: <select name="search_status"><option value=""<?php echo (($search_status == '') ? ' SELECTED' : ''); ?>>Any</option><option value="0"<?php echo (($search_status == '0') ? ' SELECTED' : ''); ?>>Active</option><option value="1"<?php echo (($search_status == '1') ? ' SELECTED' : ''); ?>>Inactive</option></select></td>
						</tr>
                        <tr>
                            <td class="main">Show Agencies with Billing Method: <select name="search_billing_method_id">
                                <option value=''>Any</option>
                                <?php
                                $query = $database->query("SELECT billing_method_id, name FROM " . TABLE_BILLING_METHODS);
                                foreach($query as $result){
                                	if($result['billing_method_id']==1)
                                	{
                                		if(!(BILLING_METHOD==null || in_array(BILLING_METHOD,array(1,3))))
                                		{
                                			continue;
                                		}
                                	}
                                	else
                                	{
                                		if(!(BILLING_METHOD==null || in_array(BILLING_METHOD,array(2,3))))
                                		{
                                			continue;
                                		}
                                	}
                                	
                                    if ($search_billing_method_id == $result['billing_method_id']) {
                                        $selected = " selected";
                                    } else {
                                        $selected = "";
                                    }
                                    echo "\t\t\t\t\t\t\t<option value='{$result['billing_method_id']}'{$selected}>{$result['name']}</option>\n";
                                }
                                ?>
                                </select>
                            </td>
						<tr>
							<td class="main">Show Agencies starting with <select name="start_letter"><?php
							$query = $database->query("select LEFT(name, 1) as letter from " . TABLE_AGENCYS . " " . (($view == 'all') ? '': " where parent_agency_id = ''") . " group by letter order by letter");
							echo '<option value="">Any</option>';
							foreach($query as $result){
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
							<td width="100%">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></td>
										<td align="right"><a href="<?php echo FILENAME_ADMIN_AGENCYS . '?page_action=add&page='.$page.'&start_letter='.$start_letter.'&search_name='.$search_name; ?>"><?php echo tep_create_button_link('create', 'Create'); ?></a></td>
									</tr>
								</table>
								
								
							</td>
							
						</tr>
					</form>
				</tr>
			</table>
		<?php
				}
			}
		?>
		</td>
	</tr>
</table>

<script type="text/javascript">
	var action = null;
	var state_id = null;
	var page_url = "<?php echo FILENAME_ADMIN_AGENCYS?>";
	
	$(document).ready(function() {	
			   
		$(document).on("change", "select[name='addr_state']", function () {	
			
			state_id = $(this).val();
			
			$.ajax({
				type: 'POST',
				url: page_url,
				dataType: "json",
				data:{'ajaxAction': 'get-state-county','state_id':state_id},
				cache: false,
				beforeSend:function(){
									
					$("select[name='addr_county']").empty();
					
				},				
				success: function(res){
					//alert(res.status); county_id
					var options = $("select[name='addr_county']");
					$.each(res.data, function(key, value) {
						options.append(new Option(value, key));
					});					

				}
			});				
			
			
		});
		
	})
</script>
