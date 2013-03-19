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
	'Controller/Component' => array(TESTS . 'proxies' . DS . 'components' . DS),
	'Controller' => array(TESTS . 'proxies' . DS . 'controllers' . DS),
	'View/Helper' => array(TESTS . 'proxies' . DS . 'helpers' . DS)
));

App::uses('ControllerTestCase', 'TestSuite');
App::uses('Security', 'Utility');
App::uses('CakeSession', 'Model/Datasource');

/**
 * CoreTestCase class
 *
 * Extends the functionality of CakeTestCase
 *
 * @package       core
 * @subpackage    core.lib
 */
class CoreTestCase extends ControllerTestCase {

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
 * Loads app settings into `Core` class, ignoring cache. Use to reload possibly
 * altered settings.
 */
	public function loadSettings() {
		$this->loadFixtures('AppSetting', 'Attachment');
		$this->_cacheDisable = Configure::read('Cache.disable');
		Configure::write('Cache.disable', true);
		Core::getInstance()->settings = array();
		Core::loadSettings(true);
	}

/**
 * Clears app settings cache
 */
	public function unloadSettings() {
		ClassRegistry::init('AppSetting')->clearCache();
		Core::getInstance()->settings = array();
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
				'User' => CakeSession::read('Auth.User')
			);
			$_defaults = array_merge(CakeSession::read('User'), $_defaults);
		}
		$user = Set::merge($_defaults, $user);

		$auth = CakeSession::write('Auth.User', $user['User']);
		$sess = CakeSession::write('User', $user);

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

