<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 11.05.16
 * Time: 20:35
 */
?>
<!DOCTYPE html>
<html lang="<?php echo i18n::lang(); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<meta name="viewport" class="viewport" content="width=device-width, user-scalable=no">
	<meta name="description" content="<?php echo $description; ?>">
	<meta name="keywords" content='<?php echo $keywords; ?>' />
	<meta name="pinterest" content="nopin" />
	<meta name="format-detection" content="telephone=no" />

	<?php echo $assets_header; ?>
</head>

<body>
<?php /*
<div id="LZ" class="LZ"></div>
<script>(function(t){var r=new XMLHttpRequest();r.open('GET','/assets/common/svg/sprite.svg',true);r.setRequestHeader('X-Requested-With','XMLHttpRequest');r.onreadystatechange=function(){if(r.readyState !== 4) return void 0;var lz = document.getElementById('LZ');if(!lz){document.createElement('DIV');lz.id='LZ';lz.className='LZ';t.appendChild(lz);};var d=document.createElement('DIV');d.innerHTML=r.responseText;lz.appendChild(d);};r.send();})(document.body);</script>
 */ ?>
<?php echo $counter_top; ?>
<?php echo $before_header; ?>
<?php echo $header; ?>
<?php echo $after_header; ?>

<?php echo $content; ?>

<?php echo $before_footer; ?>
<?php echo $footer; ?>
<?php echo $after_footer; ?>

<?php echo $modal; ?>

<?php echo $assets_footer; ?>
<?php echo $counter_bottom; ?>
</body>
</html>
