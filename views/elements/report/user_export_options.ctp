<?php
$this->Report->alias(array('JobCategory.name' => 'Job Category'));
$this->Report->alias(array('Classification.name' => 'Classification'));
$this->Report->alias(array('Group.name' => 'Permission Group'));
$this->Report->squash('Address.name', array('ActiveAddress.address_line_1', 'ActiveAddress.address_line_2', 'ActiveAddress.city', 'ActiveAddress.state', 'ActiveAddress.zip'), '%s %s %s, %s %d', 'Address');
?>
<div class="clearfix">
	<fieldset class="grid_6">
		<legend>User Information</legend>
		<div class="grid_3 alpha">
			<?php
			echo $this->Form->hidden('Export.User.username');
			echo $this->Form->input('Export.Profile.name', array(
				'type' => 'checkbox',
				'label' => 'Full name'
			));
			echo $this->Form->input('Export.Profile.first_name', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.last_name', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.birth_date', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.age', array(
				'type' => 'checkbox'
			));
			?>
		</div>
		<div class="grid_3 omega">
			<?php
			echo $this->Form->input('Export.Profile.JobCategory.name', array(
				'type' => 'checkbox',
				'label' => 'Job category'
			));
			echo $this->Form->input('Export.Profile.adult', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.gender', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.Classification.name', array(
				'type' => 'checkbox',
				'label' => 'Classification'
			));
			echo $this->Form->input('Export.Profile.occupation', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Group.name', array(
				'type' => 'checkbox',
				'label' => 'Permission group'
			));
			?>
		</div>
	</fieldset>
	<fieldset class="grid_6">
		<legend>Contact Information</legend>
		<div class="grid_3 alpha">
			<?php
			echo $this->Form->input('Export.Address.name', array(
				'type' => 'checkbox',
				'label' => 'Address'
			));
			?>
		</div>
		<div class="grid_3 omega">
			
		</div>
	</fieldset>
</div>