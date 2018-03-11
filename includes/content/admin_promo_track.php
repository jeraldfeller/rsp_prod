<?php
	$page = tep_fill_variable('page_id', 'get');
	$page_action = tep_fill_variable('page_action', 'get');
	$pID = tep_fill_variable('pID', 'get', tep_fill_variable('pID', 'post'));

	$user_id = tep_fill_variable('user_id', 'get');
	$promotional_code_id = tep_fill_variable('promotional_code_id', 'get');
	$search_code = tep_fill_variable('search_code', 'get');

	$message = '';

		
?>
<table width="100%" cellspacing="0" cellpadding="0">

	<tr>
		<td width="100%" valign="top">
		<?php

				$where = '';
				$listing_split = new split_page("select pc.promotional_code_id, pc.code, ud.firstname, ud.lastname, pctu.user_id, pctu.date_added, pctu.order_id from " . TABLE_PROMOTIONAL_CODES . " pc, " . TABLE_PROMOTIONAL_CODES_TO_USERS . " pctu, " . TABLE_USERS_DESCRIPTION . " ud where pc.promotional_code_id = pctu.promotional_code_id and pctu.user_id = ud.user_id" . ((!empty($user_id)) ? " and pctu.user_id = '" . $user_id . "'" : '') . ((!empty($promotional_code_id)) ? " and pc.promotional_code_id = '" . $promotional_code_id . "'" : '') . ((!empty($search_code)) ? " and (pc.code like '%" . $search_code . "' or pc.code like '" . $search_code . "%' or pc.code = '" . $search_code . "')" : '') . " order by pctu.date_added DESC", '20', 'pc.promotional_code_id');
					if ($listing_split->number_of_rows > 0) {
		?>			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Agent Name</td>
						<td class="pageBoxHeading">Promotional Code</td>
						<td class="pageBoxHeading">Date Used</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$rrData = array();
					$query = $database->query($listing_split->sql_query);
					    foreach($database->fetch_array($query) as $result){
							
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['firstname'].' '.$result['lastname']; ?></td>
						<td class="pageBoxContent"><?php echo $result['code']; ?></td>
						<td class="pageBoxContent"><?php echo date("n/d/Y", $result['date_added']); ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_USERS . '?uID='.$result['user_id']; ?>&page_action=edit">View User</a> | <a href="<?php echo FILENAME_ADMIN_ORDERS . '?oID='.$result['order_id']; ?>&page_action=view">View Order</a> | <a href="<?php echo FILENAME_ADMIN_PROMOTIONAL_CODES . '?pID='.$result['promotional_code_id']; ?>&page_action=edit">View Code</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
						}
						?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> promo usages)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php
					} else {
					?>
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td class="pageBoxContent">No used Promotional Codes found.  Please try again.</td>
							
						</tr>
					</table>
					<?php
					}

			?>
			</table>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<form action="<?php echo FILENAME_ADMIN_PROMO_TRACK; ?>" method="get">
							<tr>
								<td width="80"><img src="images/pixel_trans.gif" height="1" width="80" /></td>
								<td width="100%"></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>View Only Agent: </td><td class="main"><?php echo tep_draw_agent_pulldown('user_id', $user_id, '', array(array('id' => '', 'name' => 'Any'))); ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>View Only Code: </td><td class="main"><?php echo tep_draw_promo_pulldown('promotional_code_id', $promotional_code_id, array(array('id' => '', 'name' => 'Any'))); ?></td>
							</tr>
							<tr>
								<td class="main" NOWRAP>Search by Code: </td><td class="main"><input type="text" name="search_code" value="<?php echo $search_code; ?>" /></td>
							</tr>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>