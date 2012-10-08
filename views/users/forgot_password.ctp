<h1>Trouble logging in?</h1>
<?php
if (!isset($password)) {
?>
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
} else {
	$msg = '';
	if (isset($found)) {
		$msg = 'We found you in '.Core::read('general.site_name').' and your ';
		$msg .= 'login info will be sent to you shortly!';
	} else {
		$msg = 'Your new password will be sent to you shortly!';
	}
	echo $this->Html->tag('p', $msg);
}