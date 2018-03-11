<?php
	$equipment_id = tep_fill_variable('equipment_id', 'get');
	$equipment_item_id = tep_fill_variable('equipment_item_id', 'get');
	$page_action = tep_fill_variable('page_action', 'get');
    $receive_email = tep_fill_variable('receive_email', 'get');

    $query = $database->query("SELECT * FROM " . TABLE_INVENTORY_OPT_OUT . " ioo WHERE user_id = '{$user->fetch_user_id()}'");
    $opt_out = 0;
    $checked = 1;
	$vars = array();
    if ($result = $database->fetch_array($query)) {
        $opt_out = $result['email_opt_out'];
        if ($opt_out) {
            $checked = 0;
        }
    }
					if ($page_action == 'list') {
						//LIST STARTS
						$vars['total_other'] = 0;
						//Time to make our nice list.
						$count = 0;
						$query = $database->query("select equipment_id, name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment_id . "' limit 1");
						$vars['result_equipment'] = $database->fetch_array($query);
						$query = $database->query("select DISTINCT ei.equipment_item_id, ei.user_id, eia.address_id, a.house_number, a.street_name, a.city, es.equipment_status_name, ei.equipment_status_id from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT_STATUSES . " es, equipment_items_to_addresses eia, addresses a, orders o where ei.equipment_id = '" . $equipment_id . "' and (ei.user_id = '" . $user->fetch_user_id() . "' or (ei.user_id = '0' AND ei.agency_id = '" . $user->agency_id . "')) and eia.equipment_item_id = ei.equipment_item_id and ei.equipment_status_id = es.equipment_status_id AND (ei.equipment_status_id = 2 OR ei.equipment_status_id = 1) AND (eia.equipment_status_id = 2 OR eia.equipment_status_id = 1) and a.address_id = eia.address_id and o.address_id = eia.address_id and o.user_id = '" . $user->fetch_user_id() . "' GROUP BY ei.equipment_item_id");
							foreach($database->fetch_array($query) as $result){
								/*if ($result['user_id'] == 0) {
									$vars['agency_result'][] = $result;
								} else {*/
									$vars['items_result'][] = $result;
								/*}*/
								
							}
							$vars['total_available'] = 0;
							$query = $database->query("select ei.equipment_item_id, ei.equipment_status_id from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT_STATUSES . " es where ei.equipment_id = '" . $equipment_id . "' and (ei.user_id = '" . $user->fetch_user_id() . "' or (ei.user_id = '0' AND ei.agency_id = '" . $user->agency_id . "')) and ei.equipment_status_id = es.equipment_status_id AND (ei.equipment_status_id = 0)");
							foreach($database->fetch_array($query) as $result){
								$vars['total_available']++;
							}
							
							$query2 = $database->query("select DISTINCT ei.equipment_item_id, ei.user_id, eia.address_id, a.house_number, a.street_name, a.city, es.equipment_status_name, ei.equipment_status_id from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT_STATUSES . " es, equipment_items_to_addresses eia, addresses a, orders o where ei.equipment_id = '" . $equipment_id . "' and (ei.agency_id = '" . $user->agency_id . "') and eia.equipment_item_id = ei.equipment_item_id and ei.equipment_status_id = es.equipment_status_id AND (ei.equipment_status_id = 2 OR ei.equipment_status_id = 1) AND (eia.equipment_status_id = 2 OR eia.equipment_status_id = 1) and a.address_id = eia.address_id and o.address_id = eia.address_id and o.user_id != '" . $user->fetch_user_id() . "' GROUP BY ei.equipment_item_id");
							foreach($database->fetch_array($query2) as $result2){
								$vars['total_other']++;
							}
							
							/*echo '<pre>';
							print_r($vars['items_result']);
							echo '</pre>';*/
						echo $twig->render('agent/stored_equipment_list.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));			
						
						//LIST ENDS	
					
					} elseif ($page_action == 'view_history') {									
						//VIEW HISTORY STARTS
						
						$count = 0;
						$query = $database->query("select name from " . TABLE_EQUIPMENT . " where equipment_id = '" . $equipment_id . "' limit 1");		
						
						//var_dump($equipment_id);
						
						$vars['result_equipment'] = $database->fetch_array($query);
						$vars['equipment_id'] = $equipment_id;
						
						$query = $database->query("select ei.equipment_item_id, es.equipment_status_name from " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT_STATUSES . " es where ei.equipment_id = '" . $equipment_id . "' and (ei.user_id = '" . $user->fetch_user_id() . "' or (ei.user_id = '0' and ei.agency_id = '" . $user->agency_id . "')) and ei.equipment_status_id = es.equipment_status_id and ei.equipment_item_id = '" . $equipment_item_id . "' limit 1");						
						$vars['result'] = $database->fetch_array($query);			
						$history_query = $database->query("select equipment_item_id, order_id, address_id, history_status_id, history_status_name, date_added, history_status_description from " . TABLE_EQUIPMENT_ITEMS_HISTORY . " where equipment_item_id = '" . $equipment_item_id . "' order by date_added");						
						$history_count = 0;
						foreach($database->fetch_array($history_query) as $history_result){
							$vars['history_result'][] = $history_result; //print_r($vars['history_result']);		
						}																			
							echo $twig->render('agent/stored_equipment_history.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));												
						
						//VIEW HISTORY ENDS	
							
                    } elseif ($page_action == 'opt_out') {
                        if (empty($receive_email)) {
                            $query = $database->query("INSERT INTO " . TABLE_INVENTORY_OPT_OUT . " (user_id, email_opt_out) VALUES ('" . $user->fetch_user_id() . "', '1')");
                            echo "<p>You have successfully opted out from receiving inventory emails.</p>\n";
                        } else {
                            $query = $database->query("DELETE FROM " . TABLE_INVENTORY_OPT_OUT . " WHERE user_id = '" . $user->fetch_user_id() . "' LIMIT 1");
                            echo "<p>Thank you.  You will receive inventory updates in your monthly email.</p>\n";
                        }
					} else {
						
						//DEFAULT STARTS
						
						$count = 0;
						$first = true;
						$query = $database->query("select count(ei.equipment_item_id) as count, ei.equipment_id, e.name, es.equipment_status_name from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei LEFT JOIN " . TABLE_EQUIPMENT_STATUSES . " es ON ei.equipment_status_id = es.equipment_status_id where (ei.user_id = '" . $user->fetch_user_id() . "' or (ei.user_id = '0' and ei.agency_id = '" . $user->agency_id . "')) and ei.equipment_id = e.equipment_id group by ei.equipment_id");																		
							foreach($database->fetch_array($query) as $result){
								$vars['equipment_result'][] = $result;
							}
						$vars['checked'] = $checked;
						echo $twig->render('agent/stored_equipment.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
					}	
?>						
