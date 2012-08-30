<?php
$this->Report->squash('User.Profile.work_phone', array('User.Profile.work_phone', 'User.Profile.work_phone_ext'), '%d %d', 'Work Phone');
$this->Report->squash('User.Address.name', array('User.ActiveAddress.address_line_1', 'User.ActiveAddress.address_line_2', 'User.ActiveAddress.city', 'User.ActiveAddress.state', 'User.ActiveAddress.zip'), '%s %s %s, %s %d', 'Address');
$this->Report->multiple('Answer.description', 'expand');
$this->Report->alias(array('RosterStatus.name' => 'Roster Status'));
$this->Report->alias(array('Answer.description' => 'Answer'));
?>
<div class="clearfix">
	<fieldset class="grid_6">
		<legend>User Information</legend>
		<div class="grid_3 alpha">
			<?php
			echo $this->Form->input('Export.User.username', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.first_name', array(
				'type' => 'checkbox',
				'checked' => true
			));
			echo $this->Form->input('Export.User.Profile.last_name', array(
				'type' => 'checkbox',
				'checked' => true
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
	<fieldset class="grid_6">
		<legend>Contact Information</legend>
		<div class="grid_3 omega">
			<?php
			echo $this->Form->input('Export.User.Address.name', array(
				'type' => 'checkbox',
				'label' => 'Address'
			));
			echo $this->Form->input('Export.User.Profile.primary_email', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.alternate_email_1', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.User.Profile.alternate_email_2', array(
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
			?>
		</div>
	</fieldset>
	<fieldset class="grid_6">
		<legend>Roster Information</legend>
		<div class="grid_3 omega">
			<?php
			echo $this->Form->input('Export.Answer.description', array(
				'type' => 'checkbox',
				'label' => 'Answers to Questions'
			));
			echo $this->Form->input('Export.RosterStatus.name', array(
				'type' => 'checkbox',
				'label' => 'Roster Status'
			));
			?>
		</div>
	</fieldset>
</div>