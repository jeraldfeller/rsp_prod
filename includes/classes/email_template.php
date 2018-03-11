<?php
	class email_template {
		var $template_name;
		var $template_content;
		var $template_data;
		var $template_commands;

        function __construct($template_name = '', $load_template = true) {
				global $language;
					if ($load_template) {
						if (is_file(DIR_LANGUAGES . $language . '/email_templates/'.$template_name.'.php')) {
							$this->template_name = $template_name;
							$this->email_content = array();
							$this->template_commands = array();
							return true;
						} else {
							return false;
						}
					} else {
						return true;
					}
			}
			
			function set_email_template($template_content) {
				$this->template_content = $template_content;
			}
			
			function set_template_command($name, $value = '') {
				$this->template_commands[$name] = $value;
			}
			
			function load_email_template() {
				global $language;
					if (is_file(DIR_LANGUAGES . $language . '/email_templates/'.$this->template_name.'.php')) {
						$template_array = file(DIR_LANGUAGES . $language . '/email_templates/'.$this->template_name.'.php');
						$count = count($template_array);
						$n = 0;
						$this->template_content = '';
							while($n < $count) {
									if (substr($template_array[$n], 0, 4) == '(<>)') {
										$explode = explode(', ', str_replace('(<>)', '', $template_array[$n]), 2);
										$this->template_commands[$explode[0]] = $explode[1];
									} else {
											if (!empty($this->template_content)) {
												$this->template_content .= "\n";
											}
										$this->template_content .= $template_array[$n];
									}
								$n++;
							}
						
					} else {
						$this->template_content = '';
					}
			}
		
			function set_email_template_variable($name, $value = '') {
				$this->template_data[TEMPLATE_DEFINER.$name] = $value;
				$this->template_data['&'.$name] = $value;
			}
			
			function parse_template() {
				while(list($key, $value) = each($this->template_data)) {
					$this->template_content = str_replace($key, $value, $this->template_content);
						if (isset($this->template_commands['SUBJECT'])) {
							$this->template_commands['SUBJECT'] = str_replace($key, $value, $this->template_commands['SUBJECT']);
						}
				}
			}
		
			function send_email($email_address, $name = '', $send_extra = true) {
				if (empty($name)) {
					$name = $email_address;
				}

				// Instantiate a new mail object
				$message = new email(array(MAILER_NAME));
			//print_r($message);exit;
				// Build the text version
				$text = strip_tags($this->template_content);
					if (((EMAIL_USE_HTML == 'true') || (EMAIL_USE_HTML === true))) {
					  $message->add_html($this->template_content, $text, DIR_IMAGES);
					} else {
					  $message->add_text($text);
					}
			
				// Send message
				$subject = EMAIL_DEFAULT_SUBJECT;
					if (!empty($this->template_commands['SUBJECT'])) {
						$subject = $this->template_commands['SUBJECT'];
						reset($this->template_data);
							while(list($key, $value) = each($this->template_data)) {
								$subject = str_replace($key, $value, $subject);
							}
						
					}
				$message->build_message();
				$message->send($name, $email_address, EMAIL_FROM_NAME, EMAIL_FROM_ADDRESS, $subject);
					if ($send_extra && (SEND_EXTRA_EMAIL == 'true')) {
						//$message->send(SEND_EXTRA_EMAIL_TO, SEND_EXTRA_EMAIL_TO, EMAIL_FROM_NAME, EMAIL_DEFAULT_SUBJECT, $subject);
					}
			}
			
	}
?>
