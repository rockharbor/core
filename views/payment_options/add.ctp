<h2>New Payment Option</h2>
<div class="paymentOptions">
<?php echo $this->Form->create('PaymentOption', array('default'=>false));?>
	<fieldset>
 		<legend><?php printf(__('Add %s', true), __('Payment Option', true)); ?></legend>
	<?php
		echo $this->Form->hidden('involvement_id', array(
			'value' => $involvementId
		));
		echo $this->Form->input('name');
		echo $this->Form->input('total');
		echo $this->Form->input('deposit');
		echo $this->Form->input('childcare');
		echo $this->Form->input('account_code');
		echo $this->Form->input('tax_deductible');
	?>
	</fieldset>
<?php 
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>