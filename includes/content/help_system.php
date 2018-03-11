<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" align="left">
		<?php
			$page_url = tep_fill_variable('page_url', 'get', ''); //Page.
			$help_item = tep_fill_variable('help_item', 'get', ''); //Group.
			$page_action = tep_fill_variable('page_action', 'get', 'view_page');
			$help_group_id = tep_fill_variable('help_group_id', 'get');
				
				if (!empty($page_url)) {
					//The first reqiest or a subsequent.  Delete then re-register the page.
					$session->php_session_unregister('page_url');
					$session->php_session_register('page_url', $page_url);
				} else {
					$page_url = $session->php_return_session_variable('page_url');
				}
				if (($page_action == 'view_page') && empty($help_item)) {
					
				}
		?>
		<?php
		
			if ($page_action == 'view_page') {
				if (tep_page_has_help_items($page_url)) {
					$page_id = tep_fetch_page_id($page_url);
				?>
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td><span class="headerOtherWords">Available Items for this Page</span></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="0" cellpadding="0">
							<?php
								$help_items_array = array();
								
								$query = $database->query("select hi.help_item_id, hi.help_group_id, hid.help_item_name, hid.help_item_description from " . TABLE_HELP_ITEMS . " hi, " . TABLE_HELP_ITEMS_DESCRIPTION . " hid where hi.page_id = '" . $page_id . "' and hi.help_item_id = hid.help_item_id and hid.language_id = '" . $language_id . "'");
									while($result = $database->fetch_array($query)) {
										$help_items_array[$result['help_group_id']][] = array('name' => $result['help_item_name'], 'description' => $result['help_item_description']);
									}
								reset($help_items_array);
								$loop = 0;
									while(list($help_group_id, $items) = each($help_items_array)) {
										$details = tep_fetch_help_group_details($help_group_id);
									?>
										<?php
											if ($loop > 0) {
										?>
											<tr>
												<td height="25"><img src="images/pixel_trans.gif" height="25" /></td>
											</tr>
										<?php
											}
										?>
										<tr>
											<td class="main"><b><?php echo $details['name']; ?></b></td>
										</tr>
										<tr>
											<td height="3"><img src="images/pixel_trans.gif" height="3" /></td>
										</tr>
										<tr>
											<td width="100%" align="left">
												<table width="100%" cellspacing="0" cellpadding="0">
													<tr>
														<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
														<td width="100%" align="left">
															<table width="100%" cellspacing="0" cellpadding="0">
																<?php
																	$count = count($items);
																	$n = 0;
																		while($n < $count) {
																		?>
																			<?php
																				if ($n > 0) {
																				?>
																				<tr>
																					<td height="6"><img src="images/pixel_trans.gif" height="6" /></td>
																				</tr>
																				<?php
																				}
																				?>
																				<tr>
																					<td class="style9"><b><?php echo $items[$n]['name']; ?></b></td>
																				</tr>
																				<tr>
																					<td class="style9"><?php echo $items[$n]['description']; ?></td>
																				</tr>
																		<?php
																			$n++;
																		}
																?>
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
							</table>
						</td>
					</tr>
				</table>
				<?php
				} else {
				?>
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td width="100%">There are no available help topics for this page.  Please use the Table of Contents link or use the Search link to search all available help items.</td>
					</tr>
				</table>
				<?php
				}
			} elseif ($page_action == 'search')  {
		?>
		<table width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td><span class="headerOtherWords">Search Help</span></td>
			</tr>
			<tr>
				<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
			</tr>
			<tr>
				<td class="style9">Enter your terms below and the relevent search option then click search</td>
			</tr>
			<tr>
				<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
			</tr>
			<tr>
				<td width="100%" align="left">
					<table width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
							<td width="100%" align="left">
								<table width="100%" cellspacing="0" cellpadding="0">
									<form action="<?php echo FILENAME_HELP_SYSTEM; ?>?page_action=search_result" method="post">
									<tr>
										<td class="style9">Search Terms: <input type="text" name="search_terms" length="20" /></td>
									</tr>
									<tr>
										<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
									</tr>
									<tr>
										<td class="style9">Show results that match <input type="radio" name="search_type" value="all" CHECKED />&nbsp;All Terms&nbsp;&nbsp;&nbsp;<input type="radio" name="search_type" value="any" />&nbsp;Any Terms.</td>
									</tr>
									<tr>
										<td height="8"><img src="images/pixel_trans.gif" height="8" width="1" /></td>
									</tr>
									<tr>
										<td width="350" align="right"><?php echo tep_create_button_submit('search_help', 'Submit Search'); ?></td>
									</tr>
									</form>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
			} elseif ($page_action == 'search_result') {
				$search_terms = tep_fill_variable('search_terms');
				$search_type = tep_fill_variable('search_type');
				
				$search_min_word = '2';
				
				$terms_explode = explode(' ', $search_terms);
				
				$count = count($terms_explode);
				
				$search_terms_string = '';
				
				$search_string = '';
					for($n = 0, $m = $count; $n < $count; $n++) {
						$terms_explode[$n] = addslashes(strip_tags($terms_explode[$n]));
							if (!empty($search_terms_string)) {
								$search_terms_string .= ' ';
							}
							if (strlen($terms_explode[$n]) > $search_min_word) {
									if (!empty($search_string)) {
										$search_string .= ' ';
									}
									if ($search_type == 'all') {
										$search_string .= '+';
									}
								$search_string .= $terms_explode[$n].'*';
								$search_terms_string .= '<b>'.$terms_explode[$n].'</b>';
							} else {
								$search_terms_string .= $terms_explode[$n];
							}
					}
			?>
			<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td><span class="headerOtherWords">Search Results</span></td>
					</tr>
					<tr>
						<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
					</tr>
					<tr>
						<td class="style9">You searched for "<?php echo $search_terms_string; ?>" (words under 3 characters long were not used).</td>
					</tr>
					<tr>
						<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
					</tr>
					<tr>
						<td width="100%" align="left">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
									<td width="100%" align="left">
										<table width="100%" cellspacing="0" cellpadding="0">
										<?php
											if ($user->user_is_logged()) {
												$extra_string = " or ugtp.user_group_id != '" . $user->fetch_user_group_id() . "'";
												$user_group_id = $user->fetch_user_group_id();
											} else {
												$extra_string = '';
												$user_group_id = '';
											}
										$query = $database->query("select hi.help_item_id, hi.help_group_id, hi.page_id, hid.help_item_name, hid.help_item_description, hgd.help_group_name from " . TABLE_HELP_ITEMS . " hi, " . TABLE_HELP_ITEMS_DESCRIPTION . " hid, " . TABLE_HELP_GROUPS_DESCRIPTION . " hgd, " . TABLE_PAGES . " p left join " . TABLE_USER_GROUPS_TO_PAGES . " ugtp on (hi.page_id = ugtp.page_id" . $extra_string. ") where match(hid.help_item_name, hid.help_item_description) against ('" . $search_string . "' IN BOOLEAN MODE) and hid.help_item_id = hi.help_item_id and hi.help_group_id = hgd.help_group_id and hi.page_id = p.page_id and (p.page_lock_status = '0' or (ugtp.page_id is not NULL and ugtp.user_group_id = '" .$user_group_id . "'))");
										$loop = 0;
											while($result = $database->fetch_array($query)) {
													if (!tep_help_item_exists($result['help_item_id'])) {
														continue;
													}
													if ($loop > 0) {
												?>
													<tr>
														<td height="6"><img src="images/pixel_trans.gif" height="6" /></td>
													</tr>
												<?php
													}
												?>
												<tr>
													<td class="style9"><b><?php echo $result['help_item_name']; ?></b> (<a href="<?php echo FILENAME_HELP_SYSTEM; ?>?help_group_id=<?php echo $result['help_group_id']; ?>&page_action=view_group">Found in <?php echo $result['help_group_name']; ?></a>)</td>
												</tr>
												<tr>
													<td class="style9"><?php echo $result['help_item_description']; ?></td>
												</tr>
												<?php
												$loop++;
											}
											if ($loop == 0) {
												//No results.
												?>
												<tr>
													<td class="style9">We are sorry but there were no Help Items matching those terms.  Please use the button at the bottom of the page to try again.</td>
												</tr>
							
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
		<?php
			} elseif ($page_action == 'view_group')  {
				if (!tep_help_group_has_items($help_group_id)) {
				?>
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td width="100%">There are no available help topics for this page.  Please use the Table of Contents link or use the Search link to search all available help items.</td>
					</tr>
				</table>
				<?php
				} else {
					$group_details = tep_fetch_help_group_details($help_group_id);
				?>
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td><span class="headerOtherWords">Help Items in "<?php echo $group_details['name']; ?>"</span></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td width="100%">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
									<td width="100%" align="left">
										<table width="100%" cellspacing="0" cellpadding="0">
										<?php
											$query = $database->query("select hi.help_item_id, hid.help_item_name, hid.help_item_description from " . TABLE_HELP_ITEMS . " hi, " . TABLE_HELP_ITEMS_DESCRIPTION . " hid where hi.help_group_id = '" . $help_group_id . "' and hi.help_item_id = hid.help_item_id and hid.language_id = '" . $language_id . "'");
											$loop = 0;
												while($result = $database->fetch_array($query)) {
														if (!tep_help_item_exists($result['help_item_id'])) {
															continue;
														}
												?>
													<?php
														if ($loop > 0) {
													?>
														<tr>
															<td height="6"><img src="images/pixel_trans.gif" height="6" /></td>
														</tr>
													<?php
														}
													?>
														<tr>
															<td class="style9"><b><?php echo $result['help_item_name']; ?></b></td>
														</tr>
														<tr>
															<td class="style9"><?php echo $result['help_item_description']; ?></td>
														</tr>
																					
												<?php
													$loop++;
												}
										?>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<?php
				}
			} else { //TOC
				//List all available groups, description and the number of items.\
				?>
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td><span class="headerOtherWords">Help Index</span></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
				<?php
				$query = $database->query("select help_group_id, help_group_name, help_group_description from " . TABLE_HELP_GROUPS_DESCRIPTION . " where language_id = '" . $language_id . "' order by help_group_name");
				$count = 0;
					while($result = $database->fetch_array($query)) {
							if (!tep_help_group_has_items($result['help_group_id'])) {
								continue;
							}
							if ($count > 0) {
							?>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
							</tr>
							<?php
							}
						$items_count = count_help_items_in_group($result['help_group_id']);
						?>
						<tr>
							<td class="main"><b><?php echo $result['help_group_name']; ?></b> (<?php echo $items_count; ?> item<?php echo (($items_count > 1) ? 's' : ''); ?> in category)</td>
						</tr>
						<tr>
							<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
						</tr>
						<tr>
							<td width="100%" align="left">
								<table width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
										<td width="100%" align="left">
											<table width="100%" cellspacing="0" cellpadding="0">
												<tr>
													<td class="style9"><?php echo $result['help_group_description']; ?></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
						</tr>
						<tr>
							<td align="right"><a href="<?php echo FILENAME_HELP_SYSTEM; ?>?help_group_id=<?php echo $result['help_group_id']; ?>&page_action=view_group">View Items in this Category</a></td>
						</tr>
						
						<?php
						$count ++;
					}
		?>
				</table>
		<?php
			}
		$bottom_string = '';
			if ($page_action != 'search') {
				$bottom_string = '<a href="'.FILENAME_HELP_SYSTEM . '?page_action=search">'.tep_create_button_link('search_help', 'Search Help Items').'</a>';
			}
			if ($page_action != 'toc') {
					if (!empty($bottom_string)) {
						$bottom_string .= '&nbsp&nbsp;&nbsp;&nbsp;';
					}
				$bottom_string .= '<a href="'.FILENAME_HELP_SYSTEM . '?page_action=toc">'.tep_create_button_link('view_all_help', 'View all Help Items').'</a>';
			}
		?>
		</td>
	</tr>
	<tr>
		<td height="30"><img src="images/pixel_trans.gif" height="30" width="1" /></td>
	</tr>
	<tr>
		<td width="100%">
			<table width="100%" cellspacing="2" cellpadding="2">
				<tr>
					<td align="left"><a href="<?php echo $page_url; ?>"><?php echo tep_create_button_link('back_to_page', 'Return to Page'); ?></a></td>
					<td align="right"><?php echo $bottom_string; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>