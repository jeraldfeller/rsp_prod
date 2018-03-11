// JavaScript Document
function popup_window(url, width, height) {
	window.open(url,"Popup","height="+height+",width="+width+",scrollbars=yes,resizable=yes").focus();
}

function boot_sizes() {
		if (detect_browser() == 'msie') {
			screen_height = document.documentElement.clientHeight;
			screen_width = document.documentElement.clientWidth;
		} else {
			screen_height = window.innerHeight;
			screen_width = window.innerWidth;
		}
		document.getElementById('pageContent').height = (screen_height - 285);
}

function detect_browser() {
	detect = navigator.userAgent.toLowerCase();
		if(detect.indexOf('msie') + 1) {
			return 'msie';
		} else if (navigator.appName == 'Netscape') {
			return 'net';
		} else {
			return 'other';
		}
}