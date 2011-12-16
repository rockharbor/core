<h1>Forgot password</h1>
<p>Try entering your username, or one of the emails you have entered into <?php echo Core::read('general.site_name'); ?>.
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