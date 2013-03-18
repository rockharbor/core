<?php

App::import('Component', 'AuthorizeDotNet');

class ProxyAuthorizeDotNetComponent extends AuthorizeDotNetComponent {

	public function getData($key = null) {
		if ($key) {
			return $this->_data[$key];
		}
		return $this->_data;
	}

	public function setData($data = array()) {
		$this->_data = $data;
	}

	public function _formatFields($data = array()) {
		return parent::_formatFields($data);
	}

	public function _init() {
		return parent::_init();
	}

}