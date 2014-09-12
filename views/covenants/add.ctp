<h1>Add Covenant</h1>

<div>
	<?php
	echo $this->Form->create('Covenant', array('default' => false));
	echo $this->Form->hidden('user_id', array(
		'value' => $userId
	));
	?>
	<fieldset>
		<legend>Covenant</legend>
		<?php echo $this->Form->input('year', array(
			'type' => 'select',
			'label' => 'Select covenant ministry year',
			'options' => $this->SelectOptions->generateOptions('ministryYear', array('min' => '2011', 'order' => 'asc'))
		));
		?>
	</fieldset>

	<?php
	$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals: true})';
	echo $this->Js->submit('Save', $defaultSubmitOptions);
	echo $this->Form->end();
	?>
</div>
