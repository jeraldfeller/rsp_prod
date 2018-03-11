<?php
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
	
	//print_r();
	//echo $submit_type;

    /*        if (!empty($update_account) && ($update_account == 'update') && (!empty($submit_type_x) && !empty($submit_type_y))) { */
        if (!empty($change_password)) {     
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
						$database->query("update " . TABLE_USERS . " set password = '" . md5($password) . "' , next_password_reminder='".$tim30_this_day."', last_password_update = '" . time() . "' where user_id = '" . $user->fetch_user_id() . "'");
						
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
		//var_dump($error);				
		echo $twig->render('common/account_change_password.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'page_action'=>$page_action));
?>