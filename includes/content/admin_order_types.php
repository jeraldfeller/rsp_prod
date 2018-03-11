<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$otID = tep_fill_variable('otID', 'get', tep_fill_variable('otID', 'post'));

	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	
		if ($page_action == 'edit_confirm') {
			$name = tep_fill_variable('name', 'post');
			$installation_cost = tep_fill_variable('installation_cost', 'post');
			
			$database->query("update " . TABLE_ORDER_TYPES . " set name = '" . $name . "', installation_cost = '" . $installation_cost . "' where order_type_id = '" . $otID . "' limit 1");
			$message = 'Order Type Successfully Updated.';
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
		<?php
			if (empty($oID)) {
				$where = '';
				$listing_split = new split_page("select order_type_id, name, installation_cost from " . TABLE_ORDER_TYPES . " order by name", '20', 'order_type_id');
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
							if ($otID == $result['order_type_id']) {
								$sData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['name']; ?></td>
						<td class="pageBoxContent">$<?php echo $result['installation_cost']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_ORDER_TYPES . '?otID='.$result['order_type_id'].'&page_action=edit'; ?>">Edit</a></td>
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
							<form action="<?php echo FILENAME_ADMIN_ORDER_TYPES; ?>?page_action=edit_confirm&otID=<?php echo $otID; ?>" method="post">
							<tr>
								<td class="main">Name: </td><td><input type="text" name="name" value="<?php echo $sData['name']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Installation Cost: $</td><td><input type="text" name="installation_cost" value="<?php echo $sData['installation_cost']; ?>" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_ORDER_TYPES; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
							<td class="pageBoxHeading"><b>Order Type Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit an Order Type.</td>
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