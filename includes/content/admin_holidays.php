<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$sID = tep_fill_variable('sID', 'get', tep_fill_variable('sID', 'post'));

	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	
		if ($page_action == 'edit_confirm') {
			$holiday_year = tep_fill_variable('holiday_year', 'post');
			$holiday_month = tep_fill_variable('holiday_month', 'post');
			$holiday_day = tep_fill_variable('holiday_day', 'post');
			
			$database->query("update " . TABLE_HOLIDAYS . " set holiday_date = '" . $holiday_year . '-' . $holiday_month . '-' . $holiday_day . "', holiday_year = '" . $holiday_year . "' where holiday_id = '" . $sID . "' limit 1");
			$message = 'Holiday Successfully Updated.';
		} elseif ($page_action == 'add_confirm') {
			$holiday_year = tep_fill_variable('holiday_year', 'post');
			$holiday_month = tep_fill_variable('holiday_month', 'post');
			$holiday_day = tep_fill_variable('holiday_day', 'post');
			$database->query("insert into " . TABLE_HOLIDAYS . " (holiday_date, holiday_year) values ('" . $holiday_year . '-' . $holiday_month . '-' .               $holiday_day . "', '" . $holiday_year . "')");
			$message = 'Holiday Successfully Added';
			$action = '';
		} elseif ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_HOLIDAYS . " where holiday_id = '" . $sID . "' limit 1");
			
			$sID = '';
			$page_action = '';
		}
		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_holidays')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_holidays'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
		<?php
				$where = '';
				//Only show dates this year or later.
				$year = date("Y", mktime());
				$listing_split = new split_page("select holiday_id, date_format(holiday_date, '%M %e, %Y') as holiday_string, day(holiday_date) as holiday_day, month(holiday_date) as holiday_month, holiday_year from " . TABLE_HOLIDAYS . " where holiday_year >= '" . $year . "' order by holiday_date", '20', 'holiday_id');
					if ($listing_split->number_of_rows > 0) {
		?>			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Holiday Date</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$sData = array();
					$query = $database->query($listing_split->sql_query);
					    foreach($database->fetch_array($query) as $result){
							if ($sID == $result['holiday_id']) {
								$sData = $result;
							}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $result['holiday_string']; ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_HOLIDAYS . '?sID='.$result['holiday_id'].'&page_action=edit'; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_HOLIDAYS . '?sID='.$result['holiday_id'].'&page_action=delete'; ?>">Delete</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
						}
						?>
						<tr>
							<td colspan="3">
								<table class="normaltable" cellspacing="0" cellpadding="2">
									<tr>
										<td class="smallText"><?php echo $listing_split->display_count('Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> holidays)'); ?></td>
										<td class="smallText" style="text-align: right"><?php echo 'Page: ' . $listing_split->display_links(20, tep_get_all_get_params(array('page', 'info', 'page_action', 'action', 'x', 'y'))); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php
				} else {
					?>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="pageBoxContent">No holidays could be found.  Please use the menu on the right to add one.</td>
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
								<td class="pageBoxHeading" colspan="2">Editing <?php echo $sData['holiday_string']; ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_HOLIDAYS; ?>?page_action=edit_confirm&sID=<?php echo $sID; ?>" method="post">
							<tr>
								<td class="main">Month: </td><td><input type="text" name="holiday_month" value="<?php echo $sData['holiday_month']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Day: </td><td><input type="text" name="holiday_day" value="<?php echo $sData['holiday_day']; ?>" /></td>
							</tr>
							<tr>
								<td class="main">Year: </td><td><input type="text" name="holiday_year" value="<?php echo $sData['holiday_year']; ?>" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2"><?php echo tep_create_button_submit('update', 'Update'); ?><!--<input type="submit" value="Update">--></form><form action="<?php echo FILENAME_ADMIN_HOLIDAYS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
							
						</table>
						<?php
						}elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this Holiday?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_HOLIDAYS; ?>?sID=<?php echo $sID; ?>&page_action=delete_confirm" method="post"><?php echo tep_create_button_submit('delete', 'DeleteConfirm'); ?><!--<input type="submit" value="Delete Confirm" />--></form><form action="<?php echo FILENAME_ADMIN_HOLIDAYS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
							
						</table>
					<?php
						}elseif ($page_action == 'add') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxHeading" colspan="2">Adding New Holiday</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<form action="<?php echo FILENAME_ADMIN_HOLIDAYS; ?>?page_action=add_confirm" method="post">
							<tr>
								<td class="main">Month: </td><td><input type="text" name="holiday_month" value="" /></td>
							</tr>
							<tr>
								<td class="main">Day: </td><td><input type="text" name="holiday_day" value="" /></td>
							</tr>
							<tr>
								<td class="main">Year: </td><td><input type="text" name="holiday_year" value="" /></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right" colspan="2"><?php echo tep_create_button_submit('create', 'Create'); ?></form><form action="<?php echo FILENAME_ADMIN_HOLIDAYS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
							</tr>
						</table>
					<?php
					} else {
					?>
					<table width="250" cellspacing="0" celpadding="0" class="pageBox">
						<tr>
							<td class="pageBoxHeading"><b>Holiday Options</b></td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<tr>
							<td class="pageBoxContent">Click edit to edit a Holiday or press Create to create a new one.</td>
						</tr>
						<tr>
							<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
						</tr>
						<form action="<?php echo PAGE_URL; ?>?page_action=add" method="post">
						<tr>
							<td height="5"><?php echo tep_create_button_submit('create', 'Create'); ?><!--<input type="submit" value="Create" />--></td>
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
