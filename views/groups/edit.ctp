<div class="groups">
<h2>Edit Group</h2>
<?php
echo $this->Form->create('Group', array(
	'default' => false
));
?>
<fieldset>
	<legend>Group</legend>
	<?php
	echo $this->Form->input('id');
	echo $this->Form->input('name');
	echo $this->Form->input('parent_id');
	?>
</fieldset>
<?php
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>