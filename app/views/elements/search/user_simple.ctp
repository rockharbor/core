<fieldset>
	<legend>Search Users</legend>
<?php
	echo $this->Form->input('username');
	echo $this->Form->input('Profile.first_name');
	echo $this->Form->input('Profile.last_name');
?>
</fieldset>