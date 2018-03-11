<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$oID = tep_fill_variable('oID', 'get');
	$order_edit = tep_fill_variable('order_edit', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '');
	$order_type = tep_fill_variable('order_type', 'get', '');

	$message = '';
	$page_number = tep_fill_variable('page_number', 'post', 1);
	if(isset($_POST['submit']))
		{
      $housenumber=$_POST['housenumber'];
			$street=$_POST['street'];
			$city=$_POST['city'];
			$country=$_POST['country'];
			$state=$_POST['state'];
			$zip=$_POST['zip'];
			$cross=$_POST['cross'];
			$query = $database->query("update " . TABLE_ADDRESSES . " set house_number='".$housenumber. "',street_name='".$street. "', city='".$city . "', zip='". $zip . "', cross_street_directions='". $cross . "'  where address_id = '" . $oID . "' "   );
			//$query = $database->query("update " . TABLE_STATES  . " set name='".$state. "'"   );
			//$query = $database->query("update " . TABLE_COUNTYS  . " set name='".$country. "'"   );
        echo "The address is updated";

			}
else{
?>
<form name="form1" method="POST">
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('order_edit')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_users'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if (empty($oID)) {
					$where = '';
					$listing_split = new split_page("select o.order_id, o.order_total, ot.name as order_type_name, os.order_status_name, a.house_number, a.street_name, a.city from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ADDRESSES . " a where o.user_id = '" . $user->fetch_user_id() . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id " . $where . " order by o.date_added DESC", '20', 'o.order_id');
						if ($listing_split->number_of_rows > 0) {
				?>
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td class="pageBoxHeading">Order Id</td>
							<td class="pageBoxHeading" align="center">Address</td>
							<td class="pageBoxHeading" align="center">Order Total</td>
							<td class="pageBoxHeading" align="center">Order Type</td>
							<td class="pageBoxHeading" align="center">Order Status</td>
							<td class="pageBoxHeading" align="right">Action</td>
							<td width="10" class="pageBoxHeading"></td>
						</tr>
					<?php
						$query = $database->query($listing_split->sql_query);
							while($result = $database->fetch_array($query)) {
								
					?>
						<tr>
							<td class="pageBoxContent">&nbsp;&nbsp;&nbsp;<?php echo $result['order_id']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $result['house_number']; ?> <?php echo $result['street_name']; ?>, <?php echo $result['city']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $result['order_total']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $result['order_type_name']; ?></td>
							<td class="pageBoxContent" align="center"><?php echo $result['order_status_name']; ?></td>
							<td class="pageBoxContent" align="right"><a href="order_edit.php?oID=<?php echo $result['order_id']?>&page_action=edit">Edit</a></td>
							<td width="10" class="pageBoxContent"></td>
						</tr>
					<?php
							}
						}
					?>
				</table>
			<?php
				} else {
					$query = $database->query("select o.order_total, o.date_added, o.date_schedualed, o.last_modified, o.date_completed, o.order_status_id, a.house_number, a.street_name, a.city, a.zip, a.zip4, a.cross_street_directions, c.name as county_name, s.name as state_name, os.order_status_name, ot.name as order_type_name, od.number_of_posts, od.special_instructions from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_ADDRESSES . " a, " . TABLE_COUNTYS . " c, " . TABLE_STATES . " s, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDER_TYPES . " ot where o.order_id = '" . $oID . "' and o.order_id = od.order_id and o.address_id = a.address_id and a.county_id = c.county_id and a.state_id = s.state_id and o.order_status_id = os.order_status_id and o.order_type_id = ot.order_type_id limit 1");
					$result = $database->fetch_array($query);
			?>
			
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Address Information</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Address</td><td class="pageBoxContent"><input type="text" name="housenumber" value="<?php echo $result['house_number']; ?>">, <input type text="text" name="street" value="<?php echo $result['street_name']; ?>"></td>
					</tr>
					<tr>
						<td class="pageBoxContent">City</td><td class="pageBoxContent"><input type="text" name="city" value="<?php echo $result['city']; ?>"></td>
					</tr>
					<tr>
						<td class="pageBoxContent">County</td><td class="pageBoxContent"><input type="text" name="country" value="<?php echo $result['county_name']; ?>"></td>
					</tr>
					<tr>
						<td class="pageBoxContent">State</td><td class="pageBoxContent"><input type="text" name="state" value="<?php echo $result['state_name']; ?>"></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Zip</td><td class="pageBoxContent"><input type="text" name="zip" value="<?php echo $result['zip']; ?>"></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Cross Street/Directions</td>
						<td class="pageBoxContent"><input type="text" name="cross" value="<?php echo $result['cross_street_directions']; ?>"></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Job Description</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Order Type</td><td class="pageBoxContent"><?php echo $result['order_type_name']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Job Total</td><td class="pageBoxContent"><?php echo $result['order_total']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Date Added</td><td class="pageBoxContent"><?php echo date("n/d/Y", $result['date_added']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Last Modified</td><td class="pageBoxContent"><?php echo (($result['last_modified'] > 0) ? date("n/d/Y", $result['last_modified']) : 'Never'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Agent Scheduled Date</td><td class="pageBoxContent"><?php echo (($result['date_schedualed'] > 0) ? date("n/d/Y", $result['date_schedualed']) : 'Never'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Number of Posts</td><td class="pageBoxContent"><?php echo $result['number_of_posts']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Special Instructions</td>
						<td class="pageBoxContent"><?php echo $result['special_instructions']; ?></td>
						<td class="pageBoxContent"><input type="submit" value="Update" name="submit"></td>
					</tr>
					<tr>
						<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
					</tr>
					<tr>
						<td colspan="2" class="pageBoxContent"><b>Order History</b></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<?php
						$history_query = $database->query("select oh.date_added, oh.user_notified, oh.comments, os.order_status_name from " . TABLE_ORDERS_HISTORY . " oh, " . TABLE_ORDERS_STATUSES . " os where oh.order_id = '" . $oID . "' and oh.order_status_id = os.order_status_id order by oh.date_added DESC");
							while($history_result = $database->fetch_array($history_query)) {
					?>
					<tr>
						<td class="pageBoxContent" colspan="2">Date: <?php echo date("n/d/Y", $history_result['date_added']); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2">Status: <?php echo $history_result['order_status_name']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2">Comments: </td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2"><?php echo $history_result['comments']; ?></td>
					</tr>
					<tr>
						<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
					</tr>
					<?php
							}
					?>					
				</table>
			<?php
				}
			?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
		<?php
			if (!empty($oID)) {
		?>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Press cancel to go back to the previous page.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><a  method="post"><a href="admin_orders.php"> <?php echo tep_create_button_link('cancel', 'Cancel'); ?></a></form></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
			}
			}
		?>
		</td>
	</tr>
</form>