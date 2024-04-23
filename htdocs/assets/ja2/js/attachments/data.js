$(function () {
	$('#attachment_data_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		let button = $(event.relatedTarget);

		modal.find('.modal-body input[name="id"]').val(button.data('id'));

		let AttachmentClass = button.data('attachment_class');

		modal.find('.attachment_bonuses').hide();
		modal.find('#' + AttachmentClass).show();

		let data_boolean = button.data('boolean');
		$.each(data_boolean, function (key, value) {
			modal.find('.modal-body input[name="' + key + '"]').prop('checked', !!+value);
		});
	});
});