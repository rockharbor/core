<fieldset class="grid_5">
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