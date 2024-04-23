$(function () {
	$('#remove_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		let button = $(event.relatedTarget);

		modal.find('.modal-body input[name="item_index"]').val(button.data('item_index'));
		modal.find('.modal-body input[name="attach_index"]').val(button.data('attach_index'));
		modal.find('.modal-body input[name="item_name"]').val(button.data('item_name'));
		modal.find('.modal-body input[name="attach_name"]').val(button.data('attach_name'));
	});

	$('#restore_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		let button = $(event.relatedTarget);

		modal.find('.modal-body input[name="item_index"]').val(button.data('item_index'));
		modal.find('.modal-body input[name="attach_index"]').val(button.data('attach_index'));
		modal.find('.modal-body input[name="item_name"]').val(button.data('item_name'));
		modal.find('.modal-body input[name="attach_name"]').val(button.data('attach_name'));
	});
});