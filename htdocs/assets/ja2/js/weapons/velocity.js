$(function () {
	$('#velocity_modal').on('show.bs.modal', function (event) {
		var modal = $(this);
		var button = $(event.relatedTarget);

		modal.find('.modal-body input[name="id"]').val(button.data('id'));
		modal.find('.modal-body input[name="muzzle_velocity"]').val(button.data('muzzle_velocity'));
	});
});