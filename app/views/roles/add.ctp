<h1>Add Role</h1>
<?php
echo $this->Form->create(null, array(
	'default' => false,
	'url' => $this->passedArgs,
));
?>
<fieldset>
	<legend>Add a new role to <?php echo $ministry['Ministry']['name']; ?></legend>
<?php
$defaultSubmitOptions['success'] = 'CORE.update("content");';
echo $this->Form->hidden('Role.ministry_id', array('value' => $ministry['Ministry']['id']));
echo $this->Form->input('Role.name');
echo $this->Js->submit('Add', $defaultSubmitOptions);
?>
</fieldset>
<?php
echo $this->Form->end();
?>