<?php
/**
 * AppSetting controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * AppSettings Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class AppSettingsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'AppSettings';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Shows a list of AppSettings
 */
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

/**
 * Edits an AppSetting and clears the existing cache
 *
 * @param integer $id The id of the AppSetting
 */
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid setting', 'flash'.DS.'failure');
		}
		if (!empty($this->data)) {
			if ($this->AppSetting->save($this->data)) {
				// clear cached settings
				$this->AppSetting->clearCache();
				$this->Session->setFlash('The setting has been saved', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('The setting could not be saved. Please, try again.', 'flash'.DS.'failure');
			}
		}

		if (empty($this->data)) {
			$this->data = $this->AppSetting->read(null, $id);
		}
		
		if (!empty($this->data['AppSetting']['model'])) {
			$models = ClassRegistry::init($this->data['AppSetting']['model'])->find('list');
			$this->set('valueOptions', $models);
		}
	}

}


?>