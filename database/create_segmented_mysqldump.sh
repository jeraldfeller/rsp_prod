#!/bin/bash
set -o nounset
set -o errexit

DATABASE=realtysi_database
BIG_TABLES="orders_history equipment_items_history equipment_to_orders orders addresses orders_description equipment_items_to_addresses account_items"
for TABLE in $BIG_TABLES
do
    mysqldump -u realtysi_realtys -p17201 $DATABASE $TABLE > $DATABASE.$TABLE.sql
done
IGNORE=""
for TABLE in $BIG_TABLES
do
    IGNORE="$IGNORE --ignore-table=$DATABASE.$TABLE"
done
mysqldump -u realtysi_realtys -p17201 $DATABASE $IGNORE > $DATABASE.other.sql
gzip *.sql
