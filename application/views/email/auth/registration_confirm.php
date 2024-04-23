<?php defined('SYSPATH') or die('No direct script access.');

?>
<p><?php echo __('mail.auth.greetings', [':name' => $user_name]); ?></p>
<br/>
<p><?php echo __('mail.auth.registration_confirm.1', [':host' => Force_Config::get_domain()]); ?></p>
<p><?php echo __('mail.auth.registration_confirm.2'); ?></p>
<p><a href="<?php echo $confirm_url; ?>" target="_blank" rel="nofollow"><?php echo $confirm_url; ?></a></p>
<br/>
<p><?php echo __('mail.auth.subscription', [':name' => Force_Config::get_site_name()]); ?></p>