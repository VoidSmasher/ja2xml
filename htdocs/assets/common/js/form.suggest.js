$(function () {
	$.each(form_suggests, function (index, suggest) {
//		$("input[name='" + suggest.name + "']").autocomplete({
//			minLength: suggest.min_length,
//			source: suggest.uri,
//			select: function (event, ui) {
//				$("input[name='" + suggest.id_name + "']").val(ui.item.id);
//			}
//		});

		$("select[name='" + suggest.name + "']").selectize({
			valueField: 'id',
			labelField: 'value',
			searchField: 'value',
			options: suggest.options,
			create: false,
//			render: {
//				option: function (item, escape) {
//					console.log(item);
//					console.log(escape);
//
//					return '<div>' +
//							'<span class="title">' +
//							'<span class="name">' + escape(item.title) + '</span>' +
//							'</span>' +
//							'<span class="description">' + escape(item.description || 'No synopsis available at this time.') + '</span>' +
//							'<span class="actors">' +  + '</span>' +
//							'</div>';
//				}
//			},
			load: function (query, callback) {
				if (query.length < suggest.min_length) {
					return callback();
				}
				suggest.data.term = query;
				$.ajax({
					url: suggest.uri,
					type: 'GET',
					dataType: 'json',
					data: suggest.data,
					error: function () {
						callback();
					},
					success: function (res) {
						callback(res);
					}
				});
			}
		});
	});
});

