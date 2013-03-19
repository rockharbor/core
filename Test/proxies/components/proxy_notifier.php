<?php

App::uses('NotifierComponent', 'Controller/Component');

class ProxyNotifierComponent extends NotifierComponent {

	public function _send($user, $options = array()) {
		return parent::_send($user, $options);
	}

	public function _save($user, $options = array()) {
		return parent::_save($user, $options);
	}

	public function _render($template) {
		return parent::_render($template);
	}

	public function _normalizeUser($user = null) {
		return parent::_normalizeUser($user);
	}

}
