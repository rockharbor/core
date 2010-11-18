<?php

echo $this->Form->create(null, array(
	'default' => false,
	'url' => $this->passedArgs,
));
echo $this->Form->hidden('Role.ministry_id', array('value' => $ministry_id));
echo $this->Form->input('Role.name');
echo $this->Js->submit('Add', $defaultSubmitOptions);
echo $this->Form->end();

echo $this->Html->tag('p', 'Add to roles');
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
echo $this->Form->end();

?>