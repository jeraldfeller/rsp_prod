<?php
	include('includes/application_top.php');

	$selected_agent_id = tep_fill_variable('agent_id', 'session');
	//$selected_agent_id = $selected_agent_id;
	$aom_id = tep_fill_variable('user_id', 'session');
	$aID = tep_fill_variable('aID', 'get');
	$order_type = 3;

	if (!$aID) {
		tep_redirect(FILENAME_AOM_ACTIVE_ADDRESSES);
		exit();
	}

	$query = $database->query("select u.user_id, u.agent_id, u.email_address, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $selected_agent_id . "' and u.user_id = ud.user_id limit 1");
	$result = $database->fetch_array($query);
	
	$order_id = tep_fill_variable('order_id', 'get');

	$order = new orders('fetch', $order_id);
	$data = $order->fetch_order();

	$agent_name = $result['firstname'].' '.$result['lastname'];		
	$agency_name = $result['name'];					
	$house_number = $data['house_number'];		
	$street_name = $data['street_name'];
	$date_added = date("F j, Y, g:i a", $data['date_added']);
	$city = $data['city'];
	$date_schedualed = $data['date_schedualed'];
	$special_instructions = $data['special_instructions'];
	$number_of_posts = $data['number_of_posts'];
	$cross_street_directions = $data['cross_street_directions'];
	$county_name = tep_get_county_name($data['county_id']);
	$state_name = tep_get_state_name($data['state_id']);
    $zip = $data['zip'];
	$zip4 = $data['zip4'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Success</title>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<meta name="keywords" content="" />
<meta name="description" content="" />
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style1 {
	color: #FFFFFF;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
}
.style2 {
	color: #000000;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style4 {
	font-size: 17px;
	color: #000000;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {color: #0099FF}
.style6 {
	color: #000000;
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
}
-->
</style></head>

<body onLoad="window.print();">
<table width="80%" cellspacing="0" cellpadding="0" align="center">
   <tr>
		<td align="center"><img name="head_r2_c2" src="https://realtysignpost.com/dist/css/header-small.png" width="310" height="98" border="0" id="head_r2_c2" alt="" /></td>
	</tr>
	<tr>
		<td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
	  <td valign="top" align="center"><span class="headerFirstWord">Removal Confirmation</span> </td>
	</tr>
	<tr>
		<td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
		<td width="100%">
			<table width="100%" cellspacing="0" cellpadding="2" class="pageBox">
				<tr>
					<td class="mainLarge">Agent Information</td>
				</tr>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td class="main" width="140"><b>Agent Name:</b> </td><td class="main"><b><?php echo $agent_name; ?></b></td>
							</tr>
							<tr>
								<td class="main" width="140"><b>Agency Name:</b> </td><td class="main"><b><?php echo $agency_name; ?></b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td class="mainLarge">Address Information</td>
				</tr>
				<tr>
					<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
				</tr>
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td class="main" width="140"><b>Activity Requested:</b> </td><td class="main"><b><?php echo tep_get_order_type_name($order_type); ?></b></td>
							</tr>
							<tr>
								<td class="main" width="140"><b>Job Start Date:</b> </td><td class="main"><b><?php echo date("n/d/Y", $date_schedualed); ?></b></td>
							</tr>
							<tr>
								<td class="main">House Number: </td><td class="main"><?php echo $house_number; ?></td>
							</tr>
							<tr>
								<td class="main">Street Name: </td><td class="main"><?php echo $street_name; ?></td>
							</tr>
							<tr>
								<td class="main">City: </td><td class="main"><?php echo $city; ?></td>
							</tr>
							<tr>
								<td class="main">Zip+4: </td><td class="main"><?php echo $zip4; ?></td>
							</tr>
							<tr>
								<td class="main">County: </td><td class="main"><?php echo $county_name; ?></td>
							</tr>
							<tr>
								<td class="main">State: </td><td class="main"><?php echo $state_name; ?></td>
							</tr>
							<tr>
								<td class="main">Number of Posts: </td><td class="main"><?php echo $number_of_posts;; ?></td>
							</tr>
							<tr>
								<td class="main">Crossstreet/Directions: </td><td class="main"><?php echo $cross_street_directions; ?></td>
							</tr>
							<?php
							$service_area_id = tep_fetch_zip4_service_area($zip);
								if (tep_fetch_service_area_window($service_area_id) > 0) {
							?>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
							</tr>
							<tr>
								<td class="main" colspan="2"><b>This address has a <?php echo tep_fetch_service_area_window($service_area_id); ?> business day installation window (excludes weekends, Federal Holidays and severe weather days).</b></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td class="main">Special Instructions: </td><td class="main"><?php echo stripslashes($special_instructions); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
	</tr>
	<tr>
		<td height="20"><hr /></td>
	</tr>
    <tr>
     <td class="style6" align="center"><small><?php echo BUSINESS_ADDRESS; ?> | Email: <?php echo INFO_EMAIL; ?> | Fax to: <?php echo FAX_VOICE; ?></small></td>
	</tr>
	<tr>
		<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
	</tr>
</table>
<?php
$session->php_session_unregister('order_id_print');
$session->php_session_unregister('agent_id_print');
$session->php_session_close();
?>