<?php
	$box = new column_boxes('left');
	$box->set_title('Order History');

		if (($active_orders = tep_get_active_orders()) > 0) {
			
			$line_array = array();
			$line_array[] = array('text' => 'You have '.$active_orders.' active orders.');
			
			$box->set_content_layer($line_array);
		
			$line_array = array();
			$line_array[] = array('text' => '<a href="'.FILENAME_ORDER_VIEW.'?order_view=open">Click here to view your active orders.</a> ', 'extra' => 'NOWRAP');
			
			$box->set_content_layer($line_array);
			
			$line_array = array();
			$line_array[] = array('text' => '<a href="'.FILENAME_ORDER_VIEW.'?order_view=closed">Click here to view all previous orders.</a> ', 'extra' => 'NOWRAP');
			
			$box->set_content_layer($line_array);
			
		} else {
			
			$line_array = array();
			$line_array[] = array('text' => 'You have no active orders.', 'extra' => 'NOWRAP');
			
			$box->set_content_layer($line_array);
			
			$line_array = array();
			$line_array[] = array('text' => '<a href="'.FILENAME_ORDER_VIEW.'?order_view=open">Click here to view any previous orders.</a> ', 'extra' => 'NOWRAP');
			
			$box->set_content_layer($line_array);
			
		}
	
	$box->generate_box();
	
	echo $box->return_box();
?>