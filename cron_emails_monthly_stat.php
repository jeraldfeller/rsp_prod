<?php
// Cron job that should run every month on 12:01 AM, sending stats to a specific email address.

$live = true; //if we're going live

$limit = 0; 
//if we're limiting no of records (set to 0 if no limit)

$live_email = 'realtysp@yahoo.com'; 
//live email (admin)

//$live_email = 'netz_pro@hotmail.com';
//end customisable values
include_once('includes/application_top.php');

include_once("includes/classes/mailbox.php");

set_time_limit(120);
$path = "www.realtysignpost.net/";
$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-1), date("Y", tep_fetch_current_timestamp())); 

$query_install_old_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '4' and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";

$query = $database->query($query_install_old_db);
$result = $database->fetch_array($query);
$count_install_old_db = $result['count'];
$query_removal_old_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '4' and o.order_type_id = '3' and o.date_added > 0 and o.date_added >= '" . $today . "'";

$query = $database->query($query_removal_old_db);
$result = $database->fetch_array($query);
$count_removal_old_db = $result['count'];
$query_svc_old_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '4' and o.order_type_id = '2' and o.date_added > 0 and o.date_added >= '" . $today . "'";

$query = $database->query($query_svc_old_db);
$result = $database->fetch_array($query);
$count_svc_old_db = $result['count'];
$query_install_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '1' and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";

$query = $database->query($query_install_new_db);
$result = $database->fetch_array($query);
$count_install_new_db = $result['count'];
$query_removal_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '1' and o.order_type_id = '3' and o.date_added > 0 and o.date_added >= '" . $today . "'";

$query = $database->query($query_removal_new_db);
$result = $database->fetch_array($query);
$count_removal_new_db = $result['count'];
$query_svc_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.inserted_order_type_id = '1' and o.order_type_id = '2' and o.date_added > 0 and o.date_added >= '" . $today . "'";

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


 // Miss utility
//////////////////////////
$query_miss_utility_open = "select count(o.order_id) as count ";
$query_miss_utility_open.= "from " . TABLE_ORDERS . " o ";
$query_miss_utility_open.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";
$query_miss_utility_open.= "where o.order_status_id < 3 and omu.contacted = 0 ";
$query_miss_utility_open.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";
$muq = $database->query($query_miss_utility_open);
$result = $database->fetch_array($muq);
$miss_utility_open = ($result['count'] > 0) ? $result['count'] : 0;

//bgdn

$query = "select count(order_miss_utility_id) as count from ". TABLE_ORDERS_MISS_UTILITY;

$muq = $database->query($query);
$result = $database->fetch_array($muq);
$miss_utility_all_bgdn = ($result['count'] > 0) ? $result['count'] : 0;

//2

$query = "select count(order_id) as count from ". TABLE_ORDERS ." WHERE order_id > 109892";

$muq = $database->query($query);
$result = $database->fetch_array($muq);
$orders_all_bgdn = ($result['count'] > 0) ? $result['count'] : 0;

$percentage = ($miss_utility_all_bgdn*100)/$orders_all_bgdn;
$query_miss_utility_called = "select count(o.order_id) as count ";
$query_miss_utility_called.= "from " . TABLE_ORDERS . " o ";
$query_miss_utility_called.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";
$query_miss_utility_called.= "where o.order_status_id < 3 and omu.contacted = 1 ";
$query_miss_utility_called.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";
$muq = $database->query($query_miss_utility_called);
$result = $database->fetch_array($muq);
$miss_utility_called = ($result['count'] > 0) ? $result['count'] : 0;
$query_miss_utility_completed = "select count(o.order_id) as count ";
$query_miss_utility_completed.= "from " . TABLE_ORDERS . " o ";
$query_miss_utility_completed.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";
$query_miss_utility_completed.= "where o.order_status_id = 3 and omu.contacted = 1 ";
$query_miss_utility_completed.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";
$muq = $database->query($query_miss_utility_completed);
$result = $database->fetch_array($muq);
$miss_utility_completed = ($result['count'] > 0) ? $result['count'] : 0;

/////////////////////////

$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 
$query_install_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";
$query = $database->query($query_install_new_db);
$result = $database->fetch_array($query);
$count_install_new_db = $result['count'];
$query_removal_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '3' and o.date_added > 0 and o.date_added >= '" . $today . "'";
$query = $database->query($query_removal_new_db);
$result = $database->fetch_array($query);
$count_removal_new_db = $result['count'];
$query_svc_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '2' and o.date_added > 0 and o.date_added >= '" . $today . "'";
$query = $database->query($query_svc_new_db);
$result = $database->fetch_array($query);
$count_svc_new_db = $result['count'];
$midnight_ts = strtotime("midnight");
$reschedule_table = TABLE_RESCHEDULE_HISTORY;
$orders_table = TABLE_ORDERS;
$rescheduled_today_sql = "SELECT o.order_id, rh.new_scheduled_date, rh.old_scheduled_date FROM {$reschedule_table} rh JOIN {$orders_table} o ON (o.order_id = rh.order_id) WHERE rh.rescheduled_date >= {$midnight_ts} AND o.order_type_id = 3 ORDER BY o.order_id, rh.rescheduled_date";

$rescheduled_orders = array();

$query = $database->query($rescheduled_today_sql);

foreach($database->fetch_array($query) as $result){
    
$order_id = $result['order_id'];
    
if (array_key_exists($order_id, $rescheduled_orders)) {
$rescheduled_orders[$order_id]['new_scheduled_date'] = $result['new_scheduled_date'];
    } else {
$rescheduled_orders[$order_id] = array();
$rescheduled_orders[$order_id]['old_scheduled_date'] = $result['old_scheduled_date'];
$rescheduled_orders[$order_id]['new_scheduled_date'] = $result['new_scheduled_date'];
    }
}       
 $rescheduled_count = count($rescheduled_orders);

 $pushed_back_count = 0;

 $moved_up_count = 0;
foreach ($rescheduled_orders as $order_id => $schedule) {
    
if ($schedule['old_scheduled_date'] < $schedule['new_scheduled_date']) {
$pushed_back_count++;
    } elseif ($schedule['old_scheduled_date'] > $schedule['new_scheduled_date']) {
$moved_up_count++;
    }
}

$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
$midnight_future = ($midnight_tonight + ((60*60*24) * 1));

$query_red_flag = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_issue = '1'";

$query = $database->query($query_red_flag);
$result = $database->fetch_array($query);
$red_flag_orders = $result['count'];
$query_on_hold = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '5' and o.address_id = a.address_id ";

$query = $database->query($query_on_hold);
$result = $database->fetch_array($query);
$on_hold_orders = $result['count'];
// Miss utility
$query_miss_utility_open = "select count(o.order_id) as count ";
$query_miss_utility_open.= "from " . TABLE_ORDERS . " o ";
$query_miss_utility_open.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";
$query_miss_utility_open.= "where o.order_status_id < 3 and omu.contacted = 0 ";
$query_miss_utility_open.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";
$muq = $database->query($query_miss_utility_open);
$result = $database->fetch_array($muq);
$miss_utility_open = ($result['count'] > 0) ? $result['count'] : 0;
$query_miss_utility_called = "select count(o.order_id) as count ";
$query_miss_utility_called.= "from " . TABLE_ORDERS . " o ";
$query_miss_utility_called.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";
$query_miss_utility_called.= "where o.order_status_id < 3 and omu.contacted = 1 ";
$query_miss_utility_called.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";
$muq = $database->query($query_miss_utility_called);
$result = $database->fetch_array($muq);
$miss_utility_called = ($result['count'] > 0) ? $result['count'] : 0;
$query_miss_utility_completed = "select count(o.order_id) as count ";
$query_miss_utility_completed.= "from " . TABLE_ORDERS . " o ";
$query_miss_utility_completed.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";
$query_miss_utility_completed.= "where o.order_status_id = 3 and omu.contacted = 1 ";
$query_miss_utility_completed.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";
$muq = $database->query($query_miss_utility_completed);
$result = $database->fetch_array($muq);
$miss_utility_completed = ($result['count'] > 0) ? $result['count'] : 0;
// Credit Card Totals
$cc_today_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '1' AND date_added >= " . strtotime("today");
$cc_yesterday_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS ." WHERE billing_method_id = '1' AND date_added >= " . strtotime("yesterday") . " AND date_added < " . strtotime("today");

$cc_2_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '1' AND date_added >= " . strtotime("today - 2 days") . " AND date_added < " . strtotime("yesterday");

$cc_7_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '1' AND date_added >= " . strtotime("today - 7 days");
$cc_30_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '1' AND date_added >= " . strtotime("today - 30 days");
// Loop through each interval, run the SQL, and assign the total to $cc_today_total, $cc_yesterday_total, etc.
foreach (array('today', 'yesterday', '2_days', '7_days', '30_days') as $interval) {
    $sql = ${"cc_{$interval}_sql"};
    $query = $database->query($sql);
    $result = $database->fetch_array($query);
    ${"cc_{$interval}_total"} = '$'.number_format(($result ? $result['total'] : 0), 2);

${"xx_{$interval}_total"} = $result ? $result['total'] : 0;
}

//bgdn

$i_today_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '3' AND date_added >= " . strtotime("today");

$i_yesterday_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS ." WHERE billing_method_id = '3' AND date_added >= " . strtotime("yesterday") . " AND date_added < " . strtotime("today");

$i_2_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '3' AND date_added >= " . strtotime("today - 2 days") . " AND date_added < " . strtotime("yesterday");

$i_7_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '3' AND date_added >= " . strtotime("today - 7 days");
$i_30_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '3' AND date_added >= " . strtotime("today - 30 days");

foreach (array('today', 'yesterday', '2_days', '7_days', '30_days') as $interval) {
    $sql = ${"i_{$interval}_sql"};
    $query = $database->query($sql);
    $result = $database->fetch_array($query);


//print_r($result);
   // ${"cc_{$interval}_total"} = '$'.number_format(($result ? $result['total'] : 0), 2);
   
   if($result['total']>0) {
//echo ${"xx_{$interval}_total"} * 100;
//echo ((${"cc_{$interval}_total"} * 100) / $result['total']);
${"cc_{$interval}_percentage"} = number_format(((${"xx_{$interval}_total"} * 100) / ($result['total']+(${"xx_{$interval}_total"}))),2).'%';
   }
   else {
   ${"cc_{$interval}_percentage"} = '0%';
   }
}

$mail_message .='<tr>';
$mail_message .='<td align="left" class="main"><b>Rescheduled Today:</b> &nbsp;&nbsp;&nbsp;';
$mail_message .='Removals Rescheduled ( '.$rescheduled_count.' ) &nbsp;&nbsp;';
$mail_message .='Pushed Back ( '.$pushed_back_count.' ) &nbsp;&nbsp;';
$mail_message .='Moved Up ( '.$moved_up_count.' ) &nbsp;&nbsp;';
$mail_message .='</td>';
$mail_message .='</tr>';       $mail_message .=' <tr>';
  $mail_message .='  <td align="left" class="main"><b>Credit Card Totals:</b>&nbsp;&nbsp;&nbsp;&nbsp;Today/Yestderday/Two Days Ago/Last 7/Last 30: &nbsp;&nbsp;';
$mail_message .=''. $cc_today_total .'&nbsp;&nbsp;&nbsp;&nbsp;';
   $mail_message .='     '. $cc_yesterday_total .'&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .=' '. $cc_2_days_total .'&nbsp;&nbsp;&nbsp;&nbsp;';
  $mail_message .='      '. $cc_7_days_total .'&nbsp;&nbsp;&nbsp;&nbsp;';
   $mail_message .='     '. $cc_30_days_total .'';
  $mail_message .='  </td>';      $mail_message .='  </tr>';
$mail_message .='<tr>';
$mail_message .='<td align="left" class="main"><b>Credit Card Percentage:</b>&nbsp;&nbsp;&nbsp;&nbsp;Today/Yestderday/Two Days Ago/Last 7/Last 30: &nbsp;&nbsp;';
$mail_message .=''.  $cc_today_percentage .'&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='
'.  $cc_yesterday_percentage .'&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .=' '.  $cc_2_days_percentage .'&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='
'.  $cc_7_days_percentage .'&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='
'.  $cc_30_days_percentage .'';
 $mail_message .='   </td>';
$mail_message .='</tr>';
       $mail_message .=' <tr>';
 $mail_message .='   <td align="left" class="main">&nbsp;</td>';       $mail_message .=' </tr>';

$mail_message .="<tr>";
  $mail_message .='<td align="left" class="main">&nbsp;</td>';
  $mail_message .='</tr>';
  $mail_message .='<tr>'; 
  $mail_message .=' <td align="left" class="main"><b>Miss Utility:</b></td>';
  $mail_message .='</tr>';
$mail_message .='<tr>';  
$mail_message .='<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
 $mail_message .=' <td width="100%"><table width="100%" cellspacing="3" cellpadding="0" style="padding-left:15px;" border="0">';    
 $mail_message .='  <tr>';   
 $mail_message .='     <td width="250"><img src="images/pixel_trans.gif" height="1" width="220" /></td>';    
 $mail_message .='    <td width="100%"></td>';    
 $mail_message .='  </tr>';   
 $mail_message .='   <tr>';    
 $mail_message .='    <td class="main" width="250" >Open/Called/Completed/Percentage: &nbsp;&nbsp;</td>';     
 $mail_message .='   <td class="main" style="padding-left:30px;" >'.$miss_utility_open.'&nbsp;&nbsp;&nbsp;&nbsp; '. $miss_utility_called. '&nbsp;&nbsp;&nbsp;&nbsp; '. $miss_utility_completed.'</a>&nbsp;&nbsp;&nbsp;&nbsp; '. round($percentage, 2) .' %</td>';     
$mail_message .=' </tr>';  
$mail_message .='  </table></td>';$mail_message .='</tr>';

$date_tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 
$date_30 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-30), date("Y", tep_fetch_current_timestamp())); 

$query_install_wo_removal_30 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_30 . "' and o.date_completed < '" . $date_tomorrow . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
//echo "<br/><br/>";

$query = $database->query($query_install_wo_removal_30);
$result = $database->fetch_array($query);
$install_wo_removal_30 = $result['count'];
$date_31 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-30), date("Y", tep_fetch_current_timestamp())); 
$date_60 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-60), date("Y", tep_fetch_current_timestamp())); 

$query_install_wo_removal_31_60 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_60 . "' and o.date_completed < '" . $date_31 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
//echo "<br/><br/>";

$query = $database->query($query_install_wo_removal_31_60);
$result = $database->fetch_array($query);
$install_wo_removal_31_60 = $result['count'];
$date_61 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-60), date("Y", tep_fetch_current_timestamp())); 
$date_90 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-90), date("Y", tep_fetch_current_timestamp())); 

$query_install_wo_removal_61_90 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_90 . "' and o.date_completed < '" . $date_61 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
//echo "<br/><br/>";

$query = $database->query($query_install_wo_removal_61_90);
$result = $database->fetch_array($query);
$install_wo_removal_61_90 = $result['count'];
$date_91 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-90), date("Y", tep_fetch_current_timestamp())); 
$date_120 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-120), date("Y", tep_fetch_current_timestamp())); 

$query_install_wo_removal_91_120 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_120 . "' and o.date_completed < '" . $date_91 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
//echo "<br/><br/>";

$query = $database->query($query_install_wo_removal_91_120);
$result = $database->fetch_array($query);
$install_wo_removal_91_120 = $result['count'];
$date_121 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-120), date("Y", tep_fetch_current_timestamp())); 
$date_240 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-240), date("Y", tep_fetch_current_timestamp())); 

$query_install_wo_removal_121_240 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_240 . "' and o.date_completed < '" . $date_121 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
//echo "<br/><br/>";

$query = $database->query($query_install_wo_removal_121_240);
$result = $database->fetch_array($query);
$install_wo_removal_121_240 = $result['count'];
$date_241 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-240), date("Y", tep_fetch_current_timestamp())); 
$date_360 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-360), date("Y", tep_fetch_current_timestamp())); 

$query_install_wo_removal_241_360 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_360 . "' and o.date_completed < '" . $date_241 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
//echo "<br/><br/>";

$query = $database->query($query_install_wo_removal_241_360);
$result = $database->fetch_array($query);
$install_wo_removal_241_360 = $result['count'];
$date_361 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-360), date("Y", tep_fetch_current_timestamp())); 
$date_540 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-540), date("Y", tep_fetch_current_timestamp())); 

$query_install_wo_removal_361_540 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_540 . "' and o.date_completed < '" . $date_361 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
//echo "<br/><br/>";

$query = $database->query($query_install_wo_removal_361_540);
$result = $database->fetch_array($query);
$install_wo_removal_361_540 = $result['count'];
$date_541 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-540), date("Y", tep_fetch_current_timestamp())); 
$date_720 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-720), date("Y", tep_fetch_current_timestamp())); 

$query_install_wo_removal_541_720 = " select count(ei.equipment_item_id) as count from " . TABLE_EQUIPMENT . " e INNER JOIN " . TABLE_EQUIPMENT_ITEMS . " ei ON ( e.equipment_id = ei.equipment_id ) Inner join " . TABLE_EQUIPMENT_TO_ORDERS . " eto on (ei.equipment_item_id = eto.equipment_item_id) Inner Join (select o.order_id from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS . " o2 on (o.address_id = o2.address_id and o2.order_type_id = '3') inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id where  o.order_type_id = '1' and o.order_status_id = '3' and o2.order_id is NULL and o.date_completed > 0 and o.date_completed >= '" . $date_720 . "' and o.date_completed < '" . $date_541 . "') as ord on (eto.order_id = ord.order_id  ) where  e.equipment_type_id = '1' and ei.equipment_status_id='2'  ";
//echo "<br/><br/>";

$query = $database->query($query_install_wo_removal_541_720);
$result = $database->fetch_array($query);
$install_wo_removal_541_720 = $result['count'];
$date_721 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-720), date("Y", tep_fetch_current_timestamp())); 

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

$result = array();
$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND service_level_id = 1 AND o.order_type_id=1 UNION SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND service_level_id = 2 AND o.order_type_id=1 UNION SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > 0 AND o.service_level_id = 3 AND order_type_id=1");

$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > 0 AND o.service_level_id = 1 AND o.order_type_id=1");
$result = $database->fetch_array($query);
$silver_level = $result['cnt'];

$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > 0 AND o.service_level_id = 2 AND o.order_type_id=1 ");
$result = $database->fetch_array($query);
$gold_level = $result['cnt'];

$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > 0 AND o.service_level_id = 3 AND o.order_type_id=1");
$result = $database->fetch_array($query);
$platinum_level = $result['cnt'];
$levels_sum = $silver_level+$gold_level+$platinum_level;




/*
$query = $database->query("SELECT count(od.order_id) as count FROM " . TABLE_ORDERS . " o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN >0 AND od.equipment_name = 'SignPost - White, PVC' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count2 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed >0 AND od.equipment_name = 'SignPost - Black, PVC' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count3 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed >0 AND od.equipment_name = 'SignPost - Yellow, PVC' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count4 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN >0 AND od.equipment_name = 'SignPost - White, Wood' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count5 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN >0 AND od.equipment_name = 'SignPost - Black, Wood' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count6 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN >0 AND od.equipment_name = 'SignPost - Yellow, Wood' AND o.order_type_id=1");//$i=0;
while($resultz = $database->fetch_array($query))
{
$result[] = $resultz['count'];
}

//echo date('m/d/Y','1187637756');

$white_pvc = $result[0];
$black_pvc = $result[1];
$yellow_pvc = $result[2];
$white_wood = $result[3];
$black_wood = $result[4];
$yellow_wood = $result[5];

$sign_sum = $black_pvc+$white_pvc+$yellow_pvc+$black_wood+$white_wood+$yellow_wood;

*/
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
$tryit = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 
$todayz = $average_since_ts==0 ? $tryit : $average_since_ts;

//$stub = da

$tomorrowz = strtotime('+1 day', $todayz);

/*echo $todayz;
echo '<br>';
echo 'second';
echo '<br>';
echo $tomorrowz;*/
///doing % stats
$query = $database->query("SELECT count(od.order_id) as count FROM " . TABLE_ORDERS . " o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed > '" . $average_since_ts . "' AND od.equipment_name = 'SignPost - White, PVC' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count2 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed > '" . $average_since_ts . "' AND od.equipment_name = 'SignPost - Black, PVC' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count3 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed > '" . $average_since_ts . "' AND od.equipment_name = 'SignPost - Yellow, PVC' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count4 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed > '" . $average_since_ts . "' AND od.equipment_name = 'SignPost - White, Wood' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count5 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed > '" . $average_since_ts . "' AND od.equipment_name = 'SignPost - Black, Wood' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count6 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed > '" . $average_since_ts . "' AND od.equipment_name = 'SignPost - Yellow, Wood' AND o.order_type_id=1");
//$i=0;
foreach($database->fetch_array($query) as $resultz)
{
$result[] = $resultz['count'];
}

//echo date('m/d/Y','1187637756');

//comment

$white_pvc = $result[0];
$black_pvc = $result[1];
$yellow_pvc = $result[2];
$white_wood = $result[3];
$black_wood = $result[4];
$yellow_wood = $result[5];

$sign_sum = $black_pvc+$white_pvc+$yellow_pvc+$black_wood+$white_wood+$yellow_wood;

/*
$mail_message .= "<tr>";
$mail_message .= "<td width=\"100%\">";
$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" border=\"0\" >";
$mail_message .= "<tr>";

$mail_message .= "<td width=\"150\"><img src=\"images/pixel_trans.gif\" height=\"1\" /></td>";

$mail_message .= "<td >&nbsp;</td>";
$mail_message .= "</tr>";
$mail_message .= "<tr>";

$mail_message .= "<td class=\"main\" colspan=\"2\"><b>% of installed signposts:</b>";


$mail_message .='<td class="main">
White PVC ('.number_format(($white_pvc*100)/$sign_sum,2).'%)&nbsp;&nbsp;&nbsp;&nbsp;

Black PVC ('.number_format(($black_pvc*100)/$sign_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;

Yellow PVC ('. number_format(($yellow_pvc*100)/$sign_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;

White wood ('. number_format(($white_wood*100)/$sign_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;

Black wood ('. number_format(($black_wood*100)/$sign_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;

Yellow wood ('. number_format(($yellow_wood*100)/$sign_sum,2) .'%)</td>';


$mail_message .= "</tr>";
$mail_message .= "</table>";
$mail_message .= "</td>";
$mail_message .= "</tr>";
$mail_message .= "<tr>";
$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
$mail_message .= "</tr>";*/

//more bgdn
$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 
$cur_year = date('Y', strtotime('now'));
$cur_year_table = mktime(0,0,0,1,0,$cur_year);
$prev_year_table = mktime(0,0,0,1,0,$cur_year-1);
$long_prev_year_table = mktime(0,0,0,1,0,$cur_year-2);
$border_prev_year_table = mktime(0,0,0,1,0,$cur_year-3);
$todayz = $average_since_ts==0 ? $today : $average_since_ts;
//$stub = da
$tomorrowz = strtotime('+1 day', $todayz);
$result = array();
$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND service_level_id = 1 AND o.order_type_id=1 UNION SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND service_level_id = 2 AND o.order_type_id=1 UNION SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.service_level_id = 3 AND order_type_id=1");
$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.service_level_id = 1 AND o.order_type_id=1");
$result = $database->fetch_array($query);
$silver_level = $result['cnt'];
$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.service_level_id = 2 AND o.order_type_id=1 ");
$result = $database->fetch_array($query);
$gold_level = $result['cnt'];
$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.service_level_id = 3 AND o.order_type_id=1");
$result = $database->fetch_array($query);
$platinum_level = $result['cnt'];
$levels_sum = $silver_level+$gold_level+$platinum_level;
/////////////
//bgdn_old
$query = $database->query("SELECT o.order_id FROM " . TABLE_ORDERS . " o INNER JOIN `equipment_to_orders` od ON od.order_id=o.order_id WHERE o.date_completed > '" . $average_since_ts . "' AND od.equipment_group_id=1 AND o.order_type_id=1 GROUP BY o.order_id");
$result = $database->num_rows($query);
$count_bbox = $result;
$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.order_type_id=1");
$result = $database->fetch_array($query);
$count_all_with_bbox = $result['cnt'];
//bgdn_new
/*$query = $database->query("SELECT count(ei.equipment_item_id) as cnt FROM equipment_items ei JOIN equipment e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 3 and ei.equipment_status_id = 2 and ei.equipment_item_id in (select z.equipment_item_id from equipment_to_orders z inner join orders o on o.order_id=z.order_id where o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "')");
$result = $database->fetch_array($query);
$count_bbox = $result['cnt'];
$query = $database->query("SELECT count(ei.equipment_item_id) as cnt FROM equipment_items ei JOIN equipment e ON (e.equipment_id = ei.equipment_id) WHERE ei.equipment_status_id = 2 and ei.equipment_item_id in (select z.equipment_item_id from equipment_to_orders z inner join orders o on o.order_id=z.order_id where o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "')");
$result = $database->fetch_array($query);
$count_all_with_bbox = $result['cnt'];*/////////////
//bgdn_old
//Install Rider
$query = $database->query("SELECT od.order_id as oid, count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o INNER JOIN `equipment_to_orders` od ON od.order_id=o.order_id WHERE o.date_completed > '" . $average_since_ts . "' AND (od.equipment_group_id=2 OR od.equipment_group_id=3) AND o.order_type_id=1 GROUP BY oid HAVING cnt=1");
$result = $database->num_rows($query);
$count_one_riders = $result;
$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.order_type_id=1");
$result = $database->fetch_array($query);
$query = $database->query("SELECT od.order_id as oid, count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o INNER JOIN `equipment_to_orders` od ON od.order_id=o.order_id WHERE o.date_completed > '" . $average_since_ts . "' AND (od.equipment_group_id=2 OR od.equipment_group_id=3) AND o.order_type_id=1 GROUP BY oid HAVING cnt=2");
$count_two_riders = 0;
foreach($database->fetch_array($query) as $result)
{
$count_two_riders++;
}
$mail_message .= "<tr>";
$mail_message .= "<td width=\"100%\">";
$mail_message .= "<table width=\"100%\" cellspacing=\"3\" cellpadding=\"0\" border=\"0\" style=\"padding-left:15px;\">";
  $mail_message .='<tr><td class="main">% of installed signposts &nbsp;&nbsp;&nbsp;&nbsp;</td>';
$mail_message .='<td class="main">';
$mail_message .='White PVC ('. number_format(($white_pvc*100)/$sign_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='Black PVC ('. number_format(($black_pvc*100)/$sign_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='Yellow PVC ('. number_format(($yellow_pvc*100)/$sign_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='White wood ('. number_format(($white_wood*100)/$sign_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='Black wood ('. number_format(($black_wood*100)/$sign_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='Yellow wood ('. number_format(($yellow_wood*100)/$sign_sum,2) .'%)</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='  <td align="left" class="main">&nbsp;</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
    
$mail_message .='
<td class="main">% of installs for each service level  &nbsp;&nbsp;&nbsp;&nbsp;</td>';
$mail_message .='<td class="main">';
$mail_message .='Silver ('. number_format(($silver_level*100)/$levels_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='Gold ('. number_format( (($gold_level*100)/$levels_sum),2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='Platinum ('. number_format(($platinum_level*100)/$levels_sum,2) .'%)&nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='  <td align="left" class="main">&nbsp;</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
    
$mail_message .='
<td class="main"># of installs that have a BBox installed: '. $count_bbox .'  &nbsp;&nbsp;&nbsp;&nbsp;</td>';
$mail_message .='<td class="main">';
$mail_message .='% of installs that have a BBox installed: '. number_format((($count_bbox*100)/$count_all_with_bbox),2) .'%';
$mail_message .='</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='  <td align="left" class="main">&nbsp;</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
     
$mail_message .='       <td class="main"># of installs that have one rider installed: '. $count_one_riders .'  &nbsp;&nbsp;&nbsp;&nbsp;</td>';
$mail_message .='<td class="main">';
$mail_message .='% of installs that have one rider installed: '. number_format((($count_one_riders*100)/$count_all_with_bbox),2) .'%';
$mail_message .='</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
    
$mail_message .='
<td class="main"># of installs that have two riders installed: '. $count_two_riders .'  &nbsp;&nbsp;&nbsp;&nbsp;';
$mail_message .='</td> ';
$mail_message .='<td class="main">';
$mail_message .='% of installs that have two riders installed: '. number_format((($count_two_riders*100)/$count_all_with_bbox),2) .'%';
$mail_message .='</td>';
$mail_message .='</tr>';
$mail_message .='
<tr>';
     
$mail_message .='
   <td colspan="2"><br /></td>';
$mail_message .='</tr>';
$mail_message .='</table>';
$mail_message .='</td>';
$mail_message .='</tr>';

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

//CURRENT
$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 
$tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
$month_first_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp())); 
$year_first_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp()));
$query_install_today = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";
//$query_install_today = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";
//echo $query_install_today;exit;
$query = $database->query($query_install_today);
$result = $database->fetch_array($query);
$count_install_today = $result['count'];
$value_install_today = $result['value'];
// This query will get orders with payment method CC (CREDIT CARD)
$queryOrderCC = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";
//echo $queryOrderCC;
//echo strtotime("yesterday");
$query = $database->query($queryOrderCC);
$result = $database->fetch_array($query);
$countCC = $result['count'];
$valueCC = $result['value'];
// This query will get orders with payment method IO (Invoice Orders) for both Agency Monthly Invoice & Agent Monthly Invoice
$queryOrderIO = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_added > 0 and o.date_added >= '" . $today . "'";
//echo $queryOrderIO;
//echo strtotime("yesterday");
$query = $database->query($queryOrderIO);
$result = $database->fetch_array($query);
$countIO = $result['count'];
$valueIO = $result['value'];
$query_install_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id != '4' and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $tomorrow . "'";
//$query_install_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $tomorrow . "'";
$query = $database->query($query_install_month);
$result = $database->fetch_array($query);
$count_install_month = $result['count'];
$value_install_month = $result['value'];
$query_install_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id != '4' and o.order_status_id = '3' and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $tomorrow . "'";
//$query_install_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $tomorrow . "'";
$query = $database->query($query_install_ytd);
$result = $database->fetch_array($query);
$count_install_ytd= $result['count'];
$value_install_ytd= $result['value'];

$query_this_month_completed = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $tomorrow . "'";
$query = $database->query($query_this_month_completed);
$result = $database->fetch_array($query);
$this_month_complete_count = $result['count'];
$this_month_complete_value = $result['value'];
$queryOrderCC_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $tomorrow . "'";
$query = $database->query($queryOrderCC_month);
$result = $database->fetch_array($query);
$countCC_month = $result['count'];
//$valueCC = $result['value'];
$queryOrderIO_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $tomorrow . "'";
$query = $database->query($queryOrderIO_month);
$result = $database->fetch_array($query);
$countIO_month = $result['count'];
//$valueIO = $result['value'];
$count_all_stuff = $countIO_month+$countCC_month;
$countCC_percentage_month = ($countCC_month * 100) / $count_all_stuff;
($countIO_month==0) ? $countIO_percentage_month = 0 : $countIO_percentage_month = 100-$countCC_percentage_month;
//$countIO_percentage_month = 100-$countCC_percentage_month;


$queryOrderCC_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $tomorrow . "'";
$query = $database->query($queryOrderCC_year);
$result = $database->fetch_array($query);
$countCC_year = $result['count'];
//$valueCC = $result['value'];
$queryOrderIO_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $tomorrow . "'";
$query = $database->query($queryOrderIO_year);
$result = $database->fetch_array($query);
$countIO_year = $result['count'];
//$valueIO = $result['value'];
$count_all_stuff = $countIO_year+$countCC_year;
$countCC_percentage_year = ($countCC_year * 100) / $count_all_stuff;
($countIO_year==0) ? $countIO_percentage_year = 0 : $countIO_percentage_year = 100-$countCC_percentage_year;
//$countIO_percentage_month = 100-$countCC_percentage_month;
?>

<?php
$previous_month_start = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())-1, 1, date("Y", tep_fetch_current_timestamp())); 
$previous_month_end = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp())); 
$query_previous_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";
$query = $database->query($query_previous_month);
$result = $database->fetch_array($query);
$previous_month_count = $result['count'];
$previous_month_value = $result['value'];

$queryOrderCC_prev_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";
$query = $database->query($queryOrderCC_prev_month);
$result = $database->fetch_array($query);
$countCC_prev_month = $result['count'];
//$valueCC = $result['value'];
$queryOrderIO_prev_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";
$query = $database->query($queryOrderIO_prev_month);
$result = $database->fetch_array($query);
$countIO_prev_month = $result['count'];
//$valueIO = $result['value'];
$count_all_stuff = $countIO_prev_month+$countCC_prev_month;
$countCC_percentage_prev_month = ($countCC_prev_month * 100) / $count_all_stuff;
($countIO_prev_month==0) ? $countIO_percentage_prev_month = 0 : $countIO_percentage_prev_month = 100-$countCC_percentage_prev_month;

$mail_message .= "<tr>";
$mail_message .= "<td align=\"left\" class=\"main\" style=\"font-size:12px;\"><b>MONEY STATISTICS:</b></td>";
$mail_message .= "</tr>";

//CURRENT$mail_message .='<tr>';  $mail_message .='<td align="left" class="main">&nbsp;</td>';$mail_message .='</tr>';$mail_message .='<tr>';
$mail_message .='<td align="left" class="main"><b>Current Year:</b></td>';
$mail_message .='</tr>';$mail_message .='<tr>';
$mail_message .='<td width="100%">';
$mail_message .='<table width="100%" cellspacing="3" cellpadding="0" style="padding-left:15px;" border="0">';
$mail_message .='<tr>';
$mail_message .='<td class="main" width="6%" >Today (placed)</td>';
$mail_message .='<td class="main" width="16%" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_today.'</a>&nbsp;)</td>';
$mail_message .='<td class="main" width="27%" >$ value of orders placed today:&nbsp;&nbsp;(&nbsp;'.number_format ($value_install_today,2).'&nbsp;)</td>';
$mail_message .='<td class="main" width="51%" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;'.number_format(($value_install_today/$count_install_today),2).'&nbsp;)&nbsp;&nbsp; </td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='</td>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='$ of CC Orders:&nbsp;&nbsp;(&nbsp;'.number_format ($valueCC,2).'&nbsp;)&nbsp;&nbsp;';
$mail_message .='</td>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='$ of Invoice Orders:&nbsp;&nbsp;(&nbsp;';
$mail_message .='    '.number_format ($valueIO,2).'  &nbsp;)';
$mail_message .='</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='<td align="left" class="main">&nbsp;</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='<td class="main" width="18%">Month (placed)</td>';
$mail_message .='<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start='.date("m",$month_first_date).'%2F'.date("d",$month_first_date).'%2F'.date("Y",$month_first_date).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_month.'</a>&nbsp;)</td>';
$mail_message .='<td class="main" >$ value of orders placed this month:&nbsp;&nbsp;(&nbsp;'.number_format($value_install_month,2).'&nbsp;)</td>';
$mail_message .='<td class="main" colspan="3" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;';
if($count_install_month>0){ 
$mail_message .= number_format(($value_install_month/$count_install_month),2);
}
else{$mail_message .='0.00';}
$mail_message .='&nbsp;)</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='<td align="left" class="main">&nbsp;</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='<td class="main" >Month (completed)</td>';
$mail_message .='<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$month_first_date).'%2F'.date("d",$month_first_date).'%2F'.date("Y",$month_first_date).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$this_month_complete_count.'</a>&nbsp;)</td>';
$mail_message .='<td class="main" >$ value of orders completed this month:&nbsp;&nbsp;(&nbsp;'.number_format($this_month_complete_value,2).'&nbsp;)</td>';
$mail_message .='<td class="main" colspan="3" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;';
 if($this_month_complete_count>0)
 { $mail_message .= number_format(($this_month_complete_value/$this_month_complete_count),2);}
else{$mail_message .='0.00';}
$mail_message .='</td> ';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='</td>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='% of CC Orders:&nbsp;&nbsp;(&nbsp;'.number_format ($countCC_percentage_month,2).'%&nbsp;)&nbsp;&nbsp;';
$mail_message .='</td>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='% of Invoice Orders:&nbsp;&nbsp;(&nbsp;';
$mail_message .='    '.number_format ($countIO_percentage_month,2).'%&nbsp;)';
$mail_message .='</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='<td align="left" class="main">&nbsp;</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='<td class="main" >YTD (completed)</td>';
$mail_message .='<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$year_first_date).'%2F'.date("d",$year_first_date).'%2F'.date("Y",$year_first_date).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_ytd.'</a>&nbsp;)</td>';
$mail_message .='<td class="main" >$ value of orders completed from Jan 1:&nbsp;&nbsp;(&nbsp;<a href="'.FILENAME_ADMIN_ORDERS.'?group_by=date&order_status=3&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$year_first_date).'%2F'.date("d",$year_first_date).'%2F'.date("Y",$year_first_date).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.number_format($value_install_ytd,2).'</a>&nbsp;)</td>';
$mail_message .='<td class="main" colspan="3">$ value / # of installs:&nbsp;&nbsp;(&nbsp;';
if($count_install_ytd>0)
{ $mail_message .= number_format(($value_install_ytd/$count_install_ytd),2);}
else{$mail_message .='0.00';}
$mail_message .='&nbsp;)&nbsp;&nbsp;</td>';
$mail_message .='</tr>';
$mail_message .='<tr>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='</td>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='% of CC Orders:&nbsp;&nbsp;(&nbsp;'.number_format ($countCC_percentage_prev_month,2).'%&nbsp;)&nbsp;&nbsp;';
$mail_message .='</td>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='% of Invoice Orders:&nbsp;&nbsp;(&nbsp;';
$mail_message .='    '.number_format ($countIO_percentage_prev_month,2).'%&nbsp;)';
$mail_message .='</td>';
$mail_message .='</tr>';

$mail_message .='<tr>';
$mail_message .='<td align="left" class="main">&nbsp;</td>';
$mail_message .='</tr>';
$mail_message .=' <tr>';
$mail_message .='
    <td class="main">Previous Month (completed):</td>';
$mail_message .='
    <td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$previous_month_start).'%2F'.date("d",$previous_month_start).'%2F'.date("Y",$previous_month_start).'&show_between_end='.date("m",$previous_month_end).'%2F'.date("d",$previous_month_end).'%2F'.date("Y",$previous_month_end).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$previous_month_count.'</a>&nbsp;)</td>';
$mail_message .='
   <td class="main" >$ value of orders completed previous month:&nbsp;&nbsp;(&nbsp;'.number_format($previous_month_value,2).'&nbsp;)</td>';
$mail_message .='
    <td class="main" colspan="3" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;';
if($previous_month_count>0)
{ $mail_message .= number_format(($previous_month_value/$previous_month_count),2);}
else{$mail_message .='0.00';}
$mail_message .='&nbsp;)</td>';
$mail_message .='
</tr>';
$mail_message .='<tr>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='</td>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='% of CC Orders:&nbsp;&nbsp;(&nbsp;'.number_format ($countCC_percentage_prev_month,2).'%&nbsp;)&nbsp;&nbsp;';
$mail_message .='</td>';
$mail_message .='<td class="main" width="22%">';
$mail_message .='% of Invoice Orders:&nbsp;&nbsp;(&nbsp;';
$mail_message .='    '.number_format ($countIO_percentage_prev_month,2).'%&nbsp;)';
$mail_message .='</td>';
$mail_message .='</tr>';
$mail_message .='</table>';
$mail_message .='</td>';
$mail_message .='</tr>';


//PREVIOUS MONTH

$mail_message .= '<tr>';
$mail_message .= '<td align="left" class="main">&nbsp;</td>';
$mail_message .= '</tr>';$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())-1); 
$tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())-1); 
$month_first_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp())-1); 
$month_last_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())+1, 1, date("Y", tep_fetch_current_timestamp())-1); 
$year_first_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp())-1);
$year_last_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp()));
/*$query_install_today = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id  = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
$query = $database->query($query_install_today);
$result = $database->fetch_array($query);
$count_install_today = $result['count'];
$value_install_today = $result['value'];*/
$query_install_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id  = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $month_last_date . "'";
$query = $database->query($query_install_month);
$result = $database->fetch_array($query);
$count_install_month = $result['count'];
$value_install_month = $result['value'];
/////////////
$queryOrderCC_prev_year_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = 1 and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $month_last_date . "'";
$query = $database->query($queryOrderCC_prev_year_month);
$result = $database->fetch_array($query);
$countCC_prev_year_month = $result['count'];
$queryOrderIO_prev_year_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = 1 and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $month_last_date . "'";
$query = $database->query($queryOrderIO_prev_year_month);
$result = $database->fetch_array($query);
$countIO_prev_year_month = $result['count'];
$count_all_stuff = $countIO_prev_year_month+$countCC_prev_year_month;
$countCC_percentage_prev_year_month = ($countCC_prev_year_month * 100) / $count_all_stuff;
//echo $countCC_percentage_month;
($countIO_prev_year_month==0) ? $countIO_percentage_prev_year_month = 0 : $countIO_percentage_prev_year_month = 100-$countCC_percentage_prev_year_month;
////////////////
$query_install_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id  = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $year_last_date . "'";
$query = $database->query($query_install_ytd);
$result = $database->fetch_array($query);
$count_install_ytd= $result['count'];
$value_install_ytd= $result['value'];

/////////////
$queryOrderCC_prev_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = 1 and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $year_last_date . "'";
$query = $database->query($queryOrderCC_prev_year);
$result = $database->fetch_array($query);
$countCC_prev_year = $result['count'];
$queryOrderIO_prev_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $year_last_date . "'";
$query = $database->query($queryOrderIO_prev_year);
$result = $database->fetch_array($query);
$countIO_prev_year = $result['count'];
$count_all_stuff = $countIO_prev_year+$countCC_prev_year;
$countCC_percentage_prev_year = ($countCC_prev_year * 100) / $count_all_stuff;
($countIO_prev_year==0) ? $countIO_percentage_prev_year = 0 : $countIO_percentage_prev_year = 100-$countCC_percentage_prev_year;
////////////////
$mail_message .= '<tr>';
$mail_message .= '<td align="left" class="main"><b>Previous Year (Completed):</b></td>';
$mail_message .= '</tr>';
$mail_message .= '<tr>';
$mail_message .= '<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>';
$mail_message .= '</tr>';
$mail_message .= '<tr>';
$mail_message .= '<td width="100%">';
$mail_message .= '<table width="100%" cellspacing="3" cellpadding="0" style="padding-left:15px;" border="0">';
$mail_message .= '<tr>';
$mail_message .= '<td class="main" >Month</td>';
$mail_message .= '<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$month_first_date).'%2F'.date("d",$month_first_date).'%2F'.date("Y",$month_first_date).'&show_between_end='.date("m",$month_last_date).'%2F'.date("d",$month_last_date).'%2F'.date("Y",$month_last_date).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_month.'</a>&nbsp;)</td>';
$mail_message .= '<td class="main" >$ value of orders placed this month:&nbsp;&nbsp;(&nbsp;'.number_format($value_install_month,2).'&nbsp;)</td>';
$mail_message .= '<td class="main" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;';
 if($count_install_month>0)
{ 

$mail_message .= number_format(($value_install_month/$count_install_month),2);

}

else {$mail_message .= '0.00';}

$mail_message .= '&nbsp;)&nbsp;&nbsp; </td></td>';
$mail_message .= '</tr>';

$mail_message .= '<tr>';
$mail_message .= '<td class="main" width="22%">';
$mail_message .= '</td>';
$mail_message .= '<td class="main" width="22%">';
$mail_message .= ' % of CC Orders:&nbsp;&nbsp;(&nbsp;'.number_format ($countCC_percentage_prev_year_month,2).'%&nbsp;)';
$mail_message .= '</td>';
$mail_message .= '<td class="main" width="22%">';
$mail_message .= '% of Invoice Orders:&nbsp;&nbsp;(&nbsp;';
$mail_message .= '    '.number_format ($countIO_percentage_prev_year_month,2).'%&nbsp;)';
$mail_message .= '</td>';
$mail_message .= '</tr>';
$mail_message .= '<tr>';
$mail_message .= '<td class="main" >YTD</td>';
$mail_message .= '<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$year_first_date).'%2F'.date("d",$year_first_date).'%2F'.date("Y",$year_first_date).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_ytd.'</a>&nbsp;)</td>';
$mail_message .= '<td class="main" >$ value of orders placed from Jan 1:&nbsp;&nbsp;(&nbsp;'.number_format($value_install_ytd,2).'&nbsp;)</td>';
$mail_message .= '<td class="main" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;';
if($count_install_ytd>0)
{$mail_message .=  number_format(($value_install_ytd/$count_install_ytd),2);}
else{$mail_message .=  "0.00";}

$mail_message .= '</tr>';

$mail_message .= '<tr>';
$mail_message .= '<td class="main" width="22%">';
$mail_message .= '</td>';
$mail_message .= '<td class="main" width="22%">';
$mail_message .= ' % of CC Orders:&nbsp;&nbsp;(&nbsp;'.number_format ($countCC_percentage_prev_year,2).'%&nbsp;)';
$mail_message .= '</td>';
$mail_message .= '<td class="main" width="22%">';
$mail_message .= '% of Invoice Orders:&nbsp;&nbsp;(&nbsp;';
$mail_message .= '    '.number_format ($countIO_percentage_prev_year,2).'%&nbsp;)';
$mail_message .= '</td>';
$mail_message .= '</tr>';
$mail_message .= '</table>';
$mail_message .= '</td>';
$mail_message .= '</tr>';



$mail_message .= "<tr>";
$mail_message .= "<td align=\"left\" class=\"main\">&nbsp;</td>";
$mail_message .= "</tr>";

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

 $query_active_post = " select u.user_id, count(o.order_id) as order_count from " . TABLE_ORDERS . " o  inner join " . TABLE_ORDER_TYPES . " ot on o.order_type_id = ot.order_type_id Inner Join  " . TABLE_ORDERS_STATUSES . " os on o.order_status_id = os.order_status_id Inner Join " . TABLE_ADDRESSES . " a on  o.address_id = a.address_id Inner Join " . TABLE_USERS . " u on o.user_id = u.user_id  where  o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 AND o.address_id NOT IN (SELECT address_id FROM " . TABLE_ORDERS . " WHERE (order_type_id = 3 and order_status_id = '3' )  OR order_status_id = 4 ) Group By u.user_id" ;
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
foreach($database->fetch_array($order_query) as $order_result)
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


$message = new email();
$message->add_text($mail_message);
$subject = date("F j, Y") . ' Summary E-mail of Statistics';
$message->build_message();
mail($live_email, $subject, "<html><body>".$mail_message . "</body></html>", "From: " . EMAIL_FROM_NAME . "<" . EMAIL_FROM_ADDRESS . ">\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1" );


 ?>
