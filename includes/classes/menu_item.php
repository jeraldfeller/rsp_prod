<?php
	class menu_item {
		var $menu_name;
		var $menu_contents;
		var $menu_id;
        function __construct($menu_box) {
				global $database, $user;
					$query = $database->query("select page_group_id from " . TABLE_PAGES_GROUPS . " where file_name = '" . $menu_box . "' limit 1");
					$result = $database->fetch_array($query);
					$this->menu_id = $result['page_group_id'];
						if (($result['page_group_id'] != NULL) && ($user->user_can_view_menu($this->menu_id))) {
							echo $this->build_menu();
						}
			}
			
			function build_menu() {
				global $database, $language_id, $user;
				
					$group_id = $user->fetch_user_group_id();
					$box = new column_boxes('left');
	
					$query = $database->query("select name from " . TABLE_PAGES_GROUPS_DESCRIPTION . " where page_group_id= '" . $this->menu_id . "' and language_id = '" . $language_id . "' limit 1");
					$result = $database->fetch_array($query);
					
					$box->set_title($result['name']);
					
					$query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp where p.page_group_id = '" . $this->menu_id . "' and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $group_id . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "'");
						foreach($database->fetch_array($query) as $result){
							$line_array = array();
							
							$line_array[] = array('text' => '<a href="'.$result['page_url'].'">'.$result['name'].'</a> ', 'extra' => 'NOWRAP');
							
							$box->set_content_layer($line_array);
						}
						
					$box->generate_box();
	
					return $box->return_box();
			}
	
	}

?>