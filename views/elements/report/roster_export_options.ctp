<?php
// at the very least include username in the report
echo $this->Form->hidden('Export.User.username');

echo $this->Form->input('Export.User.Profile.name', array(
	'type' => 'checkbox'
));
echo $this->Form->input('Export.User.Profile.first_name', array(
	'type' => 'checkbox'
));
echo $this->Form->input('Export.User.Profile.last_name', array(
	'type' => 'checkbox'
));
?>