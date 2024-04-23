$(function () {
	$.each(form_tagsinput, function (index, obj) {
		force_init_tagsinput(obj.name, obj.params);
	});
});

function force_init_tagsinput (name, params) {
	console.log(params);
	return false;


	let items = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		prefetch: params.items_json
	});
	items.initialize();

	let obj = $("input[name='"+name+"']");
	obj.tagsinput({
		itemValue: 'value',
		itemText: 'text',
		typeaheadjs: {
			name: 'items',
			displayKey: 'text',
			source: items.ttAdapter()
		}
	});
}
