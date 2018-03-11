<?php
if($user->fetch_user_group_id()==1) {
	header('Location: agent_account_change_password.php');
}
	$change_password = tep_fill_variable('change_password', 'get');
	//$old_password = tep_fill_variable('old_password');  // disabled 2014-03-15
	$password = tep_fill_variable('new_password');
	$password_confirmation = tep_fill_variable('password_confirmation');
	$force_password_change = tep_fill_variable('force_password_change', 'session');
	$password_success = false;
	$dat = date("Y/m/d H:i:s");
	$timestap_this_day=strtotime("$dat");
	$tim30_this_day=strtotime("+30 day");
	$page_action = tep_fill_variable('page_action', 'get');
    $submit_type = tep_fill_variable('submit_type');
    $submit_type_x = tep_fill_variable('submit_type_x');
    $submit_type_y = tep_fill_variable('submit_type_y');

    /*        if (!empty($update_account) && ($update_account == 'update') && (!empty($submit_type_x) && !empty($submit_type_y))) { */
        if (!empty($submit_type_x) && !empty($submit_type_y)) {     
//		if ($change_password == 'update') {
			//if (empty($old_password)) {
			//	$error->add_error('account_change_password', 'Please enter your Old Password.');
			//}
			if (empty($password)) {
				$error->add_error('account_change_password', 'Please enter your New Password.');
			}
			if (empty($password_confirmation)) {
				$error->add_error('account_change_password', 'Please enter your Password Confirmation.');
			}
			if (!empty($password) && !empty($password_confirmation) && ($password != $password_confirmation)) {
				$error->add_error('account_change_password', 'Your New Password and Password Confirmation do not match.');
			}
			if (!$error->get_error_status('account_change_password')) {
				//$query = $database->query("select count(user_id) as count from " . TABLE_USERS . " where user_id = '" . $user->fetch_user_id() . "' and password = '" . md5($old_password) . "' limit 1");
				//$result = $database->fetch_array($query);
				//	if ($result['count'] == '0') {
				//		$error->add_error('account_change_password', 'Your Old Password is incorrect.');
				//	} else {
//print "update " . TABLE_USERS . " set password = '" . md5($password) . "' and next_password_reminder='".$tim30_this_day."' where user_id = '" . $user->fetch_user_id() . "' limit 1";exit;
						$database->query("update " . TABLE_USERS . " set password = '" . md5($password) . "' , next_password_reminder='".$tim30_this_day."', last_password_update = '" . mktime() . "' where user_id = '" . $user->fetch_user_id() . "'");
						
						tep_redirect(PAGE_URL.'?page_action=success');
				//	}
			}
		}
		if ($page_action == 'success') {
            $session->php_session_unregister('force_password_change');
            $error->add_error('account_change_password', 'Your password has been successfully updated.  Please make sure to use it from now on.', 'success');
        }

        if (!empty($force_password_change)) {
            $error->add_error('account_change_password', 'You must reset your Password.');
	    }
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($page_action != 'success') {
	?>
	<tr>
		<td class="style9">&PAGE_TEXT</td>
	</tr>
	<tr>
		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
		<td valign="top">
		<form name="change_password" method="post" action="<?php echo FILENAME_ACCOUNT_CHANGE_PASSWORD; ?>?change_password=update">
			<table cellspacing="0" cellpadding="0" class="pageBox">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="3">
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<?php
								
								if ($error->get_error_status('account_change_password')) {
							?>
							<tr>
								<td class="mainError" colspan="2"><?php echo $error->get_error_string('account_change_password'); ?></td>
							</tr>
							<?php
								}
								if ($page_action != 'success') {
							?>
							<!--<tr>
								<td class="main">Old Password: </td><td><input type="password" name="old_password" /></td>
                            </tr>
                            -->
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td class="main">New Password: </td><td><input type="password" name="new_password"/></td>
							</tr>
							<tr>
								<td class="main">New Password Confirm: </td><td><input type="password" name="password_confirmation" /></td>
							</tr>
							
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<table cellspacing="0" cellpadding="0">
										<tr>
											<td><?php echo tep_create_button_link('reset', 'Reset Form', ' onclick="document.forms[\'change_password\'].reset();"'); ?></td>
											<td width="40"><img src="images/pixel_trans.gif" height="1" width="40" /></td>
											<td><?php echo tep_create_button_submit('change_password', 'Change Password', 'name="submit_type"', 'value="Change Password"')?></td>
										</tr>
									</table>
								</td>
							</tr>
							<?php
								}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
				</tr>
				<tr>
					<td class="mainSmall"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<?php
		} else {
	?>
	<tr>
		<td width="100%" align="left"><?php echo $error->get_error_string('account_change_password', 'success'); ?></td>
	</tr>
	<?php
		}
	?>
</table>
