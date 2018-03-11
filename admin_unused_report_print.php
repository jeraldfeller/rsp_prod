<?php
	include('includes/application_top.php');
    $show_start = tep_fill_variable('show_start', 'get');
    $show_start = str_replace("-","/",$show_start);
    $page_action = tep_fill_variable('page_action', 'get');
    $submit_value = tep_fill_variable('submit_value_y');
    $equipment_type_id = tep_fill_variable('equipment_type_id', 'get');
    $warehouse_id = tep_fill_variable('warehouse_id', 'get');
    $equipment_id = tep_fill_variable('equipment_id', 'get');
    $equipment_item_id = tep_fill_variable('equipment_item_id', 'get');    
    $return_type = tep_fill_variable('return_type', 'get');

    if (empty($show_start)) {
        $show_start = date("m/d/y");
    }

    $show = str_replace("/","-",$show_start);
    
    $date = @strtotime($show_start);
        
    $message = '';
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Success</title>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<meta name="keywords" content="" />
<meta name="description" content="" />
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style1 {
	color: #FFFFFF;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
}
.style2 {
	color: #000000;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style4 {
	font-size: 17px;
	color: #000000;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {color: #0099FF}
.style6 {
	color: #000000;
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
}
-->
</style></head>

<body onLoad="window.print();">
<table width="80%" cellspacing="0" cellpadding="0" align="center">
   <tr>
		<td align="center"><img name="head_r2_c2" src="images/head_r2_c2.jpg" width="310" height="98" border="0" id="head_r2_c2" alt="" /></td>
	</tr>
	<tr>
		<td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
	  <td valign="top" align="center"><span class="headerFirstWord">Unused Equipment Report - <?php echo $show_start; ?></span> </td>
	</tr>
	<tr>
		<td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
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
                foreach($database->fetch_array($query) as $result){
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
                    foreach($database->fetch_array($query2) as $result2){
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
	</tr>
	<tr>
		<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
	</tr>
	<tr>
		<td height="20"><hr /></td>
	</tr>
    <tr>
     <td class="style6" align="center" colspan="3"><small>P.O. Box 641, McLean, VA 22101-0641 | Email: info@realtysignpost.com | Fax to: 703-995-4567 or 202-478-2131</small></td>
	</tr>
	<tr>
		<td height="20"><img src="images/pixel_trans.gif" height="20" width="1" /></td>
	</tr>
</table>
</body>
</html>