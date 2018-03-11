<?php
    $show_start = tep_fill_variable('show_start', 'get');
    $show_start = str_replace("-","/",$show_start);
	$page_action = tep_fill_variable('page_action', 'get');
	$submit_value = tep_fill_variable('submit_value_y');

    if (empty($show_start)) {
        $show_start = date("m/d/y");
    }

    $show = str_replace("/","-",$show_start);
    
    $date = @strtotime($show_start);
    	
	$message = '';
	
		
?>
<table width="100%" cellspacing="0" cellpadding="0">

    <tr>
     <td colspan="3"><font color="red"><small>If the item names and item totals are in RED, then the panels are NOT in the warehouse listed, or they may be in the field if they are incorrectly assigned to a different agent.</small></font></td>
    </tr>
    <tr>
        <td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
    </tr>
	<tr>
		<td valign="top">
				<?php
                global $database;
                $sql = "SELECT ". TABLE_WAREHOUSES . ".warehouse_id, ". TABLE_WAREHOUSES_DESCRIPTION . ".name from ". TABLE_WAREHOUSES . " INNER JOIN ";
                $sql .= TABLE_WAREHOUSES_DESCRIPTION ." on ". TABLE_WAREHOUSES . ".warehouse_id = ". TABLE_WAREHOUSES_DESCRIPTION . ".warehouse_id "; 
                $query = $database->query($sql);
                foreach($query as $result){
                    $total = 0;
                    ?>
                       <table width="100%" cellspacing="0" cellpadding="0"> 
                       <tr>
                       <td class="pageBoxHeading" align="left">Warehouse</td>
                       <td class="pageBoxHeading" align="left"></td>
                       <td class="pageBoxHeading" width="5%" align="left"></td>
                       </tr>
                       <tr>
                       <td class="pageBoxContent" align="left"><?php echo $result['name']; ?></td>                    
                       <td class="pageBoxContent" align="left"></td>                   
                       <td class="pageBoxContent" align="left"></td>                   
                       </tr>
                <?php
                    $sql2 = "SELECT ". TABLE_EQUIPMENT_TYPES . ".equipment_type_name, ". TABLE_EQUIPMENT_TYPES . ".equipment_type_id, ". TABLE_EQUIPMENT . ".name, ". TABLE_EQUIPMENT;
                    $sql2 .= ".equipment_id, ". TABLE_EQUIPMENT_ITEMS . ".agency_id, count(". TABLE_EQUIPMENT_ITEMS .".equipment_item_id) from ". TABLE_EQUIPMENT_ITEMS ." INNER JOIN ";
                    $sql2 .= TABLE_EQUIPMENT . " on ". TABLE_EQUIPMENT_ITEMS .".equipment_id = ". TABLE_EQUIPMENT. ".equipment_id INNER JOIN ". TABLE_EQUIPMENT_TYPES . " on ";
                    $sql2 .= TABLE_EQUIPMENT .".equipment_type_id = ". TABLE_EQUIPMENT_TYPES . ".equipment_type_id INNER JOIN ". TABLE_AGENCYS ." ON ". TABLE_EQUIPMENT_ITEMS;
                    $sql2 .= ".agency_id = ". TABLE_AGENCYS .".agency_id WHERE ". TABLE_EQUIPMENT_ITEMS .".warehouse_id = ".$result['warehouse_id'];
                    $sql2 .= " GROUP BY ". TABLE_EQUIPMENT_ITEMS . ".agency_id, equipment_id ORDER BY ". TABLE_AGENCYS . ".name, ". TABLE_AGENCYS . ".office";

                    $query2 = $database->query($sql2);
                    foreach($query2 as $result2){
                        $agency = tep_fetch_total_unused_agency($date, $result2['agency_id']);
                                
                        if (strlen($agency) > 0) {
                            $in_field = tep_fetch_total_equipment_status_count($date, 2, $result['warehouse_id'], $result2['equipment_type_id'], $result2['equipment_id']);
                            $in_field += tep_fetch_total_equipment_status_count($date, 1, $result['warehouse_id'], $result2['equipment_type_id'], $result2['equipment_id']);
                            $in_warehouse = tep_fetch_total_equipment_status_count($date, 0, $result['warehouse_id'], $result2['equipment_type_id'], $result2['equipment_id']);
                                ?>                        
                                <tr>
                                <td class="pageBoxContent" align="left"><?php echo $agency; ?></td>                    
                                <td class="pageBoxContent" align="left"><?php if ($in_field>0) echo '<font color="red">';?><?php echo $result2['name']; ?>&nbsp;&nbsp;&nbsp;<?php if ($in_field>0) echo '</font>';?></td>                    
                                <td class="pageBoxContent" align="right"><?php if ($in_field>0) echo '<font color="red">';?><?php echo $in_warehouse; ?>
                                <?php //echo $result2['equipment_type_name']; ?><?php if ($in_field>0) echo '</font>';?></td>                   
                                </tr>
                                <?php
                            $total += $in_warehouse;
                        }
                    }
              
                ?>
                    <tr>
                    <td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
                    </tr>
                    <tr>
                       <td class="pageBoxContent" align="left"></td>                    
                       <td class="pageBoxContent" align="right"><?php echo $result['name']; ?>&nbsp;&nbsp;</td>                   
                       <td class="pageBoxContent" align="right"><?php echo $total; ?></td>                   
                    </tr>
                <?php
        }
                ?>
                       </td>
                       </tr>
                       </table>
		</td>
        <td valign="top" align="center" width="20%" class="pageBoxContent"> <!-- the right panel -->
            <form>
            Report will list all </br>
            equipment with no orders </br>
            since the entered date</br>
            <i>(mm/dd/YY)</i> <input type="text" name="show_start" value="<?php echo $show_start; ?>" size="7" /> </br>
            <?php echo tep_create_button_submit('search', 'Search', ' name="button_action"'); ?></br>
            <a href="admin_unused_report_print.php?show_start=<?php echo $show; ?>" target="_blank">Print</a>
            </form>
        </td>
	</tr>
</table>
</td>
</tr>
</table>