<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 19.07.13
 * Time: 9:31
 */
?>
<section id="<?php echo $section_id; ?>" data-offset-top="200">
<div class="page-header">
	<h1><?php echo $title; ?></h1>
</div>
<?php if (!empty($description)): ?>
<p><?php echo $description; ?></p>
<?php endif; ?>
</section>
