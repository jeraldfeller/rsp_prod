<tr>
                  <td>
                    <?php 
                    $count = 1;
                    if (($user->user_is_logged()) && ($user->fetch_user_group_id() == 2)) { 
                        $use_num = true; 
                    ?>
                    <table class="groupNav">
                    <tr>
                      <td class="groupHeader"><span>I - My Account</span></td>
                    </tr>
                    <?php 
                    } else { 
                        $use_num = false; 
                    ?>
                    <table width="260" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td><img name="body_r1_c9" src="images/my_account_heading_small.jpg" width="260" height="48" border="0" alt="" /></td>
                    </tr>
                    <?php 
                    } 
                    ?>
                    <tr>
                      <td height="109" width="100%" align="center" valign="top" class="columnBox">
					  	<table width="100%" cellspacing="0" cellpadding="0" border="0">
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
							<form name="login" action="<?php echo HTTPS_PREFIX . '/' . PAGE_URL; ?>?action=login" method="post">
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
								<td><input name="email_address" type="text" size="15" style="max-width: 120px;"></td>
							</tr>
							<tr>
								<td height="2"><img src="images/pixel_trans.gif" height="2" width="1"></td>
							</tr>
							<tr>
								<td class="style6" width="100">Password:&nbsp;</td>
								<td><input name="password" type="password" size="15" style="max-width: 120px;"></td>
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
								<td height="8" width="100%"><img src="images/pixel_trans.gif" style="height: 8px; width: 260px;"></td>
							</tr>
							<tr>
								<td class="style6" width="100%" align="left" height="19">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
                                            <td height="20" width="19" align="left" valign="middle"><?php echo $use_num ? "<strong>{$count}.</strong>" : '<img src="images/column_arrow.gif" height="9" width="8" />'; $count++; ?></td>
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
                                            <td height="20" width="19" align="left" valign="middle"><?php echo $use_num ? "<strong>{$count}.</strong>" : '<img src="images/column_arrow.gif" height="9" width="8" />'; $count++; ?></td>
											<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
											<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ACCOUNT_CHANGE_PASSWORD; ?>">Change Password</a></td>
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
                                            <td height="20" width="19" align="left" valign="middle"><?php echo $use_num ? "<strong>{$count}.</strong>" : '<img src="images/column_arrow.gif" height="9" width="8" />'; $count++; ?></td>
											<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
											<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ACCOUNT_OVERVIEW; ?>">Account Overview</a></td>
										</tr>
									</table>
								</td>
							</tr>
							<?php
								if (($user->fetch_user_group_id() == 1) && ($user->fetch_billing_method_id() == 3)) {
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
                                            <td height="20" width="19" align="left" valign="middle"><?php echo $use_num ? "<strong>{$count}.</strong>" : '<img src="images/column_arrow.gif" height="9" width="8" />'; $count++; ?></td>
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
                  <td height="2" bgcolor="#13688D"><img src="images/pixel_trans.gif" height="2" width="1" /></td>
                </tr>
                <tr>
                  <td height="2"><img src="images/pixel_trans.gif" height="2" width="1" /></td>
                </tr>
                </table>
