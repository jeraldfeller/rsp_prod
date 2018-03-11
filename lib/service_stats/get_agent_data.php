<?php
require_once('../../includes/application_top.php');
Global $database;

$numberOfPost = $_GET['numberOfPost'];
$average_since_ts = $_GET['averageSinceTs'];
$item['data'] = array();
$i = 0;
if($numberOfPost != '0'){

    $query = $database->query("SELECT et.equipment_item_id
    FROM " . TABLE_EQUIPMENT_ITEMS . " et, " . TABLE_EQUIPMENT . " e
    WHERE et.equipment_id = e.equipment_id
    AND e.equipment_type_id = 1
    AND et.equipment_status_id = 2"
    );

    $agents = array();
    $numberOfPostCounter = 1;
    while ($result = $database->fetch_array($query)) {
        $id = $result['equipment_item_id'];

        $orderQuery = $database->query("SELECT o.order_id, o.date_completed, o.user_id, o.address_id, ud.firstname, ud.lastname
        FROM " . TABLE_ORDERS . " o, " . TABLE_ADDRESSES . " a, " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita, " . TABLE_USERS_DESCRIPTION . " ud
        WHERE o.address_id = a.address_id
        AND eita.address_id = a.address_id
        AND o.order_status_id = 3 
        AND eita.equipment_item_id = '" . $id . "'
        AND o.user_id = ud.user_id
        ORDER BY eita.equipment_item_to_address_id 
        DESC LIMIT 1
        ");

        if ($orderResult = $database->fetch_array($orderQuery)) {
            $agentId = $orderResult['user_id'];
            $firstName = $orderResult['firstname'];
            $lastName = $orderResult['lastname'];
            $dateCompleted = $orderResult['date_completed'];
            if($dateCompleted > $average_since_ts){
                if(array_key_exists($agentId, $agents)){
                    $agents[$agentId]['count'] = $agents[$agentId]['count'] + 1;
                }else{
                    $agents[$agentId] = array(
                        'count' => 1,
                        'lastName' => $lastName,
                        'firstName' => $firstName
                    );
                }
            }

        }
    }

        foreach($agents as $row){
            if($numberOfPost == '1'){
                if($row['count'] == 1){
                    $item['data'][] = array($row['lastName'], $row['firstName']);
                }
            }else if($numberOfPost == '2'){
                if($row['count'] == 2){
                    $item['data'][] = array($row['lastName'], $row['firstName']);
                }
            }else if($numberOfPost == '3-5'){
                if($row['count'] >= 3 & $row['count'] <= 5){
                    $item['data'][] = array($row['lastName'], $row['firstName']);
                }
            }
            else if($numberOfPost == '6-10'){
                if($row['count'] >= 6 & $row['count'] <= 10){
                    $item['data'][] = array($row['lastName'], $row['firstName']);
                }
            }else if($numberOfPost == '>11'){
                if($row['count'] >= 11){
                    $item['data'][] = array($row['lastName'], $row['firstName']);
                }
            }else{
                $item['data'][] = array($row['lastName'], $row['firstName']);
            }
        }

}


echo json_encode($item);