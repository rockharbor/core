<?php
$_defaults = array(
	'count' => 0,
	'householdMember' => array()
);
extract($_defaults, EXTR_SKIP);

?>
<fieldset class="grid_5" id="member<?php echo $count; ?>">
<?php
if (!empty($householdMember['User']['id'])) {
	// single user was found
	echo $this->Html->tag('p', $householdMember['Profile']['first_name'].' exists in '.Core::read('general.site_name').' and will be invited to your household.');
	echo $this->Form->hidden('HouseholdMember.'.$count.'.User.id', array(
		'value' => $householdMember['User']['id']
	));
	echo $this->Html->link('Remove from household', 'javascript:CORE_user.cancelAddMember('.$count.');', array('class' => 'cancel-add button'));
} elseif (!empty($householdMember['found'])) {
	$possibleMembers = Set::combine($householdMember['found'], '/User/id', array('{0} {1} ({2})', '/Profile/first_name', '/Profile/last_name', '/ActiveAddress/city'));
	echo $this->Form->input('HouseholdMember.'.$count.'.User.id', array(
		'type' => 'radio',
		'options' => $possibleMembers,
		'legend' => 'Please choose the person that matches who you want to add to your household.'
	));
	echo $this->Html->link('None of these', 'javascript:CORE_user.cancelAddMember('.$count.');', array('class' => 'cancel-add button'));
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