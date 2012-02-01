<h1>Forgot login info?</h1>
<p class="box">When we upgraded to the newest version of CORE, some usernames didn't take 
well to the new system. There is now a stricter policy on usernames, so if you
had a username with an @ symbol, for example, it might have been regenerated. Use
the form below to have the information emailed to you.</p>
<p>Try entering your username (if you remember), or one of the emails you have entered into <?php echo Core::read('general.site_name'); ?>.
<?php
echo $this->Form->create('User', array(
	'default' => false,
	'id' => 'UserUpdateForm'
));
echo $this->Form->input('forgotten', array(
	'label' => 'Lookup'
));
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();
?>