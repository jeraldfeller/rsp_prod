<tr>
                  <td><table width="260" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td><img name="body_r1_c9" src="images/my_orders_heading.jpg" width="260" height="48" border="0" alt="" /></td>
                    </tr>
                    <tr>
                      <td height="109" width="100%" align="center" valign="top" class="columnBox">
					  	<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td height="8" width="100%"><img src="images/pixel_trans.gif" height="8" width="260" style="height: 8px; width: 260px;"></td>
							</tr>
							<?php
								if (!$user->user_is_logged() || ($user->fetch_user_group_id() == '1')) {
								?>
								<tr>
									<td class="style6" width="100%" align="left" height="19">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
												<td height="20" width="19" align="left" valign="middle"><img src="images/column_arrow.gif" height="9" width="8" /></td>
												<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
												<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ORDER_CREATE; ?>?order_type=1">Request Sign Post Install</a></td>
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
												<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ORDER_CREATE_ADDRESS; ?>?order_type=2">Request Sign Post Service Call</a></td>
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
												<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ORDER_CREATE_ADDRESS; ?>?order_type=3">Change Sign Post Removal Date</a></td>
											</tr>
										</table>
									</td>
								</tr>
								<?php
								}
							?>
							<?php
								if ($user->user_is_logged()) {
									if ($user->fetch_user_group_id() == '1') {
										//if (tep_get_active_addresses() > 0) {
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
													<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="agent_active_addresses.php">Active Addresses</a></td>
												</tr>
											</table>
										</td>
									</tr>
										<?php
										//}
										
										if (($active_orders = tep_get_active_orders()) > 0) {
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
													<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ORDER_VIEW; ?>">Search Order History</a></td>
												</tr>
											</table>
										</td>
									</tr>
									
								<?php
										} else {
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
													<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo FILENAME_ORDER_VIEW; ?>?order_view=closed">Search Order History</a></td>
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
													<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="end_year_reports.php">End Of Year Reports</a></td>
												</tr>
											</table>
										</td>
									</tr>
										<?php
									}
									
								} else {
							?>
							<tr>
								<td class="style6" width="100%" align="left" height="19">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td height="19" class="columnBoxLeftBody" align="center" valign="middle" width="100%" NOWRAP><em>Please login to view your Orders</em></td>
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
