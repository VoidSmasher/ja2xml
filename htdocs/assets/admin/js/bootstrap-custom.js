$(function () {
	$('[rel=popover]').popover({
		placement: 'left'
	});

	$('#form_tabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});

	if (location.href.indexOf('#') != -1) {
		var current_pos = $(document).scrollTop() - 54;
		if (current_pos < 0) {
			current_pos = 0;
		}
		$("body,html").animate({"scrollTop": (current_pos + 'px')}, 100);
	}

	$('input.input-alias').keypress(function (e) {
		if (!/[A-Za-z0-9\-]/.test(String.fromCharCode(e.which))) {
			e.preventDefault();
		}
	});

	$('input.input-role').keypress(function (e) {
		if (!/[A-Za-z0-9\_\-]/.test(String.fromCharCode(e.which))) {
			e.preventDefault();
		}
	});

	// Initialize for popovers
	$('[data-toggle="popover"]').popover();

	//$('.menu-sidebar').affix({
	//	offset: {
	//		top: 0,
	//		bottom: function () {
	//			return (this.bottom = $('.footer').outerHeight())
	//		}
	//	}
	//});

	$(window).bind('scroll', function (e) {
		sidenav_parallax_scroll();
	});

	function sidenav_parallax_scroll() {
		var affix_top = 60;
		var obj = $('.menu-sidebar');
		var obj_height = obj.outerHeight();
		var screen_height = $(window).height();
		if (obj_height > screen_height) {
			var total_scrolled = $(window).scrollTop();
			var body_height = $('body').height();
			var active = $('.menu-sidenav li.active');
			var active_top = 0;
			if (active.length > 0) {
				active_top = active.position().top;
			}
			var start_height = body_height / 2;
			console.log(total_scrolled, start_height, body_height / (screen_height / 2));
			//var start_height = screen_height / 2;
			var footer_height = $('.footer').outerHeight();
			var multiplier = obj_height / body_height;
			//var multiplier = screen_height / body_height;
			if (multiplier > 1) {
				multiplier = 1;
			}
			var stopper = body_height - footer_height - obj_height;
			if (total_scrolled > start_height) {
			//if (active_top > start_height) {
				obj.addClass('affix-bottom').removeClass('affix-top').removeClass('affix');
				//var scrolled = total_scrolled - start_height;
				//var position = affix_top + start_height + (total_scrolled * multiplier);
				var scrolled = total_scrolled - start_height;
				var position = total_scrolled - (scrolled * multiplier);
				//console.log(total_scrolled, position);
				if (position < stopper) {
					obj.css('top', position + 'px');
				} else {
					obj.css('top', stopper + 'px');
				}
			} else {
				obj.addClass('affix').removeClass('affix-top').removeClass('affix-bottom');
				obj.css('top', affix_top + 'px');
			}
		} else {
			obj.css('top', affix_top + 'px');
		}
	}

});
