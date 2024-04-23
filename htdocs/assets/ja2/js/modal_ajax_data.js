$(function () {
	let get_data = function (modal, event) {
		let button = $(event.relatedTarget);
		let action = modal.data('action');
		let index = button.data('id');
		let modal_body = modal.find('.modal-body');

		modal_body.find('input[name="id"]').val(index);

		modal_body.find('input').prop("disabled", true);
		modal_body.find('select').prop("disabled", true);

		$.ajax({
			url: action + '/' + index + '.json',
			// type:"POST",
			dataType: 'json',
		}).done(function (data) {
			if (data.status === 'ok') {
				let data_input = data.input;
				$.each(data_input, function (key, value) {
					modal_body.find('input[name="' + key + '"]').val(value);
				});

				let data_select = data.select;
				$.each(data_select, function (key, value) {
					modal_body.find('select[name="' + key + '"]').val(value);
				});

				let data_checkbox = data.checkbox;
				$.each(data_checkbox, function (key, value) {
					modal_body.find('input[name="' + key + '"]').prop('checked', !!+value);
				});

				modal_body.find('input').prop("disabled", false);
				modal_body.find('select').prop("disabled", false);
			}
			else {
				console.log('Failed to get modal data');
				console.log(data);
				modal.close();
				alert('Failed to get modal data');
			}
		});
	};

	$('#weapon_data_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		get_data(modal, event);
	});
	// $('#damage_modal').on('show.bs.modal', get_data());
});