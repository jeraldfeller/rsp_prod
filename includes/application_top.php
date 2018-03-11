<?php

	set_time_limit(30);
	if (getenv("SERVER_MODE") == "TEST") {
	  include('configure_dev.php');
	  ini_set('display_errors', true);
	} else {
	  include('configure.php');
	  ini_set('display_errors', false);
	}
    putenv("TZ=US/Eastern");
    date_default_timezone_set("US/Eastern");
    
    include(DIR_FUNCTIONS . 'general.php');
    include(DIR_FUNCTIONS . 'licensing_functions.php');
	include(DIR_INCLUDES . 'database_tables.php');
	include(DIR_INCLUDES . 'database_values.php');
	include(DIR_INCLUDES . 'filenames.php');
	include(DIR_CLASSES . 'Twig/Autoloader.php'); //twig
	include(DIR_CLASSES . 'page.php');

	include(DIR_CLASSES . 'sessions.php');
	include(DIR_CLASSES . 'PDO_MySql.php');
	include(DIR_CLASSES . 'ErrorClass.php');
	include(DIR_CLASSES . 'Transaction.php');
	include(DIR_CLASSES . 'order_emails.php');
	include(DIR_CLASSES . 'orders.php');
	include(DIR_CLASSES . 'email.php');
	include(DIR_CLASSES . 'mime.php');
	include(DIR_CLASSES . 'users.php');
	include(DIR_CLASSES . 'email_template.php');
	include(DIR_CLASSES . 'column_boxes.php');
	include(DIR_CLASSES . 'fckeditor.php');
	include(DIR_CLASSES . 'split_page.php');
	include(DIR_CLASSES . 'zip4.php');
	include(DIR_CLASSES . 'menu_item.php');
	include(DIR_CLASSES . 'callender.php');
	include(DIR_CLASSES . 'cc_proccessing.php');
	include(DIR_CLASSES . 'installer_payments.php');
	include(DIR_CLASSES . 'account.php');
	include(DIR_CLASSES . 'Address.php');
	include(DIR_CLASSES . 'Post.php');
	include(DIR_CLASSES . 'DeferredBilling.php');
	
	$session = new sessions();

	$language_id = '1';
	$language = 'english';
	
	$error = new ErrorClass();
	$error->reset_error();
	
	$database = new PDO_MySql();

	$query = $database->query("select `key_name`, `value` FROM " . TABLE_CONFIGURATION . "");
	$result = $database->fetch_array($query);
	foreach($result as $row){
        if(!defined($row['key_name']))
        {
            define($row['key_name'], $row['value']);
        }
        else
        {
            error_log($row['key_name']." is defined already");
        }
	}
	$session->begin();
	
	$user = new user();
	
	
	$action = '';
		if (isset($_GET['action'])) {
			$action = addslashes($_GET['action']);
		}
		switch($action) {
			case 'login' :
			    //echo "<pre>"; print_r($_POST); die;
				$email_address = trim((isset($_POST['email_address'])) ? $_POST['email_address'] : '');
				$password = trim((isset($_POST['password'])) ? $_POST['password'] : '');
					if (!empty($email_address) && !empty($password) && $user->is_user($email_address, $password)) {
						$user->login_user();
							if ($user->user_group_id == '1') {
								$redirect_page = 'agent_active_addresses.php';
							} elseif($user->user_group_id == '4') { $redirect_page = 'aom_active_addresses.php';
							} elseif($user->user_group_id == '3'){
								$queryPage = $database->query("select page_url from ".TABLE_USERS." where email_address='$email_address'");
								$result = $database->fetch_array($queryPage);
								if(!empty($result['page_url'])){
									$redirect_page = trim($result['page_url']);
								}else{
									$redirect_page = 'account_overview.php';
								}
								
							}else {
								$redirect_page = 'account_overview.php';
							}
						
							if (($pos = strpos($redirect_page, '?')) !== false) {
								$redirect_page = substr($redirect_page, 0, $pos);
							}
							if ($session->php_session_is_registered('login_redirect_page')) {
								$redirect_page = $session->php_return_session_variable('login_redirect_page');
								$session->php_session_unregister('login_redirect_page');
							}
						tep_redirect(HTTP_PREFIX . '/' . $redirect_page);
					} elseif (!empty($email_address) && !empty($password) && $user->is_inactive($email_address, $password)) {
						//$error->add_error('login_box', 'Account currently not active.  Please contact '.BUSINESS_NAME.'.');
						//$usr_id
						$query = $database->query("select user_id, active_status from ".TABLE_USERS." where email_address='$email_address'");
						$result = $database->fetch_array($query);
						$usr_id=$result['user_id'];
						$error->add_error('login_box', "This account is currently in Inactive status. Please click <a href='/index.php?action=reactivate&uid=".$usr_id."'>here</a> to reactivate your account");
					} else {
						$error->add_error('login_box', 'Email and/or password was incorrect.');
					}
			break;
			case 'logout':
				$user->logout_user();
				tep_redirect(HTTP_PREFIX . '/index.php');
			break;
			case 'reactivate':
				$user->reactivate_user($_GET['uid']);
				$error->add_error('login_box', 'Your account has been reactivated.');
				//tep_redirect(HTTP_PREFIX . '/index.php');
			break;
		}
	
?>
