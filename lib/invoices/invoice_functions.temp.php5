<?php

/*
 * Part of Realty Sign Post (c) 2013 Realty Sign Post.
 * Description: General shared functions that make the invoicing system work. Need to be included in all invoice pages.
 *
 * Author: Brad Berger <brad@brgr2.com>
 * See version control for full commit history.
 *
 */
require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

if(substr_count('realtysignpost',$_SERVER['HTTP_HOST'])) {
  error_reporting(0);
  ini_set('error_reporting', 0);
  ini_set('display_errors', 'Off');
}

// Get REQUEST variables. Will use these later by making them global, so get all possible ones here.
$l = explode(',', 'account_item_id,date_added,reason,details,total,user_id,agency_id,month,year');
foreach ($l as $v) {
  if (isset($_REQUEST[$v])) {
    if(is_array($_REQUEST[$v])) {
      foreach($_REQUEST[$v] as $key => $value) {
        $_REQUEST[$v][$key] = addslashes($value);
      }
    } else {
      $$v = addslashes($_REQUEST[$v]);
    }
  } else {
    $$v = null;
  }
}

// These check permissions, but it's intended only in the context of invoices.
function is_admin() {
  if (is_array($_SESSION) && array_key_exists('user_group_id', $_SESSION) && $_SESSION['user_group_id'] == 2) {
    return true;
  }
  return false;
}

// This matches AOMs
function is_aom() {
  if (is_array($_SESSION) && array_key_exists('user_group_id', $_SESSION) && $_SESSION['user_group_id'] == 4) {
    return true;
  }
  return false;
}

// This matches accounts payable.
function is_ap() {
  if (is_array($_SESSION) && array_key_exists('accounts_payable', $_SESSION) && $_SESSION['accounts_payable'] == 1) {
    return true;
  }
  if (is_array($_SESSION) && array_key_exists('user_group_id', $_SESSION) && $_SESSION['user_group_id'] == 5) {
    return true;
  }
  return false;
}

function is_agent() {
  if (is_array($_SESSION) && array_key_exists('user_group_id', $_SESSION) && $_SESSION['user_group_id'] == 1) {
    return true;
  }
  return false;
}

function get_invoice_email_history($invoice_id, $string = true) {

  global $database;
  $return = "";
  $s = "SELECT * FROM invoice_email_history WHERE invoice_id = '{$invoice_id}'";
  $q = $database->query($s);
  foreach($database->fetch_array($q) as $r){
    if ($string) {
      $return .= "<span class='inv-comment-box'>Email(s) sent: {$r['date_sent']}</span>";
    }
  }
  return $return;
}

// Get a list of invoice items for the user/agency for the given month. Limit is to make query faster when displaying all invoices together.
function get_invoice_items($month, $year, $user_id, $agency_id) 
{
	global $database;
	
	// Because orders need to be displayed only if **completed** in this month, need to check date_completed in the orders table.
	// As a result, this SQL will skip all account_items that are not orders. We'll get those missed items later.
	// This will determine which records are searching for.
	$run_user_query = false;
	$run_agency_query = false;
	$run_ap_query = false;
	$run_all_query = false;
	// Figure out what kind of query running.
	if (!$user_id && !$agency_id) {
        $run_all_query = true;
    } elseif ($user_id && $agency_id) {
        $run_ap_query = true;
    } elseif ($user_id) {
		$run_user_query = true;
    } elseif ($agency_id) {
		$run_agency_query = true;
	}

	// Make sure the month/year are in the correct order, with the earlier month/year in the [0] index.
	if(is_array($month) && is_array($year)) 
	{
		// Get the timestamps
		$fts = mktime(0,0,0,$month[0],1,$year[0]);
		$lts = mktime(0,0,0,$month[1],1,$year[1]);
		
		// If the first date is later than the last date
		
		if($fts > $lts) 
		{
			// Append first value to the end
			$month[] = $month[0];
			$year[] = $year[0];
			
			// Remove the first value.
			array_splice($month,0,1);
			array_splice($year,0,1);
		}
	}
	
	// If month/year is a string, we're searching for single month/year.
	if (is_array($month) && is_array($year) && count($month) == 2 && count($year) == 2) 
	{ // Searching for range.
		// Find the first second of the first month.
		$d = new DateTime();
		$d->setTimestamp(mktime(0, 0, 0, $month[0], 1, $year[0]));
        $start_ts = $d->getTimestamp();

        $start_year = $year[0];
        $start_month = $month[0];

		// Find the last second of the last month.
		$d = new DateTime();
		$d->setTimestamp(mktime(0, 0, 0, $month[1], 1, $year[1]));
		$d->add(new DateInterval('P1M'));
        $end_ts = $d->getTimestamp() - 1;

        $end_year = $year[1];
        $end_month = $month[1]+1;
	} 
	elseif ($month && $year) 
	{
		if (is_array($month)) 
		{
			$month = $month[0];
		}
		
		if (is_array($year)) 
		{
			$year = $year[0];
		}
		
		$d = new DateTime();
		$d->setTimestamp(mktime(0, 0, 0, $month, 1, $year));
		$start_ts = $d->getTimestamp();
		$d->add(new DateInterval('P1M'));
        $end_ts = $d->getTimestamp() - 1;

        $start_year = $year;
        $start_month = $month;
        $end_year = $year;
        $end_month = $month+1;
	} 
	else 
	{ //
		return false;
	}

    if ($end_month == 13) {
        $end_year++;
        $end_month = 1;
    }

    if ($start_year < 2014) {
        $start_year = 2014;
    }
    if ($end_year < 2014) {
        $end_year = 2014;
    }
	
	// SET up the sql to match who we're looking for. ts 1356998400 is Jan 1, 2013.
	// FIXME replace u.billing_method_id with a.billing_method_id for more proper handling of items. Without it, however, things don't show up
	// because items that should have a.billing_method_id = 3 have a.billing_method_id = 2
	$s = "
	SELECT
    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(COALESCE(o.date_completed, a.date_added)), 'Australia/Perth', 'US/Eastern'), '%c/%e/%Y') AS `order_datecompleted`,
    a.account_item_id, a.direction, a.total, a.date_added, 
    COALESCE(a.user_id, o.user_id) AS `user_id`,
    COALESCE(a2.agency_id, a.agency_id, o.agency_id, u.agency_id) AS `agency_id`,
    a.month_added, a.year_added, a.reference_id,
    a.reason, a.details, a.check_date, a.check_date_received,
    COALESCE(a.billing_method_id, o.billing_method_id, u.billing_method_id) AS `billing_method_id`,
	ud.lastname, ud.firstname, o.order_id, 
    COALESCE(o.date_completed, UNIX_TIMESTAMP(CONCAT(a.year_added, '-', a.month_added, '-01 23:00:00'))) as date_completed,
    COALESCE(o.date_completed, UNIX_TIMESTAMP(CONCAT(a.year_added, '-', a.month_added, '-01 23:00:00'))) as date_completed,
	ag.name AS agency_name, ag.office AS agency_office,
	ad.house_number, ad.street_name, ad.city, ad.zip, st.name AS state, u.agent_id
	FROM account_items AS `a`
	LEFT JOIN orders AS `o` ON a.reference_id = o.order_id 
	LEFT JOIN users AS `u` ON COALESCE(a.user_id, o.user_id) = u.user_id 
	LEFT JOIN agencys AS `ag` ON IF(a.agency_id IS NOT NULL,a.agency_id,IF(o.agency_id IS NOT NULL AND o.agency_id>0,o.agency_id,IF(u.agency_id IS NOT NULL,u.agency_id,a.agency_id))) = ag.agency_id 
	LEFT JOIN users_description AS `ud` ON COALESCE(a.user_id, o.user_id) = ud.user_id 
	LEFT JOIN addresses AS `ad` ON o.address_id = ad.address_id
    LEFT JOIN states AS `st` ON ad.state_id = st.state_id
    LEFT JOIN accounts AS `a2` ON a2.account_id = a.account_id
	WHERE 
    (
        (o.date_completed >= UNIX_TIMESTAMP(CONVERT_TZ('{$start_year}-{$start_month}-01', 'US/Eastern', 'Australia/Perth')) AND
            o.date_completed < UNIX_TIMESTAMP(CONVERT_TZ('{$end_year}-{$end_month}-01', 'US/Eastern', 'Australia/Perth')))
        OR
        (a.reference_id = 0 AND a.year_added >= 2014 AND 
            UNIX_TIMESTAMP(CONCAT(a.year_added, '-', a.month_added, '-01 23:00:00')) >= UNIX_TIMESTAMP(CONVERT_TZ('{$start_year}-{$start_month}-01', 'Australia/Perth', 'US/Eastern')) AND 
            UNIX_TIMESTAMP(CONCAT(a.year_added, '-', a.month_added, '-01 23:00:00')) < UNIX_TIMESTAMP(CONVERT_TZ('{$end_year}-{$end_month}-01', 'Australia/Perth', 'US/Eastern')))
    )
    ";
	
	// This is where the function comes to a fork in the road, so to speak.
	if ($run_all_query) 
	{
		// Don't need to add additional SQL.
	} 
	elseif ($run_agency_query) 
	{
        $s .= " AND COALESCE(a.billing_method_id, o.billing_method_id, u.billing_method_id, 2) != 3";
        $s .= " AND ag.agency_id = {$agency_id}";
	} 
	elseif ($run_user_query) 
	{ 
        $s .= " AND COALESCE(a.billing_method_id, o.billing_method_id, u.billing_method_id, 2) = 3";
        $s .= " AND u.user_id = {$user_id}";
	} 
	elseif ($run_ap_query) 
	{ 
        $s .= " AND ((COALESCE(a.billing_method_id, o.billing_method_id, u.billing_method_id, 2) != 3 AND ag.agency_id = {$agency_id})";
        $s .= " OR (COALESCE(a.billing_method_id, o.billing_method_id, u.billing_method_id, 2) = 3 AND u.user_id = {$user_id}))";
	} 
	else 
	{
		return false;
	}
	
	//start added 08.01.2014 DrTech76, hook with the "move to agency" log
	$s.=" ORDER BY COALESCE(o.date_completed, a.date_added) ASC";
	//var_dump($s);
	$agent_agencies=array();
	//end added  08.01.2014 DrTech76, hook with the "move to agency" log
	
	
	$q = $database->query($s);
	$list = array();
	foreach($database->fetch_array($q) as $r)
	{
		//var_dump("RECORD");var_dump($r);
		$this_month = date('n', $r['date_completed']);
        $this_year = date('Y', $r['date_completed']);

		//start added 08.01.2014 DrTech76, hook with the "move to agency" log
		$oc_day=(int)date("j",$r['date_completed']);
		//end added 08.01.2014 DrTech76, hook with the "move to agency" log
		
		// Everything returned this way, regardless of type of search
		// $list [agencies/agents][id][year][month][total,[]items]
		// Set up the return array.
		if (!isset($list['agencies'])) 
		{
			$list = array('agencies' => array(), 'agents' => array());
		}
		
		// Direction of 1 means a credit. Make sure it's negative.
		
		$r['total'] = (float) $r['total'];
		$r['direction'] == (int) $r['direction'];
		
		if ($r['direction'] == 1 && $r['total'] > 0) 
		{
			$r['total'] *= -1;
		}
		if ($r['firstname'] == null) 
		{
			$r['firstname'] = ' ';
		}
		if ($r['lastname'] == null) 
		{
			$r['lastname'] = ' ';
		}
		if ($r['reason'] == null) 
		{
			$r['reason'] = ' ';
		}
		if ($r['details'] == null) 
		{
			$r['details'] = ' ';
		}
		if ($r['house_number'] == null) 
		{
			$r['house_number'] = 'No house number';
		}
		// Add the address details to missing.
		if((preg_match('/missing/i',$r['reason']) || preg_match('/missing/i',$r['details'])) && ! substr_count($r['details'],$r['house_number'])) 
		{
			$r['details'] .= "<br>" . $r['house_number'] . " " . $r['street_name'] . " " . $r["city"] . ", " . $r['state'] . " " . $r['zip'];
		}
		
		// 3 means an agent invoice, needs to go on their individual tab.
		if ($r['billing_method_id'] == 3) 
		{
			// Make sure the array is set up, including agency id, year, month and total at the end.
			if (!isset($list['agents'][$r['user_id']])) 
			{
				$list['agents'][$r['user_id']] = array();
			}
			if (!isset($list['agents'][$r['user_id']][$this_year])) 
			{
				$list['agents'][$r['user_id']][$this_year] = array();
			}
			if (!isset($list['agents'][$r['user_id']][$this_year][$this_month])) 
			{
				$list['agents'][$r['user_id']][$this_year][$this_month] = array('total' => 0);
			}
			
			$list['agents'][$r['user_id']][$this_year][$this_month][] = $r;
			$list['agents'][$r['user_id']][$this_year][$this_month]['total'] += $r['total'];
		} 
		else 
		{ // Goes on agency tab.
		
			//start added 08.01.2014 DrTech76, hook with the "move to agency" log
			if(!is_null($r['user_id']) and !isset($agent_agencies[$r['user_id']]))
			{
				$agent_agencies[$r['user_id']]=array();
				$sql="SELECT *, UNIX_TIMESTAMP(`action_date`) AS `timestamp`, MONTH(`action_date`) AS `change_month`, YEAR(`action_date`) AS `change_year`, DAY(`action_date`) AS `change_day` FROM `agencies_to_users` WHERE `user_id`=".$r['user_id']." ORDER BY `change_year` ASC, `change_month` ASC, `change_day` ASC, `timestamp` ASC";
				$agent_moves_res=$database->query($sql);
				if($agent_moves_res and $database->num_rows($agent_moves_res)>0)
				{
					for($ami=0;$ami<=$database->num_rows($agent_moves_res)-1;$ami++)
					{
						$rec=$database->fetch_array($agent_moves_res);
						foreach($rec as $fname=>$fvalue)
						{
							if(in_array($fname,array("user_id","agency_id","change_year","change_month","change_day","timestamp")))
							{
								$rec[$fname]=(int)$fvalue;
							}
						}
						if(!isset($agent_agencies[$r['user_id']][$rec["change_year"]]))
						{
							$agent_agencies[$r['user_id']][$rec["change_year"]]=array();
						}
						
						if(!isset($agent_agencies[$r['user_id']][$rec["change_year"]][$rec["change_month"]]))
						{
							$agent_agencies[$r['user_id']][$rec["change_year"]][$rec["change_month"]]=array();
						}
						if(!isset($agent_agencies[$r['user_id']][$rec["change_year"]][$rec["change_month"]][$rec["change_day"]]))
						{
							$agent_agencies[$r['user_id']][$rec["change_year"]][$rec["change_month"]][$rec["change_day"]]=array();
						}
						
						$agent_agencies[$r['user_id']][$rec["change_year"]][$rec["change_month"]][$rec["change_day"]][$rec["timestamp"]]=$rec["agency_id"];
					}
				}
			}
			
			$oc_year=(int)$this_year;
			$oc_month=(int)$this_month;
			$oc_timestamp=(int)$r["date_completed"];
			$amAgency=0;
			//var_dump("AGENT[".$r['user_id']."]_AGENCIES");var_dump($agent_agencies[$r['user_id']]);
			if(!is_null($r['user_id']))
			{
				$amAgency=agent_movement_pick_agency($agent_agencies[$r['user_id']],0,0,0,$oc_year,$oc_month,$oc_day,$oc_timestamp);
				$sql="UPDATE `account_items` SET `user_id`=".$r['user_id']." WHERE `account_item_id`=".$r["account_item_id"];
				$database->query($sql);
				//var_dump($sql);var_dump(mysql_error($database->db_link));
				//var_dump("PICK_AGENCY");var_dump($amAgency);
			}
			if($amAgency>0)
			{
				$r['agency_id']=$amAgency;
				$sql="UPDATE `account_items` SET `agency_id`=".$amAgency." WHERE `account_item_id`=".$r["account_item_id"];
				$database->query($sql);
				//var_dump($sql);var_dump(mysql_error($database->db_link));
			}
			
			//end added  08.01.2014 DrTech76, hook with the "move to agency" log
		
			// Make sure the array is set up, including agency id, year, month and total at the end.
			if (!isset($list['agencies'][$r['agency_id']])) 
			{
				$list['agencies'][$r['agency_id']] = array();
			}
			if (!isset($list['agencies'][$r['agency_id']][$this_year])) 
			{
				$list['agencies'][$r['agency_id']][$this_year] = array();
			}
			if (!isset($list['agencies'][$r['agency_id']][$this_year][$this_month])) 
			{
				$list['agencies'][$r['agency_id']][$this_year][$this_month] = array('total' => 0);
			}
			
			$list['agencies'][$r['agency_id']][$this_year][$this_month][] = $r;
			$list['agencies'][$r['agency_id']][$this_year][$this_month]['total'] += $r['total'];
		}
	}
	
	return $list;
}

function get_invoice_address($user_id, $agency_id, $month=0, $year=0) {
  global $database;
  if ($user_id) {
    $s = "SELECT u.user_id,ud.firstname,ud.lastname,ud.street_address,ud.postcode,ud.city,ud.state_id,s.name AS state_name FROM users u, users_description ud,states s WHERE u.user_id={$user_id} AND u.user_id = ud.user_id AND ud.state_id = s.state_id";
    $q = $database->query($s);
    foreach($database->fetch_array($q) as $r){
      return "<strong>{$r['firstname']} {$r['lastname']}</strong><br>{$r['street_address']}<br>{$r['city']} {$r['state_name']}<br>{$r['postcode']}";
    }
  } elseif($agency_id) {
    $s = "SELECT a.name,a.office,a.address FROM agencys a WHERE agency_id = {$agency_id} LIMIT 1";
    $q = $database->query($s);
    foreach($database->fetch_array($q) as $r){
      return "<strong>{$r['name']}</strong><br>{$r['office']}<br>" . nl2br($r['address']);
    }
  }
  return null;
}

// Returns total account +/- up to and including the given month.
function get_account_total($user_id, $agency_id, $month = 0, $year = 0) 
{
	// This will keep track of the amount.
	$account_balance = 0;
	
	// Since it's up to and including, and using the "previous" balance function, need to add one month to the given date.
	$date = new DateTime();
	$date->setTimestamp(mktime(0, 0, 0, $month, 1, $year));
	$this_month = $date->format('n');
	$this_year = $date->format('Y');
	
	// Now, start the loop which will go backwards month by month until it we leave 2014, which was when invoices went live.
	while (true) 
	{
		$this_month = $date->format('n');
		$this_year = $date->format('Y');
		$this_items = get_invoice_items($this_month, $this_year, $user_id, $agency_id);
		
		// If this is October, will surely be blank.
		// First, check to see if getting a user's info.
		if($user_id) 
		{
			if (!empty($this_items['agents'][$user_id][$this_year][$this_month]['total'])) 
			{
				$account_balance += $this_items['agents'][$user_id][$this_year][$this_month]['total'];
			}
		} 
		elseif (!empty($this_items['agencies'][$agency_id][$this_year][$this_month]['total'])) 
		{
			// Now, since it's not a user, we can assume agency, and get balance for the agency
			$account_balance += $this_items['agencies'][$agency_id][$this_year][$this_month]['total'];
		} 
		else 
		{
			/* Do nothing. */      
		}
		
		// Subtract and exit (maybe).
		$date->sub(new DateInterval('P1M'));
		if ($this_year < 2014) 
		{
			break;
		}
	}
	
	// Return it.
	return number_format($account_balance, 2);
}

function echo_meta_refresh_tag($sec = 10) {
  echo "<!DOCTYPE html><html><head></head><body><meta http-equiv='refresh' content='{$sec}'>";
}

function update_missing_account_item_info() {
  
  global $database;
  $s = "
    SELECT a.account_item_id, o.*, t.*, ad.*, st.name AS state_name, u.agency_id FROM account_items a 
    JOIN orders o ON a.reference_id = o.order_id
    JOIN order_types t ON o.order_type_id = t.order_type_id
    JOIN addresses ad ON o.address_id = ad.address_id
    JOIN states st ON ad.state_id = st.state_id
    JOIN users u ON o.user_id = u.user_id
    WHERE a.billing_method_id IS NULL OR a.reason IS NULL or a.agency_id IS NULL OR a.user_id IS NULL
  ";
  
  $q = $database->query($s);
  $result = false;
  foreach($database->fetch_array($q) as $r){
    
      $u = "
        UPDATE account_items 
        SET 
          billing_method_id = {$r['billing_method_id']},
          reason = '{$r['name']}',
          details = '{$r['house_number']} {$r['street_name']}, {$r['city']} {$r['state_name']} {$r['zip']}',
          user_id = {$r['user_id']},
          agency_id = {$r['agency_id']}
        WHERE account_item_id = {$r['account_item_id']}
      ";
      
      $ok = $database->query($u);
      if($ok) {
        
        if(!$result) {
          echo_meta_refresh_tag(1);
          $result = true;
        }
        
        $fc = "green";
      } else {
        $fc = "red";
      }
      echo "<span style='color: {$fc}'>{$u}</span>";
  }
}

function edit_account_item($id) {
    global $database;
  $s = "SELECT * FROM account_items WHERE account_item_id = {$id}";
  $q = $database->query($s);
  foreach($database->fetch_array($q) as $r){
    print_r($r);
  }
}

function find_missing_account_item_info() {
  global $database;
  
  // Lets auto-limit the speed of this.
  //echo_meta_refresh_tag(2);
  
  $sql = "
  SELECT a.account_item_id, a.reference_id, o.user_id, o.billing_method_id, u.agency_id
  FROM account_items a, orders o, users u
  WHERE
    a.reference_id = o.order_id AND
    o.user_id = u.user_id AND
    (a.user_id < 1 OR a.user_id IS NULL) AND
    (a.agency_id < 1 OR a.agency_id IS NULL) AND
    (a.billing_method_id < 1 OR a.billing_method_id IS NULL)
  ORDER by o.date_completed
  DESC LIMIT 4
  ";
  $q = $database->query($sql);
  foreach($database->fetch_array($q) as $r){
    $s = "UPDATE account_items SET agency_id = '{$r['agency_id']}', user_id = '{$r['user_id']}', billing_method_id = {$r['billing_method_id']} WHERE account_item_id = {$r['account_item_id']}";
    if ($database->query($s)) {
      //echo "SUCCESS {$s}<br>";
    } else {
      //echo "ERROR {$s}<br>";
    }
  }
  
}

function find_orphaned_orders() {
  global $database;
  $sql = "
    SELECT o.*, ot.name, a.*, st.name AS state_name, FROM_UNIXTIME(o.date_completed) AS completed
    FROM orders o, order_types ot, orders_description od, addresses a, states st
    WHERE
        o.order_id = od.order_id AND
        o.address_id = a.address_id AND
        a.state_id = st.state_id AND
        o.date_completed > 1356998400 AND
        (o.order_total > 0 OR o.extra_cost > 0) AND
        o.billing_method_id > 1 AND
        o.order_id NOT IN (SELECT reference_id FROM account_items) AND 
        NOT (o.billing_method_id = 2  AND o.order_type_id = 3 ) AND        
        o.order_type_id = ot.order_type_id
    ORDER BY o.date_completed DESC
    LIMIT 20
  ";
  $update = "";
  $q = $database->query($sql);
  $to_update = false;
  foreach($database->fetch_array($q) as $r){
    
    // First time through, set up the first part of the sql statements.
    if (empty($update)) {
      $update .= "
      INSERT INTO account_items (
        note,
        user_id,
        agency_id,
        date_added,
        month_added,
        year_added,
        direction,
        total,
        type,
        status_id,
        order_type_id,
        reference_id,
        reason,
        details)
      VALUES";
    }

    if ($r['billing_method_id'] == 2 && $r['order_type_id'] == 3) {
      //echo "Skipping...<br>";
    } else {

      $to_update = true;
      if ($r['billing_method_id'] == 2 || true) {
        //echo " agency";
        $month = date("n", $r['date_completed']);
        $added = $r['date_completed'];
        $year = date("Y", $r['date_completed']);
        $addr = $r['house_number'] . " " . $r['street_name'] . ", " . $r['city'] . " " . $r['state_name'] . " " . $r['zip4_start'];

        // Make sure user_id/agency_id has a value...
        if (!$user_id = $r['user_id']) {
          $user_id = 0;
        }
        if (!$agency_id = $r['agency_id']) {
          $agency_id = 0;
        }
        if (!$r['name']) {
          $r['name'] = " ";
        }
        if (!$addr) {
          $addr = " ";
        }

        // Extra cost value precedes order total, as order total sometimes is 0 for orders with charges for missing items, etc.
        if($r['extra_cost']) {
          $this_order_total = $r['extra_cost'];
        } else {
          $this_order_total = $r['order_total'];       
        }        
        $update .= "('Missing order added automatically by the invoice system.','{$r['user_id']}','{$r['agency_id']}','{$added}','{$month}','{$year}','0','{$this_order_total}',1,2,'{$r['order_type_id']}','{$r['order_id']}','{$r['name']}','{$addr}'),";
        
      }
    }
  }

  // If found orphaned orders that need to be in account_items, do it!
  if ($to_update) {
    $update = substr($update, 0, strlen($update) - 1);
    $database->query($update);
  }
  
}

// input the output of get_invoice_items() here to create an overview.
function get_invoice_overview_table($items, $status = false) {

  $balance = 0;
  $grand_total = 0;
  
  if (isset($_REQUEST['balance'])) {
    $balance = (float) $_REQUEST['balance'];
  }

  // Heads up! Need to display: invoice ID/alt ID (?)/year/month/agency/office/agent/balance/status/action/email history/comments.
  // For now, merge alt ID with comments, as both display.
  echo "<table id='invoice_tbl' class='table table-striped table-hover'><thead>\n";
  if (is_ap()) {
      echo "<tr><th>Action</th><th>Invoice ID</th><th>Year</th><th>Month</th><th>Account Name</th><th>Original Balance</th><th>Current Balance</th><th>Status</th><th>Notes</th></tr>\n";
  } elseif (is_agent()) {
      echo "<tr><th>Action</th><th>Invoice ID</th><th>Year</th><th>Month</th><th>Account Name</th><th>Original Balance</th><th>Current Balance</th><th>Status</th><th>Notes</th></tr>\n";
  } elseif (is_admin()) {
    echo "<tr><td><a href='javascript:;' onclick='window.emailQueue.addChecked();'>Add to Queue</a><br><a href='javascript:;' onclick='window.emailQueue.toggleSelect()'>Select/Unselect All</a></td><th>Invoice ID</th><th>Year</th><th>Month</th><th>Account Name</th><th>Original Balance</th><th>Current Balance</th><th>Status</th><th>Notes</th></tr>\n";
  }

  echo "</thead><tbody>\n";
  // First, deal with agencies
  if (!empty($items['agencies'])) {
    
    foreach ($items['agencies'] as $aid => $yo) {

      if (!(is_admin() || (is_ap() && $aid == $_SESSION['agency_id']))) {
        // Admins can look at any invoice, AP's only their own agency
        continue;
      }

      foreach ($yo as $y => $mo) {
        
        foreach ($mo as $m => $val) {
          
          if(!$aid) {
            continue;
          }
          // FIXME Why might this occasionally not be set?
          if(!isset($val[0]['agency_name'])) {
            global $database;
            $s1 = "SELECT name,office FROM agencys WHERE agency_id = {$aid} LIMIT 1";
            $q1 = $database->query($s1);
            foreach($database->fetch_array($q1) as $r1){
              $agency_name = $r1['name'];
              $agency_office = $r1['office'];
            }
          } else {
            $agency_name = $val[0]['agency_name'];
            $agency_office = $val[0]['agency_office'];            
          }
          
          $agency_total = 0;
          $original_balance = 0;
          $agency_comments = null;
          $invoice_id = "{$y}-{$m}-{$aid}-0";
          $month_name = date('M', mktime(0, 0, 0, $m, 1, $y));

          if (is_admin()) {
            $actions = "
              <div class='btn-group'>
                <a class='btn btn-mini dropdown-toggle' data-toggle='dropdown' href='#'>&nbsp;<i class='icon-cog'>&nbsp;</i><span class='caret'>&nbsp;</span></a>
                <ul class='dropdown-menu'>
                  <li><a target='_blank' href='admin_view_invoices.php?action=details&agency_id={$aid}&month={$m}&year={$y}'><i class='icon-edit'>&nbsp;</i> View/Edit</a>
                  <li><a href='javascript:;' onclick='window.emailQueue.add({user_id: 0, agency_id: {$aid},year: {$y},month: {$m}, name: \"{$agency_name} {$agency_office}\"});'><i class='icon-envelope'>&nbsp;</i> Add to Email Queue</a>
                  <li><a href='javascript:;' onclick='show_adjustment_form({user_id: 0, agency_id: {$aid},year: {$y},month: {$m}, name: \"{$agency_name} {$agency_office}\"})'><i class='icon-credit-card'>&nbsp;</i> Add Payment/Adjustment</a>
                </ul>
              </div>
              <input type='checkbox' name='selected_invoices' class='pull-right'>
            ";
          } else {
            $actions = "<a href='" . basename($_SERVER['REQUEST_URI']) . "?action=details&agency_id={$aid}&month={$m}&year={$y}'>View/Print</a>";
          }
          // Need to descend in here to get comments.
          foreach ($val as $id => $details) {

            $agency_total += $details['total'];
            if ($details['reference_id'] != 0 || $details['total'] > 0) {
                $original_balance += $details['total'];
            }

            if ($id == 'total') {
              continue;
            }
            // Get the details of payments, add to comments column.
            // Payments are different, and wont have a date_completed.
            // Anything with a reason containing adj will show up on the comments.
            if (empty($val['date_completed']) && stripos($details['reason'],'adj') !== false) {
              $agency_comments .= "<span class='inv-comment-box'>{$details['details']}</span>";
            }
          }
          // Convert to an actual number
          $agency_total = (float) $agency_total;

          // Set the status here for agencies. Current includes this month and last months dates.
          $lm = new DateTime();
          $lm->sub(new DateInterval('P1M'));
          if (($m == date('n') && $y == date('Y')) or ($m == $lm->format('n') && $y == $lm->format('Y'))) {
            $this_status = 'Current';
          } elseif ($agency_total <= 0) {
            $this_status = 'Paid';
          } else {
            $this_status = 'Overdue';
          }
          
          // If applying a status filter and it doesn't match, simple skip this record.
          if ($status && strtolower($this_status) != strtolower($status)) {
            continue;
          }
          
          if ($balance != 0) {
            if ($balance < 0 && $agency_total > $balance) {
              continue;
            }
            if ($balance > 0 && $agency_total < $balance) {
              continue;
            }
          }

          // Get email history for this invoice.
          $agency_comments .= get_invoice_email_history($invoice_id);
          echo "
            <tr class='inv-row' data-year='{$y}' data-name='{$agency_name} {$agency_office}' data-month='{$m}' data-user='0' data-agency='{$aid}'>
              <td>{$actions}</td>
              <td>{$invoice_id}</td>
              <td>{$y}</td>
              <td>{$month_name}</td>
              <td>{$agency_name} ({$agency_office})</td>
              <td>\${$original_balance}</td>
              <td class='balance'>\${$agency_total}</td>
              <td>{$this_status}</td>
              <td>{$agency_comments}</td>
            </tr>
          ";
              
          // Keep track of global total.          
          $grand_total += $agency_total;
          
        }
      }
    }
  }

  // Now deal with individual user invoice(s)
  if (!empty($items['agents'])) {

    //echo "<pre>" . print_r($items['agents'], 1) . "</pre>";
    foreach ($items['agents'] as $aid => $yo) {

      if (!(is_admin() || (is_agent() && $aid == $_SESSION['user_id']))) {
        // Admins can see anyone invoice, agents only their own.
        continue;
      }

      foreach ($yo as $y => $mo) {
        foreach ($mo as $m => $val) {

          if (!empty($val[0]['lastname']) && !empty($val[0]['firstname'])) {
              $name = $val[0]['lastname'] . ", " . $val[0]['firstname'];
          } else {
              $name = trim($val[0]['firstname'] . " " . $val[0]['lastname']);
          }
          $agency_total = 0;
          $original_balance = 0;
          $agency_comments = null;
          $invoice_id = "{$y}-{$m}-0-{$aid}";
          $month_name = date('M', mktime(0, 0, 0, $m, 1, $y));

          if (is_admin()) {
            $actions = "
              <div class='btn-group'>
                <a class='btn btn-mini dropdown-toggle' data-toggle='dropdown' href='#'>&nbsp;<i class='icon-cog'>&nbsp;</i><span class='caret'>&nbsp;</span></a>
                <ul class='dropdown-menu'>
                  <li><a target='_blank' href='admin_view_invoices.php?action=details&user_id={$aid}&month={$m}&year={$y}'><i class='icon-edit'>&nbsp;</i> View/Edit</a>
                  <li><a href='javascript:;' onclick='window.emailQueue.add({user_id: {$aid}, agency_id: 0,year: {$y},month: {$m}, name: \"{$name}\"});'><i class='icon-envelope'>&nbsp;</i> Add to Email Queue</a>
                  <li><a href='javascript:;' onclick='show_adjustment_form({user_id: {$aid}, agency_id: 0,year: {$y},month: {$m}, name: \"{$name}\"});'><i class='icon-credit-card'>&nbsp;</i> Add Payment/Adjustment</a>
                </ul>
              </div>
              <input type='checkbox' name='selected_invoices' class='pull-right'>
            ";
          } else {
            $actions = "<a href='" . basename($_SERVER['REQUEST_URI']) . "?action=details&user_id={$aid}&month={$m}&year={$y}'>View/Print</a>";
          }
          // Need to descend in here to get comments.
          foreach ($val as $id => $details) {

            $agency_total += $details['total'];
            if ($details['reference_id'] != 0 || $details['total'] > 0) {
                $original_balance += $details['total'];
            }
            if ($id == 'total') {
              continue;
            }
            // Get the details of payments, add to comments column.
            // Payments are different, and wont have a date_completed.
            // Anything with a reason containing adj will show up on the comments.
            if (empty($val['date_completed']) && stripos($details['reason'],'adj') !== false) {
              $agency_comments .= "<span class='inv-comment-box'>{$details['details']}</span>";
            }
          }
          // Convert to an actual number
          $agency_total = (float) $agency_total;

          // Set the status here for agents. Current includes this month and last months dates.
          $lm = new DateTime();
          $lm->sub(new DateInterval('P1M'));
          if (($m == date('n') && $y == date('Y')) or ($m == $lm->format('n') && $y == $lm->format('Y'))) {
            $this_status = 'Current';
          } elseif ($agency_total <= 0) {
            $this_status = 'Paid';
          } else {
            $this_status = 'Overdue';
          }

          // Apply the status filters.
          if ($status && strtolower($this_status) != strtolower($status)) {
            continue;
          }
          
          if ($balance != 0) {
            if ($balance < 0 && $agency_total > $balance) {
              continue;
            }
            if ($balance > 0 && $agency_total < $balance) {
              continue;
            }
          }

          // Get email history for this invoice.
          $agency_comments .= get_invoice_email_history($invoice_id);
          if (!isset($agency_name)) {
            $agency_name = "";
          }
          if (!isset($agency_office)) {
            $agency_office = "";
          }

          // Agents have fewer columns displayed than admins.
          if (is_agent()) {
              echo "<tr class='inv-row' data-year='{$y}' data-name='{$name}' data-month='{$m}' data-user='{$aid}' data-agency='0'><td>{$actions}</td><td>{$invoice_id}</td><td>{$y}</td><td>{$month_name}</td><td>{$name}</td><td>\${$original_balance}</td><td class='balance'>\${$agency_total}</td><td>{$this_status}</td><td>{$agency_comments}</td></tr>\n";
          } else {
            // Admins here.
            echo "
              <tr class='inv-row' data-year='{$y}' data-name='{$name}' data-month='{$m}' data-user='{$aid}' data-agency='0'>
                <td>{$actions}</td>
                <td>{$invoice_id}</td>
                <td>{$y}</td>
                <td>{$month_name}</td>
                <td>{$name}</td>
                <td>\${$original_balance}</td>
                <td class='balance'>\${$agency_total}</td>
                <td>{$this_status}</td>
                <td><div style='max-width: 220px;'>{$agency_comments}</div></td>
              </tr>";
                
            // Keep track of global total.          
            $grand_total += $agency_total;  
          }
        }
      }
    }
  }
  echo "</table>";
  if(is_admin()) {
    echo "<script data-cfasync='false' type='text/javascript'>var jq = jq || []; jq.push(function(){ $('.grand-total').html('$' + '".number_format($grand_total,2)."') });</script>";
  }
  
}

// This changes the DB structure of account_items to the newer, simplified structure.
// IMPORTANT! Run only once!
function update_account_items_structure() {

  global $database;

  // Add new columns.
  $s = "
  ALTER TABLE account_items
  ADD (
    agency_id INT,
    user_id INT,
    billing_method_id INT,
    reason TEXT,
    details TEXT,
    check_date VARCHAR(255),
    check_date_received VARCHAR(255)
  )";
  $q = $database->query($s);

  // Drop running_total column.
  $s1 = "ALTER TABLE account_items DROP COLUMN running_total";
  $q1 = $database->query($s1);

  if ($s) {
    echo "Database structure of account_items updated<br>";
  } else {
    echo "Database structure of account_items not updated.<br>";
  }
}


//start added 08.01.2014 DrTech76, hook with the agent "move to agency" log
function agent_movement_pick_agency($agent_movements=array(),$curr_day,$curr_month,$curr_year,$oc_year,$oc_month,$oc_day,$oc_timestamp)
{
	$amAgency=0;
	$rollDown=false;
	
	if(!empty($agent_movements))
	{
		if(!isset($curr_year) or $curr_year==0)
		{
			$curr_year=$oc_year;
		}
		
		if(!isset($curr_month) or $curr_month==0)
		{
			$curr_month=$oc_month;
		}
		
		if(!isset($curr_day) or $curr_day==0)
		{
			$curr_day=$oc_day;
		}
		
		if(isset($agent_movements[$curr_year][$curr_month][$curr_day]) and (is_array($agent_movements[$curr_year][$curr_month][$curr_day]) and !empty($agent_movements[$curr_year][$curr_month][$curr_day])))
		{
			foreach($agent_movements[$curr_year][$curr_month][$curr_day] as $amTime=>$amAgencyID)
			{
				if($amTime>$oc_timestamp)
				{
					break;
				}
				else
				{
					$amAgency=$amAgencyID;
					if($amTime==$oc_timestamp)
					{
						break;
					}
				}
			}
			if($amAgency==0)
			{
				$rollDown=true;
			}
		}
		else
		{
			$rollDown=true;
		}
		
		
		if($rollDown===true)
		{
			$years=array_keys($agent_movements);
			$months=array_keys($agent_movements[$years[0]]);
			$days=array_keys($agent_movements[$years[0]][$months[0]]);
			
			if($days[0]<$curr_day)
			{
				$curr_day-=1;
			}
			else
			{
				$curr_day=0;
			}
			
			if($curr_day==0)
			{
				if($months[0]<$curr_month)
				{
					$curr_month-=1;
				}
				else
				{
					$curr_month=0;
				}
			}
			
			if($curr_month==0)
			{
				if($years[0]<$curr_year)
				{
					$curr_year-=1;
				}
				else
				{
					$curr_year=0;
				}
			}
			
			if($curr_year>0)
			{
				$amAgency=agent_movement_pick_agency($agent_movements,$curr_day,$curr_month,$curr_year,$oc_year,$oc_month,$oc_day,$oc_timestamp);
			}
		}
	}
	return $amAgency;
}

//end added 08.01.2014 DrTech76, hook with the agent "move to agency" log
?>
