$(function () {
	$('#item_modal').on('show.bs.modal', function (event) {
		var modal = $(this);
		var button = $(event.relatedTarget);

		modal.find('.modal-body input[name="item"]').val(button.data('item'));
		modal.find('.modal-body input[name="weight"]').val(button.data('weight'));
	});
});