<?php
// set paper type and size
global $pdf;
$pdf = new Cezpdf('A4','portrait');

// RGB Colors
define('BLACK', '0,0,0');
define('GREY', '0.8,0.8,0.8');
define('DARK_GREY', '0.5,0.5,0.5');
define('WHITE', '0.99,0.99,0.99');
define('BLUE', '0,0,0.99');
define('RED', '0.99,0,0');
define('LIGHT_RED', '0.99,.3,0.3');
define('DARK_RED', '0.89,.1,0.1');

define('LEFT_MARGIN','30');
// The small indents in the Sold to: Ship to: Text blocks
define('TEXT_BLOCK_INDENT', '5');
define('SHIP_TO_COLUMN_START','300');
// This changes the 'Total', 'Sub-Total', 'Tax', and 'Shipping Method' text block
// position, for example if you choose to make the text a bigger font size you need to 
// tweak this value in order to prevent the text from clashing together
define('PRODUCT_TOTAL_TITLE_COLUMN_START','400');
define('RIGHT_MARGIN','30');


define('LINE_LENGTH', '552');
// If you have attributes for certain products, you can have the text wrap
// or just be written completely on one line, with the text wrap disabled
// it makes the tables smaller appear much better, of course that is only my opinion
// so I made this variable if anyone would like it to wrap.
define('PRODUCT_ATTRIBUTES_TEXT_WRAP', false);
// This sets the space size between sections
define('SECTION_DIVIDER', '15');
// Product table Settings
define('TABLE_HEADER_FONT_SIZE', '9');
define('HEADING_BACKGROUND', '0,0.32,0');
define('TABLE_HEADER_BKGD_COLOR', DARK_GREY);
define('PRODUCT_TABLE_HEADER_WIDTH', '530');
// This is more like cell padding, it moves the text the number
// of points specified to make the rectangle appear padded
define('PRODUCT_TABLE_BOTTOM_MARGIN', '2');
// Tiny indent right before the product name, again more like
// the cell padding effect
define('PRODUCT_TABLE_LEFT_MARGIN', '2');
// Height of the product listing rectangles
define('PRODUCT_TABLE_ROW_HEIGHT', '11');
// The column sizes are where the product listing columns start on the
// PDF page, if you make the TABLE HEADER FONT SIZE any larger you will
// need to tweak these values to prevent text from clashing together
define('PRODUCTS_COLUMN_SIZE', '365');
define('PRODUCT_LISTING_BKGD_COLOR',GREY);
define('MODEL_COLUMN_SIZE', '37');
define('PRICING_COLUMN_SIZES', '67');
$vilains = array("&#224;", "&#225;",  "&#226;", "&#227;", "&#228;", "&#229;", "&#230;", "&#231;", "&#232;", "&#233;", "&#234;", "&#235;", "&#236;", "&#237;", "&#238;", "&#239;", "&#240;", "&#241;", "&#242;", "&#243;", "&#244;", "&#245;", "&#246;", "&#247;", "&#248;", "&#249;", "&#250;", "&#251;", "&#252;", "&#253;", "&#254;", "&#255;", "&#223;","&#39;", "&nbsp;", "&agrave;", "&aacute;", "&atilde;","&auml;", "&Arond;", "&egrave;", "&aelig;", "&ecirc;", "&euml;", "&igrave;", "&iacute;", "&Iacute;", "&icirc;", "&iuml;", "&ograve;", "&oacute;", "&ocirc;", "&otilde;", "&ouml;", "&oslash;", "&ugrave;", "&uacute;", "&ucirc;", "&uuml;", "&ntilde;", "&ccedil;", "&yacute;", "&lt;","&gt;", "&amp;", '&#39;');
$cools = array('à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','÷','ø','ù','ú','û','ü','ý','þ','ÿ','ß','\'', ' ','à','á','ã','ä','å','è','æ','ê','ë','ì','í','î','Î','ï','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ñ','ç','ý','<','>','&', "'");


$pdf->selectFont(DIR_TEMPLATE . 'Helvetica.afm');
$pdf->setFontFamily(DIR_TEMPLATE . 'Helvetica.afm');

// company name and details pulled from the my store address and phone number
// in admin configuration mystore 
//$y = $pdf->ezText(STORE_NAME_ADDRESS,COMPANY_HEADER_FONT_SIZE);
//$y -= 8; 
global $page;
global $y;
global $midnight_future;
global $midnight_tonight;

$where = '';
							//Here we work out if it is today or tomorrow and change the where to match.
								if ($day_view == 'tomorrow') {
									$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
				
									//Check if tomorrow was a sunday, if so then extend that date.
										if (date("w", ($midnight_tonight+1)) == 0) {
											$midnight_tonight += (60*60*24);
										}

									$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
									
									$midnight_tonight = 0;
								} elseif ($day_view == 'tomorrow1') {
									$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), (date("d", tep_fetch_current_timestamp())+1), date("Y", tep_fetch_current_timestamp())); 
				
									//Check if tomorrow was a sunday, if so then extend that date.
										if (date("w", ($midnight_tonight+1)) == 0) {
											$midnight_tonight += (60*60*24);
										}
									//Now get the next day and work out if it is a sunday, if so then extend the date.
									$midnight_tonight += ((60*60*24));
										if (date("w", ($midnight_tonight+1)) == 0) {
											$midnight_tonight += (60*60*24);
										}
									
									$midnight_future = ($midnight_tonight + ((60*60*24) * 1));
									
									$midnight_tonight = 0;
								} else {
									$midnight_tonight = mktime(0, 0, 0, date("n", tep_fetch_current_timestamp()), date("d", tep_fetch_current_timestamp()), date("Y", tep_fetch_current_timestamp())); 
									$midnight_future = ($midnight_tonight + ((60*60*24) * 1));

									$midnight_tonight = 0;
								}

$page = 0;
$pdf->setLineStyle(1);

	if (!function_exists('page_layout')) {
		function page_layout($add_box = true) {
			global $pdf, $page, $y, $midnight_future, $user;
				$page++;
				
				//Do the header + footer.
					if ($page > 1) {
						//Line off the page.
	
						change_color(DARK_GREY);
						$pdf->addText(460,$y-3,9, '<i>Continued on next page...</i>');
						change_color(BLACK);
						$pdf->ezNewPage();
						change_color(BLACK);
					}

				$y = 805;
				
				change_color(BLACK);
				$pdf->addText(35,$y,14,'<b>Jobs for ' . date("l dS of F Y", ($midnight_future-1)) . '</b>');
				
				change_color(GREY);
				$y -= 12;
				$pdf->addText(40,$y,10,'Installations: ' . tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '1', '2', '', false));
				$pdf->addText(260,$y,10,'Service Calls: ' . tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '2', '2', '', false));
				$pdf->addText(480,$y,10,'Removals: ' . tep_count_installer_orders($user->fetch_user_id(), date("d", ($midnight_future-1)), date("n", ($midnight_future-1)), date("Y", ($midnight_future-1)), '3', '2', '', false));


				//$pdf->addText(450,$y + 11,12,'INV '.$agency_data['invoice_id']);

				$pdf->selectFont(DIR_TEMPLATE . 'Helvetica.afm');

				$y -= 24;
				
				change_color(DARK_GREY);

				$pdf->ezStartPageNumbers(560,30,12, 'left', 'Page {PAGENUM} of {TOTALPAGENUM}');
					if ($add_box) {
						change_color(GREY);
						$pdf->filledRectangle(28,$y - 40,540,50);
						change_color(BLACK);
						$pdf->addText(32,$y,8,'Service Type');
						$pdf->addText(32,$y-9,8,'Order Dates');
						$pdf->addText(32,$y-18,8,'Order #');
						$pdf->addText(32,$y-27,8,'Order Extra Items/Panels');
						$pdf->addText(32,$y-36,8,'# of Posts');
						$pdf->addText(140,$y,8,"Agent/Agency");
						$pdf->addText(140,$y-9,8,"Svc Level");
						$pdf->addText(140,$y-18,8,"Preferences");
						$pdf->addText(280,$y,8,"Address");
						$pdf->addText(280,$y-9,8,"ADC map coord");
						$pdf->addText(280,$y-18,8,"Directions");
						$pdf->addText(280,$y-27,8,"Agent Comments");
						$pdf->addText(490,$y,8,"Contact #s");
						
						//$pdf->line(94,($y-3),94,($y+10));
						//$pdf->line(254,($y-3), 254,($y+10));
						//$pdf->line(334,($y-3),334,($y+10));
						change_color(WHITE);
						//$pdf->line(138,($y),138,($y-36));
						$pdf->filledRectangle(134,$y - 40,2,50);
						$pdf->filledRectangle(274,$y - 40,2,50);
						$pdf->filledRectangle(484,$y - 40,2,50);
						//$pdf->ezSetY(60);
						//$pdf->ezText($total,12,array('justification'=>'right', 'aright' => '564'));
					}
				change_color(BLACK);

		}
	}

page_layout();
//Time to rock 'n roll.               

$y -= 50;
	$query = $database->query("select o.order_id, o.date_schedualed, o.date_added, o.user_id, os.order_status_name, o.order_type_id, ot.name as order_type_name, a.zip4, otiso.show_order_id, a.house_number, a.street_name, a.cross_street_directions, a.number_of_posts, a.city, a.address_post_allowed, a.zip, a.post_type_id, a.adc_number, s.name as state_name, c.name as county_name, sld.name as service_level_name, od.special_instructions, od.admin_comments, otiso.show_order_id as order_column from " . TABLE_ORDERS . " o left join " . TABLE_ADDRESSES . " a on (o.address_id = a.address_id) left join " . TABLE_INSTALLATION_COVERAGE_AREAS . " ica on (((ica.zip_4_first_break_start < a.zip4_start) or (ica.zip_4_first_break_start = a.zip4_start and ica.zip_4_first_break_end <= a.zip4_end)) and ((ica.zip_4_second_break_start > a.zip4_start) or (ica.zip_4_second_break_start = a.zip4_start and ica.zip_4_second_break_end >= a.zip4_end))) left join " . TABLE_INSTALLATION_AREAS . " ia on (ica.installation_area_id = ia.installation_area_id) left join " . TABLE_INSTALLERS_TO_INSTALLATION_AREAS . " itia on (ia.installation_area_id = itia.installation_area_id and (itia.date_covering <= o.date_schedualed and itia.date_end_covering >= o.date_schedualed)) left join " . TABLE_INSTALLERS_TO_ORDERS . " ito on (o.order_id = ito.order_id) left join " . TABLE_ORDERS_TO_INSTALLER_SHOW_ORDER . " otiso on (o.order_id = otiso.order_id) left join " . TABLE_STATES . " s on (a.state_id = s.state_id) left join " . TABLE_COUNTYS . " c on (a.county_id = c.county_id), " . TABLE_ORDER_TYPES . " ot, " . TABLE_ORDERS_STATUSES . " os, " . TABLE_ORDERS_DESCRIPTION . " od, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where " . ((!empty($midnight_tonight)) ? "o.date_schedualed >= '" . $midnight_tonight . "' and " : '') . "o.date_schedualed < '" . $midnight_future . "' " . ((empty($midnight_tonight)) ? " and o.order_status_id < '3' " : '') . " and o.order_status_id = '2' and o.address_id = a.address_id and o.order_type_id = ot.order_type_id and o.order_id = od.order_id and o.service_level_id = sld.service_level_id and o.order_status_id = os.order_status_id and ((ito.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id IS NULL and ia.installer_id = '" . $user->fetch_user_id() . "') or (ito.installer_id IS NULL and itia.installer_id = '" . $user->fetch_user_id() . "')) group by o.order_id order by order_column");
	$loop = 0;
		while($result = $database->fetch_array($query)) {

			$y -= 8;
				if ($y < 100) {
					page_layout();
					$y -= 58;
					$loop = 0;
				}
				if ($loop > 0) {
					change_color(GREY);
					$pdf->filledRectangle(48,$y - 1,500,1);
					change_color(BLACK);
					$y -= 14;
				}
			$agent_data = tep_fetch_agent_data($result['user_id']);
			$order = new orders('fetch', $result['order_id']);
			$order_data = $order->return_result();
			$adc = str_replace('_', ' ', $result['adc_number']);
										
			$order_description = $result['house_number'].' ' .$result['street_name'].'<br>'.$result['city'].' '.$result['state_name'].' '.$result['zip4'].'<br />'.((!empty($adc)) ? $adc . '<br>' : '') . $result['cross_street_directions'].'<br />'.$result['special_instructions'].'<br />'.$result['admin_comments'] . (($result['address_post_allowed'] == '0') ? '<br><b>Posts may not be allowed at this address.</b>' : '');
				if ($order_data['order_type_id'] == '2') {
					$sub_query = $database->query("select service_call_reason_id, service_call_detail_id from " . TABLE_ORDERS_SERVICE_CALL_OPTIONS . " where order_id = '" . $result['order_id'] . "' limit 1");
					$sub_result = $database->fetch_array($sub_query);
					$string = '<br><br>Service Call Reason:';
						if ($sub_result['service_call_reason_id'] == '1') {
							$string.= '<br>Exchange Rider';
							$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
								while($equip_result = $database->fetch_array($equip_query)) {
									$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
								}
						} elseif ($sub_result['service_call_reason_id'] == '2') {
							$string.= '<br>Install New Rider or BBox';
															
							$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
								while($equip_result = $database->fetch_array($equip_query)) {
									$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
								}
						} elseif ($sub_result['service_call_reason_id'] == '3') {
							$string.= '<br>Replace/Exchange Agent SignPanel';
							$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
								while($equip_result = $database->fetch_array($equip_query)) {
									$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
								}
						} elseif ($sub_result['service_call_reason_id'] == '4') {
							$string.= '<br>Post Leaning/Straighten Post';
								if ($sub_result['service_call_detail_id'] == '1') {
									$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weather';
								} elseif ($sub_result['service_call_detail_id'] == '2') {
									$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Improper Installation';
								} elseif ($sub_result['service_call_detail_id'] == '3') {
									$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Someone moved Post';
								} elseif ($sub_result['service_call_detail_id'] == '4') {
									$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other';
								}
						} elseif ($sub_result['service_call_reason_id'] == '5') {
							$string.= '<br>Move Post';
							//Check if any posts were marked as lost and jot them down.
							$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
								while($equip_result = $database->fetch_array($equip_query)) {
									$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
								}
						} elseif ($sub_result['service_call_reason_id'] == '6') {
							$string.= '<br>Install equipment forgotten at install';
							$equip_query = $database->query("select e.name, eto.method_id from " . TABLE_EQUIPMENT . " e, " . TABLE_EQUIPMENT_TO_ORDERS . " eto where eto.order_id = '" . $result['order_id'] . "' and eto.equipment_id = e.equipment_id");
								while($equip_result = $database->fetch_array($equip_query)) {
									$string.= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(($equip_result['method_id'] == '1') ? 'Install': 'Remove') . ' ' .$equip_result['name'];
								}
						} elseif ($sub_result['service_call_reason_id'] == '7') {
							$string.= '<br>Other';
						}
					$order_description .= str_replace($vilains, $cools, $string);
				}
			$final_y = $y;
			$temp_y = $y;
			$pdf->addText(30,$temp_y,9,'<b>'.$result['order_type_name'].'</b>');
			$temp_y -= 10;
			$pdf->addText(30,$temp_y,9,'Complete: ' . date("n/d/Y", $result['date_schedualed']));
			$temp_y -= 10;
			$pdf->addText(30,$temp_y,9,'Date Ordered: ' . date("n/d/Y", $result['date_added']));
			$temp_y -= 10;
			$pdf->addText(30,$temp_y,9,$result['order_id']);
			//$pdf->addText(30,$y-40,9,'Order Extra Items/Panels');
			$pdf->ezSetY($temp_y);

			$temp_y = $pdf->ezText(strip_tags(str_replace(array('<br>', '<br />'), "\n", str_replace('<img src="images/pixel_trans.gif" height="3" width="1">', '<br>', tep_create_view_equipment_string($order_data['optional'], true)))),9,array('justification'=>'left', 'aright' => 130, 'aleft' => 30));
			$temp_y -= 10;
			$pdf->addText(30,$temp_y,9,'# of Posts: ' .  $result['number_of_posts']);
				if ($result['order_type_id'] > 1) {
					$temp_y -= 10;
					$pdf->addText(30,$temp_y,9,tep_fetch_equipment_name($result['post_type_id']));
				}
				if ($temp_y < $final_y) {
					$final_y= $temp_y;
				}
			
			$temp_y = $y;
			$pdf->ezSetY($temp_y);
			$temp_y = $pdf->ezText($agent_data['firstname'] .' ' . $agent_data['lastname'] . ' / ' . $agent_data['name'],9,array('justification'=>'left', 'aright' => 270, 'aleft' => 138));
			$temp_y -= 10;
			$pdf->addText(138,$temp_y,9,$result['service_level_name']);
				if (tep_agent_has_preferences($result['user_id'], $result['order_type_id'])) {
					$pdf->ezSetY($temp_y);
					$temp_y = $pdf->ezText(str_replace(array('<br>', '<br />'), "\n", tep_create_agent_preferences_string($result['user_id'], $result['order_type_id'])),9,array('justification'=>'left', 'aright' => 270, 'aleft' => 138));
				}
				if ($temp_y < $final_y) {
					$final_y= $temp_y;
				}
				
			$temp_y = $y;
			$pdf->ezSetY($temp_y);
			$temp_y = $pdf->ezText(str_replace(array('<br>', '<br />'), "\n", $order_description),9,array('justification'=>'left', 'aright' => 480, 'aleft' => 278));
			
				if ($temp_y < $final_y) {
					$final_y= $temp_y;
				}
				
			$temp_y = $y;
				for ($n = 0, $m = count($agent_data['phone_numbers']); $n < $m; $n++) {
					$pdf->ezSetY($temp_y);
					$temp_y = $pdf->ezText($agent_data['phone_numbers'][$n] . (($n == 0) ? ' - Cell' : (($n == 2) ? ' - Fax' : '')),9,array('justification'=>'left', 'aright' => 566, 'aleft' => 488));
				}
				if ($temp_y < $final_y) {
					$final_y= $temp_y;
				}
				
			$y = $final_y;
			$loop++;	
		}
	$y = 120;

	change_color(BLACK);
	$y -= 40;
	$pdf->ezSetY($y);
	$pdf->ezText('',8,array('justification'=>'center'));
	$y -= 13;
	$pdf->ezSetY($y);
	$pdf->ezText('Realty Sign Post Company - P.O. Box 641 - McLean - VA - 22101 0641',8,array('justification'=>'center'));
	$y -= 13;
	$pdf->ezSetY($y);
	$pdf->ezText('Fax & VM 703-99594567 OR 202-478-2131 - Information: 202-256-0170 - Email: Info@RealtySignPost.com',8,array('justification'=>'center'));



?>
