<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<body>
		<p>
			<strong style="border:1px solid #ff0000; padding: 5px">
				Note: The application is in debug mode, so the email to <?php echo $_originalUser['Profile']['name'].' <'.$_originalUser['Profile']['primary_email'].'>'; ?> was sent to you instead.
			</strong>
		</p>
		<?php echo $content_for_layout; ?>
	</body>
</html>