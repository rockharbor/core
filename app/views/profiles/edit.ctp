<span class="breadcrumb editing"><?php
$icon = $this->element('icon', array('icon' => 'delete'));
echo $this->Html->link($icon, array('action' => 'view'), array('escape' => false, 'class' => 'no-hover'));
?>Editing<?php echo $this->Html->image('../assets/images/edit-flag-right.png'); ?></span>
<h1><?php echo $this->data['Profile']['first_name'].' '.$this->data['Profile']['last_name']; ?></h1>

<div class="profiles core-tabs">
<?php
echo $this->Form->create(array(
	'url' => $this->passedArgs,
	'inputDefaults' => array(
		'empty' => true
	)
));
?>
	<ul>
		<li><a href="#personal-information">Personal</a></li>
		<li><a href="#contact-information">Contact</a></li>
		<li><a href="#child-information">Child and School</a></li>
		<li><?php echo $this->Html->link('Subscriptions', array('controller' => 'publications', 'action' => 'subscriptions', 'User' => $this->data['Profile']['user_id']), array('title' => 'subscriptions')); ?></li>
	</ul>

	<div class="content-box clearfix">
		<div id="personal-information">
			<fieldset class="grid_5 alpha">
				<legend>Personal Info</legend>
				<?php
				echo $this->Form->input('id');
				echo $this->Form->input('first_name');
				echo $this->Form->input('last_name');
				echo $this->Form->input('gender', array(
					'type' => 'select',
					'options' => $this->SelectOptions->genders
				));
				echo $this->Form->input('marital_status', array(
					'type' => 'select',
					'options' => $this->SelectOptions->maritalStatuses
				));
				echo $this->Form->input('birth_date', array(
					'minYear' => 1900,
					'maxYear' => date('Y')
				));
				?>
			</fieldset>
			<fieldset class="grid_5 omega">
				<legend>About Me</legend>
				<?php
				echo $this->Form->input('job_category_id');
				echo $this->Form->input('occupation');
				echo $this->Form->input('campus_id', array(
					'label' => 'What campus do you most often attend?'
				));
				echo $this->Form->input('classification_id');
				echo $this->Form->input('accepted_christ');
				echo $this->Form->input('accepted_christ_year', array(
					'type' => 'select',
					'options' => $this->SelectOptions->generateOptions('year', array(
						'min' => 1900,
						'max' => date('Y')
					))
				));
				echo $this->Form->input('baptism_date', array(
					'type' => 'date'
				));
				?>
			</fieldset>
			<div style="clear:both"><?php echo $this->Js->submit('Save', $defaultSubmitOptions); ?></div>
		</div>
		<div id="contact-information">
			<fieldset class="grid_5 alpha">
				<legend>Phone Numbers</legend>
				<?php
				echo $this->Form->input('Profile.cell_phone', array(
					'maxlength' => '30',
					'value' => $this->Formatting->phone($this->data['Profile']['cell_phone'])
				));
				echo $this->Form->input('Profile.home_phone', array(
					'maxlength' => '30',
					'value' => $this->Formatting->phone($this->data['Profile']['home_phone'])
				));
				echo $this->Form->input('Profile.work_phone', array(
					'maxlength' => '30',
					'value' => $this->Formatting->phone($this->data['Profile']['work_phone'])
				));

				?>
			</fieldset>
			<fieldset class="grid_5 omega">
				<legend>Email Addresses</legend>
				<?php
				echo $this->Form->input('Profile.primary_email');
				echo $this->Form->input('Profile.alternate_email_1');
				echo $this->Form->input('Profile.alternate_email_2');
				?>
			</fieldset>
			<div style="clear:both"><?php echo $this->Js->submit('Save', $defaultSubmitOptions); ?></div>
		</div>
		<div id="child-information">
			<fieldset class="grid_5 alpha">
				<legend>Child Information</legend>
				<?php
				echo $this->Form->input('baby_dedication_date', array(
					'type' => 'date'
				));
				echo $this->Form->input('special_needs', array(
					'label' => 'Child Special Needs',
					'type' => 'textarea'
				));
				echo $this->Form->input('special_alert', array(
					'label' => 'Child Special Alerts',
					'type' => 'textarea'
				));
				echo $this->Form->input('allergies', array(
					'label' => 'Child Allergies',
					'type' => 'textarea'
				));
				?>
			</fieldset>
			<fieldset class="grid_5 omega">
				<legend>School Information</legend>
				<?php
				echo $this->Form->input('grade', array(
					'type' => 'select',
					'options' => $this->SelectOptions->grades
				));
				echo $this->Form->input('graduation_year', array(
					'type' => 'select',
					'options' => $this->SelectOptions->generateOptions('year', array(
						'min' => 1900,
						'max' => date('Y') + 20
					))
				));
				echo $this->Form->input('elementary_school_id');
				echo $this->Form->input('middle_school_id');
				echo $this->Form->input('high_school_id');
				echo $this->Form->input('college_id');
				?>
			</fieldset>
			<div style="clear:both"><?php echo $this->Js->submit('Save', $defaultSubmitOptions); ?></div>
		</div>
		<div id="subscriptions">
			<?php
			echo $this->requestAction('/publications/subscriptions', array(
				'named' => array(
					'User' => $this->data['Profile']['user_id']
				),
				'return',
				'respondAs' => 'ajax'
			));
			?>
		</div>
	</div>
<?php
echo $this->Form->end();
?>
</div>