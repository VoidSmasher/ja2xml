<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 12.02.14
 * Time: 9:43
 */
?>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<?php if (!empty($brand_links)): ?>
				<?php foreach ($brand_links as $brand_name => $brand_link): ?>
					<a class="navbar-brand" href="<?php echo $brand_link; ?>"><?php echo $brand_name; ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="navbar-collapse collapse">
			<?php echo $main_menu; ?>
			<?php if (isset($user_is_authorized) && $user_is_authorized && isset($user_menu)): ?>
				<?php echo $user_menu; ?>
			<?php else: ?>
				<ul class="nav navbar-nav navbar-right">
					<?php if (Helper_Auth::get_permission('allow_login')): ?>
						<li><a href="/auth/login"><?php echo __('auth.button.login'); ?></a></li>
					<?php endif; ?>
					<?php if (Helper_Auth::get_permission('allow_registration')): ?>
						<li><a href="/auth/registration"><?php echo __('auth.button.register'); ?></a></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
			<?php if (isset($lang_menu)) {
				echo $lang_menu;
			} ?>
		</div>
	</div>
</div>
