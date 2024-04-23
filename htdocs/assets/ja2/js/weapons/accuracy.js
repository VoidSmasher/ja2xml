$(function () {
	$('#accuracy_modal').on('show.bs.modal', function (event) {
		let modal = $(this);
		let button = $(event.relatedTarget);

		let accuracy_delta = modal.find('.modal-body input[name="accuracy_delta"]');

		let weapon_id = button.data('accuracy_weapon_id');
		if (weapon_id) {
			accuracy_delta.prop('disabled', true);
		} else {
			accuracy_delta.prop('disabled', false);
		}

		modal.find('.modal-body input[name="calibre"]').val(button.data('calibre'));
		modal.find('.modal-body input[name="accuracy_angle"]').val(button.data('accuracy_angle'));
		modal.find('.modal-body input[name="accuracy_mult"]').val(button.data('accuracy_mult'));
		accuracy_delta.val(button.data('accuracy_delta'));
		modal.find('.modal-body input[name="accuracy_x"]').val(button.data('accuracy_x'));
		modal.find('.modal-body input[name="accuracy_weapon_id"]').val(button.data('accuracy_weapon_id'));
		modal.find('.modal-body input[name="accuracy_weapon"]').val(button.data('accuracy_weapon'));
		modal.find('.modal-body input[name="sniper_range_bonus"]').val(button.data('sniper_range_bonus'));
		modal.find('.modal-body input[name="sniper_accuracy_bonus"]').val(button.data('sniper_accuracy_bonus'));
		modal.find('.modal-body input[name="velocity_mult"]').val(button.data('velocity_mult'));
	});
});