<?php
//error_reporting(0);
	$page_action = tep_fill_variable('page_action', 'get');
	$mID = tep_fill_variable('mID', 'get');
	$submit_value = tep_fill_variable('submit_value', 'post');

	$message = '';
	$emails = tep_fill_variable('emails', 'post', array());
	$user_group_id = tep_fill_variable('user_group_id', 'post');
	$order_status = tep_fill_variable('order_status', 'post');
	//print "------=$order_status=---";	
			$manual_email_id = tep_fill_variable('manual_email_id', 'post');
			$user_group_id = tep_fill_variable('user_group_id', 'post');
			$subject = tep_fill_variable('subject', 'post');
			$content = tep_fill_variable('content', 'post');
			
	
                       


$cat=$_REQUEST['export_cat'];
//print "---$cat----";
if( isset($_POST['issubmit'])){
$value=1;
$startdate=$_POST['startdate']; 
$enddate=$_POST['enddate']; 
$pieces = explode("/", $startdate);
$smonth=$pieces[0];
$sday=$pieces[1];
$syear=$pieces[2];
$startday = mktime(0, 0, 0, $smonth, $sday, $syear);
$endpieces = explode("/", $enddate);
$dmonth=$endpieces[0];
$dday=$endpieces[1];
$dyear=$endpieces[2];
$endday = mktime(0, 0, 0, $dmonth, $dday, $dyear);

/***************************/
$filename="includes/content/report.csv";
$fp = fopen( "$filename" , "wb" );

$data="Order Id,User Id,Address Id,Order Type ID,Total Order,Date Added, Agent Scheduled Date,Last modified,Date Completed,Order status Id  ";
fwrite( $fp, $data );

/****************************/
$sql="select * from " . TABLE_ORDERS . " where date_added between '" . $startday . "' and  '" . $endday . "'";
$query = $database->query( $sql);
while($result = $database->fetch_array($query)) {
 $data.=$result['order_id'].",".$result['user_id'].",".$result['address_id'].",".$result['order_type_id'].",".$result['order_total'].",".$result['date_added'].",".$result['date_schedualed'].",".$result['last_modified'].",".$result['date_completed'].",".$result['order_status_id']."\r\n";
}
//print(nl2br($data));exit();
$success=fwrite ($fp, $data);
//$cont=file("$filename");
$csv_string=$data;

print_r($csv_string);
$this->set_download('text/csv', 'database_export.csv', $csv_string);
if($success)
{
echo "Database is exported <br> <br>";
}
}
if(isset($_POST['issubmituser']))
{
$from = tep_fill_variable('startdateuser', 'post');

$to = tep_fill_variable('enddateuser', 'post');
print "to date is $to<br>";
$date_sent_time_stamp = mktime();
if(!empty($from)) 
{
$from_time_stamp = strtotime($from);
}
if(!empty($to)) 
{
$to_time_stamp = strtotime($to);
}
if(($from_time_stamp!="")&($from_time_stamp!=""))
{
//print "select u.email_address,u.date_created,ud.firstname,ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud  where u.user_id = ud.user_id and u.date_created BETWEEN $from_time_stamp AND $to_time_stamp";
$filename="includes/content/reportnew.csv";
$fp = fopen( "$filename" , "wb" );
$data="E-mail address,First name,Last name,sign-up date";
fwrite( $fp, $data );

$database->query("select u.email_address,u.date_created,ud.firstname,ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud  where u.user_id = ud.user_id and u.date_created BETWEEN $from_time_stamp AND $to_time_stamp") or die(mysql_error());
print "database qry result is $database";

while($result = $database->fetch_array($database)) 
{
$email=$result['email_address'];
print "---$email----<br>";
$data.=$result['email_address'].",".$result['firstname'].",".$result['lastname'].",".$result['date_created']."\r\n";
}

/*$success=fwrite ($fp, $data);
$cont=file("$filename");
$csv_string=$data;
$this->set_download('text/csv', 'database_export_user.csv', $csv_string);
if($success)
{
echo "Database is exported <br> <br>";
}*/

}
}
if(isset($query)) 
{

}
?>
<table width="100%" class="pageBox" cellspacing="0" cellpadding="2" border="0">
<!--<form method="POST"  name="frm" action="">-->

<form method="post" action="#" name="frmexp">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td class="pageBoxContent" width="130">Export</td><td class="pageBoxContent"><select name="export_cat" onChange="javascript: document.frmexp.submit()">
<option value="0">Select one</option>
<option value="users" <?php if($cat=='users'){?> selected <?php }?>>Users</option>

<option value="orders"<?php if($cat=='orders'){?> selected <?php }?>>Orders</option>
</select></td>
					</tr>
</form>
<form method="post" action="#" name="frm">
<?php
if($cat=='users')
{
?>
<tr>
<td class="pageBoxContent" width="130"></td><td class="pageBoxContent"><?php echo tep_draw_user_status_pulldown('order_status'); ?></td>
</tr>
<?php
}
?>					
<tr>
<td class="pageBoxContent" width="130" colspan="2"> specify date below.</td></tr>
<?php
if($cat=='orders')
{
?>					
<tr>
<td	class="pageBoxContent">From: <input type="text" name="startdate" value=""/>(mm/dd/yyyy)</td><td class="pageBoxContent">To: <input type="text" name="enddate" value=""/>(mm/dd/yyyy)</td>
</tr>
<tr>					
<td height="6" colspan="2" align="center"><input type="submit" value="Submit" onclick="check()">
<input type="hidden" name="issubmit"></td>
</tr>
<tr>
<?php
}
else if($cat=='users')
{
?>
<tr>
<td	class="pageBoxContent">From: <input type="text" name="startdateuser" value=""/>(mm/dd/yyyy)</td><td class="pageBoxContent">To: <input type="text" name="enddateuser" value=""/>(mm/dd/yyyy)</td>
</tr>
<tr>					
<td height="6" colspan="2" align="center"><input type="submit" value="Submit" onclick="check()">
<input type="hidden" name="issubmituser"></td>
</tr>
<?php
}

?>

<td class="pageBoxContent" colspan="2"></td>
</tr>
</form>
</table>



