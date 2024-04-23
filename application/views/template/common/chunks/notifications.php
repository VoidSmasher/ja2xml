<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: A.Stifanenkov
 * Date: 13.02.13
 * Time: 17:56
 */
?>
<div class="alert alert-success">
<?php
foreach ($notifications as $key => $text):
	$text = Helper_Notify::parse_notify($text, $key);
	if (!$text) continue;
?>
<p><?php echo $text; ?></p>
<?php endforeach; ?>
</div>
