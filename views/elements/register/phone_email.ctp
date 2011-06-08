<fieldset class="grid_5">
	<legend>Phone and Email</legend>
<?php
echo $this->Form->input('Profile.cell_phone', array(
	'maxlength' => '30'
));
echo $this->Form->input('Profile.home_phone', array(
	'maxlength' => '30'
));
echo $this->Form->input('Profile.work_phone', array(
	'maxlength' => '30'
));
echo $this->Form->input('Profile.primary_email');
echo $this->Form->input('Profile.alternate_email_1');
echo $this->Form->input('Profile.alternate_email_2');
?>
</fieldset>