<?php
/**
 * AppSetting controller class.
 *
 * By default, AppSettings can only be added manually. There are a few
 * AppSetting types:
 *
 * ### Types
 * - `html` Allows html content
 * - `string` Basic string
 * - `integer` An integer
 * - `list` A comma delimited list
 * - default: Looks for the model based on the type. If one is found then all
 * records from that model are considered options and the primaryKey is saved
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::import('Core', 'Sanitize');

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
 * List of types that shouldn't be considered models
 *
 * @var array
 */
	var $_reservedTypes = array(
		'list', 'string', 'html', 'integer'
	);

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
			if (!in_array($appSetting['AppSetting']['type'], $this->_reservedTypes)) {
				$models = ClassRegistry::init($appSetting['AppSetting']['type'])->find('list');
				$this->set($appSetting['AppSetting']['type'].'Options', $models);
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
			$this->AppSetting->Behaviors->detach('Sanitizer.Sanitize');

			switch($this->data['AppSetting']['type']) {
				case 'html':
					$this->data['AppSetting']['value'] = Sanitize::html($this->data['AppSetting']['value']);
				break;
				case 'string':
					$this->data['AppSetting']['value'] = Sanitize::clean($this->data['AppSetting']['value'], array('remove_html' => true));
				break;
				case 'list':
					$exps = explode(',', $this->data['AppSetting']['value']);
					foreach ($exps as &$exp) {
						$exp = Sanitize::clean($exp, array('remove_html' => true));
					}
					$this->data['AppSetting']['value'] = implode(',', $exps);
				break;
				case 'integer':
					$this->data['AppSetting']['value'] = preg_replace('/[^0-9]/', '', $this->data['AppSetting']['value']);
				break;
			}

			if ($this->AppSetting->save($this->data)) {
				$this->Session->setFlash('The setting has been saved', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('The setting could not be saved. Please, try again.', 'flash'.DS.'failure');
			}
		}

		if (empty($this->data)) {
			$this->data = $this->AppSetting->read(null, $id);
		}
		if (!in_array(strtolower($this->data['AppSetting']['type']), $this->_reservedTypes)) {
			$models = ClassRegistry::init($this->data['AppSetting']['type'])->find('list');
			$this->set('valueOptions', $models);
		}
	}

}


?>