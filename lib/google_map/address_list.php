<?php
ini_set('max_execution_time', 300);
require_once('../../includes/application_top.php');
Global $database;
$display_view = 'overview';
$addressQuery = array();
$info = array();
$extra = ', a.house_number, a.street_name, a.city, a.state_id, a.latitude, a.longitude, a.address_id   ';

$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp()));
$midnight_future = ($midnight_tonight + ((60*60*24) * 1));

if (date("w", ($midnight_tonight+1)) == 0) {
    $midnight_tonight += (60*60*24);
    $midnight_future += (60*60*24);
}

$show_only_scheduled = '';
$sort_by_status = 1;
$mainQuery = $database->query("select o.order_id, o.date_schedualed, o.order_status_id, os.order_status_name, ot.name as order_type_name, otiso.show_order_id as order_column, a.zip4".$extra." from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where o.date_schedualed < '" . $midnight_future . "' and " . (($show_only_scheduled == '1') ? " o.order_status_id = '2' " : " o.order_status_id < '3' ") . " and o.order_issue != '1' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.order_status_id = os.order_status_id and o.service_level_id = sld.service_level_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id, otiso.show_order_id order by " . (($sort_by_status == '1') ? ' o.order_status_id, ' : '') . (($display_view == 'detailed') ? 'order_column' : 'o.date_schedualed ASC'));

foreach($database->fetch_array($mainQuery) as $result){
    $stateQuery = $database->query("select name as state_name from " . TABLE_STATES . " WHERE state_id = " . $result['state_id'] . "");

    $address_id = $result['address_id'];
    $house_number = $result['house_number'];
    $street_name = $result['street_name'];
    $city = $result['city'];
    $order_type = $result['order_type_name'];
    $order_type_trim = str_replace(' ', '', $order_type);
    $state_id = $result['state_id'];
    $state = $database->fetch_array($stateQuery)['state_name'];
    $lat = ($result['latitude'] == '' ? 0 : $result['latitude']);
    $lng = ($result['longitude'] == '' ? 0 : $result['longitude']);
    $query = urlencode("$house_number+$street_name+$city+$state");


    if(!in_array($query, $addressQuery)){
        if($lat == 0 && $lng == 0){
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$query&key=AIzaSyAf_n7IU-Ui5lJTTtqqgfmjd-C6aHJOMdg";
            $result = json_decode(executeApiCall($url), true);
            if($result['status'] == 'OK'){
                $lat = $result['results'][0]['geometry']['location']['lat'];
                $lng = $result['results'][0]['geometry']['location']['lng'];

                $insertQuery = $database->query("UPDATE " . TABLE_ADDRESSES . " SET latitude = $lat, longitude = $lng WHERE address_id = $address_id");
            }
        }

        $info[] = array(
            'id' => $x,
            'name' => $order_type,
            'address' => $house_number . ' ' . $street_name . ', ' . $city . ', ' . $state,
            'lat' => $lat,
            'lng' => $lng,
            'type' => $order_type_trim
        );

        $addressQuery[] = $query;
    }
}



function parseToXML($htmlStr)
{
    $xmlStr=str_replace('<','&lt;',$htmlStr);
    $xmlStr=str_replace('>','&gt;',$xmlStr);
    $xmlStr=str_replace('"','&quot;',$xmlStr);
    $xmlStr=str_replace("'",'&#39;',$xmlStr);
    $xmlStr=str_replace("&",'&amp;',$xmlStr);
    return $xmlStr;
}

header("Content-type: text/xml");

// Start XML file, echo parent node
echo '<markers>';
// Iterate through the rows, printing XML nodes for each
foreach($info as $row){
    // Add to XML document node
    echo '<marker ';
    echo 'id="' . $row['id'] . '" ';
    echo 'name="' . parseToXML($row['name']) . '" ';
    echo 'address="' . parseToXML($row['address']) . '" ';
    echo 'lat="' . $row['lat'] . '" ';
    echo 'lng="' . $row['lng'] . '" ';
    echo 'type="' . $row['type'] . '" ';
    echo '/>';
}

// End XML file
echo '</markers>';


function executeApiCall($url){

    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_VERBOSE => 0
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);
    return $resp;
}