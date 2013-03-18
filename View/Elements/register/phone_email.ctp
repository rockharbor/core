<?php
$_defaults = array(
	'short' => false,
);
extract($_defaults, EXTR_SKIP);
?>
<fieldset class="grid_5">
	<legend>Phone and Email</legend>
<?php
echo $this->Form->input('Profile.cell_phone', array(
	'maxlength' => '30',
	'value' => !empty($this->data['Profile']) ? $this->Formatting->phone($this->data['Profile']['cell_phone']) : ''
));
if (!$short) {
	echo $this->Form->input('Profile.home_phone', array(
		'maxlength' => '30',
		'value' => !empty($this->data['Profile']) ? $this->Formatting->phone($this->data['Profile']['home_phone']) : ''
	));
	echo '<div class="clearfix">';
	echo $this->Form->input('Profile.work_phone', array(
		'maxlength' => '30',
		'value' => !empty($this->data['Profile']) ? $this->Formatting->phone($this->data['Profile']['work_phone']) : '',
		'div' => array(
			'style' => 'float:left;margin-right:5px'
		)
	));
	echo $this->Form->input('Profile.work_phone_ext', array(
		'size' => 5,
		'style' => 'float:left',
		'label' => 'ext',
		'div' => array(
			'style' => 'float:left'
		)
	));
	echo '</div>';
}
echo $this->Form->input('Profile.primary_email');
if (!$short) {
	echo $this->Form->input('Profile.alternate_email_1');
	echo $this->Form->input('Profile.alternate_email_2');
}
?>
</fieldset>