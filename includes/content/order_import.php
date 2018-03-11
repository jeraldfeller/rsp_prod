<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$run_file = '';
	$column_count = 45;
		if ($page_action == 'download_example') {
			$file = 'Agent MRSI (only one essential),Agent Email (only one essential - this is ideal),Agent Name (Must be Exact - the worst choice),Street Address,City,State (Id Preferable),County (Id Preferable),ADC Page, ADC Letter,ADC Grid #,Cross Street Directions,Zip Code,ZIP4 Code (Only required if address can\'t be matched),Number of Posts,Special Instructions,Start Date (mm/dd/YYY)-defaults to now,Installation Date (only if installed - Defaults to nothing),' .
			'Install a Generic Brocure Box,Install For Sale rider on top of post,Install Sold Rider on top of post,Install Under Contract rider on top of post,Install For Rent rider on top of post,Install Rented Rider on top of post,IMPRESSIVE,I\'M GORGEOUS INSIDE,MUST SEE INSIDE,SURE TO PLEASE,COMING SOON!,NEIGHBORHOOD SPECIALIST,WARRANTY,HONEY STOP THE CAR!,YOU COULD BE HOME BY NOW!,IMMEDIATE OCCUPANCY,REMODELED,SHOWN BY APPOINTMENT ONLY,OPEN SUNDAY 1-4,OPEN SATURDAY 1-4,PRICED TO SELL,REDUCED,SE HABLO ESPANOL,SWIMMING POOL,LAKE VIEW,LAND,COMMERCIAL,RED ARROW';
			//17 + 6 + 28 = 50
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="example_file.csv"');
			header('Content-Length: '.strlen($file));
			echo $file;
			die();
		} elseif ($page_action == 'confirm_import') {
			//Time to run the file.
				if (!file_exists(DIR_TEMP . 'temp_import.csv')) {
					$error->add_error('order_import', 'Unknown Error.  Please try again.');
					$page_action = '';
					//tep_redirect(FILENAME_ORDER_IMPORT);
					//die();
				} else {
					$file = file(DIR_TEMP . 'temp_import.csv');
						for ($n = 0, $m = count($file); $n < $m; $n++) {
							echo $file[$n] . '<br>';
							
							$line = substr($file[$n], 1, (strlen($file[$n]) -2));
							
							
							$explode = explode('", "', $line);

							//First work out the optional Array.
							$optional = array();
								if (!empty($explode[17]) && ($explode[17] == 'true')) {
									$optional[1][] = '1';
								}
								if (!empty($explode[18]) && ($explode[18] == 'true')) {
									$optional[2][] = '2';
								}
								if (!empty($explode[19]) && ($explode[19] == 'true')) {
									$optional[2][] = '3';
								}
								if (!empty($explode[20]) && ($explode[20] == 'true')) {
									$optional[2][] = '4';
								}
								if (!empty($explode[21]) && ($explode[21] == 'true')) {
									$optional[2][] = '5';
								}
								if (!empty($explode[22]) && ($explode[22] == 'true')) {
									$optional[2][] = '6';
								}
								if (!empty($explode[23]) && ($explode[23] == 'true')) {
									$optional[3][] = '7';
								}
								if (!empty($explode[24]) && ($explode[24] == 'true')) {
									$optional[3][] = '8';
								}
								if (!empty($explode[25]) && ($explode[25] == 'true')) {
									$optional[3][] = '9';
								}
								if (!empty($explode[26]) && ($explode[26] == 'true')) {
									$optional[3][] = '10';
								}
								if (!empty($explode[27]) && ($explode[27] == 'true')) {
									$optional[3][] = '11';
								}
								if (!empty($explode[28]) && ($explode[28] == 'true')) {
									$optional[3][] = '12';
								}
								if (!empty($explode[29]) && ($explode[29] == 'true')) {
									$optional[3][] = '13';
								}
								if (!empty($explode[30]) && ($explode[30] == 'true')) {
									$optional[3][] = '14';
								}
								if (!empty($explode[31]) && ($explode[31] == 'true')) {
									$optional[3][] = '15';
								}
								if (!empty($explode[32]) && ($explode[32] == 'true')) {
									$optional[3][] = '16';
								}
								if (!empty($explode[33]) && ($explode[33] == 'true')) {
									$optional[3][] = '17';
								}
								if (!empty($explode[34]) && ($explode[34] == 'true')) {
									$optional[3][] = '18';
								}
								if (!empty($explode[35]) && ($explode[35] == 'true')) {
									$optional[3][] = '19';
								}
								if (!empty($explode[36]) && ($explode[36] == 'true')) {
									$optional[3][] = '20';
								}
								if (!empty($explode[37]) && ($explode[37] == 'true')) {
									$optional[3][] = '21';
								}
								if (!empty($explode[38]) && ($explode[38] == 'true')) {
									$optional[3][] = '22';
								}
								if (!empty($explode[39]) && ($explode[39] == 'true')) {
									$optional[3][] = '23';
								}
								if (!empty($explode[40]) && ($explode[40] == 'true')) {
									$optional[3][] = '24';
								}
								if (!empty($explode[41]) && ($explode[41] == 'true')) {
									$optional[3][] = '25';
								}
								if (!empty($explode[42]) && ($explode[42] == 'true')) {
									$optional[3][] = '26';
								}
								if (!empty($explode[43]) && ($explode[43] == 'true')) {
									$optional[3][] = '27';
								}
								if (!empty($explode[44]) && ($explode[44] == 'true')) {
									$optional[3][] = '28';
								}

							
							//Second do the address.
							$zip_explode = tep_break_zip4_code($explode[12]);
							$zip4_start = $zip_explode[0];
							$zip4_end = $zip_explode[1];
							$address_explode = explode(' ', $explode[3], 2);
							
						$database->query("insert into " . TABLE_ADDRESSES . " (house_number, street_name, city, zip, state_id, county_id, zip4, zip4_start, zip4_end, adc_number, number_of_posts, cross_street_directions, address_post_allowed) values ('" . $address_explode[0] . "', '" . $address_explode[1] . "', '" . $explode[4] . "', '" . $explode[11] . "', '" . $explode[5] . "', '" . $explode[6] . "', '" . $explode[12] . "', '" . $zip4_start . "', '" . $zip4_end . "', '','" . $explode[13] . "', '" . $explode[10] . "', '1')");
							$address_id = $database->insert_id();
							$database->query("insert into " . TABLE_ADDRESSES_TO_USERS . " (address_id, user_id) values ('" . $address_id . "', '" . $user->fetch_user_id() . "')");
							
							//Third and final is to import.
							$data = array('address_id' => $address_id,
												  'order_type_id' => '1',
												  'schedualed_start' => $explode[15],
												  'date_completed' => $explode[16],
												  'order_status' => '3',
												  'special_instructions' => $explode[14],
												  'optional' => $optional,
												  'county' => $explode[6],
												  'payment_method' => '1',
												  'extra_cost' => '',
												  'extra_cost_description' => '',
												  'special_conditions' => '',
												  'number_of_posts' => $explode[13],
												  'promo_code' => '');
							$order = new orders('insert', $explode[$column_count], $data, false, '3');
						}
					$error->add_error('order_import', 'Orders successfully imported.', 'success');

					unlink(DIR_TEMP . 'temp_import.csv');
				}
			
		} elseif ($page_action == 'import') {
			$uploadfile = DIR_TEMP . basename($_FILES['import_file']['name']);
				if (move_uploaded_file($_FILES['import_file']['tmp_name'], $uploadfile)) {
					$run_file = $uploadfile;
				} else {
					$error->add_error('order_import', 'Invalid file.  Please try again.');
					$page_action = '';
				}
		}

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('order_import', 'all')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('order_import', 'all'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
				<?php
					if ($page_action == 'import') {
						$content = file($run_file);
						$new_file = '';
						$error = false;
							for ($n = 1, $m = count($content); $n < $m; $n++) {
								//$new_line = substr($content[$n], 1, (count($content[$n]) - 2));
								$new_line = $content[$n];
								$explode = explode(',', $new_line);
									if (count($explode) != $column_count) {
										echo '<tr><td class="main"><b>Incorrect column count on line '.($n+1).'.  It is currently ' . count($explode) . ' and should be ' . $column_count . '.  Please check the format and try again.</b></td></tr>';
										$error = true;
										break;
									}
									if (empty($explode[0]) && empty($explode[1]) && empty($explode[2])) {
										echo '<tr><td class="main">Warning: Entry on line '.($n+1).' has no agent information.  This entry will be added tot he lost orders page if you choose to continue.</td></tr>';
									} else {
										//Lets try and get the agent_id
										$agent_id = '';
											if (!empty($explode[1])) {
												$query = $database->query("select user_id from " . TABLE_USERS . " where email_address = '" . $explode[1] . "' limit 1");
												$result = $database->fetch_array($query);
													if (!empty($result['user_id'])) {
														$agent_id = $result['user_id'];
													}
											} elseif (!empty($explode[0])) {
												$query = $database->query("select user_id from " . TABLE_USERS . " where agent_id = '" . $explode[0] . "' limit 1");
												$result = $database->fetch_array($query);
													if (!empty($result['user_id'])) {
														$agent_id = $result['user_id'];
													}
											} elseif (!empty($explode[2])) {
												$name_explode = explode(' ', $explode[2]);
												$query = $database->query("select user_id from " . TABLE_USERS_DESCRIPTION . " where firstname = '" . $name_explode[0] . "' and lastname = '" . $name_explode[1] . "' limit 1");
												$result = $database->fetch_array($query);
													if (!empty($result['user_id'])) {
														$agent_id = $result['user_id'];
													}
											}
											if (empty($agent_id)) {
												echo '<tr><td class="main"><b>Warning: Entry on line '.($n+1).' can not be matched to an agent.  Assuming test agent.</b></td></tr>';
												//$error = true;
												////break;
												$explode[$column_count] = '895';
											} else {
												$explode[$column_count] = $agent_id;
											}
									}
								//"House Number", "Street Name", "City", "State (Id Preferable)", "County (Id Preferable)", "ADC Page", "ADC Letter", "ADC Grid #", "Cross Street Directions", "ZIP4 Code (Only required if address can\'t be matched)", "Number of Posts"

									if (empty($explode[3])) {
										echo '<tr><td class="main"><b>Entry on line '.($n+1).' has no street address.  Please insert and try again.</b></td></tr>';
										$error = true;
										break;
									}
									if (empty($explode[4])) {
										echo '<tr><td class="main"><b>Entry on line '.($n+1).' has no city.  Please insert and try again.</b></td></tr>';
										$error = true;
										break;
									}
									if (empty($explode[5])) {
										echo '<tr><td class="main"><b>Entry on line '.($n+1).' has no state.  Please insert and try again.</b></td></tr>';
										$error = true;
										break;
									}
									if (empty($explode[6])) {
										echo '<tr><td class="main"><b>Entry on line '.($n+1).' has no county.  Please insert and try again.</b></td></tr>';
										$error = true;
										break;
									}
									if (!is_numeric($explode[5])) {
										//Need to see if we can actually get the state_id.
										$query = $database->query("select state_id from " . TABLE_STATES . " where name = '" . $explode[5] . "' limit 1");
										$result = $database->fetch_array($query);
											if (empty($result['state_id'])) {
												echo '<tr><td class="main"><b>State ' .$explode[5].' on line '.($n+1).' can not be matched in the database.  Please change and try again.</b></td></tr>';
												$error = true;
												break;
											} else {
												$explode[5] = $result['state_id'];
											}
									}
									if (!is_numeric($explode[6])) {
										//Need to see if we can actually get the state_id.
										$query = $database->query("select county_id from " . TABLE_COUNTYS . " where name = '" . $explode[6] . "' limit 1");
										$result = $database->fetch_array($query);
											if (empty($result['county_id'])) {
												echo '<tr><td class="main"><b>County ' .$explode[6].' on line '.($n+1).' can not be matched in the database.  Please change and try again.</b></td></tr>';
												$error = true;
												break;
											} else {
												$explode[6] = $result['county_id'];
											}
									}
									if (empty($explode[13])) {
										echo '<tr><td class="main">Warning: Entry on line '.($n+1).' has no post number.  Am assuming 1.</td></tr>';
										$explode[13] = '1';
									}
									//Try the address and see if we can get the zip4 code.
									if (empty($explode[12])) {
										//Dont have a zip4.  Check the address.
										$address_explode = explode(' ', $explode[3], 2);
										$zip4_class=new zip4($address_explode[0].' '.$address_explode[1],tep_get_state_name($explode[5]), $explode[4], $explode[11]);
											if ($zip4_class->search()) {
												$explode[12] = $zip4_class->return_zip_code();
											} else {
												echo '<tr><td class="main"><b>Entry on line '.($n+1).' can not be auto matched to a zip4 code.  Please either re-check the address or manually insert the zip4 code and try again..</b></td></tr>';
												$error = true;
												break;
											}
									}
									if (empty($explode[15])) {
										$explode[15] = mktime();
									} else {
										if (($new_time = strtotime($explode[15])) !== false) {
											$explode[15] = $new_time;
										}
										if ($explode[15] < 1) {
											$explode[15] = mktime();
										}
									}
									
									if (empty($explode[16])) {
										$explode[16] = 0;
									} else {
										if (($new_time = strtotime($explode[16])) !== false) {
											$explode[16] = $new_time;
										}
										if ($explode[16] < 1) {
											$explode[16] = '0';
										}
									}
								//Now add it to the insert array.
									if (!empty($new_file)) {
										$new_file .= "\n";
									}
								$new_file .= '"' .preg_replace("((\r\n))", '',  implode('", "', $explode) . '"');
							}
						
							if ($error) {
								$page_action = '';
								echo '<tr><td class="main"><br><b>Fatal Error Detected.  Can not import.</b></td></tr>';
							} else {
								$write_file = fopen(DIR_TEMP . 'temp_import.csv', "w");
								fwrite($write_file, $new_file);
								fclose($write_file);
								echo '<tr><td class="main"><br><b>Import file contains ' . (count($content)-1) . ' entrys.  Are you sure you wish to insert these into the database?</b></td></tr>';
							}
						unlink($run_file);
					}
				?>
			</table>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<?php
				if ($page_action == 'import') {
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<form action="<?php echo FILENAME_ORDER_IMPORT; ?>?page_action=confirm_import" method="post">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press Import below to start the import proccess.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><input type="submit" value="Import" /></form></td>
											<td align="right"><form action="<?php echo FILENAME_ORDER_IMPORT; ?>"><input type="submit" value="Cancel" /></form></td>
										</tr>
										
									</table>
								</td>
							</tr>
						</table>
					</form>
					</td>
				</tr>
			</table>
			<?php
				} else {
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<form action="<?php echo FILENAME_ORDER_IMPORT; ?>?page_action=import" method="post" enctype="multipart/form-data">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Select a file from your system and press import to start.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">File: <input type="file" name="import_file"></td>
							</tr>
							
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>

							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><input type="submit" value="Import" /></form></td>
										</tr>
										<tr>
											<td class="main"><a href="<?php echo FILENAME_ORDER_IMPORT; ?>?page_action=download_example">Download CSV Example File</a></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</form>
					</td>
				</tr>
			</table>
			<?php
				}
			?>
		</td>
	</tr>
</table>