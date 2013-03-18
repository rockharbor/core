<?php

App::import('Controller', 'UserImages');

class ProxyUserImagesController extends UserImagesController {

	public function _getLimit($model = null, $modelClass = null) {
		return call_user_func_array('parent::_getLimit', func_get_args());
	}

}