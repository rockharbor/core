<h2>Forgot password</h2>
<p>Try entering your username, or one of the emails you have entered into <?php echo $CORE['settings']['site_name']; ?>.
<?php
echo $this->Form->create('User', array(
	'default' => false,
	'id' => 'UserUpdateForm'
));
?>
<fieldset>
	<legend>
	<?php
	echo $this->Form->input('forgotten', array(
		'label' => 'Lookup'
	));
	?>
</fieldset>
<?php
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();
?>