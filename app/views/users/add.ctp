<h2>Add a User</h2>

<div id="profile_tabs" class="profiles" class="ui-tabs">


<ul class="tabs">
	<li class="tab"><a href="#personal">Personal Information</a></li> 
	<li class="tab"><a href="#contact">Contact Information</a></li> 
	<li class="tab"><a href="#alerts">Child Alerts &amp; Needs</a></li> 
	<li class="tab"><a href="#household">Household</a></li>
	<li class="tab"><a href="#subscriptions">Subscriptions</a></li>
</ul>



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
	<fieldset id="personal">
	<?php
		if (!$activeUser) {
			echo $this->Form->input('username', array(
				'after' => 'Leave blank if you want '.Core::read('general.site_name').' to pick one for you.'
			));
			echo $this->Form->input('password', array(
				'after' => 'Leave blank if you want '.Core::read('general.site_name').' to pick one for you.'
			));
			echo $this->Form->input('confirm_password', array(
				'type' => 'password',
				'after' => 'Leave blank if you want '.Core::read('general.site_name').' to pick one for you.'
			));
		}
		echo $this->Form->input('Profile.first_name');
		echo $this->Form->input('Profile.last_name');
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
		echo $this->Form->input('Profile.job_category_id');
		echo $this->Form->input('Profile.occupation');
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
	<fieldset id="alerts">
	<?php
		echo $this->Form->input('Profile.allergies');
		echo $this->Form->input('Profile.special_needs');
		echo $this->Form->input('Profile.special_alert');
	?>
	</fieldset>	
	<fieldset id="household">
	<div id="members">
	<?php
	if (empty($this->data)) {
		$this->data['HouseholdMember'] = array(array());
	}
	$hmcount = 0;
	
	foreach ($this->data['HouseholdMember'] as $householdMember):
	?>
	
		<fieldset id="member<?php echo $hmcount; ?>" >
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
	
	</fieldset>
	<fieldset id="subscriptions">
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
	</fieldset>	
	
<?php 
echo $this->Form->submit('Save');
echo $this->Form->end();
?>
	

</div>




<?php

/** tab js **/
$this->Js->buffer('CORE.tabs(\'profile_tabs\', {cookie: {expires:0}});');


$this->Html->scriptStart();
echo 'var member = '.$hmcount.';';
echo 'function addAdditionalMember() {
	$("#members").append(\'<fieldset id="member\'+member+\'"><div class="input text"><label for="HouseholdMember\'+member+\'ProfileFirstName">First Name</label>	<input type="text" id="HouseholdMember\'+member+\'ProfileFirstName" name="data[HouseholdMember][\'+member+\'][Profile][first_name]">	</div>	<div class="input text">	<label for="HouseholdMember\'+member+\'ProfileLastName">Last Name</label>	<input type="text" id="HouseholdMember\'+member+\'ProfileLastName" name="data[HouseholdMember][\'+member+\'][Profile][last_name]">	</div><div class="input text">	<label for="HouseholdMember\'+member+\'ProfilePrimaryEmail">Primary Email</label>	<input type="text" id="HouseholdMember\'+member+\'ProfilePrimaryEmail" name="data[HouseholdMember][\'+member+\'][Profile][primary_email]">	</div>	</fieldset>\');
	member++;
	
}';
echo $this->Html->scriptEnd();

?>