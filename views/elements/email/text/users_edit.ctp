New credentials

<?php if ($reset != 'both'): ?>
You changed your <?php echo $reset; ?> in <?php echo $CORE['settings']['site_name']; ?>. Your new <?php echo $reset; ?> is:
<?php echo ${$reset}; ?>
<?php else: ?>
You changed your username and password in <?php echo $CORE['settings']['site_name']; ?>. Your new credentials are:
Username: <?php echo $username; ?>
Password: <?php echo $password; ?>
<?php endif; ?>