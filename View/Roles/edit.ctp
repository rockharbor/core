<h1>Edit Role</h1>
<?php
echo $this->Form->create(array(
	'default' => false,
	'url' => $this->passedArgs,
));
?>
<fieldset>
	<legend>Edit</legend>
<?php
echo $this->Form->hidden('Role.id');
echo $this->Form->input('Role.name');
echo $this->Form->input('Role.description');
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals: true})';
echo $this->Js->submit('Save', $defaultSubmitOptions);
?>
</fieldset>
<?php
echo $this->Form->end();
