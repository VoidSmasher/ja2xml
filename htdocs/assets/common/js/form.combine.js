$(function () {

	var last_ids = {};

	function combine_insert(e) {
		e.preventDefault();

		current_group = $(this).parent('.combine-selector').parent('.combine-group');
		combine_name = current_group.data('name');
		type = $(this).data('type');

		var current_combine = window['combine_' + combine_name],
			last_id;

		if (typeof last_ids[combine_name] === 'undefined') {
			last_id = last_ids[combine_name] = current_combine.last_control_id + 1;
		} else {
			last_id = ++last_ids[combine_name];
		}

		control = $(current_combine.controls[type].render);
		control_name = control_id = get_combine_control_name(current_combine.name, type, last_id);

		control.attr('data-id', last_id);

		switch (type) {
			case 'slider':
				control_name += '[]';
				break;
		}

		control.find('input[name]').each(function (i) {
			if ($(this).hasClass('combine-hidden')) {
				$(this).attr('name', get_combine_hidden_name(current_combine.name, type, last_id));
			} else {
				var name = $(this).attr('name');
				name = name.replace("cmbnew", last_id);
				$(this).attr('name', name);
			}
		});

		control.find('.combine-control').attr('name', control_name).attr('id', control_id);
		//control.find('.combine-hidden').attr('name', get_combine_hidden_name(current_combine.name, type, last_id));

		//control.find('.combine-remove').attr('data-name', current_combine.name).attr('data-id', last_id);
		//control.find('.combine-up').attr('data-name', current_combine.name).attr('data-id', last_id);
		//control.find('.combine-down').attr('data-name', current_combine.name).attr('data-id', last_id);
		//control.find('.combine-number').html(last_id);
		control.find('.control-label').attr('for', control_id);

		new_position = current_group.after(control);

		fn = window['force_init_' + type];
		if (typeof fn === 'function') {
			fn(control_name, current_combine.controls[type].params);
		}

		scroll_to_element(new_position);

		$('.combine-insert').unbind('click').click(combine_insert);
		$('.combine-remove').unbind('click').click(combine_remove);
		$('.combine-up').unbind('click').click(combine_up);
		$('.combine-down').unbind('click').click(combine_down);
	}

	function get_combine_control_name(name, type, id) {
		return name + '-' + id + '-' + type;
	}

	function get_combine_hidden_name(name, type, id) {
		return name + '[' + id + '-' + type + ']';
	}

	function combine_remove(e) {
		e.preventDefault();

		control = $(this).closest('div.combine-group');

		if (control.length > 0) {
			control.slideUp("fast", function () {
				$(this).remove();
			});
		}
	}

	function combine_up(e) {
		e.preventDefault();

		control = $(this).closest('div.combine-group');

		sort = control.index() - 1;

		if (sort > 1) {
			control.moveUp();
		}
	}

	function combine_down(e) {
		e.preventDefault();

		control = $(this).closest('div.combine-group');

		sort = control.index() - 1;
		max_sort = $('div.combine-group').length;

		if (sort < max_sort) {
			control.moveDown();
		}
	}

	$('.combine-insert').click(combine_insert);
	$('.combine-remove').click(combine_remove);
	$('.combine-up').click(combine_up);
	$('.combine-down').click(combine_down);

	scroll_to_element = function (e) {
		offset = e.height() - 50;
		$('html, body').animate({
			scrollTop: e.offset().top + offset
		}, 200);
	}

});

$.fn.moveUp = function () {
	$.each(this, function () {
		$(this).after($(this).prev());
	});
};
$.fn.moveDown = function () {
	$.each(this, function () {
		$(this).before($(this).next());
	});
};