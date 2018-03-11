<?php
require_once dirname(dirname(dirname(__FILE__))) . '/includes/application_top.php';

function is_admin() {
  if (isset($_SESSION) && isset($_SESSION['user_group_id']) && $_SESSION['user_group_id'] == 2) {
    return true;
  }
  return false;
}

if (!is_admin()) {
    echo "Access Denied";
    die;
}
?>
<!doctype html>
<html>
<head>
<title>Installs without Removals Tool</title>
<script data-cfasync="false" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script data-cfasync="false" language="javascript">
$(document).ready(function () {
    $.getJSON("http://<?php echo $_SERVER['SERVER_NAME']; ?>/lib/orders/installs_without_removals_json.php5?months=18", function (data) {
        $.each(data, function (key, value) {
            console.log(value);
            $("#none").remove();
            link = "<a href='http://<?php echo $_SERVER['SERVER_NAME']; ?>/lib/orders/installs_without_removals_json.php5?months=18&fix=" + value.address_id + "'>Fix address " + value.address_id + "</a>";
            $("#installs_wo_removals").append($("<li>Address ID: " + value.address_id + " - " + link + "</li>"));
        });
    });
});
</script>
</head>
<body>
<h1>Installs without Removals (Last 18 Months)</h1>
<ul id="installs_wo_removals">
<li id="none"><em>None</em></li>
</ul>
</body>
</html>
