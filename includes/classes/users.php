<?php
	class user {
		var $status;
		var $name;
		var $user_id;
		var $agent_id;
		var $user_group_id;
		var $accounts_payable;
		var $agency_id;
		var $personal_invoice;
		var $can_place_orders = true;
		var $credit_balance = '';
        function __construct() {
			global $session;
				if ($session->php_session_is_registered('user_id')) {
					$this->user_id = $session->php_return_session_variable('user_id');
					$this->user_group_id = $session->php_return_session_variable('user_group_id');
					$this->name = $session->php_return_session_variable('user_name');
					$this->agent_id = $session->php_return_session_variable('user_agent_id');
					$this->status = true;
					$this->accounts_payable = $session->php_return_session_variable('accounts_payable');
					$this->agency_id = $session->php_return_session_variable('agency_id');
					$this->personal_invoice = $session->php_return_session_variable('personal_invoice');
					$this->can_place_orders = $session->php_return_session_variable('can_place_orders');
					$this->credit_balance = $session->php_return_session_variable('credit_balance');
				} else {
					$this->user_id = false;
					$this->user_group_id = 0;
					$this->name = 'Guest';
					$this->status = false;
					$this->agent_id = false;
					$this->accounts_payable = false;
					$this->agency_id = false;
					$this->personal_invoice = false;
					$this->can_place_orders = true;
					$this->credit_balance = '';
				}
		}
		
		function user_is_logged() {
			return $this->status;
		}
		
		function set_user_id($id) {
			//For create account.
			$this->user_id = $id;
		}
		
		function fetch_user_id() {
			return $this->user_id;
		}
		
		function fetch_user_name() {
			return $this->name;
		}
		
		function fetch_user_group_id() {
			return $this->user_group_id;
		}
		
		function fetch_billing_method_id() {
			global $session;
				return $session->php_return_session_variable('billing_method_id');
		}
		
		function fetch_service_level_id() {
			global $session;
				return $session->php_return_session_variable('service_level_id');
		}
		
		function is_user($email_address, $password) {
			global $database;
				//$query = $database->query("select user_id from " . TABLE_USERS . " where email_address = '" . $email_address . "' and password = '" . md5($password) . "' limit 1");
//print "select t1.user_id  from emails_to_users as t2,". TABLE_USERS  ." as t1 where t1.email_address = '" . $email_address . "' or t2.email_address = '" . $email_address . "' and password = '" . md5($password) . "' limit 1";exit;
				$query = $database->query("select u.user_id  from ". TABLE_USERS  ." u left join " . TABLE_EMAILS_TO_USERS. " etu on (u.user_id = etu.user_id) where (u.email_address = '" . $email_address . "' or (etu.email_to_user_id is not null and etu.email_address != '' and etu.email_address = '" . $email_address . "')) and u.password = '" . md5($password) . "' and u.user_active_id = '1' and u.active_status = '1' limit 1");
				$result = $database->fetch_array($query);
					if (($result['user_id'] != NULL) && is_numeric($result['user_id'])) {
						//Got user.
						$this->user_id = $result['user_id'];
						return true;
					} else {
						return false;
					}
		}
		
		function is_inactive($email_address, $password) {
			global $database;
				//$query = $database->query("select user_id from " . TABLE_USERS . " where email_address = '" . $email_address . "' and password = '" . md5($password) . "' limit 1");
//print "select t1.user_id  from emails_to_users as t2,". TABLE_USERS  ." as t1 where t1.email_address = '" . $email_address . "' or t2.email_address = '" . $email_address . "' and password = '" . md5($password) . "' limit 1";exit;
				$query = $database->query("select u.user_id, u.active_status from ". TABLE_USERS  ." u left join " . TABLE_EMAILS_TO_USERS. " etu on (u.user_id = etu.user_id) where (u.email_address = '" . $email_address . "' or (etu.email_to_user_id is not null and etu.email_address != '' and etu.email_address = '" . $email_address . "')) and u.password = '" . md5($password) . "' and u.active_status = '0' limit 1");
				$result = $database->fetch_array($query);
					if (($result['user_id'] != NULL) && ($result['active_status'] == '0')) {
						//Got user.
						return true;
					} else {
						return false;
					}
		}
		
		function log_user_pages() {
				global $database, $user;
					if (!$this->user_is_logged()) {
						return;
					}
				
			$database->query("update " . TABLE_USERS . " set last_page_id = '" . PAGE_ID . "', last_page_view = '" . time() . "' where user_id = '" . $this->user_id . "' limit 1");
		}
		
		function login_user($override = '') {
			global $session, $database;
				if (!is_numeric($this->user_id)) {
					return false;
				}
			//First update the user table.
			if($override=="")
			{
				$database->query("update " . TABLE_USERS . " set last_login = '" . tep_return_timestamp() . "', ip_address = '" . tep_return_ip_address() . "', login_status_id ='1'  where user_id = '" . $this->user_id . "' limit 1");
			}
			
			//Now get the data.
			$query = $database->query("select u.agent_id, u.agency_id, ud.firstname, u.personal_invoice, ud.lastname, utug.user_group_id, u.billing_method_id, u.accounts_payable, u.service_level_id, u.order_hold from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = '" . $this->user_id . "' and u.user_id = ud.user_id and u.user_id = utug.user_id limit 1");
			$result = $database->fetch_array($query);

				if ($result['user_group_id'] == 5) {
					$result['accounts_payable'] = '1';
				}
				if ($result['personal_invoice'] == '1') {
					if ($result['order_hold'] == '1') {
						$can_place_orders = false; 
					} else {
						$can_place_orders = true; 
					}
				} else {
					$agency_query = $database->query("select order_hold from " . TABLE_AGENCYS . " where agency_id = '" . $result['agency_id'] . "' limit 1");
					$agency_result = $database->fetch_array($agency_query);
						if ($agency_result['order_hold'] == '1') {
							$can_place_orders = false; 
						} else {
							$can_place_orders = true; 
						}
				}
			//Now store in the session.
			$session->php_session_unregister('user_id');
			$session->php_session_unregister('user_group_id');
			$session->php_session_unregister('user_name');
			$session->php_session_unregister('user_first_name');
			$session->php_session_unregister('user_last_name');
			$session->php_session_unregister('user_agent_id');
			$session->php_session_unregister('billing_method_id');
			$session->php_session_unregister('service_level_id');
			$session->php_session_unregister('accounts_payable');
			$session->php_session_unregister('agency_id');
			$session->php_session_unregister('personal_invoice');
			$session->php_session_unregister('can_place_orders');
			
			$session->php_session_register('user_id', $this->user_id);
			$session->php_session_register('user_group_id', $result['user_group_id']);
			$session->php_session_register('user_name', ($result['firstname'].' '.$result['lastname']));
			$session->php_session_register('user_first_name', $result['firstname']);
			$session->php_session_register('user_last_name', $result['lastname']);
			$session->php_session_register('user_agent_id', $result['agent_id']);
			$session->php_session_register('billing_method_id', $result['billing_method_id']);
			$session->php_session_register('service_level_id', $result['service_level_id']);
			$session->php_session_register('accounts_payable', $result['accounts_payable']);
			$session->php_session_register('agency_id', $result['agency_id']);
			$session->php_session_register('personal_invoice', $result['personal_invoice']);
			$session->php_session_register('can_place_orders', $can_place_orders);
			
			if($result['user_group_id'] == 1 || $result['user_group_id'] == 4){
                $session->php_session_register('credit_balance', $this->get_agent_credit_balance($this->user_id));
			}
			
			$this->user_group_id = $result['user_group_id'];
			
		}
		
		function logout_user() {
			global $session, $database;
			$session->php_session_unregister('user_id');
			$session->php_session_unregister('user_group_id');
			$session->php_session_unregister('user_name');
			$session->php_session_unregister('user_first_name');
			$session->php_session_unregister('user_last_name');
			$session->php_session_unregister('user_agent_id');
			$session->php_session_unregister('billing_method_id');
			$session->php_session_unregister('service_level_id');
			$session->php_session_unregister('accounts_payable');
			
			//Time to kill all.
			$_SESSION = array();
			$database->query("update " . TABLE_USERS . " set login_status_id ='0' where user_id = '" . $this->user_id . "' limit 1");
		}
		
		function user_can_view_menu($menu_id) {
			global $database;
				//Count menu items that the user can view in this menu group and if more than one then return true.
				$query = $database->query("select count(p.page_id) as count from " . TABLE_PAGES . " p left join " . TABLE_USER_GROUPS_TO_PAGES . " ugtp on (p.page_id = ugtp.page_id) where p.page_group_id = '" . $menu_id . "' and ugtp.user_group_id = '" . $this->user_group_id . "' and (p.page_lock_status = '0' or ugtp.page_id is not NULL)");
				$result = $database->fetch_array($query);
					if ($result['count'] > 0) {
						return true;
					} else {
						return false;
					}
		}
		
		function user_can_view_page() {
			global $database, $page, $session;
			
			$query = $database->query("select page_lock_status from " . TABLE_PAGES . " where page_id = '" . $page->fetch_page_id() . "' limit 1");
			$result = $database->fetch_array($query);
				if ($result['page_lock_status'] == '0') {
					return false;
				}
			$user_group_id = "ugtp.user_group_id = '" . $this->user_group_id . "'";
				if ($this->accounts_payable == '1') {
					$user_group_id .= " or ugtp.user_group_id = '5'";
				}
			$query = $database->query("select count(p.page_id) as count from " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES . " p where p.page_id = '" . $page->fetch_page_id() . "' and p.page_id = ugtp.page_id and (" . $user_group_id . ")");
			$result = $database->fetch_array($query);
				if ($result['count'] > 0) {
					return false;
				} else {
						if ($this->user_is_logged()) {
							return FILENAME_403;
						} else {
							$session->php_session_unregister('login_redirect_page');
							$session->php_session_register('login_redirect_page', $page->fetch_page_url());
							return FILENAME_LOGIN;
						}
				}
				/*if ($this->user_is_logged()) {
					//Check the user group and if that group can view the page and if not sedn command to redirect to fail page.
					$query = $database->query("select page_lock_status, page_group_id from " . TABLE_PAGES . " where page_id = '" . $page->fetch_page_id() . "' limit 1");
					$result = $database->fetch_array($query);
						if (($result['page_lock_status'] == '1') && (($result['page_group_id'] != '0') && ($result['page_group_id'] != $this->user_group_id))) {
							return FILENAME_403;
						} else {
							return false;
						}
				} else {
					//Check if the page requires a login and if so then send command to redirect user to login page.
					$query = $database->query("select page_lock_status from " . TABLE_PAGES . " where page_id = '" . $page->fetch_page_id() . "' limit 1");
					$result = $database->fetch_array($query);
						if ($result['page_lock_status'] == '1') {
							$session->php_session_unregister('login_redirect_page');
							$session->php_session_register('login_redirect_page', $page->fetch_page_url());
							return FILENAME_LOGIN;
						} else {
							return false;
						}
				}*/
		}
		
		function generate_login_string() {
			$return_string = '';
				if ($this->status) {
					$return_string = 'You are currently logged in as <strong>' . $this->name . '</strong>.  If this is not you please <a href="index.php?action=logout">logout here</a>.';
				}	
			return $return_string;
		}
		
		function reactivate_user($id) {
			global $database;
			$id = intval($id);
			if($database->query("update " . TABLE_USERS . " set active_status ='1' where user_id = '" . $id . "' limit 1")) {
				return true;
			} else {
				return false;
			}
			
		}
		
		function get_agent_credit_balance($user_id){
            global $database;
            $query = $database->query("SELECT running_total FROM " . TABLE_ACCOUNTS . " WHERE user_id = $user_id");
            $running_balance = $database->fetch_array($query);
            if($running_balance > 0 ){
                return 'Credit balance $'. $running_balance['running_total'] . ' | ';
            }else{
            	return '';
			}
		} 
	}
	
	
	
?>
