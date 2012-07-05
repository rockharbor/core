<?php

if (!empty($auth) && !empty($auth['message'])) {
	echo $this->Html->tag('p', $auth['message'], array(
		'class' => 'auth-message'
	));
}

if (!empty($flash) && !empty($flash['message'])) {
	echo $this->Html->tag('p', $flash['message'], array(
		'class' => 'flash-message'
	));
}