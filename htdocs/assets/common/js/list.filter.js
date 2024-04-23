$(function() {
	$('div.admin-filter-slide').click(function() {
		var obj = $(this).find('span');
		var obj_filter = $('div.admin-filter-block');
		var obj_filter_line = $('hr.admin-filter-line');
		var obj_filter_header = $('#filter-header');
		obj_filter.toggle();
		var is_visible = obj_filter.is(":visible");
		if (is_visible) {
			obj.html(filter.title_hide);
			obj_filter_line.hide();
			obj_filter_header.show();
		} else {
			obj.html(filter.title_show);
			obj_filter_line.show();
			if ($('div.admin-filter-conditions').find('span').length == 0) {
				obj_filter_header.hide();
			}
		}
		$.ajax({
			url: filter.url,
			type: "POST",
			data: {is_visible: is_visible},
			dataType: 'json',
			async: true
		});
		return false;
	});
});
