$(function () {
	$('.two-lists-button-left').click(function (e) {
		e.preventDefault();
		var name = $(this).attr('rel');
		var list_available = $("#" + name + "_available");
		var list_selected = $("#" + name + "_selected");
		var container_values = $("#" + name + "_values");
		var values = list_selected.val();
		if (values == null) {
			return false;
		}
		$.each(values, function (index, value) {
			var option = list_selected.find("option[value='" + value + "']");
			if (option.length > 0) {
				list_available.append(option);
				container_values.find("input[value='" + value + "']").remove();
			}
		});
		container_values.trigger('updated');
		container_values.trigger('removed');
	});

	$('.two-lists-button-right').click(function (e) {
		e.preventDefault();
		var name = $(this).attr('rel');
		var list_available = $("#" + name + "_available");
		var list_selected = $("#" + name + "_selected");
		var container_values = $("#" + name + "_values");
		var values = list_available.val();
		if (values == null) {
			return false;
		}
		$.each(values, function (index, value) {
			var option = list_available.find("option[value='" + value + "']");
			if (option.length > 0) {
				list_selected.append(option);
				container_values.append($("<input/>")
						.attr("type", 'hidden')
						.attr("name", name + '[]')
						.attr("value", value)
				);
			}
		});
		container_values.trigger('updated');
		container_values.trigger('added');
	});
});
