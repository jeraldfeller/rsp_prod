<?php
/*
 * Part of Realty Sign Post (c) 2014 Realty Sign Post.
 * Description: Map zip codes to service areas
 *
 * Author: John Pelster <john.pelster@gmail.com>
 */

require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

if(substr_count('realtysignpost',$_SERVER['HTTP_HOST'])) {
    error_reporting(0);
    ini_set('error_reporting', 0);
    ini_set('display_errors', 'Off');
}

header('Content-Type: application/json');

$query = $database->query("SELECT zip_4_first_break_start AS low, zip_4_second_break_start AS high, sa.name as service_area, sa.service_area_id, sa.map_color FROM " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica JOIN " . TABLE_INSTALLATION_AREAS . " ia ON (ica.installation_area_id = ia.installation_area_id) JOIN " . TABLE_SERVICE_AREAS . " sa ON (ia.service_area_id = sa.service_area_id) WHERE sa.status = 0");

echo "[\n";
$i=0;
while ($result = $database->fetch_array($query)) {
    if ($i>0) {
        echo ", \n";
    }
    $i++;
    echo " {\"high\": \"{$result['high']}\", \"low\": \"{$result['low']}\", \"area\": \"{$result['service_area']}\", \"service_area_id\": {$result['service_area_id']}, \"color\": \"{$result['map_color']}\"}";
}
echo "\n]";
?>
