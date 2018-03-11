<?php
	// Cron job that should run every month on 12:01 AM, sending stats to a specific email address.

	$live = true; //if we're going live
	$limit = 0; //if we're limiting no of records (set to 0 if no limit)
	$live_email = 'realtysp@yahoo.com'; //live email (admin)
	//$live_email = 'netz_pro@hotmail.com';
	//end customisable values
	
	include_once('includes/application_top.php');
	include_once("includes/classes/mailbox.php");

	set_time_limit(120);
	
	$path = "www.realtysignpost.net/";
	
	$first_date = tep_fill_variable('first_date', 'post');
	if (empty($first_date))
	{
		$first_date=date("m/d/Y");
	}
	
	$to_time_first_date = strtotime($first_date);
	

	$strForm = '<table width="100%" cellspacing="3" cellpadding="0" border="0" >';
	$strForm .=	'<tr>';
			$strForm .=	'<td width="150"><img src="images/pixel_trans.gif" height="1" /></td>';
			$strForm .=	'<td ></td>';
		$strForm .=	'</tr>';
		$strForm .=	'<form action="" method="post">';
		$strForm .=	'<tr>';
			$strForm .=	'<td class="main" colspan="2"><b>Set Report Date</b> <input type="text" name="first_date" value="' . $first_date . '" size="7" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Update Stats" name="submit_value" id="submit_value"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Send Email" name="send_email" id="send_email"/></td>';
		$strForm .=	'</tr>';
		$strForm .=	'<tr>';
			$strForm .=	'<td class="main" style="padding-left:92px;"><i>(mm/dd/YY)</i></td>';
			$strForm .=	'<td align="right" style="padding-right:250px;">&nbsp;&nbsp;&nbsp;&nbsp;</td>';
		$strForm .=	'</tr>';
		$strForm .=	'</form>';
	$strForm .=	'</table>';
	
	echo $strForm;



					$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-1), date("Y", tep_fetch_current_timestamp())); 
					
					//$today = mktime(0, 0, 0,1,31,2009);  

					$today = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)), date("Y", $to_time_first_date)); 

					//$tomorrow = mktime(0, 0, 0,2,1,2009);  

					$tomorrow = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)+1), date("Y", $to_time_first_date)); 
					
					
					$query_install_old_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '4' and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_install_old_db);
					$result = $database->fetch_array($query);
					$count_install_old_db = $result['count'];


					$query_removal_old_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '4' and o.order_type_id = '3' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_removal_old_db);
					$result = $database->fetch_array($query);
					$count_removal_old_db = $result['count'];

					$query_svc_old_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '4' and o.order_type_id = '2' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_svc_old_db);
					$result = $database->fetch_array($query);
					$count_svc_old_db = $result['count'];


					$query_install_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '1' and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_install_new_db);
					$result = $database->fetch_array($query);
					$count_install_new_db = $result['count'];


					$query_removal_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '1' and o.order_type_id = '3' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_removal_new_db);
					$result = $database->fetch_array($query);
					$count_removal_new_db = $result['count'];

					$query_svc_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '1' and o.order_type_id = '2' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_svc_new_db);
					$result = $database->fetch_array($query);
					$count_svc_new_db = $result['count'];

					$mail_message = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" ><b>Ordered Today:</b></td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td height=\"5\"><img src=\"images/pixel_trans.gif\" height=\"5\" width=\"1\" /></td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td width=\"100%\">";
							$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" style=\"padding-left:15px;\" border=\"0\">";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" width=\"33%\" ># of Installs</td>";
									$mail_message .= "<td class=\"main\" width=\"33%\" ># of Removals</td>";
									$mail_message .= "<td class=\"main\" width=\"33%\" ># of Service Calls</td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" width=\"33%\" style=\"padding-left:10px;\" >" . $count_install_new_db . "&nbsp;via new db</td>";
									$mail_message .= "<td class=\"main\" width=\"33%\" style=\"padding-left:10px;\" >" . $count_removal_new_db . "&nbsp;via new db</td>";
									$mail_message .= "<td class=\"main\" width=\"33%\" style=\"padding-left:10px;\" >" . $count_svc_new_db . "&nbsp;via new db</td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" width=\"33%\" style=\"padding-left:10px;\" >" . $count_install_old_db . "&nbsp;via rsp.com</td>";
									$mail_message .= "<td class=\"main\" width=\"33%\" style=\"padding-left:10px;\" >" . $count_removal_old_db . "&nbsp;via rsp.com</td>";
									$mail_message .= "<td class=\"main\" width=\"33%\" style=\"padding-left:10px;\" >" . $count_svc_old_db . "&nbsp;via rsp.com</td>";
								$mail_message .= "</tr>";
							$mail_message .= "</table>";
						$mail_message .= "</td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td height=\"5\"><img src=\"images/pixel_trans.gif\" height=\"5\" width=\"1\" /></td>";
					$mail_message .= "</tr>";
					
					$date_tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 
					
					//$date_tomorrow = mktime(0, 0, 0,2,1,2009);  

					$date_tomorrow = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)+1), date("Y", $to_time_first_date)); 
					

					$date_30 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-30), date("Y", tep_fetch_current_timestamp())); 

					//$date_30 = mktime(0, 0, 0,2,-29,2009);  
					
					$date_30 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-29), date("Y", $to_time_first_date)); 
					
					
					$query_install_wo_removal_30 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_30 . "' and o.date_completed < '" . $date_tomorrow . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
					//echo "<br/><br/>";
				
					$query = $database->query($query_install_wo_removal_30);
					$result = $database->fetch_array($query);
					$install_wo_removal_30 = $result['count'];

					$date_31 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-30), date("Y", tep_fetch_current_timestamp())); 

					//$date_31 = mktime(0, 0, 0,2,-29,2009);  

					$date_31 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-29), date("Y", $to_time_first_date)); 

					$date_60 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-60), date("Y", tep_fetch_current_timestamp())); 

					//$date_60 = mktime(0, 0, 0,2,-59,2009);  
					$date_60 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-59), date("Y", $to_time_first_date)); 
					
					$query_install_wo_removal_31_60 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_60 . "' and o.date_completed < '" . $date_31 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
					//echo "<br/><br/>";
				
					$query = $database->query($query_install_wo_removal_31_60);
					$result = $database->fetch_array($query);
					$install_wo_removal_31_60 = $result['count'];

					$date_61 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-60), date("Y", tep_fetch_current_timestamp())); 

					//$date_61 = mktime(0, 0, 0,2,-59,2009);  
					$date_61 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-59), date("Y", $to_time_first_date)); 


					$date_90 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-90), date("Y", tep_fetch_current_timestamp())); 
					
					//$date_90 = mktime(0, 0, 0,2,-89,2009);  
					$date_90 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-89), date("Y", $to_time_first_date)); 
					
					$query_install_wo_removal_61_90 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_90 . "' and o.date_completed < '" . $date_61 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
					//echo "<br/><br/>";
				
					$query = $database->query($query_install_wo_removal_61_90);
					$result = $database->fetch_array($query);
					$install_wo_removal_61_90 = $result['count'];

					$date_91 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-90), date("Y", tep_fetch_current_timestamp())); 

					//$date_91 = mktime(0, 0, 0,2,-89,2009);  
					$date_91 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-89), date("Y", $to_time_first_date)); 

					$date_120 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-120), date("Y", tep_fetch_current_timestamp())); 
					
					//$date_120 = mktime(0, 0, 0,2,-119,2009);  
					$date_120 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-119), date("Y", $to_time_first_date)); 
					
					$query_install_wo_removal_91_120 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_120 . "' and o.date_completed < '" . $date_91 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
					//echo "<br/><br/>";
				
					$query = $database->query($query_install_wo_removal_91_120);
					$result = $database->fetch_array($query);
					$install_wo_removal_91_120 = $result['count'];


					$date_121 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-120), date("Y", tep_fetch_current_timestamp())); 

					//$date_121 = mktime(0, 0, 0,2,-119,2009);  
					$date_121 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-119), date("Y", $to_time_first_date)); 

					$date_240 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-240), date("Y", tep_fetch_current_timestamp())); 
					
					//$date_240 = mktime(0, 0, 0,2,-239,2009);  
					$date_240 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-239), date("Y", $to_time_first_date)); 
					
					$query_install_wo_removal_121_240 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_240 . "' and o.date_completed < '" . $date_121 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
					//echo "<br/><br/>";
				
					$query = $database->query($query_install_wo_removal_121_240);
					$result = $database->fetch_array($query);
					$install_wo_removal_121_240 = $result['count'];


					$date_241 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-240), date("Y", tep_fetch_current_timestamp())); 

					//$date_241 = mktime(0, 0, 0,2,-239,2009);  
					$date_241 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-239), date("Y", $to_time_first_date)); 

					$date_360 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-360), date("Y", tep_fetch_current_timestamp())); 
					
					//$date_360 = mktime(0, 0, 0,2,-359,2009);  
					$date_360 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-359), date("Y", $to_time_first_date)); 

					$query_install_wo_removal_241_360 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_360 . "' and o.date_completed < '" . $date_241 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
					//echo "<br/><br/>";
				
					$query = $database->query($query_install_wo_removal_241_360);
					$result = $database->fetch_array($query);
					$install_wo_removal_241_360 = $result['count'];


					$date_361 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-360), date("Y", tep_fetch_current_timestamp())); 

					//$date_361 = mktime(0, 0, 0,2,-359,2009);  
					$date_361 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-359), date("Y", $to_time_first_date)); 

					$date_540 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-540), date("Y", tep_fetch_current_timestamp())); 
					
					//$date_540 = mktime(0, 0, 0,2,-539,2009);  
					$date_540 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-539), date("Y", $to_time_first_date)); 
					
					$query_install_wo_removal_361_540 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_540 . "' and o.date_completed < '" . $date_361 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
					//echo "<br/><br/>";
				
					$query = $database->query($query_install_wo_removal_361_540);
					$result = $database->fetch_array($query);
					$install_wo_removal_361_540 = $result['count'];


					$date_541 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-540), date("Y", tep_fetch_current_timestamp())); 

					//$date_541 = mktime(0, 0, 0,2,-539,2009);  
					$date_541 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-539), date("Y", $to_time_first_date)); 


					$date_720 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-720), date("Y", tep_fetch_current_timestamp())); 
					
					//$date_720 = mktime(0, 0, 0,2,-719,2009);  
					$date_720 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-719), date("Y", $to_time_first_date)); 
					
					$query_install_wo_removal_541_720 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_720 . "' and o.date_completed < '" . $date_541 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
					//echo "<br/><br/>";
				
					$query = $database->query($query_install_wo_removal_541_720);
					$result = $database->fetch_array($query);
					$install_wo_removal_541_720 = $result['count'];


					$date_721 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-720), date("Y", tep_fetch_current_timestamp())); 

					//$date_721 = mktime(0, 0, 0,2,-719,2009);  
					$date_721 = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)-719), date("Y", $to_time_first_date)); 
					
					$query_install_wo_removal_721 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed < '" . $date_721 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
					//echo "<br/><br/>";
				
					$query = $database->query($query_install_wo_removal_721);
					$result = $database->fetch_array($query);
					$install_wo_removal_721 = $result['count'];

					
					$mail_message .= "<tr>";
						$mail_message .= "<td width=\"100%\">";
							$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" border=\"0\" >";
								$mail_message .= "<tr>";
									$mail_message .= "<td width=\"150\"><img src=\"images/pixel_trans.gif\" height=\"1\" /></td>";
									$mail_message .= "<td >&nbsp;</td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" valign=\"top\" ><b>Post Installed Time:</b>  </td>";
									$mail_message .= "<td align=\"left\" class=\"main\"> &lt;30 ( " . $install_wo_removal_30 . " ) &sbquo; 31&ndash;60 ( " . $install_wo_removal_31_60 . " ) &sbquo; 61&ndash;90 ( " . $install_wo_removal_61_90 . " ) &sbquo; 91&ndash;120 ( " . $install_wo_removal_91_120 . " ) &sbquo; 121&ndash;240 ( " . $install_wo_removal_121_240 . " ) &sbquo; <br>241&ndash;360 ( " . $install_wo_removal_241_360 . " ) &sbquo; 361&ndash;540 ( " . $install_wo_removal_361_540 . " ) &sbquo; 541&ndash;720 ( " . $install_wo_removal_541_720 . " ) &sbquo; &gt;720 ( " . $install_wo_removal_721 . " ) </td>";
								$mail_message .= "</tr>";
							$mail_message .= "</table>";
						$mail_message .= "</td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
					$mail_message .= "</tr>";
					
						$query_install_remove = "  select count(ei.equipment_item_id) as count , sum( ord.removal_completed - ord.install_completed) as install_length  from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id install_order_id, o.date_completed install_completed, o.order_status_id install_order_status_id, o2.order_id removal_order_id, o2.date_completed removal_completed, o2.order_status_id removal_order_status_id  from " . TABLE_ORDERS . " o inner join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3' and o2.order_status_id=3 and o.order_id <> o2.order_id) inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 ) as ord on (eto.order_id = ord.install_order_id  ) where  e.equipment_type_id = '1'  and ord.install_completed <= ord.removal_completed ";

					$query = $database->query($query_install_remove);
					$result = $database->fetch_array($query);
					$count = $result['count'];
					//echo "<br/><br/>";
					 $install_length = $result['install_length'];
					//echo "<br/><br/>";
					
					$average_install_length = $install_length/$count;
					//echo "<br/><br/>";
					$average_install_length = $average_install_length/(60*60*24);

					
					$mail_message .= "<tr>";
						$mail_message .= "<td width=\"100%\">";
							$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" border=\"0\" >";
								$mail_message .= "<tr>";
									$mail_message .= "<td width=\"150\"><img src=\"images/pixel_trans.gif\" height=\"1\" /></td>";
									$mail_message .= "<td >&nbsp;</td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" colspan=\"2\"><b>Average Install Length (Removed):</b> &nbsp;&nbsp;&nbsp; " . number_format($average_install_length,2) . "&nbsp;days</td>";
								$mail_message .= "</tr>";
							$mail_message .= "</table>";
						$mail_message .= "</td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
					$mail_message .= "</tr>";



						$query_install_active = "  select count(ei.equipment_item_id) as count , sum( UNIX_TIMESTAMP() - ord.install_completed) as install_length  from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id install_order_id, o.date_completed install_completed, o.order_status_id install_order_status_id from " . TABLE_ORDERS . " o  inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 and o.address_id not in ( select address_id from " . TABLE_ORDERS . " o2 where   o2.order_type_id = '3' and o2.order_status_id=3 ) ) as ord on (eto.order_id = ord.install_order_id  ) where  e.equipment_type_id = '1'  and ord.install_completed <= UNIX_TIMESTAMP() ";

					$query = $database->query($query_install_active);
					$result = $database->fetch_array($query);
					$count = $result['count'];
					//echo "<br/><br/>";
					  $install_length = $result['install_length'];
					//echo "<br/><br/>";
					if($count >0)
					{
						 $average_install_length = $install_length/$count;
						//echo "<br/><br/>";
						 $average_install_length = $average_install_length/(60*60*24);
					}
					else
					{
						$average_install_length = 0;
					}
					
					$mail_message .= "<tr>";
						$mail_message .= "<td width=\"100%\">";
							$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" border=\"0\" >";
								$mail_message .= "<tr>";
									$mail_message .= "<td width=\"150\"><img src=\"images/pixel_trans.gif\" height=\"1\" /></td>";
									$mail_message .= "<td ></td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" colspan=\"2\"><b>Average Install Length (Active):</b> &nbsp;&nbsp;&nbsp; " . number_format($average_install_length,2). "&nbsp;days</td>";
								$mail_message .= "</tr>";
							$mail_message .= "</table>";
						$mail_message .= "</td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
					$mail_message .= "</tr>";



					
					$query_install = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 ";
					
					$query = $database->query($query_install);
					$result = $database->fetch_array($query);
					$count_install = $result['count'];

					$query_install_complete = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.order_status_id = '3' and o.date_added > 0 ";
					
					$query = $database->query($query_install_complete);
					$result = $database->fetch_array($query);
					$count_install_complete = $result['count'];


					$query_removal = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '3' and o.date_added > 0 ";
					
					$query = $database->query($query_removal);
					$result = $database->fetch_array($query);
					$count_removal = $result['count'];

					$query_removal_complete = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '3' and o.order_status_id = '3' and o.date_added > 0 ";
					
					$query = $database->query($query_removal_complete);
					$result = $database->fetch_array($query);
					$count_removal_complete = $result['count'];

					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\"><b>Overall DB #s:</b></td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td height=\"5\"><img src=\"images/pixel_trans.gif\" height=\"5\" width=\"1\" /></td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td width=\"100%\">";
							$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" style=\"padding-left:15px;\" border=\"0\">";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" width=\"33%\" >Total # of Installs:&nbsp;&nbsp;(&nbsp;" . $count_install . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" width=\"33%\" >Total # of Removals:&nbsp;&nbsp;(&nbsp;" . $count_removal . "&nbsp;)</td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" width=\"33%\" >Total # of Completed Installs:&nbsp;&nbsp;(&nbsp;" . $count_install_complete . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" width=\"33%\" >Total # of Completed Removals:&nbsp;&nbsp;(&nbsp;" . $count_removal_complete . "&nbsp;)</td>";
								$mail_message .= "</tr>";
							$mail_message .= "</table>";
						$mail_message .= "</td>";
					$mail_message .= "</tr>";

					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td height=\"5\"><img src=\"images/pixel_trans.gif\" height=\"5\" width=\"1\" /></td>";
					$mail_message .= "</tr>";
					
					$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-1), date("Y", tep_fetch_current_timestamp())); 
					$tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 

					$month_first_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())-1, 1, date("Y", tep_fetch_current_timestamp())); 

					$year_first_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp())); 
					
					//$today = mktime(0, 0, 0, 1, 31, 2009); 
					//$tomorrow = mktime(0, 0, 0, 2, 1, 2009); 
					//$month_first_date = mktime(0, 0, 0, 1, 1, 2009); 
					//$year_first_date = mktime(0, 0, 0, 1, 1, 2009); 

					$today = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)), date("Y", $to_time_first_date)); 
					$tomorrow = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)+1), date("Y", $to_time_first_date)); 

					$month_first_date = mktime(0, 0, 0, date("n", $to_time_first_date), 1, date("Y", $to_time_first_date)); 

					$year_first_date = mktime(0, 0, 0, 1, 1, date("Y", $to_time_first_date)); 
					

					$query_install_today = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_install_today);
					$result = $database->fetch_array($query);
					$count_install_today = $result['count'];
					$value_install_today = $result['value'];


					$query_install_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_install_month);
					$result = $database->fetch_array($query);
					$count_install_month = $result['count'];
					$value_install_month = $result['value'];

					$query_install_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_install_ytd);
					$result = $database->fetch_array($query);
					$count_install_ytd= $result['count'];
					$value_install_ytd= $result['value'];



					
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\" style=\"font-size:12px;\"><b>MONEY STATISTICS:</b></td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\"><b>Current Year:</b></td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td height=\"5\"><img src=\"images/pixel_trans.gif\" height=\"5\" width=\"1\" /></td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td width=\"100%\">";
							$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" style=\"padding-left:15px;\" border=\"0\">";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" width=\"6%\" >Today</td>";
									$mail_message .= "<td class=\"main\" width=\"20%\" ># of Installs:&nbsp;&nbsp;(&nbsp;" . $count_install_today . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" width=\"46%\" >$ value of orders placed today:&nbsp;&nbsp;(&nbsp;" . number_format ($value_install_today,2) . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" width=\"28%\" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;";
									if($count_install_today>0)
									{ 
										$mail_message .=  number_format (($value_install_today/$count_install_today),2);
									}
									else
									{
										$mail_message .=  "0.00";
									} 
									$mail_message .= "&nbsp;)</td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" >Month</td>";
									$mail_message .= "<td class=\"main\" ># of Installs:&nbsp;&nbsp;(&nbsp;" . $count_install_month . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" >$ value of orders placed this month:&nbsp;&nbsp;(&nbsp;" . number_format($value_install_month,2) . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;";
									if($count_install_month>0)
									{ 
										$mail_message .=  number_format(($value_install_month/$count_install_month),2);
									}
									else
									{
										$mail_message .=  "0.00";
									} 
									$mail_message .=  "&nbsp;)</td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" >YTD</td>";
									$mail_message .= "<td class=\"main\" ># of Installs:&nbsp;&nbsp;(&nbsp;" . $count_install_ytd . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" >$ value of orders placed from Jan 1:&nbsp;&nbsp;(&nbsp;" . number_format($value_install_ytd,2) . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;";
									if($count_install_ytd>0)
									{ 
										$mail_message .=  number_format(($value_install_ytd/$count_install_ytd),2);
									}
									else
									{
										$mail_message .=  "0.00";
									} 
									$mail_message .=  "&nbsp;)</td>";
								$mail_message .= "</tr>";
							$mail_message .= "</table>";
						$mail_message .= "</td>";
					$mail_message .= "</tr>";

					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
					$mail_message .= "</tr>";
					
					$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-1), date("Y", tep_fetch_current_timestamp())-1); 
					$tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())-1); 

					$month_first_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())-1, 1, date("Y", tep_fetch_current_timestamp())-1); 

					$year_first_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp())-1); 
					

					//$today = mktime(0, 0, 0, 1, 31, 2008); 
					//$tomorrow = mktime(0, 0, 0, 2, 1, 2008); 
					//$month_first_date = mktime(0, 0, 0, 1, 1, 2008); 
					//$year_first_date = mktime(0, 0, 0, 1, 1, 2008); 
					
					$today = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)), date("Y", $to_time_first_date)-1); 
					$tomorrow = mktime(0, 0, 0, date("n", $to_time_first_date), (date("d", $to_time_first_date)+1), date("Y", $to_time_first_date)-1); 

					$month_first_date = mktime(0, 0, 0, date("n", $to_time_first_date), 1, date("Y", $to_time_first_date)-1); 

					$month_last_date = mktime(0, 0, 0, date("n", $to_time_first_date)+1, 1, date("Y", $to_time_first_date)-1); 

					$year_first_date = mktime(0, 0, 0, 1, 1, date("Y", $to_time_first_date)-1); 
					
					
					$query_install_today = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_install_today);
					$result = $database->fetch_array($query);
					$count_install_today = $result['count'];
					$value_install_today = $result['value'];


					$query_install_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $month_last_date . "'";
					
					$query = $database->query($query_install_month);
					$result = $database->fetch_array($query);
					$count_install_month = $result['count'];
					$value_install_month = $result['value'];

					$query_install_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $tomorrow . "'";
					
					$query = $database->query($query_install_ytd);
					$result = $database->fetch_array($query);
					$count_install_ytd= $result['count'];
					$value_install_ytd= $result['value'];



					
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\"><b>Previous Year:</b></td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td height=\"5\"><img src=\"images/pixel_trans.gif\" height=\"5\" width=\"1\" /></td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";

						$mail_message .= "<td width=\"100%\">";
							$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" style=\"padding-left:15px;\" border=\"0\">";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" width=\"6%\" >Today</td>";
									$mail_message .= "<td class=\"main\" width=\"20%\" ># of Installs:&nbsp;&nbsp;(&nbsp;" . $count_install_today . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" width=\"46%\" >$ value of orders placed today:&nbsp;&nbsp;(&nbsp;" . number_format ($value_install_today,2) . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" width=\"28%\" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;";
									if($count_install_today>0)
									{ 
										$mail_message .=  number_format (($value_install_today/$count_install_today),2);
									}
									else
									{
										$mail_message .=  "0.00";
									} 
									$mail_message .=  "&nbsp;)</td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" >Month</td>";
									$mail_message .= "<td class=\"main\" ># of Installs:&nbsp;&nbsp;(&nbsp;" . $count_install_month . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" >$ value of orders placed this month:&nbsp;&nbsp;(&nbsp;" . number_format($value_install_month,2) . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;";
									if($count_install_month>0)
									{ 
										$mail_message .=  number_format(($value_install_month/$count_install_month),2);
									}
									else
									{
										$mail_message .=  "0.00";
									} 
									$mail_message .=  "&nbsp;)</td>";
								$mail_message .= "</tr>";
								$mail_message .= "<tr>";
									$mail_message .= "<td class=\"main\" >YTD</td>";
									$mail_message .= "<td class=\"main\" ># of Installs:&nbsp;&nbsp;(&nbsp;" . $count_install_ytd . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" >$ value of orders placed from Jan 1:&nbsp;&nbsp;(&nbsp;" . number_format($value_install_ytd,2) . "&nbsp;)</td>";
									$mail_message .= "<td class=\"main\" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;";
									if($count_install_ytd>0)
									{ 
										$mail_message .=  number_format(($value_install_ytd/$count_install_ytd),2);
									}
									else
									{
										$mail_message .=  "0.00";
									} 
									$mail_message .=  "&nbsp;)</td>";
								$mail_message .= "</tr>";
							$mail_message .= "</table>";
						$mail_message .= "</td>";
					$mail_message .= "</tr>";




					 $query_active_post = " select u.user_id, count(o.order_id) as order_count from " . TABLE_ORDERS . " o  inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id  where  o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 AND o.address_id NOT IN (SELECT address_id FROM " . TABLE_ORDERS . " WHERE (order_type_id = 3 and order_status_id = '3' )  OR order_status_id = 4 ) Group By u.user_id" ; 
					 

					 //$query_active_post = " select u.user_id, count(o.order_id) as order_count from " . TABLE_ORDERS . " o  Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id  where  o.address_id NOT IN (SELECT address_id FROM " . TABLE_ORDERS . " WHERE (order_type_id = 3 and order_status_id = '3' )  OR (order_type_id = 3 AND order_status_id = 4)
		//OR (order_type_id = 1 AND order_status_id = 4) 
		//OR (order_type_id = 1 AND order_status_id = 5) ) Group By u.user_id" ; 
					 
					//echo "<br/><br/>";
				
					$order_query = $database->query($query_active_post);
					
					if ($database->num_rows($order_query) > 0) 
					{
						$total_order_count = 0;
						$total_agent_count = 0;
						$one_post_count=0;
						$two_post_count=0;
						$five_post_count=0;
						$ten_post_count=0;
						$above_ten_post_count=0;
						while ($order_result = $database->fetch_array($order_query)) 
						{
							$total_agent_count +=1;
							$total_order_count += $order_result["order_count"];
							if($order_result["order_count"]==1)
							{
								$one_post_count += 1;
							}
							elseif($order_result["order_count"]==2)
							{
								$two_post_count += 1;
							}
							elseif($order_result["order_count"]<=5)
							{
								$five_post_count += 1;
							}
							elseif($order_result["order_count"]<=10)
							{
								$ten_post_count += 1;
							}
							elseif($order_result["order_count"]>10)
							{
								$above_ten_post_count += 1;
							}
						}
						$mail_message .= "<tr>";
							$mail_message .= "<td width=\"100%\">";
								$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" border=\"0\" >";
									$mail_message .= "<tr>";
										$mail_message .= "<td width=\"350\"><img src=\"images/pixel_trans.gif\" height=\"1\" /></td>";
										$mail_message .= "<td >&nbsp;</td>";
									$mail_message .= "</tr>";
									$mail_message .= "<tr>";
										$mail_message .= "<td class=\"main\" valign=\"top\" ><b>Agents with active install orders:</b>  </td>";
										$mail_message .= "<td align=\"left\" class=\"main\">$total_agent_count</td>";
									$mail_message .= "</tr>";
									$mail_message .= "<tr>";
										$mail_message .= "<td class=\"main\" valign=\"top\" ><b>Total # of Active Signposts:</b>  </td>";
										$mail_message .= "<td align=\"left\" class=\"main\">$total_order_count</td>";
									$mail_message .= "</tr>";
									$mail_message .= "<tr>";
										$mail_message .= "<td class=\"main\" valign=\"top\" ><b>Include # of agents with just one active post</b>  </td>";
										$mail_message .= "<td align=\"left\" class=\"main\">$one_post_count</td>";
									$mail_message .= "</tr>";
									$mail_message .= "<tr>";
										$mail_message .= "<td class=\"main\" valign=\"top\" ><b>Include # of agents with just two active posts</b>  </td>";
										$mail_message .= "<td align=\"left\" class=\"main\">$two_post_count</td>";
									$mail_message .= "</tr>";
									$mail_message .= "<tr>";
										$mail_message .= "<td class=\"main\" valign=\"top\" ><b>Include # of agents with 3 - 5 posts</b>  </td>";
										$mail_message .= "<td align=\"left\" class=\"main\">$five_post_count</td>";
									$mail_message .= "</tr>";
									$mail_message .= "<tr>";
										$mail_message .= "<td class=\"main\" valign=\"top\" ><b>Include # of agents with 6 - 10 posts</b>  </td>";
										$mail_message .= "<td align=\"left\" class=\"main\">$ten_post_count</td>";
									$mail_message .= "</tr>";
									$mail_message .= "<tr>";
										$mail_message .= "<td class=\"main\" valign=\"top\" ><b>Include # of agents with over 10 posts</b>  </td>";
										$mail_message .= "<td align=\"left\" class=\"main\">$above_ten_post_count</td>";
									$mail_message .= "</tr>";
								$mail_message .= "</table>";
							$mail_message .= "</td>";
						$mail_message .= "</tr>";
						$mail_message .= "<tr>";
							$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
						$mail_message .= "</tr>";
						
					}
					else
					{
						$mail_message .= "<tr>";
							$mail_message .= "<td width=\"100%\">";
								$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" border=\"0\" >";
									$mail_message .= "<tr>";
										$mail_message .= "<td width=\"350\"><img src=\"images/pixel_trans.gif\" height=\"1\" /></td>";
										$mail_message .= "<td >&nbsp;</td>";
									$mail_message .= "</tr>";
									$mail_message .= "<tr>";
										$mail_message .= "<td class=\"main\" valign=\"top\" ><b>Total # of Active Signposts:</b>  </td>";
										$mail_message .= "<td align=\"left\" class=\"main\">0</td>";
									$mail_message .= "</tr>";
								$mail_message .= "</table>";
							$mail_message .= "</td>";
						$mail_message .= "</tr>";
						$mail_message .= "<tr>";
							$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
						$mail_message .= "</tr>";
					}
					


					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
					$mail_message .= "</tr>";
					$mail_message .= "<tr>";
						$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
					$mail_message .= "</tr>";


				$mail_message .= "</table>";

				
				echo $mail_message;
				
		echo $var_send_email = tep_fill_variable('send_email', 'post');
		
		if($var_send_email!="")
		{

			$message = new email();
			$message->add_text($mail_message);
			$subject = date("F j, Y",$to_time_first_date) . ' Summary E-mail of Statistics';
			$message->build_message();
			mail($live_email, $subject, "<html><body>".$mail_message . "</body></html>", "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1" );
	
			$live_email = 'netz_pro@hotmail.com';
			//mail($live_email, $subject, "<html><body>".$mail_message . "</body></html>", "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1" );
			
		}
	
 ?>
