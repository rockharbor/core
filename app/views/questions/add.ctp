<h2>Add Question</h2>
<div class="questions">
<?php echo $this->Form->create('Question', array('default' => false));?>
	<fieldset>
 		<legend>Add Question</legend>
	<?php
		echo $this->Form->hidden('involvement_id', array('value' => $involvementId));
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php 
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>