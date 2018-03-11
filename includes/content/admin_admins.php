<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$cID = tep_fill_variable('cID', 'get');
	$new_value = tep_fill_variable('new_value');
	$message = '';
		if (($page_action == 'edit_confirm') && is_numeric($cID)) {
			$database->query("update " . TABLE_CONFIGURATION . " set value = '" . addslashes($new_value) . "' where configuration_id = '" . $cID . "' limit 1");
			
			$message = 'Successfully Updated';
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
			<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
				<tr>
					<td width="40%" class="pageBoxHeading">Configuration Name</td>
					<td width="40%" class="pageBoxHeading">Configuration Value</td>
					<td width="40%" class="pageBoxHeading" align="right">Action</td>
					<td width="10" class="pageBoxHeading"></td>
				</tr>
			<?php
				$query = $database->query("select c.configuration_id, c.value, c.select_type, cd.name, cd.description from " . TABLE_CONFIGURATION . " c, " . TABLE_CONFIGURATION_DESCRIPTION . " cd where c.configuration_id = cd.configuration_id order by cd.name");
					foreach($database->fetch_array($query) as $result){
						if ($result['configuration_id'] == $cID) {
							$cData = $result;
						}
			?>
				<tr>
					<td width="40%" class="pageBoxContent"><?php echo $result['name']; ?></td>
					<td width="40%" class="pageBoxContent"><?php echo $result['value']; ?></td>
					<td width="40%" class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_CONFIG . '?cID='.$result['configuration_id'].'&page_action=edit'; ?>">Edit</a></td>
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
										$string .= '</select>';
									}
							?>
							<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_CONFIG . '?page_action=edit_confirm&cID='.$cID; ?>">
							<tr>
								<td class="pageBoxContent">Value: <?php echo $string; ?></td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><input type="submit" value="Update">&nbsp;&nbsp;</td>
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