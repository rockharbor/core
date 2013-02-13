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

echo $this->Html->script('misc/date', array('inline' => false));
$this->Js->buffer('CORE_date.setup();');