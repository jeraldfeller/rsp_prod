<?php
	class ErrorClass {
		var $error_array;
		var $error_status;
	

			
			function reset_error() {
				$this->error_array = array();
				$this->error_status = array();
			}
	
			function get_error_status($type, $status = 'error') {
				$return = false;
					if ($status == 'all') {
						if (isset($this->error_array[$type])) {
							if (isset($this->error_array[$type]['error']) && !empty($this->error_array[$type]['error'])) {
								$return = true;
							}
							if (isset($this->error_array[$type]['warning']) && !empty($this->error_array[$type]['warning'])) {
								$return = true;
							}
							if (isset($this->error_array[$type]['success']) && !empty($this->error_array[$type]['success'])) {
								$return = true;
							}
						}
					} else {
						if (isset($this->error_array[$type][$status]) && !empty($this->error_array[$type][$status])) {
							$return = true;
						}
					}
				return $return;
			}
			
			function add_error($type, $error_string = '', $status = 'error') {
			//echo 'error<br>';
				if (!empty($error_string)) {
					$this->error_array[$type][$status][] = $error_string;
				}
			}
			
			function get_error_string($type, $status = 'error') {
				$return_string = '';
					$error_info = array();
					if ($status == 'all') {
						
							if (isset($this->error_array[$type]['error']) && !empty($this->error_array[$type]['error'])) {
								$count = count($this->error_array[$type]['error']);
								$n = 0;
									while($n < $count) {
										$error_info[] = array('text' => $this->error_array[$type]['error'][$n], 'type' => 'error');
										$n++;
									}
							}
							if (isset($this->error_array[$type]['warning']) && !empty($this->error_array[$type]['warning'])) {
								$count = count($this->error_array[$type]['warning']);
								$n = 0;
									while($n < $count) {
										$error_info[] = array('text' => $this->error_array[$type]['warning'][$n], 'type' => 'warning');
										$n++;
									};
							}
							if (isset($this->error_array[$type]['success']) && !empty($this->error_array[$type]['success'])) {
								$count = count($this->error_array[$type]['success']);
								$n = 0;
									while($n < $count) {
										$error_info[] = array('text' => $this->error_array[$type]['success'][$n], 'type' => 'success');
										$n++;
									}
							}
					} else {
						if (isset($this->error_array[$type][$status]) && !empty($this->error_array[$type][$status])) {
							$count = count($this->error_array[$type][$status]);
							$n = 0;
								while($n < $count) {
									$error_info[] = array('text' => $this->error_array[$type][$status][$n], 'type' => $status);
									$n++;
								}
						}
					}
				$count = count($error_info);
				$return_string = '<table width="100%" cellspacing="0" cellpadding="0">';
				$n = 0;
					while($n < $count) {
							if (!empty($return_string)) {
								$return_string .= '<tr><td height="1"><img src="images/pixel_trans.gif" height="1" width="1></tr>';
							}
						$return_string .= '<tr><td width="16" height="16"><img src="images/'.$error_info[$n]['type'].'.gif" height="16" width="16"></td><td width="5"><img src="images/pixel_trans.gif" width="5" height="1"></td><td width="100%" align="left" height="16" valign="top" class="main'.ucfirst($error_info[$n]['type']).'">'.$error_info[$n]['text'].'</td></tr>';
						$n++;
					}
				$return_string .= '</table>';
				
				return $return_string;
			}

			function cc_error ($s) {
			  if (defined('CC_ERROR_LOG')) {
				if (($h = fopen(CC_ERROR_LOG, 'a')) !== FALSE) {
				  fwrite($h, date("D M j G:i:s T Y").": $s\n");
				  fclose($h);
				}
			  }
			}

			function log ($s) {
			  if (defined('ERROR_LOG')) {
				if (($h = fopen(ERROR_LOG, 'a')) !== FALSE) {
				  fwrite($h, date("D M j G:i:s T Y").": $s\n");
				  //fclose($h);
				}
			  }
			}

	}
?>
