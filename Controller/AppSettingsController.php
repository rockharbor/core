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
	public $name = 'AppSettings';

/**
 * Additional helpers for this controller
 *
 * @var array
 */
	public $helpers = array('Formatting');

/**
 * List of types that shouldn't be considered models
 *
 * @var array
 */
	protected $_reservedTypes = array(
		'list', 'string', 'html', 'integer', 'image', 'plugin'
	);

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Shows a list of AppSettings
 */
	public function index() {
		$this->paginate = array(
			'order' => 'name ASC'
		);
		$appSettings = $this->AppSetting->find('all', array(
			'conditions' => array(
				'type <>' => 'plugin'
			)
		));

		foreach ($appSettings as &$appSetting) {
			if (!in_array($appSetting['AppSetting']['type'], $this->_reservedTypes)) {
				$Model = ClassRegistry::init($appSetting['AppSetting']['type']);
				$results = $Model->find('first', array(
					'fields' => array(
						$Model->displayField
					),
					'conditions' => array(
						$Model->alias.'.id' => $appSetting['AppSetting']['value']
					)
				));
				if ($results) {
					$appSetting['AppSetting']['readable_value'] = $results[$Model->alias][$Model->displayField];
				}
			}
		}

		$this->set(compact('appSettings'));
	}

/**
 * Edits an AppSetting and clears the existing cache
 *
 * @param integer $id The id of the AppSetting
 */
	public function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->cakeError('error404');
		}
		if (!empty($this->data)) {
			switch($this->data['AppSetting']['type']) {
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
				$this->Session->setFlash('This setting has been saved.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to save setting. Please try again.', 'flash'.DS.'failure');
			}
		}

		if (empty($this->data)) {
			$this->data = $this->AppSetting->find('first', array(
				'conditions' => array(
					'AppSetting.id' => $id
				),
				'cache' => false
			));
		}
		if (!in_array(strtolower($this->data['AppSetting']['type']), $this->_reservedTypes)) {
			$this->set('model', $this->data['AppSetting']['type']);
		}
	}

/**
 * Searches a model for auto complete
 *
 * @param string $model The model
 */
	public function search($model = null) {
		if (!$model || empty($this->data)) {
			return;
		}
		$Model = ClassRegistry::init($model);
		$results = $Model->find('list', array(
			'conditions' => array(
				$Model->alias.'.'.$Model->displayField.' LIKE' => '%'.$this->data['AppSetting']['value'].'%'
			),
			'contain' => false,
			'limit' => 10
		));
		$this->set(compact('results', 'model'));
	}

}


