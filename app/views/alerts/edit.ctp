<div class="alerts">
<?php echo $this->Form->create('Alert', array(
	'default' => false
));?>
	<fieldset>
 		<legend>Edit Alert</legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('group_id', array(
			'label' => 'Visible for:'
		));
		echo $this->Form->input('importance', array(
			'type' => 'select',
			'options' => array(
				'low' => 'Regular',
				'medium' => 'Medium',
				'high' => 'High'
			)
		));
		echo $this->Form->input('expires', array(
			'empty' => true
		));
	?>
	</fieldset>
<?php 
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();

echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
echo $this->Html->css('jquery.wysiwyg');
$this->Js->buffer('CORE.wysiwyg("AlertDescription");');
?>
</div>