$(function () {
	$('#range_modal').on('show.bs.modal', function (event) {
		var modal = $(this);
		var button = $(event.relatedTarget);

		modal.find('.modal-body input[name="calibre"]').val(button.data('calibre'));
		modal.find('.modal-body input[name="range_angle"]').val(button.data('range_angle'));
		modal.find('.modal-body input[name="range_mult"]').val(button.data('range_mult'));
		modal.find('.modal-body input[name="range_div"]').val(button.data('range_div'));
		modal.find('.modal-body input[name="range_delta"]').val(button.data('range_delta'));
		modal.find('.modal-body input[name="range_weapon_id"]').val(button.data('range_weapon_id'));
		modal.find('.modal-body input[name="range_weapon"]').val(button.data('range_weapon'));
	});
});