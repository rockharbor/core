<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<body>
		<?php echo $content_for_layout; ?>
		<p><?php echo Core::read('general.site_name'); ?></p>
		<small>Please don't respond to this email. If you need help, please contact <?php echo $this->Html->link(CORE::read('notifications.support_email'), 'mailto:'.Core::read('notifications.support_email')); ?>.</small>
	</body>
</html>