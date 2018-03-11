<?php
    include('includes/application_top.php');
    $show_start = '';
    $page_action = tep_fill_variable('page_action', 'get');
    $submit_value = tep_fill_variable('submit_value_y');
    $equipment_type_id = tep_fill_variable('equipment_type_id', 'get'); //yup
    $warehouse_id = tep_fill_variable('warehouse_id', 'get'); // yup
    $equipment_id = tep_fill_variable('equipment_id', 'get');  //yup
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
      <td valign="top" align="center"><span class="headerFirstWord">Manage Equipment Matrix Report - <?php echo $show_start; ?></span> </td>
    </tr>
    <tr>
        <td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
    </tr>
    <table width="100%" cellspacing="0" cellpadding="0">

	<tr>
		<td width="100%" valign="top">
				<?php
					if (empty($equipment_type_id) && empty($warehouse_id) && empty($equipment_id)) {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxHeading" align="left">Warehouse</td>
									<?php
										$query = $database->query("select equipment_type_id, equipment_type_name from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_name");
											foreach($database->fetch_array($query) as $reuslt){
											?>
												<td class="pageBoxHeading" align="center"><strong><?php echo $result['equipment_type_name']; ?></strong></td>
											<?php
									
											}
									?>
								<td width="10" class="pageBoxHeading"></td>
							</tr>
						<?php
							$egData = array();
							$query = $database->query("select w.warehouse_id, wd.name from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = wd.warehouse_id order by wd.name");
								foreach($database->fetch_array($query) as $result){
									
						?>
							<tr>
								<td class="pageBoxContent"><strong><?php echo $result['name']; ?></strong></td>
									<?php
										$tquery = $database->query("select equipment_type_id from " . TABLE_EQUIPMENT_TYPES . " order by equipment_type_name");
											foreach($database->fetch_array($tquery) as $tresult){
											?>
												<td class="pageBoxContent" align="center"><?php echo tep_fetch_available_equipment_types_count($tresult['equipment_type_id'], $result['warehouse_id']); ?></td>
											<?php
									
											}
									?>
								<td width="10" class="pageBoxContent"></td>
							</tr>
						<?php
								}
						} elseif (!empty($equipment_type_id) && empty($warehouse_id) && empty($equipment_id)) {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxHeading" align="left">Equipment Name</td>
									<?php
											
												$query = $database->query("select w.warehouse_id, wd.name from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = wd.warehouse_id order by wd.name");
													foreach($database->fetch_array($query) as $result){
														?>
														<td class="pageBoxHeading" align="center"><?php echo $result['name']; ?>/ In Field</td>
														<?php
													}
											
									?>
								<td width="10" class="pageBoxHeading"></td>
							</tr>
						<?php
							$egData = array();
                            $count = 0;
							$query = $database->query("select equipment_id, name from " . TABLE_EQUIPMENT . " where equipment_type_id = '" . $equipment_type_id . "' order by name");
                            foreach($database->fetch_array($query) as $result){
                                            											
								?>
									<tr>
										<td class="pageBoxContent"><strong><?php echo $result['name']; ?></strong></td>
											<?php
												
												$tquery = $database->query("select w.warehouse_id from " . TABLE_WAREHOUSES . " w, " . TABLE_WAREHOUSES_DESCRIPTION . " wd where w.warehouse_id = wd.warehouse_id order by wd.name");
													foreach($database->fetch_array($tquery) as $tresult){
														$total = tep_fetch_total_equipment_count($result['equipment_id'], $tresult['warehouse_id']);
														$in_warehouse = tep_fetch_available_equipment_count($result['equipment_id'], $tresult['warehouse_id']);
														$in_field = ($total-$in_warehouse);
													?>
														<td class="pageBoxContent" align="center"><?php echo $in_warehouse; ?> / <?php echo $in_field; ?></td>
													<?php
													}
											?>
										<td width="10" class="pageBoxContent"></td>
									</tr>
								<?php
                                                        $count += 1;                                            
                                                        check_dash($count, $show);
										}
						} elseif (empty($equipment_type_id) && !empty($warehouse_id) && empty($equipment_id)) {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxHeading" align="left">Equipment Name</td>
								<td class="pageBoxHeading" align="center">In Warehouse</td>
								<td class="pageBoxHeading" align="center">In Field</td>
								<td width="10" class="pageBoxHeading"></td>
							</tr>
						<?php
							$egData = array();
                            $count =0;
                            $q = "select equipment_id, name from " . TABLE_EQUIPMENT . " WHERE (SELECT count(equipment_item_id) FROM " . TABLE_EQUIPMENT_ITEMS .
                                 " WHERE " . TABLE_EQUIPMENT_ITEMS . ".equipment_id = " . TABLE_EQUIPMENT . ".equipment_id AND " . TABLE_EQUIPMENT_ITEMS . 
                                 ".warehouse_id = ".$warehouse_id.") > 0 order by name";
							$query = $database->query($q);
							foreach($database->fetch_array($query) as $result){
												
											?>
												<tr>
                                                        <?php
                                                            $total = tep_fetch_total_equipment_count($result['equipment_id'], $warehouse_id);
                                                            $in_warehouse = tep_fetch_available_equipment_count($result['equipment_id'], $warehouse_id);
                                                            $in_field = ($total-$in_warehouse);
                                                            if ($total > 0) {
                                                            ?>
													<td class="pageBoxContent"><strong><?php echo $result['name']; ?></strong></td>
													<td class="pageBoxContent" align="center"><?php echo $in_warehouse; ?></td>
													<td class="pageBoxContent" align="center"><?php echo $in_field; ?></td>
													<td width="10" class="pageBoxContent"></td>
												</tr>
                                                    <?php
                                                        $count += 1;                                            
                                                        check_dash($count, $show);
                                                            }
                            }                                
						} elseif (!empty($equipment_type_id) && !empty($equipment_id)) {
						?>
						<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
							<tr>
								<td class="pageBoxHeading">Name</td>
									<td class="pageBoxHeading" align="center">Reference Code</td>
									<td class="pageBoxHeading" align="center">Status</td>
									<td class="pageBoxHeading" align="center">Last Checked</td>
									<td class="pageBoxHeading" align="right">Action</td>
							</tr>
						<?php
							$egData = array();
                            $count = 0;
							$query = $database->query("select e.equipment_id, e.name as equipment_name, ei.equipment_item_id, ei.code, ei.date_last_checked, es.equipment_status_name from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_ITEMS . " ei, " . TABLE_EQUIPMENT_STATUSES . " es where e.equipment_id = ei.equipment_id and ei.equipment_status_id = es.equipment_status_id and ei.equipment_id = '" . $equipment_id . "' ");
							foreach($database->fetch_array($query) as $result){
										?>
											<tr>
												<td class="pageBoxContent"><?php echo $result['equipment_name']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['code']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo $result['equipment_status_name']; ?></td>
												<td class="pageBoxContent" align="center"><?php echo (($result['date_last_checked'] > 0) ? date("n/d/Y", $result['date_last_checked']): 'Never'); ?></td>
												<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?eID='.$result['equipment_item_id'].'&page_action=edit&return_type=1&warehouse_id=&equipment_type_id='. $equipment_type_id; ?>">Edit</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_ITEMS . '?eID='.$result['equipment_item_id'].'&page_action=delete&return_type=1&warehouse_id=&equipment_type_id='. $equipment_type_id.'&equipment_id='.$equipment_id; ?>">Delete</a> | <a href="<?php echo FILENAME_ADMIN_EQUIPMENT_REPORT . '?equipment_type_id='.$equipment_type_id.'&equipment_id='.$equipment_id.'&equipment_item_id='.$result['equipment_item_id']; ?>">Details</a></td>
												<td width="10" class="pageBoxContent"></td>
											</tr>
                                                    <?php
                                                        $count += 1;                                            
                                                        check_dash($count, $show);
                                                    }
								}
			
						
					?>
		</td>
	</tr>
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
<?php
    function check_dash($count, $show) {
        if ($count % 10 == 0) {
            ?>
                <tr>
                    <td colspan="5">
                    <?php
                        for ($i=0;$i<100;$i++) {
                            echo "-";
                        }
?>
                    </td>
                </tr>
            <?php
        }
        if ($count % 60 == 0) {
            check_page($count, $show);
        }
    }
    function check_page($count, $show) {
        $page = $count / 60;
    ?>
                                        <tr>
                                            <td colspan="3">
                                                <table class="normaltable" cellspacing="0" cellpadding="2">
                                                    <tr>
                                                        <td class="smallText"><?php echo "Page ".$page; ?></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                                      <tr>
                                                        <td class="smallText">Note equipment with two zero counts are not shown.</td>
                                                    </tr>
    </table>
<table width="80%" cellspacing="0" cellpadding="0" align="center">
    <tr>
      <td valign="top" align="center"><span class="headerFirstWord">Manage Equipment Matrix Report - <?php echo $show; ?></span> </td>
    </tr>
    <tr>
        <td height="3"><img src="images/pixel_trans.gif" height="5" width="1"></td>
    </tr>
    <?php    
    }
?>