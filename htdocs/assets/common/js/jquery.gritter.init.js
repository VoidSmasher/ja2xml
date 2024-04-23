$(function () {
	getMessages();
});

function showMessage(type, title, text, image, link) {

	if(link && title){
		title = '<a href="' + link + '">' + title + '</a>';
	}
	var message_options = {
		title:title,
		text:text,
		time:''
	};

	if (image) {
		message_options['image'] = image;
	} else {
		message_options['image'] = default_image;
	}
	if (type == 1) {
		message_options['class_name'] = 'my-sticky-class';
		message_options['sticky'] = true;
	}
	$.gritter.add(message_options);

	return false;
}

function getMessages() {
	$.ajax({
		url:'/flash.json',
		dataType:'json',
		async:false
	}).done(function (messages) {
		if (messages.length > 0) {
			$.each(messages, function (key, message) {
				showMessage(message.type, message.label, message.text, message.image, message.link);
			});
		}
	});
	return false;
}