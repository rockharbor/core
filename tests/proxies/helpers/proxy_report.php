<?php

App::import('Helper', 'Report');

class ProxyReportHelper extends ReportHelper {

	public function _fields($val = '') {
		if ($val !== '') {
			$this->_fields = $val;
		} else {
			return $this->_fields;
		}
	}

}