<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 12.02.14
 * Time: 7:39
 */
?>
<ul class="nav navbar-nav navbar-right">
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<span class="glyphicon glyphicon-user"></span> <i class="caret"></i>
		</a>
		<ul class="dropdown-menu">
			<li><span><?php echo $user_name; ?></span></li>
			<li class="divider"></li>
			<?php echo $menu_body; ?>
			<li class="divider"></li>
			<li><a href="/auth/logout"><?php echo __('auth.button.logout'); ?></a></li>
		</ul>
	</li>
</ul>