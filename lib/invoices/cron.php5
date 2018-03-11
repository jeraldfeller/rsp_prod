<?php

require_once 'invoice_functions.php';
find_orphaned_orders();
update_missing_account_item_info();
find_missing_account_item_info();

?>