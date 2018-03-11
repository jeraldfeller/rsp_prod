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
require_once 'invoice_functions.php';

// Let these run each time to keep things up to date
//find_missing_account_item_info();
//find_orphaned_orders();
?>
<style type="text/css">
/* Fix this later by putting it in a proper CSS file. */
.inv-comment-box {
    display: inline-block;
    float: left;
    margin: 3px;
    max-width: 100px;
    border-bottom: 1px dotted #ccc;
}
.inv-comment-box:empty {
    display: none;
}
</style>
<?php
if (is_admin()) {
  // Make sure month is set up here!!!
  $status = 0;
  if (isset($_REQUEST['status'])) {
    $status = $_REQUEST['status'];
  }
            
  if(! $month && !$year && !$status && !$user_id && !$agency_id) {
    
    // Here the default filters are set, when first visiting page.
    //$status = "overdue";
    $lm = new DateTime();
    $lm->sub(new DateInterval('P1M'));
    $month = array($lm->format("n"), $lm->format("n"));
    $year = array($lm->format("Y"), $lm->format("Y"));
    
    if(date('n')==1 and date('Y')==2014)
    {
		$month[0]=1;
		$year[0]=2014;
	}
    
  }
  if(! $month && !$year) {
    $month = array(1, date('n'));
    //$year = array(2013, date('Y'));
    $year = array(2014, date('Y'));
  }
  
  ?>
  <div style="position: fixed; bottom: 1em; left: 1em; z-index: 100; background: #fff; box-shadow: 0 0 10px #000; border-radius: 4px; padding: 0.5em 1em;">
    Go to: 
    <a href="#main-content-container">Top</a> | <a href="#filters">Filters</a> | 
    <a href="#email-queue">Email Queue</a> 
    <strong><span class="ajax-alert" style="margin-left: 1em;">&nbsp;</span></strong>
  </div>

  <h2>Filters</h2>
  <div id="filters" class="alert alert-info">
    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="GET">
      <div class='row-fluid'>
        <div class="span2">
          From: <br>
          <select class="input-mini" name="month[]" id="first-month">
            <?php
            for ($i = 1; $i < 13; $i++) {
              if ((isset($month[0]) && $month[0] == $i) || $month == $i) {
                $selected = " SELECTED";
              } else {
                $selected = null;
              }
              echo "<option value='{$i}'{$selected}>{$i}</option>";
            }
            ?>
          </select>
          <select class="input-small" name="year[]" id="first-year">
            <?php
            //for ($i = 2013; $i <  (date('Y') + 1); $i++) 
            for ($i = 2014; $i <  (date('Y') + 1); $i++) 
			{
              if ((isset($year[0]) && $year[0] == $i) || $year == $i) {
                $selected = " SELECTED";
              } else {
                $selected = null;
              }
              echo "<option value='{$i}'{$selected}>{$i}</option>";
            }
            ?>
          </select>
          <div><a class="btn all-date-btn btn-mini">All</a></div>
        </div>
        <div class="span2">
          To:<br>
          <select class="input-mini" name="month[]" id="last-month">
            <?php
            
            for ($i = 1; $i < 13; $i++) {
              if ((isset($month[1]) && $month[1] == $i) || $month == $i) {
                $selected = " SELECTED";
              } else {
                $selected = null;
              }
              echo "<option value='{$i}'{$selected}>{$i}</option>";
            }
            ?>
          </select>
          <select class="input-small" name="year[]" id="last-year">
            <?php
            //for ($i = 2013; $i < (date('Y') + 1); $i++) 
            for ($i = 2014; $i < (date('Y') + 1); $i++) 
			{
              if ((isset($year[1]) && $year[1] == $i) || $year == $i) {
                $selected = " SELECTED";
              } else {
                $selected = null;
              }
              echo "<option value='{$i}'{$selected}>{$i}</option>";
            }
            ?>
          </select>
        </div>
        <div class="span1">
          Status:<br>
          <select name="status" class="input-small">
            <?php
            $status_list = array(
                0 => 'All',
                'current' => 'Current',
                'overdue' => 'Overdue',
                'paid' => 'Paid',
            );
            foreach ($status_list as $k => $v) {
              $status === $k ? $selected = " SELECTED" : $selected = '';
              echo "<option value='{$k}' {$selected}>{$v}</option>";
            }
            ?>
          </select>
        </div>
        <div class="span1">
          <?php
          $balance = 0;
          if (isset($_REQUEST['balance'])) {
            $balance = (int) $_REQUEST['balance'];
          }
          ?>
          Amount:<br>
          <input class="input-mini" type="text" name="balance" placeholder="Balance" value="<?php echo $balance; ?>">
        </div>
        <div class="span2"> Agency:<br><?php echo tep_draw_agency_pulldown('agency_id', $agency_id, 'id="agency_select" class="input-medium"'); ?></div>
        <div class="span2"> User:<br><?php echo tep_draw_agent_pulldown('user_id', $user_id, 'id="user_select" class="input-medium" '); ?></div>
        <div class="span2">
          &nbsp;<br>
          <input type="hidden" name="action" value="overview">
          <button type="submit" class="btn btn-small"><i class="icon-filter">&nbsp;</i> Apply</button>
        </div>
      </div>
    </form>
    <div style="text-align: center; font-size: 1.33em;">Total Invoice Amount Owed For This Search: <span class='badge badge-info grand-total' style="margin-left: 1em; font-size: 1.33em; padding: 0.3em;"><i class='icon-spin icon-spinner'>&nbsp;</i></span></div>    
  </div>

  <div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="muted"><strong>NOTE:</strong> All invoice credits/debits start from January, 2014 and onward. Data before January 2014 is not used.</span>
  </div>
  <?php
}

if(! $month && !$year) {
  $month = array(1, date('n'));
  //$year = array(2013, date('Y'));
  $year = array(2014, date('Y'));
}

get_invoice_overview_table(get_invoice_items($month, $year, $user_id, $agency_id),$status);

// If admin, add the adjustment/payment modal.
//account_item_id,date_added,reason,details,total,user_id,agency_id,month,year
if (is_admin()) {
  ?>
  
  <script data-cfasync="false" type="text/javascript">
    // obj should be: year,month,user,agency
    window.emailQueue = {
      queue: [],
      selectAll: false,
      toggleSelect: function() {
        console.log(window.emailQueue.selectAll);
        if (window.emailQueue.selectAll == false) {
          $.each($('input[name=selected_invoices]'), function() {
            $(this).prop('checked', true);
          });
          window.emailQueue.selectAll = true;
        } else if (window.emailQueue.selectAll == true) {
          $.each($('input[name=selected_invoices]'), function() {
            $(this).prop('checked', false);
          });
          window.emailQueue.selectAll = false;
        }
      },
      addChecked: function() {
        $.each($('input[name=selected_invoices]'), function(i, v) {
          if ($(v).is(':checked')) {
            console.log('checked');
            var info = $(v).parent().parent('tr');
            var user_id = info.attr('data-user');
            var agency_id = info.attr('data-agency');
            var month = info.attr('data-month');
            var year = info.attr('data-year');
            var name = info.attr('data-name');
            var obj = {
              year: year,
              month: month,
              name: name,
              user_id: user_id,
              agency_id: agency_id
            };
            window.emailQueue.add(obj);
          }
        });
      },
      exists: function(obj) {
        var exists = false;
        $.each(window.emailQueue.queue, function(i, v) {
          if (v.year === obj.year && v.month === obj.month && v.user_id === obj.user_id && v.agency_id === obj.agency_id) {
            exists = true;
          }
        });
        return exists;

      },
      generateClass: function(obj) {
        return obj.year + '-' + obj.month + '-' + obj.agency_id + '-' + obj.user_id;
      },
      add: function(obj) {
        if (window.emailQueue.exists(obj) === false) {
          window.emailQueue.queue.push(obj);
          var str_id = window.emailQueue.generateClass(obj);
          var html = "<span class='inv-comment-box email-queue " + str_id + " '>" + obj.name + " " + obj.month + "/" + obj.year + " <a href='javascript:;' id='add-email-" + str_id + "' data-invoice-id='" + str_id + "'><i class='icon-plus'>&nbsp;</i></a> <a href='javascript:;' onclick='window.emailQueue.remove({agency_id: " + obj.agency_id + ",user_id: " + obj.user_id + ",year: " + obj.year + ",month: " + obj.month + "})'><i class='icon-remove-sign'>&nbsp;</i></a></span>";
          $('#email-queue').append(html);
          $('#add-email-' + str_id).click(function () {
              var str_id = $(this).data("invoice-id");
              html = "<div class='controls controls-row' id='extra-email-row-" + str_id + "'><span class='label label-info'>Extra Email for " + obj.name + " " + obj.month + "/" + obj.year + "</span><input type='text' id='extra-email-" + str_id + "' class='span6 pull-right'></div>\n";
              $("#extra-emails").append(html);
              $(this).unbind('click');
          });
        } else {
          console.log('Exists');
          console.log(obj);
        }

      },
      list: function(obj) {

      },
      send: function() {
        if (confirm("Send the Invoices in the Queue Now? This cannot be undone.")) {
          var l = window.emailQueue.queue.length - 1;
          var msg_box = $('textarea[name=msg_box]').val();
          var msg = $('textarea[name=msg]').val();
          var extra_email = '';
          var data = {};

          for (i = l; i > -1; i--) {
            data = window.emailQueue.queue[i];
            extra_email = $('#extra-email-' + data.year + '-' + data.month + '-' + data.agency_id + '-' + data.user_id).val();
            if (extra_email) {
                extra_email = encodeURIComponent(extra_email);
            } else {
                extra_email = '';
            }
            $.ajax({
              url: "admin_view_invoices.php?email_this=1&msg=" + msg + "&msg_box=" + msg_box + "&extra_email=" + extra_email,
              data: window.emailQueue.queue[i],
              dataType: 'json',
              success: function(data) {
                
                var obj = obj || {};
                if(data.user_id != undefined) {
                  obj = {
                    user_id: data.user_id,
                    agency_id: data.agency_id,
                    year: data.year,
                    month: data.month
                  }
                }

                console.log(data);
                console.log(obj);
                
                if (data.error === false) {
                  // Append success to the comments td.
                  $('tr[data-year=' + obj.year + '][data-month=' + obj.month + '][data-user=' + obj.user_id + '][data-agency=' + obj.agency_id + '] td:last-child').append('<span class="inv-comment-box">Email sent just now.</span>');
                  // Remove it from the queue
                  window.emailQueue.remove(obj);
                } else if(data.error === true) {
                  window.emailQueue.remove(obj);
                  alert("Error sending message(s) The messages have been removed from the queue, so you'll need to add them again if you want to try again. The error is:\n\n" + data.msg);
                } else {
                  alert("An unknown error occurred: \n" + data); 
                }
                
              }
            });
          }
        }
      },
      clear: function() {
        window.emailQueue.queue = [];
        $('#email-queue').html('');
      },
      remove: function(obj) {

        if (obj == undefined) {
          return true;
        }

        if (obj != undefined && obj.year != undefined && obj.year != undefined && obj.agency_id != undefined && obj.user_id != undefined) {
          var str_id = window.emailQueue.generateClass(obj);
          $('.email-queue.' + str_id).fadeOut('slow', function() {
            $('.email-queue.' + str_id).remove();
          });
        }

        $.each(window.emailQueue.queue, function(i, v) {
          if (obj.year == undefined || obj.year == undefined || obj.agency_id == undefined || obj.user_id == undefined) {
            return true;
          }
          if (v == undefined || v.year == undefined || v.year == undefined || v.agency_id == undefined || v.user_id == undefined) {
            return true;
          }
          if (v.year == obj.year && v.month == obj.month && v.user_id == obj.user_id && v.agency_id == obj.agency_id) {
            window.emailQueue.queue.splice(i, 1);
          }
        });

        $("#extra-email-row-" + this.generateClass(obj)).remove();
      }
    }

    var jq = jq || [];
    jq.push(function() {
      $.extend( $.fn.dataTableExt.oSort, {
        "dollars-pre": function ( a ) {
          var x = a.replace( /\$/, "" );
          return parseFloat( x );
        },
        "dollars-asc": function ( a, b ) {
          return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },
        "dollars-desc": function ( a, b ) {
          return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
      });

      var user_id = <?php echo $user_id; ?>;
      var agency_id = <?php echo $agency_id; ?>;
      var select_user = '';
      var select_agency = '';
      if (user_id == 0) {
        select_user = 'selected';
      }
      if (agency_id == 0) {
        select_agency = 'selected';
      }
 
      // This powers the "all dates" button.
      $('.all-date-btn').on('click',function(){

        document.getElementById('first-month').options[0].selected = true;
        document.getElementById('last-month').options[document.getElementById('last-month').options.length - 1].selected = true;
        document.getElementById('first-year').options[0].selected = true;
        document.getElementById('last-year').options[document.getElementById('first-year').options.length - 1].selected = true;
        return false;
      });
              
      $('select[name=agency_id]').prepend('<option value="" ' + select_agency + '>All</option>');
      $('select[name=user_id]').prepend('<option value="" ' + select_user + '>All</option>');
      
      // IE compatibility.
      if(agency_id === 0) {
        document.getElementById('agency_select').options[0].setAttribute('selected','selected');
      }
      if(user_id === 0) {
        document.getElementById('user_select').options[0].setAttribute('selected','selected');
      }
      

      window.oTable = $('#invoice_tbl').dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span4'i><'span8'p>>",
        "sPaginationType": "bootstrap",
        "bLengthChange": true,
        "iDisplayLength": 50,
        "bStateSave": true,
        "oLanguage": {
          "sEmptyTable": "No matching invoices found."
        },
        "aoColumns": [null, null, null, null, null, { "sType": "dollars" }, { "sType": "dollars" }, null, null]
      });
    });
  </script>
  <h2>Email Queue</h2>
  <div class="alert alert-info">
    <div id="email-queue"></div>
    <div class="clearfix"></div>
    <div>
      <h5>Emails are sent to each Accounts Payable user for a given agency, emails assigned to receive Invoice Emails (in Manage Agencies), or the primary Agent email address on an Agent Invoice.  You can add an email address by clicking the <i class="icon-plus">&nbsp</i>(plus sign) on an Invoice in the Email Queue.  You can customize the message by inserting the following custom values. Need more variables? They can be added easily.</h5>
      <div class="row-fluid">
        <div class="span2"><span class="label label-info">{{first_name}}</span></div>
        <div class="span4">First name of the AOM/Accounts Payable user/Agent receiving the invoice</div>
        <div class="span2"><span class="label label-info">{{last_name}}</span></div>
        <div class="span4">Last name of the AOM/Accounts Payable user/Agent receiving the invoice</div>
      </div>
      <div class="row-fluid">
        <div class="span2"><span class="label label-info">{{year}}</span></div>
        <div class="span4">Four-digit year of the invoice.</div>
        <div class="span2"><span class="label label-info">{{month}}</span></div>
        <div class="span4">Month of the invoice, like "January", "June", etc.</div>
      </div>
      <div class="row-fluid">
        <div class="span2"><span class="label label-info">{{amount}}</span></div>
        <div class="span4">Invoice total, including the "$" sign.</div>
        <div class="span2"><span class="label label-info">{{email}}</span></div>
        <div class="span4">The email address of the recipient</div>        
      </div>

      <div class="controls row-fluid" id="extra-emails"></div> 
      <textarea class="input-block-level" name="msg" style="min-height: 6em;" placeholder="Message to include at top of email, above the invoice."></textarea>
      <textarea class="input-block-level" name="msg_box" style="min-height: 4em;" placeholder="Message to include in the invoice text box, in the bottom section of the invoice."></textarea>
      

      <div style="text-align: center; margin-bottom: 2em;">
        <a class="btn" href="javascript:;" onclick="window.emailQueue.clear();"><i class="icon-remove">&nbsp;</i>Clear</a>
        <a class="btn btn-primary" href="javascript:;" onclick="window.emailQueue.send();"><i class="icon-envelope">&nbsp;</i> Send</a>
      </div>
    </div>
  </div>

  <div id="adjustment_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form name="adjustment_form" action="/lib/invoices/invoice_save_item.php5" method="GET">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Payment/Adjustment Dialog</h3>
      </div>

      <div class="modal-body">
        <input class="adjustment" type="hidden" name="account_item_id" value="0">
        <input class="adjustment" type="hidden" name="user_id">
        <input class="adjustment" type="hidden" name="agency_id">
        <input class="adjustment" type="hidden" name="year">
        <input class="adjustment" type="hidden" name="month">
        <input name="date_added" type="hidden">  

        <div class="row-fluid">
          <div class="span3">
            Agency/Agent
          </div>
          <div class="span9">
            <input type="text" name="name" disabled="disabled" class="adjustment input-block-level" placeholder="Agency or agent.">
          </div>
        </div>
        <div class="row-fluid">
          <div class="span3">
            Period
          </div>
          <div class="span2">
            <span class="adjustment-label-month">&nbsp;</span>/<span class="adjustment-label-year">&nbsp;</span>
          </div>
        </div>
        <div class="row-fluid">
          <div class="span3">
            Reason
          </div>
          <div class="span9">            
            <input class="check-button" type="radio" name="reason" value="Check" required="required"> Check
            <span style="margin-left: 1em;"><input class="adjustment-button" type="radio" name="reason" value="Adjustment" required="required"> Adjustment</span>
          </div>
        </div>
        <div class="row-fluid check-details hide">
          <div class="span3">
            Check Date
          </div>
          <div class="span6">
            <input type="text" name="check_date" class="input-block-level datepicker" placeholder="Check date">
          </div>
        </div>
        <div class="row-fluid check-details hide">
          <div class="span3">
            Check #
          </div>
          <div class="span6">
            <input type="text" name="check_number" class="input-block-level" placeholder="Check #">
          </div>
        </div>        
        <div class="row-fluid">
          <div class="span3">
            Details
          </div>
          <div class="span9">
            <textarea name="details" id="adjustment-details" class="adjustment input-block-level" required="required" style="min-height: 4em;" placeholder="Add check date/invoice #, etc. here"></textarea>
          </div>
        </div>
        <div class="row-fluid">
          <div class="span3">
            Amount
          </div>
          <div class="span9">
            $<input type="text" name="total" class="adjustment input-small" required="required" placeholder="Amount">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="alert alert-info adjustment-result hide" style="text-align: center !important;"></div>
        <button type="button" class="btn" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary adjustment-btn" data-loading-text="Saving..." autocomplete="off"><i class="icon-save">&nbsp;</i> Save changes</button>
      </div>
    </form>
  </div>
  <script data-cfasync="false" type="text/javascript">
    function show_adjustment_form(obj) {

      // Simple way to populate the form, no?
      $.each(obj, function(k, v) {
        $('input[name=' + k + ']').val(v);

        if (k == 'month' || k == 'year') {
          $('.adjustment-label-' + k).html(v);
        }
      });
      
      // Clear the check date/number, the radio, and hide the check details items(s)
      $('input[name=check_date]').val('');
      $('input[name=check_number]').val('');
      $('input[name=reason]').each(function(k,v){
        $(v).prop('checked',false);
      });
      $('.check-details').hide();
      $('.adjustment-result').html('').hide();
      $('#adjustment-details').val('').attr('placeholder', 'Add check date/invoice #, etc. here');

      // Done? Show it!
      $('#adjustment_modal').modal('show');

    }
    
    var jq = jq || [];
    jq.push(function() {

      // Temporary fix. Should fix via css, naturally
      $('input.datepicker').datepicker();
      $('div.datepicker.dropdown-menu').css('z-index',99999999999);
      
      // Adjust form fields for check/adjustment options.
      $('.check-button').on('click',function(){
        $('.check-details input').attr('required','required');
        $('.check-details').fadeIn('fast');
        $('#adjustment-details').removeAttr('required').attr('placeholder', 'Optional');
      });
      $('.adjustment-button').on('click',function(){
        $('.check-details input').removeAttr('required').val('');
        $('.check-details').fadeOut('fast');
        $('#adjustment-details').attr('required', 'required').attr('placeholder', 'Add check date/invoice #, etc. here');
      });
            
      // Form submit catch.
      $('form[name=adjustment_form]').submit(function() {
        
        // Date added.
        $('input[name=date_added]').val(Math.round(new Date().getTime() / 1000));
        
        // Combine the check # and date with the reason here.
        var reason = $('form[name=adjustment_form] input[name=reason]:checked').val();
        if($('input[name=check_number]').prop('required') == true) {
          reason += " #" + $('input[name=check_number]').val() + ", Received " + $('input[name=check_date]').val();
        }
        
        // Need to reverse the amount here, so can't use serialize() :-(
        //var vals = $(this).serialize();
        var total = 0;
        if(reason == "Adjustment") {
          total = parseInt($('form[name=adjustment_form] input[name=total]').val())
        } else { // Check.
          total = parseInt($('form[name=adjustment_form] input[name=total]').val())
          if(total > 0) {
            total *= -1;
          }
          console.log(total);
        }
        
        var vals = {
          date_added: $('input[name=date_added]').val(),
          account_item_id: $('form[name=adjustment_form] input[name=account_item_id]').val(),
          agency_id: $('form[name=adjustment_form] input[name=agency_id]').val(),
          user_id: $('form[name=adjustment_form] input[name=user_id]').val(),
          month: $('form[name=adjustment_form] input[name=month]').val(),
          year: $('form[name=adjustment_form] input[name=year]').val(),
          reason: reason,
          details: $('form[name=adjustment_form] textarea[name=details]').val(),
          total: total
        };
        
        $('.adjustment-btn').button('loading');
        $('.adjustment-result').removeClass('alert-error').removeClass('alert-success').addClass('alert-info').html('Saving... please wait.').fadeIn('fast');
        $.ajax({
          url: '/lib/invoices/invoice_save_item.php5',
          cache: false,
          data: vals,
          dataType: 'json',
          success: function(data) {
            if(data.error === false) {
              
              // Show success.
              $('.adjustment-result').removeClass('alert-info').removeClass('alert-error').addClass('alert-success').html(data.msg + "<br><span class='muted'>You can now safely close this window. It will close automatically in 2 seconds.</span>");
              
              // Update the totals.
              var id_str = 'tr[data-user=' + vals.user_id + '][data-agency=' + vals.agency_id + '][data-month=' + vals.month + '][data-year=' + vals.year + '] td.balance';
              
              // Now calculate and set the new balance.
              var cur_bal = parseFloat($(id_str).html().replace(/,/g, '').replace(/\$/, ''));
              var new_bal = vals.total + cur_bal;
              $(id_str).html('$' + new_bal);
              
              // Clear the form.
              $('input[name=reason], input[name=total], textarea[name=details], input[name=date_added]').val('');
              
              // Now, auto close the modal.
              setTimeout(function(){ 
                $('#adjustment_modal').modal('hide');
              },2000);
              
              
            } else {
              $('.adjustment-result').removeClass('alert-info').removeClass('alert-success').addClass('alert-error').html(data.msg);
            }
          },
          error: function(_a, _str, _c) {
            alert(_str);
          }
        }).always(function(){
          $('.adjustment-btn').button('reset');
        });
        
        return false;
      });
    });

    function get_account_total(obj,place) {

      var target;
      var str = 'tr[data-year='+obj.year+'][data-month='+obj.month+'][data-agency='+obj.agency_id+'][data-user='+obj.user_id+'] td.totals a:first-child strong';
      console.log(str);
      if(place == 0) {
        target = $('tr[data-year='+obj.year+'][data-month='+obj.month+'][data-agency='+obj.agency_id+'][data-user='+obj.user_id+'] td.totals a:first-child strong');
      } else {
        target = $('tr[data-agency='+obj.agency_id+'][data-user='+obj.user_id+'] td.totals a:last-child strong');
      } 
      
      $.ajax({
        url: '/lib/invoices/invoice_get_account_total.php5',
        data: obj,
        dataType: 'json',
        success: function(data) {
          if (data.error == true) {
            $('.ajax-alert').html('Error: ' + data.msg);
            return false;
          }
          target.html('$' + data.total);
          //$('.ajax-alert').html('Total for ' + obj.name + ' up to and including ' + obj.month + '/' + obj.year + ' is $' + data.total);
        },
        error: function(a, str, xhr) {
          $('.ajax-alert').html('Error: ' + str);
        }
      })
    }
  </script>
<?php } else { // Agent or AOM, just init the table. ?>
  <script data-cfasync="false" type="text/javascript">
    var jq = jq || [];
    jq.push(function() {
      window.oTable = $('#invoice_tbl').dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span4'i><'span8'p>>",
        "sPaginationType": "bootstrap",
        "bLengthChange": true,
        "iDisplayLength": 50,
        "bStateSave": true,
        "oLanguage": {
          "sEmptyTable": "No matching invoices found."
        }
      });
    });
  </script>
  <?php
}
?>
