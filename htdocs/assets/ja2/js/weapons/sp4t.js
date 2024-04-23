$(function () {
	$('#sp4t_modal').on('show.bs.modal', function (event) {
		var modal = $(this);
		var button = $(event.relatedTarget);

		modal.find('.modal-body input[name="calibre"]').val(button.data('calibre'));
		modal.find('.modal-body input[name="calibre_semi_speed"]').val(button.data('calibre_semi_speed'));
		modal.find('.modal-body input[name="calibre_burst_recoil"]').val(button.data('calibre_burst_recoil'));
		modal.find('.modal-body input[name="calibre_auto_recoil"]').val(button.data('calibre_auto_recoil'));
		modal.find('.modal-body input[name="sp4t_pistol_bonus"]').val(button.data('sp4t_pistol_bonus'));
		modal.find('.modal-body input[name="sp4t_mp_bonus"]').val(button.data('sp4t_mp_bonus'));
		modal.find('.modal-body input[name="sp4t_rifle_bonus"]').val(button.data('sp4t_rifle_bonus'));
	});
});