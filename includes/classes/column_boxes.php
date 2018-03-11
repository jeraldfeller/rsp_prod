<?php
	class column_boxes {
		var $type;
		var $title;
		var $content;
		var $body;

        function __construct($type = 'left') {
			$this->type = $type;
			$this->title = '';
			$this->body = '';
			$this->content = '<table width="100%" cellspacing="0" cellpadding="0">';
		}
	
		function set_title($title) {
			$this->title = $title;
		}
		
		function set_content_layer($content = array()) {
			$this->body .= '<tr>';
			$count = count($content);
			$n = 0;
				while($n < $count) {
						if (isset($content[$n]['width'])) {
							$width = $content[$n]['width'];
						} else {
							$width = '100%';
						}
						if (isset($content[$n]['class'])) {
							$class = $content[$n]['class'];
						} else {
							$class = 'columnBoxLeftBody';
						}
						if (isset($content[$n]['colspan'])) {
							$colspan = ' colspan="'.$content[$n]['colspan'].'"';
						} else {
							$colspan = '';
						}
						if (isset($content[$n]['extra'])) {
							$extra = ' ' . $content[$n]['extra'];
						} else {
							$extra = '';
						}
						if (isset($content[$n]['align'])) {
							$align = $content[$n]['align'];
						} else {
							$align = 'left';
						}
					$this->body .= '<td width="'.$width.'" class="'.$class.'" align="'.$align.'"'. $colspan . $extra . '>'.$content[$n]['text'].'</td>';
					$n++;
				}
			$this->body .= '</tr>';
		}
		
		function generate_box() {
			$this->content .= '<tr><td height="10"><img src="images/pixel_trans.gif" height="10" width="1"></td></tr>'.
										'<tr><td class="columnBoxLeftHeader" width="100%">&nbsp;'.$this->title.'</td></tr>'.
										'<tr><td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td></tr>'.
										'<tr><td width="100%"><table width="100%" cellspacing="0" cellpadding="0">'.$this->body.'</table></td></tr>'.
										'<tr><td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td></tr>'.
										'<tr><td width="100%"><img src="images/left_dot_spacer.jpg" width="215" height="7"></td></tr>';
		}
		
		function return_box() {
			$this->content .= '</table>';
			return $this->content;
		}
	}
?>