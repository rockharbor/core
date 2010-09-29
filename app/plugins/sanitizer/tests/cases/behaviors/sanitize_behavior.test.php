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
			'allowEmpty' => false,
		)
	);
}

class MultiClean extends CakeTestModel {

	var $name = 'MultiClean';

	var $actsAs = array(
		'Sanitizer.Sanitize'
	);

	var $sanitize = null;

	var $hasMany = array(
		'Clean'
	);
}

class SanitizeBehaviorTestCase extends CakeTestCase {

	var $fixtures = array('plugin.sanitizer.clean', 'plugin.sanitizer.multi_clean');

	function startTest() {
		$this->Clean =& new Clean();
	}

	function endTest() {
		unset($this->Clean);
		ClassRegistry::flush();
	}

	function testExitEarly() {
		$this->Clean->Behaviors->Sanitize->settings['Clean']['validate'] = 'before';
		$data = array(
			'name' => '<b>Html!</b>',
			'description' => ''
		);
		$this->Clean->create();
		$this->assertFalse($this->Clean->save($data));
	}

	function testSanitizeMulti() {
		$MultiClean =& new MultiClean();

		$data = array(
			'MultiClean' => array(
				'name' => 'Multi<b>clean</b>me'
			),
			'Clean' => array(
				0 => array(
					'name' => '<em>italic</em>'
				),
				1 => array(
					'name' => 'Test<br />multi'
				)
			)
		);
		$MultiClean->create();
		$this->assertTrue($MultiClean->saveAll($data));
		
		$result = $MultiClean->read();
		$expected = array(
			'MultiClean' => array(
				'id' => 1,
				'name' => 'Multicleanme',
				'description' => null
			),
			'Clean' => array(
				0 => array(
					'id' => 1,
					'name' => 'italic',
					'description' => null,
					'multi_clean_id' => 1
				),
				1 => array(
					'id' => 2,
					'name' => 'Testmulti',
					'description' => null,
					'multi_clean_id' => 1
				)
			)
		);
		$this->assertEqual($result, $expected);
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

	function testSkipSanitizeField() {
		$this->Clean->sanitize = array(
			'name' => false,
		);

		$data = array(
			'name' => '<b>Html!</b>',
			'description' => '<b>More Html!</b>',
		);
		$this->Clean->create();
		$this->Clean->save($data);
		$result = $this->Clean->read();
		$expected = array(
			'Clean' => array(
				'id' => 1,
				'name' => '<b>Html!</b>',
				'description' => 'More Html!',
				'multi_clean_id' => null
			)
		);
		$this->assertEqual($result, $expected);
	}

	function testSkipSanitizeAll() {
		$this->Clean->sanitize = false;

		$data = array(
			'name' => '<b>Html!</b>',
			'description' => '<b>More Html!</b>',
		);
		$this->Clean->create();
		$this->Clean->save($data);
		$result = $this->Clean->read();
		$expected = array(
			'Clean' => array(
				'id' => 1,
				'name' => '<b>Html!</b>',
				'description' => '<b>More Html!</b>',
				'multi_clean_id' => null
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
				'description' => null,
				'multi_clean_id' => null
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
				'description' => null,
				'multi_clean_id' => null
			)
		);
		$this->assertEqual($result, $expected);

		$data = array(
			'name' => '<b>'
		);
		$this->Clean->create();
		$this->assertFalse($this->Clean->save($data));

		$data = array(
			'Clean' => array(
				'name' => '<em>italic</em>'
			)
		);
		$this->Clean->create();
		$this->assertTrue($this->Clean->save($data));

		$result = $this->Clean->read();
		$expected = array(
			'Clean' => array(
				'id' => 2,
				'name' => 'italic',
				'description' => null,
				'multi_clean_id' => null
			)
		);
		$this->assertEqual($result, $expected);
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
				'description' => null,
				'multi_clean_id' => null
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
				'description' => '*Somesillytring@42',
				'multi_clean_id' => null
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
				'description' => null,
				'multi_clean_id' => null
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
				'description' => null,
				'multi_clean_id' => null
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
