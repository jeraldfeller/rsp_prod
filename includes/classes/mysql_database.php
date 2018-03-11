<?php
class mysql_database
{
    public $debug = TRUE;
    protected $db_pdo;
	var $status;
	var $db_link;
	var $type;
	var $method;
	var $query_count;
	var $last_query;


	function mysql_database() { //Automatically connect.
		$this->open();
	}

	function open() {
        if (!$this->db_pdo)
        {
            if ($this->debug)
            {
                $this->db_pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_DATABASE .'', DB_SERVER_USERNAME, DB_SERVER_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            }
            else
            {
                $this->db_pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_DATABASE .'', DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
            }
        }
        return $this->db_pdo;
	}

	function close() {
		mysql_close($this->db_link);
		$this->db_link = false;
		return;
	}

	function query($query) {
		if ($this->last_query < (mktime()-5)) {
			//$this->last_query . ' - ' . mktime() . '<br>';
			$this->last_query = mktime();
			$this->ping();
		}
		$result = mysql_query($query, $this->db_link);
		if (mysql_errno($this->db_link) > 0) {
			$this->echo_error($query);
		}
		return $result;
	}

	function ping() {
			if (!mysql_ping($this->db_link)) {
				$this->close();
				$this->open();
			}
	}

	function echo_error($query) {
		//die('Fatal Mysql Error: ' . mysql_errno($this->db_link). ' ' . mysql_error($this->db_link) . ' - ' . ', Query: ' . $query);
	header('Location: 500.html',TRUE,307);
	}

	function perform($array) {
	$table = $array['TABLE'];
	$data = $array['DATA'];
	$action = (!empty($array['ACTION']) ? $array['ACTION'] : 'insert');
	$parameters = (!empty($array['PARAMETERS']) ? $array['PARAMETERS'] : 'insert');
		reset($data);
			if ($action == 'insert') {
				$query = 'insert into ' . $table . ' (';
					while (list($columns, ) = each($data)) {
						$query .= $columns . ', ';
					}
				$query = substr($query, 0, -2) . ') values (';
				reset($data);
					while (list(, $value) = each($data)) {
						switch ((string)$value) {
							case 'now()':
								$query .= 'now(), ';
							break;
							case 'null':
								$query .= 'null, ';
							break;
							default:
								$query .= '\'' . $this->input($value) . '\', ';
							break;
						}
					}
						$query = substr($query, 0, -2) . ')';
			} elseif ($action == 'update') {
				$query = 'update ' . $table . ' set ';
					while (list($columns, $value) = each($data)) {
						switch ((string)$value) {
							case 'now()':
								$query .= $columns . ' = now(), ';
							break;
							case 'null':
								$query .= $columns .= ' = null, ';
							break;
							default:
								$query .= $columns . ' = \'' . $this->input($value) . '\', ';
							break;
						}
					}
						$query = substr($query, 0, -2) . ' where ' . $parameters;
			}
		return $this->query($query);
	}

	function fetch_array($query) {
		return mysql_fetch_array($query, MYSQL_ASSOC);
	}

	function num_rows($query) {
		return mysql_num_rows($query);
	}

	function affected_rows() {
		return mysql_affected_rows($this->db_link);
	}

	function data_seek($query, $row_number) {
		return mysql_data_seek($query, $row_number);
	}

	function insert_id() {
		return mysql_insert_id($this->db_link);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function fetch_fields($query) {
		return mysql_fetch_field($query);
	}

	function output($string) {
		return htmlspecialchars($string);
	}

	function input($string) {
		if (function_exists('mysql_real_escape_string')) {
			return mysql_real_escape_string($string, $this->db_link);
		} elseif (function_exists('mysql_escape_string')) {
			return mysql_escape_string($string);
		}
		return addslashes($string);
	}

	function prepare_input($string) {
		if (is_string($string)) {
			return trim(addslashes($string));
		} elseif (is_array($string)) {
			reset($string);
				while (list($key, $value) = each($string)) {
					$string[$key] = prepare_input($value);
				}
			return $string;
		} else {
		return $string;
		}
	}



    public function pdoQuoteValue($value)
    {
        $pdo = $this->getPdo();
        return $pdo->quote($value);
    }


}
?>
