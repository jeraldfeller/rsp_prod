<?php
$current_year = date('Y');
//echo strtotime('06/19/2017');
if (isset($_GET['postsstats'])) {
	$query = $database->query("SELECT year, most_installed_count, most_installed_date, least_installed_count, least_installed_date from signpost_stats ORDER BY year DESC");
	if ($_GET['postsstats'] == 1) {
		?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody>
		<tr>
				<td align="left" class="main" style="width:400px; font-size:14px;"><b># of Most Installed Signposts by Year</b></td>
			</tr>
			<tr>
				<td align="left" class="main">&nbsp;</td>
			</tr>
		<?php
		while ($year_stats = $database->fetch_array($query)) {
			?>
			<tr>
				<td align="left" class="main" style="font-size:12px;"><b><?php echo $year_stats['year']; ?></b></td>
			</tr>
			<tr>
				<td class="main" style="width:200px"># of Most Installed Signposts:</td>
				<td><?php echo $year_stats['most_installed_count']; ?> (<?php echo date('m/d/Y', $year_stats['most_installed_date']); ?>)</td>
			</tr>
			<tr>
				<td align="left" class="main">&nbsp;</td>
			</tr>
			<?php
		}
	} else {
			?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody>
		<tr>
				<td align="left" class="main" style="width:400px; font-size:14px;"><b># of Least Installed Signposts by Year</b></td>
			</tr>
			<tr>
				<td align="left" class="main">&nbsp;</td>
			</tr>
		<?php
		while ($year_stats = $database->fetch_array($query)) {
			?>
			<tr>
				<td align="left" class="main" style="font-size:12px;"><b><?php echo $year_stats['year']; ?></b></td>
			</tr>
			<tr>
				<td class="main" style="width:200px"># of Least Installed Signposts:</td>
				<td><?php echo $year_stats['least_installed_count']; ?> (<?php echo date('m/d/Y', $year_stats['least_installed_date']); ?>)</td>
			</tr>
			<tr>
				<td align="left" class="main">&nbsp;</td>
			</tr>
			<?php
		}
	}
	?>
	
		<tr>
			<td align="left" class="main" style="font-size:12px;"><a href="/admin_service_stats.php">Back</a></td>
		</tr>
	</tbody>
	</table>
	<?php

} else {

//tst

$average_install_length_since = tep_fill_variable('average_install_length_since', 'get');
$send_email = tep_fill_variable('send_email', 'get');
//echo $average_install_length_since;
$average_since_ts = strtotime($average_install_length_since);


//bgdn
if (empty($average_since_ts)) {
    $average_since_ts = 0;
	$cur_year = date('Y', strtotime('now'));
	$cur_year_stamp = mktime(0,0,0,1,0,$cur_year);//strtotime($cur_year);
	$next_year_stamp = mktime(0,0,0,1,0,$cur_year+1);//strtotime($cur_year." +1 year");
}
else {
	$cur_year = date('Y', $average_since_ts);
	$cur_year_stamp = mktime(0,0,0,1,0,$cur_year);

	$next_year_stamp = mktime(0,0,0,1,0,$cur_year+1);//strtotime($cur_year." +1 year");
}


if ($send_email) {
    $url = "http://" . $_SERVER['SERVER_NAME'] . "/lib/reports/service_stats_wrapper.php5?average_install_length_since=" . urlencode($average_install_length_since);
    $email_results = file_get_contents($url);
    $email_results = "<div class='alert alert-info'>{$email_results}</div>\n";
} else {
    $email_results = "";
}

// Calculate Post Installed Time stats
// Get a list of all the posts that are installed

$query = $database->query("SELECT equipment_item_id FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 1 and ei.equipment_status_id = 2 ORDER BY equipment_item_id ASC");

//$query = $database->query("SELECT equipment_item_id FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 1 and ei.equipment_status_id = 2");

//$query = $database->query("SELECT ei.equipment_item_id FROM " . TABLE_EQUIPMENT_ITEMS . " ei INNER JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) INNER JOIN equipment_to_orders eto ON (ei.equipment_item_id = eto.equipment_item_id) WHERE e.equipment_type_id = 1 and eto.equipment_status_id = 2 GROUP BY equipment_item_id");

$posts = array();

while ($result = $database->fetch_array($query)) {
    $id = $result['equipment_item_id'];
    $p = new Post($id);
/*	echo "<pre>";
	print_r($p);
	echo "<pre>";exit;*/
    if ($p->getInstalledTimestamp() > $average_since_ts) {
        $posts[] = $p;
	//	echo $id."\n";
    }
}
//die();
$posts_count = 0;
$posts_lte_120 = 0;
$posts_121_240 = 0;
$posts_241_360 = 0;
$posts_361_540 = 0;
$posts_541_720 = 0;
$posts_gte_721 = 0;

 //new
$white_pvc = 0;
$black_pvc = 0;
$yellow_pvc = 0;
$white_wood = 0;
$black_wood = 0;
$yellow_wood = 0;
$sign_sum = 0;

$days_array = array();
$agents_array = array();

foreach($posts as &$p) {
    $days = $p->getInstalledDays();
    if (empty($days)) continue;
    $posts_count++;
    $days_array[] = $days;

	$namez = $p->getName();
	if($namez=='SignPost - Yellow, Wood') $yellow_wood++;
	elseif($namez=='SignPost - Black, Wood') $black_wood++;
	elseif($namez=='SignPost - White, Wood') $white_wood++;
	elseif($namez=='SignPost - Black, PVC') $black_pvc++;
	elseif($namez=='SignPost - White, PVC') $white_pvc++;
	elseif($namez=='SignPost - Yellow, PVC') $yellow_pvc++;
	
    if ($days <= 120) $posts_lte_120++;
    elseif ($days >= 121 && $days <= 240) $posts_121_240++;     
    elseif ($days >= 241 && $days <= 360) $posts_241_360++;     
    elseif ($days >= 361 && $days <= 540) $posts_361_540++;     
    elseif ($days >= 541 && $days <= 720) $posts_541_720++;     
    elseif ($days >= 721) $posts_gte_721++;

    $aid = $p->getAgentId();
    if(array_key_exists($aid, $agents_array)) {
        $agents_array[$aid] = $agents_array[$aid] + 1;
    } else {
        $agents_array[$aid] = 1;
    }
}
$sign_sum = $posts_count;  //new
$agent_count = count($agents_array);

$agents_with_1 = 0;
$agents_with_2 = 0;
$agents_with_5 = 0;
$agents_with_10 = 0;
$agents_over_10 = 0;

foreach($agents_array as &$a) {
    if ($a == 1) {
        $agents_with_1++;
    } elseif ($a == 2) {
        $agents_with_2++;
    } elseif ($a <= 5) {
        $agents_with_5++;
    } elseif ($a <= 10) {
        $agents_with_10++;
    } elseif ($a > 10) {
        $agents_over_10++;
    }
}

unset($posts);
unset($agents_array);

rsort($days_array);
if(!empty($days_array))
{
	$active_average = array_sum($days_array) / count($days_array);
	$active_median = $days_array[round(count($days_array)/2)-1];
}
else
{
	$active_average=0;
	$active_median=0;
}


//bgdn
$tryit = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 	

$todayz = $average_since_ts==0 ? $tryit : $average_since_ts;
	
	//$stub = da
	
	$tomorrowz = strtotime('+1 day', $todayz);
	
	/*echo $todayz;
	echo '<br>';
	echo 'second';
	echo '<br>';
	echo $tomorrowz;*/


///doing % stats
$query = $database->query("SELECT count(od.order_id) as count FROM " . TABLE_ORDERS . " o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "' AND od.equipment_name = 'SignPost - White, PVC' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count2 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "' AND od.equipment_name = 'SignPost - Black, PVC' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count3 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "' AND od.equipment_name = 'SignPost - Yellow, PVC' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count4 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "' AND od.equipment_name = 'SignPost - White, Wood' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count5 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "' AND od.equipment_name = 'SignPost - Black, Wood' AND o.order_type_id=1 UNION SELECT count(od.order_id) as count6 FROM `orders` o INNER JOIN `equipment_to_orders` od ON (o.order_id = od.order_id) WHERE o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "' AND od.equipment_name = 'SignPost - Yellow, Wood' AND o.order_type_id=1");
//$i=0;
	while($resultz = $database->fetch_array($query))
	{
		$result[] = $resultz['count'];
	}
	
	//echo date('m/d/Y','1187637756');
	/*
	$white_pvc = $result[0];
	$black_pvc = $result[1];
	$yellow_pvc = $result[2];
	$white_wood = $result[3];
	$black_wood = $result[4];
	$yellow_wood = $result[5];
	
	$sign_sum = $black_pvc+$white_pvc+$yellow_pvc+$black_wood+$white_wood+$yellow_wood;*/
	

//////////////
	$query = $database->query("SELECT most_installed_count, most_installed_date, least_installed_count, least_installed_date from signpost_stats where year=$current_year");
	$result = $database->fetch_array($query);
	
	$count_most = $result['most_installed_count'];
	$most_name = date('m/d/Y', $result['most_installed_date']);
	
	$count_lest = $result['least_installed_count'];
	$lest_name = date('m/d/Y', $result['least_installed_date']);
	
//more bgdn	

	$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 	
	$cur_year = date('Y', strtotime('now'));
	$cur_year_table = mktime(0,0,0,1,0,$cur_year);
	$prev_year_table = mktime(0,0,0,1,0,$cur_year-1);
	$long_prev_year_table = mktime(0,0,0,1,0,$cur_year-2);
	$border_prev_year_table = mktime(0,0,0,1,0,$cur_year-3);
	
	$todayz = $average_since_ts==0 ? $today : $average_since_ts;
	
	//$stub = da
	
	$tomorrowz = strtotime('+1 day', $todayz);
	
	$result = array();
	
	$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND service_level_id = 1 AND o.order_type_id=1 UNION SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND service_level_id = 2 AND o.order_type_id=1 UNION SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.service_level_id = 3 AND order_type_id=1");
	
	$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.service_level_id = 1 AND o.order_type_id=1");
	$result = $database->fetch_array($query);
	$silver_level = $result['cnt'];
	
	$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.service_level_id = 2 AND o.order_type_id=1 ");
	$result = $database->fetch_array($query);
	$gold_level = $result['cnt'];
	
	$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.service_level_id = 3 AND o.order_type_id=1");
	$result = $database->fetch_array($query);
	$platinum_level = $result['cnt'];

	$levels_sum = $silver_level+$gold_level+$platinum_level;
	

	
/////////////


	//bgdn_old
	
	$query = $database->query("SELECT o.order_id FROM " . TABLE_ORDERS . " o INNER JOIN `equipment_to_orders` od ON od.order_id=o.order_id WHERE o.date_completed > '" . $average_since_ts . "' AND od.equipment_group_id=1 AND o.order_type_id=1 GROUP BY o.order_id");
	$result = $database->num_rows($query);
	$count_bbox = $result;
	
	$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.order_type_id=1");
	$result = $database->fetch_array($query);
	$count_all_with_bbox = $result['cnt'];
	
	
	//bgdn_new
	/*$query = $database->query("SELECT count(ei.equipment_item_id) as cnt FROM equipment_items ei JOIN equipment e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 3 and ei.equipment_status_id = 2 and ei.equipment_item_id in (select z.equipment_item_id from equipment_to_orders z inner join orders o on o.order_id=z.order_id where o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "')");
	$result = $database->fetch_array($query);
	$count_bbox = $result['cnt'];
	
	$query = $database->query("SELECT count(ei.equipment_item_id) as cnt FROM equipment_items ei JOIN equipment e ON (e.equipment_id = ei.equipment_id) WHERE ei.equipment_status_id = 2 and ei.equipment_item_id in (select z.equipment_item_id from equipment_to_orders z inner join orders o on o.order_id=z.order_id where o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "')");
	$result = $database->fetch_array($query);
	$count_all_with_bbox = $result['cnt'];*/

////////////

	//bgdn_old
	
	//Install Rider
	
	$query = $database->query("SELECT od.order_id as oid, count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o INNER JOIN `equipment_to_orders` od ON od.order_id=o.order_id WHERE o.date_completed > '" . $average_since_ts . "' AND (od.equipment_group_id=2 OR od.equipment_group_id=3) AND o.order_type_id=1 GROUP BY oid HAVING cnt=1");
	$result = $database->num_rows($query);
	$count_one_riders = $result;
	
	$query = $database->query("SELECT count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o WHERE o.date_completed > '" . $average_since_ts . "' AND o.order_type_id=1");
	$result = $database->fetch_array($query);
	
	$query = $database->query("SELECT od.order_id as oid, count(o.order_id) as cnt FROM " . TABLE_ORDERS . " o INNER JOIN `equipment_to_orders` od ON od.order_id=o.order_id WHERE o.date_completed > '" . $average_since_ts . "' AND (od.equipment_group_id=2 OR od.equipment_group_id=3) AND o.order_type_id=1 GROUP BY oid HAVING cnt=2");
	
	$count_two_riders = 0;
	while($result = $database->fetch_array($query))
	{
		$count_two_riders++;
	}
	
	/*$result = $database->num_rows($query);
	
	$count_two_riders =$result;*/
	
	//print_r($result);
	
	//$count_two_riders = count($result);
	
	/*$query = $database->query("SELECT count(ei.equipment_item_id) as cnt FROM equipment_items ei JOIN equipment e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 2 and ei.equipment_status_id = 2 and ei.equipment_item_id in (select z.equipment_item_id from equipment_to_orders z inner join orders o on o.order_id=z.order_id where o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "')");
	$result = $database->fetch_array($query);
	$count_one_riders = $result['cnt'];
	
	$query = $database->query("SELECT count(ei.equipment_item_id) as cnt FROM equipment_items ei JOIN equipment e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 2 and ei.equipment_status_id = 2 and ei.equipment_item_id in (select z.equipment_item_id from equipment_to_orders z inner join orders o on o.order_id=z.order_id where o.date_completed BETWEEN '" . $average_since_ts . "' AND '" . $tomorrowz . "')");
	$result = $database->fetch_array($query);
	$count_two_riders = $result['cnt'];*/

///////////



//////////	
	
	
//SignPost


// Get a list of all the orders that have been successfully removed

$query = $database->query("SELECT od.number_of_posts, CEIL((o.date_completed-o2.date_completed)/(24*60*60)) AS days FROM " . TABLE_ORDERS . " o JOIN " . TABLE_ORDERS . " o2 ON (o.address_id = o2.address_id) JOIN " . TABLE_ORDERS_DESCRIPTION . " od ON (o2.order_id = od.order_id) WHERE o.order_type_id = 3 AND o.order_status_id = 3 AND o2.order_type_id = 1 AND o2.order_status_id = 3 AND o.date_completed > " . $average_since_ts . " AND o2.date_completed > 0");

unset($days_array);
$days_array = array();
while ($result = $database->fetch_array($query)) {
    $num_posts = $result['number_of_posts'];
    for ($i=0; $i<$num_posts; $i++) {
        $days_array[] = $result['days'];
    }
}

rsort($days_array);
if(!empty($days_array))
{
	$removed_average = array_sum($days_array) / count($days_array);
	$removed_median = $days_array[round(count($days_array)/2)-1];
}
else
{
	$removed_average=0;
	$removed_median=0;
}

$user_id = $user->fetch_user_id();
$user_group_id = $user->fetch_user_group_id();


?>

<script language="javascript">

function orderDetails(minDays, maxDays) {

    // Update Modal

    minDays = isNaN(minDays)?"":+minDays;

    maxDays = isNaN(maxDays)?"":+maxDays;

    since = $('#since').val();

    console.log(minDays + " - " + maxDays);

    $("table#details").addClass("hidden");

    $("div#loading").removeClass("hidden");

    $('tr.order-details').remove();

    $.getJSON( "/lib/orders/open_orders_json.php5?min=" + minDays + "&max=" + maxDays + "&since=" + since, function( data ) {

      $.each( data, function( key, val ) {

        val.name = val.name.replace("SignPost - ", "");

        if (val.removal_order_id == 0) {

            removal = "-";

        } else {

            removal = "<a target='_blank' href='/admin_orders.php?oID=" + val.removal_order_id + "&page_action=view'>#" + val.removal_order_id + "</a>";

        }

        $("table#details>tbody").append($("<tr class='order-details'><td>" + val.address_string + "</td><td><a target='_blank' href='/admin_equipment_items.php?equipment_type_id=" + val.equipment_type_id + "&equipment_id=" + val.equipment_id + "&page_action=view&eID=" + val.equipment_item_id + "'>" + val.name + "</a></td><td><a target='_blank' href='/admin_orders.php?oID=" + val.install_order_id + "&page_action=view'>#" + val.install_order_id + "</a></td><td>" + removal + "</td></tr>"));

      });

      $("div#loading").addClass("hidden");

      $("table#details").removeClass("hidden");

    });

    $('#orderDetailsModal').modal('toggle');

}



$(document).ready(function () {

    $(".datepicker").datepicker();

});

</script>



<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

        <h4 class="modal-title" id="myModalLabel">Order Details</h4>

      </div>

      <div class="modal-body">

            <div id="loading" style='text-align: center; width: 100%; height: 100%'><h4><span class='icon-spinner icon-spin'>&nbsp;</span> Loading Addresses...</h4></div>

            <table id="details" class="table table-condensed table-striped">

                <thead>

                    <th>Address</th>

                    <th>Post Type</th>

                    <th>Install</th>

                    <th>Removal</th>

                <thead>

                <tbody>

                </tbody>

            </table>

      </div>

      <div class="modal-footer">

      </div>

    </div>

  </div>

</div>



<?php echo $email_results; ?>

<table width="100%" cellspacing="0" cellpadding="0" border="0">

<?php

switch($user_group_id) {

	case '1': 

		//Agent.

	break;

	case '2': 

		//Admin.

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

        while ($result = $database->fetch_array($query)) {

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

					

					$query_red_flag = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_issue = '1'";

					

					$query = $database->query($query_red_flag);

					$result = $database->fetch_array($query);

                    $red_flag_orders = $result['count'];



					$query_on_hold = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id = '5' and o.address_id = a.address_id ";

					

					$query = $database->query($query_on_hold);

					$result = $database->fetch_array($query);

                    $on_hold_orders = $result['count'];



                    // Miss utility

                    $query_miss_utility_open = "select count(o.order_id) as count ";

                    $query_miss_utility_open.= "from " . TABLE_ORDERS . " o ";

                    $query_miss_utility_open.= "left join " . TABLE_ORDERS_MISS_UTILITY . " omu on (o.order_id = omu.order_id) ";

                    $query_miss_utility_open.= "where o.order_status_id < 3 and omu.contacted = 0 ";

                    $query_miss_utility_open.= "and not (omu.agent_requested = 0 and (omu.has_gas_lamp = 0 or omu.has_lamp = 0))";



					$muq = $database->query($query_miss_utility_open);

					$result = $database->fetch_array($muq);

                    $miss_utility_open = ($result['count'] > 0) ? $result['count'] : 0;



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

				/*	if(BILLING_METHOD==null || in_array(BILLING_METHOD, array(1,3)))
					{*/
	                    // Credit Card Totals

	                    $cc_today_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '1' AND date_added >= " . strtotime("today");

	                    $cc_yesterday_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS .

	                                        " WHERE billing_method_id = '1' AND date_added >= " . strtotime("yesterday") . " AND date_added < " . strtotime("today");
						
						$cc_2_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '1' AND date_added >= " . strtotime("today - 2 days") . " AND date_added < " . strtotime("yesterday");						

	                    $cc_7_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '1' AND date_added >= " . strtotime("today - 7 days");

	                    $cc_30_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '1' AND date_added >= " . strtotime("today - 30 days");

	                    // Loop through each interval, run the SQL, and assign the total to $cc_today_total, $cc_yesterday_total, etc.

	                    foreach (array('today', 'yesterday', '2_days', '7_days', '30_days') as $interval) {

	                        $sql = ${"cc_{$interval}_sql"};

	                        $query = $database->query($sql);

	                        $result = $database->fetch_array($query);

	                        ${"cc_{$interval}_total"} = '$'.number_format(($result ? $result['total'] : 0), 2);
							
							${"xx_{$interval}_total"} = $result ? $result['total'] : 0;

	                    }
						
						//bgdn
						
						$i_today_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '3' AND date_added >= " . strtotime("today");
						
						$i_yesterday_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS .

	                                        " WHERE billing_method_id = '3' AND date_added >= " . strtotime("yesterday") . " AND date_added < " . strtotime("today");
						
						$i_2_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '3' AND date_added >= " . strtotime("today - 2 days") . " AND date_added < " . strtotime("yesterday");						

	                    $i_7_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '3' AND date_added >= " . strtotime("today - 7 days");

	                    $i_30_days_sql = "SELECT SUM(amount) AS total FROM " . TABLE_TRANSACTIONS . " WHERE billing_method_id = '3' AND date_added >= " . strtotime("today - 30 days");
						
						foreach (array('today', 'yesterday', '2_days', '7_days', '30_days') as $interval) {

	                        $sql = ${"i_{$interval}_sql"};

	                        $query = $database->query($sql);

	                        $result = $database->fetch_array($query);
							
							
							//print_r($result);

	                       // ${"cc_{$interval}_total"} = '$'.number_format(($result ? $result['total'] : 0), 2);
						   
						   if($result['total']>0) {
							//echo ${"xx_{$interval}_total"} * 100;
							//echo ((${"cc_{$interval}_total"} * 100) / $result['total']);
							${"cc_{$interval}_percentage"} = number_format(	((${"xx_{$interval}_total"} * 100) / ($result['total']+(${"xx_{$interval}_total"}))),2).'%';
						   }
						   else {
							   ${"cc_{$interval}_percentage"} = '0%';
						   }
	                    }
						

					/*}*/
					

		?>
		

		<tr>

			<td align="left" class="main">&nbsp;</td>

		</tr>

		<tr>

			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>

		</tr>
        <?php $today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp()));  ?>
        <tr>
          <td align="left" class="main"><b>ORDERED TODAY:</b> &nbsp;&nbsp;&nbsp;
            
            Installs ( <a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&show_between_end=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&submit_value.x=41&submit_value.y=12" class="StatLink">
            <?=$count_install_new_db?>
            </a> ) &nbsp;&nbsp;
            
            Removals ( <a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&show_between_end=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&submit_value.x=41&submit_value.y=12" class="StatLink">
            <?=$count_removal_new_db?>
            </a> ) &nbsp;&nbsp;
            
            Service Calls ( <a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=&order_type=2&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&show_between_end=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&submit_value.x=41&submit_value.y=12" class="StatLink">
            <?=$count_svc_new_db?>
            </a> ) </td>
        </tr>

		<tr>

			<td align="left" class="main"><b>RESCHEDULED TODAY:</b> &nbsp;&nbsp;&nbsp;

						Removals Rescheduled ( <?=$rescheduled_count?> ) &nbsp;&nbsp;

						Pushed Back ( <?=$pushed_back_count?> ) &nbsp;&nbsp;

						Moved Up ( <?=$moved_up_count?> ) &nbsp;&nbsp;

			</td>

		</tr>
		
		        <tr>

            <td align="left" class="main"><b>CREDIT CARD TOTALS:</b>&nbsp;&nbsp;&nbsp;&nbsp;Today/Yestderday/Two Days Ago/Last 7/Last 30: &nbsp;&nbsp;
				<?php echo $cc_today_total; ?>&nbsp;&nbsp;&nbsp;&nbsp;

                <?php echo $cc_yesterday_total; ?>&nbsp;&nbsp;&nbsp;&nbsp;
				
				 <?php echo $cc_2_days_total; ?>&nbsp;&nbsp;&nbsp;&nbsp;

                <?php echo $cc_7_days_total; ?>&nbsp;&nbsp;&nbsp;&nbsp;

                <?php echo $cc_30_days_total; ?>
            </td>

        </tr>

		<tr>
		
			<td align="left" class="main"><b>CREDIT CARD PERCENTAGE:</b>&nbsp;&nbsp;&nbsp;&nbsp;Today/Yestderday/Two Days Ago/Last 7/Last 30: &nbsp;&nbsp;
				<?php echo $cc_today_percentage; ?>&nbsp;&nbsp;&nbsp;&nbsp;

                <?php echo $cc_yesterday_percentage; ?>&nbsp;&nbsp;&nbsp;&nbsp;
				
				 <?php echo $cc_2_days_percentage; ?>&nbsp;&nbsp;&nbsp;&nbsp;

                <?php echo $cc_7_days_percentage; ?>&nbsp;&nbsp;&nbsp;&nbsp;

                <?php echo $cc_30_days_percentage; ?>
            </td>
		
		</tr>
		
		
        <tr>

            <td align="left" class="main">&nbsp;</td>

        </tr>
		
		<?php 

        if(BILLING_METHOD==null || in_array(BILLING_METHOD,array(1,3)))

        {

        ?>


        

        <?php 

        }

        ?>
		<tr>

			<td align="left" class="main"><hr /><b>SIGNPOST AND ACCESSORY STATISTICS:</b></td>

		</tr>

		<tr>

			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>

		</tr>

		<tr>

			<td width="100%">

				<table width="100%" cellspacing="3" cellpadding="0" border="0">

					<tr>

						<td width="320"><img src="images/pixel_trans.gif" height="1" /></td>

						<td ></td>

					</tr>

					<tr>

						<td class="main" valign="top">Agents with Currently Installed Posts:</td>

                        <td align="left" class="main">

                            <table cellspacing="3" cellpadding="3" border="1">

                            <thead>

                                <th># of Posts<th>1</th><th>2</th><th>3-5</th><th>6-10</th><th>&ge;11</th><th>Total</th>

                            </thead>

                            <tbody>

                                <td style="text-align: center; width: 75px;"># of Agents</td>

                                <td style="text-align: center; width: 50px;"><?=$agents_with_1?></td>

                                <td style="text-align: center; width: 50px;"><?=$agents_with_2?></td>

                                <td style="text-align: center; width: 50px;"><?=$agents_with_5?></td>

                                <td style="text-align: center; width: 50px;"><?=$agents_with_10?></td>

                                <td style="text-align: center; width: 50px;"><?=$agents_over_10?></td>

                                <td style="text-align: center; width: 50px;"><?=$agent_count?></td>

                            </tbody>

                            </table>

                        </td>

                    </tr>

					<tr>

                        <td colspan="2"><br /></td>

                    </tr>

					<tr>

						<td class="main" valign="top">Currently Installed Posts:</td>

                        <td align="left" class="main">

                            <table cellspacing="3" cellpadding="3" border="1">

                            <thead>

                                <th># of Days<th>&le;120</th><th>121-240</th><th>241-360</th><th>361-540</th><th>541-720</th><th>&ge;721</th><th>Total</th>

                            </thead>

                            <tbody>

                                <td style="text-align: center; width: 75px;"># of Posts</td>

                                <td style="text-align: center; width: 50px;"><a href="javascript:orderDetails(0,120);"><?=$posts_lte_120?></a></td>

                                <td style="text-align: center; width: 50px;"><a href="javascript:orderDetails(121,240);"><?=$posts_121_240?></a></td>

                                <td style="text-align: center; width: 50px;"><a href="javascript:orderDetails(241,360);"><?=$posts_241_360?></a></td>

                                <td style="text-align: center; width: 50px;"><a href="javascript:orderDetails(361,540);"><?=$posts_361_540?></a></td>

                                <td style="text-align: center; width: 50px;"><a href="javascript:orderDetails(541,720);"><?=$posts_541_720?></a></td>

                                <td style="text-align: center; width: 50px;"><a href="javascript:orderDetails(721);"><?=$posts_gte_721?></a></td>

                                <td style="text-align: center; width: 50px;"><?=$posts_count?></td>

                            </tbody>

                            </table>

                        </td>

                    </tr>

                    <tr>

                        <td colspan="2"><br /></td>

					</tr>

					<tr>

                    <td class="main">Average Time Installed (Currently Installed):<td class="main"><b>Mean:</b> <?=number_format($active_average,2)?>&nbsp;days. &nbsp;&nbsp;&nbsp; <b>Median:</b> <?=number_format($active_median,2)?> days.</td>

					</tr>

                    <td class="main">Average Time Installed (On Post Removal):<td class="main"><b>Mean:</b> <?=number_format($removed_average,2)?>&nbsp;days. &nbsp;&nbsp;&nbsp; <b>Median:</b> <?=number_format($removed_median,2)?> days.</td>

					</tr>
					
					<tr>
					  <td align="left" class="main">&nbsp;</td>
					</tr>
					
					<tr>

                    <td class="main"># of Most Installed Signposts:<td class="main"><?php echo $count_most; ?> (<?php echo $most_name; ?>) - <a href="/admin_service_stats.php?postsstats=1">Previous Years</a></td>

					</tr>
					
					<tr>

                    <td class="main"># of Least Installed Signposts:<td class="main"><?php echo $count_lest; ?> (<?php echo $lest_name; ?>) - <a href="/admin_service_stats.php?postsstats=2">Previous Years</a></td>

					</tr>
					
					
					<tr>
					  <td align="left" class="main">&nbsp;</td>
					</tr>

					<tr>

                    <td class="main">% of installed signposts &nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td class="main">
					White PVC (<? echo number_format(($white_pvc*100)/$sign_sum,2) ?>%)&nbsp;&nbsp;&nbsp;&nbsp;
					
					Black PVC (<? echo number_format(($black_pvc*100)/$sign_sum,2) ?>%)&nbsp;&nbsp;&nbsp;&nbsp;
					
					Yellow PVC (<? echo number_format(($yellow_pvc*100)/$sign_sum,2) ?>%)&nbsp;&nbsp;&nbsp;&nbsp;
					
					White wood (<? echo number_format(($white_wood*100)/$sign_sum,2) ?>%)&nbsp;&nbsp;&nbsp;&nbsp;
					
					Black wood (<? echo number_format(($black_wood*100)/$sign_sum,2) ?>%)&nbsp;&nbsp;&nbsp;&nbsp;
					
					Yellow wood (<? echo number_format(($yellow_wood*100)/$sign_sum,2) ?>%)</td>

					</tr>
					
					<tr>
					  <td align="left" class="main">&nbsp;</td>
					</tr>
					
					<tr>

                    <td class="main">% of installs for each service level  &nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td class="main">
					Silver (<? echo number_format(($silver_level*100)/$levels_sum,2) ?>%)&nbsp;&nbsp;&nbsp;&nbsp;
					
					Gold (<? echo number_format( (($gold_level*100)/$levels_sum),2) ?>%)&nbsp;&nbsp;&nbsp;&nbsp;
					
					Platinum (<? echo number_format(($platinum_level*100)/$levels_sum,2) ?>%)&nbsp;&nbsp;&nbsp;&nbsp;
					</td>

					</tr>
					
					<tr>
					  <td align="left" class="main">&nbsp;</td>
					</tr>
					
					<tr>

                    <td class="main"># of installs that have a BBox installed: <?php echo $count_bbox; ?>  &nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td class="main">
					% of installs that have a BBox installed: <?php echo number_format((($count_bbox*100)/$count_all_with_bbox),2); ?>%
					</td>

					</tr>
					
					<tr>
					  <td align="left" class="main">&nbsp;</td>
					</tr>
					
					<tr>

                    <td class="main"># of installs that have one rider installed: <?php echo $count_one_riders; ?>  &nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td class="main">
					% of installs that have one rider installed: <?php echo number_format((($count_one_riders*100)/$count_all_with_bbox),2); ?>%
					</td>

					</tr>
					
					<tr>

                    <td class="main"># of installs that have two riders installed: <?php echo $count_two_riders; ?>  &nbsp;&nbsp;&nbsp;&nbsp;
					</td> 
					<td class="main">
					% of installs that have two riders installed: <?php echo number_format((($count_two_riders*100)/$count_all_with_bbox),2); ?>%
					</td>

					</tr>
					

                    <tr>

                        <td colspan="2"><br /></td>

					<tr>

					<form action="" method="get">

					<tr>

                        <td class="main">Filter Posts Installed or Removed Since:

                        <td>

                            <input type="text" id="since" class="datepicker" name="average_install_length_since" value="<?php echo $average_install_length_since; ?>" size="7" />

                            &nbsp;&nbsp;&nbsp;

                            <?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?>

                            &nbsp;&nbsp;&nbsp;

                            <?php echo tep_create_button_submit('send_email', 'Send Email', ' name="send_email"'); ?>

                        </td>

					</tr>

					</form>

				</table>

			</td>

		</tr>


		<tr>

			<td align="left" class="main"><hr /></td>

		</tr>

		<tr>

			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>

		</tr>


		<tr>
			<td align="left" class="main" style="font-size:12px;"><b>MONEY STATISTICS:</b></td>
		</tr>
		
<?php

		$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())); 

		$tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 

		$month_first_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp()));
		
		$month_last_date_link = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 0, date("Y", tep_fetch_current_timestamp())); 

		$year_first_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp())); 

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
		
		?>
		
	<?php
		
			
			$previous_month_start = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())-1, 1, date("Y", tep_fetch_current_timestamp())); 
			$previous_month_end = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp()));
			$previous_month_end_link = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 0, date("Y", tep_fetch_current_timestamp())); 

			$query_previous_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.order_status_id = '3' and o.date_completed >= '" . $previous_month_start . "' and o.date_completed < '" . $previous_month_end . "'";
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
			
			
		?>

<!--added-->
<tr>
  <td align="left" class="main">&nbsp;</td>
</tr>
<tr>
			<td align="left" class="main"><b>Current Year:</b></td>
		</tr>
<tr>
	
			<td width="100%">
				<table width="100%" cellspacing="3" cellpadding="0" style="padding-left:15px;" border="0">
					<tr>
						<td class="main" width="6%" >Today (placed)</td>

						<td class="main" width="16%" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&show_between_end=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$count_install_today?></a>&nbsp;)</td>

						<td class="main" width="27%" >$ value of orders placed today:&nbsp;&nbsp;(&nbsp;<?=number_format ($value_install_today,2)?>&nbsp;)</td>

						<td class="main" width="51%" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;<? if($count_install_today>0){ echo number_format(($value_install_today/$count_install_today),2);}else{echo "0.00";} ?>&nbsp;)&nbsp;&nbsp; </td>

					</tr>
					
					<tr>
					<td class="main" width="22%">
					</td>
					<td class="main" width="22%">
					$ of CC Orders:&nbsp;&nbsp;(&nbsp;<?=number_format ($valueCC,2)?>&nbsp;)&nbsp;&nbsp;
					</td>
					<td class="main" width="22%">
					$ of Invoice Orders:&nbsp;&nbsp;(&nbsp;
					    <?=number_format ($valueIO,2)?>						  &nbsp;)
					</td>
					</tr>
					
					<tr>
						<td align="left" class="main">&nbsp;</td>
					</tr>

					<tr>

						<td class="main" width="18%">Month (placed)</td>

						<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=ordered&show_between_start=<?=date("m",$month_first_date)?>%2F<?=date("d",$month_first_date)?>%2F<?=date("Y",$month_first_date)?>&show_between_end=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$count_install_month?></a>&nbsp;)</td>

						<td class="main" >$ value of orders placed this month:&nbsp;&nbsp;(&nbsp;<?=number_format($value_install_month,2)?>&nbsp;)</td>

						<td class="main" colspan="3" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;<? if($count_install_month>0){ echo number_format(($value_install_month/$count_install_month),2);}else{echo "0.00";}?>&nbsp;)</td>

					</tr>
					
					<tr>
						<td align="left" class="main">&nbsp;</td>
					</tr>
					
					<tr>

						<td class="main" >Month (completed)</td>

						<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start=<?=date("m",$month_first_date)?>%2F<?=date("d",$month_first_date)?>%2F<?=date("Y",$month_first_date)?>&show_between_end=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$this_month_complete_count?></a>&nbsp;)</td>

						<td class="main" >$ value of orders completed this month:&nbsp;&nbsp;(&nbsp;<?=number_format($this_month_complete_value,2)?>&nbsp;)</td>

						<td class="main" colspan="3" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;<? if($this_month_complete_count>0){ echo number_format(($this_month_complete_value/$this_month_complete_count),2);}else{echo "0.00";}?></td> 

					</tr>
					
					<tr>
					<td class="main" width="22%">
					</td>
					<td class="main" width="22%">
					% of CC Orders:&nbsp;&nbsp;(&nbsp;<?=number_format ($countCC_percentage_month,2)?>%&nbsp;)&nbsp;&nbsp;
					</td>
					<td class="main" width="22%">
					% of Invoice Orders:&nbsp;&nbsp;(&nbsp;
					    <?=number_format ($countIO_percentage_month,2)?>%&nbsp;)
					</td>
					</tr>
					
					<tr>
						<td align="left" class="main">&nbsp;</td>
					</tr>

					<tr>

						<td class="main" >YTD (completed)</td>

						<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start=<?=date("m",$year_first_date)?>%2F<?=date("d",$year_first_date)?>%2F<?=date("Y",$year_first_date)?>&show_between_end=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$count_install_ytd?></a>&nbsp;)</td>

						<td class="main" >$ value of orders completed from Jan 1:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?group_by=date&order_status=3&order_type=&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start=<?=date("m",$year_first_date)?>%2F<?=date("d",$year_first_date)?>%2F<?=date("Y",$year_first_date)?>&show_between_end=<?=date("m",$today)?>%2F<?=date("d",$today)?>%2F<?=date("Y",$today)?>&submit_value.x=41&submit_value.y=12" class="StatLink"><?=number_format($value_install_ytd,2)?></a>&nbsp;)</td>

						<td class="main" colspan="3">$ value / # of installs:&nbsp;&nbsp;(&nbsp;<? if($count_install_ytd>0){ echo number_format(($value_install_ytd/$count_install_ytd),2);}else{echo "0.00";}?>&nbsp;)&nbsp;&nbsp;</td>
					</tr>
					
					<tr>
					<td class="main" width="22%">
					</td>
					<td class="main" width="22%">
					% of CC Orders:&nbsp;&nbsp;(&nbsp;<?=number_format ($countCC_percentage_year,2)?>%&nbsp;)&nbsp;&nbsp;
					</td>
					<td class="main" width="22%">
					% of Invoice Orders:&nbsp;&nbsp;(&nbsp;
					    <?=number_format ($countIO_percentage_year,2)?>%&nbsp;)
					</td>
					</tr>
					
					
					<tr>
						<td align="left" class="main">&nbsp;</td>
					</tr>
					
					 <tr>
    
                        <td class="main">Previous Month (completed):</td>
    
                        <td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start=<?=date("m",$previous_month_start)?>%2F<?=date("d",$previous_month_start)?>%2F<?=date("Y",$previous_month_start)?>&show_between_end=<?=date("m",$previous_month_end_link)?>%2F<?=date("d",$previous_month_end_link)?>%2F<?=date("Y",$previous_month_end_link)?>&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$previous_month_count?></a>&nbsp;)</td>
    
                        <td class="main" >$ value of orders completed previous month:&nbsp;&nbsp;(&nbsp;<?=number_format($previous_month_value,2)?>&nbsp;)</td>
    
                        <td class="main" colspan="3" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;<? if($previous_month_count>0){ echo number_format(($previous_month_value/$previous_month_count),2);}else{echo "0.00";}?>&nbsp;)</td>
    
                    </tr>
					
					<tr>
					<td class="main" width="22%">
					</td>
					<td class="main" width="22%">
					% of CC Orders:&nbsp;&nbsp;(&nbsp;<?=number_format ($countCC_percentage_prev_month,2)?>%&nbsp;)&nbsp;&nbsp;
					</td>
					<td class="main" width="22%">
					% of Invoice Orders:&nbsp;&nbsp;(&nbsp;
					    <?=number_format ($countIO_percentage_prev_month,2)?>%&nbsp;)
					</td>
					</tr>
					
				</table>
			</td>
		</tr>
		
		
		<tr>
			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
		</tr>
		<tr>
			<td align="left" class="main">&nbsp;</td>
		</tr>
		<?php
			$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp())-1); 
			$tomorrow = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())-1); 
			$month_first_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), 1, date("Y", tep_fetch_current_timestamp())-1); 
			$month_last_date = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())+1, 1, date("Y", tep_fetch_current_timestamp())-1);
			$month_last_date_link = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp())+1, 0, date("Y", tep_fetch_current_timestamp())-1); 
			$year_first_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp())-1);
			$year_last_date = mktime(0, 0, 0, 1, 1, date("Y", tep_fetch_current_timestamp()));
			$year_last_date_link = mktime(0, 0, 0, 12, 31, date("Y", tep_fetch_current_timestamp())-1);
	
			/*$query_install_today = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id  = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 and o.date_added >= '" . $today . "' and o.date_added < '" . $tomorrow . "'";
			$query = $database->query($query_install_today);
			$result = $database->fetch_array($query);
			$count_install_today = $result['count'];
			$value_install_today = $result['value'];*/
	
			$query_install_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id  = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $month_last_date . "'";
			$query = $database->query($query_install_month);
			$result = $database->fetch_array($query);
			$count_install_month = $result['count'];
			$value_install_month = $result['value'];
			
			/////////////
			$queryOrderCC_prev_year_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = 1 and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $month_last_date . "'";

			$query = $database->query($queryOrderCC_prev_year_month);
			$result = $database->fetch_array($query);
			$countCC_prev_year_month = $result['count'];
			
			$queryOrderIO_prev_year_month = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = 1 and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $month_first_date . "' and o.date_completed < '" . $month_last_date . "'";

			$query = $database->query($queryOrderIO_prev_year_month);
			$result = $database->fetch_array($query);
			$countIO_prev_year_month = $result['count'];

			$count_all_stuff = $countIO_prev_year_month+$countCC_prev_year_month;
			$countCC_percentage_month = ($countCC_prev_year_month * 100) / $count_all_stuff;
			($countIO_prev_year_month==0) ? $countIO_percentage_prev_year_month = 0 : $countIO_percentage_prev_year_month = 100-$countCC_percentage_month;
			////////////////
	
			$query_install_ytd = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id  = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $year_last_date . "'";
			$query = $database->query($query_install_ytd);
			$result = $database->fetch_array($query);
			$count_install_ytd= $result['count'];
			$value_install_ytd= $result['value'];
			
			
			/////////////
			$queryOrderCC_prev_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id = 1 and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $year_last_date . "'";

			$query = $database->query($queryOrderCC_prev_year);
			$result = $database->fetch_array($query);
			$countCC_prev_year = $result['count'];
			
			$queryOrderIO_prev_year = "select count(o.order_id) as count, sum(order_total) as value from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.order_status_id != '4' and o.order_status_id = '3' and o.address_id = a.address_id and o.order_type_id = '1' and o.billing_method_id IN (2,3) and o.date_completed > 0 and o.date_completed >= '" . $year_first_date . "' and o.date_completed < '" . $year_last_date . "'";

			$query = $database->query($queryOrderIO_prev_year);
			$result = $database->fetch_array($query);
			$countIO_prev_year = $result['count'];

			$count_all_stuff = $countIO_prev_year+$countCC_prev_year;
			$countCC_percentage_prev_year = ($countCC_prev_year * 100) / $count_all_stuff;
			($countIO_prev_year==0) ? $countIO_percentage_prev_year = 0 : $countIO_percentage_prev_year = 100-$countCC_percentage_prev_year;
			////////////////
			
		?>

		<tr>

			<td align="left" class="main"><b>Previous Year (Completed):</b></td>

		</tr>

		<tr>

			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>

		</tr>

		<tr>

			<td width="100%">

				<table width="100%" cellspacing="3" cellpadding="0" style="padding-left:15px;" border="0">

					<tr>

						<td class="main" >Month</td>

						<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start=<?=date("m",$month_first_date)?>%2F<?=date("d",$month_first_date)?>%2F<?=date("Y",$month_first_date)?>&show_between_end=<?=date("m",$month_last_date_link)?>%2F<?=date("d",$month_last_date_link)?>%2F<?=date("Y",$month_last_date_link)?>&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$count_install_month?></a>&nbsp;)</td>

						<td class="main" >$ value of orders placed this month:&nbsp;&nbsp;(&nbsp;<?=number_format($value_install_month,2)?>&nbsp;)</td>

						<td class="main" >$ value / # of installs:&nbsp;&nbsp;(&nbsp;<? if($count_install_month>0){ echo number_format(($value_install_month/$count_install_month),2);}else{echo "0.00";}?>&nbsp;)</td></td>

					</tr>
					
					<tr>
					<td class="main" width="22%">
					</td>
					<td class="main" width="22%">
					% of CC Orders:&nbsp;&nbsp;(&nbsp;<?=number_format ($countCC_percentage_month,2)?>%&nbsp;)
					</td>
					<td class="main" width="22%">
					% of Invoice Orders:&nbsp;&nbsp;(&nbsp;
					    <?=number_format ($countIO_percentage_prev_year_month,2)?>%&nbsp;)
					</td>
					</tr>

					<tr>

						<td class="main" >YTD</td>

						<td class="main" ># of Installs:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=completed&show_between_start=<?=date("m",$year_first_date)?>%2F<?=date("d",$year_first_date)?>%2F<?=date("Y",$year_first_date)?>&show_between_end=<?=date("m",$year_last_date_link)?>%2F<?=date("d",$year_last_date_link)?>%2F<?=date("Y",$year_last_date_link)?>&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$count_install_ytd?></a>&nbsp;)</td>

						<td class="main" >$ value of orders placed from Jan 1:&nbsp;&nbsp;(&nbsp;<?=number_format($value_install_ytd,2)?>&nbsp;)</td>
					</tr>
					
					
					<tr>
					<td class="main" width="22%">
					</td>
					<td class="main" width="22%">
					% of CC Orders:&nbsp;&nbsp;(&nbsp;<?=number_format ($countCC_percentage_prev_year,2)?>%&nbsp;)
					</td>
					<td class="main" width="22%">
					% of Invoice Orders:&nbsp;&nbsp;(&nbsp;
					    <?=number_format ($countIO_percentage_prev_year,2)?>%&nbsp;)
					</td>
					</tr>

					<tr>
					
				</table>
			</td>
		</tr>
		<tr>
			<td align="left" class="main">&nbsp;</td>
		</tr>
		<tr>
			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
		</tr>
		
		<?php

		$query_install = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.date_added > 0 ";

		

		$query = $database->query($query_install);

		$result = $database->fetch_array($query);

		$count_install = $result['count'];



		$query_install_complete = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '1' and o.order_status_id = '3' and o.date_added > 0 ";

		

		$query = $database->query($query_install_complete);

		$result = $database->fetch_array($query);

		$count_install_complete = $result['count'];





		$query_removal = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '3' and o.date_added > 0 ";

		

		$query = $database->query($query_removal);

		$result = $database->fetch_array($query);

		$count_removal = $result['count'];



		$query_removal_complete = "select count(o.order_id) as count from " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a , " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_USERS . " u where o.order_type_id = ot.order_type_id and o.user_id = u.user_id and o.order_status_id = os.order_status_id and o.address_id = a.address_id and o.order_type_id = '3' and o.order_status_id = '3' and o.date_added > 0 ";

		

		$query = $database->query($query_removal_complete);

		$result = $database->fetch_array($query);

		$count_removal_complete = $result['count'];



		?>

		<tr>

			<td align="left" class="main"><b>OVERALL DATABASE SUMMARY:</b></td>

		</tr>

		<tr>

			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>

		</tr>

		<tr>

			<td width="100%">

				<table width="100%" cellspacing="3" cellpadding="0" style="padding-left:15px;" border="0">

					<tr>

						<td class="main" width="33%" >Total # of Installs:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end=&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$count_install?></a>&nbsp;)</td>

						<td class="main" width="33%" >Total # of Removals:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end=&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$count_removal?></a>&nbsp;)</td>

					</tr>

					<tr>

						<td class="main" width="33%" >Total # of Completed Installs:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=3&order_type=1&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end=&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$count_install_complete?></a>&nbsp;)</td>

						<td class="main" width="33%" >Total # of Completed Removals:&nbsp;&nbsp;(&nbsp;<a href="<?=FILENAME_ADMIN_ORDERS?>?order_status=3&order_type=3&show_house_number=&show_street_name=&show_city=&show_zip=&show_order_id=&list_method=1&show_address_id=&agent_id=&agency_id=&service_level_id=&inserted_order_type_id=&installer_id=&active=1&show_between_type=scheduled&show_between_start=&show_between_end=&submit_value.x=41&submit_value.y=12" class="StatLink"><?=$count_removal_complete?></a>&nbsp;)</td>

					</tr>

				</table>

			</td>

		</tr>
		
		<tr>
			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
		</tr>
		
		<tr>
        <td align="left" class="main" style="font-size:12px;"><hr /><b>ORDER DISTRIBUTION MAP:</b>&nbsp;&nbsp;<em>Geographical distribution of Install Orders scheduled from <?php echo date('n/d/Y', strtotime('1 year ago')) . " to " . date('n/d/Y');?></em></td>
		</tr>
		<tr>
			<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
		</tr>
		<tr>
            <td>
            <iframe src="/lib/charts/orders_distribution_map.php5" width="100%" height="600"></iframe>
            </td>
		</tr>
			<tr>
				<td align="left" class="main">&nbsp;</td>
			</tr>
		<?php
	break;
	case '3': 
		//Installer.
    break;
}
?>
</table>
<?php
}
?>