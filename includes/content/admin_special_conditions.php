<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$scID = tep_fill_variable('scID', 'get', tep_fill_variable('scID', 'post'));

	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	
		if ($page_action == 'edit_confirm') {
			$name = tep_fill_variable('name', 'post');
			$installation_cost = tep_fill_variable('installation_cost', 'post');
			
			$database->query("update " . TABLE_SPECIAL_INSTALLATION_COSTS . " set name = '" . $name . "', installation_cost = '" . $installation_cost . "' where special_installation_cost_id = '" . $scID . "' limit 1");
			$message = 'Special Condition Successfully Updated.';
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
		<?php
				$where = '';
				$listing_split = new split_page("select special_installation_cost_id, name, installation_cost from " . TABLE_SPECIAL_INSTALLATION_COSTS . " order by name", '20', 'special_installation_cost_id');
					if ($listing_split->number_of_rows > 0) {
		?>			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Name</td>
						<td class="pageBoxHeading">Installation Cost</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$sData = array();
					$query = $database->query($listing_split->sql_query);
						while($result = $database->fetch_array($query)) {
							if ($scID == $result['special_installation_cost_id']) {
								$sData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['name']; ?></td>
						<td class="pageBoxContent">$<?php echo $result['installation_cost']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_SPECIAL_CONDITIONS . '?scID='.$result['special_installation_cost_id'].'&page_action=edit'; ?>">Edit</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
						}
						?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php //echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> states)'); ?></td>
										<td class="smallText" style="text-align: right"><?php //echo 'Page: ' . $listing_split->display_links(10, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php
					}
			?>
				</table>
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
								<td class="pageBoxContent" colspan="2">Editing <?php echo $sData['name']; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_SPECIAL_CONDITIONS; ?>?page_action=edit_confirm&scID=<?php echo $scID; ?>" method="post">
							<tr>
								<td class="main">Name: </td><td><input type="text" name="name" value="<?php echo $sData['name']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Installation Payment: $</td><td><input type="text" name="installation_cost" value="<?php echo $sData['installation_cost']; ?>" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_SPECIAL_CONDITIONS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
							<td class="pageBoxHeading"><b>Special Condition Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a Special Condition.</td>
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