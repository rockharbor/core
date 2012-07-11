<h1>Edit Payment</h1>
<div class="payments">
<p><strong>Warning!</strong> Editing a payment record will directly influence the 
roster&apos;s existing balance and will not actually refund or charge the user.</p>
<?php 
echo $this->Form->create('Payment', array(
	'default' => false
));
echo $this->Form->input('id');
echo $this->Form->input('amount');
echo $this->Form->input('payment_type_id');
echo $this->Form->input('number');
echo $this->Form->input('comment', array(
	'size' => 100
));
	
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';	
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>