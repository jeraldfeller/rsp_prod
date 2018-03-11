<?php
require_once 'invoice_functions.php';
$get = explode(',', 'agency_id,month,year,user_id,email_this,msg');
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

// Get the items in the default format.
$items = get_invoice_items($month, $year, $user_id, $agency_id);

// Email function needs this returned as a php variable. Hence will set $items for it. Otherwise, this is JSON and accessed via AJAX.
// Get the previous balance. Since want total prior to current month, need to subtract one from month/year.
$last = new DateTime();
$last->setTimestamp(mktime(0, 0, 0, $month, 1, $year));
$last->sub(new DateInterval('P1M'));
$last_month = $last->format('n');
$last_year = $last->format('Y');

// Get the previous balance
$previous_balance = get_account_total($user_id, $agency_id, $last_month, $last_year);

// Now reformat them to a simpler format for the client side parsing.
if ($user_id) {
  
  unset($items['agents'][$user_id][$year][$month]['totals']);
  if($previous_balance !== 0) {
    $items['agents'][$user_id][$year][$month]['previous_balance'] = $previous_balance;
  }
  
  // Email function needs this returned as a php variable. Hence will set $items for it. Otherwise, this is JSON and accessed via AJAX.
  if (!$email_this) {
    exit(json_encode($items['agents'][$user_id][$year][$month]));
  }
  $items = $items['agents'][$user_id][$year][$month];
  
} else {

  // If not emailing, exit with a json response.
  if (!$email_this) {
    unset($items['agencies'][$agency_id][$year][$month]['total']);

    // If no previous balance, no need to print it.
    if ($previous_balance !== 0) {
      $items['agencies'][$agency_id][$year][$month]['previous_balance'] = $previous_balance;
    }
    exit(json_encode($items['agencies'][$agency_id][$year][$month]));
  }

  if (isset($items['agencies'])) {
    $items = $items['agencies'][$agency_id][$year][$month];
  }
  $items['previous_balance'] = $previous_balance;
}
?>
