<h1>Calendar</h1>
<div class="content-box">
Jump to:&nbsp;
<?php
echo $this->Form->create(null, array(
	'class' => 'core-filter-form'
));
echo $this->Form->select('Jump.month', $this->SelectOptions->generateOptions('month'));
echo '&nbsp;';
echo $this->Form->select('Jump.year', $this->SelectOptions->generateOptions('year'));
echo $this->Form->end('Jump');

$calendarid = uniqid();
echo $this->element('calendar', array(
	'id' => $calendarid,
	'filters' => $this->passedArgs,
	'size' => $size
));

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
	
	$("#calendar'.$calendarid.'").fullCalendar("gotoDate", year, month, 1);
}');

$this->Js->buffer('$("#JumpMonth").change(gotoDate);');
$this->Js->buffer('$("#JumpYear").change(gotoDate);');
$this->Js->buffer('$("#JumpYear").change();');
?>
</div>