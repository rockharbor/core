<?php

echo $this->Form->create(null, array(
	'default' => false,
	'url' => $this->passedArgs,
));
?>
<fieldset>
	<legend>Add a new role to <?php echo $ministry['Ministry']['name']; ?></legend>
<?php
echo $this->Form->hidden('Role.ministry_id', array('value' => $ministry['Ministry']['id']));
echo $this->Form->input('Role.name');
echo $this->Js->submit('Add', $defaultSubmitOptions);
?>
</fieldset>
<?php
echo $this->Form->end();
?>
<fieldset>
	<legend>Add to roles</legend>
<?php
echo $this->Form->create('Roster', array(
	'class' => 'core-filter-form',
	'url' => $this->passedArgs,
));
echo $this->Form->hidden('id');
echo $this->Form->hidden('roster_status');
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