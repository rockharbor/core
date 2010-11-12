<h1>Edit Profile</h1>

<div class="profiles form ui-tabs core-tabs">

<ul>
	<li><a href="#one">Profile</a></li>
	<li><a href="#two">Information</a></li> 
	<li><a href="#three">Child Alerts &amp; Needs</a></li> 
	<li><a href="<?php echo Router::url(array(
		'controller' => 'publications',
		'action' => 'subscriptions',
		'User' => $this->data['User']['id']
		)); ?>" title="subscriptions">Subscriptions</a></li> 
	<li><a href="<?php echo Router::url(array(
		'controller' => 'user_addresses',
		'User' => $this->data['User']['id']
		)); ?>" title="addresses">Address</a></li> 

	<li class="admin"><a href="#admin">Administration</a></li>
	<li class="admin"><a href="<?php
	echo Router::url(array(
		'controller' => 'user_documents',
		'User' => $this->data['User']['id']
	));
	?>" title="docs">Documents</a></li>
</ul>

<div class="content-box">
<?php 
echo $this->Form->create('User', array(
	'default'=> false,
	'inputDefaults' => array(
		'maxYear' => date('Y'),
		'minYear' => 1900,
		'empty' => true
	)
));
?>
	<fieldset id="one">
	<?php 
	
		echo $this->Form->input('id');
		echo $this->Form->input('Profile.id');
		echo $this->Html->tag('div', $this->Formatting->flags('User', $user).$user['User']['username'], array(
			'escape'=>false, 
			'class'=>'input'
		));
		echo $this->Html->link('Change username or password', array('action' => 'edit', 'User' => $this->data['User']['id']), array('rel' => 'modal-none'));
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
		
		
	?>
		<fieldset>
			<legend>Preferences</legend>
			<?php
			echo $this->Form->input('Profile.email_on_notification', array(
				'label' => 'Email me when I get notifications'
			));	
			echo $this->Form->input('Profile.allow_sponsorage', array(
				'label' => 'Allow others to pay for me for sponsorable events'
			));	
			echo $this->Form->input('Profile.household_contact_signups', array(
				'label' => 'Allow household contacts to sign me up for events'
			));				
			?>
		</fieldset>
	</fieldset>
	<fieldset id="two">
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
	<fieldset id="three">
	<?php
		echo $this->Form->input('Profile.allergies');
		echo $this->Form->input('Profile.special_needs');
		echo $this->Form->input('Profile.special_alert');
	?>
	</fieldset>
	<fieldset id="subscriptions"></fieldset>
	<?php
		// register this fieldset as 'updateable'
		$this->Js->buffer('CORE.register("subscriptions", "subscriptions", "'.Router::url(array(
		'controller' => 'publications',
		'action' => 'subscriptions',
		'User' => $this->data['User']['id']
		)).'")');		
	?>
	<fieldset id="addresses"></fieldset>
	<?php
		// register this fieldset as 'updateable'
		$this->Js->buffer('CORE.register(\'addresses\', \'addresses\', \''.Router::url(array(
		'controller' => 'user_addresses',
		'User' => $this->data['User']['id']
		)).'\')');		
	?>
	
	<fieldset id="admin">
	<?php
		echo $this->Form->input('flagged', array(
			'label' => 'Flag this account'
		));
		
		echo $this->Form->input('group_id', array(
			'empty' => false,
			
		));
		
		echo $this->Form->input('Profile.adult');
		echo $this->Form->input('Profile.cpr_certified_date', array(
			'type' => 'date'
		));
		echo $this->Form->input('Profile.baby_dedication_date', array(
			'type' => 'date'
		));
		echo $this->Form->input('Profile.qualified_leader');
		echo $this->Form->input('Profile.background_check_complete');
		echo $this->Form->input('Profile.background_check_by');
		echo $this->Form->input('Profile.background_check_date', array(
			'type' => 'date'
		));
		echo $this->Form->input('Profile.created_by');
		echo $this->Form->input('Profile.created_by_type');
	?>
	</fieldset>
	<fieldset id="docs">
		<?php
		// register this fieldset as 'updateable'
		$this->Js->buffer('CORE.register(\'DocumentAttachments\', \'docs\', \''.Router::url(array(
			'controller' => 'user_documents',
			'User' => $this->data['User']['id']
	)).'\')');
		?>
	</fieldset>
<?php 
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
?>
	
</div>
</div>
