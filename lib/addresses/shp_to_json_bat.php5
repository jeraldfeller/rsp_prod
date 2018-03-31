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

header("Content-type: text/plain");

$sa_query = $database->query("SELECT service_area_id FROM " . TABLE_SERVICE_AREAS . " WHERE status = 0");
foreach($database->fetch_array($sa_query) as $sa_result){
    $query = $database->query("SELECT zip_4_first_break_start AS low, zip_4_second_break_start AS high, sa.service_area_id FROM " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica JOIN " . TABLE_INSTALLATION_AREAS . " ia ON (ica.installation_area_id = ia.installation_area_id) JOIN " . TABLE_SERVICE_AREAS . " sa ON (ia.service_area_id = sa.service_area_id) WHERE sa.service_area_id = '{$sa_result['service_area_id']}'");

    $singlets = array();

    $where = "";

    echo "@ECHO OFF\n\n";

    foreach($database->fetch_array($query) as $result){
        $high = $result['high'];
        $low = $result['low'];
        $sa = "service_area_" . $result['service_area_id'] . ".json";
        $sa2merge = "sa". $result['service_area_id'] . "_to_merge.json";
        $sa_id = $result['service_area_id'];

        if ($high == $low) {
            $singlets[] = $low;
        } else { 
            if (!empty($where)) {
                $where .= " OR ";
            }
            $where .= "(ZCTA5CE10 >= '{$low}' AND ZCTA5CE10 <= '{$high}')";
        }
    }

    if (count($singlets) > 0) {
        if (!empty($where)) {
            $where .= " OR ";
        }
        $where .= "ZCTA5CE10 IN (";
        foreach ($singlets as $i => $zip) {
            if ($i > 0) {
                $where .= ", ";
            }    
            $where .= "'{$zip}'";
        }
        $where .= ")";
    }

    if ($database->num_rows($query)) {
        echo "ECHO BUILDING SERVICE AREA {$sa_id}\n";
        echo "DEL {$sa}\n"; 
        echo "ogr2ogr -f GeoJSON -simplify 0.0001 -where \"{$where}\" {$sa} tl_2012_us_zcta510.shp\n\n";
        echo "ECHO WROTE FILE {$sa}\n\n";
        echo "DEL {$sa2}\n"; 
        echo "ogr2ogr -f GeoJSON -where \"{$where}\" {$sa2} tl_2012_us_zcta510.shp\n\n";
        echo "ECHO WROTE FILE {$sa2}\n\n";
    }
}
echo "ECHO GEOJSON BUILD COMPLETE!\n";
?>
