<?php

// Very simple, it just returns the amount +/- for the given account. Needs get variables to work: user_id, agency_id, month, year.
require_once 'invoice_functions.php';

// Check permissions. Allow AOM's and users their proper permissions.
if (!is_admin()) {
  if (is_aom()) {
    $user_id = 0;
    $agency_id = $_SESSION['agency_id'];
  } elseif (is_agent()) {
    $agency_id = 0;
    $user_id = $_SESSION['user_id'];
  } else {
    if (empty($email_this)) {
      exit(json_encode(array('error' => true, 'msg' => 'access_denied')));
    } else {
      exit('Acess denied.');
    }
  }
}

// AOM's and users will have the user_id and agency_id set automatically.
// So should check to see if they are already set, and NOT reset them.
$l = explode(',', 'user_id,agency_id,month,year');
foreach ($l as $v) {
  if (!isset($_GET[$v]) && !isset($$v)) {
    exit(json_encode(array('error' => true, 'msg' => $v . ' can not be empty')));
  }
  $$v = addslashes($_GET[$v]);
}

exit(json_encode(array('error' => false, 'total' => get_account_total($user_id, $agency_id, $month, $year))));
?>