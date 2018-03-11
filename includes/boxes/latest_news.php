<?php
	$user_group_id = '';
	
		if ($user->user_is_logged()) {
			$user_group_id = $user->fetch_user_group_id();
		}
		if (tep_count_news_items($user_group_id) > 0) {
			$query = $database->query("select ni.news_item_id, ni.date_added, nid.news_item_name, nid.news_item_description from " . TABLE_NEWS_ITEMS . " ni, " . TABLE_NEWS_ITEMS_DESCRIPTION . " nid where (ni.user_group_id = '0'" . ((!empty($user_group_id)) ? " or ni.user_group_id = '" . $user_group_id . "'": '') . ") and ni.news_item_id = nid.news_item_id order by ni.date_added DESC limit 1");
			$result = $database->fetch_array($query);
				//Time to truncate.
				if (strlen($result['news_item_description']) > MAX_LATEST_NEWS_LENGTH) {
					$news_item_description = substr($result['news_item_description'], 0, MAX_LATEST_NEWS_LENGTH).'... <a href="'.FILENAME_VIEW_NEWS .'?news_item_id='.$result['news_item_id'].'">[read more]</a>';
				} else {
					$news_item_description = $result['news_item_description'];
				}
	?>
		<tr>
			<td width="3"><img src="images/pixel_trans.gif" height="1" width="3" /></td>
			<td width="1%" height="33" background="images/body_r9_c6.jpg"></td>
			<td width="95%" background="images/body_r9_c6.jpg" class="style4">Latest <span class="style5">News</span></td>
			<td width="3"><img src="images/pixel_trans.gif" height="1" width="3" /></td>
		</tr>
		<tr>
			<td height="10" colspan="4"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="5" valign="top"><img src="images/pixel_trans.gif" height="1" width="5" /></td>
						<td width="100%" valign="top">
							<table width="100%" cellspacing="2" cellpadding="2">
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
								</tr>
								<tr>
									<td class="style6" align="left"><b><?php echo $result['news_item_name']; ?> </b>(<?php echo date("n/d/Y", $result['date_added']); ?>)</td>
								</tr>
								<tr>
									<td height="3" align="left"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
								</tr>
								<tr>
									<td width="100%" align="left">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td width="10"><img src="images/pixel_trans.gif" height="1" width="5" /></td>
												<td align="left" width="100%" class="main news"><?php echo $news_item_description; ?></td>
											</tr>
											<tr>
												<td height="3" align="left"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
											</tr>
											<tr>
												<td width="10"><img src="images/pixel_trans.gif" height="1" width="5" /></td>
												<td width="100%" align="right" class="style6"><a class="news" href="<?php echo FILENAME_VIEW_NEWS; ?>">Read All New News Items</a></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td width="5" valign="top"><img src="images/pixel_trans.gif" height="1" width="5" /></td>
						<td width="149" align="right" valign="top">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td width="46"><img src="images/pixel_trans.gif" height="1" width="46" /></td>
									<td width="103" valign="top"><img src="images/img2.jpg" width="103" height="101" /></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td>&nbsp;</td>
		</tr>
	<?php
		}
?>
