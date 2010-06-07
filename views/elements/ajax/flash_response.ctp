<?php

extract($response);

// put out a nice little message
echo $this->Html->tag('div', $message, array(
	'id' => 'flashMessage',
	'class' => (isset($errors) && count($errors) > 0) ? 'message' : 'success'
));

// check for validation issues, and show error messages
if (isset($errors) && count($errors) > 0) {
	$this->Html->scriptStart();
	
	foreach ($errors as $field => $error) {
		// get the field value
		$div = $model.Inflector::camelize($field);
		
		// get error produced by form helper
		$errorDiv = $this->Form->error($model.'.'.$field);
		
		// call global js function
		$this->Js->buffer('showValidationError("'.$div.'", "'.$error.'");');
	};

	$this->Html->scriptEnd();
	
	echo $this->Js->writeBuffer(array(
		'onDomReady' => false
	));	
}

?>