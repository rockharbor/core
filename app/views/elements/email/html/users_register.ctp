<h1>Your <?php echo Core::read('site_name'); ?> Account Information</h1>
<p>Hi. I'm <?php echo Core::read('site_name'); ?>, <?php echo Core::read('church_name'); ?>'s online church management application. I have good news and bad news for you. The good news is, we're going to be friends! An account has been created for you in <?php echo Core::read('site_name'); ?>.</p>
<p>If you're thinking, "so what?" let me help you understand why this is so great. This is how I'll make being involved at <?php echo Core::read('church_name'); ?> so simple:</p>
<ul>
	<li>You can log in and view what ministries you're already a part of</li>
	<li>You can sign up for new teams and groups </li>
	<li>You can sign up and pay for events happening at <?php echo Core::read('church_name'); ?></li>
	<li>If you're a leader, you can easily manage and coordinate those you lead</li>
	<li>...and that's only the beginning!</li>
</ul>
<p>Below is your temporary account information to log into <?php echo Router::url('/'); ?>.  If you have any problems logging into your account, please contact me at <?php echo Core::read('support_email'); ?>.</p>
<p>Username: <?php echo $username; ?></p>
<p>Password: <?php echo $password; ?></p>
<p>Oh, but there is bad news...your day just peaked. Sorry about that. Can't wait to be friends anyway!</p>
<p><?php echo Core::read('site_name'); ?></p>
<p>P.S. Please do not respond to this email. If you need help, I can be reached at <?php echo Core::read('support_email'); ?></p>