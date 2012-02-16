<fieldset class="grid_5 alpha">
	<legend>Background</legend>
	<?php
	echo $this->Form->input('User.flagged');
	echo $this->Form->input('Profile.cpr_certified_date');
	echo $this->Form->input('Profile.background_check_complete');
	echo $this->Form->input('Profile.background_check_by');
	echo $this->Form->input('Profile.background_check_date');
	echo $this->Form->input('Profile.signed_covenant_date', array(
		'empty' => true
	));
	?>
</fieldset>
<fieldset class="grid_5 omega">
	<legend>User Permissions</legend>
	<?php
	echo $this->Form->input('User.active');
	echo $this->Form->input('Profile.adult');
	echo $this->Form->input('Profile.qualified_leader');
	echo $this->Form->hidden('User.group_id', array('value' => 8));
	?>
</fieldset>