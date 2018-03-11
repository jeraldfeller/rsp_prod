<style>
.ui-autocomplete-group {
  font-weight: bold;
  padding: .2em .4em;
  margin: .8em 0 .2em;
  line-height: 1.5;
}
</style>
<script data-cfasync="false" language="javascript">
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "percent-pre": function (a) {
        var x = (a == "-") ? -1 : a.replace( /%/, "" );
        return parseFloat( x );
    },
    "percent-asc": function (a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "percent-desc": function (a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
   "date-us-pre": function (a) {
       var b = a.match(/(\d{1,2})\/(\d{1,2})\/(\d{2,4})/),
           month = b[1],
           day = b[2],
           year = b[3];

       if(year.length == 2){
           if(parseInt(year, 10)<70) year = '20'+year;
           else year = '19'+year;
       }
       if(month.length == 1) month = '0'+month;
       if(day.length == 1) day = '0'+day;

       var tt = year+month+day;
       return  tt;
   },
   "date-us-asc": function (a, b) {
       return a - b;
   },
   "date-us-desc": function (a, b) {
       return b - a;
   }
});

jQuery.fn.dataTableExt.aTypes.unshift(
   function ( sData ) {
       if (sData !== null && sData.match(/\d{1,2}\/\d{1,2}\/\d{2,4}/)) {
           return 'date-us';
       }
       return null;
   }
);

var vars = [], hash;
var q = document.URL.split('?')[1];
if(q != undefined){
  q = q.split('&');
  for(var i = 0; i < q.length; i++){
    hash = q[i].split('=');
    vars.push(hash[1]);
    vars[hash[0]] = hash[1];
  }
}

var hide_inactive = typeof vars["show_hidden"] == "undefined" || vars["show_hidden"] == "false";

$(document).ready(function () {
  $.getJSON( "/lib/inventory/defaults_json.php", function( data ) {
    $.each( data, function( key, val ) {
      $("span#display_" + key).html(val);
      $("input#" + key).val(val);
    });
  });

  $("#showDefaultLevels").click(function () {
      defaultLevels();
  });

  $("#showHidden").click(function () {
      hide_inactive = !hide_inactive;
      if (hide_inactive) {
          location.search = "";
      } else {
          location.search = "?show_hidden=true";
      }
  });


  if (hide_inactive) {
    $("#showHidden").html("Show Hidden");
  } else {
    $("#showHidden").html("Hide Inactive");
  }

  $.widget( "custom.usercomplete", $.ui.autocomplete, {
    _renderMenu: function(ul, items) {
      var that = this,
      currentCategory = "";
      $.each(items, function(index, item) {
        if ( item.category != currentCategory ) {
          ul.append( "<li class='ui-autocomplete-group'>" + item.category + "</li>" );
          currentCategory = item.category;
        }
        that._renderItemData(ul, item);
      });
    }
  });

  $("input#watcher-name").usercomplete({
    source: "/lib/users/users_autocomplete_json.php",
    open: function () {
      setTimeout(function () {
        $(".ui-autocomplete").css("z-index", 1200);
      });
    },
    select: function(event, ui) {
      $("input#watcher-userid").val(ui.item.user_id);
      $("button#watcher-add").removeAttr("disabled");
    },
    change: function(event, ui) {
      if (!ui.item) {
        $("button#watcher-add").attr("disabled", "disabled");
        $("input#watcher-userid").val("");
        $("input#watcher-name").val("");
      }
    }
  });

  $("input#watcher-name").keypress(function (e) {
    if (e.which == 13) {
      $('button#watcher-add').click();
      e.preventDefault();
    }
  });

  $("#toggle-default-update").click(function () {
    $("div#default-display").slideToggle("normal", function() {
      $("div#default-update").slideToggle("normal");
    });
    $("button#default-ok").toggle();
    $("button#default-update-submit").toggle();
    $("button#default-cancel").toggle();
  });

  $("button#default-update-submit").click(function () {
    if (parseInt($("#retired_activity_date").val()) != $("#retired_activity_date").val() ||
        parseInt($("#critical_number_available_at_warehouse").val()) != $("#critical_number_available_at_warehouse").val() ||
        parseInt($("#warning_number_available_at_warehouse").val()) != $("#warning_number_available_at_warehouse").val() ||
        parseInt($("#warning_percent_available_at_warehouse").val()) != $("#warning_percent_available_at_warehouse").val()) {
      $("#defaultInventoryLevelsModal").effect("shake");
      return false;
    }
    data = {
      "action": "update",
      "retired_activity_date": $("#retired_activity_date").val(),
      "critical_number_available_at_warehouse": $("#critical_number_available_at_warehouse").val(),
      "warning_number_available_at_warehouse": $("#warning_number_available_at_warehouse").val(),
      "warning_percent_available_at_warehouse": $("#warning_percent_available_at_warehouse").val()
    };
    $.post('/lib/inventory/defaults_json.php', data, function(data) {
        console.log(data);
        location.reload();
    });
  });

  $("button#watcher-add").click(function () {
    if ($("input#watcher-userid").val() != "") {
      $("li#watchers-none").remove();
      $remove = $("<a href='javascript:;'><i class='icon-remove'>&nbsp;</i></a>").click(function () { $(this).parent().remove(); });
      $("ul#watchers").append($("<li class='watcher' data-id='" + $("input#watcher-userid").val() + "'>" + $("input#watcher-name").val() + " </li>").append($remove));
    }
    $("input#watcher-userid").val("");
    $("input#watcher-name").val("");
  });

  $("button#manageWatchersSave").click(function () {
    var users = new Array();
    var equip_id = $("input#watcher-equipid").val();
    $("li.watcher").each(function () {
      user_id = $(this).attr("data-id");
      users.push(user_id);
    });
    var watchers = {"user_ids": users};
    $.post("/lib/inventory/watchers_json.php", {"equipment_id": equip_id, "watchers": JSON.stringify(watchers)});
    $("#manageWatchersModal").modal("toggle");
  });

  $("button#manageInventoryLevelsSave").click(function () {
    var equip_id = $("input#levels-equipid").val();
    var critical_qty = $("input#critical_qty").val();
    var critical_pct = $("input#critical_pct").val();
    var warning_qty = $("input#warning_qty").val();
    var warning_pct = $("input#warning_pct").val();
    var excess_qty = $("input#excess_qty").val();
    var ruleset_name = $("input#ruleset_name").val();

    $("div#custom-critical").removeClass("error");
    $("div#custom-warning").removeClass("error");

    if (ruleset_name == "custom" && !warning_qty.trim()) {
        $("div#custom-warning").addClass("error");
        $("input#warning_qty").focus();
    }
    if (ruleset_name == "custom" && !critical_qty.trim()) {
        $("div#custom-critical").addClass("error");
        $("input#critical_qty").focus();
    }
    if (ruleset_name == "custom" && (!critical_qty.trim() || !warning_qty.trim())) {
        $("#manageInventoryLevelsModal").effect("shake");
        return false;
    }

    $.post("/lib/inventory/levels_json.php", {
        "equipment_id": equip_id,
        "critical_qty": critical_qty,
        "critical_pct": critical_pct,
        "warning_qty": warning_qty,
        "warning_pct": warning_pct,
        "excess_qty": excess_qty,
        "ruleset_name": ruleset_name,
        "action": "update"
    }, function (data) {
        var equip_id = data.equipment_id;
        $.get("/lib/inventory/inventory_json.php", {"equipment_id": equip_id}, function (inventory) {
            $record = $("tr[data-id='"+equip_id+"']");
            var cls = "alert-success";
            switch (inventory.urgency) {
                case 0: cls = "muted"; break;
                case 1: cls = "text-success"; break;
                case 3: cls = "text-warning"; break;
                case 5: cls = "text-error"; break;
            }
            $record.removeClass("muted");
            $record.removeClass("text-success");
            $record.removeClass("text-warning");
            $record.removeClass("text-error");
            $record.addClass(cls);
        });
    });
    $("#manageInventoryLevelsModal").modal("toggle");
  });

  $.get('/lib/inventory/inventory_html.php' + (hide_inactive ? "" : "?show_hidden=true"), function (data) {
    $("div#inventory").html(data);

    $("tr.inventory > td").mouseenter( function () {
        $(this).css("text-decoration", "underline");
    });
    $("tr.inventory > td").mouseleave( function () {
        $(this).css("text-decoration", "none");
    });

    $("tr.inventory").click(function () {
      var str = $(this).attr("data-warehouses");
      var whs = JSON.parse(str);
      var equip_id = $(this).attr("data-id");
      if (!$(this).hasClass("expanded")) {
        for (var wh in whs) {
          var status_str = "";
          for (var eq_status in whs[wh]) {
            if (status_str != "") {
                status_str = status_str + ", ";
            }
            status_str = status_str + eq_status + ": " + whs[wh][eq_status];
          }
          var whrow = $("<tr class='warehouse-" + equip_id + "'><td>&nbsp;</td><td>" + wh + "</td><td colspan='4'>" + status_str + "</td></tr>");
          $(this).addClass("expanded");
          $(this).after(whrow);
        }
      } else {
        $("tr.warehouse-" + equip_id).remove();
        $(this).removeClass("expanded");
      }
    });

    $("tr.inventory").each(function () {
      var equip_id = $(this).attr("data-id");
      var equip_type_id = $(this).attr("data-type-id");
      var equip_name = $(this).children(".name").children("strong").text();
      equip_name = equip_name.replace(/'/g, "&apos;");

      var $action = $("<div class='btn-group'><a class='btn btn-mini dropdown-toggle' data-toggle='dropdown' href='javascript:;'>&nbsp;<i class='icon-cog'>&nbsp;</i><span class='caret'>&nbsp;</span></a><ul class='dropdown-menu'></ul></div>");
      var $edit_action = $("<li><a target='_blank' href='/admin_equipment.php?eID=" + equip_id + "&equipment_type_id=" + equip_type_id + "&out_of_stock=&start_letter=&search_name=" + encodeURIComponent(equip_name) + "&page_action=edit'><span class='icon-edit'>&nbsp;</span> Edit Equipment Type</a></li>");
      var $watchers_action = $("<li><a href='javascript:manageWatchers(" + equip_id + ", \"" + equip_name + "\");'><span class='icon-eye-open'>&nbsp;</span> Manage Watchers</a></li>");
      var $levels_action = $("<li><a href='javascript:manageInventoryLevels(" + equip_id + ", \"" + equip_name + "\");'><span class='icon-bar-chart'>&nbsp;</span> Manage Levels</a></li>");
      $action.children("ul.dropdown-menu").append($edit_action);
      $action.children("ul.dropdown-menu").append($watchers_action);
      $action.children("ul.dropdown-menu").append($levels_action);
      $action.click(function (event) {
          event.stopPropagation();
          $(this).children("ul.dropdown-menu").toggle();
      });
      $(this).children("td.action").append($action);
      $(this).children("td.action").css("width", "35px");
    });

    $('#rider_inventory_tbl').dataTable({
      "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span4'i><'span8'p>>",
      "sPaginationType": "bootstrap",
      "bLengthChange": true,
      "iDisplayLength": 50,
      "bStateSave": false,
      "oLanguage": {
        "sEmptyTable": "No matching equipment found."
      },
      "aoColumns": [
        { "asSorting": [ "desc" ], "sWidth": "35px" },
        null,
        { "sType": "date-us" },
        { "sType": "percent" },
        null,
        null
      ],
      "aaSorting": [[ 0, "desc" ]]
    });

    $('#agent_owned_inventory_tbl').dataTable({
      "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span4'i><'span8'p>>",
      "sPaginationType": "bootstrap",
      "bLengthChange": true,
      "iDisplayLength": 25,
      "bStateSave": false,
      "oLanguage": {
        "sEmptyTable": "No matching equipment found."
      },
      "aoColumns": [
        { "asSorting": [ "desc" ], "sWidth": "35px" },
        null,
        { "sType": "date-us" },
        { "sType": "percent" },
        null,
        null
      ],
      "aaSorting": [[ 0, "desc" ]],
      "fnInitComplete": function (oSettings, json) {
        $('.page-load-spinner').fadeOut(200, function () {
          $('div#inventory').removeClass("hidden");
        });
      }
    });
    $("tr.inventory > td").css("cursor", "pointer");
    $("th.urgency").trigger( "click" );
    $("input#use_custom").click(function () {
      $("input#ruleset_name").val('custom');
      $("div.custom-levels").show();
    });
    $("input#use_default").click(function () {
      $("input#ruleset_name").val('default');
      $("div.custom-levels").hide();
    });
    $("input#use_mute").click(function () {
      $("input#ruleset_name").val('mute');
      $("div.custom-levels").hide();
    });

  });
});

function manageInventoryLevels(equip_id, equip_name) {
    $("#manageInventoryLevelsTitle").html("Equipment: " + equip_name);
    $("#manageInventoryLevelsForm")[0].reset();
    $("input#levels-equipid").val(equip_id);
    $("input#use_default").attr('checked', 'checked');
    $("div#custom-critical").removeClass("error");
    $("div#custom-warning").removeClass("error");
    $("div.custom-levels").hide();
    if (equip_name.indexOf('SignPost -') != 0) {
      $("#custom-excess").addClass("hidden");
    } else {
      $("#custom-excess").removeClass("hidden");
    }
    $.getJSON( "/lib/inventory/levels_json.php?equipment_id=" + equip_id, function( data ) {
      ruleset_name = data.ruleset_name;
      if (!(ruleset_name == "default" || ruleset_name == "mute")) {
        $("input#critical_qty").val(data.critical_qty);
        $("input#critical_pct").val(data.critical_pct);
        $("input#warning_qty").val(data.warning_qty);
        $("input#warning_pct").val(data.warning_pct);
        $("input#excess_qty").val(data.excess_qty);
        $("input#ruleset_name").val('custom');
        $("input#use_custom").attr('checked', 'checked');
        $("div.custom-levels").show();
      } else if (ruleset_name == "mute") {
        $("input#ruleset_name").val('mute');
        $("input#use_mute").attr('checked', 'checked');
      }
    });
    $('#manageInventoryLevelsModal').modal('toggle');
}

function manageWatchers(equip_id, equip_name) {
    $("#manageWatchersTitle").html("Equipment: " + equip_name);
    $("ul#watchers").empty();
    $("#manageWatchersForm")[0].reset();
    $("input#watcher-equipid").val(equip_id);
    $.getJSON( "/lib/inventory/watchers_json.php?equipment_id=" + equip_id, function( data ) {
      var i = 0;
      $.each( data, function( key, val ) {
        $remove = $("<a href='javascript:;'><i class='icon-remove'>&nbsp;</i></a>").click(function () { $(this).parent().remove(); });
        $("ul#watchers").append($("<li class='watcher' data-id='" + val.user_id + "'>" + val.name + " </li>").append($remove));
        i++;
      });
      if (i==0) $("ul#watchers").append($('<li id="watchers-none"><em>None</em></li>'));
    });
    $('#manageWatchersModal').modal('toggle');
}

function defaultLevels() {
    $('div#default-update').hide();
    $('button#default-update-submit').hide();
    $('button#default-cancel').hide();
    $('div#default-display').show();
    $('button#default-ok').show();
    $('#defaultInventoryLevelsModal').modal('toggle');
}

</script>

<div class="modal fade" id="manageInventoryLevelsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalTitle">Manage Inventory Levels</h4>
      </div>
      <div class="modal-body">
        <h5 id="manageInventoryLevelsTitle"></h5>
        <form class="form-inline" id="manageInventoryLevelsForm">
            <div class="controls controls-row">
                <label class="radio" for="use_default"><input type="radio" id="use_default" name="use_rules"> Use default levels</label>
                <input type="hidden" name="ruleset_name" id="ruleset_name">
            </div>
            <div class="controls controls-row">
                <label class="radio" for="use_custom"><input type="radio" id="use_custom" name="use_rules"> Use custom levels</label>
            </div>
            <div class="custom-levels">
              <hr>
            </div>
            <div id="custom-critical" class="control-group">
                <div class="custom-levels controls controls-row">
                    <label for="critical_qty" class="control-label span2">Critical Level:</label>
                    <div class="input-append" class="span1">
                        <input type="text" name="critical_qty" id="critical_qty" class="span1" placeholder="">
                        <span class="add-on">Qty</span>
                    </div>
                    <div class="input-append" class="span2">
                        <input type="text" name="critical_pct" id="critical_pct" class="span2" placeholder="Optional">
                        <span class="add-on">%</span>
                    </div>
                </div>
            </div>
            <div id="custom-warning" class="control-group">
                <div class="custom-levels controls controls-row">
                    <label for="warning_qty" class="control-label span2">Warning Level:</label>
                    <div class="input-append">
                        <input type="text" name="warning_qty" id="warning_qty" class="span1" placeholder="">
                        <span class="add-on">Qty</span>
                    </div>
                    <div class="input-append">
                        <input type="text" name="warning_pct" id="warning_pct" class="span2" placeholder="Optional">
                        <span class="add-on">%</span>
                    </div>
                </div>
            </div>
            <div id="custom-excess" class="control-group">
                <div class="custom-levels controls controls-row">
                    <label for="excess_qty" class="control-label span2">Excess Level:</label>
                    <div class="input-append">
                        <input type="text" name="excess_qty" id="excess_qty" class="span1" placeholder="Optional">
                        <span class="add-on">Qty</span>
                    </div>
                    <span><em>Only applies July - December</em></span>
                </div>
            </div>
            <div class="custom-levels">
              <hr>
            </div>
            <div class="controls controls-row">
                <label class="radio" for="use_mute"><input type="radio" id="use_mute" name="use_rules"> Mute alerts</label>
            </div>
            <input type="hidden" name="levels-equipid" id="levels-equipid" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" id="manageInventoryLevelsSave" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="manageWatchersModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Manage Watchers</h4>
      </div>
      <div class="modal-body">
        <h5 id="manageWatchersTitle"></h5>
        <form class="form-inline" id="manageWatchersForm">
            <div class="controls-row">
                <label for="watcher-name" class="span2">Add Watcher:</label>
                <div class="input-append">
                    <input type="text" name="watcher-name" id="watcher-name" class="span3" placeholder="User name or email">
                    <button class="btn" id="watcher-add" type="button" disabled="disabled">Add</button>
                </div>
            </div>
            <input type="hidden" name="watcher-userid" id="watcher-userid" value="">
            <input type="hidden" name="watcher-equipid" id="watcher-equipid" value="">
        </form>
        <h5>Current Watchers:</h5>
        <ul id="watchers">
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" id="manageWatchersSave" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="defaultInventoryLevelsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalTitle">Default Inventory Levels</h4>
      </div>
      <div class="modal-body">
      <div id="default-display" class="modal-body fluid-container">
        <div id="default-critical" class="fluid-row">
                <div class="span2"><b>Critical Level:</b></div>
                <div class="span1">
                    Qty <span id="display_critical_number_available_at_warehouse"></span>
                </div>
                <div class="span1">
                </div>
                <div class="span3 offset5">
                    <em>Only applies to inventory in Fairfax Warehouse</em>
                </div>
        </div>
        <div id="default-warning" class="fluid-row">
                <div class="span2"><b>Warning Level:</b></div>
                <div class="span1">
                    Qty <span id="display_warning_number_available_at_warehouse"></span>
                </div>
                <div class="span1">
                    <span id="display_warning_percent_available_at_warehouse"></span> %
                </div>
                <div class="span3 offset5">
                    <em>Only applies to inventory in Fairfax Warehouse</em>
                </div>
        </div>
        <div class="default-hide" class="fluid-row">
                <div><b>Hide Conditions:</b></div>
                <div>
                    <ul>
                        <li>Retire after <span id="display_retired_activity_date"></span> months of inactivity.</li>
                        <li>Don't show if there is no inventory (0 active, 0 available) in the Fairfax Warehouse.</li>
                    </ul>
                </div>
        </div>
        <hr>
        <div class="default-hide" class="fluid-row">
                <i>To update default levels, <a href="javascript:" id="toggle-default-update">click here</a></i>
        </div>
      </div>
      <div id='default-update' class='controls controls-row'>
        <form class="form-inline" id="defaultInventoryLevelsForm">
            <div id="default-critical" class="control-group">
                <div class="custom-levels controls controls-row">
                    <label for="critical_number_available_at_warehouse" class="control-label span2">Critical Level:</label>
                    <div class="input-append" class="span1">
                        <input type="text" name="critical_number_available_at_warehouse" id="critical_number_available_at_warehouse" class="span1">
                        <span class="add-on">Qty</span>
                    </div>
                </div>
            </div>
            <div id="default-warning" class="control-group">
                <div class="custom-levels controls controls-row">
                    <label for="warning_number_available_at_warehouse" class="control-label span2">Warning Level:</label>
                    <div class="input-append">
                        <input type="text" name="warning_number_available_at_warehouse" id="warning_number_available_at_warehouse" class="span1">
                        <span class="add-on">Qty</span>
                    </div>
                    <div class="input-append">
                        <input type="text" name="warning_percent_available_at_warehouse" id="warning_percent_available_at_warehouse" class="span2">
                        <span class="add-on">%</span>
                    </div>
                </div>
            </div>
            <div id="default-retired" class="control-group">
                <div class="custom-levels controls controls-row">
                    <label for="retired_activity_date" class="control-label span2">Retire After:</label>
                    <div class="input-append">
                        <input type="text" name="retired_activity_date" id="retired_activity_date" class="span1">
                        <span class="add-on">Months Inactive</span>
                    </div>
                </div>
            </div>
            <input type="hidden" name="action" id="action" value="update">
        </form>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="default-ok" class="btn btn-primary" data-dismiss="modal">OK</button>
        <button type="button" id="default-update-submit" class="btn btn-success" data-dismiss="modal">Save</button>
        <button type="button" id="default-cancel" class="btn" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class='page-load-spinner' style='text-align: center; width: 100%; height: 100%'><h4><span class='icon-spinner icon-spin'>&nbsp;</span> Loading Inventory...</h4></div>
<div class="pull-right">
    <button id="showHidden" class="btn btn-default">Show Hidden</button>
    <button id="showDefaultLevels" class="btn btn-default">Show Default Levels</button>
</div>
<div id='inventory' class='hidden'></div>
