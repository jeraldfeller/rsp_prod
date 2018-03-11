<?php
$address_id = (int) tep_fill_variable('address_id', 'post', 0);
$charge_type = tep_fill_variable('charge_type');
$extend_from = tep_fill_variable('extend_from');

if (!empty($address_id) && !empty($charge_type) && !empty($extend_from)) {
    $sql = "SELECT o.order_id as removal_order_id, o2.order_id as install_order_id, o.address_id, o2.date_completed as date_installed, a.house_number, a.street_name, a.city, ag.auto_remove_period, o2.user_id, o2.billing_method_id FROM " . TABLE_ORDERS . " o LEFT JOIN " . TABLE_ORDERS . " o2 ON (o.address_id = o2.address_id AND o2.order_type_id = '1') JOIN " . TABLE_ADDRESSES . " a ON (a.address_id = o.address_id) LEFT JOIN " . TABLE_USERS . " u ON (u.user_id = o2.user_id) LEFT JOIN " . TABLE_USERS_DESCRIPTION . " ud ON (u.user_id = ud.user_id) LEFT JOIN " . TABLE_AGENCYS . " ag ON (u.agency_id = ag.agency_id) WHERE o.order_type_id = '3' AND o.order_status_id IN ('1', '2', '5') AND o2.order_status_id = '3' AND o.address_id = '{$address_id}'";

    $query = $database->query($sql);
    if ($result = $database->fetch_array($query)) {
        if (empty($result['auto_remove_period']) || $result['auto_remove_period'] == 0 || $result['auto_remove_period'] == AUTOMATIC_REMOVAL_TIME) {
            $result['auto_remove_period'] = 2*AUTOMATIC_REMOVAL_TIME;
        }
        $days_to_extend = AUTOMATIC_REMOVAL_TIME;

        if ($extend_from == "today") {
            $extended_through = strtotime(date('Y-m-d') . " + {$days_to_extend} days");
        } else {
            $er_query = $database->query("SELECT MAX(extended_through) as extended_through FROM " . TABLE_EXTENDED_RENTALS . " er WHERE address_id = '{$address_id}'");
            $extended_through = strtotime(date('Y-m-d', $result['date_installed']) . " + " . $result['auto_remove_period'] . " days");
            if ($er_result = $database->fetch_array($er_query)) {
                if (!empty($er_result['extended_through']) && $er_result['extended_through'] > 0) {
                    $extended_through = $er_result['extended_through'];
                }
            }
            $extended_through = strtotime(date('Y-m-d', $extended_through) . " + {$days_to_extend} days");
        }

        if ($charge_type == "free") {
            $cost = 0;
        } else {
            $cost = 20;
        }

        $now = mktime();
        $sql = "INSERT INTO " . TABLE_EXTENDED_RENTALS . " (address_id, install_order_id, removal_order_id, user_id, extended_by, extended_date, extended_through, cost) VALUES ({$result['address_id']}, {$result['install_order_id']}, {$result['removal_order_id']}, {$result['user_id']}, {$user->fetch_user_id()}, {$now}, {$extended_through}, {$cost})";
        $database->query($sql);

        $cost_str = number_format($cost, 2);
        $extended_through_str = date("n/d/Y", $extended_through);

        // Even if the renewel is free, let's show it on the invoice.
        $account = new account($result['user_id'], '', $result['billing_method_id']);
        $account->apply_extended_rental($cost, $address_id, "{$result['house_number']} {$result['street_name']}, {$result['city']} rental extended through {$extended_through_str}");

        echo "<div class='alert alert-info'>\n";
        echo "<button type='button' class='close' data-dismiss='alert'>&times;</button>\n";
        echo "<span class='muted'>Rental at {$result['house_number']} {$result['street_name']}, {$result['city']} Extended through " . date('Y-m-d', $extended_through) . " for cost of \${$cost_str}.</span>\n";
        echo "</div>\n\n";

    } else {
        echo "<div class='alert alert-error'>\n";
        echo "<button type='button' class='close' data-dismiss='alert'>&times;</button>\n";
        echo "<span class='muted'>Error.  Rental not extended.</span>\n";
        echo "</div>\n\n";
    }
}
?>
<script language="javascript" data-cfasync="false">
function extendRental(address_id) {
    address = $("tr[data-address-id='" + address_id + "'] > td.address").html();
    $("#extendRentalTitle").html(address);
    $("#extendAddressId").val(address_id);
    $('#extendRentalModal').modal('toggle');
}
$(document).ready(function () {
    $("#extended-rentals").dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span4'i><'span8'p>>",
        "bFilter": true,
        "sPaginationType": "bootstrap",
        "bStateSave": false,
        "bSortClasses": false,
        "bLengthChange": false,
        "oLanguage": {
            "sEmptyTable": "No addresses found."
        },
        "iDisplayLength": 50
    });

    $("#extendRentalForm").submit(function () {
        $('#extendRentalModal').modal('hide');
        $('#form_submitted_modal').modal('show');
    });
});
</script>

<div class="modal fade" id="extendRentalModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="form-inline" id="extendRentalForm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalTitle">Extend Rental</h4>
      </div>
      <div class="modal-body">
        <h5 id="extendRentalTitle">Extend Rental</h5>
            <input type="hidden" id="extendAddressId" name="address_id" value="">
            <div class="controls controls-row">
                <label>Extend from:</label>
                <select name="extend_from" class="pull-right input-xlarge">
                    <option value="normal">End of previous rental period</option>
                    <option value="today">Today</option>
                </select>
            </div>
            <div class="controls controls-row">
                <label>Rental Fee:</label>
                <select name="charge_type" class="pull-right input-xlarge">
                    <option value="normal">Extended Rental - $20</option>
                    <option value="free">Free</option>
                </select>
            </div>
      </div>
      <div class="modal-footer">
        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
        <input type="submit" id="extendRentalSave" class="btn btn-primary" value="Extend rental">
      </div>
    </div>
    </form>
  </div>
</div>

<table id='extended-rentals' class='table table-condensed table-striped'>
<thead>
    <tr>
        <th>Address</th>
        <th>Agent</th>
        <th>Agency</th>
        <th>Days in Field</th>
        <th>Action</th>
    </tr>
</thead>
<tbody>
<?php
$now = mktime();
$sql = "SELECT o.order_id as removal_order_id, o2.order_id as install_order_id, o.address_id, o2.date_completed as date_installed, a.house_number, a.street_name, a.city, ag.auto_remove_period, concat(ag.name, ' (', ag.office, ')') as agency_name, concat(ud.lastname, ', ', ud.firstname) as agent_name FROM " . TABLE_ORDERS . " o LEFT JOIN " . TABLE_ORDERS . " o2 ON (o.address_id = o2.address_id AND o2.order_type_id = '1') JOIN " . TABLE_ADDRESSES . " a ON (a.address_id = o.address_id) LEFT JOIN " . TABLE_USERS . " u ON (u.user_id = o2.user_id) LEFT JOIN " . TABLE_USERS_DESCRIPTION . " ud ON (u.user_id = ud.user_id) LEFT JOIN " . TABLE_AGENCYS . " ag ON (u.agency_id = ag.agency_id) LEFT JOIN " . TABLE_EXTENDED_RENTALS . " er ON (er.address_id = a.address_id AND er.extended_through > '{$now}') WHERE o.order_type_id = '3' AND o.order_status_id IN ('1', '2', '5') AND o2.order_status_id = '3' AND o.address_id > 0 and er.extended_through IS NULL";

$query = $database->query($sql);
foreach($database->fetch_array($query) as $result)
{
    if (empty($result['auto_remove_period']) || $result['auto_remove_period'] == 0 || $result['auto_remove_period'] == AUTOMATIC_REMOVAL_TIME) {
        $result['auto_remove_period'] = 2*AUTOMATIC_REMOVAL_TIME;
    }
    $days_in_field = ceil(($now-$result['date_installed'])/(24*60*60));

    $action = "<a target='_blank' href='javascript:;' onclick='extendRental({$result['address_id']});'>Extend Rental <i class='icon-calendar'></i></a>";

    if ($days_in_field > $result['auto_remove_period']) {
        echo "<tr data-address-id='{$result['address_id']}'>\n";
        echo "<td class='address'>{$result['house_number']} {$result['street_name']}, {$result['city']}</td>\n";
        echo "<td>{$result['agent_name']}</td>\n";
        echo "<td>{$result['agency_name']}</td>\n";
        echo "<td>{$days_in_field}</td>\n";
        echo "<td>{$action}</td>\n";
        echo "</tr>\n";
    }
}
?>
</tbody>
</table>
