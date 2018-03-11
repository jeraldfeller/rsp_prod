<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$cID = tep_fill_variable('cID', 'get');
	$new_value = tep_fill_variable('new_value');
	$comment = tep_fill_variable('comment');
	$message = '';
		if (($page_action == 'edit_confirm') && is_numeric($cID)) {
			$database->query("update " . TABLE_CONFIGURATION . " set value = '" . addslashes($new_value) . "' where configuration_id = '" . $cID . "' limit 1");
			$database->query("update " . TABLE_CONFIGURATION_DESCRIPTION . " set comment = '" . addslashes($comment) . "' where configuration_id = '" . $cID . "' limit 1");
			$message = 'Successfully Updated';
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
			<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
				<tr>
					<td width="25%" class="pageBoxHeading">Configuration Name</td>
					<td width="25%" class="pageBoxHeading">Configuration Value</td>
					<td width="25%" class="pageBoxHeading">Comment</td>
					<td width="25%" class="pageBoxHeading" align="right">Action</td>
					<td width="10" class="pageBoxHeading"></td>
				</tr>
			<?php
				$query = $database->query("select c.configuration_id, c.value, c.select_type, cd.name, cd.description, cd.comment from " . TABLE_CONFIGURATION . " c, " . TABLE_CONFIGURATION_DESCRIPTION . " cd where c.configuration_id = cd.configuration_id order by cd.name");
					foreach($database->fetch_array($query) as $result){
						
							if ($result['configuration_id'] == $cID) {
								$cData = $result;
							}
						$select_type = $result['select_type'];
							if (!empty($select_type)) {
								$explode = explode(',', $result['select_type']);
									if (isset($explode[0]) && isset($explode[1]) && ($explode[0] == 'equipment_type')) {
										$value_query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $result['value'] . "' limit 1");
										$value_result = $database->fetch_array($value_query);
										
										$result['value'] = $value_result['name'];
									}
							}
			?>
				<tr>
					<td width="25%" class="pageBoxContent"><?php echo $result['name']; ?></td>
					<td width="25%" class="pageBoxContent"><?php echo $result['value']; ?></td>
					<td width="25%" class="pageBoxContent"><?php echo $result['comment']; ?></td>
					<td width="25%" class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_CONFIG . '?cID='.$result['configuration_id'].'&page_action=edit'; ?>">Edit</a></td>
					<td width="10" class="pageBoxContent"></td>
				</tr>
			<?php
					}
				?>
			</table>
		</td>
		<?php
			if (!empty($cID)) {
		?>
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
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading"><b>Editing <?php echo $cData['name']; ?></b></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent"><?php echo $cData['description']; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								$string = '';
								
									if ($cData['select_type'] == 'input') {
										$string = '<input type="text" name="new_value" value="' . $cData['value'] . '">';
									
									} else {
										$explode = explode(',', $cData['select_type']);
										$string .= '<select name="new_value">';
											if (isset($explode[0]) && ($explode[0] == 'equipment_type')) {
												$equipment_type = ((isset($explode[1])) ? $explode[1] : '');
												
												$items_query = $database->query("select equipment_id, name from " . TABLE_EQUIPMENT . " where equipment_type_id = '" . $equipment_type . "' order by name");
													foreach($database->fetch_array($items_query) as $items_result){
															if ($items_result['equipment_id'] == $cData['value']) {
																$selected = ' SELECTED';
															} else {
																$selected = '';
															}
														$string .= '<option value="' . $items_result['equipment_id'] . '"' . $selected . '>'.$items_result['name'].'</option>';
													}
											} else {
												$count = count($explode);
												$n = 0;
													while ($n < $count) {
														$selected = '';
															if ($explode[$n] == $cData['value']) {
																$selected = ' SELECTED';
															}
														$string .= '<option value="' . $explode[$n] . '"' . $selected . '>'.$explode[$n].'</option>';
														$n++;
													}
											
											}
										$string .= '</select>';
									}
							?>
							<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_CONFIG . '?page_action=edit_confirm&cID='.$cID; ?>">
							<tr>
								<td class="pageBoxContent">Value: <?php echo $string; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Comment:</td>
							</tr>
							<tr>
								<td class="pageBoxContent"><textarea name="comment"><?php echo $cData['comment']; ?></textarea></td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update">-->&nbsp;&nbsp;</td>
							</tr>
							</form>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<?php
			}
		?>
	</tr>
</table>