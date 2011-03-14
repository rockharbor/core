<h1>Add Address</h1>

<div class="addresses">
<?php if (!empty($addresses)): ?>
<p>Choose from an existing address</p>
<?php echo $this->Form->create('Address', array('default' => false));?>
	<?php
		echo $this->Form->hidden('existing', array('value'=>true));
		echo $this->Form->input('address_id');
	?>
<?php
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true});';
echo $this->Js->submit('Choose', $defaultSubmitOptions);
echo $this->Form->end();
?>
<p>or add a new one</p>
<?php endif; ?>
<?php echo $this->Form->create('Address', array('default' => false));?>
	<fieldset>
 		<legend>Address</legend>
	<?php
		echo $this->Form->hidden('foreign_key', array('value'=>$modelId));
		echo $this->Form->hidden('model', array('value'=>$model));
		echo $this->Form->hidden('primary', array(
			'value' => true
		));
		echo $this->Form->hidden('active', array(
			'value' => true
		));
		echo $this->Form->input('name');
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
echo $this->Js->submit('Add', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>