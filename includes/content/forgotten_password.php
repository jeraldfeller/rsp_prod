<?php
ob_start();
error_reporting(0);
$head=getenv("HTTP_REFERER");
$message = '';
$pages = tep_fill_variable('pages', 'post', array());
if(isset($_POST['submit']))
{
$dat = date("Y/m/d H:i:s");
$timestap_this_day=strtotime("$dat");
$email_address=$_POST['txtemail'];
//print "select * from ".TABLE_USERS." where email_address='$email_address'";
$query = $database->query("select user_id, active_status from ".TABLE_USERS." where email_address='$email_address'");
$num_rows=$database->num_rows($query);
if($num_rows > 0)
{
	$result = $database->fetch_array($query);
	$usr_id=$result['user_id'];
	
	$active_status=$result['active_status'];
	//if user is active
	if($active_status != 0) {
		$query1 = $database->query("select * from ".TABLE_USERS_DESCRIPTION." where user_id ='$usr_id'");
		$result1 = $database->fetch_array($query1);
		$first_name=$result1['firstname'];
		$last_name=$result1['lastname'];
		//$password=$result1[''];

		//print "$first_name<br>$last_name";

		$letter_seed = "abcdefghjkmnpqrstuvwxyz";
		$special_seed = "23456789!?@#$%";

		$validClientIDLetters = str_split($letter_seed . strtoupper($letter_seed));
		$validClientIDSpecial = str_split($special_seed);
		$validClientIDCombined = $validClientIDLetters + $validClientIDSpecial;

		$letter_count = count($validClientIDLetters);
		$special_count = count($validClientIDSpecial);
		$combo_count = count($validClientIDCombined);

		$password = $validClientIDLetters[rand(0,$letter_count-1)];
		$password.= $validClientIDCombined[rand(0,$combo_count-1)];
		$password.= $validClientIDCombined[rand(0,$combo_count-1)];
		$password.= $validClientIDSpecial[rand(0,$special_count-1)];
		$password.= $validClientIDCombined[rand(0,$combo_count-1)];
		$password.= $validClientIDCombined[rand(0,$combo_count-1)];
		$password.= $validClientIDLetters[rand(0,$letter_count-1)];

		$query_update = $database->query("update ".TABLE_USERS." set password=md5('$password') , next_password_reminder='-1' where user_id ='$usr_id'");
		if($query_update)
		{
		$email_template = new email_template('forgotten_password');
		$email_template->load_email_template();
		$email_template->set_email_template_variable('PASSWORD', $password);
		$email_template->parse_template();
		$email_template->send_email($email_address, $first_name.','.$last_name);
        $email_template->send_email(ADMIN_EMAIL, $first_name.','.$last_name);
		$extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $usr_id . "' and email_status = '1'");
			while($extra_result = $database->fetch_array($extra_query)) {
				$email_template->send_email($extra_result['email_address'], $first_name.','.$last_name);	
			}
		if($email_template)
		{
		$msg="Your Password has been successfully sent to $email_address";
		}
		}
	} else {
		//if user is not active
		$msg="This account is currently in Inactive status. Please click <a href='/index.php?action=reactivate&uid=".$usr_id."'>here</a> to reactivate your account.";
	}
	
	


}
else
{
$msg="Wrong details entered. Please recheck.";
}
}
	//Forgotten Password
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td>Fill in the details below for retrieving your Password.  If you have any problems then click here for the help file or use the contact form to contact us.</td>
	</tr>
	<tr>
		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
		<td valign="top">

<?
/************************************************************************************************/
?>
<table cellspacing="0" cellpadding="0" class="pageBox" border="0" width="100%">
<tr><td>

<form name="forgot" method="POST" action="<?php echo PAGE_URL; ?>">
<table cellpadding="0" cellspacing="3" border="0">
<tr><td class="subHeading" colspan="2">Retrieve your Password</td></tr>
<tr><td height="5" colspan="2" class="mainError"><?php echo $msg ?></td></tr>
<tr><td height="5" colspan="2"></td></tr>
<tr><td height="5" colspan="2"><img src="images/pixel_trans.gif" height="5" width="1"></td></tr>
<tr><td height="3" colspan="2"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>
<tr><td class="main">Email Address: </td><td><input type="text" name="txtemail">&nbsp;&nbsp;
<input type="submit" name="submit" value="submit"></td></tr>
</table></form>

</td></tr>

<tr><td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td></tr>
<tr><td class="mainSmall"></td></tr>
</table>


<?
/*******************************************************************************************/
?>

		</td>
		<td valign="top" class="main">&PAGE_TEXT</td>
	</tr>
</table>
