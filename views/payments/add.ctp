<h1>Submit Payment</h1>
<div class="payments">
<?php 
$balance = Set::apply('/Roster/balance', $users, 'array_sum');
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
			foreach ($types as $id => $name):
				echo '<li class="tab"><a href="#payment_type_'.$id.'">'.$name.'</a></li>';
			endforeach;
		?>
	</ul>

<?php 

foreach ($types as $id => $name):

	echo '<div id="payment_type_'.$id.'">';
	
	echo $this->Form->create('Payment', array(
		'default' => false,
		'url' => array(
			$mskey
		)
	));
	echo $this->Form->input('amount', array(
		'label' => 'I\'m going to pay:'
	));
	$ptypes = Set::combine(Set::extract('/PaymentType[type='.$id.']', $paymentTypes), '{n}.PaymentType.id', '{n}.PaymentType.name');
	if (count($ptypes) > 1) {
		echo $this->Form->input('payment_type_id', array('options' => $ptypes));
	} else {
		echo $this->Form->hidden('payment_type_id', array('value' => key($ptypes)));
	}
	echo $this->element('payment_type'.DS.strtolower(str_replace(' ', '_', $name)), array(
		'addresses' => $userAddresses
	));
	echo $this->Form->input('comment');
	
	$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';	
	echo $this->Js->submit('Submit '.$name.' Payment', $defaultSubmitOptions);
	echo $this->Form->end();
	
	echo '</div>';
	
endforeach;

$this->Js->buffer('CORE.tabs("payment_types");');
?>
</div>