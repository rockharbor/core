<?php

App::import('Controller', 'App');

class ProxyAppController extends AppController {

	public $name = 'App';

	public $uses = array();

	public function _extractIds($model = null, $path = '/User/id') {
		return call_user_func_array('parent::_extractIds', func_get_args());
	}

	public function _setConditionalGroups($params = array(), $user = array()) {
		return call_user_func_array('parent::_setConditionalGroups', func_get_args());
	}

	public function _editSelf() {
		return call_user_func_array('parent::_editSelf', func_get_args());
	}

}