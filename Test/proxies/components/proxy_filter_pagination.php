<?php

App::import('Component', 'FilterPagination');

class ProxyFilterPaginationComponent extends FilterPaginationComponent {

	public function _attachLinkedModels(&$Model, $linked) {
		return parent::_attachLinkedModels($Model, $linked);
	}

	public function startEmpty($val = null) {
		if ($val === null) {
			return $this->startEmpty;
		}
		$this->startEmpty = $val;
	}

}