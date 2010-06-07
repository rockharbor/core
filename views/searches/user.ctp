<h2>Search</h2>

<div class="searches">
<?php echo $this->Form->create(null, array(
	'action' => 'user',
	'default' => false,
	'id' => 'SearchUserForm',
	'inputDefaults' => array(
		'empty' => true
	)
));?>
	<fieldset>
 		<legend>Search Users</legend>
		<?php
		echo $this->Form->input('Search.operator', array(
			'type' => 'select',
			'options' => array(
				'AND' => 'Match all',
				'OR' => 'Match any'
			),
			'empty' => false
		));
		?>
		<fieldset style="float:left;width:32%;">
			<legend>Basic Information</legend>
	<?php
		echo $this->Form->input('User.username');
		echo $this->Form->input('User.active', array(
			'type' => 'select',
			'options' => array(
				1 => 'Active',
				0 => 'Inactive'
			),
			'selected' => 1
		));
		echo $this->Form->input('User.flagged', array(
			'type' => 'select',
			'options' => array(
				1 => 'Flagged',
				0 => 'Unflagged'
			)
		));
		echo $this->Form->input('Profile.first_name');
		echo $this->Form->input('Profile.last_name');
		echo $this->Form->input('Profile.email');
		echo $this->Form->input('Publication', array(
			'multiple' => 'checkbox',
			'options' => $publications,
			'label' => 'Subscribed to (These will always match any.)',
			'empty' => false
		));
		echo $this->Form->input('Profile.classification_id');
		echo $this->Form->input('Profile.gender', array(
			'type' => 'select',
			'options' => $this->SelectOptions->genders
		));
		echo $this->Form->input('Profile.marital_status', array(
			'type' => 'select',
			'options' => $this->SelectOptions->maritalStatuses
		));
		echo $this->Form->input('Profile.qualified_leader', array(
			'type' => 'select',
			'options' => $this->SelectOptions->booleans
		));
	?>
		</fieldset>
		<fieldset style="float:left;width:30%;">
			<legend>Location</legend>
	<?php
		echo $this->Form->input('Address.city');
		echo $this->Form->input('Address.state', array(
			'options' => $this->SelectOptions->states
		));
		echo $this->Form->input('Address.zip');
		echo $this->Form->input('Address.Zipcode.region_id', array(
			'type' => 'select',
			'options' => $regions
		));		
		echo $this->Form->input('Distance.distance_from', array(
			'after' => '(try: orange, ca)'
		));
		echo $this->Form->input('Distance.distance', array(
			'type' => 'select',
			'options' => array(	
				'5' => '5 miles',
				'10' => '10 miles',
				'15' => '15 miles',
				'25' => '25 miles'
			),
			'empty' => false
		));
	?>
	<?php
		
	?>
		</fieldset>		
		<fieldset style="float:left;width:32%;">
			<legend>Age</legend>
		<?php
		echo $this->Form->input('Profile.age', array(
			'type' => 'select',
			'options' => array(
				$this->SelectOptions->ageGroups
			),
			'multiple' => 'checkbox',
			'empty' => false
		));
		echo '<br clear="all" />';
		echo $this->Form->input('Profile.child', array(
			'type' => 'checkbox',				
			'hiddenField' => false,
			'label' => 'Considered a child'
		));
		echo $this->Form->input('Profile.adult', array(
			'type' => 'checkbox',				
			'hiddenField' => false,
			'label' => 'Marked as an adult'
		));
		echo $this->Form->input('Profile.Birthday.start', array(
			'type' => 'date',
			'label' => 'Birthday Range',
			'minYear' => 1900
		));
		echo $this->Form->input('Profile.Birthday.end', array(
			'type' => 'date',
			'label' => false,
			'minYear' => 1900
		));
		?>
		</fieldset>
		<fieldset style="float:left;width:32%;">
			<legend>School</legend>
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
		<fieldset style="float:left;width:32%;">
			<legend>Involvement</legend>
		<?php
		echo $this->Form->input('Roster.Involvement.name', array(
			'label' => 'Involved in'
		));
		echo $this->Form->input('Roster.Involvement.Ministry.name', array(
			'label' => 'Involved in Ministry'
		));
		echo $this->Form->input('Profile.campus_id', array(
			'multiple' => 'checkbox',
			'empty' => false
		));
		?>
		</fieldset>
	</fieldset>
<?php
$submitOptions['update'] = '#user-results';

echo $this->Js->submit('Search!', $submitOptions);
echo $this->Form->end();
?>
</div>

<div id="user-results">
</div>