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
		<li><a href="#add-personal">Personal Information</a></li>
		<li><a href="#add-about">About Me</a></li>
		<li id="childinfo-tab"><a href="#add-childinfo">Child Information</a></li>
		<li><a href="#add-contact">Contact Information</a></li>
		<li><a href="#add-household">Household</a></li>
		<li><a href="#add-subscriptions">Subscriptions</a></li>
		<li><a href="#add-admin">Admin</a></li>
	</ul>

	<div id="add-personal" class="clearfix">
		<?php echo $this->element('register'.DS.'personal'); ?>
	</div>
	<div id="add-about" class="clearfix">
		<?php echo $this->element('register'.DS.'about'); ?>
		<?php echo $this->element('register'.DS.'school'); ?>
		<?php echo $this->element('register'.DS.'christian'); ?>
	</div>
	<div id="add-childinfo">
		<?php echo $this->element('register'.DS.'child_info'); ?>
	</div>
	<div id="add-contact" class="clearfix">
		<?php echo $this->element('register'.DS.'address'); ?>
		<?php echo $this->element('register'.DS.'phone_email'); ?>
	</div>
	<div id="add-household" class="clearfix">
	<?php echo $this->element('register'.DS.'household'); ?>
	</div>
	<div id="add-subscriptions">
		<?php echo $this->element('register'.DS.'subscriptions'); ?>
	</div>
	<div id="add-admin">
		<?php echo $this->element('register'.DS.'admin'); ?>
	</div>
</div>
<?php
$defaultSubmitOptions['id'] = uniqid('submit_button');
echo $this->Form->button('Previous', array('id' => 'previous_button', 'class' => 'button', 'type' => 'button'));
echo $this->Form->button('Next', array('id' => 'next_button', 'class' => 'button', 'type' => 'button'));
echo $this->Js->submit('Sign up', $defaultSubmitOptions);
echo $this->Form->end();