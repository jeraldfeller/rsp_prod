				<tr>
                  <td><table width="291" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="1"></td>
                    </tr>
                    <tr>
                      <td><img name="body_r1_c9" src="images/body_r1_c9.jpg" width="291" height="48" border="0" id="body_r1_c9" alt="" /></td>
                    </tr>
                    <tr>
                      <td height="109" width="100%" align="center" valign="top" class="columnBox">
					  	<table width="200" cellspacing="0" cellpadding="0">
						<?php
							if (!$user->user_is_logged()) {
						?>
						<?php
							if ($error->get_error_status('login_box')) {
						?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="left" class="mediumError" colspan="2"><?php echo $error->get_error_string('login_box'); ?></td>
							</tr>
						<?php
							}
						?>
							<form name="login" action="<?php echo HTTP_PREFIX . '/' . PAGE_URL; ?>?action=login" method="post">
						<?php
							if (!$error->get_error_status('login_box')) {
						?>
							<tr>
								<td height="2"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
						<?php
							}
						?>
							<tr>
								<td class="style6" width="100">Email&nbsp;Address:&nbsp;</td>
								<td><input name="email_address" type="text" size="15"></td>
							</tr>
							<tr>
								<td height="2"><img src="images/pixel_trans.gif" height="2" width="1"></td>
							</tr>
							<tr>
								<td class="style6" width="100">Password:&nbsp;</td>
								<td><input name="password" type="password" size="15"></td>
							</tr>
						<?php
							if (!$error->get_error_status('login_box')) {
						?>
							<tr>
								<td height="8"><img src="images/pixel_trans.gif" height="8" width="1"></td>
							</tr>
						<?php
							} else {
						?>
							<tr>
								<td height="1"><img src="images/pixel_trans.gif" height="1" width="1"></td>
							</tr>
						<?php	
							}
						?>
							<tr>
								<td colspan="2" width="100%" height="22" align="center"><?php echo tep_create_button_submit('sign_in', 'Sign In'); ?></td>
							</tr>
							</form>
							<?php
								if (!$error->get_error_status('login_box')) {
							?>
							<tr>
								<td height="8"><img src="images/pixel_trans.gif" height="8" width="1"></td>
							</tr>
							<?php
								} 
							?>
							<tr>
								<td colspan="2" width="100%" NOWRAP class="columnBoxLeftFooter"><a class="columnBoxLeftFooterRed" href="account_create.php">Sign up here</a> for an Account | <a class="columnBoxLeftFooter" href="forgotten_password.php">Forgotten your Password?</a></td>
							</tr>
						<?php
							} else {
						?>
							<tr>
								<td height="8" width="100%"><img src="images/pixel_trans.gif" height="8" width="260"></td>
							</tr>
							<tr>
								<td class="style6" width="100%" align="left" height="19">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
											<td height="20" width="19" align="left" valign="middle"><img src="images/column_arrow.gif" height="9" width="8" /></td>
											<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
											<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ACCOUNT_UPDATE; ?>">Update Account Information</a></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="4"><img src="images/pixel_trans.gif" height="4" width="1"></td>
							</tr>
							<tr>
								<td class="style6" width="100%" align="left" height="19">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
											<td height="20" width="19" align="left" valign="middle"><img src="images/column_arrow.gif" height="9" width="8" /></td>
											<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
											<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ACCOUNT_CHANGE_PASSWORD; ?>">Change Password<a/></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="4"><img src="images/pixel_trans.gif" height="4" width="1"></td>
							</tr>
							<tr>
								<td class="style6" width="100%" align="left" height="19">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
											<td height="20" width="19" align="left" valign="middle"><img src="images/column_arrow.gif" height="9" width="8" /></td>
											<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
											<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ACCOUNT_OVERVIEW; ?>">View All Account Details</a></td>
										</tr>
									</table>
								</td>
							</tr>
							<?php
								if ($user->fetch_user_group_id() == 1 && $user->fetch_billing_method_id() == 3) {
							?>
							<tr>
								<td height="4"><img src="images/pixel_trans.gif" height="4" width="1"></td>
							</tr>
							<tr>
								<td class="style6" width="100%" align="left" height="19">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
											<td height="20" width="19" align="left" valign="middle"><img src="images/column_arrow.gif" height="9" width="8" /></td>
											<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
											<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_VIEW_INVOICES; ?>">View Balance/Invoices</a></td>
										</tr>
									</table>
								</td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td height="4"><img src="images/pixel_trans.gif" height="4" width="1"></td>
							</tr>
							<tr>
								<td class="style6" width="100%" align="left" height="19">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
											<td height="20" width="19" align="left" valign="middle"><img src="images/column_arrow.gif" height="9" width="8" /></td>
											<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
											<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo PAGE_URL; ?>?action=logout">Logoff</a></td>
										</tr>
									</table>
								</td>
							</tr>
						<?php
							}
						?>
						<tr>
							<td height="6"><img src="images/pixel_trans.gif" height="6" width="1" /></td>
						</tr>
						</table>
					  </td>
                    </tr>

                  </table></td>
                </tr>
				
                <tr>
                  <td height="2" bgcolor="#13688D"></td>
                </tr>
				
                <tr>
                  <td height="2"></td>
                </tr>
				
                <tr>
                  <td ><table width="291" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="1"></td>
                    </tr>
                    <tr>
                      <td><img name="body_r1_c9" src="images/body_r10_c9.jpg" width="291" height="51" border="0" id="body_r1_c9" alt="" /></td>
                    </tr>
                    <tr>
						<td width="100%" align="left" valign="top" class="columnBoxBack">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td height="7"><img src="images/pixel_trans.gif" height="7" width="1" /></td>
								</tr>
								<tr>
									<td width="100%" align="center">
										<table width="260" cellspacing="0" cellpadding="0">
											<tr>
												<td height="8"><img src="images/pixel_trans.gif" height="8" width="1" /></td>
											</tr>
											<?php
												$loop = 0;
												$query = $database->query("select sl.cost, sld.name, sld.description from " . TABLE_SERVICE_LEVELS . " sl, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where sl.service_level_id = sld.service_level_id and sld.language_id = '" . $language_id . "' order by sl.service_level_id");

                                                    foreach($query as $result){
															if ($loop > 0) {
															?>
															<tr>
																<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
															</tr>
															<?php
															}
														$description = $result['description'];
														$pos = ':';
														
														$pos_len = strlen($pos);
														
														$pos_num = strpos($description, $pos) + $pos_len -1;
														
														$description = substr($description, 0, $pos_num);
															
															/*if (strlen($description) > 100) {

																$description = substr($description, 0, 100) . '...';
															}*/
													?>
													<tr>
														<td width="100%" align="left" valign="top">
															<table width="100%" cellspacing="0" cellpadding="0">
																<tr>
																	<td class="columnBoxLeftBody"><b><?php echo $result['name']; ?></b></td>
																</tr>
																<tr>
																	<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
																</tr>
																<tr>
																	<td width="100%" align="left">
																		<table width="100%" cellspacing="0" cellpadding="0">
																			<tr>
																				<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
																				<td class="columnBoxLeftBody" align="left" width="100%"><?php echo $description; ?></td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
																</tr>
																<tr>
																	<td width="100%">
																		<table wudth="100%" cellspacing="0" cellpadding="0">
																			<tr>
																				<td width="100%"></td>
																				<td width="12" height="12"><a class="columnBoxLeftFooterRed" href="<?php echo FILENAME_SERVICE_PLANS; ?>"><img src="images/arro1.jpg" height="12" width="12" border="0" /></a></td>
																				<td width="3" height="12"><img src="images/pixel_trans.gif" height="1" width="3" /></td>
																				<td align="right" valign="middle" height="12" width="50" NOWRAP><a class="columnBoxLeftFooterRed" href="<?php echo FILENAME_SERVICE_PLANS; ?>">More Details</a></td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													
													<?php
														$loop++;
													}
											?>
											<tr>
												<td height="8"><img src="images/pixel_trans.gif" height="8" width="1" /></td>
											</tr>
											
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
                  </table></td>
                </tr>
				<tr>
                  <td height="2" bgcolor="#13688D"></td>
                </tr>
