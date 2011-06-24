<?php
echo $this->Html->script('misc/user');
$this->Js->buffer('CORE_user.init("profile_tabs");');
?>
<h1>Register</h1>
<?php
echo $this->Form->create('User', array(
	'default'=> true,
	'inputDefaults' => array(
		'maxYear' => date('Y'),
		'minYear' => 1900,
		'empty' => true
	)
));
?>
<div id="profile_tabs" class="core-tabs-wizard">
	<ul>
		<li><a href="#personal">Personal Information</a></li>
		<li><a href="#contact">Contact Information</a></li>
		<li><a href="#household">Household</a></li>
	</ul>

	<div id="personal" class="clearfix">
		<?php echo $this->element('register'.DS.'personal'); ?>
	</div>
	<div id="contact" class="clearfix">
		<?php echo $this->element('register'.DS.'address'); ?>
		<?php echo $this->element('register'.DS.'phone_email', array('short' => true)); ?>
	</div>
	<div id="household" class="clearfix">
		<?php echo $this->element('register'.DS.'household'); ?>
	</div>
</div>
<?php
$defaultSubmitOptions['id'] = uniqid('submit_button');
echo $this->Form->button('Previous', array('id' => 'previous_button', 'class' => 'button', 'type' => 'button'));
echo $this->Form->button('Next', array('id' => 'next_button', 'class' => 'button', 'type' => 'button'));
echo $this->Js->submit('Sign up', $defaultSubmitOptions);
echo $this->Form->end();