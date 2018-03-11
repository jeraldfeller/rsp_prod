<?php
	class sessions {
	var $user_session_id;
	var $session_storage_type;
	var $session_started;
	var $session_life;
	var $running;

		function __construct() {
			$this->session_life = 60*SESSION_EXPIRY_MINUTES;
			ini_set('session.bug_compat_warn', 0);
			ini_set('session.bug_compat_42', 0);
		}
		
		function begin() {
			$user = strtolower(getenv('HTTP_USER_AGENT'));
				$this->session_started = false;				
					if (isset($_GET[SID]) && !empty($_GET[SID]) && empty($_COOKIE[SID])) {
						$this->php_session_name(SID);
						$this->php_session_id(addslashes($_GET[SID]));
						$this->php_setcookie(SID, $this->php_session_id());
						$this->session_storage_type = 'non-cookie';
					} elseif (isset($_COOKIE[SID]) && !empty($_COOKIE[SID])) {
						$this->php_session_name(SID);
						$this->php_session_id(addslashes($_COOKIE[SID]));
						$this->session_storage_type = 'cookie';
					} else {
						$spider = false;
							if (!$spider) {
								$this->php_session_name(SID);
								$this->php_session_id();
								$this->php_setcookie(SID, $this->php_session_id());
								$this->session_storage_type = 'non-cookie';
							}
					}
					if (!isset($spider) || !$spider) {
							if (!$this->php_session_start()) {
								$this->session_started = false;
							} else {
								$this->user_session_id = session_id();
								$this->session_started = true;
							}
					}
		}
		
		function user_is_spider() {
		global $database;
			$user = strtolower(getenv('HTTP_USER_AGENT'));
			$check_data = $database->smart_query('spider-ref_name', 'no limit');
				if (is_array($check_data)) {
					$count = count($check_data);
						for ($i=0, $n=$count; $i<$n; $i++) {
							if (is_integer(strpos($user, trim($check_data[$i]['s-ref_name'])))) {
								return true;
							}
						}
				}
			return false;
		}
		
		function restore_session($array = array()) {
			while (list($key, $val) = each($this->running)) {
				if (!in_array($key, $array)) {
					$GLOBALS[$key] =& $_SESSION[$key];
				}
			}
		}
	   
		function _sess_open($save_path, $session_name) {
			return true;
		}
	
		function _sess_close() {
			return true;
		}
	
		function _sess_read($key) {
		global $database;
			$value_query = $database->query("select value from " . TABLE_SESSIONS . " where sesskey = '" . $key . "' and expiry > '" . time() . "' limit 1");
			$value = $database->fetch_array($value_query);
				if (isset($value['value'])) {
					return stripslashes($value['value']);
				}
			return false;
		}
	
		function _sess_write($key, $val) {
		global $database, $session;
			$expiry = time() + 60*SESSION_EXPIRY_MINUTES;
			$value = $val;
			if (!$database) return FALSE; // quash warnings
			$check_query = $database->query("select count(*) as total from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'");
			$check = $database->fetch_array($check_query);
				if ($check['total'] > 0) {
					return $database->query("update " . TABLE_SESSIONS . " set expiry = '" . $expiry . "', value = '" . addslashes($value) . "' where sesskey = '" . $key . "'");
				} else {
					return $database->query("replace into " . TABLE_SESSIONS . " values ('" . $key . "', '" . $expiry . "', '" . addslashes($value) . "')");
				}
		}
	
		function _sess_destroy($key) {
		global $database;
			return $database->query("delete from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'");
		}
	
		function _sess_gc($maxlifetime) {
		global $database;
			$database->query("delete from " . TABLE_SESSIONS . " where expiry < '" . time() . "'");
			return true;
		}
	
	
		function php_session_start() {
			session_set_save_handler(array('sessions', '_sess_open'), array('sessions', '_sess_close'), array('sessions', '_sess_read'), array('sessions', '_sess_write'), array('sessions', '_sess_destroy'), array('sessions', '_sess_gc'));
			return session_start();
		}
		
		function php_clear_session() {
			if ($this->session_started == true) {
				$_SESSION = array();
			} else {
				return false;
			}
		}
		
		function php_session_register($key, $var = '') {
			if ($this->session_started == true) {
					if (is_string($var)) {
						$var = stripslashes($var);
					}
					if (empty($var) && !is_numeric($var)) {
						if (!empty($GLOBALS[$key])) {
							$var = $GLOBALS[$key];
						}
					}
				$_SESSION[$key] = $var;
				return true;
			} else {
				return false;
			}
		}
		
		function php_session_is_registered($key) {
			if (isset($_SESSION[$key]) && !empty($_SESSION[$key])) {
				return true;
			} else {
				return false;
			}
		}
		
		function php_session_unregister($key) {
			if ($this->php_session_is_registered($key)) {
				unset($_SESSION[$key]);
			}
		}
		
		function php_return_session_variable($key) {
			if ($this->php_session_is_registered($key)) {
				return $_SESSION[$key];
			} else {
				return false;
			} 
		}
		
		function php_session_id($sessid = '') {
			if (!empty($sessid)) {
				return session_id($sessid);
			} else {
				return session_id();
			}
		}
	
		function php_session_name($name = '') {
			if (!empty($name)) {
				return session_name($name);
			} else {
				return session_name();
			}
		}
	
		function php_session_close() {
			return session_write_close();
		}
	
		function php_session_destroy() {
			return session_destroy();
		}
	
		function php_session_save_path($path = '') {
			if (!empty($path)) {
				return session_save_path($path);
			} else {
				return session_save_path();
			}
		}
	
		function php_setcookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = 0) {
			setcookie($name, $value, $expire, $path, (!empty($domain) ? $domain : ''), $secure);
		}
		
		function add_session_id($url) {
			$return = false;
			if (!empty($url)) {
				//$break = parse_url($url);
					//if ((!isset($break['host']) || empty($break['host'])) && (!isset($break['scheme']) || empty($break['scheme'])) && (isset($break['path']) && !empty($break['path']))) {
						if ($this->session_storage_type == 'non-cookie') {
							$return = true;
						}
					//} elseif ((($break['host'] .  $break['scheme']) == HTTP_SERVER) && ($break['path'] != NULL)) {
						//if ($this->session_storage_type == 'non-cookie') {
							//$return = true;
						//}
					//} else {
						//$return = true;
					//}
			}
			return $return;
		}
		
		function proccess_url($url = '') {
			if (empty($url)) {
				return $url;
			}
				if ($this->add_session_id($url)) {
					$break = parse_url($url);
					return ($url . '?' . SID . '=' . $this->user_session_id . ((empty($break['query'])) ? '' : ('&' . $break['query'])));
				} else {
					return $url;
				}
		}
		
		function finish($output) {
				if ($this->session_started) {
					$types = array("href", "action", "document.location.href");
						while(list(,$type) = each($types)) {
						   $innerT = '[a-z0-9:?=&@/._-]+?';
						   preg_match_all("|$type\=([\"'`])(".$innerT.")\\1|i", $output, $matches);
						   $ret[$type] = $matches[2];
					   }
					   reset($types);
						   while(list(,$type) = each($types)) {
							   $count = count($ret[$type]);
								   for ($n = 0, $i = $count; $n < $i; $n++) {
                                       $break = parse_url($ret[$type][$n]);
                                       if (array_key_exists('path', $break)) {
										if ((strpos($break['path'], '.html') !== false) || (strpos($break['path'], '.php') !== false) || (strpos($break['path'], '.htm') !== false) || (strpos($break['path'], '.asp') !== false) || (strpos($break['path'], '.php4') !== false)) {
											if ($this->add_session_id($ret[$type][$n])) {
												$url = (!empty($break['scheme']) ? ($break['scheme'] . '://') : '') . (!empty($break['host']) ? $break['host'] : '') . (!empty($break['path']) ? $break['path'] : '');
												$link = ($url . '?' . SID . '=' . $this->user_session_id . ((empty($break['query'])) ? '' : ('&' . $break['query'])));
												$output = str_replace(('"' . $ret[$type][$n] . '"'), ('"' . $link . '"'), $output);
											}
                                        }
                                       }
								   }
						   }
						$this->php_session_close();
					}
					if ($this->session_storage_type == 'non-cookie') {
						$output = str_replace('method="get">', 'method="get"><input type="hidden" name="'.SID.'" value="'.$this->user_session_id.'">', $output);
					}
			return $output;
		}
	}
?>
