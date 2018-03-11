<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$egID = tep_fill_variable('egID', 'get', tep_fill_variable('egID', 'post'));

	$message = '';
	
		if ($page_action == 'edit_confirm') {
			$name = tep_fill_variable('name', 'post');
			$description = tep_fill_variable('description', 'post');
			$selectable = tep_fill_variable('selectable', 'post');
			$cost = tep_fill_variable('cost', 'post');
			$discount = tep_fill_variable('discount', 'post');
			$order_type_id = tep_fill_variable('order_type_id', 'post');
			$free_for_level = tep_fill_variable('free_for_level', 'post');
			$allowed_free = tep_fill_variable('allowed_free', 'post');
				if (empty($name)) {
					$error->add_error('admin_equipment_groups', 'You must enter at least a name for this group.');
					$page_action = 'edit';
				} else {
					$database->query("update " . TABLE_EQUIPMENT_GROUPS . " set name = '" . $name . "', description = '" . $description . "', cost = '" . $cost . "', selectable = '" . $selectable . "', discount = '" . $discount . "', order_type_id = '" . $order_type_id . "', free_for_level = '" . $free_for_level . "', allowed_free = '" . $allowed_free . "' where equipment_group_id = '" . $egID . "' limit 1");
					$message = 'Optional Extra Group Successfully Updated.';
				}
		} elseif ($page_action == 'add_confirm') {
			$name = tep_fill_variable('name', 'post');
			$description = tep_fill_variable('description', 'post');
			$selectable = tep_fill_variable('selectable', 'post');
			$cost = tep_fill_variable('cost', 'post');
			$discount = tep_fill_variable('discount', 'post');
			$order_type_id = tep_fill_variable('order_type_id', 'post');
			$free_for_level = tep_fill_variable('free_for_level', 'post');
			$allowed_free = tep_fill_variable('allowed_free', 'post');
				if (empty($name)) {
					$error->add_error('admin_equipment_groups', 'You must enter at least a name for this group.');
					$page_action = 'edit';
				} else {
					$database->query("insert into " . TABLE_EQUIPMENT_GROUPS . " (name, description, cost, selectable, discount, order_type_id, free_for_level, allowed_free) values ('" . $name . "', '" . $description . "', '" . $cost . "', '" . $selectable . "', '" . $discount . "', '" . $order_type_id . "', '" . $free_for_level . "', '" . $allowed_free . "')");
					$message = 'Group Successfully Added.';
				}
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $egID . "' limit 1");
			
			$egID = '';
			$page_action = '';
		} elseif ($page_action == 'delete') {
			if (tep_count_equipment_in_group($egID) != 0) {
				$error->add_error('admin_equipment_groups', 'This group still has answers assigned to it.  You can not delete it.');
				$page_action = '';
			}
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_equipment_groups')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_equipment_groups'); ?></td>
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
						<td class="pageBoxHeading">Group Name</td>
						<td class="pageBoxHeading" align="center">Items</td>
						<td class="pageBoxHeading" align="center">Order Type</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$egData = array();
					$query = $database->query("select eg.equipment_group_id, eg.name, sl.name as service_level_name, ot.name as order_type_name from " . TABLE_EQUIPMENT_GROUPS . " eg left join " . TABLE_ORDER_TYPES . " ot on (eg.order_type_id = ot.order_type_id) left join " . TABLE_SERVICE_LEVELS . " sl on (eg.service_level_id = sl.service_level_id) order by name");
						foreach($database->fetch_array($query) as $result){
							if ($egID == $result['equipment_group_id']) {
								$egData = $result;
							}
							$count_query = $database->query("select count(equipment_id) as count from " . TABLE_EQUIPMENT . " where equipment_group_id = '" . $result['equipment_group_id'] . "'");
							$count_result = $database->fetch_array($count_query);
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['name']; ?></td>
						<td class="pageBoxContent" align="center"><?php echo $count_result['count']; ?></td>
						<td class="pageBoxContent" align="center"><?php echo (($result['order_type_name'] != NULL) ? $result['order_type_name']: 'None'); ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUPS . '?egID='.$result['equipment_group_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUPS . '?egID='.$result['equipment_group_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
						}
					?>
				</table>
			<?php
				} else {
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUPS . '?page_action='.((is_numeric($egID)) ? 'edit_confirm': 'add_confirm'); ?>">
			<?php
				$edit_message = '';
				$button_value = '';
					if (is_numeric($egID)) {
						$level_query = $database->query("select name, description, cost, selectable, discount, order_type_id, service_level_id, free_for_level, allowed_free from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $egID . "' limit 1");
						$level_result = $database->fetch_array($level_query);
						$edit_message = 'Make your required changes and press "Update" below or press "Cancel" to cancel your changes.';
						$button_value = 'Update';
						?>
						<input type="hidden" name="egID" value="<?php echo $egID; ?>" />
						<?php
					} else {
						$level_result['name'] = '';
						$level_result['description'] = '';
						$level_result['cost'] = 0;
						$level_result['selectable'] = '';
						$level_result['discount'] = '';
						$level_result['order_type_id'] = '';
						$level_result['service_level_id'] = '';
						$edit_message = 'Please enter the details below for your new Optional Extra Group.  When you are done press the "Save" button.';
						$button_value = 'Save';
					}
			?>
			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<?php
						if (!empty($edit_message)) {
					?>
					<tr>
						<td class="pageBoxContent" colspan="2"><?php echo $edit_message; ?></td>
					</tr>
					<?php
						}
					?>
					<tr>
						<td class="pageBoxContent" width="150">Optional Extra Group Name:</td><td class="pageBoxContent"><input type="text" name="name" value="<?php echo $level_result['name']; ?>" />  <i>(This shows up on the Order Pages)</i></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Optional Extra Group Cost:</td><td class="pageBoxContent"><input disabled="true" type="text" name="cost" value="<?php echo $level_result['cost']; ?>" />  <i>(The cost used for this Optional Extra Group)</i></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Order Type:</td><td class="pageBoxContent"><?php echo tep_draw_order_type_pulldown('order_type_id', $level_result['order_type_id'], '', array(array('id' => '', 'name' => 'None'))); ?> <i>(The type of order this group shows up for)</i></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Discount Type:</td><td class="pageBoxContent"><input type="text" disabled="true" name="discount" value="<?php echo $level_result['discount']; ?>" />  <i>(The type of discount, in the format of number,reduced total)</i></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Selectable:</td><td class="pageBoxContent"><input type="text" name="selectable" value="<?php echo $level_result['selectable']; ?>" />  <i>(The number of items in this group that can be selected in one go, leave blank for no limit)</i></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Free for Level:</td><td class="pageBoxContent"><?php echo tep_draw_service_level_pulldown('free_for_level',$level_result['free_for_level'], 'disabled="true"', false, array(array('id' => '0', 'name' => 'None')), false); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Allowed Free:</td><td class="pageBoxContent"><input type="text" disabled="true" name="allowed_free" value="<?php echo $level_result['allowed_free']; ?>" />  <i>(The number of times this can redeemed in an order</i></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2">Optional Extra Group Description: <i>(The description used on the order pages)</i></td>
					</tr>
					<tr>
						<td colspan="2"><?php
						$sBasePath = 'editor/';
						//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;
						$oFCKeditor = new FCKeditor('description') ;
						$oFCKeditor->BasePath = $sBasePath ;
						$oFCKeditor->Value	= $level_result['description'];
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
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php if($button_value=='Update') { echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); } else if ($button_value=='Save') { echo tep_create_button_submit('Save', 'Save', ' name="submit_value"'); }?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUPS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<?php
						}elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" cellpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this Optional Extra Group?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUPS; ?>?egID=<?php echo $egID; ?>&page_action=delete_confirm" method="post"><?php echo tep_create_button_submit('delete', 'Delete Confirm'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EQUIPMENT_GROUPS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
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
							<td class="pageBoxHeading"><b>Optional Extra Group Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a Group or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=edit" method="post">
						<tr>
							<td height="5"><?php echo tep_create_button_submit('create', 'Create'); ?></td>
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
