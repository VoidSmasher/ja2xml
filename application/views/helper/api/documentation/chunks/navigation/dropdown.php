<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 30.10.12
 * Time: 14:46
 */
?>
<?php
	if (array_key_exists('set', $menu_link) && array_key_exists('comment', $menu_link['set'])){
		$comment = Helper_Popover::get_as_string($menu_link['set']['comment'], 'bottom');
		unset($menu_link['set']);
	} else {
		$comment = '';
	}
?>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"<?php echo $comment; ?>>
		<?php echo Force_Menu_Item::update_name($menu_item); ?><b class="caret"></b>
	</a>
	<ul class="dropdown-menu">
<?php foreach ($menu_link as $sub_menu_item => $sub_menu_link): ?>
<?php if ($sub_menu_link == ':divider'): ?>
		<li class="divider"></li>
<?php else: ?>
		<?php echo View::factory('template/bootstrap/navigation/item')
				->bind('menu_item', $sub_menu_item)
				->bind('menu_link', $sub_menu_link); ?>
<?php endif; ?>
<?php endforeach; ?>
	</ul>
</li>
