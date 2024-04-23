<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.10.17
 * Time: 17:05
 */
?>
<!-- Meta -->
<?php foreach ($header_meta as $attributes):
	if (!empty($attributes)) {
		echo '<meta' . HTML::attributes($attributes) . ' />';
	}
endforeach; ?>

<!-- Links -->
<?php foreach ($header_links as $attributes):
	if (!empty($attributes)) {
		echo '<link' . HTML::attributes($attributes) . ' />';
	}
endforeach; ?>

<!-- Styles -->
<?php foreach ($header_styles as $style => $media):
	if (!empty($media)):
		echo HTML::style($style, array('media' => $media)) . "\n";
	else:
		echo HTML::style($style) . "\n";
	endif;
endforeach; ?>

<!-- Vars -->
<?php if (!empty($header_js_vars)): ?>
	<script type="application/javascript">
		<?php foreach ($header_js_vars as $jsKey => $jsVar): ?>
		var <?php echo $jsKey . ' = ' . ((is_array($jsVar)) ? json_encode($jsVar) : "'".$jsVar."'"); ?>;
		<?php endforeach; ?>
	</script>
<?php endif; ?>

<!-- Javascript -->
<?php if (!empty($header_scripts_embedded_before)): ?>
	<script type="application/javascript">
		<?php echo $header_scripts_embedded_before; ?>
	</script>
<?php endif; ?>

<?php foreach ($header_scripts as $script => $type):
	if (!empty($type)):
		echo HTML::script($script, array('type' => $type)) . "\n";
	else:
		echo HTML::script($script) . "\n";
	endif;
endforeach; ?>

<?php if (!empty($header_scripts_embedded_after)): ?>
	<script type="application/javascript">
		<?php echo $header_scripts_embedded_after; ?>
	</script>
<?php endif; ?>
