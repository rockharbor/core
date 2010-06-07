<?php

App::import('Controller', 'Addresses');

class InvolvementAddressesController extends AddressesController {

	var $model = 'Involvement';

	function beforeFilter() {	
		parent::beforeFilter();
		$this->modelId = isset($this->passedArgs[$this->model]) ? $this->passedArgs[$this->model] : null;
	}

}

?>