$(function () {
	moment.locale('ru');
	$.each(form_date_range, function (index, obj) {
		force_init_date_range(obj.name, obj.params)
	});
});

function force_init_date_range(name, params) {
	obj = $("input[name='" + name + "']");
	//obj.daterangepicker(dtp_input.params);
	obj.daterangepicker(
		{
			ranges: {
				'Сегодня': [moment('00:00', 'HH:mm'), moment('23:59', 'HH:mm')],
				'Вчера': [moment('00:00', 'HH:mm').subtract(1, 'days'), moment('23:59', 'HH:mm').subtract(1, 'days')],
				'Последние 7 дней': [moment('00:00', 'HH:mm').subtract(6, 'days'), moment('23:59', 'HH:mm')],
				'Последние 30 дней': [moment('00:00', 'HH:mm').subtract(29, 'days'), moment('23:59', 'HH:mm')],
				'Этот месяц': [moment('00:00', 'HH:mm').startOf('month'), moment('23:59', 'HH:mm').endOf('month')],
				'Прошлый месяц': [moment('00:00', 'HH:mm').subtract(1, 'month').startOf('month'), moment('23:59', 'HH:mm').subtract(1, 'month').endOf('month')]
			},
			locale: {
				applyLabel: 'Выбрать',
				cancelLabel: 'Отмена',
				fromLabel: 'От',
				toLabel: 'До',
				weekLabel: 'W',
				customRangeLabel: 'Выбрать...',
				daysOfWeek: moment.weekdaysMin(),
				monthNames: moment.months(),
				firstDay: moment.localeData()._week.dow
			},
			timePicker: true,
			timePickerIncrement: 1,
			timePicker12Hour: false,
			format: 'DD.MM.YYYY HH:mm'
		}
	);
}