<?php
/*
 * Part of Realty Sign Post (c) 2014 Realty Sign Post.
 * Description: Address counts by zipcode
 *
 * Author: John Pelster <john.pelster@gmail.com>
 */

require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

if(substr_count('realtysignpost',$_SERVER['HTTP_HOST'])) {
    error_reporting(0);
    ini_set('error_reporting', 0);
    ini_set('display_errors', 'Off');
}

// These check permissions, but it's intended only in the context of this autocomplete widget.
function is_admin() {
  if (isset($_SESSION) && isset($_SESSION['user_group_id']) && $_SESSION['user_group_id'] == 2) {
    return true;
  }
  return false;
}

if (!is_admin()) {
    die;
}

header('Content-Type: application/json');

$query = $database->query("SELECT SUBSTRING(zip4, 1, 5) AS zip, COUNT(*) AS count FROM " . TABLE_ADDRESSES . " GROUP BY SUBSTRING(zip4, 1, 5)");

echo "{\n";
$i=0;
while ($result = $database->fetch_array($query)) {
    $zip = $result['zip'];
    $count = $result['count'];
    if ($i>0) {
        echo ", \n";
    }
    $i++;
    echo " \"zip{$zip}\": {$count}";
}
echo "\n}";
?>
