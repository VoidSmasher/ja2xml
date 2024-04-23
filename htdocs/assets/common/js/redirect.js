$(function () {
	deploy_check_interval = 5000;
	alert_obj = new alert_class();
	if (fc_dams.sdp == 0 && fc_dams.alarm_time > 0) {
		alert_obj.show_alert(fc_dams.alarm_time);
	}
	setInterval(function () {
		alert_obj.check_redirect();
	}, deploy_check_interval);
});

var alert_class = function () {
	this.executed = false;
};

alert_class.prototype.check_redirect = function () {
	obj = this;
	$.getJSON('/auth/redirect.json?rc=' + fc_dams.rc + '&ra=' + fc_dams.ra + '&sdp=' + fc_dams.sdp, function (data) {
		try {
			if (data.location != false) {
				window.location = data.location;
			}
		} catch (e) {

		}
		if (fc_dams.sdp == 0) {
			if (data.alarm_time > 0) {
				if (!obj.executed) {
					obj.show_alert(data.alarm_time);
				}
			} else {
				obj.hide_alert();
			}
		}
	});
};

alert_class.prototype.hide_alert = function () {
	this.executed = false;
	alert_deploy = $('.alert-deploy');
	if (alert_deploy.length > 0) {
		alert_deploy.remove();
		navbar = $('.navbar-fixed-top');
		if (navbar.length > 0) {
			navbar.css('top', 0);
			$('body').css('padding-top', (navbar.height() + 10) + 'px');
		} else {
			$('body').css('padding-top', 0);
		}
	}
	return true;
};

alert_class.prototype.show_alert = function (alert_time) {
	this.executed = true;

	alert_clock_time = alert_time * 1000;

	jQuery('<div/>', {
		class: 'alert alert-danger alert-deploy',
		role: 'alert',
		text: fc_dams.alarm_message1 + ' '
	}).appendTo('body');

	var alert_clock = jQuery('<span/>', {
		id: 'act',
		text: fc_dams.alarm_text
	}).appendTo('.alert-deploy');

	$('.alert-deploy').append(' ' + fc_dams.alarm_message2);

	navbar = $('.navbar-fixed-top');
	if (navbar.length > 0) {
		navbar.css('top', '60px');
		$('body').css('padding-top', (navbar.height() + 70) + 'px');
	} else {
		$('body').css('padding-top', '60px');
	}

	alert_clock.countdown(alert_clock_time)
		.on('update.countdown', function (event) {
			$(this).html(event.strftime('%H:%M:%S'));
		})
		.on('finish.countdown', function (event) {
			$(this).html('00:00:00');
		});
	return true;
};
