<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 11.07.16
 * Time: 15:07
 */
?>
<!DOCTYPE html>
<html lang="<?php echo i18n::lang(); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo __('deploy.title'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<?php echo HTML::style('assets/common/css/font-awesome.min.css'); ?>
	<?php echo HTML::style('assets/common/css/bootstrap.min.css'); ?>
	<?php echo HTML::style('assets/common/css/bootstrap-override.css'); ?>
	<?php echo HTML::script('assets/common/js/jquery-2.2.3.min.js'); ?>
	<?php echo HTML::script('assets/common/js/bootstrap.min.js'); ?>

	<?php echo $assets_header; ?>

	<?php echo HTML::script('assets/common/js/redirect.js'); ?>
</head>
<body>
<br />

<div class="container-fluid">
	<div class="row-fluid">
		<div class="jumbotron">
			<h1><i class='fa fa-wrench'></i> <?php echo __('deploy.title'); ?></h1>

			<p><?php echo __('deploy.text'); ?></p>
		</div>
		<?php echo Force_Config::get_copyright(); ?>
	</div>
</div>

<?php echo $assets_footer; ?>
</body>
</html>