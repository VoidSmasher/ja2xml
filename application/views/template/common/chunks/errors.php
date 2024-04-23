<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 07.06.11
 * Time: 10:52
 */
?>
<div class="alert alert-danger">
	<?php
	if (is_array($errors)): foreach ($errors as $key => $error):
		$error = Helper_Error::parse_error($error);
		if (empty($error)) {
			continue;
		}
		?>
		<p><?php echo $error; ?></p>
	<?php endforeach; ?>
	<?php endif; ?>
</div>
