                <tr>
                <td>
                  <table class="groupNav">
                    <tr>
                      <td class="groupHeader"><span>B - Statistics / Reports</span></td>
                    </tr>
                    <tr>
                      <td height="109" width="100%" align="center" valign="top" class="columnBox">
					  	<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td height="8" width="100%"><img src="images/pixel_trans.gif" height="8" width="260" style="height: 8px; width: 260px;"></td>
							</tr>
							<?php
								$query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where pg.file_name = '" . 'stats_and_reports.php' . "' and pg.page_group_id = p.page_group_id and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $user->fetch_user_group_id() . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' UNION select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where p.page_id IN (126, 129) AND p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' order by name ASC");
								$count = 0;
								foreach($query as $result){
											if ($count > 0) {
												?>
												<tr>
													<td height="4"><img src="images/pixel_trans.gif" height="4" width="1"></td>
												</tr>
												<?php
											}
										$count++;
										?>
										<tr>
											<td class="style6" width="100%" align="left" height="19">
												<table width="100%" cellspacing="0" cellpadding="0">
													<tr>
														<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
                                                        <td height="20" width="19" align="left" valign="middle"><strong><?php echo $count; ?>.</strong></td>
														<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
														<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="<?php echo $result['page_url']; ?>"><?php echo $result['name']; ?></a></td>
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
