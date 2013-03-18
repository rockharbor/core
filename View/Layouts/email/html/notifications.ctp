<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
	</head>
	<body class="wysihtml5-editor">
		<?php if ($include_greeting): ?>
		<p>Hey <?php echo ucfirst($toUser['Profile']['first_name']); ?>,</p>
		<?php endif; ?>
		<?php echo $content_for_layout; ?>
		<?php if ($include_signoff): ?>
		<p><img src="<?php echo Router::url('/', true).'img/logo-small.png'; ?>" /><br /><?php echo $this->Html->link(Router::url('/', true), Router::url('/', true)); ?></p>
		<?php endif; ?>
	</body>
</html>