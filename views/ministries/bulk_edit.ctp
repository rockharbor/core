<h1>Bulk Edit Ministries</h1>
<?php
echo $this->Form->create(array(
	'default' => false
));
?>
<fieldset>
	<legend>Edit</legend>
	<?php
		echo $this->Form->input('move_campus', array(
			'type' => 'checkbox',
			'label' => 'Check to move to a new campus'
		));
		echo $this->Form->input('campus_id');
		echo $this->Form->input('move_ministry', array(
			'type' => 'checkbox',
			'label' => 'Check to move to a new ministry'
		));
		echo $this->Form->input('parent_id', array(
			'empty' => true
		));
		echo $this->Form->input('active', array(
			'type' => 'radio',
			'options' => array(
				1 => 'Yes',
				0 => 'No'
			)
		));
		echo $this->Form->input('private', array(
			'type' => 'radio',
			'options' => array(
				1 => 'Yes',
				0 => 'No'
			)
		));
	?>
</fieldset>
<?php
$this->Js->buffer('$("#MinistryParentId").parent().hide();');
$this->Js->buffer('$("#MinistryCampusId").parent().hide();');
$this->Js->buffer('$("#MinistryMoveMinistry").on("change", function() {
	$(this).is(":checked") ? $("#MinistryParentId").parent().show() : $("#MinistryParentId").parent().hide();
})');
$this->Js->buffer('$("#MinistryMoveCampus").on("change", function() {
	$(this).is(":checked") ? $("#MinistryCampusId").parent().show() : $("#MinistryCampusId").parent().hide();
})');
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
