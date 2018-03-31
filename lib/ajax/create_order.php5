<?php
/*
 * Description: JSON interface to inventory supply levels
 * 
 * Author: John Pelster <john.pelster@gmail.com>
 */

require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';
error_reporting(E_ALL ^E_STRICT);
if(isset($_POST['action'])) {
	if($_POST['action']=="aj_get_county_pulldown") {

			$array = array();
			$selected_state = intval($_POST['aj_selected_state']);
			if (empty($selected_state)) {
				$query = $database->query("select c.county_id, c.name as county_name, s.name as state_name from " . TABLE_STATES . " s, " . TABLE_COUNTYS . " c where c.state_id = s.state_id order by c.name");
			} else {
				$query = $database->query("select county_id, name as county_name from " . TABLE_COUNTYS . " where state_id = '" . $selected_state . "' order by name");
			}
			foreach($database->fetch_array($query) as $result){
				if (isset($result['state_name'])) {
					$insert_name = $result['county_name'] . ' (' . $result['state_name'] . ')';
				} else {
					$insert_name = $result['county_name'];
				}
				$array[] = array('id' => $result['county_id'], 'name' => $insert_name);
			}
			
			echo json_encode($array);
	}
}

//echo $output;
?>
