<div class="alerts">
<?php echo $this->Form->create('Alert', array(
	'default' => false
));?>
	<fieldset>
 		<legend>Add Alert</legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('group_id', array(
			'label' => 'Visible for:'
		));
		echo $this->Form->input('expires', array(
			'empty' => true,
			'value' => '00-00-00 00:00'
		));
	?>
	</fieldset>
<?php
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();

echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
echo $this->Html->css('jquery.wysiwyg');
$this->Js->buffer('CORE.wysiwyg("AlertDescription");');
?>
</div>