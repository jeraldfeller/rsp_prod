<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//ini_set('display_errors', '1');
require_once 'invoice_functions.php';
require_once "../../includes/classes/PHPExcel.php";

$mcenearney_corp_user_id = 6143;

if ($user->user_group_id != 2 && $user->user_id != $mcenearney_corp_user_id) {
	echo "Must login.";
	exit;
}

$get = explode(',', 'month,year,user_id,email_this,msg');
foreach ($get as $v) {
  if (!empty($_REQUEST[$v])) {
    $$v = $_REQUEST[$v];
  } else {
    $$v = 0;
  }
}

if (!$month) {
  $month = date('n');
}
if (!$year) {
  $year = date('Y');
}

// Start ts, Jan 1, 2014
$invoice_history_from = strtotime('2014-01-01');

$s = "SELECT agency_id FROM `agencys` WHERE name = 'McEnearney'";
$q = $database->query($s);

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()
	->setCellValue('A1', 'Order ID')
	->setCellValue('B1', 'Date')
	->setCellValue('C1', 'Description')	
	->setCellValue('D1', 'Details')
	->setCellValue('E1', 'City')
	->setCellValue('F1', 'Agent')
	->setCellValue('G1', 'Amount')
	->setCellValue('H1', 'Office')
	->setCellValue('I1', 'MLS')
	->setTitle("{$arr['trip_date']}");
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(60);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);

$row = 1;

foreach($database->fetch_array($q) as $r){
	$agency_id = $r['agency_id'];
	$user_id = 0;
			
	// Get the items in the default format.
	$items = get_invoice_items($month, $year, $user_id, $agency_id);

	// Email function needs this returned as a php variable. Hence will set $items for it. Otherwise, this is JSON and accessed via AJAX.
	// Get the previous balance. Since want total prior to current month, need to subtract one from month/year.
	$last = new DateTime();
	$last->setTimestamp(mktime(0, 0, 0, $month, 1, $year));
	$last->sub(new DateInterval('P1M'));
	$last_month = $last->format('n');
	$last_year = $last->format('Y');

	// Now reformat them to a simpler format for the client side parsing.
	unset($items['agencies'][$agency_id][$year][$month]['total']);	
	$invoices = $items['agencies'][$agency_id][$year][$month];

	foreach ($invoices as $index => $arr) {
		$order_id = (int) $arr['order_id'];
		if (!$order_id) {
			continue;
		}
		
		$row++;
		$objPHPExcel->getActiveSheet()
			->setCellValue("A{$row}", $arr['order_id'])
			->setCellValue("B{$row}", $arr['order_datecompleted'])
			->setCellValue("C{$row}", $arr['reason'])	
			->setCellValue("D{$row}", "{$arr['house_number']} {$arr['street_name']}")
			->setCellValue("E{$row}", $arr['city'])
			->setCellValue("F{$row}", "{$arr['lastname']}, {$arr['firstname']}")
			->setCellValue("G{$row}", $arr['total'])
			->setCellValue("H{$row}", "{$arr['agency_name']} / {$arr['agency_office']}")
			->setCellValue("I{$row}", $arr['agent_id']);
	}
}

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"Invoice-{$year}-{$month}-McEnearney.xls\"");
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>
