<?php
	class order_email {
		var $order_id;
		var $address_id;
		var $order_type_id;
		var $variables_array = array();
		var $template;
		var $order_status;
		var $installed_post_type;
		var $requested_equipment;
		var $extra_equipment;
		var $user_id;
        var $aom;
        var $aom_name;
		var $admin;

        function __construct($oID, $order_status, $condition = '', $installed_post_type = '', $requested_equipment = array(), $extra_equipment = array()) {
				global $database;
				
				$this->order_id = $oID;
				$this->order_status = $order_status;
				$this->installed_post_type = $installed_post_type;
				$this->requested_equipment = $requested_equipment;
				$this->extra_equipment = $extra_equipment;
				$this->template = $condition;
				
				$query = $database->query("select address_id, order_type_id, user_id from " . TABLE_ORDERS . " where order_id = '" . $this->order_id . "' limit 1");
				$result = $database->fetch_array($query);
				$this->address_id = $result['address_id'];
				$this->user_id = $result['user_id'];
				$this->order_type_id = $result['order_type_id'];
				
				$this->create_variables();
					if (($this->order_type_id == '1') && ($order_status)) {
						$this->create_install_equipment_string();
					} elseif ($this->order_type_id == '2' && $condition == 'service_success_rider_exchange') {
                        $this->create_exchange_rider_string();
					} elseif ($this->order_type_id == '2') {
                        $this->create_install_equipment_string();  // mjp 201109
					} elseif ($this->order_type_id == '3') {
						$this->create_remove_equipment_string();
					}
				//$this->assign_template();
				$this->send();
			}
			
			function create_variables() {
				global $database;

				$query = $database->query("select a.house_number, a.street_name, a.city, s.name as state_name, c.name as county_name from " . TABLE_ADDRESSES . " a left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id) where a.address_id = '" . $this->address_id . "' limit 1");
				$result = $database->fetch_array($query);
				
				reset($result);
				while(list($key, $val) = each($result)) {
					$this->variables_array[$key] = $val;
				}
				
				$query = $database->query("select o.date_completed, o.user_id, o.placed_by, od.installer_comments, ot.name as order_type_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_ORDER_TYPES . " ot where o.order_id = '" . $this->order_id . "' and o.order_id = od.order_id and o.order_type_id = ot.order_type_id limit 1");
				$result = $database->fetch_array($query);
				
				$this->variables_array['installer_comments'] = $result['installer_comments'];
				$this->variables_array['date_completed'] = date("F j, Y ", $result['date_completed']);
                
                if ($result['user_id'] != $result['placed_by'] && $result['placed_by'] > 0){
	                $aom_query = $database->query("select u.user_id, u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u INNER JOIN " . TABLE_USERS_DESCRIPTION . " ud ON u.user_id = ud.user_id WHERE u.user_id = '" . $result['placed_by'] . "' limit 1");
	                $aom_result = $database->fetch_array($aom_query);
	                if ($aom_result) {
	                    $this->aom = $aom_result['email_address'];
	                    $this->aom_name = $aom_result['firstname'].' '.$aom_result['lastname'];
	                }
                }
                $query = $database->query("select u.user_id, u.agent_id, u.email_address, ud.firstname, ud.lastname, a.name from " . TABLE_USERS . " u left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $result['user_id'] . "' and u.user_id = ud.user_id limit 1");
                $uresult = $database->fetch_array($query);
                $this->variables_array['AGENT_NAME'] = $uresult['firstname'].' '.$uresult['lastname'];        
                $this->variables_array['AGENT_EMAIL'] = $uresult['email_address'];        
                $this->variables_array['AGENT_ID'] = $uresult['agent_id'];        
                $this->variables_array['AGENCY_NAME'] = $uresult['name'];                    

                if (empty($extra_equipment)) {
                    $this->variables_array['EXTRA_LABEL'] = '';        
                    $this->variables_array['EXTRA_EQUIPMENT'] = '';                    
                } else {
                    $this->variables_array['EXTRA_LABEL'] = 'Extra equipment: ';        
                    $this->variables_array['EXTRA_EQUIPMENT'] = $extra_equipment;                    
                }
                
			}
			
			function assign_template() {
				$template = '';
					if ($this->order_type_id == '1') {
						if ($this->order_status) {
							//Install Success.
							$template = 'successful_install_completed';
						} else {
							//Install Failure.
							
						}
					} elseif ($this->order_type_id == '2') {
						if ($this->order_status) {
							//Service Call Success.
							
						} else {
							//Service Call Failure.
							
						}
					} elseif ($this->order_type_id == '3') {
						if ($this->order_status) {
							//Removal Success.
							
						} else {
							//Removal Failure.
							
						}
					}
				$this->template = $template;
			}
			
			function create_install_equipment_string() {
				global $database;
				    $equipment = "";
                    $extra_equipment = "";
					if ($this->order_status) {
						$this->variables_array['post_type'] = tep_get_equipment_name($this->installed_post_type);
						//Now loop over the items and mark them as part of the requested or extra items.
						$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_status_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $this->order_id . "'");
							foreach($database->fetch_array($query) as $result){
								if (($result['equipment_id'] != $this->installed_post_type) && (!is_array($this->extra_equipment) || !in_array($result['equipment_id'], $this->extra_equipment))) {
									if ($result['equipment_status_id'] == '2') {
										//Was installed.
										$equipment .= $result['equipment_name'] . ' was installed successfully.' . "\n";
									} else {
										//Was not installed.
										$equipment .= $result['equipment_name'] . ' was not installed successfully.' . "\n";
									}
								}
							}
						//Now for the extra_items
						$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_status_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $this->order_id . "'");
							foreach($database->fetch_array($query) as $result){
								if (($result['equipment_id'] != $this->installed_post_type) && (is_array($this->extra_equipment) && in_array($result['equipment_id'], $this->extra_equipment))) {
									if ($result['equipment_status_id'] == '3') {
										$extra_equipment .= $result['equipment_name'] . ' was installed successfully.' . "\n";
									} else {
										//Never happens.
									}
								}
							}
					} else {	
						$equipment = 'No equipment was installed as this order was not completed successfully.';
					}
				$this->variables_array['equipment'] = nl2br($equipment);
				$this->variables_array['extra_equipment'] = nl2br($extra_equipment);
			}
			
			function create_exchange_rider_string() {
				global $database;
				    $equipment = "";
                    $extra_equipment = "";
					if ($this->order_status) {
						$this->variables_array['post_type'] = tep_get_equipment_name($this->installed_post_type);
						//Now loop over the items and mark them as part of the requested or extra items.
						$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_status_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $this->order_id . "'");
							foreach($database->fetch_array($query) as $result){
								if (($result['equipment_id'] != $this->installed_post_type) && (!is_array($this->extra_equipment) || !in_array($result['equipment_id'], $this->extra_equipment))) {
									if ($result['equipment_status_id'] == '2') {
										//Was installed.
										$equipment .= $result['equipment_name'] . ' was installed successfully.' . "\n";
									} else {
										//Was removed.
										$equipment .= $result['equipment_name'] . ' was successfully removed.' . "\n";
									}
								}
							}
						//Now for the extra_items
						$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_status_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $this->order_id . "'");
							foreach($database->fetch_array($query) as $result){
								if (($result['equipment_id'] != $this->installed_post_type) && (is_array($this->extra_equipment) && in_array($result['equipment_id'], $this->extra_equipment))) {
									if ($result['equipment_status_id'] == '3') {
										$extra_equipment .= $result['equipment_name'] . ' was installed successfully.' . "\n";
									} else {
										//Never happens.
									}
								}
							}
					} else {	
						$equipment = 'No equipment was installed as this order was not completed successfully.';
					}
				$this->variables_array['equipment'] = nl2br($equipment);
				$this->variables_array['extra_equipment'] = nl2br($extra_equipment);
			}
			
			function create_remove_equipment_string() {
				global $database;
				
				$equipment = '';
				$extra_equipment = '';
					if ($this->order_status) {
						$this->variables_array['post_type'] = tep_get_equipment_name($this->installed_post_type);
						//Now loop over the items and mark them as part of the requested or extra items.
						$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_status_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $this->order_id . "'");
							foreach($database->fetch_array($query) as $result){
								if (($result['equipment_id'] != $this->installed_post_type) && (!is_array($this->extra_equipment) || !in_array($result['equipment_id'], $this->extra_equipment))) {
									if ($result['equipment_status_id'] == '0') {
										//Was installed.
										$equipment .= $result['equipment_name'] . ' was removed successfully.' . "\n";
									} elseif ($result['equipment_status_id'] == '4') {
										$equipment .= "<b>".$result['equipment_name'] . ' was missing.</b>' . "\n";
									} else {
										//Was not installed.
										$equipment .= $result['equipment_name'] . ' was not removed successfully.' . "\n";
									}
								}
							}
							if (empty($equipment)) {
								$equipment = 'No items needed to be Removed.';
							}
						//Now for the extra_items
						$query = $database->query("select equipment_id, equipment_item_id, equipment_name, method_id, equipment_status_id, equipment_group_answer_id from " . TABLE_EQUIPMENT_TO_ORDERS . " where order_id = '" . $this->order_id . "'");
							foreach($database->fetch_array($query) as $result){
								if (($result['equipment_id'] != $this->installed_post_type) && (is_array($this->extra_equipment) && in_array($result['equipment_id'], $this->extra_equipment))) {
									if ($result['equipment_status_id'] == '3') {
										$extra_equipment .= $result['equipment_name'] . ' was removed successfully.' . "\n";
									} else {
										//Never happens.
									}
								}
							}
					} else {	
						$equipment = 'No equipment was removed as this order was not completed successfully.';
					}
				$this->variables_array['equipment'] = nl2br($equipment);
				$this->variables_array['extra_equipment'] = nl2br($extra_equipment);
			}
			
            function send() {
                global $database;

                $email_template = new email_template($this->template);
                $email_template->load_email_template();

                reset($this->variables_array);
                
                    while(list($key, $val) = each($this->variables_array)) {
                        $email_template->set_email_template_variable(strtoupper($key), $val);
                    }
                    if (empty($this->aom)) {
                        $email_template->set_email_template_variable(strtoupper('aom_label'), '');
                        $email_template->set_email_template_variable(strtoupper('aom_name'), '');                        
                    }  else {
                        $email_template->set_email_template_variable(strtoupper('aom_label'), 'Agency order manager Name: ');
                        $email_template->set_email_template_variable(strtoupper('aom_name'), $this->aom_name);                        
                    }
                    
                $email_template->set_email_template_variable(strtoupper('admin_label'), ' ');
                $email_template->set_email_template_variable(strtoupper('admin_info'), ' ');
                $this->admin = "";
                $email_template->parse_template();
                
                $customers_info_query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud where u.user_id = '" . $this->user_id . "' and u.user_id = ud.user_id limit 1");
                $customers_info_result = $database->fetch_array($customers_info_query);
                $this->admin = $customers_info_result['email_address'];

                $email_template->send_email($customers_info_result['email_address'], $customers_info_result['firstname'].','.$customers_info_result['lastname']);

                $extra_query = $database->query("select email_address from emails_to_users where user_id = '" . $this->user_id . "' and email_status = '1'");
                foreach($database->fetch_array($extra_query) as $extra_result){
                      $email_template->send_email($extra_result['email_address'],$customers_info_result['firstname'].' '.$customers_info_result['lastname'], false);
                      $this->admin .= ", ".$extra_result['email_address'];
                }

                if (!empty($this->aom)) {
                      $email_template->send_email($this->aom, $this->aom_name, false);
                      $this->admin .= ", ".$this->aom;
                }

                //unset($email_template->template_data['&'.'ADMIN_LABEL']);
                //unset($email_template->template_data['&'.'ADMIN_INFO']);
                $email_template = new email_template($this->template);
                $email_template->load_email_template();
                reset($this->variables_array);
                
                while(list($key, $val) = each($this->variables_array)) {
                      $email_template->set_email_template_variable(strtoupper($key), $val);
                }
                if (empty($this->aom)) {
                      $email_template->set_email_template_variable(strtoupper('aom_label'), '');
                      $email_template->set_email_template_variable(strtoupper('aom_name'), '');                        
                } else {
                      $email_template->set_email_template_variable(strtoupper('aom_label'), 'Agency order manager Name: ');
                      $email_template->set_email_template_variable(strtoupper('aom_name'), $this->aom_name); 
                }
                $email_template->set_email_template_variable(strtoupper('admin_label'), 'Emails to: ');
                $email_template->set_email_template_variable(strtoupper('admin_info'), $this->admin);
                $email_template->parse_template();
                $email_template->send_email(ADMIN_EMAIL,'ADMIN EMAILS', false);

//                    $email_template->send_email('info@onlinemediasupport.com','ADMIN EMAILS', false);
//print_r($email_template->template_data);
//                die();
            }
            
            
    }
?>
