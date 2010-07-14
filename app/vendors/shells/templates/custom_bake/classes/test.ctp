<?php
/**
 * Test Case bake template
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.console.libs.templates.objects
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
echo "<?php\n";
echo "/* ". $className ." Test cases generated on: " . date('Y-m-d H:m:s') . " : ". time() . " */\n";
?>
App::import('<?php echo $type; ?>', '<?php echo $plugin . $className;?>');

<?php if ($mock and strtolower($type) == 'controller'): ?>
class Test<?php echo $fullClassName; ?> extends <?php echo $fullClassName; ?> {
	var $components = array(
		'DebugKit.Toolbar' => array(
			'autoRun' => false
		),
		'Referee.Whistle' => array(
			'enabled' => false
		),
		'QueueEmail' => array(
			'enabled' => false
		)
	);

	function redirect($url, $status = null, $exit = true) {
		if (!$this->Session->check('TestCase.redirectUrl')) {
			$this->Session->write('TestCase.flash', $this->Session->read('Message.flash'));
			$this->Session->write('TestCase.redirectUrl', $url);
		}
	}

	function _stop($status = 0) {
		$this->Session->write('TestCase.stopped', $status);
	}

	function isAuthorized() {
		$action = str_replace('controllers/Test', '', $this->Auth->action());
		$auth = parent::isAuthorized($action);
		$this->Session->write('TestCase.authorized', $auth);
		return $auth;
	}
}

<?php endif; ?>
class <?php echo $fullClassName; ?>TestCase extends CakeTestCase {
<?php
// fixtures required by CORE to run tests on controllers
if ($mock and strtolower($type) == 'controller') {
	$fixtures = array('app.notification',	'app.user', 'app.group',
	'app.profile', 'app.classification', 'app.job_category',	'app.school',
	'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement',
	'app.involvement_type', 'app.address', 'app.zipcode', 'app.region',
	'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role',
	'app.roster_status',	'app.answer', 'app.payment', 'app.payment_type',
	'app.leader', 'app.comment', 'app.comment_type', 'app.comments',
	'app.notification', 'app.image', 'plugin.media.document',
	'app.household_member',	'app.household', 'app.publication',
	'app.publications_user', 'app.log', 'app.app_setting', 'app.alert',
	'app.alerts_user', 'app.aro',	'app.aco', 'app.aros_aco',
	'app.ministries_rev', 'app.involvements_rev', 'app.error');
}
$fixtures[] = 'app.log';
if (!empty($fixtures)):
?>
	var $fixtures = array('<?php echo join("', '", $fixtures); ?>');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

<?php endif; ?>
	function startTest() {
		$this-><?php echo $className . ' =& ' . $construction; ?>
<?php if ($mock and strtolower($type) == 'controller'): ?>
		// necessary fixtures
		$this->loadFixtures('Aco', 'Aro', 'ArosAco', 'Group', 'Error');
		$this-><?php echo $className; ?>->Component->initialize($this-><?php echo $className; ?>);
		$this-><?php echo $className; ?>->Session->write('Auth.User', array('id' => 1));
		$this-><?php echo $className; ?>->Session->write('User', array('Group' => array('id' => 1)));
<?php endif; ?>
	}

	function endTest() {
<?php if ($mock and strtolower($type) == 'controller'): ?>
		$this-><?php echo $className; ?>->Session->destroy();
<?php endif; ?>
		unset($this-><?php echo $className;?>);		
		ClassRegistry::flush();
	}

<?php foreach ($methods as $method): ?>
	function test<?php echo Inflector::classify($method); ?>() {

	}

<?php endforeach;?>
}
<?php echo '?>'; ?>