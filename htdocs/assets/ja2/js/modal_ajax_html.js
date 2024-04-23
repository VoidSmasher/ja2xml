$(function () {
	let modal_html_get_data = function (modal, event) {
		let button = $(event.relatedTarget);
		let action = modal.data('action');
		let index = button.data('id');
		let modal_body = modal.find('.modal-body');

		modal_body.html('Loading...');

		$.ajax({
			url: action + '/' + index,
			// type:"POST",
			dataType: 'html',
		}).done(function (data) {
			modal_body.html(data);
		});
	};

	$('#attachment_data_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		modal_html_get_data(modal, event);
	});

	$('#attachment_type_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		modal_html_get_data(modal, event);
	});

	$('#mounts_external_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		modal_html_get_data(modal, event);
	});
	// $('#damage_modal').on('show.bs.modal', get_data());
});