<h2>Edit Address</h2>

<div class="addresses">
<?php echo $this->Form->create('Address', array('default'=>false));?>
	<fieldset>
 		<legend><?php printf(__('Edit %s', true), __('Address', true)); ?></legend>
	<?php
		echo $this->Form->hidden('foreign_key');
		echo $this->Form->hidden('model');
		echo $this->Form->input('id');
		echo $this->Form->input('primary');
		echo $this->Form->input('name');
		echo $this->Form->input('address_line_1');
		echo $this->Form->input('address_line_2');
		echo $this->Form->input('city');
		echo $this->Form->input('state', array(
			'type' => 'select',
			'options' => $this->SelectOptions->states
		));
		echo $this->Form->input('zip');
	?>
	</fieldset>
<?php


echo $this->Js->submit('Save', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<?php echo $this->Js->writeBuffer(array('onDomReady'=>false)); ?>