$(function () {
	$('#damage_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		let button = $(event.relatedTarget);

		modal.find('.modal-body input[name="calibre"]').val(button.data('calibre'));
		modal.find('.modal-body input[name="calibre_damage"]').val(button.data('calibre_damage'));
	});
});