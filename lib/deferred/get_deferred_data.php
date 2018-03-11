<?php
ini_set('memory_limit','-1');
require_once('../../includes/application_top.php');
Global $database;

//$data = json_decode($_POST['param'], true);
$data = array(
  'type' => $_GET['type'],
    'showBy' => $_GET['showBy']
);

switch(trim($data['type'])){
    case 'seven':
        if($data['showBy'] == 'Total Billed'){
            $query = $database->query("select ud.firstname, ud.lastname, a.date_added, a.amount, a.reason from " . TABLE_TRANSACTIONS . " a, " . TABLE_USERS_DESCRIPTION . " ud where (a.billing_method_id = '2' or a.billing_method_id = '3') and a.date_added >= '" . strtotime("today - 7 days") . "' and a.user_id = ud.user_id and a.amount != 0");
            $title = $data['showBy'] . ': Deferred Billing CC Total (7 days)';
        }else if($data['showBy'] == 'Total Paid'){
            $query = $database->query("select ud.firstname, ud.lastname, a.date_added, a.amount, a.reason from " . TABLE_TRANSACTIONS . " a, " . TABLE_USERS_DESCRIPTION . " ud where a.billing_method_id = '1' and a.date_added >= '" . strtotime("today - 7 days") . "' and a.user_id = ud.user_id and a.amount != 0");
            $title = $data['showBy'] . ': 7 days';
        }

        break;
    case 'current_month':
        if($data['showBy'] == 'Total Billed'){
            $query = $database->query("select ud.firstname, ud.lastname, a.date_added, a.amount, a.reason from " . TABLE_TRANSACTIONS . " a, " . TABLE_USERS_DESCRIPTION . " ud where (a.billing_method_id = '2' or a.billing_method_id = '3') and date_added >= '" . strtotime(date('Y-m-01')) . "' and date_added <= '" . strtotime(date('Y-m-d'))."' and a.user_id = ud.user_id and a.amount != 0");
            $title = $data['showBy'] . ': Deferred CC Billing Total - Current Month';
        }else if($data['showBy'] == 'Total Paid'){
            $query = $database->query("select ud.firstname, ud.lastname, a.date_added, a.amount, a.reason from " . TABLE_TRANSACTIONS . " a, " . TABLE_USERS_DESCRIPTION . " ud where a.billing_method_id = '1' and date_added >= '" . strtotime(date('Y-m-01')) . "' and date_added <= '" . strtotime(date('Y-m-d'))."' and a.user_id = ud.user_id and a.amount != 0");
            $title = $data['showBy'] . ': Current Month';
        }

        break;
    case 'prev_month':
        if($data['showBy'] == 'Total Billed'){
            $query = $database->query("select ud.firstname, ud.lastname, a.date_added, a.amount, a.reason from " . TABLE_TRANSACTIONS . " a, " . TABLE_USERS_DESCRIPTION . " ud where (a.billing_method_id = '2' or a.billing_method_id = '3') and date_added >= '" . strtotime(date('Y-m-01', strtotime('-1 month'))) . "' and date_added <= '" . strtotime(date('Y-m-t', strtotime('-1 month'))) ."' and a.user_id = ud.user_id and a.amount != 0");
            $title = $data['showBy'] . ': Deferred CC Billing Total - Previous Month';
        }else if($data['showBy'] == 'Total Paid'){
            $query = $database->query("select ud.firstname, ud.lastname, a.date_added, a.amount, a.reason from " . TABLE_TRANSACTIONS . " a, " . TABLE_USERS_DESCRIPTION . " ud where a.billing_method_id = '1' and date_added >= '" . strtotime(date('Y-m-01', strtotime('-1 month'))) . "' and date_added <= '" . strtotime(date('Y-m-t', strtotime('-1 month'))) ."' and a.user_id = ud.user_id and a.amount != 0");
            $title = $data['showBy'] . ': Previous Month';
        }

        break;

}

$item['data'] = array();
while($result = $database->fetch_array($query)) {
    $item['data'][] = array(
        $result['firstname'] . ' ' . $result['lastname'],
        $result['reason'],
        '$'.$result['amount'],
        date('m/d/Y H:i:s', $result['date_added'])
    );


}


echo json_encode($item);
