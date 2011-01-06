<?php

$acResults = array();

// format nice for autocomplete
foreach ($users as $user) {
	$acResults[] = array(
		'id' => $user['User']['id'],
		'action' => Router::url(array('controller' => 'profiles', 'action' => 'view', 'User' => $user['User']['id'])),
		'label' => $this->element('search'.DS.'autocomplete'.DS.'user', compact('user', 'query'), true),
		'value' => $user['Profile']['name']
	);
}
foreach ($ministries as $ministry) {
	$acResults[] = array(
		'id' => $ministry['Ministry']['id'],
		'action' => Router::url(array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['Ministry']['id'])),
		'label' => $this->element('search'.DS.'autocomplete'.DS.'ministry', compact('ministry', 'query'), true),
		'value' => $ministry['Ministry']['name']
	);
}
foreach ($involvements as $involvement) {
	$acResults[] = array(
		'id' => $involvement['Involvement']['id'],
		'action' => Router::url(array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id'])),
		'label' => $this->element('search'.DS.'autocomplete'.DS.'involvement', compact('involvement', 'query'), true),
		'value' => $involvement['Involvement']['name']
	);
}
echo json_encode($acResults);

?>