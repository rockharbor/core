<h1>View Email</h1>
<div class="content-box">
	<dl>
		<dt>From:</dt>
		<?php if ($email['SysEmail']['from_id'] > 0): ?>
		<dd><?php echo $this->Html->link($email['FromUser']['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $email['FromUser']['id'])); ?></dd>
		<?php else: ?>
		<dd><?php echo Core::read('general.site_name'); ?></dd>
		<?php endif; ?>
		<dt>Subject:</dt>
		<dd><?php echo $email['SysEmail']['subject']; ?></dd>
	</dl>
	<iframe width="100%" height="100%" src="<?php echo Router::url(array('controller' => 'sys_emails', 'action' => 'html_email', $email['SysEmail']['id'], 'User' => $email['FromUser']['id'])); ?>"></iframe>
</div>