<?php
//error_reporting(E_ALL);
ini_set('display_errors', '1');
if(false){
  header('Location: maintenance.html',TRUE,307);
}

// Redirect all insecure pages to realtysignpost.com, secure pages to realtysingpost.net
// Allow sharing of sessions across domains.
try{
$rsid = null;
if(!empty($_GET['rsid'])) {
  $_COOKIE['rsid'] = $_GET['rsid'];
}

if(!empty($_COOKIE['rsid'])) {
  $rsid = $_COOKIE['rsid'];
}

$action = '';
if (isset($_GET['action'])) {
  $action = addslashes($_GET['action']);
}

$get = '';
foreach($_GET as $k => $v) {
  if($k!=='rsid') {
    $get .= "&{$k}={$v}";
  }
}

if($get) {
  $rsid_str = '&rsid=' . $rsid;
} else {
  $rsid_str = '?rsid=' . $rsid;
}

$page_name = $_SERVER['REQUEST_URI'];
if (substr_count($page_name, '?')) {
    $page_name = substr($page_name, 0, strpos($page_name, '?'));
}

$secure = false;
$secure_pages = array(
    'order_create_',
    'order_deferred_',
    'pay_invoice_',
    'account_change_password'
);

foreach ($secure_pages as $secure_page) {
    if (substr_count($page_name, $secure_page)) {
        $secure = true;
    }
}

if ($action == 'login') {
    $secure = true;
}

$request_prefix = (empty($_SERVER['HTTPS']) ? "http://" : "https://") . $_SERVER['HTTP_HOST'];

if (getenv("SERVER_MODE") == "TEST") {
    define('HTTP_PREFIX', "http://testdnx.net");
    define('HTTPS_PREFIX', "http://testdnx.net");
    if ($secure) {
        error_log("SECURE REQUEST {$_SERVER['REQUEST_URI']}");
    }
} else {
    define('HTTP_PREFIX', "http://" . (substr_count($_SERVER['HTTP_HOST'], 'www.') ? 'www.' : '') . "dev.rsp_prod.com");
    define('HTTPS_PREFIX', "https://" . (substr_count($_SERVER['HTTP_HOST'], 'www.') ? 'www.' : '') . "realtysignpost.com");
    //define('HTTP_PREFIX', "http://cloud.realtysignpost.com");
    //define('HTTPS_PREFIX', "https://cloud.realtysignpost.com");

    /*
    if ($secure) {
        error_log("SECURE REQUEST {$_SERVER['REQUEST_URI']}");
    } else {
        error_log("HTTP REQUEST {$_SERVER['REQUEST_URI']}");
    }
    */
}



// uncomment code below if HTTPS Scheme enabled and update form action in includes/column_left_home.php
/*
if ($secure && $request_prefix != HTTPS_PREFIX) {
    header('Location: ' . HTTPS_PREFIX . $_SERVER['REQUEST_URI'] . $rsid_str);
    exit;
} elseif (!$secure && $request_prefix != HTTP_PREFIX) {
    header('Location: ' . HTTP_PREFIX . $_SERVER['REQUEST_URI'] . $rsid_str);
    exit;
}
*/

require_once('includes/application_top.php');
$page = new page();
$page->generate_page();
if (USE_GZIP == 'true') {
	$page->run_compression();
}
echo $page->return_content();
}catch(ErrorException $e){
    header('Location: 500.html',TRUE,307);
}
?>
