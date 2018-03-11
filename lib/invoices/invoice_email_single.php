<?php

ini_set('display_errors', true);
require_once('invoice_items.php');

$font = "font-family: Arial,Verdana,sans,sans-serif; color: #333;";
$address = get_invoice_address($user_id, $agency_id);
$date = date("F", mktime(0, 0, 0, $month, 1, 2013)) . " " . $year;

// Set up the messages to be included in the emails.
$msg = "";
if(!empty($_GET['msg'])) {
  $msg = nl2br($_GET['msg']);
}
$msg_box = "Thank you!";
if(!empty($_GET['msg_box'])) {
  $msg_box = nl2br($_GET['msg_box']);
}
$extra_email = "";
if(!empty($_GET['extra_email'])) {
  $extra_email = trim($_GET['extra_email']);
}

$bname = BUSINESS_NAME;
$baddr = BUSINESS_ADDRESS;

$html = <<<EOL
<div style="margin-bottom: 2em;">{$msg}</div>
<div>
  <div style="{$font} margin-bottom: 1em;">
    <h1 style="{$font}">Realty Sign Post Invoice - {$date}</h1>
    <h2>Invoice #{$year}-{$month}-{$agency_id}-{$user_id}</h2>
    <div style="float: right;">
      <address>From:<br><strong>{$bname}</strong><br>{$baddr}<br>Email: <a href="mailto:info@realtysignpost.com">info@realtysignpost.com</a></address>
    </div>
    <div>To:
      <address style="{$font}">{$address}</address>
    </div>
  </div>
  <hr>
  <div>
    <table style="width: 100%;">
      <thead>
        <tr>
          <th style="text-align: left;">Date
          <th style="text-align: left;">Reason
          <th style="text-align: left;">Details
          <th style="text-align: left;">Agent
          <th style="text-align: left;">Total
      </thead>
      <tbody>
EOL;

//exit("<pre>" . print_r($items, 1) . "</pre>");
foreach ($items as $k => $v) {
  if ($k === 'total') {
    continue;
  } elseif ($k === 'previous_balance') {
    $bal = floatval(str_replace(",", "", $v));
    if ($bal == 0) {
      continue;
    }
    $line_item = array(
        'date_added' => time(),
        'reason' => "Previous Balance",
        'details' => "Previous Balance",
        'firstname' => "",
        'lastname' => "",
        'total' => number_format($bal, 2)
    );
  } else {
    $line_item = $v;
  }
  if (!empty($line_item)) {
    
    $this_name = "";
    if(!empty($line_item['firstname']) && !empty($v['lastname'])) {
      $this_name = $line_item['firstname'] . " " . $v['lastname'];
    }
    $html .= "
        <tr>
        <td style = '{$font}'>{$line_item['order_datecompleted']}
        <td style = '{$font}'>{$line_item['reason']}
        <td style = '{$font}'>{$line_item['details']}
        <td style = '{$font}'>{$this_name}
        <td style = '{$font}'>{$line_item['total']}
  ";
  }
}

// Include previous balance.
if (isset($items['previous_balance'])) {
  if (!isset($items['total'])) {
    $items['total'] = 0;
  }
  $tot = floatval(str_replace(",", "", $items['total']));
  $items['total'] = floatval($bal + $tot);
}

$this_amt = "$" . number_format($items['total'], 2);
$html .= <<<EOL
      </tbody>
      <tfoot>
        <tr><th colspan="5"><hr>
      <tr><th colspan="4" style="text-align: right;
        {$font}"><span style="margin-right: 2em;
        ">Invoice Total</span><th style="text-align: left;
        {$font}">{$this_amt}
          </tfoot>
    </table>
  </div>
  <div style="padding: 0 5%; {$font}">
    <p><strong>Message</strong>
    <div style="margin-top: 0.5em; border-radius: 4px; border: 1pt solid #ccc; padding: 1em; {$font}">
    {$msg_box}
    </div>
  </div>
  <div style="{$font} text-align: center; margin-top: 2em;">
    Sign into your <a href="http://www.realtysignpost.com" style="text-decoration: none;">Realty Sign Post</a> account to view this invoice online.
  </div>
  <div style="font-family: Arial, sans-serif; text-align: center; margin-top: 2em; font-size: 0.75em; color: #999;">
    This message is intended for {{first_name}} {{last_name}} at {{email}}. If you are not the intended recipient, or person responsible for delivering this 
    information to the intended recipient, please notify Realty Sign Post immediately at <a href="mailto:info@realtysignpost.com">info@realtysignpost.com</a> or
    (202) 256 0107. Unless you are the intended recipient or his/her representative you are not authorized to, and must not, read, copy, distribute, use or 
    retain this message or any part of it.
  </div>
  
</div>
EOL;
	

// Send the email.
$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: Realty SignPost <info@realtysignpost.net>' . "\r\n";
$headers .= 'Reply-To: Realty SignPost <info@realtysignpost.net>' . "\r\n";

// Test values
$send_to = $_SESSION['email'];
$real_send_to = array();

// Production values. Look up the actual email address.
if($user_id) {
  // Send to the user, only.
  $s = "
      SELECT u.email_address, ud.firstname, ud.lastname 
      FROM users u, users_description ud 
      WHERE u.user_id = {$user_id} AND u.user_id = ud.user_ID
  ";
} elseif($agency_id) { 
  
  // Send to an agency. This should send to *all* users that are accounts payable or AOMs.
  // Now, try to match to AOM (user_group_id = 4) OR accounts_payable (user_group_id = 5) for that agency
  // Also respect the user.accounts_payable boolean, although it does not seem to be in use currently.
  // Add each result to a csv list of recipients, since csv to is accepted by PHP mail function.
  $s = "
      SELECT u.email_address, ud.firstname, ud.lastname, g.user_group_id
      FROM users u, users_description ud, users_to_user_groups g 
      WHERE
        u.user_id = ud.user_id
        AND u.user_id = g.user_id
        AND u.agency_id = {$agency_id} 
        AND (g.user_group_id = 5 OR u.accounts_payable = 1)
      UNION
      SELECT eta.email_address, a.contact_name as firstname, '' as lastname, 0 as user_group_id
      FROM emails_to_agencys eta, agencys a 
      WHERE eta.agency_id = a.agency_id 
      AND a.agency_id = {$agency_id}
      AND eta.email_status > 0 
  ";
} else {
  exit(json_encode(array('error' => true, 'msg'=>'No one to send the email to.')));
}

// Get the results.
$q = $database->query($s);
foreach($database->fetch_array($q) as $r){
  $real_send_to[$r['email_address']] = array(
      'firstname' => $r['firstname'],
      'lastname' => $r['lastname']
  );
}

if (count($real_send_to) == 0 && !empty($extra_email)) {
    $real_send_to[$extra_email] = array(
      'firstname' => "User",
      'lastname' => ""
    );
    $extra_email = "";
} elseif (count($real_send_to) == 0 && empty($extra_email)) {
    exit(json_encode(array('error' => true, 'msg'=>'No one to send the email to.')));
}

$month_string = date('F', mktime(0, 0, 0, $month));
if($send_to != $_SESSION['email']) {
  exit(json_encode(array('error' => true, 'Email not sent to test address.')));
}

$send_result = false;
$found_recipients = false;
foreach($real_send_to as $to_address => $user_info) {

  // Allow for custom variables.
  $search = array(
      '{{first_name}}',
      '{{last_name}}',
      '{{month}}',
      '{{amount}}',
      '{{year}}',
      '{{email}}'
  );
  
  $replace = array(
      $user_info['firstname'],
      $user_info['lastname'],
      $month_string,
      $this_amt,
      $year,
      $to_address
  );
  
  // At least we found an email address.
  $found_recipients = true;
  
  // Create a copy of the HTML to add custom variables.
  $personal_html = str_replace($search,$replace,$html);
  if (getenv('SERVER_MODE') == 'TEST') {
      error_log("Invoice Email to: $to_address");
      if (!empty($extra_email)) {
          error_log("Extra Email to: $extra_email");
      }
      error_log($headers);
      error_log($personal_html);
      $send_result = true;
  } else {
      if(mail($to_address, "Your {$month_string} Invoice From Realty Sign Post", $personal_html, $headers)) {
          if (!$send_result && ($to_address != ADMIN_EMAIL) && ($to_address != INFO_EMAIL)) {
            mail(ADMIN_EMAIL, "Your {$month_string} Invoice From Realty Sign Post", $personal_html, $headers);
          }
          if (!$send_result && !empty($extra_email)) {
            mail($extra_email, "Your {$month_string} Invoice From Realty Sign Post", $personal_html, $headers);
          }
          $send_result = true; // At least one got send, to consider that success.
      }
  }
  
}

// Give this one last try, if all emails in the loop failed
if (!$send_result && !empty($extra_email)) {
    if (mail($extra_email, "Your {$month_string} Invoice From Realty Sign Post", $personal_html, $headers)) {
        $send_result = true; // Good enough.
    }
}

// If at least one got sent, mark it off in the database.
if ($send_result) {
  // FIXME. Mark it off as sent in the database.
  // year-month-agency-agent
  $s = "INSERT INTO invoice_email_history (invoice_id, date_sent) VALUES ('{$year}-{$month}-{$agency_id}-{$user_id}','".date("n/d/Y")."')";
  $database->query($s);
  if($s) {
    exit(json_encode(array('error' => false, 'user_id' => (int) $user_id, 'agency_id' => (int) $agency_id, 'year' => (int) $year, 'month' => (int) $month)));
  } else {
    exit(json_encode(array('error' => true, 'msg'=>'Email send but not saved as sent.','user_id' => (int) $user_id, 'agency_id' => (int) $agency_id, 'year' => (int) $year, 'month' => (int) $month)));
  }
} else {
  if($found_recipients) {
    exit(json_encode(array('error' => true, 'msg'=>"Email could not be sent. Perhaps a network error? The mail function does not supply any more helpful debugging info.", 'user_id' => (int) $user_id, 'agency_id' => (int) $agency_id, 'year' => (int) $year, 'month' => (int) $month)));  
  } else {
    exit(json_encode(array('error' => true, 'msg'=>"Email could not be sent because could not find an address.\n\n DEBUG\nUsed the following SQL query:\n" . preg_replace("(\s{1,})"," ",$s), 'user_id' => (int) $user_id, 'agency_id' => (int) $agency_id, 'year' => (int) $year, 'month' => (int) $month)));  
  }
}

?>
