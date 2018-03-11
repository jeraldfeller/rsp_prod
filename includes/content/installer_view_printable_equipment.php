<?php
$this->change_template_file('print.tpl');	
$day_view = tep_fill_variable('day_view', 'get', 'today');
?>
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td class="main">&PAGE_TEXT</td>
    </tr>
    <tr>
        <td><img src="images/pixel_trans.gif" height="10" width="1" /></td>
    </tr>
    <tr>
        <td><hr /></td>
    </tr>
    <tr>
        <td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
    </tr>

<?php
$where = '';
//Here we work out if it is today or tomorrow and change the where to match.
if ($day_view == 'tomorrow') {
    $midnight_tonight = mktime(0, 0, 0, date("n"), (date("d")+1), date("Y"));

    //Check if tomorrow was a sunday, if so then extend that date.
    if (date("w", ($midnight_tonight+1)) == 0) {
        $midnight_tonight += (60*60*24);
    }
    //Now get the next day and work out if it is a sunday, if so then extend the date.
    //$midnight_tonight += (60*60*24);
    //if (date("w", ($midnight_tonight+1)) == 0) {
    //$midnight_tonight += (60*60*24);
    //}

    $midnight_future = ($midnight_tonight + ((60*60*24) * 1));

    $midnight_tonight = 0;
	$limit = false;
	$reverse = false;
} elseif ($day_view == 'tomorrow1') {
    $midnight_tonight = mktime(0, 0, 0, date("n"), (date("d")+1), date("Y"));

    //Check if tomorrow was a sunday, if so then extend that date.
    if (date("w", ($midnight_tonight+1)) == 0) {
        $midnight_tonight += (60*60*24);
    }
    //Now get the next day and work out if it is a sunday, if so then extend the date.
    $midnight_tonight += ((60*60*24));
    if (date("w", ($midnight_tonight+1)) == 0) {
        $midnight_tonight += (60*60*24);
    }
	$limit = true;
	$reverse = false;

    $midnight_future = ($midnight_tonight + ((60*60*24) * 1));

   // $midnight_tonight = 0;
} elseif ($day_view == 'future') {

	#echo 'hello - 1';
   /* $midnight_tonight = mktime(0, 0, 0, date("n"), (date("d")+1), date("Y"));

    //Check if tomorrow was a sunday, if so then extend that date.
    if (date("w", ($midnight_tonight+1)) == 0) {
        $midnight_tonight += (60*60*24);
    }
    //Now get the next day and work out if it is a sunday, if so then extend the date.
    $midnight_tonight += ((60*60*24));
    if (date("w", ($midnight_tonight+1)) == 0) {
        $midnight_tonight += (60*60*24);
    }*/
	$limit = false;

    #$midnight_tonight = mktime(0, 0, 0, date("n"), date("d"), date("Y"));
	$midnight_tonight = mktime(23, 59, 59, date("n"), date("d"), date("Y"));
	$midnight_future = mktime(0, 0, 0, date("n"), (date("d")+7), date("Y"));
	
#	echo "select o.order_id, o.date_schedualed, o.order_type_id, a.number_of_posts, a.address_id, ot.name as order_type_name, a.house_number, a.street_name, a.city, a.zip4  from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDER_TYPES . " ot where o.order_type_id = ot.order_type_id and o.date_schedualed >= '" . $midnight_tonight . "' and o.date_schedualed <='".$midnight_future."' and o.address_id = a.address_id and  ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by o.date_schedualed ASC";
	
	#$query = $database->query("select o.order_id, o.date_schedualed, o.order_type_id, a.number_of_posts, a.address_id, ot.name as order_type_name, a.house_number, a.street_name, a.city, a.zip4  from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDER_TYPES . " ot where o.order_type_id = ot.order_type_id and o.date_schedualed >= '" . $midnight_future . "' and o.address_id = a.address_id and  ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by o.date_schedualed ASC");
	
$query = $database->query("select o.order_id, o.date_schedualed, o.order_type_id, a.number_of_posts, a.address_id, ot.name as order_type_name, a.house_number, a.street_name, a.city, a.zip4  from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id), " . TABLE_ORDER_TYPES . " ot where o.order_type_id = ot.order_type_id and o.date_schedualed >= '" . $midnight_tonight . "' and o.date_schedualed <='".$midnight_future."' and o.address_id = a.address_id and  ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL  and ia.installation_area_id = ica.installation_area_id and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by o.date_schedualed ASC");	
	
	$reverse = true;
	$future = true;
   // $midnight_tonight = 0;
} else {
    $midnight_tonight = mktime(0, 0, 0, date("n"), date("d"), date("Y"));
    $midnight_future = ($midnight_tonight + ((60*60*24) * 1));

    //if (date("w", ($midnight_tonight+1)) == 0) {
    //	$midnight_tonight += (60*60*24);
    //	$midnight_future += (60*60*24);
    //}
    $midnight_tonight = 0;
	$limit = false;
	$reverse = false;
}

//We only want the orders for the specifed day.
?>			
    <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <!--<td class="main"><b>Jobs for <?php # if(isset($future)) {echo 'Future';} else { echo date("l dS \of F Y", ($midnight_future-1)); } ?></b></td>-->
    <td class="main"><b>Jobs for Next 7 Days</b></td>
    </tr>
	<? if(!isset($future)) { ?>
    <tr>
    <td class="main">
    <table width="100%" cellspacing="2" cellpadding="2">
    <tr>
    <td class="main">Installations: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '1', '2', '', $limit, $reverse); ?></td>
    <td class="main">Service Calls: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '2', '2', '', $limit, $reverse); ?></td>
    <td class="main">Removals: <?php echo tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '3', '2', '', $limit, $reverse); ?></td>
    </tr>
    </table>
    </td>
    </tr>
	<? } ?>
    <tr>
    <td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
    </tr>
    </table>

<?php
$warehouses = array();

$posts = 0;
$equipment_groups = array();
$equipment_items = array();

$return_posts = 0;
$return_equipment_groups = array();
$return_equipment_items = array();
if(!isset($query)) {
	$query = $database->query("select o.order_id, o.address_id, o.date_schedualed, o.date_added, o.user_id, os.order_status_name, o.order_type_id, ot.name as order_type_name, a.zip4, otiso.show_order_id, a.house_number, a.street_name, a.cross_street_directions, a.number_of_posts, a.city, a.address_post_allowed, a.zip, a.post_type_id, a.adc_number, s.name as state_name, c.name as county_name, sld.name as service_level_name, od.special_instructions, od.admin_comments, otiso.show_order_id as order_column from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where " . ((!empty($midnight_tonight)) ? "o.date_schedualed >= '" . $midnight_tonight . "' and " : '') . "o.date_schedualed < '" . $midnight_future . "' " . ((empty($midnight_tonight)) ? " and o.order_status_id < '3' " : '') . " and o.order_status_id = '2' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by order_column");
}


while($result = $database->fetch_array($query)) {
    if ($result['order_type_id'] == 1) {
        $posts += $result['number_of_posts'];
    } elseif ($result['order_type_id'] == 3) {
        $return_posts += $result['number_of_posts'];
    }
    if (($result['order_type_id'] == 1) || ($result['order_type_id'] == 2)) {
        //Now get equipment.
        $equipment = tep_get_equipment_assigned_to_order($result['order_id']);

        foreach($equipment as $group_id => $data) {
            if (!isset($equipment_groups[$group_id])) {
                $equipment_groups[$group_id] = $data['name'];
            }
            if (!isset($return_equipment_groups[$group_id])) {
                $return_equipment_groups[$group_id] = $data['name'];
            }
            $count = count($data['items']);
            $n = 0;
            while($n < $count) {
                if ($data['items'][$n]['method_id'] == '1') {
                    if (!isset($equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']])) {
                        $equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']] = array('name' => $data['items'][$n]['name'], 'count' => '1', 'ref_codes' => $data['items'][$n]['reference_code'], 'location' => $data['items'][$n]['location']);
                    } else {
                        $equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']]['count'] += 1;
                        if (!empty($data['items'][$n]['reference_code'])) {
                            $equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']]['ref_codes'] .= ', ' .$data['items'][$n]['reference_code'];
                        }
                    }
                } else {
                    if (!isset($return_equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']])) {
                        $return_equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']] = array('name' => $data['items'][$n]['name'], 'count' => '1', 'ref_codes' => $data['items'][$n]['reference_code'], 'location' => $data['items'][$n]['location']);
                    } else {
                        $return_equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']]['count'] += 1;
                        if (!empty($data['items'][$n]['reference_code'])) {
                            $equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']]['ref_codes'] .= ', ' .$data['items'][$n]['reference_code'];
                        }
                    }
                }
                if (!in_array($data['items'][$n]['warehouse_id'], $warehouses)) {
                    $warehouses[] = $data['items'][$n]['warehouse_id'];
                }
                $n++;
            }
        }
    } elseif ($result['order_type_id'] == 3) {
        //Now get equipment.
        $equipment = tep_get_equipment_assigned_to_address($result['address_id']);

        foreach($equipment as $group_id => $data) {

            if (!isset($return_equipment_groups[$group_id])) {
                $return_equipment_groups[$group_id] = $data['name'];
            }
            $count = count($data['items']);
            $n = 0;
            while($n < $count) {

                if (!isset($return_equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']])) {
                    $return_equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']] = array('name' => $data['items'][$n]['name'], 'count' => '1', 'ref_codes' => $data['items'][$n]['reference_code'], 'location' => $data['items'][$n]['location']);
                } else {
                    $return_equipment_items[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']]['count'] += 1;
                    if (!empty($data['items'][$n]['reference_code'])) {
                        $return_equipment_groups[$data['items'][$n]['warehouse_id']][$group_id][$data['items'][$n]['id']]['ref_codes'] .= ', ' .$data['items'][$n]['reference_code'];
                    }
                }

                if (!in_array($data['items'][$n]['warehouse_id'], $warehouses)) {
                    $warehouses[] = $data['items'][$n]['warehouse_id'];
                }
                $n++;
            }
        }
    }
}
$loop= 0;
//Now go through based on warehouse.
$w_count = count($warehouses);

$w_n = 0;
$return_posts_array = array();
if (isset($return_equipment_items)) {
    reset($return_equipment_items);
    foreach($return_equipment_items as $warehouse => $items) {
        if (isset($items[1])) {
            reset($items[1]);
            foreach($items[1] as $id => $details) {
                if (!isset($return_posts_array[$id])) {
                    $return_posts_array[$id] = array('name' => str_replace(array('SignPost - ', ','), '', $details['name']), 'count' => 0);
                                                }
                                            $return_posts_array[$id]['count'] += $details['count'];
                                        }
                                }
                        }
                }
            $return_posts_string = '';
                if (!empty($return_posts_array)) {
                    reset($return_posts_array);
                    foreach($return_posts_array as $id => $details) {
                        if (!empty($return_posts_string)) $return_posts_string .= ', ';
                        $return_posts_string .= $details['name'].':'.$details['count'];
                        }
                }

            $return_posts_string = $return_posts . ((!empty($return_posts_string)) ? ' (' . $return_posts_string . ')' : '');
?>
    <tr>
    <td class="main"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
    </tr>
    <tr>
    <td class="main"><b>Posts to Install: <?php echo $posts; ?></b></td>
    </tr>
    <tr>
    <td class="main"><b>Posts to Remove: <?php echo $return_posts_string; ?></b></td>
    </tr>

<?php
                while($w_n < $w_count) {
                    ob_start();
                    $item_found = false;
?>
    <tr>
    <td class="main"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
    </tr>
    <tr>
    <td class="main"><b>Warehouse: <?php echo tep_get_warehouse_name($warehouses[$w_n]); ?></b></td>
    </tr>
    <tr>
    <td class="main"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
    </tr>
    <tr>
    <td class="main"><b>To Install</b></td>
    </tr>
    <tr>
    <td width="100%" valign="top">
    <table width="100%" class="pageBox" cellspacing="0" cellpadding="2">

<?php
                    reset($equipment_groups);
                    foreach($equipment_groups as $id => $name) {
                        ob_start();
                        $found = false;
                        if ($loop > 0) {
?>
                                                    <tr>
                                                        <td height="4"><img src="images/pixel_trans.gif" height="4" width="1" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="1" colspan="8"><img src="images/pixel_grey.gif" height="1" width="100%" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                                                    </tr>
<?php
                        }

?>
                                <tr>
                                    <td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
                                </tr>
                                <tr>
                                    <td class="pageBoxContent" valign="top"><b><?php echo $name; ?></b></td>
                                </tr>
                                <tr>
                                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                                </tr>
<?php
                        if (isset($equipment_items[$warehouses[$w_n]][$id]) && is_array($equipment_items[$warehouses[$w_n]][$id])) {
                            reset($equipment_items[$warehouses[$w_n]][$id]);
                            foreach($equipment_items[$warehouses[$w_n]][$id] as $equipment_id => $data) {

                                $item_found = true;
?>
                                        <tr>
                                            <td class="pageBoxContent" valign="top"><?php echo $data['name'] . ((!empty($data['location'])) ? ' (' . $data['location'] . ')' : ''); ?></td><td class="pageBoxContent" valign="top" width="100" align="left"><?php echo $data['count'] . ((!empty($data['ref_codes'])) ? ('('.$data['ref_codes'].')') : ''); ?></td>
                                        </tr>
<?php
                                $found = true;
                            }
                        }
?>
<?php
                        $contents = ob_get_contents();
                        ob_end_clean();
                        if ($found) {
                            echo $contents;
                            $loop++;
                        }
                    }
?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
                    </tr>
                    <tr>
                        <td class="main"><b>To Remove</b></td>
                    </tr>
                    <tr>
                        <td width="100%" valign="top">
                            <table width="100%" class="pageBox" cellspacing="0" cellpadding="2">

<?php
                    reset($return_equipment_groups);


                    foreach($return_equipment_groups as $id => $name) {
                        ob_start();
                        $found = false;
                        if ($loop > 0) {
?>
                                                    <tr>
                                                        <td height="4"><img src="images/pixel_trans.gif" height="4" width="1" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="1" colspan="8"><img src="images/pixel_grey.gif" height="1" width="100%" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
                                                    </tr>
<?php
                        }
?>
                                <tr>
                                    <td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
                                </tr>
                                <tr>
                                    <td class="pageBoxContent" valign="top"><b><?php echo $name; ?></b></td>
                                </tr>
                                <tr>
                                    <td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
                                </tr>
<?php

                        if (isset($return_equipment_items[$warehouses[$w_n]][$id]) && is_array($return_equipment_items[$warehouses[$w_n]][$id])) {

                            reset($return_equipment_items[$warehouses[$w_n]][$id]);
                            foreach($return_equipment_items[$warehouses[$w_n]][$id] as $equipment_id => $data) {
                                $item_found = true;
?>
                                        <tr>
                                            <td class="pageBoxContent" valign="top"><?php echo $data['name']; ?></td><td class="pageBoxContent" valign="top" width="100" align="left"><?php echo $data['count'] . ((!empty($data['ref_codes'])) ? '('.$data['ref_codes'].')' : ''); ?></td>
                                        </tr>
<?php
                                $found = true;
                            }
                        }
?>
<?php
                        $contents = ob_get_contents();
                        ob_end_clean();
                        if ($found) {
                            echo $contents;
                            $loop++;
                        }
                    }
?>
                            </table>
                        </td>
                    </tr>
<?php
                    $contents = ob_get_contents();
                    ob_end_clean();
                    if ($item_found) {
                        echo $contents;
                    }
                    $w_n++;
                }
?>
</table>
