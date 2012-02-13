<?php
$this->Report->squash('User.Profile.work_phone', array('User.Profile.work_phone', 'User.Profile.work_phone_ext'), '%d %d', 'Work Phone');
?>
<div class="clearfix">
	<fieldset class="grid_6">
		<legend>User Information</legend>
		<div class="grid_3 alpha">
			<p>(Full name is included by default)</p>
			<?php
			// at the very least include name in the report
			echo $this->Form->hidden('Export.User.Profile.name');
			echo $this->Form->input('Export.User.username', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.first_name', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.last_name', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.primary_email', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.cell_phone', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.home_phone', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.work_phone', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.birth_date', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.age', array(
				'type' => 'checkbox'
			));
			?>
		</div>
	</fieldset>
</div>