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
		<li><a href="#about">About Me</a></li>
		<li><a href="#contact">Contact Information</a></li>
		<li><a href="#household">Household</a></li>
		<li><a href="#subscriptions">Subscriptions</a></li>
	</ul>

	<div id="personal" class="clearfix">
		<?php echo $this->element('register'.DS.'personal'); ?>
	</div>
	<div id="about" class="clearfix">
		<?php echo $this->element('register'.DS.'about'); ?>
		<?php echo $this->element('register'.DS.'school'); ?>
		<?php echo $this->element('register'.DS.'christian'); ?>
	</div>
	<div id="contact" class="clearfix">
		<?php echo $this->element('register'.DS.'address'); ?>
		<?php echo $this->element('register'.DS.'phone_email'); ?>
	</div>
	<div id="household" class="clearfix">
	<?php echo $this->element('register'.DS.'household'); ?>
	</div>
	<div id="subscriptions">
		<?php echo $this->element('register'.DS.'subscriptions'); ?>
	</div>
</div>
<?php
$defaultSubmitOptions['id'] = uniqid('submit_button');
echo $this->Form->button('Previous', array('id' => 'previous_button', 'class' => 'button', 'type' => 'button'));
echo $this->Form->button('Next', array('id' => 'next_button', 'class' => 'button', 'type' => 'button'));
echo $this->Js->submit('Sign up', $defaultSubmitOptions);
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

$this->Js->set('member', $hmcount);
$this->Js->set('element', $this->element('register'.DS.'household_member', array('count' => 'COUNT')));
$this->Html->scriptStart();
?>
function addAdditionalMember() {
	$("#members").append(window.core.element.replace(/COUNT/g, window.core.member));
	window.core.member++;

}
<?php
echo $this->Html->scriptEnd();
?>