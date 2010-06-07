<h2>Found you!</h2>

<p>
I think you're already in <?php echo $CORE['settings']['site_name']; ?>! Use this form to send an activation request. When it's approved, you'll get an email with your new username and a temporary password.
</p>

<div id="profile_tabs" class="profiles" class="ui-tabs">
<ul class="tabs">
	<li class="tab"><a href="#personal">Personal Information</a></li> 
	<li class="tab"><a href="#contact">Contact Information</a></li>
</ul>



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
	<fieldset id="personal">
	<?php
		echo $this->Form->input('username');
		echo $this->Form->input('Profile.first_name');
		echo $this->Form->input('Profile.last_name');
		echo $this->Form->input('Profile.birth_date');
	?>
	</fieldset>
	<fieldset id="contact">
		<fieldset>
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
<?php 
echo $this->Form->submit('Send Activation Request');
echo $this->Form->end();
?>
	

</div>




<?php

/** tab js **/
$this->Js->buffer('CORE.tabs(\'profile_tabs\', {cookie: {expires:0}});');


?>