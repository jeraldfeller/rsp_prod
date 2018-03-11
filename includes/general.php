<?php
	function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {

    if (SEND_EMAILS != 'true' || getenv("SERVER_MODE") == "TEST") return false;



    // Instantiate a new mail object

    $message = new email(array('X-Mailer: Realty Sign Post Mailer'));



    // Build the text version

    $text = strip_tags($email_text);

    if (EMAIL_USE_HTML == 'true') {

      $message->add_html($email_text, $text);

    } else {

      $message->add_text($text);

    }



    // Send message

    $message->build_message();

    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);

  }
  
    function tep_not_null($value) {

    if (is_array($value)) {

      if (sizeof($value) > 0) {

        return true;

      } else {

        return false;

      }

    } else {

      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {

        return true;

      } else {

        return false;

      }

    }

  }
  
  function tep_convert_linefeeds($from, $to, $string) {

    if ((PHP_VERSION < "4.0.5") && is_array($from)) {

      return ereg_replace('(' . implode('|', $from) . ')', $to, $string);

    } else {

      return str_replace($from, $to, $string);

    }

  }
  
  function tep_return_ip_address() {
  	return $_SERVER['REMOTE_ADDR'];
  }
  
  function tep_return_timestamp() {
  	return mktime();
  }
  
  function tep_redirect($location) {
  	global $session;
		header("Location: " . $session->proccess_url($location));
		die();
  }
  
  function tep_generate_pulldown_menu($name, $contents = array(), $selected = '', $params = '') {
		$return_string = '<select name="'.$name.'"'.((!empty($params)) ? ' '.$params: '').'>';
		$count = count($contents);
		$n = 0;
			while($n < $count) {
				$this_selected = '';
					if ($contents[$n]['id'] == $selected) {
						$this_selected = ' SELECTED';
					}
				$return_string .= '<option value="'.$contents[$n]['id'].'"'.$this_selected.'>'.$contents[$n]['name'].'</option>';
				$n++;
			}
		$return_string .= '</select>';
	return $return_string;
  }
  
  function tep_fill_variable($id, $method = 'post', $default = '') {
  	$return = $default;
		if ($method == 'post') {
			if (isset($_POST[$id]) && !empty($_POST[$id])) {
				if (is_array($_POST[$id])) {
					$return = $_POST[$id];
				} else {
					$return = addslashes(stripslashes($_POST[$id]));
				}
			}
		} elseif ($method == 'get') {
			if (isset($_GET[$id]) && !empty($_GET[$id])) {
				if (is_array($_GET[$id])) {
					$return = $_GET[$id];
				} else {
					$return = addslashes(stripslashes($_GET[$id]));
				}
			}
		} elseif ($method == 'session') {
			if (isset($_SESSION[$id]) && !empty($_SESSION[$id])) {
				if (is_array($_SESSION[$id])) {
					$return = $_SESSION[$id];
				} else {
					$return = addslashes(stripslashes($_SESSION[$id]));
				}
			}
		}
	return $return;
  }
  
  function tep_validate_phone_number($number = '') {
		if (empty($number) || (strlen($number) < 7)) {
			return false;
		} else {
			return true;
		}
  }
  
  function tep_validate_email_address($email = '') {
		if (empty($email) || (strpos($email, '@') === false)) {
			return false;
		} else {
			return true;
		}
  }
  
  function tep_email_address_exists($email = '') {
		global $database, $user;
			$extra_string = '';
				if ($user->user_is_logged()) {
					$extra_string = " and user_id != '" . $user->fetch_user_id() . "'";
				}
			$query = $database->query("select count(user_id) as count from " . TABLE_USERS . " where email_address = '" . $email . "'" . $extra_string);
			$result = $database->fetch_array($query);
				if ($result['count'] == 0) {
					return false;
				} else {
					return true;
				}
  }
  
  function tep_get_order_type_name($id) {
  		global $database;
			$query = $database->query("select name from " . TABLE_ORDER_TYPES . " where order_type_id = '" . $id . "' limit 1");



			$result = $database->fetch_array($query);
			
			return $result['name'];
  }
  
  function tep_fetch_email_data($user_id) {
  	global $database;
		$query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $user_id . "' and u.user_id = ud.user_id limit 1");
		$result = $database->fetch_array($query);
	return $result;
  }
  
  function tep_get_service_level_name($id) {
  		global $database;
			$query = $database->query("select name from " . TABLE_SERVICE_LEVELS . " where service_level_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
			
			return $result['name'];
  }
  
  function tep_get_equipment_name($id) {
  		global $database;
			$query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
			
			return $result['name'];
  }
  
  function tep_get_order_status_name($id) {
  		global $database;
			$query = $database->query("select order_status_name from " . TABLE_ORDERS_STATUSES . " where order_status_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
			
			return $result['order_status_name'];
  }
  
  function tep_draw_order_type_pulldown($name = '', $selected = '', $params = '', $array = array()) {
  		global $database;
			$return = '';
			$query = $database->query("select order_type_id, name from " . TABLE_ORDER_TYPES . " order by name");
			foreach($query as $result){
						$array[] = array('id' => $result['order_type_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }

  function tep_draw_agent_pulldown($name = '', $selected = '', $params = '', $array = array()) {
  		global $database;
			$return = '';
			$query = $database->query("select u.user_id, u.agent_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '1' and utug.user_id = u.user_id and u.user_id = ud.user_id order by ud.firstname");
			foreach($query as $result){
						$array[] = array('id' => $result['user_id'], 'name' => ($result['firstname'].' '.$result['lastname'].' ('.$result['agent_id'].')'));
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_draw_order_type_all_pulldown($name = '', $selected = '', $params = '') {
  		global $database;
			$return = '';
			$array = array();
			$array[] = array('id' => '', 'name' => 'All');
			$query = $database->query("select order_type_id, name from " . TABLE_ORDER_TYPES . " order by name");
			foreach($query as $result){
						$array[] = array('id' => $result['order_type_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }


    function tep_draw_user_pulldown($name = '', $selected = '', $params = '') {
  		global $database;
			$return = '';
			$array = array();
			$query = $database->query("select user_id, email_address from " . TABLE_USERS . " order by email_address");
			foreach($query as $result){
						$array[] = array('id' => $result['user_id'], 'name' => $result['email_address']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_get_county_name($id) {
  		global $database;
			$query = $database->query("select name from " . TABLE_COUNTYS . " where county_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
			
			return $result['name'];
  }
  
  function tep_get_state_name($id) {
  		global $database;
			$query = $database->query("select name from " . TABLE_STATES . " where state_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
			
			return $result['name'];
  }
  
  function tep_get_payment_type_name($id) {
  		global $database;
			$query = $database->query("select name from " . TABLE_BILLING_METHODS . " where billing_method_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
			
			return $result['name'];
  }
  
  function tep_get_default_billing_method($user_id) {
  		global $database;
			$query = $database->query("select billing_method_id from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
			$result = $database->fetch_array($query);
		return $result['billing_method_id'];
  }
  
  function tep_get_service_level_id($user_id) {
  		global $database;
			$query = $database->query("select service_level_id from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");
			$result = $database->fetch_array($query);
		return $result['service_level_id'];
  }
  
  function tep_get_total_orders() {
  		global $database, $user;
			$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user->fetch_user_id() . "'");
			$result = $database->fetch_array($query);
			
			return $result['count'];
  }
  
  function tep_get_active_orders() {
  		global $database, $user;
			$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a where o.user_id = '" . $user->fetch_user_id() . "' and o.address_id = a.address_id and a.status = '1'");
			$result = $database->fetch_array($query);
			
			return $result['count'];
  }
  
  function tep_get_pending_installs() {
  		global $database, $user;
			$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user->fetch_user_id() . "' and order_status_id = '1'");
			$result = $database->fetch_array($query);
			
			return $result['count'];
  }
  
  function tep_get_schedualed_installs() {
  		global $database, $user;
			$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user->fetch_user_id() . "'");
			$result = $database->fetch_array($query);
			
			return $result['count'];
  }
  
  function tep_draw_state_pulldown($name = '', $selected = '', $params = '') {
  		global $database;
			$return = '';
			$state_array = array();
				if (empty($selected)) {
					$state_array[] = array('id' => '', 'name' => 'Please Select');
				}
			$query = $database->query("select state_id, name from " . TABLE_STATES . " order by name");

				foreach($query as $result)
				{
						$state_array[] = array('id' => $result['state_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $state_array, $selected, $params);
  }
  
  function tep_draw_discount_type_pulldown($name = '', $selected = '') {
  		global $database;
			$return = '';
			$array = array();
			$array[] = array('id' => '1', 'name' => 'Amount');
			$array[] = array('id' => '2', 'name' => 'Percentage');
			$count = count($array);
			$n = 0;
				while($n < $count) {
					$result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
					$n++;
				}
  		return tep_generate_pulldown_menu($name, $result_array, $selected);
  }
  
  function tep_draw_personalized_type_pulldown($name = '', $selected = '', $params = '') {
  		global $database;
			$return = '';
			$array = array();
			$array[] = array('id' => '0', 'name' => 'No');
			$array[] = array('id' => '1', 'name' => 'Yes');
			$count = count($array);
			$n = 0;
				while($n < $count) {
					$result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
					$n++;
				}
  		return tep_generate_pulldown_menu($name, $result_array, $selected, $params);
  }

  function tep_draw_tracking_method_type_pulldown($name = '', $selected = '', $params = '') {
  		global $database;
			$return = '';
			$array = array();
			$array[] = array('id' => '0', 'name' => 'None');
			$array[] = array('id' => '1', 'name' => 'Unique Code');
			$count = count($array);
			$n = 0;
				while($n < $count) {
					$result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
					$n++;
				}
  		return tep_generate_pulldown_menu($name, $result_array, $selected, $params);
  }
    
  function tep_draw_billing_method_pulldown($name = '', $selected = '', $params = '', $force = true) {
  		global $database, $user;
				if (empty($selected)) {
					$selected = $user->fetch_billing_method_id();
				}
			$return = '';
			$array = array();
			$query = $database->query("select billing_method_id, name from " . TABLE_BILLING_METHODS . (($force) ? (" where billing_method_id <= '" . $user->fetch_billing_method_id() . "'") : '')." order by billing_method_id");
			foreach($query as $result){
						$array[] = array('id' => $result['billing_method_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
    function tep_draw_service_level_pulldown($name = '', $selected = '', $params = '', $force = true, $array = array(), $default_user = true) {
  		global $database, $user, $language_id;
				if (empty($selected) && $default_user) {
					$selected = $user->fetch_service_level_id();
				}
			$return = '';
			$query = $database->query("select sl.service_level_id, sld.name from " . TABLE_SERVICE_LEVELS . " sl, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where sl.service_level_id = sld.service_level_id and sld.language_id = '" . $language_id . "' " . (($force) ? (" and sl.service_level_id <= '" . $user->fetch_billing_method_id() . "'") : '')." order by sld.service_level_id");
			foreach($query as $result)
			{
						$array[] = array('id' => $result['service_level_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_draw_group_pulldown($name = '', $selected = '', $params = '') {
  		global $database, $user;
			$return = '';
			$array = array();
			$query = $database->query("select user_group_id, name from " . TABLE_USER_GROUPS . " order by name");
			foreach($query as $result){
						$array[] = array('id' => $result['user_group_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }

  function tep_draw_help_group_pulldown($name = '', $selected = '', $params = '') {
  		global $database, $user;
			$return = '';
			$array = array();
			$query = $database->query("select group_id, group_name from " . TABLE_HELP_GROUPS . " order by group_name");
			foreach($query as $result){
						$array[] = array('id' => $result['group_id'], 'name' => $result['group_name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }

  function tep_get_default_language($type = 'language_id') {
	global $database;
		$query = $database->query("select language_id, code from " . TABLE_LANGUAGES . " where language_default = '1' limit 1");
		$result = $database->fetch_array($query);
		return $result[$type];
  }
  
  function tep_get_language_code($id) {
  	global $database;
		$query = $database->query("select code from " . TABLE_LANGUAGES . " where language_id = '" . $id . "' limit 1");
		$result = $database->fetch_array($query);
		return $result['code'];
  }
  
  function tep_get_language_id($code) {
  	global $database;
		$query = $database->query("select id from " . TABLE_LANGUAGES . " where code = '" . $code . "' limit 1");
		$result = $database->fetch_array($query);
		return $result['id'];
  }
  
  function tep_language_exists($name) {
  	global $database;
		$query = $database->query("select language_id from " . TABLE_LANGUAGES . " where name = '" . $name . "' limit 1");
		$result = $database->fetch_array($query);
			if ($result['language_id'] == NULL) {
				return false;
			} else {
				return true;
			}
  }
  
  function tep_draw_language_pulldown($name = '', $selected = '', $params = '') {
		global $database;
			$return = '';
			$array = array();
			$query = $database->query("select language_id, name from " . TABLE_LANGUAGES . "");
			foreach($query as $result){
						$array[] = array('id' => $result['language_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_draw_agency_pulldown($name = '', $selected = '') {
  		global $database, $user;
			$return = '';
			$array = array();
			$query = $database->query("select agency_id, name from " . TABLE_AGENCYS . " order by name");
			foreach($query as $result){
						$array[] = array('id' => $result['agency_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected);
  }
  
  function tep_draw_equipment_group_pulldown($name = '', $selected = '', $array = array(), $params = '') {
  		global $database, $user;
			$return = '';
			$query = $database->query("select equipment_group_id, name from " . TABLE_EQUIPMENT_GROUPS . " order by name");
			foreach($query as $result){
						$array[] = array('id' => $result['equipment_group_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_draw_equipment_pulldown($name = '', $selected = '', $group_id = '', $params = '', $array = array()) {
  		global $database, $user;
			$return = '';
				if (!empty($group_id)) {
					$query = $database->query("select e.equipment_id, e.name from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_EQUIPMENT_GROUPS . " eteg where e.equipment_id = eteg.equipment_id and eteg.equipment_group_id = '" . $group_id . "' order by name");
				} else {
					$query = $database->query("select e.equipment_id, e.name from " . TABLE_EQUIPMENT . " e order by e.name");
				}
				foreach($query as $result){
						$array[] = array('id' => $result['equipment_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_draw_equipment_status_pulldown($name = '', $selected = '', $params = '') {
  		global $database, $user;
			$return = '';
			$array = array();
			$query = $database->query("select equipment_status_id, equipment_status_name from " . TABLE_EQUIPMENT_STATUSES . " order by equipment_status_id");
			foreach($query as $result){
						$array[] = array('id' => $result['equipment_status_id'], 'name' => $result['equipment_status_name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_draw_preference_group_pulldown($name = '', $selected = '', $array = array()) {
  		global $database, $user;
			$return = '';
			$query = $database->query("select agent_preference_group_id, name from " . TABLE_AGENT_PREFERENCE_GROUPS . " order by name");
			foreach($query as $result){
						$array[] = array('id' => $result['agent_preference_group_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected);
  }
  
  function tep_draw_page_group_pulldown($name = '', $selected = '', $array = array()) {
  		global $database, $language_id;
			$return = '';
			$query = $database->query("select pg.page_group_id, pgd.name from " . TABLE_PAGES_GROUPS . " pg, " . TABLE_PAGES_GROUPS_DESCRIPTION . " pgd where pg.page_group_id = pgd.page_group_id and pgd.language_id = '" . $language_id . "'");
				foreach($query as $result){
						$array[] = array('id' => $result['page_group_id'], 'name' => $result['name']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected);
  }
  
  function tep_draw_county_pulldown($name = '', $selected_state = '', $selected = '') {
  		global $database;
			$return = '';
			$array = array();
				if (empty($selected)) {
					$array[] = array('id' => '', 'name' => 'Please Select');
				}
				if (empty($selected_state)) {
					$query = $database->query("select c.county_id, c.name as county_name, s.name as state_name from " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where c.state_id = s.state_id order by c.name");
				} else {
					$query = $database->query("select county_id, name as county_name from " . TABLE_COUNTYS . " where state_id = '" . $selected_state . "' order by name");
				}
				foreach($query as $result){
						if (isset($result['state_name'])) {
							$insert_name = $result['county_name'].' ('.$result['state_name'].')';
						} else {
							$insert_name = $result['county_name'];
						}
					$array[] = array('id' => $result['county_id'], 'name' => $insert_name);
				}
  		return tep_generate_pulldown_menu($name, $array, $selected);
  }

  function tep_write_file($filename, $content) {
  	$fp = fopen($filename, "w");
		if (flock($fp, 2)) { // do an exclusive lock
			fwrite($fp, $content);
			flock($fp, 3); // release the lock
		} else {
			//Write an error.
		}
	fclose($fp);
  }
  
  function tep_delete_file($filename) {
  	if (is_file($filename)) {
		unlink($filename);
	}
  }
  function tep_count_orders_of_type($user_id, $month, $year, $type = '') {
  	 global $database;
		$start = mktime(0, 0, 1, date("n", mktime()), date("d", mktime()), date("Y", mktime()));
		$end = mktime(0, 0, 1, (date("n", mktime()) + 1), date("d", mktime()), date("Y", mktime()));
	 	$where = '';
			if (is_numeric($type)) {
				$where = " and order_type_id = '" . $type . "' ";
			}
	 	$query = $database->query("select count(order_id) as count from " . TABLE_ORDERS . " where user_id = '" . $user_id . "' and date_added > '" . $start . "' and date_added < '" . $end . "'" . $where . "");
	 	$result = $database->fetch_array($query);
		
	return $result['count'];
  }

	function tep_db_remove_dir($dir) {
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
						while (($file = readdir($dh)) !== false) {
							if (($file != '.') && ($file != '..')) {
								if (is_file($dir.'/'.$file)) {
								unlink($dir.'/'.$file);
									//echo 'delete: ' . $dir.'/'.$file . '<br>';
								} else {
									tep_db_remove_dir($dir.'/'.$file);
								}
								
							}
						}
					closedir($dh);
				}
			}
		rmdir($dir);
		//echo '<br>Remove: ' . $dir. '<br>';
	}
	
	function tep_db_copy_dir($source_dir, $destination_dir) {
		@mkdir($destination_dir);
			if (is_dir($source_dir)) {
				if ($dh = opendir($source_dir)) {
						while (($file = readdir($dh)) !== false) {
							if (($file != '.') && ($file != '..')) {
								if (is_file($source_dir.'/'.$file)) {
									copy($source_dir.'/'.$file, $destination_dir.'/'.$file);
								} else {
									tep_db_copy_dir($source_dir.'/'.$file, $destination_dir.'/'.$file);
								}
							}
						}
					closedir($dh);
				}
			}
	}
	
	function tep_count_equipment_in_group($group_id) {
		global $database;
			$query = $database->query("select count(equipment_id) as count from " . TABLE_EQUIPMENT . " where equipment_group_id = '" . $group_id . "'");
			$result = $database->fetch_array($query);
		return $result['count'];
	}
	
	function tep_count_preferences_in_group($group_id) {
		global $database;
			$query = $database->query("select count(agent_preference_id) as count from " . TABLE_AGENT_PREFERENCES . " where agent_preference_group_id = '" . $group_id . "'");
			$result = $database->fetch_array($query);
		return $result['count'];
	}
	
	function tep_is_page($page_url) {
		global $database;
			$query = $database->query("select count(page_id) as count from " . TABLE_PAGES . " where page_url = '" . $page_url . "'");
			$result = $database->fetch_array($query);
				if ($result['count'] == '0') {
					return false;
				} else {
					return true;
				}
	}
	
	function tep_page_convert_to_internal_name($page_url) {
			$page_url = str_replace('/', '-', $page_url);
				if (strpos($page_url, '.') !== false) {
					$page_url = substr($page_url, 0, strpos($page_url, '.'));
				}
		return $page_url.'.php';
	}
	
	function tep_page_convert_to_external_name($page_url) {
			$page_url = str_replace('/', '-', $page_url);
				if (strpos($page_url, '.') !== false) {
					$page_url = substr($page_url, 0, strpos($page_url, '.'));
				}
		return $page_url;
	}
	
	function tep_get_page_url($id) {
		global $database;
			$query = $database->query("select page_url from " . TABLE_PAGES . " where page_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
		return $result['page_url'];
	}
	
	//Check if page has a content file and int hat case it can not be as this would mess things up.  Other basic pages can.
	function tep_page_can_be_deleted($page_url) {
		if (is_file(DIR_CONTENT . $page_url)) {
			return false;
		} else {
			if (tep_page_is_magic($page_url)) {
				return false;
			} else {
				return true;
			}
		}
	}
	
	//If page is a number then it is determined to be magic.
	function tep_page_is_magic($page_url) {
			$page_url = tep_page_convert_to_external_name($page_url);
				if (is_numeric($page_url)) {
					return true;
				} else {
					return false;
				}
	}
	
	function tep_create_special_payment_string($cost, $special_type = '') {
			$return_string = '';
			$return_string = 'Cost: $'.$cost;
				if (!empty($special_type)) {
					$special_explode = explode(',', $special_type);
					$return_string .= ' or '.$special_explode[0].' for $'.number_format($special_explode[1], 2);
				}
		return $return_string;
	}
	
	function tep_get_service_level_cost($id) {
		global $database;
			$query = $database->query("select cost from " . TABLE_SERVICE_LEVELS . " where service_level_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
		return $result['cost'];
	}
	
	function tep_equipment_is_tracked($id) {
		global $database;
			$query = $database->query("select tracking_method_id from " . TABLE_EQUIPMENT . " where equipment_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
				if ($result['tracking_method_id'] == '0') {
					return false;
				} elseif ($result['tracking_method_id'] == '1') {
					return true;
				}
	}
	
	function tep_equipment_is_personalized($id) {
		global $database;
			$query = $database->query("select personalized from " . TABLE_EQUIPMENT . " where equipment_id = '" . $id . "' limit 1");
			$result = $database->fetch_array($query);
				if ($result['personalized'] == '0') {
					return false;
				} elseif ($result['personalized'] == '1') {
					return true;
				}
	}
	
	function tep_fetch_county_cost($county_id) {
		global $database;
			$query = $database->query("select sa.surcharge from " . TABLE_COUNTYS . " c, " . TABLE_SERVICE_AREAS . " sa where c.county_id = '" . $county_id . "' and c.service_area_id = sa.service_area_id limit 1");
			$result = $database->fetch_array($query);
				if ($result['surcharge'] != NULL) {
					return $result['surcharge'];
				} else {
					return 0;
				}
	}
	
	function tep_fetch_county_service_area_id($county_id) {
		global $database;
			$query = $database->query("select service_area_id from " . TABLE_COUNTYS . " where county_id = '" . $county_id . "' limit 1");
			$result = $database->fetch_array($query);
				if ($result['service_area_id'] != NULL) {
					return $result['service_area_id'];
				} else {
					return 0;
				}
	}
	
	function tep_fetch_county_window($county_id) {
		global $database;
			$query = $database->query("select sa.installation_window from " . TABLE_COUNTYS . " c, " . TABLE_SERVICE_AREAS . " sa where c.county_id = '" . $county_id . "' and c.service_area_id = sa.service_area_id limit 1");
			$result = $database->fetch_array($query);
				if ($result['installation_window'] != NULL) {
					return $result['installation_window'];
				} else {
					return 0;
				}
	}
	
	function tep_fetch_equipment_cost($array = array()) {
		global $database;
			//Loop through the optional array and return the total of the equipment.
			$cost = 0;
				while(list($group_id, $values) = each($array)) {
					if (empty($values) || !is_array($values) || (count($values) == 0)) {
						continue;
					} else {
						//We have items.  Now work out the cost.
						$query = $database->query("select cost, discount from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $group_id . "' limit 1");
						$result = $database->fetch_array($query);
							if (empty($result['discount']) || (count($values) == 1)) {
								//Either one or no discount, do normal cost.
								$cost += $result['cost'];
							} else {
								//Over one and a dicount, work it out.
								$discount_explode = explode(',', $result['discount']);
									if (count($values) >= $discount_explode[0]) {
										//Enough, use the discount price.
										$cost += $discount_explode[1];
									} else {
										//Not enough, use the normal cost.
										$cost += $result['cost'];
									}
							}
					}
				}
			return $cost;
	}
	
	function tep_fetch_equipment_item_name($item_id) {
		global $database;
			$query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $item_id . "' limit 1");
			$result = $database->fetch_array($query);
		return $result['name'];
	}
	
	function tep_fetch_equipment_group_name($item_id) {
		global $database;
			$query = $database->query("select name from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $item_id . "' limit 1");
			$result = $database->fetch_array($query);
		return $result['name'];
	}
	
	//Function for assigning equipment to a specified order.  This is used in both checkout proccess and installer confirmation.
	function tep_assign_equipment_to_order($order_id, $group_id, $equipment_id, $status, $zip4) {
		global $database;
			$available_query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment_id . "' limit 1");
			$available_result = $database->fetch_array($available_query);
			
			//Get the latest equipment_id and if it exists we will assign it.
				if (($equipment_item_id = tep_fetch_next_equipment_item_id($equipment_id, $zip4)) !== false) { 
					//$database->query("update " . TABLE_EQUIPMENT . " set available = '" . ($available_result['available'] - 1) . "' where equipment_id = '" . $equipment_id . "' limit 1");
					$query = $database->query("select cost, discount, name from " . TABLE_EQUIPMENT_GROUPS . " where equipment_group_id = '" . $group_id . "' limit 1");
					$result = $database->fetch_array($query);
					$database->query("update " . TABLE_EQUIPMENT_ITEMS . " set equipment_status_id = '" . $status . "' where equipment_item_id = '" . $equipment_item_id . "' limit 1");
					$database->query("insert into " . TABLE_EQUIPMENT_TO_ORDERS . " (equipment_id, equipment_item_id, order_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name) values ('" . $equipment_id . "', '" . $equipment_item_id . "', '" . $order_id . "', '" . addslashes($available_result['name']) . "', '" . $status . "', '" . $group_id . "', '" . $result['cost'] . "', '" . $result['discount'] . "', '" . addslashes($result['name']) . "')");
				} else {
					return false;
				}
	}
	
	function tep_create_warehouse_string($warehouse_id) {
		$warehouse_string = '';
			if (!empty($warehouse_id)) {
				if (is_array($warehouse_id)) {
					$warehouse_string = ' and ((';
					$count = count($warehouse_id);
					$n = 0;
						while($n < $count) {
								if ($n > 0) {
									$warehouse_string .= ") or (";
								}
							$warehouse_string .= "warehouse_id = '" . $warehouse_id[$n] . "'";
							$n++;
						}
					//$warehouse_string = " and FIND_IN_SET(warehouse_id, '" . $string . "') > 0";
					$warehouse_string .= '))';
				} else {
					$warehouse_string = " and warehouse_id = '" . $warehouse_id . "'";
				}
			}
		return $warehouse_string;
	}
	
	function tep_fetch_next_equipment_item_id($equipment_id, $zip4) {
		global $database;
			$warehouse_array = tep_get_sevicing_warehouse($zip4);
				if (tep_fetch_available_equipment_count($equipment_id, $warehouse_array) > 0) {
					//Got one.  Now get it.
					$warehouse_string = tep_create_warehouse_string($warehouse_array);
					$query = $database->query("select max(equipment_item_id) as max from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $equipment_id . "' and equipment_status_id = '0' ".$warehouse_string);
					$result = $database->fetch_array($query);
					return $result['max'];
				} else {
					return false;
				}
			
	}
	
	function tep_get_equipment_assigned_to_order($order_id) {
		global $database;
			$return_array = array();
			$query = $database->query("select equipment_id, equipment_item_id, equipment_name, equipment_status_id, equipment_group_id, cost, discount, equipment_group_name from " .TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $order_id . "'");
			foreach($query as $result){
						if (!isset($return_array[$result['equipment_group_id']])) {
							$return_array[$result['equipment_group_id']] = array(	  'items' => array(),
																												  'name' => $result['equipment_group_name'],
																												  'cost' => $result['cost'],
																												  'discount' => $result['discount']);
						}
					$return_array[$result['equipment_group_id']]['items'][] = array('id' => $result['equipment_id'],
																													   'status' => $result['equipment_status_id'],
																													   'reference_code' => tep_fetch_equipment_item_reference_code($result['equipment_item_id']),
																													   'equipment_item_id' => $result['equipment_item_id'],
																													   'name' => $result['equipment_name']);
				}
			return $return_array;
	}
	
	//Function to check if promo code exists, is valid and has not already been used by customer.
	function tep_promotional_code_is_valid($code) {
		global $database, $user;
			$return = false;
			$promo_code_query = $database->query("select promotional_code_id, valid_start, valid_end, max_number from " . TABLE_PROMOTIONAL_CODES . " where code = '" . $code . "' limit 1");
			$promo_code_result = $database->fetch_array($promo_code_query);
				if ($promo_code_result['promotional_code_id'] != NULL) {
					$time = mktime();
						if (($time > $promo_code_result['valid_start']) && ($time < $promo_code_result['valid_end'])) {
							$uses_query = $database->query("select count(user_id) as count from " . TABLE_PROMOTIONAL_CODES_TO_USERS . " where user_id = '" . $user->fetch_user_id() . "' and promotional_code_id = '" . $promo_code_result['promotional_code_id'] . "'");
							$uses_result = $database->fetch_array($uses_query);
								if ($uses_result['count'] < $promo_code_result['max_number']) {
									$return = true;
								}
						}
				}
		return $return;
	}
	
	function tep_fetch_promotional_details($promotional_code_id) {
		global $database, $user;
			$return = false;
			$promo_details_query = $database->query("select discount_type, discount_amount from " . TABLE_PROMOTIONAL_CODES . " where promotional_code_id = '" . $promotional_code_id . "' limit 1");
			$promo_code_result = $database->fetch_array($promo_details_query);
		return $promo_code_result;
	}
	
	function tep_fetch_promotional_id($code) {
		global $database;
			$promo_details_query = $database->query("select promotional_code_id from " . TABLE_PROMOTIONAL_CODES . " where code = '" . $code . "' limit 1");
			$promo_code_result = $database->fetch_array($promo_details_query);
		return $promo_code_result['promotional_code_id'];
	}
	
	//Function to assign a code to an order.  This will also assign to the user.
	function tep_assign_promotional_code_to_order($order_id, $promotional_code_id) {
		global $database, $user;
			$database->query("insert into " . TABLE_PROMOTIONAL_CODES_TO_ORDERS . " (promotional_code_id, order_id) values ('" . $promotional_code_id. "', '" . $order_id . "')");
			$database->query("insert into " . TABLE_PROMOTIONAL_CODES_TO_USERS . " (promotional_code_id, user_id) values ('" . $promotional_code_id . "', '" . $user->fetch_user_id() . "')");
	}

  function tep_get_all_get_params($exclude_array = '') {
    global $HTTP_GET_VARS;

    if (!is_array($exclude_array)) $exclude_array = array();

    $get_url = '';
    if (is_array($HTTP_GET_VARS) && (sizeof($HTTP_GET_VARS) > 0)) {
      reset($HTTP_GET_VARS);
	  var_dump($HTTP_GET_VARS);
      while (list($key, $value) = each($HTTP_GET_VARS)) {
        if (($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) {
          $get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';
        }
      }
    }

    return $get_url;
  }

  function tep_db_get_order_promotional_code($order_id) {
  		global $database;
			$query = $database->query("select pc.code from " . TABLE_PROMOTIONAL_CODES . " pc, " . TABLE_PROMOTIONAL_CODES_TO_ORDERS . " pcto where pcto.order_id = '" . $order_id . "' and pcto.promotional_code_id = pc.promotional_code_id limit 1");
			$result = $database->fetch_array($query);
				if ($query['code'] != NULL) {
					return $query['code'];
				} else {
					return '';
				}
  }
  
  function tep_fetch_order_history($order_id) {
  		global $database;
			$history_result = array();
			$query = $database->query("select oh.order_status_id, oh.date_added, oh.user_notified, oh.comments, os.order_status_name from " . TABLE_ORDERS_HISTORY . " oh, " . TABLE_ORDERS_STATUSES . " os where oh.order_id = '" . $order_id . "' and oh.order_status_id = os.order_status_id order by os.order_status_id");
			foreach($query as $result){
					$history_result[] = array(	 'order_status_id' => $result['order_status_id'],
															 'date_added' => $result['date_added'],
															 'user_notified' => $result['user_notified'],
															 'comments' => $result['comments'],
															 'order_status_name' => $result['order_status_name']);
				}
			return $history_result;
  }
  
  function tep_fetch_address_details($address_id) {
  	global $database;
		$query = $database->query("select house_number, street_name, city, zip, zip4, adc_number, cross_street_directions, state_id, county_id, number_of_posts from " .TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
		$result = $database->fetch_array($query);
		
		$result['state_name'] = tep_get_state_name($result['state_id']);
		$result['county_name'] = tep_get_county_name($result['county_id']);
	
		return $result;
  }
  
  function tep_draw_service_areas_pulldown($name = '', $selected = '', $params = '') {
		global $database;
		   $return = '';
		   $service_area_array = array();
			if (empty($selected)) {
				 $service_area_array[] = array('id' => '', 'name' => 'Please Select');
			}
		   $query = $database->query("select service_area_id, name from " . TABLE_SERVICE_AREAS . " order by name");

			foreach($query as $result){
			  $service_area_array[] = array('id' => $result['service_area_id'], 'name' => $result['name']);
			 }
		return tep_generate_pulldown_menu($name, $service_area_array, $selected, $params);
  }
  
  function tep_draw_notify_user_pulldown($name = '', $selected = '') {
		global $database;
		   $return = '';
		   $array[] = array('id' => '0', 'name' => 'No');
		   $array[] = array('id' => '1', 'name' => 'Yes');
		return tep_generate_pulldown_menu($name, $array, $selected);
  }
  
  function tep_draw_warehouse_availability_pulldown($name = '', $selected = '') {
		global $database;
		   $return = '';
		   $array[] = array('id' => '0', 'name' => 'Shared');
		   $array[] = array('id' => '1', 'name' => 'Not Shared');
		return tep_generate_pulldown_menu($name, $array, $selected);
  }

  function tep_draw_today_tomorrow_pulldown($name = '', $selected = '', $params = '') {
		global $database;
		   $return = '';
		   $array[] = array('id' => 'today', 'name' => 'Today');
		   $array[] = array('id' => 'tomorrow', 'name' => 'Tomorrow');
		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_draw_detailed_overview_pulldown($name = '', $selected = '', $params = '') {
		global $database;
		   $return = '';
		   $array[] = array('id' => 'overview', 'name' => 'Overview');
		   $array[] = array('id' => 'detailed', 'name' => 'Detailed');
		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_equipment_group_has_items($group_id, $user_id, $warehouse_data = array()) {
  		global $database;
			$found = false;
			$query = $database->query("select e.equipment_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_EQUIPMENT_GROUPS . " etg where etg.equipment_group_id = '" . $group_id . "' and etg.equipment_id = e.equipment_id and (e.personalized = '0' or e.user_id = '" . $user_id . "')");
			foreach($query as $result){
					if (tep_fetch_available_equipment_count($result['equipment_id'], $warehouse_data) > 0) {
						$found = true;
						break;
					}
				}
			
				if ($found) {
					return true;
				} else {
					return false;
				}
  }
  
  //New equipment counting functions
  function tep_fetch_total_equipment_count($equipment_id = '', $warehouse_id = '') {
  	global $database;
		$query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where '0' = '0'" . ((!empty($equipment_id)) ? " and equipment_id = '" . $equipment_id . "'": '') . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'": ''));
		$result = $database->fetch_array($query);
	return $result['count'];
  }
  
  function tep_fetch_available_equipment_count($equipment_id = '', $warehouse_id = '') {
  	global $database;
		$warehouse_string = '';
			if (!empty($warehouse_id)) {
				$warehouse_string = tep_create_warehouse_string($warehouse_id);
			}
		$query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where equipment_status_id = '0'" . ((!empty($equipment_id)) ? " and equipment_id = '" . $equipment_id . "'": '') . $warehouse_string);
		$result = $database->fetch_array($query);
	return $result['count'];
  }
  
  function tep_fetch_lost_equipment_count($equipment_id = '', $warehouse_id = '') {
  	global $database;
		$query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where equipment_status_id = '4'" . ((!empty($equipment_id)) ? " and equipment_id = '" . $equipment_id . "'": '') . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'": ''));
		$result = $database->fetch_array($query);
	return $result['count'];
  }
  
  function tep_fetch_damaged_equipment_count($equipment_id = '', $warehouse_id = '') {
  	global $database;
		$query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where  equipment_status_id = '5'" . ((!empty($equipment_id)) ? " and equipment_id = '" . $equipment_id . "'": '') . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'": ''));
		$result = $database->fetch_array($query);
	return $result['count'];
  }
  
  function tep_fetch_in_use_equipment_count($equipment_id = '', $warehouse_id = '') {
  	global $database;
		$query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " whereequipment_status_id = '2'" . ((!empty($equipment_id)) ? " and equipment_id = '" . $equipment_id . "'": '') . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'": ''));
		$result = $database->fetch_array($query);
	return $result['count'];
  }
  
  //Count equipment items in a warehouse.
  function tep_fetch_in_warehouse_equipment_count($warehouse_id) {
  	global $database;
		$query = $database->query("select count(equipment_item_id) as count from " . TABLE_EQUIPMENT_ITEMS . " where warehouse_id = '" . $warehouse_id . "' and equipment_status_id = '0'" . ((!empty($warehouse_id)) ? " and warehouse_id = '" . $warehouse_id . "'": ''));
		$result = $database->fetch_array($query);
	return $result['count'];
  }
  
  function tep_fetch_tracking_method($tracking_method_id) {
  	if ($tracking_method_id == '0') {
		return 'None';
	} elseif ($tracking_method_id == '1') {
		return 'Unique Code';
	}
  }
  //Function for getting status of item.  Preferable use id (internal), otherwise code(external).
  function fetch_equipment_status_id($equipment_id = '', $equipment_code = '') {
  	global $database;
		$return_status = NULL;
		$query = false;
			if (!empty($equipment_code)) {
				$query = $database->query("select equipment_status_id from " . TABLE_EQUIPMENT_ITEMS . " where code = '" . $equipment_code . "' limit 1");
			}
			if (!empty($equipment_id)) {
				$query = $database->query("select equipment_status_id from " . TABLE_EQUIPMENT_ITEMS . " where equipment_id = '" . $equipment_id . "' limit 1");
			}
			if ($query) {
				$result = $database->fetch_array($query);
				$return_status = $result['equipment_status_id'];
			}	
	return $return_status;
  }
  
  function tep_fetch_equipment_status_name($equipment_status_id) {
  	global $database;
		$query = $database->query("select equipment_status_name from " . TABLE_EQUIPMENT_STATUSES . " where equipment_status_id = '" . $equipment_status_id . "' limit 1");
		$result = $database->fetch_array($query);
			if ($result['equipment_status_name'] == NULL) {
				return 'Unknowen';
			} else {
				return $result['equipment_status_name'];
			}
  }
  //End
  
 
  //Work out the amount to add as the available for a equipment item.
  function tep_fetch_equipment_available($equipment_id, $type, $total) {
  	global $database;
		$available = 0;
			if ($type == 'add') {
				$available = $total;
			} else {
				$active_query = $database->query("select count(equipment_id) as count from " . TABLE_EQUIPMENT_TO_ORDERS . " where equipment_id = '" . $equipment_id . "' and (equipment_status_id != '3')");
				$active_result = $database->fetch_array($active_query);
				$available = ($total - $active_result['count']);
			}
	return $available;
  }
  
  //Below are the functions for converting an equipment arrar intot a nice string.
  function tep_create_confirmation_equipment_string($array) {
  	global $database;
		$return_string = '';
			if (empty($array)) {
				$return_string = 'No Optional Items Ordered.';
			} else {
				//Now we get to generate the nice little table.
				$return_string .= '<table width="100%" cellpadding="0" cellspacing="0">';
					while(list($group_id, $items) = each($array)) {
						$return_string .= '<tr><td class="main">'.tep_fetch_equipment_group_name($group_id).'</td></tr>';
						$count= count($items);
						$n = 0;
							while($n < $count) {
								$return_string .= '<tr><td class="main"> - '.tep_fetch_equipment_item_name($items[$n]).'</td></tr>';
								$n++;
							}
						$return_string .= '<tr><td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>';
					}
				$return_string .= '</table>';
			}
		return $return_string;
  }
  
  function tep_convert_view_equipment_array_to_edit($array) {
		$return_array = array();
			if (!empty($array)) {
				while(list($group_id, $details) = each($array)) {
						$return_array[$group_id] = array();
						$count= count($details['items']);
						$n = 0;
							while($n < $count) {
								$return_array[$group_id][] = $details['items'][$n]['id'];
								$n++;
							}
					}
			}
		return $return_array;
  }
  
  function tep_create_view_equipment_string($array, $show_id = false) {
	  global $database;
		$return_string = '';
			if (empty($array)) {
				$return_string = 'No Optional Items Ordered.';
			} else {
				//Now we get to generate the nice little table.
				$return_string .= '<table width="100%" cellpadding="0" cellspacing="0">';
					while(list($group_id, $details) = each($array)) {
						$return_string .= '<tr><td class="main">'.$details['name'].'</td></tr>';
						$count= count($details['items']);
						$n = 0;
							while($n < $count) {
								$return_string .= '<tr><td class="main"> - '.$details['items'][$n]['name']. (($show_id && $details['items'][$n]['reference_code']) ? ' ('.$details['items'][$n]['reference_code'].')' : '') . '</td></tr>';
								$n++;
							}
						$return_string .= '<tr><td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>';
					}
				$return_string .= '</table>';
			}
		return $return_string;
  }
  
  function tep_draw_service_areas_status_pulldown($name = '', $selected = '') {
  		global $database;
			$return = '';
			$array = array();
			$array[] = array('id' => '0', 'name' => 'Active');
			$array[] = array('id' => '1', 'name' => 'Inactive');
			$count = count($array);
			$n = 0;
				while($n < $count) {
					$result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
					$n++;
				}
  		return tep_generate_pulldown_menu($name, $result_array, $selected);
  }
  
  function tep_draw_orders_status_pulldown($name = '', $selected = '', $return_array = array(), $params = '') {
  		global $database;
			$query = $database->query("select order_status_id, order_status_name from " . TABLE_ORDERS_STATUSES . " order by order_status_id");
			foreach($query as $result){
					$return_array[] = array('id' => $result['order_status_id'], 'name' => $result['order_status_name']);
				}
  		return tep_generate_pulldown_menu($name, $return_array, $selected, $params);
  }
  
  function tep_draw_installer_pulldown($name = '', $selected = '', $array = array(), $params = '') {
  		global $database;
			$return_array = array();
			$query = $database->query("select u.user_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utg where utg.user_group_id = '3' and utg.user_id = u.user_id and u.user_id = ud.user_id order by ud.lastname");
			foreach($query as $result){
					$return_array[] = array('id' => $result['user_id'], 'name' => $result['lastname'].', '.$result['firstname']);
				}
  		return tep_generate_pulldown_menu($name, $return_array, $selected, $params);
  }
  
  function tep_draw_warehouse_pulldown($name = '', $selected = '', $array = array(), $params = '') {
  		global $database;
			$return_array = array();
			$query = $database->query("select w.warehouse_id, wd.name from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = wd.warehouse_id order by wd.name");
			foreach($query as $result){
					$return_array[] = array('id' => $result['warehouse_id'], 'name' => $result['name']);
				}
  		return tep_generate_pulldown_menu($name, $return_array, $selected, $params);
  }
  
  function tep_create_orders_history($order_id, $order_status_id, $comments, $notify_user = false) {
  		global $database;
				if ($notify_user) {
					$user_notified = '1';
				} else {
					$user_notified = '0';
				}
			$date_added = mktime();
			//Add to history.
			$database->query("insert into " . TABLE_ORDERS_HISTORY . " (order_id, order_status_id, date_added, user_notified, comments) values ('" . $order_id . "', '" . $order_status_id . "', '" . $date_added . "', '" . $user_notified . "', '" . $comments . "')");
  			//Update order.
			$database->query("update " . TABLE_ORDERS . " set order_status_id = '" . $order_status_id . "', last_modified = '" . mktime() . "' where order_id = '" . $order_id . "' limit 1");
  }
  
  function tep_fetch_orders_status_id($order_id) {
  		global $database;
			$query = $database->query("select order_status_id from " . TABLE_ORDERS . " where order_id = '" . $order_id . "' limit 1");
			$result = $database->fetch_array($query);
		return $result['order_status_id'];
  }
  
  function tep_zip4_is_valid($code) {
  	$return = false;
		$explode = explode('-', $code);
			if (count($explode) == 2) {
				if ((strlen($explode[0]) == 5) && (strlen($explode[1]) == 4)) {
					if (is_numeric($explode[0]) && is_numeric($explode[1])) {
						$return = true;
					}
				}
			}
		return $return;
  }
  
  function tep_break_zip4_code($code) {
  	return explode('-', $code);
  }
  
  function tep_zip4_is_assigned($code_from, $code_to, $ignore_user = array(), $ignore_id = false) {
  	global $database;
		$code_from_explode = tep_break_zip4_code($code_from);
		$code_to_explode = tep_break_zip4_code($code_to);
		//Now work it out.
		$ignore_string = '';
			if (!empty($ignore_user)) {
				$count = count($ignore_user);
				$n = 0;
					while($n < $count) {
						$ignore_string .= " and user_id != '" . $ignore_user[$n] . "' ";
						$n++;
					}
			}
			if ($ignore_id !== false) {
				$ignore_string .= " and installer_to_area_id != '" . $ignore_id . "' ";
			}
		$query = $database->query("select count(installer_to_area_id) as count from " . TABLE_INSTALLERS_TO_AREAS . " where zip_4_first_break_start >= '" . $code_from_explode[0] . "' and zip_4_first_break_end>= '" . $code_from_explode[1] . "' and zip_4_second_break_start <= '" . $code_to_explode[0] . "' and zip_4_second_break_end <= '" . $code_to_explode[1] . "'" . $ignore_string);
		$result = $database->fetch_array($query);
			if ($result['count'] > 0) {
				return true;
			} else {
				return false;
			}
  }

function tep_draw_help_page_pulldown($name = '', $selected = '', $params = '') {
  		global $database, $user;
			$return = '';
			$array = array();
			$query = $database->query("select page_id, page_url from " . TABLE_PAGES . " order by page_url");
			foreach($query as $result){
						$array[] = array('id' => $result['page_id'], 'name' => $result['page_url']);
					}
  		return tep_generate_pulldown_menu($name, $array, $selected, $params);
  }
  
  function tep_fetch_assigned_order_installer($order_id) {
  		global $database;
			//First check if the order has been re-assigned.
			$query = $database->query("select installer_id from " .TABLE_INSTALLERS_TO_ORDERS . " where order_id = '" . $order_id . "' limit 1");
			$result = $database->fetch_array($query);
				if ($result['installer_id'] != NULL) {
					return $result['installer_id'];
				}
			//Just get the zip co-ordinates and fetch the installer based on that.
			$query = $database->query("select a.zip4 from " . TABLE_ADDRESSES . " a, " . TABLE_ORDERS . " o where o.order_id = '" . $order_id . "' and o.address_id = a.address_id limit 1");
			$result = $database->fetch_array($query);
			//Now get the installer.
				if (empty($result['zip4'])) {
					return false;
				}
			$code_explode = tep_break_zip4_code($result['zip4']);
			$query = $database->query("select user_id from " . TABLE_INSTALLERS_TO_AREAS . " where ((zip_4_first_break_start < '" . $code_explode[0] . "') or (zip_4_first_break_start = '" . $code_explode[0] . "' and zip_4_first_break_end <= '" . $code_explode[1] . "')) and ((zip_4_second_break_start > '" . $code_explode[0] . "') or (zip_4_second_break_start = '" . $code_explode[0] . "' and zip_4_second_break_end > '" . $code_explode[1] . "'))");
  			$result = $database->fetch_array($query);
				if ($result['user_id'] == NULL) {
					return false;
				}
		return $result['user_id'];
  }
  
  function tep_fetch_installer_name($installer_id) {
  		global $database;
			$query = $database->query("select firstname, lastname from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $installer_id . "' limit 1");
			$result = $database->fetch_array($query);
		return ($result['lastname'].', '.$result['firstname']);
  }
  
  function tep_fetch_time_type($timestamp, $allow_special = false) {
  	//Works out if this is a weekend, we do this by checking the day number, 0 or 6 are weekends.
  		$day_number = date("w", $timestamp);
		$weekend = array('0');
			if ($allow_special) {
				$weekend[] = '6';
			}
			if (in_array($day_number, $weekend)) {
				return 'weekend';
			} else {
				return 'weekday';
			}
  }
  
  function tep_fetch_next_available_day($timestamp, $allow_special = false) {
  	//Works out if this is a weekend, we do this by checking the day number, 0 or 6 are weekends.
  		if (tep_fetch_time_type($timestamp, $allow_special) == 'weekday') {
			return $timestamp;
		} else {
			//Now get the next day.
			$day_number = date("w", $timestamp);
				if ($day_number == 0) {
					$day_number = 7;
				}
			$days_needed = (8 - $day_number);
			$extra_time = (86400 * $days_needed);
			return ($timestamp + $extra_time);
		}
  }
  
  function tep_fetch_current_timestamp() {
  	return mktime();
  }
  
  //Fetches agent information + agency information 
  function tep_fetch_agent_data($agent_id) {
  	global $database;
		$return_array = array();
		//Get basic data.
		$agent_query = $database->query("select u.agent_id, u.agency_id, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $agent_id . "' and u.user_id = ud.user_id limit 1");
		$agent_result = $database->fetch_array($agent_query);
		
		$agency_result = array('name' => '', 'contact_phone' => '');	
			if (is_numeric($agent_result['agency_id'])) {
				//If associated agency then get agency data.
				$agency_query = $database->query("select name, contact_phone from " . TABLE_AGENCYS . " where agency_id = '" . $agent_result['agency_id'] . "' limit 1");
				$agency_result = $database->fetch_array($agency_query);
			}
			
		//Get agent phone numbers.	
		$phone_numbers_array = array();
		$phone_query = $database->query("select phone_number from " . TABLE_USERS_PHONE_NUMBERS . " where user_id = '" . $agent_id . "' order by order_id");
		foreach($phone_query as $phone_result){
				$phone_numbers_array[] = $phone_result['phone_number'];
			}
		$return_array = array_merge($agent_result, $agency_result);
		$return_array['phone_numbers'] = $phone_numbers_array;
	return $return_array;
  }
  
  function tep_agent_has_preferences($agent_id) {
  	global $database;
		$query = $database->query("select count(user_id) as count from " . TABLE_AGENTS_TO_AGENT_PREFERENCES . " where user_id = '" . $agent_id . "'");
		$result = $database->fetch_array($query);
			if ($result['count'] > 0) {
				return true;
			} else {
				return false;
			}
  }
  
  function tep_create_agent_preferences_string($agent_id, $show_text = false) {
  		global $database;
			$preferences_array = array();
			$query = $database->query("select agent_preference_group_id, name from " . TABLE_AGENT_PREFERENCE_GROUPS . " order by name");
			foreach($query as $result){
						$items_query = $database->query("select p.name from " . TABLE_AGENT_PREFERENCES . " p, " . TABLE_AGENTS_TO_AGENT_PREFERENCES . " atp where p.agent_preference_group_id = '" . $result['agent_preference_group_id'] . "' and p.agent_preference_id = atp.agent_preference_id and atp.user_id = '" . $agent_id . "'");
						foreach($items_query as $items_result){
									if (!isset($preferences_array[$items_query['agent_preference_group_id']])) {
										$preferences_array[$items_query['agent_preference_group_id']] = array('name' => $result['name'], 'items' => array());
									}
								$preferences_array[$items_query['agent_preference_group_id']]['items'][] = $items_result['name'];
							}
				}
		//Now got over data array and output the contents.
			$output_string = '<table width="100%" cellspacing="0" cellpadding="0">';
			$loop = 0;
				while(list($group_id, $data) = each($preferences_array)) {
						if ($loop > 0) {
							$output_string .= '<tr><td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>';
						}
					$output_string .= '<tr><td class="mainSmall"><b>'.$data['name'].'</b></td></tr>';
					$count = count($data['items']);
					$n = 0;
						while($n < $count) {
							$output_string .= '<tr><td class="mainSmall">&nbsp;&nbsp;&nbsp;'.$data['items'][$n].'</td></tr>';
							$n++;
						}
					$loop++;
				}
				if ($show_text) {
					$output_string .= '<tr><td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td></tr>';
					$output_string .= '<tr><td class="mainSmall"><i>To change your Preferences please use the "My Personal Preferences" link in the My Preferences menu.</i></td></tr>';
				}
			$output_string .= '</table>';
		return $output_string;
  }
  
  function tep_fetch_warehouse_availability_name($availability) {
  	if ($availability == '0') {
		return 'Shared';
	} elseif ($availability == '1') {
		return 'Not Shared';
	}
  }

  /*
  Function to get warehouses servicing a certain area.
  Arguments:
  	$zip4
	
  */
  function tep_get_sevicing_warehouse($zip4) {
  	global $database;
		$return_array = array('0');
			if (tep_zip4_is_valid($zip4)) {
				$explode = tep_break_zip4_code($zip4);
				$zip4_start = $explode[0];
				$zip4_end = $explode[1];
			} else {
				return $return_array;
			}
		$query = $database->query("select w.warehouse_id from " . TABLE_WAREHOUSES . " w left join " . TABLE_WAREHOUSES_TO_AREAS . " wta on (w.warehouse_id = wta.warehouse_id and ((wta.zip_4_first_break_start < '" . $zip4_start . "') or (wta.zip_4_first_break_start = '" . $zip4_start . "' and wta.zip_4_first_break_end <= '" . $zip4_end . "')) and ((wta.zip_4_second_break_start > '" . $zip4_start . "') or (wta.zip_4_second_break_start = '" . $zip4_start . "' and wta.zip_4_second_break_end > '" . $zip4_end . "'))) where w.availability = '0' or wta.warehouse_to_area_id IS NOT NULL");
			foreach($query as $result){
				$return_array[] = $result['warehouse_id'];
			}
	return $return_array;
  }
  
  /*  
  Function to generate a list of available equipment .
  Arguments:
  	$order_type - The type of order (given by the id).  Ie. Installer = 1;
	$service_level - The service level of the user (given by the id), a blank value is the manual override which allows for any.
	$selected - The old style array which shows the currently selected items.
	$zip4 - The zip4 number of the order.  This is used when working out what equipment is in aailable warehouses.

	To do this we would work out what equipment is available for an order type and is the same service level (or leave this out for\
	override.  We would also check if equipment were available in servicing warehouse or another open
	type warehouse.
	Installer can specify to change the warehous ethey want the equipment from.
  */
  function tep_generate_available_equipment_string($order_type, $service_level, $user_id, $selected, $zip4) {
  	global $database;
		$return_string = '';
		$found = false;
		$warehouses = tep_get_sevicing_warehouse($zip4);
		$query = $database->query("select equipment_group_id, name, description, selectable, cost, discount from " . TABLE_EQUIPMENT_GROUPS . " where order_type_id = '" . $order_type . "'");
		foreach($query as $result){
					if(!tep_equipment_group_has_items($result['equipment_group_id'], $user_id, $warehouses)) {
						continue;
					}
				$found = true;
				$return_string .= '<tr>'."\n".
											'<td class="main">&nbsp;&nbsp;<u>'.$result['name'].'</u>&nbsp;&nbsp;&nbsp;'.tep_create_special_payment_string($result['cost'], $result['discount']).'</td>'."\n".
										'</tr>'."\n".
										'<tr>'."\n".
											'<td class="main">'.$result['description'].'</td>'."\n".
										'</tr>'."\n".
										'<tr>'."\n".
											'<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>'."\n".
										'</tr>'."\n";
											if ($result['selectable'] != '1') {
				$return_string .= '<script language="javascript">'."\n".
										'function checkTwo_'.$result['equipment_group_id'].'(theBox, message, limit){'."\n".
										 'boxName=theBox.name;'."\n".
										 'elm=theBox.form.elements;'."\n".
										 'count=0;'."\n".
											' for(i=0;i<elm.length;i++) {'."\n".
											   'if(elm[i].name==boxName && elm[i].checked==true) {'."\n".
												' count++'."\n".
											  ' }'."\n".
											  '}'."\n".
												' if(count > limit){'."\n".
												   'alert(\'Please select no more than two \'+message+\' items to be placed.\')'."\n".
												   'theBox.checked=false;'."\n".
												' }'."\n".
											 '}'."\n".
										'</script>'."\n";
											}
											$optional_query = $database->query("select equipment_id, name from " . TABLE_EQUIPMENT . " where equipment_group_id = '" . $result['equipment_group_id'] . "' and available > 0 and (personalized = '0' or user_id = '" . $user_id . "')");
											foreach($optional_query as $optional_result){
													if (tep_fetch_available_equipment_count($optional_result['equipment_id'], $warehouses) < 1) {
														continue;
													}
													if (isset($selected[$result['equipment_group_id']]) && is_array($selected[$result['equipment_group_id']]) && in_array($optional_result['equipment_id'], $selected[$result['equipment_group_id']])) {
														$checked = ' CHECKED ';
													} else {
														$checked = '';
													}
													if ($result['selectable'] == '1') {
														$check_box = '<input type="radio" name="optional['.$result['equipment_group_id'].'][]" value="'.$optional_result['equipment_id'].'"'.$checked.'>'."\n";
													} else {
														$check_box = '<input type="checkbox" onclick="checkTwo_'.$result['equipment_group_id'].'(this, \''.$result['name'].'\', '.$result['selectable'].');" name="optional['.$result['equipment_group_id'].'][]" value="'.$optional_result['equipment_id'].'"'.$checked.'>'."\n";
													}
												$return_string .= '<tr>'."\n".
													'<td class="main">'.$check_box . ' ' . $optional_result['name']. '</td>'."\n".
												'</tr>'."\n";
												}
										$return_string .= '<tr>'."\n".
											'<td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>'."\n".
										'</tr>'."\n";
									}
								if (!$found) {
									$return_string .= '<tr>'."\n".
										'<td class="main">There are no optional extras for your service level.  If you want these then please consider changing your level.</td>'."\n".
									'</tr>'."\n";
								}
	return $return_string;
  }
  
  function fetch_address_zip4($address_id) {
  	global $database;
		$query = $database->query("select zip4 from " . TABLE_ADDRESSES . " where address_id = '" . $address_id . "' limit 1");
		$result = $database->fetch_array($query);
	return $result['zip4'];
  }
  
  function tep_fetch_equipment_item_reference_code($equipment_item_id) {
  	global $database;
		$query = $database->query("select code from " . TABLE_EQUIPMENT_ITEMS . " where equipment_item_id = '" . $equipment_item_id . "' limit 1");
		$result = $database->fetch_array($query);
			if (!empty($result['code'])) {
				return $result['code'];
			} else {
				return false;
			}
  }
  
  function tep_date_is_holiday($day, $month, $year) {
  	global $database;
		$query = $database->query("select public_holiday_id from " . TABLE_PUBLIC_HOLIDAYS . " where day = '" . $day . "' and month = '" . $month . "' and year = '" . $year . "'");
		$result = $database->fetch_array($query);
			if ($result['public_holiday_id'] != NULL) {
				return $result['public_holiday_id'];
			} else {
				return false;
			}
  }
 
   function tep_draw_user_status_pulldown($name = '', $selected = '') {
  		global $database;
			$return = '';
			$array = array();
			$array[] = array('id' => '1', 'name' => 'With Orders');
			$array[] = array('id' => '2', 'name' => 'No Orders');
			$array[] = array('id' => '3', 'name' => 'Signed Up');
			$count = count($array);
			$n = 0;
				while($n < $count) {
					$result_array[] = array('id' => $array[$n]['id'], 'name' => $array[$n]['name']);
					$n++;
				}
  		return tep_generate_pulldown_menu($name, $result_array, $selected);
  }
   
  function tep_fetch_holiday_name($holiday_id) {
  	global $database;
		$query = $database->query("select name from " . TABLE_PUBLIC_HOLIDAYS . " where public_holiday_id = '" . $holiday_id . "' limit 1");
		$result = $database->fetch_array($query);
	return $result['name'];
  }
  
  function tep_date_is_saturday($time_stamp) {
	if(date("w", $time_stamp) == 6){
		return true;
	} else {
		return false;
	}
  }
  
  function tep_date_is_rush($time_stamp) {
  	$day = date("d", mktime());
	$month = date("m", mktime());
	$year = date("Y", mktime());
	
	if($time_stamp >= (mktime(0, 0, 0, $month, $day+1, $year)) && $time_stamp<= (mktime(0, 0, 0, $month, $day+2, $year))) {
		return true;
	} else { 
		return false;
	}
  }
?>
