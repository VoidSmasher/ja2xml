$(function () {
	var video_ids_to_attach = '';

	$('input[name=chb__select_all]').click(function(){
		if ($(this).attr('checked') == 'checked'){
			$('input.attach').attr('checked','checked');
		} else {
			$('input.attach').removeAttr('checked');
		}
		update_attached();
	});

	$('input.attach').click(function(){
		update_attached();
	});

	$('a.send-attached').click(function(){
		if (video_ids_to_attach == ''){
			alert('Необходимо отметить хотя бы одно видео для прикрепления');
		} else {
			var action_url = $(this).attr('href');

			$('body').append(
				'<form action="' + action_url + '" method="post" id="send_attached_form" accept-charset="utf-8">' +
				'<input type="hidden" name="attached_ids" value="' + video_ids_to_attach + '" />' +
				'</form>'
			);

			$('form#send_attached_form').submit();
		}

		return false;
	});

	$('form#send_attached').submit(function(){
		if ($('input[name=attached_ids]').val()==''){
			alert('Необходимо отметить хотя бы одно видео для прикрепления');
			return false;
		}
	});

	function update_attached(){
		var ids = new Array();
		var num = 0;
		var on;
		$('input.attach').each(function(i,v){
			on = ($(v).attr('checked') == 'checked' && $(v).attr('name') != 'chb__select_all') ? true : false;
			if (on) {
				ids[num++] = $(v).attr('name').substr(4);
			}
		});

		video_ids_to_attach = ids.join(',');
	}
});