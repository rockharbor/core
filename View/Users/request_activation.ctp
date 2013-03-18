<h1>Found you!</h1>
<p>I think you're already in <?php echo Core::read('general.site_name'); ?>! Use this form to send an activation request. When it's approved, you'll get an email with your new username.</p>
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
	<?php echo $this->element('register'.DS.'personal'); ?>
	</div>
	<div id="contact" class="clearfix">
	<?php
	echo $this->element('register'.DS.'address');
	echo $this->element('register'.DS.'phone_email');
	?>
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
