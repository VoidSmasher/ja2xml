<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 16.01.14
 * Time: 23:18
 */
?>
<div>
	<legend><?php echo $title; ?></legend>

	<p><?php echo nl2br($message); ?></p>
	<?php if (!empty($button)): ?>
		<p><?php echo $button; ?></p>
	<?php endif; ?>
</div>

<div class="clearfix"></div>