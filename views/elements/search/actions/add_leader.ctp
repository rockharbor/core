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

if ($named['leader_model'] == 'Involvement') {
	$links[] = array(
		'title' => 'Add to roster',
		'url' => array(
			'controller' => 'involvements',
			'action' => 'invite',
			$this->MultiSelect->token,
			1, // status = confirmed
			'Involvement' => $named['leader_model_id']
		),
		'options' => array(
			'success' => 'CORE.showFlash(data);'
		)
	);
}

echo $this->element('multiselect', compact('links', 'colCount'));

?>