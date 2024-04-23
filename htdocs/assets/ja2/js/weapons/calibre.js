$(function () {
	$('#calibre_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		let button = $(event.relatedTarget);

		let mag_size_list = button.data('mag_size_list');
		let mag_size_select = modal.find('.modal-body select[name="mag_size"]');

		// console.log(mag_size_list);

		// mag_size_list = JSON.parse(mag_size_list);
		mag_size_select.html('');

		$.each(mag_size_list, function (key, value) {
			mag_size_select.append($('<option>').val(value).html(key));
		});
		mag_size_select.val(button.data('mag_size'));

		modal.find('.modal-body input[name="id"]').val(button.data('id'));
		modal.find('.modal-body select[name="calibre"]').val(button.data('calibre'));
	});
});