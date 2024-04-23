<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 30.10.12
 * Time: 14:46
 */
?>
<li<?php echo $group_attributes; ?>>
	<a<?php echo $attributes; ?>>
		<?php echo $icon . $label; ?><i class="caret"></i>
	</a>
	<ul<?php echo $menu_attributes; ?>>
		<?php echo $menu_body; ?>
	</ul>
</li>