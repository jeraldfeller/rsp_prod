<?

	include('includes/application_top.php');
	
	$query = $database->query("SELECT equipment_item_id FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) WHERE e.equipment_type_id = 1 and ei.equipment_status_id = 2");

$posts = array();
$average_since_ts = 0;
while ($result = $database->fetch_array($query)) {
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


$query_install_month = "select most_installed_count, least_installed_count from signpost_stats where year=2016";
$query = $database->query($query_install_month);
$result = $database->fetch_array($query);
$most_installed_count = $result['most_installed_count'];
$least_installed_count = $result['least_installed_count'];

if($posts_count>$most_installed_count)
{
	$query_install_month = "update signpost_stats set most_installed_count=".$posts_count.", most_installed_date = ".$today." where year=2016";
	$query = $database->query($query_install_month);
}

if($posts_count<$least_installed_count)
{
	$query_install_month = "update signpost_stats set least_installed_count=".$posts_count.", least_installed_date = ".$today." where year=2016";
	$query = $database->query($query_install_month);
}

echo $posts_count;
die();

?>