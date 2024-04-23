$(function () {
	//numbers
	$(".table-sortable").sortable({
		items: 'tr',
		handle: $('tr i.sort'),
		axis: 'y',
		cursor: 'move',
		update: function (e, f) {
			var sort_list = {};
			$('.table-sortable tbody tr').each(function (i) {
				var sort = (i + 1);
				sort_list[$(this).data("id")] = sort;
				$(this).find("td.col-sortable .number").text(sort);
			});

			$.ajax({
				type: "POST",
				url: window.sortable_url,
				dataType: 'JSON',
				data: {sort_list: sort_list},
				async: false
			}).success(function (response) {
				//success
			});

		}
	}).disableSelection();
});