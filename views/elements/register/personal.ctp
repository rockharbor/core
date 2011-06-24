<div class="clearfix">
	<fieldset class="grid_5 alpha">
		<legend>Name</legend>
	<?php
	echo $this->Form->input('Profile.first_name');
	echo $this->Form->input('Profile.last_name');
	?>
	</fieldset>
	<fieldset class="grid_5 omega">
		<legend>User Info</legend>
	<?php
	echo $this->Form->input('username', array(
		'after' => '<div>Leave blank if you want '.Core::read('general.site_name').' to pick one for you based on your name.</div>'
	));
	echo $this->Form->input('password', array(
		'after' => '<div>Leave blank if you want '.Core::read('general.site_name').' to pick one for you.</div>'
	));
	echo $this->Form->input('confirm_password', array(
		'type' => 'password'
	));
	?>
	</fieldset>
</div>
