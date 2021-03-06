<?php
	class page {
		var $page_name, $content, $page_id, $replace_data, $download_data, $download_status;
			function __construct() {
				global $session;
				
				$page = basename($_SERVER['REQUEST_URI']);
					if (($pos = strpos($page, '?')) !== false) {
						$page = substr($page, 0, $pos);
					}
			
				$this->replace_data = array();
					if ($this->verify_page($page)) {
						$this->page_name = $page;
					} else {
						$this->page_name = 'index.php';
					}

					if (!$this->verify_page_type($this->page_name)) {
						die();
					}
				$this->set_page_id();
					if (is_file(DIR_TEMPLATE.substr($this->page_name, 0, strpos($this->page_name, '.')).'.tpl')) {
						$this->template_file = substr($this->page_name, 0, strpos($this->page_name, '.')).'.tpl';
					} else {
						$this->template_file = 'template.tpl';
					}
				$this->download_status = false;
			}
			
			//Verfiy this is a real page.
			function verify_page_type($url_string) {
				$allowed_pages = array('.php', '.html', '.php');
				$ext = strrchr($url_string, '.');
					if (in_array($ext, $allowed_pages)) {
						return true;
					} else {
						return false;
					}
			}
			
			//Just checks to make sure this is a real page name, A no page trips the above as the domain name.
			function verify_page($url_string) {
				$url_array = parse_url($url_string);
					if (empty($url_array['path']) || ($url_array['path'] == '/') || (strpos($url_array['path'], '/') !== false)) {
						return false;
					} else {
						return true;
					}
			}
			
			function generate_page() {
				global $user, $error, $database, $session;
				
				
					if(($this->page_name != 'agent_contact_us.php' && $this->page_name != 'agent_account_change_password.php' && $this->page_name != 'agent_active_addresses.php' && $this->page_name != 'agent_owned_equipment.php' && $this->page_name != 'agent_account_update.php' && $this->page_name != 'order_create_address.php' && $this->page_name != 'order_create_special.php' && $this->page_name != 'order_create_payment.php' && $this->page_name != 'order_create_confirmation.php' && $this->page_name != 'order_create_success.php' && $this->page_name != 'end_year_reports.php' && $this->page_name != 'order_view.php' && $this->page_name != 'schedule_removal_success.php' && $this->page_name != 'agent_account_overview.php' && $this->page_name != 'aom_active_addresses.php' && $this->page_name != 'aom_orders.php' && $this->page_name != 'aom_order_create_address.php' && $this->page_name != 'aom_order_create_special.php' && $this->page_name != 'aom_order_create_payment.php' && $this->page_name != 'aom_order_create_confirmation.php' && $this->page_name != 'aom_order_create_success.php' && $this->page_name != 'aom_manage_agents.php' && $this->page_name != 'aom_schedule_removal_success.php' && $this->page_name != 'aom_update_account.php' && $this->page_name != 'agent_transactions.php' && $this->page_name != 'installer_account_overview.php' && $this->page_name != 'installer_view_future1.php' && $this->page_name != 'installer_change_password.php' && $this->page_name != 'aom_export_order_info.php' && $this->page_name != 'aom_order_overview.php'))
					{
						$this->check_user_view_page();
						$this->fetch_meta_data();
						$this->fetch_name();
						$this->fetch_content();
						$this->fetch_left_menu();
						$this->fetch_availability_box();
						$this->fetch_latest_news_box();
						$this->fetch_right_menu();
						$this->fetch_footer();
						$this->fetch_top_menu();
						$this->fetch_help_link();
						$this->incorperate_data();
						
						$this->convert_sessions();
					
						if (LOG_PAGE_DATA == 'true') {
							$this->log_statistical_data();
						}
						if (TRACK_USERS == 'true') {
							$user->log_user_pages();
						}
					}
					else {
						error_reporting(E_ALL ^E_STRICT);
						$this->check_user_view_page();
						$this->fetch_meta_data();
						$this->fetch_name();
						
						if (LOG_PAGE_DATA == 'true') {
								$this->log_statistical_data();
							}
							if (TRACK_USERS == 'true') {
								$user->log_user_pages();
							}
						
						if ($this->download_status) {
							//Just download;
							$this->download();
						}
						
						//$this->convert_sessions();
							
						Twig_Autoloader::register();
						$loader = new Twig_Loader_Filesystem(DIR_INCLUDES.'twig_templates');
						$twig = new Twig_Environment($loader, array(
												'cache' => false,//DIR_INCLUDES.'twig_templates/cache',
											));
						$twig->addFilter('var_dump', new Twig_Filter_Function('var_dump'));
											
						$page = $this->replace_data;					
						if (file_exists(DIR_CONTENT . $this->page_name)) {
									include(DIR_CONTENT . $this->page_name);
								}
					}
					
									

			}
			
			function change_template_file($name) {
				if (is_file(DIR_TEMPLATE . $name)) {
					$this->template_file = $name;
				}
			}
			
			function fetch_page_id() {
				return $this->page_id;
			}
			
			function fetch_page_url() {
				return $this->page_name;
			}
			
			function set_page_id() {
				global $database;
					$query = $database->query("select page_id from " . TABLE_PAGES . " where page_url = '" . $this->page_name . "' limit 1");
					$result = $database->fetch_array($query);
						if ($result['page_id'] == NULL) {
							$this->page_name = '404.php';
							return $this->set_page_id();
						}
					define('PAGE_URL', $this->page_name);
					$this->page_id = $result['page_id'];
					define('PAGE_ID', $result['page_id']);
			}
			
			function check_user_view_page() {
				global $user;
					if ($redirect_page = $user->user_can_view_page()) {
						tep_redirect($redirect_page);
					}
			}
			
			function fetch_meta_data() {
				global $database, $language_id;
						if (!empty($this->page_id)) {
							$query = $database->query("select title, keywords, description from " . TABLE_PAGES_DESCRIPTION . " where page_id = '" . $this->page_id . "' and language_id = '" . $language_id . "' limit 1");
							$result = $database->fetch_array($query);
							
							$this->replace_data['PAGE_TITLE'] = preg_replace('/<br.*?>/i', ' ', $result['title']);
							$this->replace_data['PAGE_KEYWORDS'] = $result['keywords'];
							$this->replace_data['PAGE_DESCRIPTION'] = $result['description'];
						} else {
							$this->replace_data['PAGE_TITLE'] = BUSINESS_NAME.' - page not available';
							$this->replace_data['PAGE_KEYWORDS'] = '';
							$this->replace_data['PAGE_DESCRIPTION'] = '';
						}
					
			}
			
			function fetch_name() {
				global $database, $language_id;
					
					# Added By Mukesh 					
					$arrMenuPrefix = array(
						'A'=>array(1=>43, 2=>82, 3=>133, 4=>84, 5=>6, 6=>19, 7=>15),
						'B'=>array(1=>126, 2=>124, 3=>130, 4=>118, 5=>129),
						'C'=>array(1=>116, 2=>78, 3=>98),
						'D'=>array(1=>131, 2=>142, 3=>57, 4=>86, 5=>30, 6=>59, 7=>58, 8=>79),
						'E'=>array(1=>100, 2=>87, 3=>60, 4=>24),
						'F'=>array(1=>62, 2=>40, 3=>38, 4=>88, 5=>37),
						'G'=>array(1=>14, 2=>18, 3=>76, 4=>16, 5=>17),
						'H'=>array(1=>93, 2=>46, 3=>35, 4=>61, 5=>29, 6=>91, 7=>41),
						'I'=>array(1=>5, 2=>4, 3=>71),
						
					);
					
					$i = 0;
					$pagePrefix = null;
					$km = null;
					foreach($arrMenuPrefix as $key => $prefixArry){
						
						if( $km = array_search($this->page_id , $prefixArry)){
							$i++;
							$pagePrefix = $key.$km.' : ';
						}	
					}
					#@ End By Mukesh 
					
					if (!empty($this->page_id)) {
						$query = $database->query("select pd.name, p.page_url, pd.page_order from " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_PAGES . " p where p.page_id = '" . $this->page_id . "' and pd.language_id = '" . $language_id . "' and p.page_id = pd.page_id limit 1");
                        $result = $database->fetch_array($query);
                        // Check for special case
                        if (strpos($result['page_url'], "order_create_") === 0 || strpos($result['page_url'], "aom_order_create_") === 0 || strpos($result['page_url'], "service_area_") === 0) {
                            // check for an even more special case
                            if ($result['page_url'] == 'order_create_confirmation.php') {
                                $title_display = $result['name'];
                                $title_display = str_replace("Be Sure", "</span><span class='headerOtherWords' style='color:red'>Be Sure", $title_display);
                                $this->replace_data['PAGE_NAME'] = '<span class="headerFirstWord">' .$title_display.'</span>';
                            } else {
                                $this->replace_data['PAGE_NAME'] = '<span class="headerFirstWord">' .$result['name'].'</span>';
                            }
                        } else { 
						    //Break it up so it can be how we want it
							$result['name'] = preg_replace('/<br.*?>/i', ' ', $result['name']);
						    //$break = explode(' ',  $result['name'], 2);
                            //$this->replace_data['PAGE_NAME'] = '<span class="headerFirstWord">'.$break[0].((!empty($break[1])) ? ' '.$break[1] : '').'</span>';
							
							#$this->replace_data['PAGE_NAME'] = '<span class="headerFirstWord">'.$result['page_order'].$result['name']; #Commented By Mukesh
							 
							#$this->replace_data['PAGE_NAME'] = '<span class="headerFirstWord">('.$this->page_id.')'.$pagePrefix .$result['name'];
							$this->replace_data['PAGE_NAME'] = '<span class="headerFirstWord">'.$pagePrefix .$result['name'];
                        }
					} else {
						$this->replace_data['PAGE_NAME'] = 'Not Found';
					}
			}
			
			function incorperate_content_language($content) {
				global $language;
					$return_string = $content;
					$data = array();
						if (is_file(DIR_LANGUAGES . $language . '/' . $this->page_name)) {
							include(DIR_LANGUAGES . $language . '/' . $this->page_name);
						}
						if (is_file(DIR_LANGUAGES . $language . '/basic_text/' . $this->page_name)) {
							//$data['PAGE_TEXT'] = stripslashes(file_get_contents(DIR_LANGUAGES . $language . '/basic_text/' . $this->page_name));
							
							$data['PAGE_TEXT'] = stripslashes(parse_license_detail(file_get_contents(DIR_LANGUAGES . $language . '/basic_text/' . $this->page_name)));
							
						} else {
							$data['PAGE_TEXT'] = '';
						}
						if (!empty($data)) {
							while(list($search, $replace) = each($data)) {
								$return_string = str_replace('&'.$search, $replace, $return_string);
							}
						}
					return $return_string;
			}
			
			function fetch_content() {
				global $database, $error, $session, $user, $language_id;
					if (!empty($this->page_id)) {
							if (file_exists(DIR_CONTENT . $this->page_name)) {
								ob_start();
								include(DIR_CONTENT . $this->page_name);
								$contents = ob_get_contents();
								ob_end_clean();
								
							} else {
								$contents = '&PAGE_TEXT';
							}
						$this->replace_data['PAGE_CONTENT'] = $this->incorperate_content_language($contents);
						
					} else {
						$this->replace_data['PAGE_CONTENT'] = 'Not Found';
					}
			}
			
			function log_statistical_data() {
				global $database, $_SESSION;
					
					if (isset($_SESSION['track'])) {
						$type = 'RAW_VIEWS';
					} else {
						$_SESSION['track'] = '1';
						$type = 'UNIQUE_VIEWS';
					}
				$query = $database->query("select value from " . TABLE_SITE_STATISTICS . " where name = '" . $type . "' limit 1");
				$result = $database->fetch_array($query);
				$database->query("update " . TABLE_SITE_STATISTICS . " set value = '" . ($result['value'] + 1) . "' where name = '" . $type . "' limit 1");
				
				$query = $database->query("select page_views from " . TABLE_PAGES . " where page_id = '" . $this->page_id . "' limit 1");
				$result = $database->fetch_array($query);
				$database->query("update " . TABLE_PAGES . " set page_views = '" . ($result['page_views'] + 1) . "' where page_id = '" . $this->page_id . "' limit 1");
			}
			
			function fetch_help_link() {
				$this->replace_data['PAGE_HELP_LINK'] =  FILENAME_HELP_SYSTEM . '?page_url=' . $this->page_name;
			}
			
			function fetch_availability_box() {
				$content = '';
					if (strpos($this->page_name, 'index') !== false) {
						global $database, $error, $session, $user, $language_id;
						ob_start();
						include(DIR_BOXES . 'installation_availability.php');
						$content = ob_get_contents();
						ob_end_clean();
					}
				$this->replace_data['PAGE_AVAILABILITY_BOX'] = $content;
			}
			
			function fetch_latest_news_box() {
				$content = '';
					if ((strpos($this->page_name, 'index') !== false) || (strpos($this->page_name, 'account_overview') !== false)) {
						global $database, $error, $session, $user, $language_id;
						ob_start();
						include(DIR_BOXES . 'latest_news.php');
						$content = ob_get_contents();
						ob_end_clean();
					}
				$this->replace_data['PAGE_LATEST_NEWS_BOX'] = $content;
			}
			
			function fetch_left_menu() {
				global $database, $error, $session, $user, $language_id;
					ob_start();
						if (strpos($this->page_name, 'index') !== false) {
							include(DIR_INCLUDES . 'column_left_home.php');
						} else {
							include(DIR_INCLUDES . 'column_left.php');
						}
					$content = ob_get_contents();
					ob_end_clean();
					$this->replace_data['PAGE_COLUMN_LEFT'] = $content;
			}
			
			function fetch_right_menu() {
				$this->replace_data['PAGE_COLUMN_RIGHT'] = '';
			}
			
			function fetch_top_menu() {
				global $database, $error, $session, $user;
					ob_start();
					include(DIR_INCLUDES . 'header.php');
					$content = ob_get_contents();
					ob_end_clean();
					$this->replace_data['PAGE_TOP_MENU'] = $content;
					$this->replace_data['PAGE_TOP_LOGIN_STRING'] = $user->generate_login_string();
					//$this->replace_data['PAGE_TOP_MENU'] = file_get_contents(DIR_INCLUDES.'header.php');
			}
			
			function fetch_footer() {
				global $database, $error, $session, $user;
					ob_start();
					include(DIR_INCLUDES . 'footer.php');
					$content = ob_get_contents();
					ob_end_clean();
					$this->replace_data['PAGE_BOTTOM_MENU'] = $content;
			}
			
			function incorperate_data() {
				if ($this->download_status) {
					//Just download;
					$this->download();
				}
				$template = file_get_contents(DIR_TEMPLATE . $this->template_file);
				reset($this->replace_data);
					
					while(list($search, $replace) = each($this->replace_data)) {
						//make order pages https
						$template = str_replace('&'.$search, $replace, $template);
						$this->replace_data[$search] = '';
						unset($replace);
						unset($search);
					}
				$this->replace_data = array();
				$this->content = $template;
			}
			
			function run_compression() {
				if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))) {
					//header("Content-Encoding: gzip");
					//$this->content = gzencode($this->content, 5);
				}
			}
			
			function set_download($type, $filename = 'download', $content = '') {
				$this->download_status = true;
				$this->download_data = array($type, $filename, $content);
			}
			
			function download() {
				header('Content-type: Application/octet-stream');
				header('Content-Disposition: attachment; filename="' . $this->download_data[1] . '"');
				header("Content-Length: ".strlen($this->download_data[2]));
				echo $this->download_data[2];
				die();
			}
			
			function sanatize_output() {
			}
			
			function convert_sessions() {
				global $session;
					$this->content = $session->finish($this->content);
			}
			
			function return_content() {
				$return_string =  $this->content;
				return $return_string;
			}
	}
?>
