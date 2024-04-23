<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 30.10.12
 * Time: 14:38
 */
?>
<div class="btn-group pull-right">
	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
		<i class="icon-user"></i> <?php echo Helper_Auth::get_user()->get_name(); ?>
		<span class="caret"></span>
	</a>
	<ul class="dropdown-menu">
<?php if (!empty($user_menu)): ?>
<?php foreach ($user_menu as $menu_item => $menu_link): ?>
<?php if ($menu_link == ':divider'): ?>
		<li class="divider"></li>
<?php elseif (is_array($menu_link) && !array_key_exists('link', $menu_link)): ?>
		<?php echo View::factory('template/bootstrap/navigation/dropdown')
				->bind('menu_item', $sub_menu_item)
				->bind('menu_link', $sub_menu_link); ?>
<?php else: ?>
		<?php echo View::factory('template/bootstrap/navigation/item')
				->bind('menu_item', $menu_item)
				->bind('menu_link', $menu_link)
				->set('icon_class', 'icon'); ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
		<li class="divider"></li>
		<li><a href="/auth/logout">Выход</a></li>
	</ul>
</div>
