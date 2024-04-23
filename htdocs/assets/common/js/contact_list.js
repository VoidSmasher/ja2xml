/**
 * User: a.stifanenkov
 * Date: 28.04.12
 * Time: 1:29
 */
$(function () {
	var contact_obj;

	$('.to-contact, .right-text__addicon').click(function () {
		var action = $(this).attr('rel');
		contact_obj = $(this);
		$.ajax({
			type:"POST",
			url:action,
			dataType:'json',
			async:false
		}).done(function (done) {
			if (done.status == 'ok') {
				contact_obj.hide();
				if (window.showMessage) {
					showMessage(0, 'Контакты', done.user.name + ' успешно добавлен в контакты', done.user.image, done.user.link);
				}
			}
			else {
				if (window.showMessage) {
					showMessage(0, 'Контакты', 'Ошибка. Попробуйте добавить позже', '', '/contacts');
				}
			}
		});
	});
});