<div class="clearfix">
	<fieldset class="grid_5">
		<legend>Billing Address</legend>
	<?php
		if (isset($addresses)) {
			echo $this->Form->input('Default.address_id', array(
				'label' => 'Select an existing address to auto-fill the fields:',
				'empty' => true
			));
		}
		echo $this->Form->input('CreditCard.address_line_1');
		echo $this->Form->input('CreditCard.address_line_2');
		echo $this->Form->input('CreditCard.city');
		echo $this->Form->input('CreditCard.state');
		echo $this->Form->input('CreditCard.zip');
	?>
	</fieldset>
	<fieldset style="float:left">
		<legend>Credit Card</legend>
	<?php
		echo $this->Form->input('CreditCard.first_name');
		echo $this->Form->input('CreditCard.last_name');
		echo $this->Form->input('CreditCard.credit_card_number');
		echo $this->Form->input('CreditCard.cvv');
		echo $this->Form->input('CreditCard.expiration_date', array(
			'type' => 'date',
			'dateFormat' => 'MY',
			'minYear' => date('Y')
		));
	?>
	</fieldset>
</div>

<?php
if (isset($addresses)) {
	$this->Js->buffer('addresses = '.$this->Js->object(Set::combine($addresses, '/Address/id', '/Address')));

	$this->Js->buffer('$("#DefaultAddressId").on("change", function() {
		if ($(this).val() == 0) {
			$("#AddressAddressLine1").val("");
			$("#AddressAddressLine2").val("");
			$("#AddressCity").val("");
			$("#AddressState").val("");
			$("#AddressZip").val("");
		} else {
			selected = addresses[$(this).val()].Address;
			$("#CreditCardAddressLine1").val(selected.address_line_1);
			$("#CreditCardAddressLine2").val(selected.address_line_2);
			$("#CreditCardCity").val(selected.city);
			$("#CreditCardState").val(selected.state);
			$("#CreditCardZip").val(selected.zip); 
		}
	});');


	$this->Js->buffer('$("#DefaultAddressId").change();');
}
