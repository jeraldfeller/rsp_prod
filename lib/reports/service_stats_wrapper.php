<?php
$ip_whitelist = array(
    '173.245.50.98', 
    '50.23.231.176',
    '68.186.3.154', 
    '127.0.0.1',
    '103.241.1.121',
    '103.241.1.72'
);

if (!in_array($_SERVER['REMOTE_ADDR'], $ip_whitelist)) {
    die("Access denied");
}

require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

// Set user to run this report as:
$user->user_id = 788;      // info@realtysignpost.com
$user->user_group_id = 2;  // Admin


$recips = SERVICE_STATS_EMAILS;
$to_addresses = explode(",", $recips);
foreach ($to_addresses as $i => $address) {
    $to_addresses[$i] = trim($address);
}
$subject = "Service Stats for " . date("F j, Y");

// Get the HTML from the web view
ob_start();

require_once dirname(dirname(dirname(__FILE__))) . '/includes/content/admin_service_stats.php';

$html = ob_get_contents();
ob_end_clean();

// Now clean up the HTML to make it email friendly

$html = strip_tags($html, "<table><tbody><thead><tr><th><td><b><br><hr>");
$message  = "<html><body>\n";

$hit = 0;

foreach(explode("\n", $html) as $i => $line) {
    // Filter out a few more lines and echo the HTML
    if (strpos($line, "<table") === 0) {
        $hit = 1;
    }
    if (!$hit) {
        continue;
    }
    
    if (strpos($line, "Filter Posts") > 0 && array_key_exists("average_install_length_since", $_GET)) {
        $message .= "{$line} {$_GET['average_install_length_since']}\r\n";
        continue;
    } elseif (strpos($line, "Filter Posts") > 0) {
        continue;
    } elseif (strpos($line, "ORDER DISTRIBUTION MAP") > 0) {
        continue;
    }

    $message .= "{$line}\r\n";
}

$message .= "</body></html>";

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: RSP Reports <info@realtysignpost.com>' . "\r\n";

// Mail it
foreach ($to_addresses as $to) {
    echo "<div>Email sent to {$to}.</div>\n";
    mail($to, $subject, $message, $headers);
}
?>
