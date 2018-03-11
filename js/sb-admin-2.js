//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size

$(function () {
	
    $(window).bind("load resize", function () {
        topOffset = 0;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 1175) {
            $('div.navbar-collapse').addClass('collapse');
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

       /* height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }*/
    });

	
});



function SubmitForm(formId) {
    var oForm = document.getElementById(formId);
    if (oForm) {
        oForm.submit(); 
    }
    else {
        alert("DEBUG - could not find element " + formId);
    }
}

function toggle_fields_status() {
	
	if ($("#create_agency").hasClass("btn-info")) {
		$("#create_agency").addClass("btn-danger");
		$("#create_agency").html("Cancel");
		$("#create_agency").removeClass("btn-info");
	} else {
		$("#create_agency").addClass("btn-info");
		$("#create_agency").removeClass("btn-danger");
		$("#create_agency").html("New Agency");
	}
	
	if ($(".existingAgency").hasClass("hidden")) {
		$(".newAgency").addClass("hidden");
		$(".existingAgency").removeClass("hidden");
	} else {
		$(".newAgency").removeClass("hidden");
		$(".existingAgency").addClass("hidden");
	}

}

	     

$(document).ready(function() {
    $('#collapse-top').collapse({'toggle': false});
	
	
	$( "body" ).on( "change", "select[name=state]", function() {

		
		
		var state_id = $(this).val();
		
		//alert(state_id);
		
		if(state_id>0 && state_id != '') {
			$.ajax({
				url: '/lib/ajax/create_order.php5',
				type: "POST",	
				dataType : "json",
				data: 'action=aj_get_county_pulldown&aj_selected_state='+state_id,
				success: function (data, textStatus) {
					$('select[name=county]').html('');	
					$.each(data, function(i, val) {
						$('select[name=county]')
						 .append($("<option></option>")
						 .attr("value",val['id'])
						 .text(val['name'])); 
					});
					$("#county_select").show();
				} 
			});
		}
		else {
			$("#county_select").hide();
		}
		
		

	});
	

	$( "body" ).on( "click", ".navbar-toggle", function() {

		$('.baget').css('z-index',999);

		$(".baget").removeClass('hu-toggled');
		$("#sidebar_right_btn").removeClass('hu-button-toggled');

	});
	
	$( "body" ).on( "click", ".back-button", function() {

		//alert();
		window.history.back();

	});
	
	
	$( "body" ).on( "change", ".change-submit", function() {
		//alert();
		//console.log($(this).closest("form").find('#submit_string_y').attr('id'));
		$(this).closest("form").find('#submit_string_y').remove();
		$(this).closest("form").submit();

	});
	
	
	$('.order_form').submit(function(e) {
		
		if($('#submit_string_y').length>0)
		{
			$('#form_submitted_modal').modal('show');
		}
		
		
	});
	
	
	$( "body" ).on( "click", "#sidebar_right_btn", function() {
	
		$('.baget').css('z-index',9999);
		$(".baget").toggleClass('hu-toggled');
		$("#sidebar_right_btn").toggleClass('hu-button-toggled');
		$('#collapse-top').collapse('hide');

	});
});