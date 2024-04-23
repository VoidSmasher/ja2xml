$(function () {
	$('#weapon_data_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		let button = $(event.relatedTarget);

		modal.find('.modal-body input[name="id"]').val(button.data('id'));
		modal.find('.modal-body input[name="accuracy_bonus"]').val(button.data('accuracy_bonus'));
		if (button.data('range_bonus_percent')) {
			modal.find('.modal-body input[name="range_bonus"]').val(button.data('range_bonus_percent'));
		} else {
			modal.find('.modal-body input[name="range_bonus"]').val(button.data('range_bonus'));
		}

		attr = button.attr('data-ready_bonus');
		if (typeof attr !== typeof undefined && attr !== false) {
			modal.find('.modal-body input[name="ready_bonus"]').val(button.data('ready_bonus'));
			modal.find('.modal-body input[name="sp4t_bonus"]').val(button.data('sp4t_bonus'));
			modal.find('.modal-body input[name="burst_ap_bonus"]').val(button.data('burst_ap_bonus'));
			modal.find('.modal-body input[name="afsp5ap_bonus"]').val(button.data('afsp5ap_bonus'));
			modal.find('.modal-body input[name="recoil_x_bonus"]').val(button.data('recoil_x_bonus'));
			modal.find('.modal-body input[name="recoil_y_bonus"]').val(button.data('recoil_y_bonus'));
			modal.find('.modal-body input[name="handling_bonus"]').val(button.data('handling_bonus'));

			modal.find('.modal-body select[name="weapon_class"]').val(button.data('weapon_class'));
			modal.find('.modal-body select[name="weapon_type"]').val(button.data('weapon_type'));
		}

		attr = button.attr('data-name');
		if (typeof attr !== typeof undefined && attr !== false) {
			modal.find('.modal-body input[name="name"]').val(jQuery.parseJSON(button.data('name')));
			modal.find('.modal-body input[name="name_short"]').val(jQuery.parseJSON(button.data('name_short')));
			modal.find('.modal-body input[name="name_br"]').val(jQuery.parseJSON(button.data('name_br')));
			modal.find('.modal-body input[name="description"]').val(jQuery.parseJSON(button.data('description')));
			modal.find('.modal-body input[name="description_br"]').val(jQuery.parseJSON(button.data('description_br')));
		}

		let data_integrated = button.data('integrated');
		$.each(data_integrated, function (key, value) {
			modal.find('.modal-body select[name="' + key + '"]').val(value);
		});

		let data_mechanism = button.data('mechanism');
		$.each(data_mechanism, function (key, value) {
			modal.find('.modal-body select[name="' + key + '"]').val(value);
		});

		let data_boolean = button.data('boolean');
		$.each(data_boolean, function (key, value) {
			modal.find('.modal-body input[name="' + key + '"]').prop('checked', !!+value);
		});
	});
});