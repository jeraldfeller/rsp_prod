<?php
//tst
if($user->fetch_user_group_id()==1 || $user->fetch_user_group_id()==4) {
    header('Location: agent_account_overview.php');
}
$user_id = $user->fetch_user_id();

$query = $database->query("select next_password_reminder, last_password_update from " . TABLE_USERS . " where user_id = '" . $user_id . "' limit 1");

$result = $database->fetch_array($query);

// Force reset

if ($result['next_password_reminder'] == -1) {
    $session->php_session_register('force_password_change', 1);
    tep_redirect('account_change_password.php');
    die;
}


$today = time(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp()));

$tomorrow = time(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));


/*echo '<table width="100%" cellspacing="0" cellpadding="0" border="0">';

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

    if (count($inventory_criticals)) {

    ?>

    <tr>
      <td width="100%" align="left"><table width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td width="16" height="16"><img src="images/error.gif" height="16" width="16"></td>
            <td width="5"><img src="images/pixel_trans.gif" width="5" height="1"></td>
            <td width="100%" align="left" height="16" valign="top" class="main">Inventory Level Critical Alert: <em>
              <?

                    foreach ($inventory_criticals as $index => $alert) {

                        if ($index > 0) {

                            echo ", ";

                        }

                        echo "{$alert}";

                    }

                    ?>
              </em></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
    </tr>
    <?php

    }

    if (count($inventory_warnings)) {

    ?>
    <tr>
      <td width="100%" align="left"><table width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td width="16" height="16"><img src="images/warning.gif" height="16" width="16"></td>
            <td width="5"><img src="images/pixel_trans.gif" width="5" height="1"></td>
            <td width="100%" align="left" height="16" valign="top" class="main">Inventory Level Warning: <em>
              <?

                    foreach ($inventory_warnings as $index => $alert) {

                        if ($index > 0) {

                            echo ", ";

                        }

                        echo "{$alert}";

                    }

                    ?>
              </em></td>
        </table></td>
    </tr>
    <tr>
      <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
    </tr>
    <?php

    }*/



//Now the group specific items.

$user_group_id = $user->fetch_user_group_id();

switch($user_group_id) {

    case '1':

        //Agent.



        // Deferred Billing?

        // Check if the Agent is setup for CC billing

        $billing_method_id = tep_fill_variable('billing_method_id', 'session', 1);

        if ($billing_method_id == 1) {

            $account_id = account::getAccountId($user_id, $user->agency_id, $billing_method_id, false);

            if ($account_id > 0) {

                $deferred = new DeferredBilling($account_id);

                $deferred_total = $deferred->getTotal();

                if ($deferred_total > 0) {

                    ?>
                    <tr>
                        <td width="100%" align="left"><table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="16" height="16"><img src="images/warning.gif" height="16" width="16"></td>
                                    <td width="5"><img src="images/pixel_trans.gif" width="5" height="1"></td>
                                    <td width="100%" align="left" height="16" valign="top" class="main">Account Alert: <em>You have an unpaid balance of $<?php echo number_format($deferred_total, 2); ?> that will be added to your next order.  For more information or make a payment, <a class="StatLink" href="<?php echo FILENAME_DEFERRED_PAYMENT; ?>">click here.</a></em></td>
                                </tr>
                            </table></td>
                    </tr>
                    <tr>
                        <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
                    </tr>
                    <?php

                }

            }

        }

        $sql_active_addresses = "select distinct a.address_id  from addresses a left join orders o on (a.address_id = o.address_id and o.order_type_id = '3') left join orders ow on (a.address_id = ow.address_id and ow.order_type_id = '1'), addresses_to_users atu, states s, countys c where atu.user_id = '" . $user_id . "' and atu.address_id = a.address_id and a.state_id = s.state_id and a.county_id = c.county_id and (o.order_status_id != '3' or (o.order_id is NULL and a.status < '3')) and ow.order_status_id != '4' group by a.address_id ";

        $query = $database->query($sql_active_addresses);

        $active_addresses = $database->num_rows($query);



        ?>
        <tr>
            <td align="left" class="main"><b>Your Account at a Glance</b></td>
        </tr>
        <tr>
            <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
        </tr>
        <tr>
            <td width="100%"><table width="100%" cellspacing="3" cellpadding="0">
                    <tr>
                        <td width="350"><img src="images/pixel_trans.gif" height="1" width="220" /></td>
                    </tr>
                    <tr>
                        <td class="main" width="350">Total # of Active Addresses:&nbsp;<a href="agent_active_addresses.php" class="StatLink"><?php echo $active_addresses; ?></a></td>
                    </tr>
                </table></td>
        </tr>
        <tr>
            <td align="left" class="main">&nbsp;</td>
        </tr>
        <tr>
            <td width="100%"><table width="100%" cellspacing="3" cellpadding="0" border="0">
                    <tr>
                        <td ><img src="images/pixel_trans.gif" height="1" width="220" /></td>
                    </tr>
                    <?php

                    $date_pending = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+3), date("Y", tep_fetch_current_timestamp()));





                    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.user_id = '" . $user_id . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_status_id = '1' and o.order_type_id = '1'  and o.date_schedualed > 0 and o.date_schedualed < '" . $date_pending . "'");



                    $result = $database->fetch_array($query);

                    $pending_orders_install = $result['count'];



                    $query = $database->query("select count(o.order_id)  as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.user_id = '" . $user_id . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_status_id = '1' and o.order_type_id = '3'  and o.date_schedualed > 0 and o.date_schedualed < '" . $date_pending . "'");



                    $result = $database->fetch_array($query);

                    $pending_orders_removal = $result['count'];



                    $query = $database->query("select count(o.order_id)  as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.user_id = '" . $user_id . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_status_id = '1' and o.order_type_id = '2'  and o.date_schedualed > 0 and o.date_schedualed < '" . $date_pending . "'");



                    $result = $database->fetch_array($query);

                    $pending_orders_svc = $result['count'];



                    ?>
                    <tr>
                        <td class="main" ><b>Current Pending Orders</b> (to be completed in the next two business days):&nbsp;Total (<?php echo ($pending_orders_svc + $pending_orders_removal + $pending_orders_install)?>)</td>
                    </tr>
                    <tr>
                        <td class="main" >Installs (<?php echo $pending_orders_install?>) &nbsp;&nbsp;&nbsp;Removals (<?php echo $pending_orders_removal?>)&nbsp;&nbsp;&nbsp;SVCs (<?php echo $pending_orders_svc?>)</td>
                    </tr>
                    <tr>
                        <td class="main" style="padding-left:15px;" ><i>NOTE: You can make changes to the details of these orders under Active Addresses</i></td>
                    </tr>
                    <tr>
                        <td >&nbsp;</td>
                    </tr>
                    <?php

                    $date_pending = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+3), date("Y", tep_fetch_current_timestamp()));





                    $query = $database->query("select count(o.order_id)  as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.user_id = '" . $user_id . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_status_id = '2' and o.order_type_id = '1'  and o.date_schedualed > 0 ");



                    $result = $database->fetch_array($query);

                    $pending_orders_install = $result['count'];



                    $query = $database->query("select count(o.order_id)  as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.user_id = '" . $user_id . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_status_id = '2' and o.order_type_id = '3'  and o.date_schedualed > 0 ");



                    $result = $database->fetch_array($query);

                    $pending_orders_removal = $result['count'];



                    $query = $database->query("select count(o.order_id)  as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.user_id = '" . $user_id . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_status_id = '2' and o.order_type_id = '2'  and o.date_schedualed > 0 ");



                    $result = $database->fetch_array($query);

                    $pending_orders_svc = $result['count'];



                    ?>
                    <tr>
                        <td class="main"><b>Scheduled Orders</b> (on the installers schedule):&nbsp;Total (<?php echo ($pending_orders_svc + $pending_orders_removal + $pending_orders_install)?>)</td>
                    </tr>
                    <tr>
                        <td class="main" >Installs (<?php echo $pending_orders_install?>)&nbsp;&nbsp;&nbsp;Removals (<?php echo $pending_orders_removal?>)&nbsp;&nbsp;&nbsp;SVCs (<?php echo $pending_orders_svc?>)</td>
                    </tr>
                    <tr>
                        <td class="main" style="padding-left:15px;"><i>NOTE: If you need to change/cancel these orders, please call or e-mail us.</i></td>
                    </tr>
                    <tr>
                        <td >&nbsp;</td>
                    </tr>
                    <?php

                    $date_pending = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+3), date("Y", tep_fetch_current_timestamp()));





                    $query = $database->query("select count(o.order_id)  as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.user_id = '" . $user_id . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_status_id = '1' and o.order_type_id = '1'  and o.date_schedualed > 0 and o.date_schedualed >= '" . $date_pending . "'");



                    $result = $database->fetch_array($query);

                    $pending_orders_install = $result['count'];



                    $query = $database->query("select count(o.order_id)  as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.user_id = '" . $user_id . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_status_id = '1' and o.order_type_id = '3'  and o.date_schedualed > 0 and o.date_schedualed >= '" . $date_pending . "'");



                    $result = $database->fetch_array($query);

                    $pending_orders_removal = $result['count'];



                    $query = $database->query("select count(o.order_id)  as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os where o.user_id = '" . $user_id . "' and o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_status_id = '1' and o.order_type_id = '2'  and o.date_schedualed > 0 and o.date_schedualed >= '" . $date_pending . "'");



                    $result = $database->fetch_array($query);

                    $pending_orders_svc = $result['count'];



                    ?>
                    <tr>
                        <td class="main" ><b>Future Pending Orders</b> (orders to be completed in two+ business days):&nbsp;Total (<?php echo ($pending_orders_svc + $pending_orders_removal + $pending_orders_install)?>)</td>
                    </tr>
                    <tr>
                        <td class="main">Installs (<?php echo $pending_orders_install?>)&nbsp;&nbsp;&nbsp;Removals (<?php echo $pending_orders_removal?>)&nbsp;&nbsp;&nbsp;SVCs (<?php echo $pending_orders_svc?>)</td>
                    </tr>
                    <tr>
                        <td >&nbsp;</td>
                    </tr>
                </table></td>
        </tr>
        <?php

        break;
    case '6':

        //Admin.


        //echo '<table width="100%" cellspacing="0" cellpadding="0" border="0">';

        // Inventory Alerts

        $query = $database->query("SELECT equipment_id FROM " . TABLE_INVENTORY_WATCHERS . " WHERE user_id = '788'");

        $watching = array();
        foreach($database->fetch_array($query) as $result){

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

        if (count($inventory_criticals)) {

            ?>

            <tr>
                <td width="100%" align="left"><table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="16" height="16"><img src="images/error.gif" height="16" width="16"></td>
                            <td width="5"><img src="images/pixel_trans.gif" width="5" height="1"></td>
                            <td width="100%" align="left" height="16" valign="top" class="main">Inventory Level Critical Alert: <em>
                                    <?

                                    foreach ($inventory_criticals as $index => $alert) {

                                        if ($index > 0) {

                                            echo ", ";

                                        }

                                        echo "{$alert}";

                                    }

                                    ?>
                                </em></td>
                        </tr>
                    </table></td>
            </tr>
            <tr>
                <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
            </tr>
            <?php

        }

        if (count($inventory_warnings)) {

            ?>
            <tr>
                <td width="100%" align="left"><table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="16" height="16"><img src="images/warning.gif" height="16" width="16"></td>
                            <td width="5"><img src="images/pixel_trans.gif" width="5" height="1"></td>
                            <td width="100%" align="left" height="16" valign="top" class="main">Inventory Level Warning: <em>
                                    <?

                                    foreach ($inventory_warnings as $index => $alert) {

                                        if ($index > 0) {

                                            echo ", ";

                                        }

                                        echo "{$alert}";

                                    }

                                    ?>
                                </em></td>
                    </table></td>
            </tr>
            <tr>
                <td height="15"><img src="images/pixel_trans.gif" height="15" width="1" /></td>
            </tr>
            <?php

            }


        $today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp()));

        $query_install_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";
        $query = $database->query($query_install_new_db);
        $result = $database->fetch_array($query);
        $count_install_new_db = $result['count'];

        $query_removal_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '3' and o.date_added > 0 and o.date_added >= '" . $today . "'";
        //echo $query_removal_new_db;
        $query = $database->query($query_removal_new_db);
        $result = $database->fetch_array($query);
        $count_removal_new_db = $result['count'];

        $query_svc_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '2' and o.date_added > 0 and o.date_added >= '" . $today . "'";

        $query = $database->query($query_svc_new_db);
        $result = $database->fetch_array($query);
        $count_svc_new_db = $result['count'];

        $midnight_ts = strtotime("midnight");

        $reschedule_table = TABLE_RESCHEDULE_HISTORY;

        $orders_table = TABLE_ORDERS;

        $rescheduled_today_sql = "SELECT o.order_id, rh.new_scheduled_date, rh.old_scheduled_date FROM {$reschedule_table} rh JOIN {$orders_table} o ON (o.order_id = rh.order_id) WHERE rh.rescheduled_date >= {$midnight_ts} AND o.order_type_id = 3 ORDER BY o.order_id, rh.rescheduled_date";

        $rescheduled_orders = array();



        $query = $database->query($rescheduled_today_sql);
        foreach($database->fetch_array($query) as $result){

            $order_id = $result['order_id'];

            if (array_key_exists($order_id, $rescheduled_orders)) {

                $rescheduled_orders[$order_id]['new_scheduled_date'] = $result['new_scheduled_date'];

            } else {

                $rescheduled_orders[$order_id] = array();

                $rescheduled_orders[$order_id]['old_scheduled_date'] = $result['old_scheduled_date'];

                $rescheduled_orders[$order_id]['new_scheduled_date'] = $result['new_scheduled_date'];

            }

        }



        $rescheduled_count = count($rescheduled_orders);

        $pushed_back_count = 0;

        $moved_up_count = 0;



        foreach ($rescheduled_orders as $order_id => $schedule) {

            if ($schedule['old_scheduled_date'] < $schedule['new_scheduled_date']) {

                $pushed_back_count++;

            } elseif ($schedule['old_scheduled_date'] > $schedule['new_scheduled_date']) {

                $moved_up_count++;

            }

        }
    case '2':

    //Admin.

    $midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));
    $midnight_future = ($midnight_tonight + ((60*60*24) * 1));
    $last30 = strtotime("-".INACTIVE_MONTHS." months");
    $fully_inactive_agents = array();
    //echo $last30;
    /*$query_users = "select u.user_id, max(o.date_added) as mx from orders o, users u where o.user_id = u.user_id and u.last_login<=".$last30." and u.active_status=1 GROUP BY user_id HAVING max(o.date_added)<=".$last30;*/
    $query_users = "select DISTINCT u.user_id, max(o.date_added) as mxorder, u.last_login, u.email_address, ud.firstname, ud.lastname, ug.name, ug.user_group_id, u.agency_id, u.active_status from " . TABLE_USER_GROUPS . " ug, " . TABLE_USERS . " u left join orders o on (o.user_id=u.user_id) left join " . TABLE_AGENCYS . " a on (u.agency_id = a.agency_id), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and u.active_status=1 and ug.user_group_id=1 and u.last_login<=".$last30." GROUP BY ud.firstname, ud.lastname, u.user_id HAVING max(o.date_added)<=".$last30." order by ud.lastname, ud.firstname";
    $query = $database->query($query_users);
    //$users_inactive = $database->fetch_array($query);
    $count_inactives = 0;
    foreach($query as $result){
        /*echo $result['user_id'];
        echo "<br>";*/
        $count_inactives++;
        $fully_inactive_agents[] = $result['user_id'];
    }
    //inactive agencies
      $inactive_agencies = 0;
      $query_agency = $database->query("select agency_id from " . TABLE_AGENCYS . " where order_hold = '0' order by name, office");
        foreach($query as $result){
        //$count_inactives++;
        $count_query = $database->query("select count(u.user_id) as count from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.active_status = '1'");
        $count_result = $database->fetch_array($count_query);
        $total_agents = $count_result['count'];
        $active_agents = $count_result['count'];
        //check for inactive agents
        $count_query = $database->query("select count(u.user_id) as count from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.active_status = '0'");
        $count_result = $database->fetch_array($count_query);
        $total_agents += $count_result['count'];
        $inactive_agents = $count_result['count'];
        $count_current_agents_query = $database->query("select u.user_id from " . TABLE_USERS . " u, " . TABLE_AGENCYS . " a where u.agency_id = a.agency_id and (a.agency_id = '" . $result['agency_id'] . "' or a.parent_agency_id = '" . $result['agency_id'] . "') and u.active_status = '1'");
        $all_inactive = true;
        foreach($count_current_agents_query as $result_current_agents){
            if(!in_array($result_current_agents['user_id'], $fully_inactive_agents)) {
                $all_inactive = false;
                continue;
            }
        }
        if ( ($active_agents == 0) || ($total_agents == $inactive_agents) || ($all_inactive) ) {
            $inactive_agencies++;
        }
    }

      /*
       * Inventory Alerts
       */
      $query = $database->query("SELECT equipment_id FROM " . TABLE_INVENTORY_WATCHERS . " WHERE user_id = '788'");
      $watching = array();
      foreach($query as $result){
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


      /**
       * Issues: Unsigned, Red Flag, On Hold
       */
       $query_red_flag = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_issue = '1'";
       $query = $database->query($query_red_flag);
       $result = $database->fetch_array($query);
       $red_flag_orders = $result['count'];
       $query_on_hold = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '5' and o.address_id = a.address_id ";
       $query = $database->query($query_on_hold);
       $result = $database->fetch_array($query);
       $on_hold_orders = $result['count'];

       /*
        * Miss Utilities: Open, Called, Completed, Percentage
        */

        $query_miss_utility_open = "select count(o.order_id) as count ";
        $query_miss_utility_open.= "from " . TABLE_ORDERS . " o ";
        $query_miss_utility_open.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";
        $query_miss_utility_open.= "where o.order_status_id < 3 and omu.contacted = 0 ";
        $query_miss_utility_open.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";
        $muq = $database->query($query_miss_utility_open);
        $result = $database->fetch_array($muq);
        $miss_utility_open = ($result['count'] > 0) ? $result['count'] : 0;
        //bgdn
        $query = "select count(order_miss_utility_id) as count from ". TABLE_ORDERS_MISS_UTILITY;
        $muq = $database->query($query);
        $result = $database->fetch_array($muq);
        $miss_utility_all_bgdn = ($result['count'] > 0) ? $result['count'] : 0;
        $query = "select count(order_id) as count from ". TABLE_ORDERS ." WHERE order_id > 109892";
        $muq = $database->query($query);
        $result = $database->fetch_array($muq);
        $orders_all_bgdn = ($result['count'] > 0) ? $result['count'] : 0;
        $percentage = ($miss_utility_all_bgdn*100)/$orders_all_bgdn;
        $query_miss_utility_called = "select count(o.order_id) as count ";
        $query_miss_utility_called.= "from " . TABLE_ORDERS . " o ";
        $query_miss_utility_called.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";
        $query_miss_utility_called.= "where o.order_status_id < 3 and omu.contacted = 1 ";
        $query_miss_utility_called.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";
        $muq = $database->query($query_miss_utility_called);
        $result = $database->fetch_array($muq);
        $miss_utility_called = ($result['count'] > 0) ? $result['count'] : 0;
        $query_miss_utility_completed = "select count(o.order_id) as count ";
        $query_miss_utility_completed.= "from " . TABLE_ORDERS . " o ";
        $query_miss_utility_completed.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";
        $query_miss_utility_completed.= "where o.order_status_id = 3 and omu.contacted = 1 ";
        $query_miss_utility_completed.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";
        $muq = $database->query($query_miss_utility_completed);
        $result = $database->fetch_array($muq);
        $miss_utility_completed = ($result['count'] > 0) ? $result['count'] : 0;

        /*
         * ORDERED TODAY, RESCHEDULED TODAY
         */
         $today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp()));
         $query_install_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";
         $query = $database->query($query_install_new_db);
         $result = $database->fetch_array($query);
         $count_install_new_db = $result['count'];
         $query_removal_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '3' and o.date_added > 0 and o.date_added >= '" . $today . "'";
         $query = $database->query($query_removal_new_db);
         $result = $database->fetch_array($query);
         $count_removal_new_db = $result['count'];
         $query_svc_new_db = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '2' and o.date_added > 0 and o.date_added >= '" . $today . "'";
         $query = $database->query($query_svc_new_db);
         $result = $database->fetch_array($query);
         $count_svc_new_db = $result['count'];
         $midnight_ts = strtotime("midnight");
         $reschedule_table = TABLE_RESCHEDULE_HISTORY;
         $orders_table = TABLE_ORDERS;
         $rescheduled_today_sql = "SELECT o.order_id, rh.new_scheduled_date, rh.old_scheduled_date FROM {$reschedule_table} rh JOIN {$orders_table} o ON (o.order_id = rh.order_id) WHERE rh.rescheduled_date >= {$midnight_ts} AND o.order_type_id = 3 ORDER BY o.order_id, rh.rescheduled_date";
         $rescheduled_orders = array();
         $query = $database->query($rescheduled_today_sql);
         foreach($query as $result){
             $order_id = $result['order_id'];
             if (array_key_exists($order_id, $rescheduled_orders)) {
                 $rescheduled_orders[$order_id]['new_scheduled_date'] = $result['new_scheduled_date'];
             } else {
                 $rescheduled_orders[$order_id] = array();
                 $rescheduled_orders[$order_id]['old_scheduled_date'] = $result['old_scheduled_date'];
                 $rescheduled_orders[$order_id]['new_scheduled_date'] = $result['new_scheduled_date'];
             }
         }
         $rescheduled_count = count($rescheduled_orders);
         $pushed_back_count = 0;
         $moved_up_count = 0;
         foreach ($rescheduled_orders as $order_id => $schedule) {
             if ($schedule['old_scheduled_date'] < $schedule['new_scheduled_date']) {
                 $pushed_back_count++;
             } elseif ($schedule['old_scheduled_date'] > $schedule['new_scheduled_date']) {
                 $moved_up_count++;
             }
         }

         /**
          * Postal Total Change
          */

          $DateStartYesterday = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-1), date("Y", tep_fetch_current_timestamp()));
          $DateEndYesterday = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp()));
          $sqlCompleteInstallYesterday = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $DateStartYesterday . "' and o.date_completed < '" . $DateEndYesterday . "' and o.order_type_id = '1'";
          $query = $database->query($sqlCompleteInstallYesterday);
          $result = $database->fetch_array($query);
          $CompleteInstallYesterday = $result['count'];
          $sqlCompleteRemoveYesterday = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $DateStartYesterday . "' and o.date_completed < '" . $DateEndYesterday . "' and o.order_type_id = '3'";
          $query = $database->query($sqlCompleteRemoveYesterday);
          $result = $database->fetch_array($query);
          $CompleteRemoveYesterday = $result['count'];
          $postTotalChange = $CompleteInstallYesterday - $CompleteRemoveYesterday;

          $DateStartLastWeek = strtotime('today - 6days 00:00:01');
          $DateEndLastWeek = strtotime('today 23:59:59');
          $sqlCompleteInstallLastWeek = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $DateStartLastWeek . "' and o.date_completed < '" . $DateEndLastWeek . "' and o.order_type_id = '1'";
          $query = $database->query($sqlCompleteInstallLastWeek);
          $result = $database->fetch_array($query);
          $CompleteInstallLastWeek = $result['count'];
          $sqlCompleteRemoveLastWeek = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $DateStartLastWeek . "' and o.date_completed < '" . $DateEndLastWeek . "' and o.order_type_id = '3'";
          $query = $database->query($sqlCompleteRemoveLastWeek);
          $result = $database->fetch_array($query);
          $CompleteRemoveLastWeek = $result['count'];
          $postTotalChangeLastWeek = $CompleteInstallLastWeek - $CompleteRemoveLastWeek;

          /**
            * Overdue orders
            */
            $date_pending_overdue = time(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-2), date("Y", tep_fetch_current_timestamp()));
            $date_pending_1_overdue = time(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-3), date("Y", tep_fetch_current_timestamp()));
            $query_pending = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '1' and o.address_id = a.address_id and o.date_schedualed > 0 and o.date_schedualed < '" . $date_pending_overdue . "'";
            $query = $database->query($query_pending);
            $result = $database->fetch_array($query);
            $pending_orders = $result['count'];
            $query_scheduled = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '2' and o.address_id = a.address_id and o.date_schedualed > 0 and o.date_schedualed <= '" . $date_pending_overdue . "'";
            $query = $database->query($query_scheduled);
            $result = $database->fetch_array($query);
            $scheduled_orders = $result['count'];

        /**
         * Current Active Orders
         */
        $date_pending_current_active_pending = strtotime('+1 days');
        $date_pending_1_current_active_pending = time();
         //$date_pending_current_active_pending = time(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+3), date("Y", tep_fetch_current_timestamp()));
         //$date_pending_1_current_active_pending = time(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+2), date("Y", tep_fetch_current_timestamp()));
         $sql_query = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '1' and o.address_id = a.address_id and o.date_schedualed < '" . $date_pending_current_active_pending . "' and o.order_type_id = '1'";
         $query = $database->query($sql_query);
         $result = $database->fetch_array($query);
         $pending_orders_install = $result['count'];
         $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '1' and o.address_id = a.address_id and o.date_schedualed < '" . $date_pending_current_active_pending . "' and o.order_type_id = '3'");
         $result = $database->fetch_array($query);
         $pending_orders_removal = $result['count'];
         $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '1' and o.address_id = a.address_id and o.date_schedualed < '" . $date_pending_current_active_pending . "' and o.order_type_id = '2'");
         $result = $database->fetch_array($query);
         $pending_orders_svc = $result['count'];

            $date_pending_current_active_scheduled = time();
            $date_pending_1_current_active_scheduled = time();
         //$date_pending_current_active_scheduled =  time(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+2), date("Y", tep_fetch_current_timestamp()));
         //$date_pending_1_current_active_scheduled = time(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));
         $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '2' and o.address_id = a.address_id and o.date_schedualed < '" . $date_pending_current_active_scheduled . "' and o.order_type_id = '1'");
         $result = $database->fetch_array($query);
         $schedule_orders_install = $result['count'];
         $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '2' and o.address_id = a.address_id and o.date_schedualed < '" . $date_pending_current_active_scheduled . "' and o.order_type_id = '3'");
         $result = $database->fetch_array($query);
         $schedule_orders_removal = $result['count'];
         $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '2' and o.address_id = a.address_id and o.date_schedualed < '" . $date_pending_current_active_scheduled . "' and o.order_type_id = '2'");
         $result = $database->fetch_array($query);
         $schedule_orders_svc = $result['count'];


        /**
         * Completed Orders
         */

         $date_start = strtotime('-1 days');
         $date_end = time();
         //$date_start = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-1), date("Y", tep_fetch_current_timestamp()));
         //$date_end = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp()));
         $query_complete_install = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $date_start . "' and o.date_completed < '" . $date_end . "' and o.order_type_id = '1'";
         $query = $database->query($query_complete_install);
         $result = $database->fetch_array($query);
         $complete_orders_install = $result['count'];
         $query_complete_removal = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $date_start . "' and o.date_completed < '" . $date_end . "' and o.order_type_id = '3'";
         $query = $database->query($query_complete_removal);
         $result = $database->fetch_array($query);
         $complete_orders_removal = $result['count'];
         $query_complete_svc = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $date_start . "' and o.date_completed < '" . $date_end . "' and o.order_type_id = '2'";
         $query = $database->query($query_complete_svc);
         $result = $database->fetch_array($query);
         $complete_orders_svc= $result['count'];

         //$date_start = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp()));
         //$date_end = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));
         $date_start = time();
         $date_end = strtotime('+1 days');

         $date_start_today = time();
         $date_start_tomorrow = strtotime('+1 days');
         $date_start_yesterday = strtotime('-1 days');

         $query_complete_install = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $date_start . "' and o.date_completed < '" . $date_end . "' and o.order_type_id = '1'";
         $query = $database->query($query_complete_install);
         $result = $database->fetch_array($query);
         $complete_orders_install = $result['count'];
         $query_complete_removal = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $date_start . "' and o.date_completed < '" . $date_end . "' and o.order_type_id = '3'";
         $query = $database->query($query_complete_removal);
         $result = $database->fetch_array($query);
         $complete_orders_removal = $result['count'];
         $query_complete_svc = 	"select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '3' and o.address_id = a.address_id and o.date_completed >= '" . $date_start . "' and o.date_completed < '" . $date_end . "' and o.order_type_id = '2'";
         $query = $database->query($query_complete_svc);
         $result = $database->fetch_array($query);
         $complete_orders_svc= $result['count'];

         /**
          * Future Orders
          */

          //$date_pending = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+3), date("Y", tep_fetch_current_timestamp()));
          $date_pending_future_orders_3 = strtotime('+3 days');
          $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '1' and o.address_id = a.address_id and o.date_schedualed >= '" . $date_pending_future_orders_3 . "' and o.order_type_id = '1'");
          $result = $database->fetch_array($query);
          $pending_orders_install = $result['count'];
          $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '1' and o.address_id = a.address_id and o.date_schedualed >= '" . $date_pending_future_orders_3 . "' and o.order_type_id = '3'");
          $result = $database->fetch_array($query);
          $pending_orders_removal = $result['count'];
          $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '1' and o.address_id = a.address_id and o.date_schedualed >= '" . $date_pending_future_orders_3 . "' and o.order_type_id = '2'");
          $result = $database->fetch_array($query);
          $pending_orders_svc = $result['count'];

          //$date_pending = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+2), date("Y", tep_fetch_current_timestamp()));
          $date_pending_future_orders_2 = strtotime('+2 days');
          $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '2' and o.address_id = a.address_id and o.date_schedualed >= '" . $date_pending_future_orders_2 . "' and o.order_type_id = '1'");
          $result = $database->fetch_array($query);
          $schedule_orders_install = $result['count'];
          $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '2' and o.address_id = a.address_id and o.date_schedualed >= '" . $date_pending_future_orders_2 . "' and o.order_type_id = '3'");
          $result = $database->fetch_array($query);
          $schedule_orders_removal = $result['count'];
          $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '2' and o.address_id = a.address_id and o.date_schedualed >= '" . $date_pending_future_orders_2 . "' and o.order_type_id = '2'");
          $result = $database->fetch_array($query);
          $schedule_orders_svc = $result['count'];

          /**
           * Inventory Summarry
           */
           $inventory_summary_url = "http://" . $_SERVER['SERVER_NAME'] . "/lib/inventory/inventory_json.php5?summary=1";
           $contents = file_get_contents($inventory_summary_url);
           $inventory_summary = json_decode($contents);
           $ffx_posts_in_field = 0;
           $posts_avail = 0;
           $md_posts_in_field = 0;
           $pa_posts_in_field = 0;
           // $posts_in_field = 0;
           $posts_operational =0;
           if (is_object($inventory_summary)) {
               $ffx_posts_in_field = $inventory_summary->ffx_posts_installed;
               $md_posts_in_field = $inventory_summary->md_posts_installed;
               $pa_posts_in_field = $inventory_summary->pa_posts_installed;
               $posts_avail = $inventory_summary->posts_avail;
               $posts_operational = $inventory_summary->posts_total;
               // $posts_in_field = $inventory_summary->posts_installed;
           }

           $posts_total = $ffx_posts_in_field+$pa_posts_in_field+$md_posts_in_field;
           $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '1' and o.address_id = a.address_id  and o.order_type_id = '3'");
           $result = $database->fetch_array($query);
           $pending_removal = $result['count'];
           $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '2' and o.address_id = a.address_id  and o.order_type_id = '3'");
           $result = $database->fetch_array($query);
           $scheduled_removal = $result['count'];

           /**
            * New Active Agencies
            */
            $query_new_agencies = "select count(*) as x from " . TABLE_AGENCYS . " where agency_status_id = '0' and parent_agency_id = ''";
            $query = $database->query($query_new_agencies);
            $result = $database->fetch_array($query);
            $new_agencies_count = $result['x'];
            $query_active_agencies = "select count(*) as x from " . TABLE_AGENCYS . " where agency_status_id = '1' and parent_agency_id = ''";
            $query = $database->query($query_active_agencies);
            $result = $database->fetch_array($query);
            $active_agencies_count = $result['x'];

          /**
           * MONEY STATISTICS
           */

           $today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp()));
           $tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));
           $month_first_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp()));
           $year_first_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp()));
           $queryDeferredToday = "select sum(amount) as value from " . TABLE_TRANSACTIONS . " where (billing_method_id = '2' or billing_method_id = '3') and date_added >= '" . $today . "'";

           $query = $database->query($queryDeferredToday);
           $resultDeferredToday = $database->fetch_array($query);
           $countDeferredToday = $resultDeferredToday['value']=='' ? 0 : $resultDeferredToday['value'];

           $queryDeferred7 = "select sum(amount) as value from " . TABLE_TRANSACTIONS . " where (billing_method_id = '2' or billing_method_id = '3') and date_added >= '" . strtotime("today - 7 days") . "'";
           $query = $database->query($queryDeferred7);
           $resultDeferred7 = $database->fetch_array($query);
           $countDeferred7 = number_format($resultDeferred7['value']=='' ? 0 : $resultDeferred7['value'], 2);


           $queryTotalPaid7 = "select sum(amount) as value from " . TABLE_TRANSACTIONS . " where billing_method_id = '1' and date_added >= '" . strtotime("today - 7 days") . "'";
           $query = $database->query($queryTotalPaid7);
           $resultTotalPaid7 = $database->fetch_array($query);
           $countTotalPaid7 = number_format($resultTotalPaid7['value']=='' ? 0 : $resultTotalPaid7['value'], 2);

           $percentUnpaid7 = number_format(($countTotalPaid7 == 0 ? 100 : (($countDeferred7 - $countTotalPaid7) / $countDeferred7) * 100), 2);

           // Deffered currnet month
           $queryDeferredCurrentMonth = "select sum(amount) as value from " . TABLE_TRANSACTIONS . " where (billing_method_id = '2' or billing_method_id = '3') and date_added >= '" . strtotime(date('Y-m-01')) . "' and date_added <= '" . strtotime(date('Y-m-d')) . "'";
           $query = $database->query($queryDeferredCurrentMonth);
           $resultDeferredCurrentMonth = $database->fetch_array($query);
           $countDeferredCurrentMonth = number_format($resultDeferredCurrentMonth['value']=='' ? 0 : $resultDeferredCurrentMonth['value'], 2);

           $queryTotalPaidCurrentMonth = "select sum(amount) as value from " . TABLE_TRANSACTIONS . " where billing_method_id = '1' and date_added >= '" . strtotime(date('Y-m-01')) . "' and date_added <= '" . strtotime(date('Y-m-d')) . "'";
           $query = $database->query($queryTotalPaidCurrentMonth);
           $resultTotalPaidCurrentMonth = $database->fetch_array($query);
           $countTotalPaidCurrentMonth = number_format($resultTotalPaidCurrentMonth['value']=='' ? 0 : $resultTotalPaidCurrentMonth['value'], 2);

           $percentUnpaidCurrentMonth = number_format(($countTotalPaidCurrentMonth == 0 ? 100 : (($countDeferredCurrentMonth - $countTotalPaidCurrentMonth) / $countDeferredCurrentMonth) * 100), 2);

           // Deffered Previous Month
           $queryDeferredPrevMonth = "select sum(amount) as value from " . TABLE_TRANSACTIONS . " where (billing_method_id = '2' or billing_method_id = '3') and date_added >= '" . strtotime(date('Y-m-01', strtotime('-1 month'))) . "' and date_added <= '" . strtotime(date('Y-m-t', strtotime('-1 month'))) . "'";
           $query = $database->query($queryDeferredPrevMonth);
           $resultDeferredPrevMonth = $database->fetch_array($query);
           $countDeferredPrevMonth = number_format($resultDeferredPrevMonth['value']=='' ? 0 : $resultDeferredPrevMonth['value'], 2);

           $queryTotalPaidPrevMonth = "select sum(amount) as value from " . TABLE_TRANSACTIONS . " where billing_method_id = '1' and date_added >= '" . strtotime(date('Y-m-01', strtotime('-1 month'))) . "' and date_added <= '" . strtotime(date('Y-m-t', strtotime('-1 month'))) . "'";
           $query = $database->query($queryTotalPaidPrevMonth);
           $resultTotalPaidPrevMonth = $database->fetch_array($query);
           $countTotalPaidPrevMonth = number_format($resultTotalPaidPrevMonth['value']=='' ? 0 : $resultTotalPaidPrevMonth['value'], 2);

           $percentUnpaidPrevMonth = number_format(($countTotalPaidPrevMonth == 0 ? 100 : (($countDeferredPrevMonth - $countTotalPaidPrevMonth) / $countDeferredPrevMonth) * 100), 2);


           $queryDeferredTotal = "select sum(amount) as value from " . TABLE_TRANSACTIONS . " where (billing_method_id = '2' or billing_method_id = '3')";
           $query = $database->query($queryDeferredTotal);
           $resultDeferredTotal = $database->fetch_array($query);
           $countDeferredTotal = $resultDeferredTotal['value'];

           // CC Agents with credit balance
           $queryNumberOfAgents = "SELECT count(u.user_id) as count, sum(a.running_total) as value FROM " . TABLE_USERS . " u, " . TABLE_USERS_TO_USER_GROUPS . " ug, " . TABLE_ACCOUNTS . " a WHERE u.user_id = ug.user_id AND ug.user_group_id = 1 AND u.user_id = a.user_id AND a.running_total > 0";
           $query = $database->query($queryNumberOfAgents);
           $result = $database->fetch_array($query);
           $totalCountOfAgentsWithCredit = $result['count'];
           $valueCreditBalance = $result['value'];

           $query_install_today = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";
           //$query_install_today = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";
           //echo $query_install_today;exit;

           $query = $database->query($query_install_today);
           $result = $database->fetch_array($query);
           $count_install_today = $result['count'];
           $value_install_today = $result['value'];

           // This query will get orders with payment method CC (CREDIT CARD)
           $queryOrderCC = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "'";
           //echo $queryOrderCC;
           //echo strtotime("yesterday");
           $query = $database->query($queryOrderCC);
           $result = $database->fetch_array($query);
           $countCC = $result['count'];
           $valueCC = $result['value'];

           // This query will get orders with payment method IO (Invoice Orders) for both Agency Monthly Invoice & Agent Monthly Invoice
           $queryOrderIO = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_added > 0 and o.date_added >= '" . $today . "'";
           //echo $queryOrderIO;
           //echo strtotime("yesterday");
           $query = $database->query($queryOrderIO);
           $result = $database->fetch_array($query);
           $countIO = $result['count'];
           $valueIO = $result['value'];
           $query_install_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id != '4' and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $tomorrow . "'";
           //$query_install_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $tomorrow . "'";
           $query = $database->query($query_install_month);
           $result = $database->fetch_array($query);
           $count_install_month = $result['count'];
           $value_install_month = $result['value'];
           $query_install_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id != '4' and o.order_status_id = '3' and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $tomorrow . "'";
           //$query_install_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $tomorrow . "'";
           $query = $database->query($query_install_ytd);
           $result = $database->fetch_array($query);
           $count_install_ytd= $result['count'];
           $value_install_ytd= $result['value'];
           $query_this_month_completed = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $tomorrow . "'";
           $query = $database->query($query_this_month_completed);
           $result = $database->fetch_array($query);
           $this_month_complete_count = $result['count'];
           $this_month_complete_value = $result['value'];
           $queryOrderCC_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $tomorrow . "'";
           $query = $database->query($queryOrderCC_month);
           $result = $database->fetch_array($query);
           $countCC_month = $result['count'];
           //$valueCC = $result['value'];
           $queryOrderIO_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $tomorrow . "'";
           $query = $database->query($queryOrderIO_month);
           $result = $database->fetch_array($query);
           $countIO_month = $result['count'];
           //$valueIO = $result['value'];
           $count_all_stuff = $countIO_month+$countCC_month;
           $countCC_percentage_month = ($countCC_month * 100) / $count_all_stuff;
           ($countIO_month==0) ? $countIO_percentage_month = 0 : $countIO_percentage_month = 100-$countCC_percentage_month;
           //$countIO_percentage_month = 100-$countCC_percentage_month;
           $queryOrderCC_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $tomorrow . "'";
           $query = $database->query($queryOrderCC_year);
           $result = $database->fetch_array($query);
           $countCC_year = $result['count'];
           //$valueCC = $result['value'];
           $queryOrderIO_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $tomorrow . "'";
           $query = $database->query($queryOrderIO_year);
           $result = $database->fetch_array($query);
           $countIO_year = $result['count'];
           //$valueIO = $result['value'];
           $count_all_stuff = $countIO_year+$countCC_year;
           $countCC_percentage_year = ($countCC_year * 100) / $count_all_stuff;
           ($countIO_year==0) ? $countIO_percentage_year = 0 : $countIO_percentage_year = 100-$countCC_percentage_year;

           $previous_month_start = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp()));
           $previous_month_end = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())+1, 1, date("Y", tep_fetch_current_timestamp()));
           $previous_month_end_link = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 0, date("Y", tep_fetch_current_timestamp()));

           $query_previous_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";
           $query = $database->query($query_previous_month);
           $result = $database->fetch_array($query);
           $previous_month_count = $result['count'];
           $previous_month_value = $result['value'];


           $queryOrderCC_prev_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";

           $query = $database->query($queryOrderCC_prev_month);
           $result = $database->fetch_array($query);
           $countCC_prev_month = $result['count'];
           //$valueCC = $result['value'];

           $queryOrderIO_prev_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";

           $query = $database->query($queryOrderIO_prev_month);
           $result = $database->fetch_array($query);
           $countIO_prev_month = $result['count'];
           //$valueIO = $result['value'];

           $count_all_stuff = $countIO_prev_month+$countCC_prev_month;
           $countCC_percentage_prev_month = ($countCC_prev_month * 100) / $count_all_stuff;
           ($countIO_prev_month==0) ? $countIO_percentage_prev_month = 0 : $countIO_percentage_prev_month = 100-$countCC_percentage_prev_month;


           //////////////////////
           $query_this_month_completed = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $tomorrow . "'";
           $query = $database->query($query_this_month_completed);
           $result = $database->fetch_array($query);
           $this_month_complete_count = $result['count'];
           $this_month_complete_value = $result['value'];

           $queryOrderCC_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $tomorrow . "'";

           $query = $database->query($queryOrderCC_month);
           $result = $database->fetch_array($query);
           $countCC_month = $result['count'];
           //$valueCC = $result['value'];

           $queryOrderIO_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $tomorrow . "'";

           $query = $database->query($queryOrderIO_month);
           $result = $database->fetch_array($query);
           $countIO_month = $result['count'];
           //$valueIO = $result['value'];

           $count_all_stuff = $countIO_month+$countCC_month;
           $countCC_percentage_month = ($countCC_month * 100) / $count_all_stuff;
           ($countIO_month==0) ? $countIO_percentage_month = 0 : $countIO_percentage_month = 100-$countCC_percentage_month;
           //$countIO_percentage_month = 100-$countCC_percentage_month;

           ///////////////


           $queryOrderCC_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $tomorrow . "'";

           $query = $database->query($queryOrderCC_year);
           $result = $database->fetch_array($query);
           $countCC_year = $result['count'];
           //$valueCC = $result['value'];

           $queryOrderIO_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $tomorrow . "'";

           $query = $database->query($queryOrderIO_year);
           $result = $database->fetch_array($query);
           $countIO_year = $result['count'];
           //$valueIO = $result['value'];

           $count_all_stuff = $countIO_year+$countCC_year;
           $countCC_percentage_year = ($countCC_year * 100) / $count_all_stuff;
           ($countIO_year==0) ? $countIO_percentage_year = 0 : $countIO_percentage_year = 100-$countCC_percentage_year;
           //$countIO_percentage_month = 100-$countCC_percentage_month;



           $previous_month_start = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())-1, 1, date("Y", tep_fetch_current_timestamp()));
           $previous_month_end = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp()));

           $query_previous_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed > 0 and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";
           $query = $database->query($query_previous_month);
           $result = $database->fetch_array($query);
           $previous_month_count = $result['count'];
           $previous_month_value = $result['value'];


           $queryOrderCC_prev_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";

           $query = $database->query($queryOrderCC_prev_month);
           $result = $database->fetch_array($query);
           $countCC_prev_month = $result['count'];
           //$valueCC = $result['value'];

           $queryOrderIO_prev_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";

           $query = $database->query($queryOrderIO_prev_month);
           $result = $database->fetch_array($query);
           $countIO_prev_month = $result['count'];
           //$valueIO = $result['value'];

           $count_all_stuff = $countIO_prev_month+$countCC_prev_month;
           $countCC_percentage_prev_month = ($countCC_prev_month * 100) / $count_all_stuff;
           ($countIO_prev_month==0) ? $countIO_percentage_prev_month = 0 : $countIO_percentage_prev_month = 100-$countCC_percentage_prev_month;

           /**
            * Previouse Year
            */

            $today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())-1);
            $tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())-1);
            $month_first_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp())-1);
            $month_last_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())+1, 1, date("Y", tep_fetch_current_timestamp())-1);
            $year_first_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp())-1);
            $year_last_date = mktime(0, 0, 0, 12, 31, date("Y", tep_fetch_current_timestamp())-1);
            $this_day_last_year = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())-1);
            $query_install_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id  = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $month_last_date . "'";
            $query = $database->query($query_install_month);
            $result = $database->fetch_array($query);
            $count_install_month = $result['count'];
            $value_install_month = $result['value'];
            $queryOrderCC_prev_year_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $month_last_date . "'";
            $query = $database->query($queryOrderCC_prev_year_month);
            $result = $database->fetch_array($query);
            $countCC_prev_year_month = $result['count'];
            $queryOrderIO_prev_year_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_added > 0 and o.date_added >= '" . $month_first_date . "' and o.date_added < '" . $month_last_date . "'";
            $query = $database->query($queryOrderIO_prev_year_month);
            $result = $database->fetch_array($query);
            $countIO_prev_year_month = $result['count'];
            $count_all_stuff = $countIO_prev_year_month+$countCC_prev_year_month;
            $countCC_percentage_month = ($countCC_prev_year_month * 100) / $count_all_stuff;
            ($countIO_prev_year_month==0) ? $countIO_percentage_prev_year_month = 0 : $countIO_percentage_prev_year_month = 100-$countCC_percentage_month;
            //installs ytd
            $query_install_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id  = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $this_day_last_year . "'";
            $query = $database->query($query_install_ytd);
            $result = $database->fetch_array($query);
            $count_install_ytd= $result['count'];
            $value_install_ytd= $result['value'];
            //installs last year
            $query_install_ly = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id  = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $year_last_date . "'";
            $query = $database->query($query_install_ly);
            $result = $database->fetch_array($query);
            $count_install_ly= $result['count'];
            $value_install_ly= $result['value'];
            $queryOrderCC_prev_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $year_last_date . "'";
            $query = $database->query($queryOrderCC_prev_year);
            $result = $database->fetch_array($query);
            $countCC_prev_year = $result['count'];
            $queryOrderIO_prev_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $year_last_date . "'";
            $query = $database->query($queryOrderIO_prev_year);
            $result = $database->fetch_array($query);
            $countIO_prev_year = $result['count'];
            $count_all_stuff = $countIO_prev_year+$countCC_prev_year;
            $countCC_percentage_prev_year = ($countCC_prev_year * 100) / $count_all_stuff;
            ($countIO_prev_year==0) ? $countIO_percentage_prev_year = 0 : $countIO_percentage_prev_year = 100-$countCC_percentage_prev_year;
            $queryOrderCC_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = '1' and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $this_day_last_year . "'";
            $query = $database->query($queryOrderCC_ytd);
            $result = $database->fetch_array($query);
            $countCC_ytd = $result['count'];
            $queryOrderIO_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_added > 0 and o.date_added >= '" . $year_first_date . "' and o.date_added < '" . $this_day_last_year . "'";
            $query = $database->query($queryOrderIO_ytd);
            $result = $database->fetch_array($query);
            $countIO_ytd = $result['count'];
            $count_all_stuff_ytd = $countIO_ytd+$countCC_ytd;
            $countCC_percentage_ytd = ($countCC_ytd * 100) / $count_all_stuff_ytd;
            ($countIO_ytd==0) ? $countIO_percentage_ytd = 0 : $countIO_percentage_ytd = 100-$countCC_percentage_ytd;
            ////////////////


            /**
             * Installer information
             */

             $date_pending = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-2), date("Y", tep_fetch_current_timestamp()));
             $date_pending_1 = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())-3), date("Y", tep_fetch_current_timestamp()));
             $installer_sql = "select u.user_id, u.email_address, ud.firstname, ud.lastname,u.last_login from user_groups ug, users u , users_description ud, users_to_user_groups utug where u.user_id = ud.user_id and u.user_id = utug.user_id and u.users_status=1 and utug.user_group_id = '3' and utug.user_group_id = ug.user_group_id and (u.active_status = '1') order by ud.lastname, ud.firstname";
             $query = $database->query($installer_sql);
             $installer_info = array();
             foreach($query as $result)
             {
             $installer_pending_orders_sql = " select o.order_id, o.date_schedualed, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city, o.order_issue, ito.installer_id as itoid, itia.installer_id, ica.installation_area_id, ia.installer_id, itia.installer_id as itiaid from orders o left join addresses a on (o.address_id = a.address_id) left join installation_coverage_areas ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join installation_areas ia on (ica.installation_area_id = ia.installation_area_id) left join installers_to_installation_areas itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join installers_to_orders ito on (o.order_id = ito.order_id) left join orders_to_installer_show_order otiso on (o.order_id = otiso.order_id) left join states s on (a.state_id = s.state_id) left join countys c on (a.county_id = c.county_id), order_types ot, orders_statuses os, users u where o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.order_status_id = '1' and o.date_schedualed > 0  and o.date_schedualed < '" . $date_pending . "' and o.address_id = a.address_id and o.user_id = u.user_id and ((ito.installer_id = '$result[user_id]') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '$result[user_id]') or (ito.installer_id IS NULL and itia.installer_id = '$result[user_id]')) group by o.order_id order by o.date_schedualed ASC	";
             $installer_pending_orders_query = $database->query($installer_pending_orders_sql);
             $installer_pending_orders_count = $database->num_rows($installer_pending_orders_query);
             $installer_scheduled_orders_sql = " select o.order_id, o.date_schedualed, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city, o.order_issue, ito.installer_id as itoid, itia.installer_id, ica.installation_area_id, ia.installer_id, itia.installer_id as itiaid from orders o left join addresses a on (o.address_id = a.address_id) left join installation_coverage_areas ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join installation_areas ia on (ica.installation_area_id = ia.installation_area_id) left join installers_to_installation_areas itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join installers_to_orders ito on (o.order_id = ito.order_id) left join orders_to_installer_show_order otiso on (o.order_id = otiso.order_id) left join states s on (a.state_id = s.state_id) left join countys c on (a.county_id = c.county_id), order_types ot, orders_statuses os, users u where o.order_type_id = ot.order_type_id and o.order_status_id = os.order_status_id and o.order_status_id = '2' and o.date_schedualed > 0  and o.date_schedualed <='" . $date_pending . "' and o.address_id = a.address_id and o.user_id = u.user_id and ((ito.installer_id = '$result[user_id]') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '$result[user_id]') or (ito.installer_id IS NULL and itia.installer_id = '$result[user_id]')) group by o.order_id order by o.date_schedualed ASC	";
             $installer_scheduled_orders_query = $database->query($installer_scheduled_orders_sql);
             $installer_scheduled_orders_count = $database->num_rows($installer_scheduled_orders_query);

			

             $installer_info[] = array(
               'name' => $result['firstname'].' '.$result['lastname'],
               'last_login' => date("g:ia F j",$result['last_login']),
               'overdue_pendings' => '<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id='.$result["user_id"].'&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1).'%2F'.date("d",$date_pending_1).'%2F
               '.date("Y",$date_pending_1).'&submit_value.x=29&submit_value.y=12" class="StatLink">'.$installer_pending_orders_count.'</a>',
               'overdue_schedule' => '<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id='.$result["user_id"].'&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending).'%2F'.date("d",$date_pending).'%2F
               '.date("Y",$date_pending).'&submit_value.x=16&submit_value.y=17" class="StatLink">'.$installer_scheduled_orders_count.'</a>'
             );
/*
             $installer_info .= '<tr>
                                 <td>'.$result['firstname'].' '.$result['lastname'].'</td>
                                 <td>'.date("g:ia F j",$result['last_login']).'</td>
                                 <td><a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id='.$result["user_id"].'&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1).'%2F'.date("d",$date_pending_1).'%2F
                                 '.date("Y",$date_pending_1).'&submit_value.x=29&submit_value.y=12" class="StatLink">'.$installer_pending_orders_count.'</a></td>
                                 <td><a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id='.$result["user_id"].'&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending).'%2F'.date("d",$date_pending).'%2F
                                 '.date("Y",$date_pending).'&submit_value.x=16&submit_value.y=17" class="StatLink">'.$installer_scheduled_orders_count.'</a></td>
                               </tr>';
*/

        }

        $vars = array(
          'alerts' => array(
            'inventory_criticals' => $inventory_criticals,
            '$inventory_warnings' => $inventory_warnings
          ),
          'issues' => array(
            'unsigned' => '<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=unassigned&active=1&show_between_type=scheduled&show_between_start=&show_between_end=&submit_value.x=25&submit_value.y=8" class="StatLink">'.tep_count_unassigned_orders().'</a>',
            'red_flag' => '<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&red_flagged=1&active=1&show_between_type=scheduled&show_between_start=&show_between_end=&submit_value.x=32&submit_value.y=11" class="StatLink">'.$red_flag_orders.'</a>',
            'on_hold' => '<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=5&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end=&submit_value.x=39&submit_value.y=8" class="StatLink">'.$on_hold_orders.'</a>'
          ),
          'miss_utilities' => array(
            'open' => '<a href="'.FILENAME_ADMIN_ORDERS.'?miss_utility_open=1&order_status=" class="StatLink">'.$miss_utility_open.'</a>',
            'called' => '<a href="'.FILENAME_ADMIN_ORDERS.'?miss_utility_called=1&order_status=" class="StatLink">'.$miss_utility_called.'</a>',
            'completed' => '<a href="'.FILENAME_ADMIN_ORDERS.'?miss_utility_completed=1&order_status=" class="StatLink">'.$miss_utility_completed.'</a>',
            'percentage' => round($percentage, 2)
          ),
          'order_today' => array(
            'installs' => 'Installs ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">
                '.$count_install_new_db.'
            </a> )',
            'removals' => 'Removals ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">
                '.$count_install_new_db.'
            </a> )',
            'service_calls' => 'Service Calls ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=&order_type=2&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">
                '.$count_install_new_db.'
            </a> )'
          ),
          'rescheduled_today' => array(
            'removals' => 'Removals Rescheduled ( '.$rescheduled_count. ')',
            'pushed_back' => 'Pushed Back ( '.$pushed_back_count.' )',
            'moved_up' =>   'Moved Up ( '.$moved_up_count.' )'
          ),
          'post_total_change' => array(
            'yesterday' => $postTotalChange,
            'last_week' => $postTotalChangeLastWeek
          ),
          'overdue_orders' => array(
            'pending' => array(
              'date' => 'Pending (Ordered before <b>'.date("M j, y",$date_pending_overdue).'</b>) :',
              'count' => '<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1).'%2F'.date("d",$date_pending_1).'%2F'.date("Y",$date_pending_1).'&submit_value.x=29&submit_value.y=12" class="StatLink"> '.$pending_orders.'</a>'
            ),
            'scheduled' => array(
              'date' => 'Scheduled (Since <b>'.date("M j, y",$date_pending_overdue).'</b> or before): ',
              'count' => '<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending).'%2F'.date("d",$date_pending).'%2F'.date("Y",$date_pending).'&submit_value.x=16&submit_value.y=17" class="StatLink"> '.$scheduled_orders.'</a>'
            )
          ),
          'current_active_orders' => array(
            'pending' => array(
              'date' => 'Pending (before today + 2 ie <b>'.date("M j, y",$date_pending_current_active_pending).'</b>): ',
              'installs_count' => 'Installs (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1_current_active_pending).'%2F'.date("d",$date_pending_1_current_active_pending).'%2F'.date("Y",$date_pending_1_current_active_pending).'&submit_value.x=20&submit_value.y=10" class="StatLink">'.$pending_orders_install.'</a>)',
              'removals_count' => 'Removals (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1_current_active_pending).'%2F'.date("d",$date_pending_1_current_active_pending).'%2F'.date("Y",$date_pending_1_current_active_pending).'&submit_value.x=23&submit_value.y=4" class="StatLink">'.$pending_orders_removal.'</a>)',
              'service_calls_count' => 'Service Calls (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=2&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1_current_active_pending).'%2F'.date("d",$date_pending_1_current_active_pending).'%2F '.date("Y",$date_pending_1_current_active_pending).'&submit_value.x=24&submit_value.y=9" class="StatLink">'.$pending_orders_svc.'</a>)',
              'total' => 'Total (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1_current_active_pending).'%2F'.date("d",$date_pending_1_current_active_pending).'%2F'.date("Y",$date_pending_1).'&submit_value.x=14&submit_value.y=13" class="StatLink">'.($pending_orders_svc + $pending_orders_removal + $pending_orders_install).'</a>)'
            ),
            'scheduled' => array(
              'date' => 'Schedule (before today + 1 ie <b>'.date("M j, y",$date_pending_current_active_scheduled).'</b>): ',
              'installs_count' => 'Installs (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1_current_active_scheduled).'%2F'.date("d",$date_pending_1_current_active_scheduled).'%2F'.date("Y",$date_pending_1_current_active_scheduled).'&submit_value.x=47&submit_value.y=15" class="StatLink">'.$schedule_orders_install.'</a>)',
              'removals_count' => 'Removals (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1_current_active_scheduled).'%2F'.date("d",$date_pending_1_current_active_scheduled).'%2F'.date("Y",$date_pending_1_current_active_scheduled).'&submit_value.x=7&submit_value.y=9" class="StatLink">'.$schedule_orders_removal.'</a>)',
              'service_calls_count' => 'Service Calls (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=2&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1).'%2F'.date("d",$date_pending_1).'%2F '.date("Y",$date_pending_1).'&submit_value.x=27&submit_value.y=8" class="StatLink">'.$schedule_orders_svc.'</a>)',
              'total' => 'Total (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end='.date("m",$date_pending_1_current_active_scheduled).'%2F'.date("d",$date_pending_1_current_active_scheduled).'%2F'.date("Y",$date_pending_1_current_active_scheduled).'&submit_value.x=8&submit_value.y=7" class="StatLink">'.($schedule_orders_svc + $schedule_orders_removal + $schedule_orders_install).'</a>)'
            )
          ),
          'completed_orders' => array(
            'yesterday' => array(
              'installs_count' => 'Installs (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$date_start_yesterday).'%2F'.date("d",$date_start_yesterday).'%2F'.date("Y",$date_start_yesterday).'&show_between_end='.date("m",$date_start_yesterday).'%2F'.date("d",$date_start_yesterday).'%2F
              '.date("Y",$date_start_yesterday).'&submit_value.x=29&submit_value.y=14" class="StatLink">'.$complete_orders_install.'</a>)',
              'removals_count' => 'Removals (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$date_start_yesterday).'%2F'.date("d",$date_start_yesterday).'%2F'.date("Y",$date_start_yesterday).'&show_between_end='.date("m",$date_start_yesterday).'%2F'.date("d",$date_start_yesterday).'%2F
              '.date("Y",$date_start_yesterday).'&submit_value.x=12&submit_value.y=9" class="StatLink">'.$complete_orders_removal.'</a>)',
              'service_calls_count' => 'Service Calls (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=2&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$date_start_yesterday).'%2F'.date("d",$date_start_yesterday).'%2F
              '.date("Y",$date_start_yesterday).'&show_between_end='.date("m",$date_start_yesterday).'%2F '.date("d",$date_start_yesterday).'%2F'.date("Y",$date_start_yesterday).'&submit_value.x=17&submit_value.y=12" class="StatLink">'.$complete_orders_svc.'</a>)',
              'total' => 'Total (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$date_start).'%2F'.date("d",$date_start).'%2F'.date("Y",$date_start).'&show_between_end='.date("m",$date_start).'%2F
              '.date("d",$date_start_yesterday).'%2F '.date("Y",$date_start_yesterday).'&submit_value.x=35&submit_value.y=11" class="StatLink">'.($complete_orders_svc + $complete_orders_removal + $complete_orders_install).'</a>)'
            ),
            'today' => array(
              'installs_count' => 'Installs (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$date_start_today).'%2F'.date("d",$date_start_today).'%2F'.date("Y",$date_start_today).'&show_between_end='.date("m",$date_start_today).'%2F
              '.date("d",$date_start_today).'%2F'.date("Y",$date_start_today).'&submit_value.x=29&submit_value.y=14" class="StatLink">'.$complete_orders_install.'</a>)',
              'removals_count' => 'Removals (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$date_start_today).'%2F'.date("d",$date_start_today).'%2F'.date("Y",$date_start_today).'&show_between_end='.date("m",$date_start_today).'%2F
              '.date("d",$date_start_today).'%2F'.date("Y",$date_start_today).'&submit_value.x=12&submit_value.y=9" class="StatLink">'.$complete_orders_removal.'</a>)',
              'service_calls_count' => 'Service Calls (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=2&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$date_start_today).'%2F'.date("d",$date_start_today).'%2F'.date("Y",$date_start_today).'&show_between_end='.date("m",$date_start_today).'%2F
              '.date("d",$date_start_today).'%2F'.date("Y",$date_start_today).'&submit_value.x=17&submit_value.y=12" class="StatLink">'.$complete_orders_svc.'</a>)',
              'total' => 'Total (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$date_start_today).'%2F'.date("d",$date_start_today).'%2F'.date("Y",$date_start_today).'&show_between_end='.date("m",$date_start_today).'%2F
              '.date("d",$date_start_today).'%2F'.date("Y",$date_start_today).'&submit_value.x=35&submit_value.y=11" class="StatLink">'.($complete_orders_svc + $complete_orders_removal + $complete_orders_install).'</a>)'
            )
          ),
          'future_orders' => array(
            'pending' => array(
              'date' => 'Pending (after today + 2 ie <b>'.date("M j, y",$date_pending_future_orders_3).'</b>): ',
              'installs_count' => 'Installs (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start='.date("m",$date_pending_future_orders_3).'%2F'.date("d",$date_pending_future_orders_3).'%2F'.date("Y",$date_pending_future_orders_3).'&show_between_end=&submit_value.x=20&submit_value.y=10" class="StatLink">'.$pending_orders_install.'</a>)',
              'removals_count' => 'Removals (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start='.date("m",$date_pending_future_orders_3).'%2F'.date("d",$date_pending_future_orders_3).'%2F'.date("Y",$date_pending_future_orders_3).'&show_between_end=&submit_value.x=23&submit_value.y=4" class="StatLink">'.$pending_orders_removal.'</a>)',
              'service_calls_count' => 'Service Calls (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=2&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start='.date("m",$date_pending_future_orders_3).'%2F'.date("d",$date_pending_future_orders_3).'%2F
              '.date("Y",$date_pending_future_orders_3).'&show_between_end=&submit_value.x=24&submit_value.y=9" class="StatLink">'.$pending_orders_svc.'</a>)',
              'total' => 'Total (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start='.date("m",$date_pending_future_orders_3).'%2F'.date("d",$date_pending_future_orders_3).'%2F'.date("Y",$date_pending_future_orders_3).'&show_between_end=&submit_value.x=14&submit_value.y=13" class="StatLink">
              '.($pending_orders_svc + $pending_orders_removal + $pending_orders_install).'</a>)'
            ),
            'scheduled' => array(
              'date' => 'Schedule (after today + 1 ie <b>'.date("M j, y",$date_pending_future_orders_2).'</b>): ',
              'installs_count' => 'Installs (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start='.date("m",$date_pending_future_orders_2).'%2F'.date("d",$date_pending_future_orders_2).'%2F'.date("Y",$date_pending_future_orders_2).'&show_between_end=&submit_value.x=47&submit_value.y=15" class="StatLink">'.$schedule_orders_install.'</a>)',
              'removals_count' => 'Removals (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start='.date("m",$date_pending_future_orders_2).'%2F'.date("d",$date_pending_future_orders_2).'%2F'.date("Y",$date_pending_future_orders_2).'&show_between_end=&submit_value.x=7&submit_value.y=9" class="StatLink">'.$schedule_orders_removal.'</a>)',
              'service_calss_count' => 'Service Calls (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=2&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start='.date("m",$date_pending_future_orders_2).'%2F'.date("d",$date_pending_future_orders_2).'%2F
              '.date("Y",$date_pending_future_orders_2).'&show_between_end=&submit_value.x=27&submit_value.y=8" class="StatLink">'.$schedule_orders_svc.'</a>)',
              'total' => 'Total (<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start='.date("m",$date_pending_future_orders_2).'%2F'.date("d",$date_pending_future_orders_2).'%2F'.date("Y",$date_pending_future_orders_2).'&show_between_end=&submit_value.x=8&submit_value.y=7" class="StatLink">
              '.($schedule_orders_svc + $schedule_orders_removal + $schedule_orders_install).'</a>)'
            )
          ),
          'agents_to_be_made_inactive' => '<a href="/admin_users.php?page_action=inactive">'.$count_inactives.'</a>',
          'agencies_to_be_made_inactive' => '<a href="/admin_agencys.php?page_action=inactive">'.$inactive_agencies.'</a>',
          'post_in_the_field' => "$ffx_posts_in_field, $md_posts_in_field, $pa_posts_in_field, $posts_total",
          'total_operational_posts' => $posts_operational,
          'pending_scheduled_removals' => '<a href="'.FILENAME_ADMIN_ORDERS.'?order_status=1&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end=&submit_value.x=14&submit_value.y=13" class="StatLink">'.$pending_removal.'</a>&nbsp;/&nbsp;<a href=" '.FILENAME_ADMIN_ORDERS.'?order_status=2&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end=&submit_value.x=14&submit_value.y=13" class="StatLink">'.$scheduled_removal.'</a>',
          'new_active_agencies' => '<a href="'.FILENAME_ADMIN_NEW_AGENCYS.'" class="StatLink">'.$new_agencies_count.'</a>&nbsp;/&nbsp;<a href="'.FILENAME_ADMIN_AGENCYS.'" class="StatLink">'.$active_agencies_count.'</a>',
          'money_statistics' => array(
            'deffered_biling_total_7' => array(
              'billed' => '<a style="color: #1e90ff; cursor: pointer;" onclick="showDeferredTotal({type: \'seven\', showBy: \'Total Billed\'})">$'.$countDeferred7.'<a/>',
              'paid' => '<a style="color: #1e90ff; cursor: pointer;" onclick="showDeferredTotal({type: \'seven\', showBy: \'Total Paid\'})">$'.$countTotalPaid7.'<a/>',
              'unpaid' => '%'.$percentUnpaid7
            ),
            'deffered_biling_total_current_month' => array(
              'billed' => '<a style="color: #1e90ff; cursor: pointer;" onclick="showDeferredTotal({type: \'current_month\', showBy: \'Total Billed\'})">$'.$countDeferredCurrentMonth.'<a/>',
              'paid' => '<a style="color: #1e90ff; cursor: pointer;" onclick="showDeferredTotal({type: \'current_month\', showBy: \'Total Paid\'})">$'.$countTotalPaidCurrentMonth.'<a/>',
              'unpaid' => '%'.$percentUnpaidCurrentMonth
            ),
            'deffered_biling_total_previous_month' => array(
              'billed' => '<a style="color: #1e90ff; cursor: pointer;" onclick="showDeferredTotal({type: \'prev_month\', showBy: \'Total Billed\'})">$'.$countDeferredPrevMonth.'<a/>',
              'paid' => '<a style="color: #1e90ff; cursor: pointer;" onclick="showDeferredTotal({type: \'prev_month\', showBy: \'Total Paid\'})">$'.$countTotalPaidPrevMonth.'<a/>',
              'unpaid' => '%'.$percentUnpaidPrevMonth 
            ),
            'agents_with_credit_balance' => array(
              'count' => '<a style="color: #1e90ff; cursor: pointer;" onclick="showAgentsWithCreditBalance()">'.$totalCountOfAgentsWithCredit.'<a/>',
              'amount' => $valueCreditBalance
            )
          ),
          'current_year' => array(
            'today' => array(
              'number_of_installs' => '# of Installs: ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start='.date("m",$today).'%2F'.date("d",$today).'%2F'.date("Y",$today).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F
              '.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_today.'</a>',
              'value_of_orders_placed' => '$ value of orders placed today: ( '.number_format ($value_install_today,2) .' )',
              'value_number_of_installs' => '$ value / # of installs: ( '. ($count_install_today>0 ? number_format(($value_install_today/$count_install_today),2) : "0.00") .')',
              'amount_of_cc_orders' => '$ of CC Orders: ( '.number_format($valueCC,2).' )',
              'value_of_invoice_orders' => '$ of Invoice Orders: ( '.number_format($valueIO,2).' )'
            ),
            'month' => array(
              'placed' => array(
                'number_of_installs' => '# of Installs: ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start='.date("m",$month_first_date).'%2F'.date("d",$month_first_date).'%2F'.date("Y",$month_first_date).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F
                '.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_month.'</a> )',
                'value_of_orders_placed' => '$ value of orders placed this month: ( '.number_format($value_install_month,2).' )',
                'value_number_of_installs' => '$ value / # of installs: ( '.($count_install_month>0 ? number_format(($value_install_month/$count_install_month),2) : "0.00").' )'
              ),
              'completed' => array(
                'number_of_installs' => '# of Installs: ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$month_first_date).'%2F'.date("d",$month_first_date).'%2F<'.date("Y",$month_first_date).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F
                '.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$this_month_complete_count.'</a> )',
                'value_of_orders_placed' => '$ value of orders completed this month: ( '.number_format($this_month_complete_value,2).' )',
                'value_number_of_installs' => '$ value / # of installs: ( '.($this_month_complete_count>0 ? number_format(($this_month_complete_value/$this_month_complete_count),2) : "0.00").' )',
                'amount_of_cc_orders' => '$ of CC Orders: ( '.number_format($countCC_percentage_month,2).' )',
                'value_of_invoice_orders' => '$ of Invoice Orders: ( '.number_format($countIO_percentage_month,2).'% )'
              )
            ),
            'previous_month' => array(
              'completed' => array(
                'number_of_installs' => '# of Installs: ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$previous_month_start).'%2F'.date("d",$previous_month_start).'%2F'.date("Y",$previous_month_start).'&show_between_end='.date("m",$previous_month_end_link).'%2F
                '.date("d",$previous_month_end_link).'%2F'.date("Y",$previous_month_end_link).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$previous_month_count.'</a> )',
                'value_of_orders_placed' => '$ value of orders completed previous month: ( '.number_format($previous_month_value,2).' )',
                'value_number_of_installs' => '$ value / # of installs: ( '. ($previous_month_count>0 ? number_format(($previous_month_value/$previous_month_count),2) : "0.00").' )',
                'amount_of_cc_orders' => '$ of CC Orders: ( '.number_format($countCC_percentage_prev_month,2).' )',
                'value_of_invoice_orders' => '$ of Invoice Orders: ( '.number_format($countIO_percentage_prev_month,2).'% )'
              )
            ),
            'ytd' => array(
              'completed' => array(
                'number_of_installs' => '# of Installs: ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$year_first_date).'%2F'.date("d",$year_first_date).'%2F'.date("Y",$year_first_date).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F
                '.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_ytd.'</a> )',
                'value_of_orders_placed' => '$ value of orders completed from Jan 1: ( <a href="'.FILENAME_ADMIN_ORDERS.'?group_by=date&order_status=3&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$year_first_date).'%2F'.date("d",$year_first_date).'%2F'.date("Y",$year_first_date).'&show_between_end='.date("m",$today).'%2F'.date("d",$today).'%2F
                '.date("Y",$today).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.number_format($value_install_ytd,2).'</a> )',
                'value_number_of_installs' => '$ value / # of installs: ( '. ($count_install_ytd>0 ? number_format(($value_install_ytd/$count_install_ytd),2) : "0.00").' )',
                'amount_of_cc_orders' => '% of CC Orders: ( '.number_format ($countCC_percentage_year,2).' )',
                'value_of_invoice_orders' => '% of Invoice Orders: ( '.number_format ($countIO_percentage_year,2).'% )'
              )
            )
          ),
          'previous_year' => array(
            'month' => array(
              'completed' => array(
                'number_of_installs' => '# of Installs: ( <a href='.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$month_first_date).'%2F'.date("d",$month_first_date).'%2F'.date("Y",$month_first_date).'&show_between_end='.date("m",$month_last_date).'%2F'.date("d",$month_last_date).'%2F
                '.date("Y",$month_last_date).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_month.'</a> )',
                'value_of_orders_placed' => '$ value of orders placed this month: ( '.number_format($value_install_month,2).' )',
                'value_number_of_installs' => '$ value / # of installs: ( '. ($count_install_month>0 ? number_format(($value_install_month/$count_install_month),2) : "0.00").' )',
                'amount_of_cc_orders' => '% of CC Orders: ( '. number_format ($countCC_percentage_month,2) .' )',
                'value_of_invoice_orders' => '% of Invoice Orders: ('.number_format ($countIO_percentage_prev_year_month,2).'% )'
              )
            ),
            'ytd' => array(
              'number_of_installs' => '# of Installs: ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$year_first_date).'%2F'.date("d",$year_first_date).'%2F'.date("Y",$year_first_date).'&show_between_end='.date("m",$this_day_last_year).'%2F'.date("d",$this_day_last_year).'%2F
              '.date("Y",$this_day_last_year).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_ytd.'</a> )',
              'value_of_orders_placed' => '$ value of orders placed from Jan 1: ( '.number_format($value_install_ytd,2).' )',
              'value_number_of_installs' => '$ value / # of installs: ( '. ($count_install_ytd>0 ? number_format(($value_install_ytd/$count_install_ytd),2) : "0.00") .' )',
              'amount_of_cc_orders' => '% of CC Orders: ( '.number_format ($countCC_percentage_ytd,2).'% )',
              'value_of_invoice_orders' => '% of Invoice Orders: ( '.number_format ($countIO_percentage_ytd,2).'% )'
            ),
            'full_year' => array(
              'number_of_installs' => '# of Installs: ( <a href="'.FILENAME_ADMIN_ORDERS.'?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start='.date("m",$year_first_date).'%2F'.date("d",$year_first_date).'%2F'.date("Y",$year_first_date).'&show_between_end='.date("m",$year_last_date).'%2F'.date("d",$year_last_date).'%2F
              '.date("Y",$year_last_date).'&submit_value.x=41&submit_value.y=12" class="StatLink">'.$count_install_ly.'</a>)',
              'value_of_orders_placed' => '$ value of orders placed from Jan 1: ( '.number_format($value_install_ly,2).' )',
              'value_number_of_installs' => '$ value / # of installs: ( '.($count_install_ly>0 ? number_format(($value_install_ly/$count_install_ly),2) : "0.00").' )',
              'amount_of_cc_orders' => '% of CC Orders: ( '.number_format ($countCC_percentage_prev_year,2).'% )',
              'value_of_invoice_orders' => '% of Invoice Orders: ( '.number_format ($countIO_percentage_prev_year,2).'% )'
            )
          ),
          'installer_information' => $installer_info
        );

        echo $twig->render('admin/account_overview.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
      break;

    case '3':
	
    //Installer.

    $midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));

    $midnight_future = ($midnight_tonight + ((60*60*24) * 1));



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '1' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $current_pend_installs = $result['count'];



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '2' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $current_pend_service = $result['count'];



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '3' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $current_pend_removal = $result['count'];



    $current_pend_total = ($current_pend_installs + $current_pend_service + $current_pend_removal);





    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.order_type_id = '1' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $current_sched_installs = $result['count'];



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.order_type_id = '2' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $current_sched_service = $result['count'];



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.order_type_id = '3' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $current_sched_removal = $result['count'];



    $current_sched_total = ($current_sched_installs + $current_sched_service + $current_sched_removal);





    /*$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id = '2' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $current_sched_installs = $result['count'];



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and o.order_status_id < '3' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $current_installs = $result['count'];*/





    $midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp()));

    $midnight_future = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+2), date("Y", tep_fetch_current_timestamp()));



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed > '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '1' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $future_pend_installs = $result['count'];



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed > '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '2' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $future_pend_service = $result['count'];



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) where o.date_schedualed > '" . $midnight_future . "' and o.order_status_id = '1' and o.order_type_id = '3' and o.address_id = a.address_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $future_pend_removal = $result['count'];



    $future_pend_total = ($future_pend_installs + $future_pend_service + $future_pend_removal);



    /*$query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed > '" . $midnight_tonight . "' and o.order_status_id = '1' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $future_pend_installs = $result['count'];



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed > '" . $midnight_tonight . "' and o.order_status_id = '2' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $future_sched_installs = $result['count'];



    $query = $database->query("select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ORDER_TYPES . " ot, " . TABLE_ADDRESSES . " a, " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica, " . TABLE_INSTALLATION_AREAS . " ia left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering >= o.date_schedualed and itia.date_end_covering <= o.date_schedualed))  left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDERS_STATUSES . " os left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed > '" . $midnight_tonight . "' and o.order_status_id < '3' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id  and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ia.installation_area_id = ica.installation_area_id and ((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end)) and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "'))");

    $result = $database->fetch_array($query);

    $future_installs = $result['count'];*/


    $vars = array(
        'currentPendingTotal' => $current_pend_total,
        'currentPendingInstalls' => $current_pend_installs,
        'currentPendingService' => $current_pend_service,
        'currentPendingRemoval' => $current_pend_removal,
        'currentScheduleTotal' => $current_sched_total,
        'currentScheduleInstalls' => $current_sched_installs,
        'currentScheduleService' => $current_sched_service,
        'currentScheduleRemoval' => $current_sched_removal,
        'currentTotalOrders' => ($current_pend_total+$current_sched_total),
        'futurePendingTotal' => $future_pend_total,
        'futurePendingInstalls' => $future_pend_installs,
        'futurePendingService' => $future_pend_service,
        'futurePendingRemoval' => $future_pend_removal,
        'futurePenginRemoval' => $future_pend_removal,

    );


    echo $twig->render('installer/account_overview.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));

        break;

    case '5':

        //Accounts Payable.

        $mcenearney_corp_user_id = 6143;

        if ($user->user_id == $mcenearney_corp_user_id) {
            echo "<tr>\n";
            echo "<td colspan=\"3\" width=\"100%\">\n";
            $months = array();
            $years = array();

            for ($i = 1; $i <= 12; $i++) {
                $months[] = array('id' => $i, 'name' => date('F', strtotime("2000-$i-01")));
            }

            for ($i = 0; $i < 10; $i++) {
                $years[] = array('id' => date('Y') - $i, 'name' => date('Y') - $i);
            }

            echo "<div style='padding: 20px'>\n";
            echo "<form action='/lib/invoices/invoice_mcenearney.php5' method='get'>\n";
            echo "McEnearney Export: ";
            echo tep_generate_pulldown_menu("month", $months, (int) date('n', strtotime("1 month ago")));
            echo "&nbsp;&nbsp;&nbsp;";
            echo tep_generate_pulldown_menu("year", $years, (int) date('Y', strtotime("1 month ago")));
            echo "&nbsp;&nbsp;&nbsp;";
            echo tep_create_button_submit("export", "Export");
            echo "</form>\n";
            echo "<br>\n";
            echo "</div>\n";
            echo "</td>\n";
            echo "</tr>\n";
        }

        break;
}
?>
</table>
