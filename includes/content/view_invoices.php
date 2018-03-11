<?php

require_once('lib/invoices/invoice_functions.php');

$html = null;
$base_path = "//" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . "/admin_view_invoices.php";

// Get current user's email address for later use.
if (!empty($_SESSION['user_id']) && empty($_SESSION['email'])) {
  $e = $database->query("SELECT * FROM `users` WHERE `user_id` = {$_SESSION['user_id']}");
  foreach($database->fetch_array($e) as $r){
    $_SESSION['email'] = $r['email_address'];
  }
}

// Get the required variables.
$action = null;
$get = explode(',', 'agency_id,month,year,user_id,action,status,balance,email_this,msg');
foreach ($get as $v) {
  if (!empty($_REQUEST[$v])) {
    $$v = $_REQUEST[$v];
  } else {
    $$v = 0;
  }
}

// Check permissions. Allow AP's and users their proper permissions.
$allowed = true;
if (!is_admin()) {
  if (is_ap()) {
    $user_id = $_SESSION['user_id'];
    if (!empty($agency_id) && $agency_id != $_SESSION['agency_id']) {
        $allowed = false;
    } else {
        $agency_id = $_SESSION['agency_id'];
    }
  } elseif (is_agent()) {
    if (!empty($agency_id) && $agency_id != 0) {
        $allowed = false;
    } else {
        $agency_id = 0;
    }
    if (!empty($user_id) && $user_id != $_SESSION['user_id']) {
        $allowed = false;
    } else {
        $user_id = $_SESSION['user_id'];
    }
  } else {
    $allowed = false;
  }
}

if ($allowed) {
  // When there's a user_id and agency_id, make sure action = details to land on the details page.
  if ($month && $year && ($agency_id || $user_id) && $action == 'details') {
    if ($email_this) {
      require_once('lib/invoices/invoice_email_single.php');
    } else {
      require_once('lib/invoices/invoice_display_single.php');
    }
  } else {
    require_once('lib/invoices/invoice_display_all.php');
  }
} else {
  tep_redirect(FILENAME_403);
}
?>
