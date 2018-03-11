<?php
	//define('DIW_FS', '');
	//define('DIW_FS', '/home/realtysp/web/realtysignpost.com/public_html/');
	define('DIW_FS', 'C:/wamp64/www/RSP_PROD/');
	define('DIR_INCLUDES', DIW_FS . 'includes/');
	define('DIR_IMAGES', DIW_FS . 'images/');
	define('DIR_TEMPLATE', DIR_INCLUDES . 'template/');
	define('DIR_CONTENT', DIR_INCLUDES . 'content/');
	define('DIR_FUNCTIONS', DIR_INCLUDES . 'functions/');
	define('DIR_CLASSES', DIR_INCLUDES . 'classes/');
	define('DIR_BOXES', DIR_INCLUDES . 'boxes/');
	define('DIR_LANGUAGES', DIR_INCLUDES . 'languages/');
	define('DIR_TEMP', DIR_INCLUDES . 'temp/');
	define('HTTP_SERVER', 'http://realtysignpost.com/');
	define('SID', 'rsid');
	//define('SEND_EMAILS', 'false');
	//define('EMAIL_USE_HTML', 'false');
	//define('EMAIL_FROM_ADDRESS', 'orders@realtysignpost.com');
	//define('EMAIL_FROM_NAME', 'Realty Signpost');
	//define('EMAIL_DEFAULT_SUBJECT', 'Email from Realty Signpost');
	define('NEW_CHARSET', 'iso-8859-1');
	define('CHARSET', 'iso-8859-1');
	define('EMAIL_LINEFEED', '');
	define('TEMPLATE_DEFINER', '&amp;');
	//define('EMAIL_TRANSPORT', '');
	//define('SEND_EXTRA_EMAIL', 'true');
	//define('SEND_EXTRA_EMAIL_TO', 'orders@realtysignpost.com');
	//define('USE_GZIP', 'false');
	//define('MAILER_NAME', 'X-Mailer: RSPC Mailer');
	define('DB_SERVER', 'localhost');
	define('DB_SERVER_USERNAME', 'root');
	define('DB_SERVER_PASSWORD', '');
	define('DB_DATABASE', 'realtysi_database');
	//define('DB_SERVER_USERNAME', '');
	//define('DB_SERVER_PASSWORD', '');
	//define('DB_DATABASE', 'rspc');
	define('USE_PCONNECT', 'false');
	
	define('PREVNEXT_TITLE_FIRST_PAGE', 'First Page');
	define('PREVNEXT_TITLE_PREVIOUS_PAGE', 'Previous Page');
	define('PREVNEXT_TITLE_NEXT_PAGE', 'Next Page');
	define('PREVNEXT_TITLE_LAST_PAGE', 'Last Page');
	define('PREVNEXT_TITLE_PAGE_NO', 'Page %d');
	define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', 'Previous Set of %d Pages');
	define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', 'Next Set of %d Pages');
	define('PREVNEXT_BUTTON_FIRST', '&lt;&lt;FIRST');
	define('PREVNEXT_BUTTON_PREV', '[&lt;&lt;&nbsp;Prev]');
	define('PREVNEXT_BUTTON_NEXT', '[Next&nbsp;&gt;&gt;]');
	define('PREVNEXT_BUTTON_LAST', 'LAST&gt;&gt;');
	
  define('SESSION_EXPIRY_MINUTES', 30);

	define('MAX_LATEST_NEWS_LENGTH', '1000');
	//CCC
	//define("CC_TOKEN", "TESTA3D2B018CE6D5305338CE5F08256AD64C3CD2B9640B0C0F7ECA14D2B8DDC5DE59D5676E1C818BF");
	define("CC_TOKEN", "71AD0A15CE6D5305338CE5F08256AD64C3CD2B9640B0C0F7ECA14D2B8DDC5DE59D5667D4B7860C");
	define("CC_ERROR_LOG", 'cc_error_log.txt');
	//define("ERROR_LOG", 'debug_error_log.txt');   // DEBUG only, catches all SQL

  // For [aom_]order_create_payment.php
  define('CC_WARN_1', <<<END
<tr><td height="5"><img scr="images/pixel_trans.gif" height="5" width="1"/></td></tr>
<tr><td><div class="ccWarning">Please carefully check the information you enter.  If you have issues with your credit card, please call us at 202-256-0107 for assistance.</div></td></tr>
END
);
  define('CC_WARN_2', <<<END
<tr><td><div class="ccWarning">You will see the total of your order before your credit card is charged, and you will have the ability to cancel or change your order.</div></td></tr>
<tr><td height="5"><img scr="images/pixel_trans.gif" height="5" width="1"/></td></tr>
END
);

?>
