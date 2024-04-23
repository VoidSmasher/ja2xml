<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: Andrey Verstov
 * Date: 20.06.14
 * Time: 11:18
 */
?>
<?php if (!empty($google_code)): ?>
<!-- Google.Analytics counter -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $google_code; ?>', '<?php echo Force_URL::get_current_host(); ?>');
  ga('send', 'pageview');
</script>
<!-- /Google.Analytics counter -->
<?php endif; ?>