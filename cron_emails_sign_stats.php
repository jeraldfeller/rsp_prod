<?php

	include('includes/application_top.php');
	
	$query = $database->query("SELECT equipment_item_id FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 1 and ei.equipment_status_id = 2");

$posts = array();
$average_since_ts = 0;
foreach($database->fetch_array($query) as $result){
    $id = $result['equipment_item_id'];
    $p = new Post($id);
    if ($p->getInstalledTimestamp() > $average_since_ts) {
        $posts[] = $p;
    }
}
$posts_count = 0;

foreach($posts as &$p) {
    $days = $p->getInstalledDays();
    if (empty($days)) continue;
    $posts_count++;
}

$today = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())), date("Y", tep_fetch_current_timestamp()));

$current_year = date('Y');

$query_install_month = "select most_installed_count, least_installed_count from signpost_stats where year=$current_year";
$query = $database->query($query_install_month);
$result = $database->fetch_array($query);
	if(empty($result)){

		$query_install_month = "insert into signpost_stats set least_installed_count= '0', least_installed_date = ".$today.",most_installed_count=".$posts_count.", most_installed_date = ".$today.",year=$current_year";
		$query = $database->query($query_install_month);
	}else{

			$most_installed_count = $result['most_installed_count'];
			$least_installed_count = $result['least_installed_count'];


		if($posts_count>$most_installed_count)
		{
			$query_install_month = "update signpost_stats set most_installed_count=".$posts_count.", most_installed_date = ".$today." where year=$current_year";
			$query = $database->query($query_install_month);
		}

		if($posts_count<$least_installed_count  || $least_installed_count==0)
		{
			$query_install_month = "update signpost_stats set least_installed_count=".$posts_count.", least_installed_date = ".$today." where year=$current_year";
			$query = $database->query($query_install_month);
		}
	}

echo $posts_count;
die();

?>
