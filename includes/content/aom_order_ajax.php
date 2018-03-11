<?php
	$error = '';	
	$message = '';
	
	$nID = tep_fill_variable('nID', 'get');
	$page_action = tep_fill_variable('page_action', 'get');

	$agent_id = $user->fetch_user_id(); 
	$list_method = tep_fill_variable('list_method', 'get', '2');
	$show_order_id = tep_fill_variable('show_order_id', 'get', '');
	
	#$order_type = tep_fill_variable('order_type', 'get', '');
	#$order_status = tep_fill_variable('order_status', 'get', '1');
	
	$action = tep_fill_variable('action', 'post');
	
	if(isset($action) && $action == 'filter'){
	
		$date_from = tep_fill_variable('order_date_from', 'post');
		$date_to = tep_fill_variable('order_date_to', 'post');
		$order_type = tep_fill_variable('order_type', 'post');
		$order_status = tep_fill_variable('order_status', 'post');
		
		
		#echo '<pre>'; print_r($_REQUEST); die;
	}
	#echo 'Start Date : '.$date_from.' , End Date : '.$date_to.' , Order Type : '.$order_type. ' , Order Status : '.$order_status;die;
	/////////////////////////////////////////////////////////
	
	
	#$selectFields = "o.order_id, o.date_schedualed,o.date_accepted,o.base_cost, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city,co.name AS county_name, a.installer_comments,o.order_issue,ag.name AS agency_name,ud.firstname, ud.lastname,ud.gender";
	
$selectFields = "o.order_id, o.date_added , o.date_completed,o.date_schedualed,o.date_accepted,o.base_cost, o.order_total, ot.name as order_type_name, o.order_status_id, os.order_status_name, a.house_number, a.street_name, a.city, a.installer_comments,o.order_issue,u.user_id,a.county_id,u.agency_id";	
	
	$orderBy = "o.date_schedualed " . ($list_method == '1' ? 'ASC' : 'DESC');
	
	/*$joinTables = TABLE_ORDERS." AS o LEFT JOIN ".TABLE_ORDERS_MISS_UTILITY." AS omu ON o. order_id = omu.order_id 
			JOIN ".TABLE_ORDER_TYPES." AS ot ON o.order_type_id = ot.order_type_id 
			JOIN ".TABLE_ORDERS_STATUSES." AS os ON o.order_status_id = os.order_status_id
			JOIN ".TABLE_ADDRESSES." AS a ON o.address_id = a.address_id
			JOIN ".TABLE_USERS." AS u ON o.user_id = u.user_id
			JOIN ".TABLE_COUNTYS." AS co ON a.county_id = co.county_id
			JOIN ".TABLE_AGENCYS." AS ag ON o.agency_id = ag.agency_id
			JOIN ".TABLE_USERS_DESCRIPTION." AS ud ON u.user_id = ud.user_id";*/

	$joinTables = TABLE_ORDERS." AS o LEFT JOIN ".TABLE_ORDERS_MISS_UTILITY." AS omu ON o. order_id = omu.order_id 
		JOIN ".TABLE_ORDER_TYPES." AS ot ON o.order_type_id = ot.order_type_id 
		JOIN ".TABLE_ORDERS_STATUSES." AS os ON o.order_status_id = os.order_status_id
		JOIN ".TABLE_ADDRESSES." AS a ON o.address_id = a.address_id
		JOIN ".TABLE_USERS." AS u ON o.user_id = u.user_id";


	$conditions = '';
	#$conditions .= " o.user_id = '" . $agent_id . "'";	
	$conditions .= " u.agency_id = ".$user->agency_id;	
	
	/*if (!empty($show_order_id)) {
		
		(empty($conditions) ? $conditions .= " o.order_id = '" . (int)$show_order_id . "'" : $conditions .= " and o.order_id = '" . (int)$show_order_id . "'");
	}	*/
	if(isset($order_type) && !empty($order_type)){
		if($order_type =='all'){
			$conditions .='';
		}else{	
			(empty($conditions) ? $conditions .= " o.order_type_id ='" . $order_type. "'" : $conditions .= " and o.order_type_id ='" . $order_type. "'");
		}	
	}
	if(isset($order_status) && !empty($order_status)){
		if($order_status =='all'){
			$conditions .='';
		}else{
			(empty($conditions) ? $conditions .= " o.order_status_id ='" . $order_status. "'" : $conditions .= " and o.order_status_id ='" . $order_status. "'");
		}
	}
	if(isset($date_from) && !empty($date_from)){
		
		$start_timestamp = strtotime($date_from);
		
		(empty($conditions) ? $conditions .= " o.date_completed >='" . $start_timestamp. "'" : $conditions .= " and o.date_completed >='" . $start_timestamp. "'");
	}
	if(isset($date_to) && !empty($date_to)){
		
		$end_timestamp = strtotime($date_to);
		
		(empty($conditions) ? $conditions .= " o.date_completed <='" . $end_timestamp. "'" : $conditions .= " and o.date_completed <='" . $end_timestamp. "'");
	}	
    #echo $conditions; die;
    /*if (empty($active))
        $conditions .= " and a.status != '4' and a.status != '3'";
    
    if (!empty($agency_id)) {
        $conditions .= " and u.agency_id = '" . $agency_id . "'";
    */
	
	#$sqlQuery = "select ".$selectFields." from " . $joinTables . " where ".$conditions." order by ".$orderBy;
	
	#echo "select count(o.order_id) as total from " . $joinTables . " where ".$conditions; die;  
	$queryCount = $database->query("select count(o.order_id) as total from " . $joinTables . " where ".$conditions);
	
	
	$count = $database->fetch_array($queryCount);
	$iTotalRecords = $count['total'];
	
	$records = array();
	$records["data"] = array(); 
	
	$sEcho = intval($_REQUEST['draw']);


///////////////////////////////////////////////////////////////////////

	/*$iDisplayLength = intval($_REQUEST['length']);
	$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
	$iDisplayStart = intval($_REQUEST['start']);
	
	
	$end = $iDisplayStart + $iDisplayLength;
	$end = $end > $iTotalRecords ? $iTotalRecords : $end;	
	
	$limit = $iDisplayStart.", ".$iDisplayLength;
	
	$sqlQuery = "select ".$selectFields." from " . $joinTables . " where ".$conditions." order by ".$orderBy.' limit '.$limit;
	echo $sqlQuery; die;*/

///////////////////////////////////////////////////////////////////////
	
	if ($iTotalRecords > 0) {
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		
		
		$end = $iDisplayStart + $iDisplayLength;
		$end = $end > $iTotalRecords ? $iTotalRecords : $end;	
		
		$limit = $iDisplayStart.", ".$iDisplayLength;
		
		$sqlQuery = "select ".$selectFields." from " . $joinTables . " where ".$conditions." order by ".$orderBy.' limit '.$limit;
		#$sqlQuery = "select ".$selectFields." from " . $joinTables . " where ".$conditions." order by ".$orderBy;
		#echo $sqlQuery; die;
		$query = $database->query($sqlQuery);
		
		$i = 0;
		
		while($result = $database->fetch_array($query)) {

			$id = $result['order_id'];

			$records["data"][] = array(
				//'<input class="checkboxes" type="checkbox" name="data[id-' . $id . ']" value="1"/>',
				#$id,
				$result['order_type_name'],
				#tep_get_order_type_name($result['order_type_name']),
				date('n/d/y',($result['date_added'])),
				date('n/d/y',($result['date_completed'])),
				
				tep_get_user_name($result['user_id']),#$result['firstname'].' '.$result['lastname'],
				tep_get_aom_agency_name($result['agency_id']),
				$result['house_number'],
				$result['street_name'],
				$result['city'],
				tep_get_county_name($result['county_id']),
				'$'.number_format($result['base_cost'], 2),
				'$'.number_format($result['order_total'], 2),
				(empty($result['installer_comments']) ? 'N/A' : $result['installer_comments'])
				
				
			);			
			#echo '<pre>'; print_r($records); die;
			//$vars['split_result'][$i] = $result;
			//$vars['split_result'][$i]['date_schedualed'] =  date("n/d/Y", $result['date_schedualed']);
			$i++;
		}
	
	}
	
	#echo '<pre>';
	#print_r($vars); die;

	if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
		$records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
		$records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
	}
	
	$records["draw"] = $sEcho;
	$records["recordsTotal"] = $iTotalRecords;
	$records["recordsFiltered"] = $iTotalRecords;
  
	echo json_encode($records);die;
	
	/*
	$vars['show'] = $show;
	$vars['listing_split'] = $listing_split;
	$vars['pagination'] = tep_get_all_get_params(array('page', 'info', 'x', 'y'));
	$vars['pulldowns']['orderType'] = tep_draw_order_type_pulldown_bgdn('order_type', $order_type, 'change-submit', array(array('id' => '', 'name' => 'All Orders')));
	$vars['pulldowns']['orderStatus'] = tep_draw_orders_status_pulldown_bgdn('order_status', $order_status, array(array('id' => '', 'name' => 'Any')), 'change-submit');
	*/




