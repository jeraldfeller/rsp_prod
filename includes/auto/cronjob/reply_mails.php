<?php
print "shyju";
$USERNAME='test';
$PASSWORD='password';
$RESPONSE_EMAIL='test@email.com';
$MAIL_SERVER='';//mail server name
$SERVER_TYPE='pop'	;
$PORT='110';		
$RESPONSE_TYPE='custom';
$message="Hello It is the time to remove!\r\n";
include("../autoresponse.class.php");
$r=new autoresponse($USERNAME,$PASSWORD,$RESPONSE_EMAIL,$MAIL_SERVER,$SERVER_TYPE,$PORT);
$r->connect();
$r->responseContentSource="$message";
$r->send('custom','text');
$r->close_mailbox();
?>
