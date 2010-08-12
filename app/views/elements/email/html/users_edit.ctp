<h1>New credentials</h1>
<?php if ($reset != 'both'): ?>
<p>You changed your <?php echo $reset; ?> in <?php echo Core::read('site_name'); ?>. Your new <?php echo $reset; ?> is:<br/><strong><?php echo ${$reset}; ?></strong></p>
<?php else: ?>
<p>You changed your username and password in <?php echo Core::read('site_name'); ?>. Your new credentials are:<br/>
Username: <strong><?php echo $username; ?></strong><br />
Password: <strong><?php echo $password; ?></strong>
</p>
<?php endif; ?>