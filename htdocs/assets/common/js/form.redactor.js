$(function () {
	$.each(form_redactor, function (index, obj) {
		force_init_redactor(obj.name, obj.params);
	});
});

function force_init_redactor (name, params) {
	var obj = $("textarea[name='"+name+"']");
	obj.redactor(params);
}
