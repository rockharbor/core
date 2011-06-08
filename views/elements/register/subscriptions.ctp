<p>Select any publications you would like to subscribe to below.</p>
<fieldset>
	<legend>Subscriptions</legend>
<?php
echo $this->Form->input('Publication', array(
	'multiple' => 'checkbox',
	'empty' => false,
	'label' => false
));
?>
</fieldset>