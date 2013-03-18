<fieldset class="grid_5">
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