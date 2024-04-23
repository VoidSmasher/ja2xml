<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 12.05.16
 * Time: 13:30
 */
if (!isset($code)) {
	$code = 500;
}
if (!isset($title)) {
	$title = __('error.500.title');
}
if (!isset($text)) {
	$text = __('error.500.text');
}
if (!isset($show_back_to_main)) {
	$show_back_to_main = false;
}
?>
<!DOCTYPE html>
<html lang="<?php echo i18n::lang(); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<meta name="viewport" content="width=375">
	<link type="text/css" href="/assets/common/css/font-awesome.min.css" rel="stylesheet" media="screen">
	<link type="text/css" href="/assets/common/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link type="text/css" href="/assets/common/css/bootstrap-override.css" rel="stylesheet" media="screen">
	<link type="text/css" href="/assets/common/css/error.css" rel="stylesheet" media="screen">
</head>

<body>
<!-- container -->
<div class="container">

	<div class="row">
		<div class="error-block jumbotron">
			<h1><?php echo $title; ?></h1>

			<h3><?php echo $text; ?></h3>

			<?php if ($show_back_to_main): ?>
				<br/>
				<a class="btn btn-primary" href="/"><?php echo __('common.back_to_main'); ?></a>
			<?php endif; ?>
		</div>
	</div>

</div>
<!-- /container -->
</body>
</html>
