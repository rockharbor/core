<h1>New Payment Option</h1>
<div class="paymentOptions">
<?php echo $this->Form->create('PaymentOption', array('default'=>false));?>
	<fieldset class="grid_6">
 		<legend><?php printf(__('Add %s', true), __('Payment Option', true)); ?></legend>
	<?php
		echo $this->Form->hidden('involvement_id', array(
			'value' => $involvementId
		));
		echo $this->Form->input('name');
		echo $this->Form->input('account_code');
		echo $this->Form->input('tax_deductible');
	?>
	</fieldset>
	<fieldset class="grid_6">
		<legend>Amounts</legend>
		<?php
		echo $this->Form->input('total', array(
			'label'   => 'Price per person',
			'between' => '$ '
		));
		echo $this->Form->input('deposit', array(
			'between' => '$ '
		));
		echo $this->Form->input('childcare', array(
			'between' => '$ '
		));
		?>
	</fieldset>
<?php
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>