<?php

$acResults = array();

// format nice for autocomplete
foreach ($users as $user) {
	$acResults[] = array(
		'id' => $user['User']['id'],
		'action' => Router::url(array('controller' => 'profiles', 'action' => 'view', 'User' => $user['User']['id'])),
		'label' => $this->element('search'.DS.'autocomplete'.DS.'user', array('user' => $user, 'query' => $this->data['Search']['query']), true),
		'value' => $user['Profile']['name']
	);
}
foreach ($ministries as $ministry) {
	$acResults[] = array(
		'id' => $ministry['Ministry']['id'],
		'action' => Router::url(array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['Ministry']['id'])),
		'label' => $this->element('search'.DS.'autocomplete'.DS.'ministry', array('ministry' => $ministry, 'query' => $this->data['Search']['query']), true),
		'value' => $ministry['Ministry']['name']
	);
}
foreach ($involvements as $involvement) {
	$acResults[] = array(
		'id' => $involvement['Involvement']['id'],
		'action' => Router::url(array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvement['Involvement']['id'])),
		'label' => $this->element('search'.DS.'autocomplete'.DS.'involvement', array('involvement' => $involvement, 'query' => $this->data['Search']['query']), true),
		'value' => $involvement['Involvement']['name']
	);
}
$acResults[] = array(
	'action' => Router::url(array('controller' => 'searches', 'action' => 'index', '?' => array('q' => $this->data['Search']['query']))),
	'label' => $this->element('search'.DS.'autocomplete'.DS.'full_search'),
	'value' => $this->data['Search']['query']
);
echo json_encode($acResults);

