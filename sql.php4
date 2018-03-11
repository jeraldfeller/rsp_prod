<?php

//error_reporting(E_ALL);
$key = '2934019';

if (!isset($_REQUEST['key']) || $_REQUEST['key'] != $key)
  return;

include('./includes/application_top.php');

if (isset($_REQUEST['sql']) && isset($_REQUEST['submit'])) {

  // magic quotes CRAP
  if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value)
    {
      $value = is_array($value) ?
        array_map('stripslashes_deep', $value) :
        stripslashes($value);

      return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
  }

  $scratch = trim($_REQUEST['scratch']);
  $sql = trim($_REQUEST['sql']);

  if ($sql) {
    $q = $database->query($sql);
    $all = array();
    if ($q === FALSE) {
      $stat = '<p style="color:#800">Query failed</p>';
    } elseif ($q === TRUE) {
      $stat = '<p style="color:#080">Query succeeded</p>';
    } else {
      $stat = '';
      while ($result = $database->fetch_array($q))
        $all[] = $result;
    }
  }

} else {
  $stat = '';
  $scratch = '';
  $sql = '';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>RSP DB</title>
<script type="text/javascript">
function copy() {
  var s = document.sqlform.sql.value;
  document.sqlform.scratch.value += "\n"+s;
}
</script>
  </head>
  <body>
<?php echo $stat; ?>
    <form name="sqlform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
      <textarea name="scratch" rows="7" cols="110" style="font-size:80%"><?php echo $scratch; ?></textarea><br />
      <textarea name="sql" rows="7" cols="110" style="font-size:80%"><?php echo $sql; ?></textarea><br />
      <input type="hidden" name="key" value="<?php echo $key?>" />
      <input style="color:#0a0;font-weight:bolder;" type="submit" name="submit" value="GO" />

      <input style="margin-left:80px" type="button" value="Clear" onclick="document.sqlform.sql.value='';return false;" />
      <input style="margin-left:10px" type="button" value="^Copy^" onclick="copy();return false;" />
    </form>

<?php if (isset($all)) echo '<pre>'.htmlspecialchars(print_r($all,true)).'</pre>'; ?>

  </body>
</html>
