<h1>Register</h1>
<?php
echo $this->Form->create('User', array(
	'default' => false
));
?>
<div class="content-box clearfix">
	<p>Ready to get involved? Great! We need a little bit of information to get started.</p>
	<div class="grid_6 alpha">
	<?php
	echo $this->Form->hidden('User.group_id', array('value' => 8));
	echo $this->Form->hidden('User.active', array('value' => 1));
	echo $this->Form->input('User.username', array(
		'label' => 'Pick a username'
	));
	echo $this->Form->input('User.password', array(
		'label' => 'Pick a password'
	));
	echo $this->Form->input('User.confirm_password', array(
		'type' => 'password',
		'label' => 'Confirm your password'
	));
	echo $this->Form->input('Profile.primary_email', array(
		'label' => 'Email'
	));
	?>
	</div>
	<div class="grid_6 omega">
	<?php
	echo $this->Form->input('Profile.first_name');
	echo $this->Form->input('Profile.last_name');
	$whyInfo = 'A birth date is required so '.Core::read('general.site_name').' knows you&apos;re old enough to login.';
	$why = $this->Html->link('[why?]', '#', array(
		'class' => 'tooltip',
		'id' => 'why-birthdate'
	));
	$this->Js->buffer('CORE.tooltip($("#why-birthdate"), "'.$whyInfo.'");');
	echo $this->Form->input('Profile.birth_date', array(
		'empty' => true,
		'maxYear' => date('Y'),
		'minYear' => 1900,
		'label' => 'Birth Date '.$why
	));
	?>
	</div>
</div>
<div style="text-align:right">
<?php
$defaultSubmitOptions['id'] = uniqid('submit_button');
$defaultSubmitOptions['success'] = "CORE.successForm(event, data, textStatus, {
	autoUpdate: 'failure',
	success: function(event, data) {
		// TODO: this is a massive hack and I hate it but for the life of me
		// I can't think of a workaround
		if (/You have successfully registered/.test(data)) {
			redirect('/');
		} else {
			CORE.update(event.currentTarget, data);
		}
	}
});";
echo $this->Js->submit('Sign up', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>
