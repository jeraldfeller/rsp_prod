<?php
class split_page
{
	var $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page, $page_name;

    function __construct($query, $max_rows, $count_key = '*', $page_holder = 'page', $use_distinct = true)
	{
		global $database;

		$this->sql_query = $query;
		$this->page_name = $page_holder;

		if (array_key_exists($page_holder, $_GET)) {
			$page = $_GET[$page_holder];
		} elseif (array_key_exists($page_holder, $_POST)) {
			$page = $_POST[$page_holder];
		} else {
			$page = '';
		}

		if (empty($page) || !is_numeric($page)) $page = 1;
		$this->current_page_number = $page;

		$this->number_of_rows_per_page = $max_rows;

		$pos_to = strlen($this->sql_query);
		$pos_from = strpos($this->sql_query, ' from', 0);
		if ($use_distinct) {
			$pos_group_by = strpos($this->sql_query, ' group by', $pos_from);
			if (($pos_group_by < $pos_to) && ($pos_group_by != false)) $pos_to = $pos_group_by;
		}
		$pos_having = strpos($this->sql_query, ' having', $pos_from);
		if (($pos_having < $pos_to) && ($pos_having != false)) $pos_to = $pos_having;

		$pos_order_by = strpos($this->sql_query, ' order by', $pos_from);
		if (($pos_order_by < $pos_to) && ($pos_order_by != false)) $pos_to = $pos_order_by;

		if (strpos($this->sql_query, 'distinct') || strpos($this->sql_query, 'group by') && $use_distinct) {

			$count_string = 'distinct ' . $count_key;
		} else {
			$count_string = $count_key;
		}
		$count_query = $database->query("select count(" . $count_string . ") as total " . substr($this->sql_query, $pos_from, ($pos_to - $pos_from)));

		$count = $database->fetch_array($count_query);

		$this->number_of_rows = $count['total'];

		$this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

		if ($this->current_page_number > $this->number_of_pages) {
			$this->current_page_number = $this->number_of_pages;
		}

		$offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));
		if ($offset < 0) {
			$offset = 0;
		}
		$this->sql_query .= " limit " . $offset . ", " . $this->number_of_rows_per_page;
	}

	/* class functions */

	// display split-page-number-links
	function display_links($max_page_links, $parameters = '')
	{
		global $_SERVER, $request_type, $database;
		$PHP_SELF = $_SERVER['REQUEST_URI'];
		if (($pos = strpos($PHP_SELF, '?')) !== false) {
			$PHP_SELF = substr($PHP_SELF, 0, $pos);
		}

		$cur = $this->current_page_number;
		if ($cur > $this->number_of_pages)
			$cur = $this->number_of_pages;
		$display_links_string = '';

		$class = 'class="pageResults"';
		// BOM Mod:allow for a call when there are no rows to be displayed
		if ($this->number_of_pages > 0) {

			if (tep_not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';

			// previous button - not displayed on first page
			if ($cur > 1) $display_links_string .= '<a href="' . basename($PHP_SELF).'?'. $parameters . 'page=1" class="pageResults" title=" First "><u>[<<<&nbsp;First]</u></a>&nbsp;&nbsp;<a href="' . basename($PHP_SELF).'?'. $parameters . $this->page_name . '=' . ($cur - 1) . '" class="pageResults" title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>' . PREVNEXT_BUTTON_PREV . '</u></a>&nbsp;&nbsp;';

			// check if number_of_pages > $max_page_links
			$cur_window_num = intval($cur / $max_page_links);
			if ($cur % $max_page_links) $cur_window_num++;

			$max_window_num = intval($this->number_of_pages / $max_page_links);
			if ($this->number_of_pages % $max_page_links) $max_window_num++;

			// previous window of pages
			if ($cur_window_num > 1) $display_links_string .= '<a href="' . basename($PHP_SELF).'?'.$parameters . $this->page_name . '=' . (($cur_window_num - 1) * $max_page_links) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>';

			// page nn button
			for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
				if ($jump_to_page == $cur) {
					$display_links_string .= '&nbsp;<b>' . $jump_to_page . '</b>&nbsp;';
				} else {
					$display_links_string .= '&nbsp;<a href="' . basename($PHP_SELF). '?' . $parameters . $this->page_name . '=' . $jump_to_page . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' "><u>' . $jump_to_page . '</u></a>&nbsp;';
				}
			}

			// next window of pages
			if ($cur_window_num < $max_window_num) $display_links_string .= '<a href="' . basename($PHP_SELF). '?' . $parameters . $this->page_name . '=' . (($cur_window_num) * $max_page_links + 1) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>&nbsp;';

			// next button
			if (($cur < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '&nbsp;<a href="' . basename($PHP_SELF). '?' .  $parameters . 'page=' . ($cur + 1) . '" class="pageResults" title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><u>' . PREVNEXT_BUTTON_NEXT . '</u></a>&nbsp;&nbsp;<a href="' . basename($PHP_SELF). '?' .  $parameters . 'page=' . $this->number_of_pages . '" class="pageResults" title=" Last "><u>[Last&nbsp;>>>]</u></a>&nbsp;';

		} else {  // if zero rows, then simply say that
			$display_links_string .= '&nbsp;<b>0</b>&nbsp;';
		}
		// EMO Mod
		return $display_links_string;
	}

	// display number of total products found
	function display_count($text_output)
	{
		$to_num = ($this->number_of_rows_per_page * $this->current_page_number);
		if ($to_num > $this->number_of_rows) $to_num = $this->number_of_rows;

		$from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

		if ($to_num == 0) {
			$from_num = 0;
		} else {
			$from_num++;
		}

		return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
	}
}
?>
