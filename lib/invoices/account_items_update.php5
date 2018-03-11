<?php

require_once 'invoice_functions.php';

//////////////////////////////
///// ONE TIME FUNCTIONS /////
//////////////////////////////
//update_account_items_structure();
//transfer_from_account_payments();

//////////////////////////////////
///// SAFE TO RUN EVERY TIME /////
//////////////////////////////////
//merge_account_items_tables();
//find_orphaned_orders();
//find_missing_account_item_info();
update_missing_account_item_info();

?>