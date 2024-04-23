<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 28.08.14
 * Time: 6:40
 */
?>
<div class="well">
	<h4><?php echo __('common.tags'); ?></h4>

	<?php foreach ($tags as $_id => $_title) {
		echo Force_Button::factory($_title)->btn_xs()->link("/articles/tag/{$_id}")->render();
	} ?>
</div>
