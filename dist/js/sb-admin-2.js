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


$(document).ready(function() {
    $('#collapse-top').collapse({'toggle': false});
});

/*
var kek =  $("#side-menu").height();
 $("#page-wrapper").css("min-height", (kek+100) + "px");*/


$( "body" ).on( "click", "#sidebar_right_btn", function() {
		
		$('.baget').css('z-index',9999);

		$(".baget").toggleClass('hu-toggled');
		$("#sidebar_right_btn").toggleClass('hu-button-toggled');
		$('#collapse-top').collapse('hide');
		
		
		
		/*$("#sidebar_right_btn i").addClass('fa-arrow-left');
		$("#sidebar_right_btn i").removeClass('fa-arrow-right');*/
		
		

	});
	
	$( "body" ).on( "click", ".navbar-toggle", function() {
		
		//alert();
		
		$('.baget').css('z-index',999);

		$(".baget").removeClass('hu-toggled');
		$("#sidebar_right_btn").removeClass('hu-button-toggled');
		//$('#collapse-top').collapse('hide');
		
		
		/*$("#sidebar_right_btn i").addClass('fa-arrow-left');
		$("#sidebar_right_btn i").removeClass('fa-arrow-right');*/
		
		

	});