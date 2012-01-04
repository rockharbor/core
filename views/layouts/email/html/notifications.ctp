<!DOCTYPE html>
<html>
	<body>
		<div style="
			font-family: 'Lucida Sans',Helvetica,Arial,sans-serif;
			font-size:11px;
			color: #838383;
			width: 440px;
		">
			<p>Hey <?php echo $toUser['Profile']['first_name']; ?>,</p>
			<?php echo $content_for_layout; ?>
			<p>-<?php echo Core::read('general.site_name'); ?></p>
			<p><img src="<?php echo Router::url('/', true).'img/logo-small.png'; ?>" /><br /><?php echo $this->Html->link(Router::url('/', true), Router::url('/', true)); ?></p>
			<small>Please don't respond to this email. If you need help, please contact <?php echo $this->Html->link(CORE::read('notifications.support_email'), 'mailto:'.Core::read('notifications.support_email')); ?>.</small>
		</div>
	</body>
</html>