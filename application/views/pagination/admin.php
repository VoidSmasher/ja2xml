<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 25.06.12
 * Time: 11:36
 */
$start = $current_page - floor($pages_in_line / 2);
if ($start < 1) {
	$start = 1;
}
$finish = $start + ($pages_in_line - 1);
if ($finish > $total_pages) {
	$finish = $total_pages;
	$start = $finish - ($pages_in_line - 1);
	if ($start < 1) {
		$start = 1;
	}
}
?>
<div class="pagination-block">
	<div class="pull-left">
		<ul class="pagination">
			<?php if ($display_first_page): ?>
				<?php if ($first_page !== FALSE): ?>
					<li>
						<a href="<?php echo HTML::chars($page->url($first_page)); ?>"><?php echo __('pagination.first'); ?></a>
					</li>
				<?php else: ?>
					<li class="disabled"><a href="#"><?php echo __('pagination.first'); ?></a></li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($display_previous_page): ?>
				<?php if ($previous_page !== FALSE): ?>
					<li>
						<a href="<?php echo HTML::chars($page->url($previous_page)); ?>"><?php echo __('pagination.previous'); ?></a>
					</li>
				<?php else: ?>
					<li class="disabled"><a href="#"><?php echo __('pagination.previous'); ?></a></li>
				<?php endif; ?>
			<?php endif; ?>

			<?php for ($i = $start; $i <= $finish; $i++): ?>
				<li<?php if ($i == $current_page): ?> class="active"<?php endif ?>>
					<a href="<?php echo HTML::chars($page->url($i)); ?>"><?php echo $i; ?></a>
				</li>
			<?php endfor; ?>

			<?php if ($display_next_page): ?>
				<?php if ($next_page !== FALSE): ?>
					<li>
						<a href="<?php echo HTML::chars($page->url($next_page)); ?>"><?php echo __('pagination.next'); ?></a>
					</li>
				<?php else: ?>
					<li class="disabled"><a href="#"><?php echo __('pagination.next'); ?></a></li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($display_last_page): ?>
				<?php if ($last_page !== FALSE): ?>
					<li>
						<a href="<?php echo HTML::chars($page->url($last_page)); ?>"><?php echo __('pagination.last'); ?></a>
					</li>
				<?php else: ?>
					<li class="disabled"><a href="#"><?php echo __('pagination.last'); ?></a></li>
				<?php endif; ?>
			<?php endif; ?>
		</ul>
	</div>
	<?php if ($display_items_per_page_selector): ?>
		<?php echo Helper_Pagination::get_items_per_page_select_box_for_admin(); ?>
	<?php endif; ?>
	<div class="clearfix"></div>
</div>
