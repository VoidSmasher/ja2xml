<?php defined('SYSPATH') or die('No direct script access.');

/**
 * User: legion
 * Date: 25.06.12
 * Time: 11:36
 */
//$pages_in_line = 19;
?>
<div class="pagination">
	<div class="paginator" id="pager"></div>
		<script type="application/javascript">
			pag1 = new Paginator(
				'pager',
				<?php echo $total_pages; ?>,
				<?php echo $pages_in_line; ?>,
				<?php echo $current_page; ?>,
				'<?php echo $page->base_url(); ?>'
			);
		</script>
	<?php /*echo $limit;*/ ?>
</div>