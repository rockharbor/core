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
 * Add proxy class paths
 */
App::build(array(
	'components' => array(TESTS . 'proxies' . DS . 'components' . DS),
	'controllers' => array(TESTS . 'proxies' . DS . 'controllers' . DS),
	'helpers' => array(TESTS . 'proxies' . DS . 'helpers' . DS)
));

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
 * Ensure SimpleTest doesn't think this is a test case
 */
SimpleTest::ignore('CoreTestCase');
ClassRegistry::flush();

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
	public $fixtures = array(
		'app.aco',
		'app.address',
		'app.alert',
		'app.alerts_user',
		'app.answer',
		'app.app_setting',
		'app.aro',
		'app.aros_aco',
		'app.attachment',
		'app.campus',
		'app.campuses_rev',
		'app.classification',
		'app.comment',
		'app.date',
		'app.error',
		'app.group',
		'app.household',
		'app.household_member',
		'app.invitation',
		'app.invitations_user',
		'app.image',
		'app.involvement',
		'app.involvements_ministry',
		'app.involvement_type',
		'app.job_category',
		'app.leader',
		'app.log',
		'app.merge_request',
		'app.ministries_rev',
		'app.ministry',
		'app.notification',
		'app.paginate_test',
		'app.payment',
		'app.payment_option',
		'app.payment_type',
		'app.profile',
		'app.question',
		'app.region',
		'app.role',
		'app.roles_roster',
		'app.roster',
		'app.roster_status',
		'app.school',
		'app.sys_email',
		'app.user',
		'app.zipcode',
	);

/**
 * Don't automatically populate database
 *
 * @var boolean
 */
	public $autoFixtures = false;

/**
 * The controller we're testing. Set to null to use the original
 * `CakeTestCase::testAction` function.
 *
 * @var object
 */
	public $testController = null;

/**
 * Methods to test
 *
 * @param Array of methods to test
 */
	public $testMethods = null;

/**
 * Runs prior to running the test method
 *
 * @param string $method The test method
 * @return void
 */
	public function startTest($method) {
		parent::startTest($method);
		Router::reload();
	}

/**
 * Overrides `CakeTestCase::getTests()` to allow running a subset of tests within
 * the test case
 *
 * @return array Array of tests to run
 */
	public function getTests() {
		$tests = parent::getTests();
		$testMethods = array_udiff($tests, $this->methods, 'strcasecmp');
		if (!isset($this->testMethods) || empty($this->testMethods)) {
			$this->testMethods = $testMethods;
		}
		if (!is_array($this->testMethods)) {
			$this->testMethods = array($this->testMethods);
		}
		if (isset($this->skipSetup) && $this->skipSetup) {
			$tests = array_udiff($tests, array('start', 'end'), 'strcasecmp');
		}
		if (empty($this->testMethods)) {
			return $tests;
		}
		$removeMethods = array_udiff($testMethods, $this->testMethods, 'strcasecmp');
		$tests = array_udiff($tests, $removeMethods, 'strcasecmp');
		$skipped = array_udiff($testMethods, $this->testMethods, 'strcasecmp');
		foreach ($skipped as $skip) {
			$this->_reporter->paintSkip(sprintf(__('Skipped entire test method: %s', true), $skip));
		}
		return $tests;
	}

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
	public function testAction($url = '', $options = array()) {
		if (is_null($this->testController)) {
			return parent::testAction($url, $options);
		}

		$Controller = $this->testController;

		// reset parameters
		$Controller->passedArgs = array();
		$Controller->params = array();
		$Controller->url = null;
		$Controller->action = null;
		$Controller->viewVars = array();
		$keys = ClassRegistry::keys();
		foreach ($keys as $key) {
			if (is_a(ClassRegistry::getObject(Inflector::camelize($key)), 'Model')) {
				ClassRegistry::getObject(Inflector::camelize($key))->create(false);
			}
		}
		$Controller->Session->delete('Message');
		$Controller->activeUser = null;

		$default = array(
			'data' => array(),
			'method' => 'post'
		);
		$options = array_merge($default, $options);

		// set up the controller based on the url
		$urlParams = Router::parse($url);
		if (stripos('http', $url) === false) {
			$url = 'http://localhost/'.ltrim($url, '/');
		}
		parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
		if (!isset($urlParams['url'])) {
			$urlParams['url'] = array();
		}
		$urlParams['url'] = array_merge($urlParams['url'], $queryParams);
		if (strtolower($options['method']) == 'get') {
			$urlParams['url'] = array_merge($options['data'], $urlParams['url']);
		} else {
			$Controller->data = $options['data'];
		}
		$Controller->passedArgs = $urlParams['named'];
		$Controller->params = $urlParams;
		$Controller->params['url']['url'] = $url;
		$Controller->url = $urlParams;
		$Controller->action = $urlParams['plugin'].'/'.$urlParams['controller'].'/'.$urlParams['action'];

		// disable components that mess with testing
		$Controller->Whistler->enabled = false;
		$Controller->Toolbar->enabled = false;

		$Controller->Component->initialize($Controller);

		// configure auth
		if (isset($Controller->Auth)) {
			$Controller->Auth->initialize($Controller);
			if (!$Controller->Session->check('Auth.User') && !$Controller->Session->check('User')) {
				$this->su();
			}
		}
		// configure acl
		if (isset($Controller->Acl)) {
			$core =& Core::getInstance();
			$core->Acl = new MockAclComponent();
			$core->Acl->__construct();
			$core->Acl->enabled = true;
			$core->Acl->setReturnValue('check', true);
		}

		$Controller->beforeFilter();
		$Controller->Component->startup($Controller);

		call_user_func_array(array(&$Controller, $urlParams['action']), $urlParams['pass']);

		$Controller->beforeRender();
		$Controller->Component->triggerCallback('beforeRender', $Controller);

		// trick debugkit into skipping its __destruct method which clutters up the response
		if (class_exists('DebugKitDebugger')) {
			$_debugkit =& DebugKitDebugger::getInstance();
			$_debugkit->__benchmarks = null;
		}

		return $Controller->viewVars;
	}

/**
 * Loads app settings into `Core` class, ignoring cache. Use to reload possibly
 * altered settings.
 */
	public function loadSettings() {
		$this->loadFixtures('AppSetting');
		$this->_cacheDisable = Configure::read('Cache.disable');
		Configure::write('Cache.disable', true);
		Core::loadSettings(true);
	}

/**
 * Clears app settings cache
 */
	public function unloadSettings() {
		ClassRegistry::init('AppSetting')->clearCache();
		Configure::write('Cache.disable', $this->_cacheDisable);
	}

/**
 * Changes the session user, and therefore the `$activeUser` on the controller
 * when an action is called
 *
 * @param array $user User data to use
 * @param boolean $wipe Set to `false` to merge existing session info
 * @return boolean
 */
	public function su($user = array(), $wipe = true) {
		if (!$this->testController) {
			return false;
		}

		$_defaults = array(
			'User' => array(
				'id' => 1,
				'username' => 'testadmin',
				'password' => Security::hash('password', null, true),
				'reset_password' => false,
				'group_id' => 1
			),
			'Group' => array(
				'id' => 1,
				'lft' => 1,
				'rght' => 26
			),
			'Profile' => array(
				'name' => 'Test Admin',
				'primary_email' => 'test@test.com',
				'leading' => 0,
				'managing' => 0
			)
		);

		if (!$wipe) {
			$_defaults = array(
				'User' => $this->testController->Session->read('Auth.User')
			);
			$_defaults = array_merge($this->testController->Session->read('User'), $_defaults);
		}
		$user = Set::merge($_defaults, $user);

		$auth = $this->testController->Session->write('Auth.User', $user['User']);
		$sess = $this->testController->Session->write('User', $user);

		return $auth && $sess;
	}

/**
 * Helper function for stripping tabs, newlines and extra whitespace from strings
 *
 * @param string $str
 * @return string
 */
	public function singleLine($str = '') {
		$str = str_replace(array("\t", "\r\n", "\n"), ' ', $str);
		$str = preg_replace('/\s\s+/', ' ', $str);
		return $str;
	}

}

