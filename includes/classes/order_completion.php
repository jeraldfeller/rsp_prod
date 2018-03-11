<?php
	/*
		Order completion class.
		
		This class is only used on the complete orders page. Contains the options to generate the page and do all the needed functions.
	
		No order modifications are done here.  That is done by the order class or the script.  Only calculations and options are done here.
	
		To be stored in the session as is multi stage.
	*/
	class order_completion {
		var $order_id;
		var $order_type_id;
		var $assign_equipment_array = array();
		var $unassign_equipment_array = array();
		var $extra_charge_array = array();
		var $new_order_status_id = false;
		var $new_order_date = false;
		var $new_order_type_id = false;
		var $success_status;
		var $form_action;
		var $output_string = '';
		
		//Chnage the following and add new functions to add conditions.
		var $conditions_array = array();
		
		//Call Functions
        function __construct($order_id) {
				global $database;
				
					$this->order_id = $order_id;
					
					$query = $database->query("select order_type_id from " . TABLE_ORDERS . " where order_id = '" . $order_id . "' limit 1");
					$result = $database->fetch_array($query);
					
					$this->order_type_id = $result['order_type_id'];
					
					
			}
		
		//Lets start the party.
			function generate() {
				switch ($this->order_type_id) {
					case '1' :
						return $this->install();
					break;
					case '2' :
						return $this->service_call();
					break;
					case '3' :
						return $this->removal();
					break;
				}
			}
			
			function install() {
					if (empty($this->success_status)) {
						//Its a first page.
					} elseif ($this->success_status) {
						//It was successful.  Call the function to make the dropdown menus.
					} else {
						//It was not successful.  This needs further investigation.
					}
			}
			
			function service_call() {
			
			}
			
			function removal() {
			
			}
		
		//Conditional functions.
		
		//Internal Functions.
		//Add an item to be assigned.	
			function assign_extra_equipment_item() {
			
			}
		
		//Add an item to be unassigned.
			function unassign_extra_equipment_item() {
			
			}
		
		//Send command to add an extra charge to the charge array.
			function add_extra_charge() {
			
			}
		
		//Get the list of options this order could have at its current state.
			function get_option_list() {
			
			}
		
		//Send command to change the order to a service call.
			function change_order_type() {
			
			}
			
		//Send command to put order on hold.
			function change_order_status() {
			
			}
		
		//Send command to change order date.
			function change_order_date() {
			
			}
			
			
		//Ouput generation functions.	
			
		//Demi return functions.  Not directly called externally.
			function return_order_status() {
			
			}
			
			function return_order_type() {
			
			}
			
			function return_order_date() {
			
			}
			
		//Return Functions
			function return_total_extra_charge() {
				
			}
			
			function return_assign_items_array() {
				
			}
			
			function return_unassign_items_array() {
				
			}
		
		//Work out what kind of order change it is (if any) and return that.
			function return_order_control() {
			
			}
			
			
	}
?>