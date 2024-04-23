<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 22.07.13
 * Time: 18:25
 */
$first_nav = true;
?>
	<ul class="nav nav-list docs-sidenav" data-spy="affix">
<?php foreach ($navigation as $link => $name): ?>
		<li<?php
		if ($first_nav):
			echo ' class="active"';
			$first_nav = false;
		endif;
		?>><a href="<?php echo $link; ?>"><i class="glyphicon glyphicon-chevron-right"></i> <?php echo $name; ?></a></li>
<?php endforeach; ?>
	</ul>
