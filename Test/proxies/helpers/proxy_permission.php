<?php

App::uses('PermissionHelper', 'View/Helper');

class ProxyPermissionHelper extends PermissionHelper {

	public function _canSeePrivate($val = '') {
		if ($val !== '') {
			$this->_canSeePrivate = $val;
		} else {
			return $this->_canSeePrivate;
		}
	}

}