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
		<li><a href="#alerts">Child Alerts &amp; Needs</a></li>
		<li><a href="#household">Household</a></li>
		<li><a href="#subscriptions">Subscriptions</a></li>
	</ul>

	<div id="personal" class="clearfix">
		<fieldset class="grid_6">
			<legend>User Info</legend>
	<?php
		if (!$activeUser) {
			echo $this->Form->input('username', array(
				'after' => 'Leave blank if you want '.Core::read('general.site_name').' to pick one for you based on your name.'
			));
			echo $this->Form->input('password', array(
				'after' => 'Leave blank if you want '.Core::read('general.site_name').' to pick one for you.'
			));
			echo $this->Form->input('confirm_password', array(
				'type' => 'password',
				'after' => 'Leave blank if you want '.Core::read('general.site_name').' to pick one for you.'
			));
		} else {
			echo $this->Form->hidden('username');
			echo $this->Form->hidden('password');
			echo $this->Form->hidden('confirm_password');
		}
		echo $this->Form->input('Profile.first_name');
		echo $this->Form->input('Profile.last_name');
	?>
		</fieldset>
		<fieldset class="grid_6">
			<legend>About Me</legend>
	<?php
		echo $this->Form->input('Profile.gender', array(
			'type' => 'select',
			'options' => $this->SelectOptions->genders
		));
		echo $this->Form->input('Profile.birth_date');
		echo $this->Form->input('Profile.campus_id', array(
			'empty' => true
		));
		echo $this->Form->input('Profile.classification_id');
		echo $this->Form->input('Profile.marital_status', array(
			'type' => 'select',
			'options' => $this->SelectOptions->maritalStatuses
		));
		echo $this->Form->input('Profile.job_category_id');
		echo $this->Form->input('Profile.occupation');
	?>
		</fieldset>
		<fieldset class="grid_6">
			<legend>School Stuff</legend>
	<?php
		echo $this->Form->input('Profile.grade', array(
			'type' => 'select',
			'options' => $this->SelectOptions->grades
		));
		echo $this->Form->input('Profile.graduation_year', array(
			'type' => 'select',
			'options' => $this->SelectOptions->generateOptions('year', array(
				'min' => 1900,
				'max' => date('Y') + 20
			))
		));
		echo $this->Form->input('Profile.elementary_school_id');
		echo $this->Form->input('Profile.middle_school_id');
		echo $this->Form->input('Profile.high_school_id');
		echo $this->Form->input('Profile.college_id');
	?>
		</fieldset>
		<fieldset class="grid_6">
	<?php
		echo $this->Form->input('Profile.accepted_christ');
		echo $this->Form->input('Profile.accepted_christ_year', array(
			'type' => 'select',
			'options' => $this->SelectOptions->generateOptions('year', array(
				'min' => 1900,
				'max' => date('Y')
			))
		));
		echo $this->Form->input('Profile.baptism_date', array(
			'type' => 'date'
		));
		echo $this->Form->input('Profile.baby_dedication_date', array(
			'type' => 'date'
		));
	?>
		</fieldset>
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
	<div id="alerts">
	<?php
		echo $this->Form->input('Profile.allergies');
		echo $this->Form->input('Profile.special_needs');
		echo $this->Form->input('Profile.special_alert');
	?>
	</div>
	<div id="household" class="clearfix">
	<div id="members">
	<?php
	if (empty($this->data)) {
		$this->data['HouseholdMember'] = array(array());
	}
	$hmcount = 0;
	
	foreach ($this->data['HouseholdMember'] as $householdMember):
	?>
	
		<fieldset class="grid_5" id="member<?php echo $hmcount; ?>">
	<?php
		if (isset($householdMember['Profile']['id'])) {
			echo $householdMember['Profile']['first_name'].' already exists in '.Core::read('general.site_name').'. ';
			echo $householdMember['Profile']['gender'] == 'm' ? 'He\'ll' : 'She\'ll';
			if ($householdMember['Profile']['child']) {
				echo ' be added to your household.';
			} else {
				echo ' be invited to your household.';
			}
			echo $this->Form->hidden('HouseholdMember.'.$hmcount.'.Profile.first_name');
			echo $this->Form->hidden('HouseholdMember.'.$hmcount.'.Profile.last_name');
			echo $this->Form->hidden('HouseholdMember.'.$hmcount.'.Profile.primary_email');
		} else {
			echo $this->Form->input('HouseholdMember.'.$hmcount.'.Profile.first_name');
			echo $this->Form->input('HouseholdMember.'.$hmcount.'.Profile.last_name');
			echo $this->Form->input('HouseholdMember.'.$hmcount.'.Profile.primary_email');
		}
	?>
		</fieldset>
	<?php
		$hmcount++;
	endforeach;
	?>
	</div>
	<?php
		echo $this->Html->link('Add additional member', 'javascript:;', array(
			'onclick' => 'addAdditionalMember()',
			'class' => 'button'
		));
			
	?>
	</div>
	<div id="subscriptions">
		<p>Select any publications you would like to subscribe to below.</p>
		<fieldset>
			<legend>Subscriptions</legend>
	<?php
		echo $this->Form->input('Publication', array(
			'multiple' => 'checkbox',
			'empty' => false,
			'label' => false
		));
	?>
		</fieldset>
	</div>
</div>
<?php
$defaultSubmitOptions['id'] = 'submit_button';
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
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
		submit: "submit_button",
		alwaysAllowSubmit: true
	}
);');


$this->Html->scriptStart();
echo 'var member = '.$hmcount.';';
echo 'function addAdditionalMember() {
	$("#members").append(\'<fieldset id="member\'+member+\'"><div class="input text"><label for="HouseholdMember\'+member+\'ProfileFirstName">First Name</label>	<input type="text" id="HouseholdMember\'+member+\'ProfileFirstName" name="data[HouseholdMember][\'+member+\'][Profile][first_name]">	</div>	<div class="input text">	<label for="HouseholdMember\'+member+\'ProfileLastName">Last Name</label>	<input type="text" id="HouseholdMember\'+member+\'ProfileLastName" name="data[HouseholdMember][\'+member+\'][Profile][last_name]">	</div><div class="input text">	<label for="HouseholdMember\'+member+\'ProfilePrimaryEmail">Primary Email</label>	<input type="text" id="HouseholdMember\'+member+\'ProfilePrimaryEmail" name="data[HouseholdMember][\'+member+\'][Profile][primary_email]">	</div>	</fieldset>\');
	member++;

}';
echo $this->Html->scriptEnd();

?>