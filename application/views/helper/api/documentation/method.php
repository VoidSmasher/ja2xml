<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 19.07.13
 * Time: 9:29
 */
?>
<section id="<?php echo $section_id; ?>" data-offset-top="200">
<div class="page-header">
	<h3><?php echo $method_name; ?></h3>
	<p class="muted"><?php echo $method_path; ?></p>
</div>
<?php if (!empty($description)): ?>
<p><?php echo $description; ?></p>
<?php endif; ?>
<form action="<?php echo $action; ?>" method="post" class="form-horizontal">
	<?php if (!empty($params)):
		$param_name = strtr($controller_and_action_from_api_root, '/.', '__');
		$no = 0;
	?>
	<?php foreach ($params as $key => $value):
		if (is_numeric($key)) $key = $value;
		$no++;
	?>
		<div class="control-group">
			<label class="control-label" for="<?php echo $param_name . '_' . $no; ?>"><?php echo $key; ?></label>
			<div class="controls">
				<input class="input-xxlarge" type="text" id="<?php echo $param_name . '_' . $no; ?>" name="<?php echo $value; ?>" value="">
			</div>
		</div>
	<?php endforeach; ?>
		<?php if (!empty($errors)): ?>
		<p><b>Возможные ошибки</b></p>
		<ul>
		<?php foreach ($errors as $error => $description):
			if (is_numeric($error)):
				$error = $description;
				$description = '';
			endif;
		?>
			<li>
				<b><?php echo $error; ?></b><?php if (!empty($description)) echo ' - ' . $description; ?>
			</li>
		<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	<?php endif; ?>
	<input type="hidden" name="key" value="<?php echo $application_key; ?>"/>
	<input type="submit" class="btn btn-warning" value="Получить результат" />
</form>
</section>
