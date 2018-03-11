<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="main">&PAGE_TEXT</td>
	</tr>
	<tr>
		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
	</tr>
	<tr>
		<td width="100%">
			<table cellspacing="0" cellpadding="0" class="pageBox" width="100%">
				<?php
						if ($user->user_is_logged()) {
							$user_group_id = $user->fetch_user_group_id();
						} else {
							$user_group_id = '';
						}
					$news_item_id = tep_fill_variable('news_item_id', 'get');
						if (!empty($news_item_id)) {
							//Show the ful item.
							$query = $database->query("select n.news_item_id, n.date_added, nd.news_item_name, nd.news_item_description from " . TABLE_NEWS_ITEMS . " n, " . TABLE_NEWS_ITEMS_DESCRIPTION . " nd where n.news_item_id = '" . $news_item_id . "' and (n.user_group_id = '0'" . ((!empty($user_group_id)) ? " or n.user_group_id = '" . $user_group_id . "'": '') . ") and n.news_item_id = nd.news_item_id limit 1");
							$result = $database->fetch_array($query);
								if (!empty($result['news_item_id'])) {
								?>
								<tr>
									<td class="main"><b><?php echo $result['news_item_name']; ?></b> (added on <?php echo date("n/d/Y", $result['date_added']); ?>)</td>
								</tr>
								<tr>
									<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
								</tr>
								<tr>
									<td width="100%" align="left">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td width="10"><img src="images/pixel_trans.gif" height="1" width="10" /></td>
												<td class="style9"><?php echo $result['news_item_description']; ?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td height="8"><img src="images/pixel_trans.gif" height="8" width="1" /></td>
								</tr>
								<tr>
									<td class="style9" align="right" width="100%"><a href="<?php echo FILENAME_VIEW_NEWS; ?>">View All News Items</a></td>
								</tr>
								<?php
								} else {
								?>
								<tr>
									<td class="style9">Sorry no news item could be found.</td>
								</tr>
								<tr>
									<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
								</tr>
								<tr>
									<td class="style9"><a href="<?php echo FILENAME_VIEW_NEWS; ?>">View All News Items</a></td>
								</tr>
								<?php
								}
						} else {
							//Show the last 10 items with links to the full listing.
							$query = $database->query("select ni.news_item_id, ni.date_added, nid.news_item_name, nid.news_item_description from " . TABLE_NEWS_ITEMS . " ni, " . TABLE_NEWS_ITEMS_DESCRIPTION . " nid where (ni.user_group_id = '0'" . ((!empty($user_group_id)) ? " or ni.user_group_id = '" . $user_group_id . "'": '') . ") and ni.news_item_id = nid.news_item_id order by ni.date_added DESC limit 10");
							$count = 0;
								while($result = $database->fetch_array($query)) {
									//Time to truncate.
									if (strlen($result['news_item_description']) > MAX_LATEST_NEWS_LENGTH) {
										$news_item_description = substr($result['news_item_description'], 0, MAX_LATEST_NEWS_LENGTH).'... <a href="'.FILENAME_VIEW_NEWS .'?news_item_id='.$result['news_item_id'].'">[read more]</a>';
									} else {
										$news_item_description = $result['news_item_description'];
									}
								?>
								<?php
									if ($count > 0) {
								?>
								<tr>
									<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
								</tr>
								<?php
									}
								?>
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
												<td align="left" width="100%" class="style9"><?php echo $news_item_description; ?></td>
											</tr>
											<tr>
												<td height="3" align="left"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
											</tr>
										</table>
									</td>
								</tr>
								<?php
									$count++;
								}
						}
				?>
			</table>
		</td>
	</tr>
</table>