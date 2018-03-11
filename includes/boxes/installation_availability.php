<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" align="center">
			<table width="260" cellspacing="0" cellpadding="0">
				<?php
					$availability_action = ((isset($_GET['availability_action'])) ? $_GET['availability_action'] : '');
						if (!isset($_POST['submit_button_x']) || !isset($_POST['submit_button_y'])) {
							$availability_action = '';
						}
					$session->php_session_unregister('house_number');
					$session->php_session_unregister('street_name');
					$session->php_session_unregister('city');
					$session->php_session_unregister('zip_code');
					$session->php_session_unregister('state');
					$session->php_session_unregister('county');
					
					$house_number = tep_fill_variable('house_number', 'post', '- House Number -');
					$street_name = tep_fill_variable('street_name', 'post', '- Street Name -');
					$city = tep_fill_variable('city', 'post', '- City -');
					$zip_code = tep_fill_variable('zip_code', 'post', '- Zip Code -');
					$state = tep_fill_variable('state', 'post', '');
					$county = tep_fill_variable('county', 'post', '');
						if ($availability_action == 'search') {
							//Check if we have all the required items.
							$error = false;
							$error_message = '';
								if (empty($street_name) || ($street_name == '- Street Name -')) {
									$error = true;
										if (!empty($error_message)) {
											$error_message .= '<br>';
										}
									$error_message .= 'Please enter the Street Name.';
								}
								if ((empty($city) || ($city == '- City -')) || (empty($city) || ($city == '- City -'))) {
									$error = true;
										if (!empty($error_message)) {
											$error_message .= '<br>';
										}
									$error_message .= 'Please enter either a City or a Zip Code.';
								}
								if (empty($state)) {
									$error = true;
										if (!empty($error_message)) {
											$error_message .= '<br>';
										}
									$error_message .= 'Please select your State.';
								}
								if ($error) {
									$availability_action = '';
								}
						}
						if ($availability_action == 'search') {
							$search_street_name = '';
								if (!empty($house_number) && ($house_number != '- House Number -')) {
									$search_street_name = $house_number.' ';
								}
							$search_street_name .= $street_name;
							$search_state_name = tep_get_state_name($state);
								if (!empty($city) && ($city != '- City -')) {
									$search_city = $city;
								} else {
									$search_city = '';
								}
								if (!empty($zip_code) && ($zip_code != '- Zip Code -')) {
									$search_zip = $zip_code;
								} else {
									$search_zip = '';
								}
							
							$zip4_class=new zip4($search_street_name, $search_state_name, $search_city, $search_zip);
								if ($zip4_class->search()) {
									$zip4_code = $zip4_class->return_zip_code();
								} else {
									$availability_action = '';
									$fail_type  = $zip4_class->return_fail_type();
										if ($fail_type == 'none') {
											$error_message = 'Sorry we could not find that address.  Please check it and try again.';
										} else {
											$error_message = 'Sorry that address is too vague.';
												if (empty($house_number) || ($house_number == '- House Number -')) {
													$error_message .= '<br>Try entering a House Number.';
												} else {
													$error_message .= '<br>Try entering more details.';
												}
										}
									
										
								}
						}	
						if ($availability_action == 'search') {
							$address_string = '';
								if (!empty($search_street_name)) {
										if (!empty($address_string)) {
											$address_string .= '<br>';
										}
									$address_string .= $search_street_name;
								}
								if (!empty($search_city)) {
										if (!empty($address_string)) {
											$address_string .= '<br>';
										}
									$address_string .= $search_city;
								}
								if (!empty($search_state_name)) {
										if (!empty($address_string)) {
											$address_string .= '<br>';
										}
									$address_string .= $search_state_name;
								}
								if (!empty($search_zip)) {
										if (!empty($address_string)) {
											$address_string .= '<br>';
										}
									$address_string .= $search_zip;
								}
								if (substr($house_number, 0, 1) != '-') {
									$session->php_session_register('house_number', $house_number);
								} else {
									$session->php_session_register('house_number', '');
								}
								if (substr($house_number, 0, 1) != '-') {
									$session->php_session_register('street_name', $street_name);
								} else {
									$session->php_session_register('street_name', '');
								}
								if (substr($house_number, 0, 1) != '-') {
									$session->php_session_register('city', $city);
								} else {
									$session->php_session_register('city', '');
								}
								if (substr($house_number, 0, 1) != '-') {
									$session->php_session_register('zip_code', $zip_code);
								} else {
									$session->php_session_register('zip_code', '');
								}
								if (substr($house_number, 0, 1) != '-') {
									$session->php_session_register('state', $state);
								} else {
									$session->php_session_register('state', '');
								}
								if (substr($house_number, 0, 1) != '-') {
									$session->php_session_register('county', $county);
								} else {
									$session->php_session_register('county', '');
								}
							
							
							$address_status = tep_fetch_installation_availability($zip4_code);
				?>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				<tr>
					<td width="100%" align="left">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="style6" align="left" valign="top" NOWRAP><b>You Entered:</b></td>	
								<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
								<td width="100%" align="left" valign="top" class="style6"><?php echo $address_string; ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
				</tr>
				<?php
					if ($address_status) {
						$session->php_session_unregister('house_number');
						$session->php_session_unregister('street_name');
						$session->php_session_unregister('city');
						$session->php_session_unregister('zip');
						$session->php_session_unregister('state');
						$session->php_session_unregister('county');
						$session->php_session_unregister('cross_street_directions');
						
						$session->php_session_register('house_number', $house_number);
						$session->php_session_register('street_name', $street_name);
						$session->php_session_register('city', $city);
						$session->php_session_register('zip', $zip_code);
						$session->php_session_register('state', $state);
						$session->php_session_register('county', $county);
						
				?>
					<tr>
						<td width="100%" class="main"><strong>Yes we can install and service Sign Posts in that area.</strong></td>
					</tr>
					
					<?php
							$service_area_id = tep_fetch_zip4_service_area($zip4_code);
								if (tep_fetch_service_area_window($service_area_id) > 0) {
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2">This address has a <?php echo tep_fetch_service_area_window($service_area_id); ?> business day installation window (excludes weekends, Federal Holidays and severe weather days).</td>
							</tr>
							
							<?php
								}
							?>
							<tr>
						<td width="100%" class="pageBoxContent">If you are not currently a customer, you will have to sign up for our service.  <a href="<?php echo FILENAME_ACCOUNT_CREATE; ?>">Please click here to start the sign up process</a>.</td>
					</tr>
				
					<tr>
						<td width="100%" align="left">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td align="left"><a href="<?php echo FILENAME_ORDER_CREATE; ?>?order_type_id=1"><?php echo tep_create_button_link('request_installation', 'Request Installation'); ?></a></td>
									<td align="right"><a href="<?php echo PAGE_URL; ?>"><?php echo tep_create_button_link('try_again', 'Try Again'); ?></a></td>
								</tr>
							</table>
						</td>
					</tr>				
				<?php
					} else {
				?>
					<tr>
						<td width="100%" class="installationAreaNotAvailable">Sorry that address falls outside our usual coverage areas.<br />Please fell free to contact us if you think this is a mistake.</td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="15" width="1"></td>
					</tr>
					<tr>
						<td width="100%" align="left">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td align="left"><a href="<?php echo FILENAME_CONTACT_US; ?>"><?php echo tep_create_button_link('contact_us', 'Contact Us'); ?></a></td>
									<td align="right"><a href="<?php echo PAGE_URL; ?>"><?php echo tep_create_button_link('try_again', 'Try Again'); ?></a></td>
								</tr>
							</table>
						</td>
					</tr>	
				<?php
					}
						} else {
				?>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				<?php
					if (!empty($error_message)) {
				?>
				<tr>
					<td width="100%" align="left" class="mediumError"><?php echo $error_message; ?></td>
				</tr>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				<?php
					} else {
				?>
				
				<tr>
					<td class="style6" align="left">Enter an address below to see our service availability for that area.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<?php
					}
				?>
				<tr>
					<form name="checkAvailability" method="post" action="<?php echo PAGE_URL; ?>?availability_action=search">
					<td width="100%" align="center">
						<table width="228" cellspcaing="0" cellpadding="0">
							<tr>
								<td width="110"><input style="width:110px;" type="text" name="house_number" value="<?php echo $house_number; ?>" onclick="if (this.value == '- House Number -') { this.value = ''; }"></td>
								<td width="8"><img src="images/pixel_trans.gif" height="1" width="8"></td>
								<td width="110"><input style="width:110px;" type="text" name="street_name" value="<?php echo $street_name; ?>" onclick="if (this.value == '- Street Name -') { this.value = ''; }"></td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td width="110"><input style="width:110px;" type="text" name="city" value="<?php echo $city; ?>" onclick="if (this.value == '- City -') { this.value = ''; }"></td>
								<td width="8"><img src="images/pixel_trans.gif" height="1" width="8"></td>
								<td width="110"><?php echo tep_draw_state_pulldown('state', $state, ' onchange="this.form.submit();" style="width:79px;"', array(array('id' => '', 'name' => '- State -'))); ?></td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td width="100%" colspan="3">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><input style="width:110px;" type="text" name="zip_code" value="<?php echo $zip_code; ?>" onclick="if (this.value == '- Zip Code -') { this.value = ''; }"></td>
											<td width="8"><img src="images/pixel_trans.gif" height="1" width="8"></td>
											<td align="right"><?php echo tep_draw_county_pulldown('county', $state, $county, array(array('id' => '', 'name' => '- County -')), ' style="width:153px;"'); ?></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
				</tr>
				<tr>
					<td width="100%">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left"><?php echo tep_create_button_link('reset', 'Reset', ' onclick="document.forms[\'checkAvailability\'].reset();"'); ?></td>
								<td width="100%" align="right"><input type="image" src="images/go.gif" height="38" width="39" name="submit_button" /></td>
								<td width="8"><img src="images/pixel_trans.gif" height="1" width="8" /></td>
							</tr>
						</table>
					</td>
					</form>
				</tr>
				<tr>
					<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
				</tr>
                <tr>
                    <td width="100%">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" class="columnBoxLeftFooter">You can also <a class="columnBoxLeftFooterRed" href="/service_area_ext.php">click here</a> to view our <b><a class="columnBoxLeftFooter" href="/service_area_core.php">CORE</a></b> and <b><a class="columnBoxLeftFooter" href="/service_area_ext.php">EXTENDED</a></b> Service Areas on Google Maps.</td>
                            </tr>
                        </table>
                    </td>
                </tr>
				<?php
						}
				?>
			</table>
		</td>
    </tr>
</table>
