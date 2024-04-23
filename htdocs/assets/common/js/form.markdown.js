$(function () {
	$.each(form_markdown, function (index, obj) {
		force_init_markdown(obj.name, obj.params);
	});
});

function force_init_markdown (name, params) {
	var obj = $("textarea[name='"+name+"']");
	obj.pagedownBootstrap();
	//var idPreview = obj.attr('id').replace('input', 'preview');
	//var idButtonRow = obj.attr('id').replace('input', 'button-row');
	//var id = obj.attr('id').replace('wmd-input-', '');
	//$('#' + idButtonRow +' #wmd-button-group4-' + id ).after('<button class="btn btn-primary" id="markdown_preview_'+name+'"><i class="fa fa-search"></i></button>');
	//
	//$('#' + idPreview).hide();
	//
	//$('#markdown_preview_'+name).click(function (e) {
	//	e.preventDefault();
	//	$('#' + idPreview ).toggle();
	//});
}
