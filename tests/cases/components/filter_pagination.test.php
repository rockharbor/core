<?php

App::import('Lib', 'CoreTestCase');
App::import('Model', 'App');
App::import('Component', 'ProxyFilterPagination');

class CompletelyUnrelatedModel extends AppModel {
	public $useTable = false;
}

class UnrelatedModel extends AppModel {
	public $useTable = false;
}

class EmptyModel extends AppModel {
	public $useTable = false;
}

class PaginateTest extends AppModel {
	public $hasOne = array(
		'EmptyModel'
	);

	public $belongsTo = array(
		'EmptyModel'
	);
}

class PaginateTests2Controller extends Controller {

	public $uses = array('PaginateTest');

	public $components = array(
		'FilterPagination' => array(
			'startEmpty' => false
		),
		'Session'
	);

	public function filter() {
		$conditions = $this->postConditions($this->data, 'LIKE', 'OR');
		$limit = 3;
		$this->paginate = compact('conditions', 'limit');
		$results = $this->FilterPagination->paginate();
		$this->set(compact('results'));
	}

}

class PaginateTestsController extends Controller {

	public $name = 'PaginateTests';

	public $uses = array('PaginateTest', 'UnrelatedModel');

	public $components = array('FilterPagination', 'Session');

	public function index() {
		$this->paginate = array(
			'limit' => 1
		);
		$results = $this->FilterPagination->paginate();
		$this->set(compact('results'));
	}

	public function filter() {
		$conditions = $this->postConditions($this->data, 'LIKE', 'OR');
		$limit = 2;
		$this->paginate = compact('conditions', 'limit');
		$results = $this->FilterPagination->paginate();
		$this->set(compact('results'));
	}

	public function paginate_other_model($model = 'EmptyModel') {
		$results = $this->FilterPagination->paginate($model);
		$this->set(compact('results'));
	}
}

Mock::generatePartial('PaginateTestsController', 'MockPaginateTestsController', array('render', 'header', 'stop'));

class FilterPaginationTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('PaginateTest');
		$this->Controller = new PaginateTestsController();
		$this->Controller->__construct();
		$this->Controller->constructClasses();
		$this->Controller->Component->init($this->Controller);
		$this->Controller->FilterPagination = new ProxyFilterPaginationComponent($this->Controller);
		$this->Controller->FilterPagination->Session = $this->Controller->Session;
		$this->testController = $this->Controller;
	}

	public function endTest() {
		$this->Controller->Session->destroy();
		unset($this->Controller);
		ClassRegistry::flush();
	}

	public function testAttachLinkedModel() {
		ClassRegistry::addObject('CompletelyUnrelatedModel', new CompletelyUnrelatedModel());
		ClassRegistry::addObject('EmptyModel', new EmptyModel());
		ClassRegistry::addObject('UnrelatedModel', new UnrelatedModel());

		$link = array(
			'CompletelyUnrelatedModel'
		);
		$this->Controller->FilterPagination->_attachLinkedModels($this->Controller->PaginateTest, $link);
		$this->assertIsA($this->Controller->PaginateTest->CompletelyUnrelatedModel, 'CompletelyUnrelatedModel');

		$link = array(
			'CompletelyUnrelatedModel' => array(
				'EmptyModel' => array(
					'fields' => array('id'),
					'UnrelatedModel'
				)
			)
		);
		$this->Controller->FilterPagination->_attachLinkedModels($this->Controller->PaginateTest, $link);
		$this->assertIsA($this->Controller->PaginateTest->CompletelyUnrelatedModel, 'CompletelyUnrelatedModel');
		$this->assertIsA($this->Controller->PaginateTest->EmptyModel, 'EmptyModel');
		$this->assertIsA($this->Controller->PaginateTest->UnrelatedModel, 'UnrelatedModel');
	}

	public function testIndirectlyAssociatedModel() {
		$this->assertNoErrors();
		$vars = $this->testAction('/paginate_tests/paginate_other_model');
		$results = $vars['results'];
		$this->assertEqual($results, array());

		$this->assertNoErrors();
		$vars = $this->testAction('/paginate_tests/paginate_other_model/UnrelatedModel');
		$results = $vars['results'];
		$this->assertEqual($results, array());

		$this->assertNoErrors();
		$vars = $this->testAction('/paginate_tests/paginate_other_model/CompletelyUnrelatedModel');
		$results = $vars['results'];
		$this->assertEqual($results, array());
	}

	public function testStartEmpty() {
		// make it not start with an empty array
		$this->Controller->FilterPagination->startEmpty(false);
		$vars = $this->testAction('/paginate_tests/');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertTrue($this->Controller->Session->check('FilterPagination.PaginateTests_index'));
		$this->assertEqual($results, $expected);

		// simulate new page
		$this->Controller->Session->delete('FilterPagination.PaginateTests_index');
		$this->Controller->FilterPagination->startEmpty(false);
		$data = array('PaginateTest' => array('name' => 'CORE'));
		$vars = $this->testAction('/paginate_tests/filter', array(
			'data' => $data
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'The CORE Awesomeness'
		);
		$this->assertEqual($this->Controller->Session->read('FilterPagination.PaginateTests_filter.data'), $data);
		$this->assertEqual($results, $expected);

		// same data, different page
		$vars = $this->testAction('/paginate_tests/filter/page:1', array(
			'data' => $data
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'The CORE Awesomeness'
		);
		$this->assertEqual($this->Controller->data, $data);
		$this->assertEqual($results, $expected);

		// changed data (with passed pagination var)
		$newData = array('PaginateTest' => array('name' => 'Paginated'));
		$vars = $this->testAction('/paginate_tests/filter/page:1', array(
			'data' => $newData
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertEqual($this->Controller->Session->read('FilterPagination.PaginateTests_filter.data'), $newData);
		$this->assertEqual($results, $expected);

		// change page via paginator
		$vars = $this->testAction('/paginate_tests/filter/page:1');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertEqual($this->Controller->Session->read('FilterPagination.PaginateTests_filter.data'), $newData);
		$this->assertEqual($results, $expected);

		// change page
		$this->_componentsInitialized = false;
		$Controller = new PaginateTests2Controller();
		$Controller->__construct();
		$Controller->constructClasses();
		$Controller->Component->initialize($Controller);
		$this->testController = $Controller;
		$vars = $this->testAction('/paginate_tests_2/filter/');
		$result = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing',
			'The CORE Awesomeness',
			'Back to the Future'
		);
		$this->assertEqual($result, $expected);
	}

	public function testFilterPaginate() {
		$vars = $this->testAction('/paginate_tests/');
		$this->assertEqual($vars['results'], array());

		$data = array('Some data to persist');
		$vars = $this->testAction('/paginate_tests/', array(
			'data' => $data
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertTrue($this->Controller->Session->check('FilterPagination.PaginateTests_index'));
		$this->assertEqual($results, $expected);

		// check to make sure data leaves when a new pagination call is made
		$vars = $this->testAction('/paginate_tests/');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$this->assertEqual($results, array());
		$this->assertEqual($this->Controller->data, array());
	}

	public function testDataPersist() {
		$data = array(
			'PaginateTest' => array(
				'name' => 'a'
			)
		);
		$vars = $this->testAction('/paginate_tests/filter', array(
			'data' => $data
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing',
			'The CORE Awesomeness'
		);
		$this->assertTrue($this->Controller->Session->check('FilterPagination.PaginateTests_filter'));
		$this->assertEqual($results, $expected);

		// check to see that data persists when a pagination call is made
		$vars = $this->testAction('/paginate_tests/filter/page:1/sort:name/direction:desc');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'The CORE Awesomeness',
			'Back to the Future'
		);
		$this->assertEqual($results, $expected);
		$this->assertEqual($this->Controller->data, $data);

		// and check to see that changing pages continues the original search filter
		$vars = $this->testAction('/paginate_tests/filter/page:2/sort:name/direction:desc');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertEqual($results, $expected);
	}

	public function testNoDataLeak() {
		$data = array(
			'PaginateTest' => array(
				'name' => 'a'
			)
		);
		$vars = $this->testAction('/paginate_tests/filter', array(
			'data' => $data
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing',
			'The CORE Awesomeness'
		);
		$this->assertTrue($this->Controller->Session->check('FilterPagination.PaginateTests_filter'));
		$this->assertEqual($results, $expected);

		// check to see that data persists when a pagination call is made
		$vars = $this->testAction('/paginate_tests/filter/page:1/sort:name/direction:desc');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'The CORE Awesomeness',
			'Back to the Future'
		);
		$this->assertEqual($results, $expected);
		$this->assertEqual($this->Controller->data, $data);

		$Controller = new PaginateTests2Controller();
		$Controller->__construct();
		$Controller->constructClasses();
		$Controller->Component->initialize($Controller);
		$this->testController = $Controller;
		// try a new action and make sure no filtered data exists for it
		$vars = $this->testAction('/paginate_tests2/filter/page:1/sort:name/direction:asc');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing',
			'Back to the Future',
			'The CORE Awesomeness'
		);
		$this->assertEqual($results, $expected);
		$this->assertEqual($this->Controller->data, $data);
	}

}

