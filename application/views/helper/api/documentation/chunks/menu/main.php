<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 30.10.12
 * Time: 14:39
 */
?>
<div class="nav-collapse collapse">
	<ul class="nav">
		<?php foreach ($main_menu as $menu_item => $menu_link): ?>
			<?php if ($menu_link == ':divider'): ?>
				<li class="divider-vertical"></li>
			<?php elseif (is_array($menu_link) && !array_key_exists('link', $menu_link) && !array_key_exists('menu', $menu_link)): ?>
				<?php echo View::factory('template/bootstrap/navigation/dropdown')
						->bind('menu_item', $menu_item)
						->bind('menu_link', $menu_link);
				?>
			<?php elseif (is_array($menu_link) && !array_key_exists('link', $menu_link) && array_key_exists('menu', $menu_link)): ?>
				<?php echo View::factory('template/bootstrap/navigation/dropdown_menu')
						->bind('menu_item', $menu_item)
						->bind('menu_link', $menu_link);
				?>
			<?php else: ?>
				<?php echo View::factory('template/bootstrap/navigation/item')
						->bind('menu_item', $menu_item)
						->bind('menu_link', $menu_link); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</div>
