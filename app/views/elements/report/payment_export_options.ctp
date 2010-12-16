<?php
// at the very least include payer, amount and date
echo $this->Form->hidden('Export.Payer.Profile.name');
echo $this->Form->hidden('Export.Payment.amount');
echo $this->Form->hidden('Export.Payment.created');

$this->Report->alias(array('Payer.Profile.name' => 'Payed For'));
$this->Report->alias(array('Payment.created' => 'Date'));
$this->Report->alias(array('Roster.Involvement.name' => 'Involvement Name'));
$this->Report->alias(array('PaymentType.name' => 'Payment Type'));
$this->Report->alias(array('User.Profile.name' => 'Paid For'));
$this->Report->alias(array('Payment.number' => 'Card/Check Number'));

echo $this->Form->input('Export.Roster.Involvement.name', array(
	'type' => 'checkbox',
	'label' => 'Involvement Name'
));
echo $this->Form->input('Export.PaymentType.name', array(
	'type' => 'checkbox',
	'label' => 'Payment Type'
));
echo $this->Form->input('Export.User.Profile.name', array(
	'type' => 'checkbox',
	'label' => 'Paid For'
));
echo $this->Form->input('Export.Payment.number', array(
	'type' => 'checkbox',
	'label' => 'Card/Check Number'
));
echo $this->Form->input('Export.Payment.comment', array(
	'type' => 'checkbox'
));
echo $this->Form->input('Export.Payment.transaction_id', array(
	'type' => 'checkbox',
	'label' => 'Transaction Id'
));
?>