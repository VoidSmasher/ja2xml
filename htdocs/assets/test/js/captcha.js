$(document).ready(function () {

	$('#captcha_test').ajaxForm({
		success:function (result) {
			if (result) {
				$('#test_result_true').show();
				$('#test_result_false').hide();
			} else {
				$('#test_result_true').hide();
				$('#test_result_false').show();
			}
		}
	});

});