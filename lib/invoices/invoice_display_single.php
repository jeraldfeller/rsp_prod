<?php
/*
 * Part of Realty Sign Post (c) 2013 Realty Sign Post.
 * Description: General shared functions that make the invoicing system work. Need to be included in all invoice pages.
 *
 * Author: Brad Berger <brad@brgr2.com>
 * Last Updated:  4/6/2013
 * Updated By: Brad Berger <brad@brgr2.com>
 *
 */

// Make sure everything needed is included.
// Include and get the variables needed.
require_once 'invoice_functions.php';

/* View single invoice with $month and $year and other functions included. Cannot stand alone! */
if (isset($_GET['email_this']) && $_GET['email_this']) {
  $this->change_template_file('blank.tpl');
}

// Make sure user_id come from query string
if (array_key_exists("user_id", $_GET)) {
    $user_id = $_GET["user_id"];
} else {
    $user_id = 0;
}

// Set up the invoice id to be displayed.
$invoice_id = $year . "-" . $month . "-";
if($user_id) {
  $invoice_id .= "0-".$user_id;
} else {
  $invoice_id .= $agency_id."-0";
}

?>
<style type="text/css">
tr.new-row input, tr.edit-* input {
  /* Vintage layout fix horizontal scroll */
  padding: 0.2em;
  font-size: 0.8em;
}
</style>
<div class="invoice-container">
  <div class="invoice-header">
    <h2><?php echo date('F', mktime(0, 0, 0, $month, 1, $year)), ' ', $year; ?>     <span class="pull-right">Invoice #<?php echo $invoice_id; ?></span></h2>
    <address><?php echo get_invoice_address($user_id, $agency_id); ?></address>
  </div>

  <div id="invoice"></div>
  <div class="row-fluid">
    <div class="span4 offset8">
      <div class="row-fluid"><div class="span8" style="text-align: right;"><h5>Current Month Charges</h5></div><div class="span4" style="text-align: right;"><h5><span class="invoice-subtotal"></span></h5></div></div>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span4 offset8">
      <div class="row-fluid"><div class="span8" style="text-align: right;"><h5>Balances from Previous Invoices</h5></div><div class="span4" style="text-align: right;"><h5>$<span class="previous-balance total">0.00</span></h5></div></div>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span4 offset8">
      <div class="row-fluid"><div class="span8" style="text-align: right;"><h3>Total Amount Due</h3></div><div class="span4" style="text-align: right;"><h3><span class="invoice-total"><i class="icon-spinner icon-spin">&nbsp;</i></span></h3></div></div>
    </div>
  </div>
  <script data-cfasync="false">
    var month = <?php echo $month ?>;
    var year = <?php echo $year ?>;
    var agency_id = <?php echo $agency_id ?>;
    var user_id = <?php echo $user_id ?>;

    Date.prototype.toDateString = function () {
        var year = this.getFullYear();
        var month = this.getMonth() + 1;
        var day = this.getDate();
        return month + "/" + day + "/" + year;
    };

    $(document).ready(function () {

      get_invoice_items();

      $.extend( $.fn.dataTableExt.oSort, {
        "dollars-pre": function (a) {
          var x = a.replace( /\$/, "" );
          return parseFloat( x );
        },
        "dollars-asc": function (a, b) {
          return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },
        "dollars-desc": function ( a, b ) {
          return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
      });

      
      // Fix legacy sizing issues.
      if($('table').length > 10 && $(window).width() < 1600) {
        $('.invoice-container, tbody.invoice-items').css('width',$(window).width() - 300 + "px");
      }
      
      $('body').on('click', 'a.delete-item', function() {
        var id = $(this).attr('data-id');
        var tr = $(this).parent().parent('tr');
        var ok = confirm("Are you sure you want to delete this item, #" + id + " ?");
        if (ok === true) {
          $.ajax({
            url: '/lib/invoices/invoice_delete_item.php5?account_item_id=' + id,
            cache: false,
            dataType: 'json',
            success: function(data) {
              if (data.error != undefined && data.error === false) {
                tr.remove();
                get_invoice_total();
              } else {
                alert('Error: ' + data.msg);
              }
            },
            error: function(a, _str, c) {
              alert('Error. ' + _str);
            }
          });
        }
      });

      $('body').on('click', 'a.btn-save-new-item', function() {

        var id = $(this).parent().parent('tr').attr('data-timestamp');
        var obj = {
          account_item_id: id,
          date_added: 0,
          agency_id: agency_id,
          month: month,
          year: year,
          user_id: user_id
        }
        $.each($('input'), function() {
          if ($(this).hasClass(id)) {
            obj[$(this).attr('name')] = $(this).val();
          }
        });

        // Convert date_added to a timestamp if it's not already.
        if(isNaN(obj.date_added)) {
          var d = new Date(Date.parse(obj.date_added));
          obj.date_added = d.getTime() / 1000;
        }

        $.ajax({
          url: '/lib/invoices/invoice_save_item.php5',
          cache: false,
          data: obj,
          dataType: 'json',
          success: function(data) {

            if (data.error == undefined || data.error === false) {
              $('tr.edit-' + id).remove();
            }
            $('td.date.item-' + id).html(d.toDateString());
            $('td.reason.item-' + id).html(obj.reason);
            $('td.details.item-' + id).html(obj.details);
            $('td.total.item-' + id).html(obj.total);
            $('td.buttons.item-' + id).html('');

            get_invoice_total();
          },
          error: function(_a, _str, _c) {
            alert(_str);
          }
        });

      });
<?php
if (is_admin()) {
?>
      $('body').on('click', 'a.btn-new-item', function() {

        var d = new Date();
        var id = Math.round(d.getTime() / 1000);
        if ($('tr.' + id).html() != undefined) {
          return false;
        }
        var str = '<tr class="new-row" data-timestamp="' + id + '">';
        str += '<td class="date item-' + id + '"><input type="text" name="date_added" class="input-small ' + id + '" value="' + d.toDateString() + '">';
        str += '<td class="reason item-' + id + '"><div class="reason-options"><input name="reason_radio" type="radio" class="check-button" data-id="' + id + '"> Check<br><input name="reason_radio" type="radio" class="adjustment-button"> Adjustment<br><input name="reason_radio" type="radio" checked="checked"> Other</div><input type="text" name="reason" class="input-medium ' + id + '" value="">';
        str += '<td class="details item-' + id + '"><input type="text" name="details" required="required" class="' + id + '" value="">';
        str += '<td>';
        str += '<td class="total item-' + id + '"><input type="text" name="total" required="required" class="input-mini ' + id + '" value="0">';
        str += '<td class="create-buttons item-' + id + '"><a href="javascript:;" class="btn btn-mini cancel-item"><i class="icon-remove"></i> Cancel</a><br><a data-loading="Saving" class="btn btn-mini" onclick="saveItem(' + id + ')"><i class="icon-save"></i> Save</a>';
       
        // Append to end of the table.
        $('tbody.invoice-items').append(str);

      });
      
      $('body').on('click', 'a.cancel-item', function() {
        $(this).parent().parent('tr').remove();
      });

      $('body').on('click','.check-button', function(){
        var id = $(this).attr('data-id');
        $(this).parent().parent('td').html('Check Date<input type="text" name="check_date" class="input-block-level datepicker ' + id + '" required="required" placeholder="Check Date"><br>Check #<input type="text" class="' + id + ' input-block-level" name="check_number" required="required" placeholder="Check #">');
        $('.datepicker').datepicker();
      });
      $('body').on('click','.adjustment-button',function(){
        $(this).parent().next('input[name=reason]').val('Adjustment');
      });


      $('body').on('click', 'a.edit-item', function() {
        var id = $(this).attr('data-id');

        var cls = 'edit-' + id;
        if ($('tr.' + cls).html() != undefined) {
          return false;
        }
        var str = '<tr class="' + cls + '">';
        str += '<td><input type="text" name="date_added" class="input-small ' + id + '" value="' + $('tr.' + id + ' td.date').html() + '">';
        str += '<td><div class="reason-options"><input name="reason_radio" type="radio" class="check-button" data-id="' + id + '">Check<br><input name="reason_radio" type="radio" class="adjustment-button"> Adjustment<br><input name="reason_radio" type="radio" checked="checked"> Other<br></div><input type="text" name="reason" class="input-medium ' + id + '" value="' + $('tr.' + id + ' td.reason').html() + '">';
        str += '<td><input type="text" name="details" required="required" class="' + id + '" value="' + $('tr.' + id + ' td.details').html() + '">';
        str += '<td>';
        str += '<td><input type="text" name="total" required="required" class="input-mini ' + id + '" value="' + $('tr.' + id + ' td.total').html() + '">';
        str += '<td><a href="javascript:;" class="btn btn-mini cancel-item"><i class="icon-remove"></i> Cancel</a><br><a data-loading="Saving" class="btn btn-mini" onclick="saveItem(' + id + ')"><i class="icon-save"></i> Save</a>';
        $(this).parent().parent('tr').after(str);
      });
<?php
}
?>

      $('body').on('click', 'a.print-invoice', function() {
        $('body').html($('.invoice-container').html()).css('padding-top', 0).css('background', '#fff');
        $('.btn').remove();
      });

    });

<?php
if (is_admin()) {
?>
    function saveItem(id) {
  
      var obj = {
        account_item_id: id,
        date_added: 0,
        agency_id: agency_id,
        month: month,
        year: year,
        user_id: user_id,
        reason: ""
      };
      
      // Flag if a check, to later ensure value is < 0;
      var isCheck = false;
      
      $.each($('input.' + id), function(k,v) {
        
        if(( $(v).attr('name') == "check_date" || $(v).attr('name') == "check_number") && obj.reason == "") {

          // Make sure not empty.
          if($('input.' + id + '[name=check_number]').val() == "" || $('input.' + id + '[name=check_date]').val() == "") {
            if($('input.' + id + '[name=check_date]').val() == "") {
              $('input.' + id + '[name=check_date]').val('<?php echo date('n/d/Y'); ?>');
            }
          }
          
          // Not empty. Set the reason, combine check # and date.
          obj.reason = "Check #" + $('input.' + id + '[name=check_number]').val() + ", Received " + $('input.' + id + '[name=check_date]').val();
          
          // It's a check.
          isCheck = true;
          
        } else if ( $(v).attr('name') == "total") {
            // Remove dollar signs and commas
            obj['total'] = $(this).val().replace(/,/g, '').replace(/\$/, '');
        } else {
          obj[$(this).attr('name')] = $(this).val();
        }
      });

      // Now, to catch previous checks, to a search in obj.reason for "check".
      if(obj.reason.indexOf("Check") >= 0 || obj.reason.indexOf("check") >= 0) {
        isCheck = true;
      }
      
      // If it's a check, make sure it's a negative number.
      if(isCheck == true && obj.total > 0) {
        obj.total *= -1;
      }

      // Convert date_added to a timestamp if it's not already.
      if(isNaN(obj.date_added)) {
          var d = new Date(Date.parse(obj.date_added));
        obj.date_added = d.getTime() / 1000;
      }

      $.ajax({
        url: '/lib/invoices/invoice_save_item.php5',
        cache: false,
        data: obj,
        dataType: 'json',
        success: function(data) {
          if (data.error == undefined || data.error === false) {
            $('tr.edit-' + id).remove();
          }

          $('td.date.item-' + id).html(d.toDateString());
          $('td.reason.item-' + id).html(obj.reason);
          $('td.details.item-' + id).html(obj.details);
          $('td.total.item-' + id).html('$' + obj.total);
          $('td.create-buttons.item-' + id).html('');

          get_invoice_total();
        },
        error: function(_a, _str, _c) {
          alert(_str);
        }
      });
    }
<?php
}
?>

    function get_invoice_total() {
    
      var amt = 0;
      $.each($('.total'), function(k, v) {

        // Make the value a float. Seems like JS doesn't like the "," in numbers, so get rid of that, too.
        var this_amt = parseFloat($(v).html().replace(/,/g, '').replace(/\$/, ''));
        amt += this_amt;

      });
      var str = new String((amt).toFixed(2));
      $('.invoice-subtotal').html('$' + (amt - parseInt($('.previous-balance').html().replace(/,/g, '').replace(/\$/, ''))).toFixed(2));
      $('.invoice-total').html('$' + str);

      if (amt <= 0) {
          $('#pay-invoice-total').val(0);
          $('.pay-invoice').addClass('hidden');
      } else {
          $('#pay-invoice-total').val(amt);
          $('.pay-invoice').removeClass('hidden');
      }

      return amt;
      
    }

    function get_invoice_items() {

      $('a.edit-item').attr('disabled', 'disabled');
      var is_admin = <?php
      if (isset($_SESSION['user_group_id']) && $_SESSION['user_group_id'] == 2) {
        echo 'true';
      } else {
        echo 'false';
      }
      ?>;
          
      var data = {
        month: <?php echo $month ?>,
        year: <?php echo $year ?>,
        agency_id: <?php echo $agency_id ?>,
        user_id: <?php echo $user_id ?>
      };

      $.ajax({
        url: '/lib/invoices/invoice_items.php5',
        cache: false,
        data: data,
        dataType: 'json',
        success: function(data) {
          $('a.edit-item').removeAttr('disabled');
          var html = '<table id="invoice-line-items" class="table table-striped table-hover"><thead><tr><th>Date<th>Description<th>Details<th>Agent<th>Total<th></thead><tbody class="invoice-items">';
          $.each(data, function(k, v) {

            // First, make sure we're not dealing with the previous_balance: xxx key/value pair.
            if ($.isNumeric(k) == false) {
              if (k == "previous_balance" && v != 0) {
                // Balances from previous invoices.
                // Current month's charges
                // Total Amount Due
                $('.previous-balance').html(v);
                //html += '<tr class="previous_balance"><td>&nbsp;<td>&nbsp;<td>&nbsp;<td><strong>Previous Balance</strong><td class="total">' + v + '<td>&nbsp;';
              }
              return true;
            }

            var total = parseFloat(v.total);
            var options = '';
            if (is_admin) {
              options += ' <a class="btn btn-mini edit-item" data-id="' + v.account_item_id + '" href="javascript:;"><i class="icon-edit"></i> Edit</a>';
              options += ' <a class="btn btn-mini btn-danger delete-item" data-id="' + v.account_item_id + '" href="javascript:;"><i class="icon-trash"></i> Delete</a>';
            }
            
            var this_class = 'item-' + v.account_item_id;
            html += '<tr class="' + v.account_item_id + '">';
            html += '<td class="date ' + this_class + '">' + v.order_datecompleted;
            html += '<td class="reason ' + this_class + '">' + v.reason;
            html += '<td class="details ' + this_class + '">' + v.details;
            if (v.lastname.trim() && v.firstname.trim()) {
                html += '<td class="name ' + this_class + '">' + v.lastname + ', ' + v.firstname;
            } else {
                html += '<td class="name ' + this_class + '">';
            }
            html += '<td class="total ' + this_class + '">$' + total;
            html += '<td class="' + this_class + '">' + options;
          });
<?php
if (is_admin()) {
?>
          html += '</tbody><tfoot><tr><th colspan="7" style="text-align: center;"><span class=""><a href="javascript:;" class="btn btn-primary btn-new-item"><i class="icon-pencil"></i> Add New Item</a></span></tfoot>'
<?php
}
?>
          html += '</table>';
          $('#invoice').html(html);
          $('#invoice-line-items').dataTable({
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "bStateSave": false,
            "bSortClasses": false,
            "bLengthChange": false,
            "oLanguage": {
                "sEmptyTable": "No line items found."
            },
            "aoColumns": [
                null,
                null,
                null,
                null,
                {"sType": "dollars"},
                null
            ],
            "aaSorting": [[0, "asc"]]
          });
          get_invoice_total();
        },
        error: function(_a, _str, _c) {
          $('a.edit-item').removeAttr('disabled');
          alert(_str);
        }
      });
    }
    
    function convert_ts_to_str(str) {
      var int = 0;
      if (typeof str == "number") {
        int = str
      } else {
        int = parseInt(str);
      }
      return new Date(int * 1000).toDateString();
    }
  </script>
</div>
<div class="container">
<form method="post" action="<?php echo FILENAME_PAY_INVOICE_PAYMENT; ?>">
  <input type="hidden" name="invoice_total" id="pay-invoice-total" value="">
  <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
  <input type="hidden" name="invoice_user_id" value="<?php echo $user_id; ?>">
  <input type="hidden" name="invoice_agency_id" value="<?php echo $agency_id; ?>">
  <p style="text-align: center;">
    <a href="javascript:;" class="btn print-invoice"><i class="icon-print">&nbsp;</i> Print</a>
<?php if (is_ap() || is_agent()) { ?>
        <input type="submit" name="pay" class="btn btn-primary pay-invoice hidden" value="Pay">
<?php } ?>
  </p>
  </form>
</div>
