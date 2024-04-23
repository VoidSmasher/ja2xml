<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: Andrey Verstov
 * Date: 30.07.12
 * Time: 18:04
 */
$first_tab = true;
$tab_no = 0;
$tab_names = array_keys($tabs);
?>
<div class="row">
	<div class="col-sm-9">
		<h3><?php echo (!empty($form_title)) ? $page_title . ' - ' . $form_title : $page_title; ?></h3>

		<?php
		$first_tab = true;
		$tab_no = 0;
		echo Form::open($form_action, array(
			'class' => 'form-horizontal',
			'role' => 'form',
			'enctype' => 'multipart/form-data',
		));
		?>
		<?php foreach ($tabs as $tab_name => $fields): ?>
			<section class="fc-section" id="group-<?php echo $tab_no;
			$tab_no++; ?>">
				<h4 class="page-header"><?php echo __($tab_name); ?></h4>
				<?php
				if ($fields instanceof View):
					echo $fields; else:
					foreach ($fields as $name => $field):
						if (array_key_exists('name', $field)):
							$name = $field['name'];
						endif;
						?>
						<?php if (array_key_exists('label', $field) && !empty($field['label'])): ?>
						<?php echo Form::label($name, $field['label'], array('class' => 'col-sm-2 control-label')); ?>
					<?php endif; ?>
						<?php echo $field['control']; ?>
						<?php
						$key = 'form.' . $form_name . '.' . $name . '.description';
						$translate = (__($key) != $key) ? __($key) : false;
						$custom_description = array_key_exists('description', $field) ? $field['description'] : false;
						$description = $custom_description ? $custom_description : $translate;
						?>
						<?php if ($description): ?>
						<div class="form-group">
							<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 col-lg-offset-2 col-md-offset-2 col-sm-offset-2 col-xs-offset-0">
								<small><?php echo nl2br($description); ?></small>
							</div>
						</div>
					<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</section>
		<?php endforeach; ?>
		<?php if (is_array($form_buttons) && !empty($form_buttons)): ?>
			<section class="fc-section" id="actions">
				<h4 class="page-header"><?php echo __('form.actions'); ?></h4>
				<?php foreach ($form_buttons as $button) : ?>
					<?php echo $button; ?>
				<?php endforeach; ?>
			</section>
		<?php endif; ?>
		<?php echo Form::close(); ?>
	</div>
	<div class="col-sm-3 docs-sidebar">
		<div class="bs-docs-sidebar" role="complementary" data-offset-top="0" data-offset-bottom="100">
			<ul class="nav bs-docs-sidenav" data-spy="affix">
				<?php if (count($tab_names) > 0): ?>
					<?php if (!empty($index_uri)): ?>
						<li class="back-to-list">
							<a href="<?php echo $index_uri; ?>"><?php echo __('common.back_to_list'); ?>
							</a>
						</li>
					<?php endif; ?>
					<?php foreach ($tab_names as $tab_no => $tab_name): ?>
						<li<?php
						if ($first_tab):
							echo ' class="active"';
							$first_tab = false;
						endif;
						?>>
							<a href="#group-<?php echo $tab_no; ?>"><?php echo __($tab_name); ?>
							</a>
						</li>
					<?php endforeach; ?>
					<?php if (is_array($form_buttons) && !empty($form_buttons)): ?>
						<li>
							<a href="#actions"><?php echo __('form.actions'); ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
<div class="clearfix"></div>
