<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.10.17
 * Time: 17:08
 */
?>
<!-- Javascript -->
<?php if (!empty($footer_scripts_embedded_before)): ?>
	<script type="application/javascript">
		<?php echo $footer_scripts_embedded_before; ?>
	</script>
<?php endif; ?>

<?php foreach ($footer_scripts as $script => $type):
	if (!empty($type)):
		echo HTML::script($script, array('type' => $type)) . "\n";
	else:
		echo HTML::script($script) . "\n";
	endif;
endforeach; ?>

<?php if (!empty($footer_scripts_embedded_after)): ?>
	<script type="application/javascript">
		<?php echo $footer_scripts_embedded_after; ?>
	</script>
<?php endif; ?>

<?php echo (Kohana::$environment == Kohana::DEVELOPMENT) ? $debug : ''; ?>
