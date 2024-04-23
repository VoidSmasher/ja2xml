<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 13.08.12
 * Time: 17:15
 */
$items_per_page_variants = Helper_Pagination::get_items_per_page_variants_for_admin();
?>
<div class="btn-group pagination-skins">
	<button class="btn dropdown-toggle" data-toggle="dropdown"<?php echo Helper_Popover::get_as_string(__('pagination.items_per_page'));?>><?php echo $items_per_page; ?> <span class="caret"></span></button>
	<ul class="dropdown-menu">
<?php foreach ($items_per_page_variants as $variant): ?>
		<li<?php echo ($variant == $items_per_page) ? ' class="active"' : ''; ?>><a href="<?php echo Helper_Pagination::get_admin_setup_url($variant); ?>"><?php echo $variant; ?></a></li>
<?php endforeach; ?>
	</ul>
</div>
<div class="clearfix"></div>
