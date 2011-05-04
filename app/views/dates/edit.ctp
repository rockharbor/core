<h1>Edit Date</h1>

<div class="dates">

<div id="humanized" class="box highlight"></div>

<?php 
	echo $this->Form->create('Date', array('default' => false));	
	echo $this->Form->input('id');
?>	
	<fieldset>
		<legend>Date</legend>
	<?php
		echo $this->Form->input('exemption', array(
			'label' => 'This date is an exemption'
		));
		echo $this->Form->input('recurring');
	?>
		<fieldset id="pattern" style="display:none">
			<legend>Recurrance Pattern</legend>
		<?php
			echo $this->Form->input('recurrance_type', array(
				'type' => 'select',
				'options' => $recurranceTypes
			));
			
			echo $this->Form->input('frequency', array(
				'label' => false,
				'before' => 'Recur every ',
				'after' => ' <span id="frequency_label">day(s)</span>',
				'style' => 'width:20px'
			));
			
			echo $this->Form->input('offset', array(
				'type' => 'select',
				'options' => array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),
				'div' => array('id' => 'offset')
			));
			
			echo $this->Form->input('weekday', array(
				'label' => false,
				'before' => 'On ',
				'type' => 'select',
				'options' => $this->SelectOptions->generateOptions('week'),
				'div' => array('id' => 'weekday')
			));
			
			echo $this->Form->input('day', array(
				'type' => 'select',
				'options' => $this->SelectOptions->generateOptions('day'),
				'div' => array('id' => 'day')
			));
			
		?>
		</fieldset>
	
	<?php	
		echo $this->Form->input('start_date', array(
			'div' => array('id' => 'start_date')
		));
		echo $this->Form->input('permanent', array(
			'label' => 'Never ends'
		));		
		echo $this->Form->input('end_date', array(
			'div' => array('id' => 'end_date')
		));
		echo $this->Form->input('all_day');
		echo $this->Form->input('start_time', array(
			'interval' => 15,
			'div' => array('id' => 'start_time')
		));		
		echo $this->Form->input('end_time', array(
			'interval' => 15,
			'div' => array('id' => 'end_time')
		));	
	?>
	</fieldset>
	
<?php 
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<?php

echo $this->Html->script('super_date');

$this->Html->scriptStart(array('inline'=>true));

echo '$(\'#DateRecurranceType\').bind(\'change\', function() {
	var label = $(\'#frequency_label\');
	
	$(\'#day\').hide();
	$(\'#weekday\').hide();
	$(\'#offset\').hide();
	
	switch($(this).val()) {
		case \'h\':
		label.text(\'hour(s)\');
		break;
		case \'d\':
		label.text(\'day(s)\');
		break;
		case \'w\':
		label.text(\'week(s)\');
		$(\'#weekday\').show();
		break;
		case \'md\':
		label.text(\'month(s)\');
		$(\'#day\').show();
		break;
		case \'mw\':
		label.text(\'month(s)\');
		$(\'#offset\').show();
		$(\'#weekday\').show();
		break;
		case \'y\':
		label.text(\'year(s)\');
		break;
	}
});';

echo '$(\'#DateRecurring\').bind(\'change\', function() {
	this.checked ? $(\'#pattern\').show() : $(\'#pattern\').hide();
});';

echo '$(\'#DatePermanent\').bind(\'change\', function() {
	this.checked ? $(\'#end_date\').hide() : $(\'#end_date\').show();
});';

echo '$(\'#DateAllDay\').bind(\'change\', function() {
	this.checked ? $(\'#start_time, #end_time\').hide() : $(\'#start_time, #end_time\').show();
});';

echo '$(\'#DateExemption\').bind(\'change\', function() {
	if (this.checked) {
		$(\'#DateAllDay\').attr(\'checked\', \'checked\');
		$(\'#DateAllDay\').parent(\'div\').hide();
		$(\'#DateAllDay\').change();
	} else {
		$(\'#DateAllDay\').parent(\'div\').show();
		$(\'#DateAllDay\').change();
	}
});';

// date validation (forcing end dates to be greater than start)
echo '$(\'#DateStartDateMonth\').bind(\'change\', function() { CORE.validateDate(\'DateStartDate\', \'DateEndDate\'); });';
echo '$(\'#DateStartDateDay\').bind(\'change\', function() { CORE.validateDate(\'DateStartDate\', \'DateEndDate\'); });';
echo '$(\'#DateStartDateYear\').bind(\'change\', function() { CORE.validateDate(\'DateStartDate\', \'DateEndDate\'); });';
echo '$(\'#DateEndDateMonth\').bind(\'change\', function() { CORE.validateDate(\'DateStartDate\', \'DateEndDate\'); });';
echo '$(\'#DateEndDateDay\').bind(\'change\', function() { CORE.validateDate(\'DateStartDate\', \'DateEndDate\'); });';
echo '$(\'#DateEndDateYear\').bind(\'change\', function() { CORE.validateDate(\'DateStartDate\', \'DateEndDate\'); });';

// time validation (forcing end times to be greater than start)
echo '$(\'#DateStartTimeHour\').bind(\'change\', function() { CORE.validateTime(\'DateStartTime\', \'DateEndTime\'); });';
echo '$(\'#DateStartTimeMin\').bind(\'change\', function() { CORE.validateTime(\'DateStartTime\', \'DateEndTime\'); });';
echo '$(\'#DateStartTimeMeridian\').bind(\'change\', function() { CORE.validateTime(\'DateStartTime\', \'DateEndTime\'); });';
echo '$(\'#DateEndTimeHour\').bind(\'change\', function() { CORE.validateTime(\'DateStartTime\', \'DateEndTime\'); });';
echo '$(\'#DateEndTimeMin\').bind(\'change\', function() { CORE.validateTime(\'DateStartTime\', \'DateEndTime\'); });';
echo '$(\'#DateEndTimeMeridian\').bind(\'change\', function() { CORE.validateTime(\'DateStartTime\', \'DateEndTime\'); });';

// finally, make everything update the humanized version
echo '$(\'select, input\').bind(\'change\', function() { 
	var hr = CORE.makeHumanReadable({
		recurring: $(\'#DateRecurring\').is(\':checked\'),
		type: $(\'#DateRecurranceType\').val(),
		frequency: $(\'#DateFrequency\').val(),
		allday: $(\'#DateAllDay\').is(\':checked\'),
		day:$(\'#DateDay\').val(),
		weekday: $(\'#DateWeekday\').val(),
		offset: $(\'#DateOffset\').val(),
		permanent: $(\'#DatePermanent\').is(\':checked\'),
		startDate: $(\'#DateStartDateYear\').val()+\'-\'+$(\'#DateStartDateMonth\').val()+\'-\'+$(\'#DateStartDateDay\').val(),
		endDate: $(\'#DateEndDateYear\').val()+\'-\'+$(\'#DateEndDateMonth\').val()+\'-\'+$(\'#DateEndDateDay\').val(),
		startTime: (Number($(\'#DateStartTimeHour\').val()) + ($(\'#DateStartTimeMeridian\').val() == \'pm\' ? 12 : 0))+\':\'+$(\'#DateStartTimeMin\').val(),
		endTime: (Number($(\'#DateEndTimeHour\').val()) + ($(\'#DateEndTimeMeridian\').val() == \'pm\' ? 12 : 0))+\':\'+$(\'#DateEndTimeMin\').val()
	});
	
	$(\'#humanized\').text(hr);
});';

// initial setup
echo '$(\'#DateRecurranceType\').change();';
echo '$(\'#DateRecurring\').change();';
echo '$(\'#DatePermanent\').change();';
echo '$(\'#DateAllDay\').change();';
echo '$(\'#DateStartDateMonth\').change();';
echo '$(\'#DateStartTimeHour\').change();';
echo '$(\'#DateExemption\').change();';


echo $this->Html->scriptEnd();



?>
