<?php

$links = array(
	array(
		'title' => 'Add leader',
		'url' => array(
			'controller' => $named['leader_controller'],
			'action' => 'add',
			'model' => $named['leader_model'],
			$named['leader_model'] => $named['leader_model_id'],
			'User' => $activeUser['User']['id'],
			$this->MultiSelect->token
		),
		'options' => array(
			'success' => 'CORE.showFlash(data);'
		)
	)
);

echo $this->element('multiselect', compact('links', 'colCount'));

?>