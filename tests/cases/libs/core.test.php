<?php

App::import('Lib', array('CoreTestCase'));
App::import('Model', 'AppSetting');
App::import('Component', 'Acl');

Mock::generatePartial('AclComponent', 'CoreConfigureMockAclComponent', array('check'));

class CoreConfigureTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('AppSetting', 'Attachment');
		$this->AppSetting =& ClassRegistry::init('AppSetting');
		$this->loadSettings();
	}

	public function endTest() {
		$this->unloadSettings();
		unset($this->AppSetting);
	}

	public function _setupAcl() {
		$core = Core::getInstance();
		$this->loadFixtures('Aco', 'Aro', 'ArosAco');
		// for some reason, the Aro and Aco model's weren't being forced to use
		// the test database, so force them here
		$core->Acl->Aro = ClassRegistry::init('Aro');
		$core->Acl->Aro->setDataSource('test_suite');
		$core->Acl->Aro->Permission->setDataSource('test_suite');
		$core->Acl->Aco = ClassRegistry::init('Aco');
		$core->Acl->Aco->setDataSource('test_suite');
	}

	public function testAddAco() {
		$this->_setupAcl();
		$core = Core::getInstance();

		$this->assertFalse($core->Acl->Aco->node('anywhere'));

		Core::addAco('/anywhere', 8);

		$aco = $core->Acl->Aco->findByAlias('controllers');
		$id = $aco['Aco']['id'];

		$aco = $core->Acl->Aco->findByAlias('anywhere');
		$this->assertEqual($aco['Aco']['parent_id'], $id);
		$this->assertEqual($aco['Aco']['alias'], 'anywhere');

		Core::addAco('/SomeController/action', 8);

		$aco = $core->Acl->Aco->findByAlias('SomeController');
		$id = $aco['Aco']['id'];

		$aco = $core->Acl->Aco->findByAlias('action');
		$this->assertEqual($aco['Aco']['parent_id'], $id);

		$count = $core->Acl->Aco->find('count');
		$this->assertEqual($count, 4);
	}

	public function testRemoveAco() {
		$this->_setupAcl();
		$core = Core::getInstance();

		$this->assertTrue(Core::addAco('/anywhere', 8));
		$this->assertTrue(Core::addAco('/anywhere/action', 8));
		$this->assertTrue(Core::addAco('/anywhere/another_action', 8));

		$count = $core->Acl->Aco->find('count');
		$this->assertEqual($count, 4);

		$aco = $core->Acl->Aco->findByAlias('anywhere');
		$this->assertEqual($aco['Aco']['alias'], 'anywhere');

		Core::removeAco('/anywhere/action');
		$this->assertFalse($core->Acl->Aco->findByAlias('action'));

		Core::removeAco('/anywhere');
		$this->assertFalse($core->Acl->Aco->findByAlias('another_action'));
		$this->assertFalse($core->Acl->Aco->findByAlias('anywhere'));

		$count = $core->Acl->Aco->find('count');
		$this->assertEqual($count, 1);
	}

	public function testAcl() {
		$_oldCache = Configure::read('Cache.disable');
		Configure::write('Cache.disable', false);
		Cache::clear(false, 'acl');

		$core = Core::getInstance();
		$core->Acl = new CoreConfigureMockAclComponent();

		$core->Acl->setReturnValueAt(0, 'check', true);
		$this->assertIdentical(Core::acl(8, '/some/path'), true);

		$core->Acl->setReturnValueAt(1, 'check', false);
		// cached ('check' call not made here)
		$this->assertIdentical(Core::acl(8, '/some/path'), true);

		$this->assertIdentical(Core::acl(1, '/some/path'), false);

		Configure::write('Cache.disable', true);

		$core->Acl->setReturnValueAt(2, 'check', true);
		$this->assertIdentical(Core::acl(1, '/some/path'), true);

		Configure::write('Cache.disable', false);
		Cache::clear(false, 'acl');
		Configure::write('Cache.disable', $_oldCache);
	}

	public function testHook() {
		// remove existing hooks
		$core = Core::getInstance();
		$oldHooks = isset($core->settings['hooks']) ? $core->settings['hooks'] : null;
		unset($core->settings['hooks']);

		Core::hook();
		$this->assertEqual(Core::read('hooks'), array());

		$link1 = array('plugin' => 'plugin', 'controller' => 'controller', 'action' => 'action');
		Core::hook($link1, 'root.new-nav', array(
			'title' => 'My Nav Item'
		));
		$results = Core::read('hooks');
		$expected = array(
			'root' => array(
				'new-nav' => array(
					'options' => array(
						'url' => $link1,
						'title' => 'My Nav Item',
						'element' => null,
						'options' => array()
					)
				)
			)
		);
		$this->assertEqual($results, $expected);

		$link2 = array('plugin' => 'plugin', 'controller' => 'controller', 'action' => 'new_action');
		Core::hook($link2, 'root.new-nav.sub-item-1');
		$results = Core::read('hooks');
		$expected = array(
			'root' => array(
				'new-nav' => array(
					'options' => array(
						'url' => $link1,
						'title' => 'My Nav Item',
						'element' => null,
						'options' => array()
					),
					'sub-item-1' => array(
						'options' => array(
							'url' => $link2,
							'title' => 'New Action',
							'element' => null,
							'options' => array()
						)
					)
				)
			)
		);
		$this->assertEqual($results, $expected);

		$link3 = array('plugin' => 'plugin', 'controller' => 'controller', 'action' => 'sub_action');
		Core::hook($link3, 'root.new-nav.sub-item-2');
		$results = Core::read('hooks');
		$expected = array(
			'root' => array(
				'new-nav' => array(
					'options' => array(
						'url' => $link1,
						'title' => 'My Nav Item',
						'element' => null,
						'options' => array()
					),
					'sub-item-1' => array(
						'options' => array(
							'url' => $link2,
							'title' => 'New Action',
							'element' => null,
							'options' => array()
						)
					),
					'sub-item-2' => array(
						'options' => array(
							'url' => $link3,
							'title' => 'Sub Action',
							'element' => null,
							'options' => array()
						)
					)
				)
			)
		);
		$this->assertEqual($results, $expected);

		$core->settings['hooks'] = $oldHooks;
	}

	public function testGetHooks() {
		// remove existing hooks
		$core = Core::getInstance();
		$oldHooks = isset($core->settings['hooks']) ? $core->settings['hooks'] : null;
		unset($core->settings['hooks']);

		$link1 = array('plugin' => 'plugin', 'controller' => 'controller', 'action' => 'action');
		Core::hook($link1, 'root.new-nav');
		$results = Core::getHooks('root');
		$expected = array(
			'new-nav' => array(
				'options' => array(
					'url' => $link1,
					'title' => 'Action',
					'element' => null,
					'options' => array()
				)
			)
		);
		$this->assertEqual($results, $expected);

		$link2 = array('plugin' => 'plugin', 'controller' => 'controller', 'action' => 'sub_action');
		Core::hook($link2, 'root.new-nav.sub');
		$results = Core::getHooks('root.new-nav');
		$expected = array(
			'options' => array(
				'url' => $link1,
				'title' => 'Action',
				'element' => null,
				'options' => array()
			),
			'sub' => array(
				'options' => array(
					'url' => $link2,
					'title' => 'Sub Action',
					'element' => null,
					'options' => array()
				)
			)
		);
		$this->assertEqual($results, $expected);

		$results = Core::getHooks('root.new-nav', array('sub'));
		$expected = array(
			'options' => array(
				'url' => $link1,
				'title' => 'Action',
				'element' => null,
				'options' => array()
			)
		);
		$this->assertEqual($results, $expected);

		$core->settings['hooks'] = $oldHooks;
	}

	public function testReadImageSetting() {
		$result = Core::read('users.default_image');
		$expected = 'Default profile photo';
		$this->assertEqual($result['alternative'], $expected);

		$this->assertNull(Core::read('users.default_icon'));
	}

	public function testRead() {
		$result = Core::read('version');
		$this->assertTrue(is_string($result));

		Core::_write('a.deep.array.test', array(
			'something' => 'cool'
		));
		$result = Core::read('a');
		$expected = array(
			'deep' => array(
				'array' => array(
					'test' => array(
						'something' => 'cool'
					)
				)
			)
		);
		$this->assertEqual($result, $expected);

		$result = Core::read('a.deep.array');
		$expected = array(
			'test' => array(
				'something' => 'cool'
			)
		);
		$this->assertEqual($result, $expected);

		$this->assertIdentical(Core::read('UndefinedSetting'), null);

		$this->assertIdentical(Core::read('a.deep.UndefinedSetting'), null);

		$result = Core::read('general.church_name');
		$this->assertTags($result, array(
			'span' => array(
				'class' => 'churchname'
			),
			'b' => array(),
			'ROCK',
			'/b',
			'HARBOR',
			'/span'
		));
	}

	public function testWrite() {
		$result = Core::_write('test', 'success');
		$expected = 'success';
		$this->assertEqual($result, $expected);

		$result = Core::_write('another.test', 13);
		$expected = array(
			'test' => 13
		);
		$this->assertEqual($result, $expected);

		$result = Core::_write('another.deeper.array.test', 'horray!');
		$expected = array(
			'deeper' => array(
				'array' => array(
					'test' => 'horray!'
				)
			),
			'test' => 13
		);
		$this->assertEqual($result, $expected);

		$result = Core::_write('another', false);
		$expected = false;
		$this->assertIdentical($result, $expected);
	}
}

