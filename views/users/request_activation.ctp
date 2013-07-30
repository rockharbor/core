<h1>Found you!</h1>
<p>
	It looks like you may already be in <?php echo Core::read('general.site_name'); ?>.
	Because of this, we need a little more information to confirm your identity.
	When <?php echo Core::read('general.site_name'); ?> Support approves your request,
	you&apos;ll get an email indicating that your account is ready to login.
</p>
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
		<fieldset class="grid_5 alpha">
                       <legend>About Me</legend>
		<?php
		echo $this->Form->input('Profile.first_name');
		echo $this->Form->input('Profile.last_name');
               echo $this->Form->input('Profile.birth_date');
		?>
		</fieldset>
		<fieldset class="grid_5 omega">
			<legend>User Info</legend>
		<?php
		echo $this->Form->input('User.username');
		echo $this->Form->input('User.password');
		echo $this->Form->input('User.confirm_password', array(
			'type' => 'password'
		));
		?>
		</fieldset>
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
