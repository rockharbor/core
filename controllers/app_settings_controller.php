<?php

class AppSettingsController extends AppController {

	var $name = 'AppSettings';

/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}
	
	function index() {	
		$this->AppSetting->recursive = 0;
		
		$this->paginate = array(
			'order' => 'name ASC'
		);
		$appSettings = $this->paginate();
		
		foreach ($appSettings as $appSetting) {
			if (!empty($appSetting['AppSetting']['model'])) {
				$models = ClassRegistry::init($appSetting['AppSetting']['model'])->find('list');
				$this->set($appSetting['AppSetting']['model'].'Options', $models);
			}
		}
		
		$this->set(compact('appSettings'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid setting', 'flash_failure');
		}
		if (!empty($this->data)) {
			if ($this->AppSetting->save($this->data)) {
				// clear cached settings
				Cache::delete('core_app_settings');
				$this->Session->setFlash('The setting has been saved', 'flash_success');
			} else {
				$this->Session->setFlash('The setting could not be saved. Please, try again.', 'flash_failure');
			}
		}
		
		$this->data = $this->AppSetting->read(null, $id);		
		
		if (!empty($this->data['AppSetting']['model'])) {
			$models = ClassRegistry::init($this->data['AppSetting']['model'])->find('list');
			$this->set('valueOptions', $models);
		}
	}

}


?>