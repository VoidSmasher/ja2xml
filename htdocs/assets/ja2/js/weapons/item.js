$(function () {
	let modal = $('#item_modal');
	let table = modal.find('#stance_table');
	let input_stand = modal.find('input[name="STAND_MODIFIERS"]');
	let input_crouch = modal.find('input[name="CROUCH_MODIFIERS"]');
	let input_prone = modal.find('input[name="PRONE_MODIFIERS"]');

	let check_table = function() {
		if (table.find('tr').length > 1) {
			table.show();
			table.closest('div.container-table').find('div.table-empty-message').hide();
		} else {
			table.hide();
			table.closest('div.container-table').find('div.table-empty-message').show();
		}
	};

	modal.on('show.bs.modal', function (event) {
		let modal = $(this);
		let button = $(event.relatedTarget);

		let data_stand = button.data('stand');
		let data_crouch = button.data('crouch');
		let data_prone = button.data('prone');

		if (data_stand !== undefined) {
			console.log(data_stand);
			data_stand = JSON.parse(data_stand);

			data_stand.each(function (field, value) {
				console.log(field);
				console.log(value);
			});
		}

		modal.find('.modal-body input[name="data"]').val(button.data('data'));
	});


	let test_data_apply = function (e) {
		e.preventDefault();

		let range = modal.find('.modal-body input[name="range"]').val();
		let dx = modal.find('.modal-body input[name="dx"]').val();
		let dy = modal.find('.modal-body input[name="dy"]').val();

		if (dx.length < 1) {
			dx = dy;
		}
		if (dy.length < 1) {
			dy = dx;
		}

		let data = {};
		let json_string = input.val();
		if (json_string.length > 0) {
			data = JSON.parse(json_string);
		}

		data[range] = {
			"range": range,
			"dx": dx,
			"dy": dy,
		};

		input.val(JSON.stringify(data));

		let tr = table.find('tr[data-range="' + range + '"]');

		if (tr.length < 1) {
			let btn_edit = $('<button>')
				.attr('type', 'button')
				.addClass('btn btn-warning btn-xs')
				.attr('data-range', range)
				.attr('data-dx', dx)
				.attr('data-dy', dy)
				.attr('data-toggle', 'modal')
				.attr('data-target', '#test_data_modal')
				.append($('<i class="fa fa-edit">'))
				.append(' Редактировать');

			let btn_delete = $('<button>')
				.attr('type', 'button')
				.addClass('btn btn-danger btn-xs')
				.attr('data-range', range)
				.append($('<i class="fa fa-close">'))
				.append(' Удалить')
				.click(test_data_remove);

			tr = $('<tr>').attr('data-range', range);
			tr.append($('<td>').html(range));
			tr.append($('<td>').html(dx));
			tr.append($('<td>').html(dy));
			tr.append($('<td>').html('---'));
			tr.append($('<td>').addClass('table-col-control').append(btn_edit));
			tr.append($('<td>').addClass('table-col-control').append(btn_delete));
			table.append(tr);
		} else {
			tr.find('td').each(function (i, v) {
				switch (i) {
					case 0:
						$(this).html(range);
						break;
					case 1:
						$(this).html(dx);
						break;
					case 2:
						$(this).html(dy);
						break;
					case 3:
						$(this).html('---');
						break;
				}
			});
		}

		check_table();
	};

	let test_data_remove = function (e) {
		e.preventDefault();

		let range = $(this).data('range');

		if (!confirm('Удалить данные для дистанции ' + range + '?')) {
			return;
		}

		table.find('tr[data-range="' + range + '"]').remove();

		let json_string = input.val();
		if (json_string.length > 0) {
			let data = JSON.parse(json_string);

			delete data[range];

			input.val(JSON.stringify(data));
		}

		check_table();
	};

	table.find('button.btn-test-data-remove').each(function () {
		$(this).click(test_data_remove);
	});

	$('button.btn-apply').click(test_data_apply);
});