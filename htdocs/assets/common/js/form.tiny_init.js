$(function () {
	initMCE();
});

function initMCE() {

	tinyMCE.init({
		mode:"specific_textareas",
		editor_selector : "tiny_editor",
		convert_urls : false,
		theme : "advanced",
		skin : "o2k7",
		skin_variant : "silver",
		plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,cut,copy,paste,pastetext,pasteword|,bullist,numlist,|,undo,redo,|,outdent,indent,blockquote,|,forecolor,backcolor,|,link,unlink,image,cleanup,code",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "js/template_list.js",
		external_link_list_url : "js/link_list.js",
		external_image_list_url : "js/image_list.js",
		media_external_list_url : "js/media_list.js"

	});

}