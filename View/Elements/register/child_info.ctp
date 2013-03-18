<fieldset class="grid_6 alpha">
	<legend>Child Information</legend>
	<?php
	echo $this->Form->input('baby_dedication_date', array(
		'type' => 'date'
	));
	echo $this->Form->input('allergies', array(
		'label' => 'Child Allergies',
		'type' => 'textarea'
	));
	?>
</fieldset>
<fieldset class="grid_6 omega">
	<legend>Special Needs</legend>
	<?php
	echo $this->Form->input('special_needs', array(
		'label' => 'Child Special Needs',
		'type' => 'textarea'
	));
	echo $this->Form->input('special_alert', array(
		'label' => 'Child Special Alerts',
		'type' => 'textarea'
	));
	?>
</fieldset>