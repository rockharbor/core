<?php
/**
 * Test Case bake template
 */
echo "<?php\n";
echo "/* ". $className ." Test cases generated on: " . date('Y-m-d H:m:s') . " : ". time() . " */\n";
?>
<?php
if ($mock && strtolower($type) == 'controller'):
	$caseClass = 'CoreTestCase';
	$construction = 'new Mock'.$plugin.$className.'Controller();
		$this->'.$className.'->constructClasses();
		$this->'.$className.'->QueueEmail = new MockQueueEmailComponent();
		$this->'.$className.'->QueueEmail->setReturnValue(\'send\', true);
		$this->testController = $this->'.$className.';
';
?>
App::uses('Lib', 'CoreTestCase');
App::uses('Component', array('QueueEmail'));
App::uses('<?php echo $type; ?>', '<?php echo $plugin . $className;?>');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('<?php echo $plugin . $className;?>Controller', 'Mock<?php echo $plugin . $className;?>Controller', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));
<?php
else:
	$caseClass = 'CakeTestCase';
?>
App::uses('<?php echo $type; ?>', '<?php echo $plugin . $className;?>');
<?php
endif;
?>

class <?php echo $fullClassName; ?>TestCase extends <?php echo $caseClass; ?> {
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
	'app.household_member',	'app.household', 'app.log', 'app.app_setting', 
	'app.alert', 'app.alerts_user', 'app.aro',	'app.aco', 'app.aros_aco',
	'app.ministries_rev', 'app.involvements_rev', 'app.error');
}
$fixtures[] = 'app.log';
if (!empty($fixtures)):
?>
	var $fixtures = array('<?php echo join("', '", $fixtures); ?>');

	var $autoFixtures = false;

<?php endif; ?>
	function startTest() {
		$this-><?php echo $className . ' =& ' . $construction; ?>
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