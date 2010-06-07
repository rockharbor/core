<?php

App::import('Controller', 'Addresses');

class UserAddressesController extends AddressesController {

	var $model = 'User';

	function beforeFilter() {	
		parent::beforeFilter();
		$this->modelId = isset($this->passedArgs[$this->model]) ? $this->passedArgs[$this->model] : null;
	}

}

?>