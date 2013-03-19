<?php

App::uses('ReportHelper', 'View/Helper');

class ProxyReportHelper extends ReportHelper {

	public function _fields($val = '') {
		if ($val !== '') {
			$this->_fields = $val;
		} else {
			return $this->_fields;
		}
	}

}