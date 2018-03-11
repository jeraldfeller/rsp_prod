<?php
	include("/home/apes/web/survey2.apes.kz/public_html/classes/godb.php");
	$dbconfig = array(
	'host'     => 'localhost',
	'username' => 'apes_main',
	'passwd'   => 'temghuk31337',
	'dbname'   => 'apes_tstrsp',
	'prefix'   => '',
	'charset'  => 'utf8',
);

$db = new goDB($dbconfig);

//$query = $db->query('SELECT `equipment_item_id` FROM {equipment_items} ORDER BY `equipment_item_id` ASC LIMIT 3000,3020', array(), 'assoc');

/*foreach ($query as $item)
{*/

	$item['equipment_item_id'] = 4923;
	$bugged = 0;


	echo "\n--------------\n";
	echo $item['equipment_item_id'];
	echo "\n";
	
	$addresses =  $db->query('SELECT {equipment_items_to_addresses}.`address_id` FROM {equipment_items_to_addresses} WHERE {equipment_items_to_addresses}.`equipment_item_id`=?', array($item['equipment_item_id']), 'assoc');
	foreach ($addresses as $addr)
	{
		$orders =  $db->query('SELECT {orders}.`order_id`, {orders}.`order_type_id`, {orders}.`order_status_id` FROM {orders} WHERE {orders}.`address_id`=?', array($addr['address_id']), 'assoc');
		
		foreach ($orders as $order)
		{
			if($order['order_type_id']==3 && $order['order_status_id']==1)  {echo $order['order_id']."\n"; $bugged++;} 
			if ($bugged>=2) break;
		}
		
		if ($bugged>=2) break;
	}
	
	if ($bugged>=2) echo "bugged\n";
	else echo "valid\n";
	

/*}*/

?>