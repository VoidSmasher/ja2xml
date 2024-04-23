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
	<div class="col-sm-6">
		<h3><?php echo (!empty($title)) ? $page_title . ' - ' . $title : $page_title; ?></h3>
	</div>
	<div class="col-sm-6">
		<div class="pull-right">
			<a href="<?php echo $index_uri; ?>" class="btn btn-default"><?php echo __('common.back_to_list'); ?></a>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<hr>

<ul class="nav nav-tabs" id="form_tabs">
	<?php foreach ($tab_names as $tab_name): ?>
		<li<?php if ($first_tab): echo ' class="active"';
			$first_tab = false; endif; ?>><a href="#tab_<?php echo $tab_no;
			$tab_no++; ?>"><?php echo __($tab_name); ?></a></li>
	<?php endforeach; ?>
</ul>

<?php
$first_tab = true;
$tab_no = 0;
?>

<div class="tab-content">
	<?php foreach ($tabs as $fields) : ?>
		<div class="tab-pane<?php if ($first_tab): echo ' active';
			$first_tab = false; endif; ?>" id="tab_<?php echo $tab_no;
		$tab_no++; ?>">
			<?php
			if ($fields instanceof View):
				echo $fields; else:
				foreach ($fields as $name => $field):
					if (array_key_exists('name', $field)):
						$name = $field['name'];
					endif;
					?>
					<div class="control-group">
						<?php if (array_key_exists('label', $field) && !empty($field['label'])): ?>
							<?php echo Form::label($name, $field['label'], array('class' => 'control-label')); ?>
						<?php endif; ?>
						<div class="controls">
							<?php echo $field['control']; ?>
							<?php
							$description = array_key_exists('description', $field) ? $field['description'] : false;
							?>
							<?php if ($description): ?>
								<p>
									<small><?php echo $description; ?></small>
								</p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
