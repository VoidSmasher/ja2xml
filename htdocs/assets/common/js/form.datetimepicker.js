$(function () {
	$.each(form_datetime, function (index, obj) {
		force_init_date(obj.name, obj.params);
	});
});

function force_init_date(name, params) {
	obj = $("input[name='" + name + "']");
	obj.datetimepicker(params);
}
