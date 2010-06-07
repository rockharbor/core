<h2>Submit Payment</h2>
<div class="payments">
<?php 
$balance = Set::apply('/Roster/balance', $users, 'array_sum');

if ($balance > 0) {

?>
<p>You are submitting a payment for 
<?php echo $involvement['Involvement']['name']; ?> for 
<?php echo $this->Text->toList(Set::extract('/User/Profile/name', $users)); ?>.
<?php 
if (count($users) > 1) {
	echo ' Your payment will be averaged out against all these people. If a person has a lower balance than the average, the remaining amount will be divided amongst the remaining people.';
} 
?>
</p>
<p>Amount owed: <strong><?php echo $this->Formatting->money($balance); ?></strong>.</p>
<div id="payment_types" class="ui-tabs">	
	
	
	<ul class="tabs">
		<?php
			foreach ($paymentTypes as $paymentTypeId => $paymentTypeName):
				echo '<li class="tab"><a href="#payment_type_'.$paymentTypeId.'">'.$paymentTypeName.'</a></li>';
			endforeach;
		?>
	</ul>

<?php 

foreach ($paymentTypes as $paymentTypeId => $paymentTypeName):

	echo '<div id="payment_type_'.$paymentTypeId.'">';
	
	echo $this->Form->create('Payment', array(
		'default' => false,
		'url' => array(
			$mskey
		)
	));
	echo $this->Form->hidden('payment_type_id', array(
		'value' => $paymentTypeId
	));
	echo $this->Form->input('amount', array(
		'label' => 'I\'m going to pay:'
	));
	echo $this->element('payment_type'.DS.strtolower(str_replace(' ', '_', $paymentTypeName)), array(
		'addresses' => $userAddresses
	));
	echo $this->Form->input('comment');
	
	$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';	
	echo $this->Js->submit('Submit '.$paymentTypeName.' Payment', $defaultSubmitOptions);
	echo $this->Form->end();
	
	echo '</div>';
	
endforeach;


$this->Js->buffer('CORE.tabs("payment_types");');

} else {
?>
<p>No balance is due!</p>
<?php } ?>
</div>