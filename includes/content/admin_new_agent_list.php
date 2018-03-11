<?php

if (!isset($_SESSION) || !isset($_SESSION['user_group_id']) || $_SESSION['user_group_id'] != 2) {
  exit(json_encode(array('error' => true, 'msg' => 'Access denied.')));
}

// Default date
$t = new DateTime();
$t->sub(new DateInterval('P7D'));
$date = $t->getTimestamp();
if (!empty($_REQUEST['date']) && strtotime($_REQUEST['date'])) {
  $date = strtotime($_REQUEST['date']);
}

// This should match the first order of any user, since it's sorted ASC.
// Add the date_completed match, and you have the filter.
$s = "
  SELECT 
    ud.firstname, ud.lastname, 
    u.email_address, u.agency_id,
    o.date_completed, o.user_id, o.order_id,
    a.name as agency_name, a.office
  FROM orders o
  LEFT JOIN users u ON o.user_id = u.user_id
  LEFT JOIN users_description ud ON o.user_id = ud.user_ID
  LEFT JOIN agencys a ON u.agency_id = a.agency_id
  WHERE o.date_completed >= $date
  AND u.user_id NOT IN (SELECT o.user_id FROM orders oo WHERE o.user_id = oo.user_id AND oo.date_completed < {$date})
  GROUP BY o.user_id
  ORDER BY ud.lastname ASC, o.date_completed ASC
";
?>

<div class="row-fluid">
  <div class="span2">
    <div class="well well-small" style="text-align: center;">
      <form action="<?php $_SERVER['REQUEST_URI'] ?>" method="POST">
        <label for="date"><strong>First order completed on or after:</strong></label>
        <input 
          class="datepicker input-block-level" 
          placeholder="First order on or before date" 
          type="text" 
          name="date" 
          pattern="[0-9]{1,2}[-/]+[0-9]{1,2}[-/]+[0-9]{2,4}" 
          required="required" 
          value="<?php date('n/d/Y', $date) ?>"
          style="margin-bottom: 1em; text-align: center;">
        <button type="submit" class="btn btn-block" style="margin-top: -0.75em;"><i class="icon-search">&nbsp;</i> Find</button>
      </form>
    </div>
  </div>
  <div class="span9 offset1">
    <table class="table table-striped datatable">
      <thead>
        <tr><th>Last Name<th>First Name<th>Agency<th>Email<th>First Order Completed
      </thead>
      <tbody>
      <?php
      $q = $database->query($s);
      foreach($database->fetch_array($q) as $r){
        echo "
          <tr>
            <td><a href='admin_users.php?uID={$r['user_id']}&page_action=edit'>{$r['lastname']}</a>
            <td><a href='admin_users.php?uID={$r['user_id']}&page_action=edit'>{$r['firstname']}</a>
            <td><a href='admin_users.php?user_group_id=1&show_agency_id={$r['agency_id']}'>{$r['agency_name']} {$r['office']}</a>
            <td><a href='mailto:{$r['email_address']}'>{$r['email_address']}</a>
            <td><a href='admin_orders.php?oID={$r['order_id']}&page_action=view'>" . date('n/d/Y', $r['date_completed']) . "</a>
        ";
      }
      ?>
      </tbody>
      <tfoot>
        <tr><th>Last Name<th>First Name<th>Agency<th>Email<th>First Order Completed      
      </tfoot>
    </table>
  </div>
</div>

<script type="text/javascript">
var jq = jq || [];
jq.push(function(){
  $('.datepicker').datepicker();
  window.otable = $('.datatable').dataTable({
    "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span5'i><'span7'p>>",
    "sPaginationType": "bootstrap",
    "bStateSave": true,
    "sScrollX": "100%",
    "oLanguage": {
      "sEmptyTable": "<span class='label label-important'>No new agents with first order completed from <?= date('n/d/Y', $date) ?> to date.</span>"
    }
  });
});
</script>