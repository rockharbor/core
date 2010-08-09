<?php
/**
 * CoreTestCase class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.lib
 */

/**
 * Includes
 */
App::import('Component', 'Acl');
require_once APP.'config'.DS.'routes.php';

/**
 * Mocks
 */
Mock::generatePartial('AclComponent', 'MockAclComponent', array('check'));

/**
 * CoreTestCase class
 *
 * Extends the functionality of CakeTestCase
 *
 * @package       core
 * @subpackage    core.lib
 */
class CoreTestCase extends CakeTestCase {

/**
 * Fixtures needed for test. Overwrite if you don't need them all.
 * 
 * @var array
 */
	var $fixtures = array(
		'app.address',
		'app.alert',
		'app.alerts_user',
		'app.answer',
		'app.app_setting',
		'app.attachment',
		'app.campus',
		'app.classification',
		'app.comment',
		'app.comment_type',
		'app.comments',
		'app.date',
		'app.group',
		'app.household',
		'app.household_member',
		'app.image',
		'app.involvement',
		'app.involvement_type',
		'app.involvements_rev',
		'app.job_category',
		'app.leader',
		'app.log',
		'app.merge_request',
		'app.ministries_rev',
		'app.ministry',
		'app.notification',
		'app.payment',
		'app.payment_option',
		'app.payment_type',
		'app.profile',
		'app.publication',
		'app.publications_user',
		'app.question',
		'app.region',
		'app.role',
		'app.roster',
		'app.school',
		'app.user',
		'app.zipcode',
	);

/**
 * Don't automatically populate database
 *
 * @var boolean
 */
	var $autoFixtures = false;

/**
 * Components are initialized once to avoid redirect loop issues
 * 
 * @var boolean
 * @access protected
 */
	var $_componentsInitialized = false;

/**
 * The controller we're testing. Set to null to use the original
 * `CakeTestCase::testAction` function.
 * 
 * @var object
 */
	var $testController = null;

/**
 * Tests an action using the controller itself and skipping the dispatcher, and
 * returning the view vars.
 *
 * Since `CakeTestCase::testAction` was causing so many problems and is
 * incredibly slow, it is overwritten here to go about it a bit differently.
 * Import `CoreTestCase` from 'Lib' and extend test cases using `CoreTestCase`
 * instead to gain this functionality.
 *
 * For backwards compatibility with the original `CakeTestCase::testAction`, set
 * `testController` to `null`.
 *
 * ### Options:
 * - `data` Data to pass to the controller
 *
 * ### Limitations:
 * - only reinstantiates the default model
 * - not 100% complete, i.e., some callbacks may not be fired like they would
 *	  if regularly called through the dispatcher
 *
 * @param string $url The url to test
 * @param array $options A list of options
 * @return array The view vars
 * @link http://mark-story.com/posts/view/testing-cakephp-controllers-the-hard-way
 */
	function testAction($url = '', $options = array()) {		
		if (is_null($this->testController)) {
			return parent::testAction($url, $options);
		}

		$Controller = $this->testController;

		// reset parameters
		ClassRegistry::flush();		
		$Controller->passedArgs = array();
		$Controller->params = array();
		$Controller->url = null;
		$Controller->action = null;
		$Controller->viewVars = array();
		$Controller->{$Controller->modelClass}->create();
		$Controller->Session->delete('Message');
		$Controller->activeUser = null;

		$default = array(
			'data' => array(),
			'method' => 'post'
		);
		$options = array_merge($default, $options);

		// set up the controller based on the url
		$urlParams = Router::parse($url);
		if (strtolower($options['method']) == 'get') {
			$urlParams['url'] = array_merge($options['data'], $urlParams['url']);
		} else {
			$Controller->data = $options['data'];
		}
		$Controller->passedArgs = $urlParams['named'];
		$Controller->params = $urlParams;
		$Controller->url = $urlParams;
		$Controller->action = $urlParams['plugin'].'/'.$urlParams['controller'].'/'.$urlParams['action'];

		// only initialize the components once
		if ($this->_componentsInitialized === false) {
			$this->_componentsInitialized = true;
			$Controller->Component->initialize($Controller);
		}

		// configure auth
		if (isset($Controller->Auth)) {
			$Controller->Auth->initialize($Controller);
			if (!$Controller->Session->check('Auth.User') && !$Controller->Session->check('User')) {
				$Controller->Session->write('Auth.User', array('id' => 1, 'username' => 'testadmin'));
				$Controller->Session->write('User', array('Group' => array('id' => 1, 'lft' => 1)));
			}
		}
		// configure acl
		if (isset($Controller->Acl)) {
			$Controller->Acl = new MockAclComponent();
			$Controller->Acl->enabled = true;
			$Controller->Acl->setReturnValue('check', true);
		}
		
		$Controller->beforeFilter();
		$Controller->Component->startup($Controller);

		call_user_func_array(array(&$Controller, $urlParams['action']), $urlParams['pass']);

		$Controller->beforeRender();
		$Controller->Component->triggerCallback('beforeRender', $Controller);

		return $Controller->viewVars;
	}

}

?>