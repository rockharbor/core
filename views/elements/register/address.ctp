<fieldset class="grid_5">
	<legend>Address</legend>
<?php
echo $this->Form->input('Address.0.address_line_1');
echo $this->Form->input('Address.0.address_line_2');
echo $this->Form->input('Address.0.city');
echo $this->Form->input('Address.0.state', array(
	'options' => $this->SelectOptions->states
));
echo $this->Form->input('Address.0.zip');
?>
</fieldset>