<!DOCTYPE html>
<html lang="<?php echo i18n::lang(); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php echo $description; ?>">
	<meta name="keywords" content='<?php echo $keywords; ?>' />
	<meta name="author" content="">

	<!-- Styles -->
	<style type="text/css">
		body {
			padding-top: 60px;
			padding-bottom: 40px;
		}

		.sidebar-nav {
			padding: 9px 0;
		}
	</style>

	<?php foreach ($styles as $style => $media):
		if (!empty($media)):
			echo HTML::style($style, array('media' => $media)) . "\n";
		else:
			echo HTML::style($style) . "\n";
		endif;
	endforeach; ?>

	<!-- Vars -->
	<?php if (!empty($js_vars)): ?>
		<script type="application/javascript">
			<?php foreach ($js_vars as $jsKey => $jsVar): ?>
			var <?php echo $jsKey . ' = ' . ((is_array($jsVar)) ? json_encode($jsVar) : "'".$jsVar."'"); ?>;
			<?php endforeach; ?>
		</script>
	<?php endif; ?>

	<!-- Javascript -->
	<?php foreach ($scripts as $script => $type):
		if (!empty($type)):
			echo HTML::script($script, array('type' => $type)) . "\n";
		else:
			echo HTML::script($script) . "\n";
		endif;
	endforeach; ?>

	<?php if (!empty($scripts_embedded)): ?>
		<script type="application/javascript">
			<?php echo $scripts_embedded; ?>
		</script>
	<?php endif; ?>
</head>

<body<?php if (!empty($data_spy_target)) {
	echo ' data-spy="scroll" data-target="' . $data_spy_target . '"';
} ?>>
<?php echo $counter_top; ?>
<?php echo $header; ?>

<!-- container -->
<div class="container<?php echo ($fluid) ? '-fluid' : ''; ?>">
	<div class="row<?php echo ($fluid) ? '-fluid' : ''; ?>">

		<div class="col-sm-3 docs-sidebar">
			<?php echo $navigation; ?>
		</div>

		<div class="col-sm-9">
			<?php if (!empty($errors)): ?>
				<div class="alert alert-block alert-error">
					<?php echo $errors; ?>
				</div>
			<?php endif; ?>
			<?php if (!empty($notifications)): ?>
				<div class="alert alert-block alert-success">
					<?php echo $notifications; ?>
				</div>
			<?php endif; ?>
			<?php echo $content; ?>
		</div>

	</div>

	<div class="row<?php echo ($fluid) ? '-fluid' : ''; ?>">
		<?php echo $before_footer; ?>

		<?php echo $footer; ?>
	</div>
</div>
<!-- /container -->

<?php foreach ($scripts_in_footer as $script => $type):
	if (!empty($type)):
		echo HTML::script($script, array('type' => $type)) . "\n";
	else:
		echo HTML::script($script) . "\n";
	endif;
endforeach; ?>

<?php echo $modal; ?>

<?php echo (Kohana::$environment != Kohana::PRODUCTION) ? $debug : ''; ?>

<?php echo $counter_bottom; ?>
</body>
</html>
