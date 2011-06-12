<?php
$_defaults = array(
	'count' => 0,
	'householdMember' => array()
);
extract($_defaults, EXTR_SKIP);

?>
<fieldset class="grid_5" id="member<?php echo $count; ?>">
<?php
if (!empty($householdMember['Profile']['id'])) {
	echo $householdMember['Profile']['first_name'].' already exists in '.Core::read('general.site_name').'. ';
	echo $householdMember['Profile']['gender'] == 'm' ? 'He\'ll' : 'She\'ll';
	if ($householdMember['Profile']['child']) {
		echo ' be added to your household.';
	} else {
		echo ' be invited to your household.';
	}
	echo $this->Form->hidden('HouseholdMember.'.$count.'.Profile.first_name');
	echo $this->Form->hidden('HouseholdMember.'.$count.'.Profile.last_name');
	echo $this->Form->hidden('HouseholdMember.'.$count.'.Profile.primary_email');
	echo $this->Form->hidden('HouseholdMember.'.$count.'.Profile.birth_date');
} else {
	echo $this->Form->input('HouseholdMember.'.$count.'.Profile.first_name');
	echo $this->Form->input('HouseholdMember.'.$count.'.Profile.last_name');
	echo $this->Form->input('HouseholdMember.'.$count.'.Profile.primary_email');
	echo $this->Form->input('HouseholdMember.'.$count.'.Profile.birth_date', array(
		'type' => 'date'
	));
}
?>
</fieldset>