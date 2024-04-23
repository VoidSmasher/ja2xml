$(function () {

	$.each(force_list, function (index, obj) {
		init_force_list(obj.id, obj.params);
	});

});

function init_force_list(list_id, params) {

	var page = 1;

	$('#' + list_id + ' .table-button-more').click(function ($e) {
		$e.preventDefault();

		var this_row = $(this).parent().parent();

		page++;

		$.ajax({
			type: "POST",
			url: params.url_more,
			dataType: 'html',
			async: false,
			data: {page: page},
			success: function (html) {
				if (html.length > 0) {
					this_row.before(html);
				} else {
					this_row.hide();
				}
			}
		});
	});

}