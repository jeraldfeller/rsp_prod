<?php
	$error = '';	
	$message = '';
	
	$nID = tep_fill_variable('nID', 'get');
	$page_action = tep_fill_variable('page_action', 'get');
	$pages = tep_fill_variable('pages', 'post', array());
	
	$order_type = tep_fill_variable('order_type', 'post');

	$pulldowns = array(
		'order_type' => tep_draw_order_type_pulldown_bgdn('order_type', $order_type),
		'order_status' => tep_draw_orders_status_pulldown_bgdn('order_status', $order_type)
	);
	
	$vars = array(
		'pulldowns'=>$pulldowns,
	);
	#echo '<pre>'; print_r($vars); die;
	echo $twig->render('aom/aom_export_order_info.html.twig', array('user' => $user,'page' => $page, 'vars'=>$vars));

