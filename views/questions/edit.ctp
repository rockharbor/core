<h1>Edit Question</h1>
<div class="questions grid_12">
<?php echo $this->Form->create('Question', array('default' => false));?>
	<fieldset>
 		<legend>Edit Question</legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('description', array(
			 'type' => 'textarea',
			 'label' => 'Question'
		));
	?>
	</fieldset>
<?php
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
$this->Js->buffer('CORE.wysiwyg("QuestionDescription");');
?>
</div>