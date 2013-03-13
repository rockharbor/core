<?php
/**
 * Pages controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Pages Controller
 *
 * There is a greedy route for pages that will prevent extra actions from being
 * used automatically. A route for each new method is necessary.
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * Default helper
 *
 * @var array
 */
	public $helpers = array('Html');

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public function beforeFilter() {
		$this->Auth->allow('display', 'phrase', 'message');
		parent::beforeFilter();
	}

/**
 * Generic view for displaying flash messages in the HTML body instead of using
 * the typical flash elements. This is useful for when an action needs to
 * redirect to a generic page but still notify the user of something
 */
	public function message() {
		$this->set('title_for_layout', 'Message');

		$auth = $this->Session->read('Message.auth');
		$flash = $this->Session->read('Message.flash');

		$this->Session->delete('Message.auth');
		$this->Session->delete('Message.flash');

		$this->set(compact('auth', 'flash'));
	}

/**
 * Displays a view
 *
 * @param mixed What page to display
 */
	public function display() {
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
	public function phrase($model = null) {
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
