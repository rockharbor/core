<div>
<?php
echo $this->Form->create('Roster', array(
	'class' => 'core-filter-form',
	'url' => $this->passedArgs,
));
?>
	<fieldset>
		<legend>Add to roles</legend>
	<?php
	echo $this->Form->hidden('id');
	echo $this->Form->input('Role', array(
		'div' => array(
			'tag' => 'span',
			'class' => 'toggle'
		),
		'multiple' => 'checkbox',
		'label' => false
	));
	echo $this->Js->submit('Save');
	?>
	</fieldset>
<?php
echo $this->Form->end();
?>
</div>