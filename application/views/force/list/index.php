<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 11.09.14
 * Time: 15:16
 */
?>
<div class="container-table">
	<div class="row">
		<div class="col-sm-6">
			<?php if (!empty($title)): ?>
				<p class="admin-row-header"><?php echo $title; ?></p>
			<?php endif; ?>
		</div>
		<div class="col-sm-6">
			<div class="pull-right">
				<?php echo $buttons_body; ?>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php if (!empty($notifications)): ?>
		<div class="row">
			<?php echo $notifications; ?>
		</div>
	<?php endif; ?>

	<?php if (!empty($pagination)) {
		echo $pagination;
	} ?>

	<div style="overflow-x: scroll">
		<table<?php echo HTML::attributes($attributes); ?>>
			<?php echo $header_body; ?>

			<?php echo $table_body; ?>
		</table>
	</div>

	<div class="table-empty-message jumbotron" style="margin-top: 20px;<?php if (!empty($table_body)) echo ' display:none'; ?>">
		<p><?php echo $message_list_empty; ?></p>
		<?php echo $buttons_body; ?>
	</div>

	<?php if (!empty($pagination)) {
		echo $pagination;
	} ?>
</div>
