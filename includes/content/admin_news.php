<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$nID = tep_fill_variable('nID', 'get');
	$submit_value = tep_fill_variable('submit_value', 'post');

	$message = '';
	$pages = tep_fill_variable('pages', 'post', array());
	
		if (!empty($submit_value)) {
			
			$user_group_id = tep_fill_variable('user_group_id', 'post');
			$news_item_name = tep_fill_variable('news_item_name', 'post');
			$news_item_description = tep_fill_variable('news_item_description', 'post');

			//Check if this is an insert or a update.
				if (is_numeric($nID)) {
					$database->query("update " . TABLE_NEWS_ITEMS . " set user_group_id = '" . $user_group_id . "' where news_item_id = '" . $nID . "' limit 1");
					$database->query("update " . TABLE_NEWS_ITEMS_DESCRIPTION . " set news_item_name = '" . $news_item_name . "', news_item_description = '" . $news_item_description . "' where news_item_id = '" . $nID . "'");
				
					$message = 'News item successfully updated.';
					$page_action = '';
				} else {
					$database->query("insert into " . TABLE_NEWS_ITEMS . " (user_group_id, date_added) values ('" . $user_group_id . "', '" . mktime() . "')");
					$nID = $database->insert_id();

					$database->query("insert into " . TABLE_NEWS_ITEMS_DESCRIPTION . " (news_item_id, news_item_name, news_item_description) values ('" . $nID . "', '" . $news_item_name . "', '" . $news_item_description . "')");
							
					$nID = '';
					$page_action = '';
				}
			
			
		}
		
		if ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_NEWS_ITEMS . " where news_item_id = '" . $nID . "'");
			$database->query("delete from " . TABLE_NEWS_ITEMS_DESCRIPTION . " where news_item_id = '" . $nID . "'");
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_pages')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_pages'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if (($page_action != 'edit')) {
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">News Item Name</td>
						<td class="pageBoxHeading" align="center">Viewable By</td>
						<td class="pageBoxHeading" align="center">Date Added</td>
						<td class="pageBoxHeading" align="center" width="75">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$uData = array();
					$query = $database->query("select n.news_item_id, n.date_added, nd.news_item_name, ug.name as user_group_name from " . TABLE_NEWS_ITEMS . " n left join " . TABLE_USER_GROUPS . " ug on (n.user_group_id = ug.user_group_id), " . TABLE_NEWS_ITEMS_DESCRIPTION . " nd where n.news_item_id = nd.news_item_id order by n.date_added");
						foreach($database->fetch_array($query) as $result){
							$item_name = 'No Name';
								if (!empty($result['news_item_name'])) {
									if (strlen($result['news_item_name']) > 40) {
										$item_name = substr($result['news_item_name'], 0, 40) .'...';
									} else {
										$item_name = $result['news_item_name'];
									}
								}
							$group_name = 'All Users';
								if (($result['user_group_name'] != NULL) && !empty($result['user_group_name'])) {
									$group_name = $result['user_group_name'];
								}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $item_name; ?></td>
						<td class="pageBoxContent" align="center"><?php echo $group_name; ?></td>
						<td class="pageBoxContent" align="center"><?php echo date("n/d/Y", $result['date_added']); ?></td>
						<td class="pageBoxContent" align="center"><a href="<?php echo FILENAME_ADMIN_NEWS . '?nID='.$result['news_item_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_NEWS . '?nID='.$result['news_item_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
					?>
				</table>
			<?php
				} else {
					//Edit
					if (!is_numeric($nID)) {
						?>
						<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEWS . '?page_action=edit'; ?>">
						<?php
						$page_result = array('user_group_id' => tep_fill_variable('user_group_id', 'post'),
														 'news_item_name' => tep_fill_variable('news_item_name', 'post'),
														 'news_item_description' => tep_fill_variable('news_item_description', 'post'));
						$edit_message = 'Insert your required information and press "Insert" below or press "Cancel" to cancel your changes.';
						$button_text = 'create';
					} else {
						?>
						<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_NEWS . '?page_action=edit&nID='.$nID; ?>">
						<?php
						$page_query = $database->query("select n.user_group_id, nd.news_item_name, nd.news_item_description from " . TABLE_NEWS_ITEMS . " n, " . TABLE_NEWS_ITEMS_DESCRIPTION . " nd where n.news_item_id = '" . $nID . "' and n.news_item_id = nd.news_item_id limit 1");
						$page_result = $database->fetch_array($page_query);
						$edit_message = 'Make your required changes and press "Update" below or press "Cancel" to cancel your changes.';
						$button_text = 'update';
					}
			?>
			<input type="hidden" name="submit_value" value="1" />
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxContent" width="130">Viewable by users:</td><td class="pageBoxContent"><?php echo tep_draw_group_pulldown('user_group_id', $page_result['user_group_id'], '', array(array('id' => '0', 'name' => 'Any'))); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="130">News Item Name:</td><td class="pageBoxContent"><input type="text" name="news_item_name" length="20" value="<?php echo $page_result['news_item_name']; ?>" /></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2">News Item Content:</td>
					</tr>
					<tr>
						<td colspan="2"><?php
						$sBasePath = 'editor/';
						//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;
						$oFCKeditor = new FCKeditor('news_item_description') ;
						$oFCKeditor->BasePath = $sBasePath ;
						$oFCKeditor->Value	= $page_result['news_item_description'];
						$oFCKeditor->Height	= '400';
						$oFCKeditor->Create() ;
						?></td>
					</tr>

				</table>
			<?php
				}
			?>
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
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent"><?php echo $edit_message; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellpadding="2" cellspacing="2">
										<tr>
											<td align="left"><?php echo tep_create_button_submit($button_text, ucfirst($button_text).' News Item'); ?></form></td>
											<td align="right"><a href="<?php echo FILENAME_ADMIN_NEWS.'?nID='.$nID.'&page_action=view'; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></td>
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
								<td class="pageBoxContent">Are you sure you wish to delete this News Item?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellpadding="2" cellspacing="2">
										<tr>
											<td align="left"><a href="<?php echo FILENAME_ADMIN_NEWS; ?>?nID=<?php echo $nID; ?>&page_action=delete_confirm"><?php echo tep_create_button_link('delete', 'Delete this News Item'); ?></a></td>
											<td align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUPS; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></td>
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
					<td class="pageBoxHeading"><b>News Options</b></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td class="pageBoxContent">Click edit to edit an item or click Create to make a new one.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td height="5"><a href="<?php echo PAGE_URL; ?>?page_action=edit"><?php echo tep_create_button_link('create', 'Create New'); ?></a></td>
				</tr>
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
