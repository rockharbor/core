<?php
$links = array(
	array(
		'title' => 'Add User',
		'url' => array(
			'plugin' => 'communications_requests',
			'controller' => 'request_notifiers',
			'action' => 'add',
			$this->MultiSelect->token,
			'RequestType' => $named['RequestType']
		),
		'options' => array(
			'success' => 'CORE.showFlash(data);'
		)
	)
);

echo $this->element('multiselect', compact('links', 'colCount'));
?>