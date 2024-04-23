<?php defined('SYSPATH') or die('No direct script access.');

?>
<p><?php echo __('mail.auth.greetings', [':name' => $user_name]); ?></p>
<br />
<p><?php echo __('mail.auth.remind.1', [':host' => Force_Config::get_domain()]); ?></p>
<p><?php echo __('mail.auth.remind.2'); ?></p>
<br />
<p><?php echo __('mail.auth.remind.3'); ?></p>
<p><a href="<?php echo $reset_url; ?>" target="_blank" rel="nofollow"><?php echo $reset_url; ?></a></p>
<br />
<p><?php echo __('mail.auth.subscription', [':name' => Force_Config::get_site_name()]); ?></p>