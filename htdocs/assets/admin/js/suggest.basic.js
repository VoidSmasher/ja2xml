$(function() {
	$( "#suggest" ).autocomplete({
		minLength: 2,
		source: ajax_suggest_addr,
		select: function( event, ui ) {
			$('#'+ajax_suggest_id).val(ui.item.id);
		}
	});
});