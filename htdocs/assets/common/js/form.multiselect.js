$(function () {
	$.each(form_multiselect, function (index, _input) {
		obj = $("select[name='" + _input.name + "']");
		obj.multiselect(_input.params);
		reset_button = $("button[data-target='" + _input.name + "']");
		if (reset_button.length > 0) {
			reset_button.click(function(e) {
				e.preventDefault();
				$obj_name = $(this).attr('data-target');
				obj = $("select[name='" + $obj_name + "']");
				obj.multiselect('deselectAll', false);
				obj.multiselect('updateButtonText');
			});
		}
	});
});