<?php

// DB billing_methods
define('BILLING_METHOD_CREDIT','1');
define('BILLING_METHOD_INVOICE','2');

// DB order_types
define('ORDER_TYPE_UNKNOWN', '0');    // see account.php
define('ORDER_TYPE_INSTALL', '1');
define('ORDER_TYPE_SERVICE', '2');
define('ORDER_TYPE_REMOVAL', '3');

// DB order_statuses
define('ORDER_STATUS_PENDING', '1');
define('ORDER_STATUS_SCHEDULED', '2');
define('ORDER_STATUS_COMPLETED', '3');
define('ORDER_STATUS_CANCELLED', '4');
define('ORDER_STATUS_ONHOLD', '5');

// DB ? 
define('SERVICE_EXCHANGE_RIDER', '1');
define('SERVICE_INSTALL_NEW', '2');
define('SERVICE_EXCHANGE_AGENT', '3');
define('SERVICE_STRAIGHTEN_POST', '4');
define('SERVICE_MOVE_POST', '5');
define('SERVICE_INSTALL_FORGOTTEN', '6');
define('SERVICE_OTHER', '7');

// Constants in account class
define('ACCOUNT_DIRECTION_DEBIT',  '0');   // charge to user
define('ACCOUNT_DIRECTION_CREDIT', '1');   // payment or credit issued

define('ACCOUNT_STATUS_PENDING',   '1');
define('ACCOUNT_STATUS_COMPLETED', '2');

define('ACCOUNT_TYPE_ORDER',         '1');
define('ACCOUNT_TYPE_REFUND',        '2');
define('ACCOUNT_TYPE_PAYMENT',       '3');
define('ACCOUNT_TYPE_CANCEL',        '4');
define('ACCOUNT_TYPE_REFUND_RETURN', '5');
define('ACCOUNT_TYPE_CREDIT_USED',   '6');

