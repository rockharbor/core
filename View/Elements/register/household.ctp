
<div id="members" class="clearfix">
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
echo $this->Html->link('Add additional member', 'javascript:;', array(
	'onclick' => 'CORE_user.addAdditionalMember()',
	'id' => 'add-member'
));
?>
<?php
$this->Js->buffer('CORE_user.member = '.$hmcount);
$this->Js->buffer('CORE_user.element = "'.addslashes(str_replace(array("\r\n", "\r", "\n"), ' ', $this->element('register'.DS.'household_member', array('count' => 'COUNT')))).'"');
?>
<noscript>
<style type="text/css">
	#add-member, .cancel-add {
		display:none;
	}
</style>
</noscript>