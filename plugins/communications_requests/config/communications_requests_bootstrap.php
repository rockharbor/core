<?php

Core::hook(array(
	'plugin' => 'communications_requests',
	'controller' => 'requests',
	'action' => 'history'
), 'root.communications-requests', array(
	'title' => 'Communications',
	'options' => array(
		'rel' => 'modal-none'
	)
));

Core::hook(array(
	'plugin' => 'communications_requests',
	'controller' => 'requests',
	'action' => 'index'
), 'root.communications-requests.index', array(
	'title' => 'Manage Requests',
	'options' => array(
		'class' => 'hover-row'
	)
));


Core::hook(array(
	'plugin' => 'communications_requests',
	'controller' => 'requests',
	'action' => 'add'
), 'root.communications-requests.add', array(
	'title' => 'Make a Request',
	'options' => array(
		'rel' => 'modal-none',
		'class' => 'hover-row'
	)
));

Core::hook(array(
	'plugin' => 'communications_requests',
	'controller' => 'requests',
	'action' => 'history'
), 'root.communications-requests.history', array(
	'options' => array(
		'rel' => 'modal-none',
		'class' => 'hover-row'
	)
));