<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 12.02.14
 * Time: 7:39
 */
?>
<ul class="nav navbar-nav navbar-right navbar-lang">
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<?php echo $current_icon . $current_lang; ?> <i class="caret"></i>
		</a>
		<ul class="dropdown-menu" style="min-width: 20px">
			<?php echo $menu_body; ?>
		</ul>
	</li>
</ul>