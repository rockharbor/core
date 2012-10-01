<?php
$this->Report->alias(array('JobCategory.name' => 'Job Category'));
$this->Report->alias(array('Classification.name' => 'Classification'));
$this->Report->alias(array('Group.name' => 'Permission Group'));
$this->Report->squash('Address.name', array('ActiveAddress.address_line_1', 'ActiveAddress.address_line_2', 'ActiveAddress.city', 'ActiveAddress.state', 'ActiveAddress.zip'), '%s %s %s, %s %d', 'Address');
$this->Report->squash('Profile.work_phone', array('Profile.work_phone', 'Profile.work_phone_ext'), '%d %d', 'Work Phone');
$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.primary_email', 'expand');
$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.alternate_email_1', 'expand');
$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.alternate_email_2', 'expand');
$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.cell_phone', 'expand');
$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.home_phone', 'expand');
$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.work_phone', 'expand');
$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.name', 'expand');
$this->Report->alias(array('HouseholdMember.Household.HouseholdContact.Profile.name' => 'Household Contact'));
$this->Report->alias(array('HouseholdMember.Household.HouseholdContact.Profile.primary_email' => 'Household Contact Email'));
$this->Report->alias(array('HouseholdMember.Household.HouseholdContact.Profile.alternate_email_1' => 'Household Contact Alternate Email'));
$this->Report->alias(array('HouseholdMember.Household.HouseholdContact.Profile.alternate_email_2' => 'Household Contact Alternate Email'));
$this->Report->alias(array('HouseholdMember.Household.HouseholdContact.Profile.cell_phone' => 'Household Contact Cell Phone'));
$this->Report->alias(array('HouseholdMember.Household.HouseholdContact.Profile.home_phone' => 'Household Contact Home Phone'));
$this->Report->alias(array('HouseholdMember.Household.HouseholdContact.Profile.work_phone' => 'Household Contact Work Phone'));
$this->Report->alias(array('Profile.ElementarySchool.name' => 'Elementary School'));
$this->Report->alias(array('Profile.MiddleSchool.name' => 'Middle School'));
$this->Report->alias(array('Profile.HighSchool.name' => 'High School'));
$this->Report->alias(array('Profile.College.name' => 'College'));
?>
<div class="clearfix">
	<fieldset class="grid_3 alpha">
		<legend>User Information</legend>
			<?php
			echo $this->Form->input('Export.Profile.first_name', array(
				'type' => 'checkbox',
				'checked' => true
			));
			echo $this->Form->input('Export.Profile.last_name', array(
				'type' => 'checkbox',
				'checked' => true
			));
			echo $this->Form->input('Export.Profile.birth_date', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.age', array(
				'type' => 'checkbox'
			));
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
	</fieldset>
	<fieldset class="grid_3 omega">
		<legend>School Information</legend>
			<?php
			echo $this->Form->input('Export.Profile.grade', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.graduation_year', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.ElementarySchool.name', array(
				'type' => 'checkbox',
				'label' => 'Elementary School'
			));
			echo $this->Form->input('Export.Profile.MiddleSchool.name', array(
				'type' => 'checkbox',
				'label' => 'Middle School'
			));
			echo $this->Form->input('Export.Profile.HighSchool.name', array(
				'type' => 'checkbox',
				'label' => 'High School'
			));
			echo $this->Form->input('Export.Profile.College.name', array(
				'type' => 'checkbox',
				'label' => 'College'
			));
			?>
	</fieldset>
	<fieldset class="grid_3">
		<legend>Contact Information</legend>
			<?php
			echo $this->Form->input('Export.Address.name', array(
				'type' => 'checkbox',
				'label' => 'Address'
			));
			echo $this->Form->input('Export.Profile.primary_email', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.alternate_email_1', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.alternate_email_2', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.cell_phone', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.home_phone', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.Profile.work_phone', array(
				'type' => 'checkbox'
			));
			?>
	</fieldset>
	<fieldset class="grid_3">
		<legend>Household Contact</legend>
			<?php
			echo $this->Form->input('Export.HouseholdMember.Household.HouseholdContact.Profile.name', array(
				'type' => 'checkbox',
				'label' => 'Name'
			));
			echo $this->Form->input('Export.HouseholdMember.Household.HouseholdContact.Profile.primary_email', array(
				'type' => 'checkbox',
				'label' => 'Email'
			));
			echo $this->Form->input('Export.HouseholdMember.Household.HouseholdContact.Profile.alternate_email_1', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.HouseholdMember.Household.HouseholdContact.Profile.alternate_email_2', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.HouseholdMember.Household.HouseholdContact.Profile.cell_phone', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.HouseholdMember.Household.HouseholdContact.Profile.home_phone', array(
				'type' => 'checkbox'
			));
			echo $this->Form->input('Export.HouseholdMember.Household.HouseholdContact.Profile.work_phone', array(
				'type' => 'checkbox'
			));
			?>
	</fieldset>
</div>