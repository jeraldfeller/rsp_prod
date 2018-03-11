sf.push(function() {

	$('.modal_dialog').dialog({
		autoOpen: false,
		modal: true,
		show: {
			effect: 'explode',
		},
	});

	// Hide buttons on click;
	//$("input[type=image]").click(function() {
		// Hide the button...
		//$("input[type=image]").hide();
		//$("input[type=submit]").hide();
	//});

	//$("input[type=submit]").click(function() {
		// Hide the button...
		//$("input[type=image]").hide();
		//$("input[type=submit]").hide();
	//});

	$('form').each(function() {
		$(this).validate({

			submitHandler: function(form) {

				// Hide the button...
				//$("input[type=image]").hide();
				//$("input[type=submit]").hide();

				// Open the dialog.
				$('#order-processing').dialog('open');

				// Submit the form.
				form.submit();



			},
			invalidHandler: function(form, validator) {

				// Reshow the button...
				//$("input[type=image]").show();
				//$("input[type=submit]").show();
				//return false;
				form.submit();
				
			},
		});
	});



});


window.scripts_count  = 0;
window.scripts_loaded = 0;
function script_init() {
	if(window.scripts_loaded == window.scripts_count) {
		console.log('All required scripts loaded. Executing the queue.');
		for(i = 0; i < sf.length; i++) {
			console.log('Executing queued command ' + i);
			sf[i]();
		}
	} else { }
}

function load_script(href,req) {
	if(req == true) {
		window.scripts_count += 1;
	}

    $.ajax({
  	  type: "GET",
  	  url: href,
  	  dataType: "script",
  	  cache: true,
	  async: true,
	}).done(function() {
		console.log('Script ' + href + ' loaded');
		if(req == true) {
			window.scripts_loaded += 1;
		}
  		script_init();
	});
}

load_script('//ajax.aspnetcdn.com/ajax/jquery.ui/1.9.0/jquery-ui.min.js',true);
load_script('//ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.min.js',true);
load_script('//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js',true);