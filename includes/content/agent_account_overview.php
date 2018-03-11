<?php
//tst
		$user_id = $user->fetch_user_id();

		$query = $database->query("select next_password_reminder, last_password_update from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");

        $result = $database->fetch_array($query);

        // Force reset

        if ($result['next_password_reminder'] == -1) {
            $session->php_session_register('force_password_change', 1);
            tep_redirect('account_change_password.php');
            die;
        }

		
		$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 

		$tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
		
		
      //  echo '<table width="100%" cellspacing="0" cellpadding="0" border="0">';

            // Inventory Alerts

            $query = $database->query("SELECT equipment_id FROM " . TABLE_INVENTORY_WATCHERS . " WHERE user_id = '" . $user_id . "'");

            $watching = array();

            while ($result = $database->fetch_array($query)) {

                $watching[] = $result['equipment_id'];

            }

            $inventory_criticals = array();

            $inventory_warnings = array();

            $inventory_url = "http://" . $_SERVER['SERVER_NAME'] . "/lib/inventory/inventory_json.php5?";

            foreach ($watching as $equipment_id) {

                $inventory_url .= "equipment_id[]=" . $equipment_id . "&";

            }

            if (count($watching)) {

                // Pull the inventory JSON from the API

                $contents = file_get_contents($inventory_url);

                $inventory = json_decode($contents);

                if (is_object($inventory) && property_exists($inventory, "equipment")) {

                    $equipment = $inventory->equipment;

                    foreach ($equipment as $equip) {

                        $urgency = $equip->urgency;

                        $equip_name = $equip->name;

                        if ($urgency == 3) {

                            if ($equip->rule == "Excess at Warehouse") {

                                $inventory_warnings[] = $equip_name . " (Excess)";

                            } else {

                                $inventory_warnings[] = $equip_name;

                            }

                        } elseif ($urgency == 5) {

                            $inventory_criticals[] = $equip_name;

                        }

                    }

                }

            }


			//Now the group specific items.

			$user_group_id = $user->fetch_user_group_id();

			if ($user->user_is_logged()) {
					$user_group_id = $user->fetch_user_group_id();
				}
				if (tep_count_news_items($user_group_id) > 0) {
					$query = $database->query("select ni.news_item_id, ni.date_added, nid.news_item_name, nid.news_item_description from " . TABLE_NEWS_ITEMS . " ni, " . TABLE_NEWS_ITEMS_DESCRIPTION . " nid where (ni.user_group_id = '0'" . ((!empty($user_group_id)) ? " or ni.user_group_id = '" . $user_group_id . "'": '') . ") and ni.news_item_id = nid.news_item_id order by ni.date_added DESC limit 1");
					$result = $database->fetch_array($query);
						//Time to truncate.
						if (strlen($result['news_item_description']) > MAX_LATEST_NEWS_LENGTH) {
							$news_item_description = substr($result['news_item_description'], 0, MAX_LATEST_NEWS_LENGTH).'... <a href="'.FILENAME_VIEW_NEWS .'?news_item_id='.$result['news_item_id'].'">[read more]</a>';
						} else {
							$news_item_description = $result['news_item_description'];
						}
					$newsitems['date_added'] = $result['date_added'];
					$newsitems['news_item_name'] = $result['news_item_name'];
					$newsitems['news_description'] = $news_item_description;
					$vars['newsitem'] = $newsitems;
				}
				
				//print_r($vars['newsitem']);
				

			switch($user_group_id) {
				
				

				case '1': 
//$transactions_where = " WHERE date_added >= '{$date_since}'"
                    //Agent.

					
					echo $twig->render('agent/account_overview.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

				break;
				
				case '4': 
				
				//AOM
				echo $twig->render('aom/account_overview.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
	
				break;

			
			}
	?>
