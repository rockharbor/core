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
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	function render($action = null, $layout = null, $file = null) {
		$this->renderedAction = $action;
	}

	function _stop($status = 0) {
		$this->stopped = $status;
	}
}

<?php endif; ?>
class <?php echo $fullClassName; ?>TestCase extends CakeTestCase {
<?php 
$fixtures[] = 'app.log';
if (!empty($fixtures)):
?>
	var $fixtures = array('<?php echo join("', '", $fixtures); ?>');

<?php endif; ?>
<?php if ($mock and strtolower($type) == 'controller'): ?>
	function _prepareAction($action = '') {
		$this-><?php echo $className; ?>->params = Router::parse($action);
		$this-><?php echo $className; ?>->passedArgs = array_merge($this-><?php echo $className; ?>->params['named'], $this-><?php echo $className; ?>->params['pass']);
		$this-><?php echo $className; ?>->params['url'] = $this-><?php echo $className; ?>->params;
		$this-><?php echo $className; ?>->beforeFilter();
	}

<?php endif; ?>
	function startTest() {
		$this-><?php echo $className . ' =& ' . $construction; ?>
	}

	function endTest() {
		unset($this-><?php echo $className;?>);
		ClassRegistry::flush();
	}

<?php foreach ($methods as $method): ?>
	function test<?php echo Inflector::classify($method); ?>() {

	}

<?php endforeach;?>
}
<?php echo '?>'; ?>