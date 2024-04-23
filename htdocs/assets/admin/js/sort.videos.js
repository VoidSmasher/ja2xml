try{
	parent_id = global_parent_id;
} catch(err) {
	parent_id = 0;
}

$(function () {
	$('select#select_channel').change(function(){
		document.location.href = channel_change_addr + $(this).val();
	});

	$("tr.ln").each(function(n,o){
		$(o).children().each(function(i,e){
			$(e).attr('width', $(e).width());
		});
	});

	$("tr.ln").mousedown(function(){
		$(this).children().each(function(i,e){
			$(e).css('border-bottom', '1px solid #DDDDDD');
		})
	}).mouseup(function(){
		$(this).children().each(function(i,e){
			$(e).css('border-bottom', '0');
		})
	});

	$("#sort").sortable({
		items : 'tr.ln',
		handle : $('tr.ln'),
		axis : 'y',
		cursor : 'move',
		start : function(e, f){
			item_id = f.item.attr('id').substr(1);
		},
		stop : function(){
			var d = $('.connectedSortable').sortable('toArray');
			stop_pos = $.inArray('y'+item_id, d) + 1;
			$("#overlay").fadeTo(200, 0.85);
			tmoutID = setTimeout('location.reload(true)', 10000);
			$.post(ajax_addr, {'id1':item_id, 'id2':stop_pos, 'id3':parent_id}, function (back_data){
				clearTimeout(tmoutID);
				if (back_data=='ok'){
					$("#overlay").fadeOut(200);
				}
				else {
					alert(back_data);
				}
			}, "text");
		}
	});
	$("#sort").disableSelection();
});