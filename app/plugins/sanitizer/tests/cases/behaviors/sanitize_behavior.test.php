<?php

class Clean extends CakeTestModel {

	var $name = 'Clean';

	var $actsAs = array(
		'Sanitizer.Sanitize'
	);

	var $sanitize = null;

	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
		),
		'description' => array(
			'rule' => 'notEmpty',
			'required' => false,
			'allowEmpty' => true,
		)
	);
}

class SanitizeBehaviorTestCase extends CakeTestCase {

	var $fixtures = array('plugin.sanitizer.clean');

	function startTest() {
		$this->Clean =& new Clean();
	}

	function endTest() {
		unset($this->Clean);
		ClassRegistry::flush();
	}

	function testSetup() {
		$result = $this->Clean->Behaviors->Sanitize->settings;
		$expected = array(
			'Clean' => array(
				'validate' => 'after'
			)
		);
		$this->assertEqual($result, $expected);
	}

	function testNoSanitize() {
		$this->Clean->Behaviors->detach('Sanitize');

		$data = array(
			'name' => '<b>Html!</b>'
		);
		$this->Clean->create();
		$this->Clean->save($data);
		$result = $this->Clean->read();
		$expected = array(
			'Clean' => array(
				'id' => 1,
				'name' => '<b>Html!</b>',
				'description' => null
			)
		);
		$this->assertEqual($result, $expected);
	}

	function testSanitizeWithoutOptions() {
		$data = array(
			'name' => '<b>Html!</b>'
		);
		$this->Clean->create();
		$this->assertTrue($this->Clean->save($data));

		$result = $this->Clean->read();
		$expected = array(
			'Clean' => array(
				'id' => 1,
				'name' => 'Html!',
				'description' => null
			)
		);
		$this->assertEqual($result, $expected);

		$data = array(
			'name' => '<b>'
		);
		$this->Clean->create();
		$this->assertFalse($this->Clean->save($data));
	}

	function testSanitizeWithOptions() {
		$this->Clean->Behaviors->Sanitize->settings['Clean']['validate'] = 'after';
		$this->Clean->sanitize = array(
			'name' => 'html'
		);

		$data = array(
			'name' => '<b>Html!</b>'
		);
		$this->Clean->create();
		$this->assertTrue($this->Clean->save($data));

		$result = $this->Clean->read();
		$expected = array(
			'Clean' => array(
				'id' => 1,
				'name' => '&lt;b&gt;Html!&lt;/b&gt;',
				'description' => null
			)
		);
		$this->assertEqual($result, $expected);

		$this->Clean->sanitize = array(
			'name' => array(
				'html' => array(
					'remove' => true
				)
			),
			'description' => array(
				'paranoid' => array(
					'@', '*'
				)
			)
		);
		$data = array(
			'name' => '<b>Try this</b>',
			'description' => '*Some "silly" $tring! @ #42'
		);
		$this->Clean->create();
		$this->assertTrue($this->Clean->save($data));

		$result = $this->Clean->read();
		$expected = array(
			'Clean' => array(
				'id' => 2,
				'name' => 'Try this',
				'description' => '*Somesillytring@42'
			)
		);
		$this->assertEqual($result, $expected);
	}

	function testSanitizeBeforeValidation() {
		$this->Clean->Behaviors->Sanitize->settings['Clean']['validate'] = 'after';
		$this->Clean->sanitize = array(
			'name' => 'html'
		);

		$data = array(
			'name' => '<b>Html!</b>'
		);
		$this->Clean->create();
		$this->assertTrue($this->Clean->save($data));

		$result = $this->Clean->read();
		$expected = array(
			'Clean' => array(
				'id' => 1,
				'name' => '&lt;b&gt;Html!&lt;/b&gt;',
				'description' => null
			)
		);
		$this->assertEqual($result, $expected);

		$this->Clean->sanitize = array();
		$data = array(
			'name' => '<b>'
		);
		$this->Clean->create();
		$this->assertFalse($this->Clean->save($data));
	}

	function testSanitizeAfterValidation() {
		$this->Clean->Behaviors->Sanitize->settings['Clean']['validate'] = 'before';
		$this->Clean->sanitize = array(
			'name' => 'html'
		);

		$data = array(
			'name' => '<b>Html!</b>'
		);
		$this->Clean->create();
		$this->assertTrue($this->Clean->save($data));

		$result = $this->Clean->read();
		$expected = array(
			'Clean' => array(
				'id' => 1,
				'name' => '&lt;b&gt;Html!&lt;/b&gt;',
				'description' => null
			)
		);
		$this->assertEqual($result, $expected);

		$data = array(
			'name' => '<b>'
		);
		$this->Clean->create();
		$this->assertTrue($this->Clean->save($data));
	}
}
?>
