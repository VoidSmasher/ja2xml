$(function() {
	$.each(form_suggests, function(index, suggest) {
		$("input[name='" + suggest.name + "']").autocomplete({
			minLength: suggest.min_length,
			source: suggest.uri,
			select: function( event, ui ) {
				$("input[name='" + suggest.id_name + "']").val(ui.item.id);
			}
		});
	});
});