<?php

echo $this->Form->create(array(
	'class' => 'core-filter-form update-roles'
));
echo $this->Form->hidden('id');
echo $this->Form->hidden('roster_status');
echo $this->Form->input('Role');
echo $this->Form->end('Save');

?>