<fieldset>
	<legend>Search Involvement Opportunities</legend>
<?php
	echo $this->Form->input('Involvement.name');
	echo $this->Form->input('Involvement.active', array(
		'options' => array(
			'Inactive',
			'Active'
		),
		'empty' => true
	));
?>
</fieldset>