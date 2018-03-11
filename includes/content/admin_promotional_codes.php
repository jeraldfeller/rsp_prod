<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$pID = tep_fill_variable('pID', 'get', tep_fill_variable('pID', 'post'));
	
	$message = '';

		if ($page_action == 'edit_confirm') {
			$code = tep_fill_variable('code', 'post');
			$valid_start = tep_fill_variable('valid_start', 'post', mktime());
			$valid_end = tep_fill_variable('valid_end', 'post', mktime());
			$max_number = tep_fill_variable('max_number', 'post');
			$discount_type = tep_fill_variable('discount_type', 'post');
			$discount_amount = tep_fill_variable('discount_amount', 'post');
			$valid_start_time_stamp = strtotime($valid_start);
			$valid_end_time_stamp = strtotime($valid_end);
			$database->query("update " . TABLE_PROMOTIONAL_CODES . " set code = '" . $code . "', valid_start = '" . $valid_start_time_stamp . "', valid_end = '" . $valid_end_time_stamp . "', max_number = '" . $max_number . "', discount_type = '" . $discount_type . "', discount_amount = '" . $discount_amount . "' where promotional_code_id = '" . $pID . "' limit 1");
			
			$message = 'Promotional Code Successfully Updated.';
		} elseif ($page_action == 'add_confirm') {
			$code = tep_fill_variable('code', 'post');
			$valid_start = tep_fill_variable('valid_start', 'post', mktime());
			$valid_end = tep_fill_variable('valid_end', 'post', mktime());
			$max_number = tep_fill_variable('max_number', 'post');
			$discount_type = tep_fill_variable('discount_type', 'post');
			$discount_amount = tep_fill_variable('discount_amount', 'post');
			$valid_start_time_stamp = strtotime($valid_start);
			$valid_end_time_stamp = strtotime($valid_end);
			$database->query("insert into " . TABLE_PROMOTIONAL_CODES . " (code, valid_start, valid_end, max_number, discount_type, discount_amount) values ('" . $code . "', '" . $valid_start_time_stamp . "', '" . $valid_end_time_stamp . "', '" . $max_number . "', '" . $discount_type . "', '" . $discount_amount . "')");
				
			$message = 'Promotional Code Successfully Added';
			$action = '';
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_PROMOTIONAL_CODES . " where promotional_code_id = '" . $pID . "' limit 1");
			$pID = '';
			$page_action = '';
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
		<?php
			if (empty($oID)) {
				$where = '';
				$listing_split = new split_page("select promotional_code_id, code, valid_start, valid_end, max_number, discount_type, discount_amount from " . TABLE_PROMOTIONAL_CODES . " order by code", '10', 'promotional_code_id');
					if ($listing_split->number_of_rows > 0) {
		?>	
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Code</td>
						<td class="pageBoxHeading">Valid From</td>
						<td class="pageBoxHeading">Valid To</td>
						<td class="pageBoxHeading">Max Number</td>
						<td class="pageBoxHeading">Discount Type</td>
						<td class="pageBoxHeading">Discount Amount</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$pData = array();
					$query = $database->query($listing_split->sql_query);
					    foreach($database->fetch_array($query) as $result){
							if ($pID == $result['promotional_code_id']) {
								$pData = $result;
							}						
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['code']; ?></td>
						<td class="pageBoxContent"><?php echo date("m/d/Y", $result['valid_start']); ?></td>
						<td class="pageBoxContent"><?php echo date("m/d/Y", $result['valid_end']); ?></td>
						<td class="pageBoxContent"><?php echo $result['max_number']; ?></td>
						<td class="pageBoxContent"><?php echo (($result['discount_type'] == 1) ? 'Amount' : 'Percentage'); ?></td>
						<td class="pageBoxContent"><?php echo $result['discount_amount']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_PROMOTIONAL_CODES . '?pID='.$result['promotional_code_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_PROMOTIONAL_CODES . '?pID='.$result['promotional_code_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
						?>
						<tr>
							<td colspan="8">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> promotional codes)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(10, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
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
								<td class="pageBoxContent" colspan="2">Editing <?php echo $pData['code']; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_PROMOTIONAL_CODES; ?>?page_action=edit_confirm&pID=<?php echo $pID; ?>" method="post">
							<tr>
								<td class="main">Code: </td><td><input type="text" name="code" value="<?php echo $pData['code']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Valid From: </td><td><input type="text" name="valid_start" value="<?php echo date("m/d/Y", $pData['valid_start']); ?>" /> <i>(mm/dd/yyyy)</i></td>
							</tr>
							<tr>
								<td class="main">Valid To: </td><td><input type="text" name="valid_end" value="<?php echo date("m/d/Y", $pData['valid_end']); ?>" /> <i>(mm/dd/yyyy)</i></td>
							</tr>
							<tr>
								<td class="main">Max Number: </td><td><input type="text" name="max_number" value="<?php echo $pData['max_number']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Discount Type: </td><td><?php echo tep_draw_discount_type_pulldown('discount_type', $pData['discount_type']); ?></td>
							</tr>
							<tr>
								<td class="main">Discount Amount: </td><td><input type="text" name="discount_amount" value="<?php echo $pData['discount_amount']; ?>" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2"><input type="submit" value="Update"></form><form action="<?php echo FILENAME_ADMIN_PROMOTIONAL_CODES; ?>" method="post"><input type="submit" value="Cancel" /></form></td>
							</tr>
							
						</table>
						<?php
						}elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this Promotional Code?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_PROMOTIONAL_CODES; ?>?pID=<?php echo $pID; ?>&page_action=delete_confirm" method="post"><input type="submit" value="Delete Confirm" /></form><form action="<?php echo FILENAME_ADMIN_PROMOTIONAL_CODES; ?>" method="post"><input type="submit" value="Cancel" /></form></td>
							</tr>
							
						</table>
					<?php
						}elseif ($page_action == 'add') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent" colspan="2">Adding New Promotional Code</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_PROMOTIONAL_CODES; ?>?page_action=add_confirm" method="post">
							<tr>
								<td class="main">Code: </td><td><input type="text" name="code" value="" /></td>
							</tr>
							<tr>
								<td class="main">Valid From: </td><td><input type="text" name="valid_start" value="" />  <i>(mm/dd/yyyy)</i></td>
							</tr>
							<tr>
								<td class="main">Valid To: </td><td><input type="text" name="valid_end" value="" />  <i>(mm/dd/yyyy)</i></td>
							</tr>
							<tr>
								<td class="main">Max Number: </td><td><input type="text" name="max_number" value="" /></td>
							</tr>
							<tr>
								<td class="main">Discount Type: </td><td><?php echo tep_draw_discount_type_pulldown('discount_type'); ?></td>
							</tr>
							<tr>
								<td class="main">Discount Amount: </td><td><input type="text" name="discount_amount" value="" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2"><input type="submit" value="Create"></form><form action="<?php echo FILENAME_ADMIN_PROMOTIONAL_CODES; ?>" method="post"><input type="submit" value="Cancel" /></form></td>
							</tr>
						</table>
					<?php
					} else {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Promotional Code Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a Promotional Code or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=add" method="post">
						<tr>
							<td height="5"><input type="submit" value="Create" /></td>
						</tr>
						</form>
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