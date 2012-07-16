<fieldset class="grid_5">
	<legend>Address</legend>
<?php
if (isset($addresses)) {
	echo $this->Form->input('Default.address_id', array(
		'label' => 'Select an existing address to auto-fill the fields:',
		'empty' => true,
		'options' => Set::combine($addresses, '/Address/id', '/Address/name')
	));
}
echo $this->Form->input('Address.0.address_line_1');
echo $this->Form->input('Address.0.address_line_2');
echo $this->Form->input('Address.0.city');
echo $this->Form->input('Address.0.state', array(
	'options' => $this->SelectOptions->states
));
echo $this->Form->input('Address.0.zip');
?>
</fieldset>
<?php
if (isset($addresses)) {
	$this->Js->buffer('var addresses = '.$this->Js->object(Set::combine($addresses, '/Address/id', '/Address')));

	$this->Js->buffer('$("#DefaultAddressId").bind("change", function() {
		if ($(this).val() == 0) {
			$("#Address0AddressLine1").val("");
			$("#Address0AddressLine2").val("");
			$("#Address0City").val("");
			$("#Address0State").val("");
			$("#Address0Zip").val("");
		} else {
			var selected = addresses[$(this).val()].Address;
			$("#Address0AddressLine1").val(selected.address_line_1);
			$("#Address0AddressLine2").val(selected.address_line_2);
			$("#Address0City").val(selected.city);
			$("#Address0State").val(selected.state);
			$("#Address0Zip").val(selected.zip); 
		}
	});');

	$this->Js->buffer('$("#DefaultAddressId").change();');
}
