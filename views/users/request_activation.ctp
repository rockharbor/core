<h1>Found you!</h1>
<p>I think you're already in <?php echo Core::read('general.site_name'); ?>! Use this form to send an activation request. When it's approved, you'll get an email with your new username and a temporary password.</p>
<?php
echo $this->Form->create('User', array(
	'url' => array($foundId),
	'default'=> true,
	'inputDefaults' => array(
		'maxYear' => date('Y'),
		'minYear' => 1900,
		'empty' => true
	)
));
?>
<div class="core-tabs-wizard" id="profile_tabs">
	<ul>
		<li><a href="#personal">Personal Information</a></li>
		<li><a href="#contact">Contact Information</a></li>
	</ul>
	<div id="personal">
	<?php
		echo $this->Form->input('username');
		echo $this->Form->input('Profile.first_name');
		echo $this->Form->input('Profile.last_name');
		echo $this->Form->input('Profile.birth_date');
	?>
	</div>
	<div id="contact" class="clearfix">
		<fieldset class="grid_6">
			<legend>Address</legend>
	<?php
		echo $this->Form->input('Address.0.address_line_1');
		echo $this->Form->input('Address.0.address_line_2');
		echo $this->Form->input('Address.0.city');
		echo $this->Form->input('Address.0.state', array(
			'options' => $this->SelectOptions->states
		));
		echo $this->Form->input('Address.0.zip');
	?>
		</fieldset>
		<fieldset class="grid_6">
			<legend>Phone and Email</legend>
	<?php
		echo $this->Form->input('Profile.cell_phone', array(
			'maxlength' => '30'
		));
		echo $this->Form->input('Profile.home_phone', array(
			'maxlength' => '30'
		));
		echo $this->Form->input('Profile.work_phone', array(
			'maxlength' => '30'
		));
		echo $this->Form->input('Profile.primary_email');
		echo $this->Form->input('Profile.alternate_email_1');
		echo $this->Form->input('Profile.alternate_email_2');
	?>
		</fieldset>
	</div>
</div>
<?php
$defaultSubmitOptions['id'] = uniqid('submit_button');
$defaultSubmitOptions['url'] = Router::url(array('controller' => 'users', 'action' => 'request_activation', $foundId));
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true});CORE.showFlash(data);';
echo $this->Form->button('Previous', array('id' => 'previous_button', 'class' => 'button', 'type' => 'button'));
echo $this->Form->button('Next', array('id' => 'next_button', 'class' => 'button', 'type' => 'button'));
echo $this->Js->submit('Request Activation', $defaultSubmitOptions);
echo $this->Form->end();

/** tab js **/
$this->Js->buffer('CORE.tabs("profile_tabs",
	{
		cookie:false
	},
	{
		next: "next_button",
		previous: "previous_button",
		submit: "'.$defaultSubmitOptions['id'].'",
		alwaysAllowSubmit: true
	}
);');
?>