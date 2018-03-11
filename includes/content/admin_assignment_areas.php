<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$aID = tep_fill_variable('aID', 'get', tep_fill_variable('aID', 'post'));
	$submit_value = tep_fill_variable('submit_value_y');
	$page = tep_fill_variable('page', 'get', '1');
	$show_state_id = tep_fill_variable('show_state_id', 'get', tep_fill_variable('show_state_id', 'post', ''));
	$show_service_area_id = tep_fill_variable('show_service_area_id', 'get', tep_fill_variable('show_service_area_id', 'post', ''));
	$show_zip_code = tep_fill_variable('show_zip_code', 'get', tep_fill_variable('show_zip_code', 'post', ''));
	$show_installer_id = tep_fill_variable('show_installer_id', 'get', tep_fill_variable('show_installer_id', 'post', ''));
	$show_warehouse_id = tep_fill_variable('show_warehouse_id', 'get', tep_fill_variable('show_warehouse_id', 'post', ''));

	$message = '';
		if ($page_action == 'edit_confirm') {
		
				if (!empty($submit_value)) {
			
					$name = tep_fill_variable('name');
					$installer_id = tep_fill_variable('installer_id');
					$state_id = tep_fill_variable('state_id');
					$service_area_id = tep_fill_variable('service_area_id');
					$zip_from_new = tep_fill_variable('zip_from_new', 'post', array());
					$zip_to_new = tep_fill_variable('zip_to_new', 'post', array());
					$warehouse_id = tep_fill_variable('warehouse_id');
					
					$installation_coverage_area_id = tep_fill_variable('installation_coverage_area_id');
					$zip_from = tep_fill_variable('zip_from');
					$zip_to = tep_fill_variable('zip_to');
					
					
					$count = count($zip_from_new);
					$n = 0;
					$error_status = false;
						while($n < $count) {
							$from = ((isset($zip_from_new[$n])) ? $zip_from_new[$n] : '');
							$to = ((isset($zip_to_new[$n])) ? $zip_to_new[$n] : '');
							//As we loop these we will check them.
							//If its empty dont check it.
								if (!empty($from) || !empty($to)) {
									if (!tep_zip4_is_valid($from) || !tep_zip4_is_valid($to)) {
										$error_status = true;
									} else {
										//Check if it is taken
											if (tep_zip4_is_assigned_to_area_bgdn($from, $to)) {
												$error_status = true;
											}
									}
								}
							$n++;
						}
					//Now check the existing one.
					$count = count($installation_coverage_area_id);
					$n = 0;
						while($n < $count) {
							$from = ((isset($zip_from[$n])) ? $zip_from[$n] : '');
							$to = ((isset($zip_to[$n])) ? $zip_to[$n] : '');
							//As we loop these we will check them.
							//If its empty dont check it.
								if (!empty($from) || !empty($to)) {
									if (!tep_zip4_is_valid($from) || !tep_zip4_is_valid($to)) {
										$error_status = true;
									} else {
										//Check if it is taken
											if (tep_zip4_is_assigned_to_area_bgdn($from, $to, array($n))) {
												$error_status = true;
											}
									}
								}
							$n++;
						}
						if ($error_status) {
							$page_action= 'edit';
						} else {
							//Insert.
							$database->query("update " . TABLE_INSTALLATION_AREAS . " set name = '" . $name . "', service_area_id = '" . $service_area_id . "', last_modified = '" . time() . "', installer_id = '" . $installer_id . "', state_id = '" . $state_id . "', warehouse_id = '" . $warehouse_id . "' where installation_area_id = '" . $aID . "'");
							//$database->query("delete from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where installation_area_id = '" . $aID . "'");
							
							$installation_area_id = $database->insert_id();
							$count = count($zip_from_new);
							$n = 0;
								while($n < $count) {
									$from = ((isset($zip_from_new[$n])) ? $zip_from_new[$n] : '');
									$to = ((isset($zip_to_new[$n])) ? $zip_to_new[$n] : '');
										if (!empty($from) && !empty($to)) {
											$from_break = tep_break_zip4_code($from);
											$to_break = tep_break_zip4_code($to);
											$database->query("insert into " . TABLE_INSTALLATION_COVERAGE_AREAS . " (installation_area_id, zip_4_first_break_start, zip_4_first_break_end, zip_4_second_break_start, zip_4_second_break_end) values ('" . $aID . "', '" . $from_break[0] . "', '" . $from_break[1] . "', '" . $to_break[0] . "', '" . $to_break[1] . "')");
										}
									$n++;
								}
							$count = count($installation_coverage_area_id);
							$n = 0;
							$error_status = false;
								while($n < $count) {
									$from = ((isset($zip_from[$installation_coverage_area_id[$n]])) ? $zip_from[$installation_coverage_area_id[$n]] : '');
									$to = ((isset($zip_to[$installation_coverage_area_id[$n]])) ? $zip_to[$installation_coverage_area_id[$n]] : '');

										if (!empty($from) && !empty($to)) {
											$from_break = tep_break_zip4_code($from);
											$to_break = tep_break_zip4_code($to);
											$database->query("update " . TABLE_INSTALLATION_COVERAGE_AREAS . " set zip_4_first_break_start = '" . $from_break[0] . "', zip_4_first_break_end = '" . $from_break[1] . "', zip_4_second_break_start = '" . $to_break[0] . "', zip_4_second_break_end = '" . $to_break[1] . "' where installation_coverage_area_id = '" . $installation_coverage_area_id[$n] . "' limit 1");
										} else {
											$database->query("delete from " . TABLE_INSTALLATION_COVERAGE_AREAS . "  where installation_coverage_area_id = '" . $installation_coverage_area_id[$n] . "' limit 1");
										}
									$n++;
								}
						}
					} else {
						$page_action = 'edit';
					}
				
		} elseif ($page_action == 'add_confirm') {
			if (!empty($submit_value)) {
			
			
				$name = tep_fill_variable('name');
				$installer_id = tep_fill_variable('installer_id');
				$state_id = tep_fill_variable('state_id');
				$service_area_id = tep_fill_variable('service_area_id');
				$zip_from_new = tep_fill_variable('zip_from_new', 'post', array());
				$zip_to_new = tep_fill_variable('zip_to_new', 'post', array());
				$area_name = tep_fill_variable('area_name', 'post', array());
				$warehouse_id = tep_fill_variable('warehouse_id');
				
				$count = count($zip_from_new);
				$n = 0;
				$error_status = false;
					while($n < $count) {
						$from = ((isset($zip_from_new[$n])) ? $zip_from_new[$n] : '');
						$to = ((isset($zip_to_new[$n])) ? $zip_to_new[$n] : '');
						//As we loop these we will check them.
						//If its empty dont check it.
							if (!empty($from) || !empty($to)) {
								if (!tep_zip4_is_valid($from) || !tep_zip4_is_valid($to)) {
									$error_status = true;
								} else {
									//Check if it is taken
										if (tep_zip4_is_assigned_to_area_bgdn($from, $to)) {
											$error_status = true;
										}
								}
							}
						$n++;
					}
					
					if ($error_status) {
						$page_action= 'add';
					} else {
						//Insert.
						$database->query("insert into " . TABLE_INSTALLATION_AREAS . " (name, service_area_id, date_added, installer_id, state_id, warehouse_id) values ('" . $name . "', '" . $service_area_id . "', '" . time() . "', '" . $installer_id . "', '" . $state_id . "', '" . $warehouse_id . "')");
						$installation_area_id = $database->insert_id();
						$count = count($zip_from_new);
						$n = 0;
							while($n < $count) {
								$from = ((isset($zip_from_new[$n])) ? $zip_from_new[$n] : '');
								$to = ((isset($zip_to_new[$n])) ? $zip_to_new[$n] : '');
									if (!empty($from) && !empty($to)) {
										$from_break = tep_break_zip4_code($from);
										$to_break = tep_break_zip4_code($to);
										$current_area_name = ((!empty($area_name[$n])) ? $area_name[$n] : '');
										$area_true_name = '';
											if (isset($area_name) && isset($area_name[$n])) {
												$area_true_name = $area_name[$n];
											}
										$database->query("insert into " . TABLE_INSTALLATION_COVERAGE_AREAS . " (installation_area_id, zip_4_first_break_start, zip_4_first_break_end, zip_4_second_break_start, zip_4_second_break_end, installation_coverage_area_name) values ('" . $installation_area_id . "', '" . $from_break[0] . "', '" . $from_break[1] . "', '" . $to_break[0] . "', '" . $to_break[1] . "', '" . $area_true_name . "')");
									}
								$n++;
							}
					}

			} else {
				$page_action = 'add';
			}
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_INSTALLATION_AREAS . " where installation_area_id = '".$aID."'");
			$database->query("delete from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where installation_area_id = '".$aID."'");
			$database->query("delete from " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " where installation_area_id = '".$aID."'");
		} else if($page_action == 'change_status'){
		    if(isset($_POST['activeStatus']) && isset($_GET['installation_area_id'])){
		        $database->query("
		                UPDATE " . TABLE_INSTALLATION_AREAS . " SET active = " . $_POST['activeStatus'] . " WHERE installation_area_id = " . $_GET['installation_area_id'] . "
                ");
            }
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
	?>
	<tr>
		<td width="100%" valign="top">
				<?php
				$extra = '';
							if (!empty($show_zip_code)) {
								$break = explode('-', $show_zip_code);
									if (!empty($show_state_id) || !empty($show_service_area_id)) {
										$extra .= ' and ';
									} elseif (empty($show_installer_id)) {
										$extra .= ' where ';
									}
									if (count($break) == 2) {
										$extra .= " ((ica.zip_4_first_break_start < '" . $break[0] . "' and ica.zip_4_second_break_start > '" . $break[0] . "') or (ica.zip_4_first_break_start <= '" . $break[0] . "' and ica.zip_4_second_break_start >= '" . $break[0] . "' and ica.zip_4_first_break_end <= '" . $break[1] . "' and  ica.zip_4_second_break_end >= '" . $break[1] . "'))";
									} else {
										$extra .= " (ica.zip_4_first_break_start <= '" . $break[0] . "' and ica.zip_4_second_break_start >= '" . $break[0] . "')";
									}
								
							}
						
					$listing_split = new split_page("select ia.installation_area_id, ia.name, u.user_id, ud.firstname, ud.lastname, sa.name as service_area_name, ia.warehouse_id, ia.active  from " . TABLE_INSTALLATION_AREAS . " ia  left join " . TABLE_USERS . " u on (ia.installer_id = u.user_id) left join " . TABLE_USERS_DESCRIPTION . " ud on( u.user_id = ud.user_id) left join " . TABLE_USERS_TO_USER_GROUPS . " utug on (u.user_id = utug.user_id and utug.user_group_id = '3') left join " . TABLE_SERVICE_AREAS . " sa on (ia.service_area_id = sa.service_area_id) left join "  . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (ia.installation_area_id = ica.installation_area_id) " . (((!empty($show_state_id)) || ($show_service_area_id !== '') || !empty($show_installer_id) || !empty($show_warehouse_id)) ? ' where ' : '') . ((!empty($show_state_id)) ? " ia.state_id = '" . $show_state_id . "' " : '') . (($show_service_area_id !== '') ? (((!empty($show_state_id)) ? " and " : '') . " ia.service_area_id = '" . $show_service_area_id . "' ") : '') . $extra . ((!empty($show_installer_id)) ? (((!empty($show_state_id) || ($show_service_area_id !== '') || !empty($show_zip_code) || !empty($show_warehouse_id)) ? " and " : '')) . " ia.installer_id = '" . $show_installer_id . "' " : '') . ((!empty($show_warehouse_id)) ? (((!empty($show_state_id) || ($show_service_area_id !== '') || !empty($show_zip_code)) ? " and " : '')) . " ia.warehouse_id = '" . $show_warehouse_id . "' " : '') . " group by ia.installation_area_id, ud.firstname, ud.lastname order by ia.name", '20', 'ia.installation_area_id');
							if ($listing_split->number_of_rows > 0) {
							?>
							<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
								<tr>
									<td class="pageBoxHeading" align="center">Coverage Area Name</td>
									<td class="pageBoxHeading" align="center">Service Area</td>
									<td class="pageBoxHeading">Zip Code Ranges</td>
									<td class="pageBoxHeading">Default Installer</td>
									<td class="pageBoxHeading">Assigned Warehouse</td>
									<td class="pageBoxHeading" align="right">Action</td>
                                    <td class="pageBoxHeading" align="right">Active/Inactive</td>
									<td width="10" class="pageBoxHeading"></td>
								</tr>
							<?php
								$aData = array();
								
								$query = $database->query($listing_split->sql_query);
								    foreach($database->fetch_array($query) as $result){
										?>
											<tr>
												<td class="pageBoxContent" valign="top" align="center"><?php echo $result['name']; ?></td>
												<td class="pageBoxContent" valign="top" align="center"><?php echo $result['service_area_name']; ?></td>
												<td class="pageBoxContent" valign="top"><?php 
													$zip_query = $database->query("select zip_4_first_break_start, zip_4_first_break_end, zip_4_second_break_start, zip_4_second_break_end from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where installation_area_id = '" . $result['installation_area_id'] . "' order by zip_4_first_break_start");
													$n = 0;
													    foreach($database->fetch_array($zip_query) as $zip_result){
																if ($n > 0) {
																	echo '<br>';
																}
															echo $zip_result['zip_4_first_break_start'].'-'.$zip_result['zip_4_first_break_end'].' to ' . $zip_result['zip_4_second_break_start'] . '-'.$zip_result['zip_4_second_break_end'];
															if(tep_zip4_is_assigned_to_area_bgdn2($zip_result['zip_4_first_break_start'].'-'.$zip_result['zip_4_first_break_end'], $zip_result['zip_4_second_break_start'] . '-'.$zip_result['zip_4_second_break_end'])) {
																echo " <span style='color:red'>Duplicate</span>";
															}
															$n++;
														}
												?></td>
												<td class="pageBoxContent" valign="top"><?php echo ((!empty($result['user_id'])) ? ($result['lastname'] . ', ' . $result['firstname']) : ''); ?></td>
												<td class="pageBoxContent" valign="top"><?php echo tep_get_warehouse_name($result['warehouse_id']); ?></td>
												<td class="pageBoxContent" align="right" valign="top"><a href="<?php echo FILENAME_ADMIN_ASSIGNMENT_AREAS . '?aID='.$result['installation_area_id'].'&page_action=edit&page='.$page.'&show_state_id='.$show_state_id.'&show_service_area_id='.$show_service_area_id.'&show_installer_id='.$show_installer_id.'&show_zip_code='.$show_zip_code; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_ASSIGNMENT_AREAS . '?aID='.$result['installation_area_id'].'&page_action=delete&page='.$page.'&show_state_id='.$show_state_id.'&show_service_area_id='.$show_service_area_id.'&show_installer_id='.$show_installer_id.'&show_zip_code='.$show_zip_code; ?>">Delete</a></td>
                                                <td class="pageBoxContent" align="right" valign="top">
                                                    <?php
                                                        $selectActive = '';
                                                        $selectInactive = '';
                                                        if($result['active'] == true){
                                                            $selectActive = 'selected';
                                                        }
                                                        if($result['active'] == false){
                                                            $selectInactive = 'selected';
                                                        }

                                                    ?>
                                                    <form action="admin_assignment_areas.php?installation_area_id=<?php echo $result['installation_area_id']; ?>&page_action=change_status" method="post">
                                                        <select class="form-control" style="width:auto;" name="activeStatus" onchange="this.form.submit()">
                                                            <option value="1" <?php echo $selectActive; ?>>Active</option>
                                                            <option value="0" <?php echo $selectInactive; ?>>Inactive</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td width="10" class="pageBoxContent"></td>
											</tr>
										<?php
										}
										?>
										<tr>
											<td colspan="5">
												<table class="normaltable" cellspacing="0" cellpadding="2">
													<tr>
														<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> Assignment Areas)'); ?></td>
														<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'x', 'y', 'page_action', 'action', 'aID'))); ?></td>
													</tr>
												</table>
											</td>
										</tr>
										<?php
						} else {
					?>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="pageBoxContent">No available areas can be found.  Please click create to create a new area.</td>
						</tr>
					<?php
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
					<?php
						if ($page_action == 'edit') {
							$basic_query = $database->query("select name, installer_id, state_id, service_area_id, warehouse_id from " . TABLE_INSTALLATION_AREAS . " where installation_area_id = '" . $aID . "' limit 1");
							$basic_result = $database->fetch_array($basic_query);
							$zip_from_new = tep_fill_variable('zip_from_new', 'post', array());
							$zip_to_new = tep_fill_variable('zip_to_new', 'post', array());
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Edit this Assignment Area</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_ASSIGNMENT_AREAS; ?>?page_action=edit_confirm&aID=<?php echo $aID; ?>&page=<?php echo $page.'&show_state_id='.$show_state_id.'&show_service_area_id='.$show_service_area_id.'&show_installer_id='.$show_installer_id.'&show_zip_code='.$show_zip_code; ?>" method="post">
							<tr>
								<td class="main">Area Name: </td><td><input type="text" name="name" value="<?php echo $basic_result['name']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">State: </td><td><?php echo tep_draw_state_pulldown('state_id', $basic_result['state_id']); ?></td>
							</tr>
							<tr>
								<td class="main">Service Area: </td>
								<td><?php echo tep_draw_service_areas_pulldown('service_area_id', $basic_result['service_area_id']); ?></td>
							</tr>
							<tr>
								<td class="main">Default Installer: </td><td><?php echo tep_draw_installer_pulldown('installer_id', $basic_result['installer_id'], array(array('id' => '', 'name' => 'None'))); ?></td>
							</tr>
							<tr>
								<td class="main">Assigned Warehouse: </td>
								<td><?php echo tep_draw_warehouse_pulldown('warehouse_id', $basic_result['warehouse_id']); ?></td>
							</tr>
							<tr>
								<td class="main" colspan="2">Assigned Areas (leave blank to delete)</td>
							</tr>
							<tr>
								<td class="main">Zip From</td><td class="main">Zip To</td>
							</tr>
							<?php
								//Get all existing areas.
								$existing_query = $database->query("select installation_coverage_area_id, zip_4_first_break_start, zip_4_first_break_end, zip_4_second_break_start, zip_4_second_break_end from " . TABLE_INSTALLATION_COVERAGE_AREAS . " where installation_area_id = '" . $aID . "' order by zip_4_first_break_start, zip_4_first_break_end, zip_4_second_break_start, zip_4_second_break_end");
								$zip_from = tep_fill_variable('zip_from', 'post', array());
								$zip_to = tep_fill_variable('zip_to', 'post', array());
								    foreach($database->fetch_array($existing_query) as $existing_result){
										//If the post is set then use that instead.
										if (isset($zip_from[$existing_result['installation_coverage_area_id']])) {
											$from = $zip_from[$existing_result['installation_coverage_area_id']];
										} else {
											$from = $existing_result['zip_4_first_break_start'].'-'.$existing_result['zip_4_first_break_end'];
										}
										if (isset($zip_to[$existing_result['installation_coverage_area_id']])) {
											$to = $zip_to[$existing_result['installation_coverage_area_id']];
										} else {
											$to = $existing_result['zip_4_second_break_start'].'-'.$existing_result['zip_4_second_break_end'];
										}
										if (empty($from) || !tep_zip4_is_valid($from) || empty($to) || !tep_zip4_is_valid($to)) {
											$error = 'Zip Code invalid.';
										} else {
											//Check if it is taken
												if (tep_zip4_is_assigned_to_area_bgdn($from, $to, array($existing_result['installation_coverage_area_id']))) {
													$error = 'Zip Code already Assigned.';
												} else {
													$error = '';
												}
										}
									?>
									<input type="hidden" name="installation_coverage_area_id[]" value="<?php echo $existing_result['installation_coverage_area_id']; ?>" />
									<tr>
										<td class="main"><input type="text" name="zip_from[<?php echo $existing_result['installation_coverage_area_id']; ?>]" value="<?php echo $from; ?>" size="8" /></td><td class="main"><input type="text" name="zip_to[<?php echo $existing_result['installation_coverage_area_id']; ?>]" value="<?php echo $to; ?>" size="8" /><?php echo '&nbsp;'.$error; ?></td>
									</tr>
									<?php
									}
								//Add any that have been submitted
								$count = count($zip_from_new);
								$n = 0;
								
									while($n < $count) {
										$from = ((isset($zip_from_new[$n])) ? $zip_from_new[$n] : '');
										$to = ((isset($zip_to_new[$n])) ? $zip_to_new[$n] : '');
									//As we loop these we will check them.
											if (empty($from) || !tep_zip4_is_valid($from) || empty($to) || !tep_zip4_is_valid($to)) {
												$error = 'Zip Code invalid.';
											} else {
												//Check if it is taken
													if (tep_zip4_is_assigned_to_area_bgdn($from, $to)) {
														$error = 'Zip Code already Assigned.';
													} else {
														$error = '';
													}
											}
											if (!empty($from) || !empty($to)) {
												echo $from . ' - ' . $to . '<br>';
									?>
									<tr>
										<td class="main"><input type="text" name="zip_from_new[]" value="<?php echo $from; ?>" size="8" /></td><td class="main"><input type="text" name="zip_to_new[]" value="<?php echo $to; ?>" size="8" /></td>
									</tr>
									<tr><td align="center" colspan="2"><span style="color:red"><?php echo '&nbsp;'.$error; ?></span></td></tr>
									<?php
											
											}
										$n++;
									}
								//Add another just to be able to add.
							?>
							<tr>
								<td class="main"><input type="text" size="8" name="zip_from_new[]" value="" /></td><td class="main"><input type="text" size="8" name="zip_to_new[]" value="" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2"><input type="submit" name="submit_value" value="Add Another Range"></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_ASSIGNMENT_AREAS; ?>?page=<?php echo $page.'&show_state_id='.$show_state_id.'&show_service_area_id='.$show_service_area_id.'&show_installer_id='.$show_installer_id.'&show_zip_code='.$show_zip_code; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<?php
						}elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this Assignment Area?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_ASSIGNMENT_AREAS; ?>?aID=<?php echo $aID; ?>&page=<?php echo $page.'&show_state_id='.$show_state_id.'&show_service_area_id='.$show_service_area_id.'&show_installer_id='.$show_installer_id.'&show_zip_code='.$show_zip_code; ?>&page_action=delete_confirm" method="post"><?php echo tep_create_button_submit('delete', 'Delete'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_ASSIGNMENT_AREAS; ?>?page=<?php echo $page.'&show_state_id='.$show_state_id.'&show_service_area_id='.$show_service_area_id.'&show_installer_id='.$show_installer_id.'&show_zip_code='.$show_zip_code; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					<?php
						}elseif ($page_action == 'add') {
							$zip_from_new = tep_fill_variable('zip_from_new', 'post', array());
							$zip_to_new = tep_fill_variable('zip_to_new', 'post', array());
							$name = tep_fill_variable('name');
							$installer_id = tep_fill_variable('installer_id');
							$state_id = tep_fill_variable('state_id');
							$service_area_id = tep_fill_variable('service_area_id');
							$warehouse_id = tep_fill_variable('warehouse_id');
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Add a new Assignment Area</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_ASSIGNMENT_AREAS; ?>?page_action=add_confirm&page=<?php echo $page.'&show_state_id='.$show_state_id.'&show_service_area_id='.$show_service_area_id.'&show_installer_id='.$show_installer_id.'&show_zip_code='.$show_zip_code; ?>" method="post">
							<tr>
								<td class="main">Area Name: </td><td><input type="text" name="name" value="<?php echo $name; ?>" /></td>
							</tr>
							<tr>
								<td class="main">State: </td><td><?php echo tep_draw_state_pulldown('state_id', $state_id); ?></td>
							</tr>
							<tr>
								<td class="main">Service Area: </td>
								<td><?php echo tep_draw_service_areas_pulldown('service_area_id', $service_area_id); ?></td>
							</tr>
							<tr>
								<td class="main">Default Installer: </td><td><?php echo tep_draw_installer_pulldown('installer_id', $installer_id, array(array('id' => '', 'name' => 'None'))); ?></td>
							</tr>
							<tr>
								<td class="main">Assigned Warehouse: </td>
								<td><?php echo tep_draw_warehouse_pulldown('warehouse_id', $warehouse_id); ?></td>
							</tr>
							<tr>
								<td class="main" colspan="2">Assigned Areas (leave blank to delete)</td>
							</tr>
							<tr>
								<td class="main">Zip From</td><td class="main">Zip To</td>
							</tr>
							<?php
								//Add any that have been submitted
								$count = count($zip_from_new);
								$n = 0;
									while($n < $count) {
										$from = ((isset($zip_from_new[$n])) ? $zip_from_new[$n] : '');
										$to = ((isset($zip_to_new[$n])) ? $zip_to_new[$n] : '');
										//As we loop these we will check them.
											if (empty($from) || !tep_zip4_is_valid($from) || empty($to) || !tep_zip4_is_valid($to)) {
												$error = 'Zip Code invalid.';
											} else {
												//Check if it is taken
													if (tep_zip4_is_assigned_to_area_bgdn($from, $to)) {
														$error = 'Zip Code already Assigned.';
													} else {
														$error = '';
													}
											}
											if (!empty($from) || !empty($to)) {
									?>
									<tr>
										<td class="main"><input type="text" name="zip_from_new[]" value="<?php echo $from; ?>" size="8" /></td><td class="main"><input type="text" name="zip_to_new[]" value="<?php echo $to; ?>" size="8" />
										</td>
									</tr>
									<tr><td align="center" colspan="2"><span style="color:red"><?php echo '&nbsp;'.$error; ?></span></td></tr>
									<?php
											}
										$n++;
									}
								//Add another just to be able to add.
							?>
							<tr>
								<td class="main"><input type="text" name="zip_from_new[]" value="" size="8" /></td><td class="main"><input type="text" name="zip_to_new[]" value="" size="8" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2"><input type="submit" name="submit_value" value="Add New Zip Range"></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>

							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('create', 'Create', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_ASSIGNMENT_AREAS; ?>?page=<?php echo $page.'&show_state_id='.$show_state_id.'&show_service_area_id='.$show_service_area_id.'&show_installer_id='.$show_installer_id.'&show_zip_code='.$show_zip_code; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
							<td class="pageBoxHeading"><b>Assignment Area Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit an Assignment or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page=<?php echo $page; ?>&page=<?php echo $page; ?>" method="get">
						<tr>
							<td class="main">Show only State: <?php echo tep_draw_state_pulldown('show_state_id', $show_state_id, '', array(array('id' => '', 'name' => 'All States'))); ?></td>
						</tr>
						<tr>
							<td class="main">Show only Installer: <?php echo tep_draw_installer_pulldown('show_installer_id', $show_installer_id, array(array('id' => '', 'name' => 'All Installers'))); ?></td>
						</tr>
						<tr>
							<td class="main">Show only Service Area: <?php echo tep_draw_service_areas_pulldown('show_service_area_id', $show_service_area_id, '', array(array('id' => '', 'name' => 'All Areas'), array('id' => '0', 'name' => 'Unassigned'))); ?></td>
						</tr>
						<tr>
								<td class="main">Search by Zip Code: <input type="text" name="show_zip_code" value="<?php echo $show_zip_code; ?>"></td>
							</tr>
						<tr>
								<td class="main">Show only Warehouse: <?php echo tep_draw_warehouse_pulldown('show_warehouse_id', $show_warehouse_id, array(array('id' => '', 'name' => 'All States'))); ?></td>
						</tr>	
							
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
						</tr>
						<tr>
							<td height="5"><?php echo tep_create_button_submit('update', 'Update'); ?></td>
						</tr>
						</form>
						<tr>
							<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=add&page=<?php echo $page.'&show_state_id='.$show_state_id.'&show_service_area_id='.$show_service_area_id.'&show_installer_id='.$show_installer_id.'&show_zip_code='.$show_zip_code; ?>" method="post">
						<tr>
							<td height="5"><?php echo tep_create_button_submit('create', 'Create'); ?></td>
						</tr>
						</form>
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