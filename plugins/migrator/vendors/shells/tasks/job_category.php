<?php

class JobCategoryTask extends MigratorTask {

	var $_oldTable = 'job_categories';
	var $_oldPk = 'job_category_id';
	var $_newModel = 'JobCategory';

	function mapData() {
		$this->_editingRecord = array(
			'JobCategory' => array(
				'name' => $this->_editingRecord['job_category_name'],
			)
		);
	}

	function _prepareJobCategoryName($old) {
		$phrases = explode('/', $old);
		foreach ($phrases as &$phrase) {
			$phrase = ucwords($phrase);
		}
		return implode(' / ', $phrases);
	}

}