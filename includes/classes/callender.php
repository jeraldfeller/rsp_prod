<?php
	class callender {
		var $calender_string, $current_day, $current_month, $current_year, $selected_month, $selected_day, $width, $height, $selected_year, $view_type, $link_template;

        function __construct($height='260', $width = '210', $selected_day = '', $selected_month = '', $selected_year = '') {
			$this->set_current_data();
			$this->view_type = 'day';
				if (!empty($selected_day)) {
					$this->selected_day = $selected_day;
				} else {
					$this->selected_day = $this->current_day;
				}
				if (!empty($selected_month)) {
					$this->selected_month = $selected_month;
				} else {
					$this->selected_month = $this->current_month;
				}
				if (!empty($selected_year)) {
					$this->selected_year = $selected_year;
				} else {
					$this->selected_year = $this->current_year;
				}
			$this->width = $width;
			$this->height=$height;
		}
		
		function set_current_data() {
			$this->current_month = date("n", mktime());
			$this->current_day = date("d", mktime());
			$this->current_year = date("Y", mktime());
		}
		
		
		function set_link_template($template) {
			$this->link_template = $template;
		}
		
		function day_is_special($day) {
			if (tep_date_is_holiday($day, $this->selected_month, $this->selected_year)) {
				return true;
			} else {
				return false;
			}
		}
		
		function generate_day_link($day) {
			$month = $this->selected_month;
			$year = $this->selected_year;
			$template = $this->link_template;
			$view_type = $this->view_type;
			eval("\$return_string = \"$template\";");
			return $return_string;
		}
		
		function select_view_type() {
		
		}
		
		function fetch_class_type($day) {
			if ($day == $this->selected_day) {
				return 'callenderSelectedDay';
			} elseif (($day == $this->current_day) && ($this->selected_month == $this->current_month) && ($this->selected_year == $this->current_year)) {
				return 'callenderThisDay';
			} elseif ($this->day_is_special($day)) {
				return 'callenderSpecialDay';
			} else {
				return 'callenderCurrentDay';
			}
		}
		
		function generate_day_view() {
			$this->callender_string = '';
			$tomorrow_month = date("n", mktime(0, 0, 1, $this->selected_month+1, ($this->selected_day), $this->selected_year));
			$tomorrow_year = date("Y", mktime(0, 0, 1, $this->selected_month+1, ($this->selected_day), $this->selected_year));
			$tomorrow_day = date("d", mktime(0, 0, 1, $this->selected_month+1, ($this->selected_day), $this->selected_year));
			$yesturday_month = date("n", mktime(0, 0, 1, $this->selected_month-1, ($this->selected_day), $this->selected_year));
			$yesturday_day = date("d", mktime(0, 0, 1, $this->selected_month-1, ($this->selected_day), $this->selected_year));
			$yesturday_year = date("Y", mktime(0, 0, 1, $this->selected_month-1, ($this->selected_day), $this->selected_year));
			$this->callender_string .= '<table width="'.$this->width.'" cellspacing="1" cellpadding="0"><tr><td class="main" width="100%"><b><a href="'.PAGE_URL.'?view_type='.$this->view_type.'&day='.$yesturday_day.'&month='.$yesturday_month.'&year='.$yesturday_year.'"><-</a>&nbsp;&nbsp;'.date("F, Y", mktime(0, 0, 1, $this->selected_month, $this->selected_day, $this->selected_year)).'&nbsp;&nbsp;<a href="'.PAGE_URL.'?view_type='.$this->view_type.'&day='.$tomorrow_day.'&month='.$tomorrow_month.'&year='.$tomorrow_year.'">-></a></b></td></tr></table>';
			$this->callender_string .= '<table class="callenderBox" width="'.$this->width.'" height="'.$this->height.'" cellspacing="1" cellpadding="0">';
			
			$indiv_width = ($this->width/7);
			//Count days in month.
			$days_in_month = date("t", mktime(0, 0, 1, $this->selected_month, $this->selected_day, $this->selected_year));
			$days_in_last_month = date("t", mktime(0, 0, 1, ($this->selected_month-1), $this->selected_day, $this->selected_year));
			//Now work out extra days to beginning of month,
			$extra_beginning_days = date("w", mktime(0, 0, 0, $this->selected_month, 1, $this->selected_year));
			//Now work out extra days to end of month.
			$extra_end_days = 6 - date("w", mktime(0, 0, 0, $this->selected_month, $days_in_month, $this->selected_year));
			
			$total_days = $days_in_month+$extra_beginning_days+$extra_end_days;
			
			$indiv_height = (($this->height -50)/($total_days/7));
			$day_array = array('S', 'M', 'T', 'W', 'T', 'F', 'S');
			$n = 0;
			$this->callender_string .='<tr>';
				while($n < count($day_array)) {
					$this->callender_string .= '<td class="callenderHeader" width="'.$indiv_width.'" height="10" align="center" valign="middle">'.$day_array[$n].'</td>';
					$n++;
				}
			//Now loop.
			$n = 1;
			$this->callender_string .='</tr><tr>';
				while($n <= $total_days) {
						if ($n <= $extra_beginning_days) {
							//Last Month
							$this->callender_string .= '<td class="callenderGreyDay" width="'.$indiv_width.'" height="'.$indiv_height.'" align="center" valign="middle">'.($days_in_last_month-($extra_beginning_days-($n+1))).'</td>';
						} elseif ($n > ($extra_beginning_days + $days_in_month)) {
							//Next Month
							$this->callender_string .= '<td class="callenderGreyDay" width="'.$indiv_width.'" height="'.$indiv_height.'" align="center" valign="middle">'.($n-($days_in_month+$extra_beginning_days)).'</td>';
						} else {
							//This Month
							$class = $this->fetch_class_type($n-$extra_beginning_days);
								if ($class != 'callenderSelectedDay') {
									$this->callender_string .= '<td class="'.$class.'" width="'.$indiv_width.'" height="'.$indiv_height.'" align="center" valign="middle">'.$this->generate_day_link($n-$extra_beginning_days).'</td>';
								} else {
									$this->callender_string .= '<td class="'.$class.'" width="'.$indiv_width.'" height="'.$indiv_height.'" align="center" valign="middle">'.($n-$extra_beginning_days).'</td>';
								}
						}
						if (($n%7 == 0) && ($n != $total_days)){
							//End of Week.
							$this->callender_string .= '</tr><tr>';
						}
					$n++;
				}
			$this->callender_string .= '</tr>';
			$this->callender_string .= '</table>';
		}
		
		function generate_week_view() {
		
		}
		
		function generate_calender() {
			if ($this->view_type == 'day') {
				$this->generate_day_view();
			} else {
				$this->generate_week_view();
			}
		}
		
		function return_callender() {
			return $this->callender_string;
		}
		
	}
?>