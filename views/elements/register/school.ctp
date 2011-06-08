<fieldset class="grid_5">
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