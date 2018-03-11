<?php

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="main">&PAGE_TEXT</td>
	</tr>
	<tr>
		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
		<td valign="top" width="100%">
		<form name="change_password" method="post" action="<?php echo FILENAME_ACCOUNT_CHANGE_PASSWORD; ?>?change_password=update">
			<table width="100%" cellspacing="0" cellpadding="0" class="pageBox">
				<?php
					$query = $database->query("select count(eto.equipment_to_order_id) as count from " . TABLE_EQUIPMENT_TO_ORDERS . " eto, " . TABLE_ORDERS . " o where o.user_id = '" . $user->fetch_user_id() . "' and o.order_id = eto.order_id and (eto.equipment_status_id = '1' or eto.equipment_status_id = '2')");
					$result = $database->fetch_array($query);
						if ($result['count'] > 0) {
					?>
					<tr>
						<td class="pageBoxContent">Equipment Name</td>
						<td class="pageBoxContent">Total Installed</td>
						<td class="pageBoxContent">Total Pending</td>
					</tr>
					<?php
						$query = $database->query("select e.equipment_id, e.name from " . TABLE_EQUIPMENT_TO_ORDERS . " eto, " . TABLE_ORDERS . " o, " . TABLE_EQUIPMENT . " e where o.user_id = '" . $user->fetch_user_id() . "' and o.order_id = eto.order_id and eto.equipment_id = e.equipment_id and (eto.equipment_status_id = '1' or eto.equipment_status_id = '2') group by eto.equipment_name");
							while($result = $database->fetch_array($query)) {
								$in_count_query = $database->query("select count(equipment_to_order_id) as count from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_id = '" . $result['equipment_id'] . "' and equipment_status_id = '2'");
								$in_count_result = $database->fetch_array($in_count_query);
								
								$pe_count_query = $database->query("select count(equipment_to_order_id) as count from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_id = '" . $result['equipment_id'] . "' and equipment_status_id = '1'");
								$pe_count_result = $database->fetch_array($pe_count_query);
						?>
						<tr>
							<td class="pageBoxContent"><?php echo $result['name']; ?></td>
							<td class="pageBoxContent"><?php echo $in_count_result['count']; ?></td>
							<td class="pageBoxContent"><?php echo $pe_count_result['count']; ?></td>
						</tr>
						<?php
							}
					
					?>
					<?php
						} else {
					?>
					<tr>
						<td width="100%" class="pageBoxContent">There is currently no equipment assigned to any of your addresses.</td>
					</tr>
					<?php
						}
				?> 
				<tr>
					<td class="pageBoxHeader">

					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>