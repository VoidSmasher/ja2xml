<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 12.05.16
 * Time: 13:30
 */
?>
<!DOCTYPE html>
<html lang="<?php echo i18n::lang(); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php echo $description; ?>">
	<meta name="keywords" content='<?php echo $keywords; ?>' />

	<!-- Styles -->
	<style type="text/css">
		body {
			padding-top: 60px;
		}
	</style>

	<?php echo $assets_header; ?>
</head>

<body<?php if (!empty($data_spy_target)) {
	echo ' data-spy="scroll" data-target="' . $data_spy_target . '"';
} ?>>
<?php echo $counter_top; ?>
<?php echo $before_header; ?>
<?php echo $header; ?>
<?php echo $after_header; ?>

<!-- container -->
<div class="container<?php echo ($fluid) ? '-fluid' : ''; ?> bs-docs-container">

	<?php if (!empty($errors)): ?>
		<div class="row<?php echo ($fluid) ? '-fluid' : ''; ?>">
			<div class="alert alert-block alert-error">
				<?php echo $errors; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="row<?php echo ($fluid) ? '-fluid' : ''; ?>">
		<?php echo $content; ?>
	</div>

	<div class="row<?php echo ($fluid) ? '-fluid' : ''; ?>">
		<?php echo $before_footer; ?>
		<?php echo $footer; ?>
		<?php echo $after_footer; ?>
	</div>

</div>
<!-- /container -->

<?php echo $modal; ?>

<?php echo $assets_footer; ?>
<?php echo $counter_bottom; ?>
</body>
</html>
