<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	var $name = 'Pages';

/**
 * Default helper
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html');

/**
 * This controller does not use a model
 *
 * @var array
 * @access public
 */
	var $uses = array();

	function beforeFilter() {
		$this->Auth->allow('display', 'phrase');
		parent::beforeFilter();
	}

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @access public
 */
	function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}

/**
 * Generates a phrase
 *
 * @param string $model Name of the model to use. If empty, a generic
 *		phrase will be used instead
 */
	function phrase($model = null) {
		$result = null;

		if (!empty($model)) {
			$rand = rand(0,1);
			$Model = ClassRegistry::init($model);
			$conditions = array(
				$model.'.active' => true,
				$model.'.private' => false
			);
			if ($model == 'Involvement') {
				$conditions[$model.'.previous'] = false;
			}
			$models = $Model->find('list', array(
				'conditions' => $conditions
			));
			$ids = array_keys($models);
			if (!empty($ids)) {
				$randModelId = $ids[rand(0,count($ids)-1)];
				$result = $Model->read(null, $randModelId);
			} else {
				$model = null;
			}
		}
		$this->set(compact('result', 'model'));
	}
}
