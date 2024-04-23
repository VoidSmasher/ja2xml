<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 19.07.13
 * Time: 9:48
 */
?>
<div class="navbar navbar-fixed-top" xmlns="http://www.w3.org/1999/html">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
<?php if (!empty($brand_links)): ?>
<?php foreach ($brand_links as $brand_name => $brand_link): ?>
			<a class="brand" href="<?php echo $brand_link; ?>"><?php echo $brand_name; ?></a>
<?php endforeach; ?>
<?php endif; ?>

			<div class="nav-collapse collapse">
				<ul class="nav">
					<?php foreach ($main_menu as $menu_item => $menu_link): ?>
						<?php if ($menu_link == ':divider'): ?>
							<li class="divider-vertical"></li>
						<?php elseif (is_array($menu_link) && !array_key_exists('link', $menu_link) && !array_key_exists('menu', $menu_link)): ?>
							<?php echo View::factory(Helper_Api_Documentation::DOCUMENTATION_PATH . 'chunks/navigation/dropdown')
									->bind('menu_item', $menu_item)
									->bind('menu_link', $menu_link);
							?>
						<?php elseif (is_array($menu_link) && !array_key_exists('link', $menu_link) && array_key_exists('menu', $menu_link)): ?>
							<?php echo View::factory(Helper_Api_Documentation::DOCUMENTATION_PATH . 'chunks/navigation/dropdown_menu')
									->bind('menu_item', $menu_item)
									->bind('menu_link', $menu_link);
							?>
						<?php else: ?>
							<?php echo View::factory(Helper_Api_Documentation::DOCUMENTATION_PATH . 'chunks/navigation/item')
									->bind('menu_item', $menu_item)
									->bind('menu_link', $menu_link); ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div>