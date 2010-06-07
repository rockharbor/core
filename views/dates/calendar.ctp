<div>
<?php

echo $this->Form->input('Search.query', array(
	'id' => 'FilterMinistry',
	'label' => 'Filter for ministry:'
));
echo $this->Form->input('Search.query', array(
	'id' => 'FilterInvolvement',
	'label' => 'Filter for involvement:'
));

?>
<br />
<?php
if ($passed == 'passed') {
	echo $this->Html->link('Hide Past Events', array('controller' => 'dates', 'action' => 'calendar', 'model' => $filterModel, $filterModel=>$filterModelId), array('class' => 'button'));
} else {
	echo $this->Html->link('Show Past Events', array('controller' => 'dates', 'action' => 'calendar', 'passed', 'model' => $filterModel, $filterModel=>$filterModelId), array('class' => 'button'));
}

if (!empty($filterModel)) {
	echo $this->Html->link('Reset Filter', array('controller' => 'dates', 'action' => 'calendar', $passed), array('class' => 'button'));
}
?>
</div>
<br />
<div>
Jump to:&nbsp;
<?php 
echo $this->Form->select('Jump.month', $this->SelectOptions->generateOptions('month'));
echo '&nbsp;';
echo $this->Form->select('Jump.year', $this->SelectOptions->generateOptions('year'));
?>
</div>
<br />
<?php
echo $this->element('calendar', array(
	'filters' => array(
		$passed,
		'model' => $filterModel,
		$filterModel => $filterModelId
	)
));

$this->Js->buffer('CORE.autoComplete("FilterInvolvement", "'.Router::url(array(
	'controller' => 'searches',
	'action' => 'index',
	'model' => 'Involvement',
	'ext' => 'json'
)).'", function(item) {
	redirect("'.Router::url(array('controller'=>'dates', 'action'=>'calendar', $passed)).'/model:Involvement/Involvement:"+item.id);
})');

$this->Js->buffer('CORE.autoComplete("FilterMinistry", "'.Router::url(array(
	'controller' => 'searches',
	'action' => 'index',
	'model' => 'Ministry',
	'ext' => 'json'
)).'", function(item) {
	redirect("'.Router::url(array('controller'=>'dates', 'action'=>'calendar', $passed)).'/model:Ministry/Ministry:"+item.id);
})');

$this->Js->buffer('function gotoDate() {
	if ($("#JumpYear").val() == "") {
		year = new Date().getFullYear();
	} else {
		year = $("#JumpYear").val()
	}
	
	if ($("#JumpMonth").val() == "") {
		month = new Date().getMonth();
	} else {
		month = Number($("#JumpMonth").val())-1;
	}
	
	$("#calendar").fullCalendar("gotoDate", year, month, 1);
}');

$this->Js->buffer('$("#JumpMonth").change(gotoDate);');
$this->Js->buffer('$("#JumpYear").change(gotoDate);');
$this->Js->buffer('$("#JumpYear").change();');


?>