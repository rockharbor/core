<div id="members">
<?php
if (empty($this->data)) {
	$this->data['HouseholdMember'] = array(array());
}
$hmcount = 0;

foreach ($this->data['HouseholdMember'] as $householdMember):
	echo $this->element('register'.DS.'household_member', array(
		'householdMember' => $householdMember,
		'count' => $hmcount
	));
	$hmcount++;
endforeach;
?>
</div>
<?php
echo $this->Html->link('Add additional member', 'javascript:void();', array(
	'onclick' => 'CORE_user.addAdditionalMember()',
	'class' => 'button',
	'id' => 'add-member'
));


$this->Js->buffer('CORE_user.member = '.$hmcount);
$this->Js->buffer('CORE_user.element = '.str_replace(array("\r\n", "\r", "\n"), ' ', addslashes($this->element('register'.DS.'household_member', array('count' => 'COUNT')))));
?>
<noscript>
<style type="text/css">
	#add-member {
		display:none;
	}
</style>
</noscript>