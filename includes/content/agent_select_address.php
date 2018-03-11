<?php
/*
	agent_select_address.php
	Allows agents to select an address from an active addresses list.
	This means that they are able to see what addresses currently have signposts 
*/
	$page_action = tep_fill_variable('page_action', 'get');
	$aID = tep_fill_variable('aID', 'get');
	$order_view = tep_fill_variable('order_view', 'get', 'open');
	$order_status = tep_fill_variable('order_status', 'get', '');
	$order_type = tep_fill_variable('order_type', 'get', '');
	$job_start_date = tep_fill_variable('job_start_date', 'post', '');
	$show_house_number = tep_fill_variable('show_house_number', 'get', '');
	$show_street_name = tep_fill_variable('show_street_name', 'get', '');
	$show_city = tep_fill_variable('show_city', 'get', '');
	$sort_by = tep_fill_variable('sort_by', 'get', 'number');

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
			<?php
					$listing_split = new split_page("select a.address_id,  a.house_number, a.street_name, a.city, a.zip, a.status, s.name as state_name, c.name as county_name, a.zip4, a.status from " . TABLE_ADDRESSES . " a, " . TABLE_ADDRESSES_TO_USERS . " atu, " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c left join " . TABLE_ORDERS . " o on (a.address_id = o.address_id and o.order_type_id = '3' and o.order_status_id != '3') left join " . TABLE_ORDERS . " ow on (a.address_id = ow.address_id and ow.order_type_id = '1' and ow.order_status_id != '4') where atu.user_id = '" . $user->fetch_user_id() . "' and atu.address_id = a.address_id and a.state_id = s.state_id " . (!empty($show_house_number) ? " and a.house_number = '" . $show_house_number . "'" : '') . " " . (!empty($show_street_name) ? " and (a.street_name = '" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "' or a.street_name like '%" . $show_street_name . "%' or a.street_name like '" . $show_street_name . "%')" : '') . " " . (!empty($show_city) ? " and (a.city = '" . $show_city . "' or a.city like '%" . $show_city . "' or a.city like '%" . $show_city . "%' or a.city like '" . $show_city . "%')" : '') . " and a.county_id = c.county_id and (o.order_status_id != '3' or (o.order_id is NULL and a.status < '3')) order by " . (($sort_by == 'number') ? 'a.house_number' : (($sort_by == 'street') ? 'a.street_name' : 'a.city')) . " ASC", '20', 'a.address_id');
						if ($listing_split->number_of_rows > 0) {
				?>
					<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
						<tr>
							<td class="pageBoxContent" align="left" valign="top">&nbsp;</td>
						</tr>
					<?php
							$select_addresses = "<select name='address'>";
							
							$query = $database->query($listing_split->sql_query);
							while($result = $database->fetch_array($query)) {
								$select_addresses .= "<option value='".$result['address_id']."'>".$result['house_number'].' '.$result['street_name'].' '.$result['city'].' '.$result['state_name']."</option>";
					?>
					<?php
							}
							
							$select_addresses .= "</select>";
							?>
						<tr>
							<td class="pageBoxContent" align="left" valign="top"><?php echo $select_addresses; ?></td>
						</tr>
					</table>
					<?php
						}
					?>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">&nbsp;</td>
	</tr>
</table>