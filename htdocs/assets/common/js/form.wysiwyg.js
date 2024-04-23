$(function () {
	$('.summernote').summernote({
		toolbar: [
			//[groupname, [button list]]

			['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['insert', ['picture', 'link', 'video']],
			['table', ['table']],
			['height', ['height']]
		],
		lang: 'ru-RU',
		height: 300,
		onImageUpload: function(files, editor, welEditable) {
		                sendFile(files[0], editor, welEditable);
		}
//		toolbar: [
//			['format', ['style']],
//			['style', ['bold', 'italic', 'underline', 'strike', 'clear']],
//			['fontname', ['fontname']],
//			['fontsize', ['fontsize']],
//			['color', ['color']],
//			['para', ['ul', 'ol', 'paragraph']],
//			['table', ['table']],
//			['insert', ['picture', 'link', 'video']],
//			['help', ['help']]
//		]
	});

	function sendFile(file, editor, welEditable) {
	            data = new FormData();
	            data.append("file", file);//You can append as many data as you want. Check mozilla docs for this
	            $.ajax({
	                data: data,
	                type: "POST",
	                url: uploader_path,
	                cache: false,
	                contentType: false,
	                processData: false,
	                success: function(url) {
	                    editor.insertImage(welEditable, url);
	                }
	            });
	        }
});
