<fieldset>
	<legend>Search Ministries</legend>
<?php
	echo $this->Form->input('Ministry.name');
	echo $this->Form->input('Ministry.active', array(
		'options' => array(
			'Inactive',
			'Active'
		),
		'empty' => true
	));
?>
</fieldset>